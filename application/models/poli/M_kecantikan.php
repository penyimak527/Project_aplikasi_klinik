<?php
class M_kecantikan extends CI_Model
{
    public function result_data()
    {
        $cari = $this->input->post('cari');
        $this->db->select('a.*, b.no_rm');
        $this->db->join('mst_pasien b', 'b.id = a.id_pasien');
        $this->db->from('pol_kecantikan a');

        if ($cari != '') {
            $this->db->group_start()
                ->like('a.nama_pasien', $cari)
                ->or_like('a.nik', $cari)
                ->or_like('a.nama_dokter', $cari)
                ->group_end();
        }

        $this->db->order_by('a.id', 'DESC');
        return $this->db->get()->result_array();
    }

    private function _clean_rupiah($string)
    {
        return (float) preg_replace('/[^0-9]/', '', $string);
    }

    public function row_datakode($kode_invoice)
    {
        // untuk kpg_dokter saya ambil untuk id_poli
        $this->db->select('a.*, b.id_poli, b.nama_poli, c.no_rm, c.jenis_kelamin, c.tanggal_lahir, c.no_telp, c.alamat');
        $this->db->from('pol_kecantikan a');
        // $this->db->join('kpg_dokter b', 'b.id_pegawai = a.id_dokter');
        $this->db->join('kpg_dokter b', 'b.id_pegawai = a.id_dokter');
        $this->db->join('mst_pasien c', 'c.id = a.id_pasien');
        $this->db->where('a.kode_invoice', $kode_invoice);
        return $this->db->get()->row_array();
    }

