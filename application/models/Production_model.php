<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */
    public function get_data($limit, $start, $role_id, $plant, $username, $search = '', $order = 'PRODUCTION_DATE', $dir = 'DESC')
    {
        $role_id = (int)$role_id;

        $plants = json_decode($plant, true);
        if (!is_array($plants)) $plants = [];

        $allowedOrder = ['PRODUCTION_DATE','PRODUCTION','RECEIVE_LB','PLANT'];
        if (!in_array($order, $allowedOrder)) $order = 'PRODUCTION_DATE';
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select("
            p.PRODUCTION,
            p.PLANT,
            p.RECEIVE_LB,
            p.PRODUCTION_DATE,
            p.REMARK,
            c.CODE_NAME AS PLANT_NAME
        ");
        $this->db->from('mst_production p');
        $this->db->join('cd_code c', "c.CODE = p.PLANT AND c.HEAD_CODE = 'AJ'", 'left');

        $this->db->group_start();
        $this->db->where('p.DELETED IS NULL', null, false);
        $this->db->or_where('p.DELETED', '0');
        $this->db->group_end();

        if ($role_id !== 1) {
            if (empty($plants)) return [];
            $this->db->where_in('p.PLANT', $plants);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('p.PRODUCTION', $search);
            $this->db->or_like('p.RECEIVE_LB', $search);
            $this->db->or_like('p.REMARK', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->group_end();
        }

        $this->db->order_by("p.$order", $dir);
        $this->db->limit((int)$limit, (int)$start);

        return $this->db->get()->result_array();
    }

    public function count_data($role_id, $plant, $username, $search = '')
    {
        $role_id = (int)$role_id;

        $plants = json_decode($plant, true);
        if (!is_array($plants)) $plants = [];

        $this->db->from('mst_production p');
        $this->db->join('cd_code c', "c.CODE = p.PLANT AND c.HEAD_CODE = 'AJ'", 'left');

        $this->db->group_start();
        $this->db->where('p.DELETED IS NULL', null, false);
        $this->db->or_where('p.DELETED', '0');
        $this->db->group_end();

        if ($role_id !== 1) {
            if (empty($plants)) return 0;
            $this->db->where_in('p.PLANT', $plants);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('p.PRODUCTION', $search);
            $this->db->or_like('p.RECEIVE_LB', $search);
            $this->db->or_like('p.REMARK', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->group_end();
        }

        return (int)$this->db->count_all_results();
    }

    public function get_all_item()
    {
        return $this->db
            ->select('ITEM, FULL_NAME')
            ->from('cd_item')
            ->order_by('ITEM', 'ASC')
            ->get()
            ->result_array();
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATORS
    --------------------------------------------------------- */

    public function get_user_plants($username)
    {
        $row = $this->db->select('plant')
                        ->where('username', $username)
                        ->get('users')
                        ->row();

        if (!$row || empty($row->plant)) {
            return [];
        }

        $plants = json_decode($row->plant, true);

        return is_array($plants) ? $plants : [];
    }

    public function get_plant_select2_by_user($username)
    {
        $plantCodes = $this->get_user_plants($username);

        if (empty($plantCodes)) {
            return [];
        }

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result_array();
    }

    public function user_has_plant($username, $plant)
    {
        $plants = $this->get_user_plants($username);
        return in_array($plant, $plants);
    }

    public function generate_production_no($plant, $production_date)
    {
        $today  = date('Ymd', strtotime($production_date));
        $prefix = $today . 'PD';

        $this->db->select('PRODUCTION');
        $this->db->like('PRODUCTION', $prefix, 'after');
        $this->db->where('PLANT', $plant);
        $this->db->order_by('PRODUCTION', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get('mst_production')->row();
        $seq = $row ? ((int)substr($row->PRODUCTION, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* ---------------------------------------------------------
       PRODUCTION OPERATIONS
    --------------------------------------------------------- */

    public function insert_production_header($data)
    {
        return $this->db->insert('mst_production', $data);
    }

    public function insert_production_detail_batch($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('mst_production_detail', $rows);
    }

    public function get_production_header($production, $plant)
    {
        return $this->db->get_where(
            'mst_production',
            [
                'PRODUCTION' => $production,
                'PLANT'      => $plant
            ]
        )->row_array();
    }

    public function get_production_detail($production, $plant)
    {
        return $this->db->from('mst_production_detail')
                        ->where('PRODUCTION', $production)
                        ->where('PLANT', $plant)
                        ->order_by('SEQ_NO', 'ASC')
                        ->get()
                        ->result_array();
    }

    public function update_production_header($production, $plant, $data)
    {
        return $this->db->where('PRODUCTION', $production)
                        ->where('PLANT', $plant)
                        ->update('mst_production', $data);
    }

    public function delete_production_detail($production, $plant)
    {
        return $this->db->where('PRODUCTION', $production)
                        ->where('PLANT', $plant)
                        ->delete('mst_production_detail');
    }

    public function delete_production_header($production, $plant)
    {
        return $this->db->where('PRODUCTION', $production)
                        ->where('PLANT', $plant)
                        ->delete('mst_production');
    }

    /* ---------------------------------------------------------
       SELECT2 / HELPER (OPTIONAL)
    --------------------------------------------------------- */

    /* ---------------------------------------------------------
   SELECT2 / HELPER
--------------------------------------------------------- */

    public function search_receive_lb($q = null, $limit = 20)
    {
        $this->db->select('RECEIVE as id, RECEIVE as text');
        $this->db->from('mst_receive_lb');
        $this->db->where('STATUS IS NULL', null, false);
        $this->db->group_start();
        $this->db->where('p.DELETED IS NULL', null, false);
        $this->db->or_where('p.DELETED', '0');
        $this->db->group_end();

        if ($q) {
            $this->db->like('RECEIVE', $q);
        }

        $this->db->order_by('RECEIVE', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }


}
