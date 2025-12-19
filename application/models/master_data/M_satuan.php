<?php
class M_Satuan extends CI_Model{

  function __construct() {
    parent::__construct();
  }

  public function result_data() {
    $cari = $this->input->post('cari');

    $sql = "SELECT a.* FROM apt_satuan_barang a WHERE 1=1";
    $params = [];

    if ($cari != '') {
        $sql .= " AND (a.nama_satuan LIKE ?)";
        $params[] = "%$cari%";
    }

    $sql .= " ORDER BY a.id DESC";

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }

  public function row_data($id) {
    $sql = $this->db->query("SELECT a.* FROM apt_satuan_barang a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }

public function tambah()
{
    $nama = ucwords($this->input->post('satuan'));

    $cek = $this->db->get_where('apt_satuan_barang', [
        'nama_satuan' => $nama
    ])->row_array();

    if ($cek) {
        return [
            'status' => false,
            'message' => 'Nama satuan sudah ada!'
        ];
    }

    $this->db->trans_begin();

    $this->db->insert('apt_satuan_barang', [
        'nama_satuan' => $nama
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
    
    $id_satuan_diedit = $this->input->post('id');
    $nama_satuan_baru = $this->input->post('satuan');

    $inputan_master = array(
      'nama_satuan' => $nama_satuan_baru
    );

    $inputan_detail = array(
      'satuan_barang' => $nama_satuan_baru
    );

    $this->db->trans_begin();

    $this->db->where('id', $id_satuan_diedit);
    $this->db->update('apt_satuan_barang', $inputan_master);

    $this->db->where('id_satuan_barang', $id_satuan_diedit);
    $this->db->update('apt_barang_detail', $inputan_detail);


    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$response = array(
				'status' => false,
				'message' => "Data Gagal Diedit: " . $this->db->error()['message']
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
    $this->db->delete('apt_satuan_barang');

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
