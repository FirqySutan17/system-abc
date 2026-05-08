<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Po_model extends CI_Model {

    protected $table = 'abc_mst_po';

    public function __construct(){
        parent::__construct();
    }

    /**
     * Load list (header table)
     */
    public function get_data($limit, $start, $role_id, $plant, $username, $search = '', $order = 'PO', $dir = 'DESC')
    {
        $role_id = (int) $role_id;

        $this->db->select('
            abc_mst_po.*,
            abc_cd_customer.FULL_NAME AS SUPPLIER_NAME,
            abc_cd_code.CODE_NAME AS AJ_NAME
        ');
        $this->db->from('abc_mst_po');

        // JOIN SUPPLIER
        $this->db->join(
            'abc_cd_customer',
            'abc_mst_po.SUPPLIER COLLATE utf8mb4_unicode_ci = abc_cd_customer.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        // JOIN abc_CD_CODE (AJ)
        $this->db->join(
            'abc_cd_code',
            "abc_cd_code.CODE COLLATE utf8mb4_unicode_ci = abc_mst_po.PLANT COLLATE utf8mb4_unicode_ci
            AND abc_cd_code.HEAD_CODE = 'PLANT'",
            'left',
            false
        );

        // 🔓 ADMIN → TANPA FILTER PLANT
        if ($role_id !== 1) {

            $plants = json_decode($plant, true);

            if (is_array($plants)) {
                $plants = array_map('strval', $plants);
            } else {
                $plants = array_map('trim', explode(',', $plant));
            }

            $this->db->where_in('abc_mst_po.PLANT', $plants);
            $this->db->where('abc_mst_po.CREATED_BY', $username);
        }

        if ($search != '') {
            $this->db->group_start();
            $this->db->like('abc_mst_po.PO', $search);
            $this->db->or_like('abc_mst_po.SUPPLIER', $search);
            $this->db->or_like('abc_cd_customer.FULL_NAME', $search);
            $this->db->or_like('abc_mst_po.REMARK', $search);
            $this->db->or_like('abc_cd_code.CODE_NAME', $search); // ← tambahan
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);

        return $this->db->get('', (int)$limit, (int)$start)->result();
    }

    /**
     * Count total filtered rows
     */
    public function count_data($role_id, $plant, $username, $search = '')
    {
        $role_id = (int) $role_id;

        $this->db->from('abc_mst_po');

        $this->db->join(
            'abc_cd_customer',
            'abc_mst_po.SUPPLIER COLLATE utf8mb4_unicode_ci = abc_cd_customer.CUST COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        $this->db->join(
            'abc_cd_code',
            "abc_cd_code.CODE COLLATE utf8mb4_unicode_ci = abc_mst_po.PLANT COLLATE utf8mb4_unicode_ci
            AND abc_cd_code.HEAD_CODE = 'AJ'",
            'left',
            false
        );

        if ($role_id !== 1) {

            $plants = json_decode($plant, true);

            if (is_array($plants)) {
                $plants = array_map('strval', $plants);
            } else {
                $plants = array_map('trim', explode(',', $plant));
            }

            $this->db->where_in('abc_mst_po.PLANT', $plants);
            $this->db->where('abc_mst_po.CREATED_BY', $username);
        }

        if ($search != '') {
            $this->db->group_start();
            $this->db->like('abc_mst_po.PO', $search);
            $this->db->or_like('abc_mst_po.SUPPLIER', $search);
            $this->db->or_like('abc_cd_customer.FULL_NAME', $search);
            $this->db->or_like('abc_mst_po.REMARK', $search);
            $this->db->or_like('abc_cd_code.CODE_NAME', $search); // ← tambahan
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_user_plants($username)
    {
        $row = $this->db
            ->select('plant')
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
            ->where('HEAD_CODE', 'PLANT')
            ->where('CODE <>', '*')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('abc_cd_code')
            ->result_array();
    }

    public function get_plant_select2()
    {
        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->where('CODE <>', '*')
            ->where('USE_YN', 'Y')
            ->order_by('CODE_NAME', 'ASC')
            ->get()
            ->result_array();
    }

    public function user_has_plant($username, $plant)
    {
        $plants = $this->get_user_plants($username);
        return in_array($plant, $plants);
    }

    /**
     * Auto generate PO number
     */
    public function generate_auto_po($plant)
    {
        $today = date('Ymd');

        $this->db->where('PLANT', $plant);
        $this->db->like('PO', $today.'PO');
        $this->db->order_by('PO', 'DESC');
        $row = $this->db->get('abc_mst_po')->row();

        if (!$row) {
            $seq = 1;
        } else {
            $seq = (int)substr($row->PO, -4) + 1;
        }

        return $today . 'PO' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Insert header PO
     */
    public function insert_header($data)
    {
        return $this->db->insert('abc_mst_po', $data);
    }

    /**
     * Insert detail PO batch
     */
    public function insert_detail($rows)
    {
        return $this->db->insert_batch('abc_mst_po_detail', $rows);
    }

    /**
     * Get PO header
     */
    public function get_header($plant, $po)
    {
        $this->db->select('
            abc_mst_po.*,
            abc_cd_customer.FULL_NAME AS SUPPLIER_NAME,
            abc_cd_code.CODE_NAME AS AJ_NAME
        ');
        $this->db->from('abc_mst_po');
        $this->db->join(
            'abc_cd_customer',
            'abc_mst_po.SUPPLIER = abc_cd_customer.CUST',
            'left'
        );
        $this->db->join(
            'abc_cd_code',
            "abc_cd_code.CODE = abc_mst_po.PLANT AND abc_cd_code.HEAD_CODE = 'AJ'",
            'left'
        );
        $this->db->where('abc_mst_po.PLANT', $plant);
        $this->db->where('abc_mst_po.PO', $po);

        return $this->db->get()->row_array();
    }

    public function get_header_for_edit($plant, $po, $username, $role_id)
    {
        $this->db->select('
            abc_mst_po.*,
            s.FULL_NAME AS SUPPLIER_NAME,
            plant_code.CODE_NAME AS AJ_NAME,
            type_code.CODE_NAME AS PO_TYPE_NAME
        ');

        $this->db->from('abc_mst_po');

        // supplier
        $this->db->join(
            'abc_cd_customer s',
            's.CUST = abc_mst_po.SUPPLIER',
            'left'
        );

        // plant
        $this->db->join(
            'abc_cd_code plant_code',
            "plant_code.CODE = abc_mst_po.PLANT
            AND plant_code.HEAD_CODE = 'PLANT'",
            'left'
        );

        // po type
        $this->db->join(
            'abc_cd_code type_code',
            "type_code.CODE = abc_mst_po.PO_TYPE
            AND type_code.HEAD_CODE = 'PO'",
            'left'
        );

        $this->db->where('abc_mst_po.PLANT', $plant);
        $this->db->where('abc_mst_po.PO', $po);

        // non-admin hanya data sendiri
        if ((int)$role_id !== 1) {
            $this->db->where('abc_mst_po.CREATED_BY', $username);
        }

        return $this->db->get()->row_array();
    }

    public function get_detail($plant, $po)
    {
        $this->db->select('d.*, m.MATERIAL_NAME AS MATERIAL_NAME');
        $this->db->from('abc_mst_po_detail d');
        $this->db->join(
            'abc_cd_material m',
            'm.MATERIAL COLLATE utf8mb4_unicode_ci = d.MATERIAL COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );
        $this->db->where('d.PLANT', $plant);
        $this->db->where('d.PO', $po);
        $this->db->order_by('d.ID', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_detail_for_edit($plant, $po)
    {
        $this->db->select('
            d.*,
            m.MATERIAL_NAME,
            c.FULL_NAME AS CUSTOMER_NAME
        ');

        $this->db->from('abc_mst_po_detail d');

        $this->db->join(
            'abc_cd_material m',
            'm.MATERIAL = d.MATERIAL',
            'left'
        );

        $this->db->join(
            'abc_cd_customer c',
            'c.CUST = d.CUSTOMER',
            'left'
        );

        $this->db->where('d.PLANT', $plant);
        $this->db->where('d.PO', $po);
        $this->db->order_by('d.SEQ_NO','ASC');

        return $this->db->get()->result_array();
    }

    public function user_can_access_po($plant, $po, $username, $role_id)
    {
        if ((int)$role_id === 1) {
            return true; // admin bebas
        }

        return $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->where('CREATED_BY', $username)
            ->count_all_results('abc_mst_po') > 0;
    }

    public function update_header_safe($plant, $po, $data, $username, $role_id)
    {
        if (!$this->user_can_access_po($plant, $po, $username, $role_id)) {
            return false;
        }

        return $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->update('abc_mst_po', $data);
    }

    /**
     * Delete detail PO
     */
    public function delete_detail($plant, $po)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->delete('abc_mst_po_detail');
    }

    public function replace_detail_safe($plant, $po, $details, $username)
    {
        // pastikan PO ada
        $exists = $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->count_all_results('abc_mst_po');

        if ($exists === 0) {
            return false;
        }

        $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->delete('abc_mst_po_detail');

        $seq = 1;
        foreach ($details as $row) {

            if (empty($row['MATERIAL'])) continue;

            $this->db->insert('abc_mst_po_detail', [
                'PLANT'      => $plant,
                'PO'         => $po,
                'SEQ_NO'     => $seq++,
                'MATERIAL'   => $row['MATERIAL'],
                'CUSTOMER'   => $row['CUSTOMER'] ?? null,
                'JUMLAH'     => $row['JUMLAH'],
                'BERAT'      => $row['BERAT'],
                'HARGA'      => $row['HARGA'],
                'TOTAL'      => $row['TOTAL'],
                'UPDATED_AT' => date('Y-m-d H:i:s'),
                'UPDATED_BY' => $username
            ]);
        }

        return true;
    }

    /**
     * Delete header PO
     */
    public function delete_header($plant, $po)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->delete('abc_mst_po');
    }

    public function delete_po_safe($plant, $po, $username, $role_id)
    {
        if (!$this->user_can_access_po($plant, $po, $username, $role_id)) {
            return false;
        }

        $this->db->trans_start();

        $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->delete('abc_mst_po_detail');

        $this->db
            ->where('PLANT', $plant)
            ->where('PO', $po)
            ->delete('abc_mst_po');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Export all
     */
    public function get_all($search = '', $order = 'PO', $dir = 'ASC')
    {
        if($search != ''){
            $this->db->group_start();
            $this->db->like('PO', $search);
            $this->db->or_like('SUPPLIER', $search);
            $this->db->or_like('REMARK', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        return $this->db->get('abc_mst_po')->result();
    }
}
