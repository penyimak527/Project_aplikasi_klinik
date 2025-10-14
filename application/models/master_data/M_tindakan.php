<?php
class M_tindakan extends CI_Model
{
    //tampil data
    public function result_dat()
    {
        $cari = $this->input->post('cari');

        // $this->db->join('mst_poli', 'mst_poli.id = mst_tindakan.id_poli' );
        $sql = "SELECT a.* FROM mst_tindakan a WHERE 1=1";
        $params = [];
        if ($cari != '') {
            $sql .= " AND (a.nama LIKE ? OR a.nama_poli LIKE ?)";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";
        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    public function row_data($id)
    {
        $sql = $this->db->query("SELECT a.* FROM mst_tindakan a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }
    private function _clean_rupiah($string)
    {
        return (float) preg_replace('/[^0-9]/', '', $string);
    }
    public function result_dt()
    {
        $cari = $this->input->post('cari');

        $this->db->join('mst_poli', 'mst_poli.id = mst_tindakan.id_poli ');
        $sql = "SELECT a.*, b.* FROM mst_tindakan a JOIN mst_poli b ON a.id_poli = b.id WHERE 1=1";
        $params = [];
        if ($cari != '') {
            $sql .= " AND (a.nama LIKE ?)";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";
        $query = $this->db->query($sql, $params);
        return $query->result_array();
    }

    //tambah data
    public function tambah()
    {
        $harga_clean = $this->_clean_rupiah($this->input->post('harga'));
        $inputan = array(
            'nama' => ucfirst($this->input->post('nama')),
            'harga' => $harga_clean,
            'id_poli' => $this->input->post('id_poli'),
            'nama_poli' => $this->input->post('nama_poli')
        );

        $this->db->trans_begin();
        $this->db->insert('mst_tindakan', $inputan);

        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE) {
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

    //edit kirim
    public function edit()
    {
        $harga_clean = $this->_clean_rupiah($this->input->post('harga'));
        $inputan = array(
            'nama' => $this->input->post('nama'),
            'harga' => $harga_clean,
            'id_poli' => $this->input->post('id_poli'),
            'nama_poli' => $this->input->post('nama_poli')
        );

        $this->db->trans_begin();

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('mst_tindakan', $inputan);

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

        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('mst_tindakan');

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

    public function nama_poli()
    {
        $this->db->select('*');
        $this->db->from('mst_poli');
        return $this->db->get()->result();
        ['status' => false, 'msg' => 'data kategori ada'];
    }

}
?>