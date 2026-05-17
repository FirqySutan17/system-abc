<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportInventory_model extends CI_Model {

    public function get_plant_by_user($plant)
    {
        return $this->db
            ->where('HEAD_CODE', 'AJ')
            ->where('CODE', $plant)
            ->get('abc_cd_code')
            ->row();
    }

    public function get_plant_list()
    {
        return $this->db
            ->select('CODE, CODE_NAME')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->order_by('CODE', 'ASC')
            ->get()
            ->result();
    }

    public function get_supplier_list()
    {
        return $this->db
            ->select('CUST, FULL_NAME')
            ->from('abc_cd_customer')
            ->group_start()
                ->where('CUST_KIND', 'SUPPLIER')
                ->or_where('CUST_CLASS', 'SUPPLIER')
            ->group_end()
            ->order_by('FULL_NAME', 'ASC')
            ->get()
            ->result();
    }

    public function get_customer_list()
    {
        return $this->db
            ->select('CUST, FULL_NAME')
            ->from('abc_cd_customer')
            ->group_start()
                ->where('CUST_KIND', 'CUSTOMER')
                ->or_where('CUST_CLASS', 'CUSTOMER')
            ->group_end()
            ->order_by('FULL_NAME', 'ASC')
            ->get()
            ->result();
    }

    private function convert_date($date)
    {
        if (!$date) return null;
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

    private function base_po_report_query($filters = [])
    {
        $this->db->from('abc_mst_po a');

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_po_detail b',
            '
                a.PO = b.PO
                AND a.PLANT = b.PLANT
            ',
            'inner'
        );

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.HEAD_CODE = 'PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                a.PLANT COLLATE utf8mb4_unicode_ci
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PO TYPE
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code type',
            "
                type.HEAD_CODE = 'PO'
                AND type.CODE COLLATE utf8mb4_unicode_ci =
                a.PO_TYPE COLLATE utf8mb4_unicode_ci
            ",
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
                a.SUPPLIER COLLATE utf8mb4_unicode_ci
            ',
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
            '
                customer.CUST COLLATE utf8mb4_unicode_ci =
                b.CUSTOMER COLLATE utf8mb4_unicode_ci
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
                a.MATERIAL COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER DELETED
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'a.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER PLANT
        |--------------------------------------------------------------------------
        */

        if(!empty($filters['plant'])){

            $this->db->where(
                'a.PLANT',
                $filters['plant']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER SUPPLIER
        |--------------------------------------------------------------------------
        */

        if(!empty($filters['supplier'])){

            $this->db->where(
                'a.SUPPLIER',
                $filters['supplier']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER PO
        |--------------------------------------------------------------------------
        */

        if(!empty($filters['po'])){

            $this->db->like(
                'a.PO',
                $filters['po']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | FILTER DATE
        |--------------------------------------------------------------------------
        */

        if(!empty($filters['date_from'])){

            $this->db->where(
                'DATE(a.PO_DATE) >=',
                $filters['date_from']
            );

        }

        if(!empty($filters['date_to'])){

            $this->db->where(
                'DATE(a.PO_DATE) <=',
                $filters['date_to']
            );

        }
    }

    public function get_po_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'a.PO_DATE',
        $dir = 'DESC'
    ){
        $this->db->select("
            a.PLANT,
            plant.CODE_NAME AS PLANT_NAME,

            a.PO,
            a.PO_DATE,
            a.PO_TYPE,
            type.CODE_NAME AS PO_NAME,

            a.SUPPLIER,
            supplier.FULL_NAME AS SUPPLIER_NAME,

            a.STATUS,
            a.REMARK,

            a.NO_TRUCK,
            a.DRIVER,

            a.JUMLAH AS HEADER_QTY,
            a.BERAT AS HEADER_BERAT,
            a.HARGA AS HEADER_HARGA,
            a.TOTAL AS HEADER_TOTAL,

            b.SEQ_NO,
            b.CUSTOMER,
            customer.FULL_NAME AS CUSTOMER_NAME,

            a.MATERIAL,
            material.MATERIAL_NAME,

            b.JUMLAH,
            b.BERAT,
            b.HARGA,
            b.TOTAL
        ", false);

        $this->base_po_report_query($filters);

        $this->db->order_by($order, $dir);
        $this->db->order_by('a.PO', 'DESC');
        $this->db->order_by('b.SEQ_NO', 'ASC');

        if($limit > 0){

            $this->db->limit(
                (int)$limit,
                (int)$start
            );

        }

        return $this->db
            ->get()
            ->result();
    }

    public function get_po_summary($filters = [])
    {
        $this->db->select("
            COUNT(DISTINCT a.PO) AS TOTAL_PO,

            COUNT(DISTINCT a.SUPPLIER)
                AS TOTAL_SUPPLIER,

            COUNT(DISTINCT b.CUSTOMER)
                AS TOTAL_CUSTOMER,

            SUM(b.JUMLAH)
                AS TOTAL_QTY,

            SUM(b.BERAT)
                AS TOTAL_BERAT,

            SUM(b.TOTAL)
                AS TOTAL_AMOUNT
        ", false);

        $this->base_po_report_query($filters);

        return $this->db
            ->get()
            ->row();
    }

    public function count_po_report($filters = [])
    {
        $this->db->select(
            'COUNT(DISTINCT a.PO) AS TOTAL',
            false
        );

        $this->base_po_report_query($filters);

        return (int)
            $this->db
                ->get()
                ->row()
                ->TOTAL;
    }

    public function get_receive_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'RECEIVE_DATE',
        $dir = 'DESC'
    ){
        $allowedOrder = [

            'RECEIVE'      => 'r.RECEIVE',
            'PLANT'        => 'r.PLANT',
            'RECEIVE_DATE' => 'r.RECEIVE_DATE',
            'PO'           => 'r.PO',
            'SUPPLIER'     => 'r.SUPPLIER'

        ];

        $orderBy = $allowedOrder[$order]
            ?? 'r.RECEIVE_DATE';

        $dir = strtoupper($dir) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | HEADER QUERY
        |--------------------------------------------------------------------------
        */

        $this->db
            ->select('r.RECEIVE, r.PLANT')
            ->from('abc_mst_receive r')
            ->where('r.DELETED IS NULL', null, false);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['plant'])) {

            $this->db->where(
                'r.PLANT',
                $filters['plant']
            );

        }

        if (!empty($filters['supplier'])) {

            $this->db->where(
                'r.SUPPLIER',
                $filters['supplier']
            );

        }

        if (!empty($filters['receive'])) {

            $this->db->group_start();

            $this->db->like(
                'r.RECEIVE',
                $filters['receive']
            );

            $this->db->or_like(
                'r.PO',
                $filters['receive']
            );

            $this->db->group_end();

        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(r.RECEIVE_DATE) >=',
                $filters['date_from']
            );

        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(r.RECEIVE_DATE) <=',
                $filters['date_to']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            $orderBy,
            $dir
        );

        $this->db->order_by(
            'r.RECEIVE',
            'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | LIMIT
        |--------------------------------------------------------------------------
        */

        if ($limit > 0) {

            $this->db->limit(
                $limit,
                $start
            );

        }

        $headers = $this->db
            ->get()
            ->result();

        if (!$headers) {

            return [];

        }

        /*
        |--------------------------------------------------------------------------
        | BUILD PAIRS
        |--------------------------------------------------------------------------
        */

        $pairs = [];

        foreach ($headers as $h) {

            $pairs[] = "("
                .$this->db->escape($h->RECEIVE)
                .","
                .$this->db->escape($h->PLANT)
                .")";

        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL QUERY
        |--------------------------------------------------------------------------
        */

        $sql = "
            SELECT

                r.RECEIVE,
                r.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                r.RECEIVE_DATE,

                r.PO,
                r.NOTA,
                r.NO_REF,

                r.SUPPLIER,
                supplier.FULL_NAME AS SUPPLIER_NAME,

                r.PEMBAYARAN,
                pay.CODE_NAME AS PEMBAYARAN_NAME,

                r.JENIS_PAY,
                r.SLIP_NO,

                r.REMARK,

                r.ATTACH_FILE_NAME,

                r.STATUS_RECEIVE,

                d.SEQ_NO,

                d.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,

                d.PO_TYPE,
                po_type.CODE_NAME AS PO_TYPE_NAME,

                d.MATERIAL,
                material.MATERIAL_NAME,

                d.JUMLAH,
                d.BERAT,

                d.SUSUT_JUMLAH,
                d.SUSUT_BERAT,

                d.HARGA,
                d.TOTAL,

                d.IS_EXTRA,
                d.SALES_CREATED,
                d.SALES_NO,

                d.KETERANGAN,
                d.STATUS

            FROM abc_mst_receive r

            INNER JOIN abc_mst_receive_detail d
                ON d.RECEIVE = r.RECEIVE
                AND d.PLANT = r.PLANT
                AND d.DELETED IS NULL

            LEFT JOIN abc_cd_code plant
                ON plant.HEAD_CODE = 'PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                r.PLANT COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer supplier
                ON supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer customer
                ON customer.CUST COLLATE utf8mb4_unicode_ci =
                d.CUSTOMER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_material material
                ON material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_code po_type
                ON po_type.HEAD_CODE = 'PO'
                AND po_type.CODE COLLATE utf8mb4_unicode_ci =
                d.PO_TYPE COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_code pay
                ON pay.HEAD_CODE = 'PAYMENT'
                AND pay.CODE COLLATE utf8mb4_unicode_ci =
                r.PEMBAYARAN COLLATE utf8mb4_unicode_ci

            WHERE (r.RECEIVE,r.PLANT)
            IN (".implode(',', $pairs).")

            ORDER BY

                {$orderBy} {$dir},

                r.RECEIVE DESC,

                d.SEQ_NO ASC
        ";

        return $this->db
            ->query($sql)
            ->result();
    }

    public function count_receive_report($filters = [])
    {
        $this->db
            ->from('abc_mst_receive r')
            ->where('r.DELETED IS NULL', null, false);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['plant'])) {

            $this->db->where(
                'r.PLANT',
                $filters['plant']
            );

        }

        if (!empty($filters['supplier'])) {

            $this->db->where(
                'r.SUPPLIER',
                $filters['supplier']
            );

        }

        if (!empty($filters['receive'])) {

            $this->db->group_start();

            $this->db->like(
                'r.RECEIVE',
                $filters['receive']
            );

            $this->db->or_like(
                'r.PO',
                $filters['receive']
            );

            $this->db->group_end();

        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(r.RECEIVE_DATE) >=',
                $filters['date_from']
            );

        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(r.RECEIVE_DATE) <=',
                $filters['date_to']
            );

        }

        return $this->db
            ->count_all_results();
    }
}
