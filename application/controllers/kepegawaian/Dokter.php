<?php
class Dokter extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
      if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
    $this->load->model('kepegawaian/m_dokter', 'model');
    $this->load->model('kepegawaian/m_jadwal');
  }
  public function index()
  {
    $data['active'] = 'Kepegawaian';
    $data['title'] = 'Dokter';

    $this->load->view('templates/header', $data);
    $this->load->view('kepegawaian/dokter', $data);
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

  // jadwal dokter
  public function hapus_jadwal()
  {
    $response = $this->model->hapuss();
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }

  public function jadwal_edit($id, $hari)
  {
    $data['dokter'] = $this->model->row_data($id);
    $data['jadwal'] = $this->model->get_jadwal_by_id_and_day($id, $hari);
    $data['active'] = 'Kepegawaian';
    $data['title'] = 'Jadwal Dokter';

    $this->load->view('templates/header', $data);
    $this->load->view('kepegawaian/dokter/edit_jadwal', $data);
    $this->load->view('templates/footer');
  }

  public function edit_jadwal()
  {
    $response = $this->model->jadwal_editt();
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output(json_encode($response, JSON_PRETTY_PRINT))
      ->_display();
    exit;
  }

  // kirim tambah
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

  // detail jadwal dokter view
  public function kalender($id)
  {
    $data['dokter'] = $this->model->row_data($id);
    $data['jadwal'] = $this->model->get_jadwal_by_dokter_id($id);
    $data['active'] = 'Kepegawaian';
    $data['title'] = 'Jadwal Dokter';
    $this->load->view('templates/header', $data);
    $this->load->view('kepegawaian/dokter/detail_dokter.php', $data);
    $this->load->view('templates/footer');
  }

  public function view_tambaa($id)
  {
    $data['active'] = 'Kepegawaian';
    $data['title'] = 'Jadwal Dokter';
    // $data['data_dokter'] = $this->model->result_data();
    $data['dokter'] = $this->model->row_data($id);
    $data['jadwal'] = $this->model->get_jadwal_by_dokter_id($id);

    $this->load->view('templates/header', $data);
    $this->load->view('kepegawaian/dokter/tambah', $data);
    $this->load->view('templates/footer');
  }
  public function tambah_aksi()
  {
    $id = $this->input->post('id_dokter');
    $hari = $this->input->post('hari');
    $jam_mulai = $this->input->post('jam_mulai');
    $jam_selesai = $this->input->post('jam_selesai');

    $simpan = $this->model->update_jadwal_batch($id, $hari, $jam_mulai, $jam_selesai);

    header('Content-Type: application/json');
    echo json_encode(['status' => $simpan, 'message' => $simpan ? 'Jadwal berhasil disimpan' : 'Gagal menyimpan jadwal']);
  }

  //nama poli
  public function poli()
  {
    $response = $this->model->nama_poli();
    echo json_encode($response);
  }
  public function pegawai()
  {
    $response = $this->model->nama_pegawai();
    echo json_encode($response);
  }

}
?>