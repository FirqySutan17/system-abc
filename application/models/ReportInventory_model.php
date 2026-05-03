<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportInventory_model extends CI_Model {

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

    public function get_po_report($limit, $start, $filters, $order = 'PO_DATE', $dir = 'DESC')
    {
        // =========================
        // STEP 1: HEADER (PO ONLY)
        // =========================
        $sqlHeader = "
            SELECT a.PO, a.PLANT
            FROM mst_po a
            WHERE a.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {
            $sqlHeader .= " AND a.PLANT = " . $this->db->escape($filters['plant']);
        }
        if (!empty($filters['supplier'])) {
            $sqlHeader .= " AND a.SUPPLIER LIKE " . $this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['po'])) {
            $sqlHeader .= " AND a.PO LIKE " . $this->db->escape('%'.$filters['po'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sqlHeader .= " AND DATE(a.PO_DATE) >= " . $this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sqlHeader .= " AND DATE(a.PO_DATE) <= " . $this->db->escape($filters['date_to']);
        }

        $allowed_order = ['PO', 'PLANT', 'PO_DATE', 'SUPPLIER'];
        if (!in_array($order, $allowed_order)) $order = 'PO_DATE';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sqlHeader .= " ORDER BY a.$order $dir";

        if ($limit > 0) {
            $sqlHeader .= " LIMIT " . (int)$start . ", " . (int)$limit;
        }

        $headers = $this->db->query($sqlHeader)->result();
        if (empty($headers)) return [];

        // =========================
        // STEP 2: DETAIL JOIN
        // =========================
        $whereIn = [];
        foreach ($headers as $h) {
            $whereIn[] = "(" .
                $this->db->escape($h->PO) . "," .
                $this->db->escape($h->PLANT) .
            ")";
        }

        $sql = "
            SELECT 
                a.PO,
                a.PLANT,
                c.CODE_NAME AS PLANT_NAME,
                a.PO_DATE,
                a.SUPPLIER,
                d.FULL_NAME AS SUPPLIER_NAME,
                b.MATERIAL,
                e.MATERIAL_NAME,
                b.JUMLAH,
                b.BERAT,
                b.HARGA,
                b.TOTAL
            FROM mst_po a
            JOIN mst_po_detail b 
                ON a.PO = b.PO AND a.PLANT = b.PLANT
            LEFT JOIN cd_code c 
                ON c.HEAD_CODE = 'AJ' 
                AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = a.PLANT
            LEFT JOIN cd_customer d 
                ON d.CUST COLLATE utf8mb4_uca1400_ai_ci = a.SUPPLIER
            LEFT JOIN cd_material e 
                ON e.MATERIAL COLLATE utf8mb4_uca1400_ai_ci = b.MATERIAL
            WHERE (a.PO, a.PLANT) IN (" . implode(',', $whereIn) . ")
            ORDER BY a.$order $dir
        ";

        return $this->db->query($sql)->result();
    }

    public function count_po_report($filters)
    {
        $sql = "
            SELECT COUNT(DISTINCT a.PO, a.PLANT) AS total
            FROM mst_po a
            WHERE a.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND a.PLANT = " . $this->db->escape($filters['plant']);
        }
        if (!empty($filters['supplier'])) {
            $sql .= " AND a.SUPPLIER LIKE " . $this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['po'])) {
            $sql .= " AND a.PO LIKE " . $this->db->escape('%'.$filters['po'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.PO_DATE) >= " . $this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.PO_DATE) <= " . $this->db->escape($filters['date_to']);
        }

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_receive_report($limit, $start, $filters, $order = 'RECEIVE_DATE', $dir = 'DESC')
    {
        // =====================
        // SUBQUERY HEADER RECEIVE
        // =====================
        $sql = "
            SELECT r.RECEIVE
            FROM mst_receive r
            WHERE r.DELETED IS NULL
        ";

        // ===== FILTER HEADER =====
        if (!empty($filters['plant'])) {
            $sql .= " AND r.PLANT = " . $this->db->escape($filters['plant']);
        }
        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER LIKE " . $this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['receive'])) {
            $sql .= " AND (r.PO LIKE " . $this->db->escape('%'.$filters['receive'].'%') . "
                        OR r.RECEIVE LIKE " . $this->db->escape('%'.$filters['receive'].'%') . ")";
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= " . $this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= " . $this->db->escape($filters['date_to']);
        }

        // ===== ORDER HEADER =====
        $allowed_order = ['RECEIVE', 'PLANT', 'RECEIVE_DATE', 'PO', 'SUPPLIER'];
        if (!in_array($order, $allowed_order)) $order = 'RECEIVE_DATE';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY r.$order $dir";

        // ===== LIMIT HEADER (INI KUNCI) =====
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$start . ", " . (int)$limit;
        }

        $receiveHeaders = $this->db->query($sql)->result_array();
        if (empty($receiveHeaders)) return [];

        $receiveList = array_column($receiveHeaders, 'RECEIVE');
        $receiveIn   = implode(',', array_map([$this->db, 'escape'], $receiveList));

        // =====================
        // QUERY DETAIL
        // =====================
        $sqlDetail = "
            SELECT
                r.RECEIVE,
                r.PLANT,
                c.CODE_NAME AS PLANT_NAME,
                r.RECEIVE_DATE,
                r.PO,
                r.SUPPLIER,
                cust.FULL_NAME AS SUPPLIER_NAME,

                d.MATERIAL,
                mat.MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.HARGA,
                d.TOTAL
            FROM mst_receive r
            LEFT JOIN mst_receive_detail d 
                ON d.RECEIVE = r.RECEIVE 
                AND d.PLANT = r.PLANT
                AND d.DELETED IS NULL
            LEFT JOIN cd_code c 
                ON c.HEAD_CODE = 'AJ'
                AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = r.PLANT
            LEFT JOIN cd_customer cust 
                ON cust.CUST COLLATE utf8mb4_uca1400_ai_ci = r.SUPPLIER
            LEFT JOIN cd_material mat 
                ON mat.MATERIAL COLLATE utf8mb4_uca1400_ai_ci = d.MATERIAL
            WHERE r.RECEIVE IN ($receiveIn)
            ORDER BY r.$order $dir
        ";

        return $this->db->query($sqlDetail)->result();
    }

    public function count_receive_report($filters)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM mst_receive r
            WHERE r.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND r.PLANT = " . $this->db->escape($filters['plant']);
        }
        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER LIKE " . $this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['receive'])) {
            $sql .= " AND (r.PO LIKE " . $this->db->escape('%'.$filters['receive'].'%') . "
                        OR r.RECEIVE LIKE " . $this->db->escape('%'.$filters['receive'].'%') . ")";
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= " . $this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= " . $this->db->escape($filters['date_to']);
        }

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_receive_lb_report($limit, $start, $filters, $order, $dir)
    {
        // Ambil RECEIVE yang akan ditampilkan di page ini
        $sqlReceive = "
            SELECT DISTINCT r.RECEIVE, r.PLANT
            FROM mst_receive_lb r
            WHERE r.DELETED IS NULL
        ";

        // FILTER sama persis
        if (!empty($filters['plant'])) {

            $plants = (array)$filters['plant'];
            $plantIn = implode(',', array_map([$this->db, 'escape'], $plants));

            $sqlReceive .= " AND r.PLANT IN ($plantIn)";
        }
        if (!empty($filters['supplier'])) {
            $sqlReceive .= " AND r.SUPPLIER LIKE ".$this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['receive'])) {
            $sqlReceive .= " AND r.RECEIVE LIKE ".$this->db->escape('%'.$filters['receive'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sqlReceive .= " AND DATE(r.RECEIVE_DATE) >= ".$this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sqlReceive .= " AND DATE(r.RECEIVE_DATE) <= ".$this->db->escape($filters['date_to']);
        }

        $allowed_order = ['RECEIVE','PLANT','RECEIVE_DATE'];
        if (!in_array($order, $allowed_order)) {
            $order = 'RECEIVE_DATE';
        }
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sqlReceive .= " ORDER BY r.$order $dir";

        if ($limit > 0) {
            $sqlReceive .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        $receiveList = $this->db->query($sqlReceive)->result_array();
        if (!$receiveList) return [];

        $composite = [];
        foreach ($receiveList as $r) {
            $receive = $this->db->escape($r['RECEIVE']);
            $plant   = $this->db->escape($r['PLANT']);
            $composite[] = "($receive,$plant)";
        }

        $in = implode(',', $composite);

        // Ambil semua detail RECEIVE tsb
        $sql = "
            SELECT
                r.*,
                c.CODE_NAME AS PLANT_NAME,
                cust.FULL_NAME AS SUPPLIER_NAME
            FROM mst_receive_lb r
            LEFT JOIN cd_code c 
                ON c.HEAD_CODE = 'AJ' 
                AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = r.PLANT
            LEFT JOIN cd_customer cust 
                ON cust.CUST COLLATE utf8mb4_uca1400_ai_ci = r.SUPPLIER
            WHERE r.DELETED IS NULL
            AND (r.RECEIVE, r.PLANT) IN ($in)
        ";

        $sql .= " ORDER BY r.$order $dir";

        return $this->db->query($sql)->result();
    }

    public function get_receive_lb_grand_total($filters)
    {
        $sql = "
            SELECT
                SUM(r.WEIGHT) AS total_berat,
                SUM(r.AMOUNT) AS total_amount
            FROM mst_receive_lb r
            WHERE r.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {

            $plants = (array)$filters['plant'];
            $plantIn = implode(',', array_map([$this->db, 'escape'], $plants));

            $sql .= " AND r.PLANT IN ($plantIn)";
        }
        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER LIKE ".$this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['receive'])) {
            $sql .= " AND r.RECEIVE LIKE ".$this->db->escape('%'.$filters['receive'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= ".$this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= ".$this->db->escape($filters['date_to']);
        }

        return $this->db->query($sql)->row();
    }

    public function count_receive_lb_report($filters)
    {
        $sql = "
            SELECT COUNT(DISTINCT r.RECEIVE, r.PLANT) AS total
            FROM mst_receive_lb r
            WHERE r.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {

            $plants = (array)$filters['plant'];
            $plantIn = implode(',', array_map([$this->db, 'escape'], $plants));

            $sql .= " AND r.PLANT IN ($plantIn)";
        }
        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER LIKE ".$this->db->escape('%'.$filters['supplier'].'%');
        }
        if (!empty($filters['receive'])) {
            $sql .= " AND r.RECEIVE LIKE ".$this->db->escape('%'.$filters['receive'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= ".$this->db->escape($filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= ".$this->db->escape($filters['date_to']);
        }

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_material_balance($limit=0, $start=0, $filters=[])
    {
        $plant     = $filters['plant'] ?? '';
        $material  = $filters['material'] ?? '';
        $date_from = $filters['date_from'] ?? '';
        $date_to   = $filters['date_to'] ?? '';

        $date_from_db = !empty($date_from) ? str_replace('-', '', $date_from) : '';
        $date_to_db   = !empty($date_to)   ? str_replace('-', '', $date_to)   : '';

        $sql = "
            SELECT 
                tbl.plant,
                c.CODE_NAME AS plant_name,
                tbl.material,
                tbl.material_name,
                SUM(BEGIN_qty) AS BEGIN_qty,
                SUM(BEGIN_bw)  AS BEGIN_bw,
                SUM(in_qty)    AS in_qty,
                SUM(in_bw)     AS in_bw,
                SUM(out_qty)   AS out_qty,
                SUM(out_bw)    AS out_bw,
                SUM(BEGIN_qty) + SUM(in_qty) - SUM(out_qty) AS END_qty,
                SUM(BEGIN_bw)  + SUM(in_bw)  - SUM(out_bw)  AS END_bw
            FROM (
                -- SALDO AWAL (SEBELUM PERIODE)
                SELECT 
                    plant, material, material_name,
                    SUM(in_jumlah) AS BEGIN_qty,
                    SUM(in_berat)  AS BEGIN_bw,
                    0 AS in_qty, 0 AS in_bw,
                    0 AS out_qty, 0 AS out_bw
                FROM uv_mt_inventory
                WHERE 1=1
        ";

        if($plant) {
            $sql .= " AND plant = ".$this->db->escape($plant);
        }

        if($date_from_db) {
            $sql .= " AND transaction_date < ".$this->db->escape($date_from_db);
        }

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name

            UNION ALL

            -- TRANSAKSI DALAM PERIODE
            SELECT 
                plant, material, material_name,
                0 AS BEGIN_qty, 0 AS BEGIN_bw,
                SUM(in_jumlah)  AS in_qty,
                SUM(in_berat)   AS in_bw,
                SUM(out_jumlah) AS out_qty,
                SUM(out_berat)  AS out_bw
            FROM uv_mt_inventory
            WHERE 1=1
        ";

        if($plant) {
            $sql .= " AND plant = ".$this->db->escape($plant);
        }

        if($date_from_db && $date_to_db) {
            $sql .= " AND transaction_date BETWEEN "
                . $this->db->escape($date_from_db)
                . " AND "
                . $this->db->escape($date_to_db);
        }

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name
            ) AS tbl
            LEFT JOIN cd_code c ON c.CODE = tbl.plant AND c.HEAD_CODE = 'AJ'
            GROUP BY tbl.plant, c.CODE_NAME, tbl.material, tbl.material_name
            ORDER BY tbl.material ASC
        ";

        if($limit > 0){
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_material_balance($filters=[])
    {
        $plant     = $filters['plant'] ?? '';
        $material  = $filters['material'] ?? '';
        $date_from = $filters['date_from'] ?? '';
        $date_to   = $filters['date_to'] ?? '';

        $date_from_db = !empty($date_from) ? str_replace('-', '', $date_from) : '';
        $date_to_db   = !empty($date_to)   ? str_replace('-', '', $date_to)   : '';

        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT plant, material, material_name
                FROM (
                    SELECT plant, material, material_name
                    FROM uv_mt_inventory
                    WHERE 1=1
        ";

        if($plant){
            $sql .= " AND plant = ".$this->db->escape($plant);
        }

        if($date_from_db){
            $sql .= " AND transaction_date < ".$this->db->escape($date_from_db);
        }

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name

                UNION

                SELECT plant, material, material_name
                FROM uv_mt_inventory
                WHERE 1=1
        ";

        if($plant){
            $sql .= " AND plant = ".$this->db->escape($plant);
        }

        if($date_from_db && $date_to_db){
            $sql .= " AND transaction_date BETWEEN "
                . $this->db->escape($date_from_db)
                . " AND "
                . $this->db->escape($date_to_db);
        }

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name
                ) x
            ) y
        ";

        $row = $this->db->query($sql)->row();
        return $row ? (int)$row->total : 0;
    }

    public function get_material_balance_total($filters=[])
    {
        $plant     = $filters['plant'] ?? '';
        $material  = $filters['material'] ?? '';
        $date_from = $filters['date_from'] ?? '';
        $date_to   = $filters['date_to'] ?? '';

        $date_from_db = $date_from ? str_replace('-', '', $date_from) : '';
        $date_to_db   = $date_to   ? str_replace('-', '', $date_to)   : '';

        $sql = "
            SELECT 
                SUM(BEGIN_qty) AS BEGIN_qty,
                SUM(BEGIN_bw)  AS BEGIN_bw,
                SUM(in_qty)    AS in_qty,
                SUM(in_bw)     AS in_bw,
                SUM(out_qty)   AS out_qty,
                SUM(out_bw)    AS out_bw,
                SUM(BEGIN_qty) + SUM(in_qty) - SUM(out_qty) AS END_qty,
                SUM(BEGIN_bw)  + SUM(in_bw)  - SUM(out_bw)  AS END_bw
            FROM (
                SELECT plant, material, material_name,
                    SUM(in_jumlah) AS BEGIN_qty,
                    SUM(in_berat)  AS BEGIN_bw,
                    0 AS in_qty, 0 AS in_bw,
                    0 AS out_qty, 0 AS out_bw
                FROM uv_mt_inventory
                WHERE 1=1
        ";

        if($plant) $sql .= " AND plant = ".$this->db->escape($plant);
        if($date_from_db) $sql .= " AND transaction_date < ".$this->db->escape($date_from_db);

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name
            UNION ALL
            SELECT plant, material, material_name,
                0,0,
                SUM(in_jumlah), SUM(in_berat),
                SUM(out_jumlah), SUM(out_berat)
            FROM uv_mt_inventory
            WHERE 1=1
        ";

        if($plant) $sql .= " AND plant = ".$this->db->escape($plant);
        if($date_from_db && $date_to_db){
            $sql .= " AND transaction_date BETWEEN ".$this->db->escape($date_from_db)." AND ".$this->db->escape($date_to_db);
        }

        if ($material) {
            $m = '%'.$material.'%';
            $sql .= " AND (
                material COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
                OR material_name COLLATE utf8mb4_uca1400_ai_ci LIKE " . $this->db->escape($m) . "
            )";
        }

        $sql .= " GROUP BY plant, material, material_name
        ) x";

        return $this->db->query($sql)->row();
    }
}
