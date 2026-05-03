<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">MATERIAL BALANCE - INVENTORY</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3 align-items-end">

                <!-- FILTER NAME (SELECT2) -->
                <div class="col-md-3">
                    <label class="form-label">Material</label>
                    <select id="filter_name" class="form-control">
                        <option value="">-- Pilih Material --</option>
                    </select>
                </div>

                <!-- DATE FROM -->
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" id="date_from" class="form-control">
                </div>

                <!-- DATE TO -->
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" id="date_to" class="form-control">
                </div>

                <!-- BUTTON FILTER -->
                <div class="col-md-1">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="btnFilter">
                        <i class="fa fa-search"></i> Filter
                    </button>
                </div>

                <!-- EXPORT BUTTON -->
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-success w-100" id="btnExportExcel">
                        <i class="fa fa-file-excel"></i> Excel
                    </button>
                </div>

                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button class="btn btn-danger w-100" id="btnExportPdf">
                        <i class="fa fa-file-pdf"></i> PDF
                    </button>
                </div>

            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th rowspan="2" data-order="MATERIAL" style="text-align:center; vertical-align: middle">Material No</th>
                            <th rowspan="2" data-order="MATERIAL_NAME" style="text-align:center; vertical-align: middle">Nama</th>
                            <th rowspan="2" data-order="BEGIN_QTY" style="text-align:center; vertical-align: middle">Begin QTY</th>
                            <th data-order="IN" style="text-align:center; vertical-align: middle">IN</th>
                            <th colspan="3" data-order="OUT" style="text-align:center; vertical-align: middle">OUT</th>
                            <th rowspan="2" data-order="ENDING_QTY" style="text-align:center; vertical-align: middle">Ending QTY</th>
                        </tr>
                        <tr>
                            <th style="text-align:center; vertical-align: middle">Purchase QTY</th>
                            <th style="text-align:center; vertical-align: middle">Consumption</th>
                            <th style="text-align:center; vertical-align: middle">Trading</th>
                            <th style="text-align:center; vertical-align: middle">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center; vertical-align: middle">01220023</td>
                            <td style="text-align:center; vertical-align: middle">AYAM GORENG</td>
                            <td style="text-align:center; vertical-align: middle">1.274.300</td>
                            <td style="text-align:center; vertical-align: middle">500.000</td>
                            <td style="text-align:center; vertical-align: middle">20.000</td>
                            <td style="text-align:center; vertical-align: middle">35.000</td>
                            <td style="text-align:center; vertical-align: middle">50.000</td>
                            <td style="text-align:center; vertical-align: middle">1.134.300</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>


<script>
$(document).ready(function () {

    // DATA DUMMY
    const dataDummy = [
        { id: '01220023', text: '01220023 - AYAM GORENG' },
        { id: '01220024', text: '01220024 - AYAM BAKAR' },
        { id: '01220025', text: '01220025 - NASI GORENG' },
        { id: '01220026', text: '01220026 - MIE GORENG' }
    ];

    $('#filter_name').select2({
        data: dataDummy,
        placeholder: '-- Pilih Material --',
        allowClear: true,
        width: '100%'
    });

    // BUTTON FILTER
    $('#btnFilter').on('click', function () {
        const name = $('#filter_name').val();
        const from = $('#date_from').val();
        const to   = $('#date_to').val();

        console.log({
            name: name,
            date_from: from,
            date_to: to
        });

        // nanti bisa dipakai untuk ajax / reload datatable
    });

});
</script>
<script>
var state = { page: 1, limit: 10, search: '', order: 'RECEIVE', dir: 'DESC' };

$('#search').on('keyup', function(){
    state.search = $(this).val();
    loadPage(1);
});

