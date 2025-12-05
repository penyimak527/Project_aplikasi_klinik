<?php
class Antrian extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
      if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
    $this->load->model('antrian/m_antrian', 'model');
  }
  public function index()
  {
    $timestamp = time();
    $date = gmdate('d-m-Y', $timestamp);
    $data['active'] = 'Antrian';
    $data['title'] = 'Panel Antrian';
    $data['jumlah_pasien'] = $this->db->where('tanggal_antri', $date)->count_all_results('rsp_antrian');
    $this->load->view('antrian/panel_antrian', $data);
  }
  public function belum_p()
  {
    $data = $this->model->result_p();
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
  public function index_dokter()
  {
    $data['active'] = 'Antrian';
    $data['title'] = 'Dokter Antrian';
    $this->load->view('templates/header', $data);
    $this->load->view('antrian/dokter_antrian', $data);
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
  

  public function dokter()
  {
    $data = $this->model->dokter();

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

  public function konfirmasi()
  {
    $response = $this->model->selesai();

    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
  public function panggil()
  {
    $response = $this->model->panggil();

    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
  public function poli(){
    $data = $this->model->poli();

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

   public function cek_btn()
  {
    $response = $this->model->cek_status();

    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
   public function cek_konfirmasi()
  {
    $response = $this->model->cek_konfirmasi();

    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }
}
?>