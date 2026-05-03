<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Moving_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* =========================
        LIST DATA
    ========================= */
    public function get_data($limit, $start, $search = '', $order = 'moving_date', $dir = 'DESC')
    {
        $allowedOrder = [
            'moving_no'   => 'm.moving_no',
            'plant'       => 'm.plant',
            'moving_date' => 'm.moving_date',
            'to_plant'    => 'm.to_plant',
            'created_date'=> 'm.created_date'
        ];

        $order = $allowedOrder[$order] ?? 'm.moving_date';
        $dir   = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select('
            m.moving_no,
            m.plant,
            p1.CODE_NAME AS plant_name,
            m.to_plant,
            p2.CODE_NAME AS to_plant_name,
            m.moving_date,
            m.remark,
            m.created_date
        ', false);

        $this->db->from('mst_moving_master m');
        $this->db->join('cd_code p1', "p1.CODE = m.plant AND p1.HEAD_CODE='AJ'", 'left');
        $this->db->join('cd_code p2', "p2.CODE = m.to_plant AND p2.HEAD_CODE='AJ'", 'left');

        if ($search !== '') {
            $this->db->group_start()
                ->like('m.moving_no', $search, 'after')
                ->or_like('m.remark', $search, 'after')
                ->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result_array();
    }

    public function count_data($search = '')
    {
        $this->db->from('mst_moving_master m');

        if ($search !== '') {
            $this->db->group_start()
                ->like('m.moving_no', $search)
                ->or_like('m.remark', $search)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    /* =========================
        AUTO NUMBER
    ========================= */
    public function generate_moving_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'MV';

        $this->db->select('moving_no');
        $this->db->from('mst_moving_master');
        $this->db->where('plant', $plant);
        $this->db->like('moving_no', $prefix, 'after');
        $this->db->order_by('moving_no', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int)substr($row->moving_no, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* =========================
        HEADER
    ========================= */
    public function insert_header($data)
    {
        return $this->db->insert('mst_moving_master', $data);
    }

    public function update_header($moving_no, $data)
    {
        return $this->db
            ->where('moving_no', $moving_no)
            ->update('mst_moving_master', $data);
    }

    public function delete_header($moving_no)
    {
        return $this->db
            ->where('moving_no', $moving_no)
            ->delete('mst_moving_master');
    }

    // public function get_header($moving_no)
    // {
    //     return $this->db
    //         ->select('
    //             m.*,
    //             p1.CODE_NAME AS plant_name,
    //             p2.CODE_NAME AS to_plant_name
    //         ')
    //         ->from('mst_moving_master m')
    //         ->join('cd_code p1', "p1.CODE = m.plant AND p1.HEAD_CODE='AJ'", 'left')
    //         ->join('cd_code p2', "p2.CODE = m.to_plant AND p2.HEAD_CODE='AJ'", 'left')
    //         ->where('m.moving_no', $moving_no)
    //         ->get()
    //         ->row_array();
    // }

    public function get_header($moving)
    {
        return $this->db
            ->select('
                m.*,
                c1.CODE_NAME as plant_name,
                c2.CODE_NAME as to_plant_name
            ')
            ->from('mst_moving_master m')
            ->join('cd_code c1','c1.CODE = m.plant','left')
            ->join('cd_code c2','c2.CODE = m.to_plant','left')
            ->where('moving_no',$moving)
            ->get()
            ->row_array();
    }

    /* =========================
        DETAIL
    ========================= */
    public function insert_detail_batch($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('mst_moving_detail', $rows);
    }

    // public function get_detail($moving_no)
    // {
    //     return $this->db
    //         ->select('
    //             d.*,
    //             i.FULL_NAME AS item_name
    //         ')
    //         ->from('mst_moving_detail d')
    //         ->join(
    //             'cd_item i',
    //             'd.item COLLATE utf8mb4_unicode_ci = i.ITEM COLLATE utf8mb4_unicode_ci',
    //             'left',
    //             false
    //         )
    //         ->where('d.moving_no', $moving_no)
    //         ->order_by('d.seq_no', 'ASC')
    //         ->get()
    //         ->result_array();
    // }

    public function get_detail($moving)
    {
        return $this->db
            ->select('d.*, i.FULL_NAME as item_name')
            ->from('mst_moving_detail d')
            ->join('cd_item i','i.ITEM = d.item','left')
            ->where('moving_no',$moving)
            ->order_by('seq_no','ASC')
            ->get()
            ->result_array();
    }

    public function delete_detail($moving_no)
    {
        return $this->db
            ->where('moving_no', $moving_no)
            ->delete('mst_moving_detail');
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
}