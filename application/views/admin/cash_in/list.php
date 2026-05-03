<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">CASH IN</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari Cash In..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group "></div>
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#CashInAdd">
                        <i class="ti ti-plus"></i> Tambah Cash In
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align: center;">Plant</th>
                            <th data-order="CASH_IN" style="text-align: center;">No. Cash In</th>
                            <th data-order="CASHIN_DATE" style="text-align: center;">Tanggal</th>
                            <th data-order="CUSTOMER" style="text-align: center;">Customer</th>
                            <th data-order="AMOUNT" style="text-align: center;">Jumlah</th>
                            <th data-order="REMARK" style="text-align: center;">No. Bon / Remark</th>
                            <th style="text-align: center;">#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<style>
    .flex-inline {
        padding: 2px 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        align-content: center;
        flex-wrap: nowrap;
        flex-direction: row;
    }
    label {
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
    .modal-xl {
        --bs-modal-width: 90%;
    }
</style>

<!-- MODAL ADD CASH IN -->
<div class="modal fade" id="CashInAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fCashInAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cash In - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="PLANT_ADD" name="PLANT_SELECT" class="form-control"></select>
                            <input type="hidden" name="PLANT" id="PLANT_HIDDEN">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Customer *</label>
                            <select id="CUSTOMER" name="CUSTOMER" class="form-control" required></select>
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Transaksi</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Slip No *</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Rekening *</label>
                            <select id="NO_REK" name="NO_REK" class="form-control" required></select>
                        </div>
                        
                        
                        
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="CASHIN_DATE" name="CASHIN_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Metode</label>
                            <div style="padding:5px 0px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mode_cash_in" value="FIFO" checked>
                                    <label class="form-check-label">AUTO</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="mode_cash_in" value="MANUAL">
                                    <label class="form-check-label">MANUAL</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Bon *</label>
                            <input name="BON" id="BON" class="form-control" placeholder="Tulis disini..." required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Jumlah</label>
                            <input name="JUMLAH" id="JUMLAH_INPUT" class="form-control" placeholder="Masukkan jumlah cash in">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Attachment</label>
                            <input type="file" name="ATTACHMENT" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        
                        <div class="col-md-6 mt-2 flex-inline">
                            <div id="depositInfoBox" class="alert alert-info py-2 px-3" style="display:none; margin-bottom: 0px">
                                💰 Saldo Deposit Customer: <b id="depositAmount">0</b>
                            </div>
                        </div>
                        
                    </div>

                    <div id="previewFifoBox">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 style="margin-bottom: 0px">Preview Alokasi Otomatis (FIFO)</h5>
                            <!-- <button type="button" class="btn btn-warning btn-sm" id="btnOverrideInvoice">
                                Override Manual
                            </button> -->
                        </div>

                        <table class="table table-bordered" id="stockActualDetailTableAdd">
                            <thead>
                                <tr>
                                    <th style="text-align:center;">No. Invoice</th>
                                    <th style="text-align:center;">Tanggal offset</th>
                                    <th style="text-align:center;">Invoice</th>
                                    <th style="text-align:center;">Remain</th>
                                    <th style="text-align:center;">Offset</th>
                                    
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div id="manualModeBox" style="display:none">

                        <div style="display: flex; flex-direction: row; align-content: center; justify-content: space-between; align-items: center;">
                            <div class="alert alert-warning" style="padding: 5px 15px;">
                                Mode Manual Aktif — Silakan pilih invoice dan isi offset secara manual
                            </div>

                            <button type="button" class="btn btn-primary btn-sm mb-2" id="btnAddManualRow" style="padding: 10px 20px; font-size: 13px">
                                Tambah Invoice
                            </button>
                        </div>

                        <table class="table table-bordered" id="manualDetailTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No. Invoice</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Invoice</th>
                                    <th class="text-center">Remain</th>
                                    <th class="text-center">Offset</th>
                                    <th class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" id="btnSaveCashIn" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT CASH IN -->
<div class="modal fade" id="CashInEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fCashInEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cash In - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HIDDEN -->
                    <input type="hidden" name="CASH_IN" id="CASH_IN_EDIT">
                    <input type="hidden" name="PLANT" id="PLANT_EDIT">

                    <!-- HEADER (SAMA SEPERTI ADD) -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input type="text" id="PLANT_NAME_EDIT" class="form-control"
                                readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Customer *</label>
                            <input id="CUSTOMER_NAME_EDIT" class="form-control" readonly>
                            <input type="hidden" name="CUSTOMER" id="CUSTOMER_EDIT">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Transaksi</label>
                            <input id="CASH_IN_NO_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:5px 0; width:100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Slip No *</label>
                            <input id="SLIP_NO_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Rekening *</label>
                            <select id="NO_REK_EDIT" name="NO_REK" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="CASHIN_DATE_EDIT" name="CASHIN_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Jumlah</label>
                            <input name="JUMLAH" id="JUMLAH_EDIT" class="form-control">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Attachment</label>
                            <div style="width:100%">
                                
                                <input type="file" name="ATTACHMENT" class="form-control mt-1" accept=".jpg,.jpeg,.png,.pdf">
                                <a id="attachmentPreviewLink" href="#" target="_blank" style="display:none">
                                    Lihat Attachment Saat Ini
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Bon *</label>
                            <input name="BON" id="BON_EDIT" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <div id="depositInfoBoxEdit" class="alert alert-info py-2 px-3" style="display:none;margin-bottom:0">
                                💰 Saldo Deposit Customer: <b id="depositAmountEdit">0</b>
                            </div>
                        </div>

                    </div>

                    <!-- DETAIL FIFO PREVIEW -->
                    <h5>Preview Alokasi Otomatis (FIFO)</h5>
                    <table class="table table-bordered" id="stockActualDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center;">No. Invoice</th>
                                <th style="text-align:center;">Tanggal Offset</th>
                                <th style="text-align:center;">Invoice</th>
                                <th style="text-align:center;">Remain</th>
                                <th style="text-align:center;">Offset</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    #modalPickInvoice {
        background: #000000c7;
    }
