<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saving_model extends CI_Model
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
        $order = 'sv_date',
        $dir = 'DESC'
    )
    {
        $allowedOrder = [

            'SV_NO'         => 'sv.SV_NO',
            'SV_DATE'       => 'sv.SV_DATE',
            'PLANT'         => 'sv.PLANT',
            'CUSTOMER'      => 'sv.CUSTOMER',
            'RELATED'       => 'sv.RELATED',
            'AMOUNT'        => 'sv.AMOUNT',
            'CREATED_AT'    => 'sv.CREATED_AT'

        ];

        $orderBy =
            $allowedOrder[$order]
            ?? 'sv.created_at';

        $dir =
            strtoupper($dir) === 'ASC'
                ? 'ASC'
                : 'DESC';

        $this->db

            ->select("
                sv.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ", false)

            ->from(
                'abc_mst_saving sv'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = sv.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = sv.customer
                ",
                'left',
                false
            )

            ->where(
                'sv.deleted IS NULL',
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
                    'sv.SV_NO',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'sv.REMARK',
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
                    'sv.PLANT',
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
                    'sv.CUSTOMER',
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
                    'DATE(sv.SV_DATE) >=',
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
                    'DATE(sv.SV_DATE) <=',
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
                'sv.SV_NO',
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
                'abc_mst_saving sv'
            )

            ->join(
                'abc_cd_customer c',
                'c.CUST = sv.CUSTOMER',
                'left'
            )

            ->where(
                'sv.deleted IS NULL',
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
                    'sv.SV_NO',
                    $search
                )

                ->or_like(
                    'c.FULL_NAME',
                    $search
                )

                ->or_like(
                    'sv.REMARK',
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
                    'sv.PLANT',
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
                    'sv.CUSTOMER',
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
                    'DATE(sv.SV_DATE) >=',
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
                    'DATE(sv.SV_DATE) <=',
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

    public function generate_sv_no()
    {
        $prefix =
            'SV' .
            date('Ymd');

        $row =
            $this->db

                ->select("
                    MAX(SV_NO)
                    as last_no
                ")

                ->like(
                    'SV_NO',
                    $prefix,
                    'after'
                )

                ->get(
                    'abc_mst_saving'
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

    public function get_sv_header(
        $svNo,
        $plant
    )
    {
        return $this->db

            ->where(
                'SV_NO',
                $svNo
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
                'abc_mst_saving'
            )

            ->row_array();
    }

    /*
    |--------------------------------------------------------------------------
    | GET DETAIL
    |--------------------------------------------------------------------------
    */

    public function get_sv_history(
        $sv_no,
        $plant,
        $customer
    )
    {
        $query = $this->db

            ->from(
                'abc_mst_saving'
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
        
        if (!empty($sv_no)) {
            $query
            ->where(
                'SV_NO !=',
                $sv_no
            );
        }

        return    $query->order_by(
                'SV_DATE',
                'ASC'
            )->get()->result_array();
    }

    public function get_header(
        $idno
    )
    {
        return $this->db

            ->select("
                sv.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                c.FULL_NAME
                    AS CUSTOMER_NAME
            ")

            ->from(
                'abc_mst_saving sv'
            )

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = sv.PLANT
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_customer c',
                "
                    c.CUST = sv.CUSTOMER
                ",
                'left',
                false
            )

            ->where(
                'sv.SV_NO',
                $idno
            )

            ->where(
                'sv.DELETED IS NULL',
                null,
                false
            )

            ->get()

            ->row_array();
    }
}