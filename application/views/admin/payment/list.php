<div class="container-fluid">
    <div class="card w-100"><div class="card-body">
        <h5 class="card-title fw-semibold mb-4">PAYMENT PO</h5>
        <div class="row g-2 mb-3">
            <div class="col-md-4"><input id="searchPayment" class="form-control" placeholder="Cari payment, supplier, slip..."></div>
            <div class="col-md-3"><select id="filterPlant" class="form-control"><option value="">Semua Plant</option></select></div>
            <div class="col-md-2"><button id="btnResetFilter" class="btn btn-light w-100">Reset</button></div>
            <div class="col-md-3"><button id="btnAddPayment" class="btn btn-primary w-100"><i class="ti ti-plus"></i> Tambah Payment</button></div>
        </div>
        <div class="table-box position-relative">
            <div id="tableLoading" class="table-loading d-none"><div class="loading-card">
                <div class="spinner-border text-primary"></div><div class="mt-3 fw-semibold">Memuat data...</div>
                <small class="text-muted">Mohon tunggu, proses ini mungkin memerlukan waktu beberapa detik.</small>
            </div></div>
            <div id="tableWrapper"><div class="table-responsive">
                <table class="table table-hover align-middle table-modern" id="mainTable">
                    <thead><tr>
                        <th data-sort="PLANT_NAME">Plant</th><th data-sort="PAYMENT">Payment</th>
                        <th data-sort="PAYMENT_DATE">Tanggal</th><th data-sort="SUPPLIER_NAME">Supplier</th>
                        <th data-sort="PAYMENT_MODE">Mode Alokasi</th><th data-sort="PEMBAYARAN">Tipe Pembayaran</th>
                        <th class="text-end" data-sort="TOTAL">Total</th><th data-sort="STATUS">Status</th>
                        <th>Keterangan</th><th class="text-center">#</th>
                    </tr></thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div></div>
        </div>
        <div class="d-flex justify-content-between mt-3 align-items-center"><div id="info" class="small text-muted"></div><div id="pagination"></div></div>
    </div></div>
</div>

