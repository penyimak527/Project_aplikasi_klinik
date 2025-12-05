<?php
class M_booking extends CI_Model
{
    public function result_data()
    {
        $cari = $this->input->post('cari');

        $this->db->select('a.*');
        $this->db->from('rsp_booking a');
        if (!empty($cari)) {
            $this->db->like('a.nama_pasien', $cari);
            $this->db->or_like('a.kode_booking', $cari);
            $this->db->or_like('a.nama_poli', $cari);
            $this->db->or_like('a.nama_dokter', $cari);
        }

        $this->db->order_by('a.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function filter()
    {
        $status = $this->input->post('status');
        return $this->db->get_where('rsp_booking', ['status_booking' => $status])->result();
    }

    public function pasien($cari = null)
    {
        $cari = $this->input->post('cari');
        $this->db->select('a.*');
        $this->db->from('mst_pasien a');
        if (!empty($cari)) {
            $this->db->group_start();
            $this->db->like('a.nama_pasien', $cari);
            $this->db->or_like('a.nik', $cari);
            $this->db->or_like('a.umur', $cari);
            $this->db->or_like('a.jenis_kelamin', $cari);
            $this->db->group_end();
        }
        $this->db->order_by('a.id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function row_data($id)
    {
        $this->db->select('a.*');
        $this->db->from('rsp_booking a');
        $this->db->where('a.id', $id);
        return $this->db->get()->row_array();
    }

    public function tambah()
    {
        $timestamp = time();
        $jam = date('H:i');
        $tanggal = gmdate('dmY', $timestamp);
        $currentDate = gmdate('d-m-Y', $timestamp);
        $tanggalHariIni = gmdate('dmY', time());
        $this->db->select('kode_booking');
        $this->db->from('rsp_booking');
        $this->db->like('kode_booking', 'KB' . $tanggalHariIni, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $last_ko = $query->row()->kode_booking;
            $last_num = (int) substr($last_ko, -3);
            $next_num = $last_num + 1;
            $kode_format = 'KB' . $tanggalHariIni . '-' . str_pad($next_num, 3, '0', STR_PAD_LEFT);
        } else {
            $kode_format = 'KB' . $tanggalHariIni . '-' . '001';
        }

        $inputan = array(
            'id_pasien' => $this->input->post('id_pasien'),
            'nik' => $this->input->post('nik'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'id_poli' => $this->input->post('id_poli'),
            'nama_poli' => $this->input->post('nama_poli'),
            'kode_booking' => $kode_format,
            'tanggal_booking' => $currentDate,
            'waktu_booking' => $jam,
            'tanggal' => $this->input->post('tanggal'),
            'waktu' => $this->input->post('waktu'),
            'status_booking' => 'Pending',
            'id_dokter' => $this->input->post('id_dokter'),
            'nama_dokter' => $this->input->post('nama_dokter')
        );
        $this->db->trans_begin();
        $this->db->insert('rsp_booking', $inputan);
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
            'id_poli' => $this->input->post('id_poli'),
            'nama_poli' => $this->input->post('nama_poli'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'nik' => $this->input->post('nik'),
            'id_dokter' => $this->input->post('id_dokter'),
            'nama_dokter' => $this->input->post('nama_dokter'),
            'tanggal' => $this->input->post('tanggal'),
            'waktu' => $this->input->post('waktu'),
        );
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id_pasien'));
        $this->db->update('mst_pasien', [
            'nama_pasien' => $this->input->post('nama_pasien'),
            'nik' => $this->input->post('nik'),
        ]);
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('rsp_booking', $inputan);
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
    public function kirimsta()
    {
        $timestamp = time();
        $jam = date('H:i:s');
        $currentDate = gmdate('dmY', $timestamp);
        $currentDate1 = gmdate('d-m-Y', $timestamp);
        $tanggalHariIni = gmdate('dmY', time());
        $this->db->select('kode_invoice');
        $this->db->from('rsp_registrasi');
        $this->db->like('kode_invoice', 'KI' . $tanggalHariIni, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $last_ki = $query->row()->kode_invoice;
            $last_numki = (int) substr($last_ki, -3);
            $next_numki = $last_numki + 1;
            $ki_new = 'Ki' . $tanggalHariIni . '-' . str_pad($next_numki, 3, '0', STR_PAD_LEFT);
        } else {
            $ki_new = 'KI' . $tanggalHariIni . '-' . '001';
        }

        $id = $this->input->post('id');
        $kode_booking = $this->input->post('kode_booking');
        $status = $this->input->post('status');
        if (!$id || !$status) {
            return ['status' => false, 'message' => 'Data tidak valid'];
        }
        $cek = $this->db->get_where('rsp_booking', ['id' => $id])->row();
        if (!$cek) {
            return ['status' => false, 'message' => 'tidak ada data yang sama'];
        }
        $this->db->where('id', $id);
        $update = $this->db->update('rsp_booking', ['status_booking' => $status]);

        if ($update) {
            $inputan = array(
                'kode_invoice' => $ki_new,
                'id_booking' => $id,
                'kode_booking' => $kode_booking,
                'id_poli' => $this->input->post('id_poli'),
                'nama_poli' => $this->input->post('nama_poli'),
                'id_dokter' => $this->input->post('id_dokter'),
                'nama_dokter' => $this->input->post('nama_dokter'),
                'id_pasien' => $this->input->post('id_pasien'),
                'nama_pasien' => $this->input->post('nama_pasien'),
                'nik' => $this->input->post('nik'),
                'tanggal' => $currentDate1,
                'waktu' => $jam,
                'status_registrasi' => 'Sukses',
            );

            $poli = $this->db->get_where('mst_poli', ['id' => $this->input->post('id_poli')])->row_array();
            $tanggalantri = gmdate('d-m-Y', time());
            $this->db->select('no_antrian');
            $this->db->from('rsp_antrian');
            // $this->db->like('no_antrian', $poli['kode'].$tanggalHariIni, 'after');
            $this->db->where('tanggal_antri', $tanggalantri);
            $this->db->where('id_poli', $this->input->post('id_poli'));
            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $query3 = $this->db->get();

            if ($query3->num_rows() > 0) {
                $last_kp = $query3->row()->no_antrian;
                $last_numkp = (int) substr($last_kp, -3);
                $next_numkp = $last_numkp + 1;
                $kp_new = $poli['kode'] . '-' . str_pad($next_numkp, 3, '0', STR_PAD_LEFT);
            } else {
                $kp_new = $poli['kode'] . '-' . str_pad(1, 3, '0', STR_PAD_LEFT);
                // $kp_new = $poli['kode'].'-'.'001';
            }
            // antrian
            $inputanan = array(
                'kode_invoice' => $ki_new,
                'no_antrian' => $kp_new,
                'id_poli' => $this->input->post('id_poli'),
                'nama_poli' => $this->input->post('nama_poli'),
                'id_dokter' => $this->input->post('id_dokter'),
                'tanggal_antri' => $currentDate1,
                'waktu_antri' => $jam,
                'tanggal' => $currentDate1,
                'waktu' => $jam,
                'status_antrian' => 'Menunggu',
            );

            $this->db->trans_begin();
            $this->db->insert('rsp_registrasi', $inputan);
            $this->db->insert('rsp_antrian', $inputanan);
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
        } else {
            return ['status' => false, 'message' => 'Data gagal terkirim'];
        }
    }
    public function hapus()
    {
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('rsp_booking');

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



    public function nama_poli()
    {
        $this->db->select('*');
        $this->db->from('mst_poli');
        return $this->db->get()->result();
    }

    public function dokter()
    {
        $id_poli = $this->input->post('id_poli');
        $hari = $this->input->post('hari');
        $waktu = $this->input->post('waktu');

        // Validasi input
        if (!$id_poli) {
            echo json_encode(['status' => false, 'message' => 'ID Poli tidak ditemukan']);
            return;
        }

        $this->db->select('jd.*, d.nama_pegawai, d.id_pegawai, d.id_poli');
        $this->db->from('rsp_jadwal_dokter jd');
        $this->db->join('kpg_dokter d', 'jd.nama_pegawai = d.nama_pegawai');
        // $this->db->join('mst_poli p', 'd.id_poli = p.id');
        $this->db->where('d.id_poli', $id_poli);

        // Tambahkan kondisi hari dan waktu hanya jika ada
        if ($hari) {
            $this->db->where('jd.hari', $hari);
        }
        if ($waktu) {
            $this->db->where('jd.jam_mulai <=', $waktu);
            $this->db->where('jd.jam_selesai >=', $waktu);
        }

        return $this->db->get()->result();
    }

}
?>