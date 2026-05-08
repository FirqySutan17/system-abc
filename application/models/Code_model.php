<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code_model extends CI_Model {

    protected $table = 'abc_cd_code';
    protected $primary = ['HEAD_CODE','CODE']; // composite PK

    public function __construct()
    {
        parent::__construct();
    }

    public function get_unique_head()
    {
        $this->db->select('head_code, code_name');
        $this->db->group_by('head_code, code_name');
        $this->db->order_by('head_code', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_head_star()
    {
        // Ambil HEAD_CODE + CODE_NAME yang CODE = '*'
        $this->db->select('HEAD_CODE, CODE_NAME');
        $this->db->where('CODE', '*');
        $this->db->order_by('HEAD_CODE', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_data_by_head($head, $limit, $start, $order = 'code', $dir = 'ASC')
    {
        $this->db->where('head_code', $head);
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table, (int)$limit, (int)$start)->result();
    }

    public function count_data_by_head($head)
    {
        $this->db->where('head_code', $head);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Get filtered data with order & limit
     */
    public function get_data($limit, $start, $search = '', $order = 'head_code', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('head_code', $search);
            $this->db->or_like('code', $search);
            $this->db->or_like('code_name', $search);
            $this->db->or_like('desc1', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        return $this->db->get($this->table, (int)$limit, (int)$start)->result();
    }

    public function count_data($search = '')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('head_code', $search);
            $this->db->or_like('code', $search);
            $this->db->or_like('code_name', $search);
            $this->db->or_like('desc1', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    public function insert($data)
    {
        return $this->db->insert('abc_cd_code', $data);
    }

    public function get_by_pk($head_code, $code)
    {
        return $this->db->where('head_code', $head_code)
                        ->where('code', $code)
                        ->get('abc_cd_code')
                        ->row();
    }

    public function update($head_code, $code, $data)
    {
        return $this->db->where(['head_code'=>$head_code,'code'=>$code])->update($this->table, $data);
    }

    public function delete($head_code, $code)
    {
        return $this->db->where(['head_code'=>$head_code,'code'=>$code])->delete($this->table);
    }

    /**
     * Get all (used for export) with optional search & order
     */
    public function get_all($search = '', $order = 'head_code', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('head_code', $search);
            $this->db->or_like('code', $search);
            $this->db->or_like('code_name', $search);
            $this->db->group_end();
        }
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table)->result();
    }

    public function get_by_head($head)
    {
        return $this->db->where('head_code', $head)
                        ->order_by('code_name')
                        ->get('abc_cd_code')->result();
    }
}
