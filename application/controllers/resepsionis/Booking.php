<?php
class Booking extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
          if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
        $this->load->model('resepsionis/m_booking', 'model');
    }

    public function index()
    {
        $data['active'] = 'Resepsionis';
        $data['title'] = 'Booking';

        $this->load->view('templates/header', $data);
        $this->load->view('resepsionis/booking', $data);
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

    public function view_tambah()
    {
        // $data['pasien'] = $this->model->pasien();
        $data['active'] = 'Resepsionis';
        $data['title'] = 'Booking';

        $this->load->view('templates/header', $data);
        $this->load->view('resepsionis/booking/tambah', $data);
        $this->load->view('templates/footer');
    }

    public function view_edit($id)
    {
        $data['active'] = 'Resepsionis';
        $data['title'] = 'Booking';
        $data['row'] = $this->model->row_data($id);

        $this->load->view('templates/header', $data);
        $this->load->view('resepsionis/booking/edit', $data);
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
            $this->form_validation->set_rules('nama_pasien', 'Nama Pasien', 'required');
    $this->form_validation->set_rules('nik', 'NIK', 'required|numeric|min_length[16]|max_length[16]');
      if ($this->form_validation->run() == FALSE) {
        // Kirim error ke AJAX
        $response = [
            'status' => false,
            'message'  => validation_errors()
        ];
    } else {
        // Jika valid, baru simpan ke model
        $response = $this->model->tambah();
    }

        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function kirimstatus()
    {
        $response = $this->model->kirimsta();

        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function test()
    {
        $timestamp = time();
        $jam = date('H:i');
        $currentDate = gmdate('dmY', $timestamp);
        $kode_format = 'KB' . $currentDate . '-' . str_pad(1, 4, '0', STR_PAD_LEFT);
        echo $kode_format;
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

    public function dokter()
    {
        $response = $this->model->dokter();
        echo json_encode($response);
    }

    // nama pasisn
    public function pasien()
    {
        $response = $this->model->pasien();
        echo json_encode([
            'status' => true,
            'data' => $response
        ]);
    }
    public function pilih_filter()
    {
        $response = $this->model->filter();
        echo json_encode($response);
    }
}
?>