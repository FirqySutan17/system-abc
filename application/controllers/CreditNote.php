<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CreditNote extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('accounting_cash_in')) {
            show_404();
        }

        $this->load->model(
            'CreditNote_model'
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
                'title' => 'Credit Note'
            ]
        );

        $this->load->view(
            'templates/sidebar'
        );

        $this->load->view(
            'admin/credit_note/list'
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

            $this->CreditNote_model
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

            $this->CreditNote_model
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
                ->CreditNote_model
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
                    ->CreditNote_model
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

    public function generate_cn_no()
    {
        echo json_encode([

            'status' => true,

            'cn_no' =>

                $this->CreditNote_model
                    ->generate_cn_no()

        ]);
    }

    public function get_sales_remaining()
    {
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

        $term = trim(

            $this->input->get(
                'q',
                true
            ) ?? ''

        );

        if (
            empty($plant)
            ||
            empty($customer)
        ) {

            echo json_encode([]);

            return;
        }

        echo json_encode(

            $this->CreditNote_model
                ->get_sales_remaining_select2(
                    $plant,
                    $customer,
                    $term
                )

        );
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
            $this->CreditNote_model
                ->get_data(
                    $limit,
                    $start,
                    $filters
                );

        $total =
            $this->CreditNote_model
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

            $cnNo = trim(
                $this->input->post(
                    'CN_NO',
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

            $cnDate = trim(
                $this->input->post(
                    'CN_DATE',
                    true
                ) ?? ''
            );

            $remark = trim(
                $this->input->post(
                    'REMARK',
                    true
                ) ?? ''
            );

            $salesNo =
                $this->input->post(
                    'SALES_NO'
                );

            $cnAmount =
                $this->input->post(
                    'CN_AMOUNT'
                );

            $detailRemark =
                $this->input->post(
                    'DETAIL_REMARK'
                );

            if (
                empty($cnNo) ||
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

                'cn_no' => $cnNo,

                'plant' => $plant,

                'customer' => $customer,

                'cn_date' => $cnDate,

                'remark' => $remark,

                'total_amount' => 0,

                'created_at' =>
                    date('Y-m-d H:i:s'),

                'created_by' =>
                    $this->session
                        ->userdata(
                            'username'
                        )
            ];

            $this->db->insert(
                'abc_mst_credit_note',
                $header
            );

            $idno =
                $this->db
                    ->insert_id();

            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */

            $total = 0;

            if (
                is_array($salesNo)
            ) {

                foreach (
                    $salesNo as $i => $sales
                ) {

                    if (
                        empty($sales)
                    ) {
                        continue;
                    }

                    $amount =
                        (float)(
                            $cnAmount[$i]
                            ?? 0
                        );

                    $total +=
                        $amount;

                    $detail = [

                        'cn_no' => $idno,

                        'cn_no' => $cnNo,

                        'sales' => $sales,

                        'cn_amount' => $amount,

                        'remark' =>
                            $detailRemark[$i]
                            ?? null,

                        'created_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'created_by' =>
                            $this->session
                                ->userdata(
                                    'username'
                                )
                    ];

                    $this->db->insert(
                        'abc_mst_credit_note_dtl',
                        $detail
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL
            |--------------------------------------------------------------------------
            */

            $this->db
                ->where(
                    'cn_no',
                    $idno
                )
                ->update(
                    'abc_mst_credit_note',
                    [

                        'total_amount' =>
                            $total

                    ]
                );

            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Gagal menyimpan Credit Note.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Credit Note berhasil disimpan.',

                'cn_no' => $idno

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

        $idno = (int)

            $this->input->get(
                'cn_no',
                true
            );

        if (
            empty($idno)
        ) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID kosong.'

            ]);

            return;
        }

        $header =

            $this->CreditNote_model
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

            $this->CreditNote_model
                ->get_detail(
                    $idno
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

            $idno = (int)
                $this->input->post(
                    'cn_no'
                );

            if (empty($idno)) {

                throw new Exception(
                    'ID Credit Note kosong.'
                );
            }

            $header =
                $this->CreditNote_model
                    ->get_header(
                        $idno
                    );

            if (!$header) {

                throw new Exception(
                    'Data Credit Note tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HEADER
            |--------------------------------------------------------------------------
            */

            $cnNo = trim(
                $this->input->post(
                    'CN_NO',
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

            $cnDate = trim(
                $this->input->post(
                    'CN_DATE',
                    true
                ) ?? ''
            );

            $remark = trim(
                $this->input->post(
                    'REMARK',
                    true
                ) ?? ''
            );

            $salesNo =
                $this->input->post(
                    'SALES_NO'
                );

            $cnAmount =
                $this->input->post(
                    'CN_AMOUNT'
                );

            $detailRemark =
                $this->input->post(
                    'DETAIL_REMARK'
                );

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER
            |--------------------------------------------------------------------------
            */

            $headerData = [

                'plant' => $plant,

                'customer' => $customer,

                'cn_date' => $cnDate,

                'remark' => $remark,

                'updated_at' =>
                    date('Y-m-d H:i:s'),

                'updated_by' =>
                    $this->session
                        ->userdata(
                            'username'
                        )

            ];

            $this->db

                ->where(
                    'cn_no',
                    $idno
                )

                ->update(
                    'abc_mst_credit_note',
                    $headerData
                );

            /*
            |--------------------------------------------------------------------------
            | SOFT DELETE DETAIL LAMA
            |--------------------------------------------------------------------------
            */

            $this->db

                ->where(
                    'cn_no',
                    $idno
                )

                ->where(
                    'deleted IS NULL',
                    null,
                    false
                )

                ->update(
                    'abc_mst_credit_note_dtl',
                    [

                        'deleted' => 1,

                        'deleted_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'deleted_by' =>
                            $this->session
                                ->userdata(
                                    'username'
                                )

                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL BARU
            |--------------------------------------------------------------------------
            */

            $total = 0;

            if (is_array($salesNo)) {

                foreach (
                    $salesNo as $i => $sales
                ) {

                    if (
                        empty($sales)
                    ) {
                        continue;
                    }

                    $amount =
                        (float)(
                            $cnAmount[$i]
                            ?? 0
                        );

                    $total +=
                        $amount;

                    $detail = [

                        'cn_no' => $idno,

                        'cn_no' => $cnNo,

                        'sales' => $sales,

                        'cn_amount' => $amount,

                        'remark' =>
                            $detailRemark[$i]
                            ?? null,

                        'created_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'created_by' =>
                            $this->session
                                ->userdata(
                                    'username'
                                )

                    ];

                    $this->db
                        ->insert(
                            'abc_mst_credit_note_dtl',
                            $detail
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL
            |--------------------------------------------------------------------------
            */

            $this->db

                ->where(
                    'cn_no',
                    $idno
                )

                ->update(
                    'abc_mst_credit_note',
                    [

                        'total_amount' =>
                            $total

                    ]
                );

            if (
                $this->db
                    ->trans_status()
                === false
            ) {

                throw new Exception(
                    'Update Credit Note gagal.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Credit Note berhasil diupdate.'

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
                'cn_no'
            );

        if (empty($idno)) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'ID Credit Note kosong.'

            ]);

            return;
        }

        $header =
            $this->CreditNote_model
                ->get_header(
                    $idno
                );

        if (!$header) {

            echo json_encode([

                'status' => false,

                'message' =>
                    'Credit Note tidak ditemukan.'

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
                    'cn_no',
                    $idno
                )

                ->update(
                    'abc_mst_credit_note',
                    [

                        'deleted' => 1,

                        'deleted_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'deleted_by' =>
                            $this->session
                                ->userdata(
                                    'username'
                                )

                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */

            $this->db

                ->where(
                    'cn_no',
                    $idno
                )

                ->where(
                    'deleted IS NULL',
                    null,
                    false
                )

                ->update(
                    'abc_mst_credit_note_dtl',
                    [

                        'deleted' => 1,

                        'deleted_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        'deleted_by' =>
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
                    'Gagal menghapus Credit Note.'
                );
            }

            $this->db->trans_commit();

            echo json_encode([

                'status' => true,

                'message' =>
                    'Credit Note berhasil dihapus.'

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
