<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_user_plants($username)
    {
        $row = $this->db->select('plant')
            ->where('username', $username)
            ->get('users')
            ->row();

        if (!$row || empty($row->plant)) return [];

        $plants = json_decode($row->plant, true);
        return is_array($plants) ? $plants : [];
    }

    /* 🔑 DIPAKAI SELECT2 PLANT */
    public function get_user_plant_options($username)
    {
        // ambil plant user dari tabel users (json)
        $plants = $this->get_user_plants($username);
        if (empty($plants)) return [];

        $this->db->select("
            CODE as id,
            CONCAT(CODE, ' - ', CODE_NAME) as text
        ");
        $this->db->from('cd_code');
        $this->db->where('HEAD_CODE', 'AJ');
        $this->db->where_in('CODE', $plants);
        $this->db->order_by('CODE', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_data(
        $limit,
        $start,
        $role_id,
        $plant,
        $username,
        $search = '',
        $order = 'RECEIVE_DATE',
        $dir = 'DESC'
    ) {
        $role_id = (int)$role_id;

        $this->db->select('
            r.RECEIVE,
            r.NOTA,
            r.PLANT,
            r.PO,
            r.RECEIVE_DATE,
            r.SUPPLIER,
            c.FULL_NAME AS SUPPLIER_NAME,
            r.REMARK,
            r.SLIP_NO,
            cd.CODE_NAME AS AJ_NAME
        ');
        $this->db->from('mst_receive r');

        $this->db->join(
            'cd_customer c',
            'r.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        $this->db->join(
            'cd_code cd',
            "cd.CODE COLLATE utf8mb4_unicode_ci = r.PLANT COLLATE utf8mb4_unicode_ci
             AND cd.HEAD_CODE = 'AJ'",
            'left',
            false
        );

        $this->db->where('r.DELETED IS NULL', null, false);

        /* 🔐 NON ADMIN */
        if ($role_id !== 1) {
            $plants = json_decode($plant, true);
            if (!is_array($plants)) {
                $plants = explode(',', $plant);
            }

            $this->db->where_in('r.PLANT', $plants);
            $this->db->where('r.CREATED_BY', $username);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('r.RECEIVE', $search);
            $this->db->or_like('r.PO', $search);
            $this->db->or_like('r.NOTA', $search);
            $this->db->or_like('c.FULL_NAME', $search);
            $this->db->or_like('r.REMARK', $search);
            $this->db->or_like('cd.CODE_NAME', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit((int)$limit, (int)$start);

        return $this->db->get()->result_array();
    }

    public function count_data($role_id, $plant, $username, $search = '')
    {
        $role_id = (int)$role_id;

        $this->db->from('mst_receive r');
        $this->db->join('cd_customer c',
            'r.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
            'left', false
        );
        $this->db->join('cd_code cd',
            "cd.CODE COLLATE utf8mb4_unicode_ci = r.PLANT COLLATE utf8mb4_unicode_ci
             AND cd.HEAD_CODE = 'AJ'",
            'left', false
        );

        $this->db->where('r.DELETED IS NULL', null, false);

        if ($role_id !== 1) {
            $plants = json_decode($plant, true);
            if (!is_array($plants)) {
                $plants = explode(',', $plant);
            }
            $this->db->where_in('r.PLANT', $plants);
            $this->db->where('r.CREATED_BY', $username);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('r.RECEIVE', $search);
            $this->db->or_like('r.PO', $search);
            $this->db->or_like('r.NOTA', $search);
            $this->db->or_like('c.FULL_NAME', $search);
            $this->db->or_like('r.REMARK', $search);
            $this->db->or_like('cd.CODE_NAME', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /* 🔑 DIPAKAI CONTROLLER */
    public function user_has_plant($username, $plant)
    {
        $plants = $this->get_user_plants($username);
        return in_array((string)$plant, array_map('strval', $plants));
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATORS
    --------------------------------------------------------- */

    public function generate_po_no()
    {
        $today = date('Ymd');
        $prefix = $today . 'PO';

        $this->db->like('PO', $prefix, 'after');
        $this->db->order_by('PO', 'DESC');
        $this->db->limit(1);
        $row = $this->db->get('mst_po')->row();

        $seq = $row ? ((int)substr($row->PO, -4) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generate_receive_no($plant)
    {
        $today = date('Ymd');
        $prefix = $today . 'RC';

        $this->db->like('RECEIVE', $prefix, 'after');
        $this->db->where('PLANT', $plant); // 🔑 filter PLANT
        $this->db->order_by('RECEIVE', 'DESC');
        $this->db->limit(1);
        $row = $this->db->get('mst_receive')->row();

        $seq = $row ? ((int)substr($row->RECEIVE, -4) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generate_auto_nota($plant)
    {
        return $this->generate_receive_no($plant);
    }

    public function generate_slip_no()
    {
        $today = date('Ymd');
        $prefix = $today . 'AP';

        $this->db->like('SLIP_NO', $prefix, 'after');
        $this->db->order_by('SLIP_NO', 'DESC');
        $this->db->limit(1);
        $row = $this->db->get('mst_receive')->row();

        $seq = $row ? ((int)substr($row->SLIP_NO, -4) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function upload_file($field, $plant, $receive, $date)
    {
        if (empty($_FILES[$field]['name'])) return null;

        $path = './uploads/' . $plant . '/';
        if (!is_dir($path)) mkdir($path, 0755, true);

        $ext  = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        $name = $plant . '_' . $receive . '_' . date('Ymd', strtotime($date)) . '.' . $ext;

        $config = [
            'upload_path'   => $path,
            'allowed_types' => 'jpg|jpeg|png|pdf|xlsx|docx',
            'max_size'      => 10240,
            'file_name'     => $name,
            'overwrite'     => true
        ];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($field)) {
            return [
                'filename' => $name,
                'path'     => 'uploads/' . $plant . '/' . $name
            ];
        }

        return null;
    }

    /* ---------------------------------------------------------
       PO OPERATIONS
    --------------------------------------------------------- */

    public function insert_po($data)
    {
        return $this->db->insert('mst_po', $data);
    }

    public function insert_po_detail_batch($rows)
    {
        if(empty($rows)) return false;
        return $this->db->insert_batch('mst_po_detail', $rows);
    }

    public function get_po_header($po, $plant)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->get('mst_po')
            ->row_array();
    }

    public function get_po_detail($po, $plant)
    {
        return $this->db
            ->select('d.*, m.MATERIAL_NAME')
            ->from('mst_po_detail d')
            ->join('cd_material m',
                'm.MATERIAL COLLATE utf8mb4_unicode_ci = d.MATERIAL COLLATE utf8mb4_unicode_ci',
                'left', false
            )
            ->where('d.PO', $po)
            ->where('d.PLANT', $plant)
            ->order_by('d.ID', 'ASC')
            ->get()->result_array();
    }

    public function search_po($role_id, $plant, $q = null, $limit = 20)
    {
        $this->db->select('
            r.PO,
            r.PLANT,
            r.SUPPLIER,
            c.FULL_NAME AS SUPPLIER_NAME,
            cd.CODE_NAME AS PLANT_NAME
        ');
        $this->db->from('mst_po r');

        $this->db->join(
            'cd_customer c',
            'r.SUPPLIER = c.CUST',
            'left'
        );

        $this->db->join(
            'cd_code cd',
            "cd.CODE = r.PLANT AND cd.HEAD_CODE = 'AJ'",
            'left',
            false
        );

        // 🔐 FILTER
        $this->db->where('r.PLANT', $plant);
        $this->db->where('r.STATUS IS NULL', null, false);

        if (!empty($q)) {
            $this->db->like('r.PO', $q);
        }

        $this->db->order_by('r.PO', 'DESC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id'            => $r['PO'],
                'text'          => $r['PLANT_NAME']
                                    .' - '.$r['PO']
                                    .' - '.$r['SUPPLIER_NAME'],
                'plant'         => $r['PLANT'],
                'supplier'      => $r['SUPPLIER'],
                'supplier_text' => $r['SUPPLIER'].' - '.$r['SUPPLIER_NAME']
            ];
        }

        return $data;
    }

    /* ---------------------------------------------------------
       RECEIVE OPERATIONS
    --------------------------------------------------------- */

    public function insert_receive_header($data)
    {
        return $this->db->insert('mst_receive', $data);
    }

    public function insert_receive_detail_batch($rows)
    {
        return empty($rows) ? false : $this->db->insert_batch('mst_receive_detail', $rows);
    }

    public function set_po_received($po, $plant, $username)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('mst_po', [
                'STATUS'      => 'Y',
                'UPDATED_AT'  => date('Y-m-d H:i:s'),
                'UPDATED_BY'  => $username
            ]);
    }

    public function reset_po_status($po, $plant)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('mst_po', [
                'STATUS'     => null,
                'UPDATED_AT'=> date('Y-m-d H:i:s')
            ]);
    }

    public function get_receive_header($plant, $receive)
    {
        return $this->db
        ->select('
            r.*,
            c.FULL_NAME AS SUPPLIER_NAME,
            cd.CODE_NAME AS PLANT_NAME
        ')
        ->from('mst_receive r')
        ->join('cd_customer c', 'r.SUPPLIER = c.CUST', 'left')
        ->join('cd_code cd', "cd.CODE = r.PLANT AND cd.HEAD_CODE = 'AJ'", 'left')
        ->where('r.PLANT', $plant)
        ->where('r.RECEIVE', $receive)
        ->get()->row_array();
    }

    public function get_receive_detail($plant, $receive)
    {
        return $this->db
            ->select('d.*, m.MATERIAL_NAME')
            ->from('mst_receive_detail d')
            ->join('cd_material m',
                'm.MATERIAL COLLATE utf8mb4_unicode_ci = d.MATERIAL COLLATE utf8mb4_unicode_ci',
                'left', false
            )
            ->where('d.PLANT', $plant)
            ->where('d.RECEIVE', $receive)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()->result_array();
    }

    public function update_po_status($po, $plant, $status = 'RECEIVED')
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('mst_po', [
                'STATUS'     => $status,
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ]);
    }

    public function update_receive_header($receive, $data, $plant = null)
    {
        $this->db->where('RECEIVE', $receive);
        if ($plant !== null) {
            $this->db->where('PLANT', $plant);
        }
        return $this->db->update('mst_receive', $data);
    }

    public function delete_receive_detail_by_receive($receive, $plant = null)
    {
        $this->db->where('RECEIVE', $receive);
        if ($plant !== null) $this->db->where('PLANT', $plant);
        return $this->db->delete('mst_receive_detail');
    }

    public function delete_receive_header_by_receive($receive)
    {
        return $this->db->where('RECEIVE', $receive)
                        ->delete('mst_receive');
    }

    public function delete_receive_header_by_receive_and_plant($receive, $plant)
    {
        return $this->db->where('RECEIVE', $receive)
                        ->where('PLANT', $plant)
                        ->delete('mst_receive');
    }

    public function get_max_seq_no($plant, $receive)
    {
        $row = $this->db
            ->select_max('SEQ_NO')
            ->where('PLANT', $plant)
            ->where('RECEIVE', $receive)
            ->get('mst_receive_detail')
            ->row();

        return (int) ($row->SEQ_NO ?? 0);
    }

    public function delete_receive_detail_not_in_seq($plant, $receive, $seqs)
    {
        if (empty($seqs)) return;

        return $this->db
            ->where('PLANT', $plant)
            ->where('RECEIVE', $receive)
            ->where_not_in('SEQ_NO', $seqs)
            ->delete('mst_receive_detail');
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

    public function search_material($q = null, $limit = 20)
    {
        $this->db->select('MATERIAL as id, MATERIAL_NAME');
        $this->db->from('cd_material');

        if ($q) {
            $this->db->group_start();
            $this->db->like('MATERIAL', $q);
            $this->db->or_like('MATERIAL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('MATERIAL', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['MATERIAL_NAME']
            ];
        }

        return $out;
    }

    public function get_all_receives()
    {
        return $this->db->get('mst_receive')->result_array();
    }
}
