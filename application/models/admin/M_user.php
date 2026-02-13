<?php
class M_user extends CI_Model
{
    public function result_data()
    {
        $cari = $this->input->post('cari');
        $sql = "SELECT a.* FROM adm_user a WHERE 1=1";
        $params = [];

        if ($cari != '') {
            $sql .= " AND (a.nama_pegawai LIKE ? OR a.username LIKE ? OR a.nama_level LIKE ? OR a.status LIKE ?)";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }
        $sql .= " ORDER BY a.id DESC";
        $query = $this->db->query($sql, $params);
        return $query->result();
    }
    public function row_data($id)
    {
        $sql = $this->db->query("SELECT a.* FROM adm_user a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }
    public function tambah()
    {
        $id_pegawai = $this->input->post('id_pegawai');
        $nama_pegawai = $this->input->post('nama_pg');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $id_level = $this->input->post('id_level');
        $nama_level = $this->input->post('nama_level');
        $status = $this->input->post('status');
        $cek = $this->db->get_where('adm_user', ['username' => $username])->row_array();
        if ($cek) {
            return [
                'status' => false,
                'message' => 'Username sudah ada!'
            ];
        }
        $pass = password_hash($password, PASSWORD_DEFAULT);
        // Jika belum ada → lanjut insert
        $inputan = [
            'id_pegawai' => $id_pegawai,
            'nama_pegawai' => $nama_pegawai,
            'username' => $username,
            'password' => $pass,
            'id_level' => $id_level,
            'nama_level' => $nama_level,
            'status' => $status,
        ];
        $this->db->trans_begin();
        $this->db->insert('adm_user', $inputan);
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE) {
            $this->db->trans_rollback();
            $response = array(
                'status' => false,
                'message' => 'Data gagal tambah'
            );
        } else {
            $this->db->trans_commit();
            $response = array(
                'status' => true,
                'message' => 'Data berhasil ditambah'
            );
        }
        return $response;
    }
    // kirim edit 
    public function edit()
    {
        $inputan = array(
            'username' => $this->input->post('username'),
            'nama_level' => $this->input->post('nama_level'),
            'id_level' => $this->input->post('id_level'),
            'status' => $this->input->post('status'),
        );
        $password = $this->input->post('password');
        $pass = password_hash($password, PASSWORD_DEFAULT);
        if (!empty($password)) {
            $inputan['password'] = $pass;
        }
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('adm_user', $inputan);
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

    //hapus data 
    public function hapus()
    {
        $this->db->trans_begin();
        $this->db->delete('adm_user', ['id' => $this->input->post('id')]);
        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
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
    public function pegawai()
    {
        $cari = $this->input->post('cari');
        $this->db->select('a.*');
        $this->db->from('kpg_pegawai a');
        if (!empty($cari)) {
            $this->db->group_start();
            $this->db->like('a.nama', $cari);
            $this->db->or_like('a.nama_jabatan', $cari);
            $this->db->group_end();
        }
        $this->db->order_by('a.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function level()
    {
        return $this->db->from('adm_level')->select('*')->get()->result();
    }
}

?>