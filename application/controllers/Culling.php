<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Culling extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('inventory_receive')) {
            show_404();
        }

        $this->load->model('Culling_model');
        $this->load->library('session');
        $this->load->helper(['url', 'file']);

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view(
            'templates/header',
            [
                'title' => 'Culling'
            ]
        );

        $this->load->view('templates/sidebar');

        $this->load->view(
            'admin/culling/list'
        );

        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page = max(
            1,
            (int)$this->input->get('page')
        );

        $limit = max(
            1,
            (int)$this->input->get('limit')
        );

        $search = trim(
            $this->input->get('search', true) ?? ''
        );

        $plant = trim(
            $this->input->get('plant', true) ?? ''
        );

        $dateFrom = trim(
            $this->input->get('date_from', true) ?? ''
        );

        $dateTo = trim(
            $this->input->get('date_to', true) ?? ''
        );

        $order = trim(
            $this->input->get('order', true) ?? ''
        );

        $dir = strtoupper(
            $this->input->get('dir', true) ?? 'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | DEFAULT ORDER
        |--------------------------------------------------------------------------
        */

        if (empty($order)) {

            $order = 'CREATED_AT';
        }

        if (
            $dir !== 'ASC' &&
            $dir !== 'DESC'
        ) {

            $dir = 'DESC';
        }

        $start =
            ($page - 1) *
            $limit;

        /*
        |--------------------------------------------------------------------------
        | SESSION
        |--------------------------------------------------------------------------
        */

        $role_id =
            (int)$this->session
                ->userdata('role_id');

        $username =
            $this->session
                ->userdata('username');

        $userPlant =
            $this->session
                ->userdata('plant');

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->Culling_model
                ->get_data(
                    $limit,
                    $start,
                    $role_id,
                    $userPlant,
                    $username,
                    $search,
                    $order,
                    $dir,
                    $plant,
                    $dateFrom,
                    $dateTo
                );

        $total =
            $this->Culling_model
                ->count_data(
                    $role_id,
                    $userPlant,
                    $username,
                    $search,
                    $plant,
                    $dateFrom,
                    $dateTo
                );

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' =>
                (int)$total,

            'page' =>
                (int)$page,

            'pages' =>
                (int)$pages,

            'pagination' =>
                $this->build_pagination(
                    $pages,
                    $page
                )
        ]);
    }

    private function build_pagination(
        $pages,
        $current
    )
    {
        $html =
            '<ul class="pagination pagination-sm">';

        for (
            $i = 1;
            $i <= $pages;
            $i++
        ) {

            $active =
                ($i == $current)
                    ? 'active'
                    : '';

            $html .=
                '<li class="page-item ' .
                $active .
                '">

                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' .
                        $i .
                        ')">

                        ' .
                        $i .
                        '

                    </a>

                </li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 PLANT
    |--------------------------------------------------------------------------
    */

    public function get_plant()
    {
        $data =
            $this->Culling_model
                ->get_plant_select2();

        echo json_encode(
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 CLASS OUT
    |--------------------------------------------------------------------------
    */

    public function get_class_out()
    {
        $term = trim(
            $this->input->get('q', true) ?? ''
        );

        $data =
            $this->Culling_model
                ->get_class_out_select2(
                    $term
                );

        echo json_encode(
            $data
        );
    }

    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            $plant = trim(
                $this->input->post(
                    'PLANT',
                    true
                ) ?? ''
            );

            $classOut = trim(
                $this->input->post(
                    'CLASS_OUT',
                    true
                ) ?? ''
            );

            $ymd = trim(
                $this->input->post(
                    'YMD',
                    true
                ) ?? ''
            );

            $jumlah = (float)
                $this->input->post('JUMLAH');

            $berat = (float)
                $this->input->post('BERAT');

            $remark = trim(
                $this->input->post(
                    'REMARK',
                    true
                ) ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (
                empty($plant) ||
                empty($ymd) ||
                empty($classOut)
            ) {

                throw new Exception(
                    'Plant, Date dan Class Out wajib diisi.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            $data = [

                'plant' => $plant,

                'ymd' => $ymd,

                'jumlah' => $jumlah,

                'berat' => $berat,

                'class_out' => $classOut,

                'remark' => $remark,

                'CREATED_BY' =>
                    $this->session
                        ->userdata('username'),

                'CREATED_AT' =>
                    date('Y-m-d H:i:s')

            ];

            $this->db->insert(
                'culling',
                $data
            );

            if (
                $this->db->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menyimpan data.'
                );

            }

            $id =
                $this->db
                    ->insert_id();

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Data culling berhasil disimpan.',

                'idno' => $id

            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    public function edit()
    {
        header('Content-Type: application/json');

        $idno = (int)
            $this->input->get(
                'idno',
                true
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Culling kosong.'

            ]);

            return;
        }

        $header =
            $this->db

                ->select("
                    c.*,

                    p.CODE_NAME
                        AS PLANT_NAME,

                    co.CODE_NAME
                        AS CLASS_OUT_NAME
                ")

                ->from('culling c')

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
                )

                ->where(
                    'c.idno',
                    $idno
                )

                ->get()

                ->row_array();

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Data culling tidak ditemukan.'

            ]);

            return;
        }

        echo json_encode([

            'status' => true,

            'header' => $header

        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            $idno = (int)
                $this->input->post(
                    'IDNO'
                );

            if (
                empty($idno)
            ) {

                throw new Exception(
                    'ID Culling kosong.'
                );
            }

            $header =
                $this->Culling_model
                    ->get_culling_header(
                        $idno
                    );

            if (!$header) {

                throw new Exception(
                    'Data culling tidak ditemukan.'
                );
            }

            $data = [

                'plant' =>
                    trim(
                        $this->input->post(
                            'PLANT',
                            true
                        )
                    ),

                'ymd' =>
                    trim(
                        $this->input->post(
                            'YMD',
                            true
                        )
                    ),

                'jumlah' =>
                    (float)
                    $this->input->post(
                        'JUMLAH'
                    ),

                'berat' =>
                    (float)
                    $this->input->post(
                        'BERAT'
                    ),

                'class_out' =>
                    trim(
                        $this->input->post(
                            'CLASS_OUT',
                            true
                        )
                    ),

                'remark' =>
                    trim(
                        $this->input->post(
                            'REMARK',
                            true
                        )
                    ),

                'UPDATED_BY' =>
                    $this->session
                        ->userdata(
                            'username'
                        ),

                'UPDATED_AT' =>
                    date(
                        'Y-m-d H:i:s'
                    )
            ];

            $this->db

                ->where(
                    'idno',
                    $idno
                )

                ->update(
                    'culling',
                    $data
                );

            if (
                $this->db->trans_status()
                === false
            ) {

                throw new Exception(
                    'Update gagal.'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Data culling berhasil diupdate.'

            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }

    public function remove()
    {
        header('Content-Type: application/json');

        $idno = (int)
            $this->input->post(
                'idno'
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Culling wajib diisi.'

            ]);

            return;
        }

        $header =
            $this->Culling_model
                ->get_culling_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Data culling tidak ditemukan.'

            ]);

            return;
        }

        $this->db->trans_begin();

        try {

            $this->db

                ->where(
                    'idno',
                    $idno
                )

                ->delete(
                    'culling'
                );

            if (
                $this->db->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menghapus data.'
                );

            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Data culling berhasil dihapus.'

            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([

                'status' => false,

                'message' =>
                    $e->getMessage()

            ]);

        }
    }
}