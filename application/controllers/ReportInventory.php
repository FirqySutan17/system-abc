<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportInventory extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_inventory_po')) {
            show_404();
        }
        $this->load->model('ReportInventory_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'     => $this->ReportInventory_model->get_plant_list(),
            'suppliers'  => $this->ReportInventory_model->get_supplier_list(),
            'class_out' => $this->ReportInventory_model->get_class_out_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Inventory']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_inventory/index', $data);
        $this->load->view('templates/footer');
    }

    public function po()
    {
        $data = [
            'plants'     => $this->ReportInventory_model->get_plant_list(),
            'suppliers'  => $this->ReportInventory_model->get_supplier_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report PO']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_inventory/po', $data);
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        header('Content-Type: application/json');

        $page = max(
            1,
            (int)$this->input->get('page')
        );

        $limit = max(
            1,
            (int)$this->input->get('limit')
        );

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        $filters = [

            'plant' => trim(
                $this->input->get('plant', true)
            ),

            'supplier' => trim(
                $this->input->get('supplier', true)
            ),

            'po' => trim(
                $this->input->get('po', true)
            ),

            'date_from' => trim(
                $this->input->get('date_from', true)
            ),

            'date_to' => trim(
                $this->input->get('date_to', true)
            )

        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->ReportInventory_model
            ->get_po_report(
                $limit,
                $start,
                $filters
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $total = $this->ReportInventory_model
            ->count_po_report(
                $filters
            );

        $pages = ceil($total / $limit);

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' => $total,

            'page' => $page,

            'pages' => $pages,

            'pagination' => $this->build_pagination(
                $pages,
                $page,
                'ajax'
            )

        ]);
    }
    
    public function export_excel_po()
    {
        $filters = [
            'plant' => trim($this->input->get('plant', true)),
            'supplier' => trim($this->input->get('supplier', true)),
            'po' => trim($this->input->get('po', true)),
            'date_from' => trim($this->input->get('date_from', true)),
            'date_to' => trim($this->input->get('date_to', true))
        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->ReportInventory_model->get_po_report(
            0,
            0,
            $filters,
            'a.PO_DATE',
            'DESC'
        );

        if (empty($rows)) {
            show_error('No data found');
        }

        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('PO REPORT');

        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoPath = FCPATH . 'assets/img/abc-trans.png';

        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(55);
            $drawing->setWorksheet($sheet);
        }

        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('B1:AE1');
        $sheet->setCellValue('B1', 'PT. Abadi Bersama Cerah');

        $sheet->getStyle('B1')
            ->getFont()
            ->setBold(true)
            ->setSize(18);

        $sheet->mergeCells('B2:AE2');
        $sheet->setCellValue('B2', 'PURCHASE ORDER REPORT');

        $sheet->getStyle('B2')
            ->getFont()
            ->setBold(true)
            ->setSize(13);

        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        $periodFrom = !empty($filters['date_from'])
            ? date('d M Y', strtotime($filters['date_from']))
            : '-';

        $periodTo = !empty($filters['date_to'])
            ? date('d M Y', strtotime($filters['date_to']))
            : '-';

        $sheet->mergeCells('B3:AE3');

        $sheet->setCellValue(
            'B3',
            'Period : ' . $periodFrom . ' - ' . $periodTo
        );

        $sheet->getStyle('B3')
            ->getFont()
            ->setItalic(true);

        /*
        |--------------------------------------------------------------------------
        | GROUP HEADER
        |--------------------------------------------------------------------------
        */

        $groupHeaders = [
            'A5:I5'   => 'PO INFORMATION',
            'J5:N5'   => 'PO QUANTITY & VALUE',
            'O5:R5'   => 'JANTAN & BETINA',
            'S5:X5'   => 'ACTUAL & RECEIVE',
            'Y5:AA5'  => 'CLAIM & PAYABLE',
            'AB5:AD5' => 'PAYMENT',
            'AE5:AE5' => 'ADDITIONAL'
        ];

        foreach ($groupHeaders as $range => $label) {
            $sheet->mergeCells($range);

            $startCell = explode(':', $range)[0];

            $sheet->setCellValue($startCell, $label);
        }

        /*
        |--------------------------------------------------------------------------
        | GROUP STYLE
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A5:AE5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '0F4C81'
                ]
            ],
            'fill' => [
                'fillType' =>
                    \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'EAF3FB'
                ]
            ],
            'alignment' => [
                'horizontal' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => 'D7E5F0'
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
            'PO',
            'Tanggal',
            'Plant',
            'PO Type',
            'Supplier',
            'Material',
            'Truck',
            'Driver',
            'Status',
            'Qty',
            'Weight',
            'Avg BW',
            'Price',
            'Total',

            // JANTAN & BETINA
            'Qty Jantan',
            'BW Jantan',
            'Qty Betina',
            'BW Betina',

            // ACTUAL & RECEIVE
            'Dead Qty',
            'Dead BW',
            'Actual Weight',
            'Shrink',
            'Receive Qty',
            'Receive BW',

            // CLAIM & PAYABLE
            'Claim Qty',
            'Claim BW',
            'Payable BW',

            // PAYMENT
            'Paid Amount',
            'Remaining',
            'Payment Status',

            // ADDITIONAL
            'Remark'
        ];

        $col = 1;

        foreach ($headers as $header) {
            $columnLetter =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

            $sheet->setCellValue($columnLetter . '6', $header);

            $col++;
        }

        /*
        |--------------------------------------------------------------------------
        | COLUMN HEADER STYLE
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A6:AE6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => '334155'
                ]
            ],
            'fill' => [
                'fillType' =>
                    \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F8FAFC'
                ]
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
                        'rgb' => 'D9DEE5'
                    ]
                ]
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rowNumber = 7;

        foreach ($rows as $r) {

            /*
            |--------------------------------------------------------------------------
            | PO INFORMATION
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValueExplicit(
                "A{$rowNumber}",
                (string)$r->PO,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );

            $sheet->setCellValue(
                "B{$rowNumber}",
                !empty($r->PO_DATE)
                    ? strtoupper(date('d F Y', strtotime($r->PO_DATE)))
                    : '-'
            );

            $sheet->setCellValue(
                "C{$rowNumber}",
                $r->PLANT_NAME ?: $r->PLANT ?: '-'
            );

            $sheet->setCellValue(
                "D{$rowNumber}",
                $r->PO_NAME ?: $r->PO_TYPE ?: '-'
            );

            $sheet->setCellValue(
                "E{$rowNumber}",
                $r->SUPPLIER_NAME ?: $r->SUPPLIER ?: '-'
            );

            $sheet->setCellValue(
                "F{$rowNumber}",
                $r->MATERIAL_NAME ?: $r->MATERIAL ?: '-'
            );

            $sheet->setCellValue(
                "G{$rowNumber}",
                $r->NO_TRUCK ?: '-'
            );

            $sheet->setCellValue(
                "H{$rowNumber}",
                $r->DRIVER ?: '-'
            );

            $sheet->setCellValue(
                "I{$rowNumber}",
                $r->STATUS ?: '-'
            );

            /*
            |--------------------------------------------------------------------------
            | PO QUANTITY & VALUE
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "J{$rowNumber}",
                (float)$r->JUMLAH
            );

            $sheet->setCellValue(
                "K{$rowNumber}",
                (float)$r->BERAT
            );

            $sheet->setCellValue(
                "L{$rowNumber}",
                (float)$r->AVG_BW
            );

            $sheet->setCellValue(
                "M{$rowNumber}",
                (float)$r->HARGA
            );

            $sheet->setCellValue(
                "N{$rowNumber}",
                (float)$r->TOTAL
            );

            /*
            |--------------------------------------------------------------------------
            | JANTAN & BETINA
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "O{$rowNumber}",
                (float)($r->JANTAN_QTY ?? 0)
            );

            $sheet->setCellValue(
                "P{$rowNumber}",
                (float)($r->JANTAN_BW ?? 0)
            );

            $sheet->setCellValue(
                "Q{$rowNumber}",
                (float)($r->BETINA_QTY ?? 0)
            );

            $sheet->setCellValue(
                "R{$rowNumber}",
                (float)($r->BETINA_BW ?? 0)
            );

            /*
            |--------------------------------------------------------------------------
            | ACTUAL & RECEIVE
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "S{$rowNumber}",
                (float)$r->MATI_QTY
            );

            $sheet->setCellValue(
                "T{$rowNumber}",
                (float)$r->MATI_BW
            );

            $sheet->setCellValue(
                "U{$rowNumber}",
                (float)$r->ACTUAL_HASIL_TIMBANG
            );

            $sheet->setCellValue(
                "V{$rowNumber}",
                (float)$r->SUSUT_BW
            );

            $sheet->setCellValue(
                "W{$rowNumber}",
                (float)$r->TOTAL_TERIMA_QTY
            );

            $sheet->setCellValue(
                "X{$rowNumber}",
                (float)$r->TOTAL_TERIMA_BW
            );

            /*
            |--------------------------------------------------------------------------
            | CLAIM & PAYABLE
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "Y{$rowNumber}",
                (float)$r->CLAIM_QTY
            );

            $sheet->setCellValue(
                "Z{$rowNumber}",
                (float)$r->CLAIM_BW
            );

            $sheet->setCellValue(
                "AA{$rowNumber}",
                (float)$r->TOTAL_BAYAR_BW
            );

            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "AB{$rowNumber}",
                (float)$r->PAID_AMOUNT
            );

            $sheet->setCellValue(
                "AC{$rowNumber}",
                (float)$r->REMAINING
            );

            $sheet->setCellValue(
                "AD{$rowNumber}",
                $r->PAYMENT_STATUS ?: '-'
            );

            /*
            |--------------------------------------------------------------------------
            | ADDITIONAL
            |--------------------------------------------------------------------------
            */

            $sheet->setCellValue(
                "AE{$rowNumber}",
                $r->REMARK ?: '-'
            );

            $rowNumber++;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA STYLE
        |--------------------------------------------------------------------------
        */

        $lastRow = $rowNumber - 1;

        $sheet->getStyle("A7:AE{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => 'E5E7EB'
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
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("J7:L{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("O7:X{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("Y7:AA{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("M7:N{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');

        $sheet->getStyle("AB7:AC{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');

        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("J7:AC{$lastRow}")
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );

        $sheet->getStyle("A7:I{$lastRow}")
            ->getAlignment()
            ->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

        $sheet->getStyle("AD7:AE{$lastRow}")
            ->getAlignment()
            ->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            );

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT COLUMNS
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("A7:A{$lastRow}")
            ->getFont()
            ->setBold(true)
            ->getColor()
            ->setRGB('0066A1');

        $sheet->getStyle("N7:N{$lastRow}")
            ->getFont()
            ->setBold(true);

        /*
        |--------------------------------------------------------------------------
        | WRAP TEXT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("E7:F{$lastRow}")
            ->getAlignment()
            ->setWrapText(true);

        $sheet->getStyle("AE7:AE{$lastRow}")
            ->getAlignment()
            ->setWrapText(true);

        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = [
            'A'  => 18,
            'B'  => 20,
            'C'  => 14,
            'D'  => 18,
            'E'  => 25,
            'F'  => 25,
            'G'  => 16,
            'H'  => 22,
            'I'  => 14,

            'J'  => 13,
            'K'  => 14,
            'L'  => 12,
            'M'  => 16,
            'N'  => 18,

            'O'  => 13,
            'P'  => 13,
            'Q'  => 13,
            'R'  => 13,

            'S'  => 13,
            'T'  => 13,
            'U'  => 16,
            'V'  => 13,
            'W'  => 15,
            'X'  => 15,

            'Y'  => 13,
            'Z'  => 13,
            'AA' => 15,

            'AB' => 17,
            'AC' => 17,
            'AD' => 16,
            'AE' => 30
        ];

        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        /*
        |--------------------------------------------------------------------------
        | ROW HEIGHT
        |--------------------------------------------------------------------------
        */

        $sheet->getRowDimension(5)->setRowHeight(22);
        $sheet->getRowDimension(6)->setRowHeight(30);

        /*
        |--------------------------------------------------------------------------
        | FILTER & FREEZE
        |--------------------------------------------------------------------------
        */

        $sheet->freezePane('A7');
        $sheet->setAutoFilter("A6:AE{$lastRow}");

        /*
        |--------------------------------------------------------------------------
        | PAGE SETUP
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

        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $sheet->getPageMargins()
            ->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.25)
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
            'Content-Disposition: attachment; filename="REPORT_PO.xlsx"'
        );

        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $writer->save('php://output');

        exit;
    }

    public function export_pdf_po()
    {
        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [
            'plant'     => trim($this->input->get('plant', true)),
            'supplier'  => trim($this->input->get('supplier', true)),
            'po'        => trim($this->input->get('po', true)),
            'date_from' => trim($this->input->get('date_from', true)),
            'date_to'   => trim($this->input->get('date_to', true)),
        ];

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->ReportInventory_model->get_po_report(
            0,
            0,
            $filters,
            'a.PO_DATE',
            'DESC'
        );

        if (empty($rows)) {
            show_error('No data found for export');
        }

        /*
        |--------------------------------------------------------------------------
        | HELPER
        |--------------------------------------------------------------------------
        */

        $esc = function ($value) {
            return htmlspecialchars(
                (string)($value ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );
        };

        $num = function ($value, $decimal = 2) {
            if ($value === null || $value === '') {
                return '-';
            }

            return number_format(
                (float)$value,
                $decimal,
                ',',
                '.'
            );
        };

        $money = function ($value) {
            if ($value === null || $value === '') {
                return '-';
            }

            return 'Rp ' . number_format(
                (float)$value,
                0,
                ',',
                '.'
            );
        };

        $dateIndo = function ($date) {
            if (empty($date)) {
                return '-';
            }

            $timestamp = strtotime($date);

            if (!$timestamp) {
                return '-';
            }

            $months = [
                1  => 'JANUARI',
                2  => 'FEBRUARI',
                3  => 'MARET',
                4  => 'APRIL',
                5  => 'MEI',
                6  => 'JUNI',
                7  => 'JULI',
                8  => 'AGUSTUS',
                9  => 'SEPTEMBER',
                10 => 'OKTOBER',
                11 => 'NOVEMBER',
                12 => 'DESEMBER'
            ];

            return date('d', $timestamp)
                . ' '
                . $months[(int)date('n', $timestamp)]
                . ' '
                . date('Y', $timestamp);
        };

        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        */

        if (
            !empty($filters['date_from']) &&
            !empty($filters['date_to'])
        ) {
            $period = $dateIndo($filters['date_from'])
                . ' - '
                . $dateIndo($filters['date_to']);

        } elseif (!empty($filters['date_from'])) {
            $period = 'Mulai ' . $dateIndo($filters['date_from']);

        } elseif (!empty($filters['date_to'])) {
            $period = 'Sampai ' . $dateIndo($filters['date_to']);

        } else {
            $period = 'Semua Periode';
        }

        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logo = FCPATH . 'assets/img/abc-trans.png';
        $logoHtml = '';

        if (file_exists($logo)) {
            $logoHtml = '
                <img
                    src="' . $logo . '"
                    style="height:45px;width:auto;"
                >
            ';
        }

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $mpdf = new \Mpdf\Mpdf([
            'format'        => 'A3',
            'orientation'   => 'L',
            'margin_left'   => 8,
            'margin_right'  => 8,
            'margin_top'    => 10,
            'margin_bottom' => 12,
            'margin_header' => 5,
            'margin_footer' => 5,
            'default_font'  => 'sans'
        ]);

        $mpdf->SetHTMLFooter('
            <div style="
                text-align:right;
                font-size:8px;
                color:#666;
            ">
                Page {PAGENO} of {nbpg}
            </div>
        ');

        /*
        |--------------------------------------------------------------------------
        | CSS + HEADER
        |--------------------------------------------------------------------------
        */

        $html = '
        <style>
            body {
                font-family:sans-serif;
                font-size:7px;
                color:#222;
            }

            .report-header {
                width:100%;
                border-collapse:collapse;
                margin-bottom:7px;
            }

            .report-header td {
                border:none;
            }

            .logo-cell {
                width:65px;
                vertical-align:middle;
            }

            .title-cell {
                text-align:center;
                vertical-align:middle;
            }

            .title {
                font-size:17px;
                font-weight:bold;
            }

            .subtitle {
                font-size:10px;
                font-weight:bold;
                margin-top:2px;
            }

            .period {
                font-size:8px;
                margin-top:2px;
            }

            .filter-table {
                width:100%;
                border-collapse:collapse;
                margin-bottom:7px;
            }

            .filter-table td {
                border:0.4px solid #d9dee5;
                padding:3px 5px;
                font-size:7px;
            }

            .filter-label {
                font-weight:bold;
                background:#f1f4f8;
                width:42px;
            }

            .po-table {
                width:100%;
                border-collapse:collapse;
                table-layout:fixed;
            }

            .po-table thead {
                display:table-header-group;
            }

            .po-table tr {
                page-break-inside:avoid;
            }

            .po-table th,
            .po-table td {
                border:0.4px solid #cfd6df;
                padding:3px 1.5px;
                vertical-align:middle;
                word-wrap:break-word;
                overflow-wrap:break-word;
            }

            .group-header th {
                background:#0F4C81;
                color:#fff;
                text-align:center;
                font-size:6.5px;
                font-weight:bold;
                padding:4px 1px;
            }

            .column-header th {
                background:#e9eef4;
                color:#222;
                text-align:center;
                font-size:6px;
                font-weight:bold;
                padding:4px 1px;
            }

            .text {
                text-align:left;
            }

            .center {
                text-align:center;
            }

            .right {
                text-align:right;
            }

            .po-number {
                font-weight:bold;
                color:#0F4C81;
            }

            .total-cell {
                font-weight:bold;
            }

            .status,
            .payment {
                font-weight:bold;
                text-align:center;
            }

            .remark {
                font-size:6px;
            }
        </style>

        <table class="report-header">
            <tr>
                <td class="logo-cell">
                    ' . $logoHtml . '
                </td>

                <td class="title-cell">
                    <div class="title">
                        PT. Abadi Bersama Cerah
                    </div>

                    <div class="subtitle">
                        PURCHASE ORDER REPORT
                    </div>

                    <div class="period">
                        Periode : ' . $esc($period) . '
                    </div>
                </td>

                <td class="logo-cell"></td>
            </tr>
        </table>

        <table class="filter-table">
            <tr>
                <td class="filter-label">Plant</td>
                <td>' .
                    (!empty($filters['plant'])
                        ? $esc($filters['plant'])
                        : 'Semua Plant') .
                '</td>

                <td class="filter-label">Supplier</td>
                <td>' .
                    (!empty($filters['supplier'])
                        ? $esc($filters['supplier'])
                        : 'Semua Supplier') .
                '</td>

                <td class="filter-label">PO</td>
                <td>' .
                    (!empty($filters['po'])
                        ? $esc($filters['po'])
                        : 'Semua PO') .
                '</td>
            </tr>
        </table>

        <table class="po-table">
            <colgroup>
                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:3%;">
                <col style="width:3.5%;">
                <col style="width:4.5%;">
                <col style="width:4.5%;">
                <col style="width:3%;">
                <col style="width:3.5%;">
                <col style="width:3%;">

                <col style="width:3%;">
                <col style="width:3%;">
                <col style="width:3%;">
                <col style="width:4%;">
                <col style="width:4%;">

                <col style="width:2.5%;">
                <col style="width:2.5%;">
                <col style="width:2.5%;">
                <col style="width:2.5%;">

                <col style="width:2.5%;">
                <col style="width:2.5%;">
                <col style="width:3%;">
                <col style="width:2.5%;">
                <col style="width:2.5%;">
                <col style="width:3%;">

                <col style="width:2.5%;">
                <col style="width:2.5%;">
                <col style="width:3%;">

                <col style="width:4%;">
                <col style="width:4%;">
                <col style="width:4%;">

                <col style="width:5%;">
            </colgroup>

            <thead>
                <tr class="group-header">
                    <th colspan="9">PO INFORMATION</th>
                    <th colspan="5">PO QUANTITY &amp; VALUE</th>
                    <th colspan="4">JANTAN &amp; BETINA</th>
                    <th colspan="6">ACTUAL &amp; RECEIVE</th>
                    <th colspan="3">CLAIM &amp; PAYABLE</th>
                    <th colspan="3">PAYMENT</th>
                    <th colspan="1">ADDITIONAL</th>
                </tr>

                <tr class="column-header">
                    <th>PO</th>
                    <th>Tanggal</th>
                    <th>Plant</th>
                    <th>PO Type</th>
                    <th>Supplier</th>
                    <th>Material</th>
                    <th>Truck</th>
                    <th>Driver</th>
                    <th>Status</th>

                    <th>Qty</th>
                    <th>Weight</th>
                    <th>Avg BW</th>
                    <th>Price</th>
                    <th>Total</th>

                    <th>Qty Jantan</th>
                    <th>BW Jantan</th>
                    <th>Qty Betina</th>
                    <th>BW Betina</th>

                    <th>Dead Qty</th>
                    <th>Dead BW</th>
                    <th>Actual Weight</th>
                    <th>Shrink</th>
                    <th>Receive Qty</th>
                    <th>Receive BW</th>

                    <th>Claim Qty</th>
                    <th>Claim BW</th>
                    <th>Payable BW</th>

                    <th>Paid Amount</th>
                    <th>Remaining</th>
                    <th>Payment Status</th>

                    <th>Remark</th>
                </tr>
            </thead>

            <tbody>
        ';

        /*
        |--------------------------------------------------------------------------
        | ROWS
        |--------------------------------------------------------------------------
        */

        foreach ($rows as $r) {

            $status = !empty($r->STATUS)
                ? strtoupper($r->STATUS)
                : '-';

            $paymentStatus = !empty($r->PAYMENT_STATUS)
                ? strtoupper($r->PAYMENT_STATUS)
                : '-';

            $html .= '
                <tr>
                    <td class="text po-number">'
                        . $esc($r->PO) .
                    '</td>

                    <td class="center">'
                        . $esc($dateIndo($r->PO_DATE)) .
                    '</td>

                    <td class="text">'
                        . $esc(
                            !empty($r->PLANT_NAME)
                                ? $r->PLANT_NAME
                                : $r->PLANT
                        ) .
                    '</td>

                    <td class="text">'
                        . $esc(
                            !empty($r->PO_NAME)
                                ? $r->PO_NAME
                                : $r->PO_TYPE
                        ) .
                    '</td>

                    <td class="text">'
                        . $esc(
                            !empty($r->SUPPLIER_NAME)
                                ? $r->SUPPLIER_NAME
                                : $r->SUPPLIER
                        ) .
                    '</td>

                    <td class="text">'
                        . $esc(
                            !empty($r->MATERIAL_NAME)
                                ? $r->MATERIAL_NAME
                                : $r->MATERIAL
                        ) .
                    '</td>

                    <td class="center">'
                        . $esc(
                            !empty($r->NO_TRUCK)
                                ? $r->NO_TRUCK
                                : '-'
                        ) .
                    '</td>

                    <td class="text">'
                        . $esc(
                            !empty($r->DRIVER)
                                ? $r->DRIVER
                                : '-'
                        ) .
                    '</td>

                    <td class="status">'
                        . $esc($status) .
                    '</td>

                    <td class="right">'
                        . $num($r->JUMLAH) .
                    '</td>

                    <td class="right">'
                        . $num($r->BERAT) .
                    '</td>

                    <td class="right">'
                        . $num($r->AVG_BW) .
                    '</td>

                    <td class="right">'
                        . $money($r->HARGA) .
                    '</td>

                    <td class="right total-cell">'
                        . $money($r->TOTAL) .
                    '</td>

                    <!-- JANTAN & BETINA -->
                    <td class="right">'
                        . $num($r->JANTAN_QTY ?? 0) .
                    '</td>

                    <td class="right">'
                        . $num($r->JANTAN_BW ?? 0) .
                    '</td>

                    <td class="right">'
                        . $num($r->BETINA_QTY ?? 0) .
                    '</td>

                    <td class="right">'
                        . $num($r->BETINA_BW ?? 0) .
                    '</td>

                    <!-- ACTUAL & RECEIVE -->
                    <td class="right">'
                        . $num($r->MATI_QTY) .
                    '</td>

                    <td class="right">'
                        . $num($r->MATI_BW) .
                    '</td>

                    <td class="right">'
                        . $num($r->ACTUAL_HASIL_TIMBANG) .
                    '</td>

                    <td class="right">'
                        . $num($r->SUSUT_BW) .
                    '</td>

                    <td class="right">'
                        . $num($r->TOTAL_TERIMA_QTY) .
                    '</td>

                    <td class="right">'
                        . $num($r->TOTAL_TERIMA_BW) .
                    '</td>

                    <!-- CLAIM & PAYABLE -->
                    <td class="right">'
                        . $num($r->CLAIM_QTY) .
                    '</td>

                    <td class="right">'
                        . $num($r->CLAIM_BW) .
                    '</td>

                    <td class="right">'
                        . $num($r->TOTAL_BAYAR_BW) .
                    '</td>

                    <!-- PAYMENT -->
                    <td class="right">'
                        . $money($r->PAID_AMOUNT) .
                    '</td>

                    <td class="right">'
                        . $money($r->REMAINING) .
                    '</td>

                    <td class="payment">'
                        . $esc($paymentStatus) .
                    '</td>

                    <!-- ADDITIONAL -->
                    <td class="text remark">'
                        . (
                            !empty($r->REMARK)
                                ? $esc($r->REMARK)
                                : '-'
                        ) .
                    '</td>
                </tr>
            ';
        }

        /*
        |--------------------------------------------------------------------------
        | CLOSE TABLE
        |--------------------------------------------------------------------------
        */

        $html .= '
            </tbody>
        </table>
        ';

        /*
        |--------------------------------------------------------------------------
        | WRITE PDF
        |--------------------------------------------------------------------------
        */

        $mpdf->WriteHTML($html);

        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $mpdf->Output(
            'REPORT_PO.pdf',
            'I'
        );

        exit;
    }

    public function load_receive()
    {
        ob_clean();

        header('Content-Type: application/json');

        $page = (int)$this->input->get('page');

        $limit = (int)$this->input->get('limit');

        $page = $page > 0 ? $page : 1;

        $limit = $limit > 0 ? $limit : 10;

        $order = $this->input->get('order', true)
            ?: 'RECEIVE_DATE';

        $dirInput = $this->input->get('dir', true)
            ?? 'DESC';

        $dir = strtoupper($dirInput) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'plant' => $this->input
                ->get('plant', true),

            'supplier' => $this->input
                ->get('supplier', true),

            'receive' => $this->input
                ->get('receive', true),

            'date_from' => $this->input
                ->get('date_from', true),

            'date_to' => $this->input
                ->get('date_to', true)

        ];

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | ROWS
        |--------------------------------------------------------------------------
        */

        $rows = $this->ReportInventory_model
            ->get_receive_report(
                $limit,
                $start,
                $filters,
                $order,
                $dir
            );

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $totalRows = $this->ReportInventory_model
            ->count_receive_report($filters);

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages = ceil($totalRows / $limit);

        $pagination = $this->build_pagination(
            $pages,
            $page,
            'ajax'
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' => (int)$totalRows,

            'pagination' => $pagination,

            'page' => $page

        ]);

        exit;
    }

    public function export_excel_receive()
    {
        $filters = [

            'plant' => $this->input
                ->get('plant', true),

            'supplier' => $this->input
                ->get('supplier', true),

            'receive' => $this->input
                ->get('receive', true),

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

        $rows = $this->ReportInventory_model
            ->get_receive_report(
                0,
                0,
                $filters,
                'RECEIVE_DATE',
                'DESC'
            );


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
            'Receive Report'
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
            'B1:AL1'
        );

        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );

        $sheet->getStyle(
            'B1:AL1'
        )
            ->getFont()
            ->setBold(true)
            ->setSize(18);


        $sheet->mergeCells(
            'B2:AL2'
        );

        $sheet->setCellValue(
            'B2',
            'RECEIVE REPORT'
        );

        $sheet->getStyle(
            'B2:AL2'
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
            'B3:AL3'
        );

        $sheet->setCellValue(
            'B3',
            'Period : ' . $period
        );

        $sheet->getStyle(
            'B3:AL3'
        )
            ->getFont()
            ->setItalic(true);


        /*
        |--------------------------------------------------------------------------
        | GROUP HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'A5:H5'
        );

        $sheet->setCellValue(
            'A5',
            'RECEIVE INFORMATION'
        );


        $sheet->mergeCells(
            'I5:T5'
        );

        $sheet->setCellValue(
            'I5',
            'RECEIVE DETAIL'
        );


        $sheet->mergeCells(
            'U5:AD5'
        );

        $sheet->setCellValue(
            'U5',
            'SALES'
        );


        $sheet->mergeCells(
            'AE5:AL5'
        );

        $sheet->setCellValue(
            'AE5',
            'SAVING'
        );


        $sheet->getStyle(
            'A5:AL5'
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

            'RECEIVE',
            'TANGGAL',
            'PLANT',
            'PO',
            'SUPPLIER',
            'NOTA',
            'REF NO',
            'STATUS RECEIVE',

            'CUSTOMER',
            'MATERIAL',
            'PO TYPE',
            'QTY',
            'WEIGHT',
            'AVG BW',
            'SHRINK QTY',
            'SHRINK WEIGHT',
            'PRICE',
            'DISCOUNT',
            'TOTAL',
            'EXTRA',

            'SALES NO',
            'SALES DATE',
            'AMOUNT',
            'MODAL',
            'BIAYA',
            'DP',
            'DISCOUNT',
            'ROUNDING',
            'REMAINING',
            'STATUS',

            'SAVING NO',
            'AMOUNT',
            'BASIS',
            'RATE',
            'TOTAL QTY',
            'TOTAL WEIGHT',
            'REMAINING',
            'STATUS'

        ];


        $column = 1;


        foreach($headers as $header){

            $cell =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $column
                )
                . '6';


            $sheet->setCellValue(
                $cell,
                $header
            );


            $column++;

        }


        $sheet->getStyle(
            'A6:AL6'
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

        $row = 7;


        $totalQty = 0;
        $totalWeight = 0;
        $totalShrinkQty = 0;
        $totalShrinkWeight = 0;
        $totalReceive = 0;


        /*
        |--------------------------------------------------------------------------
        | GROUP BY RECEIVE
        |--------------------------------------------------------------------------
        */

        $grouped = [];


        foreach($rows as $r){

            $key =
                $r->RECEIVE
                . '|'
                .
                $r->PLANT;


            if(!isset($grouped[$key])){

                $grouped[$key] = [];

            }


            $grouped[$key][] = $r;

        }


        /*
        |--------------------------------------------------------------------------
        | LOOP RECEIVE
        |--------------------------------------------------------------------------
        */

        foreach($grouped as $group){

            $groupStartRow = $row;

            $groupCount =
                count($group);


            foreach($group as $index => $r){

                /*
                |--------------------------------------------------------------------------
                | RECEIVE INFORMATION
                |--------------------------------------------------------------------------
                */

                if($index === 0){

                    $sheet->setCellValue(
                        "A{$row}",
                        $r->RECEIVE ?: '-'
                    );

                    $sheet->setCellValue(
                        "B{$row}",
                        !empty($r->RECEIVE_DATE)
                            ? date(
                                'd F Y',
                                strtotime(
                                    $r->RECEIVE_DATE
                                )
                            )
                            : '-'
                    );

                    $sheet->setCellValue(
                        "C{$row}",
                        $r->PLANT_NAME
                            ?: $r->PLANT
                    );

                    $sheet->setCellValue(
                        "D{$row}",
                        $r->PO
                            ?: 'DIRECT RECEIVE'
                    );

                    $sheet->setCellValue(
                        "E{$row}",
                        trim(
                            ($r->SUPPLIER ?: '')
                            . ' - '
                            . ($r->SUPPLIER_NAME ?: '')
                        )
                    );

                    $sheet->setCellValue(
                        "F{$row}",
                        $r->NOTA ?: '-'
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        $r->NO_REF ?: '-'
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        $r->STATUS_RECEIVE ?: '-'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | RECEIVE DETAIL
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "I{$row}",
                    trim(
                        ($r->CUSTOMER ?: '')
                        . ' - '
                        . ($r->CUSTOMER_NAME ?: '')
                    )
                );


                $sheet->setCellValue(
                    "J{$row}",
                    trim(
                        ($r->MATERIAL ?: '')
                        . ' - '
                        . ($r->MATERIAL_NAME ?: '')
                    )
                );


                $sheet->setCellValue(
                    "K{$row}",
                    $r->PO_TYPE_NAME
                        ?: $r->PO_TYPE
                        ?: '-'
                );


                $sheet->setCellValue(
                    "L{$row}",
                    (float)(
                        $r->JUMLAH ?: 0
                    )
                );


                $sheet->setCellValue(
                    "M{$row}",
                    (float)(
                        $r->BERAT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "N{$row}",
                    (float)(
                        $r->AVG_BW ?: 0
                    )
                );


                $sheet->setCellValue(
                    "O{$row}",
                    (float)(
                        $r->SUSUT_JUMLAH ?: 0
                    )
                );


                $sheet->setCellValue(
                    "P{$row}",
                    (float)(
                        $r->SUSUT_BERAT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "Q{$row}",
                    (float)(
                        $r->HARGA ?: 0
                    )
                );


                $sheet->setCellValue(
                    "R{$row}",
                    (float)(
                        $r->DISCOUNT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "S{$row}",
                    (float)(
                        $r->TOTAL ?: 0
                    )
                );


                $sheet->setCellValue(
                    "T{$row}",
                    (int)$r->IS_EXTRA === 1
                        ? 'EXTRA'
                        : '-'
                );


                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "U{$row}",
                    $r->SALES_REPORT_NO ?: '-'
                );


                $sheet->setCellValue(
                    "V{$row}",
                    !empty($r->SALES_REPORT_DATE)
                        ? date(
                            'd F Y',
                            strtotime(
                                $r->SALES_REPORT_DATE
                            )
                        )
                        : '-'
                );


                $sheet->setCellValue(
                    "W{$row}",
                    (float)(
                        $r->SALES_AMOUNT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "X{$row}",
                    (float)(
                        $r->SALES_MODAL ?: 0
                    )
                );


                $sheet->setCellValue(
                    "Y{$row}",
                    (float)(
                        $r->SALES_BIAYA ?: 0
                    )
                );


                $sheet->setCellValue(
                    "Z{$row}",
                    (float)(
                        $r->SALES_DP ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AA{$row}",
                    (float)(
                        $r->SALES_DISCOUNT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AB{$row}",
                    (float)(
                        $r->SALES_ROUNDING ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AC{$row}",
                    (float)(
                        $r->SALES_REMAIN ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AD{$row}",
                    $r->SALES_STATUS ?: '-'
                );


                /*
                |--------------------------------------------------------------------------
                | SAVING
                |--------------------------------------------------------------------------
                */

                $sheet->setCellValue(
                    "AE{$row}",
                    $r->SAVING_NO_LIST ?: '-'
                );


                $sheet->setCellValue(
                    "AF{$row}",
                    (float)(
                        $r->SAVING_AMOUNT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AG{$row}",
                    $r->SAVING_BASIS ?: '-'
                );


                $sheet->setCellValue(
                    "AH{$row}",
                    (float)(
                        $r->SAVING_RATE ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AI{$row}",
                    (float)(
                        $r->SAVING_QTY ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AJ{$row}",
                    (float)(
                        $r->SAVING_WEIGHT ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AK{$row}",
                    (float)(
                        $r->SAVING_REMAIN ?: 0
                    )
                );


                $sheet->setCellValue(
                    "AL{$row}",
                    $r->SAVING_STATUS ?: '-'
                );


                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                $totalQty +=
                    (float)(
                        $r->JUMLAH ?: 0
                    );

                $totalWeight +=
                    (float)(
                        $r->BERAT ?: 0
                    );

                $totalShrinkQty +=
                    (float)(
                        $r->SUSUT_JUMLAH ?: 0
                    );

                $totalShrinkWeight +=
                    (float)(
                        $r->SUSUT_BERAT ?: 0
                    );

                $totalReceive +=
                    (float)(
                        $r->TOTAL ?: 0
                    );


                $row++;

            }


            $groupEndRow =
                $row - 1;


            /*
            |--------------------------------------------------------------------------
            | MERGE RECEIVE INFORMATION
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
                    'G',
                    'H'
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
                "A{$groupStartRow}:AL{$groupEndRow}"
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
            | SEPARATOR RECEIVE
            |--------------------------------------------------------------------------
            */

            $sheet->getStyle(
                "A{$groupStartRow}:AL{$groupStartRow}"
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
            "A{$row}:K{$row}"
        );

        $sheet->setCellValue(
            "A{$row}",
            'GRAND TOTAL'
        );


        $sheet->setCellValue(
            "L{$row}",
            $totalQty
        );


        $sheet->setCellValue(
            "M{$row}",
            $totalWeight
        );


        $sheet->setCellValue(
            "O{$row}",
            $totalShrinkQty
        );


        $sheet->setCellValue(
            "P{$row}",
            $totalShrinkWeight
        );


        $sheet->setCellValue(
            "S{$row}",
            $totalReceive
        );


        $sheet->getStyle(
            "A{$row}:AL{$row}"
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
            "L7:P{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.00'
            );


        $sheet->getStyle(
            "Q7:S{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        $sheet->getStyle(
            "W7:AC{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0'
            );


        $sheet->getStyle(
            "AH7:AK{$row}"
        )
            ->getNumberFormat()
            ->setFormatCode(
                '#,##0.00'
            );


        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "L7:S{$row}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        $sheet->getStyle(
            "W7:AC{$row}"
        )
            ->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
            );


        $sheet->getStyle(
            "AH7:AK{$row}"
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
            'B' => 15,
            'C' => 13,
            'D' => 17,
            'E' => 25,
            'F' => 18,
            'G' => 16,
            'H' => 16,

            'I' => 24,
            'J' => 24,
            'K' => 14,
            'L' => 12,
            'M' => 14,
            'N' => 12,
            'O' => 14,
            'P' => 16,
            'Q' => 14,
            'R' => 14,
            'S' => 15,
            'T' => 12,

            'U' => 18,
            'V' => 15,
            'W' => 15,
            'X' => 15,
            'Y' => 15,
            'Z' => 15,
            'AA' => 15,
            'AB' => 15,
            'AC' => 15,
            'AD' => 14,

            'AE' => 18,
            'AF' => 15,
            'AG' => 15,
            'AH' => 12,
            'AI' => 14,
            'AJ' => 16,
            'AK' => 15,
            'AL' => 14

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
            ->getRowDimension(5)
            ->setRowHeight(22);

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
        | AUTOFILTER
        |--------------------------------------------------------------------------
        */

        $sheet->setAutoFilter(
            "A6:AL{$row}"
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
            ->setFitToWidth(1);


        $sheet->getPageSetup()
            ->setFitToHeight(0);


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
            'Content-Disposition: attachment; filename="REPORT_RECEIVE.xlsx"'
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

    public function export_pdf_receive()
    {
        $filters = [

            'plant' => $this->input
                ->get('plant', true),

            'supplier' => $this->input
                ->get('supplier', true),

            'receive' => $this->input
                ->get('receive', true),

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

        $rows =
            $this->ReportInventory_model
                ->get_receive_report(
                    0,
                    0,
                    $filters,
                    'RECEIVE_DATE',
                    'DESC'
                );


        if(empty($rows)){

            show_error(
                'No data found for export'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | GROUP BY RECEIVE + PLANT
        |--------------------------------------------------------------------------
        */

        $grouped = [];


        foreach($rows as $r){

            $key =
                $r->RECEIVE
                . '|'
                .
                $r->PLANT;


            if(!isset($grouped[$key])){

                $grouped[$key] = [];

            }


            $grouped[$key][] = $r;

        }


        /*
        |--------------------------------------------------------------------------
        | HELPER
        |--------------------------------------------------------------------------
        */

        $esc = function($value){

            return htmlspecialchars(
                (string)(
                    $value ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            );

        };


        $money = function($value){

            return number_format(
                (float)(
                    $value ?: 0
                ),
                0,
                ',',
                '.'
            );

        };


        $decimal = function($value){

            return number_format(
                (float)(
                    $value ?: 0
                ),
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
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logo =
            FCPATH . 'assets/img/abc-trans.png';


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


        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |
        | IMPORTANT:
        | 1 detail Receive bisa muncul beberapa kali karena
        | memiliki beberapa Sales.
        |
        | Oleh karena itu total dihitung berdasarkan:
        | RECEIVE + PLANT + SEQ_NO
        |
        |--------------------------------------------------------------------------
        */

        $uniqueDetails = [];


        foreach($rows as $r){

            $detailKey =
                $r->RECEIVE
                . '|'
                .
                $r->PLANT
                . '|'
                .
                $r->SEQ_NO;


            if(!isset($uniqueDetails[$detailKey])){

                $uniqueDetails[$detailKey] = $r;

            }

        }


        $totalQty = 0;

        $totalWeight = 0;

        $totalShrinkQty = 0;

        $totalShrinkWeight = 0;

        $totalReceive = 0;


        foreach($uniqueDetails as $r){

            $totalQty +=
                (float)(
                    $r->JUMLAH ?: 0
                );


            $totalWeight +=
                (float)(
                    $r->BERAT ?: 0
                );


            $totalShrinkQty +=
                (float)(
                    $r->SUSUT_JUMLAH ?: 0
                );


            $totalShrinkWeight +=
                (float)(
                    $r->SUSUT_BERAT ?: 0
                );


            $totalReceive +=
                (float)(
                    $r->TOTAL ?: 0
                );

        }


        /*
        |--------------------------------------------------------------------------
        | PCRE LIMIT
        |--------------------------------------------------------------------------
        */

        ini_set(
            'pcre.backtrack_limit',
            '10000000'
        );


        ini_set(
            'pcre.recursion_limit',
            '10000000'
        );


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $mpdf =
            new \Mpdf\Mpdf([

                'format' => 'A3',

                'orientation' => 'L',

                'margin_left' => 6,

                'margin_right' => 6,

                'margin_top' => 8,

                'margin_bottom' => 10,

                'margin_header' => 4,

                'margin_footer' => 5

            ]);


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        $mpdf->SetHTMLFooter('

            <div
                style="
                    text-align:right;
                    font-size:7px;
                    color:#666;
                "
            >

                Page {PAGENO} of {nbpg}

            </div>

        ');


        /*
        |--------------------------------------------------------------------------
        | HTML HEADER
        |--------------------------------------------------------------------------
        */

        $htmlHeader = '

            <style>

                @page{

                    margin:6mm 6mm 8mm 6mm;

                }


                body{

                    font-family:sans-serif;

                    font-size:6px;

                    color:#222;

                }


                .title{

                    font-size:15px;

                    font-weight:bold;

                    text-align:center;

                }


                .subtitle{

                    font-size:9px;

                    text-align:center;

                    margin-top:2px;

                }


                .header-table{

                    width:100%;

                    border-collapse:collapse;

                    margin-bottom:8px;

                }


                .header-table td{

                    border:none;

                }


                .report-table{

                    width:100%;

                    border-collapse:collapse;

                    table-layout:fixed;

                }


                .report-table th{

                    border:0.3mm solid #b9c3ce;

                    background:#eef2f7;

                    font-weight:bold;

                    text-align:center;

                    vertical-align:middle;

                    padding:3px 2px;

                    font-size:5.8px;

                }


                .report-table .group-header{

                    background:#dce6f1;

                    font-size:6.5px;

                }


                .report-table td{

                    border:0.25mm solid #d5dde6;

                    padding:3px 2px;

                    vertical-align:middle;

                    font-size:5.8px;

                    word-wrap:break-word;

                }


                .report-table .center{

                    text-align:center;

                }


                .report-table .right{

                    text-align:right;

                }


                .report-table .receive{

                    font-weight:bold;

                }


                .report-table .group-start td{

                    border-top:0.7mm solid #94a3b8;

                }


                .report-table .grand-total{

                    background:#e9eef4;

                    font-weight:bold;

                }

            </style>


            <table class="header-table">

                <tr>

                    <td width="60">

        ';


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        if(file_exists($logo)){

            $logoPath =
                str_replace(
                    '\\',
                    '/',
                    $logo
                );


            $htmlHeader .= '

                        <img
                            src="file:///'.$logoPath.'"
                            height="45"
                        >

            ';

        }


        $htmlHeader .= '

                    </td>


                    <td>

                        <div class="title">

                            PT. Abadi Bersama Cerah

                        </div>


                        <div class="subtitle">

                            RECEIVE REPORT

                        </div>


                        <div class="subtitle">

                            Period : '.$esc($period).'

                        </div>

                    </td>


                    <td width="60"></td>

                </tr>

            </table>


            <table class="report-table">

                <thead>

                    <tr>

                        <th
                            colspan="8"
                            class="group-header"
                        >
                            RECEIVE INFORMATION
                        </th>


                        <th
                            colspan="12"
                            class="group-header"
                        >
                            RECEIVE DETAIL
                        </th>


                        <th
                            colspan="10"
                            class="group-header"
                        >
                            SALES
                        </th>


                        <th
                            colspan="8"
                            class="group-header"
                        >
                            SAVING
                        </th>

                    </tr>


                    <tr>

                        <th>RECEIVE</th>

                        <th>TANGGAL</th>

                        <th>PLANT</th>

                        <th>PO</th>

                        <th>SUPPLIER</th>

                        <th>NOTA</th>

                        <th>REF NO</th>

                        <th>STATUS</th>


                        <th>CUSTOMER</th>

                        <th>MATERIAL</th>

                        <th>PO TYPE</th>

                        <th>QTY</th>

                        <th>WEIGHT</th>

                        <th>AVG BW</th>

                        <th>SHRINK QTY</th>

                        <th>SHRINK WEIGHT</th>

                        <th>PRICE</th>

                        <th>DISCOUNT</th>

                        <th>TOTAL</th>

                        <th>EXTRA</th>


                        <th>SALES NO</th>

                        <th>SALES DATE</th>

                        <th>AMOUNT</th>

                        <th>MODAL</th>

                        <th>BIAYA</th>

                        <th>DP</th>

                        <th>DISCOUNT</th>

                        <th>ROUNDING</th>

                        <th>REMAINING</th>

                        <th>STATUS</th>


                        <th>SAVING NO</th>

                        <th>AMOUNT</th>

                        <th>BASIS</th>

                        <th>RATE</th>

                        <th>TOTAL QTY</th>

                        <th>TOTAL WEIGHT</th>

                        <th>REMAINING</th>

                        <th>STATUS</th>

                    </tr>

                </thead>

                <tbody>

        ';


        /*
        |--------------------------------------------------------------------------
        | GROUP BODY
        |--------------------------------------------------------------------------
        */

        $groupHtmlList = [];


        foreach($grouped as $group){

            $rowspan =
                count($group);


            $groupHtml = '';


            foreach($group as $index => $r){

                $groupClass =
                    $index === 0
                        ? 'group-start'
                        : '';


                $groupHtml .= '

                    <tr
                        class="'.$groupClass.'"
                    >

                ';


                /*
                |--------------------------------------------------------------------------
                | RECEIVE INFORMATION
                |--------------------------------------------------------------------------
                */

                if($index === 0){

                    $groupHtml .= '

                        <td
                            rowspan="'.$rowspan.'"
                            class="receive"
                        >
                            '.$esc(
                                $r->RECEIVE
                                    ?: '-'
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                $date(
                                    $r->RECEIVE_DATE
                                )
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                $r->PLANT_NAME
                                    ?: $r->PLANT
                                    ?: '-'
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                $r->PO
                                    ?: 'DIRECT RECEIVE'
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                trim(
                                    ($r->SUPPLIER ?: '')
                                    . ' - '
                                    . ($r->SUPPLIER_NAME ?: '')
                                )
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                $r->NOTA ?: '-'
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                        >
                            '.$esc(
                                $r->NO_REF ?: '-'
                            ).'
                        </td>


                        <td
                            rowspan="'.$rowspan.'"
                            class="center"
                        >
                            '.$esc(
                                $r->STATUS_RECEIVE
                                    ?: '-'
                            ).'
                        </td>

                    ';

                }


                /*
                |--------------------------------------------------------------------------
                | RECEIVE DETAIL
                |--------------------------------------------------------------------------
                */

                $groupHtml .= '

                        <td>
                            '.$esc(
                                trim(
                                    ($r->CUSTOMER ?: '')
                                    . ' - '
                                    . ($r->CUSTOMER_NAME ?: '')
                                )
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                trim(
                                    ($r->MATERIAL ?: '')
                                    . ' - '
                                    . ($r->MATERIAL_NAME ?: '')
                                )
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                $r->PO_TYPE_NAME
                                    ?: $r->PO_TYPE
                                    ?: '-'
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->JUMLAH
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->BERAT
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->AVG_BW
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->SUSUT_JUMLAH
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->SUSUT_BERAT
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->HARGA
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->DISCOUNT
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->TOTAL
                            ).'
                        </td>


                        <td class="center">
                            '.
                            (
                                (int)$r->IS_EXTRA === 1
                                    ? 'EXTRA'
                                    : '-'
                            )
                            .'
                        </td>


                        <!-- SALES -->

                        <td>
                            '.$esc(
                                $r->SALES_REPORT_NO
                                    ?: '-'
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                $date(
                                    $r->SALES_REPORT_DATE
                                )
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_AMOUNT
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_MODAL
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_BIAYA
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_DP
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_DISCOUNT
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_ROUNDING
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SALES_REMAIN
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                $r->SALES_STATUS
                                    ?: '-'
                            ).'
                        </td>


                        <!-- SAVING -->

                        <td>
                            '.$esc(
                                $r->SAVING_NO_LIST
                                    ?: '-'
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SAVING_AMOUNT
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                $r->SAVING_BASIS
                                    ?: '-'
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->SAVING_RATE
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->SAVING_QTY
                            ).'
                        </td>


                        <td class="right">
                            '.$decimal(
                                $r->SAVING_WEIGHT
                            ).'
                        </td>


                        <td class="right">
                            '.$money(
                                $r->SAVING_REMAIN
                            ).'
                        </td>


                        <td>
                            '.$esc(
                                $r->SAVING_STATUS
                                    ?: '-'
                            ).'
                        </td>

                    </tr>

                ';

            }


            $groupHtmlList[] =
                $groupHtml;

        }


        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $grandTotalHtml = '

            <tr class="grand-total">

                <td colspan="11">
                    GRAND TOTAL
                </td>


                <td class="right">
                    '.$decimal(
                        $totalQty
                    ).'
                </td>


                <td class="right">
                    '.$decimal(
                        $totalWeight
                    ).'
                </td>


                <td></td>


                <td class="right">
                    '.$decimal(
                        $totalShrinkQty
                    ).'
                </td>


                <td class="right">
                    '.$decimal(
                        $totalShrinkWeight
                    ).'
                </td>


                <td></td>


                <td></td>


                <td class="right">
                    '.$money(
                        $totalReceive
                    ).'
                </td>


                <td></td>


                <td colspan="18"></td>

            </tr>

        ';


        /*
        |--------------------------------------------------------------------------
        | HTML END
        |--------------------------------------------------------------------------
        */

        $htmlEnd = '

                </tbody>

            </table>

        ';


        /*
        |--------------------------------------------------------------------------
        | WRITE HTML IN CHUNKS
        |--------------------------------------------------------------------------
        */

        $maxChunkSize = 200000;

        $currentChunk = '';

        $groupCount =
            count(
                $groupHtmlList
            );


        foreach(
            $groupHtmlList as $index => $groupHtml
        ){

            /*
            |--------------------------------------------------------------------------
            | Jika chunk terlalu besar,
            | tulis terlebih dahulu
            |--------------------------------------------------------------------------
            */

            if(
                !empty($currentChunk)
                &&
                (
                    strlen($currentChunk)
                    +
                    strlen($groupHtml)
                )
                >
                $maxChunkSize
            ){

                $mpdf->WriteHTML(

                    $htmlHeader
                    .
                    $currentChunk
                    .
                    $htmlEnd

                );


                $currentChunk = '';

            }


            $currentChunk .=
                $groupHtml;


            /*
            |--------------------------------------------------------------------------
            | GROUP TERAKHIR
            |--------------------------------------------------------------------------
            */

            if(
                $index === $groupCount - 1
            ){

                $mpdf->WriteHTML(

                    $htmlHeader
                    .
                    $currentChunk
                    .
                    $grandTotalHtml
                    .
                    $htmlEnd

                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        $mpdf->Output(
            'REPORT_RECEIVE.pdf',
            'I'
        );


        exit;
    }

    public function load_culling()
    {
        ob_clean();

        header('Content-Type: application/json');

        $page =
            (int)$this->input
                ->get('page');

        $limit =
            (int)$this->input
                ->get('limit');

        $page =
            $page > 0
                ? $page
                : 1;

        $limit =
            $limit > 0
                ? $limit
                : 10;

        $order =
            $this->input
                ->get(
                    'order',
                    true
                ) ?: 'ymd';

        $dirInput =
            $this->input
                ->get(
                    'dir',
                    true
                ) ?? 'DESC';

        $dir =
            strtoupper($dirInput)
                === 'ASC'
                    ? 'ASC'
                    : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'plant' =>
                $this->input
                    ->get(
                        'plant',
                        true
                    ),

            'class_out' =>
                $this->input
                    ->get(
                        'class_out',
                        true
                    ),

            'search' =>
                $this->input
                    ->get(
                        'search',
                        true
                    ),

            'date_from' =>
                $this->input
                    ->get(
                        'date_from',
                        true
                    ),

            'date_to' =>
                $this->input
                    ->get(
                        'date_to',
                        true
                    )

        ];

        $start =
            ($page - 1)
            * $limit;

        $rows =
            $this
                ->ReportInventory_model
                ->get_culling_report(

                    $limit,
                    $start,
                    $filters,
                    $order,
                    $dir

                );

        $totalRows =
            $this
                ->ReportInventory_model
                ->count_culling_report(
                    $filters
                );

        $summary =
            $this
                ->ReportInventory_model
                ->get_culling_summary(
                    $filters
                );

        $pages =
            ceil(
                $totalRows
                / $limit
            );

        $pagination =
            $this
                ->build_pagination(

                    $pages,
                    $page,
                    'ajax'

                );

        echo json_encode([

            'status' => true,

            'rows' => $rows,

            'total' =>
                (int)$totalRows,

            'summary' =>
                $summary,

            'pagination' =>
                $pagination,

            'page' =>
                $page

        ]);

        exit;
    }

    public function export_excel_culling()
    {
        $filters = [

            'plant' =>
                $this->input->get(
                    'plant',
                    true
                ),

            'class_out' =>
                $this->input->get(
                    'class_out',
                    true
                ),

            'search' =>
                $this->input->get(
                    'search',
                    true
                ),

            'date_from' =>
                $this->input->get(
                    'date_from',
                    true
                ),

            'date_to' =>
                $this->input->get(
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
            $this
                ->ReportInventory_model
                ->get_culling_report(

                    0,
                    0,
                    $filters,
                    'ymd',
                    'DESC'

                );

        if (empty($rows)) {

            show_error(
                'No data found for export'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SPREADSHEET
        |--------------------------------------------------------------------------
        */

        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet =
            $spreadsheet
                ->getActiveSheet();

        $sheet->setTitle(
            'Culling Report'
        );

        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoPath =
            FCPATH .
            'assets/img/abc-trans.png';

        if (file_exists($logoPath)) {

            $drawing =
                new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

            $drawing->setPath(
                $logoPath
            );

            $drawing->setCoordinates(
                'A1'
            );

            $drawing->setHeight(55);

            $drawing->setWorksheet(
                $sheet
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            'B1:F1'
        );

        $sheet->setCellValue(
            'B1',
            'PT. Abadi Bersama Cerah'
        );

        $sheet->getStyle('B1')
            ->getFont()
            ->setBold(true)
            ->setSize(18);

        $sheet->mergeCells(
            'B2:F2'
        );

        $sheet->setCellValue(
            'B2',
            'CULLING REPORT'
        );

        $sheet->getStyle('B2')
            ->getFont()
            ->setBold(true)
            ->setSize(13);

        if (
            !empty(
                $filters['date_from']
            ) &&
            !empty(
                $filters['date_to']
            )
        ) {

            $period =
                date(
                    'd M Y',
                    strtotime(
                        $filters['date_from']
                    )
                )
                .
                ' - '
                .
                date(
                    'd M Y',
                    strtotime(
                        $filters['date_to']
                    )
                );

            $sheet->mergeCells(
                'B3:F3'
            );

            $sheet->setCellValue(
                'B3',
                'Period : ' .
                $period
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TABLE HEADER
        |--------------------------------------------------------------------------
        */

        $row = 6;

        $headers = [

            'NO',
            'PLANT',
            'DATE',
            'CLASS OUT',
            'JUMLAH',
            'BERAT',
            'CREATED BY',
            'REMARK'

        ];

        $col = 'A';

        foreach ($headers as $h) {

            $sheet->setCellValue(
                $col . $row,
                $h
            );

            $col++;
        }

        $sheet->getStyle(
            "A{$row}:H{$row}"
        )->applyFromArray([

            'font' => [
                'bold' => true
            ],

            'fill' => [

                'fillType' =>
                    \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                'startColor' => [
                    'rgb' => '0F4C81'
                ]

            ],

            'font' => [

                'bold' => true,

                'color' => [
                    'rgb' => 'FFFFFF'
                ]

            ]

        ]);

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $row++;

        $no = 1;

        $grandJumlah = 0;
        $grandBerat = 0;

        foreach ($rows as $r) {

            $sheet->setCellValue(
                "A{$row}",
                $no++
            );

            $sheet->setCellValue(
                "B{$row}",
                $r->PLANT_NAME
            );

            $sheet->setCellValue(
                "C{$row}",
                strtoupper(
                    date(
                        'd F Y',
                        strtotime(
                            $r->ymd
                        )
                    )
                )
            );

            $sheet->setCellValue(
                "D{$row}",
                $r->CLASS_OUT_NAME
            );

            $sheet->setCellValue(
                "E{$row}",
                (float)$r->jumlah
            );

            $sheet->setCellValue(
                "F{$row}",
                (float)$r->berat
            );

            $sheet->setCellValue(
                "G{$row}",
                $r->CREATED_BY
            );

            $sheet->setCellValue(
                "H{$row}",
                $r->remark
            );

            $grandJumlah +=
                (float)$r->jumlah;

            $grandBerat +=
                (float)$r->berat;

            $row++;
        }

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells(
            "A{$row}:D{$row}"
        );

        $sheet->setCellValue(
            "A{$row}",
            'GRAND TOTAL'
        );

        $sheet->setCellValue(
            "E{$row}",
            $grandJumlah
        );

        $sheet->setCellValue(
            "F{$row}",
            $grandBerat
        );

        $sheet->getStyle(
            "A{$row}:H{$row}"
        )->getFont()
            ->setBold(true);

        /*
        |--------------------------------------------------------------------------
        | NUMBER FORMAT
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle(
            "E7:F{$row}"
        )
        ->getNumberFormat()
        ->setFormatCode(
            '#,##0.00'
        );

        /*
        |--------------------------------------------------------------------------
        | AUTO SIZE
        |--------------------------------------------------------------------------
        */

        foreach (
            range('A', 'H')
            as $col
        ) {

            $sheet
                ->getColumnDimension(
                    $col
                )
                ->setAutoSize(
                    true
                );
        }

        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment; filename="REPORT_CULLING.xlsx"'
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

    public function export_pdf_culling()
    {
        $filters = [

            'plant' =>
                $this->input->get(
                    'plant',
                    true
                ),

            'class_out' =>
                $this->input->get(
                    'class_out',
                    true
                ),

            'search' =>
                $this->input->get(
                    'search',
                    true
                ),

            'date_from' =>
                $this->input->get(
                    'date_from',
                    true
                ),

            'date_to' =>
                $this->input->get(
                    'date_to',
                    true
                )

        ];

        $rows =
            $this
                ->ReportInventory_model
                ->get_culling_report(
                    0,
                    0,
                    $filters,
                    'ymd',
                    'DESC'
                );

        if (empty($rows)) {

            show_error(
                'No data found for export'
            );
        }

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

        $period = '';

        if (
            !empty(
                $filters['date_from']
            ) &&
            !empty(
                $filters['date_to']
            )
        ) {

            $period =
                date(
                    'd M Y',
                    strtotime(
                        $filters['date_from']
                    )
                )
                .
                ' - '
                .
                date(
                    'd M Y',
                    strtotime(
                        $filters['date_to']
                    )
                );
        }

        $html = '

        <style>

            body{
                font-family:sans-serif;
                font-size:10px;
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th,td{
                border:1px solid #d9dee5;
                padding:6px;
            }

            th{
                background:#eef2f7;
            }

            .right{
                text-align:right;
            }

            .center{
                text-align:center;
            }

            .total{
                background:#f8fafc;
                font-weight:bold;
            }

        </style>

        <table border="0">

            <tr>

                <td width="70" style="border:none;">'

                .
                (
                    $logo64
                    ? '<img src="'.$logo64.'" height="55">'
                    : ''
                )

                .'

                </td>

                <td style="border:none;">

                    <div
                        style="
                            text-align:center;
                            font-size:18px;
                            font-weight:bold;
                        ">

                        PT. Abadi Bersama Cerah

                    </div>

                    <div
                        style="
                            text-align:center;
                        ">

                        CULLING REPORT

                    </div>

                    <div
                        style="
                            text-align:center;
                        ">

                        '.$period.'

                    </div>

                </td>

            </tr>

        </table>

        <br>

        <table>

            <thead>

                <tr>

                    <th>NO</th>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>CLASS OUT</th>
                    <th>JUMLAH</th>
                    <th>BERAT</th>
                    <th>CREATED BY</th>
                    <th>REMARK</th>

                </tr>

            </thead>

            <tbody>
        ';

        $no = 1;

        $grandJumlah = 0;
        $grandBerat = 0;

        foreach ($rows as $r) {

            $grandJumlah +=
                (float)$r->jumlah;

            $grandBerat +=
                (float)$r->berat;

            $html .= '

            <tr>

                <td class="center">
                    '.$no++.'
                </td>

                <td>
                    '.$r->PLANT_NAME.'
                </td>

                <td>
                    '.strtoupper(
                        date(
                            'd F Y',
                            strtotime(
                                $r->ymd
                            )
                        )
                    ).'
                </td>

                <td>
                    '.$r->CLASS_OUT_NAME.'
                </td>

                <td class="right">
                    '.number_format(
                        $r->jumlah,
                        2,
                        ',',
                        '.'
                    ).'
                </td>

                <td class="right">
                    '.number_format(
                        $r->berat,
                        2,
                        ',',
                        '.'
                    ).'
                </td>

                <td>
                    '.$r->CREATED_BY.'
                </td>

                <td>
                    '.($r->remark ?: '-').'
                </td>

            </tr>

            ';
        }

        $html .= '

            <tr class="total">

                <td colspan="4">
                    GRAND TOTAL
                </td>

                <td class="right">
                    '.number_format(
                        $grandJumlah,
                        2,
                        ',',
                        '.'
                    ).'
                </td>

                <td class="right">
                    '.number_format(
                        $grandBerat,
                        2,
                        ',',
                        '.'
                    ).'
                </td>

                <td colspan="2"></td>

            </tr>

            </tbody>

        </table>
        ';

        $mpdf =
            new \Mpdf\Mpdf([
                'orientation' => 'L'
            ]);

        $mpdf->WriteHTML(
            $html
        );

        $mpdf->Output(
            'REPORT_CULLING.pdf',
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
