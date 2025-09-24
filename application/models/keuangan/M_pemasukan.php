<?php
class M_pemasukan extends CI_Model
{
  public function result_data()
  {
    $cari = $this->input->post('cari');
    $sql = "SELECT a.* FROM rsp_pemasukan a WHERE 1=1";
    $params = [];

    if ($cari != '') {
      $sql .= " AND (a.nama_jenis_biaya LIKE ? or a.keterangan LIKE ?)";
      $params[] = "%$cari%";
      $params[] = "%$cari%";
    }
    $sql .= " ORDER BY a.id DESC";
    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }
  private function _clean_rupiah($string)
  {
    return (float) preg_replace('/[^0-9]/', '', $string);
  }
  public function row_data($id)
  {
    $sql = $this->db->query("SELECT a.* FROM rsp_pemasukan a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }
  public function tambah()
  {
    $timestamp = time();
    $jam = date('H:i:s');
    $currentDate = gmdate('d-m-Y', $timestamp);
    $nominal = $this->_clean_rupiah($this->input->post('nominal'));
    $inputan = array(
      'id_jenis_biaya' => $this->input->post('id_jenis'),
      'nama_jenis_biaya' => ucfirst($this->input->post('nama_jenis')),
      'keterangan' => ucfirst($this->input->post('keterangan')),
      'nominal' => $nominal,
      'tanggal' => $currentDate,
      'waktu' => $jam
    );
    // $lain = $this->input->post('nama_jenis');
    // $input_lain = ucfirst($this->input->post('lainnya'));
    // if ($lain == 'Lain-lain') {
    //      $input1 = array(
    //             'nama'    => $input_lain
    //         );
    //           $this->db->insert('rsp_jenis_biaya', $input1);
    //           $jenis_biaya = $this->db->insert_id();
    //           $inputan['nama_jenis_biaya']  = $input_lain;
    //           $inputan['id_jenis_biaya']  = $jenis_biaya;
    // }
    $this->db->trans_begin();
    $this->db->insert('rsp_pemasukan', $inputan);
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
  public function jenis()
  {
    $this->db->select('*');
    $this->db->from('rsp_jenis_biaya');
    return $this->db->get()->result();
  }
  public function edit()
  {
    $nominal = $this->_clean_rupiah($this->input->post('nominal'));
    $inputan = array(
      'id_jenis_biaya' => $this->input->post('id_jenis'),
      'nama_jenis_biaya' => ucfirst($this->input->post('nama_jenis')),
      'keterangan' => ucfirst($this->input->post('keterangan')),
      'nominal' => $nominal
    );
    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->update('rsp_pemasukan', $inputan);
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
    $this->db->delete('rsp_pemasukan');
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