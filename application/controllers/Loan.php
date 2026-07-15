<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('accounting_cash_in')) {
            show_404();
        }

        $this->load->model(
            'Loan_model'
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
                'title' => 'Loan'
            ]
        );

        $this->load->view(
            'templates/sidebar'
        );

        $this->load->view(
            'admin/loan/list'
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

            $this->Loan_model
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

            $this->Loan_model
                ->get_customer_select2(
                    $term
                )

        );
    }

    public function generate_loan_no()
    {
        echo json_encode([

            'status' => true,

            'loan_no' =>

                $this->Loan_model
                    ->generate_loan_no()

        ]);
    }

    public function get_loan_history()
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

        $data = $this->Loan_model
                ->get_loan_history(
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
            $this->Loan_model
                ->get_data(
                    $limit,
                    $start,
                    $filters
                );

        $total =
            $this->Loan_model
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

            $loanNo = trim(
                $this->input->post(
                    'LOAN_NO',
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

            $loanDate = trim(
                $this->input->post(
                    'LOAN_DATE',
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

            $loanAmount =
                $this->input->post(
                    'AMOUNT'
                );

            $REMARK =
                $this->input->post(
                    'REMARK'
                );

            if (
                empty($loanNo) ||
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

                'LOAN_NO' => $loanNo,

                'PLANT' => $plant,

                'CUSTOMER' => $customer,

                'LOAN_DATE' => $loanDate,

                'RELATED' => $related,

                'REMARK' => $remark,

                'AMOUNT' => $loanAmount,

                'CREATED_AT' =>
                    date('Y-m-d H:i:s'),

                'CREATED_BY' =>
                    $this->session
                        ->userdata(
                            'username'
                        )
            ];

            $this->db->insert(
                'abc_mst_loan',
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
                    'Gagal menyimpan Loan.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Loan berhasil disimpan.',

                'loan_no' => $idno

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
                'loan_no',
                true
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Loan No tidak ditemukan.'

            ]);

            return;
        }

        $header =

            $this->Loan_model
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

            $this->Loan_model
                ->get_loan_history(
                    $header['LOAN_NO'],
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

            $loanNo =
                $this->input->post(
                    'LoanNoEdit'
                );

            if (empty($loanNo)) {

                throw new Exception(
                    'LOAN Nomor kosong.'
                );
            }

            $header =
                $this->Loan_model
                    ->get_header(
                        $loanNo
                    );

            if (!$header) {

                throw new Exception(
                    'Data Loan tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $loanDate = trim(
                $this->input->post(
                    'LOAN_DATE',
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
                'LOAN_DATE'   => $loanDate,
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
                    'LOAN_NO',
                    $loanNo
                )

                ->update(
                    'abc_mst_loan',
                    $headerData
                );

            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Update Loan gagal.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Loan berhasil diupdate.'

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
                'loan_no'
            );

        if (empty($idno)) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Loan kosong.'

            ]);

            return;
        }

        $header =
            $this->Loan_model
                ->get_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Loan tidak ditemukan.'

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
                    'LOAN_NO',
                    $idno
                )

                ->update(
                    'abc_mst_loan',
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
                    'Gagal menghapus Loan.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Loan berhasil dihapus.'

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
