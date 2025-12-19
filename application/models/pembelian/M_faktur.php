<?php
class M_Faktur extends CI_Model{

  function __construct() {
    parent::__construct();
    $this->load->model('gudang/M_Stok', 'm_stok');
  }

private function kelola_stok_berjenjang($id_barang, $jumlah_utama, $aksi, $data_harga = []) {
    $semua_satuan = $this->db->where('id_barang', $id_barang)
                              ->order_by('urutan_satuan', 'ASC')
                              ->get('apt_barang_detail')
                              ->result_array();

    if (empty($semua_satuan)) return;

    $jumlah_kumulatif = abs($this->_clean_and_convert_to_float($jumlah_utama));
    $harga_awal_kumulatif = 0;
    $harga_jual_kumulatif = 0;
    $kadaluarsa = '';

    if ($aksi == 'tambah') {
        $harga_awal_kumulatif = $this->_clean_and_convert_to_float($data_harga['harga_awal']);
        $harga_jual_kumulatif = $this->_clean_and_convert_to_float($data_harga['harga_jual']);
        $kadaluarsa = $data_harga['kadaluarsa'];
    }

    foreach ($semua_satuan as $index => $satuan) {

        if ($index > 0) {
            $konversi = floatval(trim($satuan['isi_satuan_turunan'])); 
            if ($konversi > 1) { 
                $jumlah_kumulatif *= $konversi;
                $harga_awal_kumulatif /= $konversi;
                $harga_jual_kumulatif /= $konversi;
            }
        }
        
        if ($aksi == 'tambah') {
            $laba_sekarang = $harga_jual_kumulatif - $harga_awal_kumulatif;
            $data_harga_sekarang = [
                'harga_awal' => $this->_convert_float_to_db_string(ceil($harga_awal_kumulatif)),
                'harga_jual' => $this->_convert_float_to_db_string(ceil($harga_jual_kumulatif)),
                'laba'       => $this->_convert_float_to_db_string(ceil($laba_sekarang)),
                'kadaluarsa' => $kadaluarsa
            ];
            $this->m_stok->update_stok_from_faktur(
                (int)$satuan['id_barang'],
                (int)$satuan['id'],
                $jumlah_kumulatif,
                $data_harga_sekarang['harga_awal'],
                $data_harga_sekarang['harga_jual'],
                $data_harga_sekarang['laba'],
                $data_harga_sekarang['kadaluarsa']
            );

        } elseif ($aksi == 'kurang') {
            $this->m_stok->update_stok_on_delete(
                (int)$satuan['id_barang'],
                (int)$satuan['id'],
                $jumlah_kumulatif
            );
        }
    }
}

  public function _clean_and_convert_to_float($value) {
      if (empty($value)) return 0.0;
      $cleaned_value = str_replace('.', '', $value);
      $cleaned_value = str_replace(',', '.', $cleaned_value);
      return floatval($cleaned_value);
  }

  public function _convert_float_to_db_string($float_value) {
      return str_replace('.', ',', (string)$float_value);
  }

  public function generate_non_faktur_number() {
      $this->load->helper('string');
      $tanggal = date('dmY');
      $nomor = random_string('numeric', 3);
      return $tanggal . '-' . $nomor;
  }
  
  public function get_all_supplier() {
      $query = $this->db->order_by('nama_supplier', 'ASC')->get('apt_supplier');
      return $query->result_array();
  }

  public function get_all_barang_for_popup($search = '', $limit = 10, $offset = 0) {
      $this->db->select('b.id AS id_barang_utama, b.nama_barang, 
                         bd.id AS id_barang_detail, bd.kode_barang, bd.satuan_barang, bd.urutan_satuan, 
                         bd.id_satuan_barang, 
                         s.harga_awal, s.harga_jual, s.laba, s.kadaluarsa, s.stok'
                       );
      $this->db->from('apt_barang b');
      $this->db->join('apt_barang_detail bd', 'b.id = bd.id_barang', 'left');
      $this->db->join('apt_stok s', 'bd.id = s.id_barang_detail', 'left'); 
      
      if (!empty($search)) {
          $this->db->group_start(); 
          $this->db->like('b.nama_barang', $search);
          $this->db->or_like('bd.kode_barang', $search); 
          $this->db->group_end();
      }
      
      $this->db->where('bd.urutan_satuan', '1');
      $this->db->order_by('b.nama_barang', 'ASC');
      $this->db->order_by('bd.urutan_satuan', 'ASC'); 
      $this->db->limit($limit, $offset); 
      $query = $this->db->get();
      return $query->result_array();
  }