</style>

<div class="modal fade" id="modalPickInvoice" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Pilih Invoice Tempo</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="invoiceTempoTable">
          <thead>
            <tr>
              <th style="text-align: center">PLANT</th>
              <th style="text-align: center">DATE</th>
              <th style="text-align: center">NO. INVOICE</th>
              <th style="text-align: center">CUSTOMER</th>
              <th style="text-align: center">METODE</th>
              <th style="text-align: center">BERAT</th>
              <th style="text-align: center">QTY</th>
              <th style="text-align: center">HARGA (Qty)</th>
              <th style="text-align: center">DISC</th>
              <th style="text-align: center">AMOUNT</th>
              <th style="text-align: center">TOTAL AMOUNT</th>
              <th style="text-align: center">REMAIN</th>
              <th style="text-align: center">#</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
    }
</style>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'CASHIN_DATE', dir: 'DESC' };

    let timer;
    $('#search').on('keyup', function(){
        clearTimeout(timer);
        timer = setTimeout(() => {
            state.search = $(this).val();
            loadPage(1);
        }, 500);
    });

    function initPlantSelect(){

        let $el = $('#PLANT_ADD');

        // kalau sudah select2, jangan init ulang
        if ($el.hasClass("select2-hidden-accessible")) return;

        $el.select2({
            placeholder: "-- PILIH PLANT --",
            dropdownParent: $('#CashInAdd'),
            width: "100%",
            ajax: {
                url: "<?= base_url('cash-in/get_user_plant_select2'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function(data){
                    return { results: data };
                }
            }
        });

        $el.on('change', function(){
            $('#PLANT_HIDDEN').val(this.value);

            pickedInvoices = {};
            $('#stockActualDetailTableAdd tbody').empty();
            recalcTotal();
            loadCustomerDeposit();

            let customer = $('#CUSTOMER').val();
            let plant    = this.value;

            loadFifoSource(customer, plant); // 🔥 TAMBAH INI
        });

        // 🔥 AUTO SELECT kalau cuma 1 plant
        $.getJSON('<?= base_url("cash-in/get_user_plant_select2"); ?>', function(rows){

            if(rows.length === 1){
                let option = new Option(rows[0].text, rows[0].id, true, true);
                $el.append(option).trigger('change');

                $el.prop('disabled', true); // lock select
                $('#PLANT_HIDDEN').val(rows[0].id);
            }
        });
    }

    // let manualMode = false;

    $('#btnOverrideInvoice').on('click', function(){

        if (!manualMode) {
            // manualMode = true;

            $('#stockActualDetailTableAdd tbody').empty();
            pickedInvoices = {};
            detailIndexAdd = 0;
            addDetailRow(null, '#stockActualDetailTableAdd');

            $(this).removeClass('btn-warning').addClass('btn-danger')
                .text('Kembali ke Auto FIFO');
        } else {
            // manualMode = false;

            $('#stockActualDetailTableAdd tbody').empty();
            let customer = $('#CUSTOMER').val();
            let plant    = $('#PLANT_HIDDEN').val();
            let amount   = toNumber($('input[name="JUMLAH"]').val());

            renderFifoPreview();

            $(this).removeClass('btn-danger').addClass('btn-warning')
                .text('Override Manual');
        }
    });

    let modeCashIn = 'FIFO';

    $('input[name="mode_cash_in"]').on('change', function(){

        modeCashIn = $(this).val();

        if(modeCashIn === 'FIFO'){

            $('#manualModeBox').hide();
            $('#previewFifoBox').show();

            $('#JUMLAH_INPUT').prop('readonly', false).val('');

            renderFifoPreview();
        } 
        else {

            $('#previewFifoBox').hide();
            $('#manualModeBox').show();

            $('#JUMLAH_INPUT')
                .prop('readonly', true)
                .val('0');

            $('#manualDetailTable tbody').empty();
            pickedInvoices = {};
            detailIndexAdd = 0;
        }
    });

    $('#btnAddManualRow').on('click', function(){
        addDetailRow(null, '#manualDetailTable');
    });

    let fifoSourceInvoices = [];

    function loadFifoSource(customer, plant) {

        if (!customer || !plant) return;

        $('#stockActualDetailTableAdd tbody').html(`
            <tr><td colspan="5" class="text-center text-muted">Loading invoice...</td></tr>
        `);

        $.get('<?= base_url("cash-in/get_invoice_fifo_source") ?>', {
            customer: customer,
            plant: plant
        }, function(res){

            fifoSourceInvoices = res || [];
            console.log("FIFO SOURCE:", fifoSourceInvoices);
            renderFifoPreview();

        }, 'json');
        
    }

    $('#CUSTOMER, #PLANT_ADD').on('change', function(){

        fifoSourceInvoices = [];
        $('#stockActualDetailTableAdd tbody').empty();

        let customer = $('#CUSTOMER').val();
        let plant    = $('#PLANT_HIDDEN').val();

        loadCustomerDeposit();
        loadFifoSource(customer, plant);
    });

    function calculateFifoAllocations(amount) {
        let sisa = parseFloat(amount) || 0;
        let allocations = [];

        fifoSourceInvoices.forEach(inv => {
            let remain = parseFloat(inv.REMAIN);
            let offset = 0;

            if (sisa > 0) {
                offset = Math.min(remain, sisa);
                sisa -= offset;
            }

            allocations.push({
                sales: inv.SALES,
                sales_date: inv.SALES_DATE,
                invoice_amount: inv.AMOUNT,
                invoice_remain_before: inv.REMAIN,
                offset: offset
            });
        });

        return {
            rows: allocations,
            deposit: sisa // 🔥 SISA UANG = DEPOSIT
        };
    }

    function renderFifoPreview() {

        if (modeCashIn !== 'FIFO') return;
        if (!fifoSourceInvoices.length) return;

        let jumlah = toNumber($('#JUMLAH_INPUT').val());
        let tbody  = $('#stockActualDetailTableAdd tbody');
        tbody.empty();

        // 🔥 kalau jumlah belum diisi, hanya tampilkan daftar invoice tanpa offset
        if (jumlah <= 0) {
            fifoSourceInvoices.forEach(inv => {
                tbody.append(`
                    <tr class="table-light">
                        <td class="text-center"><b>#${inv.SALES}</b></td>
                        <td class="text-center">${formatDate(inv.SALES_DATE)}</td>
                        <td class="text-end">${formatRupiah(inv.AMOUNT)}</td>
                        <td class="text-end text-danger">${formatRupiah(inv.REMAIN)}</td>
                        <td class="text-center text-muted">Menunggu jumlah</td>
                    </tr>
                `);
            });
            return;
        }

        let result = calculateFifoAllocations(jumlah);
        let rows   = result.rows;
        let sisa   = result.deposit;

        rows.forEach((r,i) => {

            if (r.offset <= 0) return;

            tbody.append(`
                <tr>
                    <td class="text-center">
                        <input type="hidden" name="DETAIL[${i}][SALES]" value="${r.sales}">
                        <input type="hidden" name="DETAIL[${i}][PLANT]" value="${$('#PLANT_HIDDEN').val()}">
                        <b>#${r.sales}</b>
                    </td>
                    <td class="text-center">${formatDate(r.sales_date)}</td>
                    <td class="text-end">${formatRupiah(r.invoice_amount)}</td>
                    <td class="text-end">${formatRupiah(r.invoice_remain_before)}</td>
                    <td class="text-end">
                        ${formatRupiah(r.offset)}
                        <input type="hidden" name="DETAIL[${i}][AMOUNT_OFFSET]" value="${r.offset}">
                    </td>
                </tr>
            `);
        });

        if (sisa > 0) {
            tbody.append(`
                <tr class="table-warning">
                    <td colspan="5" class="text-center">
                        💰 Sisa ${formatRupiah(sisa)} akan menjadi DEPOSIT CUSTOMER
                    </td>
                </tr>
            `);
        }
    }

    $('#JUMLAH_INPUT').on('input', function(){
        if (modeCashIn === 'FIFO') {
            renderFifoPreview();
        }
    });

    $(document).on('input', '#JUMLAH_EDIT', function(){

        let customer = $('#CUSTOMER_EDIT').val();
        let plant    = $('#PLANT_EDIT').val();
        let amount   = toNumber($(this).val());

        loadFifoPreview(customer, plant, amount, '#stockActualDetailTableEdit', 'sisa-row-edit');
    });

    function formatDate(dateString) {
        if (!dateString) return '-';

        const d = new Date(dateString);
        if (isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2, '0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        const month = months[d.getMonth()];
        const year = d.getFullYear();

        return `${day} ${month} ${year}`;
    }

    function formatRupiahView(val) {
        if (!val) return '0';

        // "2000000.00" → 2000000
        let num = parseFloat(val);

        if (isNaN(num)) return '0';

        return num.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function loadPage(page = 1) {
        state.page = page;

        $.get('<?= base_url("cash-in/load_data"); ?>', state, function (resp) {

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

            let tbody = $('#mainTable tbody');
            tbody.empty();

            resp.rows.forEach(function (row) {

            let actionButtons = '';

            if (CURRENT_USER === 'admin') {

                if (row.IS_LOCKED > 0) {
                    actionButtons += `<button class="btn btn-sm btn-secondary" disabled>Locked</button>`;
                } else {
                    actionButtons += `<button class="btn btn-sm btn-warning editBtn" data-id="${row.CASH_IN}" data-plant="${row.PLANT}">Edit</button>`;
                }

                actionButtons += `
                    <button class="btn btn-sm btn-danger deleteBtn"
                        data-id="${row.CASH_IN}"
                        data-plant="${row.PLANT}">
                        Hapus
                    </button>
                `;
            }

                let tr = `
                    <tr>
                        <td style="text-align:center; vertical-align: middle"><b>${row.PLANT_NAME}</b></td>
                        <td style="text-align:center; vertical-align: middle"><b>#${row.CASH_IN}</b></td>
                        <td style="text-align:center; vertical-align: middle">${formatTanggalIndo(row.CASHIN_DATE)}</td>
                        <td style="text-align:center; vertical-align: middle">
                            ${row.CUSTOMER_NAME ?? '-'}<br>
                            <b>${row.CUSTOMER}</b>
                        </td>
                        <td style="text-align:right; vertical-align: middle">${formatRupiahView(row.AMOUNT)}</td>
                        <td style="text-align:center; vertical-align: middle">
                            ${row.BON ?? '-'} <br> ${row.PEMBAYARAN ?? '-'}
                        </td>
                        <td style="text-align:center; vertical-align: middle">
                            <button class="btn btn-sm btn-primary exportPdf"
                                data-cash_in="${row.CASH_IN}"
                                data-plant="${row.PLANT}">
                                PDF
                            </button>
                            ${actionButtons}
                        </td>
                    </tr>
                `;

                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            $('#info').text(
                `Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total / state.limit)} (Total ${resp.total} data)`
            );
        });
    }

    $(document).on("click", ".exportPdf", function () {
        let cash_in    = $(this).data("cash_in");
        let plant = $(this).data("plant");

        window.open(
            "<?= base_url('cash-in/print_pdf'); ?>?cash_in=" + cash_in + "&plant=" + plant,
            "_blank"
        );
    });

    $('#CashInAdd').on('shown.bs.modal', function () {
        initCustomerSelect2('#CUSTOMER', '#CashInAdd');
        initRekeningSelect2('#NO_REK', '#CashInAdd');
        initPlantSelect();
        $('input[name="mode_cash_in"][value="FIFO"]').prop('checked', true).trigger('change');
    });

    $('#CashInAdd').on('hidden.bs.modal', function () {
        pickedInvoices = {};
        $('#stockActualDetailTableAdd tbody').empty();
        recalcTotal();
        $('#depositInfoBox').hide();
    });

    $('#CashInEdit').on('shown.bs.modal', function () {
        initRekeningSelect2('#NO_REK_EDIT', '#CashInEdit');
    });

    $('#CashInEdit').on('hidden.bs.modal', function () {
        pickedInvoices = {};
    });

    let activeRow = null;
    let pickedInvoices = {};

    $(document).on('click', '.pickInvoiceBtn', function () {
        let row = $(this).closest('tr');

        $('#modalPickInvoice')
            .data('targetRow', row)
            .modal('show');

        loadInvoiceTempo();
    });

    $(document).on('click', '.pickInvoice', function () {

        let row    = $('#modalPickInvoice').data('targetRow');
        let sales  = $(this).data('sales');
        let plant  = $(this).data('plant');
        let slip   = $(this).data('slip');

        if (pickedInvoices[sales]) {
            alert('Invoice ini sudah dipilih');
            return;
        }

        $.get('<?= base_url("cash-in/validate_invoice_remain"); ?>', {
            sales: sales,
            plant: plant
        }, function(resp){

            if (typeof resp === 'string') resp = JSON.parse(resp);

            if (!resp.status) {
                alert(resp.message);
                return;
            }

            pickedInvoices[sales] = true;

            row.find('.sales-id').val(sales);
            row.find('.plant-id').val(plant);
            row.find('.invoice-text').html(`<b>#${sales}</b>`);

            row.find('.amount-invoice-val').val(resp.amount);
            row.find('.amount-invoice-text').text(formatRupiah(resp.amount));

            row.find('.remain-val').val(resp.remain);
            row.find('.remain-text').text(formatRupiah(resp.remain));

            row.find('.slip-no-val').val(slip);
            row.find('.slip-no-text').text(slip);

            row.find('.offset-date').val(new Date().toISOString().slice(0,10));

            $('#modalPickInvoice').modal('hide');

        }, 'json');
    });

    $(document).on('click', '.removeRow', function () {
        let row = $(this).closest('tr');
        let sales = row.find('.sales-id').val();

        if (sales) {
            delete pickedInvoices[sales];
        }

        row.remove();
        recalcTotal();
    });

    $(document).on('input', '.amount-offset', function () {
        let raw = this.value.replace(/[^\d]/g, '');
        this.value = raw;
    });

    // Saat keluar field → baru format & batasi remain
    $(document).on('blur', '.amount-offset', function () {

        let row    = $(this).closest('tr');
        let remain = toNumber(row.find('.remain-val').val());
        let val    = toNumber(this.value);

        if (val > remain) {
            alert('Offset melebihi sisa invoice');
            val = remain;
        }

        this.value = formatRupiah(val);

        recalcTotal();
    });

    function recalcTotal(){

        let total = 0;

        $('.amount-offset').each(function(){
            total += toNumber($(this).val());
        });

        // ❌ JANGAN sentuh JUMLAH kalau MANUAL
        if (modeCashIn === 'FIFO') {
            $('#JUMLAH_INPUT').val(formatRupiah(total));
        }

        if ($('#CashInEdit').hasClass('show')) {
            $('#JUMLAH_EDIT').val(formatRupiah(total));
        }
    }

    function validateManualTotal() {
        if (modeCashIn !== 'MANUAL') return true;

        let totalOffset = 0;

        $('.amount-offset').each(function(){
            totalOffset += toNumber(this.value);
        });

        if (totalOffset <= 0) {
            alert('Offset belum diisi');
            return false;
        }

        return true; // ❌ jangan bandingkan dengan JUMLAH
    }

    function showManualDepositInfo() {

        // if (modeCashIn !== 'MANUAL') return;

        // let jumlah = toNumber($('input[name="JUMLAH"]').val());
        // let totalOffset = 0;

        // $('.amount-offset').each(function(){
        //     totalOffset += toNumber(this.value);
        // });

        // let sisa = jumlah - totalOffset;

        // $('#manualDetailTable tbody tr#manualDepositInfo').remove();

        // if (sisa > 0) {
        //     $('#manualDetailTable tbody').append(`
        //         <tr id="manualDepositInfo" class="table-warning">
        //             <td colspan="6" class="text-center">
        //                 💰 Sisa ${formatRupiah(sisa)} akan menjadi DEPOSIT CUSTOMER
        //             </td>
        //         </tr>
        //     `);
        // }
    }

    function formatTanggalIndo(dateString) {
        if (!dateString) return '';

        const bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        const d = new Date(dateString);
        const day = String(d.getDate()).padStart(2, '0');
        const month = bulan[d.getMonth()];
        const year = d.getFullYear();

        return `${day} ${month} ${year}`;
    }

    function formatDecimal(value, digit = 2) {
        return Number(value || 0)
            .toLocaleString('id-ID', {
                minimumFractionDigits: digit,
                maximumFractionDigits: digit
            });
    }

    function loadInvoiceTempo() {

        let customer = $('#CUSTOMER').val();
        let plant    = $('#PLANT_HIDDEN').val(); // ✅ dari hidden

        if (!customer) {
            alert('Pilih customer terlebih dahulu');
            return;
        }

        if (!plant) {
            alert('Plant belum dipilih');
            return;
        }

        $.getJSON(
            '<?= base_url("cash-in/get_invoice_tempo"); ?>',
            { customer: customer, plant: plant },
            function (rows) {

                let tbody = $('#invoiceTempoTable tbody');
                tbody.empty();

                if (!rows.length) {
                    tbody.append(`
                        <tr>
                            <td colspan="14" class="text-center text-muted">
                                Tidak ada invoice tempo
                            </td>
                        </tr>
                    `);
                    return;
                }

                // GROUP BY SALES + PLANT
                const grouped = {};
                rows.forEach(r => {
                    const key = `${r.SALES}_${r.PLANT}`;
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(r);
                });

                Object.values(grouped).forEach(items => {
                    const rowspan = items.length;

                    items.forEach((r, index) => {
                        let tr = '<tr>';

                        if (index === 0) {
                            tr += `
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="detail-row text-center"><b>${r.PLANT_NAME}</b></td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="detail-row text-center">${formatTanggalIndo(r.SALES_DATE)}</td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="detail-row text-center"><b>#${r.SALES}</b></td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="detail-row text-center">
                                    ${r.CUSTOMER_NAME}<br><b>${r.CUSTOMER}</b>
                                </td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="detail-row text-center">${r.JENIS_PAY}</td>
                            `;
                        }

                        tr += `
                            <td style="vertical-align: middle" class="detail-row text-center">${formatDecimal(r.BERAT)}</td>
                            <td style="vertical-align: middle" class="detail-row text-end">${r.QTY}</td>
                            <td style="vertical-align: middle" class="detail-row text-end">${formatRupiah(r.HARGA)}</td>
                            <td style="vertical-align: middle" class="detail-row text-end">${formatRupiah(r.DISCOUNT)}</td>
                            <td style="vertical-align: middle" class="detail-row text-end">${formatRupiah(r.DETAIL_AMOUNT)}</td>
                        `;

                        if (index === 0) {
                            tr += `
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="text-end detail-row">${formatDecimal(r.AMOUNT)}</td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="text-end detail-row">${formatDecimal(r.REMAIN)}</td>
                                <td style="vertical-align: middle" rowspan="${rowspan}" class="text-center detail-row">
                                    <button class="btn btn-success btn-sm pickInvoice"
                                        data-sales="${r.SALES}"
                                        data-plant="${r.PLANT}"
                                        data-amount="${parseFloat(r.AMOUNT)}"
                                        data-remain="${parseFloat(r.REMAIN)}"
                                        data-slip="${r.SLIP_NO}">
                                        Pilih
                                    </button>
                                </td>
                            `;
                        }

                        tr += '</tr>';
                        tbody.append(tr);
                    });
                });
            }
        );
    }

    function loadCustomerDeposit() {

        let customer = $('#CUSTOMER').val();
        let plant    = $('#PLANT_HIDDEN').val();

        if (!customer || !plant) {
            $('#depositInfoBox').hide();
            return;
        }

        $.getJSON('<?= base_url("cash-in/get_customer_deposit"); ?>', {
            customer: customer,
            plant: plant
        }, function(resp){

            if (resp.amount > 0) {
                $('#depositAmount').text(formatRupiah(resp.amount));
                $('#depositInfoBox').show();
            } else {
                $('#depositInfoBox').hide();
            }
        });
    }

    function loadCustomerDepositEdit(customer, plant) {
        $.getJSON('<?= base_url("cash-in/get_customer_deposit"); ?>', {
            customer: customer,
            plant: plant
        }, function(resp){
            if (resp.amount > 0) {
                $('#depositAmountEdit').text(formatRupiah(resp.amount));
                $('#depositInfoBoxEdit').show();
            } else {
                $('#depositInfoBoxEdit').hide();
            }
        });
    }

    function initCustomerSelect2(selector, modalId){
        if ($(selector).hasClass("select2-hidden-accessible")) return;
        $(selector).select2({
            placeholder: "-- PILIH CUSTOMER --",
            allowClear: true,
            dropdownParent: modalId ? $(modalId) : $(document.body),
            width: "100%",
            ajax: {
                url: "<?= base_url('cash-in/get-customer'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });
    }

    function initRekeningSelect2(selector, modalId){
        if ($(selector).hasClass("select2-hidden-accessible")) return;
        $(selector).select2({
            placeholder: "-- PILIH REKENING --",
            allowClear: true,
            dropdownParent: modalId ? $(modalId) : $(document.body),
            width: "100%",
            ajax: {
                url: "<?= base_url('cash-in/get-rekening'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });
    }

    function toNumber(val) {
        if (!val) return 0;

        return parseFloat(
            val.toString().replace(/[^\d]/g, '')
        ) || 0;
    }

    function formatRupiah(val) {
        let num = Number(val) || 0;
        return num.toLocaleString('id-ID');
    }

    /* -------------------------
    Add / remove detail rows
    ------------------------- */
    let detailIndexAdd = 0;

    function addDetailRow(data, targetTable) {

        let tbody = $(`${targetTable} tbody`);
        let index = detailIndexAdd++;
        if (!tbody.length) return;

        data = data || {};
        let today = new Date().toISOString().slice(0, 10);

        let row = `
        <tr>
            <!-- NO INVOICE -->
            <td style="display:flex; align-items:center;">
                <input type="hidden" name="DETAIL[${index}][SALES]" class="sales-id">
                <input type="hidden" name="DETAIL[${index}][PLANT]" class="plant-id">
                <div class="invoice-text me-2 text-center">-</div>
                <button type="button" class="btn btn-sm btn-primary pickInvoiceBtn">
                    Pilih Invoice
                </button>
            </td>

            <!-- DATE -->
            <td>
                <input type="date"
                    name="DETAIL[${index}][DATE_OFFSET]"
                    class="text-center form-control form-control-sm offset-date"
                    value="${data.DATE_OFFSET ?? today}">
            </td>

            <!-- TOTAL INVOICE -->
            <td class="text-end">
                <input type="hidden" name="DETAIL[${index}][AMOUNT_INVOICE]" class="amount-invoice-val">
                <span class="text-end amount-invoice-text">0</span>
            </td>

            <!-- 🔥 REMAIN -->
            <td class="text-end">
                <input type="hidden" class="remain-val">
                <span class=" text-end remain-text text-danger fw-bold">0</span>
            </td>

            <!-- OFFSET -->
            <td>
                <input type="text"
                    name="DETAIL[${index}][AMOUNT_OFFSET]"
                    class="text-end form-control form-control-sm amount-offset text-end"
                    placeholder="0">
            </td>

            

            <!-- ACTION -->
            <td class="text-center">
                <button class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>
        `;

        tbody.append(row);
    }

    function calcRow(el){
        let tr = $(el).closest('tr');
        let qty   = parseFloat(tr.find('.qty').val()) || 0;
        let berat = parseFloat(tr.find('.berat').val()) || 0;
        // opsional kalkulasi lainnya jika diperlukan
    }

    function cleanNumber(val){
        if (val === null || val === undefined) return 0;

        val = val.toString().trim();

        // Jika ada 1 titik dan setelah titik ada 2 digit → anggap decimal valid (100.00, 275.50)
        if (val.includes('.') && /^[0-9]+\.[0-9]{2}$/.test(val)) {
            return parseFloat(val);
        }

        // Hapus semua titik (misal: 1.234 → 1234)
        val = val.replace(/\./g, "");
        
        return parseFloat(val) || 0;
    }

    function safeDate(val){
        if (!val) return '';
        if (val === '0000-00-00 00:00:00') return '';
        return val.substring(0,10);
    }

    function renderExistingFifoEdit(details, plant) {

        let tbody = $('#stockActualDetailTableEdit tbody');
        tbody.empty();

        if (!details.length) {
            tbody.append(`<tr><td colspan="5" class="text-center text-muted">Tidak ada detail invoice</td></tr>`);
            return;
        }

        details.forEach((row, i) => {

            let remain = parseFloat(row.REMAIN_NOW);

            tbody.append(`
                <tr data-remain="${remain}">
                    <td class="text-center">
                        <input type="hidden" name="DETAIL[${i}][SALES]" value="${row.SALES}">
                        <input type="hidden" name="DETAIL[${i}][PLANT]" value="${plant}">
                        <b>#${row.SALES}</b>
                    </td>
                    <td class="text-center">
                        <input type="hidden" name="DETAIL[${i}][DATE_OFFSET]" value="${row.DATE_OFFSET.substring(0,10)}">
                        ${formatTanggalIndo(row.DATE_OFFSET)}
                    </td>
                    <td class="text-end">
                        ${formatRupiah(row.AMOUNT_INVOICE)}
                        <input type="hidden" name="DETAIL[${i}][AMOUNT_INVOICE]" value="${row.AMOUNT_INVOICE}">
                    </td>
                    <td class="text-end">${formatRupiah(remain)}</td>
                    <td class="text-end bayar-col">
                        ${formatRupiah(row.AMOUNT_OFFSET)}
                        <input type="hidden" name="DETAIL[${i}][AMOUNT_OFFSET]" value="${row.AMOUNT_OFFSET}">
                    </td>
                </tr>
            `);
        });
    }

    $('#CUSTOMER').on('change', function () {
        pickedInvoices = {};
        $('#stockActualDetailTableAdd tbody').empty();
        recalcTotal();
        loadCustomerDeposit();
    });

    $('#cashInDetailTableEdit').on('click', '.pickInvoiceBtn', function(){
        alert('Invoice tidak dapat diganti pada mode edit');
    });

    $(function(){
        loadPage(1);

        // add row
        $('#addDetailRowAdd').click(function(){ addDetailRow(null, '#stockActualDetailTableAdd'); });

        // remove row
        $('#stockActualDetailTableAdd, #stockActualDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

        $('#fCashInAdd').submit(function(e){
            e.preventDefault();

            let btn = $('#btnSaveCashIn');

            /* ================= VALIDASI BERDASARKAN MODE ================= */

            if (modeCashIn === 'FIFO') {

                // Harus ada invoice yang teralokasi
                let rowCount = $('#stockActualDetailTableAdd input[name*="[SALES]"]').length;
                if (rowCount === 0) {
                    alert('Tidak ada invoice yang bisa dialokasikan');
                    return;
                }

            } else { // MANUAL

                let validRows = 0;
                let totalOffset = 0;

                $('#manualDetailTable tbody tr').each(function(){
                    let sales  = $(this).find('.sales-id').val();
                    let offset = toNumber($(this).find('.amount-offset').val());

                    if (sales && offset > 0) {
                        validRows++;
                        totalOffset += offset;
                    }
                });

                if (validRows === 0) {
                    alert('Belum ada invoice manual yang diisi offset');
                    return;
                }

                if (totalOffset <= 0) {
                    alert('Total offset harus lebih dari 0');
                    return;
                }
            }

            /* ================= NORMALISASI ANGKA ================= */
            $('.amount-offset').each(function(){
                this.value = toNumber(this.value);
            });

            let formData = new FormData(this);

            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '<?= base_url("cash-in/create"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){

                    try {
                        resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    } catch (e) {
                        alert("Server tidak mengembalikan JSON!\nCek console.");
                        btn.prop('disabled', false).text('Simpan');
                        return;
                    }

                    alert(resp.message);

                    if(resp.status){
                        $('#CashInAdd').modal('hide');
                        $('#fCashInAdd')[0].reset();
                        $('#stockActualDetailTableAdd tbody').empty();
                        $('#manualDetailTable tbody').empty();
                        pickedInvoices = {};
                        loadPage(state.page);
                    }

                    btn.prop('disabled', false).text('Simpan');
                },
                error: function(){
                    alert('Terjadi kesalahan server');
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });

        $(document).on('click', '.editBtn', function () {

            const cashIn = $(this).data('id');

            $.get('<?= base_url("cash-in/edit"); ?>', { cash_in: cashIn }, function(resp){

                if (!resp.status) return alert(resp.message);

                const h = resp.header;

                $('#CASH_IN_EDIT').val(h.CASH_IN);
                $('#PLANT_EDIT').val(h.PLANT);
                $('#PLANT_NAME_EDIT').val(h.PLANT_NAME);
                $('#CUSTOMER_EDIT').val(h.CUSTOMER);
                $('#CUSTOMER_NAME_EDIT').val(h.CUSTOMER + ' - ' + h.CUSTOMER_NAME);
                $('#CASH_IN_NO_EDIT').val(h.CASH_IN);
                $('#SLIP_NO_EDIT').val(h.SLIP_NO);
                $('#BON_EDIT').val(h.BON);
                $('#CASHIN_DATE_EDIT').val(h.CASHIN_DATE);
                $('#JUMLAH_EDIT').val(formatRupiah(h.AMOUNT));

                // loadFifoPreview(h.CUSTOMER, h.PLANT, toNumber(h.AMOUNT), '#stockActualDetailTableEdit', 'sisa-row-edit');

                if (h.ATTACHMENT) {
                    $('#attachmentPreviewLink')
                        .attr('href', '<?= base_url(); ?>' + h.ATTACHMENT)
                        .show();
                } else {
                    $('#attachmentPreviewLink').hide();
                }

                $('input[name="PEMBAYARAN"][value="'+h.PEMBAYARAN+'"]').prop('checked', true);

                initRekeningSelect2('#NO_REK_EDIT', '#CashInEdit');
                let opt = new Option(h.REK_NAME, h.NO_REK, true, true);
                $('#NO_REK_EDIT').append(opt).trigger('change');

                loadCustomerDepositEdit(h.CUSTOMER, h.PLANT);
                renderExistingFifoEdit(resp.detail, h.PLANT);

                $('#CashInEdit').modal('show');
            }, 'json');
        });

        $('#fCashInEdit').submit(function(e){
            e.preventDefault();

            let btn = $(this).find('button[type=submit]');
            btn.prop('disabled', true).text('Menyimpan...');

            let formData = new FormData(this);

            if ($('#stockActualDetailTableEdit input[name*="[AMOUNT_OFFSET]"]').filter(function(){ 
                return toNumber(this.value) > 0; 
            }).length === 0) {
                alert('Tidak ada invoice yang teralokasi');
                return;
            }

            $.ajax({
                url: '<?= base_url("cash-in/update"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#CashInEdit').modal('hide');
                        loadPage(state.page);
                    }
                    btn.prop('disabled', false).text('Update');
                },
                error: function(){
                    alert('Terjadi kesalahan server');
                    btn.prop('disabled', false).text('Simpan');
                }
            });
        });

        // Detail modal
        $(document).on('click', '.detailBtn', function(){
            var id = $(this).data('id');
            $('#stockActualDetailBody').html('Loading...');
            $.get('<?= base_url("stock-actual/detail"); ?>',{id: id}, function(resp){
                $('#stockActualDetailBody').html(resp);
            });
            $('#stockActualDetail').modal('show');
        });

        // Delete
        $(document).on('click', '.deleteBtn', function () {

            const cashIn = $(this).data('id');
            const plant  = $(this).data('plant');

            if (!confirm(`Yakin ingin menghapus CASH IN: ${cashIn} ?`)) return;

            $.post("<?= base_url('cash-in/remove'); ?>", {
                cash_in: cashIn,
                plant: plant
            }, function (res) {
                res = typeof res === 'string' ? JSON.parse(res) : res;
                alert(res.message);
                if (res.status) loadPage(state.page);
            });
        });

    }); // end ready

    $('#CashInAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split("T")[0];
        $('#CASHIN_DATE').val(today); // hari ini
        let now = new Date();
        detailIndexAdd = 0;
    });

    const CURRENT_USER = "<?= $this->session->userdata('username'); ?>";
</script>

