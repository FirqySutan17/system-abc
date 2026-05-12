<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="receive-report-wrap">
    <!-- FILTER -->
    <div class="report-filter-card">
        <div class="row g-3 align-items-end">

            <div class="col-md-2">
                <label class="form-label fw-semibold">Plant</label>
                <select id="rc_filter_plant" class="form-control">
                    <option value="">Choose Plant</option>
                    <?php foreach ($plants as $p): ?>
                        <?php if ($p->CODE != '*'): ?>
                            <option value="<?= $p->CODE ?>">
                                <?= $p->CODE_NAME ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Supplier</label>
                <select id="rc_filter_supplier" class="form-control">
                    <option value="">Choose Supplier</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s->CUST ?>">
                            <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Receive / PO</label>
                <input
                    type="text"
                    id="rc_filter_receive"
                    class="form-control"
                    placeholder="Search Receive / PO..."
                >
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date From</label>
                <input type="date" id="rc_date_from" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Date To</label>
                <input type="date" id="rc_date_to" class="form-control">
            </div>

            <div class="col-md-10"></div>
            
            <div class="col-md-2">
                <div class="d-grid gap-2">
                    <div class="btn-group">
                        <button
                            class="btn btn-success dropdown-toggle"
                            data-bs-toggle="dropdown"
                        >
                            <i class="fa fa-download me-1"></i> Export
                        </button>

                        <ul class="dropdown-menu w-100">
                            <li>
                                <a href="#" class="dropdown-item" id="rc_exportExcel">
                                    <i class="fa fa-file-excel text-success me-2"></i>
                                    Export Excel
                                </a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item" id="rc_exportPDF">
                                    <i class="fa fa-file-pdf text-danger me-2"></i>
                                    Export PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- LOADING -->
    <div id="rcLoading" class="report-loading d-none">
        <div class="text-center">
            <div class="spinner-border text-primary"></div>
            <div class="fw-semibold mt-3">Loading report...</div>
            <small class="text-muted">Please wait a moment</small>
        </div>
    </div>

    <!-- CONTENT -->
    <div id="receiveReportWrapper"></div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div id="rc_pageInfo" class="text-muted small"></div>
        <div id="rc_pagination"></div>
    </div>

</div>

<style>
.receive-report-wrap{
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

.receive-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #edf2f7;
}

.receive-head{
    background:linear-gradient(135deg,#0f4c81,#2563eb);
    color:#fff;
    padding:22px 24px;
}

.receive-title{
    font-size:22px;
    font-weight:700;
    letter-spacing:.5px;
}

.status-badge{
    padding:7px 14px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
}

.status-open{
    background:#fff3cd;
    color:#856404;
}

.status-received{
    background:#d1fae5;
    color:#065f46;
}

.meta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px 40px;
    margin-top:18px;
    font-size:14px;
}

.meta-item{
    display:flex;
    gap:8px;
}

.meta-label{
    min-width:110px;
    opacity:.85;
    font-weight:600;
}

.meta-value{
    flex:1;
    font-weight:500;
}

.receive-body{
    padding:24px;
}

.attach-badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
    background:#e0f2fe;
    color:#075985;
}

.table-detail{
    margin:0;
    font-size:14px;
}

.table-detail thead th{
    background:#f8fafc;
    border-color:#e5e7eb;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.4px;
}

.table-detail td{
    border-color:#edf2f7;
    vertical-align:middle;
}

.subtotal-row{
    background:#f8fafc;
    font-weight:700;
}

@media(max-width:768px){
    .meta-grid{
        grid-template-columns:1fr;
        gap:8px;
    }

    .receive-title{
        font-size:18px;
    }

    .receive-head,
    .receive-body{
        padding:18px;
    }
}
</style>

