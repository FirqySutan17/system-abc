<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

    protected $table = 'users';
    protected $primary = ['id'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get filtered data with order & limit
     */
    public function get_data($limit, $start, $search = '', $order = 'username', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('username', $search);
            $this->db->or_like('name', $search);
            $this->db->or_like('plant', $search); // masih string JSON
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        $rows = $this->db->get($this->table, (int)$limit, (int)$start)->result();

        if (!$rows) {
            return [];
        }

        // ambil mapping plant
        $plantMap = $this->get_plant_mapping();

        // inject plant_name
        foreach ($rows as &$row) {

            $plantCodes = json_decode($row->plant, true);

            // normalisasi hasil decode
            if (!is_array($plantCodes)) {
                // handle data lama CSV
                if (is_string($row->plant) && strpos($row->plant, ',') !== false) {
                    $plantCodes = explode(',', $row->plant);
                }
                // handle single value
                elseif (is_string($row->plant) && trim($row->plant) !== '') {
                    $plantCodes = [ $row->plant ];
                } else {
                    $plantCodes = [];
                }
            }

            $plantNames = [];

            foreach ($plantCodes as $code) {
                $code = trim((string)$code); // ⬅️ INI KUNCI UTAMA

                if ($code !== '' && isset($plantMap[$code])) {
                    $plantNames[] = $plantMap[$code];
                }
            }

            $row->plant_name = !empty($plantNames)
                ? implode(', ', $plantNames)
                : '-';
        }

        return $rows;
    }

    public function count_data($search = '')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('username', $search);
            $this->db->or_like('name', $search);
            $this->db->or_like('plant', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results($this->table);
    }

    private function get_plant_mapping()
    {
        $rows = $this->db
            ->where('HEAD_CODE', 'AJ')
            ->get('cd_code')
            ->result();

        $map = [];
        foreach ($rows as $r) {
            $map[trim((string)$r->CODE)] = $r->CODE_NAME;
        }

        return $map;
    }

    public function username_exists($username, $exclude_id)
    {
        return $this->db
            ->where('username', $username)
            ->where('id !=', $exclude_id)
            ->count_all_results($this->table) > 0;
    }

    public function get_plants_by_codes($codes = [])
    {
        if (empty($codes)) {
            return [];
        }

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $codes)
            ->get('cd_code')
            ->result_array();
    }


    /**
     * INSERT USER
     * plant (array) → JSON
     */
    public function insert($data)
    {
        // plant harus string JSON
        if (isset($data['plant']) && is_array($data['plant'])) {
            $data['plant'] = json_encode(array_values($data['plant']));
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * GET BY ID
     * plant (JSON) → array
     */
    public function get_by_pk($id)
    {
        return $this->db
            ->where('id', $id)
            ->get($this->table)
            ->row();
    }

    /**
     * UPDATE USER
     * plant (array) → JSON
     */
    public function update($id, $data)
    {
        if (isset($data['plant']) && is_array($data['plant'])) {
            $data['plant'] = json_encode(array_values($data['plant']));
        }

        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where(['id' => $id])->delete($this->table);
    }

    /**
     * Get all (used for export) with optional search & order
     */
    public function get_all($search = '', $order = 'username', $dir = 'ASC')
    {
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('username', $search);
            $this->db->or_like('name', $search);
            $this->db->or_like('plant', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        return $this->db->get($this->table)->result();
    }
}
