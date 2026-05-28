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
        $limit,
        $start,
        $filter = []
    )
    {
        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $this->db->select("

            s.SALES,

            s.PLANT,

            plant.CODE_NAME AS PLANT_NAME,

            s.SALES_DATE,

            s.CUSTOMER,

            customer.FULL_NAME AS CUSTOMER_NAME,

            s.PEMBAYARAN,

            s.SLIP_NO,

            s.NOTA,

            s.REMARK,

            s.STATUS,

            s.REMAIN,

            COUNT(d.ID) AS TOTAL_ITEM,

            COALESCE(
                SUM(d.TOTAL),
                0
            ) AS GRAND_TOTAL

        ", false);

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

            'abc_cd_code plant',

            '

                plant.CODE = s.PLANT
                AND plant.HEAD_CODE = "PLANT"

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
                s.CUSTOMER COLLATE utf8mb4_unicode_ci

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
            's.DELETED IS NULL',
            null,
            false
        );

        if(!empty($filter['search'])){

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $filter['search']
            );

            $this->db->or_like(
                's.SLIP_NO',
                $filter['search']
            );

            $this->db->or_like(
                's.NOTA',
                $filter['search']
            );

            $this->db->or_like(
                'customer.FULL_NAME',
                $filter['search']
            );

            $this->db->group_end();
        }

        if(!empty($filter['plant'])){

            $this->db->where(
                's.PLANT',
                $filter['plant']
            );
        }

        if(!empty($filter['customer'])){

            $this->db->where(
                's.CUSTOMER',
                $filter['customer']
            );
        }

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                's.PEMBAYARAN',
                $filter['pembayaran']
            );
        }

        if(!empty($filter['status'])){

            $this->db->where(
                's.STATUS',
                $filter['status']
            );
        }

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filter['date_from']
            );
        }

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
                $filter['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by(
            's.SALES'
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            's.SALES_DATE',
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

        $rows =
            $this->db
                ->get()
                ->result_array();

        /*
        |--------------------------------------------------------------------------
        | DETAIL LOOP
        |--------------------------------------------------------------------------
        */

        foreach($rows as &$row){

            $details =
                $this->db

                    ->select("

                        d.*,

                        material.material_name
                        AS MATERIAL_NAME

                    ")

                    ->from(
                        'abc_mst_sales_detail d'
                    )

                    ->join(

                        'abc_cd_material material',

                        '

                            material.material COLLATE utf8mb4_unicode_ci =
                            d.MATERIAL COLLATE utf8mb4_unicode_ci

                        ',

                        'left',
                        false

                    )

                    ->where(
                        'd.SALES',
                        $row['SALES']
                    )

                    ->where(
                        'd.PLANT',
                        $row['PLANT']
                    )

                    ->where(
                        'd.DELETED IS NULL',
                        null,
                        false
                    )

                    ->order_by(
                        'd.ID',
                        'ASC'
                    )

                    ->get()

                    ->result_array();

            $row['DETAILS'] =
                $details;
        }

        return $rows;
    }

    public function summary_sales_report(
        $filter = []
    )
    {
        $this->db->select("

            COALESCE(
                SUM(d.TOTAL),
                0
            ) AS TOTAL_SALES,

            COUNT(DISTINCT s.SALES)
            AS TOTAL_DOC,

            COUNT(d.ID)
            AS TOTAL_ITEM,

            COUNT(DISTINCT s.CUSTOMER)
            AS TOTAL_CUSTOMER

        ", false);

        $this->db->from(
            'abc_mst_sales s'
        );

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

        $this->db->where(
            's.DELETED IS NULL',
            null,
            false
        );

        return $this->db
            ->get()
            ->row_array();
    }

    public function count_sales_report(
        $filter = []
    )
    {
        $this->db->from(
            'abc_mst_sales s'
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
                s.CUSTOMER COLLATE utf8mb4_unicode_ci

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
            's.DELETED IS NULL',
            null,
            false
        );

        if(!empty($filter['search'])){

            $this->db->group_start();

            $this->db->like(
                's.SALES',
                $filter['search']
            );

            $this->db->or_like(
                's.SLIP_NO',
                $filter['search']
            );

            $this->db->or_like(
                's.NOTA',
                $filter['search']
            );

            $this->db->or_like(
                'customer.FULL_NAME',
                $filter['search']
            );

            $this->db->group_end();
        }

        if(!empty($filter['plant'])){

            $this->db->where(
                's.PLANT',
                $filter['plant']
            );
        }

        if(!empty($filter['customer'])){

            $this->db->where(
                's.CUSTOMER',
                $filter['customer']
            );
        }

        if(!empty($filter['pembayaran'])){

            $this->db->where(
                's.PEMBAYARAN',
                $filter['pembayaran']
            );
        }

        if(!empty($filter['status'])){

            $this->db->where(
                's.STATUS',
                $filter['status']
            );
        }

        if(!empty($filter['date_from'])){

            $this->db->where(
                'DATE(s.SALES_DATE) >=',
                $filter['date_from']
            );
        }

        if(!empty($filter['date_to'])){

            $this->db->where(
                'DATE(s.SALES_DATE) <=',
                $filter['date_to']
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
