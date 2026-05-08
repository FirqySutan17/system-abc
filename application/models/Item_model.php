<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_model extends CI_Model {

    protected $table = 'abc_cd_item';
    protected $primary = ['item']; // composite PK

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filtered data with order & limit
     */
    public function get_data($limit, $start, $search = '', $order = 'item', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('item', $search);
            $this->db->or_like('full_name', $search);
            $this->db->or_like('goods', $search);
            $this->db->group_end();
        }
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table, (int)$limit, (int)$start)->result();
    }

    public function count_data($search = '')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('item', $search);
            $this->db->or_like('full_name', $search);
            $this->db->or_like('goods', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_pk($item)
    {
        return $this->db->get_where($this->table, ['item' => $item])->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($item, $data)
    {
        return $this->db->where('item', $item)->update($this->table, $data);
    }

    public function delete($item)
    {
        return $this->db->where('item', $item)->delete($this->table);
    }

    public function get_all($search = '', $order = 'item', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('item', $search);
            $this->db->or_like('full_name', $search);
            $this->db->group_end();
        }
        $this->db->order_by($order, $dir);
        return $this->db->get($this->table)->result();
    }
}
