<?php
class Jadwal extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
          if($this->session->userdata('username') == null) {
            redirect('login/login');
        }
        $this->load->model('kepegawaian/m_jadwal');
        $this->load->model('kepegawaian/m_dokter');
        $this->load->model('master_data/m_poli');
    }

    private function process_schedule_data($raw_schedule)
    {
        $processed = [];
        $days_of_week = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        foreach ($raw_schedule as $item) {
            $poli = $item->nama_poli;
            $dokter_id = $item->id_pegawai;

            if (!isset($processed[$poli][$dokter_id])) {
                $processed[$poli][$dokter_id]['nama_dokter'] = $item->nama_pegawai;
                $processed[$poli][$dokter_id]['id_kpg_dokter'] = $item->id_kpg_dokter;
                foreach ($days_of_week as $day) {
                    $processed[$poli][$dokter_id]['jadwal'][$day] = '-';
                }
            }
            $processed[$poli][$dokter_id]['jadwal'][$item->hari] = $item->jam_mulai . ' - ' . $item->jam_selesai;
        }
        return $processed;
    }
    public function filter_jadwal()
    {
        $id_poli = $this->input->post('id_poli');
        $jadwal_raw = $this->m_jadwal->get_all($id_poli);
        $data['schedule_data'] = $this->process_schedule_data($jadwal_raw);
        $this->load->view('kepegawaian/dokter/partial_jadwal', $data);
    }
    public function index()
    {
        $data['active'] = 'Kepegawaian';
        $data['title'] = 'Jadwal Dokter';
        $jadwal_raw = $this->m_jadwal->get_all();
        $data['schedule_data'] = $this->process_schedule_data($jadwal_raw);
        $data['data_poli'] = $this->m_poli->result_dat();

        $this->load->view('templates/header', $data);
        $this->load->view('kepegawaian/jadwal', $data);
        $this->load->view('templates/footer');
    }
}
?>