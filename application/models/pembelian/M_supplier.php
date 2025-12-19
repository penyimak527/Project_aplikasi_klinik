<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Supplier extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function result_data() {
        $cari = $this->input->post('cari');

        $sql = "SELECT a.* FROM apt_supplier a WHERE 1=1";
        $params = [];

        if ($cari != '') {
            $sql .= " AND (a.nama_supplier LIKE ? OR a.kode_supplier LIKE ?)";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function get_all_supplier() {
        $query = $this->db->order_by('nama_supplier', 'ASC')->get('apt_supplier');
        return $query->result_array();
    }

    public function row_data($id) {
        $sql = $this->db->query("SELECT a.* FROM apt_supplier a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }

    public function tambah() {
        $inputan = array(
            'kode_supplier' => $this->input->post('kode_supplier'),
            'nama_supplier' => $this->input->post('nama_supplier'),
            'alamat'        => $this->input->post('alamat'),
            'no_telp'       => $this->input->post('no_telp'),
            'no_rek'        => $this->input->post('no_rek'),
            'bank'          => $this->input->post('bank')
        );

        $this->db->trans_begin();
        $this->db->insert('apt_supplier', $inputan);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => "Data Gagal Ditambahkan: " . $this->db->error()['message']
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => "Data Berhasil Ditambahkan"
            ];
        }
    }

    public function edit() {
        $inputan = array(
            'kode_supplier' => $this->input->post('kode_supplier'),
            'nama_supplier' => $this->input->post('nama_supplier'),
            'alamat'        => $this->input->post('alamat'),
            'no_telp'       => $this->input->post('no_telp'),
            'no_rek'        => $this->input->post('no_rek'),
            'bank'          => $this->input->post('bank')
        );

        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('apt_supplier', $inputan);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => "Data Gagal Diedit: " . $this->db->error()['message']
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => "Data Berhasil Diedit"
            ];
        }
    }

    public function hapus() {
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('apt_supplier');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => "Data Gagal Dihapus: " . $this->db->error()['message']
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => "Data Berhasil Dihapus"
            ];
        }
    }
}
