<?php
class Pegawai extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('kepegawaian/m_pegawai', 'model');
    }

    public function index()
    {
        $data['active'] = 'Kepegawaian';
        $data['title'] = 'Pegawai';

        $this->load->view('templates/header', $data);
        $this->load->view('kepegawaian/pegawai', $data);
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

        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // view tambah
    public function view_tambah()
    {
        $data['active'] = 'Kepegawaian';
        $data['title'] = 'Pegawai';

        $this->load->view('templates/header', $data);
        $this->load->view('kepegawaian/pegawai/tambah', $data);
        $this->load->view('templates/footer');
    }

    // view edit
    public function view_edit($id)
    {
        $data['row'] = $this->model->row_data($id);
        $data['active'] = 'Kepegawaian';
        $data['title'] = 'Pegawai';

        $this->load->view('templates/header', $data);
        $this->load->view('kepegawaian/pegawai/edit', $data);
        $this->load->view('templates/footer');
    }

    // kirim tambah
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

    public function hapus()
    {
        $response = $this->model->hapus();

        $this
            ->output
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

    public function jabatan()
    {
        $response = $this->model->nama_jabatan();
        echo json_encode($response);
    }
}
?>