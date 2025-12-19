<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('pembelian/M_Retur', 'model_retur');
        $this->load->helper('url');
        $this->load->library('form_validation');
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
    }

    public function index() {
        $data['active'] = 'retur';
        $data['title'] = 'Retur Barang';
        $data['kode_retur_otomatis'] = $this->model_retur->generateReturnCode();

        $this->load->view('templates/header', $data);
        $this->load->view('pembelian/retur', $data);
        $this->load->view('templates/footer');
    }

    public function get_faktur_data_for_retur_modal() {
        if ($this->input->is_ajax_request()) {
            $search = $this->input->post('search') ?: '';
            $page = $this->input->post('page') ?: 1;
            $limit = $this->input->post('limit') ?: 10;
            $offset = ($page - 1) * $limit;

            $faktur_list = $this->model_retur->getAllInvoicesForReturnModal($search, $limit, $offset);
            $total_rows = $this->model_retur->countAllInvoicesForReturnModal($search);

            foreach ($faktur_list as &$faktur) {
                $faktur['tanggal_waktu'] = date('d-m-Y H:i:s', strtotime($faktur['tanggal'] . ' ' . $faktur['waktu']));
            }

            echo json_encode(['result' => true, 'data' => $faktur_list, 'total_rows' => $total_rows]);
        } else {
            show_404();
        }
    }

    public function get_barang_from_faktur_detail_modal() {
        if ($this->input->is_ajax_request()) {
            $id_faktur = $this->input->post('id_faktur');
            $search = $this->input->post('search') ?: '';
            $page = $this->input->post('page') ?: 1;
            $limit = $this->input->post('limit') ?: 10;
            $offset = ($page - 1) * $limit;

            if (empty($id_faktur)) {
                echo json_encode(['result' => false, 'message' => 'ID Faktur tidak ditemukan.']);
                return;
            }

            $barang_list = $this->model_retur->getItemsFromInvoiceDetail($id_faktur, $search, $limit, $offset);
            $total_rows = $this->model_retur->countItemsFromInvoiceDetail($id_faktur, $search);

            echo json_encode(['result' => true, 'data' => $barang_list, 'total_rows' => $total_rows]);
        } else {
            show_404();
        }
    }
    
    public function save_retur() {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('faktur_id', 'ID Faktur', 'required');
            $this->form_validation->set_rules('retur_data', 'Data Barang Retur', 'required');

            if ($this->form_validation->run() === FALSE) {
                echo json_encode(['result' => false, 'message' => validation_errors()]);
                return;
            }

            $faktur_id = $this->input->post('faktur_id');
            $retur_data = json_decode($this->input->post('retur_data'), true);

            if (empty($retur_data)) {
                echo json_encode(['result' => false, 'message' => 'Tidak ada barang yang dipilih untuk retur.']);
                return;
            }
            
            $result = $this->model_retur->saveReturnWithStockUpdate($faktur_id, $retur_data);

            echo json_encode($result);

        } else {
            show_404();
        }
    }

    public function riwayat() {
        $data['active'] = 'retur';
        $data['title'] = 'Riwayat Retur Barang';
        
        $this->load->view('templates/header', $data);
        $this->load->view('pembelian/retur/riwayat_retur', $data); 
        $this->load->view('templates/footer');
    }

    public function get_riwayat_retur_data() {
        if ($this->input->is_ajax_request()) {
            $search = $this->input->post('search') ?: '';
            $page = $this->input->post('page') ?: 1;
            $limit = $this->input->post('limit') ?: 10;
            $offset = ($page - 1) * $limit;

            $retur_list = $this->model_retur->getRiwayatRetur($search, $limit, $offset);
            $total_rows = $this->model_retur->countRiwayatRetur($search);

            echo json_encode([
                'result' => true,
                'data' => $retur_list,
                'total_rows' => $total_rows
            ]);
        } else {
            show_404();
        }
    }

    public function get_detail_retur_data() {
        header('Content-Type: application/json');
        
        try {
            if (!$this->input->is_ajax_request()) {
                throw new Exception("Invalid request");
            }

            $id_retur = $this->input->post('id_retur');
            
            if (empty($id_retur)) {
                throw new Exception("ID Retur tidak valid");
            }

            $this->load->model('pembelian/M_Retur');
            
            $header = $this->M_Retur->getReturHeader($id_retur);
            if (!$header) {
                throw new Exception("Data retur tidak ditemukan");
            }
            
            $details = $this->M_Retur->getReturDetails($id_retur);

            echo json_encode([
                'result' => true,
                'header' => $header,
                'details' => $details
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Error in get_detail_retur_data: ' . $e->getMessage());
            echo json_encode([
                'result' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}