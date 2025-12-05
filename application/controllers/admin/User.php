<?php
class User extends CI_Controller{
      function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
      if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
    $this->load->model('admin/m_user', 'model');
  }
  public function index()
  {
    $data['active'] = 'Admin';
    $data['title'] = 'User';

    $this->load->view('templates/header', $data);
    $this->load->view('admin/user', $data);
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
        $data['active'] = 'Admin';
        $data['title'] = 'User';

        $this->load->view('templates/header', $data);
        $this->load->view('admin/user/tambah', $data);
        $this->load->view('templates/footer');
    }

    // view edit
    public function view_edit($id)
    {
        $data['row'] = $this->model->row_data($id);
        $data['active'] = 'Admin';
        $data['title'] = 'User';

        $this->load->view('templates/header', $data);
        $this->load->view('admin/user/edit', $data);
        $this->load->view('templates/footer');
    }

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

    // hapus
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
      public function pegawai()
    {
        $data = $this->model->pegawai();

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
      public function level()
    {
        $data = $this->model->level();

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
}

?>