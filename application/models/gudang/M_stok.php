<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Stok extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    private function _clean_and_convert_to_float($value) {
        if (empty($value)) return 0;
        $cleaned_value = str_replace('.', '', $value);
        $cleaned_value = str_replace(',', '.', $cleaned_value);
        return floatval($cleaned_value);
    }

    private function _convert_float_to_db_string($float_value) {
        return str_replace('.', ',', (string)$float_value);
    }

    public function ensure_unique_stok_record($id_barang, $id_barang_detail) {
        $this->db->trans_begin();
        $this->db->select('id_barang, id_barang_detail, COUNT(*) as total');
        $this->db->from('apt_stok');
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_barang_detail', $id_barang_detail);
        $this->db->group_by('id_barang, id_barang_detail');
        $this->db->having('total > 1');
        $duplicate_check = $this->db->get()->row();
        
        if ($duplicate_check && $duplicate_check->total > 1) {
            
            $this->db->where('id_barang', $id_barang);
            $this->db->where('id_barang_detail', $id_barang_detail);
            $this->db->order_by('kadaluarsa', 'DESC');
            $this->db->order_by('id', 'DESC');
            $all_records = $this->db->get('apt_stok')->result_array();
            
            if (count($all_records) > 1) {
                $primary_record = $all_records[0];
                $total_stok = $this->_clean_and_convert_to_float($primary_record['stok']);
                
                for ($i = 1; $i < count($all_records); $i++) {
                    $total_stok += $this->_clean_and_convert_to_float($all_records[$i]['stok']);
                }
                
                $this->db->where('id', $primary_record['id']);
                $this->db->update('apt_stok', [
                    'stok' => $this->_convert_float_to_db_string($total_stok)
                ]);
                
                $this->db->where('id_barang', $id_barang);
                $this->db->where('id_barang_detail', $id_barang_detail);
                $this->db->where('id !=', $primary_record['id']);
                $this->db->delete('apt_stok');
            }
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    public function update_stok_on_delete($id_barang, $id_barang_detail, $jumlah_kurang) {
        $this->db->trans_begin();
        $this->ensure_unique_stok_record($id_barang, $id_barang_detail);
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_barang_detail', $id_barang_detail);
        $stok_data = $this->db->get('apt_stok')->row_array();

        if ($stok_data) {
            $jumlah_kurang_float = $this->_clean_and_convert_to_float($jumlah_kurang);
            $current_stok = $this->_clean_and_convert_to_float($stok_data['stok']);
            
            $new_stok = $current_stok - $jumlah_kurang_float;

            $data_update = array(
                'stok' => $this->_convert_float_to_db_string($new_stok)
            );

            $this->db->where('id', $stok_data['id']);
            $this->db->update('apt_stok', $data_update);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    public function update_stok_from_faktur($id_barang, $id_barang_detail, $jumlah_beli, $harga_awal, $harga_jual, $laba, $kadaluarsa) {
        $this->db->trans_begin();

        $this->ensure_unique_stok_record($id_barang, $id_barang_detail);
        
        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_barang_detail', $id_barang_detail);
        $stok_data = $this->db->get('apt_stok')->row_array();

        $jumlah_beli_float = $this->_clean_and_convert_to_float($jumlah_beli);
        $harga_awal_float = $this->_clean_and_convert_to_float($harga_awal);
        $harga_jual_float = $this->_clean_and_convert_to_float($harga_jual);
        $laba_float = $this->_clean_and_convert_to_float($laba);

        if ($stok_data) {
            $current_stok = $this->_clean_and_convert_to_float($stok_data['stok']);
            $new_stok = $current_stok + $jumlah_beli_float;

            $data_update = array(
                'stok'          => $this->_convert_float_to_db_string($new_stok),
                'harga_awal'    => $this->_convert_float_to_db_string($harga_awal_float),
                'harga_jual'    => $this->_convert_float_to_db_string($harga_jual_float),
                'laba'          => $this->_convert_float_to_db_string($laba_float),
                'kadaluarsa'    => $kadaluarsa
            );
            $this->db->where('id', $stok_data['id']);
            $this->db->update('apt_stok', $data_update);
        } else {
            $data_insert = array(
                'id_barang'         => $id_barang,
                'id_barang_detail'  => $id_barang_detail,
                'stok'              => $this->_convert_float_to_db_string($jumlah_beli_float),
                'harga_awal'        => $this->_convert_float_to_db_string($harga_awal_float),
                'harga_jual'        => $this->_convert_float_to_db_string($harga_jual_float),
                'laba'              => $this->_convert_float_to_db_string($laba_float),
                'kadaluarsa'        => $kadaluarsa
            );
            $this->db->insert('apt_stok', $data_insert);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function get_all_stok($search = '', $limit = 10, $offset = 0) {
        $this->db->select('s.id, s.stok, s.harga_awal, s.harga_jual, s.laba, s.kadaluarsa,
                        b.nama_barang, bd.kode_barang, bd.satuan_barang,
                        b.id as barang_id, bd.id as barang_detail_id');
        $this->db->from('apt_stok s');
        $this->db->join('apt_barang b', 's.id_barang = b.id', 'left');
        $this->db->join('apt_barang_detail bd', 's.id_barang_detail = bd.id', 'left');
        
        $this->db->where('s.id IN (SELECT MAX(id) FROM apt_stok GROUP BY id_barang, id_barang_detail)');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->or_like('bd.satuan_barang', $search);
            $this->db->group_end();
        }

        $this->db->order_by('b.nama_barang', 'ASC');
        $this->db->order_by('bd.urutan_satuan', 'ASC'); 
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_all_stok($search = '') {
        $this->db->select('COUNT(DISTINCT CONCAT(s.id_barang, "-", s.id_barang_detail)) as total_rows');
        $this->db->from('apt_stok s');
        $this->db->join('apt_barang b', 's.id_barang = b.id', 'left');
        $this->db->join('apt_barang_detail bd', 's.id_barang_detail = bd.id', 'left');

        $this->db->where('s.id IN (SELECT MAX(id) FROM apt_stok GROUP BY id_barang, id_barang_detail)');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->or_like('bd.satuan_barang', $search);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->row()->total_rows;
    }

    public function cleanup_all_duplicates() {
        $this->db->trans_begin();
        
        $this->db->select('id_barang, id_barang_detail, COUNT(*) as total');
        $this->db->from('apt_stok');
        $this->db->group_by('id_barang, id_barang_detail');
        $this->db->having('total > 1');
        $duplicates = $this->db->get()->result_array();
        
        $cleaned_count = 0;
        
        foreach ($duplicates as $dup) {
            if ($this->ensure_unique_stok_record($dup['id_barang'], $dup['id_barang_detail'])) {
                $cleaned_count++;
            }
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['success' => false, 'cleaned' => $cleaned_count];
        } else {
            $this->db->trans_commit();
            return ['success' => true, 'cleaned' => $cleaned_count];
        }
    }

    private function _set_default_financials(&$data_array) {
        $data_array['harga_awal'] = '0';
        $data_array['harga_jual'] = '0';
        $data_array['laba'] = '0';
        $data_array['kadaluarsa'] = NULL;
    }
    public function cekStokDetail($id_barang_detail) {
        $this->db->where('id_barang_detail', $id_barang_detail);
        // Kita asumsikan stok sudah unik
        $stok_data = $this->db->get('apt_stok')->row_array(); 
        
        if ($stok_data) {
            return $this->_clean_and_convert_to_float($stok_data['stok']);
        } else {
            return 0;
        }
    }
    public function setStokProgramatik($id_barang_detail, $jumlah_stok_baru_float) {
    
    $detail = $this->db->get_where('apt_barang_detail', ['id' => $id_barang_detail])->row();
    if (!$detail) {
      return false;
    }
    $id_barang = $detail->id_barang;

    $this->ensure_unique_stok_record($id_barang, $id_barang_detail);
    $stok_data = $this->db->get_where('apt_stok', ['id_barang_detail' => $id_barang_detail])->row_array();
    $data_to_save = [
      'stok' => $this->_convert_float_to_db_string($jumlah_stok_baru_float)
    ];

    
    if ($detail && $detail->urutan_satuan > 1) {
      
      $urutan_parent = $detail->urutan_satuan - 1;
      $parent_detail = $this->db->get_where('apt_barang_detail', [
        'id_barang' => $id_barang, 
        'urutan_satuan' => $urutan_parent
      ])->row();

      if ($parent_detail) {
        $this->ensure_unique_stok_record($id_barang, $parent_detail->id);
        $parent_stok_data = $this->db->get_where('apt_stok', ['id_barang_detail' => $parent_detail->id])->row_array();
        
        if ($parent_stok_data) {
          
          $current_isi_turunan = $this->_clean_and_convert_to_float($detail->isi_satuan_turunan);
          if ($current_isi_turunan == 0) $current_isi_turunan = 1; 
          $parent_harga_awal = $this->_clean_and_convert_to_float($parent_stok_data['harga_awal']);
          $parent_harga_jual = $this->_clean_and_convert_to_float($parent_stok_data['harga_jual']);

          $new_harga_awal = $parent_harga_awal / $current_isi_turunan;
          $new_harga_jual = $parent_harga_jual / $current_isi_turunan;
          $new_laba = $new_harga_jual - $new_harga_awal;

          $data_to_save['harga_awal'] = $this->_convert_float_to_db_string($new_harga_awal);
          $data_to_save['harga_jual'] = $this->_convert_float_to_db_string($new_harga_jual);
          $data_to_save['laba'] = $this->_convert_float_to_db_string($new_laba);
          $data_to_save['kadaluarsa'] = $parent_stok_data['kadaluarsa']; 

        } else {
          $this->_set_default_financials($data_to_save);
        }
      } else {
        $this->_set_default_financials($data_to_save);
      }
    } else if ($detail && $detail->urutan_satuan == 1) {
      if (!$stok_data) {
        $this->_set_default_financials($data_to_save);
      }
    } else {
      $this->_set_default_financials($data_to_save);
    }

    if ($stok_data) {

      $this->db->where('id', $stok_data['id']);
      $this->db->update('apt_stok', $data_to_save);

    } else {
      $data_to_save['id_barang'] = $id_barang;
      $data_to_save['id_barang_detail'] = $id_barang_detail;
      $this->db->insert('apt_stok', $data_to_save);
    }
    
    return true;
  }
}