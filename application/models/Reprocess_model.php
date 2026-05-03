<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reprocess_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* =========================
        LIST DATA
    ========================= */
    public function get_data($limit, $start, $search = '', $order = 'process_date', $dir = 'DESC')
    {
        $allowedOrder = [
            'process_no'   => 'p.process_no',
            'plant'        => 'p.plant',
            'process_date' => 'p.process_date',
            'process_class'=> 'p.process_class',
            'created_date' => 'p.created_date'
        ];

        $order = $allowedOrder[$order] ?? 'p.process_date';
        $dir   = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select('
            p.process_no,
            p.plant,
            c1.CODE_NAME AS plant_name,
            p.process_date,
            p.process_class,
            c2.CODE_NAME AS process_class_name,
            p.remark,
            p.created_date
        ', false);

        $this->db->from('mst_process_master p');
        $this->db->join('cd_code c1', "c1.CODE = p.plant AND c1.HEAD_CODE='AJ'", 'left');
        $this->db->join('cd_code c2', "c2.CODE = p.process_class AND c2.HEAD_CODE='PT'", 'left');

        if ($search !== '') {
            $this->db->group_start()
                ->like('p.process_no', $search, 'after')
                ->or_like('p.remark', $search, 'after')
                ->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result_array();
    }

    public function count_data($search = '')
    {
        $this->db->from('mst_process_master p');

        if ($search !== '') {
            $this->db->group_start()
                ->like('p.process_no', $search)
                ->or_like('p.remark', $search)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    /* =========================
        AUTO NUMBER
    ========================= */
    public function generate_process_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'PR';

        $this->db->trans_start(); // 🔥 lock

        $row = $this->db
            ->select('process_no')
            ->from('mst_process_master')
            ->like('process_no', $prefix, 'after')
            ->order_by('process_no', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        $seq = $row ? ((int)substr($row->process_no, -4) + 1) : 1;

        $newNo = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $this->db->trans_complete();

        return $newNo;
    }

    /* =========================
        HEADER
    ========================= */
    public function insert_header($data)
    {
        return $this->db->insert('mst_process_master', $data);
    }

    public function update_header($process_no, $data)
    {
        return $this->db
            ->where('process_no', $process_no)
            ->update('mst_process_master', $data);
    }

    public function delete_header($process_no)
    {
        return $this->db
            ->where('process_no', $process_no)
            ->delete('mst_process_master');
    }

    public function get_header($process)
    {
        return $this->db
            ->select('
                p.*,
                c1.CODE_NAME as plant_name,
                c2.CODE_NAME as process_class_name
            ')
            ->from('mst_process_master p')

            // 🔥 PLANT (AJ)
            ->join(
                'cd_code c1',
                "c1.CODE = p.plant AND c1.HEAD_CODE = 'AJ'",
                'left'
            )

            // 🔥 PROCESS CLASS (PT)
            ->join(
                'cd_code c2',
                "c2.CODE = p.process_class AND c2.HEAD_CODE = 'PT'",
                'left'
            )

            ->where('p.process_no', $process)
            ->get()
            ->row_array();
    }

    /* =========================
        DETAIL
    ========================= */
    public function insert_detail_batch($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('mst_process_detail', $rows);
    }

    public function get_detail($process)
    {
        return $this->db
            ->select('
                d.*,
                i1.FULL_NAME as item_name,
                i2.FULL_NAME as to_item_name
            ')
            ->from('mst_process_detail d')
            ->join('cd_item i1','i1.ITEM = d.item','left')
            ->join('cd_item i2','i2.ITEM = d.to_item','left')
            ->where('process_no',$process)
            ->order_by('seq_no','ASC')
            ->get()
            ->result_array();
    }

    public function delete_detail($process_no)
    {
        return $this->db
            ->where('process_no', $process_no)
            ->delete('mst_process_detail');
    }

    /* =========================
        SELECT2 ITEM
    ========================= */
    public function search_item($q = null, $limit = 20)
    {
        $this->db->select('ITEM as id, FULL_NAME');
        $this->db->from('cd_item');

        if ($q) {
            $this->db->group_start();
            $this->db->like('ITEM', $q);
            $this->db->or_like('FULL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('ITEM', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['FULL_NAME']
            ];
        }

        return $out;
    }

    /* =========================
        SELECT2 PROCESS CLASS
    ========================= */
    public function get_process_class($q = null)
    {
        $this->db->select('CODE as id, CODE_NAME as text');
        $this->db->from('cd_code');
        $this->db->where('HEAD_CODE', 'PT');

        if ($q) {
            $this->db->group_start();
            $this->db->like('CODE', $q);
            $this->db->or_like('CODE_NAME', $q);
            $this->db->group_end();
        }

        return $this->db->get()->result_array();
    }

    /* =========================
        SELECT2 PLANT
    ========================= */
    public function get_plant_select2_by_user($username)
    {
        $row = $this->db
            ->select('plant')
            ->from('users')
            ->where('username', $username)
            ->get()
            ->row();

        if (!$row || empty($row->plant)) {
            return [];
        }

        $plants = json_decode($row->plant, true);

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plants)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result_array();
    }

    /* =========================
        VALIDATION
    ========================= */
    public function user_has_plant($username, $plant)
    {
        $row = $this->db
            ->select('plant')
            ->from('users')
            ->where('username', $username)
            ->get()
            ->row();

        if (!$row || empty($row->plant)) return false;

        $plants = json_decode($row->plant, true);

        return in_array($plant, $plants);
    }

    public function is_valid_process_class($code)
    {
        return $this->db
            ->where('HEAD_CODE', 'PT')
            ->where('CODE', $code)
            ->count_all_results('cd_code') > 0;
    }
}