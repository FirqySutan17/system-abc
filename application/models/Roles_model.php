<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles_model extends CI_Model {

    protected $table = 'roles';
    protected $pk    = 'id';

    /* ===================== DATATABLE ===================== */

    public function get_data($limit, $start, $search = NULL, $order = 'role_name', $dir = 'ASC')
    {
        $this->db->from($this->table);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('role_name', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }

    public function count_data($search = NULL)
    {
        $this->db->from($this->table);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('role_name', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /* ===================== CRUD ===================== */

    public function get_by_pk($id)
    {
        return $this->db
            ->where($this->pk, $id)
            ->get($this->table)
            ->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where($this->pk, $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        // hapus permission dulu
        $this->db->where('role_id', $id)->delete('role_permissions');
        return $this->db->where($this->pk, $id)->delete($this->table);
    }

    /* ===================== PERMISSION ===================== */

    public function get_permissions()
    {
        return $this->db->get('permissions')->result();
    }

    public function get_role_permissions($role_id)
    {
        $rows = $this->db->select('permission_id')
            ->where('role_id', $role_id)
            ->get('role_permissions')
            ->result();

        return array_column($rows, 'permission_id');
    }

    public function save_permissions($role_id, $menus = [])
    {
        // hapus dulu permission lama
        $this->db->where('role_id', $role_id)->delete('role_permissions');

        if(empty($menus)) return true;

        $data = [];
        foreach($menus as $menu_id){
            $data[] = ['role_id' => $role_id, 'permission_id' => $menu_id];
        }

        return $this->db->insert_batch('role_permissions', $data);
    }
}
