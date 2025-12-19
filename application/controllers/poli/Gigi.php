<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gigi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('poli/m_gigi', 'gigi');
        $this->load->model('master_data/m_diagnosa', 'diagnosa');
        $this->load->model('master_data/m_tindakan', 'tindakan');
    }

    public function index()
    {
        $data['title'] = "Poli Gigi";
        $this->load->view('templates/header', $data);
        $this->load->view('poli/gigi', $data);
        $this->load->view('templates/footer');
    }

    public function result_data()
    {
        $cari = $this->input->post('cari');
        $data_pasien = $this->gigi->get_pasien_poli_gigi($cari);
        header('Content-Type: application/json');
        echo json_encode(['result' => !empty($data_pasien), 'data' => $data_pasien]);
    }

    public function proses($kode_invoice)
    {
        $rekam_medis = $this->gigi->get_or_create_rekam_medis($kode_invoice);
        if ($rekam_medis) {
            redirect('poli/gigi/view_proses/' . $rekam_medis['id']);
        } else {
            redirect('poli/gigi');
        }
    }

    public function view_proses($id_pol_gigi = null)
    {
        if ($id_pol_gigi == null) {
            redirect('poli/gigi');
        }
        $data['title'] = "Proses Poli Gigi";
        $data['data'] = $this->gigi->get_rekam_medis_detail($id_pol_gigi);

        if (empty($data['data']['rekam_medis'])) {
            redirect('poli/gigi');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('poli/gigi/proses', $data);
        $this->load->view('templates/footer');
    }

    public function proses_aksi()
    {
        $data = $this->input->post();
        $simpan = $this->gigi->save_rekam_medis($data);
        $response = ['status' => $simpan, 'message' => $simpan ? 'Rekam medis berhasil disimpan.' : 'Gagal menyimpan rekam medis.'];
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function search_diagnosa()
    {
        $data = $this->gigi->diagnosa();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function search_tindakan()
    {
        $data = $this->gigi->tindakan();
        // var_dump($data);
        foreach ($data as $key =>$item) {
            $harga_row = (int) str_replace(',', '', $item['harga']);
            $data[$key]['harga_raw'] = $harga_row;
            $data[$key]['harga']   = number_format($item['harga'], 0, ',', '.');
            // $item['harga_row'] = number_format($item['harga'], 0, ',', '.');
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function search_obat()
    {
        $cari = $this->input->post('cari');
        $data = $this->gigi->get_data_barang($cari);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function tambah_diagnosa_ajax()
    {
        $nama_diagnosa = $this->input->post('nama_diagnosa');
        if (empty($nama_diagnosa)) {
            echo json_encode(['status' => false, 'message' => 'Nama diagnosa tidak boleh kosong']);
            return;
        }
        $data = [
            'nama_diagnosa' => $nama_diagnosa,
            'id_poli' => 4,
            'nama_poli' => 'Poli Gigi'
        ];

        $insert_result = $this->diagnosa->insert_master_data($data);
        if ($insert_result === false) {
            echo json_encode([
                'status' => false,
                'message' => 'Diagnosa "' . $nama_diagnosa . '" sudah ada.'
            ]);
            return;
        }
        $new_data = $this->diagnosa->row_data($insert_result);
        if ($new_data) {
            echo json_encode(['status' => true, 'data' => $new_data]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tersimpan tapi gagal diambil kembali.']);
        }
    }

    public function tambah_tindakan_ajax()
    {
        $nama_tindakan = $this->input->post('nama_tindakan');
        $harga = preg_replace('/[^0-9]/', '', $this->input->post('harga'));
        if (empty($nama_tindakan)) {
            echo json_encode(['status' => false, 'message' => 'Nama tindakan tidak boleh kosong']);
            return;
        }
        $data = [
            'nama' => $nama_tindakan,
            'harga' => $harga,
            'id_poli' => 4,
            'nama_poli' => 'Poli Gigi'
        ];
        $tindakan_insert = $this->tindakan->insert_master_data($data);
        if ($tindakan_insert === false) {
            echo json_encode([
                'status' => false,
                'message' => 'Tindakan "' . $nama_tindakan . '" sudah ada.'
            ]);
            return;
        }
        $new_data = $this->tindakan->row_data($tindakan_insert);
        if ($new_data) {
            echo json_encode(['status' => true, 'data' => $new_data]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Data tersimpan tapi gagal diambil kembali.']);
        }
    }
}
