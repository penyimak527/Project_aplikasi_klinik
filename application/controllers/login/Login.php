<?php
class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();  

        $this->load->model('login/M_login', 'model');
        $this->load->library('session');
    }

    public function index()
    {        if($this->session->userdata('logged_in')) {
            redirect('welcome');
        }
        $this->load->view('login/login');
    }
    public function aksi_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $result = $this->model->login($username, $password);
    // Jika login gagal
    if ($result['status'] === false) {
        echo json_encode([
            'status'    => false,
            'message'   => $result['message'],
            'pindah'    => 'tidak'
        ]);
        return;
    }

    // Jika username & password benar
    $user = $result['data'];

    $user_data = [
        'id'        => $user['id'],
        'id_pegawai'  => $user['id_pegawai'],
        'username'  => $user['username'],
        'id_level'  => $user['id_level'],
        'nama_level'  => $user['nama_level'],
        'status'  => $user['status'],
        'logged_in' => true,
    ];

    $this->session->set_userdata($user_data);

    echo json_encode([
        'status'    => true,
        'message'   => "Login Berhasil!",
        'pindah'    => 'iya',
        'data'      => $user,
    ]);
    }
   public function logout(){
        $this->session->unset_userdata('nama');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('login/login');
    }
}
?>