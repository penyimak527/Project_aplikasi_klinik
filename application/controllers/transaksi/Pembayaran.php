<?php
class Pembayaran extends CI_Controller{
      function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('transaksi/m_transaksi','model');
  }
    public function index(){
    $data['active'] = 'Transaksi';
    $data['title'] = 'Pembayaran';

    $this->load->view('templates/header', $data);
    $this->load->view('transaksi/pembayaran', $data);
    $this->load->view('templates/footer');
    }
    public function result_Data(){
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
    public function result_Dataa(){
    $data = $this->model->result_dataa();
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
    public function tambah(){
        $response = $this->model->tambah();

		$this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
    }
    public function riwayat(){
    $data['active'] = 'Transaksi';
    $data['title'] = 'Riwayat Pembayaran';

    $this->load->view('templates/header', $data);
    $this->load->view('transaksi/riwayat_pembayaran', $data);
    $this->load->view('templates/footer');
    }

        public function cetak_struk($kode_invoice)
    {
        $data['title'] = 'Struk Pembayaran';
        $data['data'] = $this->model->get_full_detail_by_invoice($kode_invoice);
        $this->load->view('transaksi/cetak/struk', $data);
    }

    public function cetak_kwitansi($kode_invoice)
    {
        $data['title'] = 'Kwitansi Pembayaran';
        $data['data'] = $this->model->get_full_detail_by_invoice($kode_invoice);
        $this->load->view('transaksi/cetak/kwitansi', $data);
    }
}
?>