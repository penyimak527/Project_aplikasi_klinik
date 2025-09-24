<?php
class Contoh_multiple extends CI_Controller{
  function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('M_contoh_multiple','model');
  }

  public function index(){
    $data['active'] = 'contoh_multiple';
    $data['title'] = 'Contoh Multiple';

    $this->load->view('templates/header', $data);
    $this->load->view('contoh_multiple', $data);
    $this->load->view('templates/footer');
  }

  public function view_tambah(){
    $data['active'] = 'contoh_multiple';
    $data['title'] = 'Contoh Multiple';

    $this->load->view('templates/header', $data);
    $this->load->view('contoh_multiple/tambah', $data);
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
    $data['res'] = $this->model->row_data_detail($id);
    $data['active'] = 'contoh_multiple';
    $data['title'] = 'Contoh Multiple';

    $this->load->view('templates/header', $data);
    $this->load->view('contoh_multiple/edit', $data);
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

  public function result_data_detail(){
    $data = $this->model->result_data_detail();

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
}
