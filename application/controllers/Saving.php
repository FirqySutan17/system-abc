<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saving extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('accounting_cash_in')) {
            show_404();
        }

        $this->load->model(
            'Saving_model'
        );

        $this->load->library(
            'session'
        );

        $this->load->helper([
            'url',
            'file'
        ]);

        error_reporting(E_ALL);
        ini_set(
            'display_errors',
            1
        );
    }

    public function index()
    {
        $this->load->view(
            'templates/header',
            [
                'title' => 'Saving'
            ]
        );

        $this->load->view(
            'templates/sidebar'
        );

        $this->load->view(
            'admin/saving/list'
        );

        $this->load->view(
            'templates/footer'
        );
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
                $i == $current
                    ? 'active'
                    : '';

            $html .=

                '<li class="page-item '
                .$active.
                '">

                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage('
                        .$i.
                        ')">

                        '.$i.'

                    </a>

                </li>';
        }

        $html .=
            '</ul>';

        return $html;
    }

    public function get_plant()
    {
        echo json_encode(

            $this->Saving_model
                ->get_plant_select2()

        );
    }

    public function get_customer()
    {
        $term = trim(
            $this->input->get(
                'q',
                true
            ) ?? ''
        );

        echo json_encode(

            $this->Saving_model
                ->get_customer_select2(
                    $term
                )

        );
    }

    public function get_sales()
    {
        $plant = trim(

            $this->input
                ->get(
                    'plant',
                    true
                )

            ?? ''

        );

        $customer = trim(

            $this->input
                ->get(
                    'customer',
                    true
                )

            ?? ''

        );

        $term = trim(

            $this->input
                ->get(
                    'q',
                    true
                )

            ?? ''

        );

        $data =
            $this
                ->Saving_model
                ->get_sales_select2(

                    $plant,
                    $customer,
                    $term

                );

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE CN
        |--------------------------------------------------------------------------
        */

        foreach ($data as &$d)
        {
            $used =

                $this
                    ->Saving_model
                    ->get_total_credit_note_by_sales(

                        $d['id'],
                        $plant

                    );

            $d['total_cn'] =
                $used;

            $d['available_cn'] =
                (float)
                $d['sales_amt']
                -
                $used;
        }

        echo json_encode(
            $data
        );
    }

    public function generate_sv_no()
    {
        echo json_encode([

            'status' => true,

            'sv_no' =>

                $this->Saving_model
                    ->generate_sv_no()

        ]);
    }

    public function get_sv_history()
    {
        $plant = trim(

            $this->input->post(
                'plant',
                true
            ) ?? ''

        );

        $customer = trim(

            $this->input->post(
                'customer',
                true
            ) ?? ''

        );

        if (
            empty($plant)
            ||
            empty($customer)
        ) {

             echo json_encode([
                'status' => false,
                'message' =>
                    'Data tidak ditemukan.',
                'data'  => []
            ]);

            return;
        }

        $data = $this->Saving_model
                ->get_sv_history(
                    "",
                    $plant,
                    $customer
                );

        echo json_encode([
            'status' => true,
            'message'=> 'Data ditemukan',
            'data'  => $data
        ]);
    }

    public function load_data()
    {
        $page = max(
            1,
            (int)$this->input->get(
                'page'
            )
        );

        $limit = max(
            1,
            (int)$this->input->get(
                'limit'
            )
        );

        $search = trim(
            $this->input->get(
                'search',
                true
            ) ?? ''
        );

        $plant = trim(
            $this->input->get(
                'plant',
                true
            ) ?? ''
        );

        $customer = trim(
            $this->input->get(
                'customer',
                true
            ) ?? ''
        );

        $dateFrom = trim(
            $this->input->get(
                'date_from',
                true
            ) ?? ''
        );

        $dateTo = trim(
            $this->input->get(
                'date_to',
                true
            ) ?? ''
        );

        $start =
            ($page - 1)
            *
            $limit;

        $filters = [

            'search' =>
                $search,

            'plant' =>
                $plant,

            'customer' =>
                $customer,

            'date_from' =>
                $dateFrom,

            'date_to' =>
                $dateTo

        ];

        $rows =
            $this->Saving_model
                ->get_data(
                    $limit,
                    $start,
                    $filters
                );

        $total =
            $this->Saving_model
                ->count_data(
                    $filters
                );

        $pages =
            $total > 0
                ? ceil(
                    $total /
                    $limit
                )
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

    public function create()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            $svNo = trim(
                $this->input->post(
                    'SV_NO',
                    true
                ) ?? ''
            );

            $plant = trim(
                $this->input->post(
                    'PLANT',
                    true
                ) ?? ''
            );

            $customer = trim(
                $this->input->post(
                    'CUSTOMER',
                    true
                ) ?? ''
            );

            $svDate = trim(
                $this->input->post(
                    'SV_DATE',
                    true
                ) ?? ''
            );

            $remark = trim(
                $this->input->post(
                    'REMARK',
                    true
                ) ?? ''
            );

            $related =
                $this->input->post(
                    'RELATED'
                );

            $svAmount =
                $this->input->post(
                    'AMOUNT'
                );

            $REMARK =
                $this->input->post(
                    'REMARK'
                );

            if (
                empty($svNo) ||
                empty($plant) ||
                empty($customer)
            ) {

                throw new Exception(
                    'Data header belum lengkap.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $header = [

                'SV_NO' => $svNo,

                'PLANT' => $plant,

                'CUSTOMER' => $customer,

                'SV_DATE' => $svDate,

                'RELATED' => $related,

                'REMARK' => $remark,

                'AMOUNT' => $svAmount,

                'CREATED_AT' =>
                    date('Y-m-d H:i:s'),

                'CREATED_BY' =>
                    $this->session
                        ->userdata(
                            'username'
                        )
            ];

            $this->db->insert(
                'abc_mst_saving',
                $header
            );

            $idno =
                $this->db
                    ->insert_id();

            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menyimpan Saving.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Saving berhasil disimpan.',

                'sv_no' => $idno

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
        header(
            'Content-Type: application/json'
        );

        $idno =

            $this->input->post(
                'sv_no',
                true
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'SV No tidak ditemukan.'

            ]);

            return;
        }

        $header =

            $this->Saving_model
                ->get_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Data tidak ditemukan.'

            ]);

            return;
        }

        $detail =

            $this->Saving_model
                ->get_sv_history(
                    $header['SV_NO'],
                    $header['PLANT'],
                    $header['CUSTOMER']
                );

        echo json_encode([

            'status' => true,

            'header' => $header,

            'detail' => $detail

        ]);
    }

    public function update()
    {
        header('Content-Type: application/json');

        $this->db->trans_begin();

        try {

            $savingNo =
                $this->input->post(
                    'SVNoEdit'
                );

            if (empty($savingNo)) {

                throw new Exception(
                    'SV Nomor kosong.'
                );
            }

            $header =
                $this->Saving_model
                    ->get_header(
                        $savingNo
                    );

            if (!$header) {

                throw new Exception(
                    'Data Saving tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $svDate = trim(
                $this->input->post(
                    'SV_DATE',
                    true
                ) ?? ''
            );

            $amount = trim(
                $this->input->post(
                    'AMOUNT',
                    true
                ) ?? ''
            );

            $remark = trim(
                $this->input->post(
                    'REMARK',
                    true
                ) ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER
            |--------------------------------------------------------------------------
            */

            $headerData = [
                'SV_DATE'   => $svDate,
                'AMOUNT'    => $amount,
                'REMARK'    => $remark,
                'UPDATED_AT' =>
                    date('Y-m-d H:i:s'),
                'UPDATED_BY' =>
                    $this->session
                        ->userdata(
                            'username'
                        )
            ];

            $this->db

                ->where(
                    'SV_NO',
                    $savingNo
                )

                ->update(
                    'abc_mst_saving',
                    $headerData
                );

            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Update Saving gagal.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Saving berhasil diupdate.'

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

        $idno =
            $this->input->post(
                'sv_no'
            );

        if (empty($idno)) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Saving kosong.'

            ]);

            return;
        }

        $header =
            $this->Saving_model
                ->get_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Saving tidak ditemukan.'

            ]);

            return;
        }

        $this->db->trans_begin();

        try {

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $this->db

                ->where(
                    'SV_NO',
                    $idno
                )

                ->update(
                    'abc_mst_saving',
                    [
                        'DELETED' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'DELETED_BY' =>
                            $this->session
                                ->userdata(
                                    'username'
                                )

                    ]
                );
            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menghapus Saving.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Saving berhasil dihapus.'

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
