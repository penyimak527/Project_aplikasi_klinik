<?php
class M_pasien extends CI_Model
{
  public function result_data()
  {
    $cari = $this->input->post('cari');
    $sql = 'SELECT a.* FROM mst_pasien a WHERE 1=1';
    $params = [];
    if ($cari != '') {
      $sql .= ' AND (a.nama_pasien LIKE ? OR a.nik LIKE ? OR a.no_rm LIKE ?)';
      $params[] = "%$cari%";
      $params[] = "%$cari%";
      $params[] = "%$cari%";
    }

    $sql .= ' ORDER BY a.id DESC';

    $query = $this->db->query($sql, $params);
    return $query->result_array();
  }
  public function row_data($id)
  {
    $sql = $this->db->query('SELECT a.* FROM mst_pasien a WHERE a.id = ?', array($id));
    return $sql->row_array();
  }

  public function tambah()
  {
    $timestamp = time();
    $currentDate = gmdate('dmY', $timestamp);
    $this->db->from('mst_pasien');
    $this->db->select('no_rm');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      $las_rm = $query->row()->no_rm;
      $last_num = (int) substr($las_rm, 5);
      $net_rm = $last_num + 1;
      $rm_new = 'RM' . str_pad($net_rm, 5, '0', STR_PAD_LEFT);
    } else {
      $rm_new = 'RM' . '00001';
    }
    $inputan = array(
      'no_rm' => $rm_new,
      'nama_pasien' => ucwords($this->input->post('nama_pasien')),
      'nik' => $this->input->post('nik'),
      'jenis_kelamin' => $this->input->post('jk'),
      'tanggal_lahir' => $this->input->post('tgl_lahir'),
      'umur' => $this->input->post('umur'),
      'alamat' => ucwords($this->input->post('alamat')),
      'pekerjaan' => ucwords($this->input->post('pekerjaan')),
      // 'no_telp' => $this->input->post('no_tp'),
      'status_perkawinan' => $this->input->post('st_perkawinan'),
      'nama_wali' => ucwords($this->input->post('nama_wali')),
      'golongan_darah' => $this->input->post('golongan_darah'),
      'alergi' => ucwords($this->input->post('alergi')),
      'status_operasi' => ucwords($this->input->post('status_op')),
      // 'username' => $this->input->post('username'),
      // 'password' => $this->input->post('password'),
    );
    $inputan['no_telp'] = $this->input->post('no_tp');
    $no_tp = preg_replace('/[^0-9]/', '', $inputan['no_telp']);


    // Validasi no_telp
    if (!preg_match('/^(62|0)[0-9]{9,13}$/', $no_tp)) {
      return array(
        'status' => false,
        'message' => 'Nomor telepon tidak valid. Gunakan format 08xxx atau 62xxx.'
      );
    }

    $this->db->trans_begin();
    $this->db->insert('mst_pasien', $inputan);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      $response = array(
        'status' => false,
        'message' => 'Data Gagal Ditambahkan'
      );
    } else {
      $this->db->trans_commit();
      $response = array(
        'status' => true,
        'message' => 'Data Berhasil Ditambahkan'
      );
    }
    return $response;
  }

  public function edit()
  {
    $inputan = array(
      'nama_pasien' => ucfirst($this->input->post('nama_pasien')),
      'nik' => $this->input->post('nik'),
      'jenis_kelamin' => $this->input->post('jk'),
      'tanggal_lahir' => $this->input->post('tgl_lahir'),
      'umur' => $this->input->post('umur'),
      'alamat' => ucfirst($this->input->post('alamat')),
      'pekerjaan' => ucfirst($this->input->post('pekerjaan')),
      'no_telp' => $this->input->post('no_tp'),
      'status_perkawinan' => $this->input->post('st_perkawinan'),
      'nama_wali' => ucfirst($this->input->post('nama_wali')),
      'golongan_darah' => $this->input->post('golongan_darah'),
      'alergi' => ucfirst($this->input->post('alergi')),
      'status_operasi' => ucfirst($this->input->post('status_op')),
      // 'username' => $this->input->post('username'),
      // 'password' => $this->input->post('password'),
    );

    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->update('mst_pasien', $inputan);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      $response = array(
        'status' => false,
        'message' => 'Data Gagal Ditambahkan'
      );
    } else {
      $this->db->trans_commit();
      $response = array(
        'status' => true,
        'message' => 'Data Berhasil Ditambahkan'
      );
    }
    return $response;
  }

  public function hapus()
  {
    $this->db->trans_begin();
    $this->db->where('id', $this->input->post('id'));
    $this->db->delete('mst_pasien');
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      $response = array(
        'status' => false,
        'message' => 'Data Gagal Dihapus'
      );
    } else {
      $this->db->trans_commit();
      $response = array(
        'status' => true,
        'message' => 'Data Berhasil Dihapus'
      );
    }

    return $response;
  }
}
?>