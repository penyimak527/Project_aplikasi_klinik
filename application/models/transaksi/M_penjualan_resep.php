<?php
class M_penjualan_resep extends CI_Model{

  function __construct() {
    parent::__construct();
  }
  
  public function get_resep_belum_lunas() {
    $cari = $this->input->post('cari');
    $sql = "
      SELECT a.id, a.kode_invoice, a.nama_pasien as nama_customer, a.nama_dokter, a.tanggal 
      FROM pol_resep a
      WHERE 
        NOT EXISTS (SELECT 1 FROM apt_transaksi_resep b WHERE a.kode_invoice = b.kode_invoice_registrasi)
        AND (
          EXISTS (SELECT 1 FROM pol_resep_obat WHERE id_pol_resep = a.id)
          OR
          EXISTS (SELECT 1 FROM pol_resep_racikan WHERE id_pol_resep = a.id)
        )
    ";
    if ($cari != '') {
        $sql .= " AND (a.kode_invoice LIKE '%$cari%' OR a.nama_pasien LIKE '%$cari%' OR a.nama_dokter LIKE '%$cari%')";
    }
    $sql .= " ORDER BY a.id DESC";
    
    $resep_list = $this->db->query($sql)->result_array();

    foreach ($resep_list as $key => $resep) {
        $row_obat = $this->db->select_sum('sub_total_harga')->where('id_pol_resep', $resep['id'])->get('pol_resep_obat')->row();
        $total_obat = $row_obat ? $row_obat->sub_total_harga : 0;
        
        $total_racikan_akurat = 0;
        $racikan_list = $this->get_resep_racikan($resep['id']);
        if($racikan_list){
            foreach($racikan_list as $racikan) {
                $total_racikan_akurat += floatval($racikan['sub_total_harga']);
            }
        }
        
        $resep_list[$key]['total_harga'] = (floatval($total_obat) + $total_racikan_akurat);
    }
    return $resep_list;
  }

  public function get_resep_for_processing($id_pol_resep) {
    $data['resep_header'] = $this->get_resep_header($id_pol_resep);
    if (!$data['resep_header']) { return null; }
    $data['resep_obat'] = $this->get_resep_obat($id_pol_resep);
    $data['resep_racikan'] = $this->get_resep_racikan($id_pol_resep);

    $recalculated_total = 0;
    if($data['resep_obat']){
        foreach($data['resep_obat'] as $obat) {
            $recalculated_total += floatval($obat['sub_total_harga']);
        }
    }
    if($data['resep_racikan']){
        foreach($data['resep_racikan'] as $racikan) {
            $recalculated_total += floatval($racikan['sub_total_harga']);
        }
    }

    $data['resep_header']['total_harga'] = $recalculated_total;
    return $data;
  }

  public function get_resep_header($id) {
    if (empty($id)) return null;
    return $this->db->get_where('pol_resep', ['id' => $id])->row_array();
  }

  public function get_resep_obat($id_pol_resep) {
    $this->db->select('
    pro.*,
    bd.id_barang as id_barang_utama,
    sb.id as id_satuan_barang,
    sb.nama_satuan as satuan_barang,
    bd.urutan_satuan
');
$this->db->from('pol_resep_obat pro');
$this->db->join('apt_barang_detail bd', 'bd.id = pro.id_barang_detail', 'left');
$this->db->join('apt_satuan_barang sb', 'sb.id = bd.id_satuan_barang', 'left');
$this->db->where('pro.id_pol_resep', $id_pol_resep);
    return $this->db->get()->result_array();
  }

  public function get_resep_racikan($id_pol_resep) {
    $racikan_result = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $id_pol_resep])->result_array();
    
