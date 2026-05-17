<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_data($limit, $start, $role_id, $plants, $search = '', $order = 'SALES_DATE', $dir = 'DESC')
    {
        $allowedOrder = [
            'SALES'      => 's.SALES',
            'SALES_DATE' => 's.SALES_DATE',
            'CUSTOMER'   => 's.CUSTOMER',
            'PEMBAYARAN' => 's.PEMBAYARAN',
            'JENIS_PAY'  => 's.JENIS_PAY',
            'PLANT'      => 's.PLANT',
            'CREATED_AT' => 's.CREATED_AT'
        ];

        $order = $allowedOrder[$order] ?? 's.SALES_DATE';
        $dir   = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select('
            s.SALES,
            s.PLANT,
            cc.CODE_NAME AS PLANT_NAME,
            s.CUSTOMER,
            c.FULL_NAME AS CUSTOMER_NAME,
            s.SALES_DATE,
            s.PEMBAYARAN,
            s.JENIS_PAY,
            s.REMARK,
            s.NOTA,
            s.AMOUNT
        ', false);

        $this->db->from('abc_mst_sales s');
        $this->db->join('abc_cd_code cc', "cc.CODE = s.PLANT AND cc.HEAD_CODE='AJ'", 'left');
        $this->db->join('abc_cd_customer c', 'c.CUST = s.CUSTOMER', 'left');
        $this->db->where('s.DELETED IS NULL', null, false);

        if ($role_id !== 1) {
            if (empty($plants)) return [];
            $this->db->where_in('s.PLANT', $plants);
        }

        if ($search !== '') {
            $this->db->group_start()
                ->like('s.SALES', $search, 'after')   // 🔥 lebih cepat dari both side
                ->or_like('s.CUSTOMER', $search, 'after')
                ->or_like('s.REMARK', $search, 'after')
                ->or_like('s.NOTA', $search, 'after')
                ->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit($limit, $start);

        return $this->db->get()->result_array();
    }

    public function count_data($role_id, $plants, $search = '')
    {
        $role_id = (int)$role_id;

        $this->db->from('abc_mst_sales s');
        $this->db->where('s.DELETED IS NULL', null, false);

        if ($role_id !== 1) {
            if (empty($plants)) return 0;
            $this->db->where_in('s.PLANT', $plants);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('s.SALES', $search);
            $this->db->or_like('s.CUSTOMER', $search);
            $this->db->or_like('s.REMARK', $search);
            $this->db->or_like('s.NOTA', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_user_plants($username)
    {
        $cacheKey = 'user_plants_' . $username;

        $plants = $this->cache->get($cacheKey);
        if ($plants !== false) {
            return $plants;
        }

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
        $plants = is_array($plants) ? array_map('strval', $plants) : [];

        // cache 10 menit
        $this->cache->save($cacheKey, $plants, 600);

        return $plants;
    }

    public function get_plant_select2()
    {
        return $this->db
            ->select('CODE as id, CODE_NAME as text')

            ->from('abc_cd_code')

            ->where('HEAD_CODE', 'PLANT')

            ->where('CODE !=', '*')

            ->order_by('CODE_NAME', 'ASC')

            ->get()

            ->result_array();
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
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('abc_cd_code')
            ->result_array();
    }

    public function user_has_plant(
        $username,
        $plant
    ){
        if (!$plant) {
            return false;
        }

        $plant = trim((string)$plant);

        $plants = array_map(
            function($p){
                return trim((string)$p);
            },
            $this->get_user_plants($username)
        );

        return in_array(
            $plant,
            $plants,
            true
        );
    }

    public function delete_auto_dp_cashin($sales, $plant)
    {
        $details = $this->db->where([
            'SALES'       => $sales,
            'PLANT'       => $plant,
            'ORG_SLIP_NO' => 'AUTO_DP'
        ])->get('abc_mst_cash_in_detail')->result_array();

        foreach ($details as $d) {
            $this->db->delete('abc_mst_cash_in_detail', ['ID' => $d['ID']]);

            $remain = $this->db->where([
                'CASH_IN' => $d['CASH_IN'],
                'PLANT'   => $plant
            ])->count_all_results('abc_mst_cash_in_detail');

            if ($remain == 0) {
                $this->db->delete('abc_mst_cash_in', [
                    'CASH_IN' => $d['CASH_IN'],
                    'PLANT'   => $plant
                ]);
            } else {
                $this->recalculate_cash_in_header($d['CASH_IN'], $plant); // ✅ UPDATE HEADER
            }
        }
    }

    public function update_sales_amount_full($plant, $sales, $total, $dp, $remain, $status)
    {
        return $this->db->where([
                'PLANT' => $plant,
                'SALES' => $sales
            ])->update('abc_mst_sales', [
                'AMOUNT'     => $total,
                'DP_AMOUNT'  => $dp,        // ✅ FIX
                'REMAIN'     => $remain,
                'STATUS'     => $status,
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ]);
    }

    private function recalculate_cash_in_header($cashIn, $plant)
    {
        $total = $this->db->select_sum('AMOUNT_OFFSET')
            ->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
            ->get('abc_mst_cash_in_detail')
            ->row()->AMOUNT_OFFSET;

        $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
            ->update('abc_mst_cash_in', ['AMOUNT'=>$total]);
    }

    public function get_total_paid($sales, $plant)
    {
        return (float)$this->db
            ->select('COALESCE(SUM(AMOUNT_OFFSET),0) AS TOTAL_PAID')
            ->from('abc_mst_cash_in_detail')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->get()
            ->row()
            ->TOTAL_PAID;
    }

    public function get_sales_payment_summary($sales, $plant)
    {
        $paid = $this->get_total_paid($sales, $plant);

        return [
            'paid' => $paid
        ];
    }

    public function generate_sales_no($plant)
    {
        $today  = date('Ymd');

        /*
        |--------------------------------------------------------------------------
        | FORMAT
        |--------------------------------------------------------------------------
        | 20260517SO0001
        */

        $prefix = $today . 'SO';

        $this->db->select('SALES');

        $this->db->from('abc_mst_sales');

        $this->db->where('PLANT', $plant);

        $this->db->like(
            'SALES',
            $prefix,
            'after'
        );

        $this->db->order_by('SALES', 'DESC');

        $this->db->limit(1);

        $row = $this->db
            ->get()
            ->row();

        $seq = $row
            ? ((int)substr($row->SALES, -4) + 1)
            : 1;

        return
            $prefix .
            str_pad(
                $seq,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function generate_slip_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'AR';

        $this->db->select('SLIP_NO');
        $this->db->from('abc_mst_sales');
        $this->db->where('PLANT', $plant);
        $this->db->like('SLIP_NO', $prefix, 'after');
        $this->db->order_by('SLIP_NO', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int)substr($row->SLIP_NO, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generate_cash_in_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'CI';

        $this->db->select('CASH_IN');
        $this->db->from('abc_mst_cash_in');
        $this->db->where('PLANT', $plant);
        $this->db->like('CASH_IN', $prefix, 'after');
        $this->db->order_by('CASH_IN', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int)substr($row->CASH_IN, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function get_sales_header_secure($sales, $plant, $username, $role_id)
    {
        $this->db->from('abc_mst_sales');
        $this->db->where('SALES', $sales);
        $this->db->where('PLANT', $plant); // 🔒 KUNCI PLANT

        if ($role_id !== 1) {
            $plants = $this->get_user_plants($username);
            if (empty($plants) || !in_array($plant, $plants)) {
                return null;
            }
        }

        return $this->db->get()->row_array();
    }

    public function insert_sales_header($data)
    {
        return $this->db
            ->insert(
                'abc_mst_sales',
                $data
            );
    }

    public function update_sales_amount($plant, $salesNo, $amount)
    {
        $this->db->where('PLANT', $plant);
        $this->db->where('SALES', $salesNo);
        $this->db->update('abc_mst_sales', [
            'AMOUNT' => $amount
        ]);
    }

    public function insert_sales_detail_batch($rows)
    {
        if (empty($rows)) {
            return false;
        }

        return $this->db
            ->insert_batch(
                'abc_mst_sales_detail',
                $rows
            );
    }

    public function get_sales_header($sales, $plant)
    {
        return $this->db
            ->select('
                s.*,
                c.FULL_NAME AS CUSTOMER_NAME,
                cc.CODE_NAME AS PLANT_NAME
            ')
            ->from('abc_mst_sales s')
            ->join(
                'abc_cd_customer c',
                's.CUSTOMER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci',
                'left',
                false
            )
            ->join(
                'abc_cd_code cc',
                "cc.CODE = s.PLANT AND cc.HEAD_CODE = 'AJ'",
                'left'
            )
            ->where('s.SALES', $sales)
            ->where('s.PLANT', $plant)
            ->get()
            ->row_array();
    }

    public function get_sales_detail($sales, $plant)
    {
        return $this->db
            ->select('
                d.*,
                i.FULL_NAME AS ITEM_NAME
            ')
            ->from('abc_mst_sales_detail d')
            ->join(
                'abc_cd_item i',
                'd.ITEM COLLATE utf8mb4_unicode_ci = i.ITEM COLLATE utf8mb4_unicode_ci',
                'left',
                false
            )
            ->where('d.SALES', $sales)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC') // 🔥 PENTING
            ->get()
            ->result_array();
    }

    public function update_sales_header(
        $plant,
        $sales,
        $data
    ){
        return $this->db
            ->where('PLANT', $plant)
            ->where('SALES', $sales)
            ->update(
                'abc_mst_sales',
                $data
            );
    }

    public function delete_sales_detail($plant, $sales)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('SALES', $sales)
            ->delete('abc_mst_sales_detail');
    }

    public function delete_sales_header($plant, $sales)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('SALES', $sales)
            ->delete('abc_mst_sales');
    }

    /* ---------------------------------------------------------
       SELECT2 HELPERS
    --------------------------------------------------------- */
    public function search_customer($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('abc_cd_customer');

        // hanya yang aktif
        $this->db->where('STATUS', 'Y');

        // hanya CUSTOMER
        $this->db->group_start();
        $this->db->where('CUST_KIND', 'CUSTOMER');
        $this->db->or_where('CUST_CLASS', 'CUSTOMER');
        $this->db->group_end();

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

    public function get_customer_by_id($cust)
    {
        $this->db->select('CUST, FULL_NAME');
        $this->db->from('abc_cd_customer');

        $this->db->where('CUST', $cust);
        $this->db->where('STATUS', 'Y');

        $this->db->group_start();
        $this->db->where('CUST_KIND', 'CUSTOMER');
        $this->db->or_where('CUST_CLASS', 'CUSTOMER');
        $this->db->group_end();

        return $this->db->get()->row_array();
    }

    public function search_material($q = null, $limit = 20)
    {
        // Asumsikan master item ada di abc_cd_material (MATERIAL, MATERIAL_NAME)
        $this->db->select('MATERIAL as id, MATERIAL_NAME');
        $this->db->from('abc_cd_material');

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

    public function get_all_sales()
    {
        return $this->db->get('abc_mst_sales')->result_array();
    }
}
