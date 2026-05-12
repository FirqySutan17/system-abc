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

        $page  = (int) $this->input->get('page');
        $limit = (int) $this->input->get('limit');

        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : 100; // report card style → default besar

        $date = $this->convert_date(
            $this->input->get('date', TRUE)
        );

        $filters = [
            'role_id'   => (int) $this->session->userdata('role_id'),
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'po'        => $this->input->get('po', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
        ];

        // non-admin force plant
        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | ROWS
        |--------------------------------------------------------------------------
        */
        $rows = $this->ReportInventory_model->get_po_report(
            $limit,
            $start,
            $filters,
            'PO_DATE',
            'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | ALL ROWS (summary + grand)
        |--------------------------------------------------------------------------
        */
        $allRows = $this->ReportInventory_model->get_po_report(
            0,
            0,
            $filters,
            'PO_DATE',
            'DESC'
        );

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */
        $uniquePO       = [];
        $uniqueSupplier = [];
        $uniqueCustomer = [];

        $sumJumlah = 0;
        $sumBerat  = 0;
        $sumTotal  = 0;

        foreach ($allRows as $r) {

            $uniquePO[$r->PO . '|' . $r->PLANT] = true;

            if (!empty($r->SUPPLIER)) {
                $uniqueSupplier[$r->SUPPLIER] = true;
            }

            if (!empty($r->CUSTOMER)) {
                $uniqueCustomer[$r->CUSTOMER] = true;
            }

            $sumJumlah += (float) $r->JUMLAH;
            $sumBerat  += (float) $r->BERAT;
            $sumTotal  += (float) $r->TOTAL;
        }

        $summary = [
            'total_po'       => count($uniquePO),
            'total_supplier' => count($uniqueSupplier),
            'total_customer' => count($uniqueCustomer),
            'total_jumlah'   => number_format($sumJumlah, 2, '.', ','),
            'total_berat'    => number_format($sumBerat, 2, '.', ','),
            'total_amount'   => $sumTotal
        ];

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */
        $grand = [
            'jumlah' => number_format($sumJumlah, 2, '.', ','),
            'berat'  => number_format($sumBerat, 2, '.', ','),
            'total'  => $sumTotal
        ];

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        echo json_encode([
            'status'  => true,
            'rows'    => $rows,
            'summary' => $summary,
            'grand'   => $grand,
            'total'   => count($uniquePO)
        ]);
    }

    public function export_excel_po()
    {
        $filters = [
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'po'        => $this->input->get('po', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
        ];

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

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PO . '|' . $r->PLANT;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'PO'            => $r->PO,
                    'PLANT_NAME'    => $r->PLANT_NAME,
                    'PO_DATE'       => $r->PO_DATE,
                    'PO_NAME'       => $r->PO_NAME,
                    'SUPPLIER'      => $r->SUPPLIER,
                    'SUPPLIER_NAME' => $r->SUPPLIER_NAME,
                    'STATUS'        => $r->STATUS,
                    'REMARK'        => $r->REMARK,
                    'DETAIL'        => []
                ];
            }

            $grouped[$key]['DETAIL'][] = $r;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PO Report');

        /*
        |--------------------------------------------------------------------------
        | HEADER
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

        $sheet->mergeCells('B1:H1');
        $sheet->setCellValue('B1', 'PT. Abadi Bersama Cerah');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(18);

        $sheet->mergeCells('B2:H2');
        $sheet->setCellValue('B2', 'PURCHASE ORDER REPORT');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(13);

        $period = date('d M Y', strtotime($filters['date_from'])) .
            ' - ' .
            date('d M Y', strtotime($filters['date_to']));

        $sheet->mergeCells('B3:H3');
        $sheet->setCellValue('B3', 'Period : ' . $period);
        $sheet->getStyle('B3')->getFont()->setItalic(true);

        $row = 6;

        $grandQty = 0;
        $grandWeight = 0;
        $grandTotal = 0;

        foreach ($grouped as $po) {

            /*
            |--------------------------------------------------------------------------
            | CARD HEADER
            |--------------------------------------------------------------------------
            */
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->setCellValue("A{$row}", '#' . $po['PO']);
            $sheet->setCellValue("G{$row}", $po['STATUS'] ? 'RECEIVED' : 'OPEN');

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 11
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F4C81']
                ]
            ]);

            $row++;

            /*
            |--------------------------------------------------------------------------
            | META GRID (RAPI)
            |--------------------------------------------------------------------------
            */
            $metaStyle = [
                'font' => [
                    'bold' => true
                ]
            ];

            // row 1
            $sheet->setCellValue("A{$row}", 'PLANT');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $po['PLANT_NAME']);

            $sheet->setCellValue("E{$row}", 'SUPPLIER');
            $sheet->setCellValue("F{$row}", ':');
            $sheet->setCellValue("G{$row}", $po['SUPPLIER'] . ' - ' . $po['SUPPLIER_NAME']);

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("E{$row}")->applyFromArray($metaStyle);

            $row++;

            // row 2
            $sheet->setCellValue("A{$row}", 'PO DATE');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", strtoupper(date('d F Y', strtotime($po['PO_DATE']))));

            $sheet->setCellValue("E{$row}", 'PO TYPE');
            $sheet->setCellValue("F{$row}", ':');
            $sheet->setCellValue("G{$row}", $po['PO_NAME']);

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("E{$row}")->applyFromArray($metaStyle);

            $row++;

            // row 3 remark
            $sheet->setCellValue("A{$row}", 'REMARK');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->mergeCells("C{$row}:G{$row}");
            $sheet->setCellValue("C{$row}", !empty($po['REMARK']) ? $po['REMARK'] : '-');

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);

            $row += 2;

            /*
            |--------------------------------------------------------------------------
            | TABLE HEADER
            |--------------------------------------------------------------------------
            */
            $headers = ['CUSTOMER', 'MATERIAL', 'QTY', 'WEIGHT', 'PRICE', 'TOTAL'];

            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . $row, $h);
                $col++;
            }

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ]
            ]);

            $row++;

            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */
            $subQty = 0;
            $subWeight = 0;
            $subTotal = 0;

            foreach ($po['DETAIL'] as $d) {

                $sheet->setCellValue("A{$row}", $d->CUSTOMER . ' - ' . $d->CUSTOMER_NAME);
                $sheet->setCellValue("B{$row}", $d->MATERIAL . ' - ' . $d->MATERIAL_NAME);
                $sheet->setCellValue("C{$row}", (float)$d->JUMLAH);
                $sheet->setCellValue("D{$row}", (float)$d->BERAT);
                $sheet->setCellValue("E{$row}", (float)$d->HARGA);
                $sheet->setCellValue("F{$row}", (float)$d->TOTAL);

                $subQty += (float)$d->JUMLAH;
                $subWeight += (float)$d->BERAT;
                $subTotal += (float)$d->TOTAL;

                $row++;
            }

            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", 'SUBTOTAL');
            $sheet->setCellValue("C{$row}", $subQty);
            $sheet->setCellValue("D{$row}", $subWeight);
            $sheet->setCellValue("F{$row}", $subTotal);

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ]);

            $grandQty += $subQty;
            $grandWeight += $subWeight;
            $grandTotal += $subTotal;

            $row += 2;
        }

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'GRAND TOTAL');
        $sheet->setCellValue("C{$row}", $grandQty);
        $sheet->setCellValue("D{$row}", $grandWeight);
        $sheet->setCellValue("F{$row}", $grandTotal);

        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | FORMAT
        |--------------------------------------------------------------------------
        */
        $sheet->getStyle("C1:F{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(40);

        $sheet->freezePane('A7');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="REPORT_PO.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf_po()
    {
        $filters = [
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'po'        => $this->input->get('po', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
        ];

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

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PO . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        $logo = FCPATH . 'assets/img/abc-trans.png';
        $logo64 = file_exists($logo)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo))
            : '';

        $period = date('d M Y', strtotime($filters['date_from'])) .
            ' - ' .
            date('d M Y', strtotime($filters['date_to']));

        $html = '
        <style>
            body{
                font-family:sans-serif;
                font-size:10px;
                color:#222;
            }

            .head{
                margin-bottom:18px;
            }

            .title{
                text-align:center;
                font-weight:bold;
                font-size:18px;
            }

            .subtitle{
                text-align:center;
                margin-top:4px;
                font-size:11px;
            }

            .card{
                border:1px solid #dfe7ef;
                border-radius:10px;
                margin-bottom:20px;
                overflow:hidden;
            }

            .card-head{
                background:#0F4C81;
                color:#fff;
                padding:10px 14px;
                font-weight:bold;
                font-size:12px;
            }

            .meta{
                padding:12px 14px;
                background:#f8fafc;
            }

            .meta-table{
                width:100%;
                border-collapse:collapse;
            }

            .meta-table td{
                border:none;
                padding:3px 0;
                vertical-align:top;
                font-size:10px;
            }

            .meta-label{
                width:70px;
                font-weight:bold;
                white-space:nowrap;
            }

            .meta-sep{
                width:10px;
                text-align:center;
                font-weight:bold;
            }

            .meta-gap{
                width:30px;
            }

            .meta-value{
                font-weight:normal;
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th,td{
                border:1px solid #d9dee5;
                padding:6px;
                font-size:10px;
            }

            th{
                background:#eef2f7;
                font-weight:bold;
            }

            .right{
                text-align:right;
            }

            .subtotal{
                background:#f6f8fa;
                font-weight:bold;
            }
        </style>

        <div class="head">
            <table width="100%" border="0" style="border:none;">
                <tr>
                    <td width="70" style="border:none;">' .
                        ($logo64 ? '<img src="'.$logo64.'" height="55">' : '') .
                    '</td>
                    <td style="border:none;">
                        <div class="title">PT. Abadi Bersama Cerah</div>
                        <div class="subtitle">PURCHASE ORDER REPORT</div>
                        <div class="subtitle">Period : '.$period.'</div>
                    </td>
                </tr>
            </table>
        </div>
        ';

        foreach ($grouped as $group) {

            $h = $group[0];

            $html .= '
            <div class="card">

                <div class="card-head">
                    #'.$h->PO.' — '.($h->STATUS ? 'RECEIVED' : 'OPEN').'
                </div>

                <div class="meta">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">PLANT</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->PLANT_NAME.'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">SUPPLIER</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->SUPPLIER.' - '.$h->SUPPLIER_NAME.'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">PO DATE</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.strtoupper(date('d F Y', strtotime($h->PO_DATE))).'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">PO TYPE</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->PO_NAME.'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">REMARK</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value" colspan="5">'.(!empty($h->REMARK) ? $h->REMARK : '-').'</td>
                        </tr>
                    </table>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>CUSTOMER</th>
                            <th>MATERIAL</th>
                            <th>QTY</th>
                            <th>WEIGHT</th>
                            <th>PRICE</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $subQty = 0;
            $subWeight = 0;
            $subTotal = 0;

            foreach ($group as $d) {

                $subQty += (float)$d->JUMLAH;
                $subWeight += (float)$d->BERAT;
                $subTotal += (float)$d->TOTAL;

                $html .= '
                <tr>
                    <td>'.$d->CUSTOMER.' - '.$d->CUSTOMER_NAME.'</td>
                    <td>'.$d->MATERIAL.' - '.$d->MATERIAL_NAME.'</td>
                    <td class="right">'.number_format($d->JUMLAH,2,',','.').'</td>
                    <td class="right">'.number_format($d->BERAT,2,',','.').'</td>
                    <td class="right">'.number_format($d->HARGA,0,',','.').'</td>
                    <td class="right">'.number_format($d->TOTAL,0,',','.').'</td>
                </tr>';
            }

            $html .= '
                        <tr class="subtotal">
                            <td colspan="2">SUBTOTAL</td>
                            <td class="right">'.number_format($subQty,2,',','.').'</td>
                            <td class="right">'.number_format($subWeight,2,',','.').'</td>
                            <td></td>
                            <td class="right">'.number_format($subTotal,0,',','.').'</td>
                        </tr>
                    </tbody>
                </table>

            </div>';
        }

        $mpdf = new \Mpdf\Mpdf([
            'orientation'   => 'L',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 12
        ]);

        $mpdf->SetHTMLFooter('
            <div style="text-align:right;font-size:9px;color:#666;">
                Page {PAGENO} of {nbpg}
            </div>
        ');

        $mpdf->WriteHTML($html);
        $mpdf->Output('REPORT_PO.pdf', 'I');
        exit;
    }

    public function load_receive()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;
        $order = $this->input->get('order', true) ?: 'RECEIVE_DATE';

        $dirInput = $this->input->get('dir', true) ?? 'DESC';
        $dir = strtoupper($dirInput) === 'ASC' ? 'ASC' : 'DESC';

        $date_from = $this->convert_date($this->input->get('date_from', true));
        $date_to   = $this->convert_date($this->input->get('date_to', true));

        $filters = [
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'receive'   => $this->input->get('receive', true),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportInventory_model
            ->get_receive_report($limit, $start, $filters, $order, $dir);

        $totalRows = $this->ReportInventory_model
            ->count_receive_report($filters);

        // group card
        $grouped = [];
        $grand = [
            'jumlah' => 0,
            'berat'  => 0,
            'total'  => 0
        ];

        foreach ($rows as $r) {

            $key = $r->RECEIVE . '|' . $r->PLANT;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'RECEIVE'          => $r->RECEIVE,
                    'PLANT'            => $r->PLANT,
                    'PLANT_NAME'       => $r->PLANT_NAME,
                    'RECEIVE_DATE'     => $r->RECEIVE_DATE,
                    'PO'               => $r->PO,
                    'NOTA'             => $r->NOTA,
                    'SUPPLIER'         => $r->SUPPLIER,
                    'SUPPLIER_NAME'    => $r->SUPPLIER_NAME,
                    'PEMBAYARAN'       => $r->PEMBAYARAN,
                    'JENIS_PAY'        => $r->JENIS_PAY,
                    'NO_REF'           => $r->NO_REF,
                    'SLIP_NO'          => $r->SLIP_NO,
                    'REMARK'           => $r->REMARK,
                    'ATTACH_FILE_NAME' => $r->ATTACH_FILE_NAME,
                    'DETAIL'           => []
                ];
            }

            $grouped[$key]['DETAIL'][] = [
                'SEQ_NO'         => $r->SEQ_NO,
                'CUSTOMER'       => $r->CUSTOMER,
                'CUSTOMER_NAME'  => $r->CUSTOMER_NAME,
                'MATERIAL'       => $r->MATERIAL,
                'MATERIAL_NAME'  => $r->MATERIAL_NAME,
                'JUMLAH'         => (float)$r->JUMLAH,
                'BERAT'          => (float)$r->BERAT,
                'SUSUT_JUMLAH'   => (float)$r->SUSUT_JUMLAH,
                'SUSUT_BERAT'    => (float)$r->SUSUT_BERAT,
                'HARGA'          => (float)$r->HARGA,
                'TOTAL'          => (float)$r->TOTAL,
                'KETERANGAN'     => $r->KETERANGAN,
                'STATUS'         => $r->STATUS
            ];

            $grand['jumlah'] += (float)$r->JUMLAH;
            $grand['berat']  += (float)$r->BERAT;
            $grand['total']  += (float)$r->TOTAL;
        }

        $pages = ceil($totalRows / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => array_values($grouped),
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page
        ]);
        exit;
    }

    public function export_excel_receive()
    {
        $filters = [
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'receive'   => $this->input->get('receive', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
        ];

        $rows = $this->ReportInventory_model->get_receive_report(
            0,
            0,
            $filters,
            'RECEIVE_DATE',
            'DESC'
        );

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE . '|' . $r->PLANT;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'RECEIVE'          => $r->RECEIVE,
                    'PLANT_NAME'       => $r->PLANT_NAME,
                    'RECEIVE_DATE'     => $r->RECEIVE_DATE,
                    'PO'               => $r->PO,
                    'NOTA'             => $r->NOTA,
                    'SUPPLIER'         => $r->SUPPLIER,
                    'SUPPLIER_NAME'    => $r->SUPPLIER_NAME,
                    'PEMBAYARAN'       => $r->PEMBAYARAN,
                    'JENIS_PAY'        => $r->JENIS_PAY,
                    'NO_REF'           => $r->NO_REF,
                    'SLIP_NO'          => $r->SLIP_NO,
                    'REMARK'           => $r->REMARK,
                    'ATTACH_FILE_NAME' => $r->ATTACH_FILE_NAME,
                    'DETAIL'           => []
                ];
            }

            $grouped[$key]['DETAIL'][] = $r;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Receive Report');

        $logoPath = FCPATH . 'assets/img/abc-trans.png';

        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(55);
            $drawing->setWorksheet($sheet);
        }

        $sheet->mergeCells('B1:J1');
        $sheet->setCellValue('B1', 'PT. Abadi Bersama Cerah');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(18);

        $sheet->mergeCells('B2:J2');
        $sheet->setCellValue('B2', 'RECEIVE REPORT');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(13);

        $period = date('d M Y', strtotime($filters['date_from'])) .
            ' - ' .
            date('d M Y', strtotime($filters['date_to']));

        $sheet->mergeCells('B3:J3');
        $sheet->setCellValue('B3', 'Period : ' . $period);
        $sheet->getStyle('B3')->getFont()->setItalic(true);

        $row = 6;

        $grandQty = 0;
        $grandWeight = 0;
        $grandTotal = 0;

        foreach ($grouped as $rc) {

            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("A{$row}", '#' . $rc['RECEIVE']);
            $sheet->setCellValue("J{$row}", 'RECEIVED');

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size'  => 11
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F4C81']
                ]
            ]);

            $row++;

            $metaStyle = ['font' => ['bold' => true]];

            // line 1
            $sheet->setCellValue("A{$row}", 'PLANT');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $rc['PLANT_NAME']);

            $sheet->setCellValue("F{$row}", 'SUPPLIER');
            $sheet->setCellValue("G{$row}", ':');
            $sheet->setCellValue("H{$row}", $rc['SUPPLIER'].' - '.$rc['SUPPLIER_NAME']);

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("F{$row}")->applyFromArray($metaStyle);

            $row++;

            // line 2
            $sheet->setCellValue("A{$row}", 'PO');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $rc['PO']);

            $sheet->setCellValue("F{$row}", 'RECEIVE DATE');
            $sheet->setCellValue("G{$row}", ':');
            $sheet->setCellValue("H{$row}", strtoupper(date('d F Y', strtotime($rc['RECEIVE_DATE']))));

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("F{$row}")->applyFromArray($metaStyle);

            $row++;

            // line 3
            $sheet->setCellValue("A{$row}", 'NOTA');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $rc['NOTA'] ?: '-');

            $sheet->setCellValue("F{$row}", 'REF NO');
            $sheet->setCellValue("G{$row}", ':');
            $sheet->setCellValue("H{$row}", $rc['NO_REF'] ?: '-');

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("F{$row}")->applyFromArray($metaStyle);

            $row++;

            // line 4
            $sheet->setCellValue("A{$row}", 'PAYMENT');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $rc['PEMBAYARAN'] ?: '-');

            $sheet->setCellValue("F{$row}", 'PAY TYPE');
            $sheet->setCellValue("G{$row}", ':');
            $sheet->setCellValue("H{$row}", $rc['JENIS_PAY'] ?: '-');

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("F{$row}")->applyFromArray($metaStyle);

            $row++;

            // line 5
            $sheet->setCellValue("A{$row}", 'SLIP NO');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->setCellValue("C{$row}", $rc['SLIP_NO'] ?: '-');

            $sheet->setCellValue("F{$row}", 'ATTACHMENT');
            $sheet->setCellValue("G{$row}", ':');
            $sheet->setCellValue("H{$row}", $rc['ATTACH_FILE_NAME'] ? 'Available' : '-');

            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);
            $sheet->getStyle("F{$row}")->applyFromArray($metaStyle);

            $row++;

            // remark
            $sheet->setCellValue("A{$row}", 'REMARK');
            $sheet->setCellValue("B{$row}", ':');
            $sheet->mergeCells("C{$row}:J{$row}");
            $sheet->setCellValue("C{$row}", $rc['REMARK'] ?: '-');
            $sheet->getStyle("A{$row}")->applyFromArray($metaStyle);

            $row += 2;

            $headers = [
                'CUSTOMER',
                'MATERIAL',
                'QTY',
                'WEIGHT',
                'SHRINK QTY',
                'SHRINK WEIGHT',
                'PRICE',
                'TOTAL',
                'REMARK',
                'STATUS'
            ];

            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col.$row, $h);
                $col++;
            }

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ]
            ]);

            $row++;

            $subQty = 0;
            $subWeight = 0;
            $subTotal = 0;

            foreach ($rc['DETAIL'] as $d) {

                $sheet->setCellValue("A{$row}", $d->CUSTOMER.' - '.$d->CUSTOMER_NAME);
                $sheet->setCellValue("B{$row}", $d->MATERIAL.' - '.$d->MATERIAL_NAME);
                $sheet->setCellValue("C{$row}", (float)$d->JUMLAH);
                $sheet->setCellValue("D{$row}", (float)$d->BERAT);
                $sheet->setCellValue("E{$row}", (float)$d->SUSUT_JUMLAH);
                $sheet->setCellValue("F{$row}", (float)$d->SUSUT_BERAT);
                $sheet->setCellValue("G{$row}", (float)$d->HARGA);
                $sheet->setCellValue("H{$row}", (float)$d->TOTAL);
                $sheet->setCellValue("I{$row}", $d->KETERANGAN ?: '-');
                $sheet->setCellValue("J{$row}", $d->STATUS ?: '-');

                $subQty += (float)$d->JUMLAH;
                $subWeight += (float)$d->BERAT;
                $subTotal += (float)$d->TOTAL;

                $row++;
            }

            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", 'SUBTOTAL');
            $sheet->setCellValue("C{$row}", $subQty);
            $sheet->setCellValue("D{$row}", $subWeight);
            $sheet->setCellValue("H{$row}", $subTotal);

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ]);

            $grandQty += $subQty;
            $grandWeight += $subWeight;
            $grandTotal += $subTotal;

            $row += 2;
        }

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'GRAND TOTAL');
        $sheet->setCellValue("C{$row}", $grandQty);
        $sheet->setCellValue("D{$row}", $grandWeight);
        $sheet->setCellValue("H{$row}", $grandTotal);

        $sheet->getStyle("A{$row}:J{$row}")->getFont()->setBold(true);

        $sheet->getStyle("C1:H{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="REPORT_RECEIVE.xlsx"');

        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))
            ->save('php://output');
        exit;
    }

    public function export_pdf_receive()
    {
        $filters = [
            'plant'     => $this->input->get('plant', true),
            'supplier'  => $this->input->get('supplier', true),
            'receive'   => $this->input->get('receive', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
        ];

        $rows = $this->ReportInventory_model->get_receive_report(
            0,
            0,
            $filters,
            'RECEIVE_DATE',
            'DESC'
        );

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        $logo = FCPATH . 'assets/img/abc-trans.png';
        $logo64 = file_exists($logo)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo))
            : '';

        $period = date('d M Y', strtotime($filters['date_from'])) .
            ' - ' .
            date('d M Y', strtotime($filters['date_to']));

        $html = '
        <style>
            body{
                font-family:sans-serif;
                font-size:10px;
                color:#222;
            }

            .head{
                margin-bottom:18px;
            }

            .title{
                text-align:center;
                font-weight:bold;
                font-size:18px;
            }

            .subtitle{
                text-align:center;
                margin-top:4px;
                font-size:11px;
            }

            .card{
                border:1px solid #dfe7ef;
                border-radius:10px;
                margin-bottom:20px;
                overflow:hidden;
            }

            .card-head{
                background:#0F4C81;
                color:#fff;
                padding:10px 14px;
                font-weight:bold;
                font-size:12px;
            }

            .meta{
                padding:12px 14px;
                background:#f8fafc;
            }

            .meta-table{
                width:100%;
                border-collapse:collapse;
            }

            .meta-table td{
                border:none;
                padding:3px 0;
                vertical-align:top;
                font-size:10px;
            }

            .meta-label{
                width:85px;
                font-weight:bold;
                white-space:nowrap;
            }

            .meta-sep{
                width:10px;
                text-align:center;
                font-weight:bold;
            }

            .meta-gap{
                width:30px;
            }

            .meta-value{
                font-weight:normal;
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th,td{
                border:1px solid #d9dee5;
                padding:6px;
                font-size:10px;
            }

            th{
                background:#eef2f7;
                font-weight:bold;
            }

            .right{
                text-align:right;
            }

            .subtotal{
                background:#f6f8fa;
                font-weight:bold;
            }
        </style>

        <div class="head">
            <table width="100%" border="0" style="border:none;">
                <tr>
                    <td width="70" style="border:none;">' .
                        ($logo64 ? '<img src="'.$logo64.'" height="55">' : '') .
                    '</td>
                    <td style="border:none;">
                        <div class="title">PT. Abadi Bersama Cerah</div>
                        <div class="subtitle">RECEIVE REPORT</div>
                        <div class="subtitle">Period : '.$period.'</div>
                    </td>
                </tr>
            </table>
        </div>
        ';

        foreach ($grouped as $group) {

            $h = $group[0];

            $html .= '
            <div class="card">

                <div class="card-head">
                    #'.$h->RECEIVE.' — RECEIVED
                </div>

                <div class="meta">
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">PLANT</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->PLANT_NAME.'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">SUPPLIER</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->SUPPLIER.' - '.$h->SUPPLIER_NAME.'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">PO</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.$h->PO.'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">RECEIVE DATE</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.strtoupper(date('d F Y', strtotime($h->RECEIVE_DATE))).'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">NOTA</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->NOTA ?: '-').'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">REF NO</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->NO_REF ?: '-').'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">PAYMENT</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->PEMBAYARAN ?: '-').'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">PAY TYPE</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->JENIS_PAY ?: '-').'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">SLIP NO</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->SLIP_NO ?: '-').'</td>

                            <td class="meta-gap"></td>

                            <td class="meta-label">ATTACHMENT</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value">'.($h->ATTACH_FILE_NAME ? 'Available' : '-').'</td>
                        </tr>

                        <tr>
                            <td class="meta-label">REMARK</td>
                            <td class="meta-sep">:</td>
                            <td class="meta-value" colspan="5">'.($h->REMARK ?: '-').'</td>
                        </tr>
                    </table>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>CUSTOMER</th>
                            <th>MATERIAL</th>
                            <th>QTY</th>
                            <th>WEIGHT</th>
                            <th>SHRINK QTY</th>
                            <th>SHRINK WEIGHT</th>
                            <th>PRICE</th>
                            <th>TOTAL</th>
                            <th>REMARK</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $subQty = 0;
            $subWeight = 0;
            $subTotal = 0;

            foreach ($group as $d) {

                $subQty += (float)$d->JUMLAH;
                $subWeight += (float)$d->BERAT;
                $subTotal += (float)$d->TOTAL;

                $html .= '
                <tr>
                    <td>'.$d->CUSTOMER.' - '.$d->CUSTOMER_NAME.'</td>
                    <td>'.$d->MATERIAL.' - '.$d->MATERIAL_NAME.'</td>
                    <td class="right">'.number_format($d->JUMLAH,2,',','.').'</td>
                    <td class="right">'.number_format($d->BERAT,2,',','.').'</td>
                    <td class="right">'.number_format($d->SUSUT_JUMLAH,2,',','.').'</td>
                    <td class="right">'.number_format($d->SUSUT_BERAT,2,',','.').'</td>
                    <td class="right">'.number_format($d->HARGA,0,',','.').'</td>
                    <td class="right">'.number_format($d->TOTAL,0,',','.').'</td>
                    <td>'.($d->KETERANGAN ?: '-').'</td>
                    <td>'.$d->STATUS.'</td>
                </tr>';
            }

            $html .= '
                        <tr class="subtotal">
                            <td colspan="2">SUBTOTAL</td>
                            <td class="right">'.number_format($subQty,2,',','.').'</td>
                            <td class="right">'.number_format($subWeight,2,',','.').'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="right">'.number_format($subTotal,0,',','.').'</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

            </div>';
        }

        $mpdf = new \Mpdf\Mpdf([
            'orientation'   => 'L',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 12
        ]);

        $mpdf->SetHTMLFooter('
            <div style="text-align:right;font-size:9px;color:#666;">
                Page {PAGENO} of {nbpg}
            </div>
        ');

        $mpdf->WriteHTML($html);
        $mpdf->Output('REPORT_RECEIVE.pdf', 'I');
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
