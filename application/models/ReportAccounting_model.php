<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportAccounting_model extends CI_Model {

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

    public function get_cost_report($limit, $start, $filters, $order = 'COST_DATE', $dir = 'DESC')
    {
        // =========================
        // STEP 1: HEADER QUERY
        // =========================
        $this->db->distinct();
        $this->db->select('c.COST, c.PLANT');
        $this->db->from('mst_cost c');
        $this->db->where('c.DELETED IS NULL', null, false);

        $this->db->where_in('c.PLANT', $filters['plants']);

        if (!empty($filters['cost'])) {
            $this->db->like('c.COST', $filters['cost']);
        }

        if (!empty($filters['pembayaran'])) {
            $this->db->like('c.PEMBAYARAN', $filters['pembayaran']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('c.COST_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('c.COST_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $this->db->order_by("c.$order", $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        $headers = $this->db->get()->result();

        if (empty($headers)) return [];

        // =========================
        // STEP 2: DETAIL QUERY
        // =========================
        $this->db->select("
            c.COST,
            c.PLANT,
            cc.CODE_NAME AS PLANT_NAME,
            c.COST_DATE,
            c.PEMBAYARAN,
            d.SEQ_NO,
            d.TIPE_COST,
            cost.COST_NAME AS TIPE_COST_NAME,
            d.JUMLAH,
            d.TOTAL,
            d.REMARK AS DETAIL_REMARK
        ");
        $this->db->from('mst_cost c');
        $this->db->join('mst_cost_detail d',
            'd.COST = c.COST AND d.PLANT = c.PLANT AND d.DELETED IS NULL'
        );
        $this->db->join('cd_code cc',
            "cc.HEAD_CODE = 'AJ' AND cc.CODE = c.PLANT",
            'left'
        );
        $this->db->join('cd_cost cost',
            'cost.COST = d.TIPE_COST',
            'left'
        );

        $this->db->where('c.DELETED IS NULL', null, false);

        // 🔐 FILTER HEADER RESULT
        $this->db->group_start();
        foreach ($headers as $h) {
            $this->db->or_group_start();
            $this->db->where('c.COST', $h->COST);
            $this->db->where('c.PLANT', $h->PLANT);
            $this->db->group_end();
        }
        $this->db->group_end();

        $this->db->order_by('c.COST_DATE', 'DESC');
        $this->db->order_by('c.COST', 'ASC');
        $this->db->order_by('d.SEQ_NO', 'ASC');

        return $this->db->get()->result();
    }

    public function count_cost_report($filters)
    {
        $this->db->select('COUNT(DISTINCT CONCAT(c.COST,"|",c.PLANT)) AS total', false);
        $this->db->from('mst_cost c');
        $this->db->where('c.DELETED IS NULL', null, false);

        $this->db->where_in('c.PLANT', $filters['plants']);

        if (!empty($filters['cost'])) {
            $this->db->like('c.COST', $filters['cost']);
        }

        if (!empty($filters['pembayaran'])) {
            $this->db->like('c.PEMBAYARAN', $filters['pembayaran']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('c.COST_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('c.COST_DATE <=', $filters['date_to'].' 23:59:59');
        }

        return (int)$this->db->get()->row()->total;
    }

    public function get_payment_report($limit, $start, $filters, $order, $dir)
    {
        $this->db->select("
            p.PAYMENT,
            p.PLANT,
            pl.CODE_NAME AS PLANT_NAME,
            p.PAYMENT_DATE,
            p.PEMBAYARAN,
            p.SUPPLIER,
            sup.FULL_NAME AS SUPPLIER_NAME,

            d.RECEIVE_NO,
            d.MATERIAL,
            mat.MATERIAL_NAME,
            d.JUMLAH,
            d.BERAT,
            d.HARGA,
            d.TOTAL AS DETAIL_TOTAL
        ");

        $this->db->from('mst_payment p');

        $this->db->join(
            'mst_payment_detail d',
            'd.PAYMENT = p.PAYMENT 
            AND d.PLANT = p.PLANT
            AND d.deleted IS NULL',
            'inner'
        );

        $this->db->join(
            'cd_code pl',
            "pl.HEAD_CODE = 'AJ' AND pl.CODE = p.PLANT",
            'left',
            false
        );

        $this->db->join(
            'cd_customer sup',
            "sup.CUST = p.SUPPLIER 
            AND (sup.CUST_KIND = 'SUPPLIER' OR sup.CUST_CLASS = 'SUPPLIER')",
            'left',
            false
        );

        $this->db->join(
            'cd_material mat',
            "mat.MATERIAL = d.MATERIAL",
            'left',
            false
        );

        $this->db->where('p.deleted IS NULL', null, false);

        // 🔐 ALWAYS WHERE IN
        $this->db->where_in('p.PLANT', $filters['plants']);

        if (!empty($filters['supplier'])) {
            $this->db->where('p.SUPPLIER', $filters['supplier']);
        }

        if (!empty($filters['payment'])) {
            $this->db->like('p.PAYMENT', $filters['payment']);
        }

        if (!empty($filters['payment_type'])) {
            $this->db->where('p.PEMBAYARAN', $filters['payment_type']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('p.PAYMENT_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('p.PAYMENT_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $this->db->order_by("p.$order", $dir);
        $this->db->order_by("d.RECEIVE_NO", "ASC");

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_payment_grand_total($filters)
    {
        $this->db->select("
            SUM(d.JUMLAH) AS jumlah,
            SUM(d.BERAT) AS berat,
            SUM(d.TOTAL) AS total
        ");

        $this->db->from('mst_payment p');

        $this->db->join(
            'mst_payment_detail d',
            'd.PAYMENT = p.PAYMENT 
            AND d.PLANT = p.PLANT
            AND d.deleted IS NULL',
            'inner'
        );

        $this->db->where('p.deleted IS NULL', null, false);
        $this->db->where_in('p.PLANT', $filters['plants']);

        if (!empty($filters['supplier'])) {
            $this->db->where('p.SUPPLIER', $filters['supplier']);
        }

        if (!empty($filters['payment'])) {
            $this->db->like('p.PAYMENT', $filters['payment']);
        }

        if (!empty($filters['payment_type'])) {
            $this->db->where('p.PEMBAYARAN', $filters['payment_type']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('p.PAYMENT_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('p.PAYMENT_DATE <=', $filters['date_to'].' 23:59:59');
        }

        return $this->db->get()->row();
    }

    public function count_payment_report($filters)
    {
        $this->db->from('mst_payment p');
        $this->db->where('p.deleted IS NULL', null, false);
        $this->db->where_in('p.PLANT', $filters['plants']);

        if (!empty($filters['supplier'])) {
            $this->db->where('p.SUPPLIER', $filters['supplier']);
        }

        if (!empty($filters['payment'])) {
            $this->db->like('p.PAYMENT', $filters['payment']);
        }

        if (!empty($filters['payment_type'])) {
            $this->db->where('p.PEMBAYARAN', $filters['payment_type']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('p.PAYMENT_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('p.PAYMENT_DATE <=', $filters['date_to'].' 23:59:59');
        }

        return $this->db->count_all_results();
    }

    public function get_cash_in_report($limit, $start, $filters, $order = 'CASHIN_DATE', $dir = 'DESC')
    {
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select("
            d.SALES,
            d.ORG_SLIP_NO AS INVOICE_NO,
            d.AMOUNT_INVOICE,
            d.AMOUNT_OFFSET,
            d.SEQ_NO,
            d.SLIP_NO,

            h.CASH_IN,
            h.CASHIN_DATE,
            h.PLANT,
            pl.CODE_NAME AS PLANT_NAME,

            h.CUSTOMER,
            cust.FULL_NAME AS CUSTOMER_NAME,

            h.PEMBAYARAN,
            h.NO_REK,
            rek.CODE_NAME AS NO_REK_NAME
        ");

        $this->db->from('mst_cash_in_detail d');

        $this->db->join(
            'mst_cash_in h',
            'h.CASH_IN = d.CASH_IN 
            AND h.PLANT = d.PLANT
            AND h.DELETED IS NULL',
            'inner'
        );

        $this->db->join(
            'cd_code pl',
            "pl.HEAD_CODE='AJ' AND pl.CODE = h.PLANT",
            'left',
            false
        );

        $this->db->join(
            'cd_customer cust',
            "cust.CUST = h.CUSTOMER 
            AND cust.DELETED IS NULL 
            AND (cust.CUST_KIND='CUSTOMER' OR cust.CUST_CLASS='CUSTOMER')",
            'left',
            false
        );

        $this->db->join(
            'cd_code rek',
            "rek.HEAD_CODE='AK' AND rek.CODE = h.NO_REK",
            'left',
            false
        );

        $this->db->where('d.DELETED IS NULL', null, false);

        // 🔐 ALWAYS WHERE IN
        $this->db->where_in('h.PLANT', $filters['plants']);

        if (!empty($filters['customer'])) {
            $this->db->where('h.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['cash_in'])) {
            $this->db->like('h.CASH_IN', $filters['cash_in']);
        }

        if (!empty($filters['pembayaran'])) {
            $this->db->where('h.PEMBAYARAN', $filters['pembayaran']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('h.CASHIN_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('h.CASHIN_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $this->db->order_by('d.SALES', 'ASC');
        $this->db->order_by('h.PLANT', 'ASC');
        $this->db->order_by('h.CASHIN_DATE', $dir);
        $this->db->order_by('h.CASH_IN', 'ASC');
        $this->db->order_by('d.SEQ_NO', 'ASC');

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_cash_in_grand_total($filters)
    {
        $this->db->select("
            SUM(d.AMOUNT_INVOICE) AS amount_invoice,
            SUM(d.AMOUNT_OFFSET)  AS amount_offset
        ");

        $this->db->from('mst_cash_in_detail d');

        $this->db->join(
            'mst_cash_in h',
            'h.CASH_IN = d.CASH_IN 
            AND h.PLANT = d.PLANT
            AND h.DELETED IS NULL',
            'inner'
        );

        $this->db->where('d.DELETED IS NULL', null, false);
        $this->db->where_in('h.PLANT', $filters['plants']);

        if (!empty($filters['customer'])) {
            $this->db->where('h.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['cash_in'])) {
            $this->db->like('h.CASH_IN', $filters['cash_in']);
        }

        if (!empty($filters['pembayaran'])) {
            $this->db->where('h.PEMBAYARAN', $filters['pembayaran']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('h.CASHIN_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('h.CASHIN_DATE <=', $filters['date_to'].' 23:59:59');
        }

        return $this->db->get()->row();
    }

    public function count_cash_in_report($filters)
    {
        $this->db->select('COUNT(DISTINCT CONCAT(d.SALES,"|",h.PLANT)) AS total', false);

        $this->db->from('mst_cash_in_detail d');

        $this->db->join(
            'mst_cash_in h',
            'h.CASH_IN = d.CASH_IN 
            AND h.PLANT = d.PLANT
            AND h.DELETED IS NULL',
            'inner'
        );

        $this->db->where('d.DELETED IS NULL', null, false);
        $this->db->where_in('h.PLANT', $filters['plants']);

        if (!empty($filters['customer'])) {
            $this->db->where('h.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['cash_in'])) {
            $this->db->like('h.CASH_IN', $filters['cash_in']);
        }

        if (!empty($filters['pembayaran'])) {
            $this->db->where('h.PEMBAYARAN', $filters['pembayaran']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('h.CASHIN_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('h.CASHIN_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $row = $this->db->get()->row();

        return (int)($row->total ?? 0);
    }

    public function get_ar_item_detail($sales, $plant)
    {
        $this->db->select("
            d.SEQ_NO,
            d.ITEM,
            COALESCE(i.FULL_NAME, d.ITEM) AS ITEM_NAME,

            d.QTY,
            d.BERAT,

            CASE 
                WHEN d.QTY = 0 AND d.BERAT > 0 THEN d.BERAT
                ELSE d.QTY
            END AS DISPLAY_QTY,

            CASE 
                WHEN d.QTY = 0 AND d.BERAT > 0 THEN 'BERAT'
                ELSE 'QTY'
            END AS DISPLAY_TYPE,

            CAST(d.AMOUNT AS DECIMAL(20,2)) AS DETAIL_AMOUNT
        ", false);

        $this->db->from('mst_sales_detail d');

        $this->db->join(
            'cd_item i',
            'i.ITEM = d.ITEM',
            'left'
        );

        $this->db->where('d.DELETED IS NULL', null, false);
        $this->db->where('d.SALES', $sales);
        $this->db->where('d.PLANT', $plant);

        $this->db->order_by('d.SEQ_NO','ASC');

        return $this->db->get()->result();
    }

    public function get_ar_report($limit, $start, $filters, $order = 'SALES_DATE', $dir = 'DESC')
    {
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->select("
            s.SALES,
            s.PLANT,
            pl.CODE_NAME AS PLANT_NAME,

            s.CUSTOMER,
            cust.FULL_NAME AS CUSTOMER_NAME,

            s.SALES_DATE,
            s.AMOUNT AS INVOICE_AMOUNT,

            (s.AMOUNT - s.REMAIN) AS TOTAL_PAID,
            s.REMAIN AS OUTSTANDING
        ", false);

        $this->db->from('mst_sales s');

        // JOIN pembayaran detail
        $this->db->join(
            'mst_cash_in_detail cid',
            'cid.SALES = s.SALES 
            AND cid.PLANT = s.PLANT
            AND cid.DELETED IS NULL',
            'left'
        );

        // JOIN plant
        $this->db->join(
            'cd_code pl',
            "pl.HEAD_CODE='AJ' 
            AND pl.CODE COLLATE utf8mb4_general_ci = s.PLANT",
            'left',
            false
        );

        // JOIN customer
        $this->db->join(
            'cd_customer cust',
            "cust.CUST COLLATE utf8mb4_general_ci = s.CUSTOMER
            AND cust.DELETED IS NULL
            AND (cust.CUST_KIND='CUSTOMER' OR cust.CUST_CLASS='CUSTOMER')",
            'left',
            false
        );

        // ================= FILTER WAJIB =================
        $this->db->where('s.DELETED IS NULL', null, false);
        $this->db->where('s.JENIS_PAY', 'TEMPO');

        if (!empty($filters['plant'])) {
            if (is_array($filters['plant'])) {

                $this->db->group_start();
                $this->db->where_in('s.PLANT', $filters['plant']);
                $this->db->or_where('s.PLANT', '*');
                $this->db->group_end();
            } else {
                if ($filters['plant'] === '*') {

                    $userPlants = $this->session->userdata('plant');

                    $this->db->group_start();
                    $this->db->where_in('s.PLANT', $userPlants);
                    $this->db->or_where('s.PLANT', '*');
                    $this->db->group_end();
                } else {
                    $this->db->where('s.PLANT', $filters['plant']);
                }
            }
        }

        if (!empty($filters['customer'])) {
            $this->db->where('s.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('s.SALES_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('s.SALES_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $this->db->group_by(['s.SALES','s.PLANT']);

        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            if ($filters['status'] === 'PAID') {
                $this->db->where('s.REMAIN', 0);
            }

            if ($filters['status'] === 'OUTSTANDING') {
                $this->db->where('s.REMAIN >', 0);
            }
        }

        // ================= ORDER =================
        $this->db->order_by("s.$order", $dir);

        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }

    public function get_ar_payment_detail($sales, $plant)
    {
        $this->db->select("
            d.CASH_IN,
            h.CASHIN_DATE,
            d.AMOUNT_OFFSET,
            h.PEMBAYARAN,
            h.NO_REK
        ");

        $this->db->from('mst_cash_in_detail d');

        $this->db->join(
            'mst_cash_in h',
            'h.CASH_IN = d.CASH_IN 
            AND h.PLANT = d.PLANT
            AND h.DELETED IS NULL',
            'inner'
        );

        $this->db->where('d.DELETED IS NULL', null, false);
        $this->db->where('d.SALES', $sales);
        $this->db->where('d.PLANT', $plant);

        $this->db->order_by('h.CASHIN_DATE', 'ASC');

        return $this->db->get()->result();
    }

    public function get_ar_grand_total($filters)
    {
        $this->db->select("
            SUM(s.AMOUNT) AS total_invoice,
            SUM(IFNULL(cid.AMOUNT_OFFSET,0)) AS total_paid,
            SUM(s.AMOUNT - IFNULL(cid.AMOUNT_OFFSET,0)) AS total_outstanding
        ", false);

        $this->db->from('mst_sales s');

        $this->db->join(
            '(SELECT SALES, PLANT, SUM(AMOUNT_OFFSET) AS AMOUNT_OFFSET
            FROM mst_cash_in_detail
            WHERE DELETED IS NULL
            GROUP BY SALES, PLANT) cid',
            'cid.SALES = s.SALES AND cid.PLANT = s.PLANT',
            'left',
            false
        );

        $this->db->where('s.DELETED IS NULL', null, false);
        $this->db->where('s.JENIS_PAY', 'TEMPO');

        if (!empty($filters['plant'])) {
            if (is_array($filters['plant'])) {

                $this->db->group_start();
                $this->db->where_in('s.PLANT', $filters['plant']);
                $this->db->or_where('s.PLANT', '*');
                $this->db->group_end();

            } else {

                if ($filters['plant'] === '*') {

                    $userPlants = $this->session->userdata('plant');

                    $this->db->group_start();
                    $this->db->where_in('s.PLANT', $userPlants);
                    $this->db->or_where('s.PLANT', '*');
                    $this->db->group_end();

                } else {
                    $this->db->where('s.PLANT', $filters['plant']);
                }
            }
        }

        if (!empty($filters['customer'])) {
            $this->db->where('s.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('s.SALES_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('s.SALES_DATE <=', $filters['date_to'].' 23:59:59');
        }

        return $this->db->get()->row();
    }

    public function count_ar_report($filters)
    {
        $this->db->select('COUNT(DISTINCT CONCAT(s.SALES,"|",s.PLANT)) AS total', false);
        $this->db->from('mst_sales s');

        $this->db->where('s.DELETED IS NULL', null, false);
        $this->db->where('s.JENIS_PAY', 'TEMPO');

        if (!empty($filters['plant'])) {
            if (is_array($filters['plant'])) {

                $this->db->group_start();
                $this->db->where_in('s.PLANT', $filters['plant']);
                $this->db->or_where('s.PLANT', '*');
                $this->db->group_end();

            } else {

                if ($filters['plant'] === '*') {

                    $userPlants = $this->session->userdata('plant');

                    $this->db->group_start();
                    $this->db->where_in('s.PLANT', $userPlants);
                    $this->db->or_where('s.PLANT', '*');
                    $this->db->group_end();

                } else {
                    $this->db->where('s.PLANT', $filters['plant']);
                }
            }
        }

        if (!empty($filters['customer'])) {
            $this->db->where('s.CUSTOMER', $filters['customer']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('s.SALES_DATE >=', $filters['date_from'].' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('s.SALES_DATE <=', $filters['date_to'].' 23:59:59');
        }

        $row = $this->db->get()->row();
        return (int)($row->total ?? 0);
    }

    public function get_daily_summary($plant, $date)
    {
        return $this->db->query("
            SELECT
                ? AS PLANT,
                ? AS REPORT_DATE,

                COALESCE(sales.total_sales,0) AS TOTAL_SALES,
                COALESCE(sales.sales_cash,0) AS SALES_CASH,
                COALESCE(sales.sales_tempo,0) AS SALES_TEMPO,

                COALESCE(ar_opening.ar_opening,0) AS AR_OPENING,
                COALESCE(ar_collection.ar_collection,0) AS AR_COLLECTION,

                (
                    COALESCE(ar_opening.ar_opening,0)
                    + COALESCE(sales.sales_tempo,0)
                    - COALESCE(ar_collection.ar_collection,0)
                ) AS AR_CLOSING,

                COALESCE(cash_open.cash_opening,0) AS CASH_OPENING,
                COALESCE(deposit.deposit_today,0) AS DEPOSIT_TODAY,
                COALESCE(cost.cost_today,0) AS COST_TODAY,

                (
                    COALESCE(cash_open.cash_opening,0)
                    + COALESCE(sales.sales_cash,0)
                    + COALESCE(ar_collection.ar_collection,0)
                    + COALESCE(deposit.deposit_today,0)
                    - COALESCE(cost.cost_today,0)
                ) AS CASH_CLOSING,

                COALESCE(method_breakdown.sales_method_cash,0) AS SALES_METHOD_CASH,
                COALESCE(method_breakdown.sales_method_transfer,0) AS SALES_METHOD_TRANSFER,
                COALESCE(method_breakdown.cashin_method_cash,0) AS CASHIN_METHOD_CASH,
                COALESCE(method_breakdown.cashin_method_transfer,0) AS CASHIN_METHOD_TRANSFER,
                (
                    COALESCE(method_breakdown.sales_method_cash,0)
                    + COALESCE(method_breakdown.cashin_method_cash,0)
                ) AS TOTAL_METHOD_CASH,
                (
                    COALESCE(method_breakdown.sales_method_transfer,0)
                    + COALESCE(method_breakdown.cashin_method_transfer,0)
                ) AS TOTAL_METHOD_TRANSFER

            FROM (SELECT 1) dummy

            LEFT JOIN (
                SELECT
                    SUM(AMOUNT) AS total_sales,
                    SUM(CASE WHEN JENIS_PAY='LUNAS' THEN AMOUNT ELSE 0 END) AS sales_cash,
                    SUM(CASE WHEN JENIS_PAY='TEMPO' THEN AMOUNT ELSE 0 END) AS sales_tempo
                FROM mst_sales
                WHERE SALES_DATE >= ?
                AND SALES_DATE < DATE_ADD(?, INTERVAL 1 DAY)
                AND PLANT=?
                AND DELETED IS NULL
            ) sales ON 1=1

            LEFT JOIN (
                SELECT SUM(REMAIN) AS ar_opening
                FROM mst_sales
                WHERE SALES_DATE < ?
                AND PLANT=?
                AND JENIS_PAY='TEMPO'
                AND DELETED IS NULL
            ) ar_opening ON 1=1

            LEFT JOIN (
                SELECT SUM(d.AMOUNT_OFFSET) AS ar_collection
                FROM mst_cash_in_detail d
                JOIN mst_cash_in h 
                    ON h.CASH_IN=d.CASH_IN
                    AND h.PLANT=d.PLANT
                WHERE h.CASHIN_DATE=?
                AND h.PLANT=?
                AND d.DELETED IS NULL
            ) ar_collection ON 1=1

            LEFT JOIN (
                SELECT
                    (
                        SELECT COALESCE(SUM(AMOUNT),0)
                        FROM mst_cash_in
                        WHERE CASHIN_DATE < ?
                        AND PLANT=?
                        AND DELETED IS NULL
                    )
                    -
                    (
                        SELECT COALESCE(SUM(d.TOTAL),0)
                        FROM mst_cost c
                        JOIN mst_cost_detail d
                            ON c.COST=d.COST
                            AND c.PLANT=d.PLANT
                        WHERE c.COST_DATE < ?
                        AND c.PLANT=?
                        AND c.DELETED IS NULL
                        AND d.DELETED IS NULL
                    )
                    AS cash_opening
            ) cash_open ON 1=1

            LEFT JOIN (
                SELECT SUM(AMOUNT) AS deposit_today
                FROM mst_customer_deposit
                WHERE DATE(CREATED_AT)=?
                AND PLANT=?
            ) deposit ON 1=1

            LEFT JOIN (
                SELECT SUM(d.TOTAL) AS cost_today
                FROM mst_cost c
                JOIN mst_cost_detail d
                    ON c.COST=d.COST
                    AND c.PLANT=d.PLANT
                WHERE c.COST_DATE >= ?
                AND c.COST_DATE < DATE_ADD(?, INTERVAL 1 DAY)
                AND c.PLANT=?
                AND c.DELETED IS NULL
                AND d.DELETED IS NULL
            ) cost ON 1=1

            LEFT JOIN (
                SELECT
                    -- SALES (LUNAS) BY METHOD
                    SUM(
                        CASE 
                            WHEN s.JENIS_PAY='LUNAS'
                            AND (s.PEMBAYARAN='CASH' OR s.PEMBAYARAN='TUNAI')
                            THEN s.AMOUNT ELSE 0
                        END
                    ) AS sales_method_cash,

                    SUM(
                        CASE 
                            WHEN s.JENIS_PAY='LUNAS'
                            AND s.PEMBAYARAN='TRANSFER'
                            THEN s.AMOUNT ELSE 0
                        END
                    ) AS sales_method_transfer,

                    -- CASH IN BY METHOD
                    (
                        SELECT COALESCE(SUM(h.AMOUNT),0)
                        FROM mst_cash_in h
                        WHERE h.CASHIN_DATE >= ?
                        AND h.CASHIN_DATE < DATE_ADD(?, INTERVAL 1 DAY)
                        AND h.PLANT=?
                        AND h.DELETED IS NULL
                        AND (h.PEMBAYARAN='CASH' OR h.PEMBAYARAN='TUNAI')
                    ) AS cashin_method_cash,

                    (
                        SELECT COALESCE(SUM(h.AMOUNT),0)
                        FROM mst_cash_in h
                        WHERE h.CASHIN_DATE >= ?
                        AND h.CASHIN_DATE < DATE_ADD(?, INTERVAL 1 DAY)
                        AND h.PLANT=?
                        AND h.DELETED IS NULL
                        AND h.PEMBAYARAN='TRANSFER'
                    ) AS cashin_method_transfer

                FROM mst_sales s
                WHERE s.SALES_DATE >= ?
                AND s.SALES_DATE < DATE_ADD(?, INTERVAL 1 DAY)
                AND s.PLANT=?
                AND s.DELETED IS NULL
            ) method_breakdown ON 1=1

        ", [
            $plant,
            $date,

            $date, $date, $plant,

            $date, $plant,

            $date, $plant,

            $date, $plant,
            $date, $plant,

            $date, $plant,

            $date, $date, $plant,
            $date, $date, $plant,   // cashin cash
            $date, $date, $plant,   // cashin transfer
            $date, $date, $plant,   // sales breakdown

        ])->row();
    }

    public function get_daily_sales_detail($plant, $date)
    {
        return $this->db->query("
            SELECT
                s.SALES,
                s.SALES_DATE,
                s.CUSTOMER,
                c.FULL_NAME AS CUSTOMER_NAME,
                s.JENIS_PAY,
                s.AMOUNT AS SALES_TOTAL,

                d.SEQ_NO,
                d.ITEM,
                i.FULL_NAME AS ITEM_NAME,

                d.QTY,
                d.BERAT,

                -- 🔥 Logic Quantity Display
                CASE 
                    WHEN d.QTY = 0 AND d.BERAT > 0 THEN d.BERAT
                    ELSE d.QTY
                END AS DISPLAY_QTY,

                CASE 
                    WHEN d.QTY = 0 AND d.BERAT > 0 THEN 'BERAT'
                    ELSE 'QTY'
                END AS DISPLAY_TYPE,

                d.AMOUNT AS DETAIL_AMOUNT

            FROM mst_sales s

            JOIN mst_sales_detail d
                ON s.SALES = d.SALES
                AND s.PLANT = d.PLANT

            LEFT JOIN cd_customer c
                ON c.CUST = s.CUSTOMER

            LEFT JOIN cd_item i
                ON i.ITEM = d.ITEM

            WHERE s.SALES_DATE >= ?
            AND s.SALES_DATE < DATE_ADD(?, INTERVAL 1 DAY)
            AND s.PLANT = ?
            AND s.DELETED IS NULL
            AND d.DELETED IS NULL

            ORDER BY s.SALES, d.SEQ_NO
        ", [$date, $date, $plant])->result();
    }

    public function get_daily_cash_detail($plant, $date)
    {
        return $this->db->query("
            SELECT
                h.CASH_IN,
                h.CASHIN_DATE,
                h.CUSTOMER,
                COALESCE(c.FULL_NAME, h.CUSTOMER) AS CUSTOMER_NAME,
                CAST(h.AMOUNT AS DECIMAL(20,2)) AS CASH_TOTAL,

                d.SALES,
                CAST(d.AMOUNT_OFFSET AS DECIMAL(20,2)) AS AMOUNT_OFFSET

            FROM mst_cash_in h

            LEFT JOIN mst_cash_in_detail d
                ON h.CASH_IN = d.CASH_IN
                AND h.PLANT = d.PLANT
                AND d.DELETED IS NULL

            LEFT JOIN cd_customer c
                ON c.CUST = h.CUSTOMER

            WHERE h.CASHIN_DATE = ?
            AND h.PLANT = ?
            AND h.DELETED IS NULL

            ORDER BY h.CASH_IN
        ", [$date, $plant])->result();
    }

    public function get_daily_cost_detail($plant, $date)
    {
        return $this->db->query("
            SELECT
                c.COST,
                c.COST_DATE,
                c.PEMBAYARAN,

                d.SEQ_NO,
                d.TIPE_COST,
                d.REMARK,

                COALESCE(mc.COST_NAME, d.TIPE_COST) AS COST_NAME,

                CAST(d.TOTAL AS DECIMAL(20,2)) AS TOTAL

            FROM mst_cost c

            JOIN mst_cost_detail d
                ON c.COST = d.COST
                AND c.PLANT = d.PLANT
                AND d.DELETED IS NULL

            LEFT JOIN cd_cost mc
                ON mc.COST = d.TIPE_COST

            WHERE DATE(c.COST_DATE) = ?
            AND c.PLANT = ?
            AND c.DELETED IS NULL

            ORDER BY c.COST, d.SEQ_NO
        ", [$date, $plant])->result();
    }

    private function get_plant_name($plant)
    {
        $row = $this->db->get_where('cd_code', ['HEAD_CODE'=>'AJ','CODE'=>$plant])->row();
        return $row->CODE_NAME ?? $plant;
    }

}
