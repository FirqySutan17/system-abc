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
}