  public function count_all_barang_for_popup($search = '') {
      $this->db->select('COUNT(bd.id) as total_rows'); 
      $this->db->from('apt_barang b');
      $this->db->join('apt_barang_detail bd', 'b.id = bd.id_barang', 'left');
      $this->db->join('apt_stok s', 'bd.id = s.id_barang_detail', 'left'); 
      if (!empty($search)) {
          $this->db->group_start();
          $this->db->like('b.nama_barang', $search);
          $this->db->or_like('bd.kode_barang', $search);
          $this->db->group_end();
      }
      $this->db->where('bd.urutan_satuan', '1');
      $query = $this->db->get();
      return $query->row()->total_rows;
  }

  public function get_barang_detail_by_id_and_urutan($id_barang, $urutan_satuan) {
      $this->db->select('b.id AS id_barang_utama, b.nama_barang, bd.*, s.harga_awal, s.harga_jual, s.laba, s.kadaluarsa, s.stok');
      $this->db->from('apt_barang b');
      $this->db->join('apt_barang_detail bd', 'b.id = bd.id_barang', 'left');
      $this->db->join('apt_stok s', 'bd.id = s.id_barang_detail', 'left');
      $this->db->where('b.id', $id_barang);
      $this->db->where('bd.urutan_satuan', $urutan_satuan); 
      $query = $this->db->get();
      return $query->row_array();
  }

