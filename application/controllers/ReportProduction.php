<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportProduction extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_productions_production')) {
            show_404();
        }
        $this->load->model('ReportProduction_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'     => $this->ReportProduction_model->get_plant_list(),
            'suppliers'  => $this->ReportProduction_model->get_supplier_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Production']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_production/index', $data);
        $this->load->view('templates/footer');
    }

    public function production()
    {
        $data = [
            'plants'     => $this->ReportProduction_model->get_plant_list(),
            'suppliers'  => $this->ReportProduction_model->get_supplier_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Production']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_production/production', $data);
        $this->load->view('templates/footer');
    }

    public function load_production()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;

        $order  = $this->input->get('order') ?: 'PRODUCTION_DATE';
        $dirParam = $this->input->get('dir');
        $dir = strtoupper($dirParam ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $production = $this->input->get('production');
        $receive_lb = $this->input->get('receive_lb');
        $date_from  = $this->input->get('date_from');
        $date_to    = $this->input->get('date_to');
        $selectedPlant = $this->input->get('plant');

        $userPlants = json_decode($this->session->userdata('plant'), true);

        // 🔒 SECURITY
        if (empty($selectedPlant) || !in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'      => [$selectedPlant], // always array
            'production' => $production,
            'receive_lb' => $receive_lb,
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        $start = ($page - 1) * $limit;

        $rows  = $this->ReportProduction_model
            ->get_production_report($limit, $start, $filters, $order, $dir);

        $total = $this->ReportProduction_model
            ->count_production_report($filters);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    public function export_excel_production()
    {
        $date_from   = $this->input->get('date_from');
        $date_to     = $this->input->get('date_to');
        $production  = $this->input->get('production');
        $receive_lb  = $this->input->get('receive_lb');
        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');

        if (empty($selectedPlant) || !in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'      => [$selectedPlant],
            'production' => $production,
            'receive_lb' => $receive_lb,
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        $rows = $this->ReportProduction_model
            ->get_production_report(0, 0, $filters, 'PRODUCTION_DATE', 'DESC');

        if (empty($rows)) show_error('No data found for export');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['PLANT','DATE','PRODUCTION','RECEIVE LB','ITEM','QTY','BERAT','REMARK'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PRODUCTION . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);

            $totalQty = 0;
            $totalBerat = 0;

            foreach ($group as $i => $r) {

                $sheet->setCellValue("E{$rowExcel}", $r->ITEM.' - '.$r->ITEM_NAME);
                $sheet->setCellValue("F{$rowExcel}", $r->QTY);
                $sheet->setCellValue("G{$rowExcel}", $r->BERAT);
                $sheet->setCellValue("H{$rowExcel}", $r->DETAIL_REMARK);

                $totalQty   += $r->QTY;
                $totalBerat += $r->BERAT;

                if ($i === 0) {
                    $sheet->setCellValue("A{$rowExcel}", $r->PLANT_NAME);
                    $sheet->setCellValue("B{$rowExcel}", date('d/m/Y', strtotime($r->PRODUCTION_DATE)));
                    $sheet->setCellValue("C{$rowExcel}", $r->PRODUCTION);
                    $sheet->setCellValue("D{$rowExcel}", $r->RECEIVE_LB);
                }

                $rowExcel++;
            }

            // Subtotal
            $sheet->setCellValue("E{$rowExcel}", 'TOTAL');
            $sheet->setCellValue("F{$rowExcel}", $totalQty);
            $sheet->setCellValue("G{$rowExcel}", $totalBerat);

            $sheet->getStyle("E{$rowExcel}:G{$rowExcel}")
                ->getFont()->setBold(true);

            $rowExcel++;

            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (['A','B','C','D'] as $col) {
                    $sheet->mergeCells("{$col}{$startRow}:{$col}{$endRow}");
                }
            }
        }

        foreach (range('A','H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_production.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf_production()
    {
        $date_from   = $this->input->get('date_from');
        $date_to     = $this->input->get('date_to');
        $production  = $this->input->get('production');
        $receive_lb  = $this->input->get('receive_lb');
        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');

        if (empty($selectedPlant) || !in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'      => [$selectedPlant],
            'production' => $production,
            'receive_lb' => $receive_lb,
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        $rows = $this->ReportProduction_model
            ->get_production_report(0, 0, $filters, 'PRODUCTION_DATE', 'DESC');

        if (empty($rows)) show_error('No data found for export');

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PRODUCTION.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:10px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}.c{text-align:center}.l{text-align:left}
        </style>
        <h3 style="text-align:center">REPORT PRODUCTION</h3>
        <table>
        <thead>
            <tr>
                <th>PLANT</th>
                <th>DATE</th>
                <th>PRODUCTION</th>
                <th>RECEIVE LB</th>
                <th>ITEM</th>
                <th>QTY</th>
                <th>BERAT</th>
                <th>REMARK</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($grouped as $group) {

            $rowspan = count($group);
            $totalQty = 0;
            $totalBerat = 0;

            foreach ($group as $i => $r) {

                $totalQty   += $r->QTY;
                $totalBerat += $r->BERAT;

                $html .= '<tr>';

                if ($i === 0) {
                    $html .= '
                        <td rowspan="'.$rowspan.'" class="c">'.$r->PLANT_NAME.'</td>
                        <td rowspan="'.$rowspan.'" class="c">'.date('d/m/Y', strtotime($r->PRODUCTION_DATE)).'</td>
                        <td rowspan="'.$rowspan.'" class="c">'.$r->PRODUCTION.'</td>
                        <td rowspan="'.$rowspan.'" class="c">'.$r->RECEIVE_LB.'</td>';
                }

                $html .= '
                    <td class="l">'.$r->ITEM.' - '.$r->ITEM_NAME.'</td>
                    <td class="r">'.number_format($r->QTY,2,',','.').'</td>
                    <td class="r">'.number_format($r->BERAT,2,',','.').'</td>
                    <td class="l">'.$r->DETAIL_REMARK.'</td>
                </tr>';
            }

            $html .= '
                <tr style="background:#eee;font-weight:bold">
                    <td colspan="5" class="r">TOTAL</td>
                    <td class="r">'.number_format($totalQty,2,',','.').'</td>
                    <td class="r">'.number_format($totalBerat,2,',','.').'</td>
                    <td></td>
                </tr>';
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_production.pdf','I');
    }

    public function load_stock_actual()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 50;
        $order = $this->input->get('order') ?: 'ITEM';

        $dirParam = $this->input->get('dir');
        $dir = strtoupper($dirParam ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $date = $this->input->get('date');
        $selectedPlant = $this->input->get('plant');

        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant' => $selectedPlant,
            'date'  => $date
        ];

        $start = ($page - 1) * $limit;

        $result = $this->ReportProduction_model
            ->get_stock_actual_report($limit, $start, $filters, $order, $dir);

        $total = $this->ReportProduction_model
            ->count_stock_actual_report($filters);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page,'ajax');

        echo json_encode([
            'rows'       => $result['rows'],
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page,
            'totals'     => $result['totals']
        ]);
        exit;
    }

    public function export_excel_stock_actual()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');
        $date = $this->convert_date($this->input->get('date'));

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant' => $selectedPlant,
            'date'  => $date
        ];

        $result = $this->ReportProduction_model->get_stock_actual_report(0, 0, $filters);

        if (empty($result['rows'])) {
            show_error('No data found for export');
        }

        $rows = $result['rows'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','ITEM','ITEM NAME',
            'SYSTEM QTY','SYSTEM BW',
            'ADJUST QTY','ADJUST BW',
            'FINAL QTY','FINAL BW'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;
        $totalFinalQty = 0;
        $totalFinalBW  = 0;

        foreach ($rows as $r) {
            $sheet->setCellValue("A$rowExcel", $r->PLANT_NAME);
            $sheet->setCellValue("B$rowExcel", $r->ITEM);
            $sheet->setCellValue("C$rowExcel", $r->ITEM_NAME);
            $sheet->setCellValue("D$rowExcel", $r->SYSTEM_QTY);
            $sheet->setCellValue("E$rowExcel", $r->SYSTEM_BW);
            $sheet->setCellValue("F$rowExcel", $r->ADJUST_QTY);
            $sheet->setCellValue("G$rowExcel", $r->ADJUST_BW);
            $sheet->setCellValue("H$rowExcel", $r->FINAL_QTY);
            $sheet->setCellValue("I$rowExcel", $r->FINAL_BERAT);

            $totalFinalQty += (float)$r->FINAL_QTY;
            $totalFinalBW  += (float)$r->FINAL_BERAT;

            $rowExcel++;
        }

        // TOTAL
        $sheet->setCellValue("G$rowExcel", "TOTAL:");
        $sheet->setCellValue("H$rowExcel", $totalFinalQty);
        $sheet->setCellValue("I$rowExcel", $totalFinalBW);

        foreach (range('A','I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $sheet->getStyle("D2:I$rowExcel")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("G$rowExcel:I$rowExcel")
            ->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_stock_actual.xlsx"');

        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_stock_actual()
    {
        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');
        $date = $this->convert_date($this->input->get('date'));

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant' => $selectedPlant,
            'date'  => $date
        ];

        $result = $this->ReportProduction_model->get_stock_actual_report(0, 0, $filters);

        if (empty($result['rows'])) {
            show_error('No data found');
        }

        $rows = $result['rows'];

        $totalFinalQty = 0;
        $totalFinalBW  = 0;

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .c{text-align:center}.r{text-align:right}
        </style>

        <h3 style="text-align:center">REPORT STOCK ACTUAL</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th><th>ITEM</th><th>ITEM NAME</th>
                    <th>SYSTEM QTY</th><th>SYSTEM BW</th>
                    <th>ADJUST QTY</th><th>ADJUST BW</th>
                    <th>FINAL QTY</th><th>FINAL BW</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($rows as $r) {
            $html .= "
            <tr>
                <td class='c'>{$r->PLANT_NAME}</td>
                <td class='c'>{$r->ITEM}</td>
                <td>{$r->ITEM_NAME}</td>
                <td class='r'>".number_format($r->SYSTEM_QTY,2)."</td>
                <td class='r'>".number_format($r->SYSTEM_BW,2)."</td>
                <td class='r'>".number_format($r->ADJUST_QTY,2)."</td>
                <td class='r'>".number_format($r->ADJUST_BW,2)."</td>
                <td class='r'>".number_format($r->FINAL_QTY,2)."</td>
                <td class='r'>".number_format($r->FINAL_BERAT,2)."</td>
            </tr>";

            $totalFinalQty += (float)$r->FINAL_QTY;
            $totalFinalBW  += (float)$r->FINAL_BERAT;
        }

        $html .= "
            </tbody>
            <tfoot>
                <tr>
                    <th colspan='7'>TOTAL</th>
                    <th class='r'>".number_format($totalFinalQty,2)."</th>
                    <th class='r'>".number_format($totalFinalBW,2)."</th>
                </tr>
            </tfoot>
        </table>";

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right'=> 10,
            'margin_top'  => 10,
            'margin_bottom'=>10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('report_stock_actual.pdf','I');
    }

    public function load_item_balance()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 10;

        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $selectedPlant = $this->input->get('plant');
        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'     => $selectedPlant,
            'item'      => $this->input->get('item'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $start = ($page - 1) * $limit;

        $rows  = $this->ReportProduction_model
            ->get_item_balance($limit, $start, $filters);

        $total = $this->ReportProduction_model
            ->count_item_balance($filters);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    public function export_excel_item_balance()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $selectedPlant = $this->input->get('plant');
        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'     => $selectedPlant,
            'item'      => $this->input->get('item'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $rows = $this->ReportProduction_model->get_item_balance(0, 0, $filters);

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT','ITEM','BEGIN QTY','BEGIN BW','IN QTY','IN BW','OUT QTY','OUT BW','END QTY','END BW'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue("A$rowExcel", $r->plant_name);
            $sheet->setCellValue("B$rowExcel", $r->item.' - '.$r->item_name);
            $sheet->setCellValue("C$rowExcel", number_format($r->BEGIN_QTY, 2, ',', '.'));
            $sheet->setCellValue("D$rowExcel", number_format($r->BEGIN_BW, 2, ',', '.'));
            $sheet->setCellValue("E$rowExcel", number_format($r->IN_QTY, 2, ',', '.'));
            $sheet->setCellValue("F$rowExcel", number_format($r->IN_BW, 2, ',', '.'));
            $sheet->setCellValue("G$rowExcel", number_format($r->OUT_QTY, 2, ',', '.'));
            $sheet->setCellValue("H$rowExcel", number_format($r->OUT_BW, 2, ',', '.'));
            $sheet->setCellValue("I$rowExcel", number_format($r->END_QTY, 2, ',', '.'));
            $sheet->setCellValue("J$rowExcel", number_format($r->END_BW, 2, ',', '.'));
            $rowExcel++;
        }

        foreach (range('A','J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="item_balance.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
    
    public function export_pdf_item_balance()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $selectedPlant = $this->input->get('plant');
        $userPlants = json_decode($this->session->userdata('plant'), true);

        if (!in_array($selectedPlant, $userPlants)) {
            show_error('Unauthorized plant access');
        }

        $filters = [
            'plant'     => $selectedPlant,
            'item'      => $this->input->get('item'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $rows = $this->ReportProduction_model->get_item_balance(0, 0, $filters);

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $html = '
        <style>
            table {border-collapse: collapse; width: 100%; font-size: 10px}
            th, td {border: 1px solid #000; padding: 4px; text-align: center}
            th {background: #eee}
            .r {text-align: right}
            .l {text-align: left}
        </style>
        <h3 style="text-align:center">ITEM BALANCE</h3>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">PLANT</th>
                    <th rowspan="2">ITEM</th>
                    <th colspan="2">BEGIN</th>
                    <th colspan="2">IN</th>
                    <th colspan="2">OUT</th>
                    <th colspan="2">END</th>
                </tr>
                <tr>
                    <th>QTY</th>
                    <th>BW</th>
                    <th>QTY</th>
                    <th>BW</th>
                    <th>QTY</th>
                    <th>BW</th>
                    <th>QTY</th>
                    <th>BW</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($rows as $r) {
            $html .= '<tr>';
            $html .= "<td class='l'>{$r->plant_name}</td>";
            $html .= "<td class='l'>{$r->item} - {$r->item_name}</td>";
            $html .= "<td class='r'>".number_format($r->BEGIN_QTY, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->BEGIN_BW, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->IN_QTY, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->IN_BW, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->OUT_QTY, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->OUT_BW, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->END_QTY, 2, ',', '.')."</td>";
            $html .= "<td class='r'>".number_format($r->END_BW, 2, ',', '.')."</td>";
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right'=> 10,
            'margin_top'  => 10,
            'margin_bottom'=>10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('item_balance.pdf', 'I');
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
