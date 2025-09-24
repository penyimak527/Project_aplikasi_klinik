<?php
class M_contoh extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');

    $sql = "SELECT a.* FROM contoh a WHERE 1=1";
    $params = [];

    if ($cari != '') {
        $sql .= " AND (a.contoh LIKE ?)";
        $params[] = "%$cari%";
    }

    $sql .= " ORDER BY a.id DESC";

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }

  public function row_data($id) {
    $sql = $this->db->query("SELECT a.* FROM contoh a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }

  public function tambah() {
    $inputan = array(
      'contoh' => $this->input->post('contoh')
    );

    $this->db->trans_begin();

    $this->db->insert('contoh', $inputan);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$response = array(
				'status' => false,
				'message' => "Data Gagal Ditambahkan"
			);
		} else {
			$this->db->trans_commit();
			$response = array(
				'status' => true,
				'message' => "Data Berhasil Ditambahkan"
			);
		}

    return $response;
  }

  public function edit(){
    $inputan = array(
      'contoh' => $this->input->post('contoh')
    );

    $this->db->trans_begin();

    $this->db->where('id', $this->input->post('id'));
    $this->db->update('contoh', $inputan);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$response = array(
				'status' => false,
				'message' => "Data Gagal Diedit"
			);
		} else {
			$this->db->trans_commit();
			$response = array(
				'status' => true,
				'message' => "Data Berhasil Diedit"
			);
		}

    return $response;
  }

  public function hapus() {
    $this->db->trans_begin();

    $this->db->where('id', $this->input->post('id'));
    $this->db->delete('contoh');

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$response = array(
				'status' => false,
				'message' => "Data Gagal Dihapus"
			);
		} else {
			$this->db->trans_commit();
			$response = array(
				'status' => true,
				'message' => "Data Berhasil Dihapus"
			);
		}

    return $response;
  }
}
