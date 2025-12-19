<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('transaksi/M_penjualan', 'm_penjualan');
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
    }

    public function index()
    {
        $data['active'] = 'penjualan';
        $data['title'] = 'Penjualan';
          
        $this->load->view('templates/header', $data);
        $this->load->view('transaksi/penjualan', $data);
        $this->load->view('templates/footer');
    }


    public function get_barang_list()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        $search = $this->input->post('search');
        $jenis_id = $this->input->post('jenis_id');
        
        echo json_encode($this->m_penjualan->get_barang_list($search, $jenis_id));
    }

    public function get_barang_list_pagination()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $page = intval($this->input->post('page')) ?: 1;
        $limit = intval($this->input->post('limit')) ?: 10;
        $search = $this->input->post('search');
        $jenis_id = $this->input->post('jenis_id');
        $offset = ($page - 1) * $limit;
        $data_barang = $this->m_penjualan->get_barang_pagination($search, $limit, $offset, $jenis_id);
        $total_rows = $this->m_penjualan->count_barang_pagination($search, $jenis_id);

        header('Content-Type: application/json');
        echo json_encode([
            'data' => $data_barang,
            'total_rows' => $total_rows
        ]);
    }

    public function get_barang_by_id($id_barang_detail)
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        header('Content-Type: application/json');
        echo json_encode($this->m_penjualan->get_barang_by_id($id_barang_detail));
    }

    public function get_jenis_barang_ajax()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        echo json_encode($this->m_penjualan->get_jenis_barang());
    }

    public function get_pelanggan_ajax()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $search = $this->input->get('search');
        $result = $this->m_penjualan->cari_pelanggan($search);
        
        echo json_encode($result);
    }

    public function get_pelanggan_detail_ajax()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $id = $this->input->post('id');
        $result = $this->m_penjualan->get_detail_pelanggan($id);
        
        echo json_encode($result);
    }

    public function simpan_penjualan()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        
        header('Content-Type: application/json');
        
        $detail_items = json_decode($this->input->post('detail'), true);

        if (empty($detail_items)) {
            echo json_encode(['status' => false, 'message' => 'Keranjang belanja tidak boleh kosong.']);
            return;
        }

        $data = [
            'id_pelanggan'      => $this->input->post('id_pelanggan') ?: null,
            'nama_customer'     => trim($this->input->post('nama_customer')),
            'jenis_kelamin'     => $this->input->post('jenis_kelamin'),
            'umur'              => $this->input->post('umur') ?: null,
            'no_telp'           => $this->input->post('no_telp'),
            'total_invoice'     => $this->input->post('nilai_transaksi'),
            'bayar'             => $this->input->post('dibayar'),
            'kembali'           => $this->input->post('kembali'),
            'metode_pembayaran' => $this->input->post('metode_bayar'),
            'bank'              => $this->input->post('metode_bayar') === 'Transfer' ? $this->input->post('nama_bank') : null,
            'detail'            => $detail_items
        ];

        $result = $this->m_penjualan->simpan_transaksi($data);
        
        echo json_encode($result);
    }
    
    public function cetak_struk($id_transaksi)
    {
        $transaksi = $this->db->get_where('apt_transaksi', ['id' => $id_transaksi])->row_array();
        
        if (!$transaksi) {
            show_404();
        }

        $this->db->select('td.*, bd.kode_barang');
        $this->db->from('apt_transaksi_detail td');
        $this->db->join('apt_barang_detail bd', 'td.id_barang_detail = bd.id', 'left');
        $this->db->where('td.id_transaksi', $id_transaksi);
        $detail = $this->db->get()->result_array();

        $data['data'] = [
            'pembayaran' => $transaksi,
            'resep' => $detail 
        ];  

        $this->load->view('transaksi/penjualan/struk_penjualan', $data);
    }
}