  public function insert_faktur() {
      $this->db->trans_begin();

      $data_faktur = array(
          'id_user' => $this->session->userdata('id_user'),
          'nama_user' => $this->session->userdata('nama_user'),
          'id_supplier' => $this->input->post('id_supplier'),
          'nama_supplier' => $this->input->post('selected_nama_supplier'),
          'no_faktur' => ($this->input->post('pilih_nomer') == 'default') ? $this->input->post('no_faktur_auto') : $this->input->post('no_faktur_manual'),
          'status_bayar' => $this->input->post('status_bayar'),
          'tanggal_bayar' => !empty($this->input->post('tanggal_bayar')) ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tanggal_bayar')))) : null,
          'metode_pembayaran' => $this->input->post('metode_pembayaran'),
          'bank' => $this->input->post('selected_bank_supplier'),
          'bayar' => $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($this->input->post('bayar_dp'))),
          'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tanggal_faktur')))),
          'waktu' => date('H:i:s'),
      );

      $status_diskon = $this->input->post('flexSwitchCheckDefault') == 'on' ? 'ada' : 'tidak ada';
      $data_faktur['status_diskon'] = $status_diskon;
      $jenis_diskon = $this->input->post('jenis_diskon');
      $data_faktur['jenis_diskon'] = $jenis_diskon;

      $diskon_value = 0;
      if ($status_diskon == 'ada') {
          if ($jenis_diskon == 'persen') {
              $diskon_value = $this->_clean_and_convert_to_float($this->input->post('diskon_persen'));
          } else {
              $diskon_value = $this->_clean_and_convert_to_float($this->input->post('diskon_rp'));
          }
      }
      $data_faktur['diskon'] = $this->_convert_float_to_db_string($diskon_value);
      $data_faktur['total_harga'] = $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($this->input->post('total_harga_faktur')));
      $this->db->insert('apt_faktur', $data_faktur);
      $id_faktur = $this->db->insert_id();

      $detail_faktur_batch = array();
      $stok_updates = array();

      $id_barang_arr = $this->input->post('id_barang') ?: [];
      $id_barang_detail_arr = $this->input->post('id_barang_detail') ?: [];
      $nama_barang_arr = $this->input->post('nama_barang') ?: [];
      $id_satuan_barang_arr = $this->input->post('id_satuan_barang') ?: [];
      $satuan_arr = $this->input->post('satuan') ?: [];
      $urutan_satuan_arr = $this->input->post('urutan_satuan') ?: [];
      $jumlah_arr = $this->input->post('jumlah') ?: []; 
      $harga_awal_arr = $this->input->post('harga_awal') ?: [];
      $sub_total_harga_awal_arr = $this->input->post('sub_total_harga_awal') ?: [];
      $harga_jual_arr = $this->input->post('harga_jual') ?: [];
      $laba_arr = $this->input->post('laba') ?: [];
      $kadaluarsa_arr = $this->input->post('kadaluarsa') ?: [];

      if (!empty($id_barang_arr)) {
          foreach ($id_barang_arr as $key => $id_barang) {
              if (isset($id_barang_detail_arr[$key]) && isset($nama_barang_arr[$key]) &&
                  isset($id_satuan_barang_arr[$key]) && isset($satuan_arr[$key]) &&
                  isset($jumlah_arr[$key]) && isset($harga_awal_arr[$key]) && 
                  isset($sub_total_harga_awal_arr[$key]) && isset($harga_jual_arr[$key]) && 
                  isset($laba_arr[$key]) && isset($kadaluarsa_arr[$key])) {

                  $kadaluarsa_db = !empty($kadaluarsa_arr[$key]) ? date('Y-m-d', strtotime(str_replace('/', '-', $kadaluarsa_arr[$key]))) : null;
                  $jumlah_beli_float = $this->_clean_and_convert_to_float($jumlah_arr[$key]);
                  $harga_awal_float = $this->_clean_and_convert_to_float($harga_awal_arr[$key]);
                  $harga_jual_float = $this->_clean_and_convert_to_float($harga_jual_arr[$key]);
                  $laba_float = $this->_clean_and_convert_to_float($laba_arr[$key]);
                  $id_barang_detail_int = (int)$this->_clean_and_convert_to_float($id_barang_detail_arr[$key]);
                  $id_barang_int = (int)$id_barang;

                  $detail_faktur_batch[] = array(
                      'id_faktur'             => $id_faktur,
                      'id_barang'             => $id_barang_int,
                      'id_barang_detail'      => $id_barang_detail_int,
                      'nama_barang'           => $nama_barang_arr[$key],
                      'id_satuan_barang'      => (int)$this->_clean_and_convert_to_float($id_satuan_barang_arr[$key]), 
                      'satuan_barang'         => $satuan_arr[$key],
                      'urutan_satuan'         => $this->_clean_and_convert_to_float($urutan_satuan_arr[$key]),
                      'jumlah'                => $this->_convert_float_to_db_string($jumlah_beli_float),
                      'harga_awal'            => $this->_convert_float_to_db_string($harga_awal_float),
                      'sub_total_harga_awal'  => $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($sub_total_harga_awal_arr[$key])),
                      'harga_jual'            => $this->_convert_float_to_db_string($harga_jual_float),
                      'laba'                  => $this->_convert_float_to_db_string($laba_float),
                      'kadaluarsa'            => $kadaluarsa_db
                  );

                  $stok_updates[] = array(
                      'id_barang'         => $id_barang_int,
                      'id_barang_detail'  => $id_barang_detail_int,
                      'jumlah_beli'       => $jumlah_beli_float,
                      'harga_awal'        => $harga_awal_float,
                      'harga_jual'        => $harga_jual_float,
                      'laba'              => $laba_float,
                      'kadaluarsa'        => $kadaluarsa_db
                  );
              }
          }
      }

      if (!empty($detail_faktur_batch)) {
          $this->db->insert_batch('apt_faktur_detail', $detail_faktur_batch);
      }

       foreach ($stok_updates as $stok_data) {
          $this->kelola_stok_berjenjang(
              $stok_data['id_barang'],
              $stok_data['jumlah_beli'],
              'tambah',
              $stok_data
          );
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          return array(
              'status' => false,
              'message' => "Data Gagal Disimpan: " . $this->db->error()['message']
          );
      } else {
          return array(
              'status' => true,
              'message' => "Data Berhasil Disimpan",
              'id_faktur' => $id_faktur
          );
      }
  }

  public function edit_faktur_data() {
      $this->db->trans_begin();

      $id_faktur = $this->input->post('id_faktur');
      $old_faktur_details = $this->get_detail_faktur($id_faktur);

      foreach ($old_faktur_details as $old_detail) {
          $this->kelola_stok_berjenjang(
              (int)$old_detail['id_barang'],
              $old_detail['jumlah'],
              'kurang' 
          );
      }

      $status_diskon = $this->input->post('flexSwitchCheckDefault') == 'on' ? 'ada' : 'tidak ada';
      $jenis_diskon = $this->input->post('jenis_diskon');
      $diskon_value = 0;
      if ($status_diskon == 'ada') {
          if ($jenis_diskon == 'persen') {
              $diskon_value = $this->_clean_and_convert_to_float($this->input->post('diskon_persen'));
          } else {
              $diskon_value = $this->_clean_and_convert_to_float($this->input->post('diskon_rp'));
          }
      }

      $data_faktur_update = array(
          'id_user' => $this->session->userdata('id_user'),
          'nama_user' => $this->session->userdata('nama_user'),
          'id_supplier' => $this->input->post('id_supplier'),
          'nama_supplier' => $this->input->post('selected_nama_supplier'),
          'no_faktur' => ($this->input->post('pilih_nomer') == 'default') ? $this->input->post('no_faktur_auto') : $this->input->post('no_faktur_manual'),
          'total_harga' => $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($this->input->post('total_harga_faktur'))),
          'status_bayar' => $this->input->post('status_bayar'),
          'tanggal_bayar' => !empty($this->input->post('tanggal_bayar')) ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tanggal_bayar')))) : null,
          'metode_pembayaran' => $this->input->post('metode_pembayaran'),
          'bank' => $this->input->post('selected_bank_supplier'),
          'bayar' => $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($this->input->post('bayar_dp'))),
          'status_diskon' => $status_diskon,
          'jenis_diskon' => $jenis_diskon,
          'diskon' => $this->_convert_float_to_db_string($diskon_value),
          'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('tanggal_faktur')))),
          'waktu' => date('H:i:s'),
      );

      $this->db->where('id', $id_faktur);
      $this->db->update('apt_faktur', $data_faktur_update);
      $this->db->where('id_faktur', $id_faktur);
      $this->db->delete('apt_faktur_detail');

      $detail_faktur_batch = array();
      $stok_updates = array();
      $id_barang_arr = $this->input->post('id_barang') ?: [];
      $id_barang_detail_arr = $this->input->post('id_barang_detail') ?: [];
      $nama_barang_arr = $this->input->post('nama_barang') ?: [];
      $id_satuan_barang_arr = $this->input->post('id_satuan_barang') ?: [];
      $satuan_arr = $this->input->post('satuan') ?: [];
      $urutan_satuan_arr = $this->input->post('urutan_satuan') ?: [];
      $jumlah_arr = $this->input->post('jumlah') ?: []; 
      $harga_awal_arr = $this->input->post('harga_awal') ?: [];
      $sub_total_harga_awal_arr = $this->input->post('sub_total_harga_awal') ?: [];
      $harga_jual_arr = $this->input->post('harga_jual') ?: [];
      $laba_arr = $this->input->post('laba') ?: [];
      $kadaluarsa_arr = $this->input->post('kadaluarsa') ?: [];

      if (!empty($id_barang_arr)) {
          foreach ($id_barang_arr as $key => $id_barang) {
              if (isset($id_barang_detail_arr[$key]) && isset($nama_barang_arr[$key]) &&
                  isset($id_satuan_barang_arr[$key]) && isset($satuan_arr[$key]) &&
                  isset($jumlah_arr[$key]) && isset($harga_awal_arr[$key]) && 
                  isset($sub_total_harga_awal_arr[$key]) && isset($harga_jual_arr[$key]) && 
                  isset($laba_arr[$key]) && isset($kadaluarsa_arr[$key])) {

                  $kadaluarsa_db = !empty($kadaluarsa_arr[$key]) ? date('Y-m-d', strtotime(str_replace('/', '-', $kadaluarsa_arr[$key]))) : null;
                  $jumlah_beli_float = $this->_clean_and_convert_to_float($jumlah_arr[$key]);
                  $harga_awal_float = $this->_clean_and_convert_to_float($harga_awal_arr[$key]);
                  $harga_jual_float = $this->_clean_and_convert_to_float($harga_jual_arr[$key]);
                  $laba_float = $this->_clean_and_convert_to_float($laba_arr[$key]);
                  $id_barang_detail_int = (int)$this->_clean_and_convert_to_float($id_barang_detail_arr[$key]);
                  $id_barang_int = (int)$id_barang;

                  $detail_faktur_batch[] = array(
                      'id_faktur'             => $id_faktur,
                      'id_barang'             => $id_barang_int,
                      'id_barang_detail'      => $id_barang_detail_int,
                      'nama_barang'           => $nama_barang_arr[$key],
                      'id_satuan_barang'      => (int)$this->_clean_and_convert_to_float($id_satuan_barang_arr[$key]), 
                      'satuan_barang'         => $satuan_arr[$key],
                      'urutan_satuan'         => $this->_clean_and_convert_to_float($urutan_satuan_arr[$key]),
                      'jumlah'                => $this->_convert_float_to_db_string($jumlah_beli_float),
                      'harga_awal'            => $this->_convert_float_to_db_string($harga_awal_float),
                      'sub_total_harga_awal'  => $this->_convert_float_to_db_string($this->_clean_and_convert_to_float($sub_total_harga_awal_arr[$key])),
                      'harga_jual'            => $this->_convert_float_to_db_string($harga_jual_float),
                      'laba'                  => $this->_convert_float_to_db_string($laba_float),
                      'kadaluarsa'            => $kadaluarsa_db
                  );

                  $stok_updates[] = array(
                      'id_barang'         => $id_barang_int,
                      'id_barang_detail'  => $id_barang_detail_int,
                      'jumlah_beli'       => $jumlah_beli_float,
                      'harga_awal'        => $harga_awal_float,
                      'harga_jual'        => $harga_jual_float,
                      'laba'              => $laba_float,
                      'kadaluarsa'        => $kadaluarsa_db
                  );
              }
          }
      }

      if (!empty($detail_faktur_batch)) {
          $this->db->insert_batch('apt_faktur_detail', $detail_faktur_batch);
      }

      foreach ($stok_updates as $stok_data) {
          $this->kelola_stok_berjenjang(
              $stok_data['id_barang'],
              $stok_data['jumlah_beli'],
              'tambah',
              $stok_data
          );
      }
      
      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          return array(
              'status' => false,
              'message' => "Data Gagal Diedit: " . $this->db->error()['message']
          );
      } else {
          return array(
              'status' => true,
              'message' => "Data Berhasil Diedit"
          );
      }
    
    }

  public function row_data($id) {
    $this->db->select('f.*, s.nama_supplier');
    $this->db->from('apt_faktur f');
    $this->db->join('apt_supplier s', 'f.id_supplier = s.id', 'left');
    $this->db->where('f.id', $id);
    $query = $this->db->get();
    return $query->row_array();
  }

  public function get_detail_faktur($id) {
    $this->db->select('fd.id, fd.id_barang, fd.id_barang_detail, fd.nama_barang, fd.satuan_barang, fd.urutan_satuan,
                     fd.jumlah, fd.harga_awal, fd.harga_jual, fd.laba, fd.kadaluarsa, fd.sub_total_harga_awal, fd.id_satuan_barang');
    $this->db->from('apt_faktur_detail fd');
    $this->db->where('fd.id_faktur', $id);
    $query = $this->db->get();
    return $query->result_array();   
  }
  
public function hapus($id) {
    $this->db->trans_begin();

    $faktur_details = $this->get_detail_faktur($id);

    foreach ($faktur_details as $detail) {
        $this->kelola_stok_berjenjang(
            (int)$detail['id_barang'],
            $detail['jumlah'], 
            'kurang' 
        );
    }

    $id_barang_details_in_faktur = array_column($faktur_details, 'id_barang_detail');
    
    if (!empty($id_barang_details_in_faktur)) {
        $this->db->select('id_transaksi');
        $this->db->from('apt_transaksi_detail');
        $this->db->where_in('id_barang_detail', $id_barang_details_in_faktur);
        $related_transactions = $this->db->get()->result_array();
        $id_transaksi_to_delete = array_unique(array_column($related_transactions, 'id_transaksi'));

        if (!empty($id_transaksi_to_delete)) {
            $this->db->where_in('id_transaksi', $id_transaksi_to_delete);
            $this->db->delete('apt_transaksi_detail');
            $this->db->where_in('id', $id_transaksi_to_delete);
            $this->db->delete('apt_transaksi');
        }
    }

    $this->db->where('id_faktur', $id);
    $this->db->delete('apt_faktur_detail'); 
    $this->db->where('id', $id);
    $this->db->delete('apt_faktur'); 
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return array(
            'status' => false,
            'message' => "Data Gagal Dihapus: " . $this->db->error()['message']
        );
    } else {
        $this->db->trans_commit();
        return array(
            'status' => true,
            'message' => "Data Faktur dan Penjualan Terkait Berhasil Dihapus"
        );
    }
}

  public function get_all_faktur($tanggal_dari, $tanggal_sampai, $search, $id_supplier) {
      $this->db->select('f.*, s.nama_supplier');
      $this->db->from('apt_faktur f');
      $this->db->join('apt_supplier s', 'f.id_supplier = s.id', 'left');

      if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
          $this->db->where('DATE(f.tanggal) >=', $tanggal_dari); 
          $this->db->where('DATE(f.tanggal) <=', $tanggal_sampai); 
      }

      if (!empty($search)) {
          $this->db->like('f.no_faktur', $search);
      }

      if ($id_supplier != 'semua' && !empty($id_supplier)) {
          $this->db->where('f.id_supplier', $id_supplier);
      }

      $this->db->order_by('f.tanggal', 'DESC'); 
      $this->db->order_by('f.waktu', 'DESC');   
      $query = $this->db->get();
      return $query->result_array();
  }

    public function get_faktur_for_pelunasan($id_faktur) {
        $this->db->select('f.id, f.no_faktur, f.nama_supplier, f.tanggal, f.total_harga, f.bayar, f.status_bayar, f.tanggal_bayar, f.metode_pembayaran');
        $this->db->from('apt_faktur f');
        $this->db->where('f.id', $id_faktur);
        $query = $this->db->get();
        return $query->row_array();
        if ($faktur) {
        $faktur['total_harga'] = $this->_clean_and_convert_to_float($faktur['total_harga']);
        $faktur['bayar'] = $this->_clean_and_convert_to_float($faktur['bayar']);
        }

        return $faktur;
    }

    public function update_pelunasan($id_faktur, $jumlah_bayar_baru, $tanggal_pelunasan) {
        $this->db->trans_begin();

        $data_pelunasan = array(
            'id_faktur' => $id_faktur,
            'id_user' => null,
            'nama_user' => null,
            'bayar' => $this->_convert_float_to_db_string($jumlah_bayar_baru),
            'tanggal' => date('Y-m-d', strtotime(str_replace('/', '-', $tanggal_pelunasan))),
            'waktu' => date('H:i:s')
        );
        $this->db->insert('apt_faktur_pelunasan', $data_pelunasan);

        $faktur = $this->db->select('total_harga, bayar')->where('id', $id_faktur)->get('apt_faktur')->row_array();
        if (!$faktur) {
            $this->db->trans_rollback();
            return array('status' => false, 'message' => "Faktur tidak ditemukan.");
        }
        $total_harga = $this->_clean_and_convert_to_float($faktur['total_harga']);
        $bayar_awal = $this->_clean_and_convert_to_float($faktur['bayar']);

        $this->db->select_sum('bayar', 'total_cicilan');
        $this->db->where('id_faktur', $id_faktur);
        $query_cicilan = $this->db->get('apt_faktur_pelunasan')->row();
        $total_cicilan = $this->_clean_and_convert_to_float($query_cicilan->total_cicilan);
        $total_pembayaran_keseluruhan = $bayar_awal + $total_cicilan;
        if ($total_pembayaran_keseluruhan >= $total_harga) {
            $this->db->where('id', $id_faktur);
            $this->db->update('apt_faktur', array('status_bayar' => 'Lunas'));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array(
                'status' => false,
                'message' => "Gagal melakukan pelunasan: " . $this->db->error()['message']
            );
        } else {
            $this->db->trans_commit();
            return array(
                'status' => true,
                'message' => "Pelunasan berhasil diproses."
            );
        }
    }

    public function get_riwayat_pembayaran($id_faktur) {

        $this->db->select('total_harga, bayar, tanggal, status_bayar');
        $this->db->where('id', $id_faktur);
        $faktur = $this->db->get('apt_faktur')->row_array();

        if (!$faktur) {
            return [];
        }

        $total_harga_faktur = $this->_clean_and_convert_to_float($faktur['total_harga']);
        $riwayat_final = [];
        $total_dibayar_kumulatif = 0;

        $pembayaran = [];
        $bayar_awal = $this->_clean_and_convert_to_float($faktur['bayar']);
        if ($bayar_awal > 0) {
            $pembayaran[] = [
                'tanggal' => $faktur['tanggal'],
                'jumlah' => $bayar_awal
            ];
        }
        
        $cicilan = $this->db->select('tanggal, bayar AS jumlah')
                            ->where('id_faktur', $id_faktur)
                            ->get('apt_faktur_pelunasan')
                            ->result_array();

        foreach($cicilan as &$c) {
            $c['jumlah'] = $this->_clean_and_convert_to_float($c['jumlah']);
        }

        $pembayaran = array_merge($pembayaran, $cicilan);
        
        usort($pembayaran, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });
        
        if (empty($pembayaran)) {
            $riwayat_final[] = [
                'tanggal_pembayaran' => !empty($faktur['tanggal']) ? date('d-m-Y', strtotime($faktur['tanggal'])) : '-',
                'status_pembayaran'  => 'Belum Lunas',
                'total_harga'        => $total_harga_faktur,
                'dibayar'            => 0,
                'sisa_kurang'        => $total_harga_faktur
            ];
            return $riwayat_final;
        }

        foreach ($pembayaran as $bayar) {
            $total_dibayar_kumulatif += $bayar['jumlah'];
            $sisa_kurang = $total_harga_faktur - $total_dibayar_kumulatif;
            $status_saat_ini = ($sisa_kurang <= 0.01) ? 'Lunas' : 'Belum Lunas';

            $riwayat_final[] = [
                'tanggal_pembayaran' => !empty($bayar['tanggal']) ? date('d-m-Y', strtotime($bayar['tanggal'])) : '-',
                'status_pembayaran'  => $status_saat_ini,
                'total_harga'        => $total_harga_faktur,
                'dibayar'            => $total_dibayar_kumulatif,
                'sisa_kurang'        => $sisa_kurang < 0 ? 0 : $sisa_kurang
            ];
        }

        return $riwayat_final;
    }
    public function get_status_terakhir_pembayaran($id_faktur) {
    $riwayat_lengkap = $this->get_riwayat_pembayaran($id_faktur);

    if (empty($riwayat_lengkap)) {
        return null;
    }

    $status_terakhir = end($riwayat_lengkap);

    return $status_terakhir;
}
}