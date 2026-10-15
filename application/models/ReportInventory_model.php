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

    public function get_class_out_list()
    {
        return $this->db

            ->select('CODE, CODE_NAME')

            ->from('abc_cd_code')

            ->where(
                'HEAD_CODE',
                'CLASS OUT'
            )

            ->where(
                'CODE <>',
                '*'
            )

            ->where(
                'USE_YN',
                'Y'
            )

            ->order_by(
                'CODE_NAME',
                'ASC'
            )

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
    ) {
        $this->base_po_report_query($filters);

        $this->db->select('
            a.PLANT,
            plant.CODE_NAME AS PLANT_NAME,

            a.PO,
            a.PO_DATE,
            a.PO_TYPE,
            type.CODE_NAME AS PO_NAME,

            a.SUPPLIER,
            supplier.FULL_NAME AS SUPPLIER_NAME,

            a.MATERIAL,
            material.MATERIAL_NAME,

            a.JUMLAH,
            a.BERAT,
            a.AVG_BW,

            a.JANTAN_QTY,
            a.JANTAN_BW,
            a.BETINA_QTY,
            a.BETINA_BW,

            a.MATI_QTY,
            a.MATI_BW,

            a.ACTUAL_HASIL_TIMBANG,
            a.SUSUT_BW,

            a.TOTAL_TERIMA_QTY,
            a.TOTAL_TERIMA_BW,

            a.CLAIM_QTY,
            a.CLAIM_BW,

            a.TOTAL_BAYAR_BW,

            a.HARGA,
            a.TOTAL,

            a.PAID_AMOUNT,
            a.REMAINING,
            a.PAYMENT_STATUS,

            a.NO_TRUCK,
            a.DRIVER,

            a.STATUS,
            a.REMARK
        ');

        $this->db->order_by($order, $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_po_summary($filters = [])
    {
        $this->db->select("
            COUNT(*) AS TOTAL_PO,

            SUM(CASE WHEN a.STATUS = 'OPEN' THEN 1 ELSE 0 END) AS TOTAL_OPEN,

            SUM(CASE WHEN a.STATUS = 'RECEIVED' THEN 1 ELSE 0 END) AS TOTAL_RECEIVED,

            SUM(CASE WHEN a.STATUS = 'PAID' THEN 1 ELSE 0 END) AS TOTAL_PAID,

            SUM(a.JUMLAH) AS TOTAL_QTY,

            SUM(a.BERAT) AS TOTAL_BERAT,

            SUM(a.TOTAL_BAYAR_BW) AS TOTAL_BAYAR_BW,

            SUM(a.TOTAL) AS TOTAL_AMOUNT
        ", false);

        $this->base_po_report_query($filters);

        return $this->db
            ->get()
            ->row();
    }

    public function count_po_report($filters = [])
    {
        $this->db->select('COUNT(*) AS TOTAL', false);

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
            ->where(
                'r.DELETED IS NULL',
                null,
                false
            );

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
        | BUILD RECEIVE + PLANT PAIRS
        |--------------------------------------------------------------------------
        */

        $pairs = [];

        foreach ($headers as $h) {

            $pairs[] = "("
                . $this->db->escape($h->RECEIVE)
                . ","
                . $this->db->escape($h->PLANT)
                . ")";

        }

        /*
        |--------------------------------------------------------------------------
        | RECEIVE DETAIL + SALES + SAVING
        |--------------------------------------------------------------------------
        */

        $sql = "

            SELECT

                /*
                |--------------------------------------------------------------------------
                | RECEIVE INFORMATION
                |--------------------------------------------------------------------------
                */

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

                r.TOTAL_QTY,
                r.TOTAL_BW,
                r.TOTAL_AVG_BW,

                r.MATERIAL AS RECEIVE_MATERIAL,

                r.TOTAL_CUSTOMER,

                /*
                |--------------------------------------------------------------------------
                | RECEIVE DETAIL
                |--------------------------------------------------------------------------
                */

                d.ID AS RECEIVE_DETAIL_ID,

                d.SEQ_NO,

                d.CUSTOMER,

                customer.FULL_NAME AS CUSTOMER_NAME,

                d.PO_TYPE,

                po_type.CODE_NAME AS PO_TYPE_NAME,

                d.MATERIAL,

                material.MATERIAL_NAME,

                d.JUMLAH,
                d.BERAT,
                d.AVG_BW,

                d.SUSUT_JUMLAH,
                d.SUSUT_BERAT,

                d.HARGA,
                d.DISCOUNT,
                d.TOTAL,

                d.IS_EXTRA,

                d.SALES_CREATED,
                d.SALES_NO,

                d.STATUS,

                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                s.SALES AS SALES_REPORT_NO,

                s.SALES_DATE AS SALES_REPORT_DATE,

                s.AMOUNT AS SALES_AMOUNT,

                s.MODAL AS SALES_MODAL,

                s.BIAYA AS SALES_BIAYA,

                s.DP_AMOUNT AS SALES_DP,

                s.DISCOUNT AS SALES_DISCOUNT,

                s.ROUNDING AS SALES_ROUNDING,

                s.REMAIN AS SALES_REMAIN,

                s.STATUS AS SALES_STATUS,

                /*
                |--------------------------------------------------------------------------
                | SAVING
                |--------------------------------------------------------------------------
                */

                saving_data.SAVING_NO_LIST,

                saving_data.SAVING_AMOUNT,

                saving_data.SAVING_BASIS,

                saving_data.SAVING_RATE,

                saving_data.SAVING_QTY,

                saving_data.SAVING_WEIGHT,

                saving_data.SAVING_REMAIN,

                saving_data.SAVING_STATUS

            FROM abc_mst_receive r

            /*
            |--------------------------------------------------------------------------
            | RECEIVE DETAIL
            |--------------------------------------------------------------------------
            */

            INNER JOIN abc_mst_receive_detail d

                ON d.RECEIVE = r.RECEIVE

                AND d.PLANT = r.PLANT

                AND d.DELETED IS NULL

            /*
            |--------------------------------------------------------------------------
            | PLANT
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_code plant

                ON plant.HEAD_CODE = 'PLANT'

                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                r.PLANT COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | SUPPLIER
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_customer supplier

                ON supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_customer customer

                ON customer.CUST COLLATE utf8mb4_unicode_ci =
                d.CUSTOMER COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_material material

                ON material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | PO TYPE
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_code po_type

                ON po_type.HEAD_CODE = 'PO'

                AND po_type.CODE COLLATE utf8mb4_unicode_ci =
                d.PO_TYPE COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            LEFT JOIN abc_cd_code pay

                ON pay.HEAD_CODE = 'PAYMENT'

                AND pay.CODE COLLATE utf8mb4_unicode_ci =
                r.PEMBAYARAN COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | SALES
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | Tidak menggunakan RECEIVE_DETAIL_ID.
            |
            | Relasi hanya:
            |
            | s.RECEIVE = r.RECEIVE
            |
            | dan:
            |
            | s.REMARK = AUTO FROM RECEIVE {RECEIVE}
            |
            */

            LEFT JOIN abc_mst_sales s

                ON s.RECEIVE COLLATE utf8mb4_unicode_ci =
                r.RECEIVE COLLATE utf8mb4_unicode_ci

                AND s.REMARK COLLATE utf8mb4_unicode_ci =
                CONCAT(
                    'AUTO FROM RECEIVE ',
                    r.RECEIVE
                ) COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | SAVING
            |--------------------------------------------------------------------------
            */

            LEFT JOIN (

                SELECT

                    sv.RECEIVE,

                    GROUP_CONCAT(
                        DISTINCT sv.SV_NO
                        ORDER BY sv.SV_NO
                        SEPARATOR ', '
                    ) AS SAVING_NO_LIST,

                    SUM(
                        COALESCE(sv.AMOUNT, 0)
                    ) AS SAVING_AMOUNT,

                    GROUP_CONCAT(
                        DISTINCT sv.BASIS
                        ORDER BY sv.BASIS
                        SEPARATOR ', '
                    ) AS SAVING_BASIS,

                    GROUP_CONCAT(
                        DISTINCT sv.RATE
                        ORDER BY sv.RATE
                        SEPARATOR ', '
                    ) AS SAVING_RATE,

                    SUM(
                        COALESCE(sv.TOTAL_QTY, 0)
                    ) AS SAVING_QTY,

                    SUM(
                        COALESCE(sv.TOTAL_WEIGHT, 0)
                    ) AS SAVING_WEIGHT,

                    SUM(
                        COALESCE(sv.REMAIN, 0)
                    ) AS SAVING_REMAIN,

                    GROUP_CONCAT(
                        DISTINCT sv.STATUS
                        ORDER BY sv.STATUS
                        SEPARATOR ', '
                    ) AS SAVING_STATUS

                FROM abc_mst_saving sv

                GROUP BY sv.RECEIVE

            ) saving_data

                ON saving_data.RECEIVE COLLATE utf8mb4_unicode_ci =
                r.RECEIVE COLLATE utf8mb4_unicode_ci

            /*
            |--------------------------------------------------------------------------
            | RECEIVE PAIRS
            |--------------------------------------------------------------------------
            */

            WHERE (r.RECEIVE, r.PLANT)
            IN (" . implode(',', $pairs) . ")

            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            ORDER BY

                {$orderBy} {$dir},

                r.RECEIVE DESC,

                d.SEQ_NO ASC,

                s.SALES_DATE ASC,

                s.SALES ASC

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

    public function get_culling_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'ymd',
        $dir = 'DESC'
    ){
        $allowedOrder = [

            'idno'       => 'c.idno',
            'plant'      => 'c.plant',
            'ymd'        => 'c.ymd',
            'class_out'  => 'c.class_out',
            'CREATED_AT' => 'c.CREATED_AT'

        ];

        $orderBy =
            $allowedOrder[$order]
            ?? 'c.ymd';

        $dir =
            strtoupper($dir) === 'ASC'
                ? 'ASC'
                : 'DESC';

        $this->db

            ->select("
                c.*,

                plant.CODE_NAME
                    AS PLANT_NAME,

                class.CODE_NAME
                    AS CLASS_OUT_NAME
            ")

            ->from('abc_mst_culling c')

            ->join(
                'abc_cd_code plant',
                "
                    plant.HEAD_CODE='PLANT'
                    AND plant.CODE COLLATE utf8mb4_unicode_ci =
                    c.plant COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_code class',
                "
                    class.HEAD_CODE='CLASS OUT'
                    AND class.CODE COLLATE utf8mb4_unicode_ci =
                    c.class_out COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['plant'])) {

            $this->db->where(
                'c.plant',
                $filters['plant']
            );
        }

        if (!empty($filters['class_out'])) {

            $this->db->where(
                'c.class_out',
                $filters['class_out']
            );
        }

        if (!empty($filters['search'])) {

            $this->db->group_start()

                ->like(
                    'c.remark',
                    $filters['search']
                )

                ->or_like(
                    'c.CREATED_BY',
                    $filters['search']
                )

                ->group_end();
        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(c.ymd) >=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(c.ymd) <=',
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
            'c.idno',
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

        return $this->db
            ->get()
            ->result();
    }

    public function get_culling_summary(
        $filters = []
    ){
        $this->db
            ->select("\n                COUNT(c.idno) AS TOTAL_CULLING,\n                SUM(c.JUMLAH) AS TOTAL_QTY,\n                SUM(c.BERAT) AS TOTAL_BERAT,\n                SUM(CASE WHEN class.CODE_NAME = 'DIPOTONG' THEN 1 ELSE 0 END) AS TOTAL_DIPOTONG,\n                SUM(CASE WHEN class.CODE_NAME = 'MATI' THEN 1 ELSE 0 END) AS TOTAL_MATI\n            ", false)
            ->from('abc_mst_culling c')
            ->join(
                'abc_cd_code class',
                "class.HEAD_CODE='CLASS OUT'\n                    AND class.CODE COLLATE utf8mb4_unicode_ci =\n                    c.class_out COLLATE utf8mb4_unicode_ci",
                'left',
                false
            );

        if (!empty($filters['plant'])) {

            $this->db->where(
                'c.plant',
                $filters['plant']
            );
        }

        if (!empty($filters['class_out'])) {

            $this->db->where(
                'c.class_out',
                $filters['class_out']
            );
        }

        if (!empty($filters['search'])) {

            $this->db->group_start()

                ->like(
                    'c.remark',
                    $filters['search']
                )

                ->or_like(
                    'c.CREATED_BY',
                    $filters['search']
                )

                ->group_end();
        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(c.ymd) >=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(c.ymd) <=',
                $filters['date_to']
            );
        }

        return $this->db
            ->get()
            ->row_array() ?: [];
    }

    public function count_culling_report(
        $filters = []
    ){
        $this->db
            ->from('abc_mst_culling c');

        if (!empty($filters['plant'])) {

            $this->db->where(
                'c.plant',
                $filters['plant']
            );
        }

        if (!empty($filters['class_out'])) {

            $this->db->where(
                'c.class_out',
                $filters['class_out']
            );
        }

        if (!empty($filters['search'])) {

            $this->db->group_start()

                ->like(
                    'c.remark',
                    $filters['search']
                )

                ->or_like(
                    'c.CREATED_BY',
                    $filters['search']
                )

                ->group_end();
        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(c.ymd) >=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(c.ymd) <=',
                $filters['date_to']
            );
        }

        return $this->db
            ->count_all_results();
    }
}
