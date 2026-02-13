<?php
class M_pendaftaran extends CI_Model{
public function result_data(){
    $cari = $this->input->post('cari');

    $this->db->select('
        rsp_registrasi.*, 
        rsp_registrasi.id AS id_registrasi,
        mst_pasien.no_telp AS telp_pasien,
        mst_pasien.alamat AS alamat_pasien,
        rsp_antrian.no_antrian AS kode_antrian
    ');
    $this->db->from('rsp_registrasi');
    $this->db->join('mst_pasien', 'mst_pasien.id = rsp_registrasi.id_pasien', 'left');
    $this->db->join('rsp_antrian', 'rsp_antrian.kode_invoice = rsp_registrasi.kode_invoice', 'left');

    if (!empty($cari)) {
        $this->db->like('rsp_registrasi.nama_pasien', $cari);
        $this->db->or_like('rsp_registrasi.kode_invoice', $cari);
        $this->db->or_like('rsp_registrasi.nama_poli', $cari);
    }

    $this->db->order_by('rsp_registrasi.id', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
  }
 public function pasien()
{
    $cari = $this->input->post('cari');
    $this->db->select('a.*');
    $this->db->from('mst_pasien a');
    if (!empty($cari)) {
         $this->db->group_start();
         $this->db->like('a.nama_pasien', $cari);
        $this->db->or_like('a.nik',$cari);
        $this->db->or_like('a.umur',$cari);
        $this->db->or_like('a.jenis_kelamin',$cari);
        $this->db->group_end();
    }
    $this->db->order_by('a.id', 'DESC');
    return $this->db->get()->result_array();
}

    public function row_data($id)
    {
      $sql = $this->db->query("SELECT a.* FROM rsp_registrasi a WHERE a.id = ?", array($id));
      return $sql->row_array();
  }
  public function tambah(){
      $timestamp = time();
      $jam = date('H:i:s');
      // $currentDate = gmdate('dmY', $timestamp);
      $tanggalHariIni = gmdate('dmY', time());
      $currentDate1 = gmdate('d-m-Y', $timestamp);
        // untuk no-ki start
    $this->db->select('kode_invoice');
    $this->db->from('rsp_registrasi');
    $this->db->like('kode_invoice', 'KI'.$tanggalHariIni, 'after');
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
      // untuk no-ki end
        // untuk no-rm start
        $this->db->from('mst_pasien');
        $this->db->select('no_rm');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
          $las_rm = $query->row()->no_rm;
          // $explode = explode('-', $las_rm);
          $last_num = (int) substr($las_rm, 5);
          $net_rm = $last_num + 1;
          $rm_new = 'RM'.str_pad($net_rm, 5, '0', STR_PAD_LEFT);
        }else {
          $rm_new = 'RM'.'00001';
        }
        // untuk no-rm end

    $tipe_pasien = $this->input->post('tipe_pasien');
        if ($tipe_pasien == 'lama') {
      $inputan2 = array(
          'kode_invoice'      => $ki_new,
          'id_dokter'         => $this->input->post('id_dokter'),
          'nama_dokter'       => $this->input->post('nama_dokter'),
          'id_poli'           => $this->input->post('id_poli'),
          'nama_poli'         => $this->input->post('nama_poli'),
          'id_pasien'         => $this->input->post('id_pasien'),
          'nik'               => $this->input->post('nik'),
          'nama_pasien'       => $this->input->post('nama_pasien'),
          'tanggal'         => $currentDate1,
          'waktu'         => $jam,
          'status_registrasi' => 'Sukses',
      );    
          // untuk no-antrian start 
          $poli = $this->db->get_where('mst_poli', ['id' => $this->input->post('id_poli')])->row_array();
          // $tanggalantri = gmdate('d-m-Y', time());
          $this->db->select('no_antrian');
          $this->db->from('rsp_antrian');
          $this->db->where('tanggal_antri', $currentDate1);
          $this->db->where('id_poli', $this->input->post('id_poli'));
          $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $query3 = $this->db->get();

    if ($query3->num_rows() > 0) {
      $last_kp = $query3->row()->no_antrian;
      $last_numkp = (int) substr($last_kp, -3);
      $next_numkp = $last_numkp + 1;
      $kp_new = $poli['kode'].'-'.str_pad($next_numkp, 3, '0', STR_PAD_LEFT);
    } else {
      $kp_new = $poli['kode'].'-'.str_pad(1, 3, '0', STR_PAD_LEFT);
    }
    // untuk no-antrian end

    $inputan3 = array(
      'kode_invoice'    => $ki_new,
      'no_antrian'      => $kp_new,
      'id_poli'         => $this->input->post('id_poli'),
      'nama_poli'       => $this->input->post('nama_poli'),
      'id_dokter'       => $this->input->post('id_dokter'),
      'tanggal_antri'   => $currentDate1,
      'waktu_antri'     => $jam,
      'status_antrian'  => 'Menunggu',
      'tanggal'         => $currentDate1,
      'waktu'           => $jam,

    );
    $this->db->trans_begin();
    $this->db->insert('rsp_registrasi', $inputan2);
    $this->db->insert('rsp_antrian', $inputan3);
    $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          return array('status' => false, 'message' => 'Registrasi gagal ditambahkan');
      } else {
          $this->db->trans_commit();
          return array('status' => true, 'message' => 'Registrasi berhasil ditambahkan');
      }
    }elseif ($tipe_pasien == 'baru') {
    $inputan = array(
        'no_rm'             => $rm_new,
        'nama_pasien'       => ucwords($this->input->post('nama_pasien1')),
        'nik'               => $this->input->post('nik1'),
        'jenis_kelamin'     => $this->input->post('jk1'),
        'tanggal_lahir'     => $this->input->post('tgl_lahir1'),
        'umur'              => $this->input->post('umur1'),
        'alamat'            => ucwords($this->input->post('alamat1')),
        'pekerjaan'         => ucwords($this->input->post('pekerjaan1')),
        // 'no_telp'           => $this->input->post('no_telpon1'),
        'status_perkawinan' => $this->input->post('st_perkawinan1'),
        'nama_wali'         => ucwords($this->input->post('nama_wali1')),
        'golongan_darah'    => $this->input->post('golongan_darah1'),
        'alergi'            => ucwords($this->input->post('alergi1')),
        'status_operasi'    => ucwords($this->input->post('status_op1')),
      );
    $inputan2 = array(
      'kode_invoice'        => $ki_new,
      'id_dokter'           => $this->input->post('id_dokter'),
      'nama_dokter'         => $this->input->post('nama_dokter'),
      'id_poli'             => $this->input->post('id_poli'),
      'nama_poli'           => $this->input->post('nama_poli'),
      // 'id_pasien'           => $this->input->post('id_pasien1'),
      'nik'                 => $this->input->post('nik1'),
      'nama_pasien'         => ucwords($this->input->post('nama_pasien1')),
      'tanggal'         => $currentDate1,
      'waktu'         => $jam,
      'status_registrasi'   => 'Sukses',
    );
    $poli = $this->db->get_where('mst_poli', ['id' => $this->input->post('id_poli')])->row_array();
    $this->db->select('no_antrian');
    $this->db->from('rsp_antrian');
    $this->db->where('tanggal_antri', $currentDate1);
    $this->db->where('id_poli', $this->input->post('id_poli'));
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $query3 = $this->db->get();

    if ($query3->num_rows() > 0) {
        $last_kp = $query3->row()->no_antrian;
        $last_numkp = (int) substr($last_kp, -3);
        $next_numkp = $last_numkp + 1;
        $kp_new = $poli['kode'].'-'.str_pad($next_numkp, 3, '0', STR_PAD_LEFT);
    } else {
        $kp_new = $poli['kode'].'-'.str_pad(1, 3, '0', STR_PAD_LEFT);
    }
    $inputan3 = array(
      'kode_invoice'    => $ki_new,
      'no_antrian'      => $kp_new,
      'id_poli'         => $this->input->post('id_poli'),
      'nama_poli'       => $this->input->post('nama_poli'),
      'id_dokter'       => $this->input->post('id_dokter'),
      'tanggal_antri'   => $currentDate1,
      'waktu_antri'     => $jam,
      'status_antrian'    => 'Menunggu',
      'tanggal'         => $currentDate1,
      'waktu'           => $jam,

    );
    $inputan['no_telp']  = $this->input->post('no_telpon1');
    $no_tp = preg_replace('/[^0-9]/', '', $inputan['no_telp']);
     // Validasi no_telp
    if (!preg_match('/^(62|0)[0-9]{9,13}$/', $no_tp)) {
      return array(
        'status' => false,
        'message' => 'Nomor telepon tidak valid. Gunakan format 08xxx atau 62xxx.'
      );
    }

      $this->db->trans_begin();
      $cek = $this->db->get_where('mst_pasien', ['nik' => $inputan['nik']]);
      if ($cek->num_rows() == 0) {
        // echo 'NIK tidak ada';
        $response = array(
	  			'status' => true,
	  			'message' => "Data pasien belum ada"
	  		);
      $this->db->insert('mst_pasien', $inputan);
      $id_pasien = $this->db->insert_id();
      // $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
	  		$this->db->trans_rollback();
	  		$response = array(
	  			'status' => false,
	  			'message' => "Data Gagal Ditambahkan"
	  		);
	  	} else {
        $inputan2['id_pasien'] = $id_pasien;
        $this->db->insert('rsp_registrasi', $inputan2);
        $this->db->insert('rsp_antrian', $inputan3);
	  		$this->db->trans_commit();
	  		$response = array(
	  			'status' => true,
	  			'message' => "Data Berhasil Ditambahkan"
	  		);
	  	} 

      }else {
      $this->db->insert('rsp_registrasi', $inputan2);
      // $this->db->trans_complete();
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
    }
    return $response;
    }
  }

  public function edit(){
     $inputan = array(
      'id_dokter' => $this->input->post('id_dokter'),
      'nama_dokter' => $this->input->post('nama_dokter'),
      'id_poli' => $this->input->post('id_poli'),
      'nama_poli' => $this->input->post('nama_poli'),
      'id_pasien' => $this->input->post('id_pasien'),
      'nama_pasien' => $this->input->post('nama_pasien'),
      'nik' => $this->input->post('nik'),
    );
    $inputanp = array(
     'nama_pasien' => $this->input->post('nama_pasien'),
     'nik' => $this->input->post('nik'),
   );
    $ambildatr = $this->db->get_where('rsp_registrasi', ['id' => $this->input->post('id')])->row_array();
    $this->db->trans_begin();
  if (!empty($ambildatr['id_booking']) || !empty($ambildatr['kode_booking'])) {
    $this->db->where('id', $ambildatr['id_booking']);
    $this->db->update('rsp_booking', $inputanp);    
    }
    $this->db->where('id', $this->input->post('id'));
    $this->db->update('rsp_registrasi', $inputan);
    $this->db->where('id', $this->input->post('id_pasien'));
    $this->db->update('mst_pasien', $inputanp);
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

  public function hapus(){
    $this->db->trans_begin();
    $this->db->where('kode_invoice', $this->input->post('kode_invoice'));
    $this->db->delete('rsp_registrasi');
    $this->db->where('kode_invoice', $this->input->post('kode_invoice'));
    $this->db->delete('rsp_antrian');
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
        // ['status' => false, 'msg' => 'data kategori ada'];
    }

    public function dokter()
{
     $id_poli = $this->input->post('id_poli');
     $waktu    = $this->input->post('jam_m');     // jam yang dipilih
    $tanggall  = $this->input->post('hari');
     $jam = date('H:i:s');
     $timestamp = time();
     $currentDate1 = gmdate('d-m-Y', $timestamp);
     $hari_indonesia = [
       1 => 'Senin',
       2 => 'Selasa', 
       3 => 'Rabu',
       4 => 'Kamis',
       5 => 'Jumat',
       6 => 'Sabtu',
        7 => 'Minggu'
      ];
    $hari_ini = $hari_indonesia[date('N')];
    $this->db->select('a.*, b.id_poli, b.nama_poli');
    $this->db->from('rsp_jadwal_dokter a');
    $this->db->join('kpg_dokter b', 'a.nama_pegawai = b.nama_pegawai');
    $this->db->where('b.id_poli', $id_poli);
    if (!empty($tanggall)) {
    $tanggalll = strtotime($tanggall);
    // // Ubah timestamp menjadi nama hari dalam bahasa Inggris (contoh: 'Monday')
    $day_of_week_en = date('N', $tanggalll);
    $hari = $hari_indonesia[$day_of_week_en];
      $this->db->where('hari', $hari);
      $this->db->where("jam_mulai <=", $waktu);
      $this->db->where("jam_selesai >=", $waktu);
    }else{
      $this->db->where("hari", $hari_ini);
      $this->db->where("jam_mulai <=", $jam);
      $this->db->where("jam_selesai >=", $jam);
    }
        return $this->db->get()->result();
    }
}
?>