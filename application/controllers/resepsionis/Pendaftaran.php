<?php
class Pendaftaran extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
    $this->load->model('resepsionis/m_pendaftaran', 'model');
  }

  public function index()
  {
    $data['active'] = 'Resepsionis';
    $data['title'] = 'Registrasi';

    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pendaftaran', $data);
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

  public function view_tambah()
  {
    $data['active'] = 'Resepsionis';
    $data['title'] = 'Registrasi';

    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pendaftaran/tambah', $data);
    $this->load->view('templates/footer');
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

  public function view_edit($id)
  {
    $data['row'] = $this->model->row_data($id);
    $data['active'] = 'Resepsionis';
    $data['title'] = 'Registrasi';

    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pendaftaran/edit', $data);
    $this->load->view('templates/footer');
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

  // mengambil poli dan dokter
  public function poli()
  {
    $response = $this->model->nama_poli();
    echo json_encode($response);
  }

  public function dokter()
  {
    $response = $this->model->dokter();
    echo json_encode($response);
  }
  public function pasien()
  {
    $response = $this->model->pasien();
    echo json_encode([
      'status' => true,
      'data' => $response
    ]);
  }
}
?>