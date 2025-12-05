<?php
class Grup_hak_akses extends CI_Controller{
      function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
      if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
    $this->load->model('admin/m_grup_hak_akses', 'm_grup_hak_akses');    
  }
  public function index()
    {
        
        $data['title'] = 'Grup Hak Akses';
        $this->load->view('templates/header', $data);
        $this->load->view('admin/hak_akses_grup', $data);
        $this->load->view('templates/footer');
    }

    public function result_data()
    {
        $cari = $this->input->post('cari');
        $data_grup_hak_akses = $this->m_grup_hak_akses->get_data_grup($cari);

        $response = [];
        if ($data_grup_hak_akses) {
            $response['result'] = true;
            $response['data'] = $data_grup_hak_akses;
        } else {
            $response['result'] = false;
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function view_tambah()
    {
        $data['title'] = 'Grup Hak Akses';
        $this->load->view('templates/header', $data);
        $this->load->view('admin/grup_hak_akses/tambah', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_aksi()
    {
        $data = ['nama_grup_hak_akses' => $this->input->post('nama_grup')];
        $simpan = $this->m_grup_hak_akses->insert_grup($data);

        $response = [];
        if ($simpan) {
            $response['status'] = true;
            $response['message'] = 'Data berhasil disimpan.';
        } else {
            $response['status'] = false;
            $response['message'] = 'Gagal menyimpan data atau data sudah ada.';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function view_edit($id)
    {
        $data['title'] = 'Grup Hak Akses';
        $data['row'] = $this->m_grup_hak_akses->get_grup_by_id($id);
        $this->load->view('templates/header', $data);
        $this->load->view('admin/grup_hak_akses/edit', $data);
        $this->load->view('templates/footer');
    }

    public function edit_aksi()
    {
        $id = $this->input->post('id');
        $data = ['nama_grup_hak_akses' => $this->input->post('nama_grup')];
        $update = $this->m_grup_hak_akses->update_grup($id, $data);

        $response = [];
        if ($update) {
            $response['status'] = true;
            $response['message'] = 'Data berhasil diperbarui';
        } else {
            $response['status'] = false;
            $response['message'] = 'Gagal memperbarui data';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        $delete = $this->m_grup_hak_akses->delete_grup($id);

        $response = [];
        if ($delete) {
            $response['status'] = true;
            $response['message'] = 'Data berhasil dihapus';
        } else {
            $response['status'] = false;
            $response['message'] = 'Gagal menghapus data';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

}?>