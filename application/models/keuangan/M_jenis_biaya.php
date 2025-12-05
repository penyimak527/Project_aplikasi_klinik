<?php
class M_jenis_biaya extends CI_Model
{
  public function result_data()
  {
    $cari = $this->input->post('cari');
    $sql = "SELECT a.* FROM rsp_jenis_biaya a WHERE 1=1";
    $params = [];
    if ($cari != '') {
      $sql .= " AND (a.nama LIKE ?)";
      $params[] = "%$cari%";
    }
    $sql .= " ORDER BY a.id DESC";
    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }
  public function row_data($id)
  {
    $sql = $this->db->query("SELECT a.* FROM rsp_jenis_biaya a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }
  public function tambah()
  {
    $nama = ucfirst($this->input->post('jenis_biaya'));
    $cek = $this->db->get_where('rsp_jenis_biaya', ['nama' => $nama])->row_array();

    if ($cek) {
      return [
        'status' => false,
        'message' => 'Nama Jenis Biaya sudah ada!'
      ];
    }

    // Jika belum ada → lanjut insert
    $inputan = [
      'nama' => $nama
    ];
    $this->db->trans_begin();
    $this->db->insert('rsp_jenis_biaya', $inputan);
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

  public function edit()
  {
    $inputan = array(
      'nama' => ucfirst($this->input->post('jenis_biaya'))
    );
    $inputantable = array(
      'nama_jenis_biaya' => ucfirst($this->input->post('jenis_biaya'))
    );
    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->update('rsp_jenis_biaya', $inputan);
    $this->db->where('id_jenis_biaya', $this->input->post('id'));
    $this->db->update('rsp_pemasukan', $inputantable);
    $this->db->where('id_jenis_biaya', $this->input->post('id'));
    $this->db->update('rsp_pengeluaran', $inputantable);
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

  public function hapus()
  {
    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->delete('rsp_jenis_biaya');
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
?>