<?php
class Satuan extends CI_Controller{
  function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('master_data/M_Satuan','model');
    if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
  }

  public function index(){
    $data['active'] = 'satuan';
    $data['title'] = 'Satuan';

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/satuan', $data);
    $this->load->view('templates/footer');
  }

  public function view_tambah(){
    $data['active'] = 'satuan';
    $data['title'] = 'Satuan';

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/satuan/tambah', $data);
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
    $nama_satuan = ucwords($this->input->post('nama_satuan')); // Pastikan name di form adalah 'nama_satuan'

    if (empty($nama_satuan)) {
        echo json_encode(['status' => false, 'message' => 'Nama satuan tidak boleh kosong!']);
        exit;
    }

    $cek = $this->db->get_where('apt_satuan_barang', ['nama_satuan' => $nama_satuan])->num_rows();

    if ($cek > 0) {
        echo json_encode([
            'status' => false, 
            'message' => 'Nama satuan "'. $nama_satuan .'" sudah ada!'
        ]);
        exit;
    }

    $data = ['nama_satuan' => $nama_satuan];
    $insert = $this->db->insert('apt_satuan_barang', $data);

    if ($insert) {
        echo json_encode(['status' => true, 'message' => 'Data berhasil ditambahkan!']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Gagal menyimpan data ke database.']);
    }

  }

  public function view_edit($id){
    $data['row'] = $this->model->row_data($id);
    $data['active'] = 'satuan';
    $data['title'] = 'Satuan';

    $this->load->view('templates/header', $data);
    $this->load->view('master_data/satuan/edit', $data);
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
}