/* =========================
   UTIL
========================= */
function formatDate(dateString){
    if(!dateString) return '-';
    const d = new Date(dateString);
    if(isNaN(d)) return dateString;

    const day = String(d.getDate()).padStart(2,'0');
    const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function cleanNumber(val){
    if(!val) return 0;
    val = val.toString();
    if(val.includes('.') && /^[0-9]+\.[0-9]{2}$/.test(val)) return parseFloat(val);
    return parseFloat(val.replace(/\./g,'')) || 0;
}

function formatRupiah(x){
    let n = cleanNumber(x).toString().split('.');
    return n[0].replace(/\B(?=(\d{3})+(?!\d))/g,'.') + (n[1] ? '.'+n[1] : '');
}

/* =========================
   LOAD DATA
========================= */
function loadPage(page = 1){
    state.page = page;
    $.get('<?= base_url("receive-lb/load_data"); ?>', state, function(resp){
        resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

        let tbody = $('#table-body').empty();

        resp.rows.forEach(row=>{
            tbody.append(`
                <tr>
                    <td style="text-align:center;">${row.PLANT_NAME}</td>
                    <td style="text-align:center;">${formatDate(row.RECEIVE_DATE)}</td>
                    <td style="text-align:center;">#${row.RECEIVE}</td>
                    <td style="text-align:center;">#${row.DO ?? '-'}</td>
                    <td style="text-align:center;">(${row.SUPPLIER ?? '-'}) ${row.SUPPLIER_NAME ?? '-'}</td>
                    <td style="text-align:center;">${row.WEIGHT ?? '-'}</td>
                    <td style="text-align:center;">${row.RECEIVE_AMOUNT ?? '-'}</td>
                    <td style="text-align:center;">${row.REMARK ?? ''}</td>
                    <td style="text-align:center;">
                        <button class="btn btn-sm btn-warning editBtn" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.RECEIVE}" data-plant="${row.PLANT}">Hapus</button>
                    </td>
                </tr>
            `);
        });

        $('#pagination').html(resp.pagination);
        $('#info').text(
            `Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`
        );
    });
}

/* =========================
   SELECT2
========================= */
function initSupplierSelect2(selector, modal){
    $(selector).select2({
        placeholder:'Pilih Supplier',
        dropdownParent:$(modal),
        width:'100%',
        ajax:{
            url:'<?= base_url("receive-lb/get-supplier"); ?>',
            dataType:'json',
            delay:250,
            data:p=>({q:p.term}),
            processResults:d=>({results:d})
        }
    }).on('select2:select', function(e){
        $(this).closest('form')
               .find('input[name="SUPPLIER"]')
               .val(e.params.data.id);
    });
}

function initSupplierEdit(){
    $('#supplierEdit').select2({
        placeholder: 'Pilih Supplier',
        dropdownParent: $('#receiveLbEdit'), // ✅ WAJIB INI
        width: '100%',
        ajax: {
            url: '<?= base_url("receive-lb/get-supplier"); ?>',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data })
        }
    }).on('select2:select', function (e) {
        $('#hiddensupplierEdit').val(e.params.data.id);
    });
}

/* =========================
   DETAIL ROW
========================= */
function addDetailRow(data={}, table){
    let modal = table.includes('Edit') ? '#receiveEdit' : '#receiveLbAdd';

    let row = `
        <tr>
            <td><select class="form-control material-select"></select></td>
            <td><input class="form-control berat text-end" value="${data.BERAT||''}"></td>
            <td><input class="form-control jumlah text-end" value="${data.QTY||''}"></td>
            <td><input class="form-control harga text-end" value="${data.PRICE||''}"></td>
            <td><input class="form-control total text-end" value="${data.AMOUNT||''}" readonly></td>
            <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
        </tr>
    `;

    $(table+' tbody').append(row);

    let $select = $(table+' tbody tr:last .material-select');
    initMaterialSelect2($select, modal);

    if(data.MATERIAL && data.MATERIAL_NAME){
        let opt = new Option(data.MATERIAL_NAME, data.MATERIAL, true, true);
        $select.append(opt).trigger('change');
    }
}

function updateTotalRow(tr){
    let qty = cleanNumber(tr.find('.jumlah').val());
    let harga = cleanNumber(tr.find('.harga').val());
    tr.find('.total').val(formatRupiah((qty*harga).toString()));
}

