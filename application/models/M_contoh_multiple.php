<?php
class M_contoh_multiple extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');

    $sql = "SELECT a.* FROM contoh_multiple a WHERE 1=1";
    $params = [];

    if ($cari != '') {
        $sql .= " AND (a.contoh_multiple LIKE ?)";
        $params[] = "%$cari%";
    }

    $sql .= " ORDER BY a.id DESC";

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }

  public function row_data($id) {
    $sql = $this->db->query("SELECT a.* FROM contoh_multiple a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }

  public function row_data_detail($id) {
    $sql = $this->db->query("SELECT a.* FROM contoh_multiple_detail a WHERE a.id_contoh_multiple = ?", array($id));
    return $sql->result_array();
  }

  public function result_data_detail() {
    $id = $this->input->post('id');
    $sql = $this->db->query("SELECT a.* FROM contoh_multiple_detail a WHERE a.id_contoh_multiple = ?", array($id));
    return $sql->result_array();
  }

  public function tambah() {
    $inputan = array(
      'contoh' => $this->input->post('contoh')
    );

    $this->db->trans_begin();

    $this->db->insert('contoh_multiple', $inputan);
    $id_contoh_multiple = $this->db->insert_id();

    $contoh_multiple = $this->input->post('contoh_multiple');
    $data_detail = [];
    foreach ($contoh_multiple as $key => $val_d) {
      $data_detail[] = array(
        'id_contoh_multiple' => $id_contoh_multiple,
        'contoh_multiple' => $val_d
      );
    }

    $this->db->insert_batch('contoh_multiple_detail', $data_detail);

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
    $id_contoh_multiple = $this->input->post('id');
    $inputan = array(
      'contoh' => $this->input->post('contoh')
    );

    $this->db->trans_begin();

    $this->db->where('id', $id_contoh_multiple);
    $this->db->update('contoh_multiple', $inputan);

    $this->db->where('id_contoh_multiple', $id_contoh_multiple);
    $this->db->delete('contoh_multiple_detail');

    $contoh_multiple = $this->input->post('contoh_multiple');
    $data_detail = [];
    foreach ($contoh_multiple as $key => $val_d) {
      $data_detail[] = array(
        'id_contoh_multiple' => $id_contoh_multiple,
        'contoh_multiple' => $val_d
      );
    }

    $this->db->insert_batch('contoh_multiple_detail', $data_detail);

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
    $this->db->delete('contoh_multiple');

    $this->db->where('id_contoh_multiple', $this->input->post('id'));
    $this->db->delete('contoh_multiple_detail');

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
