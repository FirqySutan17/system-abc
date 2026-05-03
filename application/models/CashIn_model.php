<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CashIn_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */
    public function get_data($limit, $start, $search = '', $order = 'CASHIN_DATE', $dir = 'DESC')
    {
        $username = $this->session->userdata('username');
        $plants   = $this->get_user_plants($username);

        $this->db->select('
            h.CASH_IN,
            h.PLANT,
            c.CODE_NAME AS PLANT_NAME,
            h.CASHIN_DATE,
            h.CUSTOMER,
            cust.FULL_NAME AS CUSTOMER_NAME,
            h.AMOUNT,
            h.SLIP_NO,
            h.PEMBAYARAN,
            h.BON,
            (
                SELECT COUNT(*) 
                FROM mst_cash_in newer
                WHERE newer.CUSTOMER = h.CUSTOMER
                AND newer.PLANT = h.PLANT
                AND newer.CASHIN_DATE > h.CASHIN_DATE
                AND newer.DELETED IS NULL
            ) AS IS_LOCKED
        ');
        $this->db->from('mst_cash_in h');

        $this->db->join('cd_code c', "c.CODE = h.PLANT AND c.HEAD_CODE = 'AJ'", 'left');
        $this->db->join('cd_customer cust', 'cust.CUST = h.CUSTOMER', 'left');

        // filter deleted
        $this->db->where('h.DELETED IS NULL', null, false);

        /*
        ==============================
        FILTER PLANT BERDASARKAN USER
        ==============================
        */
        if (!empty($plants)) {
            $this->db->where_in('h.PLANT', $plants);
        } else {
            $this->db->where('1 = 0'); // user tidak punya plant
        }

        // search
        if ($search != '') {
            $this->db->group_start();
            $this->db->like('h.CASH_IN', $search);
            $this->db->or_like('cust.FULL_NAME', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->or_like('h.BON', $search);
            $this->db->group_end();
        }

        $allowedOrder = ['CASH_IN','CASHIN_DATE','CUSTOMER','AMOUNT','SLIP_NO','PEMBAYARAN'];
        if (!in_array($order, $allowedOrder)) {
            $order = 'CASHIN_DATE';
        }

        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        $this->db->order_by("h.$order", $dir);
        $this->db->limit((int)$limit, (int)$start);

        return $this->db->get()->result_array();
    }

    public function count_data($search = '')
    {
        $username = $this->session->userdata('username');
        $plants   = $this->get_user_plants($username);

        $this->db->from('mst_cash_in h');
        $this->db->join('cd_code c', "c.CODE = h.PLANT AND c.HEAD_CODE = 'AJ'", 'left');
        $this->db->join('cd_customer cust', 'cust.CUST = h.CUSTOMER', 'left');

        $this->db->where('h.DELETED IS NULL', null, false);

        /*
        ==============================
        FILTER PLANT BERDASARKAN USER
        ==============================
        */
        if (!empty($plants)) {
            $this->db->where_in('h.PLANT', $plants);
        } else {
            $this->db->where('1 = 0');
        }

        if ($search != '') {
            $this->db->group_start();
            $this->db->like('h.CASH_IN', $search);
            $this->db->or_like('cust.FULL_NAME', $search);
            $this->db->or_like('c.CODE_NAME', $search);
            $this->db->or_like('h.BON', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    private $plantCache = [];

    public function get_user_plants($username)
    {
        if (isset($this->plantCache[$username])) {
            return $this->plantCache[$username];
        }

        $row = $this->db
            ->select('plant')
            ->from('users')
            ->where('username', $username)
            ->get()
            ->row();

        if (!$row || empty($row->plant)) {
            return $this->plantCache[$username] = [];
        }

        $plants = json_decode($row->plant, true);

        return $this->plantCache[$username] = (
            is_array($plants) ? array_map('strval', $plants) : []
        );
    }

    public function get_plant_select2_by_user($username)
    {
        $plantCodes = $this->get_user_plants($username);

        if (empty($plantCodes)) {
            return [];
        }

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result_array();
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

    public function get_header($plant, $cashIn)
    {
        return $this->db
            ->from('mst_cash_in')
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashIn
            ])
            ->get()
            ->row_array();
    }

    public function search_customer($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('cd_customer');
        $this->db->where('STATUS', 'N');

        // 🔥 FILTER CUSTOMER
        $this->db->group_start();
            $this->db->where('CUST_KIND', 'CUSTOMER');
            $this->db->or_where('CUST_CLASS', 'CUSTOMER');
        $this->db->group_end();

        if ($q) {
            $this->db->group_start();
            $this->db->like('CUST', $q);
            $this->db->or_like('FULL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');
        $this->db->limit($limit);
        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['name']
            ];
        }
        return $out;
    }

    public function search_rekening($q = null, $limit = 20)
    {
        $this->db->select('CODE as id, CODE_NAME as name');
        $this->db->from('cd_code');

        // 🔥 FILTER UTAMA
        $this->db->where('HEAD_CODE', 'AK');
        $this->db->where('CODE !=', '*');

        // 🔍 SEARCH
        if ($q) {
            $this->db->group_start();
                $this->db->like('CODE', $q);
                $this->db->or_like('CODE_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CODE', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['name']
            ];
        }

        return $out;
    }

    public function get_plant_code_by_id($id)
    {
        return $this->db
            ->select('code')
            ->from('cd_code')
            ->where([
                'head_code' => 'AJ',
                'id'        => $id
            ])
            ->get()
            ->row('code');
    }

    public function get_plant_code_by_code($code)
    {
        return $this->db
            ->select('code')
            ->from('cd_code')
            ->where([
                'head_code' => 'AJ',
                'code'      => $code
            ])
            ->get()
            ->row('code');
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */
    public function generate_cash_in_no($plant, $date)
    {
        $ymd = date('Ymd', strtotime($date));
        $prefix = $ymd . 'CI';
        
        $this->db->select('CASH_IN');
        $this->db->from('mst_cash_in');
        $this->db->where('PLANT', $plant);
        $this->db->like('CASH_IN', $prefix, 'after');
        $this->db->order_by('CASH_IN', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        if ($row) {
            $seq = (int)substr($row->CASH_IN, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generate_slip_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'AG';

        $this->db->select('SLIP_NO');
        $this->db->from('mst_cash_in');
        $this->db->where('PLANT', $plant);
        $this->db->like('SLIP_NO', $prefix, 'after');
        $this->db->order_by('SLIP_NO', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int)substr($row->SLIP_NO, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function get_invoice_remain($sales, $plant)
    {
        return $this->db
            ->select('CUSTOMER as customer, AMOUNT as invoice_amount, REMAIN as remain')
            ->from('mst_sales')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->get()
            ->row_array();
    }

    public function insert_customer_deposit($data)
    {
        return $this->db->insert('mst_customer_deposit', $data);
    }

    public function use_deposit($id, $amount)
    {
        $this->db->set('REMAIN', "REMAIN - {$amount}", false)
                ->where('ID', $id)
                ->update('mst_customer_deposit');
    }

    public function get_available_deposit($customer, $plant)
    {
        return $this->db
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->order_by('CREATED_AT', 'ASC') // FIFO deposit
            ->get('mst_customer_deposit')
            ->result_array();
    }

    public function get_total_deposit($customer, $plant)
    {
        return $this->db->select_sum('REMAIN')
            ->from('mst_customer_deposit')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->get()
            ->row()
            ->REMAIN ?? 0;
    }

    public function get_invoice_remain_batch($salesList, $plant)
    {
        $this->db->select('SALES, CUSTOMER, AMOUNT, REMAIN');
        $this->db->from('mst_sales');
        $this->db->where('PLANT', $plant);
        $this->db->where_in('SALES', $salesList);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[$r['SALES']] = $r;
        }
        return $out;
    }

    // public function reduce_invoice_batch($updates)
    // {
    //     foreach ($updates as $u) {
    //         $this->db->set('REMAIN', 'REMAIN - '.$u['amount'], false);
    //         $this->db->where('SALES', $u['sales']);
    //         $this->db->where('PLANT', $u['plant']);
    //         $this->db->update('mst_sales');
    //     }
    // }

    public function get_cash_in_details($cashIn, $plant){
        return $this->db->get_where('mst_cash_in_detail', [
            'CASH_IN'=>$cashIn,'PLANT'=>$plant,'DELETED'=>null
        ])->result_array();
    }

    // public function restore_invoice_offset($sales,$plant,$amount){
    //     $this->db->set('AMOUNT_PAID',"AMOUNT_PAID-$amount",false)
    //             ->where(['SALES'=>$sales,'PLANT'=>$plant])
    //             ->update('mst_sales');
    // }

    // public function rollback_deposit_usage($cashIn)
    // {
    //     $this->db->set('REMAIN','REMAIN+USED_AMOUNT',false)
    //             ->where('CASH_IN',$cashIn)
    //             ->update('mst_customer_deposit');
    // }

    public function delete_cash_in_details($cashIn,$plant){
        $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
                ->delete('mst_cash_in_detail');
    }

    // public function restore_invoice_batch($updates)
    // {
    //     foreach ($updates as $u) {
    //         $this->db->set('REMAIN', 'REMAIN + '.$u['amount'], false);
    //         $this->db->where('SALES', $u['sales']);
    //         $this->db->where('PLANT', $u['plant']);
    //         $this->db->update('mst_sales');
    //     }
    // }

    public function restore_deposit_usage_by_cash_in($cashIn)
    {
        $this->db->set('REMAIN', 'AMOUNT', false);
        $this->db->where('CASH_IN', $cashIn);
        $this->db->update('mst_customer_deposit');
    }

    public function recalc_invoice($sales, $plant)
    {
        $this->db->query("
            SELECT SALES 
            FROM mst_sales 
            WHERE SALES = ? AND PLANT = ? 
            FOR UPDATE
        ", [$sales, $plant]);

        $inv = $this->db->select('AMOUNT')
            ->from('mst_sales')
            ->where([
                'SALES' => $sales,
                'PLANT' => $plant
            ])
            ->get()->row_array();

        if (!$inv) return;

        $invoiceAmount = (float)$inv['AMOUNT'];

        // 🔥 Semua pembayaran termasuk DP dibaca dari sini
        $paid = $this->db->select('IFNULL(SUM(AMOUNT_OFFSET),0) AS TOTAL')
            ->from('mst_cash_in_detail')
            ->where([
                'SALES'   => $sales,
                'PLANT'   => $plant,
                'DELETED' => null
            ])
            ->get()->row_array();

        $totalPaid = (float)$paid['TOTAL'];

        // ✅ DP TIDAK DIKURANGI LAGI
        $remain = max($invoiceAmount - $totalPaid, 0);

        if ($remain <= 0) {
            $status = 'PAID';
        } elseif ($totalPaid > 0) {
            $status = 'PARTIAL';
        } else {
            $status = 'UNPAID';
        }

        $this->db->where([
                'SALES' => $sales,
                'PLANT' => $plant
            ])
            ->update('mst_sales', [
                'REMAIN'     => $remain,
                'STATUS'     => $status,
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ]);
    }

    public function delete_deposit_by_cash_in($cashIn)
    {
        $this->db->where('CASH_IN', $cashIn);
        $this->db->delete('mst_customer_deposit');
    }

    public function delete_deposit_by_cashin($cashIn){
        $this->db->where('CASH_IN',$cashIn)
                ->delete('mst_customer_deposit');
    }

    public function get_fifo_open_invoices($customer, $plant)
    {
        return $this->db
            ->from('mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO') // 🔥 WAJIB TEMPO
            ->where('REMAIN >', 0)
            ->group_start()               // 🔥 BELUM LUNAS
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->order_by('SALES_DATE', 'ASC')
            ->order_by('SALES', 'ASC')
            ->get()
            ->result_array();
    }

    public function reduce_invoice_remain($sales, $plant, $amount)
    {
        // Hanya lock untuk mencegah race
        $this->lock_invoice_row($sales, $plant);

        // Tidak perlu update remain di sini
        return true;
    }

    public function restore_invoice_remain($sales, $plant, $amount)
    {
        $this->db->set('REMAIN', 'REMAIN + '.$amount, false)
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->update('mst_sales');
    }

    public function get_next_seq($cashIn, $plant)
    {
        $row = $this->db->select_max('SEQ_NO')
            ->where('CASH_IN',$cashIn)
            ->where('PLANT',$plant)
            ->get('mst_cash_in_detail')
            ->row();

        return ($row->SEQ_NO ?? 0) + 1;
    }

    public function lock_invoice_row($sales, $plant)
    {
        return $this->db->query("
            SELECT SALES
            FROM mst_sales
            WHERE SALES = ? AND PLANT = ?
            FOR UPDATE
        ", [$sales, $plant])->row();
    }

    public function simulate_fifo($customer, $plant, $newAmount)
    {
        $allocations = [];

        // =============================
        // 1. Ambil DEPOSIT aktif
        // =============================
        $deposit = $this->db->select_sum('REMAIN')
            ->from('mst_customer_deposit')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->get()
            ->row()
            ->REMAIN ?? 0;

        $available = $deposit + $newAmount;

        // =============================
        // 2. Ambil INVOICE TEMPO FIFO
        // =============================
        $invoices = $this->get_fifo_open_invoices($customer, $plant);

        foreach ($invoices as $inv) {

            if ($available <= 0) break;

            $remainInvoice = (float)$inv['REMAIN'];
            if ($remainInvoice <= 0) continue;

            $offset = min($remainInvoice, $available);

            $allocations[] = [
                'sales'                 => $inv['SALES'],
                'sales_date'            => $inv['SALES_DATE'],
                'invoice_amount'        => (float)$inv['AMOUNT'],
                'invoice_remain_before' => $remainInvoice,
                'offset'                => $offset
            ];

            $available -= $offset;
        }

        return [
            'allocations'       => $allocations,
            'deposit_used'      => min($deposit, $deposit + $newAmount),
            'deposit_remaining' => $available > 0 ? $available : 0
        ];
    }

    public function get_valid_invoice_for_payment($sales, $customer, $plant)
    {
        return $this->db
            ->select('SALES, AMOUNT, REMAIN, SALES_DATE')
            ->from('mst_sales')
            ->where('SALES', $sales)
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO')
            ->where('REMAIN >', 0)
            ->group_start()
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->get()
            ->row_array();
    }

    public function get_all_open_tempo_invoices($customer, $plant)
    {
        $rows = $this->db
            ->select('SALES, SALES_DATE, AMOUNT, REMAIN')
            ->from('mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('JENIS_PAY', 'TEMPO')
            ->where('REMAIN >', 0)
            ->group_start()
                ->where('STATUS !=', 'PAID')
                ->or_where('STATUS IS NULL', null, false)
            ->group_end()
            ->order_by('SALES_DATE', 'ASC')
            ->order_by('SALES', 'ASC')
            ->get()
            ->result_array();

        // 🔥 PENTING: ubah decimal string jadi float
        foreach ($rows as &$r) {
            $r['AMOUNT'] = (float)$r['AMOUNT'];
            $r['REMAIN'] = (float)$r['REMAIN'];
        }

        return $rows;
    }

    public function deposit_still_used($cashIn)
    {
        return $this->db
            ->where('CASH_IN_REF', $cashIn) // kolom referensi saat deposit dipakai
            ->count_all_results('mst_customer_deposit_usage') > 0;
    }

    public function deposit_has_remain($cashIn)
    {
        return $this->db
            ->where('CASH_IN', $cashIn)
            ->where('REMAIN < AMOUNT', null, false)
            ->count_all_results('mst_customer_deposit') > 0;
    }

    public function rebuild_customer_payment_history($customer, $plant)
    {
        $this->db->trans_start();

        /* =========================
        1. RESET SEMUA INVOICE (DP AWARE)
        REMAIN = TOTAL - DP
        ========================= */
        $this->db->set('REMAIN', 'AMOUNT - DP_AMOUNT', false)
                ->set('STATUS', "'UNPAID'", false)
                ->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->update('mst_sales');


        /* =========================
        2. HAPUS SEMUA DEPOSIT CUSTOMER
        ========================= */
        $this->db->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->delete('mst_customer_deposit');


        /* =========================
        3. HAPUS SEMUA DETAIL OFFSET
        (berdasarkan header CASH IN milik customer tsb)
        ========================= */
        $this->db->where_in('CASH_IN', function($db) use ($customer,$plant){
            $db->select('CASH_IN')
            ->from('mst_cash_in')
            ->where('CUSTOMER',$customer)
            ->where('PLANT',$plant);
        }, false)->delete('mst_cash_in_detail');


        /* =========================
        4. AMBIL SEMUA CASH IN URUT TANGGAL (FIFO GLOBAL)
        ========================= */
        $cashins = $this->db->from('mst_cash_in')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->order_by('CASHIN_DATE', 'ASC')
            ->order_by('CASH_IN', 'ASC')
            ->get()->result_array();


        foreach ($cashins as $cash) {

            $cashInNo = $cash['CASH_IN'];
            $amount   = (float)$cash['AMOUNT'];
            $seq      = 1;

            /* =========================
            4A. APPLY KE INVOICE (FIFO)
            ========================= */
            $invoices = $this->db->from('mst_sales')
                ->where('CUSTOMER', $customer)
                ->where('PLANT', $plant)
                ->where('REMAIN >', 0)
                ->order_by('SALES_DATE', 'ASC')
                ->get()->result_array();

            foreach ($invoices as $inv) {

                if ($amount <= 0) break;

                $remainInvoice = (float)$inv['REMAIN'];
                if ($remainInvoice <= 0) continue;

                $offset = min($remainInvoice, $amount);

                /* INSERT DETAIL OFFSET */
                $this->db->insert('mst_cash_in_detail', [
                    'CASH_IN'        => $cashInNo,
                    'PLANT'          => $plant,
                    'SEQ_NO'         => $seq++,
                    'SALES'          => $inv['SALES'],
                    'AMOUNT_INVOICE' => $inv['AMOUNT'],
                    'AMOUNT_OFFSET'  => $offset,
                    'DATE_OFFSET'    => $cash['CASHIN_DATE'],
                    'ORG_SLIP_NO'    => 'AUTO',
                    'SLIP_NO'        => 'AUTO',
                    'CREATED_AT'     => date('Y-m-d H:i:s')
                ]);

                /* KURANGI REMAIN INVOICE */
                $this->db->set('REMAIN', "REMAIN - $offset", false)
                        ->where('SALES', $inv['SALES'])
                        ->where('PLANT', $plant)
                        ->update('mst_sales');

                $amount -= $offset;
            }

            /* =========================
            4B. SISA UANG → DEPOSIT
            ========================= */
            if ($amount > 0) {
                $this->db->insert('mst_customer_deposit', [
                    'CUSTOMER'   => $customer,
                    'PLANT'      => $plant,
                    'CASH_IN'    => $cashInNo,
                    'AMOUNT'     => $amount,
                    'REMAIN'     => $amount,
                    'CREATED_AT' => date('Y-m-d H:i:s')
                ]);
            }
        }


        /* =========================
        5. HITUNG ULANG STATUS INVOICE
        ========================= */
        $salesList = $this->db->select('SALES, AMOUNT, DP_AMOUNT, REMAIN')
            ->from('mst_sales')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->get()->result_array();

        foreach ($salesList as $s) {

            $totalTagihan = $s['AMOUNT'] - $s['DP_AMOUNT'];
            $remain       = (float)$s['REMAIN'];

            if ($remain <= 0) {
                $status = 'PAID';
            } elseif ($remain < $totalTagihan) {
                $status = 'PARTIAL';
            } else {
                $status = 'UNPAID';
            }

            $this->db->where('SALES', $s['SALES'])
                    ->where('PLANT', $plant)
                    ->update('mst_sales', ['STATUS' => $status]);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function is_cash_in_locked($cashInNo, $customer, $plant)
    {
        $current = $this->db->select('CASHIN_DATE')
            ->from('mst_cash_in')
            ->where('CASH_IN', $cashInNo)
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->get()
            ->row();

        if (!$current) return true;

        $exists = $this->db->from('mst_cash_in')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('CASHIN_DATE >', $current->CASHIN_DATE)
            ->count_all_results();

        return $exists > 0;
    }

    public function get_open_deposits_fifo($customer, $plant)
    {
        return $this->db->from('mst_customer_deposit')
            ->where('CUSTOMER', $customer)
            ->where('PLANT', $plant)
            ->where('REMAIN >', 0)
            ->order_by('CREATED_AT', 'ASC') // FIFO deposit lama dulu
            ->get()->result_array();
    }

    public function reduce_deposit($id, $amount)
    {
        $this->db->set('REMAIN', "REMAIN - {$amount}", false)
            ->where('ID', $id)
            ->update('mst_customer_deposit');
    }

    public function sales_has_payment($sales, $plant)
    {
        return $this->db
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO !=', 'AUTO_DP') // abaikan DP otomatis
            ->where('DELETED IS NULL', null, false)
            ->count_all_results('mst_cash_in_detail') > 0;
    }

    public function delete_dp_by_sales($sales, $plant)
    {
        // Ambil semua header yang terpengaruh
        $headers = $this->db
            ->select('CASH_IN')
            ->from('mst_cash_in_detail')
            ->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO', 'AUTO_DP')
            ->group_by('CASH_IN')
            ->get()
            ->result_array();

        if (!$headers) return;

        // Hapus semua AUTO DP detail sekaligus
        $this->db->where('SALES', $sales)
            ->where('PLANT', $plant)
            ->where('ORG_SLIP_NO', 'AUTO_DP')
            ->delete('mst_cash_in_detail');

        foreach ($headers as $h) {
            $cashIn = $h['CASH_IN'];

            // Cek sisa detail
            $remain = $this->db
                ->where('CASH_IN', $cashIn)
                ->where('PLANT', $plant)
                ->count_all_results('mst_cash_in_detail');

            if ($remain == 0) {
                $this->db->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->delete('mst_cash_in');
            } else {
                $total = $this->db
                    ->select_sum('AMOUNT_OFFSET')
                    ->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->get('mst_cash_in_detail')
                    ->row()->AMOUNT_OFFSET ?? 0;

                $this->db->where('CASH_IN', $cashIn)
                    ->where('PLANT', $plant)
                    ->update('mst_cash_in', ['AMOUNT' => $total]);
            }
        }
    }

    // public function reduce_invoice_remain($sales, $plant, $amount)
    // {
    //     $this->db->set('REMAIN', "REMAIN - {$amount}", false);
    //     $this->db->where([
    //         'SALES' => $sales,
    //         'PLANT' => $plant
    //     ]);
    //     $this->db->update('mst_sales');
    // }

    // public function restore_invoice_remain($sales, $plant, $amount)
    // {
    //     $this->db->set('REMAIN', "REMAIN + {$amount}", false);
    //     $this->db->where([
    //         'SALES' => $sales,
    //         'PLANT' => $plant
    //     ]);
    //     $this->db->update('mst_sales');
    // }

    public function get_detail_by_cash_in($plant, $cashInNo)
    {
        return $this->db
            ->from('mst_cash_in_detail')
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->get()
            ->result_array();
    }

    public function lock_invoice($sales, $plant)
    {
        return $this->db
            ->query("
                SELECT SALES
                FROM mst_sales
                WHERE SALES = ? AND PLANT = ?
                FOR UPDATE
            ", [$sales, $plant])
            ->row_array();
    }

    // public function update_invoice_status($sales, $plant)
    // {
    //     $row = $this->db
    //         ->select('AMOUNT, REMAIN')
    //         ->from('mst_sales')
    //         ->where('SALES', $sales)
    //         ->where('PLANT', $plant)
    //         ->get()
    //         ->row_array();

    //     if (!$row) return false;

    //     if ($row['REMAIN'] <= 0) {
    //         $status = 'PAID';
    //     } elseif ($row['REMAIN'] < $row['AMOUNT']) {
    //         $status = 'PARTIAL';
    //     } else {
    //         $status = 'OPEN';
    //     }

    //     return $this->db
    //         ->where('SALES', $sales)
    //         ->where('PLANT', $plant)
    //         ->update('mst_sales', [
    //             'STATUS'     => $status,
    //             'UPDATED_AT' => date('Y-m-d H:i:s')
    //         ]);
    // }

    public function update_header_amount($cashIn, $plant, $amount)
    {
        return $this->db->where([
            'CASH_IN' => $cashIn,
            'PLANT'   => $plant
        ])->update('mst_cash_in', [
            'AMOUNT' => $amount
        ]);
    }

    // public function rollback_invoice_offset($sales, $plant, $amount)
    // {
    //     $this->db->set('PAID_AMOUNT', 'PAID_AMOUNT - '.$amount, false);
    //     $this->db->set('REMAIN', 'REMAIN + '.$amount, false);
    //     $this->db->where([
    //         'SALES' => $sales,
    //         'PLANT' => $plant
    //     ]);
    //     $this->db->update('mst_sales');
    // }

    /* ---------------------------------------------------------
       HEADER OPERATIONS
    --------------------------------------------------------- */
    public function insert_header($data)
    {
        return $this->db->insert('mst_cash_in', $data);
    }

    public function update_header($plant, $cashInNo, $data)
    {
        $this->db
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->update('mst_cash_in', $data);
    }

    /* ---------------------------------------------------------
       DETAIL OPERATIONS
    --------------------------------------------------------- */
    public function delete_header($cashIn,$plant){
        $this->db->where(['CASH_IN'=>$cashIn,'PLANT'=>$plant])
                ->delete('mst_cash_in');
    }

    public function delete_detail($plant, $cashInNo)
    {
        $this->db
            ->where([
                'PLANT'   => $plant,
                'CASH_IN' => $cashInNo
            ])
            ->delete('mst_cash_in_detail');
    }

    public function insert_detail_batch($rows)
    {
        return $this->db->insert_batch('mst_cash_in_detail', $rows);
    }
}