    public function tambah_proses()
    {
        $this->db->trans_begin();

        try {
            // 1. Update data utama
            $inputan = array(
                'keluhan' => $this->input->post('keluhan'),
                'jenis_treatment' => $this->input->post('jenis_treatment'),
                'riwayat_alergi' => $this->input->post('r_alergi'),
                'produk_digunakan' => $this->input->post('produk_digunakan'),
                'hasil_perawatan' => $this->input->post('hasil_perawatan')
            );
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('pol_kecantikan', $inputan);
            $id_pol_kecantikan = $this->input->post('id');
            // 2. Proses diagnosa
            $this->_process_diagnosa($id_pol_kecantikan);

            // 3. Proses tindakan
            $this->_process_tindakan($id_pol_kecantikan);

            // 4. Proses upload foto
            $upload_result = $this->_process_upload($id_pol_kecantikan);
            if (!$upload_result['status'] && $upload_result['message'] != 'Tidak ada file yang diupload') {
                throw new Exception($upload_result['message']);
            }

            // $kode_invoice = $this->input->post('kode_invoice');
            // 5. pol resep obat 
            $this->_process_pol_resep();

            // 6. rsp pembayaran
            $this->_process_rsp_pembayaran();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Gagal menyimpan data");
            }

            $this->db->trans_commit();
            return ['status' => true, 'message' => 'Data berhasil disimpan'];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    private function _process_diagnosa($id_pol_kecantikan)
    {
        // Proses diagnosa baru (input text)
        $diag_baru = $this->input->post('diagnosab') ?: [];
        foreach ($diag_baru as $valdb) {
            if (!empty(trim($valdb))) {
                // Cek apakah diagnosa sudah ada
                $existing = $this->db->get_where('mst_diagnosa', [
                    'nama_diagnosa' => $valdb,
                    'id_poli' => $this->input->post('id_poli')
                ])->row();

                if (!$existing) {
                    $this->db->insert('mst_diagnosa', [
                        'id_poli' => $this->input->post('id_poli'),
                        'nama_poli' => $this->input->post('nama_poli'),
                        'nama_diagnosa' => $valdb
                    ]);
                    $diagnosa_id = $this->db->insert_id();
                } else {
                    $diagnosa_id = $existing->id;
                }

                // Tambahkan ke tabel relasi
                $this->db->insert('pol_kecantikan_diagnosa', [
                    'id_pol_kecantikan' => $id_pol_kecantikan,
                    'id_diagnosa' => $diagnosa_id,
                    'diagnosa' => $valdb
                ]);
            }
        }

        // Proses diagnosa existing (dipilih dari dropdown)
        $all_diagnosa = $this->input->post('diagnosa') ?: [];
        $id_diagnosa = $this->input->post('id_diagnosa') ?: []; // Ambil ID diagnosa

        foreach ($all_diagnosa as $index => $diagnosa) {
            if (!empty(trim($diagnosa))) {
                $diagnosa_id = $id_diagnosa[$index] ?? null;

                if ($diagnosa_id) {
                    // Ambil data diagnosa dari database
                    $diagnosa_data = $this->db->get_where('mst_diagnosa', ['id' => $diagnosa_id])->row();

                    if ($diagnosa_data) {
                        $this->db->insert('pol_kecantikan_diagnosa', [
                            'id_pol_kecantikan' => $id_pol_kecantikan,
                            'id_diagnosa' => $diagnosa_id,
                            'diagnosa' => $diagnosa_data->nama_diagnosa
                        ]);
                    }
                }
            }
        }
    }

    private function _process_tindakan($id_pol_kecantikan)
    {
        // Proses tindakan baru (input text)
        $tindakan_baru = $this->input->post('tindakanb') ?: [];
        $harga_baru = $this->input->post('harga_tindakanb') ?: [];

        $harga_baru_clean = array_map([$this, '_clean_rupiah'], $harga_baru);

        if (count($tindakan_baru) !== count($harga_baru_clean)) {
            throw new Exception("Jumlah tindakan dan harga tidak sama");
        }

        for ($i = 0; $i < count($tindakan_baru); $i++) {
            if (!empty(trim($tindakan_baru[$i]))) {
                // Cek apakah tindakan sudah ada
                $existing = $this->db->get_where('mst_tindakan', [
                    'nama' => $tindakan_baru[$i],
                    'id_poli' => $this->input->post('id_poli')
                ])->row();

                if (!$existing) {
                    $this->db->insert('mst_tindakan', [
                        'nama' => $tindakan_baru[$i],
                        'harga' => $harga_baru_clean[$i],
                        'id_poli' => $this->input->post('id_poli'),
                        'nama_poli' => $this->input->post('nama_poli')
                    ]);
                    $tindakan_id = $this->db->insert_id();
                } else {
                    $tindakan_id = $existing->id;
                }

                // Tambahkan ke tabel relasi
                $this->db->insert('pol_kecantikan_tindakan', [
                    'id_rm_kecantikan' => $id_pol_kecantikan,
                    'id_tindakan' => $tindakan_id,
                    'tindakan' => $tindakan_baru[$i],
                    'harga' => $harga_baru_clean[$i]
                ]);
            }
        }

        // Proses tindakan existing (dipilih dari dropdown)
        $tindakan_existing = $this->input->post('tindakan') ?: [];
        $harga_existing = $this->input->post('harga_tindakan') ?: [];
        $id_tindakan = $this->input->post('id_tindakan') ?: [];

        $harga_exiting_clean = array_map([$this, '_clean_rupiah'], $harga_existing);
        foreach ($tindakan_existing as $index => $tindakan) {
            if (!empty(trim($tindakan))) {
                $tindakan_id = $id_tindakan[$index] ?? null;
                $harga = $harga_exiting_clean[$index] ?? 0;

                if ($tindakan_id) {
                    // Ambil data tindakan dari database
                    $tindakan_data = $this->db->get_where('mst_tindakan', ['id' => $tindakan_id])->row();

                    if ($tindakan_data) {
                        $this->db->insert('pol_kecantikan_tindakan', [
                            'id_rm_kecantikan' => $id_pol_kecantikan,
                            'id_tindakan' => $tindakan_id,
                            'tindakan' => $tindakan_data->nama,
                            'harga' => $this->_clean_rupiah($tindakan_data->harga)
                        ]);
                    }
                }
            }
        }
    }

    private function _process_upload($id_pol_kecantikan)
    {
        if (empty($_FILES['upload_foto']['name'])) {
            return ['status' => true, 'message' => 'Tidak ada file yang diupload'];
        }
        $kode_invo = $this->input->post('kode_invoice');
        $status_fo = $this->input->post('status_foto');
        $config['upload_path'] = './upload/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = 50048; // 50MB
        $ext = pathinfo($_FILES['upload_foto']['name'], PATHINFO_EXTENSION);
        $config['file_name'] = $kode_invo . '(' . $status_fo . ')' . '.' . $ext;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('upload_foto')) {
            return ['status' => false, 'message' => $this->upload->display_errors()];
        }

        $data = $this->upload->data();

        $this->db->insert('pol_kecantikan_detail', [
            'id_pol_kecantikan' => $id_pol_kecantikan,
            'status' => $this->input->post('status_foto'),
            'foto' => $data['file_name']
        ]);

        return ['status' => true, 'message' => 'File berhasil diupload'];
    }

