<?php
class M_riwayat_penjualan extends CI_Model{

  function __construct() {
    parent::__construct();
  }
  
  public function get_unified_transactions() {
    $cari = $this->input->post('cari');
    $tanggal_dari = $this->input->post('tanggal_dari');
    $tanggal_sampai = $this->input->post('tanggal_sampai');
    
    $sql_biasa = "
      SELECT id, kode_invoice, nama_customer, tanggal, waktu, total_invoice, 'biasa' as tipe_transaksi 
      FROM apt_transaksi
      WHERE 1=1
    ";
    
    $sql_resep = "
      SELECT id, kode_invoice_registrasi as kode_invoice, nama_pasien as nama_customer, tanggal, waktu, total_invoice, 'resep' as tipe_transaksi 
      FROM apt_transaksi_resep
      WHERE 1=1
    ";

    if ($cari != '') {
        $search_clause_biasa = " AND (kode_invoice LIKE '%$cari%' OR nama_customer LIKE '%$cari%')";
        $search_clause_resep = " AND (kode_invoice_registrasi LIKE '%$cari%' OR nama_pasien LIKE '%$cari%')";
        
        $sql_biasa .= $search_clause_biasa;
        $sql_resep .= $search_clause_resep;
    }
    if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
      $datea = str_replace('/', '-', $tanggal_dari); 
    $dates = str_replace('/', '-', $tanggal_sampai);
    $ndatea = date('Y-m-d', strtotime($datea));
    $ndates = date('Y-m-d', strtotime($dates));
        $tanggald = " AND date(tanggal) >= '$ndatea'  ";
        $tanggals = " AND date(tanggal) <= '$ndates' ";
        // $search_clause_resep = " AND (kode_invoice_registrasi LIKE '%$cari%' OR nama_pasien LIKE '%$cari%')";
        
        $sql_biasa .= $tanggald . $tanggals;
        $sql_resep .= $tanggald . $tanggals;
    }
    
    $final_sql = "($sql_biasa) UNION ALL ($sql_resep) ORDER BY tanggal DESC, waktu DESC";
    
    return $this->db->query($final_sql)->result_array();
  }

  public function get_detail_transaksi($tipe, $id) {
    if ($tipe == 'resep') {
        $header = $this->db->select("*, nama_pasien as nama_customer, kode_invoice_registrasi as kode_invoice")
                           ->get_where('apt_transaksi_resep', ['id' => $id])->row_array();
        
        $details = $this->db->get_where('apt_transaksi_resep_obat', ['id_transaksi_resep' => $id])->result_array();
        $racikan = $this->db->get_where('apt_transaksi_resep_racikan', ['id_transaksi_resep' => $id])->result_array();

        foreach($racikan as $key => $r) {
            $racikan[$key]['detail'] = $this->db->get_where('apt_transaksi_resep_racikan_detail', ['id_transaksi_resep_racikan' => $r['id']])->result_array();
        }
        return ['result' => true, 'header' => $header, 'details' => $details, 'racikan' => $racikan];
    } else {
        $header = $this->db->select("*") 
                           ->get_where('apt_transaksi', ['id' => $id])->row_array();
        $details = $this->db->get_where('apt_transaksi_detail', ['id_transaksi' => $id])->result_array(); 
        return ['result' => true, 'header' => $header, 'details' => $details, 'racikan' => []];
    }
  }

  public function get_data_for_cetak($tipe, $id) {
    if ($tipe == 'resep') {
      $pembayaran = $this->db->select("kode_invoice_registrasi as kode_invoice, DATE_FORMAT(tanggal, '%d-%m-%Y') as tanggal, waktu, nama_pasien as nama_customer, nama_dokter, total_invoice, metode_pembayaran, bayar, kembali")->get_where('apt_transaksi_resep', ['id' => $id])->row_array();
      $resep = $this->db->select("nama_barang, jumlah, satuan_barang, harga, laba, sub_total_harga")->get_where('apt_transaksi_resep_obat', ['id_transaksi_resep' => $id])->result_array();
      $racikan = $this->db->select("nama_racikan, jumlah, sub_total_harga, sub_total_laba")->get_where('apt_transaksi_resep_racikan', ['id_transaksi_resep' => $id])->result_array();
      return ['pembayaran' => $pembayaran, 'resep' => $resep, 'racikan' => $racikan];
    } else {
      $pembayaran = $this->db->select("kode_invoice, nama_customer, DATE_FORMAT(tanggal, '%d-%m-%Y') as tanggal, waktu, total_invoice, metode_pembayaran, bayar, kembali")->get_where('apt_transaksi', ['id' => $id])->row_array();
      $resep = $this->db->select("nama_barang, jumlah, satuan_barang, harga, laba, sub_total_harga")->get_where('apt_transaksi_detail', ['id_transaksi' => $id])->result_array();
      return ['pembayaran' => $pembayaran, 'resep' => $resep, 'racikan' => []];
    }
  }
}