<?php
class M_hak_akses extends CI_Model{
     public function get_data_hak_akses($cari = null)
    {
        $this->db->select('a.*, b.nama_grup_hak_akses');
        $this->db->from('adm_hak_akses a');
        $this->db->join('adm_grup_hak_akses b', 'a.id_grup_hak_akses = b.id', 'left');
        if ($cari) {
            $this->db->like('a.nama_hak_akses', $cari);
        }
        $this->db->order_by('a.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_hak_akses_by_id($id)
    {
        return $this->db->get_where('adm_hak_akses', ['id' => $id])->row_array();
    }

    public function get_grup_hak_akses()
    {
        return $this->db->get('adm_grup_hak_akses')->result();
    }

    public function insert_hak_akses($data)
    {
        return $this->db->insert('adm_hak_akses', $data);
    }

    public function update_hak_akses($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('adm_hak_akses', $data);
    }

    public function delete_hak_akses($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('adm_hak_akses');
    }

    public function get_all_hak_akses_grouped()
    {
        $this->db->select('ha.id as id_akses, ha.nama_hak_akses, gha.id as id_grup, gha.nama_grup_hak_akses');
        $this->db->from('adm_hak_akses ha');
        $this->db->join('adm_grup_hak_akses gha', 'ha.id_grup_hak_akses = gha.id', 'left');
        $this->db->order_by('gha.id', 'ASC');
        $this->db->order_by('ha.id', 'ASC');
        $result = $this->db->get()->result();

        $grouped = [];
        foreach ($result as $row) {
            $grup_nama = $row->nama_grup_hak_akses ? $row->nama_grup_hak_akses : 'Lainnya';
            $grouped[$grup_nama][] = $row;
        }
        return $grouped;
    }

    public function get_akses_by_level($id_level)
    {
        $this->db->select('id_hak_akses');
        $this->db->from('adm_level_akses');
        $this->db->where('id_level', $id_level);
        $result = $this->db->get()->result_array();
        return array_column($result, 'id_hak_akses');
    }

    public function update_akses_level($id_level, $hak_akses_array)
    {
        $this->db->trans_begin();

        $this->db->where('id_level', $id_level);
        $this->db->delete('adm_level_akses');

        if (!empty($hak_akses_array)) {
            $data_akses = [];
            foreach ($hak_akses_array as $id_akses) {
                $data_akses[] = [
                    'id_level' => $id_level,
                    'id_hak_akses' => $id_akses
                ];
            }
            $this->db->insert_batch('adm_level_akses', $data_akses);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
}?>