    private function _process_pol_resep()
    {
        $timestamp = time();
        $jam = date('H:i:s');
        $currentDate = gmdate('d-m-Y', $timestamp);
        $this->db->select('kode_resep');
        $this->db->from('pol_resep');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $last_rsp = $query->row()->kode_resep;
            $last_numrsp = (int) substr($last_rsp, -3);
            $next_numrsp = $last_numrsp + 1;
            $rsp_new = 'RSP' . '-' . str_pad($next_numrsp, 3, '0', STR_PAD_LEFT);
        } else {
            $rsp_new = 'RSP' . '-' . '001';
        }
      
        $subtotal_hl = $this->input->post('subtotal_hl_all') ?: []; // Ensure it's at least an empty array
        $total = array_sum(array_map(function ($val) {
            return (float) str_replace(['Rp', '.', ','], '', $val);
        }, $subtotal_hl));
        $inputan = array(
            'kode_invoice' => $this->input->post('kode_invoice'),
            'kode_resep' => $rsp_new,
            'id_pasien' => $this->input->post('id_pasien'),
            'nik' => $this->input->post('nik'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'id_dokter' => $this->input->post('id_dokter'),
            'tanggal' => $currentDate,
            'waktu' => $jam,
            'nama_dokter' => $this->input->post('nama_dokter'),
            'total_harga' => $total
        );
        // $this->db->trans_begin();
        $this->db->insert('pol_resep', $inputan);
        $id_rsp = $this->db->insert_id();
        if (!$id_rsp) {
            // Jika insert gagal, lempar exception agar transaksi utama melakukan rollback
            throw new Exception("Gagal menyimpan data resep utama.");
        }
        // kirim ke
        $this->_polrsp_obat($id_rsp);
        $this->_polrsp_racikan($id_rsp);
        return true;
    }

    private function _polrsp_obat($id_rsp)
    {
        $obat = $this->input->post('obat');
        if (!empty($obat)) {
            $batch_data = [];
            foreach ($obat as $key => $item) {
                if (!empty($item['id_obat_detail_o'])) {
                    $batch_data[] = array(
                        'id_pol_resep' => $id_rsp,
                        'id_barang' => $item['id_obat_o'],
                        'id_barang_detail' => $item['id_obat_detail_o'],
                        'nama_barang' => $item['nama_obat_o'],
                        'id_satuan_barang' => $item['id_satuan_o'],
                        'satuan_barang' => $item['satuan_o'],
                        'urutan_satuan' => $item['urutan_satuan_o'],
                        'jumlah' => $item['jumlah_o'],
                        'aturan_pakai' => $item['aturan_pakai_o'],
                        'harga' => $this->_clean_rupiah($item['harga_o']), // Gunakan helper
                        'laba' => $this->_clean_rupiah($item['laba_o']),
                        'sub_total_harga' => $this->_clean_rupiah($item['subtotal_o']),
                        'sub_total_laba' => $this->_clean_rupiah($item['subtotal_laba_o'])
                    );
                }
            }
            if (!empty($batch_data)) {
                $this->db->insert_batch('pol_resep_obat', $batch_data);
            }
        }
    }

