<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function check_login($username, $password)
    {
        $this->db->where('username', $username);
        $user = $this->db->get('users')->row();

        // cek user ada
        if (!$user) {
            return false;
        }

        // password pakai password_hash()
        if (password_verify($password, $user->password)) {
            return $user;
        } else {
            return false;
        }
    }
}
