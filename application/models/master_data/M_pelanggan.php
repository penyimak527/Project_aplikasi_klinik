<?php
class M_pelanggan extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');
    $this->db->select('*');
    $this->db->from('apt_pelanggan');
    if ($cari != '') {
        $this->db->group_start();
        $this->db->like('nama_customer', $cari);
        $this->db->or_like('no_telp', $cari);
        $this->db->group_end();
    }
    $this->db->order_by('id', 'DESC');
    return $this->db->get()->result_array();
  }

  public function hapus() {
    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->delete('apt_pelanggan');
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => "Data Gagal Dihapus"];
		} else {
			$this->db->trans_commit();
			return ['status' => true, 'message' => "Data Berhasil Dihapus"];
		}
  }
}