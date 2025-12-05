<?php
class Hak_akses extends CI_Controller{
      function __construct()
  {
    parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
      if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
    $this->load->model('admin/m_hak_akses', 'm_hak_akses');
    $this->load->model('admin/m_level', 'm_level');
  }
  public function index()
  {
    $data['active'] = 'Admin';
    $data['title'] = 'Hak Akses';

    $this->load->view('templates/header', $data);
    $this->load->view('admin/hak_akses', $data);
    $this->load->view('templates/footer');
  }
  public function view_tambah()
  {
    $data['active'] = 'Admin';
    $data['title'] = 'Hak Akses';
    $data['grup'] = $this->m_hak_akses->get_grup_hak_akses();
    $this->load->view('templates/header', $data);
    $this->load->view('admin/hak_akses/tambah', $data);
    $this->load->view('templates/footer');
  }
   public function result_data()
    {
        $cari = $this->input->post('cari');
        $data = $this->m_hak_akses->get_data_hak_akses($cari);
        header('Content-Type: application/json');
        echo json_encode(['result' => !empty($data), 'data' => $data]);
    }
     public function tambah_aksi()
    {
        $data = [
            'nama_hak_akses' => $this->input->post('nama_hak_akses'),
            'link' => $this->input->post('link'),
            'id_grup_hak_akses' => $this->input->post('id_grup_hak_akses')
        ];
        $simpan = $this->m_hak_akses->insert_hak_akses($data);
        header('Content-Type: application/json');
        echo json_encode(['status' => $simpan, 'message' => $simpan ? 'Berhasil' : 'Gagal']);
    }
      public function view_edit($id)
    {
        $data['title'] = 'Hak Akses';
        $data['row'] = $this->m_hak_akses->get_hak_akses_by_id($id);
        $data['grup'] = $this->m_hak_akses->get_grup_hak_akses();
        $this->load->view('templates/header', $data);
        $this->load->view('admin/hak_akses/edit', $data);
        $this->load->view('templates/footer');
    }
public function edit_aksi()
    {
        $id = $this->input->post('id');
        $data = [
            'nama_hak_akses' => $this->input->post('nama_hak_akses'),
            'link' => $this->input->post('link'),
            'id_grup_hak_akses' => $this->input->post('id_grup_hak_akses')
        ];
        $update = $this->m_hak_akses->update_hak_akses($id, $data);
        header('Content-Type: application/json');
        echo json_encode(['status' => $update, 'message' => $update ? 'Berhasil' : 'Gagal']);
    }

    public function hapus()
    {
        $id = $this->input->post('id');
        $delete = $this->m_hak_akses->delete_hak_akses($id);
        header('Content-Type: application/json');
        echo json_encode(['status' => $delete, 'message' => $delete ? 'Berhasil' : 'Gagal']);
    }
    public function setting_aksi()
    {
        $id_level = $this->input->post('id_level');
        $hak_akses = $this->input->post('hak_akses');

        if (empty($hak_akses)) {
            echo json_encode(['status' => false, 'message' => 'Pilih minimal satu akses.']);
            return;
        }

        $update = $this->m_hak_akses->update_akses_level($id_level, $hak_akses);

        header('Content-Type: application/json');
        echo json_encode(['status' => $update, 'message' => $update ? 'Hak akses level diperbarui.' : 'Gagal.']);
    }
     public function setting($id_level)
    {
        $data['title'] = 'Setting Hak Akses';
        $data['level'] = $this->m_level->get_level_by_id($id_level);
        $data['grouped_akses'] = $this->m_hak_akses->get_all_hak_akses_grouped();
        $data['current_akses'] = $this->m_hak_akses->get_akses_by_level($id_level);
        
        $this->load->view('templates/header', $data);
        $this->load->view('admin/hak_akses/setting', $data);
        $this->load->view('templates/footer');
    }
}?>