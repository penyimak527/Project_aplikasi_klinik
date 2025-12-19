<?php
class Riwayat_penjualan extends CI_Controller{
  function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('transaksi/M_riwayat_penjualan','model');
    if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
  }

  public function index(){
    $data['active'] = 'riwayat_penjualan';
    $data['title'] = 'Riwayat Penjualan';
    $this->load->view('templates/header', $data);
    $this->load->view('transaksi/riwayat_penjualan', $data);
    $this->load->view('templates/footer');
  }

  public function result_data(){
    $data = $this->model->get_unified_transactions(); 
    $response = $data ? ['result' => true, 'data' => $data] : ['result' => false, 'message' => 'Data Kosong'];
		$this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }
  
  public function get_detail_transaksi_ajax($tipe, $id){
    $detail = $this->model->get_detail_transaksi($tipe, $id);
    header('Content-Type: application/json');
    echo json_encode($detail);
  }

  public function cetak_struk($tipe, $id){
    $data['title'] = 'Cetak Struk';
    $data['data'] = $this->model->get_data_for_cetak($tipe, $id);

    if ($tipe == 'resep') {
      $this->load->view('transaksi/penjualan_resep/struk', $data);
    } else {
      $this->load->view('transaksi/penjualan/struk_penjualan', $data);
    }
  }

  public function cetak_kwitansi($tipe, $id){
    $data['title'] = 'Cetak Kwitansi';
    $data['data'] = $this->model->get_data_for_cetak($tipe, $id);
    $this->load->view('transaksi/penjualan_resep/kwitansi', $data);
  }
}