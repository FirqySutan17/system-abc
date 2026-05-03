<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockActual_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */
    public function get_data(
        $limit,
        $start,
        $role_id,
        $plant,
        $username,
        $search = '',
        $order = 'SA_DATE',
        $dir = 'DESC'
    )
    {
        $role_id = (int) $role_id;

        $this->db->select('
            h.STOCK_ACTUAL,
            h.SA_DATE,
            h.SA_TIME,
            h.REMARK,
            h.PLANT,
            c.CODE_NAME AS PLANT_NAME
        ');
        $this->db->from('mst_stock_actual h');

        // JOIN PLANT NAME
        $this->db->join(
            'cd_code c',
            "c.CODE = h.PLANT AND c.HEAD_CODE = 'AJ'",
            'left'
        );

        /* 🔐 NON ADMIN → FILTER PLANT + CREATED_BY */
        if ($role_id !== 1) {

            // plant bisa JSON ["1001","1002"] atau string
            $plants = json_decode($plant, true);

            if (!is_array($plants)) {
                $plants = array_map('trim', explode(',', $plant));
            }

            $this->db->where_in('h.PLANT', $plants);
            $this->db->where('h.CREATED_BY', $username);
        }

        /* 🔍 SEARCH */
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('h.STOCK_ACTUAL', $search);
            $this->db->or_like('h.REMARK', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->group_end();
        }

        $this->db->order_by($order, $dir);
        $this->db->limit((int)$limit, (int)$start);

        return $this->db->get()->result_array();
    }

    public function count_data(
        $role_id,
        $plant,
        $username,
        $search = ''
    )
    {
        $role_id = (int) $role_id;

        $this->db->from('mst_stock_actual h');

        $this->db->join(
            'cd_code c',
            "c.CODE = h.PLANT AND c.HEAD_CODE = 'AJ'",
            'left'
        );

        /* 🔐 NON ADMIN → FILTER PLANT + CREATED_BY */
        if ($role_id !== 1) {

            $plants = json_decode($plant, true);

            if (!is_array($plants)) {
                $plants = array_map('trim', explode(',', $plant));
            }

            $this->db->where_in('h.PLANT', $plants);
            $this->db->where('h.CREATED_BY', $username);
        }

        /* 🔍 SEARCH */
        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('h.STOCK_ACTUAL', $search);
            $this->db->or_like('h.REMARK', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    public function get_user_plants($username)
    {
        $row = $this->db
            ->select('plant')
            ->where('username', $username)
            ->get('users')
            ->row();

        if (!$row || empty($row->plant)) {
            return [];
        }

        $plants = json_decode($row->plant, true);
        return is_array($plants) ? $plants : [];
    }

    public function get_plant_select2_by_user($username)
    {
        $plants = $this->get_user_plants($username);

        if (empty($plants)) {
            return [];
        }

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plants)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result_array();
    }

    public function user_has_plant($username, $plant)
    {
        return in_array($plant, $this->get_user_plants($username));
    }

    public function get_plant_code_by_id($plant_id)
    {
        return $this->db
            ->select('code')
            ->from('cd_code')
            ->where([
                'head_code' => 'AJ',
                'code'        => $plant_id   // JIKA ID-nya sama
            ])
            ->get()
            ->row('code');
    }

    public function get_plant_code_by_code($plant_id)
    {
        return $this->db
            ->select('code')
            ->from('cd_code')
            ->where([
                'head_code' => 'AJ',
                'id'        => $plant_id   // JIKA ID-nya sama
            ])
            ->get()
            ->row('code');
    }

    public function get_all_item_by_plant($plant_id)
    {
        if (!$plant_id) {
            return [];
        }

        $sql = "
            SELECT DISTINCT 
                d.ITEM,
                COALESCE(i.FULL_NAME,'-') AS FULL_NAME
            FROM mst_production_detail d
            JOIN mst_production p 
                ON p.PRODUCTION = d.PRODUCTION
            LEFT JOIN cd_item i 
                ON i.ITEM = d.ITEM
            WHERE p.PLANT = ?

            UNION

            SELECT DISTINCT 
                d.ITEM,
                COALESCE(i.FULL_NAME,'-') AS FULL_NAME
            FROM mst_sales_detail d
            JOIN mst_sales s 
                ON s.SALES = d.SALES
            LEFT JOIN cd_item i 
                ON i.ITEM = d.ITEM
            WHERE s.PLANT = ?

            ORDER BY ITEM ASC
        ";

        return $this->db->query($sql, [$plant_id, $plant_id])->result_array();
    }

    public function get_stock_actual_by_plant($plant_id)
    {
        if (!$plant_id) {
            return [];
        }

        $sql = "
            SELECT
                x.PLANT,
                x.ITEM,
                COALESCE(i.FULL_NAME, '-') AS FULL_NAME,
                SUM(x.qty)   AS stock_qty,
                SUM(x.berat) AS stock_bw
            FROM (
                /* ===============================
                PRODUCTION MASUK (PLANT FIX)
                =============================== */
                SELECT
                    pd.PLANT,
                    pd.ITEM,
                    pd.QTY   AS qty,
                    pd.BERAT AS berat
                FROM mst_production_detail pd
                WHERE pd.PLANT = ?

                UNION ALL

                /* ===============================
                SALES KELUAR (PLANT FIX)
                =============================== */
                SELECT
                    sd.PLANT,
                    sd.ITEM,
                    -sd.QTY   AS qty,
                    -sd.BERAT AS berat
                FROM mst_sales_detail sd
                WHERE sd.PLANT = ?
            ) x
            LEFT JOIN cd_item i
                ON i.ITEM = x.ITEM
            GROUP BY x.PLANT, x.ITEM, i.FULL_NAME
            ORDER BY x.ITEM ASC
        ";

        return $this->db->query($sql, [$plant_id, $plant_id])->result_array();
    }

    public function get_stock_by_item($item, $plant)
    {
        if (!$item || !$plant) {
            return [
                'stock_qty' => 0,
                'stock_bw'  => 0
            ];
        }

        $sql = "
            SELECT
                x.ITEM,
                SUM(x.qty)   AS stock_qty,
                SUM(x.berat) AS stock_bw
            FROM (
                /* ===============================
                PRODUCTION MASUK (PLANT FIX)
                =============================== */
                SELECT
                    pd.ITEM,
                    pd.QTY   AS qty,
                    pd.BERAT AS berat
                FROM mst_production_detail pd
                WHERE pd.PLANT = ?
                AND pd.ITEM  = ?

                UNION ALL

                /* ===============================
                SALES KELUAR (PLANT FIX)
                =============================== */
                SELECT
                    sd.ITEM,
                    -sd.QTY   AS qty,
                    -sd.BERAT AS berat
                FROM mst_sales_detail sd
                WHERE sd.PLANT = ?
                AND sd.ITEM  = ?
            ) x
            GROUP BY x.ITEM
        ";

        $row = $this->db
            ->query($sql, [$plant, $item, $plant, $item])
            ->row_array();

        return [
            'stock_qty' => isset($row['stock_qty']) ? (float)$row['stock_qty'] : 0,
            'stock_bw'  => isset($row['stock_bw'])  ? (float)$row['stock_bw']  : 0
        ];
    }

    public function get_last_seq_no($plant, $stockActual)
    {
        return (int) $this->db
            ->select_max('SEQ_NO')
            ->where('PLANT', $plant)
            ->where('STOCK_ACTUAL', $stockActual)
            ->get('mst_stock_actual_detail')
            ->row()
            ->SEQ_NO;
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */
    public function generate_stock_actual_no($plant, $sa_date)
    {
        $date = date('Ymd', strtotime($sa_date)); // contoh: 20251219
        $prefix = $date . 'SA';

        $this->db->select('STOCK_ACTUAL');
        $this->db->from('mst_stock_actual');
        $this->db->where('PLANT', $plant);
        $this->db->like('STOCK_ACTUAL', $prefix, 'after');
        $this->db->order_by('STOCK_ACTUAL', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        if ($row) {
            $seq = (int)substr($row->STOCK_ACTUAL, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* ---------------------------------------------------------
       HEADER OPERATIONS
    --------------------------------------------------------- */
    public function insert_header($data)
    {
        return $this->db->insert('mst_stock_actual', $data);
    }

    public function get_header($plant, $stockActual)
    {
        return $this->db->get_where('mst_stock_actual', [
            'PLANT'        => $plant,
            'STOCK_ACTUAL' => $stockActual
        ])->row_array();
    }

    public function get_header_by_stock($stockActual)
    {
        return $this->db->get_where('mst_stock_actual', [
            'STOCK_ACTUAL' => $stockActual
        ])->row_array();
    }

    public function update_header($plant, $stockActual, $data)
    {
        return $this->db->where([
            'PLANT'        => $plant,
            'STOCK_ACTUAL' => $stockActual
        ])->update('mst_stock_actual', $data);
    }

    public function delete_header($plant, $stockActual)
    {
        return $this->db->where([
                'PLANT'        => $plant,
                'STOCK_ACTUAL' => $stockActual
            ])->delete('mst_stock_actual');
    }

    /* ---------------------------------------------------------
       DETAIL OPERATIONS
    --------------------------------------------------------- */
    public function get_detail($plant, $stockActual)
    {
        return $this->db
            ->where('PLANT', $plant)
            ->where('STOCK_ACTUAL', $stockActual)
            ->order_by('SEQ_NO', 'ASC') // 🔥 PENTING
            ->get('mst_stock_actual_detail')
            ->result_array();
    }

    public function delete_detail($plant, $stockActual)
    {
        return $this->db->where([
            'PLANT'        => $plant,
            'STOCK_ACTUAL' => $stockActual
        ])->delete('mst_stock_actual_detail');
    }

    public function insert_detail_batch($rows)
    {
        return $this->db->insert_batch('mst_stock_actual_detail', $rows);
    }

    /* ---------------------------------------------------------
       SELECT2 HELPERS (optional)
    --------------------------------------------------------- */
    public function search_item($q = null, $limit = 20)
    {
        $this->db->select('ITEM as id, ITEM_NAME as text');
        $this->db->from('mst_item'); // pastikan ada table master item

        if ($q) {
            $this->db->group_start();
            $this->db->like('ITEM', $q);
            $this->db->or_like('ITEM_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('ITEM', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }
}
