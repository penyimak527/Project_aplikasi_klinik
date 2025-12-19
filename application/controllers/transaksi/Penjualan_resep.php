<?php
class Penjualan_resep extends CI_Controller{
  function __construct(){
		parent::__construct();
    date_default_timezone_set('Asia/Jakarta');
		$this->load->model('transaksi/M_penjualan_resep','model');
    if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login/login');
        }
  }

  public function index(){
    $data['active'] = 'penjualan_resep';
    $data['title'] = 'Penjualan Resep';
    $this->load->view('templates/header', $data);
    $this->load->view('transaksi/penjualan_resep', $data);
    $this->load->view('templates/footer');
  }

  public function proses($id_pol_resep){
    $processed_data = $this->model->get_resep_for_processing($id_pol_resep);

    if(!$processed_data){
      redirect('transaksi/penjualan_resep');
    }

    $data['active'] = 'penjualan_resep';
    $data['title'] = 'Proses Pembayaran';
    $data = array_merge($data, $processed_data);

    $this->load->view('templates/header', $data);
    $this->load->view('transaksi/penjualan_resep/proses', $data);
    $this->load->view('templates/footer');
  }

  public function result_data(){
    $data = $this->model->get_resep_belum_lunas();
    $response = $data ? ['result' => true, 'data' => $data] : ['result' => false, 'message' => 'Data Kosong'];
		$this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }
  
  public function proses_pembayaran(){
    $response = $this->model->proses_pembayaran();
		$this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
        ->_display();
        exit;
  }
  
  public function cetak_kwitansi($id_transaksi_resep){
    $data['title'] = 'Kwitansi Pembayaran';
    $data['data'] = $this->model->get_data_for_cetak($id_transaksi_resep);
    $this->load->view('transaksi/penjualan_resep/kwitansi', $data); 
  }

  public function cetak_struk($id_transaksi_resep){
    $data['title'] = 'Struk Pembayaran';
    $data['data'] = $this->model->get_data_for_cetak($id_transaksi_resep);
    $this->load->view('transaksi/penjualan_resep/struk', $data); 
  }
}