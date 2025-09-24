<?php
class M_dokter extends CI_Model
{
    public function result_data()
    {
        $cari = $this->input->post('cari');
        $sql = 'SELECT a.* FROM kpg_dokter a WHERE 1=1';
        $params = [];
        if ($cari != '') {
            $sql .= ' AND (a.nama_pegawai LIKE ? or a.nama_poli LIKE ?)';
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= ' ORDER BY a.id DESC';

        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    public function row_data($id)
    {
        $sql = $this->db->query('SELECT a.* FROM kpg_dokter a WHERE a.id = ?', array($id));
        return $sql->row_array();
    }

    public function update_jadwal_batch($id, $hari, $jam_mulai, $jam_selesai)
    {
        $dokter = $this->row_data($id);  // FIX
        if (!$dokter)
            return false;

        $this->db->trans_begin();

        // Hapus jadwal lama
        $this->db->where('id_pegawai', $dokter['id_pegawai']);
        $this->db->delete('rsp_jadwal_dokter');

        // Siapkan batch data baru
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

    public function get_jadwal_by_dokter_id($id)
    {
        $sql = "SELECT a.* FROM rsp_jadwal_dokter a 
                JOIN kpg_dokter b ON a.id_pegawai = b.id_pegawai 
                WHERE b.id = ? 
                ORDER BY FIELD(a.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')";
        $query = $this->db->query($sql, array($id));
        // return $query->result();
        return $query->result_array();
    }

    public function get_jadwal_by_id_and_day($id, $hari)
    {
        $sql = 'SELECT a.* FROM rsp_jadwal_dokter a 
            JOIN kpg_dokter b ON a.id_pegawai = b.id_pegawai 
            WHERE b.id = ? AND a.hari = ? 
            LIMIT 1';
        $query = $this->db->query($sql, [$id, $hari]);
        return $query->row_array();  // hanya satu jadwal
    }

    // untuk jadwal
    public function tambah()
    {
        $inputan = array(
            'id_pegawai' => $this->input->post('id_pegawai'),
            'nama_pegawai' => $this->input->post('nama_pegawai'),
            'hari' => $this->input->post('hari'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai')
        );
        $this->db->trans_begin();
        $this->db->insert('rsp_jadwal_dokter', $inputan);
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
    public function hapuss()
    {
        $this->db->trans_begin();
        $this->db->delete('rsp_jadwal_dokter', ['id' => $this->input->post('id')]);

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

    // jadwal edit
    public function jadwal_editt()
    {
        $inputan = array(
            'id_pegawai' => $this->input->post('id_pegawai'),
            'nama_pegawai' => $this->input->post('nama_pegawai'),
            'hari' => $this->input->post('hari'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai'),
        );
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('rsp_jadwal_dokter', $inputan);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $response = array(
                'status' => false,
                'message' => 'Data Gagal Diedit'
            );
        } else {
            $this->db->trans_commit();
            $response = array(
                'status' => true,
                'message' => 'Data Berhasil Diedit'
            );
        }

        return $response;
    }

    public function nama_poli()
    {
        $this->db->select('*');
        $this->db->from('mst_poli');
        return $this->db->get()->result();
        ['status' => false, 'msg' => 'data kategori ada'];
    }

    public function nama_pegawai()
    {
        $this->db->select('*');
        $this->db->from('kpg_pegawai');
        return $this->db->get()->result();
        ['status' => false, 'msg' => 'data kategori ada'];
    }
}
?>