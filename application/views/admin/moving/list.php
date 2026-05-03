<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">MOVING - INPUT</h5>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" class="form-control" placeholder="Cari moving..." />
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#movingAdd">
                        Tambah Moving
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th style="text-align:center;">No Moving</th>
                            <th style="text-align:center;">Plant</th>
                            <th style="text-align:center;">Tujuan</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Ket.</th>
                            <th style="text-align:center;">#</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
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
</style>

<div class="modal fade" id="movingAdd">
    <div class="modal-dialog modal-xl">
        <form id="fmovingAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>MOVING - TAMBAH</h5>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No Moving</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background: #efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" required name="MOVING_DATE">
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" name="PLANT" class="form-control" required>></select>
                        </div>


                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">To Plant *</label>
                            <select id="toPlantAdd" name="TO_PLANT" class="form-control" required>></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" class="form-control" placeholder="Tulis disini...">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Item</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addRowAdd">Tambah Item</button>
                    </div>

                    <table class="table table-bordered" id="detailAdd">
                        <thead>
                            <tr>
                                <th style="width:30%; text-align: center; vertical-align: middle">Item</th>
                                <th style="text-align: center; vertical-align: middle">Qty</th>
                                <th style="text-align: center; vertical-align: middle">Berat</th>
                                <th style="text-align: center; vertical-align: middle">Remark</th>
                                <th style="text-align: center; vertical-align: middle">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="movingEdit">
    <div class="modal-dialog modal-xl">
        <form id="fmovingEdit">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>MOVING - EDIT</h5>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No Moving</label>
                            <input id="MOVING_EDIT" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="MOVING_DATE_EDIT" name="MOVING_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-2  flex-inline">
                            <label class="form-label">Plant</label>
                            <select id="PLANT_EDIT" class="form-control" disabled></select>
                        </div>

                        <div class="col-md-6 mt-2  flex-inline">
                            <label class="form-label">To Plant *</label>
                            <select id="TO_PLANT_EDIT" name="TO_PLANT" class="form-control"></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input id="REMARK_EDIT" name="REMARK" class="form-control">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Item</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addRowEdit">
                            Tambah Item
                        </button>
                    </div>

                    <table class="table table-bordered" id="detailEdit">
                        <thead>
                            <tr>
                                <th style="width:30%; text-align: center; vertical-align: middle">Item</th>
                                <th style="text-align: center; vertical-align: middle">Qty</th>
                                <th style="text-align: center; vertical-align: middle">Berat</th>
                                <th style="text-align: center; vertical-align: middle">Remark</th>
                                <th style="text-align: center; vertical-align: middle">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
   var state = { page: 1, limit: 10, search: '' };
    let ajaxRequest = null;

    /* =========================
    LOAD LIST
    ========================= */
    function loadPage(page = 1) {
        state.page = page;

        if (ajaxRequest) ajaxRequest.abort();

        ajaxRequest = $.get('<?= base_url("moving/load_data"); ?>', state, function(resp){
            ajaxRequest = null;

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

            let tbody = $('#table-body').empty();

            resp.rows.forEach(function(r){
                tbody.append(`
                    <tr>
                        <td style="text-align:center; vertical-align: middle"><b>#${r.moving_no}</b></td>
                        <td style="text-align:center; vertical-align: middle"><b>${r.plant_name}</b></td>
                        <td style="text-align:center; vertical-align: middle">${r.to_plant_name}</td>
                        <td style="text-align:center; vertical-align: middle">${formatDate(r.moving_date)}</td>
                        <td style="text-align:center; vertical-align: middle">${r.remark || ''}</td>
                        <td style="text-align:center; vertical-align: middle">
                            <button class="btn btn-sm btn-warning editBtn" data-id="${r.moving_no}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${r.moving_no}">Hapus</button>
                        </td>
                    </tr>
                `);
            });

            $('#pagination').html(resp.pagination || '');
        });
    }

    /* =========================
    FORMAT DATE
    ========================= */
    function formatDate(dateString) {
        if (!dateString) return '-';
        let d = new Date(dateString);
        if (isNaN(d)) return dateString;
        return d.toLocaleDateString('id-ID');
    }

    /* =========================
    SELECT2 PLANT
    ========================= */
    function initPlantSelect(selector, modal){
        $(selector).select2({
            dropdownParent: $(modal),
            width:'100%',
            placeholder: '-- PILIH PLANT --',
            ajax:{
                url:'<?= base_url("moving/get_plant_by_user"); ?>',
                dataType:'json',
                delay:250,
                processResults:d=>({results:d})
            }
        });
    }

    /* =========================
    SELECT2 ITEM
    ========================= */
    function initItemSelect(el, modal){
        $(el).select2({
            placeholder:'Pilih Item',
            dropdownParent: $(modal),
            width:'100%',
            minimumInputLength:2,
            ajax:{
                url:'<?= base_url("moving/get_item"); ?>',
                dataType:'json',
                delay:300,
                data:p=>({q:p.term}),
                processResults:d=>({results:d})
            }
        });
    }

    /* =========================
    ADD ROW DETAIL
    ========================= */
    function addRow(targetTable, modal){
        let row = `
            <tr>
                <td><select class="form-control item"></select></td>
                <td><input class="form-control qty text-end decimal"></td>
                <td><input class="form-control berat text-end decimal"></td>
                <td><input class="form-control remark"></td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

        $(`${targetTable} tbody`).append(row);

        let tr = $(`${targetTable} tbody tr`).last();
        initItemSelect(tr.find('.item'), modal);
    }

    /* =========================
    REMOVE ROW
    ========================= */
    $(document).on('click','.removeRow', function(){
        $(this).closest('tr').remove();
    });

    /* =========================
    PARSE DECIMAL
    ========================= */
    function parseDecimal(val){
        return parseDecimalID(val);
    }

    function parseDecimalID(val) {
        if (!val) return 0;

        return parseFloat(
            val.toString()
            .replace(/\./g, '')
            .replace(',', '.')
        ) || 0;
    }

    function formatDecimalID(num, digit = 2) {
        num = Number(num || 0);
        return num.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    $(document).on('input', '.decimal', function () {
        let val = $(this).val();

        val = val.replace(/[^0-9.,]/g, '');

        let parts = val.split(',');
        if (parts.length > 2) {
            val = parts[0] + ',' + parts[1];
        }

        $(this).val(val);
    });

    // format saat keluar field
    $(document).on('blur', '.decimal', function () {
        let num = parseDecimalID($(this).val());
        $(this).val(formatDecimalID(num));
    });

    /* =========================
    SEARCH
    ========================= */
    let searchTimer = null;

    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);
        let val = $(this).val();

        searchTimer = setTimeout(()=>{
            state.search = val;
            loadPage(1);
        }, 400);
    });

    /* =========================
    SUBMIT CREATE
    ========================= */
    $('#fmovingAdd').submit(function(e){
        e.preventDefault();

        let DETAIL = [];

        $('#detailAdd tbody tr').each(function(){

            let item  = $(this).find('.item').val();
            let qty   = parseDecimal($(this).find('.qty').val());
            let berat = parseDecimal($(this).find('.berat').val());

            if(!item) return;

            if(qty <= 0 && berat <= 0){
                alert('Qty atau Berat harus diisi');
                throw 'invalid';
            }

            DETAIL.push({
                ITEM   : item,
                QTY    : qty,
                BERAT  : berat,
                REMARK : $(this).find('.remark').val()
            });
        });

        if(!DETAIL.length){
            alert('Detail tidak boleh kosong');
            return;
        }

        let formData = new FormData(this);
        formData.append('DETAIL', JSON.stringify(DETAIL));

        $.ajax({
            url:'<?= base_url("moving/create"); ?>',
            method:'POST',
            data:formData,
            processData:false,
            contentType:false,
            dataType:'json',
            success:function(res){
                alert(res.message);

                if(res.status){
                    $('#movingAdd').modal('hide');
                    $('#fmovingAdd')[0].reset();
                    $('#detailAdd tbody').empty();
                    loadPage(state.page);
                }
            }
        });
    });

    /* =========================
    EDIT
    ========================= */
    $(document).on('click','.editBtn', function(){

        let id = $(this).data('id');

        $('#fmovingEdit')[0].reset();
        $('#detailEdit tbody').empty();

        $.get('<?= base_url("moving/edit"); ?>', {moving:id}, function(resp){

            if(typeof resp === 'string') resp = JSON.parse(resp);

            if(!resp.status){
                alert(resp.message);
                return;
            }

            let h = resp.header;
            let d = resp.detail;

            // HEADER
            $('#MOVING_EDIT').val(h.moving_no);
            $('#MOVING_DATE_EDIT').val(h.moving_date);
            let optPlant = new Option(
                h.plant_name,
                h.plant,
                true,
                true
            );

            $('#PLANT_EDIT')
                .append(optPlant)
                .trigger('change');

            // 🔥 TO PLANT
            let optToPlant = new Option(
                h.to_plant_name,
                h.to_plant,
                true,
                true
            );

            $('#TO_PLANT_EDIT')
                .append(optToPlant)
                .trigger('change');

            $('#REMARK_EDIT').val(h.remark);

            // DETAIL
            d.forEach(function(row){

                addRow('#detailEdit', '#movingEdit');

                let tr = $('#detailEdit tbody tr').last();

                let opt = new Option(
                    row.item + ' - ' + row.item_name,
                    row.item,
                    true,
                    true
                );

                tr.find('.item').append(opt).trigger('change');

                tr.find('.qty').val(formatDecimalID(row.qty));
                tr.find('.berat').val(formatDecimalID(row.berat));
                tr.find('.remark').val(row.remark);
            });

            $('#movingEdit').modal('show');

        }, 'json');
    });

    /* =========================
    SUBMIT UPDATE
    ========================= */
    $('#fmovingEdit').submit(function(e){
        e.preventDefault();

        let DETAIL = [];

        $('#detailEdit tbody tr').each(function(){

            let item  = $(this).find('.item').val();
            let qty   = parseDecimal($(this).find('.qty').val());
            let berat = parseDecimal($(this).find('.berat').val());

            if(!item) return;

            if(qty <= 0 && berat <= 0){
                alert('Qty atau Berat harus diisi');
                throw 'invalid';
            }

            DETAIL.push({
                ITEM   : item,
                QTY    : qty,
                BERAT  : berat,
                REMARK : $(this).find('.remark').val()
            });
        });

        if(!DETAIL.length){
            alert('Detail tidak boleh kosong');
            return;
        }

        let formData = new FormData(this);
        formData.append('MOVING_NO', $('#MOVING_EDIT').val());
        formData.append('DETAIL', JSON.stringify(DETAIL));

        $.ajax({
            url:'<?= base_url("moving/update"); ?>',
            method:'POST',
            data:formData,
            processData:false,
            contentType:false,
            dataType:'json',
            success:function(res){
                alert(res.message);

                if(res.status){
                    $('#movingEdit').modal('hide');
                    loadPage(state.page);
                }
            }
        });
    });

    /* =========================
    DELETE
    ========================= */
    $(document).on('click','.deleteBtn', function(){

        let id = $(this).data('id');

        Swal.fire({
            title: 'Hapus Moving?',
            text: `Data ${id} akan dihapus permanen`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $.post('<?= base_url("moving/remove"); ?>', { moving:id }, function(res){

                if(res.status){
                    showToast('success', res.message);
                    loadPage(state.page);
                }else{
                    showToast('error', res.message);
                }

            },'json');

        });
    });

    /* =========================
    INIT
    ========================= */
    $(function(){

        loadPage(1);

        initPlantSelect('#plantAdd','#movingAdd');
        initPlantSelect('#toPlantAdd','#movingAdd');

        initPlantSelect('#PLANT_EDIT', '#movingEdit');
        initPlantSelect('#TO_PLANT_EDIT', '#movingEdit');

        $('#addRowAdd').click(()=> addRow('#detailAdd','#movingAdd'));
        $('#addRowEdit').click(()=> addRow('#detailEdit','#movingEdit'));

    });
</script>

<script>
    $('#salesAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split("T")[0];
        const dateInput = $(this).find('input[name="SALES_DATE"]')[0];
        if(dateInput){
            dateInput.value = today; // hari ini
        }
    });

    $(document).on('input','#BAYAR_AWAL_EDIT', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });
</script>
