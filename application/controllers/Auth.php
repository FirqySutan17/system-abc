<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('session');
        $this->load->helper(array('url', 'form'));
    }

    public function login()
    {
        // Jika sudah login, langsung redirect ke dashboard
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        $this->load->view('auth/login');
    }

    public function process_login()
    {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->Auth_model->check_login($username, $password);

        if (!$user) {
            $this->session->set_flashdata('error', 'Username atau password salah!');
            redirect('auth/login');
        }

        $plant_name = $this->db->select('CODE_NAME')
            ->from('cd_code')
            ->where('HEAD_CODE', 'AJ')
            ->where('CODE', $user->plant)
            ->get()
            ->row('CODE_NAME');

        $role = $this->db->select('role_name')
            ->from('roles')
            ->where('id', $user->role_id)
            ->get()
            ->row('role_name');

        $permissions_raw = $this->db
            ->select('p.permission_key')
            ->from('permissions p')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $user->role_id)
            ->get()
            ->result_array();

        $permissions = array_column($permissions_raw, 'permission_key');

        $this->session->set_userdata([
            'user_id'     => $user->id,
            'username'    => $user->username,
            'name'        => $user->name,
            'plant'       => $user->plant,
            'plant_name'  => $plant_name,

            // ✅ INI YANG PALING PENTING
            'role_id'     => (int) $user->role_id,

            // ✅ BOLEH SIMPAN NAMA ROLE TERPISAH
            'role'        => $role, // "Super Admin"

            'permissions' => $permissions,
            'logged_in'   => TRUE
        ]);

        redirect('dashboard');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
