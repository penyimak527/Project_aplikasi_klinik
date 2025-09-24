<?php
class M_jadwal extends CI_Model
{
  public function get_all($id_poli = null)
  {
    $sql = 'SELECT a.hari, a.jam_mulai, a.jam_selesai,
                   b.id as id_kpg_dokter, b.id_pegawai, b.nama_pegawai, b.nama_poli 
            FROM rsp_jadwal_dokter a
            JOIN kpg_dokter b ON a.id_pegawai = b.id_pegawai';

    $params = [];
    if ($id_poli) {
      $sql .= ' WHERE b.id_poli = ?';
      $params[] = $id_poli;
    }

    $sql .= " ORDER BY b.nama_poli, b.nama_pegawai, FIELD(a.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";

    $query = $this->db->query($sql, $params);
    return $query->result();
  }

  public function row_data($id)
  {
    $sql = $this->db->query('SELECT a.* FROM rsp_jadwal_dokter a WHERE a.id = ?', array($id));
    return $sql->row_array();
  }

  public function update_jadwal_batch($id, $hari, $jam_mulai, $jam_selesai)
  {
    $this->load->model('kepegawaian/m_dokter');
    $dokter = $this->m_dokter->row_data($id);
    if (!$dokter)
      return false;

    $this->db->trans_begin();

    $this->db->where('id_pegawai', $dokter['id_pegawai']);
    $this->db->delete('rsp_jadwal_dokter');

    $batch_data = [];
    if ($hari) {
      foreach ($hari as $h) {
        if (!empty($jam_mulai[$h]) && !empty($jam_selesai[$h])) {
          $batch_data[] = [
            'id_pegawai' => $dokter['id_pegawai'],
            'nama_pegawai' => $dokter['nama_pegawai'],
            'hari' => $h,
            'jam_mulai' => $jam_mulai[$h],
            'jam_selesai' => $jam_selesai[$h]
          ];
        }
      }
    }

    if (!empty($batch_data)) {
      $this->db->insert_batch('rsp_jadwal_dokter', $batch_data);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
  }

  // hapus
  public function hapus($id)
  {
    $this->load->model('kepegawaian/m_dokter');
    $dokter = $this->m_dokter->row_data($id);
    if (!$dokter)
      return false;

    $this->db->where('id_pegawai', $dokter['id_pegawai']);
    $this->db->delete('rsp_jadwal_dokter');
    return $this->db->affected_rows() >= 0;
  }

  public function dokter()
  {
    $this->db->select('*');
    $this->db->from('kpg_dokter');
    return $this->db->get()->result();
  }

  public function pegawai()
  {
    return $this->db->get_where('kpg_pegawai', ['nama_jabatan' => 'Dokter'])->result();
  }
}
?>