<?php
class M_level extends CI_Model{
    public function result_data()
    {
        $cari = $this->input->post('cari');
        $sql = "SELECT a.* FROM adm_level a WHERE 1=1";
        $params = [];

        if ($cari != '') {
            $sql .= " AND (a.nama_level LIKE ? )";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";  
        $query = $this->db->query($sql, $params);
        return $query->result();
    }
        public function get_level_by_id($id)
    {
        $sql = $this->db->query("SELECT a.* FROM adm_level a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }
    public function tambah()
    {
        
    $nama = $this->input->post('nama');
    $cek = $this->db->get_where('adm_level', ['nama_level' => $nama])->row_array();
    if ($cek) {
        return [
            'status' => false,
            'message' => 'Level sudah ada!'
        ];
    }

    // Jika belum ada → lanjut insert
    $inputan = [
        'nama_level' => $nama
    ];
        $this->db->trans_begin();
        $this->db->insert('adm_level', $inputan);
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
            'nama_level' => $this->input->post('nama')
        );

        $this->db->trans_begin();

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('adm_level', $inputan);
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
        $this->db->delete('adm_level', ['id' => $this->input->post('id')]);
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
      public function get_sidebar_menu($id_level)
    {
        $this->db->select('g.nama_grup_hak_akses, h.nama_hak_akses, h.link');
        $this->db->from('adm_level_akses la');
        $this->db->join('adm_hak_akses h', 'la.id_hak_akses = h.id');
        $this->db->join('adm_grup_hak_akses g', 'h.id_grup_hak_akses = g.id');
        $this->db->where('la.id_level', $id_level);
        $this->db->order_by('g.id', 'ASC');
        $this->db->order_by('h.id', 'ASC');
        $result = $this->db->get()->result();

        $menu = [];
        foreach ($result as $row) {
            $menu[$row->nama_grup_hak_akses][] = [
                'nama' => $row->nama_hak_akses,
                'link' => $row->link
            ];
        }
        return $menu;
    }
}

?>