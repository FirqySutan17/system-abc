<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{

    public function summary_po()
{
    return $this->db
        ->query("
            SELECT
                COUNT(*) total_po,
                SUM(TOTAL) total_nominal
            FROM abc_mst_po
            WHERE MONTH(PO_DATE)=MONTH(CURDATE())
        ")
        ->row_array();
}

public function summary_receive()
{
    return $this->db
        ->query("
            SELECT
                COUNT(*) total_receive
            FROM abc_mst_receive
        ")
        ->row_array();
}

public function summary_sales()
{
    return $this->db
        ->query("
            SELECT
                COUNT(*) total_sales,
                SUM(AMOUNT) omzet
            FROM abc_mst_sales
        ")
        ->row_array();
}

public function top_material()
{
    return $this->db
        ->query("
            SELECT
                d.MATERIAL,

                m.MATERIAL_NAME,

                SUM(d.JUMLAH) qty

            FROM abc_mst_receive_detail d

            LEFT JOIN abc_cd_material m
                ON m.MATERIAL = d.MATERIAL

            GROUP BY d.MATERIAL

            ORDER BY qty DESC

            LIMIT 10
        ")
        ->result_array();
}

public function monthly_trend()
{
    $sql = "

        SELECT
            bulan,

            SUM(po_total) po_total,

            SUM(receive_total) receive_total,

            SUM(sales_total) sales_total

        FROM (

            /*
            |--------------------------------------------------------------------------
            | PO
            |--------------------------------------------------------------------------
            */

            SELECT
                DATE_FORMAT(PO_DATE,'%Y-%m') bulan,

                SUM(TOTAL) po_total,

                0 receive_total,

                0 sales_total

            FROM abc_mst_po

            GROUP BY bulan

            UNION ALL

            /*
            |--------------------------------------------------------------------------
            | RECEIVE
            |--------------------------------------------------------------------------
            */

            SELECT
                DATE_FORMAT(RECEIVE_DATE,'%Y-%m') bulan,

                0 po_total,

                COUNT(*) receive_total,

                0 sales_total

            FROM abc_mst_receive

            GROUP BY bulan

            UNION ALL

            /*
            |--------------------------------------------------------------------------
            | SALES
            |--------------------------------------------------------------------------
            */

            SELECT
                DATE_FORMAT(SALES_DATE,'%Y-%m') bulan,

                0 po_total,

                0 receive_total,

                SUM(AMOUNT) sales_total

            FROM abc_mst_sales

            GROUP BY bulan

        ) x

        GROUP BY bulan

        ORDER BY bulan ASC

    ";

    return $this->db
        ->query($sql)
        ->result_array();
}

    /* ================= KPI ================= */
    public function get_kpi($year, $plant = null)
    {
        $this->db->select("
            SUM(s.AMOUNT) AS total_sales,
            SUM(s.DP_AMOUNT) AS total_dp,
            SUM(s.REMAIN) AS total_outstanding,
            COUNT(DISTINCT s.CUSTOMER) AS total_customer
        ");
        $this->db->from('mst_sales s');
        $this->db->where('s.DELETED IS NULL');
        $this->db->where('YEAR(s.SALES_DATE)', $year);

        if (!empty($plant)) {
            $this->db->where('s.PLANT', $plant);
        }

        return $this->db->get()->row();
    }


    /* ================= SALES TREND ================= */
    public function get_sales_trend($year, $plant = null)
    {
        $this->db->select("
            DATE_FORMAT(s.SALES_DATE, '%Y-%m') AS ym,
            SUM(s.AMOUNT) AS total_sales
        ");
        $this->db->from('mst_sales s');
        $this->db->where('s.DELETED IS NULL');
        $this->db->where('YEAR(s.SALES_DATE)', $year);

        if (!empty($plant)) {
            $this->db->where('s.PLANT', $plant);
        }

        $this->db->group_by('ym');
        $this->db->order_by('ym', 'ASC');

        return $this->db->get()->result();
    }

    public function get_sales_per_plant($year)
    {
        $this->db->select("
            s.PLANT,
            c.CODE_NAME AS plant_name,
            SUM(s.AMOUNT) AS total_sales
        ");
        $this->db->from('mst_sales s');
        $this->db->join(
            'cd_code c',
            'c.CODE = s.PLANT AND c.HEAD_CODE = "AJ" AND c.CODE != "*"',
            'inner'
        );

        $this->db->where('s.DELETED IS NULL');
        $this->db->where('YEAR(s.SALES_DATE)', $year);
        $this->db->where('s.PLANT !=', '*'); // safety layer

        $this->db->group_by(['s.PLANT', 'c.CODE_NAME']);
        $this->db->order_by('total_sales', 'DESC');

        return $this->db->get()->result();
    }

    /* ================= TOP ITEM ================= */
    public function get_top_items($year, $plant = null)
    {
        $this->db->select("
            d.ITEM,
            i.FULL_NAME AS item_name,
            SUM(CAST(d.AMOUNT AS DECIMAL(20,2))) AS total_sales
        ");
        $this->db->from('mst_sales_detail d');
        $this->db->join('mst_sales h', 'd.SALES = h.SALES AND d.PLANT = h.PLANT');
        $this->db->join('cd_item i', 'i.ITEM = d.ITEM', 'left');

        $this->db->where('d.DELETED IS NULL');
        $this->db->where('h.DELETED IS NULL');
        $this->db->where('YEAR(h.SALES_DATE)', $year);

        if (!empty($plant)) {
            $this->db->where('h.PLANT', $plant);
        }

        $this->db->group_by(['d.ITEM', 'i.FULL_NAME']);
        $this->db->order_by('total_sales', 'DESC');
        $this->db->limit(10);

        return $this->db->get()->result();
    }

    public function get_plant_list()
    {
        $this->db->select("
            c.CODE,
            c.CODE_NAME
        ");
        $this->db->from('cd_code c');
        $this->db->where('c.HEAD_CODE', 'AJ');
        $this->db->where('c.CODE !=', '*'); // exclude *
        $this->db->order_by('c.CODE_NAME', 'ASC');

        return $this->db->get()->result();
    }

}