/* =========================
   READY
========================= */
$(function(){

    loadPage(1);

    initSupplierSelect2('#supplierAdd','#receiveLbAdd');
    initSupplierSelect2('#supplierEdit','#receiveLbEdit');

    $('#addDetailRowAdd').click(()=>addDetailRow({},'#receiveDetailTableAdd'));
    $('#addDetailRowEdit').click(()=>addDetailRow({},'#receiveDetailTableEdit'));

    $('#receiveDetailTableAdd, #receiveDetailTableEdit')
        .on('click','.removeRow',function(){ $(this).closest('tr').remove(); })
        .on('input','.jumlah,.harga',function(){ updateTotalRow($(this).closest('tr')); });

    /* =========================
       CREATE
    ========================= */
   $('#freceiveLbAdd').submit(function(e){
        e.preventDefault();

        $.post('<?= base_url("receive-lb/create"); ?>',{
            RECEIVE_DATE     : $('input[name="RECEIVE_DATE"]').val(),
            PEMBAYARAN       : $('input[name="PEMBAYARAN"]:checked').val(),
            JENIS_PAY        : $('input[name="JENIS_PAY"]:checked').val(),

            SLIP_NO          : $('input[name="SLIP_NO"]').val() || '',
            DO               : $('input[name="DO"]').val() || '',
            DRIVER           : $('input[name="DRIVER"]').val() || '',
            NO_CAR           : $('input[name="NO_CAR"]').val() || '',

            SUPPLIER         : $('#hiddensupplierAdd').val(),

            ARRIVE_SCHEDULE  : $('input[name="ARRIVE_SCHEDULE"]').val(),
            DEPART_SCHEDULE  : $('input[name="DEPART_SCHEDULE"]').val(),

            QTY              : cleanNumber($('input[name="QTY"]').val()),
            WEIGHT           : cleanNumber($('input[name="WEIGHT"]').val()),
            AVG_BW           : cleanNumber($('input[name="AVG_BW"]').val()),
            PRICE            : cleanNumber($('input[name="PRICE"]').val()),
            AMOUNT           : cleanNumber($('input[name="AMOUNT"]').val()),

            DEAD             : cleanNumber($('input[name="DEAD"]').val()),
            DEAD_WEIGHT      : cleanNumber($('input[name="DEAD_WEIGHT"]').val()),
            SHRINK           : cleanNumber($('input[name="SHRINK"]').val()),
            RECEIVE_AMOUNT   : cleanNumber($('input[name="RECEIVE_AMOUNT"]').val()),

            REMARK           : $('input[name="REMARK"]').val()
        },function(res){
            res = typeof res==='string'?JSON.parse(res):res;
            alert(res.message);
            if(res.status){
                $('#receiveLbAdd').modal('hide');
                $('#freceiveLbAdd')[0].reset();
                loadPage(state.page);
            }
        },'json');
    });

    /* =========================
       EDIT
    ========================= */
   $(document).on('click', '.editBtn', function () {
        let receive = $(this).data('receive');
        let plant   = $(this).data('plant'); // ambil plant dari data-row

        if (!receive) {
            alert('Receive tidak ditemukan');
            return;
        }

        // Request ke controller
        $.get('<?= base_url("receive-lb/edit"); ?>', { receive: receive, plant: plant }, function (resp) {
            if (typeof resp === 'string') resp = JSON.parse(resp);

            if (!resp.status) {
                alert(resp.message);
                return;
            }

            let d = resp.data;
            let form = $('#fReceiveLbEdit');

            // 🔑 RECEIVE (hidden)
            if(form.find('[name="RECEIVE"]').length === 0){
                form.prepend('<input type="hidden" name="RECEIVE">');
            }
            form.find('[name="RECEIVE"]').val(d.RECEIVE);

            // 🔑 PLANT (hidden)
            if(form.find('[name="PLANT"]').length === 0){
                form.prepend('<input type="hidden" name="PLANT" id="PLANT_EDIT">');
            }
            form.find('[name="PLANT"]').val(d.PLANT);

            // tanggal receive
            form.find('[name="RECEIVE_DATE"]').val(d.RECEIVE_DATE.substr(0,10));

            // radio pembayaran
            form.find('[name="PEMBAYARAN"]').prop('checked', false);
            if(d.PEMBAYARAN) form.find('[name="PEMBAYARAN"][value="'+d.PEMBAYARAN+'"]').prop('checked', true);

            form.find('[name="JENIS_PAY"]').prop('checked', false);
            if(d.JENIS_PAY) form.find('[name="JENIS_PAY"][value="'+d.JENIS_PAY+'"]').prop('checked', true);

            // text fields
            form.find('[name="SLIP_NO"]').val(d.SLIP_NO);
            form.find('[name="DO"]').val(d.DO);
            form.find('[name="DRIVER"]').val(d.DRIVER);
            form.find('[name="NO_CAR"]').val(d.NO_CAR);

            // datetime-local
            form.find('[name="ARRIVE_SCHEDULE"]').val(d.ARRIVE_SCHEDULE?.replace(' ','T'));
            form.find('[name="DEPART_SCHEDULE"]').val(d.DEPART_SCHEDULE?.replace(' ','T'));

            // supplier (select2)
            let opt = new Option(
                d.SUPPLIER + ' - ' + (d.SUPPLIER_NAME || ''),
                d.SUPPLIER,
                true,
                true
            );
            $('#supplierEdit').empty().append(opt).trigger('change');
            $('#hiddensupplierEdit').val(d.SUPPLIER);

            // numeric fields
            form.find('[name="QTY"]').val(d.QTY);
            form.find('[name="WEIGHT"]').val(d.WEIGHT);
            form.find('[name="AVG_BW"]').val(d.AVG_BW);
            form.find('[name="PRICE"]').val(d.PRICE);
            form.find('[name="AMOUNT"]').val(d.AMOUNT);
            form.find('[name="DEAD"]').val(d.DEAD);
            form.find('[name="DEAD_WEIGHT"]').val(d.DEAD_WEIGHT);
            form.find('[name="SHRINK"]').val(d.SHRINK);
            form.find('[name="RECEIVE_AMOUNT"]').val(d.RECEIVE_AMOUNT);

            // remark
            form.find('[name="REMARK"]').val(d.REMARK);

            // tampilkan modal
            $('#receiveLbEdit').modal('show');
        }, 'json');
    });


    /* =========================
    SUBMIT UPDATE FORM
    ========================= */
    $('#fReceiveLbEdit').submit(function(e){
        e.preventDefault();

        let f = $(this);

        $.post('<?= base_url("receive-lb/update"); ?>', {
            RECEIVE        : f.find('input[name="RECEIVE"]').val(),
            PLANT          : f.find('input[name="PLANT"]').val(), // 🔑 kirim plant
            RECEIVE_DATE   : f.find('input[name="RECEIVE_DATE"]').val(),

            PEMBAYARAN     : f.find('input[name="PEMBAYARAN"]:checked').val(),
            JENIS_PAY      : f.find('input[name="JENIS_PAY"]:checked').val(),

            SLIP_NO        : f.find('input[name="SLIP_NO"]').val(),
            DO             : f.find('input[name="DO"]').val(),
            DRIVER         : f.find('input[name="DRIVER"]').val(),
            NO_CAR         : f.find('input[name="NO_CAR"]').val(),

            SUPPLIER       : $('#hiddensupplierEdit').val(),

            ARRIVE_SCHEDULE: f.find('input[name="ARRIVE_SCHEDULE"]').val(),
            DEPART_SCHEDULE: f.find('input[name="DEPART_SCHEDULE"]').val(),

            QTY            : cleanNumber(f.find('input[name="QTY"]').val()),
            WEIGHT         : cleanNumber(f.find('input[name="WEIGHT"]').val()),
            AVG_BW         : cleanNumber(f.find('input[name="AVG_BW"]').val()),
            PRICE          : cleanNumber(f.find('input[name="PRICE"]').val()),
            AMOUNT         : cleanNumber(f.find('input[name="AMOUNT"]').val()),
            DEAD           : cleanNumber(f.find('input[name="DEAD"]').val()),
            DEAD_WEIGHT    : cleanNumber(f.find('input[name="DEAD_WEIGHT"]').val()),
            SHRINK         : cleanNumber(f.find('input[name="SHRINK"]').val()),
            RECEIVE_AMOUNT : cleanNumber(f.find('input[name="RECEIVE_AMOUNT"]').val()),
            REMARK         : f.find('input[name="REMARK"]').val()
        }, function(res){
            if(typeof res === 'string') res = JSON.parse(res);
            alert(res.message);

            if(res.status){
                $('#receiveLbEdit').modal('hide');
                loadPage(state.page);
            }
        }, 'json');
    });

    /* =========================
       DELETE
    ========================= */
    $(document).on('click', '.deleteBtn', function () {

        let receive = $(this).data('id');
        let plant   = $(this).data('plant');

        if (!receive || !plant) {
            alert('Data tidak lengkap');
            return;
        }

        if (!confirm(`Yakin hapus RECEIVE ${receive} (Plant ${plant}) ?`)) return;

        $.post("<?= base_url('receive-lb/remove'); ?>", {
            receive: receive,
            plant  : plant
        }, function (res) {

            if (typeof res === 'string') res = JSON.parse(res);/
            alert(res.message);

            if (res.status) loadPage(state.page);
        }, 'json');
    });

    /* =========================
       PDF
    ========================= */
    $(document).on('click','.exportPdf',function(){
        window.open('<?= base_url("receive-lb/print_pdf/"); ?>'+$(this).data('id'),'_blank');
    });

});
</script>

