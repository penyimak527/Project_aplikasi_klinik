<?php
class M_login extends CI_Model
{
    public function login($username, $password)
    {
        $cek = $this->db->get_where('adm_user', ['username' => $username]);
        // jika data kosong
        if ($cek->num_rows() == 0) {
            return [
                'status' => false,
                'message' => 'Username tidak ada!'
            ];
        }
        $user = $cek->row_array();
        if ($user['username'] == $username) {
            $response = ([
                'status' => true,
                'message' => 'Username Benar!',
                'data' => $user

            ]);
            // pengecekan password
            if (password_verify($password, $user['password'])) {
                $response = ([
                    'status' => true,
                    'message' => 'Password Benar!',
                    'data' => $user,
                ]);
                if ($user['status'] == 'Aktif') {
                    $response = ([
                        'status' => true,
                        'message' => 'Status Aktif!',
                        'data' => $user,
                    ]);
                } else {
                    $response = ([
                        'status' => false,
                        'message' => 'Status Tidak Aktif!',
                        'data' => $user,
                    ]);
                }
            } else {
                $response = ([
                    'status' => false,
                    'message' => 'Password Salah!'
                ]);
            }
        } else {
            $response = ([
                'status' => false,
                'message' => 'Username Salah!'
            ]);
        }
        return $response;
    }
}
?>