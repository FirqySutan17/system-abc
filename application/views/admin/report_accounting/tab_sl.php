<div class="row mb-4">

    <div class="col-md-4">

        <div class="report-card bg-primary">

            <div class="report-card-title">TOTAL AMOUNT</div>

            <div class="report-card-value" id="summaryTotalAmount">Rp 0</div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="report-card bg-success">

            <div class="report-card-title">TOTAL SAVING AMOUNT</div>

            <div class="report-card-value" id="summaryTotalSavingAmount">Rp 0</div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="report-card bg-warning">

            <div class="report-card-title">TOTAL LOAN AMOUNT</div>

            <div class="report-card-value" id="summaryTotalLoanAmount">Rp 0</div>

        </div>

    </div>

</div>

<!-- FILTER -->
<div class="mb-4">

    <div class="row g-3">

        <div class="col-md-4">
            <input type="text" id="slSearch" class="form-control" placeholder="Cari doc, customer, related...">
        </div>

        <div class="col-md-2">
            <select id="slPlant" class="form-select">
                <option value="">Semua Plant</option>
            </select>
        </div>

        <div class="col-md-2">
            <select id="slCustomer" class="form-select">
                <option value="">Semua Customer</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" id="slDateFrom" class="form-control" value="<?= date('Y-m-01'); ?>">
        </div>

        <div class="col-md-2">
            <input type="date" id="slDateTo" class="form-control" value="<?= date('Y-m-d'); ?>">
        </div>

    </div>

</div>

<!-- RESULT -->
<div id="slResult"></div>

<script>
window.ReportSL = (function(){
    let state = { page: 1, limit: 20 };
    let INITIALIZED = false;

    function formatRupiah(value){ return Number(value||0).toLocaleString('id-ID'); }
    function formatDate(date){ if(!date) return '-'; return new Date(date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); }

    function init(){ if(INITIALIZED) return; INITIALIZED = true; loadPlant(); loadCustomer(); bindEvents(); loadData(); }

    function bindEvents(){
        $('#slSearch').on('keyup', ()=>loadData(1));
        $('#slPlant,#slCustomer').on('change', ()=>loadData(1));
        $('#slDateFrom,#slDateTo').on('change', ()=>loadData(1));
        $(document).on('click','#slReportPagination a', function(e){ e.preventDefault(); let page = $(this).data('sl-pagination-page'); if(page) loadData(page); });
    }

    function loadData(page=1){
        state.page = page;
        $.get('<?= base_url("report-accounting/load_sl"); ?>',{
            page: state.page, limit: state.limit,
            search: $('#slSearch').val(), plant: $('#slPlant').val(), customer: $('#slCustomer').val(),
            date_from: $('#slDateFrom').val(), date_to: $('#slDateTo').val()
        }, function(res){
            renderSummary(res.summary||{});
            renderTable(res.rows||[]);
            $('#slReportPagination').html(res.pagination||'');
            $('#slReportInfo').html(`Total : ${res.total||0} data`);
        }, 'json');
    }

    function renderSummary(summary){
        $('#summaryTotalAmount').html('Rp '+formatRupiah(summary.TOTAL_AMOUNT||summary.total_amount||0));
        $('#summaryTotalSavingAmount').html('Rp '+formatRupiah(summary.TOTAL_SAVING_AMOUNT||summary.total_saving_amount||0));
        $('#summaryTotalLoanAmount').html('Rp '+formatRupiah(summary.TOTAL_LOAN_AMOUNT||summary.total_loan_amount||0));
    }

    function renderTable(rows){
        let wrapper = $('#slResult'); wrapper.html('');
        if(!rows || rows.length===0){
            let tableEmpty = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-modern">
                        <thead>
                            <tr>
                                <th class="text-center">Plant</th>
                                <th class="text-center">Plant Name</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Customer Name</th>
                                <th>Doc No</th>
                                <th>Type</th>
                                <th class="text-center">Date</th>
                                <th class="text-end">Amount</th>
                                <th>Remark</th>
                                <th>Related</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center text-muted">Data Tidak Ditemukan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>`;

            wrapper.html(tableEmpty);
            return;
        }

        let totalAmount = 0;
        let rowsHtml = rows.map(function(group){
            let details = group.DETAILS || [];
            let rowCount = Math.max(details.length,1);
            let html = '';
            if(details.length===0){
                html += `
                    <tr>
                        <td class="text-center align-middle" rowspan="${rowCount}">${group.PLANT||'-'}</td>
                        <td class="text-center align-middle" rowspan="${rowCount}">${group.PLANT_NAME||'-'}</td>
                        <td class="text-center align-middle" rowspan="${rowCount}">${group.CUSTOMER||'-'}</td>
                        <td class="text-center align-middle" rowspan="${rowCount}">${group.CUSTOMER_NAME||'-'}</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="text-end">Rp 0</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>`;
            } else {
                details.forEach(function(d, idx){
                    html += `
                        <tr>
                            ${idx===0?`<td class="text-center align-middle" rowspan="${rowCount}">${group.PLANT||'-'}</td><td class="text-center align-middle" rowspan="${rowCount}">${group.PLANT_NAME||'-'}</td><td class="text-center align-middle" rowspan="${rowCount}">${group.CUSTOMER||'-'}</td><td class="text-center align-middle" rowspan="${rowCount}">${group.CUSTOMER_NAME||'-'}</td>`:''}
                            <td>${d.DOC_NO||'-'}</td>
                            <td>${d.TYPE||'-'}</td>
                            <td class="text-center">${formatDate(d.DATE||d.SV_DATE||d.LOAN_DATE)}</td>
                            <td class="text-end">Rp ${formatRupiah(d.AMOUNT||d.TOTAL||0)}</td>
                            <td>${d.REMARK||'-'}</td>
                            <td>${d.RELATED||'-'}</td>
                        </tr>`;
                    totalAmount += Number(d.AMOUNT||d.TOTAL||0);
                });
            }
            return html;
        }).join('');

        let table = `
            <div class="table-responsive">
                <table class="table table-hover align-middle table-modern">
                    <thead>
                        <tr>
                            <th class="text-center">Plant</th>
                            <th class="text-center">Plant Name</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Customer Name</th>
                            <th>Doc No</th>
                            <th>Type</th>
                            <th class="text-center">Date</th>
                            <th class="text-end">Amount</th>
                            <th>Remark</th>
                            <th>Related</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="7" class="text-end">Total Semua</td>
                            <td class="text-end">Rp ${formatRupiah(totalAmount)}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>`;

        wrapper.html(table);
    }

    function loadPlant(){ $.get('<?= base_url("payment/get_plant"); ?>', function(rows){ rows.forEach(function(r){ $('#slPlant').append(`<option value="${r.id}">${r.text}</option>`); }); }, 'json'); }
    function loadCustomer(){ $.get('<?= base_url("saving/get_customer"); ?>', function(rows){ rows.forEach(function(r){ $('#slCustomer').append(`<option value="${r.id}">${r.text}</option>`); }); }, 'json'); }

    return { init: init, loadData: loadData };
})();

$(document).ready(function(){ if(window.ReportSL) ReportSL.init(); });
</script>
