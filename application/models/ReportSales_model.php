<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportSales_model extends CI_Model {

    public function get_plant_by_user($plant)
    {
        return $this->db
            ->where('HEAD_CODE', 'AJ')
            ->where('CODE', $plant)
            ->get('cd_code')
            ->row();
    }

    public function get_plant_list()
    {
        return $this->db
            ->select('CODE, CODE_NAME')
            ->from('cd_code')
            ->where('HEAD_CODE', 'AJ')
            ->order_by('CODE', 'ASC')
            ->get()
            ->result();
    }

    public function get_supplier_list()
    {
        return $this->db
            ->select('CUST, FULL_NAME')
            ->from('cd_customer')
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
            ->from('cd_customer')
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

    public function get_sales_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'SALES_DATE',
        $dir = 'DESC'
    ){

        $allowedOrder = [

            'SALES'      => 's.SALES',
            'PLANT'      => 's.PLANT',
            'SALES_DATE' => 's.SALES_DATE',
            'CUSTOMER'   => 's.CUSTOMER'

        ];

        $orderBy = $allowedOrder[$order]
            ?? 's.SALES_DATE';

        $dir = strtoupper($dir) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | HEADER QUERY
        |--------------------------------------------------------------------------
        */

        $this->db
            ->select('s.SALES, s.PLANT')
            ->from('abc_mst_sales s')
            ->where('s.DELETED IS NULL', null, false);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['plant'])) {

            $this->db->where(
                's.PLANT',
                $filters['plant']
            );
        }

        if (!empty($filters['customer'])) {

            $this->db->where(
                's.CUSTOMER',
                $filters['customer']
            );
        }

        if (!empty($filters['status'])) {

            $this->db->where(
                's.STATUS',
                $filters['status']
            );
        }

        if (!empty($filters['search'])) {

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $filters['search']
            );

            $this->db->or_like(
                's.NOTA',
                $filters['search']
            );

            $this->db->group_end();
        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filters['date_from']
            );
        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
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
            's.SALES',
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
                .$this->db->escape($h->SALES)
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

                s.SALES,
                s.PLANT,

                plant.CODE_NAME AS PLANT_NAME,

                s.SALES_DATE,

                s.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,

                s.PEMBAYARAN,
                s.JENIS_PAY,

                s.STATUS,

                s.NOTA,

                s.REMARK,

                s.AMOUNT,

                s.ATTACHMENT_NAME,
                s.ATTACHMENT_PATH,

                d.SEQ_NO,

                d.MATERIAL,

                material.MATERIAL_NAME,

                d.JUMLAH,

                d.BERAT,

                d.HARGA,

                d.TOTAL

            FROM abc_mst_sales s

            INNER JOIN abc_mst_sales_detail d
                ON d.SALES = s.SALES
                AND d.PLANT = s.PLANT

            LEFT JOIN abc_cd_code plant
                ON plant.HEAD_CODE = 'PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                s.PLANT COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer customer
                ON customer.CUST COLLATE utf8mb4_unicode_ci =
                s.CUSTOMER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_material material
                ON material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci

            WHERE (s.SALES,s.PLANT)
            IN (".implode(',', $pairs).")

            ORDER BY

                {$orderBy} {$dir},

                s.SALES DESC,

                d.SEQ_NO ASC

        ";

        $details = $this->db
            ->query($sql)
            ->result_array();

        /*
        |--------------------------------------------------------------------------
        | GROUP HEADER + DETAILS
        |--------------------------------------------------------------------------
        */

        $grouped = [];

        foreach($details as $row){

            $key = $row['SALES'].'|'.$row['PLANT'];

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            if(!isset($grouped[$key])){

                $grouped[$key] = [

                    'SALES'            => $row['SALES'],
                    'PLANT'            => $row['PLANT'],
                    'PLANT_NAME'       => $row['PLANT_NAME'],

                    'SALES_DATE'       => $row['SALES_DATE'],

                    'CUSTOMER'         => $row['CUSTOMER'],
                    'CUSTOMER_NAME'    => $row['CUSTOMER_NAME'],

                    'PEMBAYARAN'       => $row['PEMBAYARAN'],
                    'JENIS_PAY'        => $row['JENIS_PAY'],

                    'STATUS'           => $row['STATUS'],

                    'NOTA'             => $row['NOTA'],

                    'REMARK'           => $row['REMARK'],

                    'AMOUNT'           => $row['AMOUNT'],

                    'ATTACHMENT_NAME'  => $row['ATTACHMENT_NAME'],

                    'ATTACHMENT_PATH'  => $row['ATTACHMENT_PATH'],

                    'DETAILS' => []
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | DETAILS
            |--------------------------------------------------------------------------
            */

            $grouped[$key]['DETAILS'][] = [

                'SEQ_NO'        => $row['SEQ_NO'],

                'MATERIAL'      => $row['MATERIAL'],

                'MATERIAL_NAME' => $row['MATERIAL_NAME'],

                'JUMLAH'        => $row['JUMLAH'],

                'BERAT'         => $row['BERAT'],

                'HARGA'         => $row['HARGA'],

                'TOTAL'         => $row['TOTAL']
            ];
        }

        return array_values($grouped);
    }

    public function count_sales_report($filters = [])
    {
        $this->db
            ->from('abc_mst_sales s')
            ->where('s.DELETED IS NULL', null, false);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['plant'])) {

            $this->db->where(
                's.PLANT',
                $filters['plant']
            );

        }

        if (!empty($filters['customer'])) {

            $this->db->where(
                's.CUSTOMER',
                $filters['customer']
            );

        }

        if (!empty($filters['status'])) {

            $this->db->where(
                's.STATUS',
                $filters['status']
            );

        }

        if (!empty($filters['sales'])) {

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $filters['sales']
            );

            $this->db->or_like(
                's.NOTA',
                $filters['sales']
            );

            $this->db->group_end();

        }

        if (!empty($filters['date_from'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filters['date_from']
            );

        }

        if (!empty($filters['date_to'])) {

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
                $filters['date_to']
            );

        }

        return $this->db
            ->count_all_results();
    }

    public function get_items($q = null)
    {
        $this->db->select('ITEM, FULL_NAME');

        $this->db->from('cd_item');

        if (!empty($q)) {

            $this->db->group_start();

            $this->db->like('ITEM', $q);

            $this->db->or_like('FULL_NAME', $q);

            $this->db->group_end();
        }

        $this->db->limit(20);

        $this->db->order_by('ITEM', 'ASC');

        return $this->db->get()->result();
    }

    public function get_user_plants($username)
    {
        $this->db->reset_query();

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

        return is_array($plants)
            ? array_map('strval', $plants)
            : [];
    }

    public function get_plant_select2_by_user($username)
    {
        $plantCodes = $this->get_user_plants($username);

        if (empty($plantCodes)) {
            return [];
        }

        return $this->db
            ->select('CODE, CODE_NAME')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result();
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
}
