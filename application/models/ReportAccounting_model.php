<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportAccounting_model extends CI_Model
{
    /*
    |--------------------------------------------------------------------------
    | PAYMENT QUERY
    |--------------------------------------------------------------------------
    */

    private function payment_query(
        $search = '',
        $plant = '',
        $supplier = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select('

            p.PAYMENT,

            p.PAYMENT_DATE,

            p.PLANT,

            p.SUPPLIER,

            supplier.FULL_NAME AS SUPPLIER_NAME,

            d.PO_NO,

            d.MATERIAL,

            material.MATERIAL_NAME,

            d.JUMLAH,

            d.BERAT,

            d.HARGA,

            d.TOTAL

        ');

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_payment_detail d'
        );

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_payment p',
            '
                p.PAYMENT = d.PAYMENT
                AND p.PLANT = d.PLANT
            ',
            'inner'
        );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            '
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_material material',
            '
                material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'p.DELETED IS NULL',
            null,
            false
        );

        $this->db->where(
            'd.DELETED IS NULL',
            null,
            false
        );
        
        


        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($plant)){

            $this->db->where(
                'p.PLANT',
                $plant
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        if(!empty($supplier)){

            $this->db->where(
                'p.SUPPLIER',
                $supplier
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'DATE(p.PAYMENT_DATE) >=',
                $dateFrom
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'DATE(p.PAYMENT_DATE) <=',
                $dateTo
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
                'p.PAYMENT',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'd.PO_NO',
                $search
            );

            $this->db->group_end();

        }
    }

    private function sl_build_filter(
        $alias,
        $filter,
        $dateField
    ) {
        $sql = '';

        if (!empty($filter['search'])) {
            $search = $this->db->escape_like_str(
                $filter['search']
            );

            $key = $alias === 'sv' ? 'SV_NO' : 'LOAN_NO';

            $sql .= " AND (
                {$alias}.{$key} LIKE '%{$search}%'
                OR customer.FULL_NAME LIKE '%{$search}%'
                OR {$alias}.RELATED LIKE '%{$search}%'
                OR {$alias}.REMARK LIKE '%{$search}%'
            )";
        }

        if (!empty($filter['plant'])) {
            $sql .= ' AND ' . $alias . '.PLANT = ' .
                $this->db->escape($filter['plant']);
        }

        if (!empty($filter['customer'])) {
            $sql .= ' AND ' . $alias . '.CUSTOMER = ' .
                $this->db->escape($filter['customer']);
        }

        if (!empty($filter['date_from'])) {
            $sql .= ' AND DATE(' . $alias . '.' .
                $dateField . ') >= ' .
                $this->db->escape($filter['date_from']);
        }

        if (!empty($filter['date_to'])) {
            $sql .= ' AND DATE(' . $alias . '.' .
                $dateField . ') <= ' .
                $this->db->escape($filter['date_to']);
        }

        return $sql;
    }

    public function get_sl_report(
        $limit,
        $start,
        $filter = []
    ) {
        $savingFilter = $this->sl_build_filter(
            'sv',
            $filter,
            'SV_DATE'
        );

        $loanFilter = $this->sl_build_filter(
            'loan',
            $filter,
            'LOAN_DATE'
        );

        $savingSql = "
            SELECT
                sv.SV_NO AS DOC_NO,
                'Saving' AS TYPE,
                sv.PLANT,
                plant.CODE_NAME AS PLANT_NAME,
                sv.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,
                sv.RELATED,
                sv.SV_DATE AS DOC_DATE,
                sv.AMOUNT AS AMOUNT,
                sv.REMARK AS REMARK
            FROM abc_mst_saving sv
            LEFT JOIN abc_cd_code plant
                ON plant.CODE = sv.PLANT
                AND plant.HEAD_CODE = 'PLANT'
            LEFT JOIN abc_cd_customer customer
                ON customer.CUST = sv.CUSTOMER
            WHERE sv.DELETED IS NULL
            " . $savingFilter;

        $loanSql = "
            SELECT
                loan.LOAN_NO AS DOC_NO,
                'Loan' AS TYPE,
                loan.PLANT,
                plant.CODE_NAME AS PLANT_NAME,
                loan.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,
                loan.RELATED,
                loan.LOAN_DATE AS DOC_DATE,
                loan.AMOUNT AS AMOUNT,
                loan.REMARK AS REMARK
            FROM abc_mst_loan loan
            LEFT JOIN abc_cd_code plant
                ON plant.CODE = loan.PLANT
                AND plant.HEAD_CODE = 'PLANT'
            LEFT JOIN abc_cd_customer customer
                ON customer.CUST = loan.CUSTOMER
            WHERE loan.DELETED IS NULL
            " . $loanFilter;

        $unionSql = "(
            {$savingSql}
        )
        UNION ALL
        (
            {$loanSql}
        )
        ORDER BY DOC_DATE DESC, TYPE ASC
        LIMIT {$start}, {$limit} ";

        $rows = $this->db
            ->query($unionSql)
            ->result_array();

        $countSql = "
            SELECT COUNT(*) AS cnt FROM (
                {$savingSql}
                UNION ALL
                {$loanSql}
            ) AS t
        ";

        $countResult = $this->db
            ->query($countSql)
            ->row_array();

        $summarySql = "
            SELECT
                COALESCE(SUM(AMOUNT), 0) AS TOTAL_AMOUNT,
                COALESCE(SUM(CASE WHEN TYPE = 'Saving' THEN AMOUNT ELSE 0 END), 0) AS TOTAL_SAVING_AMOUNT,
                COALESCE(SUM(CASE WHEN TYPE = 'Loan' THEN AMOUNT ELSE 0 END), 0) AS TOTAL_LOAN_AMOUNT,
                COUNT(*) AS TOTAL_DOC,
                SUM(CASE WHEN TYPE = 'Saving' THEN 1 ELSE 0 END) AS TOTAL_SAVING_DOC,
                SUM(CASE WHEN TYPE = 'Loan' THEN 1 ELSE 0 END) AS TOTAL_LOAN_DOC
            FROM (
                {$savingSql}
                UNION ALL
                {$loanSql}
            ) AS t
        ";

        $summary = $this->db
            ->query($summarySql)
            ->row_array();

        return [
            'rows' => $rows,
            'total_count' => (int) ($countResult['cnt'] ?? 0),
            'summary' => $summary
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | REPORT SAVING
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | SAVING CUSTOMER QUERY
    |--------------------------------------------------------------------------
    */

    private function saving_customer_query(
        $filter = []
    )
    {
        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_saving sv'
        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $this->db->join(

            'abc_cd_customer customer',

            '

                customer.CUST COLLATE utf8mb4_unicode_ci =

                sv.CUSTOMER COLLATE utf8mb4_unicode_ci

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

            'abc_cd_code plant',

            '

                plant.CODE = sv.PLANT

                AND plant.HEAD_CODE = "PLANT"

            ',

            'left',

            false

        );


        /*
        |--------------------------------------------------------------------------
        | BASE FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(

            'sv.DELETED IS NULL',

            null,

            false

        );


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['search']
            )
        ){

            $search =
                $filter['search'];


            $this->db->group_start();


            $this->db->like(
                'sv.SV_NO',
                $search
            );


            $this->db->or_like(
                'sv.CUSTOMER',
                $search
            );


            $this->db->or_like(
                'customer.FULL_NAME',
                $search
            );


            $this->db->or_like(
                'sv.RELATED',
                $search
            );


            $this->db->or_like(
                'sv.SALES',
                $search
            );


            $this->db->or_like(
                'sv.REMARK',
                $search
            );


            $this->db->group_end();

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['customer']
            )
        ){

            $this->db->where(
                'sv.CUSTOMER',
                $filter['customer']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if (!empty($filter['status'])) {

            if ($filter['status'] === 'PAID') {

                $this->db->where(
                    'sv.REMAIN <=',
                    0
                );

            } elseif ($filter['status'] === 'OUTSTANDING') {

                $this->db->where(
                    'sv.REMAIN >',
                    0
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['date_from']
            )
        ){

            $this->db->where(

                'DATE(sv.SV_DATE) >=',

                $filter['date_from']

            );

        }


        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['date_to']
            )
        ){

            $this->db->where(

                'DATE(sv.SV_DATE) <=',

                $filter['date_to']

            );

        }

    }

    /*
    |--------------------------------------------------------------------------
    | GET SAVING REPORT
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | GET SAVING REPORT
    |--------------------------------------------------------------------------
    */

    public function get_saving_report(
        $limit,
        $start,
        $filter = []
    )
    {

        /*
        |--------------------------------------------------------------------------
        | STEP 1
        | GET CUSTOMER LIST
        |--------------------------------------------------------------------------
        */

        $this->saving_customer_query(
            $filter
        );


        $this->db->select('

            sv.CUSTOMER,

            customer.FULL_NAME AS CUSTOMER_NAME,

            GROUP_CONCAT(
                DISTINCT sv.PLANT
                ORDER BY sv.PLANT
                SEPARATOR ", "
            ) AS PLANT_LIST,

            GROUP_CONCAT(
                DISTINCT plant.CODE_NAME
                ORDER BY plant.CODE_NAME
                SEPARATOR ", "
            ) AS PLANT_NAME_LIST

        ', false);


        /*
        |--------------------------------------------------------------------------
        | GROUP CUSTOMER
        |--------------------------------------------------------------------------
        */

        $this->db->group_by(
            'sv.CUSTOMER'
        );


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'customer.FULL_NAME',
            'ASC'
        );


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $this->db->limit(
            $limit,
            $start
        );


        $customerRows =
            $this->db
                ->get()
                ->result_array();


        /*
        |--------------------------------------------------------------------------
        | NO CUSTOMER
        |--------------------------------------------------------------------------
        */

        if(
            empty(
                $customerRows
            )
        ){

            return [

                'rows' => [],

                'total_count' =>
                    $this->count_saving_customers(
                        $filter
                    )

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LIST
        |--------------------------------------------------------------------------
        */

        $customers = [];


        foreach(
            $customerRows as $row
        ){

            $customers[] =
                $row['CUSTOMER'];

        }


        /*
        |--------------------------------------------------------------------------
        | STEP 2
        | GET ALL SAVING HISTORY
        |--------------------------------------------------------------------------
        */

        $this->db->select('

            sv.SV_NO,

            sv.PLANT,

            plant.CODE_NAME AS PLANT_NAME,

            sv.SV_DATE,

            sv.CUSTOMER,

            customer.FULL_NAME AS CUSTOMER_NAME,

            sv.RELATED,

            sv.RECEIVE,

            sv.SALES,

            sv.AMOUNT,

            sv.BASIS,

            sv.RATE,

            sv.TOTAL_QTY,

            sv.TOTAL_WEIGHT,

            sv.REMAIN,

            sv.REMARK,

            sv.STATUS

        ', false);


        $this->db->from(
            'abc_mst_saving sv'
        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $this->db->join(

            'abc_cd_customer customer',

            '

                customer.CUST COLLATE utf8mb4_unicode_ci =

                sv.CUSTOMER COLLATE utf8mb4_unicode_ci

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

            'abc_cd_code plant',

            '

                plant.CODE = sv.PLANT

                AND plant.HEAD_CODE = "PLANT"

            ',

            'left',

            false

        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER LIST
        |--------------------------------------------------------------------------
        */

        $this->db->where_in(
            'sv.CUSTOMER',
            $customers
        );


        /*
        |--------------------------------------------------------------------------
        | DELETED
        |--------------------------------------------------------------------------
        */

        $this->db->where(

            'sv.DELETED IS NULL',

            null,

            false

        );


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |
        | History harus tetap mengikuti filter yang sama
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['search']
            )
        ){

            $search =
                $filter['search'];


            $this->db->group_start();


            $this->db->like(
                'sv.SV_NO',
                $search
            );


            $this->db->or_like(
                'sv.CUSTOMER',
                $search
            );


            $this->db->or_like(
                'customer.FULL_NAME',
                $search
            );


            $this->db->or_like(
                'sv.RELATED',
                $search
            );


            $this->db->or_like(
                'sv.SALES',
                $search
            );


            $this->db->or_like(
                'sv.REMARK',
                $search
            );


            $this->db->group_end();

        }


        if (!empty($filter['status'])) {

            if ($filter['status'] === 'PAID') {

                $this->db->where(
                    'sv.REMAIN <=',
                    0
                );

            } elseif ($filter['status'] === 'OUTSTANDING') {

                $this->db->where(
                    'sv.REMAIN >',
                    0
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER FILTER
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['customer']
            )
        ){

            $this->db->where(
                'sv.CUSTOMER',
                $filter['customer']
            );

        }


        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if(
            !empty(
                $filter['date_from']
            )
        ){

            $this->db->where(

                'DATE(sv.SV_DATE) >=',

                $filter['date_from']

            );

        }


        if(
            !empty(
                $filter['date_to']
            )
        ){

            $this->db->where(

                'DATE(sv.SV_DATE) <=',

                $filter['date_to']

            );

        }


        /*
        |--------------------------------------------------------------------------
        | ORDER HISTORY
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'sv.CUSTOMER',
            'ASC'
        );


        $this->db->order_by(
            'sv.SV_DATE',
            'ASC'
        );


        $this->db->order_by(
            'sv.SV_NO',
            'ASC'
        );


        $historyRows =
            $this->db
                ->get()
                ->result_array();


        /*
        |--------------------------------------------------------------------------
        | STEP 2.5
        | GET PAYMENT HISTORY
        |--------------------------------------------------------------------------
        |
        | Ambil semua SV_NO dari history yang sedang ditampilkan.
        |
        */

        $savingNos = [];


        foreach(
            $historyRows as $history
        ){

            if(
                !empty(
                    $history['SV_NO']
                )
            ){

                $savingNos[] =
                    $history['SV_NO'];

            }

        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE DUPLICATE SV_NO
        |--------------------------------------------------------------------------
        */

        $savingNos =
            array_values(
                array_unique(
                    $savingNos
                )
            );


        /*
        |--------------------------------------------------------------------------
        | PAYMENT MAP
        |--------------------------------------------------------------------------
        |
        | Format:
        |
        | [
        |     'SV001' => [
        |         payment 1,
        |         payment 2
        |     ],
        |
        |     'SV002' => [
        |         payment 1
        |     ]
        | ]
        |
        */

        $paymentMap =
            $this->get_saving_payment_history(
                $savingNos
            );


        /*
        |--------------------------------------------------------------------------
        | STEP 3
        | GROUP HISTORY BY CUSTOMER
        |--------------------------------------------------------------------------
        */

        $historyByCustomer = [];


        foreach(
            $historyRows as $history
        ){

            $customer =
                $history['CUSTOMER'];


            /*
            |--------------------------------------------------------------------------
            | PAYMENT HISTORY
            |--------------------------------------------------------------------------
            |
            | Tambahkan payment history ke masing-masing Saving.
            |
            */

            $history['PAYMENTS'] =
                $paymentMap[
                    $history['SV_NO']
                ]
                ?? [];


            /*
            |--------------------------------------------------------------------------
            | GROUP CUSTOMER
            |--------------------------------------------------------------------------
            */

            if(
                !isset(
                    $historyByCustomer[
                        $customer
                    ]
                )
            ){

                $historyByCustomer[
                    $customer
                ] = [];

            }


            $historyByCustomer[
                $customer
            ][] = $history;

        }


        /*
        |--------------------------------------------------------------------------
        | STEP 4
        | BUILD FINAL RESPONSE
        |--------------------------------------------------------------------------
        */

        $rows = [];


        foreach(
            $customerRows as $customer
        ){

            $customerCode =
                $customer['CUSTOMER'];


            $details =
                $historyByCustomer[
                    $customerCode
                ]
                ?? [];


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER GROUP
            |--------------------------------------------------------------------------
            */

            $rows[] = [

                'CUSTOMER' =>
                    $customerCode,

                'CUSTOMER_NAME' =>
                    $customer['CUSTOMER_NAME']
                    ?: '-',

                'PLANT' =>
                    $customer['PLANT_LIST']
                    ?: '-',

                'PLANT_NAME' =>
                    $customer['PLANT_NAME_LIST']
                    ?: '-',

                'DETAILS' =>
                    $details

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL CUSTOMER
        |--------------------------------------------------------------------------
        */

        $total =
            $this->count_saving_customers(
                $filter
            );


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'rows' =>
                $rows,

            'total_count' =>
                $total

        ];

    }

    /**
     * Get payment / offset history for saving
     *
     * Relationship:
     * abc_mst_cash_in_saving
     *      -> CASH_IN + PLANT
     * abc_mst_cash_in
     */
    public function get_saving_payment_history($savingNos = [])
    {
        if (empty($savingNos)) {
            return [];
        }

        $this->db->select("
            cis.SV_NO,
            cis.CASH_IN,
            cis.PLANT,
            cis.SALES,
            cis.AMOUNT_OFFSET,
            cis.DATE_OFFSET,

            ci.CASHIN_DATE,
            ci.CUSTOMER,
            ci.AMOUNT AS CASH_IN_AMOUNT,
            ci.PEMBAYARAN,
            ci.NO_REK,
            ci.STATUS AS CASH_IN_STATUS,
            ci.REMARK AS CASH_IN_REMARK
        ");

        $this->db->from('abc_mst_cash_in_saving cis');

        $this->db->join(
            'abc_mst_cash_in ci',
            'ci.CASH_IN = cis.CASH_IN
            AND ci.PLANT = cis.PLANT
            AND ci.DELETED IS NULL',
            'left'
        );

        $this->db->where_in('cis.SV_NO', $savingNos);

        // hanya data yang belum dihapus
        $this->db->where('cis.DELETED IS NULL', null, false);

        $this->db->order_by('cis.SV_NO', 'ASC');
        $this->db->order_by('cis.DATE_OFFSET', 'ASC');
        $this->db->order_by('cis.CASH_IN', 'ASC');

        $query = $this->db->get();

        $result = [];

        foreach ($query->result() as $row) {

            $svNo = $row->SV_NO;

            if (!isset($result[$svNo])) {
                $result[$svNo] = [];
            }

            $result[$svNo][] = [
                'CASH_IN' => $row->CASH_IN,
                'PLANT' => $row->PLANT,
                'SALES' => $row->SALES,

                'DATE_OFFSET' => $row->DATE_OFFSET,
                'CASHIN_DATE' => $row->CASHIN_DATE,

                'CASH_IN_AMOUNT' => (float) ($row->CASH_IN_AMOUNT ?? 0),
                'AMOUNT_OFFSET' => (float) ($row->AMOUNT_OFFSET ?? 0),

                'PEMBAYARAN' => $row->PEMBAYARAN,
                'NO_REK' => $row->NO_REK,

                'CASH_IN_STATUS' => $row->CASH_IN_STATUS,
                'CASH_IN_REMARK' => $row->CASH_IN_REMARK
            ];
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT SAVING CUSTOMER
    |--------------------------------------------------------------------------
    */

    public function count_saving_customers(
        $filter = []
    )
    {

        $this->saving_customer_query(
            $filter
        );


        $this->db->select(
            'COUNT(DISTINCT sv.CUSTOMER) AS TOTAL_CUSTOMER',
            false
        );


        $result =
            $this->db
                ->get()
                ->row_array();


        return (int) (
            $result['TOTAL_CUSTOMER']
            ?? 0
        );

    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function get_payment_header_report(
        $limit,
        $start,
        $search = '',
        $plant = '',
        $supplier = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            p.PAYMENT,

            p.PLANT,

            plant.CODE_NAME AS PLANT_NAME,

            p.PAYMENT_DATE,

            p.SUPPLIER,

            supplier.FULL_NAME AS SUPPLIER_NAME,

            p.PEMBAYARAN,

            p.SLIP_NO,

            p.REMARK,
            
            d.PO_NO,
            d.JUMLAH,

            COUNT(d.ID) AS TOTAL_ITEM,

            COALESCE(
                SUM(d.TOTAL),
                0
            ) AS GRAND_TOTAL

        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_payment p'
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_payment_detail d',
            '
                d.PAYMENT = p.PAYMENT
                AND d.PLANT = p.PLANT
                AND d.DELETED IS NULL
            ',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            '
                plant.CODE = p.PLANT
                AND plant.HEAD_CODE = "PLANT"
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            '
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'p.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($search)){

            $this->db->group_start();

            $this->db->like(
                'p.PAYMENT',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'p.SLIP_NO',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($plant)){

            $this->db->where(
                'p.PLANT',
                $plant
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        if(!empty($supplier)){

            $this->db->where(
                'p.SUPPLIER',
                $supplier
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'DATE(p.PAYMENT_DATE) >=',
                $dateFrom
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'DATE(p.PAYMENT_DATE) <=',
                $dateTo
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by(
            'p.PAYMENT'
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'p.PAYMENT_DATE',
            'DESC'
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

    public function get_payment_detail_report(
        $payment,
        $plant
    )
    {
        $this->db->select('

            d.*,

            material.material_name
                AS MATERIAL_NAME

        ');

        $this->db->from(
            'abc_mst_payment_detail d'
        );

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_material material',
            '
                material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'd.PAYMENT',
            $payment
        );

        $this->db->where(
            'd.PLANT',
            $plant
        );

        $this->db->where(
            'd.DELETED IS NULL',
            null,
            false
        );

        return $this->db
            ->get()
            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT
    |--------------------------------------------------------------------------
    */

    public function count_payment_report(
        $search = '',
        $plant = '',
        $supplier = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $this->payment_query(
            $search,
            $plant,
            $supplier,
            $dateFrom,
            $dateTo
        );

        return $this->db
            ->count_all_results();
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    public function summary_payment_report(
        $search = '',
        $plant = '',
        $supplier = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $this->payment_query(
            $search,
            $plant,
            $supplier,
            $dateFrom,
            $dateTo
        );

        $rows = $this->db
            ->get()
            ->result_array();

        $totalPayment = 0;

        $supplierList = [];

        $poList = [];

        foreach($rows as $r){

            $totalPayment +=
                (float) $r['TOTAL'];

            $supplierList[] =
                $r['SUPPLIER'];

            $poList[] =
                $r['PO_NO'];

        }

        return [

            'total_payment' =>
                $totalPayment,

            'total_supplier' =>
                count(
                    array_unique(
                        $supplierList
                    )
                ),

            'total_po' =>
                count(
                    array_unique(
                        $poList
                    )
                )

        ];
    }

    public function get_report_cashin(
        $limit,
        $start,
        $filter = []
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            c.CASH_IN,

            c.PLANT,

            plant.CODE_NAME AS PLANT_NAME,

            c.CASHIN_DATE,

            c.CUSTOMER,

            customer.FULL_NAME AS CUSTOMER_NAME,

            c.PEMBAYARAN,

            c.SLIP_NO,

            c.BON,

            c.REMARK,

            c.AMOUNT,

            COUNT(DISTINCT d.SALES)
                AS TOTAL_INVOICE,

            COALESCE(
                SUM(d.AMOUNT_OFFSET),
                0
            ) AS TOTAL_ALLOCATED,

            (
                c.AMOUNT
                -
                COALESCE(
                    SUM(d.AMOUNT_OFFSET),
                    0
                )
            ) AS TOTAL_DEPOSIT

        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_cash_in c'
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
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(

            'abc_cd_code plant',

            "

                plant.CODE = c.PLANT

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
        | BASE FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['search'])){

            $search =
                $filter['search'];

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
                'd.SALES',
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
        | PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['customer'])){

            $this->db->where(
                'c.CUSTOMER',
                $filter['customer']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) >=',
                $filter['date_from']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) <=',
                $filter['date_to']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by(
            'c.CASH_IN'
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'c.CASHIN_DATE',
            'DESC'
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

    public function get_report_cashin_detail(
        $cashIn,
        $plant
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            d.SEQ_NO,

            d.SALES,

            d.AMOUNT_INVOICE,

            d.AMOUNT_OFFSET,

            (
                d.AMOUNT_INVOICE
                -
                d.AMOUNT_OFFSET
            ) AS REMAINING,

            sales.STATUS AS SALES_STATUS

        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_cash_in_detail d'
        );

        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        $this->db->join(

            'abc_mst_sales sales',

            "

                sales.SALES = d.SALES

                AND sales.PLANT = d.PLANT

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
            'd.CASH_IN',
            $cashIn
        );

        $this->db->where(
            'd.PLANT',
            $plant
        );

        $this->db->where(
            'd.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'd.SEQ_NO',
            'ASC'
        );

        return $this->db
            ->get()
            ->result_array();
    }

    public function count_report_cashin(
        $filter = []
    )
    {
        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_cash_in c'
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
        | BASE FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['search'])){

            $search =
                $filter['search'];

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
                'd.SALES',
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
        | PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['customer'])){

            $this->db->where(
                'c.CUSTOMER',
                $filter['customer']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) >=',
                $filter['date_from']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) <=',
                $filter['date_to']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by(
            'c.CASH_IN'
        );

        /*
        |--------------------------------------------------------------------------
        | RESULT
        |--------------------------------------------------------------------------
        */

        return $this->db
            ->get()
            ->num_rows();
    }

    public function get_report_cashin_summary(
        $filter = []
    )
    {
        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            COALESCE(
                SUM(c.AMOUNT),
                0
            ) AS TOTAL_CASHIN,

            COUNT(
                DISTINCT c.CUSTOMER
            ) AS TOTAL_CUSTOMER,

            COUNT(
                DISTINCT d.SALES
            ) AS TOTAL_INVOICE,

            COALESCE(
                SUM(
                    c.AMOUNT
                    -
                    COALESCE(
                        d.AMOUNT_OFFSET,
                        0
                    )
                ),
                0
            ) AS TOTAL_DEPOSIT

        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from(
            'abc_mst_cash_in c'
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
        | BASE FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['search'])){

            $search =
                $filter['search'];

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
                'd.SALES',
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
        | PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['customer'])){

            $this->db->where(
                'c.CUSTOMER',
                $filter['customer']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) >=',
                $filter['date_from']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.CASHIN_DATE) <=',
                $filter['date_to']
            );

        }

        return $this->db
            ->get()
            ->row_array();
    }
    
    private function cost_query(
        $filter = []
    )
    {
        $this->db->from('abc_mst_cost c');

        $this->db->join(
            'abc_cd_code plant',
            '
                plant.CODE = c.PLANT
                AND plant.HEAD_CODE = "PLANT"
            '
        );

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        if(!empty($filter['search'])){

            $this->db->group_start()
                ->db->like(
                    'c.COST',
                    $filter['search']
                )
                ->or_like(
                    'c.SLIP_NO',
                    $filter['search']
                )
                ->group_end();
        }

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );
        }

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );
        }

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.COST_DATE) >=',
                $filter['date_from']
            );
        }

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.COST_DATE) <=',
                $filter['date_to']
            );
        }
    }

    private function cost_limit($filter, $limit, $start) {
        $this->cost_query($filter);
        return $this->db->limit($limit, $start)->get()->result_array();
    }

    private function cost_count($filter) {
        $this->cost_query($filter);
        return $this->db->count_all_results();
    }

    public function get_cost_report(
        $limit,
        $start,
        $filter = []
    )
    {

        $header_data = $this->cost_limit($filter, $limit, $start);


        $costList = [];
        $costFilter = array_map(function($row) use (&$costList){
            $row['DETAIL'] = [];
            $costList[] = $row;
            return $row['COST'];
        }, $header_data);

        if (empty($costFilter)) {
            return [];
        }

        
        // build index for quick lookup of header rows by COST value
        $costIndex = [];
        foreach ($costList as $i => $hdr) {
            if (isset($hdr['COST'])) {
                $costIndex[$hdr['COST']] = $i;
            }
        }

        // get detail data grouped by TIPE_COST and joined to cost master for name
        $detail_data = $this->db
            ->select('d.COST, d.TIPE_COST, cst.COST_NAME, SUM(d.QTY) AS TOTAL_QTY, SUM(d.JUMLAH) AS TOTAL_JUMLAH, SUM(d.TOTAL) AS TOTAL')
            ->from('abc_mst_cost_detail d')
            ->join('abc_cd_cost cst', 'd.TIPE_COST = cst.COST', 'left')
            ->where_in('d.COST', $costFilter)
            ->group_by(['d.COST','d.TIPE_COST'])
            ->order_by('d.TIPE_COST','ASC')
            ->get()
            ->result_array();

        // attach grouped detail rows back to their header

        $totalCost = 0;
        $totalItem = 0;
        $totalCostDoc = count($costList);
        foreach ($detail_data as $row) {
            if (isset($costIndex[$row['COST']])) {
                $costList[$costIndex[$row['COST']]]['DETAIL'][] = $row;
                $totalItem += 1;
                $totalCost += (float) $row['TOTAL'];
            }
        }

        $return = [
            'cost_list' => $costList,
            'total_count' => $this->cost_count($filter),
            'summary' => [
                'total_cost' => $totalCost,
                'total_item' => $totalItem,
                'total_cost_doc' => $totalCostDoc
            ]
        ];
        return $return;
    }

    public function count_cost_report(
        $filter = []
    )
    {
        $this->db->from(
            'abc_mst_cost c'
        );

        $this->db->where(
            'c.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if(!empty($filter['search'])){

            $this->db->group_start();

            $this->db->like(
                'c.COST',
                $filter['search']
            );

            $this->db->or_like(
                'c.SLIP_NO',
                $filter['search']
            );

            $this->db->group_end();
        }

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );
        }

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );
        }

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.COST_DATE) >=',
                $filter['date_from']
            );
        }

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.COST_DATE) <=',
                $filter['date_to']
            );
        }

        return $this->db
            ->count_all_results();
    }

    public function summary_cost_report(
        $filter = []
    )
    {
        $this->db->select("

            COALESCE(
                SUM(d.TOTAL),
                0
            ) AS TOTAL_COST,

            COUNT(DISTINCT c.COST)
            AS TOTAL_COST_DOC,

            COUNT(d.ID)
            AS TOTAL_ITEM

        ", false);

        $this->db->from(
            'abc_mst_cost c'
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(

            'abc_mst_cost_detail d',

            '

                d.COST = c.COST
                AND d.PLANT = c.PLANT
                AND d.DELETED IS NULL

            ',

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

        if(!empty($filter['search'])){

            $this->db->group_start();

            $this->db->like(
                'c.COST',
                $filter['search']
            );

            $this->db->or_like(
                'c.SLIP_NO',
                $filter['search']
            );

            $this->db->group_end();
        }

        if(!empty($filter['plant'])){

            $this->db->where(
                'c.PLANT',
                $filter['plant']
            );
        }

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                'c.PEMBAYARAN',
                $filter['pembayaran']
            );
        }

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(c.COST_DATE) >=',
                $filter['date_from']
            );
        }

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(c.COST_DATE) <=',
                $filter['date_to']
            );
        }

        return $this->db
            ->get()
            ->row_array();
    }

    /* ============================================================
    | REPORT AR
    | ============================================================ */

    /**
     * ============================================================
     * AR CUSTOMER QUERY
     *
     * Customer yang ditampilkan adalah customer yang masih memiliki
     * Grand Outstanding:
     *
     *     Sales Remain + Saving Remain
     *
     * Hanya Sales TEMPO.
     *
     * Tidak menggunakan:
     * - Plant filter
     * - Date From
     * - Date To
     *
     * Pagination berdasarkan CUSTOMER.
     * ============================================================
     */
    private function ar_customer_query($filter = [])
    {
        $this->db->from(
            'abc_mst_sales s'
        );

        $this->db->join(
            'abc_cd_customer customer',
            '
                customer.CUST COLLATE utf8mb4_unicode_ci =
                s.CUSTOMER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        /*
        |----------------------------------------------------------
        | HANYA SALES TEMPO
        |----------------------------------------------------------
        */
        $this->db->where(
            's.JENIS_PAY',
            'TEMPO'
        );


        /*
        |----------------------------------------------------------
        | GRAND OUTSTANDING
        |
        | Sales REMAIN
        | +
        | Saving REMAIN
        |
        | Saving diambil dari abc_mst_saving yang terkait
        | dengan Sales.
        |----------------------------------------------------------
        */
        $this->db->where(
            "
            (
                GREATEST(
                    COALESCE(s.REMAIN, 0),
                    0
                )
                +
                COALESCE(
                    (
                        SELECT
                            SUM(
                                GREATEST(
                                    COALESCE(sv.REMAIN, 0),
                                    0
                                )
                            )
                        FROM abc_mst_saving sv
                        WHERE sv.SALES = s.SALES
                        AND sv.RELATED = 'SALES'
                        AND sv.DELETED IS NULL
                    ),
                    0
                )
            ) > 0
            ",
            null,
            false
        );


        /*
        |----------------------------------------------------------
        | CUSTOMER
        |----------------------------------------------------------
        */
        if (!empty($filter['customer'])) {

            $this->db->where(
                's.CUSTOMER',
                $filter['customer']
            );

        }


        /*
        |----------------------------------------------------------
        | SEARCH
        |
        | Bisa mencari:
        | - No Sales
        | - Customer Code
        | - Customer Name
        | - Remark
        |----------------------------------------------------------
        */
        if (!empty($filter['search'])) {

            $search =
                $filter['search'];

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
                'customer.FULL_NAME',
                $search
            );

            $this->db->or_like(
                's.REMARK',
                $search
            );

            $this->db->group_end();

        }


        /*
        |----------------------------------------------------------
        | STATUS
        |
        | Status dihitung berdasarkan:
        |
        | Sales Payment
        | +
        | Saving Payment
        |
        | Grand Outstanding > 0
        |----------------------------------------------------------
        */
        if (!empty($filter['status'])) {

            if (
                $filter['status'] === 'UNPAID'
            ) {

                /*
                * Tidak pernah menerima pembayaran
                * baik ke Sales maupun Saving.
                */
                $this->db->where(
                    "
                    NOT EXISTS (
                        SELECT 1
                        FROM abc_mst_cash_in_detail cid
                        WHERE cid.SALES = s.SALES
                        AND cid.PLANT = s.PLANT
                        AND cid.DELETED IS NULL
                        AND cid.AMOUNT_OFFSET > 0
                    )
                    AND
                    NOT EXISTS (
                        SELECT 1
                        FROM abc_mst_cash_in_saving cis
                        WHERE cis.SALES = s.SALES
                        AND cis.PLANT = s.PLANT
                        AND cis.DELETED IS NULL
                        AND cis.AMOUNT_OFFSET > 0
                    )
                    ",
                    null,
                    false
                );


            } elseif (
                $filter['status'] === 'PARTIAL'
            ) {

                /*
                * Pernah menerima pembayaran,
                * tetapi Grand Outstanding masih > 0.
                */
                $this->db->where(
                    "
                    (
                        EXISTS (
                            SELECT 1
                            FROM abc_mst_cash_in_detail cid
                            WHERE cid.SALES = s.SALES
                            AND cid.PLANT = s.PLANT
                            AND cid.DELETED IS NULL
                            AND cid.AMOUNT_OFFSET > 0
                        )
                        OR
                        EXISTS (
                            SELECT 1
                            FROM abc_mst_cash_in_saving cis
                            WHERE cis.SALES = s.SALES
                            AND cis.PLANT = s.PLANT
                            AND cis.DELETED IS NULL
                            AND cis.AMOUNT_OFFSET > 0
                        )
                    )
                    ",
                    null,
                    false
                );

            }

        }
    }


    /**
     * ============================================================
     * GET AR REPORT
     *
     * Pagination berdasarkan CUSTOMER.
     *
     * 1 customer = 1 group.
     *
     * Semua Sales yang masih memiliki:
     *
     *     Sales Remain
     *     +
     *     Saving Remain
     *
     * akan ditampilkan.
     * ============================================================
     */
    public function get_ar_report(
        $limit,
        $start,
        $filter = []
    ) {

        /* ========================================================
        | STEP 1
        | CUSTOMER AR
        ======================================================== */

        $this->db->select('
            s.CUSTOMER,
            customer.FULL_NAME AS CUSTOMER_NAME
        ', false);

        $this->db->from(
            'abc_mst_sales s'
        );

        $this->db->join(
            'abc_cd_customer customer',
            '
                customer.CUST COLLATE utf8mb4_unicode_ci =
                s.CUSTOMER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        $this->db->where(
            's.JENIS_PAY',
            'TEMPO'
        );


        /* ========================================================
        | SEARCH
        ======================================================== */

        if (!empty($filter['search'])) {

            $search =
                $filter['search'];

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
                'customer.FULL_NAME',
                $search
            );

            $this->db->or_like(
                's.REMARK',
                $search
            );

            $this->db->group_end();

        }


        /* ========================================================
        | CUSTOMER FILTER
        ======================================================== */

        if (!empty($filter['customer'])) {

            $this->db->where(
                's.CUSTOMER',
                $filter['customer']
            );

        }


        /*
        |===========================================================
        | CUSTOMER HARUS PUNYA:
        |
        | SALES REMAIN > 0
        | ATAU
        | SAVING REMAIN > 0
        |===========================================================
        */
        $this->db->where(
            "
            (
                COALESCE(s.REMAIN, 0) > 0

                OR

                EXISTS (
                    SELECT 1
                    FROM abc_mst_saving sv
                    WHERE sv.SALES = s.SALES
                    AND sv.RELATED = 'SALES'
                    AND sv.DELETED IS NULL
                    AND COALESCE(sv.REMAIN, 0) > 0
                )
            )
            ",
            null,
            false
        );


        /* ========================================================
        | STATUS
        ======================================================== */

        if (
            !empty($filter['status'])
        ) {

            if (
                $filter['status'] === 'UNPAID'
            ) {

                $this->db->where(
                    "
                    NOT EXISTS (
                        SELECT 1
                        FROM abc_mst_cash_in_detail cid
                        WHERE cid.SALES = s.SALES
                        AND cid.PLANT = s.PLANT
                        AND cid.DELETED IS NULL
                        AND cid.AMOUNT_OFFSET > 0
                    )

                    AND

                    NOT EXISTS (
                        SELECT 1
                        FROM abc_mst_cash_in_saving cis
                        WHERE cis.SALES = s.SALES
                        AND cis.PLANT = s.PLANT
                        AND cis.DELETED IS NULL
                        AND cis.AMOUNT_OFFSET > 0
                    )
                    ",
                    null,
                    false
                );

            }

            elseif (
                $filter['status'] === 'PARTIAL'
            ) {

                $this->db->where(
                    "
                    (
                        EXISTS (
                            SELECT 1
                            FROM abc_mst_cash_in_detail cid
                            WHERE cid.SALES = s.SALES
                            AND cid.PLANT = s.PLANT
                            AND cid.DELETED IS NULL
                            AND cid.AMOUNT_OFFSET > 0
                        )

                        OR

                        EXISTS (
                            SELECT 1
                            FROM abc_mst_cash_in_saving cis
                            WHERE cis.SALES = s.SALES
                            AND cis.PLANT = s.PLANT
                            AND cis.DELETED IS NULL
                            AND cis.AMOUNT_OFFSET > 0
                        )
                    )
                    ",
                    null,
                    false
                );

            }

        }


        /* ========================================================
        | GROUP CUSTOMER
        ======================================================== */

        $this->db->group_by(
            's.CUSTOMER'
        );

        $this->db->order_by(
            'customer.FULL_NAME',
            'ASC'
        );

        $this->db->limit(
            $limit,
            $start
        );


        $customerRows =
            $this->db
                ->get()
                ->result_array();


        /* ========================================================
        | NO CUSTOMER
        ======================================================== */

        if (empty($customerRows)) {

            return [

                'rows' => [],

                'total_count' =>
                    $this->count_ar_customers(
                        $filter
                    )

            ];

        }


        /* ========================================================
        | CUSTOMER CODES
        ======================================================== */

        $customers = [];

        foreach (
            $customerRows
            as $customer
        ) {

            if (
                !empty(
                    $customer['CUSTOMER']
                )
            ) {

                $customers[] =
                    $customer['CUSTOMER'];

            }

        }


        /* ========================================================
        | STEP 2
        | AMBIL SALES CUSTOMER
        |
        | JANGAN FILTER SALES_REMAIN DI SINI.
        |
        | Kita akan menentukan outstanding setelah Saving
        | juga sudah didapatkan.
        ======================================================== */

        $this->db->select('

            s.SALES,
            s.PLANT,
            s.CUSTOMER,

            customer.FULL_NAME AS CUSTOMER_NAME,

            s.SALES_DATE,
            s.SLIP_NO,
            s.JENIS_PAY,

            s.AMOUNT AS SALES_AMOUNT,

            s.DISCOUNT,
            s.DP_AMOUNT,

            COALESCE(
                s.REMAIN,
                0
            ) AS SALES_REMAIN,

            s.REMARK AS SALES_REMARK,
            s.STATUS AS SALES_STATUS

        ', false);

        $this->db->from(
            'abc_mst_sales s'
        );

        $this->db->join(
            'abc_cd_customer customer',
            '
                customer.CUST COLLATE utf8mb4_unicode_ci =
                s.CUSTOMER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        $this->db->where_in(
            's.CUSTOMER',
            $customers
        );

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        $this->db->where(
            's.JENIS_PAY',
            'TEMPO'
        );


        /* ========================================================
        | SEARCH
        ======================================================== */

        if (!empty($filter['search'])) {

            $search =
                $filter['search'];

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
                'customer.FULL_NAME',
                $search
            );

            $this->db->or_like(
                's.REMARK',
                $search
            );

            $this->db->group_end();

        }


        /* ========================================================
        | CUSTOMER
        ======================================================== */

        if (!empty($filter['customer'])) {

            $this->db->where(
                's.CUSTOMER',
                $filter['customer']
            );

        }


        /* ========================================================
        | ORDER
        ======================================================== */

        $this->db->order_by(
            's.CUSTOMER',
            'ASC'
        );

        $this->db->order_by(
            's.SALES_DATE',
            'ASC'
        );

        $this->db->order_by(
            's.SALES',
            'ASC'
        );


        $historyRows =
            $this->db
                ->get()
                ->result_array();


        /* ========================================================
        | STEP 3
        | SALES LIST
        ======================================================== */

        $salesList = [];

        foreach (
            $historyRows
            as $row
        ) {

            if (
                !empty(
                    $row['SALES']
                )
            ) {

                $salesList[] =
                    $row['SALES'];

            }

        }

        $salesList =
            array_values(
                array_unique(
                    $salesList
                )
            );


        /* ========================================================
        | STEP 4
        | SAVING
        ======================================================== */

        $savingMap = [];

        if (!empty($salesList)) {

            $this->db->select('

                sv.SV_NO,
                sv.PLANT,
                sv.CUSTOMER,
                sv.SALES,
                sv.SV_DATE,

                sv.AMOUNT AS SAVING_AMOUNT,

                COALESCE(
                    sv.REMAIN,
                    0
                ) AS SAVING_REMAIN,

                sv.REMARK AS SAVING_REMARK,
                sv.STATUS AS SAVING_STATUS

            ', false);

            $this->db->from(
                'abc_mst_saving sv'
            );

            $this->db->where_in(
                'sv.SALES',
                $salesList
            );

            $this->db->where(
                'sv.RELATED',
                'SALES'
            );

            $this->db->where(
                'sv.DELETED IS NULL',
                null,
                false
            );

            $savingRows =
                $this->db
                    ->get()
                    ->result_array();


            foreach (
                $savingRows
                as $saving
            ) {

                $sales =
                    $saving['SALES'];

                if (
                    !isset(
                        $savingMap[$sales]
                    )
                ) {

                    $savingMap[$sales] =
                        [];

                }

                $savingMap[$sales][] =
                    $saving;

            }

        }


        /* ========================================================
        | STEP 5
        | PAYMENT SALES
        ======================================================== */

        $salesPaymentMap = [];

        if (!empty($salesList)) {

            $this->db->select('

                cid.SALES,
                cid.CASH_IN,
                cid.PLANT,
                cid.AMOUNT_OFFSET,
                cid.DATE_OFFSET,

                ci.CASHIN_DATE,
                ci.CUSTOMER,
                ci.AMOUNT AS CASH_IN_AMOUNT,
                ci.PEMBAYARAN,
                ci.NO_REK,
                ci.STATUS AS CASH_IN_STATUS,
                ci.REMARK AS CASH_IN_REMARK

            ', false);

            $this->db->from(
                'abc_mst_cash_in_detail cid'
            );

            $this->db->join(
                'abc_mst_cash_in ci',
                '
                    ci.CASH_IN = cid.CASH_IN
                    AND ci.PLANT = cid.PLANT
                    AND ci.DELETED IS NULL
                ',
                'left',
                false
            );

            $this->db->where_in(
                'cid.SALES',
                $salesList
            );

            $this->db->where(
                'cid.DELETED IS NULL',
                null,
                false
            );

            $this->db->where(
                'cid.AMOUNT_OFFSET >',
                0
            );

            $this->db->order_by(
                'cid.DATE_OFFSET',
                'ASC'
            );

            $this->db->order_by(
                'cid.CASH_IN',
                'ASC'
            );


            $paymentRows =
                $this->db
                    ->get()
                    ->result_array();


            foreach (
                $paymentRows
                as $payment
            ) {

                $sales =
                    $payment['SALES'];

                if (
                    !isset(
                        $salesPaymentMap[$sales]
                    )
                ) {

                    $salesPaymentMap[$sales] =
                        [];

                }

                $salesPaymentMap[$sales][] = [

                    'CASH_IN' =>
                        $payment['CASH_IN'],

                    'PLANT' =>
                        $payment['PLANT'],

                    'DATE_OFFSET' =>
                        $payment['DATE_OFFSET'],

                    'CASHIN_DATE' =>
                        $payment['CASHIN_DATE'],

                    'CASH_IN_AMOUNT' =>
                        (float)
                        (
                            $payment['CASH_IN_AMOUNT']
                            ?? 0
                        ),

                    'AMOUNT_OFFSET' =>
                        (float)
                        (
                            $payment['AMOUNT_OFFSET']
                            ?? 0
                        ),

                    'PEMBAYARAN' =>
                        $payment['PEMBAYARAN'],

                    'NO_REK' =>
                        $payment['NO_REK'],

                    'CASH_IN_STATUS' =>
                        $payment['CASH_IN_STATUS'],

                    'CASH_IN_REMARK' =>
                        $payment['CASH_IN_REMARK']

                ];

            }

        }


        /* ========================================================
        | STEP 6
        | PAYMENT SAVING
        ======================================================== */

        $savingPaymentMap = [];

        $savingNos = [];

        foreach (
            $savingMap
            as $salesSavings
        ) {

            foreach (
                $salesSavings
                as $saving
            ) {

                if (
                    !empty(
                        $saving['SV_NO']
                    )
                ) {

                    $savingNos[] =
                        $saving['SV_NO'];

                }

            }

        }


        $savingNos =
            array_values(
                array_unique(
                    $savingNos
                )
            );


        if (!empty($savingNos)) {

            $this->db->select('

                cis.SV_NO,
                cis.CASH_IN,
                cis.PLANT,
                cis.SALES,
                cis.AMOUNT_OFFSET,
                cis.DATE_OFFSET,

                ci.CASHIN_DATE,
                ci.CUSTOMER,
                ci.AMOUNT AS CASH_IN_AMOUNT,
                ci.PEMBAYARAN,
                ci.NO_REK,
                ci.STATUS AS CASH_IN_STATUS,
                ci.REMARK AS CASH_IN_REMARK

            ', false);

            $this->db->from(
                'abc_mst_cash_in_saving cis'
            );

            $this->db->join(
                'abc_mst_cash_in ci',
                '
                    ci.CASH_IN = cis.CASH_IN
                    AND ci.PLANT = cis.PLANT
                    AND ci.DELETED IS NULL
                ',
                'left',
                false
            );

            $this->db->where_in(
                'cis.SV_NO',
                $savingNos
            );

            $this->db->where(
                'cis.DELETED IS NULL',
                null,
                false
            );

            $this->db->where(
                'cis.AMOUNT_OFFSET >',
                0
            );

            $this->db->order_by(
                'cis.DATE_OFFSET',
                'ASC'
            );

            $this->db->order_by(
                'cis.CASH_IN',
                'ASC'
            );


            $savingPaymentRows =
                $this->db
                    ->get()
                    ->result_array();


            foreach (
                $savingPaymentRows
                as $payment
            ) {

                $svNo =
                    $payment['SV_NO'];

                if (
                    !isset(
                        $savingPaymentMap[$svNo]
                    )
                ) {

                    $savingPaymentMap[$svNo] =
                        [];

                }

                $savingPaymentMap[$svNo][] = [

                    'CASH_IN' =>
                        $payment['CASH_IN'],

                    'PLANT' =>
                        $payment['PLANT'],

                    'SALES' =>
                        $payment['SALES'],

                    'DATE_OFFSET' =>
                        $payment['DATE_OFFSET'],

                    'CASHIN_DATE' =>
                        $payment['CASHIN_DATE'],

                    'CASH_IN_AMOUNT' =>
                        (float)
                        (
                            $payment['CASH_IN_AMOUNT']
                            ?? 0
                        ),

                    'AMOUNT_OFFSET' =>
                        (float)
                        (
                            $payment['AMOUNT_OFFSET']
                            ?? 0
                        ),

                    'PEMBAYARAN' =>
                        $payment['PEMBAYARAN'],

                    'NO_REK' =>
                        $payment['NO_REK'],

                    'CASH_IN_STATUS' =>
                        $payment['CASH_IN_STATUS'],

                    'CASH_IN_REMARK' =>
                        $payment['CASH_IN_REMARK']

                ];

            }

        }


        /* ========================================================
        | STEP 7
        | BUILD DETAIL
        ======================================================== */

        $historyByCustomer = [];


        foreach (
            $historyRows
            as $row
        ) {

            $sales =
                $row['SALES'];

            $salesRemain =
                max(
                    (float)
                    (
                        $row['SALES_REMAIN']
                        ?? 0
                    ),
                    0
                );


            $savingDetails =
                $savingMap[$sales]
                ?? [];


            $savingAmount =
                0;

            $savingRemain =
                0;


            foreach (
                $savingDetails
                as $saving
            ) {

                $savingAmount +=
                    (float)
                    (
                        $saving['SAVING_AMOUNT']
                        ?? 0
                    );

                $savingRemain +=
                    max(
                        (float)
                        (
                            $saving['SAVING_REMAIN']
                            ?? 0
                        ),
                        0
                    );

            }


            /*
            |--------------------------------------------------------
            | GRAND OUTSTANDING
            |--------------------------------------------------------
            */
            $grandOutstanding =
                round(
                    $salesRemain
                    +
                    $savingRemain,
                    2
                );


            /*
            |--------------------------------------------------------
            | HANYA MASUK AR JIKA MASIH OUTSTANDING
            |--------------------------------------------------------
            */
            if (
                $grandOutstanding <= 0
            ) {

                continue;

            }


            /*
            |--------------------------------------------------------
            | SALES PAYMENT
            |--------------------------------------------------------
            */
            $salesPayments =
                $salesPaymentMap[$sales]
                ?? [];


            $totalPaidSales =
                0;


            foreach (
                $salesPayments
                as $payment
            ) {

                $totalPaidSales +=
                    (float)
                    (
                        $payment['AMOUNT_OFFSET']
                        ?? 0
                    );

            }


            /*
            |--------------------------------------------------------
            | SAVING PAYMENT
            |--------------------------------------------------------
            */
            $totalPaidSaving =
                0;


            foreach (
                $savingDetails
                as $saving
            ) {

                $savingPayments =
                    $savingPaymentMap[
                        $saving['SV_NO']
                    ]
                    ?? [];


                foreach (
                    $savingPayments
                    as $payment
                ) {

                    $totalPaidSaving +=
                        (float)
                        (
                            $payment['AMOUNT_OFFSET']
                            ?? 0
                        );

                }

            }


            /*
            |--------------------------------------------------------
            | TOTAL PAID
            |--------------------------------------------------------
            */
            $totalPaid =
                round(
                    $totalPaidSales
                    +
                    $totalPaidSaving,
                    2
                );


            /*
            |--------------------------------------------------------
            | STATUS
            |--------------------------------------------------------
            */
            if (
                $totalPaid > 0
            ) {

                $arStatus =
                    'PARTIAL';

            } else {

                $arStatus =
                    'UNPAID';

            }


            /*
            |--------------------------------------------------------
            | STATUS FILTER
            |
            | Kita cek lagi di level detail supaya tidak ada
            | customer yang lolos tetapi detailnya salah status.
            |--------------------------------------------------------
            */
            if (
                !empty($filter['status'])
                &&
                $filter['status'] !== $arStatus
            ) {

                continue;

            }


            /*
            |--------------------------------------------------------
            | PAYMENT HISTORY
            |--------------------------------------------------------
            */
            $payments = [];


            foreach (
                $salesPayments
                as $payment
            ) {

                $payment[
                    'ALLOCATION_TYPE'
                ] =
                    'SALES';

                $payments[] =
                    $payment;

            }


            foreach (
                $savingDetails
                as $saving
            ) {

                $savingPayments =
                    $savingPaymentMap[
                        $saving['SV_NO']
                    ]
                    ?? [];


                foreach (
                    $savingPayments
                    as $payment
                ) {

                    $payment[
                        'ALLOCATION_TYPE'
                    ] =
                        'SAVING';

                    $payment[
                        'SV_NO'
                    ] =
                        $saving['SV_NO'];

                    $payments[] =
                        $payment;

                }

            }


            usort(
                $payments,
                function (
                    $a,
                    $b
                ) {

                    $dateA =
                        !empty(
                            $a['DATE_OFFSET']
                        )
                        ? $a['DATE_OFFSET']
                        : (
                            $a['CASHIN_DATE']
                            ?? ''
                        );

                    $dateB =
                        !empty(
                            $b['DATE_OFFSET']
                        )
                        ? $b['DATE_OFFSET']
                        : (
                            $b['CASHIN_DATE']
                            ?? ''
                        );

                    return strcmp(
                        $dateA,
                        $dateB
                    );

                }
            );


            /*
            |--------------------------------------------------------
            | FINAL ROW
            |--------------------------------------------------------
            */
            $row['SALES_AMOUNT'] =
                (float)
                (
                    $row['SALES_AMOUNT']
                    ?? 0
                );

            $row['SAVING_AMOUNT'] =
                round(
                    $savingAmount,
                    2
                );

            $row['SALES_REMAIN'] =
                round(
                    $salesRemain,
                    2
                );

            $row['SAVING_REMAIN'] =
                round(
                    $savingRemain,
                    2
                );

            $row['GRAND_OUTSTANDING'] =
                $grandOutstanding;

            $row['TOTAL_PAID_SALES'] =
                round(
                    $totalPaidSales,
                    2
                );

            $row['TOTAL_PAID_SAVING'] =
                round(
                    $totalPaidSaving,
                    2
                );

            $row['TOTAL_PAID'] =
                $totalPaid;

            $row['AR_STATUS'] =
                $arStatus;

            $row['PAYMENTS'] =
                $payments;


            /*
            |--------------------------------------------------------
            | CUSTOMER GROUP
            |--------------------------------------------------------
            */
            $customer =
                $row['CUSTOMER'];


            if (
                !isset(
                    $historyByCustomer[$customer]
                )
            ) {

                $historyByCustomer[$customer] =
                    [];

            }


            $historyByCustomer[$customer][] =
                $row;

        }


        /* ========================================================
        | STEP 8
        | FINAL CUSTOMER GROUP
        ======================================================== */

        $rows = [];


        foreach (
            $customerRows
            as $customer
        ) {

            $customerCode =
                $customer['CUSTOMER'];

            $details =
                $historyByCustomer[
                    $customerCode
                ]
                ?? [];


            if (
                empty($details)
            ) {

                continue;

            }


            $rows[] = [

                'CUSTOMER' =>
                    $customerCode,

                'CUSTOMER_NAME' =>
                    $customer['CUSTOMER_NAME']
                    ?: '-',

                'DETAILS' =>
                    $details

            ];

        }


        return [

            'rows' =>
                $rows,

            'total_count' =>
                $this->count_ar_customers(
                    $filter
                )

        ];

    }


    /**
     * ============================================================
     * COUNT AR CUSTOMER
     * ============================================================
     */
    public function count_ar_customers(
        $filter = []
    ) {

        $this->ar_customer_query(
            $filter
        );

        $this->db->select(
            'COUNT(DISTINCT s.CUSTOMER) AS TOTAL_CUSTOMER',
            false
        );

        $row =
            $this->db
                ->get()
                ->row_array();

        return (int)
            (
                $row['TOTAL_CUSTOMER']
                ?? 0
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AR PAYMENT HISTORY
    |--------------------------------------------------------------------------
    |
    | Relationship:
    |
    | abc_mst_cash_in_detail
    |       |
    |       +-- CASH_IN
    |       +-- SALES
    |       +-- AMOUNT_OFFSET
    |       |
    |       v
    | abc_mst_cash_in
    |
    */

    public function get_ar_payment_history(
        $salesNos = []
    ) {

        if (
            empty($salesNos)
        ) {

            return [];

        }


        $this->db->select("

            d.SALES,

            d.PLANT,

            d.CASH_IN,

            d.AMOUNT_OFFSET,

            d.DATE_OFFSET,

            ci.CASHIN_DATE,

            ci.PEMBAYARAN,

            ci.REMARK

        ", false);


        $this->db->from(
            'abc_mst_cash_in_detail d'
        );


        $this->db->join(
            'abc_mst_cash_in ci',
            "
                ci.CASH_IN = d.CASH_IN
                AND ci.PLANT = d.PLANT
                AND ci.DELETED IS NULL
            ",
            'left'
        );


        $this->db->where_in(
            'd.SALES',
            $salesNos
        );


        $this->db->where(
            'd.DELETED IS NULL',
            null,
            false
        );


        $this->db->where(
            'd.AMOUNT_OFFSET >',
            0
        );


        $this->db->order_by(
            'ci.CASHIN_DATE',
            'ASC'
        );


        $this->db->order_by(
            'd.CASH_IN',
            'ASC'
        );


        $rows =
            $this->db
                ->get()
                ->result_array();


        $result = [];


        foreach (
            $rows
            as $row
        ) {

            $sales =
                $row['SALES'];


            if (
                !isset(
                    $result[$sales]
                )
            ) {

                $result[$sales] = [];

            }


            $result[$sales][] = [

                'CASH_IN' =>
                    $row['CASH_IN'],

                'CASH_IN_DATE' =>
                    $row['CASHIN_DATE']
                    ?? $row['DATE_OFFSET'],

                'JENIS_PAY' =>
                    $row['PEMBAYARAN'],

                'AMOUNT_OFFSET' =>
                    (float)
                    $row['AMOUNT_OFFSET'],

                'REMARK' =>
                    $row['REMARK']

            ];

        }


        return $result;
    }
}