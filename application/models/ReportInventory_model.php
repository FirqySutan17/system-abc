<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportInventory_model extends CI_Model {

    public function get_plant_by_user($plant)
    {
        return $this->db
            ->where('HEAD_CODE', 'AJ')
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

    public function get_po_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'a.PO_DATE',
        $dir = 'DESC'
    ){
        $allowedOrder = [
            'PO_DATE'  => 'a.PO_DATE',
            'PO'       => 'a.PO',
            'PLANT'    => 'a.PLANT',
            'SUPPLIER' => 'a.SUPPLIER',
            'STATUS'   => 'a.STATUS'
        ];

        $orderKey = str_replace('a.', '', $order);
        $orderBy  = $allowedOrder[$orderKey] ?? 'a.PO_DATE';
        $dir      = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | STEP 1 : HEADER ONLY (pagination = 10 card)
        |--------------------------------------------------------------------------
        */
        $header = $this->db
            ->select('a.PO, a.PLANT')
            ->from('abc_mst_po a')
            ->where('a.DELETED IS NULL', null, false);

        if (!empty($filters['plant'])) {
            $this->db->where('a.PLANT', $filters['plant']);
        }

        if (!empty($filters['supplier'])) {
            $this->db->where('a.SUPPLIER', $filters['supplier']);
        }

        if (!empty($filters['po'])) {
            $this->db->like('a.PO', $filters['po']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(a.PO_DATE) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(a.PO_DATE) <=', $filters['date_to']);
        }

        $this->db->order_by($orderBy, $dir);
        $this->db->order_by('a.PO', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        $headers = $this->db->get()->result();

        if (empty($headers)) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD WHERE IN HEADER
        |--------------------------------------------------------------------------
        */
        $pairs = [];

        foreach ($headers as $h) {
            $pairs[] = "("
                .$this->db->escape($h->PO)
                .","
                .$this->db->escape($h->PLANT)
                .")";
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 : FULL DETAIL
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT
                a.PLANT,
                plant.CODE_NAME AS PLANT_NAME,

                a.PO,
                a.PO_DATE,
                a.PO_TYPE,
                type.CODE_NAME AS PO_NAME,

                a.SUPPLIER,
                supplier.FULL_NAME AS SUPPLIER_NAME,

                a.STATUS,
                a.REMARK,

                b.SEQ_NO,
                b.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,

                b.MATERIAL,
                material.MATERIAL_NAME,

                b.JUMLAH,
                b.BERAT,
                b.HARGA,
                b.TOTAL

            FROM abc_mst_po a

            INNER JOIN abc_mst_po_detail b
                ON a.PO = b.PO
                AND a.PLANT = b.PLANT

            LEFT JOIN abc_cd_code plant
                ON plant.HEAD_CODE = 'PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                a.PLANT COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_code type
                ON type.HEAD_CODE = 'PO'
                AND type.CODE COLLATE utf8mb4_unicode_ci =
                a.PO_TYPE COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer supplier
                ON supplier.CUST COLLATE utf8mb4_unicode_ci =
                a.SUPPLIER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer customer
                ON customer.CUST COLLATE utf8mb4_unicode_ci =
                b.CUSTOMER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_material material
                ON material.MATERIAL COLLATE utf8mb4_unicode_ci =
                b.MATERIAL COLLATE utf8mb4_unicode_ci

            WHERE (a.PO,a.PLANT) IN (" . implode(',', $pairs) . ")

            ORDER BY
                $orderBy $dir,
                a.PO DESC,
                b.SEQ_NO ASC
        ";

        return $this->db->query($sql)->result();
    }

    public function count_po_report($filters = [])
    {
        $this->db
            ->select('COUNT(*) total', false)
            ->from('abc_mst_po a')
            ->where('a.DELETED IS NULL', null, false);

        if (!empty($filters['plant'])) {
            $this->db->where('a.PLANT', $filters['plant']);
        }

        if (!empty($filters['supplier'])) {
            $this->db->where('a.SUPPLIER', $filters['supplier']);
        }

        if (!empty($filters['po'])) {
            $this->db->like('a.PO', $filters['po']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(a.PO_DATE) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(a.PO_DATE) <=', $filters['date_to']);
        }

        return (int)$this->db->get()->row()->total;
    }

    public function get_receive_report(
        $limit = 0,
        $start = 0,
        $filters = [],
        $order = 'RECEIVE_DATE',
        $dir = 'DESC'
    ){
        $allowedOrder = [
            'RECEIVE'      => 'r.RECEIVE',
            'PLANT'        => 'r.PLANT',
            'RECEIVE_DATE' => 'r.RECEIVE_DATE',
            'PO'           => 'r.PO',
            'SUPPLIER'     => 'r.SUPPLIER'
        ];

        $orderBy = $allowedOrder[$order] ?? 'r.RECEIVE_DATE';
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | STEP 1 HEADER
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT r.RECEIVE, r.PLANT
            FROM abc_mst_receive r
            WHERE r.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND r.PLANT = ".$this->db->escape($filters['plant']);
        }

        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER = ".$this->db->escape($filters['supplier']);
        }

        if (!empty($filters['receive'])) {
            $search = $this->db->escape('%'.$filters['receive'].'%');

            $sql .= " AND (
                r.RECEIVE LIKE {$search}
                OR r.PO LIKE {$search}
            )";
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= ".$this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= ".$this->db->escape($filters['date_to']);
        }

        $sql .= " ORDER BY {$orderBy} {$dir}, r.RECEIVE DESC";

        if ($limit > 0) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        $headers = $this->db->query($sql)->result();

        if (!$headers) {
            return [];
        }

        $pairs = [];
        foreach ($headers as $h) {
            $pairs[] = "("
                .$this->db->escape($h->RECEIVE)
                .","
                .$this->db->escape($h->PLANT)
                .")";
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2 DETAIL
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT
                r.RECEIVE,
                r.PLANT,
                plant.CODE_NAME AS PLANT_NAME,
                r.RECEIVE_DATE,
                r.PO,
                r.NOTA,
                r.SUPPLIER,
                supplier.FULL_NAME AS SUPPLIER_NAME,
                r.PEMBAYARAN,
                r.JENIS_PAY,
                r.NO_REF,
                r.SLIP_NO,
                r.REMARK,
                r.ATTACH_FILE_NAME,

                d.SEQ_NO,
                d.CUSTOMER,
                customer.FULL_NAME AS CUSTOMER_NAME,
                d.MATERIAL,
                material.MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.SUSUT_JUMLAH,
                d.SUSUT_BERAT,
                d.HARGA,
                d.TOTAL,
                d.KETERANGAN,
                d.STATUS

            FROM abc_mst_receive r

            INNER JOIN abc_mst_receive_detail d
                ON d.RECEIVE = r.RECEIVE
                AND d.PLANT = r.PLANT
                AND d.DELETED IS NULL

            LEFT JOIN abc_cd_code plant
                ON plant.HEAD_CODE = 'PLANT'
                AND plant.CODE COLLATE utf8mb4_unicode_ci =
                r.PLANT COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer supplier
                ON supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_customer customer
                ON customer.CUST COLLATE utf8mb4_unicode_ci =
                d.CUSTOMER COLLATE utf8mb4_unicode_ci

            LEFT JOIN abc_cd_material material
                ON material.MATERIAL COLLATE utf8mb4_unicode_ci =
                d.MATERIAL COLLATE utf8mb4_unicode_ci

            WHERE (r.RECEIVE,r.PLANT) IN (".implode(',', $pairs).")

            ORDER BY
                {$orderBy} {$dir},
                r.RECEIVE DESC,
                d.SEQ_NO ASC
        ";

        return $this->db->query($sql)->result();
    }

    public function count_receive_report($filters = [])
    {
        $sql = "
            SELECT COUNT(*) total
            FROM abc_mst_receive r
            WHERE r.DELETED IS NULL
        ";

        if (!empty($filters['plant'])) {
            $sql .= " AND r.PLANT = ".$this->db->escape($filters['plant']);
        }

        if (!empty($filters['supplier'])) {
            $sql .= " AND r.SUPPLIER = ".$this->db->escape($filters['supplier']);
        }

        if (!empty($filters['receive'])) {
            $search = $this->db->escape('%'.$filters['receive'].'%');

            $sql .= " AND (
                r.RECEIVE LIKE {$search}
                OR r.PO LIKE {$search}
            )";
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) >= ".$this->db->escape($filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(r.RECEIVE_DATE) <= ".$this->db->escape($filters['date_to']);
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
            LEFT JOIN abc_cd_code c 
                ON c.HEAD_CODE = 'AJ' 
                AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = r.PLANT
            LEFT JOIN abc_cd_customer cust 
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
            LEFT JOIN abc_cd_code c ON c.CODE = tbl.plant AND c.HEAD_CODE = 'AJ'
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
