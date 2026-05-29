<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportClosingSalesPl_model extends CI_Model {

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
            ->where('CODE !=', '*')
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

    public function get_daily_sales_pl($limit, $start, $filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $this->db->select("
            a.plant,
            p.CODE_NAME AS plant_name,
            a.ymd,

            a.item,
            i.full_name AS item_name,
            cls.code_name AS class_name,

            a.bg_bw AS bg_bw,
            a.bg_amount / NULLIF(a.bg_bw,0) AS bg_up,
            a.bg_amount AS begin_amt,

            a.pd_bw AS production_bw,
            a.pd_amount / NULLIF(a.pd_bw,0) AS production_up,
            a.pd_amount AS production_amt,

            a.pr_bw AS purchase_bw,
            a.pr_amount / NULLIF(a.pr_bw,0) AS purchase_up,
            a.pr_amount AS purchase_amt,

            a.ds_bw AS adjust_bw,
            a.ds_amount / NULLIF(a.ds_bw,0) AS adjust_up,
            a.ds_amount AS adjust_amt,

            a.sl_cost_bw AS cogs_bw,
            a.sl_cost_amount / NULLIF(a.sl_cost_bw,0) AS cogs_up,
            a.sl_cost_amount AS cogs_amt,

            a.end_amount AS ending_amt,
            a.sl_net_amount AS sales_net_amt,
            a.sl_profit AS sales_profit_amt
        ", false);

        $this->db->from('abc_cl_sales_pl a');

        $this->db->join('abc_cd_item i','i.item=a.item','left');
        $this->db->join('abc_cd_code cls',"cls.head_code='GT' AND cls.code=i.item_class",'left');
        $this->db->join('abc_cd_code p',"p.head_code='PLANT' AND p.code=a.plant",'left');

        $this->db->where("a.plant IN ($plantList)", null, false);

        if (!empty($filters['date'])) {
            $this->db->where('a.ymd', $filters['date']);
        }

        $this->db->order_by('a.item','ASC');

        if ($limit > 0) {
            $this->db->limit($limit,$start);
        }

        return $this->db->get()->result();
    }

    public function count_daily_sales_pl($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $this->db->from('abc_cl_sales_pl a');
        $this->db->where("a.plant IN ($plantList)", null, false);

        if (!empty($filters['date'])) {
            $this->db->where('a.ymd', $filters['date']);
        }

        return $this->db->count_all_results();
    }

    public function get_daily_sales_pl_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $this->db->select("
            SUM(bg_bw) AS bg_bw,
            SUM(bg_amount) AS begin_amt,
            SUM(bg_amount) / NULLIF(SUM(bg_bw),0) AS bg_up,

            SUM(pd_bw) AS production_bw,
            SUM(pd_amount) AS production_amt,
            SUM(pd_amount) / NULLIF(SUM(pd_bw),0) AS production_up,

            SUM(pr_bw) AS purchase_bw,
            SUM(pr_amount) AS purchase_amt,
            SUM(pr_amount) / NULLIF(SUM(pr_bw),0) AS purchase_up,

            SUM(ds_bw) AS adjust_bw,
            SUM(ds_amount) AS adjust_amt,
            SUM(ds_amount) / NULLIF(SUM(ds_bw),0) AS adjust_up,

            SUM(sl_cost_bw) AS cogs_bw,
            SUM(sl_cost_amount) AS cogs_amt,
            SUM(sl_cost_amount) / NULLIF(SUM(sl_cost_bw),0) AS cogs_up,

            SUM(end_amount) AS ending_amt,
            SUM(sl_net_amount) AS sales_net_amt,
            SUM(sl_profit) AS sales_profit_amt
        ", false);

        $this->db->from('abc_cl_sales_pl a');

        $this->db->where("a.plant IN ($plantList)", null, false);

        if (!empty($filters['date'])) {
            $this->db->where('a.ymd', $filters['date']);
        }

        return $this->db->get()->row();
    }

    public function get_monthly_sales_pl($limit, $start, $filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $this->db->select("
            a.item,
            i.full_name AS item_name,
            cls.code_name AS class_name,

            SUM(a.bg_amount) AS begin_amt,
            SUM(a.pd_amount) AS production_amt,
            SUM(a.pr_amount) AS purchase_amt,
            SUM(a.ds_amount) AS adjust_amt,

            SUM(a.sl_cost_amount) AS cogs_amt,

            SUM(a.end_amount) AS ending_amt,

            SUM(a.sl_net_amount) AS sales_net_amt,

            SUM(a.sl_profit) AS sales_profit_amt
        ", false);

        $this->db->from('abc_cl_sales_pl a');

        $this->db->join('abc_cd_item i','i.item=a.item','left');
        $this->db->join('abc_cd_code cls',"cls.head_code='GT' AND cls.code=i.item_class",'left');
        $this->db->join('abc_cd_code p',"p.head_code='PLANT' AND p.code=a.plant",'left');

        $this->db->where("a.plant IN ($plantList)", null, false);
        $this->db->where("LEFT(a.ymd,6)", $filters['month']);

        if (!empty($filters['item'])) {
            $this->db->like('a.item', $filters['item']);
        }

        $this->db->group_by([
            'a.item',
            'i.full_name',
            'cls.code_name'
        ]);

        $this->db->order_by('a.item','ASC');

        if ($limit > 0) {
            $this->db->limit($limit,$start);
        }

        return $this->db->get()->result();
    }

    public function count_monthly_sales_pl($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) total FROM (
                SELECT a.plant, a.item
                FROM abc_cl_sales_pl a
                WHERE a.plant IN ($plantList)
                AND LEFT(a.ymd,6) = ".$this->db->escape($filters['month'])."
                GROUP BY a.plant, a.item
            ) x
        ";

        return (int)$this->db->query($sql)->row()->total;
    }

    public function get_monthly_sales_pl_grand($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $this->db->select("

            COUNT(DISTINCT item) AS total_item,

            SUM(bg_amount) AS begin_amt,
            SUM(pd_amount) AS production_amt,
            SUM(pr_amount) AS purchase_amt,
            SUM(ds_amount) AS adjust_amt,

            SUM(sl_cost_amount) AS cogs_amt,

            SUM(end_amount) AS ending_amt,

            SUM(sl_net_amount) AS sales_net_amt,

            SUM(sl_profit) AS sales_profit_amt

        ", false);

        $this->db->from('abc_cl_sales_pl a');

        $this->db->where("a.plant IN ($plantList)", null, false);
        $this->db->where("LEFT(a.ymd,6)", $filters['month']);

        if (!empty($filters['item'])) {
            $this->db->like('a.item', $filters['item']);
        }

        return $this->db->get()->row();
    }

    public function get_summary($limit, $start, $filters, $order='plant_name', $dir='ASC')
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return [];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sitem = $filters['sitem'] ?? '';
        $date  = $filters['date'] ?? '';

        $yearStart = substr($date,0,4).'0101';

        $sql = "
            SELECT 
                p.CODE_NAME AS PLANT,

                /* PERIOD */
                SUM(CASE 
                    WHEN a.YMD = ".$this->db->escape($date)."
                    THEN a.AMOUNT ELSE 0 END) AS TAMT,

                /* YTD */
                SUM(CASE 
                    WHEN a.YMD BETWEEN ".$this->db->escape($yearStart)." 
                                AND ".$this->db->escape($date)."
                    THEN a.AMOUNT ELSE 0 END) AS AAMT

            FROM abc_cl_pl a
            LEFT JOIN abc_cd_code p 
                ON p.code = a.PLANT 
                AND p.head_code = 'PLANT'

            WHERE a.PLANT IN ($plantList)
        ";

        if (!empty($sitem)) {
            $sql .= " AND a.ITEM LIKE ".$this->db->escape($sitem.'%');
        }

        $sql .= "
            GROUP BY p.CODE_NAME
            ORDER BY $order $dir
        ";

        if ($limit > 0) {
            $sql .= " LIMIT ".(int)$start.", ".(int)$limit;
        }

        return $this->db->query($sql)->result();
    }

    public function count_summary($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return 0;

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT a.PLANT
                FROM abc_cl_pl a
                WHERE a.PLANT IN ($plantList)
                GROUP BY a.PLANT
            ) X
        ";

        return (int)($this->db->query($sql)->row()->total ?? 0);
    }

    public function get_summary_grand_total($filters)
    {
        $plants = $filters['plants'] ?? [];
        if (empty($plants)) return (object)[];

        $plantList = implode(',', array_map([$this->db,'escape'], $plants));

        $sitem = $filters['sitem'] ?? '';
        $date  = $filters['date'] ?? '';

        $yearStart = substr($date,0,4).'0101';

        $sql = "
            SELECT
                SUM(CASE 
                    WHEN a.YMD = ".$this->db->escape($date)."
                    THEN a.AMOUNT ELSE 0 END) AS TAMT,

                SUM(CASE 
                    WHEN a.YMD BETWEEN ".$this->db->escape($yearStart)." 
                                AND ".$this->db->escape($date)."
                    THEN a.AMOUNT ELSE 0 END) AS AAMT

            FROM abc_cl_pl a
            WHERE a.PLANT IN ($plantList)
        ";

        if (!empty($sitem)) {
            $sql .= " AND a.ITEM LIKE ".$this->db->escape($sitem.'%');
        }

        return $this->db->query($sql)->row();
    }
}
