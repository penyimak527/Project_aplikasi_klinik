<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('pembelian/M_Faktur', 'model');
        $this->load->model('pembelian/M_supplier');
        $this->load->model('master_data/M_barang');
        $this->load->helper('url');
        $this->load->library('form_validation');
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
    }

    public function index() {
        $data['active'] = 'faktur';
        $data['title'] = 'Faktur';
        $tanggal_dari = $this->input->get('tanggal_dari') ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->get('tanggal_dari')))) : date('Y-m-01');
        $tanggal_sampai = $this->input->get('tanggal_sampai') ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->get('tanggal_sampai')))) : date('Y-m-d');
        $search = $this->input->get('search') ?: '';
        $id_supplier = $this->input->get('id_supplier') ?: 'semua';
        $data['supplier_list'] = $this->M_supplier->get_all_supplier();

        $this->load->view('templates/header', $data);
        $this->load->view('pembelian/faktur', $data);
        $this->load->view('templates/footer');
    }

    public function get_faktur_data_ajax() {
        if ($this->input->is_ajax_request()) {
            $raw_tanggal_dari = $this->input->get('tanggal_dari');
            $raw_tanggal_sampai = $this->input->get('tanggal_sampai');

            $tanggal_dari = !empty($raw_tanggal_dari) ? date('Y-m-d', strtotime(str_replace('/', '-', $raw_tanggal_dari))) : '';
            $tanggal_sampai = !empty($raw_tanggal_sampai) ? date('Y-m-d', strtotime(str_replace('/', '-', $raw_tanggal_sampai))) : '';

            $search = $this->input->get('search') ?: '';
            $id_supplier = $this->input->get('id_supplier') ?: 'semua';

            $faktur_list = $this->model->get_all_faktur($tanggal_dari, $tanggal_sampai, $search, $id_supplier);

            foreach ($faktur_list as &$faktur) {
                $faktur['tanggal'] = date('d-m-Y', strtotime($faktur['tanggal']));
                if (!empty($faktur['tanggal_bayar'])) {
                    $faktur['tanggal_bayar'] = date('d-m-Y', strtotime($faktur['tanggal_bayar']));
                }
            }

            echo json_encode(['faktur_list' => $faktur_list]);
        } else {
            show_404();
        }
    }

    public function get_detail_faktur($id_faktur) {
        if ($this->input->is_ajax_request()) {
            $barang_detail = $this->model->get_detail_faktur($id_faktur);
            foreach ($barang_detail as &$detail) {
                if (!empty($detail['kadaluarsa'])) {
                    $detail['kadaluarsa'] = date('d-m-Y', strtotime($detail['kadaluarsa']));
                }
            }

            $riwayat_pembayaran = $this->model->get_riwayat_pembayaran($id_faktur);

            echo json_encode([
                'barang_detail' => $barang_detail,
                'riwayat_bayar' => $riwayat_pembayaran 
            ]);
        } else {
            show_404();
        }
    }

    public function tambah() {
        if ($this->input->method() == 'post') {
            $response = $this->model->insert_faktur();
            echo json_encode($response);
            return;
        }

        $data['active'] = 'faktur';
        $data['title'] = 'Faktur';
        $data['no_faktur_otomatis'] = $this->model->generate_non_faktur_number();
        $data['supplier_list'] = $this->M_supplier->get_all_supplier();

        $this->load->view('templates/header', $data);
        $this->load->view('pembelian/faktur/tambah', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id = null) {
        if ($id == null) {
            redirect('pembelian/faktur');
        }

        if ($this->input->method() == 'post') {
            $response = $this->model->edit_faktur_data();
            echo json_encode($response);
            return;
        } else {
            $data['active'] = 'faktur';
            $data['title'] = 'Faktur';
            $row_data = $this->model->row_data($id);

            if (!empty($row_data['tanggal'])) {
                $row_data['tanggal'] = date('d-m-Y', strtotime($row_data['tanggal']));
            }
            if (!empty($row_data['tanggal_bayar'])) {
                $row_data['tanggal_bayar'] = date('d-m-Y', strtotime($row_data['tanggal_bayar']));
            }

            $data['row'] = $row_data;
            $data['row']['details'] = $this->model->get_detail_faktur($id);
        
            foreach ($data['row']['details'] as &$detail) {
                if (!empty($detail['kadaluarsa'])) {
                    $detail['kadaluarsa'] = date('d-m-Y', strtotime($detail['kadaluarsa']));
                }
            }
            unset($detail);

            $total_harga_beli_current = 0;
            foreach ($data['row']['details'] as $detail) {
                $jumlah = (float)str_replace(',', '.', $detail['jumlah']); 
            $harga_awal = (float)str_replace(',', '.', $detail['harga_awal']);
                $total_harga_beli_current += $jumlah * $harga_awal;
            }
            $data['row']['total_harga_beli'] = $total_harga_beli_current;

            $subtotal_after_discount = $total_harga_beli_current;
            if (isset($data['row']['status_diskon']) && $data['row']['status_diskon'] == 'ada' && isset($data['row']['diskon'])) {
                $diskon_val = (float)$data['row']['diskon'];
                $data['row']['jenis_diskon_for_display'] = $data['row']['jenis_diskon'];

                if ($data['row']['jenis_diskon'] == 'persen') {
                    $data['row']['diskon_persen_display'] = (string)$diskon_val;
                    $data['row']['diskon_rp_display'] = '0';
                    $subtotal_after_discount = $total_harga_beli_current - ($total_harga_beli_current * ($diskon_val / 100));
                } else {
                    $data['row']['diskon_rp_display'] = (string)$diskon_val;
                    $data['row']['diskon_persen_display'] = '0';
                    $subtotal_after_discount = $total_harga_beli_current - $diskon_val;
                }
            } else {
                $data['row']['jenis_diskon_for_display'] = 'persen';
                $data['row']['diskon_persen_display'] = '0';
                $data['row']['diskon_rp_display'] = '0';
            }
            $data['row']['subtotal_harga_diskon'] = $subtotal_after_discount;

            $bayar_val = (float)$data['row']['bayar'];
            $total_harga_val = (float)$data['row']['total_harga'];
            $data['row']['kembalian'] = $bayar_val - $total_harga_val;

            $data['no_faktur_otomatis'] = $this->model->generate_non_faktur_number();
            $data['supplier_list'] = $this->M_supplier->get_all_supplier();

            if (!isset($data['row']['diskon'])) {
                $data['row']['diskon'] = '0';
            }

            $this->load->view('templates/header', $data);
            $this->load->view('pembelian/faktur/edit', $data);
            $this->load->view('templates/footer');
        }
    }

    public function get_all_barang_for_popup() {
        $search = $this->input->post('search');
        $page = $this->input->post('page') ?: 1;
        $limit = $this->input->post('limit') ?: 10;
        $offset = ($page - 1) * $limit;

        $data = $this->model->get_all_barang_for_popup($search, $limit, $offset);
        $total_rows = $this->model->count_all_barang_for_popup($search);

        foreach ($data as &$item) {
            if (!empty($item['kadaluarsa'])) {
                $item['kadaluarsa'] = date('d-m-Y', strtotime($item['kadaluarsa']));
            }
        }

        echo json_encode(['result' => true, 'data' => $data, 'total_rows' => $total_rows]);
    }

    public function klik_barang() {
        $id_barang = $this->input->post('id_barang');
        $urutan_satuan = $this->input->post('urutan_satuan');

        $data_detail = $this->model->get_barang_detail_by_id_and_urutan($id_barang, $urutan_satuan);

        if (!empty($data_detail['kadaluarsa'])) {
            $data_detail['kadaluarsa'] = date('d-m-Y', strtotime($data_detail['kadaluarsa']));
        }

        echo json_encode($data_detail);
    }

    public function hapus($id) {
        if ($this->input->is_ajax_request()) {
            $result = $this->model->hapus($id);
            echo json_encode($result);
        } else {
            show_404();
        }
    }

    public function pelunasan_bayar($id_faktur = null) {
        if ($id_faktur == null) {
            redirect('pembelian/faktur');
        }

        $data['active'] = 'faktur';
        $data['title'] = 'Pelunasan';
        $faktur_data = $this->model->get_faktur_for_pelunasan($id_faktur);

        if (empty($faktur_data)) {
            show_404();
        }

        $status_pembayaran_terakhir = $this->model->get_status_terakhir_pembayaran($id_faktur);
        $total_dibayar = $status_pembayaran_terakhir ? $status_pembayaran_terakhir['dibayar'] : 0;
        $faktur_data['sisa_kurang'] = $faktur_data['total_harga'] - $total_dibayar;

        if (!empty($faktur_data['tanggal'])) {
            $faktur_data['tanggal'] = date('d-m-Y', strtotime($faktur_data['tanggal']));
        }
        
        $data['faktur'] = $faktur_data;
        $data['status_pembayaran_terakhir'] = $status_pembayaran_terakhir;

        $this->load->view('templates/header', $data);
        $this->load->view('pembelian/faktur/pelunasan_bayar', $data);
        $this->load->view('templates/footer');
    }


    public function proses_pelunasan() {
        if ($this->input->is_ajax_request()) {
            $id_faktur = $this->input->post('id_faktur');
            $jumlah_bayar_baru = floatval(str_replace('.', '', str_replace(',', '.', $this->input->post('jumlah_bayar_baru'))));
            $tanggal_pelunasan = $this->input->post('tanggal_pelunasan');

            $result = $this->model->update_pelunasan($id_faktur, $jumlah_bayar_baru, $tanggal_pelunasan);
            echo json_encode($result);
        } else {
            show_404();
        }
    }

}