<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
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

            'customer' => trim(
                $this->input->get(
                    'customer',
                    true
                )
            ),

            'pembayaran' => trim(
                $this->input->get(
                    'pembayaran',
                    true
                )
            ),

            'date_from' => trim(
                $this->input->get(
                    'date_from',
                    true
                )
            ),

            'date_to' => trim(
                $this->input->get(
                    'date_to',
                    true
                )
            )

        ];


        /*
        |--------------------------------------------------------------------------
        | DATA
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

        unset($row);


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
                ? ceil(
                    $total / $limit
                )
                : 1;


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' =>
                true,

            'rows' =>
                $rows,

            'summary' =>
                $summary,

            'page' =>
                (int) $page,

            'pages' =>
                (int) $pages,

            'total' =>
                (int) $total,

            'pagination' =>
                $this->build_cashin_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    private function build_cashin_pagination(
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
        | PREVIOUS
        |--------------------------------------------------------------------------
        */

        if($current > 1){

            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-ci-pagination-page="'
                        . ($current - 1)
                        . '">

                        &laquo;

                    </a>

                </li>

            ';

        }
        else{

            $html .= '

                <li class="page-item disabled">

                    <span class="page-link">

                        &laquo;

                    </span>

                </li>

            ';

        }


        /*
        |--------------------------------------------------------------------------
        | RANGE
        |--------------------------------------------------------------------------
        */

        $range = 2;


        $start =
            max(
                1,
                $current - $range
            );


        $end =
            min(
                $pages,
                $current + $range
            );


        /*
        |--------------------------------------------------------------------------
        | FIRST PAGE
        |--------------------------------------------------------------------------
        */

        if($start > 1){

            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-ci-pagination-page="1">

                        1

                    </a>

                </li>

            ';


            if($start > 2){

                $html .= '

                    <li class="page-item disabled">

                        <span class="page-link">

                            ...

                        </span>

                    </li>

                ';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE NUMBER
        |--------------------------------------------------------------------------
        */

        for(
            $i = $start;
            $i <= $end;
            $i++
        ){

            $active =
                $i == $current
                    ? ' active'
                    : '';


            $html .= '

                <li class="page-item'
                . $active
                . '">

                    <a
                        href="#"
                        class="page-link"
                        data-ci-pagination-page="'
                        . $i
                        . '">

                        '
                        . $i
                        . '

                    </a>

                </li>

            ';

        }


        /*
        |--------------------------------------------------------------------------
        | LAST PAGE
        |--------------------------------------------------------------------------
        */

        if($end < $pages){

            if($end < ($pages - 1)){

                $html .= '

                    <li class="page-item disabled">

                        <span class="page-link">

                            ...

                        </span>

                    </li>

                ';

            }


            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-ci-pagination-page="'
                        . $pages
                        . '">

                        '
                        . $pages
                        . '

                    </a>

                </li>

            ';

        }


        /*
        |--------------------------------------------------------------------------
        | NEXT
        |--------------------------------------------------------------------------
        */

        if($current < $pages){

            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-ci-pagination-page="'
                        . ($current + 1)
                        . '">

                        &raquo;

                    </a>

                </li>

            ';

        }
        else{

            $html .= '

                <li class="page-item disabled">

                    <span class="page-link">

                        &raquo;

                    </span>

                </li>

            ';

        }


        $html .=
            '</ul>';


        return $html;
    }

    public function export_excel_cashin()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'search' => trim(
                $this->input->get(
                    'search',
                    true
                )
            ),

            'customer' => trim(
                $this->input->get(
                    'customer',
                    true
                )
            ),

            'pembayaran' => trim(
                $this->input->get(
                    'pembayaran',
                    true
                )
            ),

            'date_from' => trim(
                $this->input->get(
                    'date_from',
                    true
                )
            ),

            'date_to' => trim(
                $this->input->get(
                    'date_to',
                    true
                )
            )

        ];


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
                    $filters
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

        unset($row);


        /*
        |--------------------------------------------------------------------------
        | NO DATA
        |--------------------------------------------------------------------------
        */

        if(empty($rows)){

            show_error(
                'No data found for export'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet =
            $spreadsheet->getActiveSheet();

        $sheet->setTitle(
            'Cash In Report'
        );


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoPath =
            FCPATH . 'assets/img/abc-trans.png';


        if(file_exists($logoPath)){

            $drawing =
                new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

            $drawing->setPath(
                $logoPath
            );

            $drawing->setCoordinates(
                'A1'
            );

            $drawing->setHeight(
                55
            );

            $drawing->setWorksheet(
                $sheet
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B1:L1'
        );

        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );

        $sheet->getStyle(
            'B1:L1'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(18);


        $sheet->mergeCells(
            'B2:L2'
        );

        $sheet->setCellValue(
            'B2',
            'CASH IN REPORT'
        );

        $sheet->getStyle(
            'B2:L2'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(13);


        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        $dateFrom =
            !empty($filters['date_from'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_from']
                    )
                )
                : '-';


        $dateTo =
            !empty($filters['date_to'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_to']
                    )
                )
                : '-';


        $period =
            $dateFrom
            . ' - '
            . $dateTo;


        $sheet->mergeCells(
            'B3:L3'
        );

        $sheet->setCellValue(
            'B3',
            'Period : ' . $period
        );

        $sheet->getStyle(
            'B3:L3'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | FILTER INFORMATION
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B4:L4'
        );


        $filterText =
            'Filter : ';


        if(!empty($filters['customer'])){

            $filterText .=
                'Customer ' .
                $filters['customer'];

        }
        else{

            $filterText .=
                'All Customer';

        }


        if(!empty($filters['pembayaran'])){

            $filterText .=
                ' | Payment ' .
                $filters['pembayaran'];

        }
        else{

            $filterText .=
                ' | All Payment';

        }


        if(!empty($filters['search'])){

            $filterText .=
                ' | Search ' .
                $filters['search'];

        }


        $sheet->setCellValue(
            'B4',
            $filterText
        );

        $sheet->getStyle(
            'B4:L4'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | GROUP HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A6:G6'
        );

        $sheet->setCellValue(
            'A6',
            'CASH IN INFORMATION'
        );


        $sheet->mergeCells(
            'H6:L6'
        );

        $sheet->setCellValue(
            'H6',
            'CASH IN DETAIL'
        );


        $sheet->getStyle(
            'A6:L6'
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 11

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'DCE6F1'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | COLUMN HEADER
        |--------------------------------------------------------------------------
        */

        $headers = [

            'CASH IN',
            'TANGGAL',
            'PLANT',
            'CUSTOMER',
            'PAYMENT',
            'SLIP NO',
            'AMOUNT',

            'SALES',
            'INVOICE',
            'PAID',
            'REMAINING',
            'STATUS'

        ];


        $column = 1;


        foreach($headers as $header){

            $cell =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $column
                )
                . '7';


            $sheet->setCellValue(
                $cell,
                $header
            );


            $column++;

        }


        $sheet->getStyle(
            'A7:L7'
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 10

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    'wrapText' => true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'EEF2F7'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'C7D0D9'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $row = 8;


        $totalAmount =
            0;

        $totalInvoice =
            0;

        $totalPaid =
            0;

        $totalRemaining =
            0;


        /*
        |--------------------------------------------------------------------------
        | GROUP BY CASH IN
        |--------------------------------------------------------------------------
        */

        $grouped = [];


        foreach($rows as $r){

            $key =
                ($r['CASH_IN'] ?? '')
                . '|'
                .
                ($r['PLANT'] ?? '');


            if(!isset($grouped[$key])){

                $grouped[$key] = [];

            }


            $grouped[$key][] =
                $r;

        }


        /*
        |--------------------------------------------------------------------------
        | LOOP CASH IN
        |--------------------------------------------------------------------------
        */

        foreach($grouped as $group){

            /*
            |--------------------------------------------------------------------------
            | Karena setiap header Cash In biasanya sudah satu row,
            | gunakan row pertama sebagai header information.
            |--------------------------------------------------------------------------
            */

            $r =
                $group[0];


            $details =
                $r['DETAILS']
                ?? [];


            /*
            |--------------------------------------------------------------------------
            | Jika tidak ada detail
            |--------------------------------------------------------------------------
            */

            if(empty($details)){

                $sheet->setCellValue(
                    "A{$row}",
                    $r['CASH_IN']
                        ?? '-'
                );


                $sheet->setCellValue(
                    "B{$row}",
                    !empty($r['CASHIN_DATE'])
                        ? date(
                            'd F Y',
                            strtotime(
                                $r['CASHIN_DATE']
                            )
                        )
                        : '-'
                );


                $sheet->setCellValue(
                    "C{$row}",
                    $r['PLANT_NAME']
                        ?? $r['PLANT']
                        ?? '-'
                );


                $sheet->setCellValue(
                    "D{$row}",
                    trim(
                        ($r['CUSTOMER'] ?? '')
                        . ' - '
                        .
                        ($r['CUSTOMER_NAME'] ?? '')
                    )
                );


                $sheet->setCellValue(
                    "E{$row}",
                    $r['PEMBAYARAN']
                        ?? '-'
                );


                $sheet->setCellValue(
                    "F{$row}",
                    $r['SLIP_NO']
                        ?? '-'
                );


                $sheet->setCellValue(
                    "G{$row}",
                    (float)(
                        $r['AMOUNT']
                        ?? 0
                    )
                );


                $sheet->setCellValue(
                    "H{$row}",
                    '-'
                );


                $sheet->setCellValue(
                    "I{$row}",
                    0
                );


                $sheet->setCellValue(
                    "J{$row}",
                    0
                );


                $sheet->setCellValue(
                    "K{$row}",
                    0
                );


                $sheet->setCellValue(
                    "L{$row}",
                    '-'
                );


                /*
                |--------------------------------------------------------------------------
                | HEADER TOTAL
                |--------------------------------------------------------------------------
                */

                $totalAmount +=
                    (float)(
                        $r['AMOUNT']
                        ?? 0
                    );


                $row++;

                continue;

            }


            /*
            |--------------------------------------------------------------------------
            | GROUP START
            |--------------------------------------------------------------------------
            */

            $groupStartRow =
                $row;


            $groupCount =
                count($details);


            /*
            |--------------------------------------------------------------------------
            | DETAIL LOOP
            |--------------------------------------------------------------------------
            */

            foreach($details as $index => $detail){

                /*
                |--------------------------------------------------------------------------
                | CASH IN INFORMATION
                |--------------------------------------------------------------------------
                */

                if($index === 0){

                    $sheet->setCellValue(
                        "A{$row}",
                        $r['CASH_IN']
                            ?? '-'
                    );


                    $sheet->setCellValue(
                        "B{$row}",
                        !empty($r['CASHIN_DATE'])
                            ? date(
                                'd F Y',
                                strtotime(
                                    $r['CASHIN_DATE']
                                )
                            )
                            : '-'
                    );


                    $sheet->setCellValue(
                        "C{$row}",
                        $r['PLANT_NAME']
                            ?? $r['PLANT']
                            ?? '-'
                    );


                    $sheet->setCellValue(
                        "D{$row}",
                        trim(
                            ($r['CUSTOMER'] ?? '')
                            . ' - '
                            .
                            ($r['CUSTOMER_NAME'] ?? '')
                        )
                    );


                    $sheet->setCellValue(
                        "E{$row}",
                        $r['PEMBAYARAN']
                            ?? '-'
                    );


                    $sheet->setCellValue(
                        "F{$row}",
                        $r['SLIP_NO']
                            ?? '-'
                    );


                    $sheet->setCellValue(
                        "G{$row}",
                        (float)(
                            $r['AMOUNT']
                            ?? 0
                        )
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | HEADER TOTAL
                    |--------------------------------------------------------------------------
                    */

                    $totalAmount +=
                        (float)(
                            $r['AMOUNT']
                            ?? 0
                        );

                }


                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "H{$row}",
                    $detail['SALES']
                        ?? '-'
                );


                /*
                |--------------------------------------------------------------------------
                | INVOICE
                |--------------------------------------------------------------------------
                */

                $invoice =
                    (float)(
                        $detail['AMOUNT_INVOICE']
                        ?? 0
                    );


                $sheet->setCellValue(
                    "I{$row}",
                    $invoice
                );


                /*
                |--------------------------------------------------------------------------
                | PAID
                |--------------------------------------------------------------------------
                */

                $paid =
                    (float)(
                        $detail['AMOUNT_OFFSET']
                        ?? 0
                    );


                $sheet->setCellValue(
                    "J{$row}",
                    $paid
                );


                /*
                |--------------------------------------------------------------------------
                | REMAINING
                |--------------------------------------------------------------------------
                */

                $remaining =
                    (float)(
                        $detail['REMAINING']
                        ?? 0
                    );


                $sheet->setCellValue(
                    "K{$row}",
                    $remaining
                );


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                $status =
                    $detail['SALES_STATUS']
                    ?? 'OPEN';


                if(empty($status)){

                    $status =
                        'OPEN';

                }


                $sheet->setCellValue(
                    "L{$row}",
                    $status
                );


                /*
                |--------------------------------------------------------------------------
                | DETAIL TOTAL
                |--------------------------------------------------------------------------
                */

                $totalInvoice +=
                    $invoice;


                $totalPaid +=
                    $paid;


                $totalRemaining +=
                    $remaining;


                $row++;

            }


            /*
            |--------------------------------------------------------------------------
            | GROUP END
            |--------------------------------------------------------------------------
            */

            $groupEndRow =
                $row - 1;


            /*
            |--------------------------------------------------------------------------
            | MERGE CASH IN INFORMATION
            |--------------------------------------------------------------------------
            */

            if($groupCount > 1){

                foreach([

                    'A',
                    'B',
                    'C',
                    'D',
                    'E',
                    'F',
                    'G'

                ] as $col){

                    $sheet->mergeCells(
                        "{$col}{$groupStartRow}:{$col}{$groupEndRow}"
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | GROUP BORDER
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$groupStartRow}:L{$groupEndRow}"
            )
                ->applyFromArray([

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                            'color' => [

                                'rgb' => 'D1D9E2'

                            ]

                        ]

                    ],

                    'alignment' => [

                        'vertical' =>
                            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                    ]

                ]);


            /*
            |--------------------------------------------------------------------------
            | SEPARATOR CASH IN
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$groupStartRow}:L{$groupStartRow}"
            )
                ->getBorders()
                ->getTop()
                ->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                );

        }


        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "A{$row}:F{$row}"
        );


        $sheet->setCellValue(
            "A{$row}",
            'GRAND TOTAL'
        );


        $sheet->setCellValue(
            "G{$row}",
            $totalAmount
        );


        $sheet->setCellValue(
            "H{$row}",
            ''
        );


        $sheet->setCellValue(
            "I{$row}",
            $totalInvoice
        );


        $sheet->setCellValue(
            "J{$row}",
            $totalPaid
        );


        $sheet->setCellValue(
            "K{$row}",
            $totalRemaining
        );


        $sheet->setCellValue(
            "L{$row}",
            ''
        );


        $sheet->getStyle(
            "A{$row}:L{$row}"
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'F3F6F9'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "G8:G{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        $sheet->getStyle(
            "I8:K{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "G8:G{$row}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        $sheet->getStyle(
            "I8:K{$row}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = [

            'A' => 18,
            'B' => 16,
            'C' => 16,
            'D' => 30,
            'E' => 15,
            'F' => 18,
            'G' => 18,

            'H' => 18,
            'I' => 18,
            'J' => 18,
            'K' => 18,
            'L' => 15

        ];


        foreach($widths as $col => $width){

            $sheet
                ->getColumnDimension($col)
                ->setWidth($width);

        }


        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHT
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getRowDimension(6)
            ->setRowHeight(22);


        $sheet
            ->getRowDimension(7)
            ->setRowHeight(35);


        /*
        |--------------------------------------------------------------------------
        | FREEZE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'A8'
        );


        /*
        |--------------------------------------------------------------------------
        | AUTOFILTER
        |--------------------------------------------------------------------------
        */

        $sheet->setAutoFilter(
            "A7:L{$row}"
        );


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );


        $sheet->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3
            );


        $sheet->getPageSetup()
            ->setFitToWidth(
                1
            );


        $sheet->getPageSetup()
            ->setFitToHeight(
                0
            );


        $sheet->getPageMargins()
            ->setTop(0.4);


        $sheet->getPageMargins()
            ->setBottom(0.4);


        $sheet->getPageMargins()
            ->setLeft(0.25);


        $sheet->getPageMargins()
            ->setRight(0.25);


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment; filename="REPORT_CASHIN.xlsx"'
        );

        header(
            'Cache-Control: max-age=0'
        );


        $writer =
            new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                $spreadsheet
            );


        $writer->save(
            'php://output'
        );


        exit;
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

        $report_data = $this->ReportAccounting_model
                ->get_cost_report(

                    $limit,
                    $start,
                    $filter

                );

        $rows = $report_data['cost_list'] ?? [];
            

        $total = $report_data['total_count'] ?? 0;

        $summary = $report_data['summary'] ?? [];

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
        $filters = [

            'plant' => $this->input
                ->get('plant', true),

            'pembayaran' => $this->input
                ->get('pembayaran', true),

            'search' => $this->input
                ->get('search', true),

            'date_from' => $this->input
                ->get('date_from', true),

            'date_to' => $this->input
                ->get('date_to', true)

        ];


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $reportData =
            $this->ReportAccounting_model
                ->get_cost_report(
                    999999,
                    0,
                    $filters
                );


        $rows =
            $reportData['cost_list']
            ?? [];


        if(empty($rows)){

            show_error(
                'No data found for export'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet =
            $spreadsheet->getActiveSheet();

        $sheet->setTitle(
            'Cost Report'
        );


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoPath =
            FCPATH . 'assets/img/abc-trans.png';


        if(file_exists($logoPath)){

            $drawing =
                new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

            $drawing->setPath(
                $logoPath
            );

            $drawing->setCoordinates(
                'A1'
            );

            $drawing->setHeight(
                55
            );

            $drawing->setWorksheet(
                $sheet
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B1:I1'
        );

        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );

        $sheet->getStyle(
            'B1:I1'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(18);


        $sheet->mergeCells(
            'B2:I2'
        );

        $sheet->setCellValue(
            'B2',
            'COST REPORT'
        );

        $sheet->getStyle(
            'B2:I2'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(13);


        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        $dateFrom =
            !empty($filters['date_from'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_from']
                    )
                )
                : '-';


        $dateTo =
            !empty($filters['date_to'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_to']
                    )
                )
                : '-';


        $period =
            $dateFrom
            . ' - '
            . $dateTo;


        $sheet->mergeCells(
            'B3:I3'
        );

        $sheet->setCellValue(
            'B3',
            'Period : ' . $period
        );

        $sheet->getStyle(
            'B3:I3'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | FILTER INFORMATION
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B4:I4'
        );

        $filterText = 'Filter : ';


        if(!empty($filters['plant'])){

            $filterText .=
                'Plant ' .
                $filters['plant'];

        }else{

            $filterText .=
                'All Plant';

        }


        if(!empty($filters['pembayaran'])){

            $filterText .=
                ' | Payment ' .
                $filters['pembayaran'];

        }else{

            $filterText .=
                ' | All Payment';

        }


        $sheet->setCellValue(
            'B4',
            $filterText
        );

        $sheet->getStyle(
            'B4:I4'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | GROUP HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A6:E6'
        );

        $sheet->setCellValue(
            'A6',
            'COST INFORMATION'
        );


        $sheet->mergeCells(
            'F6:I6'
        );

        $sheet->setCellValue(
            'F6',
            'COST DETAIL'
        );


        $sheet->getStyle(
            'A6:I6'
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 11

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'DCE6F1'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | COLUMN HEADER
        |--------------------------------------------------------------------------
        */

        $headers = [

            'COST',
            'TANGGAL',
            'PLANT',
            'PAYMENT',
            'SLIP NO',

            'TIPE COST',
            'KUANTITAS',
            'HARGA',
            'TOTAL'

        ];


        $column = 1;


        foreach($headers as $header){

            $cell =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $column
                )
                . '7';


            $sheet->setCellValue(
                $cell,
                $header
            );


            $column++;

        }


        $sheet->getStyle(
            'A7:I7'
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true,

                    'size' => 10

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    'wrapText' => true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'EEF2F7'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'C7D0D9'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $row = 8;


        $totalQty = 0;

        $totalPrice = 0;

        $totalCost = 0;


        /*
        |--------------------------------------------------------------------------
        | LOOP COST
        |--------------------------------------------------------------------------
        */

        foreach($rows as $r){

            $details =
                $r['DETAIL']
                ?? [];


            /*
            |--------------------------------------------------------------------------
            | COST WITHOUT DETAIL
            |--------------------------------------------------------------------------
            */

            if(empty($details)){

                $sheet->setCellValue(
                    "A{$row}",
                    $r['COST']
                        ?? '-'
                );

                $sheet->setCellValue(
                    "B{$row}",
                    !empty($r['COST_DATE'])
                        ? date(
                            'd F Y',
                            strtotime(
                                $r['COST_DATE']
                            )
                        )
                        : '-'
                );

                $sheet->setCellValue(
                    "C{$row}",
                    $r['PLANT_NAME']
                        ?? $r['PLANT']
                        ?? '-'
                );

                $sheet->setCellValue(
                    "D{$row}",
                    $r['PEMBAYARAN']
                        ?? '-'
                );

                $sheet->setCellValue(
                    "E{$row}",
                    $r['SLIP_NO']
                        ?? '-'
                );

                $sheet->setCellValue(
                    "F{$row}",
                    '-'
                );

                $sheet->setCellValue(
                    "G{$row}",
                    0
                );

                $sheet->setCellValue(
                    "H{$row}",
                    0
                );

                $sheet->setCellValue(
                    "I{$row}",
                    0
                );

                $row++;

                continue;

            }


            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */

            $groupStartRow = $row;

            $groupCount =
                count($details);


            foreach($details as $index => $detail){

                /*
                |--------------------------------------------------------------------------
                | COST INFORMATION
                |--------------------------------------------------------------------------
                */

                if($index === 0){

                    $sheet->setCellValue(
                        "A{$row}",
                        $r['COST']
                            ?? '-'
                    );

                    $sheet->setCellValue(
                        "B{$row}",
                        !empty($r['COST_DATE'])
                            ? date(
                                'd F Y',
                                strtotime(
                                    $r['COST_DATE']
                                )
                            )
                            : '-'
                    );

                    $sheet->setCellValue(
                        "C{$row}",
                        $r['PLANT_NAME']
                            ?? $r['PLANT']
                            ?? '-'
                    );

                    $sheet->setCellValue(
                        "D{$row}",
                        $r['PEMBAYARAN']
                            ?? '-'
                    );

                    $sheet->setCellValue(
                        "E{$row}",
                        $r['SLIP_NO']
                            ?? '-'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | COST DETAIL
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "F{$row}",
                    $detail['COST_NAME']
                        ?? $detail['TIPE_COST']
                        ?? '-'
                );


                $qty =
                    (float)(
                        $detail['TOTAL_QTY']
                        ?? 0
                    );


                $price =
                    (float)(
                        $detail['TOTAL_JUMLAH']
                        ?? 0
                    );


                $total =
                    (float)(
                        $detail['TOTAL']
                        ?? 0
                    );


                $sheet->setCellValue(
                    "G{$row}",
                    $qty
                );


                $sheet->setCellValue(
                    "H{$row}",
                    $price
                );


                $sheet->setCellValue(
                    "I{$row}",
                    $total
                );


                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                $totalQty +=
                    $qty;


                $totalPrice +=
                    $price;


                $totalCost +=
                    $total;


                $row++;

            }


            /*
            |--------------------------------------------------------------------------
            | MERGE COST INFORMATION
            |--------------------------------------------------------------------------
            */

            $groupEndRow =
                $row - 1;


            if($groupCount > 1){

                foreach([

                    'A',
                    'B',
                    'C',
                    'D',
                    'E'

                ] as $col){

                    $sheet->mergeCells(
                        "{$col}{$groupStartRow}:{$col}{$groupEndRow}"
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | GROUP BORDER
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$groupStartRow}:I{$groupEndRow}"
            )
                ->applyFromArray([

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                            'color' => [

                                'rgb' => 'D1D9E2'

                            ]

                        ]

                    ],

                    'alignment' => [

                        'vertical' =>
                            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                    ]

                ]);


            /*
            |--------------------------------------------------------------------------
            | SEPARATOR COST
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$groupStartRow}:I{$groupStartRow}"
            )
                ->getBorders()
                ->getTop()
                ->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                );

        }


        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "A{$row}:F{$row}"
        );

        $sheet->setCellValue(
            "A{$row}",
            'GRAND TOTAL'
        );


        $sheet->setCellValue(
            "G{$row}",
            $totalQty
        );


        $sheet->setCellValue(
            "H{$row}",
            $totalPrice
        );


        $sheet->setCellValue(
            "I{$row}",
            $totalCost
        );


        $sheet->getStyle(
            "A{$row}:I{$row}"
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' => 'F3F6F9'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "G8:G{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.00'
            );


        $sheet->getStyle(
            "H8:I{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "G8:I{$row}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = [

            'A' => 18,
            'B' => 16,
            'C' => 18,
            'D' => 15,
            'E' => 18,

            'F' => 25,
            'G' => 14,
            'H' => 16,
            'I' => 18

        ];


        foreach($widths as $col => $width){

            $sheet
                ->getColumnDimension($col)
                ->setWidth($width);

        }


        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHT
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getRowDimension(6)
            ->setRowHeight(22);


        $sheet
            ->getRowDimension(7)
            ->setRowHeight(35);


        /*
        |--------------------------------------------------------------------------
        | FREEZE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'A8'
        );


        /*
        |--------------------------------------------------------------------------
        | AUTOFILTER
        |--------------------------------------------------------------------------
        */

        $sheet->setAutoFilter(
            "A7:I{$row}"
        );


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );


        $sheet->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            );


        $sheet->getPageSetup()
            ->setFitToWidth(
                1
            );


        $sheet->getPageSetup()
            ->setFitToHeight(
                0
            );


        $sheet->getPageMargins()
            ->setTop(0.4);

        $sheet->getPageMargins()
            ->setBottom(0.4);

        $sheet->getPageMargins()
            ->setLeft(0.25);

        $sheet->getPageMargins()
            ->setRight(0.25);


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment; filename="REPORT_COST.xlsx"'
        );

        header(
            'Cache-Control: max-age=0'
        );


        $writer =
            new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                $spreadsheet
            );


        $writer->save(
            'php://output'
        );


        exit;
    }

    public function load_sl()
    {
        $page = max(1, (int) $this->input->get('page'));

        $limit = max(1, (int) $this->input->get('limit'));

        $start = ($page - 1) * $limit;

        $filter = [
            'search' => trim($this->input->get('search', true)),
            'plant' => trim($this->input->get('plant', true)),
            'customer' => trim($this->input->get('customer', true)),
            'date_from' => trim($this->input->get('date_from', true)),
            'date_to' => trim($this->input->get('date_to', true))
        ];

        $reportData = $this->ReportAccounting_model
            ->get_sl_report(
                $limit,
                $start,
                $filter
            );

        $rows = [];
        foreach($reportData['rows'] as $row){
            $index = $row['PLANT'] . '|' . $row['CUSTOMER'];
            if(!isset($rows[$index])){
                $rows[$index] = [
                    'PLANT' => $row['PLANT'],
                    'PLANT_NAME' => $row['PLANT_NAME'],
                    'CUSTOMER' => $row['CUSTOMER'],
                    'CUSTOMER_NAME' => $row['CUSTOMER_NAME'],
                    'DETAILS' => []
                ];
            }
            $rows[$index]['DETAILS'][] = [
                'DOC_NO' => $row['DOC_NO'],
                'TYPE' => $row['TYPE'],
                'DATE' => $row['DOC_DATE'],
                'AMOUNT' => $row['AMOUNT'],
                'REMARK' => $row['REMARK'],
                'RELATED' => $row['RELATED']
            ];
        }

        $rows = array_values($rows);
        $total = $reportData['total_count'] ?? 0;

        $summary = $reportData['summary'] ?? [];

        $pages = $total > 0 ? ceil($total / $limit) : 1;

        echo json_encode([
            'status' => true,
            'rows' => $rows,
            'summary' => $summary,
            'page' => (int) $page,
            'pages' => (int) $pages,
            'total' => (int) $total,
            'pagination' => $this->build_pagination(
                $pages,
                $page,
                'ReportSL.loadData'
            )
        ]);
    }

    /* =========================================================
    * LOAD REPORT SAVING
    * =======================================================*/

    public function load_saving()
    {
        $page = max(
            1,
            (int) $this->input->get('page')
        );

        $limit = max(
            1,
            (int) $this->input->get('limit')
        );

        $start = ($page - 1) * $limit;


        $filter = [

            'search' =>
                trim(
                    $this->input->get(
                        'search',
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

            'status' =>
                trim(
                    $this->input->get(
                        'status',
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


        $reportData =
            $this->ReportAccounting_model
                ->get_saving_report(
                    $limit,
                    $start,
                    $filter
                );


        $rows =
            $reportData['rows'] ?? [];


        $total =
            $reportData['total_count'] ?? 0;


        $pages =
            $total > 0
                ? (int) ceil(
                    $total / $limit
                )
                : 1;


        echo json_encode([

            'status' =>
                true,

            'rows' =>
                $rows,

            'total' =>
                (int) $total,

            'page' =>
                (int) $page,

            'pages' =>
                (int) $pages,

            'pagination' =>
                $this->build_saving_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    public function export_excel_saving()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'search' => trim(
                $this->input->get(
                    'search',
                    true
                )
            ),

            'customer' => trim(
                $this->input->get(
                    'customer',
                    true
                )
            ),

            'status' => trim(
                $this->input->get(
                    'status',
                    true
                )
            ),

            'date_from' => trim(
                $this->input->get(
                    'date_from',
                    true
                )
            ),

            'date_to' => trim(
                $this->input->get(
                    'date_to',
                    true
                )
            )

        ];


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        |
        | Gunakan limit sangat besar karena export tidak mengikuti pagination UI.
        |
        */

        $reportData =
            $this->ReportAccounting_model
                ->get_saving_report(
                    999999,
                    0,
                    $filters
                );


        $rows =
            $reportData['rows']
            ?? [];


        /*
        |--------------------------------------------------------------------------
        | NO DATA
        |--------------------------------------------------------------------------
        */

        if(empty($rows)){

            show_error(
                'Tidak ada data Saving untuk diekspor.'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();


        $sheet =
            $spreadsheet->getActiveSheet();


        $sheet->setTitle(
            'Laporan Saving'
        );


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoPath =
            FCPATH . 'assets/img/abc-trans.png';


        if(file_exists($logoPath)){

            $drawing =
                new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();


            $drawing->setPath(
                $logoPath
            );


            $drawing->setCoordinates(
                'A1'
            );


            $drawing->setHeight(
                55
            );


            $drawing->setWorksheet(
                $sheet
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B1:N1'
        );


        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );


        $sheet->getStyle(
            'B1:N1'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(18);


        $sheet->mergeCells(
            'B2:N2'
        );


        $sheet->setCellValue(
            'B2',
            'LAPORAN SAVING'
        );


        $sheet->getStyle(
            'B2:N2'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(13);


        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        $dateFrom =
            !empty($filters['date_from'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_from']
                    )
                )
                : '-';


        $dateTo =
            !empty($filters['date_to'])
                ? date(
                    'd M Y',
                    strtotime(
                        $filters['date_to']
                    )
                )
                : '-';


        $period =
            $dateFrom
            . ' - '
            . $dateTo;


        $sheet->mergeCells(
            'B3:N3'
        );


        $sheet->setCellValue(
            'B3',
            'Periode : ' . $period
        );


        $sheet->getStyle(
            'B3:N3'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | FILTER INFORMATION
        |--------------------------------------------------------------------------
        */

        $filterText =
            'Filter : ';


        /*
        | SEARCH
        */

        if(!empty($filters['search'])){

            $filterText .=
                'Pencarian "' .
                $filters['search'] .
                '"';

        }else{

            $filterText .=
                'Semua Data';

        }


        /*
        | CUSTOMER
        */

        if(!empty($filters['customer'])){

            $filterText .=
                ' | Pelanggan ' .
                $filters['customer'];

        }else{

            $filterText .=
                ' | Semua Pelanggan';

        }


        /*
        | STATUS
        */

        if(
            !empty($filters['status'])
        ){

            $statusLabel =
                $filters['status'] === 'PAID'
                    ? 'Lunas'
                    : 'Belum Lunas';


            $filterText .=
                ' | Status ' .
                $statusLabel;

        }else{

            $filterText .=
                ' | Semua Status';

        }


        $sheet->mergeCells(
            'B4:N4'
        );


        $sheet->setCellValue(
            'B4',
            $filterText
        );


        $sheet->getStyle(
            'B4:N4'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $headerRow = 6;


        $headers = [

            'A' => 'Tanggal',

            'B' => 'No. Saving',

            'C' => 'Terkait',

            'D' => 'Penjualan',

            'E' => 'Nilai',

            'F' => 'Dasar',

            'G' => 'Tarif',

            'H' => 'Kuantitas',

            'I' => 'Berat',

            'J' => 'Dibayar',

            'K' => 'Sisa',

            'L' => 'Status',

            'M' => 'Pembayaran',

            'N' => 'Keterangan'

        ];


        foreach($headers as $column => $title){

            $sheet->setCellValue(
                $column . $headerRow,
                $title
            );

        }


        /*
        |--------------------------------------------------------------------------
        | HEADER STYLE
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "A{$headerRow}:N{$headerRow}"
        )
            ->applyFromArray([

                'font' => [

                    'bold' => true

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    'wrapText' => true

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                        'color' => [

                            'rgb' => 'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $row = 7;


        foreach($rows as $customerGroup){

            $customerCode =
                $customerGroup['CUSTOMER']
                ?? '-';


            $customerName =
                $customerGroup['CUSTOMER_NAME']
                ?? '-';


            $details =
                $customerGroup['DETAILS']
                ?? [];


            if(empty($details)){

                continue;

            }


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER HEADER
            |--------------------------------------------------------------------------
            */

            $customerStartRow =
                $row;


            $sheet->mergeCells(
                "A{$row}:N{$row}"
            );


            $sheet->setCellValue(
                "A{$row}",
                'PELANGGAN : ' .
                $customerCode .
                ' - ' .
                $customerName
            );


            $sheet->getStyle(
                "A{$row}:N{$row}"
            )
                ->applyFromArray([

                    'font' => [

                        'bold' => true

                    ],

                    'alignment' => [

                        'vertical' =>
                            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                    ],

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                            'color' => [

                                'rgb' => 'B8C4D0'

                            ]

                        ]

                    ]

                ]);


            $row++;


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER TOTAL
            |--------------------------------------------------------------------------
            */

            $totalAmount = 0;

            $totalPaid = 0;

            $totalRemain = 0;


            /*
            |--------------------------------------------------------------------------
            | SAVING DETAILS
            |--------------------------------------------------------------------------
            */

            foreach($details as $detail){

                $amount =
                    (float) (
                        $detail['AMOUNT']
                        ?? 0
                    );


                $remain =
                    (float) (
                        $detail['REMAIN']
                        ?? 0
                    );


                $paid =
                    $amount - $remain;


                if($paid < 0){

                    $paid = 0;

                }


                $totalAmount +=
                    $amount;


                $totalPaid +=
                    $paid;


                $totalRemain +=
                    $remain;


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                $status =
                    $remain <= 0
                        ? 'LUNAS'
                        : 'BELUM LUNAS';


                /*
                |--------------------------------------------------------------------------
                | PAYMENT COUNT
                |--------------------------------------------------------------------------
                */

                $payments =
                    $detail['PAYMENTS']
                    ?? [];


                $paymentCount =
                    count($payments);


                $paymentText =
                    $paymentCount > 0
                        ? $paymentCount .
                            ' Pembayaran'
                        : '-';


                /*
                |--------------------------------------------------------------------------
                | MAIN ROW
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "A{$row}",
                    !empty($detail['SV_DATE'])
                        ? date(
                            'd/m/Y',
                            strtotime(
                                $detail['SV_DATE']
                            )
                        )
                        : '-'
                );


                $sheet->setCellValue(
                    "B{$row}",
                    $detail['SV_NO']
                    ?? '-'
                );


                $sheet->setCellValue(
                    "C{$row}",
                    $detail['RELATED']
                    ?? '-'
                );


                $sheet->setCellValue(
                    "D{$row}",
                    $detail['SALES']
                    ?? '-'
                );


                $sheet->setCellValue(
                    "E{$row}",
                    $amount
                );


                $sheet->setCellValue(
                    "F{$row}",
                    $detail['BASIS']
                    ?? '-'
                );


                $sheet->setCellValue(
                    "G{$row}",
                    (float) (
                        $detail['RATE']
                        ?? 0
                    )
                );


                $sheet->setCellValue(
                    "H{$row}",
                    (float) (
                        $detail['TOTAL_QTY']
                        ?? 0
                    )
                );


                $sheet->setCellValue(
                    "I{$row}",
                    (float) (
                        $detail['TOTAL_WEIGHT']
                        ?? 0
                    )
                );


                $sheet->setCellValue(
                    "J{$row}",
                    $paid
                );


                $sheet->setCellValue(
                    "K{$row}",
                    $remain
                );


                $sheet->setCellValue(
                    "L{$row}",
                    $status
                );


                $sheet->setCellValue(
                    "M{$row}",
                    $paymentText
                );


                $sheet->setCellValue(
                    "N{$row}",
                    $detail['REMARK']
                    ?? '-'
                );


                /*
                |--------------------------------------------------------------------------
                | MAIN ROW STYLE
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    "A{$row}:N{$row}"
                )
                    ->applyFromArray([

                        'borders' => [

                            'allBorders' => [

                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                                'color' => [

                                    'rgb' => 'D1D9E2'

                                ]

                            ]

                        ],

                        'alignment' => [

                            'vertical' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                        ]

                    ]);


                /*
                |--------------------------------------------------------------------------
                | PAYMENT HISTORY
                |--------------------------------------------------------------------------
                */

                if(!empty($payments)){

                    $row++;

                    $paymentStartRow =
                        $row;


                    $sheet->mergeCells(
                        "A{$row}:N{$row}"
                    );


                    $sheet->setCellValue(
                        "A{$row}",
                        'RIWAYAT PEMBAYARAN - ' .
                        ($detail['SV_NO'] ?? '-')
                    );


                    $sheet->getStyle(
                        "A{$row}:N{$row}"
                    )
                        ->applyFromArray([

                            'font' => [

                                'bold' => true

                            ],

                            'alignment' => [

                                'vertical' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                            ]

                        ]);


                    $row++;


                    /*
                    | PAYMENT HEADER
                    */

                    $paymentHeaderRow =
                        $row;


                    $paymentHeaders = [

                        'A' => 'Tanggal',

                        'B' => 'No. Cash In',

                        'C' => 'Metode Pembayaran',

                        'D' => 'Nilai Cash In',

                        'E' => 'Pembayaran Saving'

                    ];


                    foreach(
                        $paymentHeaders
                        as $column => $title
                    ){

                        $sheet->setCellValue(
                            $column . $row,
                            $title
                        );

                    }


                    $sheet->mergeCells(
                        "E{$row}:N{$row}"
                    );


                    $sheet->getStyle(
                        "A{$row}:N{$row}"
                    )
                        ->applyFromArray([

                            'font' => [

                                'bold' => true

                            ],

                            'alignment' => [

                                'horizontal' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                                'vertical' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                                'wrapText' => true

                            ],

                            'borders' => [

                                'allBorders' => [

                                    'borderStyle' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                                    'color' => [

                                        'rgb' => 'D1D9E2'

                                    ]

                                ]

                            ]

                        ]);


                    $row++;


                    /*
                    | PAYMENT DATA
                    */

                    $paymentTotal =
                        0;


                    foreach($payments as $payment){

                        $paymentDate =
                            $payment['DATE_OFFSET']
                            ?? $payment['CASHIN_DATE']
                            ?? '';


                        $paymentAmount =
                            (float) (
                                $payment['CASH_IN_AMOUNT']
                                ?? 0
                            );


                        $paymentOffset =
                            (float) (
                                $payment['AMOUNT_OFFSET']
                                ?? 0
                            );


                        $paymentTotal +=
                            $paymentOffset;


                        $sheet->setCellValue(
                            "A{$row}",
                            !empty($paymentDate)
                                ? date(
                                    'd/m/Y',
                                    strtotime(
                                        $paymentDate
                                    )
                                )
                                : '-'
                        );


                        $sheet->setCellValue(
                            "B{$row}",
                            $payment['CASH_IN']
                            ?? '-'
                        );


                        $sheet->setCellValue(
                            "C{$row}",
                            $payment['PEMBAYARAN']
                            ?? '-'
                        );


                        $sheet->setCellValue(
                            "D{$row}",
                            $paymentAmount
                        );


                        $sheet->mergeCells(
                            "E{$row}:N{$row}"
                        );


                        $sheet->setCellValue(
                            "E{$row}",
                            $paymentOffset
                        );


                        $sheet->getStyle(
                            "A{$row}:N{$row}"
                        )
                            ->applyFromArray([

                                'borders' => [

                                    'allBorders' => [

                                        'borderStyle' =>
                                            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                                        'color' => [

                                            'rgb' => 'E0E5EA'

                                        ]

                                    ]

                                ],

                                'alignment' => [

                                    'vertical' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                                ]

                            ]);


                        $row++;

                    }


                    /*
                    | PAYMENT TOTAL
                    */

                    $sheet->mergeCells(
                        "A{$row}:D{$row}"
                    );


                    $sheet->setCellValue(
                        "A{$row}",
                        'TOTAL DIBAYAR'
                    );


                    $sheet->mergeCells(
                        "E{$row}:N{$row}"
                    );


                    $sheet->setCellValue(
                        "E{$row}",
                        $paymentTotal
                    );


                    $sheet->getStyle(
                        "A{$row}:N{$row}"
                    )
                        ->applyFromArray([

                            'font' => [

                                'bold' => true

                            ],

                            'borders' => [

                                'allBorders' => [

                                    'borderStyle' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                                    'color' => [

                                        'rgb' => 'B8C4D0'

                                    ]

                                ]

                            ]

                        ]);


                    $row++;

                }


                /*
                |--------------------------------------------------------------------------
                | SPACING
                |--------------------------------------------------------------------------
                */

                $row++;

            }


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER SUBTOTAL
            |--------------------------------------------------------------------------
            */

            $sheet->mergeCells(
                "A{$row}:D{$row}"
            );


            $sheet->setCellValue(
                "A{$row}",
                'TOTAL PELANGGAN'
            );


            $sheet->setCellValue(
                "E{$row}",
                $totalAmount
            );


            $sheet->setCellValue(
                "J{$row}",
                $totalPaid
            );


            $sheet->setCellValue(
                "K{$row}",
                $totalRemain
            );


            $sheet->setCellValue(
                "L{$row}",
                $totalRemain <= 0
                    ? 'LUNAS'
                    : 'BELUM LUNAS'
            );


            $sheet->mergeCells(
                "M{$row}:N{$row}"
            );


            $sheet->getStyle(
                "A{$row}:N{$row}"
            )
                ->applyFromArray([

                    'font' => [

                        'bold' => true

                    ],

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,

                            'color' => [

                                'rgb' => '8FA1B3'

                            ]

                        ]

                    ],

                    'alignment' => [

                        'vertical' =>
                            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                    ]

                ]);


            $row += 2;

        }


        /*
        |--------------------------------------------------------------------------
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $lastRow =
            $row - 1;


        $sheet->getStyle(
            "E7:E{$lastRow}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        $sheet->getStyle(
            "G7:K{$lastRow}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.##'
            );


        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT NUMERIC
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "E7:K{$lastRow}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = [

            'A' => 14,
            'B' => 18,
            'C' => 22,
            'D' => 20,
            'E' => 18,
            'F' => 15,
            'G' => 14,
            'H' => 15,
            'I' => 15,
            'J' => 18,
            'K' => 18,
            'L' => 18,
            'M' => 20,
            'N' => 30

        ];


        foreach(
            $widths
            as $column => $width
        ){

            $sheet
                ->getColumnDimension($column)
                ->setWidth($width);

        }


        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHT
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getRowDimension(6)
            ->setRowHeight(35);


        /*
        |--------------------------------------------------------------------------
        | FREEZE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'A7'
        );


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        $sheet->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );


        $sheet->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3
            );


        $sheet->getPageSetup()
            ->setFitToWidth(
                1
            );


        $sheet->getPageSetup()
            ->setFitToHeight(
                0
            );


        $sheet->getPageMargins()
            ->setTop(0.4);


        $sheet->getPageMargins()
            ->setBottom(0.4);


        $sheet->getPageMargins()
            ->setLeft(0.25);


        $sheet->getPageMargins()
            ->setRight(0.25);


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );


        header(
            'Content-Disposition: attachment; filename="REPORT_SAVING.xlsx"'
        );


        header(
            'Cache-Control: max-age=0'
        );


        $writer =
            new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                $spreadsheet
            );


        $writer->save(
            'php://output'
        );


        exit;
    }

    private function build_saving_pagination(
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
        | PREVIOUS
        |--------------------------------------------------------------------------
        */

        $prev =
            max(
                1,
                $current - 1
            );


        $disabled =
            $current <= 1
                ? 'disabled'
                : '';


        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-saving-pagination-page="'.$prev.'">

                    Previous

                </a>

            </li>

        ';


        /*
        |--------------------------------------------------------------------------
        | PAGE RANGE
        |--------------------------------------------------------------------------
        */

        $range = 2;


        $startPage =
            max(
                1,
                $current - $range
            );


        $endPage =
            min(
                $pages,
                $current + $range
            );


        /*
        |--------------------------------------------------------------------------
        | FIRST PAGE
        |--------------------------------------------------------------------------
        */

        if($startPage > 1){

            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-saving-pagination-page="1">

                        1

                    </a>

                </li>

            ';


            if($startPage > 2){

                $html .= '

                    <li class="page-item disabled">

                        <span class="page-link">
                            ...
                        </span>

                    </li>

                ';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE NUMBER
        |--------------------------------------------------------------------------
        */

        for(
            $i = $startPage;
            $i <= $endPage;
            $i++
        ){

            $active =
                $i == $current
                    ? 'active'
                    : '';


            $html .= '

                <li class="page-item '.$active.'">

                    <a
                        href="#"
                        class="page-link"
                        data-saving-pagination-page="'.$i.'">

                        '.$i.'

                    </a>

                </li>

            ';

        }


        /*
        |--------------------------------------------------------------------------
        | LAST PAGE
        |--------------------------------------------------------------------------
        */

        if($endPage < $pages){

            if($endPage < $pages - 1){

                $html .= '

                    <li class="page-item disabled">

                        <span class="page-link">
                            ...
                        </span>

                    </li>

                ';

            }


            $html .= '

                <li class="page-item">

                    <a
                        href="#"
                        class="page-link"
                        data-saving-pagination-page="'.$pages.'">

                        '.$pages.'

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
            $current >= $pages
                ? 'disabled'
                : '';


        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-saving-pagination-page="'.$next.'">

                    Next

                </a>

            </li>

        ';


        $html .=
            '</ul>';


        return $html;

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

    /* =========================================================
    * LOAD REPORT AR
    * =======================================================*/

    public function load_ar()
    {
        $page = max(
            1,
            (int) $this->input->get('page')
        );

        $limit = max(
            1,
            (int) $this->input->get('limit')
        );

        $start =
            ($page - 1) * $limit;


        /* =====================================================
        | FILTER AR
        |
        | TIDAK ADA:
        | - PLANT
        | - DATE FROM
        | - DATE TO
        ===================================================== */

        $filter = [

            'search' =>
                trim(
                    $this->input->get(
                        'search',
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

            'status' =>
                trim(
                    $this->input->get(
                        'status',
                        true
                    )
                )

        ];


        /* =====================================================
        | GET REPORT
        ===================================================== */

        $reportData =
            $this->ReportAccounting_model
                ->get_ar_report(
                    $limit,
                    $start,
                    $filter
                );


        $rows =
            $reportData['rows']
            ?? [];


        $total =
            $reportData['total_count']
            ?? 0;


        /* =====================================================
        | PAGINATION
        ===================================================== */

        $pages =
            $total > 0
                ? (int) ceil(
                    $total / $limit
                )
                : 1;


        /* =====================================================
        | JSON
        | ===================================================== */

        echo json_encode([

            'status' =>
                true,

            'rows' =>
                $rows,

            'total' =>
                (int) $total,

            'page' =>
                (int) $page,

            'pages' =>
                (int) $pages,

            'pagination' =>
                $this->build_ar_pagination(
                    $pages,
                    $page
                )

        ]);
    }

    /* =========================================================
    * EXPORT REPORT AR - EXCEL
    * =======================================================*/

    public function export_ar_excel()
    {
        /* =====================================================
        | FILTER
        ===================================================== */

        $filter = [

            'search' =>
                trim(
                    $this->input->get(
                        'search',
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

            'status' =>
                trim(
                    $this->input->get(
                        'status',
                        true
                    )
                )

        ];


        /* =====================================================
        | GET ALL AR DATA
        |
        | Tidak menggunakan pagination.
        |
        | UI menggunakan 20 customer per page,
        | sedangkan Excel harus mengambil seluruh data.
        ===================================================== */

        $reportData =
            $this->ReportAccounting_model
                ->get_ar_report(
                    999999,
                    0,
                    $filter
                );


        $groups =
            $reportData['rows']
            ?? [];


        /* =====================================================
        | NO DATA
        ===================================================== */

        if (
            empty($groups)
        ) {

            show_error(
                'Tidak ada data AR untuk diekspor'
            );

            return;

        }


        /* =====================================================
        | SPREADSHEET
        ===================================================== */

        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();


        $sheet =
            $spreadsheet->getActiveSheet();


        $sheet->setTitle(
            'AR Report'
        );


        /* =====================================================
        | LOGO
        ===================================================== */

        $logoPath =
            FCPATH .
            'assets/img/abc-trans.png';


        if (
            file_exists(
                $logoPath
            )
        ) {

            $drawing =
                new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();


            $drawing->setPath(
                $logoPath
            );


            $drawing->setCoordinates(
                'A1'
            );


            $drawing->setHeight(
                55
            );


            $drawing->setWorksheet(
                $sheet
            );

        }


        /* =====================================================
        | TITLE
        ===================================================== */

        $sheet->mergeCells(
            'B1:K1'
        );


        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );


        $sheet->getStyle(
            'B1:K1'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(18);


        $sheet->mergeCells(
            'B2:K2'
        );


        $sheet->setCellValue(
            'B2',
            'ACCOUNT RECEIVABLE REPORT'
        );


        $sheet->getStyle(
            'B2:K2'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(13);


        /* =====================================================
        | FILTER INFORMATION
        ===================================================== */

        $sheet->mergeCells(
            'B3:K3'
        );


        $filterText =
            'Filter : ';


        /* CUSTOMER */

        if (
            !empty(
                $filter['customer']
            )
        ) {

            $filterText .=
                'Customer ' .
                $filter['customer'];

        }
        else {

            $filterText .=
                'All Customer';

        }


        /* STATUS */

        if (
            !empty(
                $filter['status']
            )
        ) {

            $statusLabel =
                $filter['status'] === 'PARTIAL'
                    ? 'SEBAGIAN'
                    : (
                        $filter['status'] === 'UNPAID'
                            ? 'BELUM LUNAS'
                            : $filter['status']
                    );


            $filterText .=
                ' | Status ' .
                $statusLabel;

        }
        else {

            $filterText .=
                ' | All Status';

        }


        /* SEARCH */

        if (
            !empty(
                $filter['search']
            )
        ) {

            $filterText .=
                ' | Search ' .
                $filter['search'];

        }


        $sheet->setCellValue(
            'B3',
            $filterText
        );


        $sheet->getStyle(
            'B3:K3'
        )
            ->getFont()
            ->setItalic(true);


        /* =====================================================
        | INFORMATION
        ===================================================== */

        $sheet->mergeCells(
            'B4:K4'
        );


        $sheet->setCellValue(
            'B4',
            'Report berdasarkan Sales Tempo yang masih memiliki outstanding AR'
        );


        $sheet->getStyle(
            'B4:K4'
        )
            ->getFont()
            ->setItalic(true);


        /* =====================================================
        | COLUMN HEADER
        ===================================================== */

        $headers = [

            'Tanggal',
            'No. Sales',
            'Plant',
            'Customer',
            'Nilai',
            'Saving',
            'Dibayar',
            'Sisa',
            'Status',
            'Pembayaran',
            'Keterangan'

        ];


        $column =
            1;


        foreach (
            $headers
            as $header
        ) {

            $cell =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate
                    ::stringFromColumnIndex(
                        $column
                    )
                . '6';


            $sheet->setCellValue(
                $cell,
                $header
            );


            $column++;

        }


        /* =====================================================
        | HEADER STYLE
        ===================================================== */

        $sheet->getStyle(
            'A6:K6'
        )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'size' =>
                        10

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment
                            ::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment
                            ::VERTICAL_CENTER,

                    'wrapText' =>
                        true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill
                            ::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            'EEF2F7'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border
                                ::BORDER_THIN,

                        'color' => [

                            'rgb' =>
                                'C7D0D9'

                        ]

                    ]

                ]

            ]);


        /* =====================================================
        | DATA
        ===================================================== */

        $row =
            7;


        $grandNilai =
            0;


        $grandSaving =
            0;


        $grandDibayar =
            0;


        $grandSisa =
            0;


        /* =====================================================
        | CUSTOMER LOOP
        ===================================================== */

        foreach (
            $groups
            as $group
        ) {

            $details =
                $group['DETAILS']
                ?? [];


            if (
                empty($details)
            ) {

                continue;

            }


            /* =================================================
            | CUSTOMER HEADER
            ================================================= */

            $sheet->mergeCells(
                "A{$row}:K{$row}"
            );


            $sheet->setCellValue(
                "A{$row}",

                'PELANGGAN : ' .

                ($group['CUSTOMER'] ?? '-') .

                ' - ' .

                ($group['CUSTOMER_NAME'] ?? '-')

            );


            $sheet->getStyle(
                "A{$row}:K{$row}"
            )
                ->applyFromArray([

                    'font' => [

                        'bold' =>
                            true

                    ],

                    'fill' => [

                        'fillType' =>
                            \PhpOffice\PhpSpreadsheet\Style\Fill
                                ::FILL_SOLID,

                        'startColor' => [

                            'rgb' =>
                                'F8FAFC'

                        ]

                    ],

                    'borders' => [

                        'top' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border
                                    ::BORDER_MEDIUM,

                            'color' => [

                                'rgb' =>
                                    'C7CDD4'

                            ]

                        ]

                    ]

                ]);


            $row++;


            /* =================================================
            | CUSTOMER TOTAL
            ================================================= */

            $customerNilai =
                0;


            $customerSaving =
                0;


            $customerDibayar =
                0;


            $customerSisa =
                0;


            /* =================================================
            | SALES DETAIL
            ================================================= */

            foreach (
                $details
                as $detail
            ) {

                $sales =
                    $detail['SALES']
                    ?? '-';


                $plant =
                    $detail['PLANT']
                    ?? '-';


                $customer =
                    trim(

                        ($detail['CUSTOMER'] ?? '')
                        .
                        ' - '
                        .
                        ($detail['CUSTOMER_NAME'] ?? '')

                    );


                $salesDate =
                    !empty(
                        $detail['SALES_DATE']
                    )

                    ? date(
                        'd M Y',
                        strtotime(
                            $detail['SALES_DATE']
                        )
                    )

                    : '-';


                $nilai =
                    (float)(
                        $detail['SALES_AMOUNT']
                        ?? 0
                    );


                $saving =
                    (float)(
                        $detail['SAVING_AMOUNT']
                        ?? 0
                    );


                $dibayar =
                    (float)(
                        $detail['TOTAL_PAID']
                        ?? 0
                    );


                $sisa =
                    (float)(
                        $detail['GRAND_OUTSTANDING']
                        ?? 0
                    );


                $status =
                    $detail['AR_STATUS']
                    ?? 'UNPAID';


                if (
                    $status === 'PARTIAL'
                ) {

                    $status =
                        'SEBAGIAN';

                }
                else {

                    $status =
                        'BELUM LUNAS';

                }


                $remark =
                    $detail['SALES_REMARK']
                    ?? '-';


                /* =============================================
                | PEMBAYARAN
                ============================================= */

                $payments =
                    $detail['PAYMENTS']
                    ?? [];


                $paymentSummary =
                    '';


                if (
                    !empty($payments)
                ) {

                    $paymentLabels =
                        [];


                    foreach (
                        $payments
                        as $payment
                    ) {

                        $paymentDate =
                            !empty(
                                $payment['DATE_OFFSET']
                                ??
                                $payment['CASHIN_DATE']
                            )

                            ? date(
                                'd M Y',
                                strtotime(

                                    $payment['DATE_OFFSET']
                                    ??
                                    $payment['CASHIN_DATE']

                                )
                            )

                            : '-';


                        $cashIn =
                            $payment['CASH_IN']
                            ?? '-';


                        $offset =
                            (float)(
                                $payment['AMOUNT_OFFSET']
                                ?? 0
                            );


                        $allocation =
                            strtoupper(

                                $payment['ALLOCATION_TYPE']
                                ??
                                'SALES'

                            );


                        $paymentLabels[] =

                            $paymentDate
                            .
                            ' | '
                            .
                            $cashIn
                            .
                            ' | '
                            .
                            $allocation
                            .
                            ' | Rp '
                            .
                            number_format(
                                $offset,
                                0,
                                ',',
                                '.'
                            );

                    }


                    $paymentSummary =
                        implode(
                            "\n",
                            $paymentLabels
                        );

                }
                else {

                    $paymentSummary =
                        '-';

                }


                /* =============================================
                | WRITE ROW
                ============================================= */

                $sheet->setCellValue(
                    "A{$row}",
                    $salesDate
                );


                $sheet->setCellValue(
                    "B{$row}",
                    $sales
                );


                $sheet->setCellValue(
                    "C{$row}",
                    $plant
                );


                $sheet->setCellValue(
                    "D{$row}",
                    $customer
                );


                $sheet->setCellValue(
                    "E{$row}",
                    $nilai
                );


                $sheet->setCellValue(
                    "F{$row}",
                    $saving
                );


                $sheet->setCellValue(
                    "G{$row}",
                    $dibayar
                );


                $sheet->setCellValue(
                    "H{$row}",
                    $sisa
                );


                $sheet->setCellValue(
                    "I{$row}",
                    $status
                );


                $sheet->setCellValue(
                    "J{$row}",
                    $paymentSummary
                );


                $sheet->setCellValue(
                    "K{$row}",
                    $remark
                );


                /* =============================================
                | TOTAL CUSTOMER
                ============================================= */

                $customerNilai +=
                    $nilai;


                $customerSaving +=
                    $saving;


                $customerDibayar +=
                    $dibayar;


                $customerSisa +=
                    $sisa;


                /* =============================================
                | GRAND TOTAL
                ============================================= */

                $grandNilai +=
                    $nilai;


                $grandSaving +=
                    $saving;


                $grandDibayar +=
                    $dibayar;


                $grandSisa +=
                    $sisa;


                /* =============================================
                | BORDER
                ============================================= */

                $sheet->getStyle(
                    "A{$row}:K{$row}"
                )
                    ->applyFromArray([

                        'borders' => [

                            'allBorders' => [

                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border
                                        ::BORDER_THIN,

                                'color' => [

                                    'rgb' =>
                                        'D1D9E2'

                                ]

                            ]

                        ],

                        'alignment' => [

                            'vertical' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment
                                    ::VERTICAL_CENTER

                        ]

                    ]);


                $row++;

            }


            /* =================================================
            | CUSTOMER TOTAL
            ================================================= */

            $sheet->mergeCells(
                "A{$row}:D{$row}"
            );


            $sheet->setCellValue(
                "A{$row}",
                'TOTAL PELANGGAN'
            );


            $sheet->setCellValue(
                "E{$row}",
                $customerNilai
            );


            $sheet->setCellValue(
                "F{$row}",
                $customerSaving
            );


            $sheet->setCellValue(
                "G{$row}",
                $customerDibayar
            );


            $sheet->setCellValue(
                "H{$row}",
                $customerSisa
            );


            $sheet->setCellValue(
                "I{$row}",
                ''
            );


            $sheet->setCellValue(
                "J{$row}",
                ''
            );


            $sheet->setCellValue(
                "K{$row}",
                ''
            );


            $sheet->getStyle(
                "A{$row}:K{$row}"
            )
                ->applyFromArray([

                    'font' => [

                        'bold' =>
                            true

                    ],

                    'fill' => [

                        'fillType' =>
                            \PhpOffice\PhpSpreadsheet\Style\Fill
                                ::FILL_SOLID,

                        'startColor' => [

                            'rgb' =>
                                'F3F6F9'

                        ]

                    ],

                    'borders' => [

                        'allBorders' => [

                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border
                                    ::BORDER_THIN,

                            'color' => [

                                'rgb' =>
                                    'B8C4D0'

                            ]

                        ]

                    ]

                ]);


            $row += 2;

        }


        /* =====================================================
        | GRAND TOTAL
        ===================================================== */

        $sheet->mergeCells(
            "A{$row}:D{$row}"
        );


        $sheet->setCellValue(
            "A{$row}",
            'GRAND TOTAL'
        );


        $sheet->setCellValue(
            "E{$row}",
            $grandNilai
        );


        $sheet->setCellValue(
            "F{$row}",
            $grandSaving
        );


        $sheet->setCellValue(
            "G{$row}",
            $grandDibayar
        );


        $sheet->setCellValue(
            "H{$row}",
            $grandSisa
        );


        $sheet->getStyle(
            "A{$row}:K{$row}"
        )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill
                            ::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            'DCE6F1'

                    ]

                ],

                'borders' => [

                    'allBorders' => [

                        'borderStyle' =>
                            \PhpOffice\PhpSpreadsheet\Style\Border
                                ::BORDER_THIN,

                        'color' => [

                            'rgb' =>
                                'B8C4D0'

                        ]

                    ]

                ]

            ]);


        /* =====================================================
        | NUMBER FORMAT
        ===================================================== */

        if (
            $row >= 7
        ) {

            $sheet->getStyle(
                "E7:H{$row}"
            )
                ->getNumberFormat()
                ->setFormatCode(
                    '#,##0'
                );


            $sheet->getStyle(
                "E7:H{$row}"
            )
                ->getAlignment()
                ->setHorizontal(
                    \PhpOffice\PhpSpreadsheet\Style\Alignment
                        ::HORIZONTAL_RIGHT
                );

        }


        /* =====================================================
        | WRAP PAYMENT
        ===================================================== */

        $sheet->getStyle(
            "J7:J{$row}"
        )
            ->getAlignment()
            ->setWrapText(
                true
            );


        $sheet->getStyle(
            "K7:K{$row}"
        )
            ->getAlignment()
            ->setWrapText(
                true
            );


        /* =====================================================
        | COLUMN WIDTH
        ===================================================== */

        $widths = [

            'A' => 16,
            'B' => 23,
            'C' => 12,
            'D' => 30,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 16,
            'J' => 45,
            'K' => 35

        ];


        foreach (
            $widths
            as $columnName => $width
        ) {

            $sheet
                ->getColumnDimension(
                    $columnName
                )
                ->setWidth(
                    $width
                );

        }


        /* =====================================================
        | ROW HEIGHT
        ===================================================== */

        $sheet
            ->getRowDimension(6)
            ->setRowHeight(
                35
            );


        /* =====================================================
        | FREEZE
        ===================================================== */

        $sheet->freezePane(
            'A7'
        );


        /* =====================================================
        | AUTOFILTER
        ===================================================== */

        $sheet->setAutoFilter(
            "A6:K{$row}"
        );


        /* =====================================================
        | PRINT
        ===================================================== */

        $sheet->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup
                    ::ORIENTATION_LANDSCAPE
            );


        $sheet->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup
                    ::PAPERSIZE_A3
            );


        $sheet->getPageSetup()
            ->setFitToWidth(
                1
            );


        $sheet->getPageSetup()
            ->setFitToHeight(
                0
            );


        $sheet->getPageMargins()
            ->setTop(
                0.4
            );


        $sheet->getPageMargins()
            ->setBottom(
                0.4
            );


        $sheet->getPageMargins()
            ->setLeft(
                0.25
            );


        $sheet->getPageMargins()
            ->setRight(
                0.25
            );


        /* =====================================================
        | OUTPUT
        ===================================================== */

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );


        header(
            'Content-Disposition: attachment; filename="REPORT_AR.xlsx"'
        );


        header(
            'Cache-Control: max-age=0'
        );


        $writer =
            new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                $spreadsheet
            );


        $writer->save(
            'php://output'
        );


        exit;
    }

    private function build_ar_pagination(
        $pages,
        $current
    )
    {
        if ($pages <= 1) {

            return '';

        }


        $html =
            '<ul class="pagination pagination-sm mb-0">';


        /* =====================================================
        | PREVIOUS
        ===================================================== */

        $prev =
            max(
                1,
                $current - 1
            );

        $disabled =
            $current <= 1
                ? 'disabled'
                : '';


        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-ar-pagination-page="'.$prev.'">

                    Previous

                </a>

            </li>

        ';


        /* =====================================================
        | PAGE RANGE
        ===================================================== */

        $range = 2;

        $startPage =
            max(
                1,
                $current - $range
            );

        $endPage =
            min(
                $pages,
                $current + $range
            );


        for (
            $i = $startPage;
            $i <= $endPage;
            $i++
        ) {

            $active =
                $i == $current
                    ? 'active'
                    : '';


            $html .= '

                <li class="page-item '.$active.'">

                    <a
                        href="#"
                        class="page-link"
                        data-ar-pagination-page="'.$i.'">

                        '.$i.'

                    </a>

                </li>

            ';

        }


        /* =====================================================
        | NEXT
        ===================================================== */

        $next =
            min(
                $pages,
                $current + 1
            );

        $disabled =
            $current >= $pages
                ? 'disabled'
                : '';


        $html .= '

            <li class="page-item '.$disabled.'">

                <a
                    href="#"
                    class="page-link"
                    data-ar-pagination-page="'.$next.'">

                    Next

                </a>

            </li>

        ';


        $html .=
            '</ul>';


        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    private function build_pagination($pages, $current)
    {
        if ($pages <= 1) {
            return '';
        }


        $html = '';

        $html .= '<ul class="pagination pagination-sm mb-0">';


        /*
        |--------------------------------------------------------------------------
        | PREVIOUS
        |--------------------------------------------------------------------------
        */

        if ($current > 1) {

            $html .= '
                <li class="page-item">
                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' . ($current - 1) . ')"
                    >
                        &laquo;
                    </a>
                </li>
            ';

        } else {

            $html .= '
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            ';

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE RANGE
        |--------------------------------------------------------------------------
        */

        $range = 2;


        $start = max(
            1,
            $current - $range
        );


        $end = min(
            $pages,
            $current + $range
        );


        /*
        |--------------------------------------------------------------------------
        | FIRST PAGE
        |--------------------------------------------------------------------------
        */

        if ($start > 1) {

            $html .= '
                <li class="page-item">
                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(1)"
                    >
                        1
                    </a>
                </li>
            ';


            if ($start > 2) {

                $html .= '
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                ';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE NUMBERS
        |--------------------------------------------------------------------------
        */

        for ($i = $start; $i <= $end; $i++) {

            $active = (
                $i == $current
            )
                ? ' active'
                : '';


            $html .= '
                <li class="page-item' . $active . '">
                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' . $i . ')"
                    >
                        ' . $i . '
                    </a>
                </li>
            ';

        }


        /*
        |--------------------------------------------------------------------------
        | LAST PAGE
        |--------------------------------------------------------------------------
        */

        if ($end < $pages) {

            if ($end < ($pages - 1)) {

                $html .= '
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                ';

            }


            $html .= '
                <li class="page-item">
                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' . $pages . ')"
                    >
                        ' . $pages . '
                    </a>
                </li>
            ';

        }


        /*
        |--------------------------------------------------------------------------
        | NEXT
        |--------------------------------------------------------------------------
        */

        if ($current < $pages) {

            $html .= '
                <li class="page-item">
                    <a
                        href="javascript:void(0)"
                        class="page-link"
                        onclick="loadPage(' . ($current + 1) . ')"
                    >
                        &raquo;
                    </a>
                </li>
            ';

        } else {

            $html .= '
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            ';

        }


        $html .= '</ul>';


        return $html;
    }
}