<script>
window.ReceiveReport = {

    loaded:false,
    page:1,
    limit:10,

    init(){
        if(this.loaded) return;
        this.loaded = true;

        this.initSelect2();
        this.bind();

        setTimeout(()=>{
            this.setDefault();
            this.load();
        },100);
    },

    initSelect2(){
        $('#rc_filter_plant').select2({
            width:'100%',
            placeholder:'Choose Plant'
        });

        $('#rc_filter_supplier').select2({
            width:'100%',
            placeholder:'Choose Supplier',
            allowClear:true
        });
    },

    setDefault(){
        const now = new Date();

        const yyyy = now.getFullYear();
        const mm = String(now.getMonth()+1).padStart(2,'0');
        const dd = String(now.getDate()).padStart(2,'0');

        $('#rc_date_from').val(`${yyyy}-${mm}-01`);
        $('#rc_date_to').val(`${yyyy}-${mm}-${dd}`);

        const firstPlant = $('#rc_filter_plant option')
            .filter(function(){
                return $(this).val() !== '';
            })
            .first()
            .val();

        if(firstPlant){
            $('#rc_filter_plant').val(firstPlant).trigger('change');
        }
    },

    bind(){
        let timer;

        $('#rc_btnFilter').on('click', ()=>{
            this.page = 1;
            this.load();
        });

        $('#rc_filter_receive').on('keyup', ()=>{
            clearTimeout(timer);
            timer = setTimeout(()=>{
                this.page = 1;
                this.load();
            },300);
        });

        $('#rc_filter_plant,#rc_filter_supplier,#rc_date_from,#rc_date_to')
            .on('change', ()=>{
                this.page = 1;
                this.load();
            });

        $('#rc_exportExcel').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_excel_receive"); ?>?'+$.param(this.query()),
                '_blank'
            );
        });

        $('#rc_exportPDF').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_pdf_receive"); ?>?'+$.param(this.query()),
                '_blank'
            );
        });
    },

    query(){
        return {
            page:this.page,
            limit:this.limit,
            plant:$('#rc_filter_plant').val(),
            supplier:$('#rc_filter_supplier').val(),
            receive:$('#rc_filter_receive').val(),
            date_from:$('#rc_date_from').val(),
            date_to:$('#rc_date_to').val()
        };
    },

    showLoading(){
        $('#rcLoading').removeClass('d-none');
        $('#receiveReportWrapper').hide();
    },

    hideLoading(){
        $('#rcLoading').addClass('d-none');
        $('#receiveReportWrapper').show();
    },

    load(page=null){
        if(page) this.page = page;

        this.showLoading();

        $.get(
            '<?= base_url("report-inventory/load_receive"); ?>',
            this.query(),
            (resp)=>{
                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                this.render(resp.rows || []);
                $('#rc_pagination').html(resp.pagination || '');
                $('#rc_pageInfo').html(`Total data : ${resp.total || 0}`);
            }
        )
        .always(()=>{
            this.hideLoading();
        });
    },

    render(rows){
        const wrap = $('#receiveReportWrapper').empty();

        if(!rows.length){
            wrap.html(`
                <div class="text-center py-5 text-muted bg-white rounded-4 border">
                    No data found
                </div>
            `);
            return;
        }

        rows.forEach(rc=>{

            let badge = `
                <span class="status-badge status-received">
                    RECEIVED
                </span>
            `;

            let attachment = rc.ATTACH_FILE_NAME
                ? `<span class="attach-badge">Available</span>`
                : '-';

            let detailRows = '';
            let subQty = 0;
            let subWeight = 0;
            let subTotal = 0;

            (rc.DETAIL || []).forEach(r=>{

                subQty += Number(r.JUMLAH || 0);
                subWeight += Number(r.BERAT || 0);
                subTotal += Number(r.TOTAL || 0);

                detailRows += `
                    <tr>
                        <td>${r.CUSTOMER_NAME}</td>
                        <td>${r.MATERIAL_NAME}</td>
                        <td class="text-end">${this.decimal(r.JUMLAH)}</td>
                        <td class="text-end">${this.decimal(r.BERAT)}</td>
                        <td class="text-end">${this.decimal(r.SUSUT_JUMLAH)}</td>
                        <td class="text-end">${this.decimal(r.SUSUT_BERAT)}</td>
                        <td class="text-end">${this.money(r.HARGA)}</td>
                        <td class="text-end fw-semibold">${this.money(r.TOTAL)}</td>
                        <td>${r.KETERANGAN || '-'}</td>
                        <td class="text-center">${r.STATUS || '-'}</td>
                    </tr>
                `;
            });

            wrap.append(`
                <div class="receive-card">

                    <div class="receive-head">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="receive-title">#${rc.RECEIVE}</div>
                            ${badge}
                        </div>

                        <div class="meta-grid">
                            <div class="meta-item">
                                <span class="meta-label">PLANT</span>
                                <span class="meta-value">: ${rc.PLANT_NAME}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">SUPPLIER</span>
                                <span class="meta-value">: ${rc.SUPPLIER} - ${rc.SUPPLIER_NAME}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">PO</span>
                                <span class="meta-value">: ${rc.PO}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">RECEIVE DATE</span>
                                <span class="meta-value">: ${this.dateIndoLong(rc.RECEIVE_DATE)}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">NOTA</span>
                                <span class="meta-value">: ${rc.NOTA || '-'}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">REF NO</span>
                                <span class="meta-value">: ${rc.NO_REF || '-'}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">PAYMENT</span>
                                <span class="meta-value">: ${rc.PEMBAYARAN_NAME || rc.PEMBAYARAN || '-'}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">PAY TYPE</span>
                                <span class="meta-value">: ${rc.JENIS_PAY || '-'}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">SLIP NO</span>
                                <span class="meta-value">: ${rc.SLIP_NO || '-'}</span>
                            </div>

                            <div class="meta-item">
                                <span class="meta-label">ATTACHMENT</span>
                                <span class="meta-value">: ${attachment}</span>
                            </div>

                            <div class="meta-item" style="grid-column:1 / -1">
                                <span class="meta-label">REMARK</span>
                                <span class="meta-value">: ${rc.REMARK || '-'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="receive-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-detail">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Material</th>
                                        <th class="text-end" style="vertical-align: middle">Qty</th>
                                        <th class="text-end" style="vertical-align: middle">Weight</th>
                                        <th class="text-end" style="vertical-align: middle">Shrink Qty</th>
                                        <th class="text-end" style="vertical-align: middle">Shrink Weight</th>
                                        <th class="text-end" style="vertical-align: middle">Price</th>
                                        <th class="text-end" style="vertical-align: middle">Total</th>
                                        <th class="text-center">Remark</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    ${detailRows}

                                    <tr class="subtotal-row">
                                        <td colspan="2">SUBTOTAL</td>
                                        <td class="text-end" style="vertical-align: middle">${this.decimal(subQty)}</td>
                                        <td class="text-end" style="vertical-align: middle">${this.decimal(subWeight)}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end" style="vertical-align: middle">${this.money(subTotal)}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            `);
        });
    },

    dateIndoLong(date){
        if(!date) return '-';

        const bulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        const d = new Date(date);

        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    },

    money(v){
        return Number(v || 0).toLocaleString('id-ID');
    },

    decimal(v){
        return Number(v || 0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }
};

$(document).on('click','#rc_pagination a',function(e){
    e.preventDefault();

    const page = $(this).data('page');

    if(page){
        ReceiveReport.load(page);
    }
});
</script>