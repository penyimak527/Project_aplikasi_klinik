<?php
class Barang extends CI_Controller{
  function __construct(){
        parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
        $this->load->model('master_data/M_Barang','model');
        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
  }

  public function index(){
    $data['active'] = 'barang';
    $data['title'] = 'Barang';
    $data['jenis_barang_list'] = $this->model->get_all_jenis_barang();

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/barang', $data);
    $this->load->view('templates/footer');
  }

  public function view_tambah(){
    $data['active'] = 'barang';
    $data['title'] = 'Barang';
    $data['jenis_barang_list'] = $this->model->get_all_jenis_barang();
    $data['satuan_barang_list'] = $this->model->get_all_satuan_barang();

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/barang/tambah', $data);
    $this->load->view('templates/footer');
  }

  public function tambah(){
    $response = $this->model->tambah();

        $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }

  public function view_edit($id){
    $data['row'] = $this->model->row_data($id); 
    $data['active'] = 'barang';
    $data['title'] = 'Barang';
    $data['jenis_barang_list'] = $this->model->get_all_jenis_barang();
    $data['satuan_barang_list'] = $this->model->get_all_satuan_barang();

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/barang/edit', $data);
    $this->load->view('templates/footer');
  }

  public function edit(){
    $response = $this->model->edit();

        $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }

  public function hapus(){
    $response = $this->model->hapus();

        $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }

  public function result_data(){
    $data = $this->model->result_data();

    if ($data) {
            $response = array(
                'result' => true,
        'data' => $data
            );
        }else {
            $response = array(
                'result' => false,
                'message' => 'Data Kosong'
            );
        }

        $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }

  public function get_detail_data($id) {
    $item_details = $this->model->get_item_details($id);

    if ($item_details) {
        $response = array(
            'result' => true,
            'data' => $item_details
        );
    } else {
        $response = array(
            'result' => false,
            'message' => 'Data detail barang tidak ditemukan.'
        );
    }

    $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, JSON_PRETTY_PRINT))
        ->_display();
    exit;
  }
}
