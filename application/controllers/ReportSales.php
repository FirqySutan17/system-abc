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
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary =
            $this->ReportSales_model
                ->summary_sales_report(
                    $filter
                );


        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new Spreadsheet();


        $sheet =
            $spreadsheet
                ->getActiveSheet();


        $sheet->setTitle(
            'Report Sales'
        );


        /*
        |--------------------------------------------------------------------------
        | COLORS
        |--------------------------------------------------------------------------
        */

        $blue =
            '2563EB';

        $darkBlue =
            '1E3A8A';

        $lightBlue =
            'EFF6FF';

        $borderColor =
            'D9E2EC';

        $lightGray =
            'F8FAFC';

        $gray =
            '64748B';

        $green =
            '16A34A';

        $orange =
            'F59E0B';


        /*
        |--------------------------------------------------------------------------
        | DEFAULT FONT
        |--------------------------------------------------------------------------
        */

        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A1:O1'
        );


        $sheet->setCellValue(
            'A1',
            'REPORT SALES'
        );


        $sheet
            ->getStyle('A1:O1')
            ->applyFromArray([

                'font' => [

                    'name' =>
                        'Arial',

                    'size' =>
                        18,

                    'bold' =>
                        true,

                    'color' => [

                        'rgb' =>
                            'FFFFFF'

                    ]

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            $blue

                    ]

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER

                ]

            ]);


        $sheet
            ->getRowDimension(1)
            ->setRowHeight(32);


        /*
        |--------------------------------------------------------------------------
        | FILTER INFORMATION
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A3',
            'FILTER INFORMATION'
        );


        $sheet->mergeCells(
            'A3:O3'
        );


        $sheet
            ->getStyle('A3:O3')
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'color' => [

                        'rgb' =>
                            $darkBlue

                    ]

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            $lightBlue

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | FILTER LABELS
        |--------------------------------------------------------------------------
        */

        $plantName = '-';

        $customerName = '-';


        if(!empty($rows)){

            if(
                !empty($rows[0]['PLANT_NAME'])
            ){

                $plantName =
                    $rows[0]['PLANT_NAME'];

            }


            if(
                !empty($rows[0]['CUSTOMER_NAME'])
            ){

                $customerName =
                    $rows[0]['CUSTOMER_NAME'];

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        $dateFrom = !empty($filter['date_from'])
            ? date('d/m/Y', strtotime($filter['date_from']))
            : '-';

        $dateTo = !empty($filter['date_to'])
            ? date('d/m/Y', strtotime($filter['date_to']))
            : '-';


        /*
        |--------------------------------------------------------------------------
        | FILTER VALUE
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A4',
            $dateFrom . ' - ' . $dateTo
        );

        $sheet->setCellValue(
            'B4',
            $dateFrom . ' - ' . $dateTo
        );


        $sheet->setCellValue(
            'D4',
            'Plant'
        );

        $sheet->setCellValue(
            'E4',
            !empty($filter['plant'])
                ? $plantName
                : 'Semua Plant'
        );


        $sheet->setCellValue(
            'G4',
            'Customer'
        );

        $sheet->setCellValue(
            'H4',
            !empty($filter['customer'])
                ? $customerName
                : 'Semua Customer'
        );


        $sheet->setCellValue(
            'J4',
            'Payment'
        );

        $sheet->setCellValue(
            'K4',
            !empty($filter['pembayaran'])
                ? $filter['pembayaran']
                : 'Semua Payment'
        );


        $sheet->setCellValue(
            'M4',
            'Status'
        );

        $sheet->setCellValue(
            'N4',
            !empty($filter['status'])
                ? $filter['status']
                : 'Semua Status'
        );


        $sheet->setCellValue(
            'A5',
            'Search'
        );

        $sheet->setCellValue(
            'B5',
            !empty($filter['search'])
                ? $filter['search']
                : '-'
        );


        $sheet->setCellValue(
            'D5',
            'Generated'
        );

        $sheet->setCellValue(
            'E5',
            date('d/m/Y H:i:s')
        );


        /*
        |--------------------------------------------------------------------------
        | FILTER STYLE
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle('A4:N5')
            ->getFont()
            ->setSize(9);


        $sheet
            ->getStyle('A4:N5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB(
                $borderColor
            );


        $sheet
            ->getStyle('A4:A5')
            ->getFont()
            ->setBold(true);


        $sheet
            ->getStyle('D4:D5')
            ->getFont()
            ->setBold(true);


        $sheet
            ->getStyle('G4:G4')
            ->getFont()
            ->setBold(true);


        $sheet
            ->getStyle('J4:J4')
            ->getFont()
            ->setBold(true);


        $sheet
            ->getStyle('M4:M4')
            ->getFont()
            ->setBold(true);


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A7:O7'
        );


        $sheet->setCellValue(
            'A7',
            'SUMMARY'
        );


        $sheet
            ->getStyle('A7:O7')
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'color' => [

                        'rgb' =>
                            $darkBlue

                    ]

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            $lightBlue

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | SUMMARY VALUES
        |--------------------------------------------------------------------------
        */

        $sheet->setCellValue(
            'A8',
            'TOTAL SALES'
        );

        $sheet->setCellValue(
            'B8',
            (float)(
                $summary['TOTAL_SALES']
                ?? 0
            )
        );


        $sheet->setCellValue(
            'D8',
            'TOTAL SALES DOC'
        );

        $sheet->setCellValue(
            'E8',
            (int)(
                $summary['TOTAL_DOC']
                ?? 0
            )
        );


        $sheet->setCellValue(
            'G8',
            'TOTAL CUSTOMER'
        );

        $sheet->setCellValue(
            'H8',
            (int)(
                $summary['TOTAL_CUSTOMER']
                ?? 0
            )
        );


        $sheet->setCellValue(
            'J8',
            'TOTAL ITEM'
        );

        $sheet->setCellValue(
            'K8',
            (int)(
                $summary['TOTAL_ITEM']
                ?? 0
            )
        );


        /*
        |--------------------------------------------------------------------------
        | SUMMARY STYLE
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle('A8:K8')
            ->getFont()
            ->setBold(true);


        $sheet
            ->getStyle('A8:K8')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB(
                $borderColor
            );


        $sheet
            ->getStyle('B8')
            ->getNumberFormat()
            ->setFormatCode(
                '"Rp" #,##0'
            );


        /*
        |--------------------------------------------------------------------------
        | TABLE HEADER
        |--------------------------------------------------------------------------
        */

        $headerRow = 10;


        $headers = [

            'Sales',

            'Tanggal',

            'Plant',

            'Customer',

            'Source',

            'Payment',

            'Status',

            'Material',

            'Basis',

            'Ekor',

            'Berat',

            'Avg BW',

            'Harga',

            'Discount',

            'Total'

        ];


        foreach(
            $headers as $index => $header
        ){

            $column =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $index + 1
                );


            $sheet->setCellValue(
                $column . $headerRow,
                $header
            );

        }


        $sheet
            ->getStyle(
                'A' .
                $headerRow .
                ':O' .
                $headerRow
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'color' => [

                        'rgb' =>
                            'FFFFFF'

                    ]

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            $darkBlue

                    ]

                ],

                'alignment' => [

                    'horizontal' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

                    'wrapText' =>
                        true

                ]

            ]);


        $sheet
            ->getRowDimension(
                $headerRow
            )
            ->setRowHeight(25);


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $currentRow =
            $headerRow + 1;


        $grandQty =
            0;

        $grandWeight =
            0;

        $grandDiscount =
            0;

        $grandDetailTotal =
            0;


        foreach(
            $rows as $row
        ){

            $details =
                is_array(
                    $row['DETAILS']
                    ?? null
                )
                    ? $row['DETAILS']
                    : [];


            $savings =
                is_array(
                    $row['SAVINGS']
                    ?? null
                )
                    ? $row['SAVINGS']
                    : [];


            /*
            |--------------------------------------------------------------------------
            | SOURCE
            |--------------------------------------------------------------------------
            */

            $receiveNo =
                $row['RECEIVE']
                ?? '';


            if(empty($receiveNo)){

                if(
                    preg_match(
                        '/AUTO FROM RECEIVE\s+([A-Z0-9]+)/i',
                        $row['REMARK'] ?? '',
                        $match
                    )
                ){

                    $receiveNo =
                        $match[1];

                }

            }


            $source =
                !empty($receiveNo)
                    ? 'FROM RECEIVE - ' . $receiveNo
                    : 'MANUAL';


            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */

            if(empty($details)){

                $details = [

                    [

                        'MATERIAL_NAME' => '-',

                        'CALC_BASIS' =>
                            '-',

                        'JUMLAH' =>
                            0,

                        'BERAT' =>
                            0,

                        'AVG_BW' =>
                            0,

                        'HARGA' =>
                            0,

                        'DISCOUNT' =>
                            0,

                        'TOTAL' =>
                            0

                    ]

                ];

            }


            $salesStartRow =
                $currentRow;


            $subtotalQty =
                0;

            $subtotalWeight =
                0;

            $subtotalDiscount =
                0;

            $subtotalTotal =
                0;


            /*
            |--------------------------------------------------------------------------
            | SALES DETAIL
            |--------------------------------------------------------------------------
            */

            foreach(
                $details as $detail
            ){

                $qty =
                    (float)(
                        $detail['JUMLAH']
                        ?? 0
                    );


                $weight =
                    (float)(
                        $detail['BERAT']
                        ?? 0
                    );


                $avgBw =
                    (float)(
                        $detail['AVG_BW']
                        ?? 0
                    );


                $price =
                    (float)(
                        $detail['HARGA']
                        ?? 0
                    );


                $discount =
                    (float)(
                        $detail['DISCOUNT']
                        ?? 0
                    );


                $total =
                    (float)(
                        $detail['TOTAL']
                        ?? 0
                    );


                $subtotalQty +=
                    $qty;


                $subtotalWeight +=
                    $weight;


                $subtotalDiscount +=
                    $discount;


                $subtotalTotal +=
                    $total;


                $grandQty +=
                    $qty;


                $grandWeight +=
                    $weight;


                $grandDiscount +=
                    $discount;


                $grandDetailTotal +=
                    $total;


                /*
                |--------------------------------------------------------------------------
                | FIRST DETAIL - SALES INFORMATION
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    'A' . $currentRow,
                    $row['SALES'] ?? '-'
                );


                $sheet->setCellValue(
                    'B' . $currentRow,
                    !empty($row['SALES_DATE'])
                        ? date(
                            'd/m/Y',
                            strtotime(
                                $row['SALES_DATE']
                            )
                        )
                        : '-'
                );


                $sheet->setCellValue(
                    'C' . $currentRow,
                    $row['PLANT_NAME'] ?? '-'
                );


                $sheet->setCellValue(
                    'D' . $currentRow,
                    $row['CUSTOMER_NAME'] ?? '-'
                );


                $sheet->setCellValue(
                    'E' . $currentRow,
                    $source
                );


                $sheet->setCellValue(
                    'F' . $currentRow,
                    $row['PEMBAYARAN'] ?? '-'
                );


                $sheet->setCellValue(
                    'G' . $currentRow,
                    $row['STATUS'] ?? '-'
                );


                $sheet->setCellValue(
                    'H' . $currentRow,
                    $detail['MATERIAL_NAME'] ?? '-'
                );


                $sheet->setCellValue(
                    'I' . $currentRow,
                    $detail['CALC_BASIS'] ?? '-'
                );


                $sheet->setCellValue(
                    'J' . $currentRow,
                    $qty
                );


                $sheet->setCellValue(
                    'K' . $currentRow,
                    $weight
                );


                $sheet->setCellValue(
                    'L' . $currentRow,
                    $avgBw
                );


                $sheet->setCellValue(
                    'M' . $currentRow,
                    $price
                );


                $sheet->setCellValue(
                    'N' . $currentRow,
                    $discount
                );


                $sheet->setCellValue(
                    'O' . $currentRow,
                    $total
                );


                $currentRow++;

            }


            /*
            |--------------------------------------------------------------------------
            | SAVING
            |--------------------------------------------------------------------------
            */

            foreach(
                $savings as $saving
            ){

                $qty =
                    (float)(
                        $saving['TOTAL_QTY']
                        ?? 0
                    );


                $weight =
                    (float)(
                        $saving['TOTAL_WEIGHT']
                        ?? 0
                    );


                $rate =
                    (float)(
                        $saving['RATE']
                        ?? 0
                    );


                $savingTotal =
                    (float)(
                        $saving['AMOUNT']
                        ?? (
                            $saving['TOTAL']
                            ?? 0
                        )
                    );


                $sheet->setCellValue(
                    'A' . $currentRow,
                    $row['SALES'] ?? '-'
                );


                $sheet->setCellValue(
                    'B' . $currentRow,
                    !empty($row['SALES_DATE'])
                        ? date(
                            'd/m/Y',
                            strtotime(
                                $row['SALES_DATE']
                            )
                        )
                        : '-'
                );


                $sheet->setCellValue(
                    'C' . $currentRow,
                    $row['PLANT_NAME'] ?? '-'
                );


                $sheet->setCellValue(
                    'D' . $currentRow,
                    $row['CUSTOMER_NAME'] ?? '-'
                );


                $sheet->setCellValue(
                    'E' . $currentRow,
                    $source
                );


                $sheet->setCellValue(
                    'F' . $currentRow,
                    $row['PEMBAYARAN'] ?? '-'
                );


                $sheet->setCellValue(
                    'G' . $currentRow,
                    $row['STATUS'] ?? '-'
                );


                $sheet->setCellValue(
                    'H' . $currentRow,
                    'SAVING' .
                    (
                        !empty($saving['SV_NO'])
                            ? ' - ' .
                            $saving['SV_NO']
                            : ''
                    )
                );


                $sheet->setCellValue(
                    'I' . $currentRow,
                    $saving['BASIS'] ?? '-'
                );


                $sheet->setCellValue(
                    'J' . $currentRow,
                    $qty
                );


                $sheet->setCellValue(
                    'K' . $currentRow,
                    $weight
                );


                $sheet->setCellValue(
                    'L' . $currentRow,
                    0
                );


                $sheet->setCellValue(
                    'M' . $currentRow,
                    $rate
                );


                $sheet->setCellValue(
                    'N' . $currentRow,
                    0
                );


                $sheet->setCellValue(
                    'O' . $currentRow,
                    $savingTotal
                );


                /*
                |--------------------------------------------------------------------------
                | SAVING STYLE
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle(
                        'A' .
                        $currentRow .
                        ':O' .
                        $currentRow
                    )
                    ->getFill()
                    ->setFillType(
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setRGB(
                        'FFF8E6'
                    );


                $sheet
                    ->getStyle(
                        'H' .
                        $currentRow
                    )
                    ->getFont()
                    ->setBold(true);


                $currentRow++;

            }


            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */

            $sheet->mergeCells(

                'A' .
                $currentRow .
                ':I' .
                $currentRow

            );


            $sheet->setCellValue(
                'A' . $currentRow,
                'Subtotal Detail (' .
                count($details) .
                ' item)'
            );


            $sheet->setCellValue(
                'J' . $currentRow,
                $subtotalQty
            );


            $sheet->setCellValue(
                'K' . $currentRow,
                $subtotalWeight
            );


            $sheet->setCellValue(
                'N' . $currentRow,
                $subtotalDiscount
            );


            $sheet->setCellValue(
                'O' . $currentRow,
                $subtotalTotal
            );


            $sheet
                ->getStyle(
                    'A' .
                    $currentRow .
                    ':O' .
                    $currentRow
                )
                ->applyFromArray([

                    'font' => [

                        'bold' =>
                            true

                    ],

                    'fill' => [

                        'fillType' =>
                            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                        'startColor' => [

                            'rgb' =>
                                $lightBlue

                        ]

                    ]

                ]);


            $currentRow += 2;

        }


        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(

            'A' .
            $currentRow .
            ':I' .
            $currentRow

        );


        $sheet->setCellValue(
            'A' . $currentRow,
            'TOTAL SEMUA'
        );


        $sheet->setCellValue(
            'J' . $currentRow,
            $grandQty
        );


        $sheet->setCellValue(
            'K' . $currentRow,
            $grandWeight
        );


        $sheet->setCellValue(
            'N' . $currentRow,
            $grandDiscount
        );


        $sheet->setCellValue(
            'O' . $currentRow,
            $grandDetailTotal
        );


        $sheet
            ->getStyle(
                'A' .
                $currentRow .
                ':O' .
                $currentRow
            )
            ->applyFromArray([

                'font' => [

                    'bold' =>
                        true,

                    'color' => [

                        'rgb' =>
                            'FFFFFF'

                    ]

                ],

                'fill' => [

                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [

                        'rgb' =>
                            $darkBlue

                    ]

                ]

            ]);


        /*
        |--------------------------------------------------------------------------
        | BORDER
        |--------------------------------------------------------------------------
        */

        $lastDataRow =
            $currentRow;


        $sheet
            ->getStyle(
                'A' .
                $headerRow .
                ':O' .
                $lastDataRow
            )
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB(
                $borderColor
            );


        /*
        |--------------------------------------------------------------------------
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'J' .
                ($headerRow + 1) .
                ':J' .
                $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.##'
            );


        $sheet
            ->getStyle(
                'K' .
                ($headerRow + 1) .
                ':L' .
                $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.###'
            );


        $sheet
            ->getStyle(
                'M' .
                ($headerRow + 1) .
                ':O' .
                $lastDataRow
            )
            ->getNumberFormat()
            ->setFormatCode(
                '"Rp" #,##0'
            );


        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getStyle(
                'A' .
                ($headerRow + 1) .
                ':I' .
                $lastDataRow
            )
            ->getAlignment()
            ->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );


        $sheet
            ->getStyle(
                'J' .
                ($headerRow + 1) .
                ':O' .
                $lastDataRow
            )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        /*
        |--------------------------------------------------------------------------
        | FREEZE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane(
            'A11'
        );


        /*
        |--------------------------------------------------------------------------
        | AUTO FILTER
        |--------------------------------------------------------------------------
        */

        $sheet->setAutoFilter(
            'A10:O' .
            $lastDataRow
        );


        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = [

            'A' => 22,

            'B' => 13,

            'C' => 16,

            'D' => 28,

            'E' => 28,

            'F' => 13,

            'G' => 12,

            'H' => 28,

            'I' => 12,

            'J' => 12,

            'K' => 14,

            'L' => 12,

            'M' => 16,

            'N' => 16,

            'O' => 18

        ];


        foreach(
            $widths as $column => $width
        ){

            $sheet
                ->getColumnDimension(
                    $column
                )
                ->setWidth(
                    $width
                );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE SETUP
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getPageSetup()
            ->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );


        $sheet
            ->getPageSetup()
            ->setPaperSize(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            );


        $sheet
            ->getPageSetup()
            ->setFitToWidth(1);


        $sheet
            ->getPageSetup()
            ->setFitToHeight(0);


        $sheet
            ->getPageSetup()
            ->setRowsToRepeatAtTopByStartAndEnd(
                10,
                10
            );


        /*
        |--------------------------------------------------------------------------
        | MARGINS
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getPageMargins()
            ->setTop(0.5);

        $sheet
            ->getPageMargins()
            ->setBottom(0.5);

        $sheet
            ->getPageMargins()
            ->setLeft(0.3);

        $sheet
            ->getPageMargins()
            ->setRight(0.3);


        /*
        |--------------------------------------------------------------------------
        | HEADER / FOOTER
        |--------------------------------------------------------------------------
        */

        $sheet
            ->getHeaderFooter()
            ->setOddHeader(
                '&L&"Arial,Bold"REPORT SALES&R' .
                date('d/m/Y H:i')
            );


        $sheet
            ->getHeaderFooter()
            ->setOddFooter(
                '&LGenerated by ERP&RPage &P of &N'
            );


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $filename =
            'REPORT_SALES_' .
            date('Ymd_His') .
            '.xlsx';


        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );


        header(
            'Content-Disposition: attachment; filename="' .
            $filename .
            '"'
        );


        header(
            'Cache-Control: max-age=0'
        );


        /*
        |--------------------------------------------------------------------------
        | WRITE
        |--------------------------------------------------------------------------
        */

        $writer =
            new Xlsx(
                $spreadsheet
            );


        $writer->save(
            'php://output'
        );


        exit;
    }

    public function export_pdf_sales()
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


        if(empty($rows)){

            show_error(
                'No data found for export'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary =
            $this->ReportSales_model
                ->summary_sales_report(
                    $filter
                );


        /*
        |--------------------------------------------------------------------------
        | HELPER
        |--------------------------------------------------------------------------
        */

        $e = function($value){

            return htmlspecialchars(
                (string)($value ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );

        };


        $money = function($value){

            return 'Rp ' .
                number_format(
                    (float)$value,
                    0,
                    ',',
                    '.'
                );

        };


        $number = function($value){

            return number_format(
                (float)$value,
                2,
                ',',
                '.'
            );

        };


        $date = function($value){

            if(empty($value)){

                return '-';

            }

            return date(
                'd F Y',
                strtotime($value)
            );

        };


        /*
        |--------------------------------------------------------------------------
        | FILTER LABEL
        |--------------------------------------------------------------------------
        */

        $plantLabel =
            'Semua Plant';

        if(!empty($filter['plant'])){

            $plantRow =
                $this->db
                    ->select(
                        'CODE_NAME'
                    )
                    ->where(
                        'HEAD_CODE',
                        'PLANT'
                    )
                    ->where(
                        'CODE',
                        $filter['plant']
                    )
                    ->get(
                        'abc_cd_code'
                    )
                    ->row();

            $plantLabel =
                $plantRow
                    ? $plantRow->CODE_NAME
                    : $filter['plant'];

        }


        $customerLabel =
            'Semua Customer';

        if(!empty($filter['customer'])){

            $customerRow =
                $this->db
                    ->select(
                        'FULL_NAME'
                    )
                    ->where(
                        'CUST',
                        $filter['customer']
                    )
                    ->get(
                        'abc_cd_customer'
                    )
                    ->row();

            $customerLabel =
                $customerRow
                    ? $customerRow->FULL_NAME
                    : $filter['customer'];

        }


        $paymentLabel =
            !empty($filter['pembayaran'])
                ? $filter['pembayaran']
                : 'Semua Payment';


        $statusLabel =
            !empty($filter['status'])
                ? $filter['status']
                : 'Semua Status';


        $searchLabel =
            !empty($filter['search'])
                ? $filter['search']
                : '-';


        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        $period =
            '-';

        if(
            !empty($filter['date_from']) &&
            !empty($filter['date_to'])
        ){

            $period =
                date(
                    'd/m/Y',
                    strtotime(
                        $filter['date_from']
                    )
                )
                .
                ' - '
                .
                date(
                    'd/m/Y',
                    strtotime(
                        $filter['date_to']
                    )
                );

        }
        elseif(!empty($filter['date_from'])){

            $period =
                date(
                    'd/m/Y',
                    strtotime(
                        $filter['date_from']
                    )
                )
                .
                ' - ...';

        }
        elseif(!empty($filter['date_to'])){

            $period =
                '... - '
                .
                date(
                    'd/m/Y',
                    strtotime(
                        $filter['date_to']
                    )
                );

        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logo =
            FCPATH .
            'assets/img/abc-trans.png';


        $logo64 =
            file_exists($logo)

                ? 'data:image/png;base64,' .
                    base64_encode(
                        file_get_contents(
                            $logo
                        )
                    )

                : '';


        /*
        |--------------------------------------------------------------------------
        | HTML
        |--------------------------------------------------------------------------
        */

        $html = '

        <style>

            body{

                font-family:
                    sans-serif;

                font-size:
                    8.5px;

                color:
                    #1f2937;

            }


            .report-header{

                margin-bottom:
                    12px;

            }


            .report-title{

                font-size:
                    18px;

                font-weight:
                    bold;

                color:
                    #0f172a;

            }


            .report-subtitle{

                font-size:
                    11px;

                font-weight:
                    bold;

                margin-top:
                    3px;

            }


            .report-period{

                font-size:
                    9px;

                color:
                    #64748b;

                margin-top:
                    3px;

            }


            .filter-table{

                width:
                    100%;

                border-collapse:
                    collapse;

                margin-bottom:
                    10px;

            }


            .filter-table td{

                border:
                    1px solid #dbe3ec;

                padding:
                    5px 7px;

                vertical-align:
                    top;

            }


            .filter-label{

                width:
                    70px;

                background:
                    #f1f5f9;

                font-weight:
                    bold;

                white-space:
                    nowrap;

            }


            .summary-table{

                width:
                    100%;

                border-collapse:
                    separate;

                border-spacing:
                    6px 0;

                margin:
                    0 -6px 12px -6px;

            }


            .summary-table td{

                border:
                    1px solid #dbe3ec;

                padding:
                    7px 9px;

                background:
                    #f8fafc;

            }


            .summary-label{

                color:
                    #64748b;

                font-size:
                    8px;

            }


            .summary-value{

                font-size:
                    12px;

                font-weight:
                    bold;

                margin-top:
                    2px;

            }


            .sales-card{

                border:
                    1px solid #d8e3f0;

                margin-bottom:
                    12px;

                page-break-inside:
                    avoid;

            }


            .sales-card-header{

                background:
                    #2563eb;

                color:
                    #ffffff;

                padding:
                    7px 9px;

                font-size:
                    10px;

                font-weight:
                    bold;

            }


            .sales-header-table{

                width:
                    100%;

                border-collapse:
                    collapse;

            }


            .sales-header-table td{

                border:
                    none;

                padding:
                    3px 5px;

                vertical-align:
                    top;

            }


            .sales-label{

                color:
                    #64748b;

                font-size:
                    7.5px;

                font-weight:
                    bold;

            }


            .sales-value{

                font-size:
                    8.5px;

            }


            .sales-total{

                text-align:
                    right;

                font-size:
                    11px;

                font-weight:
                    bold;

            }


            .detail-table{

                width:
                    100%;

                border-collapse:
                    collapse;

            }


            .detail-table th{

                background:
                    #eef2f7;

                color:
                    #475569;

                font-weight:
                    bold;

                border:
                    1px solid #d9e0e8;

                padding:
                    5px 4px;

                font-size:
                    7.5px;

                text-align:
                    center;

            }


            .detail-table td{

                border:
                    1px solid #e2e8f0;

                padding:
                    4px;

                font-size:
                    7.5px;

            }


            .text-right{

                text-align:
                    right;

            }


            .text-center{

                text-align:
                    center;

            }


            .saving-row{

                background:
                    #f0fdf4;

            }


            .saving-row td{

                color:
                    #166534;

            }


            .subtotal-row{

                background:
                    #f1f5f9;

                font-weight:
                    bold;

            }


            .sales-footer{

                padding:
                    7px 9px;

                background:
                    #f8fafc;

                border-top:
                    1px solid #dbe3ec;

            }


            .footer-table{

                width:
                    100%;

                border-collapse:
                    collapse;

            }


            .footer-table td{

                border:
                    none;

                padding:
                    2px 4px;

            }


            .footer-label{

                color:
                    #64748b;

            }


            .footer-value{

                text-align:
                    right;

                font-weight:
                    bold;

            }


            .grand-total{

                font-size:
                    10px;

            }


            .grand-total td{

                border-top:
                    1px solid #cbd5e1;

                padding-top:
                    5px;

            }

        </style>


        <div class="report-header">

            <table
                width="100%"
                style="border:none;"
            >

                <tr>

                    <td
                        width="65"
                        style="border:none;"
                    >
        ' .
        (
            file_exists($logo)
            ? '<img src="var:company_logo" height="55">'
            : ''
        )
        . '
                    </td>

                    <td
                        style="border:none;"
                    >

                        <div
                            class="report-title"
                        >
                            PT. Abadi Bersama Cerah
                        </div>

                        <div
                            class="report-subtitle"
                        >
                            REPORT SALES
                        </div>

                        <div
                            class="report-period"
                        >
                            Periode :
                            '.$e($period).'
                        </div>

                    </td>

                    <td
                        width="160"
                        style="
                            border:none;
                            text-align:right;
                            vertical-align:top;
                        "
                    >

                        <div class="report-period">

                            Generated :
                            '.date('d/m/Y H:i:s').'

                        </div>

                    </td>

                </tr>

            </table>

        </div>


        <!-- FILTER -->

        <table class="filter-table">

            <tr>

                <td class="filter-label">
                    Plant
                </td>

                <td>
                    '.$e($plantLabel).'
                </td>

                <td class="filter-label">
                    Customer
                </td>

                <td>
                    '.$e($customerLabel).'
                </td>

                <td class="filter-label">
                    Payment
                </td>

                <td>
                    '.$e($paymentLabel).'
                </td>

            </tr>

            <tr>

                <td class="filter-label">
                    Status
                </td>

                <td>
                    '.$e($statusLabel).'
                </td>

                <td class="filter-label">
                    Search
                </td>

                <td colspan="3">
                    '.$e($searchLabel).'
                </td>

            </tr>

        </table>


        <!-- SUMMARY -->

        <table class="summary-table">

            <tr>

                <td>

                    <div class="summary-label">
                        TOTAL SALES
                    </div>

                    <div class="summary-value">
                        '.$money(
                            $summary['TOTAL_SALES'] ?? 0
                        ).'
                    </div>

                </td>

                <td>

                    <div class="summary-label">
                        TOTAL SALES DOC
                    </div>

                    <div class="summary-value">
                        '.number_format(
                            (int)(
                                $summary['TOTAL_DOC'] ?? 0
                            ),
                            0,
                            ',',
                            '.'
                        ).'
                    </div>

                </td>

                <td>

                    <div class="summary-label">
                        TOTAL CUSTOMER
                    </div>

                    <div class="summary-value">
                        '.number_format(
                            (int)(
                                $summary['TOTAL_CUSTOMER'] ?? 0
                            ),
                            0,
                            ',',
                            '.'
                        ).'
                    </div>

                </td>

                <td>

                    <div class="summary-label">
                        TOTAL ITEM
                    </div>

                    <div class="summary-value">
                        '.number_format(
                            (int)(
                                $summary['TOTAL_ITEM'] ?? 0
                            ),
                            0,
                            ',',
                            '.'
                        ).'
                    </div>

                </td>

            </tr>

        </table>

        ';


        /*
        |--------------------------------------------------------------------------
        | SALES
        |--------------------------------------------------------------------------
        */

        foreach($rows as $row){

            $details =
                is_array($row['DETAILS'] ?? null)
                    ? $row['DETAILS']
                    : [];


            $savings =
                is_array($row['SAVINGS'] ?? null)
                    ? $row['SAVINGS']
                    : [];


            /*
            |--------------------------------------------------------------------------
            | SOURCE RECEIVE
            |--------------------------------------------------------------------------
            */

            $receiveNo =
                trim(
                    (string)(
                        $row['RECEIVE'] ?? ''
                    )
                );


            if(!$receiveNo){

                if(
                    preg_match(
                        '/AUTO FROM RECEIVE\s+([A-Z0-9]+)/i',
                        (string)(
                            $row['REMARK'] ?? ''
                        ),
                        $match
                    )
                ){

                    $receiveNo =
                        $match[1];

                }

            }


            $source =
                $receiveNo
                    ? 'FROM RECEIVE - '.$receiveNo
                    : 'MANUAL';


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $status =
                $row['STATUS'] ?? '';

            $statusClass =
                $status === 'PAID'
                    ? 'PAID'
                    : (
                        $status === 'PARTIAL'
                            ? 'PARTIAL'
                            : 'OPEN'
                    );


            /*
            |--------------------------------------------------------------------------
            | DETAIL TOTAL
            |--------------------------------------------------------------------------
            */

            $subtotalQty =
                0;

            $subtotalWeight =
                0;

            $subtotalDiscount =
                0;

            $subtotalTotal =
                0;


            $savingTotal =
                0;


            foreach($details as $d){

                $subtotalQty +=
                    (float)(
                        $d['JUMLAH'] ?? 0
                    );

                $subtotalWeight +=
                    (float)(
                        $d['BERAT'] ?? 0
                    );

                $subtotalDiscount +=
                    (float)(
                        $d['DISCOUNT'] ?? 0
                    );

                $subtotalTotal +=
                    (float)(
                        $d['TOTAL'] ?? 0
                    );

            }


            foreach($savings as $saving){

                $savingTotal +=
                    (float)(
                        $saving['AMOUNT'] ??
                        $saving->TOTAL ??
                        0
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | SALES CARD
            |--------------------------------------------------------------------------
            */

            $html .= '

            <div class="sales-card">

                <div class="sales-card-header">

                    SALES :
                    '.$e(
                        $row['SALES'] ?? '-'
                    ).'

                    &nbsp;&nbsp; | &nbsp;&nbsp;

                    STATUS :
                    '.$e($statusClass).'

                </div>


                <table class="sales-header-table">

                    <tr>

                        <td width="13%">

                            <div class="sales-label">
                                TANGGAL
                            </div>

                            <div class="sales-value">
                                '.$e(
                                    $date(
                                        $row['SALES_DATE'] ?? null
                                    )
                                ).'
                            </div>

                        </td>


                        <td width="17%">

                            <div class="sales-label">
                                PLANT
                            </div>

                            <div class="sales-value">
                                '.$e(
                                    $row['PLANT_NAME']
                                    ?? $row['PLANT']
                                    ?? '-'
                                ).'
                            </div>

                        </td>


                        <td width="27%">

                            <div class="sales-label">
                                CUSTOMER
                            </div>

                            <div class="sales-value">
                                '.$e(
                                    $row['CUSTOMER_NAME']
                                    ?? '-'
                                ).'

                                <br>

                                <span
                                    style="color:#64748b;"
                                >
                                    '.$e(
                                        $row['CUSTOMER']
                                        ?? ''
                                    ).'
                                </span>

                            </div>

                        </td>


                        <td width="20%">

                            <div class="sales-label">
                                SOURCE
                            </div>

                            <div class="sales-value">
                                '.$e($source).'
                            </div>

                        </td>


                        <td width="10%">

                            <div class="sales-label">
                                PAYMENT
                            </div>

                            <div class="sales-value">
                                '.$e(
                                    $row['PEMBAYARAN']
                                    ?? '-'
                                ).'
                            </div>

                        </td>


                        <td width="13%">

                            <div class="sales-total">

                                TOTAL SALES

                                <br>

                                '.$money(
                                    $row['AMOUNT'] ?? 0
                                ).'

                            </div>

                        </td>

                    </tr>

                </table>


                <table class="detail-table">

                    <thead>

                        <tr>

                            <th width="26%">
                                MATERIAL
                            </th>

                            <th width="9%">
                                BASIS
                            </th>

                            <th width="9%">
                                EKOR
                            </th>

                            <th width="10%">
                                BERAT
                            </th>

                            <th width="10%">
                                AVG BW
                            </th>

                            <th width="12%">
                                HARGA
                            </th>

                            <th width="11%">
                                DISCOUNT
                            </th>

                            <th width="13%">
                                TOTAL
                            </th>

                        </tr>

                    </thead>

                    <tbody>
            ';


            /*
            |--------------------------------------------------------------------------
            | DETAIL ROW
            |--------------------------------------------------------------------------
            */

            if(empty($details)){

                $html .= '

                    <tr>

                        <td
                            colspan="8"
                            class="text-center"
                            style="color:#94a3b8;"
                        >
                            Tidak ada detail
                        </td>

                    </tr>

                ';

            }
            else {

                foreach($details as $d){

                    $avgBw = (float)(
                        $d['AVG_BW'] ?? 0
                    );

                    $jumlah =
                        (float)(
                            $d['JUMLAH'] ?? 0
                        );

                    $berat =
                        (float)(
                            $d['BERAT'] ?? 0
                        );

                    if(
                        $avgBw <= 0 &&
                        $jumlah > 0 &&
                        $berat > 0
                    ){

                        $avgBw =
                            $berat /
                            $jumlah;

                    }

                    $html .= '

                        <tr>

                            <td>
                                '.$e(
                                    $d['MATERIAL_NAME']
                                    ?? $d->MATERIAL
                                    ?? '-'
                                ).'
                            </td>

                            <td class="text-center">
                                '.$e(
                                    $d['CALC_BASIS']
                                    ?? '-'
                                ).'
                            </td>

                            <td class="text-right">
                                '.$number(
                                    $d['JUMLAH'] ?? 0
                                ).'
                            </td>

                            <td class="text-right">
                                '.$number(
                                    $d['BERAT'] ?? 0
                                ).'
                            </td>

                            <td class="text-right">

                                '.$number(
                                    $avgBw
                                ).'

                            </td>

                            <td class="text-right">
                                '.$money(
                                    $d['HARGA'] ?? 0
                                ).'
                            </td>

                            <td class="text-right">
                                '.$money(
                                    $d['DISCOUNT'] ?? 0
                                ).'
                            </td>

                            <td class="text-right">
                                '.$money(
                                    $d['TOTAL'] ?? 0
                                ).'
                            </td>

                        </tr>

                    ';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | SAVING ROW
            |--------------------------------------------------------------------------
            */

            foreach($savings as $saving){

                $savingAmount =
                    (float)(
                        $saving['AMOUNT'] ??
                        $saving->TOTAL ??
                        0
                    );

                $savingQty =
                    (float)(
                        $saving['TOTAL_QTY'] ?? 0
                    );

                $savingWeight =
                    (float)(
                        $saving['TOTAL_WEIGHT'] ?? 0
                    );

                $savingRate =
                    (float)(
                        $saving['RATE'] ?? 0
                    );


                $html .= '

                    <tr class="saving-row">

                        <td>

                            SAVING

                            '.
                            (
                                !empty(
                                    $saving['SV_NO']
                                )
                                ? ' - ' .
                                    $e(
                                        $saving['SV_NO']
                                    )
                                : ''
                            )
                            .'

                        </td>

                        <td class="text-center">

                            '.$e(
                                $saving['BASIS']
                                ?? '-'
                            ).'

                        </td>

                        <td class="text-right">

                            '.
                            (
                                $savingQty
                                    ? $number(
                                        $savingQty
                                    )
                                    : '-'
                            )
                            .'

                        </td>

                        <td class="text-right">

                            '.
                            (
                                $savingWeight
                                    ? $number(
                                        $savingWeight
                                    )
                                    : '-'
                            )
                            .'

                        </td>

                        <td class="text-right">
                            -
                        </td>

                        <td class="text-right">

                            '.
                            (
                                $savingRate
                                    ? $money(
                                        $savingRate
                                    )
                                    : '-'
                            )
                            .'

                        </td>

                        <td class="text-right">
                            -
                        </td>

                        <td class="text-right">

                            '.$money(
                                $savingAmount
                            ).'

                        </td>

                    </tr>

                ';

            }


            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */

            $html .= '

                    <tr class="subtotal-row">

                        <td colspan="2">

                            SUBTOTAL DETAIL
                            ('.count($details).' item)

                        </td>

                        <td class="text-right">

                            '.$number(
                                $subtotalQty
                            ).'

                        </td>

                        <td class="text-right">

                            '.$number(
                                $subtotalWeight
                            ).'

                        </td>

                        <td></td>

                        <td></td>

                        <td class="text-right">

                            '.
                            (
                                $subtotalDiscount
                                    ? $money(
                                        $subtotalDiscount
                                    )
                                    : '-'
                            )
                            .'

                        </td>

                        <td class="text-right">

                            '.$money(
                                $subtotalTotal
                            ).'

                        </td>

                    </tr>

                    </tbody>

                </table>


                <div class="sales-footer">

                    <table class="footer-table">

                        <tr>

                            <td
                                class="footer-label"
                            >
                                Saving
                            </td>

                            <td
                                class="footer-value"
                            >
                                '.$money(
                                    $savingTotal
                                ).'
                            </td>

                        </tr>

                        <tr class="grand-total">

                            <td
                                class="footer-label"
                            >
                                Total Sales
                            </td>

                            <td
                                class="footer-value"
                            >
                                '.$money(
                                    $row['AMOUNT'] ?? 0
                                ).'
                            </td>

                        </tr>

                    </table>

                </div>

            </div>

            ';

        }


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $mpdf =
            new \Mpdf\Mpdf([

                'orientation' =>
                    'L',

                'format' =>
                    'A4',

                'margin_left' =>
                    8,

                'margin_right' =>
                    8,

                'margin_top' =>
                    10,

                'margin_bottom' =>
                    12,

                'default_font_size' =>
                    8

            ]);

        if(file_exists($logo)){

            $mpdf->imageVars['company_logo'] =
                file_get_contents(
                    $logo
                );

        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        $mpdf->SetHTMLFooter('

            <div
                style="
                    text-align:right;
                    font-size:8px;
                    color:#64748b;
                    border-top:1px solid #e2e8f0;
                    padding-top:4px;
                "
            >

                Report Sales
                &nbsp; | &nbsp;
                Page {PAGENO} of {nbpg}

            </div>

        ');


        /*
        |--------------------------------------------------------------------------
        | WRITE HTML IN CHUNKS
        |--------------------------------------------------------------------------
        */

        $htmlChunks =
            preg_split(
                '/(?=<div class="sales-card">)/',
                $html
            );


        foreach(
            $htmlChunks
            as $index => $chunk
        ){

            if(
                trim($chunk) === ''
            ){

                continue;

            }


            $mpdf->WriteHTML(
                $chunk
            );

        }


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $mpdf->Output(

            'REPORT_SALES_' .
            date('Ymd_His') .
            '.pdf',

            'I'

        );

        exit;
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
