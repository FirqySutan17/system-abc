<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_model extends CI_Model
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
        $order = 'LOAN_DATE',
        $dir = 'DESC'
    )
    {
        $allowedOrder = [

            'LOAN_NO'         => 'loan.LOAN_NO',
            'LOAN_DATE'       => 'loan.LOAN_DATE',
            'PLANT'         => 'loan.PLANT',
            'CUSTOMER'      => 'loan.CUSTOMER',
            'RELATED'       => 'loan.RELATED',
            'AMOUNT'        => 'loan.AMOUNT',
            'CREATED_AT'    => 'loan.CREATED_AT'

        ];

        $orderBy =
            $allowedOrder[$order]
            ?? 'loan.CREATED_AT';

        $dir =
            strtoupper($dir) === 'ASC'
                ? 'ASC'
                : 'DESC';

        $this->db

            ->select("
                loan.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ", false)

            ->from(
                'abc_mst_loan loan'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = loan.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = loan.customer
                ",
                'left',
                false
            )

            ->where(
                'loan.deleted IS NULL',
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
                    'loan.LOAN_NO',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'loan.REMARK',
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
                    'loan.PLANT',
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
                    'loan.CUSTOMER',
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
                    'DATE(loan.LOAN_DATE) >=',
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
                    'DATE(loan.LOAN_DATE) <=',
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
                'loan.LOAN_NO',
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
                'abc_mst_loan loan'
            )

            ->join(
                'abc_cd_customer c',
                'c.CUST = loan.CUSTOMER',
                'left'
            )

            ->where(
                'loan.deleted IS NULL',
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
                    'loan.LOAN_NO',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'loan.REMARK',
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
                    'loan.PLANT',
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
                    'loan.CUSTOMER',
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
                    'DATE(loan.LOAN_DATE) >=',
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
                    'DATE(loan.LOAN_DATE) <=',
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

    public function generate_LOAN_NO()
    {
        $prefix =
            'LOAN' .
            date('Ymd');

        $row =
            $this->db

                ->select("
                    MAX(LOAN_NO)
                    as last_no
                ")

                ->like(
                    'LOAN_NO',
                    $prefix,
                    'after'
                )

                ->get(
                    'abc_mst_loan'
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
    | GET HEADER
    |--------------------------------------------------------------------------
    */

    public function get_loan_header(
        $loanNo,
        $plant
    )
    {
        return $this->db

            ->where(
                'LOAN_NO',
                $loanNo
            )

            ->where(
                'PLANT',
                $plant
            )

            ->where(
                'DELETED IS NULL',
                null,
                false
            )

            ->get(
                'abc_mst_loan'
            )

            ->row_array();
    }

    /*
    |--------------------------------------------------------------------------
    | GET DETAIL
    |--------------------------------------------------------------------------
    */

    public function get_loan_history(
        $loan_no,
        $plant,
        $customer
    )
    {
        $query = $this->db

            ->from(
                'abc_mst_loan'
            )
            
            ->where(
                'DELETED IS NULL',
                null,
                false
            )
            ->where(
                'CUSTOMER',
                $customer
            )

            ->where(
                'plant',
                $plant
            );
        
        if (!empty($loan_no)) {
            $query
            ->where(
                'LOAN_NO !=',
                $loan_no
            );
        }

        return    $query->order_by(
                'LOAN_DATE',
                'ASC'
            )->get()->result_array();
    }

    public function get_header(
        $idno
    )
    {
        return $this->db

            ->select("
                loan.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ")

            ->from(
                'abc_mst_loan loan'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = loan.PLANT
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = loan.CUSTOMER
                ",
                'left',
                false
            )

            ->where(
                'loan.LOAN_NO',
                $idno
            )

            ->where(
                'loan.DELETED IS NULL',
                null,
                false
            )

            ->get()

            ->row_array();
    }
}