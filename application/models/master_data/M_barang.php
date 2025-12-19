<?php
class M_Barang extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');
    $id_jenis_barang = $this->input->post('id_jenis_barang');

    $sql = "SELECT a.*, b.nama_jenis FROM apt_barang a LEFT JOIN apt_jenis_barang b ON a.id_jenis_barang = b.id WHERE 1=1";
    $params = [];

    if ($cari != '') {
        $sql .= " AND (a.nama_barang LIKE ? OR b.nama_jenis LIKE ?)";
        $params[] = "%$cari%";
        $params[] = "%$cari%";
    }

    if ($id_jenis_barang != '') {
        $sql .= " AND a.id_jenis_barang = ?";
        $params[] = $id_jenis_barang;
    }

    $sql .= " ORDER BY a.id DESC";

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }

  public function row_data($id) {
    $sql_barang = $this->db->query("SELECT a.*, b.nama_jenis FROM apt_barang a LEFT JOIN apt_jenis_barang b ON a.id_jenis_barang = b.id WHERE a.id = ?", array($id));
    $row_barang = $sql_barang->row_array();
    $sql_detail = $this->db->query("SELECT ad.*, s.nama_satuan FROM apt_barang_detail ad LEFT JOIN apt_satuan_barang s ON ad.id_satuan_barang = s.id WHERE ad.id_barang = ? ORDER BY ad.urutan_satuan ASC", array($id));
    $row_barang['detail_satuan'] = $sql_detail->result_array();

    return $row_barang;
  }