<script>
$('#receiveLbAdd').on('shown.bs.modal', function () {
    let today = new Date().toISOString().split('T')[0];
    $('#RECEIVE_DATE').val(today).attr('min', today);
});

$('#receiveLbEdit').on('shown.bs.modal', function () {
    if (!$('#supplierEdit').hasClass('select2-hidden-accessible')) {
        initSupplierEdit();
    }
});

function setNowNoBackDateTime(selector) {
    const now = new Date();

    // Format ke yyyy-MM-ddTHH:mm (wajib untuk datetime-local)
    const pad = n => String(n).padStart(2, '0');
    const formatted =
        now.getFullYear() + '-' +
        pad(now.getMonth() + 1) + '-' +
        pad(now.getDate()) + 'T' +
        pad(now.getHours()) + ':' +
        pad(now.getMinutes());

    $(selector)
        .val(formatted)     // auto isi
        .attr('min', formatted); // lock backdate + backtime
}

// Saat halaman / modal siap
$(function () {
    setNowNoBackDateTime('#ARRIVE_SCHEDULE');
    setNowNoBackDateTime('#DEPART_SCHEDULE');
});

function hitungAvgBW(form){
    let qty    = parseFloat($(form).find('input[name="QTY"]').val());
    let weight = parseFloat($(form).find('input[name="WEIGHT"]').val());

    let avg = 0;
    if(qty > 0 && weight > 0){
        avg = weight / qty;
    }

    $(form).find('input[name="AVG_BW"]').val(avg.toFixed(2));
}

