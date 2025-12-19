<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public function index()
	{
		 if($this->session->userdata('username') == null) {
            redirect('login/login');
        }

		$level = $this->session->userdata('nama_level');
		

		if ($level == 'Administrator') {
			redirect('master_data/poli');
		} elseif ($level == 'Superadmin') {
			redirect('admin/user');
		}
		 elseif ($level == 'Dokter') {
			redirect('antrian/antrian/index_dokter');
		}elseif ($level == 'Dokter Kecantikan') {
			redirect('antrian/antrian/index_dokter');
		}elseif ($level == 'Dokter Gigi') {
			redirect('antrian/antrian/index_dokter');
		} elseif ($level == 'Dokter Umum') {
			redirect('antrian/antrian/index_dokter');
		}elseif ($level == 'Resepsionis') {
			redirect('resepsionis/pendaftaran');
		} elseif ($level == 'Administrator Apotek') {
			redirect('master_data/barang');
		} elseif ($level == 'Kasir Penjualan') {
			redirect('transaksi/penjualan');
		} elseif ($level == 'Kasir Penjualan Resep') {
			redirect('transaksi/penjualan_resep');
		} else {
			$this->load->view('welcome_message');
			echo $level;
		}
	}
}