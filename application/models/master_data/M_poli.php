<?php
class M_poli extends CI_Model
{

    //ambil semua data 
    public function result_dat()
    {
        $cari = $this->input->post('cari');
        $sql = "SELECT a.* FROM mst_poli a WHERE 1=1";
        $params = [];

        if ($cari != '') {
            $sql .= " AND (a.nama LIKE ? OR a.kode LIKE ?)";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";

        $query = $this->db->query($sql, $params);
        return $query->result();
    }

    public function row_data($id)
    {
        $sql = $this->db->query("SELECT a.* FROM mst_poli a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }
    public function tambah()
    {
        $inputan = array(
            'kode' => $this->input->post('kode'),
            'nama' => ucwords($this->input->post('nama')),
        );
        $this->db->trans_begin();
        $this->db->insert('mst_poli', $inputan);
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
            'kode' => $this->input->post('kode'),
            'nama' => ucwords($this->input->post('nama'))
        );
        $inputanedit = array(
            'nama_poli' => ucwords($this->input->post('nama'))
        );

        $this->db->trans_begin();

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('mst_poli', $inputan);
        $this->db->where('id_poli', $this->input->post('id'));
        $this->db->update('mst_diagnosa', $inputanedit);
        $this->db->where('id_poli', $this->input->post('id'));
        $this->db->update('mst_tindakan', $inputanedit);
        $this->db->where('id_poli', $this->input->post('id'));
        $this->db->update('kpg_dokter', $inputanedit);
        $this->db->where('id_poli', $this->input->post('id'));
        $this->db->update('rsp_booking', $inputanedit);
        $this->db->where('id_poli', $this->input->post('id'));
        $this->db->update('rsp_registrasi', $inputanedit);

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
        $this->db->delete('mst_poli', ['id' => $this->input->post('id')]);
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
}
?>