    private function _polrsp_racikan($id_rsp)
    {
        $racikan = $this->input->post('racikan');

        if (!empty($racikan)) {
            foreach ($racikan as $key => $item) {
                // Validasi data racikan utama
                if (!isset($item['nama_r']) || !isset($item['jumlah_r'])) {
                    continue; // Lewati jika data tidak lengkap
                }

                // Hitung total harga dan laba dari komponen
                $total_harga_racikan = 0;
                $total_laba_racikan = 0;

                if (isset($item['obat']) && is_array($item['obat'])) {
                    foreach ($item['obat'] as $obat) {
                        // Pastikan semua field yang diperlukan ada
                        if (!isset($obat['subtotal_br']) || !isset($obat['subtotal_laba_br'])) {
                            continue;
                        }

                        $total_harga_racikan += $this->_clean_rupiah($obat['subtotal_br'] ?? 0);
                        $total_laba_racikan += $this->_clean_rupiah($obat['subtotal_laba_br'] ?? 0);
                    }
                }

                // Simpan data racikan utama
                $racikan_data = array(
                    'id_pol_resep' => $id_rsp,
                    'nama_racikan' => $item['nama_r'] ?? '',
                    'jumlah' => $item['jumlah_r'] ?? 1,
                    'aturan_pakai' => $item['aturan_r'] ?? '',
                    'keterangan' => $item['keterangan_r'] ?? '',
                    'harga' => $total_harga_racikan,
                    'laba' => $total_laba_racikan,
                    'sub_total_harga' => $total_harga_racikan * ((float) ($item['jumlah_r'] ?? 1)),
                    'sub_total_laba' => $total_laba_racikan * ((int) ($item['jumlah_r'] ?? 1))
                );

                $this->db->insert('pol_resep_racikan', $racikan_data);
                $id_racikan = $this->db->insert_id();

                // Simpan detail komposisi racikan
                if (isset($item['obat']) && is_array($item['obat'])) {
                    $batch_detail = [];

                    foreach ($item['obat'] as $obat) {
                        // Validasi data obat
                        if (empty($obat['id_barang_br']) || empty($obat['id'])) {
                            continue;
                        }

                        $batch_detail[] = array(
                            'id_pol_resep_racikan' => $id_racikan,
                            'id_barang' => $obat['id_barang_br'] ?? null,
                            'id_barang_detail' => $obat['id'] ?? null,
                            'nama_barang' => $obat['nama_br'] ?? '',
                            'id_satuan_barang' => $obat['id_satuan_br'] ?? null,
                            'satuan_barang' => $obat['satuan_br'] ?? '',
                            'urutan_satuan' => $obat['urutan_satuan_br'] ?? 0,
                            'jumlah' => $this->_clean_rupiah($obat['jumlah_br'] ?? 0),
                            'harga' => $this->_clean_rupiah($obat['harga_br'] ?? 0),
                            'laba' => $this->_clean_rupiah($obat['laba_br'] ?? 0),
                            'sub_total_harga' => $this->_clean_rupiah($obat['subtotal_br'] ?? 0),
                            'sub_total_laba' => $this->_clean_rupiah($obat['subtotal_laba_br'] ?? 0)
                        );
                    }
                    if (!empty($batch_detail)) {
                        $this->db->insert_batch('pol_resep_racikan_detail', $batch_detail);
                    }
                }
            }
        }
    }

