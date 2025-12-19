<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('gudang/M_Stok', 'model'); 
        $this->load->helper('url');
        $this->load->library('form_validation');
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
    }

    public function index() {
        $data['active'] = 'stok';
        $data['title'] = 'Stok Barang';

        $this->model->cleanup_all_duplicates();

        $this->load->view('templates/header', $data);
        $this->load->view('gudang/stok', $data); 
        $this->load->view('templates/footer');
    }

    public function get_stok_data_ajax() {
        if ($this->input->is_ajax_request()) {
            $search = $this->input->get('search') ?: '';
            $page = $this->input->get('page') ?: 1;
            $limit = $this->input->get('limit') ?: 10;
            $offset = ($page - 1) * $limit;
            $stok_list = $this->model->get_all_stok($search, $limit, $offset);
            $total_rows = $this->model->count_all_stok($search);

            foreach ($stok_list as &$stok) {
                if (!empty($stok['kadaluarsa'])) {
                    $stok['kadaluarsa'] = date('d-m-Y', strtotime($stok['kadaluarsa']));
                }
            }

            echo json_encode([
                'stok_list' => $stok_list,
                'total_rows' => $total_rows,
                'current_page' => (int)$page,
                'per_page' => (int)$limit
            ]);
        } else {
            show_404();
        }
    }


}