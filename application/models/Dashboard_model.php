<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{

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