    private function _process_rsp_pembayaran()
    {
        $subtotal_hl = $this->input->post('subtotal_hl_all') ?: []; // Ensure it's at least an empty array
        $totalresep = array_sum(array_map(function ($val) {
            return (float) str_replace(['Rp', '.', ','], '', $val);
        }, $subtotal_hl));
        $subtotaltindakan = $this->input->post('utindakan_all') ?: [];
        $totaltindakan = array_sum(array_map(function ($val1) {
            return (float) str_replace(['Rp', '.', ','], '', $val1);
        }, $subtotaltindakan));
        $subtotaltindakan_obat = $this->input->post('subtotal_all_to') ?: [];
        $totaltindakano = array_sum(array_map(function ($val2) {
            return (float) str_replace(['Rp', '.', ','], '', $val2);
        }, $subtotaltindakan_obat));
        $inputan = array(
            'kode_invoice' => $this->input->post('kode_invoice'),
            'id_pasien' => $this->input->post('id_pasien'),
            'nik' => $this->input->post('nik'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'id_dokter' => $this->input->post('id_dokter'),
            'nama_dokter' => $this->input->post('nama_dokter'),
            'biaya_tindakan' => $totaltindakan,
            'biaya_resep' => $totalresep,
            'total_invoice' => $totaltindakano,
        );
        $this->db->insert('rsp_pembayaran', $inputan);
    }

    // untuk mengambil data tindakan yang akan menampilkan pada selected option
    public function tindakan()
    {
        $cari = $this->input->post('carit');

        $this->db->select('id AS id_tindakan, nama, harga, id_poli, nama_poli');
        $this->db->from('mst_tindakan');
        $this->db->group_start()
            ->like('nama_poli', 'kecantikan')
            ->or_like('id_poli', 16)
            ->group_end();

        if (!empty($cari)) {
            $this->db->group_start()
                ->like('nama', $cari)
                ->or_like('harga', $cari)
                ->group_end();
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }

    // untuk mengambil data diagnosa yang akan menampilkan pada selected option
    public function diagnosa()
    {
        $cari = $this->input->post('caridiagnosa');

        $this->db->select('id AS id_diagnosa, nama_diagnosa, nama_poli, id_poli');
        $this->db->from('mst_diagnosa');
        $this->db->group_start()
            ->like('nama_poli', 'kecantikan')
            ->or_like('id_poli', 16)
            ->group_end();

        if (!empty($cari)) {
            $this->db->like('nama_diagnosa', $cari);
        }

        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }

    public function satuan_b()
    {
        $this->db->select('*');
        $this->db->from('apt_satuan_barang');
        return $this->db->get()->result();
    }

    // mengambil data obat
    public function obat()
    {
        $cari = $this->input->post('carit');
        $this->db->select('a.*, b.nama_satuan, c.nama_barang, c.id_jenis_barang, d.harga_jual, d.harga_awal, d.laba');
        $this->db->from('apt_barang_detail a');
        $this->db->join('apt_satuan_barang b', 'b.id = a.id_satuan_barang');
        $this->db->join('apt_barang c', 'c.id = a.id_barang');
        $this->db->join('apt_stok d', 'd.id_barang_detail = a.id');
        $this->db->where('d.stok >', 0);
        $this->db->where('d.harga_awal >', 0);
        $this->db->where('d.harga_jual >', 0);
        $this->db->where('d.laba >', 0);
        $this->db->where('d.kadaluarsa >', 0);
        if ($cari != '') {
            $this->db->group_start()
                ->like('a.nama_barang', $cari)
                ->or_like('a.satuan_barang', $cari)
                ->group_end();
        }

        $this->db->order_by('a.id', 'ASC');
        return $this->db->get()->result_array();
        return $this->db->get()->result();
    }
    public function racikan()
    {
        $this->db->select('*');
        $this->db->from('pol_resep_racikan');
        return $this->db->get()->result_array();
    }
}
?>