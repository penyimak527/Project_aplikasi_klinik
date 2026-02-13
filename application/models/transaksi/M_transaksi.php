<?php
class M_transaksi extends CI_Model
{
    public function get_full_detail_by_invoice($kode_invoice)
    {
        $data = [];
        $data['pembayaran'] = $this->db->get_where('rsp_pembayaran', ['kode_invoice' => $kode_invoice])->row_array();

        if (!$data['pembayaran']) {
            return null;
        }

        $pol_kecantikan = $this->db->get_where('pol_kecantikan', ['kode_invoice' => $kode_invoice])->row_array();
        $pol_gigi = $this->db->get_where('pol_gigi', ['kode_invoice' => $kode_invoice])->row_array();
        $pol_umum = $this->db->get_where('pol_umum', ['kode_invoice' => $kode_invoice])->row_array();
        $data['tindakan'] = [];
        if ($pol_kecantikan) {
            $data['tindakan'] = $this->db->get_where('pol_kecantikan_tindakan', ['id_pol_kecantikan' => $pol_kecantikan['id']])->result_array();
        }
        if ($pol_gigi) {
            $data['tindakan'] = $this->db->get_where('pol_gigi_tindakan', ['id_pol_gigi' => $pol_gigi['id']])->result_array();
        }
        if ($pol_umum) {
            $data['tindakan'] = $this->db->get_where('pol_umum_tindakan', ['id_pol_umum' => $pol_umum['id']])->result_array();
        }

        $pol_resep = $this->db->get_where('pol_resep', ['kode_invoice' => $kode_invoice])->row_array();
        $data['resep'] = [];
        $data['racikan'] = [];

        if ($pol_resep) {
            $data['resep'] = $this->db->get_where('pol_resep_obat', ['id_pol_resep' => $pol_resep['id']])->result_array();
            $racikan_utama = $this->db->get_where('pol_resep_racikan', ['id_pol_resep' => $pol_resep['id']])->result_array();

            foreach ($racikan_utama as $racik) {
                $detail = $this->db->get_where('pol_resep_racikan_detail', ['id_pol_resep_racikan' => $racik['id']])->result_array();
                $racik['detail'] = $detail;
                $data['racikan'][] = $racik;
            }
        }
        return $data;
    }

    // Untuk mengambil data dari rsp_pembayaran yang column bayar ull / kosong
    public function result_data()
    {
        $cari = $this->input->post('cari');

        // Pertama, dapatkan list invoice saja
        $this->db->select('a.kode_invoice, a.nama_pasien, a.tanggal, a.nik');
        $this->db->from('rsp_pembayaran a');
        $this->db->join('pol_resep b', 'a.kode_invoice = b.kode_invoice');

        $this->db->group_start();
        $this->db->where("(a.bayar IS NULL OR a.bayar = '')");
        $this->db->where("(a.metode_pembayaran IS NULL OR a.metode_pembayaran = '')");
        $this->db->group_end();

        if ($cari != '') {
            $this->db->like('a.nama_pasien', $cari);
            $this->db->or_like('a.kode_invoice', $cari);
            $this->db->or_like('a.nik', $cari);
        }

        $this->db->order_by('a.id', 'DESC');
        $invoices = $this->db->get()->result_array();

        // Kemudian dapatkan detail lengkap untuk setiap invoice
        $result = [];
        foreach ($invoices as $invoice) {
            $detail = $this->get_full_detail_by_invoice($invoice['kode_invoice']);
            if ($detail) {
                $result[] = array_merge($invoice, $detail);
            }
        }

        return $result;
    }

    // Untuk mengambil data dari rsp_pembayaran yang column bayar tidak null / tidak kosong
    public function result_dataa()
    {
        $timestamp = time();
        $cari = $this->input->post('cari');
        $tanggal = $this->input->post('tanggal');
        // Pertama, dapatkan list invoice saja
        $this->db->from('rsp_pembayaran a');
        $this->db->select('a.kode_invoice, a.nama_pasien, a.tanggal');
        $this->db->join('pol_resep b', 'a.kode_invoice = b.kode_invoice');
        // $this->db->where('a.tanggal', $tanggal);
        $this->db->group_start();
        $this->db->where("(a.bayar IS NOT NULL OR a.bayar != '')");
        $this->db->where("(a.metode_pembayaran IS NOT NULL OR a.metode_pembayaran != '')");
        $this->db->group_end();
        if ($cari != '') {
            $this->db->group_start();
            $this->db->like('a.nama_pasien', $cari);
            $this->db->or_like('a.metode_pembayaran', $cari);
            $this->db->or_like('a.nik', $cari);
            $this->db->or_like('a.kode_invoice', $cari);
            $this->db->group_end();
        }
        if ($tanggal != '') {
            $this->db->where('a.tanggal', $tanggal);
        }
        $this->db->order_by('a.id', 'DESC');
        $invoices = $this->db->get()->result_array();

        // Kemudian dapatkan detail lengkap untuk setiap invoice
        $result = [];
        foreach ($invoices as $invoice) {
            $detail = $this->get_full_detail_by_invoice($invoice['kode_invoice']);
            if ($detail) {
                $result[] = array_merge($invoice, $detail);
            }
        }
        return $result;
    }
    private function _clean_rupiah($string)
    {
        return (float) preg_replace('/[^0-9]/', '', $string);
    }
    public function tambah()
    {
        $timestamp = time();
        $jam = date('H:i:s');
        $currentDate = gmdate('d-m-Y', $timestamp);
        $bayar = $this->_clean_rupiah($this->input->post('bayar'));
        $kembalian = $this->_clean_rupiah($this->input->post('kembali'));
        $kode_invoice = $this->input->post('kode_invoice');
        $inputan = array(
            'metode_pembayaran' => $this->input->post('metode_pembayaran'),
            'bank' => $this->input->post('bank'),
            'bayar' => $bayar,
            'kembali' => $kembalian,
            'tanggal' => $currentDate,
            'waktu' => $jam,
        );
        $this->db->trans_begin();
        $this->db->where('id', $this->input->post('id_pembayaran'));
        $this->db->update('rsp_pembayaran', $inputan);
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
                'message' => "Data Berhasil Ditambahkan",
                'kode_invoice' => $kode_invoice
            );
        }

        return $response;
    }
}
?>