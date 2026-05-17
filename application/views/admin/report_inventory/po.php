<?php
$userPlant = $this->session->userdata('plant');
$roleId    = $this->session->userdata('role_id');
?>

<div class="po-report-wrap">
    <!-- SUMMARY -->
    <div class="row mb-4" id="summaryWrapper">

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    TOTAL PO
                </div>

                <div class="summary-value" id="sumPO">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    SUPPLIER
                </div>

                <div class="summary-value" id="sumSupplier">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    CUSTOMER
                </div>

                <div class="summary-value" id="sumCustomer">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    TOTAL QTY
                </div>

                <div class="summary-value" id="sumQty">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    TOTAL WEIGHT
                </div>

                <div class="summary-value" id="sumWeight">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card summary-money">
                <div class="summary-label">
                    TOTAL AMOUNT
                </div>

                <div class="summary-value" id="sumAmount">
                    0
                </div>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="report-filter-card">
        <div class="row g-3 align-items-end">

            <div class="col-md-2">
                <label class="form-label fw-semibold">Plant</label>
                <select id="filter_plant" class="form-control">
                    <?php foreach ($plants as $p): ?>
                        <?php if ($p->CODE == '*') continue; ?>
                        <option value="<?= $p->CODE ?>">
                            <?= $p->CODE_NAME ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Supplier</label>
                <select id="filter_supplier" class="form-control">
                    <option value=""></option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s->CUST ?>">
                            <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">PO Number</label>
                <input
                    type="text"
                    id="filter_po"
                    class="form-control"
                    placeholder="Search PO..."
                >
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date From</label>
                <input
                    type="date"
                    id="filter_date_from"
                    class="form-control"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date To</label>
                <input
                    type="date"
                    id="filter_date_to"
                    class="form-control"
                >
            </div>

            <div class="col-md-10"></div>

            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button
                        class="btn btn-success dropdown-toggle w-100"
                        data-bs-toggle="dropdown"
                    >
                        <i class="fa fa-download me-1"></i>
                        Export
                    </button>

                    <ul class="dropdown-menu w-100">
                        <li>
                            <a href="#" class="dropdown-item" id="exportExcel">
                                <i class="fa fa-file-excel text-success me-2"></i>
                                Export Excel
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item" id="exportPDF">
                                <i class="fa fa-file-pdf text-danger me-2"></i>
                                Export PDF
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- LOADING -->
    <div id="poLoading" class="report-loading d-none">
        <div class="text-center">
            <div class="spinner-border text-primary"></div>
            <div class="fw-semibold mt-3">Loading report...</div>
            <small class="text-muted">Please wait a moment</small>
        </div>
    </div>

    <!-- CONTENT -->
    <div id="poReportWrapper"></div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div id="pageInfo" class="small text-muted"></div>
        <div id="pagination"></div>
    </div>

</div>

<style>

.po-report-wrap{
    padding:4px;
}

.report-filter-card{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 25px rgba(15,23,42,.05);
    margin-bottom:24px;
}

.report-loading{
    min-height:280px;
    background:#fff;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    border:1px solid #edf2f7;
}

#poReportWrapper.loading{
    opacity:.35;
    pointer-events:none;
    transition:.25s;
}
.po-card{
    border:1px solid #e9ecef;
    border-radius:18px;
    overflow:hidden;
    margin-bottom:22px;
    box-shadow:0 8px 30px rgba(0,0,0,.05);
}

