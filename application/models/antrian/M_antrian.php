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
        $this->db->order_by('a.id', 'ASC');
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
                'message' => "Pasien Gagal Dipanggil"
            );
        } else {
            $this->db->trans_commit();
            $response = array(
                'status' => true,
                'message' => "Pasien Berhasil Dipanggil"
            );
        }
        return $response;
    }
    public function cek_status()
    {
        // Ambil kode_invoice dari POST data
        $kode_invoice = $this->input->post('kode_invoice');
        if (!$kode_invoice) {
            $response = array(
                'status' => 'error',
                'message' => 'Kode invoice tidak ditemukan'
            );
            return $response;
        }
        $poli_id = $this->input->post('id_poli');

        // ganti dengan kode poli saja
        $mapPoli = [
            16 => 'pol_kecantikan',
            15 => 'pol_gigi',
            14 => 'pol_umum',
            27 => 'pol_anak'
        ];
        if (!isset($mapPoli[$poli_id])) {
            return ['status' => 'poli_tidak_valid'];
        }
        $table = $mapPoli[$poli_id];

        $cek_pol = $this->db->get_where($table, [
            'kode_invoice' => $kode_invoice
        ])->row_array();

        if (!$cek_pol) {
            $response = array(
                'status' => 'tidak_ada_pol',
                'tindakan' => 0,
                'diagnosa' => 0
            );
            return $response;
        }

        $id_pol = $cek_pol['id'];

        // cek tindakan
        $tindakan = $this->db
            ->get_where("{$table}_tindakan", ["id_{$table}" => $id_pol])
            ->num_rows();
        // cek diagnosa
        $diagnosa = $this->db
            ->get_where("{$table}_diagnosa", ["id_{$table}" => $id_pol])
            ->num_rows();

        if (!$tindakan || !$diagnosa) {
            $response = array(
                'status' => 'data_belum_lengkap',
                'tindakan' => $tindakan,
                'diagnosa' => $diagnosa,
                'id_pol' => $id_pol
            );
        }
        $response = array(
            'status' => 'ada_pol',
            'tindakan' => $tindakan,
            'diagnosa' => $diagnosa,
            'id_pol' => $id_pol
        );
        return $response;
    }

    public function cek_konfirmasi()
    {
        $kode_invoice = $this->input->post('kode_invoice');
        $poli_id = $this->input->post('id_poli');

        // ganti dengan kode poli saja
        $mapPoli = [
            16 => 'pol_kecantikan',
            15 => 'pol_gigi',
            14 => 'pol_umum',
            27 => 'pol_anak'
        ];
        if (!isset($mapPoli[$poli_id])) {
            return ['status' => 'poli_tidak_valid'];
        }
        $table = $mapPoli[$poli_id];

        $cek_pol = $this->db->get_where($table, [
            'kode_invoice' => $kode_invoice
        ])->row_array();

        $id_pol = $cek_pol['id']; // id_pol_kecantikan

      // cek tindakan
        $cek_tindakan = $this->db
            ->get_where("{$table}_tindakan", ["id_{$table}" => $id_pol])
            ->num_rows();
        // cek diagnosa
        $cek_diagnosa = $this->db
            ->get_where("{$table}_diagnosa", ["id_{$table}" => $id_pol])
            ->num_rows();

        if (!$cek_tindakan || !$cek_diagnosa) {
            $response = array(
                'status' => 'data_belum_lengkap',
                'tindakan' => $cek_tindakan,
                'diagnosa' => $cek_diagnosa,
                'id_pol' => $id_pol
            );
        }
        $response = array(
            'status' => 'ada',
            'tindakan' => $cek_tindakan,
            'diagnosa' => $cek_diagnosa,
            'id_polis'  => $poli_id,
            'id_poli_datas' => $id_pol 
        );
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
        $id_antrian = $this->input->post('id');

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
        $get_antrian = $this->db->get_where('rsp_antrian', ["id" => $id_antrian])->row_array();

        if (!empty($get_antrian['kode_invoice'])) {
            $get_registrasi = $this->db->get_where("rsp_registrasi", ['kode_invoice' => $get_antrian['kode_invoice']])->row_array();
            $get_pasien = $this->db->get_where("mst_pasien", ['id' => $get_registrasi['id_pasien']])->row_array();

            //cek poli
            $ambil_poli = $this->db->get_where('mst_poli', ['id' => $this->input->post('id_poli')])->row_array();

            // ganti dengan kode poli saja
            $mapPoli = [
                16 => 'pol_kecantikan',
                15 => 'pol_gigi',
                14 => 'pol_umum',
                27 => 'pol_anak'
            ];

            if (array_key_exists($ambil_poli['id'], $mapPoli)) {
                // Ambil table tujuan
                $table = $mapPoli[$ambil_poli['id']];
                $id_pasien = $get_pasien['id'];

                if ($table == 'pol_kecantikan') {

                    // 1. Ambil data terakhir pol_kecantikan berdasarkan id_pasien
                    $lastPol = $this->db->order_by('id', 'DESC')
                        ->get_where('pol_kecantikan', ['id_pasien' => $id_pasien])
                        ->row_array();

                    // 2. Cek detail terakhir di pol_kecantikan_detail berdasarkan id_pasien
                    $lastDetail = $this->db->order_by('id', 'DESC')
                        ->limit(1)
                        ->get_where('pol_kecantikan_detail', ['id_pasien' => $id_pasien])
                        ->row_array();

                    $id_pol_baru = null;

                    if (!$lastPol) {
                        // Insert data BARU dengan kode_invoice BARU
                        $inputan1['riwayat_alergi'] = $this->input->post('riwayat_alergi');
                        $this->db->insert('pol_kecantikan', $inputan1);
                        $id_pol_baru = $this->db->insert_id();

                        $response = [
                            'status' => true,
                            'message' => 'Kunjungan pertama — upload foto SEBELUM',
                            'mode' => 'sebelum',
                            'id_pol_kecantikan' => $id_pol_baru
                        ];
                    } else {
                        if (!$lastDetail || $lastDetail['status'] == 'Sesudah') {
                            $inputan_baru = array(
                                'kode_invoice' => $this->input->post('kode_invoice'), // SELALU BARU
                                'id_pasien' => $id_pasien,
                                'nama_pasien' => $get_pasien['nama_pasien'],
                                'nik' => $get_pasien['nik'],
                                'id_dokter' => $this->input->post('id_dokter'),
                                'nama_dokter' => $this->input->post('nama_dokter'),
                                'tanggal' => $currentDate1,
                                'waktu' => $jam,
                                // AMBIL DATA RIWAYAT DARI KUNJUNGAN SEBELUMNYA
                                'keluhan' => $lastPol['keluhan'],
                                'jenis_treatment' => $lastPol['jenis_treatment'],
                                'riwayat_alergi' => $lastPol['riwayat_alergi'],
                                'produk_digunakan' => $lastPol['produk_digunakan'],
                                'hasil_perawatan' => $lastPol['hasil_perawatan']
                            );

                            $this->db->insert('pol_kecantikan', $inputan_baru);
                            $id_pol_baru = $this->db->insert_id();

                            $response = [
                                'status' => true,
                                'message' => 'Kunjungan baru — upload foto SEBELUM',
                                'mode' => 'sebelum',
                                'id_pol_kecantikan' => $id_pol_baru
                            ];
                        }
                        // Jika foto terakhir adalah SEBELUM → perlu upload SESUDAH
                        else if ($lastDetail['status'] == 'Sebelum') {
                            // BUAT DATA BARU untuk kunjungan ini
                            $inputan_baru = array(
                                'kode_invoice' => $this->input->post('kode_invoice'), // SELALU BARU
                                'id_pasien' => $id_pasien,
                                'nama_pasien' => $get_pasien['nama_pasien'],
                                'nik' => $get_pasien['nik'],
                                'id_dokter' => $this->input->post('id_dokter'),
                                'nama_dokter' => $this->input->post('nama_dokter'),
                                'tanggal' => $currentDate1,
                                'waktu' => $jam,
                                // AMBIL DATA RIWAYAT DARI KUNJUNGAN SEBELUMNYA
                                'keluhan' => $lastPol['keluhan'],
                                'jenis_treatment' => $lastPol['jenis_treatment'],
                                'riwayat_alergi' => $lastPol['riwayat_alergi'],
                                'produk_digunakan' => $lastPol['produk_digunakan'],
                                'hasil_perawatan' => $lastPol['hasil_perawatan']
                            );

                            $this->db->insert('pol_kecantikan', $inputan_baru);
                            $id_pol_baru = $this->db->insert_id();

                            $response = [
                                'status' => true,
                                'message' => 'Upload foto SESUDAH untuk kunjungan sebelumnya',
                                'mode' => 'sesudah',
                                'id_pol_kecantikan' => $id_pol_baru
                            ];
                        }
                    }
                } else {
                    $this->db->insert($table, $inputan1);
                }
            }

            $this->db->where('id', $id_antrian);
            $this->db->update('rsp_antrian', $inputan);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $response = array(
                    'status' => false,
                    'message' => "Pasien Gagal Ditambahkan"
                );
            } else {
                $this->db->trans_commit();
                if (!isset($response)) {
                    $response = array(
                        'status' => true,
                        'message' => "Pasien Berhasil Ditambahkan"
                    );
                }
            }
            return $response;
        }
    }
    public function selectp()
    {
        $id_level = $this->input->post('id_level');
        $this->db->select('id, id_level, id_pegawai, nama_level, nama_pegawai, status, username');
        $this->db->from('adm_user');
        $this->db->where('id_level', $id_level);
        $query = $this->db->get()->row();
        if ($query->id_level) {
            $id_pegawai = $query->id_pegawai;
            $poli = $this->__poli($id_pegawai);
        }
        $response = array(
            'status' => true,
            'message' => "Pengecekan Poli",
            'poli' => $poli,
            'user' => $query
        );

        return $response;
    }
    private function __poli($id_pegawai)
    {
        if ($id_pegawai) {
            $dokter = $this->db->get_where('kpg_dokter', ['id_pegawai' => $id_pegawai])->row();
            if ($dokter) {
                if ((int)$dokter->id_pegawai === (int)$id_pegawai) {
                    $this->db->select('*');
                    $this->db->from('mst_poli');
                    $this->db->where('id', $dokter->id_poli);
                    $hasil = array(
                        'kirim' => $this->db->get()->row(),
                        'status_poli' => 'Dokter ada!'
                    );

                } else {
                    $this->db->select('*');
                    $this->db->from('mst_poli');
                    $hasil = array(
                        'kirim' => $this->db->get()->result(),
                        'status_poli' => 'Tidak ada!'
                    );
                }
            } else {
                $this->db->select('*');
                $this->db->from('mst_poli');
                $hasil = array(
                    'kirim' => $this->db->get()->result(),
                    'status_poli' => 'Tidak ada!'
                );
            }
        }
        return $hasil;
    }


}
?>