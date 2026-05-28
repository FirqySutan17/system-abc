<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportAccounting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model(
            'ReportAccounting_model'
        );

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */

        if(
            !$this->session->userdata('username')
        ){

            redirect('login');

        }
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Report Accounting']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_accounting/index');
        $this->load->view('templates/footer');
        
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD PAYMENT
    |--------------------------------------------------------------------------
    */

    public function load_payment()
    {
        /*
        |--------------------------------------------------------------------------
        | PARAM
        |--------------------------------------------------------------------------
        */

        $page =
            max(
                1,
                (int) $this->input->get('page')
            );

        $limit =
            max(
                1,
                (int) $this->input->get('limit')
            );

        $search =
            trim(
                $this->input->get(
                    'search',
                    true
                )
            );

        $plant =
            $this->input->get(
                'plant',
                true
            );

        $supplier =
            $this->input->get(
                'supplier',
                true
            );

        $dateFrom =
            $this->input->get(
                'date_from',
                true
            );

        $dateTo =
            $this->input->get(
                'date_to',
                true
            );

        $start =
            ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | MODEL
        |--------------------------------------------------------------------------
        */

        $this->load->model(
            'ReportAccounting_model'
        );

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_payment_header_report(

                    $limit,

                    $start,

                    $search,

                    $plant,

                    $supplier,

                    $dateFrom,

                    $dateTo

                );

        foreach($rows as &$r){

            $r['DETAILS'] =
                $this->ReportAccounting_model
                    ->get_payment_detail_report(

                        $r['PAYMENT'],

                        $r['PLANT']

                    );
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $total =
            $this->ReportAccounting_model
                ->count_payment_report(

                    $search,

                    $plant,

                    $supplier,

                    $dateFrom,

                    $dateTo

                );

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary =
            $this->ReportAccounting_model
                ->summary_payment_report(

                    $search,

                    $plant,

                    $supplier,

                    $dateFrom,

                    $dateTo

                );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'summary' => $summary,

            'total' => (int) $total,

            'page' => (int) $page,

            'pages' => (int) $pages,

            'pagination' => $this->build_pagination(
                $pages,
                $page,
                'ReportPayment.loadData'
            )

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PAYMENT EXCEL
    |--------------------------------------------------------------------------
    */

    public function export_payment_excel()
    {
        /*
        |--------------------------------------------------------------------------
        | PARAM
        |--------------------------------------------------------------------------
        */

        $search =
            trim(
                $this->input->get(
                    'search',
                    true
                )
            );

        $plant =
            $this->input->get(
                'plant',
                true
            );

        $supplier =
            $this->input->get(
                'supplier',
                true
            );

        $dateFrom =
            $this->input->get(
                'date_from',
                true
            );

        $dateTo =
            $this->input->get(
                'date_to',
                true
            );

        /*
        |--------------------------------------------------------------------------
        | MODEL
        |--------------------------------------------------------------------------
        */

        $this->load->model(
            'ReportAccounting_model'
        );

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_payment_header_report(

                    999999,

                    0,

                    $search,

                    $plant,

                    $supplier,

                    $dateFrom,

                    $dateTo

                );

        /*
        |--------------------------------------------------------------------------
        | HEADER EXCEL
        |--------------------------------------------------------------------------
        */

        header(
            "Content-Type: application/vnd.ms-excel"
        );

        header(
            "Content-Disposition: attachment; filename=REPORT_PAYMENT_".date('YmdHis').".xls"
        );

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $data = [

            'rows' => $rows

        ];

        $this->load->view(

            'admin/report_accounting/export_payment_excel',

            $data

        );
    }

    public function load_cashin()
    {
        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $page =
            max(
                1,
                (int) $this->input->get('page')
            );

        $limit =
            max(
                1,
                (int) $this->input->get('limit')
            );

        $start =
            ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $search =
            trim(
                $this->input->get(
                    'search',
                    true
                )
            );

        $plant =
            trim(
                $this->input->get(
                    'plant',
                    true
                )
            );

        $customer =
            trim(
                $this->input->get(
                    'customer',
                    true
                )
            );

        $pembayaran =
            trim(
                $this->input->get(
                    'pembayaran',
                    true
                )
            );

        $mode =
            trim(
                $this->input->get(
                    'mode',
                    true
                )
            );

        $dateFrom =
            trim(
                $this->input->get(
                    'date_from',
                    true
                )
            );

        $dateTo =
            trim(
                $this->input->get(
                    'date_to',
                    true
                )
            );

        /*
        |--------------------------------------------------------------------------
        | FILTER ARRAY
        |--------------------------------------------------------------------------
        */

        $filter = [

            'search'      => $search,

            'plant'       => $plant,

            'customer'    => $customer,

            'pembayaran'  => $pembayaran,

            'mode'        => $mode,

            'date_from'   => $dateFrom,

            'date_to'     => $dateTo

        ];

        /*
        |--------------------------------------------------------------------------
        | LOAD MODEL
        |--------------------------------------------------------------------------
        */

        $this->load->model(
            'ReportAccounting_model'
        );

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_report_cashin(

                    $limit,

                    $start,

                    $filter

                );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        foreach($rows as &$row){

            $row['DETAILS'] =

                $this->ReportAccounting_model
                    ->get_report_cashin_detail(

                        $row['CASH_IN'],

                        $row['PLANT']

                    );
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $total =
            $this->ReportAccounting_model
                ->count_report_cashin(
                    $filter
                );

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary =
            $this->ReportAccounting_model
                ->get_report_cashin_summary(
                    $filter
                );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'summary' => $summary,

            'page' => (int) $page,

            'pages' => (int) $pages,

            'total' => (int) $total,

            'pagination' =>
                $this->build_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    public function export_excel_cashin()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filter = [

            'search' =>
                trim(
                    $this->input->get(
                        'search',
                        true
                    )
                ),

            'plant' =>
                trim(
                    $this->input->get(
                        'plant',
                        true
                    )
                ),

            'customer' =>
                trim(
                    $this->input->get(
                        'customer',
                        true
                    )
                ),

            'pembayaran' =>
                trim(
                    $this->input->get(
                        'pembayaran',
                        true
                    )
                ),

            'mode' =>
                trim(
                    $this->input->get(
                        'mode',
                        true
                    )
                ),

            'date_from' =>
                trim(
                    $this->input->get(
                        'date_from',
                        true
                    )
                ),

            'date_to' =>
                trim(
                    $this->input->get(
                        'date_to',
                        true
                    )
                )

        ];

        /*
        |--------------------------------------------------------------------------
        | LOAD MODEL
        |--------------------------------------------------------------------------
        */

        $this->load->model(
            'ReportAccounting_model'
        );

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_report_cashin(
                    999999,
                    0,
                    $filter
                );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        foreach($rows as &$row){

            $row['DETAILS'] =

                $this->ReportAccounting_model
                    ->get_report_cashin_detail(

                        $row['CASH_IN'],

                        $row['PLANT']

                    );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER EXCEL
        |--------------------------------------------------------------------------
        */

        header(
            "Content-Type: application/vnd.ms-excel"
        );

        header(
            "Content-Disposition: attachment; filename=REPORT_CASHIN_".date('YmdHis').".xls"
        );

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $this->load->view(

            'admin/report_accounting/excel_cashin',

            [

                'rows' => $rows

            ]

        );
    }

    /* =========================================================
    * LOAD REPORT COST
    * =======================================================*/

    public function load_cost()
    {
        $page =
            max(
                1,
                (int) $this->input->get('page')
            );

        $limit =
            max(
                1,
                (int) $this->input->get('limit')
            );

        $start =
            ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filter = [

            'search' => trim(
                $this->input->get('search', true)
            ),

            'plant' => $this->input->get(
                'plant',
                true
            ),

            'pembayaran' => $this->input->get(
                'pembayaran',
                true
            ),

            'date_from' => $this->input->get(
                'date_from',
                true
            ),

            'date_to' => $this->input->get(
                'date_to',
                true
            )

        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_cost_report(

                    $limit,
                    $start,
                    $filter

                );

        $total =
            $this->ReportAccounting_model
                ->count_cost_report(
                    $filter
                );

        $summary =
            $this->ReportAccounting_model
                ->summary_cost_report(
                    $filter
                );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages =
            $total > 0
                ? ceil($total / $limit)
                : 1;

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'summary' => $summary,

            'page' => (int) $page,

            'pages' => (int) $pages,

            'total' => (int) $total,

            'pagination' =>
                $this->build_cost_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    public function export_excel_cost()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filter = [

            'search' => trim(
                $this->input->get(
                    'search',
                    true
                )
            ),

            'plant' => $this->input->get(
                'plant',
                true
            ),

            'pembayaran' => $this->input->get(
                'pembayaran',
                true
            ),

            'date_from' => $this->input->get(
                'date_from',
                true
            ),

            'date_to' => $this->input->get(
                'date_to',
                true
            )

        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows =
            $this->ReportAccounting_model
                ->get_cost_report(

                    999999,
                    0,
                    $filter

                );

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        header(
            "Content-Type: application/vnd.ms-excel"
        );

        header(

            "Content-Disposition: attachment; filename=REPORT_COST_".date('YmdHis').".xls"

        );

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $this->load->view(

            'admin/report_accounting/excel_cost',

            [

                'rows' => $rows

            ]

        );
    }

    /* =========================================================
    * PAGINATION
    * =======================================================*/

    private function build_cost_pagination(
        $pages,
        $current
    )
    {
        if($pages <= 1){

            return '';

        }

        $html =
            '<ul class="pagination pagination-sm mb-0">';

        /*
        |--------------------------------------------------------------------------
        | PREV
        |--------------------------------------------------------------------------
        */

        $prev =
            max(
                1,
                $current - 1
            );

        $disabled =
            $current == 1
                ? 'disabled'
                : '';

        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-cost-pagination-page="'.$prev.'">

                    Prev

                </a>

            </li>

        ';

        /*
        |--------------------------------------------------------------------------
        | NUMBER
        |--------------------------------------------------------------------------
        */

        for($i = 1; $i <= $pages; $i++){

            $active =
                $i == $current
                    ? 'active'
                    : '';

            $html .= '

                <li class="page-item '.$active.'">

                    <a
                        href="#"
                        class="page-link"
                        data-cost-pagination-page="'.$i.'">

                        '.$i.'

                    </a>

                </li>

            ';
        }

        /*
        |--------------------------------------------------------------------------
        | NEXT
        |--------------------------------------------------------------------------
        */

        $next =
            min(
                $pages,
                $current + 1
            );

        $disabled =
            $current == $pages
                ? 'disabled'
                : '';

        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-cost-pagination-page="'.$next.'">

                    Next

                </a>

            </li>

        ';

        $html .= '</ul>';

        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    private function build_pagination(
        $totalPages,
        $currentPage,
        $jsFunction = 'loadPage'
    ){
        if($totalPages <= 1){

            return '';

        }

        $html = '<ul class="pagination pagination-sm mb-0">';

        /*
        |--------------------------------------------------------------------------
        | PREV
        |--------------------------------------------------------------------------
        */

        $prev =
            max(
                1,
                $currentPage - 1
            );

        $disabled =
            $currentPage <= 1
                ? 'disabled'
                : '';

        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    class="page-link"
                    href="javascript:void(0)"
                    onclick="'.$jsFunction.'('.$prev.')">

                    Previous

                </a>

            </li>

        ';

        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        for(
            $i = 1;
            $i <= $totalPages;
            $i++
        ){

            $active =
                $i == $currentPage
                    ? 'active'
                    : '';

            $html .= '

                <li class="page-item '.$active.'">

                    <a
                        class="page-link"
                        href="javascript:void(0)"
                        onclick="'.$jsFunction.'('.$i.')">

                        '.$i.'

                    </a>

                </li>

            ';
        }

        /*
        |--------------------------------------------------------------------------
        | NEXT
        |--------------------------------------------------------------------------
        */

        $next =
            min(
                $totalPages,
                $currentPage + 1
            );

        $disabled =
            $currentPage >= $totalPages
                ? 'disabled'
                : '';

        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    class="page-link"
                    href="javascript:void(0)"
                    onclick="'.$jsFunction.'('.$next.')">

                    Next

                </a>

            </li>

        ';

        $html .= '</ul>';

        return $html;
    }
}