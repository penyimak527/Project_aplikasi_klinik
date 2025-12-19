<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Retur extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function _cleanAndConvertToFloat($value) {
        $cleaned_value = str_replace('.', '', $value);
        $cleaned_value = str_replace(',', '.', $cleaned_value);
        return floatval($cleaned_value);
    }

    private function _convertFloatToDbString($float_value) {
        return str_replace('.', ',', (string)$float_value);
    }

    private function _update_stock_hierarchical($id_barang, $id_barang_detail_terjual, $jumlah_terjual) {
        // 1. Ambil semua level satuan untuk barang ini
        $levels = $this->db->from('apt_barang_detail')
                           ->where('id_barang', $id_barang)
                           ->order_by('urutan_satuan', 'ASC')
                           ->get()->result_array();

        if (empty($levels)) throw new Exception("Struktur satuan barang (ID: $id_barang) tidak ditemukan.");

        $total_terjual_satuan_terkecil = $jumlah_terjual;
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
        $stok_terkecil_sekarang = (float)$this->db->get_where('apt_stok', ['id_barang_detail' => $satuan_terkecil['id']])->row()->stok;
        $stok_terkecil_baru = $stok_terkecil_sekarang - $total_terjual_satuan_terkecil;
        if ($stok_terkecil_baru < 0) {
            throw new Exception("Stok untuk barang " . $satuan_terkecil['nama_barang'] . " tidak mencukupi untuk melakukan retur.");
        }

        $stok_sisa = $stok_terkecil_baru;
        $reversed_levels = array_reverse($levels); 

        foreach ($reversed_levels as $level) {
            $this->db->where('id_barang_detail', $level['id']);
            $this->db->update('apt_stok', ['stok' => $stok_sisa]);
            
            if ((int)$level['urutan_satuan'] > 1) {
                $stok_sisa = $stok_sisa / (int)$level['isi_satuan_turunan'];
            }
        }
        return true;
    }

    public function getAllInvoicesForReturnModal($search = '', $limit = 10, $offset = 0) {
        $this->db->select('id, no_faktur, nama_supplier, total_harga, tanggal, waktu');
        $this->db->from('apt_faktur');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('no_faktur', $search);
            $this->db->or_like('nama_supplier', $search);
            $this->db->group_end();
        }
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function countAllInvoicesForReturnModal($search = '') {
        $this->db->select('COUNT(id) as total_rows');
        $this->db->from('apt_faktur');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('no_faktur', $search);
            $this->db->or_like('nama_supplier', $search);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->row()->total_rows;
    }

    public function countItemsFromInvoiceDetail($id_faktur, $search = '') {
        $this->db->select('COUNT(fd.id) as total_rows');
        $this->db->from('apt_faktur_detail fd');
        $this->db->join('apt_barang_detail bd', 'fd.id_barang_detail = bd.id', 'left');
        $this->db->join('apt_barang b', 'bd.id_barang = b.id', 'left');
        $this->db->where('fd.id_faktur', $id_faktur);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->row()->total_rows;
    }

    public function getItemsFromInvoiceDetail($id_faktur, $search = '', $limit = 10, $offset = 0) {
        $this->db->select('
            fd.id_barang,
            fd.id_barang_detail,
            fd.jumlah,
            fd.harga_awal,
            fd.harga_jual,
            bd.kode_barang,
            b.nama_barang,
            s.stok,
            bd.satuan_barang as satuan_barang,
            fd.id_satuan_barang
        ');
        $this->db->from('apt_faktur_detail fd');
        $this->db->join('apt_barang_detail bd', 'fd.id_barang_detail = bd.id', 'left');
        $this->db->join('apt_barang b', 'bd.id_barang = b.id', 'left');
        $this->db->join('apt_stok s', 'fd.id_barang_detail = s.id_barang_detail', 'left');
        $this->db->where('fd.id_faktur', $id_faktur);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->group_end();
        }
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function saveReturnWithStockUpdate($faktur_id, $retur_data) {
        $this->db->trans_begin();

        try {
            $faktur_data = $this->db->get_where('apt_faktur', array('id' => $faktur_id))->row_array();
            if (!$faktur_data) {
                throw new Exception("Faktur tidak ditemukan.");
            }
            $kode_retur = $this->generateReturnCode();
            $retur_header = array(
                'id_faktur' => $faktur_id,
                'kode_retur' => $kode_retur,
                'tanggal' => date('Y-m-d'),
                'waktu' => date('H:i:s'),
                'id_user' => NULL,
                'nama_user' => NULL,
            );

            $this->db->insert('apt_retur', $retur_header);
            $id_retur = $this->db->insert_id();

            foreach ($retur_data as $item) {
                $id_barang_detail = $item['id_barang_detail'];
                $return_quantity = $item['jumlah_retur'];
                
                $item_info = $this->db->select('bd.id_barang, b.nama_barang')
                                    ->from('apt_barang_detail bd')
                                    ->join('apt_barang b', 'bd.id_barang = b.id')
                                    ->where('bd.id', $id_barang_detail)
                                    ->get()->row_array();
                
                if (!$item_info) {
                    throw new Exception("Informasi barang dengan id_barang_detail {$id_barang_detail} tidak ditemukan.");
                }

                $retur_detail = array(
                    'id_retur' => $id_retur,
                    'id_barang_detail' => $id_barang_detail,
                    'id_barang' => $item_info['id_barang'],
                    'nama_barang' => $item_info['nama_barang'],
                    'jumlah_retur' => $return_quantity,
                    'jumlah_beli' => $item['jumlah'] ?? $return_quantity
                );

                $this->db->insert('apt_retur_detail', $retur_detail);

                $this->_update_stock_hierarchical($item_info['id_barang'], $id_barang_detail, $return_quantity);
            }
            
            $this->db->trans_commit();
            return array(
                'status' => true,
                'message' => 'Data retur berhasil disimpan.'
            );

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return array(
                'status' => false,
                'message' => "Data Gagal Disimpan: " . $e->getMessage()
            );
        }
    }

    public function generateReturnCode() {
        $bulan_tahun = date('ymd');
        $this->db->select("MAX(RIGHT(kode_retur, 3)) AS max_code");
        $this->db->like('kode_retur', "RT-{$bulan_tahun}", 'after');
        $query = $this->db->get('apt_retur');
        $row = $query->row();
        $max_code = (int) $row->max_code;
        $next_code = $max_code + 1;
        $return_code = "RT-{$bulan_tahun}" . sprintf('%03s', $next_code);
        return $return_code;
    }

    public function getRiwayatRetur($search = '', $limit = 10, $offset = 0) {
        $this->db->select('r.id, r.kode_retur, f.no_faktur, f.nama_supplier, r.tanggal, r.waktu');
        $this->db->from('apt_retur r');
        $this->db->join('apt_faktur f', 'r.id_faktur = f.id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('r.kode_retur', $search);
            $this->db->or_like('f.no_faktur', $search);
            $this->db->or_like('f.nama_supplier', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('r.tanggal', 'DESC');
        $this->db->order_by('r.waktu', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    public function countRiwayatRetur($search = '') {
        $this->db->select('COUNT(r.id) as total_rows');
        $this->db->from('apt_retur r');
        $this->db->join('apt_faktur f', 'r.id_faktur = f.id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('r.kode_retur', $search);
            $this->db->or_like('f.no_faktur', $search);
            $this->db->or_like('f.nama_supplier', $search);
            $this->db->group_end();
        }
        
        return $this->db->get()->row()->total_rows;
    }

    public function getReturHeader($id_retur) {
        $this->db->select('r.id, r.kode_retur, f.no_faktur, f.nama_supplier, r.tanggal, r.waktu');
        $this->db->from('apt_retur r');
        $this->db->join('apt_faktur f', 'r.id_faktur = f.id', 'left');
        $this->db->where('r.id', $id_retur);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getReturDetails($id_retur) {
        $this->db->select('rd.id_barang, bd.kode_barang, b.nama_barang, rd.jumlah_beli, rd.jumlah_retur');
        $this->db->from('apt_retur_detail rd');
        $this->db->join('apt_barang_detail bd', 'rd.id_barang_detail = bd.id', 'left');
        $this->db->join('apt_barang b', 'bd.id_barang = b.id', 'left');
        $this->db->where('rd.id_retur', $id_retur);
        
        $query = $this->db->get();
        return $query->result_array();
    }
}