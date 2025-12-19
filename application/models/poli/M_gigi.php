<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_gigi extends CI_Model
{
    public function get_data_barang($cari = null)
    {
        $this->db->select("
            d.id_barang,
            d.nama_barang,
            JSON_ARRAYAGG(JSON_OBJECT(
                'id', d.id,
                'id_satuan_barang', d.id_satuan_barang, 
                'satuan_barang', d.satuan_barang,
                'urutan_satuan', d.urutan_satuan,
                'harga_awal', st.harga_awal,
                'laba', st.laba,
                'harga_jual', st.harga_jual
            )) as units
        ");

        $this->db->from('apt_barang_detail d');
        $this->db->join('apt_stok st', 'd.id = st.id_barang_detail');
        $this->db->where('st.stok >', 0);

        if ($cari) {
            $this->db->group_start();
            $this->db->like('d.nama_barang', $cari);
            $this->db->or_like('d.kode_barang', $cari);
            $this->db->group_end();
        }

        $this->db->group_by('d.id_barang, d.nama_barang');
        return $this->db->get()->result();
    }

    public function get_pasien_poli_gigi($cari = null)
    {
        $this->db->select('r.kode_invoice, r.nik, r.nama_pasien, r.nama_dokter, p.keluhan, p.id as id_pol_gigi');
        $this->db->from('pol_gigi p');
        $this->db->join('rsp_registrasi r', 'p.kode_invoice = r.kode_invoice', 'left');
        $this->db->where('r.nama_poli', 'Poli Gigi');
        if ($cari) {
            $this->db->group_start();
            $this->db->like('r.kode_invoice', $cari);
            $this->db->or_like('r.nama_pasien', $cari);
            $this->db->or_like('r.nik', $cari);
            $this->db->group_end();
        }
        $this->db->order_by('p.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_or_create_rekam_medis($kode_invoice)
    {
        $ada = $this->db->get_where('pol_gigi', ['kode_invoice' => $kode_invoice])->row_array();
        if ($ada) {
            return $ada;
        } else {
            $pasien_data = $this->db->get_where('rsp_registrasi', ['kode_invoice' => $kode_invoice])->row_array();
            if ($pasien_data) {
                $data_to_insert = [
                    'kode_invoice' => $pasien_data['kode_invoice'],
                    'id_pasien' => $pasien_data['id_pasien'],
                    'nik' => $pasien_data['nik'],
                    'nama_pasien' => $pasien_data['nama_pasien'],
                    'id_dokter' => $pasien_data['id_dokter'],
                    'nama_dokter' => $pasien_data['nama_dokter'],
                    'tanggal' => $pasien_data['tanggal'],
                    'waktu' => $pasien_data['waktu']
                ];
                $this->db->insert('pol_gigi', $data_to_insert);
                $new_id = $this->db->insert_id();
                return $this->db->get_where('pol_gigi', ['id' => $new_id])->row_array();
            }
            return null;
        }
    }

    public function get_rekam_medis_detail($id_pol_gigi)
    {
        $this->db->select('pg.*, pas.*, pg.id as id_pol_gigi');
        $this->db->from('pol_gigi pg');
        $this->db->join('mst_pasien pas', 'pg.id_pasien = pas.id');
        $this->db->where('pg.id', $id_pol_gigi);
        $data['rekam_medis'] = $this->db->get()->row_array();

        $data['diagnosa'] = $this->db->get_where('pol_gigi_diagnosa', ['id_pol_gigi' => $id_pol_gigi])->result_array();
        $data['tindakan'] = $this->db->get_where('pol_gigi_tindakan', ['id_pol_gigi' => $id_pol_gigi])->result_array();

        $data['resep'] = [];
        $data['racikan'] = [];
        $resep_master = $this->db->get_where('pol_resep', ['kode_invoice' => $data['rekam_medis']['kode_invoice']])->row_array();
        if ($resep_master) {
            $data['resep'] = $this->db->get_where('pol_resep_obat', ['id_pol_resep' => $resep_master['id']])->result_array();

            $racikan_utama = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $resep_master['id']])->result_array();
            $racikan_grup = [];

            foreach ($racikan_utama as $racikan) {
                $detail = $this->db->get_where('pol_resep_racikan_detail', ['id_pol_resep_racikan' => $racikan['id']])->result_array();
                $racikan['detail'] = $detail;
                $racikan_grup[] = $racikan;
            }
            $data['racikan'] = $racikan_grup;
        }
        return $data;
    }

    public function save_rekam_medis($post_data)
    {
        $this->db->trans_begin();

        $id_pol_gigi = $post_data['id_pol_gigi'];
        $rekam_medis = $this->db->get_where('pol_gigi', ['id' => $id_pol_gigi])->row_array();
        $kode_invoice = $rekam_medis['kode_invoice'];

        $this->db->where('id', $id_pol_gigi)->update('pol_gigi', [
            'keluhan' => $post_data['keluhan'],
            'catatan' => $post_data['catatan']
        ]);

        $this->db->where('id_pol_gigi', $id_pol_gigi)->delete('pol_gigi_diagnosa');
        if (isset($post_data['id_diagnosa'])) {
            $kelompok_diagnosa = [];
            foreach ($post_data['id_diagnosa'] as $key => $id) {
                $kelompok_diagnosa[] = ['id_pol_gigi' => $id_pol_gigi, 'id_diagnosa' => $id, 'diagnosa' => $post_data['diagnosa'][$key]];
            }
            if (!empty($kelompok_diagnosa)) $this->db->insert_batch('pol_gigi_diagnosa', $kelompok_diagnosa);
        }

        $this->db->where('id_pol_gigi', $id_pol_gigi)->delete('pol_gigi_tindakan');
        $total_biaya_tindakan = 0;
        if (isset($post_data['id_tindakan'])) {
            $kelompok_tindakan = [];
            foreach ($post_data['id_tindakan'] as $key => $id) {
                $harga_tindakan = (int) preg_replace('/[^\d]/', '', $post_data['harga_tindakan'][$key]);
                $kelompok_tindakan[] = ['id_pol_gigi' => $id_pol_gigi, 'id_tindakan' => $id, 'tindakan' => $post_data['tindakan'][$key], 'harga' => $harga_tindakan];
                $total_biaya_tindakan += $harga_tindakan;
            }
            if (!empty($kelompok_tindakan)) $this->db->insert_batch('pol_gigi_tindakan', $kelompok_tindakan);
        }

        $has_resep = isset($post_data['resep_obat']) || isset($post_data['racikan']);
        $resep_master = $this->db->get_where('pol_resep', ['kode_invoice' => $kode_invoice])->row_array();
        $id_pol_resep = $resep_master ? $resep_master['id'] : null;

        if ($id_pol_resep) {
            $this->db->where('id_pol_resep', $id_pol_resep)->delete('pol_resep_obat');
            $racikan_lama = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $id_pol_resep])->result_array();
            foreach ($racikan_lama as $rl) {
                $this->db->where('id_pol_resep_racikan', $rl['id'])->delete('pol_resep_racikan_detail');
            }
            $this->db->where('id_pol_resep', $id_pol_resep)->delete('pol_resep_racikan');
        }

        if ($has_resep && !$id_pol_resep) {
            $tanggal_kode = date('dmy');
            $kode_prefix = 'RSP' . $tanggal_kode . '-';
            $last_resep = $this->db->like('kode_resep', $kode_prefix, 'after')->order_by('kode_resep', 'DESC')->limit(1)->get('pol_resep')->row_array();
            $nomor_urut = 1;
            if ($last_resep) {
                $last_nomor = (int) substr($last_resep['kode_resep'], -3);
                $nomor_urut = $last_nomor + 1;
            }
            $kode_resep_baru = $kode_prefix . str_pad($nomor_urut, 3, '0', STR_PAD_LEFT);

            $resep_data = [
                'kode_invoice' => $kode_invoice,
                'kode_resep' => $kode_resep_baru,
                'id_pasien' => $rekam_medis['id_pasien'],
                'nik' => $rekam_medis['nik'],
                'nama_pasien' => $rekam_medis['nama_pasien'],
                'id_dokter' => $rekam_medis['id_dokter'],
                'nama_dokter' => $rekam_medis['nama_dokter'],
                'tanggal' => date('d-m-Y'),
                'waktu' => date('H:i:s'),
                'total_harga' => 0
            ];
            $this->db->insert('pol_resep', $resep_data);
            $id_pol_resep = $this->db->insert_id();
        }

        $sub_total_resep = 0;
        if ($id_pol_resep) {
            if (isset($post_data['resep_obat'])) {
                $kelompok_resep = [];
                foreach ($post_data['resep_obat'] as $obat) {
                    $harga = (int)preg_replace('/[^\d]/', '', $obat['harga']);
                    $laba = (int)preg_replace('/[^\d]/', '', $obat['laba']);
                    $jumlah = (int)$obat['jumlah'];
                    $subtotal = ($harga + $laba) * $jumlah;
                    $sub_total_resep += $subtotal;
                    $kelompok_resep[] = [
                        'id_pol_resep' => $id_pol_resep,
                        'id_barang' => $obat['id_barang'],
                        'id_barang_detail' => $obat['id_barang_detail'],
                        'nama_barang' => $obat['nama_barang'],
                        'id_satuan_barang' => $obat['id_satuan_barang'],
                        'satuan_barang' => $obat['satuan_barang'],
                        'urutan_satuan' => $obat['urutan_satuan'],
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'sub_total_harga' => $harga * $jumlah,
                        'laba' => $laba,
                        'sub_total_laba' => $laba * $jumlah,
                        'aturan_pakai' => $obat['aturan_pakai']
                    ];
                }
                if (!empty($kelompok_resep)) $this->db->insert_batch('pol_resep_obat', $kelompok_resep);
            }

            if (isset($post_data['racikan'])) {
                foreach ($post_data['racikan'] as $racikan) {
                    $total_racikan_harga_awal = 0;
                    $total_racikan_laba = 0;
                    if (isset($racikan['bahan'])) {
                        foreach ($racikan['bahan'] as $bahan) {
                            $harga = (float)preg_replace('/[^\d.]/', '', $bahan['harga']);
                            $laba = (float)preg_replace('/[^\d.]/', '', $bahan['laba']);
                            $jumlah = (float)$bahan['jumlah'];

                            $total_racikan_harga_awal += $harga * $jumlah;
                            $total_racikan_laba += $laba * $jumlah;
                        }
                    }

                    $jumlah_racikan = (float)$racikan['jumlah'];
                    $sub_total_resep += ($total_racikan_harga_awal + $total_racikan_laba) * $jumlah_racikan;

                    $data_racikan = [
                        'id_pol_resep' => $id_pol_resep,
                        'nama_racikan' => $racikan['nama_racikan'],
                        'jumlah' => $jumlah_racikan,
                        'keterangan' => $racikan['keterangan'],
                        'aturan_pakai' => $racikan['aturan_pakai'],
                        'harga' => $total_racikan_harga_awal,
                        'sub_total_harga' => $total_racikan_harga_awal * $jumlah_racikan,
                        'laba' => $total_racikan_laba,
                        'sub_total_laba' => $total_racikan_laba * $jumlah_racikan
                    ];
                    $this->db->insert('pol_resep_racikan', $data_racikan);
                    $id_pol_resep_racikan = $this->db->insert_id();

                    if (isset($racikan['bahan'])) {
                        $kelompok_bahan = [];
                        foreach ($racikan['bahan'] as $bahan) {
                            $harga = (float)preg_replace('/[^\d.]/', '', $bahan['harga']);
                            $laba = (float)preg_replace('/[^\d.]/', '', $bahan['laba']);
                            $jumlah = (float)$bahan['jumlah'];
                            $kelompok_bahan[] = [
                                'id_pol_resep_racikan' => $id_pol_resep_racikan,
                                'id_barang' => $bahan['id_barang'],
                                'id_barang_detail' => $bahan['id_barang_detail'],
                                'nama_barang' => $bahan['nama_barang'],
                                'id_satuan_barang' => $bahan['id_satuan_barang'],
                                'satuan_barang' => $bahan['satuan_barang'],
                                'urutan_satuan' => $bahan['urutan_satuan'],
                                'jumlah' => $jumlah,
                                'harga' => $harga,
                                'sub_total_harga' => $harga * $jumlah,
                                'laba' => $laba,
                                'sub_total_laba' => $laba * $jumlah
                            ];
                        }
                        if (!empty($kelompok_bahan)) $this->db->insert_batch('pol_resep_racikan_detail', $kelompok_bahan);
                    }
                }
            }
            $this->db->where('id', $id_pol_resep)->update('pol_resep', ['total_harga' => $sub_total_resep]);
        }

        $total_invoice = $total_biaya_tindakan + $sub_total_resep;
        $pembayaran_data = [
            'kode_invoice' => $kode_invoice,
            'id_pasien' => $rekam_medis['id_pasien'],
            'nik' => $rekam_medis['nik'],
            'nama_pasien' => $rekam_medis['nama_pasien'],
            'id_dokter' => $rekam_medis['id_dokter'],
            'nama_dokter' => $rekam_medis['nama_dokter'],
            'biaya_tindakan' => $total_biaya_tindakan,
            'biaya_resep' => $sub_total_resep,
            'total_invoice' => $total_invoice,
        ];

        $pembayaran_tersedia = $this->db->get_where('rsp_pembayaran', ['kode_invoice' => $kode_invoice])->row();
        if ($pembayaran_tersedia) {
            if (is_null($pembayaran_tersedia->bayar)) {
                $this->db->where('kode_invoice', $kode_invoice)->update('rsp_pembayaran', $pembayaran_data);
            }
        } else {
            $this->db->insert('rsp_pembayaran', $pembayaran_data);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // untuk mengambil data tindakan yang akan menampilkan pada modal tindakan
    public function tindakan()
    {
        $cari = $this->input->post('cari');
        $this->db->select('id AS id_tindakan, nama, harga, id_poli, nama_poli');
        $this->db->from('mst_tindakan');
        $this->db->group_start()
            ->like('nama_poli', 'gigi')
            ->or_like('id_poli', 15)
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

    // untuk mengambil data tindakan yang akan menampilkan pada modal tindakan
    public function diagnosa()
    {
        $cari = $this->input->post('cari');
        $this->db->select('id AS id_diagnosa, nama_diagnosa, nama_poli, id_poli');
        $this->db->from('mst_diagnosa');
        $this->db->group_start()
            ->like('nama_poli', 'gigi')
            ->or_like('id_poli', 15)
            ->group_end();
        if (!empty($cari)) {
            $this->db->like('nama_diagnosa', $cari);
        }
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->result_array();
    }
}