public function tambah() {
    $nama_barang = trim($this->input->post('nama_barang'));
    $id_jenis = $this->input->post('id_jenis_barang');

    if ($nama_barang == "" || $id_jenis == "") {
        return [
            'status' => false,
            'message' => "Nama barang dan jenis barang tidak boleh kosong!"
        ];
    }

    $cek_nama = $this->db->get_where('apt_barang', [
        'nama_barang' => $nama_barang
    ])->row_array();

    if ($cek_nama) {
        return [
            'status' => false,
            'message' => "Nama barang sudah ada!"
        ];
    }

    $kode_barang_arr = $this->input->post('kode_barang');
    $id_satuan_barang_arr = $this->input->post('id_satuan_barang');
    $isi_satuan_turunan_arr = $this->input->post('isi_satuan_turunan');
    $urutan_satuan_arr = $this->input->post('urutan_satuan');
    $kode_set = [];
    foreach ($kode_barang_arr as $kb) {
        $kb = trim($kb);
        if ($kb == "") continue;

        if (in_array($kb, $kode_set)) {
            return [
                'status' => false,
                'message' => "Kode barang duplikat ditemukan: $kb"
            ];
        }
        $kode_set[] = $kb;
    }

    if (!empty($kode_set)) {
        $this->db->where_in('kode_barang', $kode_set);
        $cek_kode = $this->db->get('apt_barang_detail')->result_array();

        if (!empty($cek_kode)) {
            return [
                'status' => false,
                'message' => "Kode barang sudah ada di database: " . $cek_kode[0]['kode_barang']
            ];
        }
    }

    $this->db->trans_begin();
    $data_barang = [
        'nama_barang' => $nama_barang,
        'id_jenis_barang' => $id_jenis,
    ];

    $this->db->insert('apt_barang', $data_barang);
    $id_barang_baru = $this->db->insert_id();
    $detail_satuan_batch = [];

    if (!empty($kode_barang_arr)) {
        foreach ($kode_barang_arr as $key => $kb) {

            $kb = trim($kb);

            if ($kb != "" && $id_satuan_barang_arr[$key] != 'Kosong' && $id_satuan_barang_arr[$key] != '') {

                $satuan_row = $this->db->get_where('apt_satuan_barang', [
                    'id' => $id_satuan_barang_arr[$key]
                ])->row_array();

                $nama_satuan = $satuan_row ? $satuan_row['nama_satuan'] : null;

                $detail_satuan_batch[] = [
                    'id_barang'           => $id_barang_baru,
                    'kode_barang'         => $kb,
                    'nama_barang'         => $nama_barang,
                    'id_satuan_barang'    => $id_satuan_barang_arr[$key],
                    'satuan_barang'       => $nama_satuan,
                    'isi_satuan_turunan'  => str_replace(',', '', $isi_satuan_turunan_arr[$key]),
                    'urutan_satuan'       => $urutan_satuan_arr[$key]
                ];
            }
        }
    }

    if (!empty($detail_satuan_batch)) {
        $this->db->insert_batch('apt_barang_detail', $detail_satuan_batch);
    }

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return [
            'status' => false,
            'message' => "Data gagal ditambahkan: " . $this->db->error()['message']
        ];
    }

    $this->db->trans_commit();
    return [
        'status' => true,
        'message' => "Data berhasil ditambahkan!"
    ];
}

  public function edit(){
  
    $this->db->trans_begin();
    
    $this->load->model('gudang/M_Stok', 'm_stok');

    $id_barang = $this->input->post('id');
    $nama_barang_baru = $this->input->post('nama_barang');
    
    $data_barang_master = array(
      'nama_barang' => $nama_barang_baru,
      'id_jenis_barang' => $this->input->post('id_jenis_barang')
    );
    $this->db->where('id', $id_barang);
    $this->db->update('apt_barang', $data_barang_master);

    $data_barang_detail_nama = array('nama_barang' => $nama_barang_baru);
    $this->db->where('id_barang', $id_barang);
    $this->db->update('apt_barang_detail', $data_barang_detail_nama);

    $id_barang_detail_arr  = $this->input->post('id_barang_detail'); 
    $kode_barang_arr    = $this->input->post('kode_barang');
    $id_satuan_barang_arr  = $this->input->post('id_satuan_barang');
    $isi_satuan_turunan_arr = $this->input->post('isi_satuan_turunan');
    $urutan_satuan_arr   = $this->input->post('urutan_satuan');

    $batch_insert = [];
    $batch_update = [];
    $ids_to_keep = [];

    if (!empty($kode_barang_arr)) {
      foreach ($kode_barang_arr as $key => $kb) {
        if (empty(trim($kb)) || $id_satuan_barang_arr[$key] == 'Kosong' || $id_satuan_barang_arr[$key] == '') {
          continue;
        }
        $satuan_row = $this->db->get_where('apt_satuan_barang', array('id' => $id_satuan_barang_arr[$key]))->row_array();
        $nama_satuan = $satuan_row ? $satuan_row['nama_satuan'] : null;

        $data_detail = array(
          'id_barang'      => $id_barang,
          'kode_barang'     => trim($kb),
          'nama_barang'     => $nama_barang_baru, 
          'id_satuan_barang'  => $id_satuan_barang_arr[$key],
          'satuan_barang'    => $nama_satuan, 
         'isi_satuan_turunan' => preg_replace('/[^0-9]/', '', $isi_satuan_turunan_arr[$key]),
          'urutan_satuan'    => $urutan_satuan_arr[$key]
        );

        $id_detail = isset($id_barang_detail_arr[$key]) ? $id_barang_detail_arr[$key] : null;

        if ($id_detail) { 
          $data_detail['id'] = $id_detail;
          $batch_update[] = $data_detail; 
          $ids_to_keep[] = $id_detail; 
        } else { 
          $batch_insert[] = $data_detail; 
        }
      }
    }

    $this->db->select('id, urutan_satuan');
    $this->db->where('id_barang', $id_barang);
    $current_details = $this->db->get('apt_barang_detail')->result_array();
    
    $current_ids_from_db = [];
    $master_detail_id = null;

    foreach ($current_details as $detail) {
      $current_ids_from_db[] = $detail['id'];
      if ($detail['urutan_satuan'] == 1) {
        $master_detail_id = $detail['id'];
      }
    }

    $ids_to_delete = array_diff($current_ids_from_db, $ids_to_keep);

    if ($master_detail_id !== null && ($key = array_search($master_detail_id, $ids_to_delete)) !== false) {
      unset($ids_to_delete[$key]);
    }

    if (!empty($ids_to_delete)) {
      $this->db->where_in('id_barang_detail', $ids_to_delete);
      $this->db->delete('apt_stok');
      
      $this->db->where_in('id', $ids_to_delete);
      $this->db->delete('apt_barang_detail');
    }

    if (!empty($batch_update)) {
      $this->db->update_batch('apt_barang_detail', $batch_update, 'id');
    }
    if (!empty($batch_insert)) {
      $this->db->insert_batch('apt_barang_detail', $batch_insert);
    }

    $this->_hitungUlangStok($id_barang);
    
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
     $this->db->trans_rollback();
     return array(
      'status' => false,
      'message' => "Data Gagal Diedit: " . $this->db->error()['message']
     );
    } else {
     $this->db->trans_commit();
     return array(
      'status' => true,
      'message' => "Data Berhasil Diedit."
     );
    }
  }
  public function hapus() {
    $this->db->trans_begin();
    $id_barang = $this->input->post('id');

    $this->db->where('id_barang', $id_barang);
    $this->db->delete('apt_barang_detail');
    $this->db->where('id', $id_barang);
    $this->db->delete('apt_barang');
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
        'message' => "Data Berhasil Dihapus"
      );
    }
  }

  public function get_all_jenis_barang() {
      $query = $this->db->get('apt_jenis_barang');
      return $query->result_array();
  }

  public function get_all_satuan_barang() {
      $query = $this->db->get('apt_satuan_barang');
      return $query->result_array();
  }

  public function get_item_details($id) {
    $sql_barang = $this->db->query("SELECT a.*, b.nama_jenis FROM apt_barang a LEFT JOIN apt_jenis_barang b ON a.id_jenis_barang = b.id WHERE a.id = ?", array($id));
    $item_data = $sql_barang->row_array();

    if ($item_data) {

        $sql_detail = $this->db->query("SELECT ad.*, s.nama_satuan FROM apt_barang_detail ad LEFT JOIN apt_satuan_barang s ON ad.id_satuan_barang = s.id WHERE ad.id_barang = ? ORDER BY ad.urutan_satuan ASC", array($id));
        $item_data['detail_satuan'] = $sql_detail->result_array();
    }

    return $item_data;
  }


private function _hitungUlangStok($id_barang) {
    $this->load->model('gudang/M_Stok', 'm_stok_recalc'); 
    
    $this->db->where('id_barang', $id_barang);
    $this->db->order_by('urutan_satuan', 'ASC'); 
    $all_details = $this->db->get('apt_barang_detail')->result_array();

    if (empty($all_details)) {
        return; 
    }
    $cumulative_stok_value = 0; 

    foreach ($all_details as $index => $detail) {
        $id_detail = $detail['id'];
        
        if ($index == 0) {
            $master_stok = $this->m_stok_recalc->cekStokDetail($id_detail);
            $cumulative_stok_value = $master_stok;

        } else {
            $current_ratio_str = $detail['isi_satuan_turunan'];
            $current_ratio = intval(preg_replace('/[^0-9]/', '', $current_ratio_str));

            if ($current_ratio <= 0) {
                $current_ratio = 1;
            }
            
            $cumulative_stok_value = $cumulative_stok_value * $current_ratio;
            $this->m_stok_recalc->setStokProgramatik($id_detail, $cumulative_stok_value);
        }
    }
}
}
