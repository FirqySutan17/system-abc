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

            d.REMARK,

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
}