.po-head{
    background:linear-gradient(135deg,#0F4C81,#1d6fb1);
    color:#fff;
    padding:18px 22px;
}

.po-title{
    font-size:18px;
    font-weight:700;
}

.po-body{
    padding:10px;
    background:#fff;
}

.status-badge{
    padding:5px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.status-open{
    background:#fff3cd;
    color:#9a6700;
}

.status-received{
    background:#d1e7dd;
    color:#0f5132;
}

.table-detail th{
    background:#f8f9fa;
    font-size:13px;
    text-transform:uppercase;
}

.table-detail td{
    vertical-align:middle;
}

.subtotal-row{
    background:#f8fafc;
    font-weight:700;
}

.po-meta-grid{
    margin-top:14px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px 28px;
    font-size:13px;
}

.meta-label{
    display:inline-block;
    width:85px;
    font-weight:700;
    color:rgba(255,255,255,.85);
    letter-spacing:.4px;
}

.meta-value{
    font-weight:500;
    color:#fff;
}
.summary-card{
    background:#fff;
    border-radius:18px;
    padding:20px;
    border:1px solid #edf2f7;
    box-shadow:0 8px 24px rgba(15,23,42,.05);
    height:100%;
    transition:.2s;
}

.summary-card:hover{
    transform:translateY(-2px);
}

.summary-label{
    font-size:12px;
    font-weight:700;
    color:#64748b;
    letter-spacing:.5px;
    margin-bottom:10px;
}

.summary-value{
    font-size:18px;
    font-weight:800;
    color:#0f172a;
}

.summary-money .summary-value{
    color:#0F4C81;
}
</style>

<script>
const POReport = {

    state:{
        page:1,
        limit:10
    },

    init(){
        this.initSelect2();
        this.bind();

        setTimeout(()=>{
            this.setDefault();
            this.load();
        },100);
    },

    setDefault(){
        const now = new Date();

        const yyyy = now.getFullYear();
        const mm   = String(now.getMonth()+1).padStart(2,'0');
        const dd   = String(now.getDate()).padStart(2,'0');

        const today = `${yyyy}-${mm}-${dd}`;
        const first = `${yyyy}-${mm}-01`;

        $('#filter_date_from').val(first);
        $('#filter_date_to').val(today);

        // skip CODE = *
        const $plant = $('#filter_plant');

        const firstValid = $plant.find('option').filter(function () {
            const val = ($(this).val() || '').trim();

            return val !== '' && val !== '*';
        }).first();

        if(firstValid.length){
            $plant.val(firstValid.val()).trigger('change.select2');
        }

        console.log('DEFAULT PLANT =', $plant.val());
    },

    initSelect2(){
        $('#filter_plant').select2({
            width:'100%'
        });

        $('#filter_supplier').select2({
            width:'100%',
            placeholder:'Choose Supplier',
            allowClear:true
        });
    },

    showLoading(){
        $('#poLoading').removeClass('d-none');
        $('#poReportWrapper').addClass('loading');
    },

    hideLoading(){
        $('#poLoading').addClass('d-none');
        $('#poReportWrapper').removeClass('loading');
    },

    bind(){
        let timer;

        $('#filter_po').on('keyup', ()=>{
            clearTimeout(timer);
            timer = setTimeout(()=>{
                this.state.page = 1;
                this.load();
            },300);
        });

        $('#filter_plant,#filter_supplier,#filter_date_from,#filter_date_to')
            .on('change', ()=>{
                this.state.page = 1;
                this.load();
            });

        $('#exportExcel').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_excel_po"); ?>?'+this.query(),
                '_blank'
            );
        });

        $('#exportPDF').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_pdf_po"); ?>?'+this.query(),
                '_blank'
            );
        });

        $(document).on('click','#pagination a',(e)=>{
            e.preventDefault();

            const page = $(e.currentTarget).data('page');

            if(page){
                this.state.page = page;
                this.load();
            }
        });
    },

    query(){
        return $.param({
            page      : this.state.page,
            limit     : this.state.limit,
            plant     : $('#filter_plant').val(),
            supplier  : $('#filter_supplier').val(),
            po        : $('#filter_po').val(),
            date_from : $('#filter_date_from').val(),
            date_to   : $('#filter_date_to').val()
        });
    },

    money(x){
        return Number(x || 0).toLocaleString('id-ID');
    },

    decimal(x){
        return Number(x || 0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    },

    dateIndoLong(date){
        if(!date) return '-';

        const bulan = [
            'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
            'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
        ];

        const d = new Date(date);

        return `${String(d.getDate()).padStart(2,'0')} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    },

    renderSummary(summary){

        if(!summary){

            return;

        }

        $('#sumPO').text(
            this.money(summary.TOTAL_PO || 0)
        );

        $('#sumSupplier').text(
            this.money(summary.TOTAL_SUPPLIER || 0)
        );

        $('#sumCustomer').text(
            this.money(summary.TOTAL_CUSTOMER || 0)
        );

        $('#sumQty').text(
            this.decimal(summary.TOTAL_QTY || 0)
        );

        $('#sumWeight').text(
            this.decimal(summary.TOTAL_BERAT || 0)
        );

        $('#sumAmount').text(
            'Rp ' + this.money(summary.TOTAL_AMOUNT || 0)
        );
    },

    render(rows){
        const wrap = $('#poReportWrapper').empty();

        if(!rows || !rows.length){
            wrap.html(`
                <div class="text-center py-5 text-muted">
                    No data found
                </div>
            `);
            return;
        }

        const grouped = {};

        rows.forEach(r=>{
            const key = r.PO+'|'+r.PLANT;

            if(!grouped[key]){
                grouped[key] = {
                    PO: r.PO,
                    PLANT_NAME: r.PLANT_NAME,
                    PO_DATE: r.PO_DATE,
                    PO_NAME: r.PO_NAME,
                    SUPPLIER: r.SUPPLIER,
                    SUPPLIER_NAME: r.SUPPLIER_NAME,
                    STATUS: r.STATUS,
                    REMARK: r.REMARK,

                    NO_TRUCK: r.NO_TRUCK,
                    DRIVER: r.DRIVER,

                    HEADER_QTY: r.HEADER_QTY,
                    HEADER_BERAT: r.HEADER_BERAT,
                    HEADER_HARGA: r.HEADER_HARGA,
                    HEADER_TOTAL: r.HEADER_TOTAL,

                    DETAIL:[]
                };
            }

            grouped[key].DETAIL.push(r);
        });

        Object.values(grouped).forEach(po=>{

            let badge = po.STATUS
                ? `<span class="status-badge status-received">RECEIVED</span>`
                : `<span class="status-badge status-open">OPEN</span>`;

            let detailRows = '';
            let subQty = 0;
            let subWeight = 0;
            let subTotal = 0;

            po.DETAIL.forEach(r=>{

                subQty += Number(r.JUMLAH || 0);
                subWeight += Number(r.BERAT || 0);
                subTotal += Number(r.TOTAL || 0);

                detailRows += `
                    <tr>
                        <td>${r.CUSTOMER_NAME}</td>
                        <td>${r.MATERIAL_NAME}</td>
                        <td class="text-end">${this.decimal(r.JUMLAH)}</td>
                        <td class="text-end">${this.decimal(r.BERAT)}</td>
                        <td class="text-end">${this.money(r.HARGA)}</td>
                        <td class="text-end fw-semibold">${this.money(r.TOTAL)}</td>
                    </tr>
                `;
            });

            wrap.append(`
                <div class="po-card">
                    <div class="po-head">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="po-title">#${po.PO}</div>
                            ${badge}
                        </div>

                        <div class="po-meta-grid">
                            <div>
                                <span class="meta-label">PLANT</span>
                                <span class="meta-value">: ${po.PLANT_NAME}</span>
                            </div>

                            <div>
                                <span class="meta-label">SUPPLIER</span>
                                <span class="meta-value">: ${po.SUPPLIER} - ${po.SUPPLIER_NAME}</span>
                            </div>

                            <div>
                                <span class="meta-label">PO DATE</span>
                                <span class="meta-value">: ${this.dateIndoLong(po.PO_DATE)}</span>
                            </div>

                            <div>
                                <span class="meta-label">PO TYPE</span>
                                <span class="meta-value">: ${po.PO_NAME || '-'}</span>
                            </div>
                            <div>
                                <span class="meta-label">REMARK</span>
                                <span class="meta-value">: ${po.REMARK || '-'}</span>
                            </div>
                            <div>
                                <span class="meta-label">DRIVER / NO.</span>
                                <span class="meta-value">
                                    : ${po.DRIVER || '-'} / ${po.NO_TRUCK || '-'}
                                </span>
                            </div>

                            <div>
                                <span class="meta-label">QTY</span>
                                <span class="meta-value">
                                    : ${this.decimal(po.HEADER_QTY)}
                                </span>
                            </div>

                            <div>
                                <span class="meta-label">WEIGHT</span>
                                <span class="meta-value">
                                    : ${this.decimal(po.HEADER_BERAT)}
                                </span>
                            </div>

                            <div>
                                <span class="meta-label">PRICE</span>
                                <span class="meta-value">
                                    : Rp ${this.money(po.HEADER_HARGA)}
                                </span>
                            </div>

                            <div>
                                <span class="meta-label">TOTAL</span>
                                <span class="meta-value">
                                    : Rp ${this.money(po.HEADER_TOTAL)}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="po-body">
                        <table class="table table-bordered table-hover table-detail mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Material</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Weight</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${detailRows}
                                <tr class="subtotal-row">
                                    <td colspan="2">SUBTOTAL</td>
                                    <td class="text-end">${this.decimal(subQty)}</td>
                                    <td class="text-end">${this.decimal(subWeight)}</td>
                                    <td></td>
                                    <td class="text-end">${this.money(subTotal)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `);
        });
    },

    load(){
        this.showLoading();

        $.get(
            '<?= base_url("report-inventory/load_data"); ?>',
            this.query(),
            (resp)=>{
                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                this.renderSummary(resp.summary || {});
                this.render(resp.rows || []);
                $('#pagination').html(resp.pagination || '');
                $('#pageInfo').html(`
                    Showing page ${resp.page} of ${resp.pages}
                    (${this.money(resp.total)} PO)
                `);
            }
        )
        .fail(()=>{
            $('#poReportWrapper').html(`
                <div class="alert alert-danger mb-0">
                    Failed load report data
                </div>
            `);
        })
        .always(()=>{
            this.hideLoading();
        });
    },
};

$(function(){
    POReport.init();
});
</script>