<?php
class M_umum extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_by_kode_invoice($kode_invoice)
    {
        $this->db->where('kode_invoice', $kode_invoice);
        $query = $this->db->get('pol_umum');
        return $query->row_array(); // ambil satu baris data (bukan array banyak)
    }

    public function get_diagnosa_terisi($id_pol_umum)
    {
        $this->db->select('d.id_diagnosa, md.nama_diagnosa');
        $this->db->from('pol_umum_diagnosa d');
        $this->db->join('mst_diagnosa md', 'md.id = d.id_diagnosa', 'left');
        $this->db->where('d.id_pol_umum', $id_pol_umum);
        return $this->db->get()->result_array();
    }

    public function get_tindakan_terisi($id_pol_umum)
    {
        $this->db->select('t.id_tindakan, mt.nama, mt.harga');
        $this->db->from('pol_umum_tindakan t');
        $this->db->join('mst_tindakan mt', 'mt.id = t.id_tindakan', 'left');
        $this->db->where('t.id_pol_umum', $id_pol_umum);
        return $this->db->get()->result_array();
    }

    public function get_resep_header_by_invoice($kode_invoice)
    {
        return $this->db->get_where('pol_resep', ['kode_invoice' => $kode_invoice])->row_array();
    }

    public function get_resep_obat($id_pol_resep)
    {
        return $this->db->get_where('pol_resep_obat', ['id_pol_resep' => $id_pol_resep])->result_array();
    }

    public function get_resep_racikan($id_pol_resep)
    {
        $racikan = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $id_pol_resep])->result_array();
        foreach ($racikan as &$r) {
            $r['obat'] = $this->db->get_where('pol_resep_racikan_detail', [
                'id_pol_resep_racikan' => $r['id']
            ])->result_array();
        }
        return $racikan;
    }


    public function get_all_antrian($id_poli = null, $status = null)
    {
        $this->db->select('
        a.id,
        a.no_antrian,
        a.nama_poli,
        a.status_antrian AS status,
        r.nama_pasien
    ');
        $this->db->from('rsp_antrian a');
        $this->db->join('rsp_registrasi r', 'a.kode_invoice = r.kode_invoice');

        if (!empty($id_poli)) {
            if ($id_poli != 'all') {
                $this->db->where('a.id_poli', $id_poli);
            }
        }

        if (!empty($status)) {
            if ($status == 'dipanggil') {
                $this->db->where_in('a.status_antrian', ['Menunggu', 'Dipanggil']);
            } elseif ($status == 'dikonfirmasi') {
                $this->db->where('a.status_antrian', 'Dikonfirmasi');
            }
        }

        $this->db->order_by('a.id', 'DESC');
        return $this->db->get()->result_array();
    }


    // public function result_antrian($id, $status)
    // {
    //     $this->db->where('id', $id);
    //     return $this->db->update('rsp_antrian', [
    //         'status_antrian' => $status
    //     ]);
    // }


    public function get_data_proses($kode_invoice)
    {
        $this->db->select('
            r.kode_invoice,
            r.nama_dokter,
            r.nama_pasien,
            r.nik,
            r.id_poli,
            r.nama_poli,
            r.id_dokter,
            r.id_pasien,
            p.no_rm,
            p.tanggal_lahir,
            p.no_telp,
            p.alamat,
            u.keluhan,
            u.tekanan_darah,
            u.suhu,
            u.nadi,
            u.catatan
        ');
        $this->db->from('rsp_registrasi r');
        $this->db->join('mst_pasien p', 'r.id_pasien = p.id', 'left');
        $this->db->join('pol_umum u', 'u.kode_invoice = r.kode_invoice');
        $this->db->where('r.kode_invoice', $kode_invoice);
        return $this->db->get()->row_array();
    }
    public function row_data($id)
    {
        $sql = $this->db->query("SELECT a.* FROM rsp_antrian a WHERE a.id = ?", array($id));
        return $sql->row_array();
    }

    public function diagnosa($keyword = null)
    {
        $this->db->from('mst_diagnosa');

        // grouping biar OR nggak rusak
        $this->db->group_start();
        $this->db->where('id_poli', 12);
        $this->db->or_where('nama_poli', 'Poli Umum');
        $this->db->group_end();

        if (!empty($keyword)) {
            $this->db->like('nama_diagnosa', $keyword);
        }

        return $this->db->get()->result_array();
    }


    public function tindakan($keyword = null)
    {
        $this->db->from('mst_tindakan');

        $this->db->group_start();
        $this->db->where('id_poli', 12);
        $this->db->or_where('nama_poli', 'Poli Umum');
        $this->db->group_end();

        if (!empty($keyword)) {
            $this->db->like('nama', $keyword);
        }

        return $this->db->get()->result_array();
    }

    public function get_all_satuan_by_barang($id_barang)
    {
        $this->db->select('
        abd.id AS id_barang_detail,
        abd.kode_barang,
        abd.nama_barang,
        abd.id_satuan_barang,
        asb.nama_satuan AS satuan_barang,   
        abd.urutan_satuan,
        abd.id_satuan_barang,
        aps.harga_awal,
        aps.harga_jual,
        aps.laba,
        aps.stok,
        ab.nama_barang AS nama_obat_utama
    ');

        $this->db->from('apt_barang_detail abd');
        $this->db->join('apt_barang ab', 'abd.id_barang = ab.id', 'left');
        $this->db->join('apt_satuan_barang asb', 'abd.id_satuan_barang = asb.id', 'left');
        $this->db->join('apt_stok aps', 'abd.id = aps.id_barang_detail', 'left');
        $this->db->where('abd.id_barang', $id_barang);
        $this->db->order_by('abd.urutan_satuan', 'ASC');

        return $this->db->get()->result_array();
    }

    // Update fungsi obat() untuk ambil hanya BOX di modal
    public function obat($keyword = null)
    {
        $this->db->select('
        b.id AS id_barang,
        b.nama_barang,
        ad.id AS id_barang_detail,
        ad.id_satuan_barang,
        sb.nama_satuan AS satuan_barang,
        ad.urutan_satuan,
        ad.id_satuan_barang,
        s.harga_awal,
        s.harga_jual,
        s.laba,
        s.stok
    ');

        $this->db->from('apt_barang b');
        $this->db->join('apt_barang_detail ad', 'b.id = ad.id_barang', 'inner');
        $this->db->join('apt_satuan_barang sb', 'ad.id_satuan_barang = sb.id', 'left');
        $this->db->join('apt_stok s', 'ad.id = s.id_barang_detail', 'left');

        $this->db->where('s.stok > ', 0);

        // Filter: Hanya ambil satuan dengan id_satuan_barang = 1 (BOX) untuk modal
        $this->db->where('ad.id_satuan_barang', 1);

        if (!empty($keyword)) {
            $this->db->like('b.nama_barang', $keyword);
        }

        $this->db->order_by('b.nama_barang', 'ASC');

        return $this->db->get()->result_array();
    }

    public function kode_resep()
    {
        //ambil format hari ini ddmmyy
        $today = date('dmY');
        //format prefix awal kode resep
        $prefix = "RSP" . $today;

        //cek di db suffix num terakhir untuk hari ini
        $this->db->select('kode_resep');
        $this->db->from('pol_resep');
        $this->db->like('kode_resep', $prefix, 'after');
        $this->db->order_by('kode_resep', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get()->row();

        //jika belum ada data hari ini, reset dari 1
        if (!$query) {
            $new_number = 1;
        } else {
            //ambil 3 digit terakhir dari nomor urut :
            $last_code = $query->kode_resep;
            $last_number = intval(substr($last_code, -3));

            //+1 untuk urutan berikutnya 
            $new_number = $last_number + 1;
        }

        //format ulang menjadi nomor 3 digit 
        $number_formatted = str_pad($new_number, 3, '0', STR_PAD_LEFT);

        //gabungkan prefix dan nomor 
        return $prefix . '-' . $number_formatted;
    }


    // Di model, tambahkan/ubah method clean_currency:
    public function clean_currency($value)
    {
        // HANDLE NULL/WARNING PHP
        if ($value === null || $value === '' || !is_scalar($value)) {
            return 0;
        }

        // Convert ke string
        $value = (string) $value;

        // Hapus semua karakter non-digit kecuali koma dan titik
        $clean = preg_replace('/[^0-9]/', '', $value);

        return (int) $clean ?: 0;
    }

    public function tambah_proses()
    {
        $id = $this->input->post('id');
        $kode_invoice = $this->input->post('kode_invoice');

        //Menu Tindakan
        $detail = array(
            'keluhan' => $this->input->post('keluhan'),
            'tekanan_darah' => $this->input->post('tekanan_darah'),
            'suhu' => $this->input->post('suhu'),
            'nadi' => $this->input->post('nadi'),
            'catatan' => $this->input->post('catatan')
        );

        // Ambil data dengan struktur baru
        $diagnosa_modal = $this->input->post('diagnosa_modal');
        $diagnosa_manual = $this->input->post('diagnosa_manual');
        $tindakan_modal = $this->input->post('tindakan_modal');
        $tindakan_manual = $this->input->post('tindakan_manual');

        $obat = $this->input->post('obat');
        $racikan = $this->input->post('racikan');

        $insertRacikanDetail = [];

        //mulai transaksi untuk semua proses
        $this->db->trans_begin();

        //insert proses poli umum
        $this->db->where('id', $id);
        $this->db->update('pol_umum', $detail);

        // ambil id nya poli umum setelah insert.
        $id_pol_umum = $id;
        $this->db->where('id_pol_umum', $id_pol_umum)->delete('pol_umum_diagnosa');
        $this->db->where('id_pol_umum', $id_pol_umum)->delete('pol_umum_tindakan');


        // ============ DIAGNOSA ============

        // 1. Insert diagnosa manual (baru) ke master dan relasi
        if (!empty($diagnosa_manual['nama'])) {
            foreach ($diagnosa_manual['nama'] as $index => $nama_diagnosa) {
                if (!empty($nama_diagnosa)) {
                    // Insert ke master diagnosa
                    $this->db->insert('mst_diagnosa', [
                        'nama_diagnosa' => $nama_diagnosa,
                        'id_poli' => '12',
                        'nama_poli' => 'Poli Umum'
                    ]);

                    // Insert ke tabel relasi
                    $id_diagnosa_baru = $this->db->insert_id();
                    $this->db->insert('pol_umum_diagnosa', [
                        'id_pol_umum' => $id_pol_umum,
                        'id_diagnosa' => $id_diagnosa_baru,
                        'diagnosa' => $nama_diagnosa
                    ]);
                }
            }
        }

        // 2. Insert diagnosa modal (sudah ada di master) hanya ke relasi
        if (!empty($diagnosa_modal['id_diagnosa'])) {
            foreach ($diagnosa_modal['id_diagnosa'] as $index => $id_diagnosa_exist) {
                if (!empty($id_diagnosa_exist)) {
                    $nama_diagnosa = $diagnosa_modal['nama'][$index] ?? '';

                    $this->db->insert('pol_umum_diagnosa', [
                        'id_pol_umum' => $id_pol_umum,
                        'id_diagnosa' => $id_diagnosa_exist,
                        'diagnosa' => $nama_diagnosa
                    ]);
                }
            }
        }

        // ============ TINDAKAN ============
        $total_tindakan = 0;
        // 1. Insert tindakan manual (baru) ke master dan relasi
        if (!empty($tindakan_manual['nama'])) {
            foreach ($tindakan_manual['nama'] as $index => $nama_tindakan) {
                if (!empty($nama_tindakan)) {
                    $harga = $this->clean_currency($tindakan_manual['harga'][$index] ?? 0);

                    // TAMBAHKAN KE TOTAL TINDAKAN
                    $total_tindakan += $harga;

                    // Insert ke master tindakan
                    $this->db->insert('mst_tindakan', [
                        'nama' => $nama_tindakan,
                        'harga' => $harga,
                        'id_poli' => '12',
                        'nama_poli' => 'Poli Umum'
                    ]);

                    // Insert ke tabel relasi
                    $id_tindakan_baru = $this->db->insert_id();
                    $this->db->insert('pol_umum_tindakan', [
                        'id_pol_umum' => $id_pol_umum,
                        'id_tindakan' => $id_tindakan_baru,
                        'tindakan' => $nama_tindakan,
                        'harga' => $harga
                    ]);
                }
            }
        }

        // 2. Insert tindakan modal (sudah ada di master) hanya ke relasi
        if (!empty($tindakan_modal['id_tindakan'])) {
            foreach ($tindakan_modal['id_tindakan'] as $index => $id_tindakan_exist) {
                if (!empty($id_tindakan_exist)) {
                    $nama_tindakan = $tindakan_modal['nama'][$index] ?? '';
                    $harga = $this->clean_currency($tindakan_modal['harga'][$index] ?? 0);

                    // TAMBAHKAN KE TOTAL TINDAKAN
                    $total_tindakan += $harga;

                    $this->db->insert('pol_umum_tindakan', [
                        'id_pol_umum' => $id_pol_umum,
                        'id_tindakan' => $id_tindakan_exist,
                        'tindakan' => $nama_tindakan,
                        'harga' => $harga
                    ]);
                }
            }
        }

        // ============ INISIALISASI VARIABEL TOTAL ============
        $total_obat_harga = 0;
        $total_obat_laba = 0;
        $total_racikan_harga = 0;
        $total_racikan_laba = 0;

        // ============ RESEP OBAT BIASA ============
        $id_pol_resep = null;

        if (!empty($obat)) {
            // Hitung total untuk obat biasa dulu
            foreach ($obat as $row) {
                $harga = $this->clean_currency($row['harga_o'] ?? $row['harga_jual_o'] ?? 0);
                $laba = $this->clean_currency($row['laba_o'] ?? 0);
                $jumlah = (int) ($row['jumlah_o'] ?? 1);

                $total_obat_harga += ($harga * $jumlah);
                $total_obat_laba += ($laba * $jumlah);
            }
            $inputan = array(
                'kode_invoice' => $kode_invoice,
                'id_pasien' => $this->input->post('id_pasien'),
                'nama_pasien' => $this->input->post('nama_pasien'),
                'nik' => $this->input->post('nik'),
                'id_dokter' => $this->input->post('id_dokter'),
                'nama_dokter' => $this->input->post('nama_dokter'),
                'total_harga' => 0, // SEMENTARA 0, NANTI DIUPDATE
                'tanggal' => date('d-m-Y'),
                'waktu' => date('H:i:s')
            );
            $resep = $this->db->get_where('pol_resep', ['kode_invoice' => $kode_invoice])->row_array();
            if ($resep) {
                $id_rsp = $resep['id'];

                // update header
                $this->db->where('id', $id_rsp)->update('pol_resep', $inputan);
                $id_pol_resep = $resep['id'];
                // bersihkan detail lama
                $this->db->where('id_pol_resep', $id_rsp)->delete('pol_resep_obat');

                $racikan_lama = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $id_pol_resep])->result_array();
                if (!empty($racikan_lama)) {
                    $ids = array_column($racikan_lama, 'id');
                    $this->db->where_in('id_pol_resep_racikan', $ids)->delete('pol_resep_racikan_detail');
                }
                $this->db->where('id_pol_resep', $id_pol_resep)->delete('pol_resep_racikan');

            } else {
                $inputan['kode_resep'] = $this->kode_resep();
                $this->db->insert('pol_resep', $inputan);
                $id_pol_resep = $this->db->insert_id();
            }
            // Insert header resep (MASUKKAN TOTAL 0 DULU, NANTI DIUPDATE)
            // $this->db->insert('pol_resep', [
            //     'kode_invoice' => $kode_invoice,
            //     'kode_resep' => $this->kode_resep(),
            //     'id_pasien' => $this->input->post('id_pasien'),
            //     'nama_pasien' => $this->input->post('nama_pasien'),
            //     'nik' => $this->input->post('nik'),
            //     'id_dokter' => $this->input->post('id_dokter'),
            //     'nama_dokter' => $this->input->post('nama_dokter'),
            //     'total_harga' => 0, // SEMENTARA 0, NANTI DIUPDATE
            //     'tanggal' => date('d-m-Y'),
            //     'waktu' => date('H:i:s')
            // ]);

            // $id_pol_resep = $this->db->insert_id();

            // Build batch untuk pol_resep_obat
            $batch = [];
            foreach ($obat as $row) {
                // ambil dan bersihkan Nilai harga
                $harga = $this->clean_currency($row['harga_o'] ?? $row['harga_jual_o'] ?? 0);
                $laba = $this->clean_currency($row['laba_o'] ?? 0);
                $jumlah = (int) ($row['jumlah_o'] ?? 1);

                // hitung subtotal
                $sub_total_harga = $harga * $jumlah;
                $sub_total_laba = $laba * $jumlah;

                $batch[] = [
                    'id_pol_resep' => $id_pol_resep,
                    'id_barang' => $row['id_obat_o'] ?? null,
                    'id_barang_detail' => $row['id_obat_detail_o'] ?? null,
                    'nama_barang' => $row['nama_obat_o'] ?? null,
                    'id_satuan_barang' => $row['id_satuan_o'] ?? null,
                    'satuan_barang' => $row['satuan_o'] ?? null,
                    'urutan_satuan' => $row['urutan_satuan_o'] ?? null,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'aturan_pakai' => $row['aturan_pakai_o'] ?? null,
                    'sub_total_harga' => $sub_total_harga,
                    'laba' => $laba,
                    'sub_total_laba' => $sub_total_laba,
                ];
            }

            // Insert batch
            if (!empty($batch)) {
                $this->db->insert_batch('pol_resep_obat', $batch);
            }
        }

        // ============ RACIKAN OBAT ============
        $racikanIds = [];

        if (!empty($racikan) && $id_pol_resep) {
            foreach ($racikan as $index => $r) {
                $jumlahRacikan = $r['jumlah_r'];

                // Insert racikan utama terlebih dahulu
                $this->db->insert('pol_resep_racikan', [
                    'id_pol_resep' => $id_pol_resep,
                    'nama_racikan' => $r['nama_r'],
                    'jumlah' => $jumlahRacikan,
                    'keterangan' => $r['keterangan_r'],
                    'harga' => 0, // Nanti diupdate
                    'sub_total_harga' => 0, // Nanti diupdate
                    'laba' => 0, // Nanti diupdate
                    'sub_total_laba' => 0, // Nanti diupdate
                    'aturan_pakai' => $r['aturan_r'],
                ]);

                $id_racikan = $this->db->insert_id();
                $racikanIds[$index] = $id_racikan;

                // Siapkan data detail racikan
                if (!empty($r['obat'])) {
                    foreach ($r['obat'] as $o) {
                        $harga = $this->clean_currency($o['harga_br']);
                        $laba = $this->clean_currency($o['laba_br']);
                        $jumlahObat = (int) $o['jumlah_br'];

                        $hargaPerObat = $harga * $jumlahObat;
                        $labaPerObat = $laba * $jumlahObat;

                        // Tambahkan ke total racikan
                        $total_racikan_harga += $hargaPerObat;
                        $total_racikan_laba += $labaPerObat;

                        $insertRacikanDetail[] = [
                            'id_pol_resep_racikan' => $id_racikan,
                            'id_barang' => $o['id_barang_br'] ?? null,
                            'id_barang_detail' => $o['id'] ?? null,
                            'nama_barang' => $o['nama_br'] ?? null,
                            'id_satuan_barang' => $o['id_satuan_br'] ?? null,
                            'satuan_barang' => $o['satuan_br'] ?? null,
                            'urutan_satuan' => $o['urutan_satuan_br'] ?? null,
                            'jumlah' => $jumlahObat,
                            'harga' => $harga,
                            'laba' => $laba,
                            'sub_total_harga' => $hargaPerObat,
                            'sub_total_laba' => $labaPerObat,
                        ];
                    }
                }
            }

            // ✅ STEP 4: INSERT BATCH DETAIL OBAT RACIKAN
            if (!empty($insertRacikanDetail)) {
                $this->db->insert_batch('pol_resep_racikan_detail', $insertRacikanDetail);
            }

            // ✅ STEP 5: UPDATE TABEL UTAMA RACIKAN DENGAN TOTAL
            foreach ($racikanIds as $index => $id_racikan) {
                // Hitung total per racikan dari array detail
                $racikan_total_harga = 0;
                $racikan_total_laba = 0;

                foreach ($insertRacikanDetail as $detail) {
                    if ($detail['id_pol_resep_racikan'] == $id_racikan) {
                        $racikan_total_harga += $detail['sub_total_harga'];
                        $racikan_total_laba += $detail['sub_total_laba'];
                    }
                }

                // Ambil jumlah racikan dari data input
                $jumlahRacikan = $racikan[$index]['jumlah_r'] ?? 1;

                $this->db->where('id', $id_racikan);
                $this->db->update('pol_resep_racikan', [
                    'harga' => $racikan_total_harga,
                    'sub_total_harga' => $racikan_total_harga * $jumlahRacikan,
                    'laba' => $racikan_total_laba,
                    'sub_total_laba' => $racikan_total_laba * $jumlahRacikan,
                ]);
            }
        }
        $total_harga_resep = 0;
        // ============ UPDATE TOTAL HARGA DI POL_RESEP ============
        if ($id_pol_resep) {
            // Hitung total akhir
            $total_akhir_obat = $total_obat_harga + $total_obat_laba;
            $total_akhir_racikan = $total_racikan_harga + $total_racikan_laba;
            $total_harga_resep = $total_akhir_obat + $total_akhir_racikan;

            // Update total_harga di tabel pol_resep
            $this->db->where('id', $id_pol_resep);
            $this->db->update('pol_resep', [
                'total_harga' => $total_harga_resep
            ]);
        }


        // ============ INSERT KE TABEL RSP_PEMBAYARAN ============
        // Ambil data dari payload
        $total_invoice = $total_tindakan + $total_harga_resep;

        // Data untuk insert ke rsp_pembayaran
        $pembayaran_data = [
            'kode_invoice' => $kode_invoice,
            'nik' => $this->input->post('nik'),
            'id_pasien' => $this->input->post('id_pasien'),
            'nama_pasien' => $this->input->post('nama_pasien'),
            'id_dokter' => $this->input->post('id_dokter'),
            'nama_dokter' => $this->input->post('nama_dokter'),
            'biaya_tindakan' => $total_tindakan,
            'biaya_resep' => $total_harga_resep,
            'total_invoice' => $total_invoice,
        ];

        // Insert ke tabel rsp_pembayaran
        // $this->db->insert('rsp_pembayaran', $pembayaran_data);
        // ============ UPSERT RSP_PEMBAYARAN ============
        $cekBayar = $this->db->get_where('rsp_pembayaran', ['kode_invoice' => $kode_invoice])->row_array();

        if ($cekBayar) {
            $this->db->where('kode_invoice', $kode_invoice)->update('rsp_pembayaran', $pembayaran_data);
        } else {
            $this->db->insert('rsp_pembayaran', $pembayaran_data);
        }


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

    public function edit()
    {
        $inputan = array(
            'contoh' => $this->input->post('contoh')
        );

        $this->db->trans_begin();

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('contoh', $inputan);

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

    public function hapus()
    {
        $this->db->trans_begin();

        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('contoh');

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

    public function generateNoAntrian($id_poli)
    {
        $CI = &get_instance();

        // ambil kode poli dari mst_poli
        $poli = $CI->db->get_where('mst_poli', ['id' => $id_poli])->row_array();
        $kode = $poli['kode'];

        $today = date('dmY');
        $CI->db->like('tanggal_antri', date('d-m-Y'));
        $CI->db->where('id_poli', $id_poli);
        $CI->db->from('rsp_antrian');
        $count = $CI->db->count_all_results();

        $next = str_pad($count + 1, 3, "0", STR_PAD_LEFT);
        return $kode . "-" . $next;
    }

    public function update_status($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update('rsp_antrian', ['status_antrian' => $status]);
    }
}
