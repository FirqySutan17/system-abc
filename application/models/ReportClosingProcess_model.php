<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportClosingProcess_model extends CI_Model {

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
            ->where('CODE <>', '*')
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

    public function get_daily_closing_pl($limit, $start, $filters, $order = 'ymd', $dir = 'DESC')
    {
        // =========================
        // BASE QUERY
        // =========================
        $sql = "
            SELECT
                a.plant,
                c.CODE_NAME AS plant_name,
                a.ymd,
                a.account,
                a.item,
                SUM(a.amount) AS amount
            FROM cl_pl a
            LEFT JOIN abc_cd_code c
              ON c.HEAD_CODE = 'PLANT'
             AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = a.plant
            WHERE 1=1
        ";

        // =========================
        // FILTERS
        // =========================
        if (!empty($filters['plant'])) {
            $sql .= " AND a.plant = " . $this->db->escape($filters['plant']);
        }

        if (!empty($filters['account'])) {
            $sql .= " AND a.account LIKE " . $this->db->escape('%'.$filters['account'].'%');
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND a.ymd >= " . $this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND a.ymd <= " . $this->db->escape($filters['date_to']);
        }

        // =========================
        // GROUPING
        // =========================
        $sql .= "
            GROUP BY
                a.plant,
                a.ymd,
                a.account,
                a.item
        ";

        // =========================
        // ORDERING (WHITELIST)
        // =========================
        $allowed_order = [
            'ymd',
            'account',
            'item',
            'amount'
        ];

        if (!in_array($order, $allowed_order)) {
            $order = 'ymd';
        }

        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $sql .= " ORDER BY $order $dir ";

        // =========================
        // PAGINATION
        // =========================
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$start . ", " . (int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    /* =========================
     * COUNT DAILY CLOSING P/L
     * ========================= */

    public function count_daily_closing_pl($filters)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                SELECT
                    a.plant,
                    a.ymd,
                    a.account,
                    a.item
                FROM cl_pl a
                WHERE 1=1
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND a.plant = " . $this->db->escape($filters['plant']);
        }

        if (!empty($filters['account'])) {
            $sql .= " AND a.account LIKE " . $this->db->escape('%'.$filters['account'].'%');
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND a.ymd >= " . $this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND a.ymd <= " . $this->db->escape($filters['date_to']);
        }

        $sql .= "
                GROUP BY
                    a.plant,
                    a.ymd,
                    a.account,
                    a.item
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_closing_pl($limit, $start, $filters, $order='ym', $dir='DESC')
    {
        $sql = "
            SELECT
                a.plant,
                SUBSTRING(a.ymd,1,6) AS ym,
                a.account,
                a.item,
                SUM(a.amount) AS amount
            FROM cl_pl a
            WHERE 1=1
        ";

        /* ================= FILTER ================= */

        if (!empty($filters['plant'])) {
            $sql .= " AND a.plant = " . $this->db->escape($filters['plant']);
        }

        if (!empty($filters['month_from'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6) >= " . $this->db->escape($filters['month_from']);
        }

        if (!empty($filters['month_to'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6) <= " . $this->db->escape($filters['month_to']);
        }

        /* ================= GROUP ================= */

        $sql .= "
            GROUP BY
                a.plant,
                ym,
                a.account,
                a.item
        ";

        /* ================= ORDER ================= */

        $allowed_order = ['ym','account','item','amount'];
        if (!in_array($order, $allowed_order)) {
            $order = 'ym';
        }

        $dir = strtoupper($dir)==='ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY $order $dir ";

        /* ================= LIMIT ================= */

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$start . ", " . (int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_monthly_closing_pl($filters)
    {
        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT
                    a.plant,
                    SUBSTRING(a.ymd,1,6) AS ym,
                    a.account,
                    a.item
                FROM cl_pl a
                WHERE 1=1
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND a.plant = " . $this->db->escape($filters['plant']);
        }

        if (!empty($filters['month_from'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6) >= " . $this->db->escape($filters['month_from']);
        }

        if (!empty($filters['month_to'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6) <= " . $this->db->escape($filters['month_to']);
        }

        $sql .= "
                GROUP BY
                    a.plant,
                    ym,
                    a.account,
                    a.item
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_grand_total($filters)
    {
        $this->db->select("
            SUM(prd_qty)    AS qty,
            SUM(prd_bw)     AS bw,
            SUM(moc_amount) AS amount
        ");
        $this->db->from('cl_cost');

        if (!empty($filters['plant'])) {
            $this->db->where('plant', $filters['plant']);
        }

        if (!empty($filters['month_from'])) {
            $this->db->where("LEFT(ymd,6) >=", $filters['month_from']);
        }

        if (!empty($filters['month_to'])) {
            $this->db->where("LEFT(ymd,6) <=", $filters['month_to']);
        }

        return $this->db->get()->row();
    }
}
