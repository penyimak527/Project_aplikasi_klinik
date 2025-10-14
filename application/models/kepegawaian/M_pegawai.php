<?php
class M_pegawai extends CI_Model
{
    public function result_data()
    {
        $cari = $this->input->post('cari');

        $sql = 'SELECT a.* FROM kpg_pegawai a WHERE 1=1';
        $params = [];

        if ($cari != '') {
            $sql .= ' AND (a.nama LIKE ?  or a.nama_jabatan LIKE ?)';
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= ' ORDER BY a.id DESC';

        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function row_data($id)
    {
        //  $sql = $this->db->query('SELECT a.* FROM kpg_pegawai a WHERE a.id = ?', array($id));
        // return $sql->row_array(); 
        $this->db->select('a.*, b.id_poli, b.nama_poli');
        $this->db->from('kpg_pegawai a');
        $this->db->where('a.id', $id);
        $this->db->join('kpg_dokter b', 'a.id = b.id_pegawai', 'left');
        return $this->db->get()->row_array();
    }

    public function tambah()
    {
        $this->db->trans_begin();
        $inputan = array(
            'id_jabatan' => $this->input->post('id_jabatan'),
            'nama' => ucwords($this->input->post('nama_pegawai')),
            // 'no_telp' => $this->input->post('no_tp'),
            'nama_jabatan' => $this->input->post('nama_jabatan'),
            'alamat' => ucfirst($this->input->post('alamat'))
        );
        $inputan['no_telp']  = $this->input->post('no_tp');
        $no_tp = preg_replace('/[^0-9]/', '', $inputan['no_telp']);   
         // Validasi no_telp
        if (!preg_match('/^(62|0)[0-9]{9,13}$/', $no_tp)) {
          return array(
            'status' => false,
            'message' => 'Nomor telepon tidak valid. Gunakan format 08xxx atau 62xxx.'
          );
        }
        $this->db->insert('kpg_pegawai', $inputan);
        $id_pegawai = $this->db->insert_id();  // ambil id terakhir

        $nama_pegawai = ucfirst($this->input->post('nama_pegawai'));
        $id_poli = $this->input->post('id_poli');
        $nama_poli = $this->input->post('nama_poli');
        $nama_j = $this->input->post('nama_jabatan');


        if ($nama_j  == 'Dokter') {
            $input1 = array(
                'id_pegawai' => $id_pegawai,
                'nama_pegawai' => $nama_pegawai,
                'id_poli'       => $id_poli,
                'nama_poli'     => $nama_poli
            );
              $this->db->insert('kpg_dokter', $input1);
        }

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
            'id_jabatan' => $this->input->post('id_jabatan'),
            'nama' => ucwords($this->input->post('nama_pegawai')),
            // 'no_telp' => $this->input->post('no_tp'),
            'nama_jabatan' => $this->input->post('nama_jabatan'),
            'alamat' => ucfirst($this->input->post('alamat'))
        );
        $inputan['no_telp']  = $this->input->post('no_tp');
        $no_tp = preg_replace('/[^0-9]/', '', $inputan['no_telp']);   
         // Validasi no_telp
        if (!preg_match('/^(62|0)[0-9]{9,13}$/', $no_tp)) {
          return array(
            'status' => false,
            'message' => 'Nomor telepon tidak valid. Gunakan format 08xxx atau 62xxx.'
          );
        }
        $id_pegawai = $this->input->post('id');
        $namajabat  = strtolower($this->input->post('nama_jabatan'));
        
        $this->db->where('id_pegawai', $id_pegawai)->delete('kpg_dokter');
        
        if ($namajabat == 'dokter') {
        $inputan1 = array(
                'id_pegawai'   => $this->input->post('id'),
                'nama_pegawai' => ucwords($this->input->post('nama_pegawai')),
                'id_poli'      => $this->input->post('id_poli'),
                'nama_poli'      => $this->input->post('nama_poli'),
            );
            $inputan2 = array(
            'id_pegawai'   => $this->input->post('id'),
            'nama_pegawai' => ucwords($this->input->post('nama_pegawai')),
            );
            $this->db->where('id_pegawai', $this->input->post('id'));
            $this->db->insert('kpg_dokter', $inputan1);
        $this->db->where('id_pegawai', $this->input->post('id'));
        $this->db->update('rsp_jadwal_dokter', $inputan2);
        }
        else{
            $this->db->where('id_pegawai', $id_pegawai)->delete('rsp_jadwal_dokter');
        }
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('kpg_pegawai', $inputan);
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

    public function hapus()
    {
        $this->db->trans_begin();

        // hapus pegawai
        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('kpg_pegawai');
        // hapus dokter
        $this->db->where('id_pegawai', $this->input->post('id'));
        $this->db->delete('kpg_dokter');
        // hapus jadwal
        $this->db->where('id_pegawai', $this->input->post('id'));
        $this->db->delete('rsp_jadwal_dokter');

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

    public function nama_jabatan()
    {
        $this->db->select('*');
        $this->db->from('kpg_jabatan');
        return $this->db->get()->result();
        ['status' => false, 'msg' => 'data kategori ada'];
    }

    public function nama_poli()
    {
        $this->db->select('*');
        $this->db->from('mst_poli');
        return $this->db->get()->result();
        ['status' => false, 'msg' => 'data kategori ada'];
    }
}
?>