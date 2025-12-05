<?php
class Poli extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
          if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
        $this->load->model('master_data/m_poli', 'model');
    }

    public function index()
    {        
        $data['active'] = 'Master data';
        $data['title'] = 'Poli';
        $this->load->view('templates/header', $data);
        $this->load->view('master_data/poli', $data);
        $this->load->view('templates/footer');
    }

    // view tambah
    public function view_tambah()
    {
        $data['active'] = 'Master Data';
        $data['title'] = 'Poli';
        $this->load->view('templates/header', $data);
        $this->load->view('master_data/poli/tambah.php', $data);
        $this->load->view('templates/footer');
    }

    // view edit
    public function view_edit($id)
    {
        $data['row'] = $this->model->row_data($id);
        $data['active'] = 'Master Data';
        $data['title'] = 'Poli';
        $this->load->view('templates/header', $data);
        $this->load->view('master_data/poli/edit.php', $data);
        $this->load->view('templates/footer');
    }

    // kirim tambah
    public function tambah()
    {
        $response = $this->model->tambah();
        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('aplication/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // kirim edit
    public function edit()
    {
        $response = $this->model->edit();
        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // tampil data
    public function result_data()
    {
        $data = $this->model->result_dat();

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

        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // hapus
    public function hapus()
    {
        $response = $this->model->hapus();
        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('aplication/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
}
?>