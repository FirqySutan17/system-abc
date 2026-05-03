<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcost extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('accounting_cost_entry')) {
            show_404();
        }
        $this->load->model('Mcost_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        $this->db->save_queries = false;
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Cost']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/mcost/list');
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $allowedOrder = ['COST','COST_DATE','PEMBAYARAN','SLIP_NO','PLANT'];

        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);

        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder) ? $orderInput : 'COST_DATE';
        $dir   = strtoupper($this->input->get('dir', TRUE)) === 'ASC' ? 'ASC' : 'DESC';

        $start = ($page - 1) * $limit;

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        $plants = ($role_id === 1) ? [] : $this->Mcost_model->get_user_plants($username);

        $rows  = $this->Mcost_model->get_data($limit,$start,$role_id,$plants,$search,$order,$dir);
        $total = $this->Mcost_model->count_data($role_id,$plants,$search);

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $this->build_pagination(ceil($total/$limit),$page),
            'page'       => $page
        ]);
    }

    private function build_pagination($pages, $current)
    {
        if ($pages <= 1) return '';
        $html = '<ul class="pagination pagination-sm">';
        for ($i=1;$i<=$pages;$i++){
            $active = ($i==$current)?'active':'';
            $html.='<li class="page-item '.$active.'">
                    <a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                    </li>';
        }
        return $html.'</ul>';
    }

    public function get_cost()
    {
        echo json_encode($this->Mcost_model->search_cost($this->input->get('q',TRUE)));
    }

    public function get_plant_by_user()
    {
        echo json_encode(
            $this->Mcost_model->get_plant_select2_by_user(
                $this->session->userdata('username')
            )
        );
    }

    /* =======================================================
       CREATE
    ======================================================= */
    public function create()
    {
        $data     = $this->input->post(NULL, TRUE);
        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');
        $plant    = $data['PLANT'] ?? null;

        if (!$plant)
            return $this->jsonError('Plant wajib dipilih');

        if ($role_id !== 1 && !$this->Mcost_model->user_has_plant($username,$plant))
            return $this->jsonError('Plant tidak diizinkan');

        if (empty($data['COST_DATE']) || empty($data['PEMBAYARAN']))
            return $this->jsonError('Data header belum lengkap');

        if (empty($data['DETAIL']) || !is_array($data['DETAIL']))
            return $this->jsonError('Detail cost wajib diisi');

        $validDetail = false;
        foreach ($data['DETAIL'] as $row) {
            if (!empty($row['TIPE_COST']) && (float)$row['JUMLAH'] > 0) {
                $validDetail = true; break;
            }
        }
        if (!$validDetail)
            return $this->jsonError('Detail cost tidak valid');

        $this->db->trans_begin();

        $costNo = $this->Mcost_model->generate_cost_no($plant);
        $slipNo = $this->Mcost_model->generate_slip_no($data['PEMBAYARAN'],$plant);

        $this->Mcost_model->insert_cost_header([
            'PLANT'=>$plant,
            'COST'=>$costNo,
            'COST_DATE'=>date('Y-m-d H:i:s',strtotime($data['COST_DATE'])),
            'PEMBAYARAN'=>$data['PEMBAYARAN'],
            'SLIP_NO'=>$slipNo,
            'REMARK'=>$data['REMARK']??null,
            'CREATED_AT'=>date('Y-m-d H:i:s'),
            'CREATED_BY'=>$username
        ]);

        $detailRows     = [];
        $attachmentRows = [];

        foreach ($data['DETAIL'] as $row) {
            $seq    = (int)$row['SEQ_NO'];
            $qty    = (float)$row['QTY'];
            $jumlah = (float)$row['JUMLAH'];

            $detailRows[] = [
                'PLANT'=>$plant,'COST'=>$costNo,'SEQ_NO'=>$seq,
                'TIPE_COST'=>$row['TIPE_COST'],'QTY'=>$qty,
                'JUMLAH'=>$jumlah,'TOTAL'=>$qty*$jumlah,
                'REMARK'=>$row['REMARK']??null,
                'CREATED_AT'=>date('Y-m-d H:i:s'),'CREATED_BY'=>$username
            ];

            if (!empty($_FILES['ATTACHMENT']['name'][$seq])) {
                foreach ($_FILES['ATTACHMENT']['name'][$seq] as $i=>$name) {
                    $_FILES['file']=[
                        'name'=>$_FILES['ATTACHMENT']['name'][$seq][$i],
                        'type'=>$_FILES['ATTACHMENT']['type'][$seq][$i],
                        'tmp_name'=>$_FILES['ATTACHMENT']['tmp_name'][$seq][$i],
                        'error'=>$_FILES['ATTACHMENT']['error'][$seq][$i],
                        'size'=>$_FILES['ATTACHMENT']['size'][$seq][$i]
                    ];

                    $upload=$this->Mcost_model->upload_cost_file('file',$plant,$costNo,$seq,$username);

                    if($upload){
                        $attachmentRows[]=[
                            'PLANT'=>$plant,'COST'=>$costNo,'SEQ_NO'=>$seq,
                            'FILE_NAME'=>$upload['file'],'FILE_ORIGINAL'=>$upload['original'],
                            'FILE_PATH'=>$upload['path'],'FILE_TYPE'=>$upload['type'],
                            'CREATED_AT'=>date('Y-m-d H:i:s'),'CREATED_BY'=>$username
                        ];
                    }
                }
            }
        }

        if($detailRows) $this->db->insert_batch('mst_cost_detail',$detailRows);
        if($attachmentRows) $this->db->insert_batch('mst_cost_attachment',$attachmentRows);

        if ($this->db->trans_status()===FALSE){
            $this->db->trans_rollback();
            return $this->jsonError('Gagal menyimpan Cost');
        }

        $this->db->trans_commit();
        echo json_encode(['status'=>true,'cost'=>$costNo,'message'=>'Cost berhasil disimpan']);
    }

    public function edit()
    {
        $cost  = $this->input->get('cost', true);
        $plant = $this->input->get('plant', true);

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$cost || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Parameter tidak lengkap']);
            return;
        }

        if ($role_id !== 1 && !$this->Mcost_model->user_has_plant($username, $plant)) {
            echo json_encode(['status'=>false,'message'=>'Tidak punya akses ke plant ini']);
            return;
        }

        $header = $this->db
            ->select('c.*, cd.CODE_NAME AS PLANT_NAME')
            ->from('mst_cost c')
            ->join('cd_code cd', "cd.CODE=c.PLANT AND cd.HEAD_CODE='AJ'", 'left')
            ->where('c.PLANT',$plant)
            ->where('c.COST',$cost)
            ->where('c.DELETED IS NULL',null,false)
            ->get()->row_array();

        if (!$header) {
            echo json_encode(['status'=>false,'message'=>'Data tidak ditemukan']);
            return;
        }

        $detail = $this->Mcost_model->get_cost_detail($plant,$cost);

        foreach ($detail as &$d) {
            $d['SEQ_NO'] = (int)$d['SEQ_NO'];
            $d['QTY']    = (int)$d['QTY'];
            $d['JUMLAH'] = (int)$d['JUMLAH'];
            $d['TOTAL']  = (int)$d['TOTAL'];

            $files = $this->Mcost_model->get_cost_attachments_by_seq($plant,$cost,$d['SEQ_NO']);

            $d['FILES'] = array_map(function($f){
                return [
                    'id'   => $f['ID'],
                    'name' => $f['FILE_ORIGINAL'],
                    'url'  => base_url(rtrim($f['FILE_PATH'],'/').'/'.$f['FILE_NAME'])
                ];
            }, $files);
        }

        echo json_encode(['status'=>true,'header'=>$header,'detail'=>$detail]);
    }

    public function update()
    {
        $data     = $this->input->post(NULL, FALSE);
        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        $plant   = $data['PLANT'];
        $oldCost = $data['OLD_COST'];
        $newCost = $data['COST'];

        if ($role_id !== 1 && !$this->Mcost_model->user_has_plant($username,$plant)) {
            return $this->jsonError('Plant tidak diizinkan');
        }

        if (empty($data['DETAIL']) || !is_array($data['DETAIL'])) {
            return $this->jsonError('Detail tidak ditemukan');
        }

        $this->db->trans_begin();

        /* ================= UPDATE HEADER ================= */
        $this->Mcost_model->update_cost_header($plant,$oldCost,[
            'COST'=>$newCost,
            'COST_DATE'=>date('Y-m-d H:i:s',strtotime($data['COST_DATE'])),
            'PEMBAYARAN'=>$data['PEMBAYARAN'],
            'REMARK'=>$data['REMARK']??null,
            'UPDATED_AT'=>date('Y-m-d H:i:s'),
            'UPDATED_BY'=>$username
        ]);

        /* ================= SOFT DELETE DETAIL LAMA ================= */
        $this->db->where(['PLANT'=>$plant,'COST'=>$oldCost])
                ->where('DELETED IS NULL',null,false)
                ->update('mst_cost_detail',[
                    'DELETED'=>date('Y-m-d H:i:s'),
                    'UPDATED_AT'=>date('Y-m-d H:i:s'),
                    'UPDATED_BY'=>$username
                ]);

        /* ================= GET ATTACHMENT LAMA ================= */
        $oldAttachments = $this->db
            ->where('PLANT',$plant)
            ->where('COST',$oldCost)
            ->where('DELETED IS NULL',null,false)
            ->get('mst_cost_attachment')
            ->result_array();

        $oldById = [];
        foreach ($oldAttachments as $att) {
            $oldById[$att['ID']] = $att;
        }

        $keepAttachmentIds = [];

        /* ================= LOOP DETAIL BARU ================= */
        foreach ($data['DETAIL'] as $row) {

            $seq = (int)$row['SEQ_NO'];

            /* ===== INSERT DETAIL BARU ===== */
            $this->db->insert('mst_cost_detail',[
                'PLANT'=>$plant,
                'COST'=>$newCost,
                'SEQ_NO'=>$seq,
                'TIPE_COST'=>$row['TIPE_COST'],
                'QTY'=>$row['QTY'],
                'JUMLAH'=>$row['JUMLAH'],
                'TOTAL'=>$row['QTY']*$row['JUMLAH'],
                'REMARK'=>$row['REMARK']??null,
                'CREATED_AT'=>date('Y-m-d H:i:s'),
                'CREATED_BY'=>$username
            ]);

            /* ===== ATTACHMENT LAMA YANG MASIH DIPAKAI ===== */
            $oldIds = $data['OLD_ATTACHMENT'][$seq] ?? [];
            if (!is_array($oldIds)) $oldIds = [$oldIds];

            foreach ($oldIds as $attId) {
                $attId = (int)$attId;
                if (!isset($oldById[$attId])) continue;

                $att = $oldById[$attId];
                $oldFile = rtrim($att['FILE_PATH'],'/').'/'.$att['FILE_NAME'];

                $newDir = "./uploads/{$plant}/cost/{$newCost}/{$seq}/";
                if (!is_dir($newDir)) mkdir($newDir,0755,true);

                $newFile = $newDir.$att['FILE_NAME'];

                if ($oldCost !== $newCost && is_file($oldFile)) {
                    rename($oldFile,$newFile);
                }

                $this->db->where('ID',$attId)->update('mst_cost_attachment',[
                    'COST'=>$newCost,
                    'SEQ_NO'=>$seq,
                    'FILE_PATH'=>"uploads/{$plant}/cost/{$newCost}/{$seq}/"
                ]);

                $keepAttachmentIds[] = $attId;
            }

            /* ===== UPLOAD FILE BARU (REPLACE MODE) ===== */
            if (!empty($_FILES['ATTACHMENT']['name'][$seq])) {

                // HAPUS semua attachment lama di seq ini
                $this->db->where('PLANT',$plant)
                        ->where('COST',$newCost)
                        ->where('SEQ_NO',$seq)
                        ->where('DELETED IS NULL',null,false);

                if (!empty($keepAttachmentIds)) {
                    $this->db->where_not_in('ID',$keepAttachmentIds);
                }

                $toDelete = $this->db->get('mst_cost_attachment')->result_array();

                foreach ($toDelete as $del) {
                    $file = rtrim($del['FILE_PATH'],'/').'/'.$del['FILE_NAME'];
                    if (is_file($file)) unlink($file);
                }

                if ($toDelete) {
                    $this->db->where_in('ID',array_column($toDelete,'ID'))
                            ->update('mst_cost_attachment',['DELETED'=>date('Y-m-d H:i:s')]);
                }

                foreach ($_FILES['ATTACHMENT']['name'][$seq] as $i=>$name) {

                    $_FILES['file'] = [
                        'name'=>$_FILES['ATTACHMENT']['name'][$seq][$i],
                        'type'=>$_FILES['ATTACHMENT']['type'][$seq][$i],
                        'tmp_name'=>$_FILES['ATTACHMENT']['tmp_name'][$seq][$i],
                        'error'=>$_FILES['ATTACHMENT']['error'][$seq][$i],
                        'size'=>$_FILES['ATTACHMENT']['size'][$seq][$i]
                    ];

                    $upload = $this->Mcost_model->upload_cost_file('file',$plant,$newCost,$seq,$username);

                    if ($upload) {
                        $this->db->insert('mst_cost_attachment',[
                            'PLANT'=>$plant,
                            'COST'=>$newCost,
                            'SEQ_NO'=>$seq,
                            'FILE_NAME'=>$upload['file'],
                            'FILE_ORIGINAL'=>$upload['original'],
                            'FILE_PATH'=>$upload['path'],
                            'FILE_TYPE'=>$upload['type'],
                            'CREATED_AT'=>date('Y-m-d H:i:s'),
                            'CREATED_BY'=>$username
                        ]);
                    }
                }
            }
        }

        /* ================= HAPUS ATTACHMENT YANG TIDAK DIPAKAI ================= */
        if (!empty($keepAttachmentIds)) {
            $this->db->where('PLANT',$plant)
                    ->where('COST',$newCost)
                    ->where_not_in('ID',$keepAttachmentIds)
                    ->update('mst_cost_attachment',['DELETED'=>date('Y-m-d H:i:s')]);
        }

        if ($this->db->trans_status()===FALSE){
            $this->db->trans_rollback();
            return $this->jsonError('Gagal update COST');
        }

        $this->db->trans_commit();
        echo json_encode(['status'=>true,'message'=>'COST berhasil diupdate']);
    }

    private function jsonError($msg){
        echo json_encode(['status'=>false,'message'=>$msg]);
    }

    public function remove()
    {
        $cost     = $this->input->post('cost', TRUE);
        $plantReq = $this->input->post('plant', TRUE);

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$cost) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid COST'
            ]);
            return;
        }

        // ================= PLANT =================
        if ($role_id === 1) {
            if (!$plantReq) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Plant wajib diisi'
                ]);
                return;
            }
            $plant = $plantReq;
        } else {
            if (!$this->Mcost_model->user_has_plant($username, $plantReq)) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Plant tidak diizinkan'
                ]);
                return;
            }
            $plant = $plantReq;
        }

        $this->db->trans_begin();

        // ================= ATTACHMENT =================
        $this->Mcost_model->delete_cost_attachments_by_cost($plant, $cost);

        // ================= DETAIL =================
        $this->Mcost_model->delete_cost_detail($plant, $cost, $username);

        // ================= HEADER (SOFT DELETE) =================
        $this->Mcost_model->delete_cost_header($plant, $cost, $username);

        // ================= COMMIT / ROLLBACK =================
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus COST'
            ]);
            return;
        }

        $this->db->trans_commit();

        // hapus folder fisik (setelah commit)
        $this->Mcost_model->delete_cost_folder($plant, $cost);

        echo json_encode([
            'status'  => true,
            'message' => 'COST berhasil dihapus'
        ]);
    }

    public function print_pdf()
    {
        $cost  = $this->input->get('cost', true);
        $plant = $this->input->get('plant', true);

        if (!$cost || !$plant) {
            show_error('Parameter COST atau PLANT tidak lengkap');
        }

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if ($role_id !== 1 && !$this->Mcost_model->user_has_plant($username, $plant)) {
            show_error('Tidak punya akses ke plant ini');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                c.COST,
                c.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                c.COST_DATE,
                c.PEMBAYARAN,
                c.SLIP_NO,
                c.REMARK
            ')
            ->from('mst_cost c')
            ->join(
                'cd_code aj',
                "aj.CODE = c.PLANT AND aj.HEAD_CODE = 'AJ'",
                'left'
            )
            ->where('c.COST', $cost)
            ->where('c.PLANT', $plant)
            ->where('c.DELETED IS NULL', null, false)
            ->get()
            ->row();

        if (!$header) {
            show_error('Data COST tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                cc.COST_NAME,
                d.TIPE_COST,
                d.QTY,
                d.JUMLAH,
                d.TOTAL,
                d.REMARK
            ')
            ->from('mst_cost_detail d')
            ->join(
                'cd_cost cc',
                'cc.COST = d.TIPE_COST',
                'left'
            )
            ->where('d.COST', $cost)
            ->where('d.PLANT', $plant)
            ->where('d.DELETED IS NULL', null, false)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        $data = [
            'header' => $header,
            'detail' => $detail
        ];

        /* ================= PDF ================= */
        $html = $this->load->view(
            'admin/mcost/pdf_template',
            $data,
            true
        );

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "COST_{$cost}.pdf",
            ['Attachment' => false] // inline
        );
    }

    function format_decimal_id($number, $dec = 2)
    {
        return number_format((float)$number, $dec, ',', '.');
    }

    function format_rupiah($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }

    private function normalize_number($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // hapus titik ribuan, ganti koma desimal (jika ada)
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

}
