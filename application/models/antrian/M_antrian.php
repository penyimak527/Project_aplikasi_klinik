<?php
class M_antrian extends CI_Model
{
    public function result_data()
    {
        $timestamp = time();
        // $jam = date('H:i:s');
        $tanggal = gmdate('d-m-Y', $timestamp);
        $poli = $this->input->post('poli');
        $this->db->select('a.* , b.kode_invoice, b.id_pasien, b.nama_pasien,  b.nik, b.id_dokter, b.nama_dokter, c.alergi');
        $this->db->from('rsp_antrian a');
        if (!empty($poli)) {
            $this->db->where('a.id_poli', $poli);
        }
        $this->db->where('a.tanggal', $tanggal);
        $this->db->join('rsp_registrasi b', 'b.kode_invoice = a.kode_invoice');
        $this->db->join('mst_pasien c', 'c.id = b.id_pasien');
        return $this->db->get()->result_array();

    }
    //memanggil antrain yang dipanggil
    public function result_p()
    {
        $this->db->select('a.no_antrian, b.nama, c.nama_pegawai');
        $this->db->from('rsp_antrian a');
        $this->db->join('mst_poli b', 'a.id_poli = b.id');
        $this->db->join('kpg_dokter c', 'a.id_dokter = c.id_pegawai');
        $this->db->where('a.status_antrian != ', 'Dipanggil');
        $this->db->order_by('a.waktu_antri', 'ASC');
        return $this->db->get()->result_array();
    }
    public function result_card()
    {
        $this->db->select('a.*, b.nama_pegawai');
        $this->db->from('rsp_antrian a');
        $this->db->join('kpg_dokter b', 'a.id_dokter = b.id_pegawai');
        $this->db->where('a.status_antrian', 'Dipanggil');
        $this->db->order_by('a.waktu_antri', 'ASC');
        return $this->db->get()->result_array();
    }
    public function result_sp()
    {
        $this->db->select('a.no_antrian, b.nama, c.nama_pegawai');
        $this->db->from('rsp_antrian a');
        $this->db->join('mst_poli b', 'a.id_poli = b.id');
        $this->db->join('kpg_dokter c', 'a.id_dokter = c.id_pegawai');
        $this->db->where('a.status_antrian', 'Dipanggil');
        $this->db->order_by('a.waktu_antri', 'ASC');
        return $this->db->get()->result_array();
    }
    public function panggil()
    {
        // Cek status antrian sekarang
        $cek = $this->db->get_where('rsp_antrian', ['id' => $this->input->post('id')])->row_array();
        if ($cek['status_antrian'] == 'Dipanggil') {
            return [
                'status' => true,
                'message' => 'Pasien sudah pernah dipanggil'
            ];
        }
        $timestamp = time();
        $jam = date('H:i:s');
        $currentDate1 = gmdate('d-m-Y', $timestamp);
        $inputan = array(
            'status_antrian' => 'Dipanggil',
            'tanggal' => $currentDate1,
            'tanggal_dipanggil' => $currentDate1,
            'waktu' => $jam,
            'waktu_dipanggil' => $jam,
        );
        $this->db->trans_begin();
        $ambil = $this->db->get_where('rsp_antrian', ['id' => $this->input->post('id')])->row_array();
        $jam1 = strtotime($ambil['waktu_antri']);
        $jam2 = strtotime($jam);
        $hitung = $jam2 - $jam1;
        $hasil = gmdate("H:i:s", $hitung);
        $inputan['lama_menunggu'] = $hasil;
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('rsp_antrian', $inputan);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $response = array(
                'status' => false,
                'message' => "Data Gagal Dipanggil"
            );
        } else {
            $this->db->trans_commit();
            $response = array(
                'status' => true,
                'message' => "Data Berhasil Dipanggil"
            );
        }
        return $response;
    }

    public function selesai()
    {
        $timestamp = time();
        $jam = date('H:i:s');
        $currentDate1 = gmdate('d-m-Y', $timestamp);
        $inputan = array(
            'status_antrian' => 'Konfirmasi'
        );
        $inputan1 = array(
            'kode_invoice' => $this->input->post('kode_invoice'),
            'id_pasien' => $this->input->post('id_pasien'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'nik' => $this->input->post('nik'),
            'id_dokter' => $this->input->post('id_dokter'),
            'nama_dokter' => $this->input->post('nama_dokter'),
            'tanggal' => $currentDate1,
            'waktu' => $jam,
        );
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('rsp_antrian', $inputan);
        //cek poli
        $ambil_poli = $this->db->get_where('mst_poli', ['id' => $this->input->post('id_poli')])->row_array();
        // ganti dengan kode poli saja
        if ($ambil_poli['nama'] == 'Poli Kecantikan' || $ambil_poli['id'] == 16) {
            $inputan1['riwayat_alergi'] = $this->input->post('riwayat_alergi');
            $this->db->insert('pol_kecantikan', $inputan1);
        } elseif ($ambil_poli['nama'] == 'Poli Gigi' || $ambil_poli['id'] == 15) {
            $this->db->insert('pol_gigi', $inputan1);
        } elseif ($ambil_poli['nama'] == 'Poil Umum' || $ambil_poli['id'] == 14) {
            $this->db->insert('pol_umum', $inputan1);
        } elseif ($ambil_poli['nama'] == 'Poli Anak' || $ambil_poli['id'] == 27) {
            $this->db->insert('pol_anak', $inputan1);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
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
    public function poli(){
    $this->db->select("*");
    $this->db->from('mst_poli');
    return $this->db->get()->result();
    }


}
?>