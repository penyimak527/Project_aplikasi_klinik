<?php
class Pemasukan extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
    $this->load->model('keuangan/m_pemasukan', 'model');
  }
  public function index()
  {
    $data['active'] = 'Keuangan';
    $data['title'] = 'Pemasukan';
    $this->load->view('templates/header', $data);
    $this->load->view('keuangan/pemasukan', $data);
    $this->load->view('templates/footer');
  }
  public function view_tambah()
  {
    $data['active'] = 'Keuangan';
    $data['title'] = 'Pemasukan';
    $this->load->view('templates/header', $data);
    $this->load->view('keuangan/pemasukan/tambah', $data);
    $this->load->view('templates/footer');
  }
  public function view_edit($id)
  {
    $data['row'] = $this->model->row_data($id);
    $data['active'] = 'Keuangan';
    $data['title'] = 'Pemasukan';
    $this->load->view('templates/header', $data);
    $this->load->view('keuangan/pemasukan/edit', $data);
    $this->load->view('templates/footer');
  }
  public function result_data()
  {
    $data = $this->model->result_data();
    if ($data) {
      $response = array(
        'result' => true,
        'data' => $data
      );
    } else {
      $response = array(
        'result' => false,
        'message' => 'Data Kosong'
      );
    }
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
  public function hapus()
  {
    $response = $this->model->hapus();
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }

  public function tambah()
  {
    $response = $this->model->tambah();
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
  public function edit()
  {
    $response = $this->model->edit();
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
  public function jenis()
  {
    $data = $this->model->jenis();
    if ($data) {
      $response = array(
        'result' => true,
        'data' => $data
      );
    } else {
      $response = array(
        'result' => false,
        'message' => 'Data Kosong'
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
?>