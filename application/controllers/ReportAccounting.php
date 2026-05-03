<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportAccounting extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_accounting_cost')) {
            show_404();
        }
        $this->load->model('ReportAccounting_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'    => $this->ReportAccounting_model->get_plant_list(),
            'suppliers'  => $this->ReportAccounting_model->get_supplier_list(),
            'customers'  => $this->ReportAccounting_model->get_customer_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Accounting']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_accounting/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_cost()
    {
       $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;

        $order = (string)$this->input->get('order', TRUE);
        $allowedOrder = ['COST', 'PLANT', 'COST_DATE', 'PEMBAYARAN'];
        if (!in_array($order, $allowedOrder)) {
            $order = 'COST_DATE';
        }

        $dir = strtoupper((string)$this->input->get('dir', TRUE));
        $dir = $dir === 'ASC' ? 'ASC' : 'DESC';

        // ===== GET USER PLANTS =====
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant', TRUE);

        // ===== VALIDASI PLANT =====
        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'cost'       => $this->input->get('cost', TRUE),
            'pembayaran' => $this->input->get('pembayaran', TRUE),
            'date_from'  => $this->input->get('date_from', TRUE),
            'date_to'    => $this->input->get('date_to', TRUE),
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportAccounting_model
            ->get_cost_report($limit, $start, $filters, $order, $dir);

        $total = $this->ReportAccounting_model
            ->count_cost_report($filters);

        $pages = $limit > 0 ? ceil($total / $limit) : 1;
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    public function export_excel_cost()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'cost'       => $this->input->get('cost', TRUE),
            'pembayaran' => $this->input->get('pembayaran', TRUE),
            'date_from'  => $this->input->get('date_from', TRUE),
            'date_to'    => $this->input->get('date_to', TRUE),
        ];

        $rows = $this->ReportAccounting_model
            ->get_cost_report(0, 0, $filters, 'COST_DATE', 'DESC');

        if (empty($rows)) show_error('No data found for export');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ===== HEADER =====
        $headers = ['PLANT','DATE','COST','PEMBAYARAN','TIPE COST','JUMLAH','TOTAL','REMARK'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        // ===== GROUP BY COST + PLANT =====
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->COST.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);
            $grandTotal = 0;

            foreach ($group as $i => $r) {

                $sheet->setCellValue("E{$rowExcel}", $r->TIPE_COST_NAME);
                $sheet->setCellValue("F{$rowExcel}", $r->JUMLAH);
                $sheet->setCellValue("G{$rowExcel}", $r->TOTAL);
                $sheet->setCellValue("H{$rowExcel}", $r->DETAIL_REMARK);

                $grandTotal += $r->TOTAL;

                if ($i === 0) {
                    $sheet->setCellValue("A{$rowExcel}", $r->PLANT_NAME ?? $r->PLANT);
                    $sheet->setCellValue("B{$rowExcel}", date('d/m/Y', strtotime($r->COST_DATE)));
                    $sheet->setCellValue("C{$rowExcel}", $r->COST);
                    $sheet->setCellValue("D{$rowExcel}", $r->PEMBAYARAN);
                }

                $rowExcel++;
            }

            // ===== SUBTOTAL =====
            $sheet->setCellValue("F{$rowExcel}", 'TOTAL');
            $sheet->setCellValue("G{$rowExcel}", $grandTotal);

            $sheet->getStyle("F{$rowExcel}:G{$rowExcel}")->getFont()->setBold(true);
            $sheet->getStyle("F{$rowExcel}:H{$rowExcel}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('EFEFEF');

            $rowExcel++;

            // ===== MERGE HEADER =====
            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (['A','B','C','D'] as $col) {
                    $sheet->mergeCells("{$col}{$startRow}:{$col}{$endRow}");
                    $sheet->getStyle("{$col}{$startRow}")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }
        }

        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("F2:G".($rowExcel-1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_cost.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf_cost()
    {
       $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'cost'       => $this->input->get('cost', TRUE),
            'pembayaran' => $this->input->get('pembayaran', TRUE),
            'date_from'  => $this->input->get('date_from', TRUE),
            'date_to'    => $this->input->get('date_to', TRUE),
        ];

        $rows = $this->ReportAccounting_model
            ->get_cost_report(0, 0, $filters, 'COST_DATE', 'DESC');

        if (empty($rows)) show_error('No data found for export');

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->COST.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '<style>
            table{border-collapse:collapse;width:100%;font-size:10px}
            th,td{border:1px solid #000;padding:4px}
            th{background:#eee;text-align:center}
            td.l{text-align:left} td.c{text-align:center} td.r{text-align:right}
        </style>';

        $html .= '<h3 style="text-align:center">REPORT COST</h3>';
        $html .= '<table><thead>
            <tr>
                <th>PLANT</th>
                <th>DATE</th>
                <th>COST</th>
                <th>PEMBAYARAN</th>
                <th>TIPE COST</th>
                <th>JUMLAH</th>
                <th>TOTAL</th>
                <th>REMARK</th>
            </tr></thead><tbody>';

        foreach ($grouped as $group) {

            $rowspan = count($group);
            $grandTotal = 0;

            foreach ($group as $i => $r) {

                $grandTotal += $r->TOTAL;
                $html .= '<tr>';

                if ($i === 0) {
                    $html .= '<td rowspan="'.$rowspan.'" class="c">'.($r->PLANT_NAME ?? $r->PLANT).'</td>';
                    $html .= '<td rowspan="'.$rowspan.'" class="c">'.date('d/m/Y', strtotime($r->COST_DATE)).'</td>';
                    $html .= '<td rowspan="'.$rowspan.'" class="c">'.$r->COST.'</td>';
                    $html .= '<td rowspan="'.$rowspan.'" class="c">'.$r->PEMBAYARAN.'</td>';
                }

                $html .= '<td class="c">'.$r->TIPE_COST_NAME.'</td>';
                $html .= '<td class="r">'.number_format($r->JUMLAH,2,',','.').'</td>';
                $html .= '<td class="r">'.number_format($r->TOTAL,2,',','.').'</td>';
                $html .= '<td class="l">'.$r->DETAIL_REMARK.'</td>';
                $html .= '</tr>';
            }

            $html .= '
                <tr style="background:#eee;font-weight:bold">
                    <td colspan="6" class="r">TOTAL</td>
                    <td class="r">'.number_format($grandTotal,2,',','.').'</td>
                    <td></td>
                </tr>';
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_cost.pdf','I');
    }

    public function load_payment()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;

        // ===== ORDER SAFE =====
        $order = (string)$this->input->get('order', TRUE);
        $allowedOrder = ['PAYMENT_DATE','PAYMENT','PLANT','SUPPLIER'];
        if (!in_array($order, $allowedOrder)) {
            $order = 'PAYMENT_DATE';
        }

        $dir = strtoupper((string)$this->input->get('dir', TRUE));
        $dir = $dir === 'ASC' ? 'ASC' : 'DESC';

        // ===== USER PLANTS =====
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        $filters = [
            'plants'       => $plants,
            'supplier'     => $this->input->get('supplier', TRUE),
            'payment'      => $this->input->get('payment', TRUE),
            'payment_type' => $this->input->get('payment_type', TRUE),
            'date_from'    => $date_from,
            'date_to'      => $date_to,
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportAccounting_model
            ->get_payment_report($limit, $start, $filters, $order, $dir);

        $totalRows = $this->ReportAccounting_model
            ->count_payment_report($filters);

        $grandRow = $this->ReportAccounting_model
            ->get_payment_grand_total($filters);

        $grand = [
            'jumlah' => (float)($grandRow->jumlah ?? 0),
            'berat'  => (float)($grandRow->berat ?? 0),
            'total'  => (float)($grandRow->total ?? 0)
        ];

        $pages = $limit > 0 ? ceil($totalRows / $limit) : 1;
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page
        ]);
        exit;
    }

    public function export_excel_payment()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'       => $plants,
            'supplier'     => $this->input->get('supplier'),
            'payment'      => $this->input->get('payment'),
            'payment_type' => $this->input->get('payment_type'),
            'date_from'    => $date_from,
            'date_to'      => $date_to,
        ];

        $rows = $this->ReportAccounting_model
            ->get_payment_report(0, 0, $filters, 'PAYMENT_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $headers = [
            'PLANT','DATE','PAYMENT NO','SUPPLIER','PEMBAYARAN',
            'RECEIVE NO','MATERIAL','JUMLAH','BERAT','HARGA','TOTAL'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        // ================= GROUP =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PAYMENT.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        foreach ($grouped as $group) {
            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {

                $sheet->setCellValue("F$rowExcel", $r->RECEIVE_NO);
                $sheet->setCellValue("G$rowExcel", $r->MATERIAL_NAME);
                $sheet->setCellValue("H$rowExcel", $r->JUMLAH);
                $sheet->setCellValue("I$rowExcel", $r->BERAT);
                $sheet->setCellValue("J$rowExcel", $r->HARGA);
                $sheet->setCellValue("K$rowExcel", $r->DETAIL_TOTAL);

                if ($i === 0) {
                    $sheet->setCellValue("A$rowExcel", $r->PLANT_NAME);
                    $sheet->setCellValue("B$rowExcel", date('d/m/Y', strtotime($r->PAYMENT_DATE)));
                    $sheet->setCellValue("C$rowExcel", $r->PAYMENT);
                    $sheet->setCellValue("D$rowExcel", $r->SUPPLIER_NAME);
                    $sheet->setCellValue("E$rowExcel", $r->PEMBAYARAN);
                }

                $rowExcel++;
            }

            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (['A','B','C','D','E'] as $c) {
                    $sheet->mergeCells("$c$startRow:$c$endRow");
                    $sheet->getStyle("$c$startRow")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }
        }

        // ================= GRAND TOTAL =================
        $grandRow = $this->ReportAccounting_model
            ->get_payment_grand_total($filters);

        $sheet->setCellValue("G{$rowExcel}", 'GRAND TOTAL');
        $sheet->mergeCells("G{$rowExcel}:J{$rowExcel}");
        $sheet->setCellValue("K{$rowExcel}", $grandRow->total ?? 0);
        $sheet->getStyle("G{$rowExcel}:K{$rowExcel}")->getFont()->setBold(true);

        foreach (range('A','K') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_payment.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_payment()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'       => $plants,
            'supplier'     => $this->input->get('supplier'),
            'payment'      => $this->input->get('payment'),
            'payment_type' => $this->input->get('payment_type'),
            'date_from'    => $date_from,
            'date_to'      => $date_to,
        ];

        $rows = $this->ReportAccounting_model
            ->get_payment_report(0, 0, $filters, 'PAYMENT_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PAYMENT.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}
            .c{text-align:center}
        </style>

        <h3 style="text-align:center">REPORT PAYMENT</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>PAYMENT NO</th>
                    <th>SUPPLIER</th>
                    <th>PEMBAYARAN</th>
                    <th>RECEIVE</th>
                    <th>MATERIAL</th>
                    <th>JUMLAH</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($grouped as $group) {
            $rowspan = count($group);

            foreach ($group as $i => $r) {
                $html .= '<tr>';

                if ($i === 0) {
                    $html .= "
                        <td rowspan='$rowspan' class='c'>$r->PLANT_NAME</td>
                        <td rowspan='$rowspan' class='c'>".date('d/m/Y',strtotime($r->PAYMENT_DATE))."</td>
                        <td rowspan='$rowspan' class='c'><b>$r->PAYMENT</b></td>
                        <td rowspan='$rowspan'>$r->SUPPLIER_NAME</td>
                        <td rowspan='$rowspan' class='c'>$r->PEMBAYARAN</td>
                    ";
                }

                $html .= "
                    <td>$r->RECEIVE_NO</td>
                    <td>$r->MATERIAL_NAME</td>
                    <td class='r'>".number_format($r->JUMLAH,2)."</td>
                    <td class='r'>".number_format($r->BERAT,2)."</td>
                    <td class='r'>".number_format($r->HARGA,0,',','.')."</td>
                    <td class='r'>".number_format($r->DETAIL_TOTAL,0,',','.')."</td>
                </tr>";
            }
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_payment.pdf', 'I');
    }

    public function load_cash_in()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;

        // ===== USER PLANTS =====
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant', true);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'customer'   => $this->input->get('customer', true),
            'cash_in'    => $this->input->get('cash_in', true),
            'pembayaran' => $this->input->get('pembayaran', true),
            'date_from'  => $this->convert_date($this->input->get('date_from', true)),
            'date_to'    => $this->convert_date($this->input->get('date_to', true)),
        ];

        $start = ($page - 1) * $limit;

        $rows = $this->ReportAccounting_model
            ->get_cash_in_report($limit, $start, $filters);

        $totalRows = $this->ReportAccounting_model
            ->count_cash_in_report($filters);

        $grandRow = $this->ReportAccounting_model
            ->get_cash_in_grand_total($filters);

        $pages = ($limit > 0) ? ceil($totalRows / $limit) : 1;

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => [
                'amount_invoice' => (float)($grandRow->amount_invoice ?? 0),
                'amount_offset'  => (float)($grandRow->amount_offset ?? 0),
            ],
            'pagination' => $this->build_pagination($pages, $page, 'ajax'),
            'page'       => $page
        ]);
        exit;
    }

    public function export_excel_cash_in()
    {
       $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'customer'   => $this->input->get('customer'),
            'cash_in'    => $this->input->get('cash_in'),
            'pembayaran' => $this->input->get('pembayaran'),
            'date_from'  => $this->convert_date($this->input->get('date_from')),
            'date_to'    => $this->convert_date($this->input->get('date_to')),
        ];

        $rows = $this->ReportAccounting_model
            ->get_cash_in_report(0, 0, $filters, 'CASHIN_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $headers = [
            'SALES','PLANT','DATE','CASH IN','CUSTOMER',
            'PEMBAYARAN','NO REK','SLIP NO','INV','OFFSET'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
            $col++;
        }

        // ================= GROUP SALES + PLANT =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->SALES.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        $rowExcel = 2;

        foreach ($grouped as $group) {
            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {

                // ===== DETAIL (PER ROW) =====
                $sheet->setCellValue("C$rowExcel", date('d/m/Y', strtotime($r->CASHIN_DATE)));
                $sheet->setCellValue("D$rowExcel", $r->CASH_IN);
                $sheet->setCellValue("E$rowExcel", $r->CUSTOMER_NAME.' | '.$r->CUSTOMER);
                $sheet->setCellValue("F$rowExcel", $r->PEMBAYARAN);
                $sheet->setCellValue("G$rowExcel", $r->NO_REK_NAME);
                $sheet->setCellValue("H$rowExcel", $r->INVOICE_NO);
                $sheet->setCellValue("I$rowExcel", $r->AMOUNT_INVOICE);
                $sheet->setCellValue("J$rowExcel", $r->AMOUNT_OFFSET);

                // ===== MERGE HEADER (SALES + PLANT) =====
                if ($i === 0) {
                    $sheet->setCellValue("A$rowExcel", $r->SALES);
                    $sheet->setCellValue("B$rowExcel", $r->PLANT_NAME);
                }

                $rowExcel++;
            }

            // ===== MERGE CELL SALES + PLANT =====
            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (['A','B'] as $c) {
                    $sheet->mergeCells("$c$startRow:$c$endRow");
                    $sheet->getStyle("$c$startRow")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }
        }

        // ================= AUTO WIDTH =================
        foreach (range('A','J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // ================= OUTPUT =================
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_cash_in.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_cash_in()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        if (!is_array($userPlants) || empty($userPlants)) {
            show_error('Unauthorized plant access');
        }

        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plants = [$selectedPlant];

        } else {
            $plants = $userPlants;
        }

        $filters = [
            'plants'     => $plants,
            'customer'   => $this->input->get('customer'),
            'cash_in'    => $this->input->get('cash_in'),
            'pembayaran' => $this->input->get('pembayaran'),
            'date_from'  => $this->convert_date($this->input->get('date_from')),
            'date_to'    => $this->convert_date($this->input->get('date_to')),
        ];

        $rows = $this->ReportAccounting_model
            ->get_cash_in_report(0, 0, $filters, 'CASHIN_DATE', 'DESC');

        if (!$rows) show_error('No data found');

        // ===== GROUP BY SALES + PLANT =====
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->SALES . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '<style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}
            .c{text-align:center}
        </style>';

        $html .= '<h3 style="text-align:center">REPORT CASH IN</h3>';
        $html .= '<table><thead><tr>
            <th>SALES</th><th>PLANT</th><th>DATE</th><th>CASH IN</th>
            <th>CUSTOMER</th><th>PEMBAYARAN</th><th>NO REK</th><th>SLIP NO</th>
            <th>INV</th><th>OFFSET</th>
        </tr></thead><tbody>';

        foreach ($grouped as $group) {
            $rowspan = count($group);
            foreach ($group as $i => $r) {
                $html .= '<tr>';
                if ($i === 0) {
                    $html .= "
                        <td rowspan='$rowspan' class='c'>{$r->SALES}</td>
                        <td rowspan='$rowspan' class='c'>{$r->PLANT_NAME}</td>
                    ";
                }
                $html .= "
                    <td class='c'>".date('d/m/Y',strtotime($r->CASHIN_DATE))."</td>
                    <td class='c'>{$r->CASH_IN}</td>
                    <td>{$r->CUSTOMER_NAME} | {$r->CUSTOMER}</td>
                    <td>{$r->PEMBAYARAN}</td>
                    <td>{$r->NO_REK_NAME}</td>
                    <td>{$r->INVOICE_NO}</td>
                    <td class='r'>".number_format($r->AMOUNT_INVOICE,2)."</td>
                    <td class='r'>".number_format($r->AMOUNT_OFFSET,2)."</td>
                </tr>";
            }
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_cash_in.pdf','I');
    }

    public function load_ar()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;

        $order = $this->input->get('order', TRUE) ?: 'SALES_DATE';
        $dirInput = (string) $this->input->get('dir', TRUE);
        $dir = strtoupper($dirInput) === 'ASC' ? 'ASC' : 'DESC';

        $filters = [
            'plant'     => $this->input->get('plant', TRUE),
            'customer'  => $this->input->get('customer', TRUE),
            'status'    => $this->input->get('status', TRUE),
            'date_from' => $this->convert_date($this->input->get('date_from', TRUE)),
            'date_to'   => $this->convert_date($this->input->get('date_to', TRUE)),
        ];

        $userPlants = $this->session->userdata('plant');

        if (!is_array($userPlants)) {
            $userPlants = json_decode($userPlants, true);
        }

        // 🔐 ROLE LOCK
        if ($this->session->userdata('role_id') != 1) {

            // Jika user bukan admin → hanya boleh lihat plant miliknya
            $filters['plant'] = $userPlants;

        } else {

            // Jika admin pilih "*"
            if ($filters['plant'] === '*') {
                $filters['plant'] = $userPlants;
            }

            // Jika kosong → tampil semua (tidak difilter)
            if (empty($filters['plant'])) {
                unset($filters['plant']);
            }
        }

        $start = ($page - 1) * $limit;

        // ================= DATA =================
        $rows = $this->ReportAccounting_model
            ->get_ar_report($limit, $start, $filters, $order, $dir);

        $totalRows = $this->ReportAccounting_model
            ->count_ar_report($filters);

        // ================= GRAND TOTAL =================
        $grandRow = $this->ReportAccounting_model
            ->get_ar_grand_total($filters);

        $grand = [
            'invoice'     => (float)($grandRow->total_invoice ?? 0),
            'paid'        => (float)($grandRow->total_paid ?? 0),
            'outstanding' => (float)($grandRow->total_outstanding ?? 0)
        ];

        $pages = ($limit > 0) ? ceil($totalRows / $limit) : 1;

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $this->build_pagination($pages, $page, 'ajax'),
            'page'       => $page
        ]);
        exit;
    }

    public function load_ar_detail()
    {
        $sales = $this->input->get('sales');
        $plant = $this->input->get('plant');

        $payments = $this->ReportAccounting_model
            ->get_ar_payment_detail($sales, $plant);

        $items = $this->ReportAccounting_model
            ->get_ar_item_detail($sales, $plant);

        echo json_encode([
            'items'    => $items,
            'payments' => $payments
        ]);
    }

    public function export_excel_ar()
    {
        $filters = [
            'plant'     => $this->input->get('plant', TRUE),
            'customer'  => $this->input->get('customer', TRUE),
            'status'    => $this->input->get('status', TRUE),
            'date_from' => $this->convert_date($this->input->get('date_from', TRUE)),
            'date_to'   => $this->convert_date($this->input->get('date_to', TRUE)),
        ];

        // 🔐 ROLE LOCK (sama seperti sebelumnya)
        $userPlants = $this->session->userdata('plant');
        if (!is_array($userPlants)) {
            $userPlants = json_decode($userPlants, true);
        }

        if ($this->session->userdata('role_id') != 1) {

            $filters['plant'] = $userPlants;

        } else {

            if ($filters['plant'] === '*') {
                $filters['plant'] = $userPlants;
            }

            if (empty($filters['plant'])) {
                unset($filters['plant']);
            }
        }

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $userPlants;
        }

        $rows = $this->ReportAccounting_model
            ->get_ar_report(0, 0, $filters, 'SALES_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $rowExcel = 1;

        foreach ($rows as $r) {

            $status = ($r->OUTSTANDING == 0) ? 'PAID' : 'OUTSTANDING';

            // ================= HEADER INVOICE =================
            $sheet->setCellValue("A$rowExcel", "INVOICE : ".$r->SALES);
            $sheet->getStyle("A$rowExcel")->getFont()->setBold(true);
            $rowExcel++;

            $sheet->setCellValue("A$rowExcel", "Plant");
            $sheet->setCellValue("B$rowExcel", $r->PLANT_NAME ?? $r->PLANT);

            $sheet->setCellValue("C$rowExcel", "Customer");
            $sheet->setCellValue("D$rowExcel", $r->CUSTOMER_NAME ?? $r->CUSTOMER);

            $sheet->setCellValue("E$rowExcel", "Date");
            $sheet->setCellValue("F$rowExcel", date('d/m/Y', strtotime($r->SALES_DATE)));
            $rowExcel++;

            $sheet->setCellValue("A$rowExcel", "Invoice");
            $sheet->setCellValue("B$rowExcel", $r->INVOICE_AMOUNT);

            $sheet->setCellValue("C$rowExcel", "Paid");
            $sheet->setCellValue("D$rowExcel", $r->TOTAL_PAID);

            $sheet->setCellValue("E$rowExcel", "Outstanding");
            $sheet->setCellValue("F$rowExcel", $r->OUTSTANDING);

            $sheet->setCellValue("G$rowExcel", "Status");
            $sheet->setCellValue("H$rowExcel", $status);
            $rowExcel += 2;

            // ================= ITEM DETAIL =================
            $sheet->setCellValue("A$rowExcel", "ITEM DETAIL");
            $sheet->getStyle("A$rowExcel")->getFont()->setBold(true);
            $rowExcel++;

            $sheet->fromArray(
                ['Item','Type','Qty','Amount'],
                null,
                "A$rowExcel"
            );
            $sheet->getStyle("A$rowExcel:D$rowExcel")->getFont()->setBold(true);
            $rowExcel++;

            $items = $this->ReportAccounting_model
                ->get_ar_item_detail($r->SALES, $r->PLANT);

            foreach ($items as $i) {
                $sheet->setCellValue("A$rowExcel", $i->ITEM_NAME);
                $sheet->setCellValue("B$rowExcel", $i->DISPLAY_TYPE);
                $sheet->setCellValue("C$rowExcel", $i->DISPLAY_QTY);
                $sheet->setCellValue("D$rowExcel", $i->DETAIL_AMOUNT);
                $rowExcel++;
            }

            $rowExcel++;

            // ================= PAYMENT HISTORY =================
            $sheet->setCellValue("A$rowExcel", "PAYMENT HISTORY");
            $sheet->getStyle("A$rowExcel")->getFont()->setBold(true);
            $rowExcel++;

            $sheet->fromArray(
                ['Date','Cash In','Pembayaran','Amount'],
                null,
                "A$rowExcel"
            );
            $sheet->getStyle("A$rowExcel:D$rowExcel")->getFont()->setBold(true);
            $rowExcel++;

            $payments = $this->ReportAccounting_model
                ->get_ar_payment_detail($r->SALES, $r->PLANT);

            foreach ($payments as $p) {
                $sheet->setCellValue("A$rowExcel", date('d/m/Y', strtotime($p->CASHIN_DATE)));
                $sheet->setCellValue("B$rowExcel", $p->CASH_IN);
                $sheet->setCellValue("C$rowExcel", $p->PEMBAYARAN);
                $sheet->setCellValue("D$rowExcel", $p->AMOUNT_OFFSET);
                $rowExcel++;
            }

            $rowExcel += 3;
        }

        foreach (range('A','H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_ar_detail.xlsx"');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_ar()
    {
        $filters = [
            'plant'     => $this->input->get('plant', TRUE),
            'customer'  => $this->input->get('customer', TRUE),
            'status'    => $this->input->get('status', TRUE),
            'date_from' => $this->convert_date($this->input->get('date_from', TRUE)),
            'date_to'   => $this->convert_date($this->input->get('date_to', TRUE)),
        ];

        $userPlants = $this->session->userdata('plant');
        if (!is_array($userPlants)) {
            $userPlants = json_decode($userPlants, true);
        }

        if ($this->session->userdata('role_id') != 1) {

            $filters['plant'] = $userPlants;

        } else {

            if ($filters['plant'] === '*') {
                $filters['plant'] = $userPlants;
            }

            if (empty($filters['plant'])) {
                unset($filters['plant']);
            }
        }

        $rows = $this->ReportAccounting_model
            ->get_ar_report(0, 0, $filters, 'SALES_DATE', 'DESC');

        $html = '
        <style>
            body{font-family:sans-serif;font-size:10px}
            table{border-collapse:collapse;width:100%}
            th,td{border:1px solid #000;padding:4px}
            th{background:#eee}
            .r{text-align:right}
            .c{text-align:center}
            .section{margin-top:10px;font-weight:bold}
        </style>

        <h3 style="text-align:center">REPORT AR DETAIL</h3>
        ';

        foreach ($rows as $r) {

            $status = ($r->OUTSTANDING == 0) ? 'PAID' : 'OUTSTANDING';

            $html .= "
            <div class='section'>INVOICE : {$r->SALES}</div>

            <table>
                <tr>
                    <td><b>Plant</b></td><td>{$r->PLANT_NAME}</td>
                    <td><b>Date</b></td><td>".date('d/m/Y', strtotime($r->SALES_DATE))."</td>
                </tr>
                <tr>
                    <td><b>Customer</b></td><td>{$r->CUSTOMER_NAME}</td>
                    <td><b>Status</b></td><td>{$status}</td>
                </tr>
                <tr>
                    <td><b>Invoice</b></td><td class='r'>".number_format($r->INVOICE_AMOUNT,2)."</td>
                    <td><b>Outstanding</b></td><td class='r'>".number_format($r->OUTSTANDING,2)."</td>
                </tr>
            </table>
            ";

            // ITEM
            $items = $this->ReportAccounting_model
                ->get_ar_item_detail($r->SALES, $r->PLANT);

            $html .= "<div class='section'>ITEM DETAIL</div>
            <table>
            <tr><th>Item</th><th>Type</th><th>Qty</th><th>Amount</th></tr>";

            foreach ($items as $i) {
                $html .= "
                <tr>
                    <td>{$i->ITEM_NAME}</td>
                    <td class='c'>{$i->DISPLAY_TYPE}</td>
                    <td class='r'>".number_format($i->DISPLAY_QTY,2)."</td>
                    <td class='r'>".number_format($i->DETAIL_AMOUNT,2)."</td>
                </tr>";
            }

            $html .= "</table>";

            // PAYMENT
            $payments = $this->ReportAccounting_model
                ->get_ar_payment_detail($r->SALES, $r->PLANT);

            $html .= "<div class='section'>PAYMENT HISTORY</div>
            <table>
            <tr><th>Date</th><th>Cash In</th><th>Pembayaran</th><th>Amount</th></tr>";

            foreach ($payments as $p) {
                $html .= "
                <tr>
                    <td class='c'>".date('d/m/Y', strtotime($p->CASHIN_DATE))."</td>
                    <td class='c'>{$p->CASH_IN}</td>
                    <td class='c'>{$p->PEMBAYARAN}</td>
                    <td class='r'>".number_format($p->AMOUNT_OFFSET,2)."</td>
                </tr>";
            }

            $html .= "</table><br><br>";
        }

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_ar_detail.pdf','I');
    }

    public function load_daily_summary()
    {
        $plant = $this->input->get('plant');
        $date  = $this->input->get('date');

        if (!$plant || !$date) {
            echo json_encode(['error' => 'Invalid parameter']);
            return;
        }

        $summary = $this->ReportAccounting_model
            ->get_daily_summary($plant, $date);

        $sales   = $this->ReportAccounting_model
            ->get_daily_sales_detail($plant, $date);

        $cash    = $this->ReportAccounting_model
            ->get_daily_cash_detail($plant, $date);

        $cost    = $this->ReportAccounting_model
            ->get_daily_cost_detail($plant, $date);

        echo json_encode([
            'summary' => $summary,
            'sales'   => $sales,
            'cash'    => $cash,
            'cost'    => $cost
        ]);
    }

    public function export_daily_excel()
    {
        $plant = $this->input->get('plant');
        $date  = $this->input->get('date');

        $summary = $this->ReportAccounting_model->get_daily_summary($plant,$date);
        $sales   = $this->ReportAccounting_model->get_daily_sales_detail($plant,$date);
        $cash    = $this->ReportAccounting_model->get_daily_cash_detail($plant,$date);
        $cost    = $this->ReportAccounting_model->get_daily_cost_detail($plant,$date);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        /* ================= SUMMARY ================= */

        $sheet->setCellValue("A$row","PERINCIAN SETORAN HARIAN");
        $sheet->mergeCells("A$row:D$row");
        $row+=2;

        $sheet->setCellValue("A$row","Plant");
        $sheet->setCellValue("B$row",$plant);
        $row++;

        $sheet->setCellValue("A$row","Date");
        $sheet->setCellValue("B$row",$date);
        $row+=2;

        $salesCash    = $summary->SALES_CASH ?? 0;
        $salesTempo   = $summary->SALES_TEMPO ?? 0;
        $arCollection = $summary->AR_COLLECTION ?? 0;
        $costToday    = $summary->COST_TODAY ?? 0;
        $depositToday = $summary->DEPOSIT_TODAY ?? 0;

        $penjualan = $salesCash + $salesTempo;
        $total     = $arCollection + $penjualan;
        $saldo     = $total - ($costToday + $salesTempo);
        $setoran   = $saldo - $depositToday;

        $sheet->setCellValue("A$row","Tagihan (Cash In)");
        $sheet->setCellValue("B$row",$arCollection); $row++;

        $sheet->setCellValue("A$row","Penjualan (Cash + Tempo)");
        $sheet->setCellValue("B$row",$penjualan); $row++;

        $sheet->setCellValue("A$row","Total (Kas Masuk)");
        $sheet->setCellValue("B$row",$total); $row++;

        $sheet->setCellValue("A$row","Biaya");
        $sheet->setCellValue("B$row",$costToday); $row++;

        $sheet->setCellValue("A$row","Piutang Hari Ini");
        $sheet->setCellValue("B$row",$salesTempo); $row++;

        $sheet->setCellValue("A$row","Saldo");
        $sheet->setCellValue("B$row",$saldo); $row++;

        $sheet->setCellValue("A$row","Deposit");
        $sheet->setCellValue("B$row",$depositToday); $row++;

        $sheet->setCellValue("A$row","Setoran");
        $sheet->setCellValue("B$row",$setoran);
        $row+=3;

        /* ================= METODE PEMASUKAN ================= */

        $salesMethodCash      = $summary->SALES_METHOD_CASH ?? 0;
        $salesMethodTransfer  = $summary->SALES_METHOD_TRANSFER ?? 0;
        $cashinMethodCash     = $summary->CASHIN_METHOD_CASH ?? 0;
        $cashinMethodTransfer = $summary->CASHIN_METHOD_TRANSFER ?? 0;

        $totalMethodCash      = $summary->TOTAL_METHOD_CASH ?? 0;
        $totalMethodTransfer  = $summary->TOTAL_METHOD_TRANSFER ?? 0;

        $sheet->setCellValue("A$row","RINCIAN METODE PEMASUKAN");
        $sheet->mergeCells("A$row:B$row");
        $row++;

        $sheet->setCellValue("A$row","Sales (Cash)");
        $sheet->setCellValue("B$row",$salesMethodCash); 
        $row++;

        $sheet->setCellValue("A$row","Sales (Transfer)");
        $sheet->setCellValue("B$row",$salesMethodTransfer); 
        $row++;

        $sheet->setCellValue("A$row","Cash In (Cash)");
        $sheet->setCellValue("B$row",$cashinMethodCash); 
        $row++;

        $sheet->setCellValue("A$row","Cash In (Transfer)");
        $sheet->setCellValue("B$row",$cashinMethodTransfer); 
        $row++;

        $sheet->setCellValue("A$row","Total Cash");
        $sheet->setCellValue("B$row",$totalMethodCash); 
        $row++;

        $sheet->setCellValue("A$row","Total Transfer");
        $sheet->setCellValue("B$row",$totalMethodTransfer); 
        $row+=3;

        /* ================= SALES ================= */

        $sheet->setCellValue("A$row","A. SALES TODAY");
        $row++;

        $sheet->fromArray(
            ['Sales','Customer','Jenis','Item','Qty','Total'],
            NULL,'A'.$row
        );
        $row++;

        foreach($sales as $s){
            $sheet->fromArray([
                $s->SALES,
                $s->CUSTOMER_NAME,
                $s->JENIS_PAY,
                $s->ITEM_NAME,
                $s->DISPLAY_QTY,
                $s->DETAIL_AMOUNT
            ],NULL,'A'.$row);
            $row++;
        }

        $row+=2;

        /* ================= CASH ================= */

        $sheet->setCellValue("A$row","B. CASH IN TODAY");
        $row++;

        $sheet->fromArray(
            ['Cash In','Customer','Invoice','Amount'],
            NULL,'A'.$row
        );
        $row++;

        foreach($cash as $c){
            $sheet->fromArray([
                $c->CASH_IN,
                $c->CUSTOMER_NAME,
                $c->SALES,
                $c->AMOUNT_OFFSET
            ],NULL,'A'.$row);
            $row++;
        }

        $row+=2;

        /* ================= COST ================= */

        $sheet->setCellValue("A$row","C. COST TODAY");
        $row++;

        $sheet->fromArray(
            ['Cost','Tipe','Remark','Total'],
            NULL,'A'.$row
        );
        $row++;

        foreach($cost as $c){
            $sheet->fromArray([
                $c->COST,
                $c->COST_NAME,
                $c->REMARK,
                $c->TOTAL
            ],NULL,'A'.$row);
            $row++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Daily_Summary_'.$date.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function export_daily_pdf()
    {
        $plant = $this->input->get('plant');
        $date  = $this->input->get('date');

        $data['summary'] = $this->ReportAccounting_model->get_daily_summary($plant,$date);
        $data['sales']   = $this->ReportAccounting_model->get_daily_sales_detail($plant,$date);
        $data['cash']    = $this->ReportAccounting_model->get_daily_cash_detail($plant,$date);
        $data['cost']    = $this->ReportAccounting_model->get_daily_cost_detail($plant,$date);
        $data['plant']   = $plant;
        $data['date']    = $date;

        $html = $this->load->view(
            'admin/report_accounting/pdf_daily_summary',
            $data,
            true
        );

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4','portrait');
        $this->pdf->render();
        $this->pdf->stream("Daily_Summary_$date.pdf", ["Attachment"=>1]);
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
