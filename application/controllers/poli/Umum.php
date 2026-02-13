<?php
class Umum extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('poli/M_umum', 'model');
        $this->load->model('master_data/M_poli', 'model_poli');
    }
 public function index()
    {

        $data['active'] = 'Poli';
        $data['title'] = 'Umum';

        $this->load->view('templates/header', $data);
        $this->load->view('poli/umum', $data);
        $this->load->view('templates/footer');
    }
    public function proses($kode_invoice = null)
    {
        $data['active'] = 'Antrian';
        $data['title'] = 'Dokter Antrian';

        $data['semuapoli'] = $this->model_poli->result_dat();
        // ambil data hasil join dari model
        $data['row'] = $this->model->get_data_proses($kode_invoice);

        $this->load->view('templates/header', $data);
        $this->load->view('poli/umum/proses', $data); // isi
        $this->load->view('templates/footer');
    }

    public function get_poli()
    {
        $result = $this->model_poli->result_dat();
        echo json_encode($result);
    }

    public function view_proses($kode_invoice = null)
    {
        $data['active'] = 'poli';
        $data['title'] = 'Poli Umum';

        $data['semuapoli'] = $this->model_poli->result_dat();
        // ambil data hasil join dari model
        $data['row'] = $this->model->get_data_proses($kode_invoice);

        // ambil data berdasarkan kode_invoice dari tabel pol_umum
        $data['id'] = $this->model->get_by_kode_invoice($kode_invoice);
        $id_pol = $data['id']['id'];
        $data['diagnosa_terisi'] = $this->model->get_diagnosa_terisi($id_pol);
        $data['tindakan_terisi'] = $this->model->get_tindakan_terisi($id_pol);

        $resep = $this->model->get_resep_header_by_invoice($kode_invoice);
        $data['resep_header'] = $resep;
        $data['obat_terisi'] = [];
        $data['racikan_terisi'] = [];

        if ($resep) {
            $data['obat_terisi'] = $this->model->get_resep_obat($resep['id']);
            $data['racikan_terisi'] = $this->model->get_resep_racikan($resep['id']);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('poli/umum/proses', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_proses()
    {
        $response = $this->model->tambah_proses();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function view_edit($id)
    {
        $data['row'] = $this->model->row_data($id);
        $data['active'] = 'contoh';
        $data['title'] = 'Contoh';

        $this->load->view('templates/header', $data);
        $this->load->view('contoh/edit', $data);
        $this->load->view('templates/footer');
    }

    public function edit()
    {
        $response = $this->model->edit();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response,  JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function diagnosa()
    {
        $keyword = $this->input->post('caridiagnosa');

        $result = $this->model->diagnosa($keyword);

        echo json_encode([
            'status' => !empty($result),
            'data'   => $result
        ]);
    }

    public function tindakan()
    {
        $keyword = $this->input->post('carit');

        $result = $this->model->tindakan($keyword);

        echo json_encode([
            'status' => !empty($result),
            'data'   => $result
        ]);
    }

    // Di controller 
    public function obat()
    {
        $keyword = $this->input->post('carit');
        $id_barang = $this->input->post('id_barang'); // Untuk request satuan

        if (!empty($id_barang)) {
            // Jika ada id_barang, ambil semua satuan untuk obat tersebut
            $result = $this->model->get_all_satuan_by_barang($id_barang);
        } else {
            $result = $this->model->obat($keyword);
        }


        echo json_encode([
            'status' => !empty($result),
            'data'   => $result
        ]);
    }

    public function result_data()
    {
        $id_poli = $this->input->post('id_poli');
        $status = $this->input->post('status');

        $data = $this->model->get_all_antrian($id_poli, $status);

        $response = [
            'result'  => !empty($data),
            'data'    => $data ?? [],
            'message' => empty($data) ? 'Data Kosong' : ''
        ];

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // public function result_antrian($id = null)
    // {
    //   if ($id === null) {
    //     echo json_encode(['success' => false, 'message' => 'ID antrian tidak ditemukan']);
    //     return;
    //   }

    //   // Panggil model untuk ubah status jadi "Dipanggil"
    //   $result = $this->model->result_antrian($id, 'Dipanggil');

    //   if ($result) {
    //     echo json_encode(['success' => true, 'message' => 'Data monitor berhasil diperbarui']);
    //   } else {
    //     echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data monitor']);
    //   }
    // }

    public function panggil($id)
    {
        $update = $this->model->update_status($id, 'Dipanggil');

        if ($update) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function konfirmasi($id)
    {
        $update = $this->model->update_status($id, 'Dikonfirmasi');
        echo json_encode(['success' => $update]);
    }

    // public function panggil_pasien()
    // {
    //   $id = $this->input->post('id');

    //   if (empty($id)) {
    //     $response = [
    //       'status' => false,
    //       'message' => 'ID tidak ditemukan'
    //     ];
    //   } else {
    //     $update = $this->model->update_status($id, 'Dipanggil');

    //     if ($update) {
    //       $response = [
    //         'status' => true,
    //         'message' => 'Pasien berhasil dipanggil!'
    //       ];
    //     } else {
    //       $response = [
    //         'status' => false,
    //         'message' => 'Gagal memanggil pasien.'
    //       ];
    //     }
    //   }

    //   $this->output
    //     ->set_status_header(200)
    //     ->set_content_type('application/json', 'utf-8')
    //     ->set_output(json_encode($response, JSON_PRETTY_PRINT))
    //     ->_display();
    //   exit;
    // }
}
