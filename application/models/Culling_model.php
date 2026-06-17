<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Culling_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
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

        $plants = json_decode(
            $row->plant,
            true
        );

        return is_array($plants)
            ? $plants
            : [];
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 PLANT BY USER
    |--------------------------------------------------------------------------
    */

    public function get_user_plant_options(
        $username
    )
    {
        $plants =
            $this->get_user_plants(
                $username
            );

        if (empty($plants)) {
            return [];
        }

        return $this->db

            ->select("
                CODE as id,
                CONCAT(
                    CODE,
                    ' - ',
                    CODE_NAME
                ) as text
            ")

            ->from('abc_cd_code')

            ->where(
                'HEAD_CODE',
                'PLANT'
            )

            ->where_in(
                'CODE',
                $plants
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
    | SELECT2 CLASS OUT
    |--------------------------------------------------------------------------
    */

    public function get_class_out_select2(
        $term = ''
    )
    {
        $this->db

            ->select("
                CODE as id,
                CODE_NAME as text
            ")

            ->from('abc_cd_code')

            ->where(
                'HEAD_CODE',
                'CLASS OUT'
            )

            ->where(
                'CODE <>',
                '*'
            )

            ->where(
                'USE_YN',
                'Y'
            );

        if (!empty($term)) {

            $this->db
                ->group_start()

                ->like(
                    'CODE',
                    $term
                )

                ->or_like(
                    'CODE_NAME',
                    $term
                )

                ->group_end();
        }

        return $this->db

            ->order_by(
                'CODE_NAME',
                'ASC'
            )

            ->get()

            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | LIST DATA
    |--------------------------------------------------------------------------
    */

    public function get_data(
        $limit,
        $start,
        $role_id,
        $plant,
        $username,
        $search = '',
        $order = 'ymd',
        $dir = 'DESC',
        $filterPlant = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $role_id =
            (int)$role_id;

        $this->db

            ->select("
                c.*,

                p.CODE_NAME
                    AS PLANT_NAME,

                co.CODE_NAME
                    AS CLASS_OUT_NAME
            ", false)

            ->from('abc_mst_culling c')

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = c.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_code co',
                "
                    co.CODE = c.class_out
                    AND co.HEAD_CODE='CLASS OUT'
                ",
                'left',
                false
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            $search !== ''
        ) {

            $this->db
                ->group_start()

                ->like(
                    'c.remark',
                    $search
                )

                ->or_like(
                    'p.CODE_NAME',
                    $search
                )

                ->or_like(
                    'co.CODE_NAME',
                    $search
                )

                ->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER PLANT
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filterPlant
            )
        ) {

            $this->db
                ->where(
                    'c.plant',
                    $filterPlant
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $dateFrom
            )
        ) {

            $this->db
                ->where(
                    'c.ymd >=',
                    $dateFrom
                );
        }

        if (
            !empty(
                $dateTo
            )
        ) {

            $this->db
                ->where(
                    'c.ymd <=',
                    $dateTo
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db
            ->order_by(
                'c.' . $order,
                $dir
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

        return $this->db
            ->get()
            ->result_array();
    }

    /*
    |--------------------------------------------------------------------------
    | COUNT DATA
    |--------------------------------------------------------------------------
    */

    public function count_data(
        $role_id,
        $plant,
        $username,
        $search = '',
        $filterPlant = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $role_id =
            (int)$role_id;

        $this->db

            ->from('abc_mst_culling c')

            ->join(
                'abc_cd_code p',
                "
                    p.CODE = c.plant
                    AND p.HEAD_CODE='PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_cd_code co',
                "
                    co.CODE = c.class_out
                    AND co.HEAD_CODE='CLASS OUT'
                ",
                'left',
                false
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (
            $search !== ''
        ) {

            $this->db
                ->group_start()

                ->like(
                    'c.remark',
                    $search
                )

                ->or_like(
                    'p.CODE_NAME',
                    $search
                )

                ->or_like(
                    'co.CODE_NAME',
                    $search
                )

                ->group_end();
        }

        if (
            !empty(
                $filterPlant
            )
        ) {

            $this->db
                ->where(
                    'c.plant',
                    $filterPlant
                );
        }

        if (
            !empty(
                $dateFrom
            )
        ) {

            $this->db
                ->where(
                    'c.ymd >=',
                    $dateFrom
                );
        }

        if (
            !empty(
                $dateTo
            )
        ) {

            $this->db
                ->where(
                    'c.ymd <=',
                    $dateTo
                );
        }

        return $this->db
            ->count_all_results();
    }

    /*
    |--------------------------------------------------------------------------
    | GET SINGLE DATA
    |--------------------------------------------------------------------------
    */

    public function get_culling_header(
        $idno
    )
    {
        return $this->db

            ->where(
                'idno',
                $idno
            )

            ->get(
                'abc_mst_culling'
            )

            ->row_array();
    }
}