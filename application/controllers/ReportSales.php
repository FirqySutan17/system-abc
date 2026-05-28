<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportSales extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_productions_sales')) {
            show_404();
        }
        $this->load->model('ReportSales_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data['plants'] = $this->db
            ->where('HEAD_CODE', 'PLANT')
            ->order_by('CODE_NAME', 'ASC')
            ->get('abc_cd_code')
            ->result();

        $data['customers'] = $this->db
            ->order_by('FULL_NAME', 'ASC')
            ->get('abc_cd_customer')
            ->result();

        $this->load->view('templates/header', ['title' => 'Report Sales']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_sales/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_sales()
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
                $this->input->get(
                    'search',
                    true
                )
            ),

            'plant' => $this->input->get(
                'plant',
                true
            ),

            'customer' => $this->input->get(
                'customer',
                true
            ),

            'pembayaran' => $this->input->get(
                'pembayaran',
                true
            ),

            'status' => $this->input->get(
                'status',
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
            $this->ReportSales_model
                ->get_sales_report(

                    $limit,
                    $start,
                    $filter

                );

        $total =
            $this->ReportSales_model
                ->count_sales_report(
                    $filter
                );

        $summary =
            $this->ReportSales_model
                ->summary_sales_report(
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
                $this->build_sales_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    private function build_sales_pagination(
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
                    data-sales-pagination-page="'.$prev.'">

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
                        data-sales-pagination-page="'.$i.'">

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
                    data-sales-pagination-page="'.$next.'">

                    Next

                </a>

            </li>

        ';

        $html .= '</ul>';

        return $html;
    }

    public function export_excel_sales()
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

            'customer' => $this->input->get(
                'customer',
                true
            ),

            'pembayaran' => $this->input->get(
                'pembayaran',
                true
            ),

            'status' => $this->input->get(
                'status',
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
            $this->ReportSales_model
                ->get_sales_report(

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

            "Content-Disposition: attachment; filename=REPORT_SALES_".date('YmdHis').".xls"

        );

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        $this->load->view(

            'admin/report_sales/excel_sales',

            [

                'rows' => $rows

            ]

        );
    }

    private function build_pagination($totalPages, $currentPage, $mode = 'url')
    {
        if ($totalPages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';

        $range = 2; // tampilkan ±2 halaman dari current

        $start = max(1, $currentPage - $range);
        $end   = min($totalPages, $currentPage + $range);

        // ================= PREV =================
        if ($currentPage > 1) {
            $prev = $currentPage - 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$prev'>&laquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$prev' class='page-link'>&laquo;</a>
                    </li>
                ";
            }
        }

        // ================= FIRST PAGE =================
        if ($start > 1) {
            $html .= $this->page_link(1, $currentPage, $mode);
            if ($start > 2) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }

        // ================= MIDDLE PAGES =================
        for ($i = $start; $i <= $end; $i++) {
            $html .= $this->page_link($i, $currentPage, $mode);
        }

        // ================= LAST PAGE =================
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $html .= $this->page_link($totalPages, $currentPage, $mode);
        }

        // ================= NEXT =================
        if ($currentPage < $totalPages) {
            $next = $currentPage + 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$next'>&raquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$next' class='page-link'>&raquo;</a>
                    </li>
                ";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    private function page_link($page, $currentPage, $mode)
    {
        $active = $page == $currentPage ? 'active' : '';

        if ($mode === 'ajax') {
            return "
                <li class='page-item $active'>
                    <a href='#' class='page-link' data-page='$page'>$page</a>
                </li>
            ";
        } else {
            return "
                <li class='page-item $active'>
                    <a href='?page=$page' class='page-link'>$page</a>
                </li>
            ";
        }
    }

    private function convert_date($date)
    {
        if (!$date) return null;

        // support yyyy-mm-dd (dari input date)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // support dd/mm/yyyy (kalau ada)
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

}
