<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportClosingPl_model extends CI_Model {

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

    public function get_daily_closing_pl($limit, $start, $filters, $order='ymd', $dir='DESC')
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'],$plants));

        $sql = "
            SELECT
                a.plant,
                p.CODE_NAME AS plant_name,
                a.ymd,

                a.account_cd,
                acc.ACCOUNT_NAME,

                SUM(a.amount) AS amount

            FROM cl_pl a

            LEFT JOIN cd_code p
                ON p.HEAD_CODE='AJ'
                AND p.CODE=a.plant

            LEFT JOIN cd_account acc
                ON acc.ACCOUNT=a.account_cd

            WHERE a.plant IN ($plantList)
        ";

        /* DATE FILTER */
        if (!empty($filters['date'])) {
            $sql .= " AND a.ymd=".$this->db->escape($filters['date']);
        }

        /* ACCOUNT FILTER */
        if (!empty($filters['account'])) {
            $sql .= " AND a.account_cd LIKE ".$this->db->escape('%'.$filters['account'].'%');
        }

        $sql .= "
            GROUP BY
                a.plant,
                p.CODE_NAME,
                a.ymd,
                a.account_cd,
                acc.ACCOUNT_NAME
        ";

        /* ORDER */
        $allowed_order=['ymd','account_cd','amount'];
        if(!in_array($order,$allowed_order)){
            $order='account_cd';
        }

        $dir=strtoupper($dir)==='ASC'?'ASC':'DESC';

        $sql .= " ORDER BY $order $dir";

        if($limit>0){
            $sql .= " LIMIT ".(int)$start.",".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_daily_closing_pl($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'],$plants));

        $sql="
            SELECT COUNT(*) total
            FROM(
                SELECT
                    a.plant,
                    a.ymd,
                    a.account_cd
                FROM cl_pl a
                WHERE a.plant IN ($plantList)
        ";

        if(!empty($filters['date'])){
            $sql.=" AND a.ymd=".$this->db->escape($filters['date']);
        }

        if(!empty($filters['account'])){
            $sql.=" AND a.account_cd LIKE ".$this->db->escape('%'.$filters['account'].'%');
        }

        $sql.="
                GROUP BY
                    a.plant,
                    a.ymd,
                    a.account_cd
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_daily_closing_pl_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return (object)['amount'=>0];

        $plantList = implode(',', array_map([$this->db,'escape'],$plants));

        $sql="
            SELECT SUM(a.amount) AS amount
            FROM cl_pl a
            WHERE a.plant IN ($plantList)
        ";

        if(!empty($filters['date'])){
            $sql.=" AND a.ymd=".$this->db->escape($filters['date']);
        }

        if(!empty($filters['account'])){
            $sql.=" AND a.account_cd LIKE ".$this->db->escape('%'.$filters['account'].'%');
        }

        return $this->db->query($sql)->row();
    }

    public function get_monthly_closing_pl($limit, $start, $filters, $order='ym', $dir='DESC')
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT
                a.plant,
                p.CODE_NAME AS plant_name,
                SUBSTRING(a.ymd,1,6) AS ym,

                a.account_cd,
                acc.ACCOUNT_NAME,

                SUM(a.amount) AS amount

            FROM cl_pl a

            LEFT JOIN cd_code p
                ON p.HEAD_CODE='AJ'
                AND p.CODE=a.plant

            LEFT JOIN cd_account acc
                ON acc.ACCOUNT=a.account_cd

            WHERE a.plant IN ($plantList)
        ";

        /* MONTH FILTER */
        if (!empty($filters['month'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6)=" . $this->db->escape($filters['month']);
        }

        $sql .= "
            GROUP BY
                a.plant,
                p.CODE_NAME,
                ym,
                a.account_cd,
                acc.ACCOUNT_NAME
        ";

        /* ORDER WHITELIST */
        $allowed_order = ['ym','account_cd','amount'];

        if (!in_array($order,$allowed_order)) {
            $order='ym';
        }

        $dir = strtoupper($dir)==='ASC'?'ASC':'DESC';

        $sql .= " ORDER BY $order $dir ";

        if ($limit>0) {
            $sql .= " LIMIT ".(int)$start.",".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_monthly_closing_pl($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) total FROM (
                SELECT
                    a.plant,
                    SUBSTRING(a.ymd,1,6) AS ym,
                    a.account_cd
                FROM cl_pl a
                WHERE a.plant IN ($plantList)
        ";

        if (!empty($filters['month'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6)=".$this->db->escape($filters['month']);
        }

        $sql .= "
                GROUP BY
                    a.plant,
                    ym,
                    a.account_cd
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_closing_pl_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return (object)['amount'=>0];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT SUM(a.amount) amount
            FROM cl_pl a
            WHERE a.plant IN ($plantList)
        ";

        if (!empty($filters['month'])) {
            $sql .= " AND SUBSTRING(a.ymd,1,6)=".$this->db->escape($filters['month']);
        }

        return $this->db->query($sql)->row();
    }
}