<style>
#mainTable thead th[data-sort]{cursor:pointer;user-select:none;position:relative}
#mainTable thead th[data-sort]:hover{background:#f8f9fa}
#mainTable td,#mainTable th{white-space:nowrap;vertical-align:middle}
.table-box{min-height:300px}
.table-loading{position:absolute;inset:0;z-index:10;background:rgba(255,255,255,.82);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;border-radius:12px}
.loading-card{text-align:center;padding:28px 40px;background:#fff;border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
.loading-hide{opacity:.35;pointer-events:none}
.modal-body{max-height:75vh;overflow-y:auto}
.modal-xl{--bs-modal-width:90%}
.flex-inline{padding:2px 10px;margin-bottom:5px;display:flex;align-items:center;gap:12px}
.flex-inline>label{width:30%;min-width:125px;margin-bottom:0}
.select2-container--open{z-index:999999!important}
.select2-dropdown{position:absolute!important}
.mode-wrapper{display:flex;gap:16px;flex-wrap:wrap}
.mode-card{flex:1;min-width:260px;border:2px solid #e5e7eb;border-radius:16px;padding:20px;cursor:pointer;transition:.2s;position:relative;background:#fff}
.mode-card:hover{border-color:#2563eb}
.mode-card:has(input:checked){border-color:#2563eb;background:#eff6ff}
.mode-card input{position:absolute;opacity:0}
.mode-card input:checked+.mode-content{color:#2563eb}
.mode-title{font-weight:700;font-size:16px}
.mode-sub{margin-top:5px;font-size:13px;color:#6b7280}
.summary-card{border-radius:18px;padding:18px;color:white;box-shadow:0 10px 25px rgba(0,0,0,.08);height:100%}
.summary-label{font-size:13px;opacity:.9;margin-bottom:10px}
.summary-value{font-size:clamp(18px,2vw,24px);font-weight:bold;overflow-wrap:anywhere}
.summary-blue{background:linear-gradient(135deg,#2563eb,#1d4ed8)}
.summary-green{background:linear-gradient(135deg,#10b981,#059669)}
.summary-orange{background:linear-gradient(135deg,#f59e0b,#d97706)}
.summary-red{background:linear-gradient(135deg,#64748b,#334155)}
@media(max-width:767px){.flex-inline{display:block}.flex-inline>label{display:block;width:100%;margin-bottom:6px}.summary-card{padding:14px}}
</style>

<!-- MODAL ADD / EDIT -->
<div class="modal fade" id="PaymentModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-xl"><form id="fPayment"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="paymentModalTitle">Payment PO - TAMBAH</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="PAYMENT" id="paymentNo">
        <div class="row g-2 mb-3">
            <div class="col-md-6 flex-inline"><label>Plant *</label><select name="PLANT" id="paymentPlant" class="form-control" required style="width:100%"></select></div>
            <div class="col-md-6 flex-inline"><label>No. Payment</label><input id="paymentNoDisplay" class="form-control" readonly placeholder="Auto Generate" style="background:#efefef"></div>
            <div class="col-md-6 flex-inline"><label>Tanggal *</label><input type="date" name="PAYMENT_DATE" id="paymentDate" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
            <div class="col-md-6 flex-inline"><label>Supplier *</label><select name="SUPPLIER" id="paymentSupplier" class="form-control" required style="width:100%"></select></div>
            <div class="col-md-6 flex-inline"><label>Tipe Pembayaran *</label><div class="w-100 pt-1">
                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH" required><label class="form-check-label">CASH</label></div>
                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER" required><label class="form-check-label">TRANSFER</label></div>
            </div></div>
            <div class="col-md-6 flex-inline"><label>Slip No.</label><input name="SLIP_NO" id="paymentSlip" class="form-control" placeholder="Nomor slip / bukti transfer"></div>
            <div class="col-md-6 flex-inline"><label>Total Pembayaran *</label><input type="text" name="TOTAL_INPUT" id="paymentAmount" class="form-control text-end" placeholder="0" autocomplete="off" required></div>
            <div class="col-md-6 flex-inline"><label>Keterangan</label><input name="REMARK" id="paymentRemark" class="form-control" placeholder="Keterangan..."></div>
        </div>
        <div class="row mb-4"><div class="col-md-12"><div class="mode-wrapper">
            <label class="mode-card"><input type="radio" name="PAYMENT_MODE" value="FIFO" checked><div class="mode-content"><div class="mode-title">FIFO AUTO</div><div class="mode-sub">Sistem mengalokasikan pembayaran ke PO tertua yang masih memiliki outstanding.</div></div></label>
            <label class="mode-card"><input type="radio" name="PAYMENT_MODE" value="MANUAL"><div class="mode-content"><div class="mode-title">MANUAL</div><div class="mode-sub">Pilih PO yang ingin dibayar dan tentukan nominal alokasinya sendiri.</div></div></label>
        </div></div></div>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="summary-card summary-blue"><div class="summary-label">TOTAL PAYMENT</div><div class="summary-value" id="summaryTotalInput">Rp 0</div></div></div>
            <div class="col-md-3"><div class="summary-card summary-green"><div class="summary-label">TOTAL ALOKASI</div><div class="summary-value" id="summaryAllocated">Rp 0</div></div></div>
            <div class="col-md-3"><div class="summary-card summary-orange"><div class="summary-label">SISA ALOKASI</div><div class="summary-value" id="summaryRemaining">Rp 0</div></div></div>
            <div class="col-md-3"><div class="summary-card summary-red"><div class="summary-label">SELISIH</div><div class="summary-value" id="summaryDifference">Rp 0</div></div></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2"><h6 class="mb-0">Detail Alokasi PO</h6><button type="button" class="btn btn-success btn-sm" id="btnPickPO"><i class="ti ti-list-check"></i> Pilih PO</button></div>
        <div class="table-responsive"><table class="table table-bordered align-middle table-modern" id="paymentDetailTable">
            <thead class="table-light"><tr><th>No. PO</th><th>Tanggal PO</th><th>Material</th><th class="text-end">Total PO</th><th class="text-end">Outstanding</th><th class="text-end">Alokasi</th><th class="text-end">Sisa Setelah Bayar</th><th class="text-center">Status</th><th class="text-center">#</th></tr></thead>
            <tbody><tr class="empty-row"><td colspan="9" class="text-center text-muted py-5">Pilih plant dan supplier terlebih dahulu.</td></tr></tbody>
        </table></div>
        <div class="row mt-3"><div class="col-md-6"></div><div class="col-md-6"><div class="input-group"><span class="input-group-text fw-bold">Grand Total Alokasi</span><input type="text" id="grandTotal" class="form-control text-end fw-bold" readonly value="0" style="background:#efefef"></div></div></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" id="btnSavePayment" class="btn btn-primary">Simpan Payment</button></div>
</div></form></div></div>

<!-- PICKER PO -->
<div class="modal fade" id="modalPickPO" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">PILIH PO UNTUK DIBAYAR</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row mb-3"><div class="col-md-5"><input type="text" id="searchPO" class="form-control" placeholder="Cari nomor PO / material..."></div></div>
        <div class="table-responsive"><table class="table table-bordered table-hover align-middle"><thead class="table-light"><tr><th>#</th><th>No. PO</th><th>Tanggal</th><th>Material</th><th class="text-end">Total PO</th><th class="text-end">Sudah Dibayar</th><th class="text-end">Outstanding</th></tr></thead><tbody id="poPickerBody"></tbody></table></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" id="btnChoosePO">Pilih PO</button></div>
</div></div></div>

<!-- DETAIL PAYMENT -->
<div class="modal fade" id="PaymentDetailModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">DETAIL PAYMENT PO</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="paymentDetailContent"></div>
</div></div></div>

<script>
(function($) {
    'use strict';
    const base = '<?= base_url("payment/"); ?>';
    const state = {page:1, limit:10, order:'PAYMENT_DATE', dir:'DESC'};
    let detailRows = [], pickerRows = [], editing = false, activePayment = '', searchTimer = null, amountTimer = null;

    function esc(v){return String(v ?? '').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}
    function num(v){
        if(v===null||v===undefined||v==='') return 0;
        if(typeof v==='number') return Number.isFinite(v)?v:0;

        let s=String(v).trim().replace(/\s/g,'').replace(/Rp/gi,'');
        if(!s) return 0;

        // Format Indonesia, misalnya 217.500.000,00 -> 217500000.00
        if(s.includes(',')) {
            s=s.replace(/\./g,'').replace(',', '.');
        } else {
            // Angka dari DB, misalnya 217500000.000000, harus tetap desimal.
            // Titik berulang biasanya format ribuan, misalnya 217.500.000.
            const dots=(s.match(/\./g)||[]).length;
            if(dots>1) s=s.replace(/\./g,'');
        }

        const n=Number(s);
        return Number.isFinite(n)?n:0;
    }
    function round(v){return Math.round((num(v)+Number.EPSILON)*100)/100;}
    function money(v){return round(v).toLocaleString('id-ID',{minimumFractionDigits:0,maximumFractionDigits:2});}
    function rupiah(v){return 'Rp '+money(v);}
    function dateFmt(v){if(!v)return '-';const p=String(v).substring(0,10).split('-');if(p.length!==3)return String(v);const m=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];return `${p[2]} ${m[Number(p[1])-1]||p[1]} ${p[0]}`;}
    function alertMsg(msg,type='info'){if(window.Swal)Swal.fire({icon:type,text:msg});else alert(msg);}
    function showModal(id){if(window.bootstrap?.Modal)bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();else $('#'+id).modal('show');}
    function hideModal(id){if(window.bootstrap?.Modal)bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();else $('#'+id).modal('hide');}
    function loading(on){$('#tableLoading').toggleClass('d-none',!on);$('#tableWrapper').toggleClass('loading-hide',on);}
    function mode(){return $('input[name="PAYMENT_MODE"]:checked').val()||'FIFO';}
    function paymentAmount(){return round($('#paymentAmount').val());}

    function loadPlants(){
        $.getJSON(base+'get_plant').done(rows=>{
            rows=Array.isArray(rows)?rows:[];
            const opts='<option value="">-- Pilih Plant --</option>'+rows.map(r=>`<option value="${esc(r.id)}">${esc(r.text)}</option>`).join('');
            $('#paymentPlant').html(opts);
            $('#filterPlant').html('<option value="">Semua Plant</option>'+rows.map(r=>`<option value="${esc(r.id)}">${esc(r.text)}</option>`).join(''));
            if($.fn.select2){
                $('#filterPlant').select2({width:'100%',placeholder:'Semua plant',allowClear:true});
                $('#paymentPlant').select2({width:'100%',dropdownParent:$('#PaymentModal'),placeholder:'Pilih plant'});
                $('#paymentSupplier').select2({width:'100%',dropdownParent:$('#PaymentModal'),placeholder:'Pilih supplier'});
            }
            if(rows.length===1)$('#paymentPlant').val(String(rows[0].id)).trigger('change.select2');
            loadList(1);
        }).fail(()=>alertMsg('Gagal memuat daftar plant.','error'));
    }

    function loadSuppliers(selected=''){
        const plant=$('#paymentPlant').val();
        $('#paymentSupplier').html('<option value="">-- Pilih supplier --</option>');
        if(!plant){$('#paymentSupplier').trigger('change.select2');return;}
        $.getJSON(base+'get_supplier',{plant}).done(rows=>{
            rows=Array.isArray(rows)?rows:[];
            let opts='<option value="">-- Pilih supplier --</option>'+rows.map(r=>`<option value="${esc(r.id)}">${esc(r.text)}</option>`).join('');
            if(selected&&!rows.some(r=>String(r.id)===String(selected)))opts+=`<option value="${esc(selected)}">${esc(selected)}</option>`;
            $('#paymentSupplier').html(opts).val(selected||'').trigger('change.select2');
        }).fail(()=>alertMsg('Gagal memuat supplier.','error'));
    }

    function loadList(page=1){
        state.page=page;loading(true);
        $.getJSON(base+'load_data',{page,limit:state.limit,search:$('#searchPayment').val(),plant:$('#filterPlant').val()})
        .done(resp=>{
            if(!resp||!resp.status){$('#table-body').html(`<tr><td colspan="10" class="text-center text-danger py-4">${esc(resp?.message||'Gagal memuat data.')}</td></tr>`);return;}
            let rows=Array.isArray(resp.rows)?resp.rows:[];
            rows.sort((a,b)=>{let av=a[state.order]??'',bv=b[state.order]??'';let c=String(av).localeCompare(String(bv),'id',{numeric:true});return state.dir==='ASC'?c:-c;});
            if(!rows.length)$('#table-body').html('<tr><td colspan="10" class="text-center text-muted py-5">Belum ada data Payment PO.</td></tr>');
            else $('#table-body').html(rows.map(r=>{
                const status=r.STATUS||'POSTED';
                return `<tr>
                    <td>${esc(r.PLANT_NAME||r.PLANT||'-')}</td><td><strong>${esc(r.PAYMENT||'-')}</strong></td><td>${esc(dateFmt(r.PAYMENT_DATE))}</td>
                    <td>${esc(r.SUPPLIER_NAME||r.SUPPLIER||'-')}</td><td><span class="badge ${r.PAYMENT_MODE==='FIFO'?'bg-primary':'bg-info text-dark'}">${esc(r.PAYMENT_MODE||'-')}</span></td>
                    <td>${esc(r.PEMBAYARAN||'-')}</td><td class="text-end fw-semibold">${rupiah(r.TOTAL)}</td>
                    <td class="text-center"><span class="badge ${status==='POSTED'?'bg-success':'bg-secondary'}">${esc(status)}</span></td><td>${esc(r.REMARK||'-')}</td>
                    <td class="text-center text-nowrap"><button type="button" class="btn btn-info btn-sm btn-view" data-payment="${esc(r.PAYMENT)}" data-plant="${esc(r.PLANT)}" title="Detail"><i class="ti ti-eye"></i></button>
                    ${status==='POSTED'?` <button type="button" class="btn btn-warning btn-sm btn-edit" data-payment="${esc(r.PAYMENT)}" data-plant="${esc(r.PLANT)}" title="Ubah"><i class="ti ti-edit"></i></button> <button type="button" class="btn btn-danger btn-sm btn-void" data-payment="${esc(r.PAYMENT)}" data-plant="${esc(r.PLANT)}" title="Void"><i class="ti ti-trash"></i></button>`:''}</td>
                </tr>`;
            }).join(''));
            renderPagination(Number(resp.page||page),Number(resp.pages||1));
            $('#info').text(`Menampilkan ${rows.length} data pada halaman ${resp.page||page} dari total ${resp.total||0} transaksi.`);
        }).fail(xhr=>{ $('#table-body').html('<tr><td colspan="10" class="text-center text-danger py-4">Gagal menghubungi server.</td></tr>');console.error(xhr.responseText);})
        .always(()=>loading(false));
    }

    function renderPagination(page,pages){
        let h='<ul class="pagination pagination-sm mb-0">';
        h+=`<li class="page-item ${page<=1?'disabled':''}"><a class="page-link" href="#" data-page="${page-1}">&laquo;</a></li>`;
        for(let p=Math.max(1,page-2);p<=Math.min(pages,page+2);p++)h+=`<li class="page-item ${p===page?'active':''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        h+=`<li class="page-item ${page>=pages?'disabled':''}"><a class="page-link" href="#" data-page="${page+1}">&raquo;</a></li></ul>`;
        $('#pagination').html(h);
    }

    function resetForm(){
        editing=false;activePayment='';detailRows=[];pickerRows=[];
        $('#fPayment')[0].reset();$('#paymentNo,#paymentNoDisplay').val('');
        $('#paymentDate').val('<?= date('Y-m-d'); ?>');
        $('input[name="PAYMENT_MODE"][value="FIFO"]').prop('checked',true);
        $('input[name="PEMBAYARAN"]').prop('checked',false);
        $('#paymentDetailTable tbody').html('<tr class="empty-row"><td colspan="9" class="text-center text-muted py-5">Pilih plant dan supplier terlebih dahulu.</td></tr>');
        $('#summaryTotalInput,#summaryAllocated,#summaryRemaining,#summaryDifference').text('Rp 0');$('#grandTotal').val('0');
        $('#paymentModalTitle').text('Payment PO - TAMBAH');$('#btnSavePayment').show().prop('disabled',false).text('Simpan Payment');$('#btnPickPO').show();
        $('#paymentSupplier').html('<option value="">-- Pilih supplier --</option>').trigger('change.select2');
        $('#fPayment :input').prop('disabled',false);
    }

    function emptyDetail(msg){$('#paymentDetailTable tbody').html(`<tr class="empty-row"><td colspan="9" class="text-center text-muted py-5">${esc(msg)}</td></tr>`);}
    function findDetail(po){return detailRows.find(r=>String(r.PO_NO)===String(po));}
    function detailStatus(pay,out){pay=round(pay);out=round(out);if(pay<=0)return '<span class="badge bg-secondary">OPEN</span>';if(pay>=out)return '<span class="badge bg-success">PAID</span>';return '<span class="badge bg-warning text-dark">PARTIAL</span>';}

    function renderDetails(rows,editable=true){
        detailRows=(rows||[]).map(r=>({
            PO_NO:String(r.PO_NO??r.PO??''),PO_DATE:r.PO_DATE||'',MATERIAL:r.MATERIAL||'',MATERIAL_NAME:r.MATERIAL_NAME||'',
            PO_TOTAL:round(r.PO_TOTAL??r.TOTAL??0),PAID_BEFORE:round(r.PAID_BEFORE??r.PAID_AMOUNT_CALC??0),
            OUTSTANDING:round(r.OUTSTANDING??r.OUTSTANDING_BEFORE??r.REMAINING_AFTER??0),
            AMOUNT_ALLOCATED:round(r.AMOUNT_ALLOCATED||0),REMARK:r.REMARK||''
        })).filter(r=>r.PO_NO);
        if(!detailRows.length){emptyDetail('Tidak ada PO outstanding untuk supplier ini.');updateSummary();return;}
        $('#paymentDetailTable tbody').html(detailRows.map(r=>{
            const rem=Math.max(0,round(r.OUTSTANDING-r.AMOUNT_ALLOCATED));
            return `<tr data-po="${esc(r.PO_NO)}">
                <td><strong>${esc(r.PO_NO)}</strong></td><td>${esc(dateFmt(r.PO_DATE))}</td><td>${esc(r.MATERIAL_NAME||r.MATERIAL||'-')}</td>
                <td class="text-end">${rupiah(r.PO_TOTAL)}</td><td class="text-end">${rupiah(r.OUTSTANDING)}</td>
                <td><input class="form-control form-control-sm text-end allocation-input" data-po="${esc(r.PO_NO)}" value="${r.AMOUNT_ALLOCATED?money(r.AMOUNT_ALLOCATED):''}" placeholder="0" ${editable&&mode()==='MANUAL'?'':'readonly'}></td>
                <td class="text-end remain-cell">${rupiah(rem)}</td><td class="text-center status-cell">${detailStatus(r.AMOUNT_ALLOCATED,r.OUTSTANDING)}</td>
                <td class="text-center">${editable&&mode()==='MANUAL'?`<button type="button" class="btn btn-danger btn-sm btn-remove-detail" data-po="${esc(r.PO_NO)}"><i class="ti ti-trash"></i></button>`:'-'}</td>
            </tr>`;
        }).join(''));
        updateSummary();
    }

    function allocatedTotal(){let t=0;$('#paymentDetailTable .allocation-input').each(function(){t+=round($(this).val());});return round(t);}
    function updateSummary(){
        const total=paymentAmount(),allocated=allocatedTotal(),diff=round(total-allocated);
        $('#summaryTotalInput').text(rupiah(total));$('#summaryAllocated').text(rupiah(allocated));
        $('#summaryRemaining').text(rupiah(Math.max(0,diff)));$('#summaryDifference').text(rupiah(Math.abs(diff)<.01?0:diff));
        $('#grandTotal').val(money(allocated));
        const invalid=total<=0||allocated<=0||Math.abs(diff)>.009;
        $('#btnSavePayment').prop('disabled',invalid||!$('#paymentPlant').val()||!$('#paymentSupplier').val());
    }

    function normalizePOs(rows){
        return (Array.isArray(rows)?rows:[]).map(r=>({
            PO_NO:String(r.PO||r.PO_NO||''),PO_DATE:r.PO_DATE||'',MATERIAL:r.MATERIAL||'',MATERIAL_NAME:r.MATERIAL_NAME||'',
            PO_TOTAL:round(r.TOTAL??r.PO_TOTAL??0),PAID_BEFORE:round(r.PAID_AMOUNT_CALC??r.PAID_BEFORE??0),
            OUTSTANDING:round(r.OUTSTANDING??0),AMOUNT_ALLOCATED:0,REMARK:''
        })).filter(r=>r.PO_NO&&r.OUTSTANDING>0);
    }

    function loadOpenPO(){
        const plant=$('#paymentPlant').val(),supplier=$('#paymentSupplier').val();
        if(!plant||!supplier){detailRows=[];emptyDetail('Pilih plant dan supplier terlebih dahulu.');updateSummary();return;}
        $.getJSON(base+'get_open_po',{plant,supplier,payment:editing?activePayment:''}).done(rows=>{
            const pos=normalizePOs(rows);
            if(mode()==='FIFO'){
                let remaining=paymentAmount(),alloc=[];
                pos.forEach(po=>{if(remaining<=0)return;const a=round(Math.min(remaining,po.OUTSTANDING));if(a<=0)return;alloc.push({...po,AMOUNT_ALLOCATED:a});remaining=round(remaining-a);});
                renderDetails(alloc,false);
                if(remaining>.009)alertMsg('Total pembayaran melebihi outstanding PO yang tersedia. Selisih tidak dapat dialokasikan.','warning');
            }else{
                pickerRows=pos;
                emptyDetail('Klik "Pilih PO" untuk menentukan PO yang akan dibayar.');
                updateSummary();
            }
        }).fail(xhr=>{emptyDetail('Gagal memuat PO outstanding.');alertMsg('Gagal memuat PO outstanding.','error');console.error(xhr.responseText);});
    }

    function renderPicker(){
        const q=String($('#searchPO').val()||'').toLowerCase().trim();
        const selected=new Set(detailRows.map(r=>String(r.PO_NO)));
        const rows=pickerRows.filter(r=>!q||`${r.PO_NO} ${r.MATERIAL} ${r.MATERIAL_NAME}`.toLowerCase().includes(q));
        if(!rows.length){$('#poPickerBody').html('<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada PO outstanding.</td></tr>');return;}
        $('#poPickerBody').html(rows.map(r=>`<tr><td class="text-center"><input type="checkbox" class="pickPO" value="${esc(r.PO_NO)}" ${selected.has(r.PO_NO)?'checked':''} ${mode()==='FIFO'?'disabled':''}></td>
            <td><strong>${esc(r.PO_NO)}</strong></td><td>${esc(dateFmt(r.PO_DATE))}</td><td>${esc(r.MATERIAL_NAME||r.MATERIAL||'-')}</td>
            <td class="text-end">${rupiah(r.PO_TOTAL)}</td><td class="text-end">${rupiah(r.PAID_BEFORE)}</td><td class="text-end fw-semibold">${rupiah(r.OUTSTANDING)}</td></tr>`).join(''));
        $('#btnChoosePO').prop('disabled',mode()==='FIFO');
    }

    function openPicker(){
        if(!$('#paymentPlant').val()||!$('#paymentSupplier').val()){alertMsg('Pilih plant dan supplier terlebih dahulu.','warning');return;}
        $.getJSON(base+'get_open_po',{plant:$('#paymentPlant').val(),supplier:$('#paymentSupplier').val(),payment:editing?activePayment:''})
        .done(rows=>{pickerRows=normalizePOs(rows);$('#searchPO').val('');renderPicker();showModal('modalPickPO');})
        .fail(xhr=>{alertMsg('Gagal memuat daftar PO.','error');console.error(xhr.responseText);});
    }

    function choosePOs(){
        if(mode()!=='MANUAL'){hideModal('modalPickPO');return;}
        const selected=[];$('#poPickerBody .pickPO:checked').each(function(){selected.push(String($(this).val()));});
        const old=new Map(detailRows.map(r=>[String(r.PO_NO),r]));
        const chosen=pickerRows.filter(r=>selected.includes(r.PO_NO)).map(r=>({...r,AMOUNT_ALLOCATED:old.get(r.PO_NO)?.AMOUNT_ALLOCATED||0,REMARK:old.get(r.PO_NO)?.REMARK||''}));
        renderDetails(chosen,true);hideModal('modalPickPO');
    }

    function updateAllocation($input){
        const row=findDetail(String($input.data('po')));if(!row)return;
        let val=round($input.val());
        if(val<0)val=0;
        if(val>row.OUTSTANDING){val=row.OUTSTANDING;$input.val(money(val));alertMsg('Alokasi tidak boleh melebihi outstanding PO.','warning');}
        row.AMOUNT_ALLOCATED=val;
        const tr=$input.closest('tr');tr.find('.remain-cell').text(rupiah(Math.max(0,round(row.OUTSTANDING-val))));
        tr.find('.status-cell').html(detailStatus(val,row.OUTSTANDING));updateSummary();
    }

    function collectDetails(){
        const out=[];
        $('#paymentDetailTable tbody tr[data-po]').each(function(){
            const po=String($(this).attr('data-po')||''),r=findDetail(po);if(!r)return;
            const amount=round($(this).find('.allocation-input').val());
            if(amount>0)out.push({PO_NO:po,AMOUNT_ALLOCATED:amount,REMARK:r.REMARK||''});
        });
        return out;
    }

    function validate(){
        const total=paymentAmount(),alloc=allocatedTotal();
        if(!$('#paymentPlant').val()){alertMsg('Plant wajib dipilih.','warning');return false;}
        if(!$('#paymentSupplier').val()){alertMsg('Supplier wajib dipilih.','warning');return false;}
        if(!$('#paymentDate').val()){alertMsg('Tanggal pembayaran wajib diisi.','warning');return false;}
        if(!$('input[name="PEMBAYARAN"]:checked').val()){alertMsg('Tipe pembayaran wajib dipilih.','warning');return false;}
        if(total<=0){alertMsg('Total pembayaran harus lebih dari 0.','warning');return false;}
        if(!collectDetails().length){alertMsg('Belum ada PO yang dialokasikan.','warning');return false;}
        if(Math.abs(total-alloc)>.009){alertMsg(`Total alokasi (${rupiah(alloc)}) harus sama dengan total pembayaran (${rupiah(total)}).`,'warning');return false;}
        return true;
    }

    function resetAndOpen(){
        resetForm();showModal('PaymentModal');
        if($('#paymentPlant option').length>1&&!$('#paymentPlant').val())$('#paymentPlant').val($('#paymentPlant option:eq(1)').val()).trigger('change.select2');
        loadSuppliers();
    }

    function loadPayment(payment,plant,edit){
        $.getJSON(base+'detail',{payment,plant}).done(resp=>{
            if(!resp||!resp.status){alertMsg(resp?.message||'Gagal memuat detail payment.','error');return;}
            resetForm();editing=!!edit;activePayment=String(payment);
            const h=resp.header||{};
            $('#paymentNo').val(h.PAYMENT||payment);$('#paymentNoDisplay').val(h.PAYMENT||payment);
            $('#paymentPlant').val(String(h.PLANT||plant)).trigger('change.select2');
            $('#paymentDate').val(String(h.PAYMENT_DATE||'').substring(0,10));
            $('#paymentSlip').val(h.SLIP_NO||'');$('#paymentRemark').val(h.REMARK||'');$('#paymentAmount').val(money(h.TOTAL||0));
            $('input[name="PEMBAYARAN"]').prop('checked',false);
            $(`input[name="PEMBAYARAN"][value="${h.PEMBAYARAN||''}"]`).prop('checked',true);
            $('input[name="PAYMENT_MODE"]').prop('checked',false);
            $(`input[name="PAYMENT_MODE"][value="${h.PAYMENT_MODE||'FIFO'}"]`).prop('checked',true);
            $('#paymentModalTitle').text(edit?'Payment PO - UBAH':'Payment PO - DETAIL');
            $('#btnSavePayment').toggle(!!edit);$('#btnPickPO').toggle(!!edit);
            loadSuppliers(h.SUPPLIER);
            const rows=(resp.detail||[]).map(d=>({
                PO_NO:d.PO_NO,PO_DATE:d.PO_DATE,MATERIAL:d.MATERIAL,MATERIAL_NAME:d.MATERIAL_NAME,
                PO_TOTAL:round(d.PO_TOTAL||d.CURRENT_PO_TOTAL||0),PAID_BEFORE:round(d.PAID_BEFORE||0),
                OUTSTANDING:Math.max(0,round((d.PO_TOTAL||d.CURRENT_PO_TOTAL||0)-(d.PAID_BEFORE||0))),
                AMOUNT_ALLOCATED:round(d.AMOUNT_ALLOCATED||0),REMARK:d.REMARK||''
            }));
            renderDetails(rows,!!edit);
            if(!edit){$('#fPayment :input').prop('disabled',true);$('#PaymentModal .btn-close').prop('disabled',false);$('#btnSavePayment').hide();$('#btnPickPO').hide();}
            showModal('PaymentModal');updateSummary();
        }).fail(xhr=>{alertMsg('Gagal memuat payment.','error');console.error(xhr.responseText);});
    }

    $(function(){
        loadPlants();
        $('#btnAddPayment').on('click',resetAndOpen);
        $('#searchPayment').on('input',function(){clearTimeout(searchTimer);searchTimer=setTimeout(()=>loadList(1),300);});
        $('#filterPlant').on('change',()=>loadList(1));
        $('#btnResetFilter').on('click',function(){$('#searchPayment').val('');$('#filterPlant').val('').trigger('change.select2');loadList(1);});
        $('#pagination').on('click','a[data-page]',function(e){e.preventDefault();const p=Number($(this).data('page'));if(p>0)loadList(p);});
        $('#mainTable').on('click','th[data-sort]',function(){const key=$(this).data('sort');state.dir=state.order===key&&state.dir==='ASC'?'DESC':'ASC';state.order=key;loadList(state.page);});

        $('#paymentPlant').on('change',function(){
            if(editing)return;
            $('#paymentSupplier').val(null).trigger('change.select2');
            detailRows=[];
            emptyDetail('Pilih supplier terlebih dahulu.');
            loadSuppliers();
        });
        $('#paymentSupplier').on('change',function(){if(!$(this).val()){detailRows=[];emptyDetail('Pilih plant dan supplier terlebih dahulu.');updateSummary();return;}if(mode()==='FIFO')loadOpenPO();else{detailRows=[];emptyDetail('Klik "Pilih PO" untuk menentukan PO yang akan dibayar.');updateSummary();}});
        $('input[name="PAYMENT_MODE"]').on('change',function(){
            if(mode()==='FIFO'){
                $('#btnPickPO').html('<i class="ti ti-list-check"></i> Lihat PO');
                loadOpenPO();
            }else{
                $('#btnPickPO').html('<i class="ti ti-list-check"></i> Pilih PO');
                detailRows=[];
                emptyDetail('Klik "Pilih PO" untuk menentukan PO yang akan dibayar.');
                updateSummary();
            }
        });
        $('#paymentAmount').on('input',function(){updateSummary();if(mode()==='FIFO'&&$('#paymentSupplier').val()){clearTimeout(amountTimer);amountTimer=setTimeout(loadOpenPO,250);}});
        $('#btnPickPO').on('click',openPicker);$('#searchPO').on('input',renderPicker);$('#btnChoosePO').on('click',choosePOs);
        $('#paymentDetailTable').on('input change','.allocation-input',function(){updateAllocation($(this));});
        $('#paymentDetailTable').on('click','.btn-remove-detail',function(){const po=String($(this).data('po'));detailRows=detailRows.filter(r=>r.PO_NO!==po);renderDetails(detailRows,true);});

        $('#fPayment').on('submit',function(e){
            e.preventDefault();if(!validate())return;
            const fd=new FormData(this),details=collectDetails();
            details.forEach((d,i)=>{fd.append(`DETAIL[${i}][PO_NO]`,d.PO_NO);fd.append(`DETAIL[${i}][AMOUNT_ALLOCATED]`,Number(d.AMOUNT_ALLOCATED).toFixed(2));fd.append(`DETAIL[${i}][REMARK]`,d.REMARK||'');});
            $('#btnSavePayment').prop('disabled',true);
            $.ajax({url:base+(editing?'update':'create'),method:'POST',data:fd,processData:false,contentType:false,dataType:'json'})
            .done(resp=>{if(!resp||!resp.status){alertMsg(resp?.message||'Gagal menyimpan payment.','error');return;}hideModal('PaymentModal');alertMsg(resp.message||'Payment berhasil disimpan.','success');loadList(1);})
            .fail(xhr=>{alertMsg(xhr.responseJSON?.message||'Terjadi kesalahan saat menyimpan payment.','error');console.error(xhr.responseText);})
            .always(()=>$('#btnSavePayment').prop('disabled',false));
        });

        $('#table-body').on('click','.btn-view',function(){loadPayment($(this).data('payment'),$(this).data('plant'),false);});
        $('#table-body').on('click','.btn-edit',function(){loadPayment($(this).data('payment'),$(this).data('plant'),true);});
        $('#table-body').on('click','.btn-void',function(){
            const payment=$(this).data('payment'),plant=$(this).data('plant');
            const run=()=>$.post(base+'remove',{payment,plant},null,'json').done(resp=>{if(!resp||!resp.status){alertMsg(resp?.message||'Gagal membatalkan payment.','error');return;}alertMsg(resp.message||'Payment berhasil dibatalkan.','success');loadList(state.page);}).fail(xhr=>alertMsg(xhr.responseJSON?.message||'Gagal membatalkan payment.','error'));
            if(window.Swal)Swal.fire({icon:'warning',title:'Batalkan Payment?',text:`Payment ${payment} akan di-void dan saldo PO dihitung ulang.`,showCancelButton:true,confirmButtonText:'Ya, batalkan',cancelButtonText:'Tidak'}).then(r=>{if(r.isConfirmed)run();});
            else if(confirm(`Batalkan payment ${payment}? Saldo PO akan dihitung ulang.`))run();
        });
        $('#PaymentModal').on('hidden.bs.modal',function(){$('#fPayment :input').prop('disabled',false);});
    });
})(jQuery);
</script>
