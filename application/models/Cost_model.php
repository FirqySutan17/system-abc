<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost_model extends CI_Model {

    protected $table = 'cd_cost';
    protected $primary = ['COST']; // composite PK

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filtered data with order & limit
     */
    public function get_data($limit, $start, $search = '', $order = 'COST', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('COST', $search);
            $this->db->or_like('COST_NAME', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        return $this->db->get($this->table, (int)$limit, (int)$start)->result();
    }

    public function count_data($search = '')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('COST', $search);
            $this->db->or_like('COST_NAME', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    public function insert($data)
    {
        return $this->db->insert('cd_cost', $data);
    }

    public function get_by_pk($cost)
    {
        return $this->db->where('cost', $cost)
                        ->get('cd_cost')
                        ->row();
    }

    public function update($cost, $data)
    {
        return $this->db->where(['COST'=>$cost])->update($this->table, $data);
    }

    public function delete($cost)
    {
        return $this->db->where('COST', $cost)->delete($this->table);
    }

    /**
     * Get all (used for export) with optional search & order
     */
    public function get_all($search = '', $order = 'COST', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('COST', $search);
            $this->db->or_like('COST_NAME', $search);
            $this->db->group_end();
        }
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table)->result();
    }
}
