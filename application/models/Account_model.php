<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends CI_Model {

    private $table = 'cd_account';
    private $pk    = 'ACCOUNT';

    public function get_data($limit, $offset, $search, $order, $dir)
    {
        $this->db->from($this->table);

        if ($search !== '') {
            $this->db->group_start()
                     ->like('ACCOUNT', $search)
                     ->or_like('ACCOUNT_NAME', $search)
                     ->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    public function count_all($search)
    {
        $this->db->from($this->table);

        if ($search !== '') {
            $this->db->group_start()
                     ->like('ACCOUNT', $search)
                     ->or_like('ACCOUNT_NAME', $search)
                     ->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_by_pk($account) {
        return $this->db->get_where($this->table, ['ACCOUNT' => $account])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($account, $data) {
        return $this->db->where('ACCOUNT', $account)->update($this->table, $data);
    }

    public function delete($account) {
        return $this->db->where('ACCOUNT', $account)->delete($this->table);
    }
}
