<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemBalance_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */

    public function get_data($limit, $start, $search = '', $order = 'RECEIVE_DATE', $dir = 'DESC')
    {
        $role_id = (int) $this->session->userdata('role_id');
        $plant   = $this->session->userdata('plant');

        $this->db->select('
            r.PLANT,
            aj.CODE_NAME AS PLANT_NAME,

            r.RECEIVE,
            r.RECEIVE_DATE,
            r.DO,
            r.SUPPLIER,
            c.FULL_NAME AS SUPPLIER_NAME,
            r.DRIVER,
            r.QTY,
            r.WEIGHT,
            r.RECEIVE_AMOUNT,
            r.REMARK,
            r.SLIP_NO
        ');

        $this->db->from('mst_receive_lb r');

        // 🔗 supplier
        $this->db->join(
            'cd_customer c',
            'r.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        // 🔗 plant name (AJ)
        $this->db->join(
            'cd_code aj',
            "aj.HEAD_CODE = 'AJ' AND aj.CODE = r.PLANT",
            'left',
            false
        );

        // ❌ soft delete
        $this->db->where('r.DELETED IS NULL', null, false);

        // 🔒 FILTER PLANT (KECUALI ADMIN)
        if ($role_id !== 1) {
            $this->db->where('r.PLANT', $plant);
        }

        // 🔍 search
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('r.RECEIVE', $search);
            $this->db->or_like('r.DO', $search);
            $this->db->or_like('r.DRIVER', $search);
            $this->db->or_like('c.FULL_NAME', $search);
            $this->db->or_like('r.REMARK', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit((int)$limit, (int)$start);

        return $this->db->get()->result_array();
    }

    public function count_data($search = '')
    {
        $role_id = (int) $this->session->userdata('role_id');
        $plant   = $this->session->userdata('plant');

        $this->db->from('mst_receive_lb r');
        $this->db->join(
            'cd_customer c',
            'r.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        $this->db->where('r.DELETED IS NULL', null, false);

        // 🔒 FILTER PLANT (KECUALI ADMIN)
        if ($role_id !== 1) {
            $this->db->where('r.PLANT', $plant);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('r.RECEIVE', $search);
            $this->db->or_like('r.DO', $search);
            $this->db->or_like('r.DRIVER', $search);
            $this->db->or_like('c.FULL_NAME', $search);
            $this->db->or_like('r.REMARK', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */

    public function generate_receive_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'RC'; // atau LB sesuai kebutuhan

        $this->db->select('RECEIVE');
        $this->db->from('mst_receive_lb');
        $this->db->where('PLANT', $plant);          // 🔑 KUNCI UTAMA
        $this->db->like('RECEIVE', $prefix, 'after');
        $this->db->order_by('RECEIVE', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int) substr($row->RECEIVE, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* ---------------------------------------------------------
       CRUD OPERATIONS
    --------------------------------------------------------- */

    public function insert($data)
    {
        return $this->db->insert('mst_receive_lb', $data);
    }

    public function get_by_receive_and_plant($receive, $plant)
    {
        $this->db->select('r.*, c.FULL_NAME AS SUPPLIER_NAME');
        $this->db->from('mst_receive_lb r');
        $this->db->join(
            'cd_customer c',
            'r.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );
        $this->db->where('r.RECEIVE', $receive);
        $this->db->where('r.PLANT', $plant);
        return $this->db->get()->row_array();
    }

    public function update($plant, $receive, $data)
    {
        if ($plant !== null) {
            $this->db->where('PLANT', $plant);
        }
        $this->db->where('RECEIVE', $receive);
        return $this->db->update('mst_receive_lb', $data);
    }

    public function soft_delete($plant, $receive, $username)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('RECEIVE', $receive)
            ->update('mst_receive_lb', [
                'DELETED'    => 'Y',
                'UPDATED_AT'=> date('Y-m-d H:i:s'),
                'UPDATED_BY'=> $username
            ]);
    }

    /* ---------------------------------------------------------
       SELECT2 HELPERS
    --------------------------------------------------------- */

    public function search_supplier($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('cd_customer');
        $this->db->where('CUST_KIND', 'SUPPLIER');
        $this->db->where('CUST_CLASS', 'SUPPLIER');
        $this->db->where('STATUS', 'N');

        if ($q) {
            $this->db->group_start();
            $this->db->like('CUST', $q);
            $this->db->or_like('FULL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['name']
            ];
        }
        return $out;
    }

    /* ---------------------------------------------------------
       OTHERS
    --------------------------------------------------------- */

    public function get_all()
    {
        $this->db->where('DELETED IS NULL', null, false);
        return $this->db->get('mst_receive_lb')->result_array();
    }
}
