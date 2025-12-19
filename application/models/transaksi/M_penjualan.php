<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_penjualan extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_barang_pagination($search, $limit, $offset, $jenis_id)
    {
        $this->db->select('
            b.id AS id_barang_utama, 
            b.nama_barang, 
            b.id_jenis_barang,
            bd.id AS id_barang_detail, 
            bd.kode_barang, 
            bd.satuan_barang, 
            s.harga_jual, 
            s.stok
        ');
        $this->db->from('apt_barang b');
        $this->db->join('apt_barang_detail bd', 'b.id = bd.id_barang', 'left');
        $this->db->join('apt_stok s', 'bd.id = s.id_barang_detail', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->group_end();
        }

        if ($jenis_id && $jenis_id !== 'all') {
            $this->db->where('b.id_jenis_barang', $jenis_id);
        }

        $this->db->where('bd.urutan_satuan', '1');
        $this->db->where('s.stok >', 0); 
        $this->db->order_by('b.nama_barang', 'ASC');
        $this->db->limit($limit, $offset);
        
        $result = $this->db->get()->result();

        foreach ($result as $row) {
            $row->satuan_list = $this->get_satuan_list_by_barang_id($row->id_barang_utama);
        }

        return $result;
    }
 
    public function count_barang_pagination($search, $jenis_id)
    {
        $this->db->from('apt_barang b');
        $this->db->join('apt_barang_detail bd', 'b.id = bd.id_barang', 'left');        
        $this->db->join('apt_stok s', 'bd.id = s.id_barang_detail', 'left');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('b.nama_barang', $search);
            $this->db->or_like('bd.kode_barang', $search);
            $this->db->group_end();
        }

        if ($jenis_id && $jenis_id !== 'all') {
            $this->db->where('b.id_jenis_barang', $jenis_id);
        }
        
        $this->db->where('bd.urutan_satuan', '1');
        $this->db->where('s.stok >', 0);
        return $this->db->count_all_results();
    }
    private function get_satuan_list_by_barang_id($id_barang)
    {
        return $this->db->select('
            bd.satuan_barang as satuan, 
            s.harga_jual as harga, 
            bd.isi_satuan_turunan as konversi,
            bd.id as id_satuan,
            s.stok as stok_satuan
        ')
        ->from('apt_barang_detail bd')
        ->join('apt_stok s', 's.id_barang_detail = bd.id')
        ->where('bd.id_barang', $id_barang)
        ->order_by('bd.urutan_satuan', 'ASC')
        ->get()
        ->result_array();
    }

    public function get_jenis_barang()
    {
        return $this->db->get('apt_jenis_barang')->result();
    }

    public function simpan_transaksi($data)
    {
        $this->db->trans_begin();

        try {
            $id_pelanggan = $data['id_pelanggan'];
            $nama_customer = $data['nama_customer'] ?: 'Umum';

            if (empty($id_pelanggan)) {
                if (strtolower($nama_customer) !== 'umum') {
                    $pelanggan_data = [
                        'nama_customer' => $nama_customer,
                        'jenis_kelamin' => $data['jenis_kelamin'],
                        'umur'          => $data['umur'],
                        'no_telp'       => $data['no_telp'],
                    ];
                    $this->db->insert('apt_pelanggan', $pelanggan_data);
                    $id_pelanggan = $this->db->insert_id();
                }
            }

            $kode_invoice = $this->_generate_kode_invoice(); 

            $transaksi_data = [
                'id_user'           => $this->session->userdata('id_user'),
                'kode_invoice'      => $kode_invoice,
                'id_pelanggan'      => $id_pelanggan,
                'nama_customer'     => $nama_customer,
                'total_invoice'     => (float)str_replace(',', '.', $data['total_invoice']),
                'bayar'             => (float)str_replace(',', '.', $data['bayar']),
                'kembali'           => (float)str_replace(',', '.', $data['kembali']),
                'metode_pembayaran' => $data['metode_pembayaran'],
                'bank'              => $data['bank'],
                'tanggal'           => date('Y-m-d'),
                'waktu'             => date('H:i:s'),
            ];

            $this->db->insert('apt_transaksi', $transaksi_data);
            $id_transaksi = $this->db->insert_id();
            $detail_batch = [];
            $total_laba_transaksi = 0;

            foreach ($data['detail'] as $item) {
                $db_barang = $this->get_barang_detail_for_transaction($item['id_barang'], $item['satuan_barang']);

                if (!$db_barang) {
                    throw new Exception("Barang {$item['nama_barang']} dengan satuan {$item['satuan_barang']} tidak valid atau tidak ditemukan.");
                }

                $jumlah_beli = (float)str_replace(',', '.', $item['jumlah_beli']);
                $harga = (float)$item['harga_jual']; 
                $laba = (float)$db_barang->laba; 
                
                $sub_total_harga = $jumlah_beli * $harga;
                $sub_total_laba = $jumlah_beli * $laba;

                $detail_batch[] = [
                    'id_transaksi'      => $id_transaksi,
                    'id_barang'         => $item['id_barang'],
                    'id_barang_detail'  => $db_barang->id_barang_detail,
                    'nama_barang'       => $item['nama_barang'],
                    'satuan_barang'     => $item['satuan_barang'],
                    'jumlah'            => $jumlah_beli,
                    'harga'             => $harga,
                    'laba'              => $laba,
                    'sub_total_harga'   => $sub_total_harga,
                    'sub_total_laba'    => $sub_total_laba,
                ];

                $this->_update_stock_hierarchical($item['id_barang'], $db_barang->id_barang_detail, $jumlah_beli);

                $total_laba_transaksi += $sub_total_laba;
            }
            
            if (!empty($detail_batch)) {
                $this->db->insert_batch('apt_transaksi_detail', $detail_batch);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['status' => 'error', 'message' => 'Gagal menyimpan transaksi.'];
            } else {
                $this->db->trans_commit();
                return ['status' => 'success', 'id_transaksi' => $id_transaksi, 'message' => 'Transaksi berhasil disimpan!'];
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function get_barang_detail_for_transaction($id_barang, $satuan)
    {
        return $this->db->select('bd.id as id_barang_detail, s.laba')
            ->from('apt_barang_detail bd')
            ->join('apt_stok s', 's.id_barang_detail = bd.id')
            ->where('bd.id_barang', $id_barang)
            ->where('bd.satuan_barang', $satuan)
            ->get()
            ->row();
    }


    private function _generate_kode_invoice()
    {
        $tanggal = date('dmy');
        $this->db->select('MAX(SUBSTRING(kode_invoice, 12)) as max_urut');
        $this->db->like('kode_invoice', 'INV' . $tanggal, 'after');
        $result = $this->db->get('apt_transaksi')->row();
        
        $urut = 1;
        if ($result && $result->max_urut) {
            $urut = (int)$result->max_urut + 1;
        }

        return 'INV' . $tanggal . sprintf('%04d', $urut);
    }

    private function _update_stock_hierarchical($id_barang, $id_barang_detail_terjual, $jumlah_terjual) {
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
            throw new Exception("Stok untuk barang " . $satuan_terkecil['nama_barang'] . " tidak mencukupi.");
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

    public function cari_pelanggan($search)
    {
        $this->db->select('id, nama_customer AS text');
        $this->db->from('apt_pelanggan');
        if ($search) {
            $this->db->like('nama_customer', $search);
        }
        $this->db->order_by('nama_customer', 'ASC');
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }

    public function get_detail_pelanggan($id_pelanggan)
    {
        return $this->db->get_where('apt_pelanggan', ['id' => $id_pelanggan])->row_array();
    }

    public function get_barang_list($search, $jenis_id)
    {
        $this->db->select('
            b.id as id_barang, 
            bd.id as id_barang_detail, 
            bd.kode_barang,
            b.nama_barang, 
            j.nama_jenis, 
            bd.satuan_barang, 
            s.harga_jual, 
            s.stok
        ');
        $this->db->from('apt_barang b');
        $this->db->join('apt_barang_detail bd', 'bd.id_barang = b.id');
        $this->db->join('apt_jenis_barang j', 'j.id = b.id_jenis_barang', 'left');
        $this->db->join('apt_stok s', 's.id_barang_detail = bd.id'); 
        
        
        if (!empty($search)) {
            $this->db->group_start()
                    ->like('b.nama_barang', $search)
                    ->or_like('bd.kode_barang', $search)
                    ->group_end();
        }

        if ($jenis_id !== 'all') {
            $this->db->where('b.id_jenis_barang', $jenis_id);
        }

        return $this->db->get()->result();
    }

    public function get_barang_by_id($id_barang_detail)
    {
        return $this->db->select('
                b.id as id_barang, 
                bd.id as id_barang_detail, 
                b.nama_barang, 
                bd.satuan_barang, 
                s.harga_jual, 
                s.laba, 
                s.stok
            ')
            ->from('apt_barang_detail bd')
            ->join('apt_barang b', 'b.id = bd.id_barang')
            ->join('apt_stok s', 's.id_barang_detail = bd.id')
            ->where('bd.id', $id_barang_detail)
            ->get()
            ->row();
    }
    
    public function get_all_penjualan()
    {
        return $this->db->select('p.*, u.nama_user, pl.nama_customer')
                        ->from('apt_transaksi p')
                        ->join('user u', 'u.id_user = p.id_user', 'left')
                        ->join('apt_pelanggan pl', 'pl.id = p.id_pelanggan', 'left')
                        ->order_by('p.id_transaksi', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_penjualan_by_id($id_transaksi)
    {
        return $this->db->select('p.*, u.nama_user, pl.nama_customer')
                        ->from('apt_transaksi p')
                        ->join('user u', 'u.id_user = p.id_user', 'left')
                        ->join('apt_pelanggan pl', 'pl.id = p.id_pelanggan', 'left')
                        ->where('p.id_transaksi', $id_transaksi)
                        ->get()
                        ->row();
    }
}