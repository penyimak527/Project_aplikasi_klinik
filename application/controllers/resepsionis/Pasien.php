<?php
class Pasien extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
    $this->load->model('resepsionis/m_pasien', 'model');
  }
  public function index()
  {
    $data['active'] = 'Resepsionis';
    $data['title'] = 'Pasien';

    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pasien', $data);
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
    $data['title'] = 'Pasien';

    // if ($ambil) {
    //   # code...
    //   $last_num = (int) substr($ambil, 3); //ambil angka setelah rm
    //   $next_num = $last_num + 1;
    //   $data['kode_rm'] = 'RM-'. str_pad($next_num, 4,'0', STR_PAD_LEFT);
    // }else {
    //   $data['kode_rm'] = 'RM-0001';
    // }
    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pasien/tambah', $data);
    $this->load->view('templates/footer');
  }

  public function view_edit($id)
  {
    $data['active'] = 'Resepsionis';
    $data['title'] = 'Pasien';
    $data['row'] = $this->model->row_data($id);

    $this->load->view('templates/header', $data);
    $this->load->view('resepsionis/pasien/edit', $data);
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
}
?>