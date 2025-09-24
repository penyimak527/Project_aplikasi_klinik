<?php
class M_diagnosa extends CI_Model
{

    public function result_data() {
        $cari = $this->input->post('cari');
        $sql = "SELECT a.* FROM mst_diagnosa a WHERE 1=1";
        // $sql = "SELECT a.*, b.nama AS nama_poli FROM mst_diagnosa a LEFT JOIN mst_poli b ON a.id_poli = b.id WHERE 1=1";
        $params = [];

        if ($cari != '') {
            $sql .= " AND (a.nama_diagnosa LIKE ? OR a.nama_poli LIKE ?)";
            $params[] = "%$cari%";
            $params[] = "%$cari%";
        }

        $sql .= " ORDER BY a.id DESC";

        $query = $this->db->query($sql, $params);
        return $query->result_array();

  }

  public function row_data($id){
    $sql = $this->db->query("SELECT a.* FROM mst_diagnosa a WHERE a.id = ?", array($id));
    return $sql->row_array();
  }

    public function tambah()
    {
        $inputan = array(
            'nama_diagnosa'     => ucfirst($this->input->post('nama_diag')),
            'id_poli'           => $this->input->post('id_poli'),
            'nama_poli'         => $this->input->post('nama_poli')
        );

        $this->db->trans_begin();

        $this->db->insert('mst_diagnosa', $inputan);

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

    //edit kirim
    public function edit(){
        $inputan = array(
            'nama_diagnosa'     => ucfirst($this->input->post('nama_diag')),
            'id_poli'           => $this->input->post('id_poli'),
            'nama_poli'         => $this->input->post('nama_poli')
        );
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('mst_diagnosa', $inputan);
        $this->db->trans_complete();
        if ($this->db->trans_status() == FALSE) { 
            $this->db->trans_rollback();
            $response = array(
                'status' => false,
				'message' => "Data Gagal Diedit"
            );
        } else {
            $this->db->trans_commit();
			$response = array(
				'status' => true,
				'message' => "Data Berhasil Diedit");
        }
        return $response;
    }

    //hapus
    public function hapus(){
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('mst_diagnosa');

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