    foreach($racikan_result as $key => $racikan) {
      $detail = $this->db->get_where('pol_resep_racikan_detail', ['id_pol_resep_racikan' => $racikan['id']])->result_array();
      
      $harga_per_unit_racikan = 0;
      foreach($detail as $bahan_key => $bahan) {
          $jumlah_bahan = floatval($bahan['jumlah']);
          $jumlah_efektif = $jumlah_bahan;

          if ($jumlah_bahan >= 0.5 && $jumlah_bahan < 1.0) {
              $jumlah_efektif = 1.0;
          }
          
          $barang_detail = $this->db->select('id_barang')->get_where('apt_barang_detail', ['id' => $bahan['id_barang_detail']])->row();
          $detail[$bahan_key]['id_barang_utama'] = $barang_detail ? $barang_detail->id_barang : null;

          $detail[$bahan_key]['jumlah_efektif'] = $jumlah_efektif;
          $subtotal_bahan_akurat = $jumlah_efektif * floatval($bahan['harga']);
          $detail[$bahan_key]['sub_total_harga'] = $subtotal_bahan_akurat; 
          $harga_per_unit_racikan += $subtotal_bahan_akurat;
      }

      $racikan_result[$key]['detail'] = $detail;
      $sub_total_akurat = $harga_per_unit_racikan * floatval($racikan['jumlah']);
      $racikan_result[$key]['sub_total_harga'] = $sub_total_akurat;
    }
    return $racikan_result;
  }
  
  public function proses_pembayaran() {
    $id_pol_resep = $this->input->post('id_pol_resep');
    
    if (empty($id_pol_resep)) {
        return ['status' => false, 'message' => "Gagal memproses. ID Resep tidak terkirim."];
    }

    $resep_header = $this->get_resep_header($id_pol_resep);
    if (!$resep_header) {
      return ['status' => false, 'message' => "Gagal memproses. Data resep dengan ID ($id_pol_resep) tidak ditemukan di database."];
    }
    
    $obat_list = $this->get_resep_obat($id_pol_resep);
    $racikan_list = $this->get_resep_racikan($id_pol_resep);
    
    $items_to_check = [];
    if($obat_list){
        foreach($obat_list as $obat) { 
            if(empty($obat['id_barang_detail'])) continue;
            $items_to_check[] = ['id_barang_detail' => $obat['id_barang_detail'],'nama_barang' => $obat['nama_barang'],'jumlah_dibutuhkan' => floatval($obat['jumlah'])]; 
        }
    }
    if($racikan_list){
        foreach($racikan_list as $racikan) {
            if(!empty($racikan['detail'])){
                foreach($racikan['detail'] as $detail) { 
                    if(empty($detail['id_barang_detail'])) continue;
                    $items_to_check[] = ['id_barang_detail' => $detail['id_barang_detail'],'nama_barang' => $detail['nama_barang'],'jumlah_dibutuhkan' => floatval($detail['jumlah_efektif']) * floatval($racikan['jumlah'])]; 
                }
            }
        }
    }
    
    foreach($items_to_check as $item) {
        $stok_db_query = $this->db->select('stok')->get_where('apt_stok', ['id_barang_detail' => $item['id_barang_detail']]);
        if ($stok_db_query->num_rows() > 0) {
            $stok_saat_ini = floatval($stok_db_query->row()->stok);
            if ($stok_saat_ini < ($item['jumlah_dibutuhkan'] - 0.0001)) {
                $error_msg = "Stok untuk '{$item['nama_barang']}' tidak mencukupi! Sisa stok: " . number_format($stok_saat_ini, 2, ',', '.') . ", dibutuhkan: " . number_format($item['jumlah_dibutuhkan'], 2, ',', '.') . ".";
                return ['status' => false, 'message' => $error_msg];
            }
        } else { 
            return ['status' => false, 'message' => "Data stok untuk '{$item['nama_barang']}' tidak ditemukan di sistem."]; 
        }
    }

    $this->db->trans_begin();
    try {
      $transaksi_resep = [
        'kode_invoice_registrasi' => $resep_header['kode_invoice'],
        'id_pasian' => $resep_header['id_pasien'], 
        'nik' => $resep_header['nik'], 
        'nama_pasien' => $resep_header['nama_pasien'], 
        'id_dokter' => $resep_header['id_dokter'], 
        'nama_dokter' => $resep_header['nama_dokter'],
        'total_invoice' => $this->input->post('total_tagihan'), 
        'metode_pembayaran' => $this->input->post('metode_pembayaran'),
        'bank' => $this->input->post('bank'),
        'bayar' => $this->input->post('jumlah_bayar'),
        'kembali' => $this->input->post('kembali'),
        'tanggal' => date('Y-m-d'), 
        'waktu' => date('H:i:s')
      ];
      
      if($this->db->field_exists('nama_customer', 'apt_transaksi_resep')) {
         $transaksi_resep['nama_customer'] = $resep_header['nama_pasien'];
         unset($transaksi_resep['nama_pasien']);
      }

      $this->db->insert('apt_transaksi_resep', $transaksi_resep);
      $id_transaksi_resep = $this->db->insert_id();
   
      if(!empty($obat_list)) {
        foreach($obat_list as $obat) {
          $transaksi_obat = $obat; 
          unset($transaksi_obat['id'], $transaksi_obat['id_pol_resep'], $transaksi_obat['id_barang_utama']);
          $transaksi_obat['id_transaksi_resep'] = $id_transaksi_resep;
          $this->db->insert('apt_transaksi_resep_obat', $transaksi_obat);
          
          if(!empty($obat['id_barang_utama'])) {
             $this->_update_stock_hierarchical($obat['id_barang_utama'], $obat['id_barang_detail'], floatval($obat['jumlah']));
          }
        }
      }

      if(!empty($racikan_list)) {
        foreach($racikan_list as $racikan) {
          $transaksi_racikan = $racikan; 
          unset($transaksi_racikan['id'], $transaksi_racikan['id_pol_resep'], $transaksi_racikan['detail']);
          $transaksi_racikan['id_transaksi_resep'] = $id_transaksi_resep;
          $this->db->insert('apt_transaksi_resep_racikan', $transaksi_racikan);
          $id_transaksi_racikan = $this->db->insert_id();
          
          if(!empty($racikan['detail'])){
              foreach($racikan['detail'] as $racikan_detail) {
                $transaksi_racikan_detail = $racikan_detail; 
                unset($transaksi_racikan_detail['id'], $transaksi_racikan_detail['id_pol_resep_racikan'], $transaksi_racikan_detail['jumlah_efektif'], $transaksi_racikan_detail['id_barang_utama']);
                $transaksi_racikan_detail['id_transaksi_resep_racikan'] = $id_transaksi_racikan;
                $this->db->insert('apt_transaksi_resep_racikan_detail', $transaksi_racikan_detail);
                
                $jumlah_pengurang = floatval($racikan_detail['jumlah_efektif']) * floatval($racikan['jumlah']);            
                if(!empty($racikan_detail['id_barang_utama'])) {
                    $this->_update_stock_hierarchical($racikan_detail['id_barang_utama'], $racikan_detail['id_barang_detail'], $jumlah_pengurang);
                }
              }
          }
        }
      }
    } catch (Exception $e) { 
        $this->db->trans_rollback();
        return ['status' => false, 'message' => "System Error: " . $e->getMessage()];
    }

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return ['status' => false, 'message' => "Transaksi Gagal Diproses (DB Error)"];
    } else {
        $this->db->trans_commit();
        return ['status' => true, 'message' => "Transaksi Berhasil", 'id_transaksi' => $id_transaksi_resep];
    }
  }

  private function _update_stock_hierarchical($id_barang, $id_barang_detail_terjual, $jumlah_terjual) {
    if(empty($id_barang)) return; 

    $levels = $this->db->from('apt_barang_detail')
                       ->where('id_barang', $id_barang)
                       ->order_by('urutan_satuan', 'ASC')
                       ->get()->result_array();

    if (empty($levels)) {
        throw new Exception("Struktur satuan barang (ID Barang: $id_barang) tidak ditemukan. Cek Master Data Barang.");
    }

    $total_terjual_satuan_terkecil = floatval($jumlah_terjual);
    $urutan_satuan_terjual = 0;
    foreach($levels as $level) {
        if ($level['id'] == $id_barang_detail_terjual) {
            $urutan_satuan_terjual = (int)$level['urutan_satuan'];
            break;
        }
    }

    foreach($levels as $level) {
        if ((int)$level['urutan_satuan'] > $urutan_satuan_terjual) {
            $total_terjual_satuan_terkecil *= (int)$level['isi_satuan_turunan'];
        }
    }

    $satuan_terkecil = end($levels);
    
    $row_stok = $this->db->get_where('apt_stok', ['id_barang_detail' => $satuan_terkecil['id']])->row();
    if(!$row_stok) {
        throw new Exception("Data stok master tidak ditemukan untuk detail barang ID: " . $satuan_terkecil['id']);
    }
    
    $stok_terkecil_sekarang = (float)$row_stok->stok;
    $stok_terkecil_baru = $stok_terkecil_sekarang - $total_terjual_satuan_terkecil;
    
    if ($stok_terkecil_baru < -0.0001) {
        throw new Exception("Stok konversi untuk barang " . $satuan_terkecil['nama_barang'] . " minus. Cek stok satuan terkecil.");
    }

    $stok_sisa = $stok_terkecil_baru;
    $reversed_levels = array_reverse($levels); 

    foreach ($reversed_levels as $level) {
        $this->db->where('id_barang_detail', $level['id']);
        $this->db->update('apt_stok', ['stok' => (string)$stok_sisa]);
        
        if ((int)$level['urutan_satuan'] > 1) {
            $isi = (int)$level['isi_satuan_turunan'];
            if($isi > 0) {
                $stok_sisa = $stok_sisa / $isi;
            }
        }
    }
    return true;
  }

public function get_data_for_cetak($id_transaksi_resep) {
    $this->db->select("
        kode_invoice_registrasi as kode_invoice, 
        DATE_FORMAT(tanggal, '%d-%m-%Y') as tanggal, 
        waktu, 
        nama_pasien as nama_customer, 
        nama_dokter, 
        total_invoice, 
        metode_pembayaran, 
        bayar, 
        kembali
    ");
    
    $pembayaran = $this->db->get_where('apt_transaksi_resep', ['id' => $id_transaksi_resep])->row_array();
    
    if(!$pembayaran) {
        return null;
    }

    $resep = $this->db->select("nama_barang, jumlah, satuan_barang, harga, laba")->get_where('apt_transaksi_resep_obat', ['id_transaksi_resep' => $id_transaksi_resep])->result_array();
    $racikan = $this->db->select("nama_racikan, jumlah, sub_total_harga, sub_total_laba")->get_where('apt_transaksi_resep_racikan', ['id_transaksi_resep' => $id_transaksi_resep])->result_array();
    return ['pembayaran' => $pembayaran, 'resep' => $resep, 'racikan' => $racikan, 'tindakan' => []];
  }
}