<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportClosingCost_model extends CI_Model {

    public function get_plant_by_user($plant)
    {
        return $this->db
            ->where('HEAD_CODE', 'PLANT')
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
            ->where('CODE !=', '*')
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

    public function get_daily_closing_cost($limit, $start, $filters)
    {
        $plants = $filters['plants'] ?? [];
        $date   = $filters['date'] ?? '';

        if (empty($plants) || !$date) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT 
                a.ymd,
                a.plant,
                p.CODE_NAME AS plant_name,
                a.item,
                i.full_name AS item_name,
                cls.code_name AS class_name,
                a.prd_qty     AS qty,
                a.prd_bw      AS kg,
                a.index_price AS harga,
                a.index_amount AS amount,
                a.market_price AS trend_market,
                a.amount_mp   AS amount_market,
                a.cost_up     AS modal,
                a.moc_amount  AS amount_modal
            FROM abc_cl_cost a
            JOIN abc_cd_item i ON a.item = i.item
            LEFT JOIN abc_cd_code cls ON cls.head_code='GT' AND cls.code=i.item_class
            LEFT JOIN abc_cd_code p   ON p.head_code='PLANT' AND p.code=a.plant
            WHERE a.plant IN ($plantList)
            AND a.ymd = ".$this->db->escape($date)."
            ORDER BY LEFT(a.item,4) DESC, a.item
        ";

        if ($limit > 0) {
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_daily_closing_cost($filters)
    {
        $plants = $filters['plants'] ?? [];
        $date   = $filters['date'] ?? '';

        if (empty($plants) || !$date) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) total
            FROM abc_cl_cost a
            WHERE a.plant IN ($plantList)
            AND a.ymd = ".$this->db->escape($date);

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_daily_closing_cost_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        $date   = $filters['date'] ?? '';

        if (empty($plants) || !$date) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT 
                SUM(prd_qty)    AS qty,
                SUM(prd_bw)     AS bw,
                SUM(moc_amount) AS amount
            FROM abc_cl_cost a
            WHERE a.plant IN ($plantList)
            AND a.ymd = ".$this->db->escape($date);

        return $this->db->query($sql)->row();
    }

    public function get_monthly_closing_cost($limit=0, $start=0, $filters=[])
    {
        $plants = $filters['plants'] ?? [];
        $item   = $filters['item'] ?? '';
        $month  = $filters['month'] ?? '';

        if (empty($plants) || !$month) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT
                a.item,
                i.full_name AS item_name,
                cls.code_name AS class_name,

                SUM(a.prd_qty)      AS qty,
                SUM(a.prd_bw)       AS kg,

                AVG(a.index_price)  AS index_price,
                SUM(a.index_amount) AS index_amount,

                AVG(a.market_price) AS market_price,
                SUM(a.amount_mp)    AS market_amount,

                AVG(a.cost_up)      AS cost_up,
                SUM(a.moc_amount)   AS modal_amount

            FROM abc_abc_cl_cost a

            LEFT JOIN abc_cd_item i
                ON i.item = a.item

            LEFT JOIN abc_cd_code cls
                ON cls.head_code='GT'
                AND cls.code=i.item_class

            WHERE a.plant IN ($plantList)
            AND LEFT(a.ymd,6) = ".$this->db->escape($month);

        if ($item) {
            $sql .= " AND (
                a.item LIKE ".$this->db->escape('%'.$item.'%')."
                OR i.full_name LIKE ".$this->db->escape('%'.$item.'%')."
            )";
        }

        $sql .= "
            GROUP BY
                a.item,
                i.full_name,
                cls.code_name

            ORDER BY a.item
        ";

        if ($limit > 0) {
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_monthly_closing_cost($filters=[])
    {
        $plants = $filters['plants'] ?? [];
        $item   = $filters['item'] ?? '';
        $month  = $filters['month'] ?? '';

        if (empty($plants) || !$month) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT a.plant, LEFT(a.ymd,6), a.item
                FROM abc_cl_cost a
                WHERE a.plant IN ($plantList)
                AND LEFT(a.ymd,6) = ".$this->db->escape($month);

        if ($item) {
            $sql .= " AND a.item LIKE ".$this->db->escape('%'.$item.'%');
        }

        $sql .= "
                GROUP BY a.plant, LEFT(a.ymd,6), a.item
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_closing_cost_grand($filters=[])
    {
        $plants = $filters['plants'] ?? [];
        $item   = $filters['item'] ?? '';
        $month  = $filters['month'] ?? '';

        if (empty($plants) || !$month) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT

                COUNT(DISTINCT a.item) AS total_item,

                SUM(a.prd_qty)      AS qty,
                SUM(a.prd_bw)       AS kg,

                SUM(a.index_amount) AS index_amount,
                SUM(a.amount_mp)    AS market_amount,
                SUM(a.moc_amount)   AS modal_amount

            FROM abc_abc_cl_cost a

            WHERE a.plant IN ($plantList)
            AND LEFT(a.ymd,6) = ".$this->db->escape($month);

        if ($item) {
            $sql .= " AND a.item LIKE ".$this->db->escape('%'.$item.'%');
        }

        return $this->db->query($sql)->row();
    }
}
