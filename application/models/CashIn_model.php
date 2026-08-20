<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CashIn_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */
    public function get_data(
        $limit,
        $start,
        $search = '',
        $order = 'CASHIN_DATE',
        $dir = 'ASC',
        $pembayaran = '',
        $date_from = '',
        $date_to = ''
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            c.*,

            plant.CODE_NAME AS PLANT_NAME,

            customer.FULL_NAME AS CUSTOMER_NAME,

            COUNT(DISTINCT d.SALES) AS TOTAL_INVOICE,

            (
                c.AMOUNT
                -
                COALESCE(
                    SUM(d.AMOUNT_OFFSET),
                    0
                )
            ) AS TOTAL_DEPOSIT,

            (
                NOT EXISTS (

                    /*
                    |--------------------------------------------------------------------------
                    | NEWER ACTIVE CASH IN VIA SALES
                    |--------------------------------------------------------------------------
                    */

                    SELECT 1

                    FROM abc_mst_cash_in c2

                    WHERE c2.PLANT = c.PLANT
                    AND c2.CUSTOMER = c.CUSTOMER
                    AND c2.DELETED IS NULL
                    AND c2.CREATED_AT > c.CREATED_AT

                    AND EXISTS (

                        SELECT 1

                        FROM abc_mst_cash_in_detail d_old

                        WHERE d_old.CASH_IN = c.CASH_IN
                            AND d_old.PLANT = c.PLANT
                            AND d_old.DELETED IS NULL

                            AND EXISTS (

                                SELECT 1

                                FROM abc_mst_cash_in_detail d_new

                                WHERE d_new.CASH_IN = c2.CASH_IN
                                AND d_new.PLANT = c2.PLANT
                                AND d_new.SALES = d_old.SALES
                                AND d_new.DELETED IS NULL

                            )

                    )

                )

                AND

                /*
                |--------------------------------------------------------------------------
                | NEWER ACTIVE CASH IN VIA SAVING
                |--------------------------------------------------------------------------
                */

                NOT EXISTS (

                    SELECT 1

                    FROM abc_mst_cash_in c2

                    WHERE c2.PLANT = c.PLANT
                    AND c2.CUSTOMER = c.CUSTOMER
                    AND c2.DELETED IS NULL
                    AND c2.CREATED_AT > c.CREATED_AT

                    AND EXISTS (

                        SELECT 1

                        FROM abc_mst_cash_in_saving s_old

                        WHERE s_old.CASH_IN = c.CASH_IN
                            AND s_old.PLANT = c.PLANT
                            AND s_old.DELETED IS NULL

                            AND EXISTS (

                                SELECT 1

                                FROM abc_mst_cash_in_saving s_new

                                WHERE s_new.CASH_IN = c2.CASH_IN
                                AND s_new.PLANT = c2.PLANT
                                AND s_new.SALES = s_old.SALES
                                AND s_new.DELETED IS NULL

                            )

                    )

                )

            ) AS IS_LATEST

        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from('abc_mst_cash_in c');

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.CODE COLLATE utf8mb4_unicode_ci =
                c.PLANT COLLATE utf8mb4_unicode_ci
                AND plant.HEAD_CODE = 'PLANT'
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
            'abc_cd_customer customer',
            "
                customer.CUST COLLATE utf8mb4_unicode_ci =
                c.CUSTOMER COLLATE utf8mb4_unicode_ci
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_cash_in_detail d',
            "
                d.CASH_IN = c.CASH_IN
                AND d.PLANT = c.PLANT
                AND d.DELETED IS NULL
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        if(!empty($pembayaran)){

            $this->db->where(
                'c.PEMBAYARAN',
                $pembayaran
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DATE
        |--------------------------------------------------------------------------
        */

        if(!empty($date_from)){

            $this->db->where(
                'DATE(c.CASHIN_DATE) >=',
                $date_from
            );

        }

        if(!empty($date_to)){

            $this->db->where(
                'DATE(c.CASHIN_DATE) <=',
                $date_to
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($search)){

            $this->db->group_start();

            $this->db->like(
                'c.CASH_IN',
                $search
            );

            $this->db->or_like(
                'customer.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'c.SLIP_NO',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by('c.CASH_IN');

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'c.' . $order,
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
        $search = '',
        $pembayaran = '',
        $date_from = '',
        $date_to = ''
    )
    {
        $this->db->from(
            'abc_mst_cash_in c'
        );

        $this->db->join(
            'abc_cd_customer customer',
            "
                customer.CUST COLLATE utf8mb4_unicode_ci =
                c.CUSTOMER COLLATE utf8mb4_unicode_ci
            ",
            'left',
            false
        );

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        if(!empty($pembayaran)){

            $this->db->where(
                'c.PEMBAYARAN',
                $pembayaran
            );

        }

        if(!empty($date_from)){

            $this->db->where(
                'DATE(c.CASHIN_DATE) >=',
                $date_from
            );

        }

        if(!empty($date_to)){

            $this->db->where(
                'DATE(c.CASHIN_DATE) <=',
                $date_to
            );

        }

        if(!empty($search)){

            $this->db->group_start();

            $this->db->like(
                'c.CASH_IN',
                $search
            );

            $this->db->or_like(
                'customer.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'c.SLIP_NO',
                $search
            );

            $this->db->group_end();

        }

        return $this->db
            ->count_all_results();
    }

    public function generate_cash_in_number($plant)
    {
        $date = date('Ymd');

        $prefix =
            $date . 'CI';

        $last = $this->db

            ->select('CASH_IN')

            ->like('CASH_IN', $prefix, 'after')

            ->order_by('CASH_IN', 'DESC')

            ->get('abc_mst_cash_in')

            ->row_array();

        $urut = 1;

        if($last){

            $urut =
                (int) substr(
                    $last['CASH_IN'],
                    -4
                ) + 1;
        }

        return
            $prefix .
            str_pad(
                $urut,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function generate_slip_number($plant)
    {
        $date = date('Ymd');

        $prefix =
            $date . 'CI';

        $last = $this->db

            ->select('SLIP_NO')

            ->like('SLIP_NO', $prefix, 'after')

            ->order_by('SLIP_NO', 'DESC')

            ->get('abc_mst_cash_in')

            ->row_array();

        $urut = 1;

        if($last){

            $urut =
                (int) substr(
                    $last['SLIP_NO'],
                    -4
                ) + 1;
        }

        return
            $prefix .
            str_pad(
                $urut,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function get_sales_by_number(
        $sales,
        $plant
    ){
        return $this->db

            ->where('SALES', $sales)

            ->where('PLANT', $plant)

            ->where('DELETED IS NULL', null, false)

            ->get('abc_mst_sales')

            ->row_array();
    }

    /**
     * ============================================================
     * SALES PICKER
     * ============================================================
     */
    public function get_sales_picker($plant, $customer, $search = null)
    {
        /*
        |--------------------------------------------------------------------------
        | SALES DETAIL
        |--------------------------------------------------------------------------
        | SALES_AMOUNT = SUM(TOTAL) dari sales detail aktif.
        |
        | Ini yang kita tampilkan sebagai nilai Sales setelah discount
        | sesuai nilai detail.
        |--------------------------------------------------------------------------
        */

        $salesDetailSql = "
            SELECT
                sd.SALES,
                sd.PLANT,
                SUM(
                    CASE
                        WHEN sd.DELETED IS NULL
                        THEN COALESCE(sd.TOTAL, 0)
                        ELSE 0
                    END
                ) AS SALES_AMOUNT
            FROM abc_mst_sales_detail sd
            GROUP BY
                sd.SALES,
                sd.PLANT
        ";

        /*
        |--------------------------------------------------------------------------
        | SAVING
        |--------------------------------------------------------------------------
        |
        | Data baru:
        |   sv.SALES terisi
        |
        | Legacy:
        |   sv.SALES bisa NULL
        |   referensi SALES diambil dari REMARK
        |
        | Hanya Saving yang:
        |   - RELATED = SALES
        |   - aktif
        |   - REMAIN > 0
        |--------------------------------------------------------------------------
        */

        $savingSql = "
            SELECT
                x.PLANT,
                x.SALES,

                SUM(x.AMOUNT) AS SAVING_AMOUNT,

                SUM(x.REMAIN) AS SAVING_REMAIN

            FROM (

                SELECT
                    sv.PLANT,
                    sv.AMOUNT,
                    sv.REMAIN,

                    CASE

                        /*
                        --------------------------------------------------
                        | DATA BARU
                        --------------------------------------------------
                        */
                        WHEN sv.SALES IS NOT NULL
                            AND TRIM(sv.SALES) <> ''
                        THEN TRIM(sv.SALES)

                        /*
                        --------------------------------------------------
                        | LEGACY:
                        | AUTO FROM SALES 20260806SLS0049
                        --------------------------------------------------
                        */
                        WHEN sv.REMARK LIKE 'AUTO FROM SALES %'
                        THEN TRIM(
                            SUBSTRING(
                                sv.REMARK,
                                LENGTH('AUTO FROM SALES ') + 1
                            )
                        )

                        /*
                        --------------------------------------------------
                        | LEGACY:
                        | user remark | AUTO FROM SALES 20260806SLS0049
                        --------------------------------------------------
                        */
                        WHEN sv.REMARK LIKE '%AUTO FROM SALES %'
                        THEN TRIM(
                            SUBSTRING_INDEX(
                                sv.REMARK,
                                'AUTO FROM SALES ',
                                -1
                            )
                        )

                        ELSE NULL

                    END AS SALES

                FROM abc_mst_saving sv

                WHERE sv.RELATED = 'SALES'
                AND sv.DELETED IS NULL
                AND COALESCE(sv.REMAIN, 0) > 0

            ) x

            WHERE x.SALES IS NOT NULL
            AND TRIM(x.SALES) <> ''

            GROUP BY
                x.PLANT,
                x.SALES
        ";

        /*
        |--------------------------------------------------------------------------
        | MAIN QUERY
        |--------------------------------------------------------------------------
        */

        $this->db->select("
            s.SALES,
            s.PLANT,
            s.CUSTOMER,
            s.CUSTOMER_NAME,
            s.SALES_DATE,
            s.SLIP_NO,
            s.JENIS_PAY,

            /*
            --------------------------------------------------------------
            | SALES AMOUNT
            --------------------------------------------------------------
            */
            COALESCE(sd.SALES_AMOUNT, 0)
                AS SALES_AMOUNT,

            COALESCE(s.DISCOUNT, 0)
                AS DISCOUNT,

            /*
            --------------------------------------------------------------
            | SALES REMAIN
            --------------------------------------------------------------
            */
            GREATEST(
                COALESCE(s.REMAIN, 0),
                0
            ) AS SALES_REMAIN,

            /*
            --------------------------------------------------------------
            | SAVING
            --------------------------------------------------------------
            */
            COALESCE(sv.SAVING_AMOUNT, 0)
                AS SAVING_AMOUNT,

            COALESCE(sv.SAVING_REMAIN, 0)
                AS SAVING_REMAIN,

            /*
            --------------------------------------------------------------
            | GRAND OUTSTANDING
            --------------------------------------------------------------
            */
            (
                GREATEST(
                    COALESCE(s.REMAIN, 0),
                    0
                )
                +
                COALESCE(sv.SAVING_REMAIN, 0)
            ) AS GRAND_OUTSTANDING,

            /*
            --------------------------------------------------------------
            | SOURCE
            --------------------------------------------------------------
            */
            CASE
                WHEN s.RECEIVE IS NOT NULL
                    AND TRIM(s.RECEIVE) <> ''
                THEN 'RECEIVE'
                ELSE 'SALES'
            END AS SALES_SOURCE,

            s.RECEIVE,

            s.REMARK AS SALES_REMARK,

            s.STATUS

        ", false);

        $this->db->from(
            'abc_mst_sales s'
        );

        /*
        |--------------------------------------------------------------------------
        | JOIN SALES DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            "($salesDetailSql) sd",
            "
                sd.SALES = s.SALES
                AND sd.PLANT = s.PLANT
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | JOIN SAVING
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            "($savingSql) sv",
            "
                sv.SALES = s.SALES
                AND sv.PLANT = s.PLANT
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            's.PLANT',
            $plant
        );

        $this->db->where(
            's.CUSTOMER',
            $customer
        );

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | HANYA INVOICE YANG MASIH PUNYA OUTSTANDING
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            "
            (
                GREATEST(
                    COALESCE(s.REMAIN, 0),
                    0
                )
                +
                COALESCE(sv.SAVING_REMAIN, 0)
            ) > 0
            ",
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (!empty($search)) {

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $search
            );

            $this->db->or_like(
                's.CUSTOMER_NAME',
                $search
            );

            $this->db->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | FIFO ORDER
        |--------------------------------------------------------------------------
        */

        $this->db
            ->order_by(
                's.SALES_DATE',
                'ASC'
            )
            ->order_by(
                's.SALES',
                'ASC'
            );

        $rows =
            $this->db
                ->get()
                ->result_array();

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE NUMERIC VALUES
        |--------------------------------------------------------------------------
        */

        foreach ($rows as &$row) {

            $row['SALES_AMOUNT'] =
                round(
                    (float)(
                        $row['SALES_AMOUNT'] ?? 0
                    ),
                    2
                );

            $row['DISCOUNT'] =
                round(
                    (float)(
                        $row['DISCOUNT'] ?? 0
                    ),
                    2
                );

            $row['SALES_REMAIN'] =
                round(
                    (float)(
                        $row['SALES_REMAIN'] ?? 0
                    ),
                    2
                );

            $row['SAVING_AMOUNT'] =
                round(
                    (float)(
                        $row['SAVING_AMOUNT'] ?? 0
                    ),
                    2
                );

            $row['SAVING_REMAIN'] =
                round(
                    (float)(
                        $row['SAVING_REMAIN'] ?? 0
                    ),
                    2
                );

            $row['GRAND_OUTSTANDING'] =
                round(
                    $row['SALES_REMAIN']
                    +
                    $row['SAVING_REMAIN'],
                    2
                );
        }

        unset($row);

        return $rows;
    }

    public function insert_header($data)
    {
        return $this->db
            ->insert(
                'abc_mst_cash_in',
                $data
            );
    }

    public function insert_detail($data)
    {
        return $this->db
            ->insert(
                'abc_mst_cash_in_detail',
                $data
            );
    }

    public function update_header_by_key(
        $cashIn,
        $plant,
        $data
    ){
        return $this->db

            ->where('CASH_IN', $cashIn)

            ->where('PLANT', $plant)

            ->update(
                'abc_mst_cash_in',
                $data
            );
    }

    public function reset_customer_invoice_remain(
    $customer,
    $plant
    ){
        $this->db
            ->set('REMAIN', 'AMOUNT - IFNULL(DP_AMOUNT,0)', false)

            ->set('STATUS', '
                CASE
                    WHEN (AMOUNT - IFNULL(DP_AMOUNT,0)) <= 0
                        THEN "PAID"
                    ELSE "OPEN"
                END
            ', false)

            ->where('CUSTOMER', $customer)

            ->where('PLANT', $plant)

            ->where('JENIS_PAY', 'TEMPO')

            ->where('DELETED IS NULL', null, false)

            ->update('abc_mst_sales');
    }

    public function delete_customer_cashin_detail(
    $customer,
    $plant
    ){
        $cashins = $this->db

            ->select('CASH_IN')

            ->from('abc_abc_mst_cash_in')

            ->where('CUSTOMER', $customer)

            ->where('PLANT', $plant)

            ->where('DELETED IS NULL', null, false)

            ->get()

            ->result_array();

        if(empty($cashins)){
            return;
        }

        $cashinNos =
            array_column($cashins, 'CASH_IN');

        $this->db

            ->where_in('CASH_IN', $cashinNos)

            ->where('PLANT', $plant)

            ->delete('abc_abc_mst_cash_in_detail');
    }

    public function get_customer_cashin_history(
    $customer,
    $plant
    ){
        return $this->db

            ->from('abc_abc_mst_cash_in')

            ->where('CUSTOMER', $customer)

            ->where('PLANT', $plant)

            ->where('DELETED IS NULL', null, false)

            ->order_by('CASHIN_DATE', 'ASC')

            ->order_by('CASH_IN', 'ASC')

            ->get()

            ->result_array();
    }

    public function get_open_invoice_fifo(
    $customer,
    $plant
    ){
        return $this->db

            ->from('abc_mst_sales')

            ->where('CUSTOMER', $customer)

            ->where('PLANT', $plant)

            ->where('JENIS_PAY', 'TEMPO')

            ->where('REMAIN >', 0)

            ->where('DELETED IS NULL', null, false)

            ->order_by('SALES_DATE', 'ASC')

            ->order_by('SALES', 'ASC')

            ->get()

            ->result_array();
    }

    public function rebuild_customer_fifo_history(
        $customer,
        $plant
    ){
        /*
        |--------------------------------------------------------------------------
        | RESET REMAIN
        |--------------------------------------------------------------------------
        */

        $this->reset_customer_invoice_remain(
            $customer,
            $plant
        );

        /*
        |--------------------------------------------------------------------------
        | DELETE DETAIL
        |--------------------------------------------------------------------------
        */

        $this->delete_customer_cashin_detail(
            $customer,
            $plant
        );

        /*
        |--------------------------------------------------------------------------
        | GET HISTORY
        |--------------------------------------------------------------------------
        */

        $cashins =
            $this->get_customer_cashin_history(
                $customer,
                $plant
            );

        foreach($cashins as $cashin){

            $amount =
                (float)$cashin['TOTAL'];

            $balance = $amount;

            $seqNo = 1;

            /*
            |--------------------------------------------------------------------------
            | FIFO INVOICE
            |--------------------------------------------------------------------------
            */

            $invoices =
                $this->get_open_invoice_fifo(
                    $customer,
                    $plant
                );

            foreach($invoices as $inv){

                if($balance <= 0){
                    break;
                }

                $remain =
                    (float)$inv['REMAIN'];

                if($remain <= 0){
                    continue;
                }

                $offset =
                    min(
                        $balance,
                        $remain
                    );

                /*
                |--------------------------------------------------------------------------
                | INSERT DETAIL
                |--------------------------------------------------------------------------
                */

                $this->db->insert(
                    'abc_abc_mst_cash_in_detail',
                    [

                        'PLANT' => $plant,

                        'CASH_IN' =>
                            $cashin['CASH_IN'],

                        'SEQ_NO' =>
                            $seqNo,

                        'SALES' =>
                            $inv['SALES'],

                        'ORG_SLIP_NO' =>
                            $inv['SLIP_NO'],

                        'AMOUNT_INVOICE' =>
                            $inv['AMOUNT'],

                        'AMOUNT_OFFSET' =>
                            $offset,

                        'REMAIN' =>
                            $remain - $offset,

                        'CREATED_AT' =>
                            date('Y-m-d H:i:s'),

                        'CREATED_BY' =>
                            'SYSTEM_REBUILD'
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | UPDATE SALES
                |--------------------------------------------------------------------------
                */

                $newRemain =
                    $remain - $offset;

                $status =
                    $newRemain <= 0
                        ? 'PAID'
                        : 'PARTIAL';

                $this->db

                    ->where(
                        'SALES',
                        $inv['SALES']
                    )

                    ->where(
                        'PLANT',
                        $plant
                    )

                    ->update(
                        'abc_mst_sales',
                        [
                            'REMAIN' =>
                                $newRemain,

                            'STATUS' =>
                                $status
                        ]
                    );

                $balance -= $offset;

                $seqNo++;
            }
        }
    }

    public function get_plant_select2()
    {
        return $this->db

            ->select('
                CODE as id,
                CODE_NAME as text
            ')

            ->from('abc_cd_code')

            ->where('HEAD_CODE', 'PLANT')

            ->where('CODE !=', '*')

            ->order_by('CODE', 'ASC')

            ->get()

            ->result_array();
    }

    public function user_has_plant($username, $plant)
    {
        if (!$plant) return false;

        $plant  = (string)trim($plant);
        $plants = array_map(
            fn($p) => (string)trim($p),
            $this->get_user_plants($username)
        );

        return in_array($plant, $plants, true);
    }

    public function get_header($plant, $cashIn)
    {
        return $this->db
            ->from('abc_mst_cash_in')
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashIn
            ])
            ->get()
            ->row_array();
    }

    public function search_customer($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('abc_cd_customer');
        $this->db->where('STATUS', 'Y');

        // 🔥 FILTER CUSTOMER
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

    public function search_rekening($q = null, $limit = 20)
    {
        $this->db->select('CODE as id, CODE_NAME as name');
        $this->db->from('abc_cd_code');

        // 🔥 FILTER UTAMA
        $this->db->where('HEAD_CODE', 'AK');
        $this->db->where('CODE !=', '*');

        // 🔍 SEARCH
        if ($q) {
            $this->db->group_start();
                $this->db->like('CODE', $q);
                $this->db->or_like('CODE_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CODE', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['name']
            ];
        }

        return $out;
    }

    public function get_plant_code_by_id($id)
    {
        return $this->db
            ->select('code')
            ->from('abc_cd_code')
            ->where([
                'head_code' => 'AJ',
                'id'        => $id
            ])
            ->get()
            ->row('code');
    }

    public function get_plant_code_by_code($code)
    {
        return $this->db
            ->select('code')
            ->from('abc_cd_code')
            ->where([
                'head_code' => 'AJ',
                'code'      => $code
            ])
            ->get()
            ->row('code');
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */
    public function generate_cash_in_no($plant, $date)
    {
        $ymd = date('Ymd', strtotime($date));
        $prefix = $ymd . 'CI';
        
        $this->db->select('CASH_IN');
        $this->db->from('abc_mst_cash_in');
        $this->db->where('PLANT', $plant);
        $this->db->like('CASH_IN', $prefix, 'after');
        $this->db->order_by('CASH_IN', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        if ($row) {
            $seq = (int)substr($row->CASH_IN, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generate_slip_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'AG';

        $this->db->select('SLIP_NO');
        $this->db->from('abc_mst_cash_in');
        $this->db->where('PLANT', $plant);
        $this->db->like('SLIP_NO', $prefix, 'after');
        $this->db->order_by('SLIP_NO', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int)substr($row->SLIP_NO, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function get_invoice_remain($sales, $plant)
    {
        return $this->db
            ->select('CUSTOMER as customer, AMOUNT as invoice_amount, REMAIN as remain')
            ->from('abc_mst_sales')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->get()
            ->row_array();
    }

    public function insert_customer_deposit($data)
    {
        return $this->db->insert('abc_mst_customer_deposit', $data);
    }

    public function use_deposit($id, $amount)
    {
        $this->db->set('REMAIN', "REMAIN - {$amount}", false)
                ->where('ID', $id)
                ->update('abc_mst_customer_deposit');
    }

    public function get_available_deposit($customer, $plant)
    {
        return $this->db
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->order_by('CREATED_AT', 'ASC') // FIFO deposit
            ->get('abc_mst_customer_deposit')
            ->result_array();
    }

    public function get_total_deposit($customer, $plant)
    {
        return $this->db->select_sum('REMAIN')
            ->from('abc_mst_customer_deposit')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->get()
            ->row()
            ->REMAIN ?? 0;
    }

    public function delete_customer_deposit_by_cash_in(
        $cashInNo,
        $plant,
        $deletedBy = null
    ) {
        $data = [
            'DELETED' => date('Y-m-d H:i:s')
        ];

        if ($deletedBy) {
            $data['DELETED_BY'] = $deletedBy;
        }

        return $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->update(
                'abc_mst_customer_deposit',
                $data
            );
    }

    public function get_invoice_remain_batch($salesList, $plant)
    {
        $this->db->select('SALES, CUSTOMER, AMOUNT, REMAIN');
        $this->db->from('abc_mst_sales');
        $this->db->where('PLANT', $plant);
        $this->db->where_in('SALES', $salesList);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[$r['SALES']] = $r;
        }
        return $out;
    }

    // public function reduce_invoice_batch($updates)
    // {
    //     foreach ($updates as $u) {
    //         $this->db->set('REMAIN', 'REMAIN - '.$u['amount'], false);
    //         $this->db->where('SALES', $u['sales']);
    //         $this->db->where('PLANT', $u['plant']);
    //         $this->db->update('abc_mst_sales');
    //     }
    // }

    public function get_cash_in_details($cashIn, $plant){
        return $this->db->get_where('abc_mst_cash_in_detail', [
            'CASH_IN'=>$cashIn,'PLANT'=>$plant,'DELETED'=>null
        ])->result_array();
    }

    // public function restore_invoice_offset($sales,$plant,$amount){
    //     $this->db->set('AMOUNT_PAID',"AMOUNT_PAID-$amount",false)
    //             ->where(['SALES'=>$sales,'PLANT'=>$plant])
    //             ->update('abc_mst_sales');
    // }

    // public function rollback_deposit_usage($cashIn)
    // {
    //     $this->db->set('REMAIN','REMAIN+USED_AMOUNT',false)
    //             ->where('CASH_IN',$cashIn)
    //             ->update('abc_mst_customer_deposit');
    // }

    public function delete_cash_in_details($cashIn,$plant){
        $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
                ->delete('abc_mst_cash_in_detail');
    }

    // public function restore_invoice_batch($updates)
    // {
    //     foreach ($updates as $u) {
    //         $this->db->set('REMAIN', 'REMAIN + '.$u['amount'], false);
    //         $this->db->where('SALES', $u['sales']);
    //         $this->db->where('PLANT', $u['plant']);
    //         $this->db->update('abc_mst_sales');
    //     }
    // }

    public function restore_deposit_usage_by_cash_in($cashIn)
    {
        $this->db->set('REMAIN', 'AMOUNT', false);
        $this->db->where('CASH_IN', $cashIn);
        $this->db->update('abc_mst_customer_deposit');
    }

    public function recalc_invoice($sales, $plant)
    {
        $this->db->query("
            SELECT SALES 
            FROM mst_sales 
            WHERE SALES = ? AND PLANT = ? 
            FOR UPDATE
        ", [$sales, $plant]);

        $inv = $this->db->select('AMOUNT')
            ->from('abc_mst_sales')
            ->where([
                'SALES' => $sales,
                'PLANT' => $plant
            ])
            ->get()->row_array();

        if (!$inv) return;

        $invoiceAmount = (float)$inv['AMOUNT'];

        // 🔥 Semua pembayaran termasuk DP dibaca dari sini
        $paid = $this->db->select('IFNULL(SUM(AMOUNT_OFFSET),0) AS TOTAL')
            ->from('abc_mst_cash_in_detail')
            ->where([
                'SALES'   => $sales,
                'PLANT'   => $plant,
                'DELETED' => null
            ])
            ->get()->row_array();

        $totalPaid = (float)$paid['TOTAL'];

        // ✅ DP TIDAK DIKURANGI LAGI
        $remain = max($invoiceAmount - $totalPaid, 0);

        if ($remain <= 0) {
            $status = 'PAID';
        } elseif ($totalPaid > 0) {
            $status = 'PARTIAL';
        } else {
            $status = 'UNPAID';
        }

        $this->db->where([
                'SALES' => $sales,
                'PLANT' => $plant
            ])
            ->update('abc_mst_sales', [
                'REMAIN'     => $remain,
                'STATUS'     => $status,
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ]);
    }

    public function delete_deposit_by_cash_in($cashIn)
    {
        $this->db->where('CASH_IN', $cashIn);
        $this->db->delete('abc_mst_customer_deposit');
    }

    public function delete_deposit_by_cashin($cashIn){
        $this->db->where('CASH_IN',$cashIn)
                ->delete('abc_mst_customer_deposit');
    }

    public function get_fifo_open_invoices($customer, $plant)
    {
        return $this->db
            ->from('abc_mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO') // 🔥 WAJIB TEMPO
            ->where('REMAIN >', 0)
            ->group_start()               // 🔥 BELUM LUNAS
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->order_by('SALES_DATE', 'ASC')
            ->order_by('SALES', 'ASC')
            ->get()
            ->result_array();
    }

    public function reduce_invoice_remain($sales, $plant, $amount)
    {
        // Hanya lock untuk mencegah race
        $this->lock_invoice_row($sales, $plant);

        // Tidak perlu update remain di sini
        return true;
    }

    public function restore_invoice_remain($sales, $plant, $amount)
    {
        $this->db->set('REMAIN', 'REMAIN + '.$amount, false)
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->update('abc_mst_sales');
    }

    public function get_next_seq($cashIn, $plant)
    {
        $row = $this->db->select_max('SEQ_NO')
            ->where('CASH_IN',$cashIn)
            ->where('PLANT',$plant)
            ->get('abc_mst_cash_in_detail')
            ->row();

        return ($row->SEQ_NO ?? 0) + 1;
    }

    public function lock_invoice_row($sales, $plant)
    {
        return $this->db->query("
            SELECT SALES
            FROM mst_sales
            WHERE SALES = ? AND PLANT = ?
            FOR UPDATE
        ", [$sales, $plant])->row();
    }

    public function simulate_fifo($customer, $plant, $newAmount)
    {
        $allocations = [];
        $newAmount   = round((float) $newAmount, 2);

        $savingDebt = round(
            (float) $this->get_customer_saving_debt($customer, $plant),
            2
        );

        $savingPayment = min($newAmount, max($savingDebt, 0));
        $available     = round($newAmount - $savingPayment, 2);

        $invoices = $this->get_fifo_open_invoices($customer, $plant);

        foreach ($invoices as $inv) {

            if ($available <= 0) break;

            $remainInvoice = round((float)$inv['REMAIN'], 2);
            if ($remainInvoice <= 0) continue;

            $offset = round(min($remainInvoice, $available), 2);

            $allocations[] = [
                'sales'                 => $inv['SALES'],
                'sales_date'            => $inv['SALES_DATE'],
                'invoice_amount'        => (float)$inv['AMOUNT'],
                'invoice_remain_before' => $remainInvoice,
                'offset'                => $offset
            ];

            $available = round($available - $offset, 2);
        }

        return [
            'allocations'       => $allocations,
            'saving_debt'       => $savingDebt,
            'saving_payment'    => $savingPayment,
            'deposit_used'      => 0,
            'deposit_remaining' => max($available, 0)
        ];
    }

    public function get_valid_invoice_for_payment($sales, $customer, $plant)
    {
        return $this->db
            ->select('SALES, AMOUNT, REMAIN, SALES_DATE')
            ->from('abc_mst_sales')
            ->where('SALES', $sales)
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO')
            ->where('REMAIN >', 0)
            ->group_start()
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->get()
            ->row_array();
    }

    public function get_all_open_tempo_invoices($customer, $plant)
    {
        $rows = $this->db
            ->select('SALES, SALES_DATE, AMOUNT, REMAIN')
            ->from('abc_mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO')
            ->where('REMAIN >', 0)
            ->group_start()
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->order_by('SALES_DATE', 'ASC')
            ->order_by('SALES', 'ASC')
            ->get()
            ->result_array();

        // 🔥 PENTING: ubah decimal string jadi float
        foreach ($rows as &$r) {
            $r['AMOUNT'] = (float)$r['AMOUNT'];
            $r['REMAIN'] = (float)$r['REMAIN'];
        }

        return $rows;
    }

    public function deposit_still_used($cashIn)
    {
        return $this->db
            ->where('CASH_IN_REF', $cashIn) // kolom referensi saat deposit dipakai
            ->count_all_results('abc_mst_customer_deposit_usage') > 0;
    }

    public function deposit_has_remain($cashIn)
    {
        return $this->db
            ->where('CASH_IN', $cashIn)
            ->where('REMAIN < AMOUNT', null, false)
            ->count_all_results('abc_mst_customer_deposit') > 0;
    }

    public function rebuild_customer_payment_history($customer, $plant)
    {
        $this->db->trans_start();

        /* =========================
        1. RESET SEMUA INVOICE (DP AWARE)
        REMAIN = TOTAL - DP
        ========================= */
        $this->db->set('REMAIN', 'AMOUNT - DP_AMOUNT', false)
                ->set('STATUS', "'UNPAID'", false)
                ->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->update('abc_mst_sales');


        /* =========================
        2. HAPUS SEMUA DEPOSIT CUSTOMER
        ========================= */
        $this->db->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->delete('abc_mst_customer_deposit');


        /* =========================
        3. HAPUS SEMUA DETAIL OFFSET
        (berdasarkan header CASH IN milik customer tsb)
        ========================= */
        $this->db->where_in('CASH_IN', function($db) use ($customer,$plant){
            $db->select('CASH_IN')
            ->from('abc_mst_cash_in')
            ->where('CUSTOMER',$customer)
            ->where('PLANT',$plant);
        }, false)->delete('abc_mst_cash_in_detail');


        /* =========================
        4. AMBIL SEMUA CASH IN URUT TANGGAL (FIFO GLOBAL)
        ========================= */
        $cashins = $this->db->from('abc_mst_cash_in')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->order_by('CASHIN_DATE', 'ASC')
            ->order_by('CASH_IN', 'ASC')
            ->get()->result_array();


        foreach ($cashins as $cash) {

            $cashInNo = $cash['CASH_IN'];
            $amount   = (float)$cash['AMOUNT'];
            $seq      = 1;

            /* =========================
            4A. APPLY KE INVOICE (FIFO)
            ========================= */
            $invoices = $this->db->from('abc_mst_sales')
                ->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->where('REMAIN >', 0)
                ->order_by('SALES_DATE', 'ASC')
                ->get()->result_array();

            foreach ($invoices as $inv) {

                if ($amount <= 0) break;

                $remainInvoice = (float)$inv['REMAIN'];
                if ($remainInvoice <= 0) continue;

                $offset = min($remainInvoice, $amount);

                /* INSERT DETAIL OFFSET */
                $this->db->insert('abc_mst_cash_in_detail', [
                    'CASH_IN'        => $cashInNo,
                    'PLANT'          => $plant,
                    'SEQ_NO'         => $seq++,
                    'SALES'          => $inv['SALES'],
                    'AMOUNT_INVOICE' => $inv['AMOUNT'],
                    'AMOUNT_OFFSET'  => $offset,
                    'DATE_OFFSET'    => $cash['CASHIN_DATE'],
                    'ORG_SLIP_NO'    => 'AUTO',
                    'SLIP_NO'        => 'AUTO',
                    'CREATED_AT'     => date('Y-m-d H:i:s')
                ]);

                /* KURANGI REMAIN INVOICE */
                $this->db->set('REMAIN', "REMAIN - $offset", false)
                        ->where('SALES', $inv['SALES'])
                        ->where('PLANT', $plant)
                        ->update('abc_mst_sales');

                $amount -= $offset;
            }

            /* =========================
            4B. SISA UANG → DEPOSIT
            ========================= */
            if ($amount > 0) {
                $this->db->insert('abc_mst_customer_deposit', [
                    'CUSTOMER'   => $customer,
                    'PLANT'      => $plant,
                    'CASH_IN'    => $cashInNo,
                    'AMOUNT'     => $amount,
                    'REMAIN'     => $amount,
                    'CREATED_AT' => date('Y-m-d H:i:s')
                ]);
            }
        }


        /* =========================
        5. HITUNG ULANG STATUS INVOICE
        ========================= */
        $salesList = $this->db->select('SALES, AMOUNT, DP_AMOUNT, REMAIN')
            ->from('abc_mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->get()->result_array();

        foreach ($salesList as $s) {

            $totalTagihan = $s['AMOUNT'] - $s['DP_AMOUNT'];
            $remain       = (float)$s['REMAIN'];

            if ($remain <= 0) {
                $status = 'PAID';
            } elseif ($remain < $totalTagihan) {
                $status = 'PARTIAL';
            } else {
                $status = 'UNPAID';
            }

            $this->db->where('SALES', $s['SALES'])
                    ->where('PLANT', $plant)
                    ->update('abc_mst_sales', ['STATUS' => $status]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function is_cash_in_locked($cashInNo, $customer, $plant)
    {
        $current = $this->db->select('CASHIN_DATE')
            ->from('abc_mst_cash_in')
            ->where('CASH_IN', $cashInNo)
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->get()
            ->row();

        if (!$current) return true;

        $exists = $this->db->from('abc_mst_cash_in')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('CASHIN_DATE >', $current->CASHIN_DATE)
            ->count_all_results();

        return $exists > 0;
    }

    public function get_open_deposits_fifo($customer, $plant)
    {
        return $this->db->from('abc_mst_customer_deposit')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->order_by('CREATED_AT', 'ASC') // FIFO deposit lama dulu
            ->get()->result_array();
    }

    public function reduce_deposit($id, $amount)
    {
        $this->db->set('REMAIN', "REMAIN - {$amount}", false)
            ->where('ID', $id)
            ->update('abc_mst_customer_deposit');
    }

    public function sales_has_payment($sales, $plant)
    {
        return $this->db
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO !=', 'AUTO_DP') // abaikan DP otomatis
            ->where('DELETED IS NULL', null, false)
            ->count_all_results('abc_mst_cash_in_detail') > 0;
    }

    public function delete_dp_by_sales($sales, $plant)
    {
        // Ambil semua header yang terpengaruh
        $headers = $this->db
            ->select('CASH_IN')
            ->from('abc_mst_cash_in_detail')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO', 'AUTO_DP')
            ->group_by('CASH_IN')
            ->get()
            ->result_array();

        if (!$headers) return;

        // Hapus semua AUTO DP detail sekaligus
        $this->db->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO', 'AUTO_DP')
            ->delete('abc_mst_cash_in_detail');

        foreach ($headers as $h) {
            $cashIn = $h['CASH_IN'];

            // Cek sisa detail
            $remain = $this->db
                ->where('CASH_IN', $cashIn)
                ->where('PLANT', $plant)
                ->count_all_results('abc_mst_cash_in_detail');

            if ($remain == 0) {
                $this->db->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->delete('abc_mst_cash_in');
            } else {
                $total = $this->db
                    ->select_sum('AMOUNT_OFFSET')
                    ->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->get('abc_mst_cash_in_detail')
                    ->row()->AMOUNT_OFFSET ?? 0;

                $this->db->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->update('abc_mst_cash_in', ['AMOUNT' => $total]);
            }
        }
    }

    // public function reduce_invoice_remain($sales, $plant, $amount)
    // {
    //     $this->db->set('REMAIN', "REMAIN - {$amount}", false);
    //     $this->db->where([
    //         'SALES' => $sales,
    //         'PLANT' => $plant
    //     ]);
    //     $this->db->update('abc_mst_sales');
    // }

    // public function restore_invoice_remain($sales, $plant, $amount)
    // {
    //     $this->db->set('REMAIN', "REMAIN + {$amount}", false);
    //     $this->db->where([
    //         'SALES' => $sales,
    //         'PLANT' => $plant
    //     ]);
    //     $this->db->update('abc_mst_sales');
    // }

    public function get_detail_by_cash_in($plant, $cashInNo)
    {
        return $this->db
            ->from('abc_mst_cash_in_detail')
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->get()
            ->result_array();
    }

    public function lock_invoice($sales, $plant)
    {
        return $this->db
            ->query("
                SELECT SALES
                FROM mst_sales
                WHERE SALES = ? AND PLANT = ?
                FOR UPDATE
            ", [$sales, $plant])
            ->row_array();
    }

    // public function update_invoice_status($sales, $plant)
    // {
    //     $row = $this->db
    //         ->select('AMOUNT, REMAIN')
    //         ->from('abc_mst_sales')
    //         ->where('SALES', $sales)
    //         ->where('PLANT', $plant)
    //         ->get()
    //         ->row_array();

    //     if (!$row) return false;

    //     if ($row['REMAIN'] <= 0) {
    //         $status = 'PAID';
    //     } elseif ($row['REMAIN'] < $row['AMOUNT']) {
    //         $status = 'PARTIAL';
    //     } else {
    //         $status = 'OPEN';
    //     }

    //     return $this->db
    //         ->where('SALES', $sales)
    //         ->where('PLANT', $plant)
    //         ->update('abc_mst_sales', [
    //             'STATUS'     => $status,
    //             'UPDATED_AT' => date('Y-m-d H:i:s')
    //         ]);
    // }

    public function update_header_amount($cashIn, $plant, $amount)
    {
        return $this->db->where([
            'CASH_IN' => $cashIn,
            'PLANT'   => $plant
        ])->update('abc_mst_cash_in', [
            'AMOUNT' => $amount
        ]);
    }

    public function update_header($plant, $cashInNo, $data)
    {
        $this->db
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->update('abc_mst_cash_in', $data);
    }

    /* ---------------------------------------------------------
       DETAIL OPERATIONS
    --------------------------------------------------------- */
    public function delete_header($cashIn,$plant){
        $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
                ->delete('abc_mst_cash_in');
    }

    public function delete_detail($plant, $cashInNo)
    {
        $this->db
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->delete('abc_mst_cash_in_detail');
    }

    public function insert_detail_batch($rows)
    {
        return $this->db->insert_batch('abc_mst_cash_in_detail', $rows);
    }

    /*
    |--------------------------------------------------------------------------
    | SAVING ALLOCATION
    |--------------------------------------------------------------------------
    */

    public function get_customer_saving_debt($customer, $plant)
    {
        $row = $this->db
            ->select(
                'COALESCE(SUM(AMOUNT), 0) AS total',
                false
            )
            ->from('abc_mst_saving')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->get()
            ->row();

        return max(0, round((float) ($row->total ?? 0), 2));
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

    public function insert_saving_payment(
        $customer,
        $plant,
        $amount,
        $cashInNo,
        $date,
        $user
    )
    {
        $amount = round((float) $amount, 2);

        if ($amount <= 0) {
            return false;
        }

        return $this->db->insert('abc_mst_saving', [
            'SV_NO'      => $this->generate_saving_no(),
            'PLANT'      => $plant,
            'CUSTOMER'   => $customer,
            'SV_DATE'    => $date,
            'RELATED'    => 'CASH_IN',
            'AMOUNT'     => -$amount,
            'REMARK'     => 'AUTO FROM CASH_IN ' . $cashInNo,
            'CREATED_AT' => date('Y-m-d H:i:s'),
            'CREATED_BY' => $user
        ]);
    }

    public function delete_saving_payment_by_cash_in($cashInNo, $plant, $deletedBy = null)
    {
        $data = [
            'DELETED' => date('Y-m-d H:i:s')
        ];

        if ($deletedBy) {
            $data['DELETED_BY'] = $deletedBy;
        }

        return $this->db
            ->where('PLANT', $plant)
            ->where('RELATED', 'CASH_IN')
            ->where('REMARK', 'AUTO FROM CASH_IN ' . $cashInNo)
            ->update('abc_mst_saving', $data);
    }

    /**
     * ============================================================
     * GET SALES + SAVING PER INVOICE
     * ============================================================
     *
     * Saving HARUS berdasarkan SALES.
     *
     * SALES AMOUNT DISPLAY:
     *     AMOUNT - DISCOUNT
     *
     * GRAND OUTSTANDING:
     *     SALES REMAIN + SAVING REMAIN
     *
     * Catatan:
     * SALES.REMAIN dianggap sebagai outstanding Sales setelah
     * discount / DP / pembayaran Sales sebelumnya.
     */
    public function get_sales_with_saving($customer, $plant)
    {
        $sql = "
            SELECT
                s.SALES,
                s.PLANT,
                s.CUSTOMER,
                s.CUSTOMER_NAME,
                s.SALES_DATE,
                s.SLIP_NO,
                s.JENIS_PAY,

                /* =================================================
                * SALES VALUE UNTUK DISPLAY
                * AMOUNT - DISCOUNT
                * ================================================= */
                (
                    COALESCE(s.AMOUNT, 0)
                    -
                    COALESCE(s.DISCOUNT, 0)
                ) AS SALES_AMOUNT,

                COALESCE(s.AMOUNT, 0) AS SALES_GROSS,
                COALESCE(s.DISCOUNT, 0) AS DISCOUNT,
                COALESCE(s.DP_AMOUNT, 0) AS DP_AMOUNT,

                /* =================================================
                * SALES OUTSTANDING
                * ================================================= */
                GREATEST(
                    COALESCE(s.REMAIN, 0),
                    0
                ) AS SALES_REMAIN,

                /* =================================================
                * SAVING
                * ================================================= */
                COALESCE(sv.SAVING_AMOUNT, 0) AS SAVING_AMOUNT,

                COALESCE(sv.SAVING_REMAIN, 0) AS SAVING_REMAIN,

                /* =================================================
                * GRAND OUTSTANDING
                * ================================================= */
                (
                    GREATEST(
                        COALESCE(s.REMAIN, 0),
                        0
                    )
                    +
                    COALESCE(sv.SAVING_REMAIN, 0)
                ) AS GRAND_OUTSTANDING,

                s.STATUS

            FROM abc_mst_sales s

            /* =====================================================
            * AGGREGATE SAVING DULU
            *
            * Jangan JOIN langsung ke abc_mst_saving karena satu
            * SALES bisa memiliki banyak Saving dan akan membuat
            * row Sales menjadi duplicate.
            * ===================================================== */
            LEFT JOIN (
                SELECT
                    SALES,
                    PLANT,

                    SUM(
                        CASE
                            WHEN DELETED IS NULL
                            THEN COALESCE(AMOUNT, 0)
                            ELSE 0
                        END
                    ) AS SAVING_AMOUNT,

                    SUM(
                        CASE
                            WHEN DELETED IS NULL
                            THEN GREATEST(COALESCE(REMAIN, 0), 0)
                            ELSE 0
                        END
                    ) AS SAVING_REMAIN

                FROM abc_mst_saving

                WHERE DELETED IS NULL
                AND SALES IS NOT NULL
                AND SALES <> ''

                GROUP BY SALES, PLANT

            ) sv
                ON sv.SALES = s.SALES
                AND sv.PLANT = s.PLANT

            WHERE s.CUSTOMER = ?
            AND s.PLANT = ?
            AND s.DELETED IS NULL

            /* Hanya invoice yang masih memiliki outstanding */
            AND (
                    COALESCE(s.REMAIN, 0)
                    +
                    COALESCE(sv.SAVING_REMAIN, 0)
                ) > 0

            ORDER BY
                s.SALES_DATE ASC,
                s.SALES ASC
        ";

        return $this->db
            ->query($sql, [
                $customer,
                $plant
            ])
            ->result_array();
    }

    /**
     * ============================================================
     * GET OPEN SAVING BY SALES
     * ============================================================
     */
    public function get_saving_by_sales($sales, $plant)
    {
        $sales = trim($sales);

        if ($sales === '' || $plant === '') {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Saving harus:
        | - Plant sama
        | - Belum dihapus
        | - Remark mengandung SALES
        |--------------------------------------------------------------------------
        */

        return $this->db
            ->select('
                s.SV_NO,
                s.PLANT,
                s.SV_DATE,
                s.CUSTOMER,
                s.AMOUNT,
                s.REMARK,
                s.STATUS,

                COALESCE(
                    (
                        SELECT SUM(cis.AMOUNT_OFFSET)
                        FROM abc_mst_cash_in_saving cis
                        WHERE cis.SV_NO = s.SV_NO
                        AND cis.PLANT = s.PLANT
                        AND cis.DELETED IS NULL
                    ),
                    0
                ) AS TOTAL_OFFSET

            ', false)

            ->from('abc_mst_saving s')

            ->where(
                's.PLANT',
                $plant
            )

            ->where(
                's.DELETED IS NULL',
                null,
                false
            )

            ->like(
                's.REMARK',
                $sales,
                'both'
            )

            ->order_by(
                's.SV_DATE',
                'ASC'
            )

            ->order_by(
                's.SV_NO',
                'ASC'
            )

            ->get()
            ->result_array();
    }

    /**
     * ============================================================
     * GET SAVING TOTAL BY SALES
     * ============================================================
     */
    public function get_saving_total_by_sales($sales, $plant)
    {
        $rows = $this->db
            ->select('
                SV_NO,
                PLANT,
                SV_DATE,
                CUSTOMER,
                RELATED,
                SALES,
                AMOUNT,
                REMAIN,
                REMARK,
                STATUS,
                CREATED_AT
            ')
            ->from('abc_mst_saving')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('RELATED', 'SALES')
            ->where('DELETED IS NULL', null, false)
            ->order_by('CREATED_AT', 'ASC')
            ->order_by('SV_NO', 'ASC')
            ->get()
            ->result_array();

        if (!is_array($rows)) {
            $rows = [];
        }

        $totalAmount = 0;
        $totalRemain = 0;

        foreach ($rows as $row) {

            $totalAmount += round(
                (float)($row['AMOUNT'] ?? 0),
                2
            );

            $totalRemain += round(
                (float)($row['REMAIN'] ?? 0),
                2
            );
        }

        return [
            'amount' => round($totalAmount, 2),
            'remain' => round($totalRemain, 2),
            'rows'   => $rows
        ];
    }

    /**
     * ============================================================
     * ALLOCATE SAVING
     * ============================================================
     *
     * Saving dialokasikan FIFO berdasarkan:
     *
     * SV_DATE
     * CREATED_AT
     * SV_NO
     *
     * Return:
     * [
     *     'allocated' => total saving yang dibayar,
     *     'remaining' => sisa Cash In setelah saving
     * ]
     */
    public function allocate_saving(
        $cashInNo,
        $plant,
        $sales,
        $amount,
        $date,
        $user
    ) {
        $amount =
            round(
                (float)$amount,
                2
            );

        if ($amount <= 0) {

            return [
                'allocated' => 0,
                'details'   => []
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil semua Saving milik SALES tersebut
        |--------------------------------------------------------------------------
        */

        $savingRows =
            $this->get_saving_by_sales(
                $sales,
                $plant
            );

        if (!is_array($savingRows)) {
            $savingRows = [];
        }

        $remaining =
            $amount;

        $allocatedTotal = 0;

        $details = [];

        foreach ($savingRows as $saving) {

            if ($remaining <= 0) {
                break;
            }

            $svNo =
                $saving['SV_NO'];

            $savingAmount =
                round(
                    (float)$saving['AMOUNT'],
                    2
                );

            /*
            |--------------------------------------------------------------------------
            | Hitung yang sudah pernah dipakai
            |--------------------------------------------------------------------------
            */

            $offsetRow =
                $this->db
                    ->select(
                        'COALESCE(SUM(AMOUNT_OFFSET),0) AS TOTAL_OFFSET',
                        false
                    )
                    ->from(
                        'abc_mst_cash_in_saving'
                    )
                    ->where(
                        'SV_NO',
                        $svNo
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
                    ->get()
                    ->row_array();

            $alreadyOffset =
                round(
                    (float)(
                        $offsetRow['TOTAL_OFFSET']
                        ?? 0
                    ),
                    2
                );

            /*
            |--------------------------------------------------------------------------
            | Remaining Saving
            |--------------------------------------------------------------------------
            */

            $savingRemain =
                round(
                    $savingAmount
                    -
                    $alreadyOffset,
                    2
                );

            if ($savingRemain <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Amount yang dibayar ke Saving ini
            |--------------------------------------------------------------------------
            */

            $offset =
                min(
                    $remaining,
                    $savingRemain
                );

            $offset =
                round(
                    $offset,
                    2
                );

            if ($offset <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT HISTORI
            |--------------------------------------------------------------------------
            */

            $inserted =
                $this->db->insert(
                    'abc_mst_cash_in_saving',
                    [

                        'CASH_IN' =>
                            $cashInNo,

                        'PLANT' =>
                            $plant,

                        'SALES' =>
                            $sales,

                        'SV_NO' =>
                            $svNo,

                        'AMOUNT_OFFSET' =>
                            $offset,

                        'DATE_OFFSET' =>
                            date(
                                'Y-m-d H:i:s',
                                strtotime($date)
                            ),

                        'CREATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'CREATED_BY' =>
                            $user

                    ]
                );

            if (!$inserted) {

                throw new Exception(
                    'Gagal menyimpan allocation Saving '
                    . $svNo
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HITUNG TOTAL
            |--------------------------------------------------------------------------
            */

            $allocatedTotal += $offset;

            $remaining -= $offset;

            $remaining =
                round(
                    $remaining,
                    2
                );

            $details[] = [

                'SV_NO' =>
                    $svNo,

                'AMOUNT' =>
                    $savingAmount,

                'PREVIOUS_OFFSET' =>
                    $alreadyOffset,

                'OFFSET' =>
                    $offset,

                'REMAIN_AFTER' =>
                    round(
                        $savingRemain - $offset,
                        2
                    )

            ];

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS SAVING
            |--------------------------------------------------------------------------
            */

            $remainAfter =
                round(
                    $savingRemain - $offset,
                    2
                );

            if ($remainAfter <= 0) {

                $remainAfter = 0;

                $savingStatus = 'PAID';

            } elseif (
                $remainAfter < $savingAmount
            ) {

                $savingStatus = 'PARTIAL';

            } else {

                $savingStatus = 'OPEN';
            }

            $updated = $this->db
                ->where('SV_NO', $svNo)
                ->where('PLANT', $plant)
                ->where(
                    'DELETED IS NULL',
                    null,
                    false
                )
                ->update(
                    'abc_mst_saving',
                    [
                        'REMAIN' =>
                            $remainAfter,

                        'STATUS' =>
                            $savingStatus,

                        'UPDATED_AT' =>
                            date('Y-m-d H:i:s'),

                        'UPDATED_BY' =>
                            $user
                    ]
                );

            if (!$updated) {
                throw new Exception(
                    'Gagal update Saving ' . $svNo
                );
            }
        }

        return [
            'allocated' =>
                round(
                    $allocatedTotal,
                    2
                ),

            'remaining' =>
                round(
                    max(
                        $remaining,
                        0
                    ),
                    2
                ),

            'details' =>
                $details
        ];
    }

    /**
     * ============================================================
     * ALLOCATE SALES
     * ============================================================
     */
    public function allocate_sales(
        $cashInNo,
        $plant,
        $sales,
        $amount,
        $date,
        $slipNo,
        $user,
        $seq
    ) {
        $amount =
            round(
                (float)$amount,
                2
            );

        if ($amount <= 0) {

            return [
                'allocated' => 0
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | LOCK SALES
        |--------------------------------------------------------------------------
        */

        $this->lock_sales_row(
            $sales,
            $plant
        );

        /*
        |--------------------------------------------------------------------------
        | GET CURRENT SALES
        |--------------------------------------------------------------------------
        */

        $row =
            $this->db
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
                ->get(
                    'abc_mst_sales'
                )
                ->row_array();

        if (!$row) {

            throw new Exception(
                'Sales tidak ditemukan: '
                . $sales
            );
        }

        $remain =
            round(
                (float)$row['REMAIN'],
                2
            );

        if ($remain <= 0) {

            return [
                'allocated' => 0
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | MAX PAYMENT
        |--------------------------------------------------------------------------
        */

        $offset =
            min(
                $amount,
                $remain
            );

        $offset =
            round(
                $offset,
                2
            );

        if ($offset <= 0) {

            return [
                'allocated' => 0
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | REMAIN AFTER
        |--------------------------------------------------------------------------
        */

        $remainAfter =
            round(
                $remain - $offset,
                2
            );

        /*
        |--------------------------------------------------------------------------
        | STATUS SALES
        |--------------------------------------------------------------------------
        */

        if ($remainAfter <= 0) {

            $status = 'PAID';

        } else {

            $status = 'PARTIAL';
        }

        /*
        |--------------------------------------------------------------------------
        | INSERT CASH IN DETAIL
        |--------------------------------------------------------------------------
        */

        $inserted =
            $this->db->insert(
                'abc_mst_cash_in_detail',
                [

                    'CASH_IN' =>
                        $cashInNo,

                    'PLANT' =>
                        $plant,

                    'SALES' =>
                        $sales,

                    'SEQ_NO' =>
                        $seq,

                    'AMOUNT_INVOICE' =>
                        $remain,

                    'AMOUNT_OFFSET' =>
                        $offset,

                    'DATE_OFFSET' =>
                        date(
                            'Y-m-d H:i:s',
                            strtotime($date)
                        ),

                    'ORG_SLIP_NO' =>
                        $slipNo,

                    'SLIP_NO' =>
                        $slipNo,

                    'CREATED_AT' =>
                        date(
                            'Y-m-d H:i:s'
                        ),

                    'CREATED_BY' =>
                        $user

                ]
            );

        if (!$inserted) {

            throw new Exception(
                'Gagal menyimpan Cash In Detail untuk Sales '
                . $sales
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE SALES REMAIN
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->db
                ->where(
                    'SALES',
                    $sales
                )
                ->where(
                    'PLANT',
                    $plant
                )
                ->update(
                    'abc_mst_sales',
                    [

                        'REMAIN' =>
                            $remainAfter,

                        'STATUS' =>
                            $status,

                        'UPDATED_AT' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'UPDATED_BY' =>
                            $user

                    ]
                );

        if (!$updated) {

            throw new Exception(
                'Gagal update REMAIN Sales '
                . $sales
            );
        }

        return [

            'allocated' =>
                $offset,

            'remain_before' =>
                $remain,

            'remain_after' =>
                $remainAfter,

            'status' =>
                $status

        ];
    }

    /**
     * ============================================================
     * RESTORE SAVING FROM CASH IN
     * ============================================================
     */
    public function restore_cash_in_saving(
        $cashInNo,
        $plant,
        $deletedBy
    )
    {
        $rows = $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->get('abc_mst_cash_in_saving')
            ->result_array();

        foreach ($rows as $row) {

            /*
            ========================================================
            RESTORE SAVING REMAIN
            ========================================================
            */
            $saving = $this->db
                ->where('SV_NO', $row['SV_NO'])
                ->where('PLANT', $plant)
                ->where('DELETED IS NULL', null, false)
                ->get('abc_mst_saving')
                ->row_array();

            if (!$saving) {
                continue;
            }

            $newRemain = round(
                (float)$saving['REMAIN']
                +
                (float)$row['AMOUNT_OFFSET'],
                2
            );

            $amount = round(
                (float)$saving['AMOUNT'],
                2
            );

            if ($newRemain >= $amount) {

                $newRemain = $amount;
                $status = 'OPEN';

            } elseif ($newRemain > 0) {

                $status = 'PARTIAL';

            } else {

                $status = 'PAID';
            }

            $this->db
                ->where('SV_NO', $row['SV_NO'])
                ->where('PLANT', $plant)
                ->update(
                    'abc_mst_saving',
                    [
                        'REMAIN'     => $newRemain,
                        'STATUS'     => $status,
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $deletedBy
                    ]
                );
        }

        /*
        ============================================================
        SOFT DELETE ALLOCATION
        ============================================================
        */
        $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->update(
                'abc_mst_cash_in_saving',
                [
                    'DELETED'    => date('Y-m-d H:i:s'),
                    'DELETED_BY' => $deletedBy
                ]
            );

        return true;
    }

    /**
     * ============================================================
     * RESTORE SALES FROM CASH IN DETAIL
     * ============================================================
     */
    public function restore_cash_in_sales(
        $cashInNo,
        $plant,
        $deletedBy
    )
    {
        $details = $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->get('abc_mst_cash_in_detail')
            ->result_array();

        foreach ($details as $detail) {

            $sales = $this->db
                ->where('SALES', $detail['SALES'])
                ->where('PLANT', $plant)
                ->where('DELETED IS NULL', null, false)
                ->get('abc_mst_sales')
                ->row_array();

            if (!$sales) {
                continue;
            }

            $newRemain = round(
                (float)$sales['REMAIN']
                +
                (float)$detail['AMOUNT_OFFSET'],
                2
            );

            /*
            ========================================================
            BATAS MAKSIMAL
            ========================================================
            *
            * Jangan sampai REMAIN > nilai invoice net.
            */
            $maxRemain = round(
                (float)$sales['AMOUNT']
                -
                (float)$sales['DISCOUNT']
                -
                (float)$sales['DP_AMOUNT'],
                2
            );

            if ($maxRemain < 0) {
                $maxRemain = 0;
            }

            if ($newRemain > $maxRemain) {
                $newRemain = $maxRemain;
            }

            if ($newRemain <= 0) {

                $status = 'PAID';

            } elseif ($newRemain < $maxRemain) {

                $status = 'PARTIAL';

            } else {

                $status = 'OPEN';
            }

            $this->db
                ->where('SALES', $detail['SALES'])
                ->where('PLANT', $plant)
                ->update(
                    'abc_mst_sales',
                    [
                        'REMAIN'     => $newRemain,
                        'STATUS'     => $status,
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $deletedBy
                    ]
                );
        }

        /*
        ============================================================
        SOFT DELETE CASH IN DETAIL
        ============================================================
        */
        $this->db
            ->where('CASH_IN', $cashInNo)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->update(
                'abc_mst_cash_in_detail',
                [
                    'DELETED'    => date('Y-m-d H:i:s'),
                    'UPDATED_AT' => date('Y-m-d H:i:s'),
                    'UPDATED_BY' => $deletedBy
                ]
            );

        return true;
    }

    public function lock_sales_row($sales, $plant)
    {
        return $this->db->query(
            "SELECT SALES
            FROM abc_mst_sales
            WHERE SALES = ?
            AND PLANT = ?
            AND DELETED IS NULL
            FOR UPDATE",
            [
                $sales,
                $plant
            ]
        );
    }
}
