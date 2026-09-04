<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_data(
        $limit,
        $start,
        $filters = [],
        $order = 'CREATED_AT',
        $dir = 'DESC'
    )
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

        $order =
            $allowedOrder[$order]
            ?? 's.SALES_DATE';

        $dir =
            strtoupper($dir) === 'ASC'
                ? 'ASC'
                : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select('

            s.SALES,

            s.PLANT,

            cc.CODE_NAME AS PLANT_NAME,

            s.CUSTOMER,

            c.FULL_NAME AS CUSTOMER_NAME,

            s.SALES_DATE,

            s.PEMBAYARAN,

            s.JENIS_PAY,

            s.STATUS,

            s.FLAG,

            s.FLAG_REASON,

            s.FLAGGED_AT,

            s.FLAGGED_BY,

            s.REMARK,

            s.NOTA,

            s.AMOUNT,

            s.ATTACHMENT_NAME,

            SUM(d.JUMLAH) AS JUMLAH,

            SUM(d.BERAT) AS BERAT,

            MAX(d.MATERIAL) AS MATERIAL,

            MAX(m.material_name) AS MATERIAL_NAME

        ', false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_sales s'
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_sales_detail d',
            '
                d.SALES = s.SALES
                AND d.PLANT = s.PLANT
                AND d.DELETED IS NULL
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code cc',
            "
                cc.CODE = s.PLANT
                AND cc.HEAD_CODE = 'PLANT'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer c',
            'c.CUST = s.CUSTOMER',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_material m',
            'm.MATERIAL = d.MATERIAL',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | DELETED SALES
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search =
                trim(
                    $filters['search']
                );

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $search
            );

            $this->db->or_like(
                's.CUSTOMER',
                $search
            );

            $this->db->or_like(
                'c.FULL_NAME',
                $search
            );

            $this->db->or_like(
                's.NOTA',
                $search
            );

            $this->db->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $this->db->where(
                's.STATUS',
                $filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filters['date_from']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
                $filters['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by([
            's.PLANT',
            's.SALES'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            $order,
            $dir
        );

        /*
        |--------------------------------------------------------------------------
        | LIMIT
        |--------------------------------------------------------------------------
        */

        $this->db->limit(
            $limit,
            $start
        );

        return $this->db
            ->get()
            ->result_array();
    }

    public function count_data(
        $filters = []
    )
    {
        $this->db->from(
            'abc_mst_sales s'
        );

        $this->db->join(
            'abc_cd_customer c',
            'c.CUST = s.CUSTOMER',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | DELETED
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search =
                trim(
                    $filters['search']
                );

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $search
            );

            $this->db->or_like(
                's.CUSTOMER',
                $search
            );

            $this->db->or_like(
                'c.FULL_NAME',
                $search
            );

            $this->db->or_like(
                's.NOTA',
                $search
            );

            $this->db->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {

            $this->db->where(
                's.STATUS',
                $filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filters['date_from']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
                $filters['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COUNT DISTINCT SALES
        |--------------------------------------------------------------------------
        */

        $this->db->select(
            'COUNT(DISTINCT CONCAT(s.PLANT, "|", s.SALES)) AS TOTAL',
            false
        );

        $row =
            $this->db
                ->get()
                ->row_array();

        return (int) (
            $row['TOTAL']
            ?? 0
        );
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

    public function get_plant_select2($username = null)
    {
        $plantCodes = null;

        if ($username) {
            $plantCodes = $this->get_user_plants($username);
            if (empty($plantCodes)) {
                return [];
            }
        }

        // echo '<pre>';
        // print_r($plantCodes);
        // echo '</pre>';
        // exit;
        $this->db->reset_query();

        $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->where('CODE !=', '*');

        // if ($plantCodes) {
        //     $this->db->where_in('CODE', $plantCodes);
        // }

        return $this->db
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

        $this->db->reset_query();

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get()
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

    public function update_sales_amount_full(
        $plant,
        $sales,
        $amount,
        $dp,
        $remain,
        $status
    )
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('SALES', $sales)
            ->update(
                'abc_mst_sales',
                [

                    'AMOUNT'    => $amount,

                    'DP_AMOUNT' => $dp,

                    'REMAIN'    => $remain,

                    'STATUS'    => $status
                ]
            );
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

        $prefix = $today . 'SLS';

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

    public function get_sales_header(
        $sales,
        $plant
    )
    {
        return $this->db
            ->select('

                s.PLANT,

                s.SALES,

                s.CUSTOMER,

                c.FULL_NAME AS CUSTOMER_NAME,

                s.SALES_DATE,

                s.PEMBAYARAN,

                s.JENIS_PAY,

                s.STATUS,

                s.NOTA,

                s.REMARK,

                s.AMOUNT,

                s.DP_AMOUNT,

                s.REMAIN,

                s.ATTACHMENT_NAME,

                s.ATTACHMENT_PATH,

                s.ATTACHMENT_TYPE,

                s.BIAYA,
                s.DISCOUNT,
                s.ROUNDING,

                cc.CODE_NAME AS PLANT_NAME

            ', false)

            ->from('abc_mst_sales s')

            ->join(
                'abc_cd_customer c',
                'c.CUST = s.CUSTOMER',
                'left'
            )

            ->join(
                'abc_cd_code cc',
                "cc.CODE = s.PLANT
                AND cc.HEAD_CODE='PLANT'",
                'left'
            )

            ->where('s.PLANT', $plant)

            ->where('s.SALES', $sales)

            ->where(
                's.DELETED IS NULL',
                null,
                false
            )

            ->get()

            ->row_array();
    }

    public function get_sales_detail(
        $sales,
        $plant
    )
    {
        return $this->db
            ->select('

                d.SEQ_NO,

                d.MATERIAL,

                m.MATERIAL_NAME,

                d.CALC_BASIS,

                d.JUMLAH,

                d.BERAT,

                d.HARGA,

                d.TOTAL

            ', false)

            ->from('abc_mst_sales_detail d')

            ->join(
                'abc_cd_material m',
                'm.MATERIAL = d.MATERIAL',
                'left'
            )

            ->where('d.PLANT', $plant)

            ->where('d.SALES', $sales)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result_array();
    }

    public function get_sales_detail_rows(
        $plant,
        $sales
    )
    {
        return $this->db

            ->select(
                'PLANT, MATERIAL, JUMLAH, BERAT'
            )

            ->from(
                'abc_mst_sales_detail'
            )

            ->where(
                'PLANT',
                $plant
            )

            ->where(
                'SALES',
                $sales
            )

            ->where(
                'DELETED IS NULL',
                null,
                false
            )

            ->get()

            ->result_array();
    }

    public function update_sales_header(
        $plant,
        $sales,
        $data
    )
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('SALES', $sales)
            ->update(
                'abc_mst_sales',
                $data
            );
    }

    public function delete_sales_detail(
        $plant,
        $sales
    )
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

    public function search_customer($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');

        $this->db->from('abc_cd_customer');

        // hanya yang aktif
        $this->db->where('STATUS', 'Y');

        // exclude customer tertentu
        $this->db->where('CUST !=', 'CS000001');

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

        $rows = $this->db
            ->get()
            ->result_array();

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
        $row = $this->db
            ->select('CUST, FULL_NAME')
            ->from('abc_cd_customer')
            ->where('CUST', $cust)
            ->get()
            ->row_array();

        if (!$row) {
            return null;
        }

        return [
            'id' => $row['CUST'],
            'text' => $row['CUST'] . ' - ' . $row['FULL_NAME'],
            'CUST' => $row['CUST'],
            'FULL_NAME' => $row['FULL_NAME']
        ];
    }

    public function search_material($q = null, $selectedPlant = null)
    {
        $limit = 20; // batasi hasil pencarian untuk performa

        $this->db->select('a.MATERIAL as id, a.MATERIAL_NAME, b.QTY, b.BW');
        $this->db->from('abc_cd_material as a');
        $this->db->join(
            'abc_mst_company_stock b',
            'a.MATERIAL COLLATE utf8mb4_unicode_ci = b.MATERIAL',
            'left',
            FALSE
        );

        if ($q) {
            $this->db->group_start();
            $this->db->like('a.MATERIAL', $q);
            $this->db->or_like('a.MATERIAL_NAME', $q);
            $this->db->group_end();
        }

        if (!empty($selectedPlant)) {
            $this->db->where('b.PLANT', $selectedPlant);
        }

        $this->db->where('b.STATUS', 'AVAILABLE');
        $this->db->order_by('a.MATERIAL', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['MATERIAL_NAME'],
                'bw'    => $r['BW'],
                'qty'   => $r['QTY']
            ];
        }

        return $out;
    }

    public function get_company_stock($plant, $material)
    {
        if (!$plant || !$material) {
            return [
                'PLANT' => $plant,
                'MATERIAL' => $material,
                'QTY' => 0,
                'BW' => 0,
                'AVG_BW' => 0,
                'STATUS' => 'NOT_FOUND'
            ];
        }

        $row = $this->db
            ->select('PLANT, MATERIAL, QTY, BW, AVG_BW, STATUS')
            ->from('abc_mst_company_stock')
            ->where('PLANT', $plant)
            ->where('MATERIAL', $material)
            ->get()
            ->row_array();

        if (!$row) {
            return [
                'PLANT' => $plant,
                'MATERIAL' => $material,
                'QTY' => 0,
                'BW' => 0,
                'AVG_BW' => 0,
                'STATUS' => 'NOT_FOUND'
            ];
        }

        return [
            'PLANT' => $row['PLANT'] ?? $plant,
            'MATERIAL' => $row['MATERIAL'] ?? $material,
            'QTY' => (float)($row['QTY'] ?? 0),
            'BW' => (float)($row['BW'] ?? 0),
            'AVG_BW' => (float)($row['AVG_BW'] ?? 0),
            'STATUS' => $row['STATUS'] ?? 'AVAILABLE'
        ];
    }

    private function get_table_columns($table)
    {
        $columns = [];

        $tableName = str_replace('`', '', $table);
        $result = $this->db->query('SHOW COLUMNS FROM `' . $tableName . '`');

        if ($result && $result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $columns[] = $row['Field'];
            }
        }

        return $columns;
    }

    public function insert_company_stock_transaction(array $rows)
    {
        if (empty($rows)) {
            return true;
        }

        $columns = $this->get_table_columns('abc_trx_company_stock_card');

        $payload = [];

        foreach ($rows as $row) {
            $item = [];

            if (in_array('PLANT', $columns, true)) {
                $item['PLANT'] = $row['PLANT'] ?? null;
            }

            if (in_array('MATERIAL', $columns, true)) {
                $item['MATERIAL'] = $row['MATERIAL'] ?? null;
            }

            if (in_array('QTY_OUT', $columns, true)) {
                $item['QTY_OUT'] = (float)($row['QTY_OUT'] ?? 0);
            } elseif (in_array('QTY', $columns, true)) {
                $item['QTY'] = (float)($row['QTY_OUT'] ?? 0);
            }

            if (in_array('BW_OUT', $columns, true)) {
                $item['BW_OUT'] = (float)($row['BW_OUT'] ?? 0);
            } elseif (in_array('BW', $columns, true)) {
                $item['BW'] = (float)($row['BW_OUT'] ?? 0);
            }

            if (in_array('CREATED_AT', $columns, true)) {
                $item['CREATED_AT'] = $row['CREATED_AT'] ?? date('Y-m-d H:i:s');
            }

            if (in_array('CREATED_BY', $columns, true)) {
                $item['CREATED_BY'] = $row['CREATED_BY'] ?? null;
            }

            if (!empty($item)) {
                $payload[] = $item;
            }
        }

        if (empty($payload)) {
            return true;
        }

        return $this->db->insert_batch('abc_trx_company_stock_card', $payload);
    }

    public function update_company_stock_for_sales(array $rows, $createdBy = null)
    {
        foreach ($rows as $row) {
            $stock = $this->get_company_stock($row['PLANT'], $row['MATERIAL']);

            if (($stock['STATUS'] ?? '') === 'NOT_FOUND') {
                continue;
            }

            $newQty = (float)($stock['QTY'] ?? 0) - (float)($row['QTY_OUT'] ?? 0);
            $newBw  = (float)($stock['BW'] ?? 0) - (float)($row['BW_OUT'] ?? 0);
            $avgBw  = $newQty > 0 ? round($newBw / $newQty, 2) : 0;

            $this->db
                ->where('PLANT', $row['PLANT'])
                ->where('MATERIAL', $row['MATERIAL'])
                ->update('abc_mst_company_stock', [
                    'QTY' => $newQty,
                    'BW' => $newBw,
                    'AVG_BW' => $avgBw,
                    'UPDATED_AT' => date('Y-m-d H:i:s'),
                    'UPDATED_BY' => $createdBy
                ]);
        }

        return true;
    }

    public function restore_company_stock_for_sales(array $rows, $createdBy = null)
    {
        foreach ($rows as $row) {
            $stock = $this->get_company_stock($row['PLANT'], $row['MATERIAL']);

            if (($stock['STATUS'] ?? '') === 'NOT_FOUND') {
                continue;
            }

            $newQty = (float)($stock['QTY'] ?? 0) + (float)($row['QTY_OUT'] ?? 0);
            $newBw  = (float)($stock['BW'] ?? 0) + (float)($row['BW_OUT'] ?? 0);
            $avgBw  = $newQty > 0 ? round($newBw / $newQty, 2) : 0;

            $this->db
                ->where('PLANT', $row['PLANT'])
                ->where('MATERIAL', $row['MATERIAL'])
                ->update('abc_mst_company_stock', [
                    'QTY' => $newQty,
                    'BW' => $newBw,
                    'AVG_BW' => $avgBw,
                    'UPDATED_AT' => date('Y-m-d H:i:s'),
                    'UPDATED_BY' => $createdBy
                ]);
        }

        return true;
    }

    public function insert_company_stock_card(array $rows, $referenceNo, $referenceType, $referenceDate, $createdBy = null)
    {
        if (empty($rows)) {
            return true;
        }

        $columns = $this->get_table_columns('abc_trx_company_stock_card');

        foreach ($rows as $row) {
            $stock = $this->get_company_stock($row['PLANT'], $row['MATERIAL']);
            $balanceQty = (float)($stock['QTY'] ?? 0);
            $balanceBw  = (float)($stock['BW'] ?? 0);
            $avgBw      = $balanceQty > 0 ? round($balanceBw / $balanceQty, 2) : 0;

            $payload = [];

            if (in_array('CARD_NO', $columns, true)) {
                $payload['CARD_NO'] = $this->generate_company_stock_card_no();
            }

            if (in_array('PLANT', $columns, true)) {
                $payload['PLANT'] = $row['PLANT'];
            }

            if (in_array('MATERIAL', $columns, true)) {
                $payload['MATERIAL'] = $row['MATERIAL'];
            }

            if (in_array('TRANSACTION_DATE', $columns, true)) {
                $payload['TRANSACTION_DATE'] = $referenceDate;
            }

            if (in_array('REFERENCE_NO', $columns, true)) {
                $payload['REFERENCE_NO'] = $referenceNo;
            }

            if (in_array('REFERENCE_TYPE', $columns, true)) {
                $payload['REFERENCE_TYPE'] = $referenceType;
            }

            if (in_array('QTY_IN', $columns, true)) {
                $payload['QTY_IN'] = 0;
            }

            if (in_array('BW_IN', $columns, true)) {
                $payload['BW_IN'] = 0;
            }

            if (in_array('QTY_OUT', $columns, true)) {
                $payload['QTY_OUT'] = (float)($row['QTY_OUT'] ?? 0);
            }

            if (in_array('BW_OUT', $columns, true)) {
                $payload['BW_OUT'] = (float)($row['BW_OUT'] ?? 0);
            }

            if (in_array('BALANCE_QTY', $columns, true)) {
                $payload['BALANCE_QTY'] = $balanceQty;
            }

            if (in_array('BALANCE_BW', $columns, true)) {
                $payload['BALANCE_BW'] = $balanceBw;
            }

            if (in_array('BALANCE_AVG_BW', $columns, true)) {
                $payload['BALANCE_AVG_BW'] = $avgBw;
            }

            if (in_array('AVG_BW', $columns, true)) {
                $payload['AVG_BW'] = $avgBw;
            }

            if (in_array('REMARK', $columns, true)) {
                $payload['REMARK'] = 'Sales ' . $referenceNo;
            }

            if (in_array('CREATED_AT', $columns, true)) {
                $payload['CREATED_AT'] = date('Y-m-d H:i:s');
            }

            if (in_array('CREATED_BY', $columns, true)) {
                $payload['CREATED_BY'] = $createdBy;
            }

            if (empty($payload)) {
                continue;
            }

            $this->db->insert('abc_trx_company_stock_card', $payload);
        }

        return true;
    }

    public function insert_company_stock_card_reversal(array $rows, $referenceNo, $referenceType, $referenceDate, $createdBy = null)
    {
        if (empty($rows)) {
            return true;
        }

        $columns = $this->get_table_columns('abc_trx_company_stock_card');

        foreach ($rows as $row) {
            $stock = $this->get_company_stock($row['PLANT'], $row['MATERIAL']);
            $balanceQty = (float)($stock['QTY'] ?? 0);
            $balanceBw  = (float)($stock['BW'] ?? 0);
            $avgBw      = $balanceQty > 0 ? round($balanceBw / $balanceQty, 2) : 0;

            $payload = [];

            if (in_array('CARD_NO', $columns, true)) {
                $payload['CARD_NO'] = $this->generate_company_stock_card_no();
            }

            if (in_array('PLANT', $columns, true)) {
                $payload['PLANT'] = $row['PLANT'];
            }

            if (in_array('MATERIAL', $columns, true)) {
                $payload['MATERIAL'] = $row['MATERIAL'];
            }

            if (in_array('TRANSACTION_DATE', $columns, true)) {
                $payload['TRANSACTION_DATE'] = $referenceDate;
            }

            if (in_array('REFERENCE_NO', $columns, true)) {
                $payload['REFERENCE_NO'] = $referenceNo;
            }

            if (in_array('REFERENCE_TYPE', $columns, true)) {
                $payload['REFERENCE_TYPE'] = $referenceType;
            }

            if (in_array('QTY_IN', $columns, true)) {
                $payload['QTY_IN'] = (float)($row['QTY_OUT'] ?? 0);
            }

            if (in_array('BW_IN', $columns, true)) {
                $payload['BW_IN'] = (float)($row['BW_OUT'] ?? 0);
            }

            if (in_array('QTY_OUT', $columns, true)) {
                $payload['QTY_OUT'] = 0;
            }

            if (in_array('BW_OUT', $columns, true)) {
                $payload['BW_OUT'] = 0;
            }

            if (in_array('BALANCE_QTY', $columns, true)) {
                $payload['BALANCE_QTY'] = $balanceQty;
            }

            if (in_array('BALANCE_BW', $columns, true)) {
                $payload['BALANCE_BW'] = $balanceBw;
            }

            if (in_array('BALANCE_AVG_BW', $columns, true)) {
                $payload['BALANCE_AVG_BW'] = $avgBw;
            }

            if (in_array('AVG_BW', $columns, true)) {
                $payload['AVG_BW'] = $avgBw;
            }

            if (in_array('REMARK', $columns, true)) {
                $payload['REMARK'] = 'Sales reversal ' . $referenceNo;
            }

            if (in_array('CREATED_AT', $columns, true)) {
                $payload['CREATED_AT'] = date('Y-m-d H:i:s');
            }

            if (in_array('CREATED_BY', $columns, true)) {
                $payload['CREATED_BY'] = $createdBy;
            }

            if (empty($payload)) {
                continue;
            }

            $this->db->insert('abc_trx_company_stock_card', $payload);
        }

        return true;
    }

    public function delete_company_stock_card_by_reference(
        $referenceNo,
        $referenceType
    )
    {
        return $this->db

            ->where(
                'REFERENCE_NO',
                $referenceNo
            )

            ->where(
                'REFERENCE_TYPE',
                $referenceType
            )

            ->delete(
                'abc_trx_company_stock_card'
            );
    }

    private function generate_company_stock_card_no()
    {
        $prefix = 'CSC' . date('Ymd');

        $this->db->select('CARD_NO');
        $this->db->from('abc_trx_company_stock_card');
        $this->db->like('CARD_NO', $prefix, 'after');
        $this->db->order_by('CARD_NO', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        if ($row) {
            $last = (int) substr($row->CARD_NO, -4);
            $running = $last + 1;
        } else {
            $running = 1;
        }

        return $prefix . str_pad($running, 4, '0', STR_PAD_LEFT);
    }

    public function get_all_sales()
    {
        return $this->db->get('abc_mst_sales')->result_array();
    }

    public function generate_saving_no()
    {
        $prefix = 'SV' . date('Ymd');

        $row = $this->db
            ->select('SV_NO')
            ->from('abc_mst_saving')
            ->like('SV_NO', $prefix, 'after')
            ->order_by('SV_NO', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        $seq = $row
            ? ((int) substr($row->SV_NO, -4) + 1)
            : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function get_saving_by_sales($sales, $plant)
    {
        return $this->db
            ->from('abc_mst_saving')
            ->where('PLANT', $plant)
            ->where('RELATED', 'SALES')
            ->group_start()
                ->where('REMARK', 'AUTO FROM SALES ' . $sales)
                ->or_like('REMARK', 'AUTO FROM SALES ' . $sales, 'before')
            ->group_end()
            ->where('DELETED IS NULL', null, false)
            ->order_by('CREATED_AT', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function insert_saving_batch(array $rows)
    {
        if (empty($rows)) {
            return true;
        }

        return $this->db->insert_batch('abc_mst_saving', $rows);
    }

    public function delete_saving_by_sales(
        $sales,
        $plant,
        $deletedBy = null
    )
    {
        $data = [

            'DELETED' =>
                date('Y-m-d H:i:s')

        ];

        if ($deletedBy) {

            $data['DELETED_BY'] =
                $deletedBy;
        }

        return $this->db

            ->where(
                'PLANT',
                $plant
            )

            ->where(
                'SALES',
                $sales
            )

            ->where(
                'RELATED',
                'SALES'
            )

            ->where(
                'DELETED IS NULL',
                null,
                false
            )

            ->update(
                'abc_mst_saving',
                $data
            );
    }

    public function is_saving_used_by_cash_in(
        $sales,
        $plant
    )
    {
        $count =
            $this->db
                ->from(
                    'abc_mst_cash_in_saving'
                )
                ->where(
                    'SALES',
                    $sales
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )
                ->count_all_results();

        return $count > 0;
    }
}
