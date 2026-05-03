<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportProduction_model extends CI_Model {

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

    private function convert_date($date)
    {
        if (!$date) return null;
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

    public function get_production_report($limit, $start, $filters, $order = 'PRODUCTION_DATE', $dir = 'DESC')
    {
        // =========================
        // STEP 1: Ambil HEADER (pagination per PRODUCTION)
        // =========================
        $sqlHeader = "
            SELECT DISTINCT p.PRODUCTION, p.PLANT
            FROM mst_production p
            WHERE (p.DELETED = 0 OR p.DELETED IS NULL)
        ";

        if (!empty($filters['plant'])) {

            if (is_array($filters['plant'])) {

                $plantIn = implode(',', array_map([$this->db,'escape'],$filters['plant']));
                $sqlHeader .= " AND p.PLANT IN ($plantIn)";

            } else {

                $sqlHeader .= " AND p.PLANT = " . $this->db->escape($filters['plant']);
            }
        }
        if (!empty($filters['receive_lb'])) {
            $sqlHeader .= " AND p.RECEIVE_LB LIKE " . $this->db->escape('%'.$filters['receive_lb'].'%');
        }
        if (!empty($filters['production'])) {
            $sqlHeader .= " AND p.PRODUCTION LIKE " . $this->db->escape('%'.$filters['production'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sqlHeader .= " AND p.PRODUCTION_DATE >= " 
                . $this->db->escape($filters['date_from'].' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $sqlHeader .= " AND p.PRODUCTION_DATE <= " 
                . $this->db->escape($filters['date_to'].' 23:59:59');
        }

        $allowed_order = ['PRODUCTION', 'PLANT', 'PRODUCTION_DATE', 'RECEIVE_LB'];
        if (!in_array($order, $allowed_order)) {
            $order = 'PRODUCTION_DATE';
        }
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sqlHeader .= " ORDER BY p.$order $dir";

        if ($limit > 0) {
            $sqlHeader .= " LIMIT $start, $limit";
        }

        $headers = $this->db->query($sqlHeader)->result();
        if (empty($headers)) return [];

        // =========================
        // STEP 2: JOIN DETAIL
        // =========================
        $whereIn = [];
        foreach ($headers as $h) {
            $whereIn[] = "(" .
                $this->db->escape($h->PRODUCTION) . "," .
                $this->db->escape($h->PLANT) .
            ")";
        }

        $sql = "
            SELECT 
                p.PRODUCTION,
                p.PLANT,
                c.CODE_NAME AS PLANT_NAME,
                p.RECEIVE_LB,
                p.PRODUCTION_DATE,
                d2.SEQ_NO,
                d2.ITEM,
                i.FULL_NAME AS ITEM_NAME,
                d2.QTY,
                d2.BERAT,
                d2.REMARK AS DETAIL_REMARK
            FROM mst_production p
            JOIN mst_production_detail d2 
                ON d2.PRODUCTION = p.PRODUCTION 
                AND d2.PLANT = p.PLANT
            LEFT JOIN cd_code c 
                ON c.HEAD_CODE = 'AJ'
                AND c.CODE COLLATE utf8mb4_uca1400_ai_ci = p.PLANT
            LEFT JOIN cd_item i
                ON i.ITEM COLLATE utf8mb4_uca1400_ai_ci = d2.ITEM
            WHERE (p.PRODUCTION, p.PLANT) IN (" . implode(',', $whereIn) . ")
            ORDER BY 
                p.PRODUCTION_DATE DESC,
                p.PRODUCTION ASC,
                d2.SEQ_NO ASC
        ";

        return $this->db->query($sql)->result();
    }

    public function count_production_report($filters)
    {
        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT DISTINCT p.PRODUCTION, p.PLANT
                FROM mst_production p
                WHERE (p.DELETED = 0 OR p.DELETED IS NULL)
        ";

        if (!empty($filters['plant'])) {

            if (is_array($filters['plant'])) {

                $plantIn = implode(',', array_map([$this->db,'escape'],$filters['plant']));
                $sql .= " AND p.PLANT IN ($plantIn)";

            } else {

                $sql .= " AND p.PLANT = " . $this->db->escape($filters['plant']);
            }
        }
        if (!empty($filters['receive_lb'])) {
            $sql .= " AND p.RECEIVE_LB LIKE " . $this->db->escape('%'.$filters['receive_lb'].'%');
        }
        if (!empty($filters['production'])) {
            $sql .= " AND p.PRODUCTION LIKE " . $this->db->escape('%'.$filters['production'].'%');
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND p.PRODUCTION_DATE >= " 
                . $this->db->escape($filters['date_from'].' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND p.PRODUCTION_DATE <= " 
                . $this->db->escape($filters['date_to'].' 23:59:59');
        }

        $sql .= ") x";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_stock_actual_report($limit, $start, $filters, $order='ITEM', $dir='ASC')
    {
        $plant = $filters['plant'];
        $date  = $filters['date'];

        // ==========================
        // 1️⃣ Cari SA terakhir <= T
        // ==========================
        $lastSA = $this->db->query("
            SELECT MAX(SA_DATE) AS last_sa
            FROM mst_stock_actual
            WHERE PLANT = ?
            AND SA_DATE <= ?
        ", [$plant, $date])->row()->last_sa;

        // ==========================
        // 2️⃣ SYSTEM TOTAL (<= T)
        // ==========================
        $sqlSystemTotal = "
            SELECT ITEM,
                SUM(QTY) AS total_qty,
                SUM(BERAT) AS total_bw
            FROM (
                SELECT pd.ITEM,
                    pd.QTY,
                    pd.BERAT
                FROM mst_production_detail pd
                JOIN mst_production p
                    ON p.PRODUCTION = pd.PRODUCTION
                    AND p.PLANT = pd.PLANT
                WHERE pd.PLANT = ?
                AND p.PRODUCTION_DATE <= ?

                UNION ALL

                SELECT sd.ITEM,
                    -sd.QTY,
                    -sd.BERAT
                FROM mst_sales_detail sd
                JOIN mst_sales s
                    ON s.SALES = sd.SALES
                    AND s.PLANT = sd.PLANT
                WHERE sd.PLANT = ?
                AND s.SALES_DATE <= ?
            ) x
            GROUP BY ITEM
        ";

        $systemTotal = $this->db->query(
            $sqlSystemTotal,
            [$plant, $date.' 23:59:59', $plant, $date.' 23:59:59']
        )->result_array();

        $mapSystemTotal = [];
        foreach ($systemTotal as $r) {
            $mapSystemTotal[$r['ITEM']] = $r;
        }

        // ==========================
        // 3️⃣ SYSTEM UNTIL SA
        // ==========================
        $mapSystemUntilSA = [];

        if ($lastSA) {

            $sqlSystemUntilSA = "
                SELECT ITEM,
                    SUM(QTY) AS sa_qty,
                    SUM(BERAT) AS sa_bw
                FROM (
                    SELECT pd.ITEM,
                        pd.QTY,
                        pd.BERAT
                    FROM mst_production_detail pd
                    JOIN mst_production p
                        ON p.PRODUCTION = pd.PRODUCTION
                        AND p.PLANT = pd.PLANT
                    WHERE pd.PLANT = ?
                    AND p.PRODUCTION_DATE <= ?

                    UNION ALL

                    SELECT sd.ITEM,
                        -sd.QTY,
                        -sd.BERAT
                    FROM mst_sales_detail sd
                    JOIN mst_sales s
                        ON s.SALES = sd.SALES
                        AND s.PLANT = sd.PLANT
                    WHERE sd.PLANT = ?
                    AND s.SALES_DATE <= ?
                ) x
                GROUP BY ITEM
            ";

            $systemUntilSA = $this->db->query(
                $sqlSystemUntilSA,
                [$plant, $lastSA.' 23:59:59', $plant, $lastSA.' 23:59:59']
            )->result_array();

            foreach ($systemUntilSA as $r) {
                $mapSystemUntilSA[$r['ITEM']] = $r;
            }
        }

        // ==========================
        // 4️⃣ SA SNAPSHOT
        // ==========================
        $mapSA = [];

        if ($lastSA) {

            $saRows = $this->db->query("
                SELECT d.ITEM,
                    d.ACTUAL_QTY,
                    d.ACTUAL_BERAT
                FROM mst_stock_actual_detail d
                JOIN mst_stock_actual sa
                    ON sa.STOCK_ACTUAL = d.STOCK_ACTUAL
                    AND sa.PLANT = d.PLANT
                WHERE d.PLANT = ?
                AND sa.SA_DATE = ?
            ", [$plant, $lastSA])->result_array();

            foreach ($saRows as $r) {
                $mapSA[$r['ITEM']] = $r;
            }
        }

        // ==========================
        // 5️⃣ MASTER ITEM LIST
        // ==========================
        $items = $this->db->query("
            SELECT DISTINCT x.ITEM, COALESCE(i.FULL_NAME,'-') AS ITEM_NAME
            FROM (
                SELECT ITEM FROM mst_production_detail WHERE PLANT = ?
                UNION
                SELECT ITEM FROM mst_sales_detail WHERE PLANT = ?
                UNION
                SELECT ITEM FROM mst_stock_actual_detail WHERE PLANT = ?
            ) x
            LEFT JOIN cd_item i ON i.ITEM = x.ITEM
        ", [$plant,$plant,$plant])->result_array();

        $rows = [];

        foreach ($items as $it) {

            $item = $it['ITEM'];
            $itemName = $it['ITEM_NAME'];

            $systemQty = $mapSystemTotal[$item]['total_qty'] ?? 0;
            $systemBW  = $mapSystemTotal[$item]['total_bw'] ?? 0;

            if (isset($mapSA[$item])) {

                $saQty = $mapSA[$item]['ACTUAL_QTY'];
                $saBW  = $mapSA[$item]['ACTUAL_BERAT'];

                $systemUntilSA_Qty = $mapSystemUntilSA[$item]['sa_qty'] ?? 0;
                $systemUntilSA_BW  = $mapSystemUntilSA[$item]['sa_bw'] ?? 0;

                $movementAfterSA_Qty = $systemQty - $systemUntilSA_Qty;
                $movementAfterSA_BW  = $systemBW  - $systemUntilSA_BW;

                $finalQty = $saQty + $movementAfterSA_Qty;
                $finalBW  = $saBW  + $movementAfterSA_BW;

            } else {

                $finalQty = $systemQty;
                $finalBW  = $systemBW;
            }

            $adjustQty = $systemQty - $finalQty;
            $adjustBW  = $systemBW  - $finalBW;

            $rows[] = (object)[
                'PLANT'         => $plant,
                'PLANT_NAME'    => $this->get_plant_name($plant),
                'ITEM'          => $item,
                'ITEM_NAME'     => $itemName,
                'SYSTEM_QTY'    => $systemQty,
                'SYSTEM_BW'     => $systemBW,
                'ADJUST_QTY'    => $adjustQty,
                'ADJUST_BW'     => $adjustBW,
                'FINAL_QTY'     => $finalQty,
                'FINAL_BERAT'   => $finalBW
            ];
        }

        // ==========================
        // TOTALS
        // ==========================
        $totals = ['FINAL_QTY'=>0,'FINAL_BERAT'=>0];

        foreach ($rows as $r) {
            $totals['FINAL_QTY']   += $r->FINAL_QTY;
            $totals['FINAL_BERAT'] += $r->FINAL_BERAT;
        }

        return [
            'rows'   => $rows,
            'totals' => $totals
        ];
    }

    public function count_stock_actual_report($filters)
    {
        $plant = $this->db->escape($filters['plant']);

        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                SELECT DISTINCT ITEM, PLANT
                FROM mst_production_detail WHERE PLANT = $plant
                UNION
                SELECT DISTINCT ITEM, PLANT
                FROM mst_sales_detail WHERE PLANT = $plant
                UNION
                SELECT DISTINCT ITEM, PLANT
                FROM mst_stock_actual_detail WHERE PLANT = $plant
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_item_balance($limit = 0, $start = 0, $filters = [])
    {
        $plant     = $filters['plant'];
        $item      = $filters['item'] ?? '';
        $date_from = $filters['date_from'];
        $date_to   = $filters['date_to'];

        $dateFromSql = str_replace('-', '', $date_from);
        $dateToSql   = str_replace('-', '', $date_to);

        $sql = "
        SELECT 
            x.plant,
            c.CODE_NAME AS plant_name,
            x.item,
            x.item_name,

            /* BEGIN */
            SUM(CASE WHEN x.transaction_date < '$dateFromSql'
                    THEN x.in_qty - x.out_qty ELSE 0 END) AS BEGIN_QTY,
            SUM(CASE WHEN x.transaction_date < '$dateFromSql'
                    THEN x.in_bw - x.out_bw ELSE 0 END) AS BEGIN_BW,

            /* IN */
            SUM(CASE WHEN x.transaction_date BETWEEN '$dateFromSql' AND '$dateToSql'
                    THEN x.in_qty ELSE 0 END) AS IN_QTY,
            SUM(CASE WHEN x.transaction_date BETWEEN '$dateFromSql' AND '$dateToSql'
                    THEN x.in_bw ELSE 0 END) AS IN_BW,

            /* OUT */
            SUM(CASE WHEN x.transaction_date BETWEEN '$dateFromSql' AND '$dateToSql'
                    THEN x.out_qty ELSE 0 END) AS OUT_QTY,
            SUM(CASE WHEN x.transaction_date BETWEEN '$dateFromSql' AND '$dateToSql'
                    THEN x.out_bw ELSE 0 END) AS OUT_BW,

            /* END */
            SUM(CASE WHEN x.transaction_date <= '$dateToSql'
                    THEN x.in_qty - x.out_qty ELSE 0 END) AS END_QTY,
            SUM(CASE WHEN x.transaction_date <= '$dateToSql'
                    THEN x.in_bw - x.out_bw ELSE 0 END) AS END_BW

        FROM (
            /* PRODUCTION */
            SELECT
                a.PLANT AS plant,
                DATE_FORMAT(a.PRODUCTION_DATE,'%Y%m%d') AS transaction_date,
                b.ITEM AS item,
                i.FULL_NAME AS item_name,
                b.QTY AS in_qty,
                b.BERAT AS in_bw,
                0 AS out_qty,
                0 AS out_bw
            FROM mst_production a
            JOIN mst_production_detail b 
                ON a.PLANT = b.PLANT AND a.PRODUCTION = b.PRODUCTION
            JOIN cd_item i ON b.ITEM = i.ITEM
            WHERE a.PLANT = " . $this->db->escape($plant) . "

            UNION ALL

            /* SALES */
            SELECT
                a.PLANT AS plant,
                DATE_FORMAT(a.SALES_DATE,'%Y%m%d') AS transaction_date,
                b.ITEM AS item,
                i.FULL_NAME AS item_name,
                0 AS in_qty,
                0 AS in_bw,
                b.QTY AS out_qty,
                b.BERAT AS out_bw
            FROM mst_sales a
            JOIN mst_sales_detail b 
                ON a.PLANT = b.PLANT AND a.SALES = b.SALES
            JOIN cd_item i ON b.ITEM = i.ITEM
            WHERE a.PLANT = " . $this->db->escape($plant) . "
        ) x

        LEFT JOIN cd_code c ON c.CODE = x.plant AND c.HEAD_CODE = 'AJ'
        WHERE 1=1
        ";

        if (!empty($item)) {
            $sql .= "
            AND (
                x.item LIKE " . $this->db->escape('%'.$item.'%') . "
                OR x.item_name LIKE " . $this->db->escape('%'.$item.'%') . "
            )";
        }

        $sql .= "
        GROUP BY x.plant, c.CODE_NAME, x.item, x.item_name
        ORDER BY x.item ASC
        ";

        if ($limit > 0) {
            $sql .= " LIMIT $start, $limit";
        }

        return $this->db->query($sql)->result();
    }

    public function count_item_balance($filters = [])
    {
        $plant = $filters['plant'];
        $item  = $filters['item'] ?? '';

        $sql = "
        SELECT COUNT(*) total FROM (
            SELECT x.item
            FROM (
                SELECT b.ITEM AS item
                FROM mst_production a
                JOIN mst_production_detail b 
                    ON a.PLANT=b.PLANT AND a.PRODUCTION=b.PRODUCTION
                WHERE a.PLANT = " . $this->db->escape($plant) . "

                UNION

                SELECT b.ITEM AS item
                FROM mst_sales a
                JOIN mst_sales_detail b 
                    ON a.PLANT=b.PLANT AND a.SALES=b.SALES
                WHERE a.PLANT = " . $this->db->escape($plant) . "
            ) x
            WHERE 1=1
        ";

        if (!empty($item)) {
            $sql .= " AND x.item LIKE " . $this->db->escape('%'.$item.'%');
        }

        $sql .= " GROUP BY x.item ) y";

        $row = $this->db->query($sql)->row();
        return $row ? (int)$row->total : 0;
    }

    private function get_plant_name($plant)
    {
        $row = $this->db->get_where('cd_code', ['HEAD_CODE'=>'AJ','CODE'=>$plant])->row();
        return $row->CODE_NAME ?? $plant;
    }

}
