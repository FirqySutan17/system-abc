<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportClosingInventoryPrice_model extends CI_Model {

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

    public function get_material_list()
    {
        return $this->db
            ->select('material, material_name')
            ->from('abc_cd_material')
            ->order_by('material', 'ASC')
            ->get()
            ->result();
    }

    private function convert_date($date)
    {
        if (!$date) return null;
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

    public function get_daily_inventory_price($limit, $start, $filters, $order, $dir)
    {
        $plants = $filters['plants'] ?? [];
        $ymd    = $filters['ymd'] ?? '';

        if (!$ymd || empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT 
                TBL.plant,
                c.CODE_NAME AS plant_name,
                TBL.material,
                m.material_name,

                SUM(bg_qty)     AS bg_qty,
                SUM(bg_bw)      AS bg_bw,
                SUM(bg_amount)  AS bg_amount,
                SUM(in_qty)     AS in_qty,
                SUM(in_bw)      AS in_bw,
                SUM(in_amount)  AS in_amount,
                SUM(out_qty)    AS out_qty,
                SUM(out_bw)     AS out_bw,
                SUM(out_amount) AS out_amount,
                SUM(end_qty)    AS end_qty,
                SUM(end_bw)     AS end_bw,
                SUM(end_amount) AS end_amount

            FROM cl_inventory_price TBL
            LEFT JOIN abc_cd_material m ON m.material = TBL.material
            LEFT JOIN abc_cd_code c ON c.CODE = TBL.plant AND c.HEAD_CODE = 'PLANT'

            WHERE TBL.ymd = ".$this->db->escape($ymd)."
            AND TBL.plant IN ($plantList)

            GROUP BY TBL.plant, c.CODE_NAME, TBL.material, m.material_name
            ORDER BY {$order} {$dir}
        ";

        if ($limit > 0) {
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_daily_inventory_price($filters)
    {
        $plants = $filters['plants'] ?? [];
        $ymd    = $filters['ymd'] ?? '';

        if (!$ymd || empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT plant, material
                FROM cl_inventory_price
                WHERE ymd = ".$this->db->escape($ymd)."
                AND plant IN ($plantList)
                GROUP BY plant, material
            ) X
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_daily_inventory_price_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        $ymd    = $filters['ymd'] ?? '';

        if (!$ymd || empty($plants)) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT 
                SUM(bg_qty)     AS bg_qty,
                SUM(bg_bw)      AS bg_bw,
                SUM(bg_amount)  AS bg_amount,
                SUM(in_qty)     AS in_qty,
                SUM(in_bw)      AS in_bw,
                SUM(in_amount)  AS in_amount,
                SUM(out_qty)    AS out_qty,
                SUM(out_bw)     AS out_bw,
                SUM(out_amount) AS out_amount,
                SUM(end_qty)    AS end_qty,
                SUM(end_bw)     AS end_bw,
                SUM(end_amount) AS end_amount
            FROM cl_inventory_price
            WHERE ymd = ".$this->db->escape($ymd)."
            AND plant IN ($plantList)
        ";

        return $this->db->query($sql)->row();
    }

    public function get_monthly_inventory_price($limit=0, $start=0, $filters=[], $order='material', $dir='ASC')
    {
        $plants   = $filters['plants'] ?? [];
        $material = $filters['material'] ?? '';
        $month    = $filters['month'] ?? '';

        if (empty($plants) || !$month) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $where = "
            WHERE cip.plant IN ($plantList)
            AND LEFT(cip.ymd,6) = ".$this->db->escape($month)."
        ";

        if ($material) {
            $where .= "
                AND (
                    cip.material LIKE ".$this->db->escape('%'.$material.'%')."
                    OR m.material_name LIKE ".$this->db->escape('%'.$material.'%')."
                )
            ";
        }

        $sql = "
            SELECT 
                cip.plant,
                c.CODE_NAME AS plant_name,
                cip.material,
                m.material_name,

                SUM(cip.bg_qty)     AS bg_qty,
                SUM(cip.bg_bw)      AS bg_bw,
                SUM(cip.bg_amount)  AS bg_amount,

                SUM(cip.in_qty)     AS in_qty,
                SUM(cip.in_bw)      AS in_bw,
                SUM(cip.in_amount)  AS in_amount,

                SUM(cip.out_qty)    AS out_qty,
                SUM(cip.out_bw)     AS out_bw,
                SUM(cip.out_amount) AS out_amount,

                SUM(cip.end_qty)    AS end_qty,
                SUM(cip.end_bw)     AS end_bw,
                SUM(cip.end_amount) AS end_amount

            FROM cl_inventory_price cip
            LEFT JOIN abc_cd_material m ON m.material = cip.material
            LEFT JOIN abc_cd_code c ON c.CODE = cip.plant AND c.HEAD_CODE = 'PLANT'

            $where

            GROUP BY cip.plant, c.CODE_NAME, cip.material, m.material_name
            ORDER BY $order $dir
        ";

        if ($limit > 0) {
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_monthly_inventory_price($filters=[])
    {
        $plants   = $filters['plants'] ?? [];
        $material = $filters['material'] ?? '';
        $month    = $filters['month'] ?? '';

        if (empty($plants) || !$month) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $where = "
            WHERE plant IN ($plantList)
            AND LEFT(ymd,6) = ".$this->db->escape($month)."
        ";

        if ($material) {
            $where .= " AND material LIKE ".$this->db->escape('%'.$material.'%');
        }

        $sql = "
            SELECT COUNT(DISTINCT CONCAT(plant,material)) AS total
            FROM cl_inventory_price
            $where
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_inventory_price_grand($filters=[])
    {
        $plants = $filters['plants'] ?? [];
        $month  = $filters['month'] ?? '';

        if (empty($plants) || !$month) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT 
                SUM(bg_qty)     AS bg_qty,
                SUM(bg_bw)      AS bg_bw,
                SUM(bg_amount)  AS bg_amount,
                SUM(in_qty)     AS in_qty,
                SUM(in_bw)      AS in_bw,
                SUM(in_amount)  AS in_amount,
                SUM(out_qty)    AS out_qty,
                SUM(out_bw)     AS out_bw,
                SUM(out_amount) AS out_amount,
                SUM(end_qty)    AS end_qty,
                SUM(end_bw)     AS end_bw,
                SUM(end_amount) AS end_amount
            FROM cl_inventory_price
            WHERE plant IN ($plantList)
            AND LEFT(ymd,6) = ".$this->db->escape($month);

        return $this->db->query($sql)->row();
    }
}
