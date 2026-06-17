<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CreditNote_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set(
            'Asia/Jakarta'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LIST DATA
    |--------------------------------------------------------------------------
    */

    public function get_data(
        $limit = 10,
        $start = 0,
        $filters = [],
        $order = 'cn_date',
        $dir = 'DESC'
    )
    {
        $allowedOrder = [

            'cn_no'        => 'cn.cn_no',
            'cn_date'      => 'cn.cn_date',
            'plant'        => 'cn.plant',
            'customer'     => 'cn.customer',
            'total_amount' => 'cn.total_amount',
            'created_at'   => 'cn.created_at'

        ];

        $orderBy =
            $allowedOrder[$order]
            ?? 'cn.created_at';

        $dir =
            strtoupper($dir) === 'ASC'
                ? 'ASC'
                : 'DESC';

        $this->db

            ->select("
                cn.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ", false)

            ->from(
                'abc_mst_credit_note cn'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = cn.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = cn.customer
                ",
                'left',
                false
            )

            ->where(
                'cn.deleted IS NULL',
                null,
                false
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['search']
            )
        ) {

            $search =
                $filters['search'];

            $this->db

                ->group_start()

                ->like(
                    'cn.cn_no',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'cn.remark',
                    $search
                )

                ->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['plant']
            )
        ) {

            $this->db
                ->where(
                    'cn.plant',
                    $filters['plant']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['customer']
            )
        ) {

            $this->db
                ->where(
                    'cn.customer',
                    $filters['customer']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_from']
            )
        ) {

            $this->db
                ->where(
                    'DATE(cn.cn_date) >=',
                    $filters['date_from']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_to']
            )
        ) {

            $this->db
                ->where(
                    'DATE(cn.cn_date) <=',
                    $filters['date_to']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db
            ->order_by(
                $orderBy,
                $dir
            );

        $this->db
            ->order_by(
                'cn.cn_no',
                'DESC'
            );

        /*
        |--------------------------------------------------------------------------
        | LIMIT
        |--------------------------------------------------------------------------
        */

        $this->db
            ->limit(
                (int)$limit,
                (int)$start
            );

        return
            $this->db
                ->get()
                ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT DATA
    |--------------------------------------------------------------------------
    */

    public function count_data(
        $filters = []
    )
    {
        $this->db

            ->from(
                'abc_mst_credit_note cn'
            )

            ->join(
                'abc_cd_customer c',
                'c.CUST = cn.customer',
                'left'
            )

            ->where(
                'cn.deleted IS NULL',
                null,
                false
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['search']
            )
        ) {

            $search =
                $filters['search'];

            $this->db

                ->group_start()

                ->like(
                    'cn.cn_no',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'cn.remark',
                    $search
                )

                ->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['plant']
            )
        ) {

            $this->db
                ->where(
                    'cn.plant',
                    $filters['plant']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['customer']
            )
        ) {

            $this->db
                ->where(
                    'cn.customer',
                    $filters['customer']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FROM
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_from']
            )
        ) {

            $this->db
                ->where(
                    'DATE(cn.cn_date) >=',
                    $filters['date_from']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE TO
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filters['date_to']
            )
        ) {

            $this->db
                ->where(
                    'DATE(cn.cn_date) <=',
                    $filters['date_to']
                );
        }

        return
            $this->db
                ->count_all_results();
    }

    /*
    |--------------------------------------------------------------------------
    | USER PLANT
    |--------------------------------------------------------------------------
    */

    public function get_user_plants(
        $username
    )
    {
        $row = $this->db

            ->select('plant')

            ->where(
                'username',
                $username
            )

            ->get('users')

            ->row();

        if (
            !$row ||
            empty($row->plant)
        ) {

            return [];

        }

        $plants =
            json_decode(
                $row->plant,
                true
            );

        return is_array($plants)
            ? $plants
            : [];
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 PLANT
    |--------------------------------------------------------------------------
    */

    public function get_plant_select2()
    {
        return $this->db

            ->select("
                CODE as id,
                CODE_NAME as text
            ")

            ->from('abc_cd_code')

            ->where(
                'HEAD_CODE',
                'PLANT'
            )

            ->where(
                'CODE <>',
                '*'
            )

            ->where(
                'USE_YN',
                'Y'
            )

            ->order_by(
                'CODE_NAME',
                'ASC'
            )

            ->get()

            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 CUSTOMER
    |--------------------------------------------------------------------------
    */

    public function get_customer_select2(
        $term = ''
    )
    {
        $this->db

            ->select("
                CUST as id,
                CONCAT(
                    CUST,
                    ' - ',
                    FULL_NAME
                ) as text
            ")

            ->from(
                'abc_cd_customer'
            )

            ->where(
                'CUST_KIND',
                'CUSTOMER'
            )

            ->where(
                'CUST_CLASS',
                'CUSTOMER'
            );

        if (!empty($term)) {

            $this->db

                ->group_start()

                ->like(
                    'CUST',
                    $term
                )

                ->or_like(
                    'FULL_NAME',
                    $term
                )

                ->group_end();
        }

        return $this->db

            ->order_by(
                'FULL_NAME',
                'ASC'
            )

            ->get()

            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE CN NUMBER
    |--------------------------------------------------------------------------
    */

    public function generate_cn_no()
    {
        $prefix =
            'CN' .
            date('Ymd');

        $row =
            $this->db

                ->select("
                    MAX(cn_no)
                    as last_no
                ")

                ->like(
                    'cn_no',
                    $prefix,
                    'after'
                )

                ->get(
                    'abc_mst_credit_note'
                )

                ->row();

        $urut = 1;

        if (
            !empty(
                $row->last_no
            )
        ) {

            $urut =
                (int)
                substr(
                    $row->last_no,
                    -4
                ) + 1;
        }

        return
            $prefix .
            str_pad(
                $urut,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 SALES
    |--------------------------------------------------------------------------
    */

    public function get_sales_select2(
        $plant,
        $customer,
        $term = ''
    )
    {
        $this->db

            ->select("
                s.SALES as id,

                s.SALES as text,

                s.AMOUNT
                    as sales_amt,

                IFNULL(
                    SUM(d.JUMLAH),
                    0
                ) as act_qty,

                IFNULL(
                    SUM(d.BERAT),
                    0
                ) as act_bw
            ", false)

            ->from(
                'abc_mst_sales s'
            )

            ->join(
                'abc_mst_sales_detail d',
                '
                    d.SALES = s.SALES
                    AND d.PLANT = s.PLANT
                    AND d.DELETED IS NULL
                ',
                'left',
                false
            )

            ->where(
                's.DELETED IS NULL',
                null,
                false
            );

        if (
            !empty($plant)
        ) {

            $this->db
                ->where(
                    's.PLANT',
                    $plant
                );
        }

        if (
            !empty($customer)
        ) {

            $this->db
                ->where(
                    's.CUSTOMER',
                    $customer
                );
        }

        if (
            !empty($term)
        ) {

            $this->db
                ->like(
                    's.SALES',
                    $term
                );
        }

        $this->db

            ->group_by(
                's.SALES'
            )

            ->group_by(
                's.AMOUNT'
            )

            ->order_by(
                's.SALES',
                'DESC'
            );

        return $this->db

            ->get()

            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL CREDIT NOTE PER SALES
    |--------------------------------------------------------------------------
    */

    public function get_total_credit_note_by_sales(
        $salesNo,
        $excludeIdno = null
    )
    {
        $this->db

            ->select("
                IFNULL(
                    SUM(cn_amount),
                    0
                ) AS total
            ")

            ->where(
                'sales',
                $salesNo
            )

            ->where(
                'deleted IS NULL',
                null,
                false
            );

        if (!empty($excludeIdno)) {

            $this->db
                ->where(
                    'idno <>',
                    $excludeIdno
                );
        }

        $row =
            $this->db
                ->get(
                    'abc_mst_credit_note_dtl'
                )
                ->row();

        return
            (float)
            $row->total;
    }

    public function get_sales_remaining_select2(
        $plant,
        $customer,
        $term = ''
    )
    {
        $this->db

            ->select("
                s.SALES,
                s.AMOUNT,

                IFNULL(
                    SUM(d.JUMLAH),
                    0
                ) AS JUMLAH,

                IFNULL(
                    SUM(d.BERAT),
                    0
                ) AS BERAT
            ", false)

            ->from(
                'abc_mst_sales s'
            )

            ->join(
                'abc_mst_sales_detail d',
                "
                    d.SALES = s.SALES
                    AND d.DELETED IS NULL
                ",
                'left',
                false
            )

            ->where(
                's.PLANT',
                $plant
            )

            ->where(
                's.CUSTOMER',
                $customer
            )

            ->where(
                's.DELETED IS NULL',
                null,
                false
            )

            ->where(
                's.STATUS <>',
                'CANCEL'
            );

        if (!empty($term)) {

            $this->db
                ->like(
                    's.SALES',
                    $term
                );
        }

        $this->db
            ->group_by(
                's.SALES'
            );

        $rows =
            $this->db
                ->get()
                ->result();

        $result = [];

        foreach ($rows as $r) {

            $cn =
                $this
                    ->get_total_credit_note_by_sales(
                        $r->SALES
                    );

            $remaining =
                (float)$r->AMOUNT
                -
                $cn;

            /*
            |--------------------------------------------------------------------------
            | HANYA SALES YANG MASIH ADA REMAINING
            |--------------------------------------------------------------------------
            */

            if ($remaining <= 0) {
                continue;
            }

            $result[] = [

                'id' =>
                    $r->SALES,

                'text' =>
                    $r->SALES,

                'amount' =>
                    (float)$r->AMOUNT,

                'remaining' =>
                    $remaining,

                'qty' =>
                    (float)$r->JUMLAH,

                'bw' =>
                    (float)$r->BERAT

            ];
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | GET HEADER
    |--------------------------------------------------------------------------
    */

    public function get_cn_header(
        $cnNo,
        $plant
    )
    {
        return $this->db

            ->where(
                'cn_no',
                $cnNo
            )

            ->where(
                'plant',
                $plant
            )

            ->where(
                'DELETED IS NULL',
                null,
                false
            )

            ->get(
                'abc_mst_credit_note'
            )

            ->row_array();
    }

    /*
    |--------------------------------------------------------------------------
    | GET DETAIL
    |--------------------------------------------------------------------------
    */

    public function get_cn_detail(
        $cnNo,
        $plant
    )
    {
        return $this->db

            ->from(
                'abc_mst_credit_note_dtl'
            )

            ->where(
                'cn_no',
                $cnNo
            )

            ->where(
                'plant',
                $plant
            )

            ->order_by(
                'seq_no',
                'ASC'
            )

            ->get()

            ->result_array();
    }

    public function get_header(
        $idno
    )
    {
        return $this->db

            ->select("
                cn.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ")

            ->from(
                'abc_mst_credit_note cn'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = cn.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = cn.customer
                ",
                'left',
                false
            )

            ->where(
                'cn.cn_no',
                $idno
            )

            ->where(
                'cn.deleted IS NULL',
                null,
                false
            )

            ->get()

            ->row_array();
    }

    public function get_detail(
        $idno
    )
    {
        return $this->db

            ->select("
                d.*,

                s.AMOUNT,

                IFNULL(
                    SUM(sd.JUMLAH),
                    0
                ) AS JUMLAH,

                IFNULL(
                    SUM(sd.BERAT),
                    0
                ) AS BERAT
            ", false)

            ->from(
                'abc_mst_credit_note_dtl d'
            )

            ->join(
                'abc_mst_sales s',
                "
                    s.SALES =
                    d.sales

                    AND s.DELETED IS NULL
                ",
                'left',
                false
            )

            ->join(
                'abc_mst_sales_detail sd',
                "
                    sd.SALES =
                    s.SALES

                    AND sd.DELETED IS NULL
                ",
                'left',
                false
            )

            ->where(
                'd.idno',
                $idno
            )

            ->where(
                'd.deleted IS NULL',
                null,
                false
            )

            ->group_by(
                'd.id'
            )

            ->order_by(
                'd.id',
                'ASC'
            )

            ->get()

            ->result_array();
    }
}