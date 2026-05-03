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

    public function get_sales_report($limit, $start, $filters, $order='SALES_DATE', $dir='DESC', $username=null)
    {
        $plants = $filters['plant'] ?? [];

        if (empty($plants)) return [];

        $escapedPlants = array_map([$this->db, 'escape'], $plants);

        // ================= HEADER =================
        $sqlHeader = "
            SELECT a.SALES, a.PLANT
            FROM mst_sales a
            WHERE a.DELETED IS NULL
            AND a.PLANT IN (" . implode(',', $escapedPlants) . ")
        ";

        if (!empty($filters['customer'])) {
            $sqlHeader .= " AND a.CUSTOMER LIKE " . $this->db->escape('%'.$filters['customer'].'%');
        }

        if (!empty($filters['sales'])) {
            $sqlHeader .= " AND a.SALES LIKE " . $this->db->escape('%'.$filters['sales'].'%');
        }

        if (!empty($filters['item'])) {
            $sqlHeader .= "
                AND EXISTS (

                    SELECT 1
                    FROM mst_sales_detail b

                    WHERE b.SALES = a.SALES
                    AND b.PLANT = a.PLANT
                    AND b.ITEM = ".$this->db->escape($filters['item'])."

                )
            ";
        }

        // ================= DATE RANGE =================

        if (!empty($filters['date_from'])) {
            $sqlHeader .= " AND DATE(a.SALES_DATE) >= " . $this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sqlHeader .= " AND DATE(a.SALES_DATE) <= " . $this->db->escape($filters['date_to']);
        }


        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {

            $sqlHeader .= "
                AND (
                    CASE
                        WHEN a.JENIS_PAY = 'LUNAS' THEN 'LUNAS'
                        WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'PAID' THEN 'LUNAS'
                        WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'UNPAID' THEN 'TEMPO'
                        ELSE 'TEMPO'
                    END
                ) = ".$this->db->escape($filters['status'])."
            ";

        }

        $allowed_order = ['SALES','PLANT','SALES_DATE','CUSTOMER','STATUS'];
        if (!in_array($order, $allowed_order)) $order = 'SALES_DATE';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sqlHeader .= " ORDER BY a.$order $dir";

        if ($limit > 0) {
            $sqlHeader .= " LIMIT " . (int)$start . ", " . (int)$limit;
        }

        $headers = $this->db->query($sqlHeader)->result();
        if (empty($headers)) return [];

        // ================= DETAIL =================
        $whereIn = [];
        foreach ($headers as $h) {
            $whereIn[] = "(".$this->db->escape($h->SALES).",".$this->db->escape($h->PLANT).")";
        }

        $sql = "
            SELECT
                a.SALES,
                a.NOTA,
                a.PLANT,
                c.CODE_NAME AS PLANT_NAME,
                a.SALES_DATE,
                a.CUSTOMER,
                a.REMAIN,

                CASE
                    WHEN a.JENIS_PAY = 'LUNAS' THEN 'LUNAS'
                    WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'PAID' THEN 'LUNAS'
                    WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'UNPAID' THEN 'TEMPO'
                    ELSE 'TEMPO'
                END AS STATUS_REPORT,

                d.FULL_NAME AS CUSTOMER_NAME,
                b.SEQ_NO,
                b.ITEM,
                b.QTY,
                b.BERAT,
                b.HARGA,
                b.DISCOUNT,
                b.AMOUNT AS DETAIL_AMOUNT,
                z.FULL_NAME

            FROM mst_sales a
            JOIN mst_sales_detail b
                ON a.SALES=b.SALES
                AND a.PLANT=b.PLANT

            LEFT JOIN cd_code c
                ON c.HEAD_CODE='AJ'
                AND c.CODE=a.PLANT

            LEFT JOIN cd_item z
                ON z.ITEM=b.ITEM

            LEFT JOIN cd_customer d
                ON d.CUST=a.CUSTOMER

            WHERE (a.SALES, a.PLANT) IN (" . implode(',', $whereIn) . ")

            ORDER BY a.$order $dir, b.SEQ_NO ASC
        ";

        return $this->db->query($sql)->result();
    }

    public function count_sales_report($filters, $username=null)
    {
        $plants = $filters['plant'] ?? [];
        if (empty($plants)) return 0;

        $escapedPlants = array_map([$this->db, 'escape'], $plants);

        $sql = "
            SELECT COUNT(DISTINCT a.SALES, a.PLANT) AS total
            FROM mst_sales a
            WHERE a.DELETED IS NULL
            AND a.PLANT IN (" . implode(',', $escapedPlants) . ")
        ";

        if (!empty($filters['customer'])) {
            $sql .= " AND a.CUSTOMER LIKE " . $this->db->escape('%'.$filters['customer'].'%');
        }

        if (!empty($filters['sales'])) {
            $sql .= " AND a.SALES LIKE " . $this->db->escape('%'.$filters['sales'].'%');
        }

        if (!empty($filters['item'])) {
            $sql .= "
                AND EXISTS (

                    SELECT 1
                    FROM mst_sales_detail b

                    WHERE b.SALES = a.SALES
                    AND b.PLANT = a.PLANT
                    AND b.ITEM = ".$this->db->escape($filters['item'])."

                )
            ";
        }

        // ================= DATE RANGE =================

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.SALES_DATE) >= " . $this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.SALES_DATE) <= " . $this->db->escape($filters['date_to']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {

            $sql .= "
                AND (
                    CASE
                        WHEN a.JENIS_PAY = 'LUNAS' THEN 'LUNAS'
                        WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'PAID' THEN 'LUNAS'
                        WHEN a.JENIS_PAY = 'TEMPO' AND a.STATUS = 'UNPAID' THEN 'TEMPO'
                        ELSE 'TEMPO'
                    END
                ) = ".$this->db->escape($filters['status'])."
            ";

        }

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_sales_item($limit, $start, $filters)
    {
        $plants = $filters['plant'] ?? [];

        if (empty($plants)) return [];

        $escapedPlants = array_map(
            [$this->db, 'escape'],
            $plants
        );


        $sql = "

            SELECT

                b.PLANT,
                c.CODE_NAME AS PLANT_NAME,

                b.ITEM,
                i.FULL_NAME AS ITEM_NAME,

                SUM(b.QTY)   AS QTY,
                SUM(b.BERAT) AS BERAT,
                SUM(b.AMOUNT) AS AMOUNT

            FROM mst_sales a

            JOIN mst_sales_detail b
                ON a.SALES=b.SALES
                AND a.PLANT=b.PLANT

            LEFT JOIN cd_code c
                ON c.HEAD_CODE='AJ'
                AND c.CODE=b.PLANT

            LEFT JOIN cd_item i
                ON i.ITEM=b.ITEM

            WHERE a.DELETED IS NULL
            AND b.DELETED IS NULL

            AND a.PLANT IN (".implode(',', $escapedPlants).")

        ";


        // ITEM FILTER
        if (!empty($filters['item'])) {

            $sql .= "
                AND b.ITEM LIKE
                ".$this->db->escape(
                    '%'.$filters['item'].'%'
                )."
            ";
        }


        // DATE
        if (!empty($filters['date1'])) {

            $sql .= "
                AND DATE(a.SALES_DATE) >=
                ".$this->db->escape(
                    $filters['date1']
                )."
            ";
        }

        if (!empty($filters['date2'])) {

            $sql .= "
                AND DATE(a.SALES_DATE) <=
                ".$this->db->escape(
                    $filters['date2']
                )."
            ";
        }


        $sql .= "

            GROUP BY
                b.PLANT,
                b.ITEM

            ORDER BY
                b.PLANT ASC,
                b.ITEM ASC

        ";


        if ($limit > 0) {

            $sql .= "
                LIMIT ".(int)$start.",
                    ".(int)$limit."
            ";
        }


        return $this->db
            ->query($sql)
            ->result();
    }

    public function count_sales_item($filters)
    {
        $plants = $filters['plant'] ?? [];

        if (empty($plants)) return 0;

        $escapedPlants = array_map(
            [$this->db, 'escape'],
            $plants
        );


        $sql = "

            SELECT COUNT(*) AS total

            FROM (

                SELECT
                    b.PLANT,
                    b.ITEM

                FROM mst_sales a

                JOIN mst_sales_detail b
                    ON a.SALES=b.SALES
                    AND a.PLANT=b.PLANT

                WHERE a.DELETED IS NULL
                AND b.DELETED IS NULL

                AND a.PLANT IN (".implode(',', $escapedPlants).")

        ";


        if (!empty($filters['item'])) {

            $sql .= "
                AND b.ITEM LIKE
                ".$this->db->escape(
                    '%'.$filters['item'].'%'
                )."
            ";
        }


        if (!empty($filters['date1'])) {

            $sql .= "
                AND DATE(a.SALES_DATE) >=
                ".$this->db->escape(
                    $filters['date1']
                )."
            ";
        }


        if (!empty($filters['date2'])) {

            $sql .= "
                AND DATE(a.SALES_DATE) <=
                ".$this->db->escape(
                    $filters['date2']
                )."
            ";
        }


        $sql .= "

                GROUP BY
                    b.PLANT,
                    b.ITEM

            ) x

        ";


        return (int)
            $this->db
            ->query($sql)
            ->row()
            ->total;
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
