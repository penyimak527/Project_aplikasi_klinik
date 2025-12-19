<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan extends CI_Controller {

  function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('master_data/M_pelanggan','model');
    if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
  }

  public function index(){
    $data['active'] = 'pelanggan';
    $data['title'] = 'Pelanggan';

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/pelanggan', $data);
    $this->load->view('templates/footer');
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
			$response = ['result' => true, 'data' => $data];
		} else {
			$response = ['result' => false, 'message' => 'Data Kosong'];
		}
    $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
    exit;
  }
}