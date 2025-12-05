<?php
class Diagnosa extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
          if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
        $this->load->model('master_data/m_diagnosa', 'model');
    }

    public function index()
    {
        $data['active'] = 'Master data';
        $data['title'] = 'Diagnosa';
        $this->load->view('templates/header', $data);
        $this->load->view('master_data/diagnosa', $data);
        $this->load->view('templates/footer');
    }

    // tampil data
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

    // view tambah
    public function view_tambah()
    {
        $data['active'] = 'Master Data';
        $data['title'] = 'Diagnosa';

        $this->load->view('templates/header', $data);
        $this->load->view('master_data/diagnosa/tambah', $data);
        $this->load->view('templates/footer');
    }

    // view edit
    public function view_edit($id)
    {
        $data['row'] = $this->model->row_data($id);
        $data['active'] = 'Master Data';
        $data['title'] = 'Diagnosa';

        $this->load->view('templates/header', $data);
        $this->load->view('master_data/diagnosa/edit', $data);
        $this->load->view('templates/footer');
    }

    // tambah kirim
    public function tambah()
    {
        $response = $this->model->tambah();

        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    //edit kirim 
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

    //hapus data 
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

    // nama poli
    public function poli()
    {
        $response = $this->model->nama_poli();
        echo json_encode($response);
    }
}
?>