function hitungTotalHarga(form){
    let qty   = parseFloat($(form).find('input[name="QTY"]').val());
    let price = parseFloat(cleanNumber($(form).find('input[name="PRICE"]').val()));

    let total = 0;
    if(qty > 0 && price > 0){
        total = qty * price;
    }

    $(form).find('input[name="AMOUNT"]').val(formatRupiah(total.toFixed(0)));
}

function hitungTotalTerima(form){
    let weight = parseFloat($(form).find('input[name="WEIGHT"]').val());
    let dead   = parseFloat($(form).find('input[name="DEAD_WEIGHT"]').val());
    let shrink = parseFloat($(form).find('input[name="SHRINK"]').val());

    // default aman
    weight = isNaN(weight) ? 0 : weight;
    dead   = isNaN(dead)   ? 0 : dead;
    shrink = isNaN(shrink) ? 0 : shrink;

    let total = weight - dead - shrink;
    if(total < 0) total = 0;

    $(form)
        .find('input[name="RECEIVE_AMOUNT"]')
        .val(total.toFixed(2));
}

$(document).on(
    'input',
    `
    input[name="QTY"],
    input[name="WEIGHT"],
    input[name="PRICE"],
    input[name="DEAD_WEIGHT"],
    input[name="SHRINK"]
    `,
    function(){
        let form = $(this).closest('form');

        hitungAvgBW(form);        // AVG_BW
        hitungTotalHarga(form);  // AMOUNT
        hitungTotalTerima(form); // RECEIVE_AMOUNT
    }
);

</script>


