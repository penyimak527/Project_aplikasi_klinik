<?php
class Kecantikan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
          if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
        $this->load->model('poli/m_kecantikan', 'model');
    }

    public function index()
    {

        $data['active'] = 'Poli';
        $data['title'] = 'Kecantikan';

        $this->load->view('templates/header', $data);
        $this->load->view('poli/kecantikan', $data);
        $this->load->view('templates/footer');
    }

    public function view_proses($kode_invoice)
    {

        $data['row'] = $this->model->row_datakode($kode_invoice);
        $data['racikan'] = $this->model->racikan();
        $data['active'] = 'Poli';
        $data['title'] = ' Poli Kecantikan';

        $this->load->view('templates/header', $data);
        $this->load->view('poli/kecantikan/proses', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_proses()
    {
        $response = $this->model->tambah_proses();
        $this
            ->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
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
    public function tindakan()
    {
        $data = $this->model->tindakan();
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }

    public function diagnosa()
    {
        $data = $this->model->diagnosa();
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }
    public function j_satuan()
    {
        $data = $this->model->satuan_b();
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }
    public function obat()
    {
        $data = $this->model->obat();
        echo json_encode([
            'status' => true,
            'data' => $data
        ]);
    }
 public function get_satuan_by_barang($id_barang)
    {
        $data = $this->model->get_all_satuan_by_barang($id_barang);
        
        if ($data) {
            echo json_encode([
                'status' => true,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'data' => []
            ]);
        }
    }

    public function get_satuan_detail($id_barang_detail)
    {
        $data = $this->model->get_satuan_by_id($id_barang_detail);
        
        if ($data) {
            echo json_encode([
                'status' => true,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'data' => []
            ]);
        }
    }
}

?>



