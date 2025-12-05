<?php
class M_grup_hak_akses extends CI_Model{
    public function get_data_grup($cari = null)
    {
        $sql = "SELECT a.* FROM adm_grup_hak_akses a WHERE 1=1";
        $params = [];

        if ($cari) {
            $sql .= " AND a.nama_grup_hak_akses LIKE ?";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";
        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    public function get_grup_by_id($id)
    {
        $sql = "SELECT a.* FROM adm_grup_hak_akses a WHERE a.id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row_array();
    }

    public function cek_duplikat($nama_grup)
    {
        $this->db->where('nama_grup_hak_akses', $nama_grup);
        $query = $this->db->get('adm_grup_hak_akses');
        if ($query->num_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function insert_grup($data)
    {
        $nama_grup = $data['nama_grup_hak_akses'];
        if ($this->cek_duplikat($nama_grup)) {
            return false;
        } else {
            $this->db->insert('adm_grup_hak_akses', $data);
            return $this->db->affected_rows() > 0;
        }
    }

    public function update_grup($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('adm_grup_hak_akses', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete_grup($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('adm_grup_hak_akses');
        return $this->db->affected_rows() > 0;
    }

}?>