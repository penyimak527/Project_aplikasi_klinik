<?php
class M_Jenis extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');

    $sql = "SELECT a.* FROM apt_jenis_barang a WHERE 1=1";
    $params = [];

    if ($cari != '') {
        $sql .= " AND (a.nama_jenis LIKE ?)";
        $params[] = "%$cari%";
    }

    $sql .= " ORDER BY a.id DESC";

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }

  public function row_data($id) {
    $sql = $this->db->query("SELECT a.* FROM apt_jenis_barang a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }

public function tambah()
{
    $nama = ucwords($this->input->post('jenis'));

    $cek = $this->db->get_where('apt_jenis_barang', [
        'nama_jenis' => $nama
    ])->row_array();

    if ($cek) {
        return [
            'status' => false,
            'message' => 'Nama jenis sudah ada!'
        ];
    }

    $this->db->trans_begin();

    $this->db->insert('apt_jenis_barang', [
        'nama_jenis' => $nama
    ]);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return [
            'status' => false,
            'message' => 'Data gagal ditambahkan!'
        ];
    }

    $this->db->trans_commit();
    return [
        'status' => true,
        'message' => 'Data berhasil ditambahkan!'
    ];
}


  public function edit(){
    $inputan = array(
      'nama_jenis' => $this->input->post('jenis')
    );

    $this->db->trans_begin();

    $this->db->where('id', $this->input->post('id'));
    $this->db->update('apt_jenis_barang', $inputan);

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
    $this->db->delete('apt_jenis_barang');

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
