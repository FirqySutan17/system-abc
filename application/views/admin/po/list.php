<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">PO - INVENTORY</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari PO..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group ">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button> -->
                        <!-- <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#poAdd">
                        <i class="ti ti-plus"></i> Tambah PO
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th class="border-bottom-0 cursor-pointer" data-order="PO" style="text-align: center; vertical-align: middle;">Plant</th>
                            <th class="border-bottom-0 cursor-pointer" data-order="PO" style="text-align: center; vertical-align: middle;">PO</th>
                            <th class="border-bottom-0 cursor-pointer" data-order="PO_DATE" style="text-align: center; vertical-align: middle;">Tanggal</th>
                            <th class="border-bottom-0" data-order="SUPPLIER" style="text-align: center; vertical-align: middle;">Supplier</th>
                            <th class="border-bottom-0" data-order="REMARK" style="text-align: center; vertical-align: middle;">Remark</th>
                            <th style="text-align: center; vertical-align: middle;">#</th>
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

<!-- MODAL ADD PO -->
<div class="modal fade" id="poAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fpoAdd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PO - TAMBAH</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- HEADER -->
                <div class="row g-2 mb-3">
                    <div class="col-md-12">
                        <label class="form-label">PO (Auto Generate)</label>
                        <input name="PO" id="PO_ADD_AUTO" class="form-control" placeholder="Auto Generate" readonly style="background:#efefef">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Plant *</label>
                        <select id="plantAdd" class="form-control" required></select>
                        <input type="hidden" name="PLANT" id="hiddenPlantAdd">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal *</label>
                        <input name="PO_DATE" type="date" class="form-control" value="<?= date('Y-m-d'); ?>"  required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier *</label>
                        <select id="supplierAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddensupplierAdd" name="SUPPLIER">
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label">Remark</label>
                        <input name="REMARK" class="form-control" placeholder="Input disini..">
                    </div>
                </div>

                <!-- DETAIL -->
                <div style="display: flex; align-content: center; justify-content: space-between; align-items: center; padding: 0px 5px; margin-bottom: 5px">
                    <h5 style="margin-bottom: 0px">Material</h5>
                    <button type="button" class="btn btn-success btn-sm" id="addDetailRowAdd">Tambah Material</button>
                </div>
                
                <table class="table table-bordered" id="poDetailTableAdd">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">Material</th>
                            <th style="text-align: center; vertical-align: middle;">Jumlah</th>
                            <th style="text-align: center; vertical-align: middle;">Berat</th>
                            <th style="text-align: center; vertical-align: middle;">Harga</th>
                            <th style="text-align: center; vertical-align: middle;">Total</th>
                            <th style="text-align: center; vertical-align: middle;" style="width:40px;">#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PO -->
<div class="modal fade" id="poEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fpoEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PO - EDIT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- HEADER -->
                <div class="row g-2 mb-3">
                    <div class="col-md-12">
                        <label class="form-label">PO</label>
                        <input name="PO" id="PO_EDIT_AUTO" class="form-control" readonly style=" background: #efefef">
                        <input type="hidden" name="orig_po" id="orig_po">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Plant</label>
                        <input type="text"
                            id="PLANT_NAME_EDIT"
                            class="form-control"
                            readonly
                            style="background:#efefef">
                        <input type="hidden" name="PLANT" id="PLANT_EDIT">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal *</label>
                        <input name="PO_DATE" type="date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Supplier *</label>
                        <select id="supplierEdit" class="form-control" required></select>
                        <input type="hidden" id="hiddensupplierEdit" name="SUPPLIER">
                    </div>
                    <div class="col-md-12 mt-2">
                        <label class="form-label">Remark</label>
                        <input name="REMARK" class="form-control" placeholder="Input disini..">
                    </div>
                </div>

                <!-- DETAIL -->
                <div style="display: flex; align-content: center; justify-content: space-between; align-items: center; padding: 0px 5px; margin-bottom: 5px">
                    <h5 style="margin-bottom: 0px">Material</h5>
                    <button type="button" class="btn btn-success btn-sm" id="addDetailRowEdit">Tambah Material</button>
                </div>
                
                <table class="table table-bordered" id="poDetailTableEdit">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">Material</th>
                            <th style="text-align: center; vertical-align: middle;">Jumlah</th>
                            <th style="text-align: center; vertical-align: middle;">Berat</th>
                            <th style="text-align: center; vertical-align: middle;">Harga</th>
                            <th style="text-align: center; vertical-align: middle;">Total</th>
                            <th style="text-align: center; vertical-align: middle;" style="width:40px;">#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL PO -->
<div class="modal fade" id="poDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailBody">Loading...</div>
        </div>
    </div>
</div>

<script>
    const LOGIN_USER = "<?= $this->session->userdata('username'); ?>";
    const LOGIN_ROLE = "<?= $this->session->userdata('role_id'); ?>";
</script>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'PO', dir: 'DESC' };

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    function formatTanggalIndo(dateStr) {
        if (!dateStr) return '';

        const bulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        const d = new Date(dateStr);
        return d.getDate() + ' ' +
            bulan[d.getMonth()] + ' ' +
            d.getFullYear();
    }

    function initPlantSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "Pilih PLANT",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_plant_by_user'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return { results: data };
                }
            }
        }).on('select2:select', function(e){
            $('#hiddenPlantAdd').val(e.params.data.id);
            if (!$('#hiddenPlantAdd').val()) {
                alert('Plant wajib dipilih');
                return;
            }
        });
    }

    

    // Load table
    function loadPage(page = 1) {
        state.page = page;

        $.get('<?= base_url("po/load_data"); ?>', state, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body');
            tbody.empty();

            resp.rows.forEach(function(row){
                let actionBtn = `
                    <button class="btn btn-sm btn-primary me-1 exportPdf"
                        data-po="${row.PO}" data-plant="${row.PLANT}">
                        PDF
                    </button>
                `;

                if (LOGIN_ROLE == 1 || row.CREATED_BY === LOGIN_USER) {
                    actionBtn += `
                        <button class="btn btn-sm btn-warning me-1 editBtn"
                            data-po="${row.PO}" data-plant="${row.PLANT}">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn"
                            data-po="${row.PO}"
                            data-plant="${row.PLANT}">
                            Hapus
                        </button>
                    `;
                }
                var tr = `<tr>
                    <td style="text-align: center; vertical-align: middle;"><b>${row.AJ_NAME}</b></td>
                    <td style="text-align: center; vertical-align: middle;"><b>#${row.PO}</b></td>
                    <td style="text-align: center; vertical-align: middle;">${formatTanggalIndo(row.PO_DATE)}</td>
                    <td style="text-align: center; vertical-align: middle;">${row.SUPPLIER_NAME} <br> <b>${row.SUPPLIER}</b></td>
                    <td style="text-align: center; vertical-align: middle;">${row.REMARK}</td>
                    <td style="text-align: center; vertical-align: middle;">
                        ${actionBtn}
                    </td>
                </tr>`;
                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
        });
    }

    // Inisialisasi select2 supplier
    function initSupplierSelect2(selector, modalId, hiddenInput){
        $(selector).select2({
            placeholder: "Pilih SUPPLIER",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_supplier'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){ return { q: params.term }; },
                processResults: function(data){ return { results: data }; }
            }
        }).on('select2:select', function(e){
            $(hiddenInput).val(e.params.data.id);
        });
    }

    function setDefaultSupplier(selector, custId){
        $.ajax({
            url: "<?= base_url('po/get_supplier'); ?>",
            dataType: "json",
            success: function(data){
                let found = data.find(item => item.id === custId);
                if(found){
                    let option = new Option(found.text, found.id, true, true);
                    $(selector).append(option).trigger('change');

                    // set hidden input
                    $('#hiddensupplierAdd').val(found.id);
                }
            }
        });
    }

    // Inisialisasi select2 material
    function initMaterialSelect2(el, parentModal){
        $(el).select2({
            placeholder: "Pilih MATERIAL",
            width:'100%',
            dropdownParent: $(parentModal),
            ajax:{
                url:'<?= base_url("po/get_material"); ?>',
                dataType:'json',
                delay:250,
                data: function(params){ return { q: params.term }; },
                processResults: function(data){ return { results: data }; }
            }
        });
    }

    function cleanInt(val){
        return val ? val.replace(/\D/g,'') : '0';
    }

    function cleanDecimal(val){
        if(!val) return '0';
        return val.replace(/,/g,''); // hanya hapus pemisah ribuan
    }

    function cleanRupiah(val){
        if(!val) return '0';
        return val.replace(/\./g,'').replace(/,/g,'');
    }

    function calcRow(el){
        let tr = $(el).closest('tr');

        let jumlah   = parseDecimalID(tr.find('.jumlah').val());
        let harga  = parseFloat(cleanRupiah(tr.find('.harga').val()));

        if (isNaN(jumlah) || isNaN(harga)) return;

        let total = jumlah * harga;

        tr.find('.total').val(formatRupiah(total.toString()));
    }

    function formatDecimalID(value, decimals = 2) {
        if (value === null || value === '' || isNaN(value)) return '';

        return parseFloat(value)
            .toLocaleString('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
    }

    function parseDecimalID(value) {
        if (!value) return 0;
        return parseFloat(
            value.replace(/\./g, '').replace(',', '.')
        ) || 0;
    }

    // Tambah row detail
    function addDetailRow(tableId, modalId, data=null){

        var material = data?.MATERIAL ?? '';
        var materialText = material && data?.MATERIAL_NAME
            ? material + ' - ' + data.MATERIAL_NAME
            : '';

        var row = `<tr>
            <input type="hidden" class="detail-id" value="${data?.ID ?? ''}">

            <td style="text-align:center; vertical-align:middle; width:30%">
                <select class="form-control materialSelect" name="DETAIL[][MATERIAL]"></select>
            </td>

            <td>
                <input type="text"
                    class="form-control jumlah decimal-input"
                    name="DETAIL[][JUMLAH]"
                    style="text-align:right"
                    value="${data ? formatDecimalID(data.JUMLAH) : ''}"
                    placeholder="0,00">
            </td>

            <td>
                <input type="text"
                    class="form-control berat decimal-input"
                    name="DETAIL[][BERAT]"
                    style="text-align:right"
                    value="${data ? formatDecimalID(data.BERAT) : ''}"
                    placeholder="0,00">
            </td>

            <td>
                <input type="text"
                    class="form-control harga"
                    name="DETAIL[][HARGA]"
                    style="text-align:right"
                    value="${data ? formatRupiah(data.HARGA) : ''}"
                    oninput="this.value=formatRupiah(this.value); calcRow(this)" placeholder="0">
            </td>

            <td>
                <input type="text"
                    class="form-control total"
                    name="DETAIL[][TOTAL]"
                    style="text-align:right; background:#efefef"
                    value="${data ? formatRupiah(data.TOTAL) : ''}"
                    readonly placeholder="0">
            </td>

            <td style="text-align:center">
                <button type="button" class="btn btn-sm btn-danger removeRow">x</button>
            </td>
        </tr>`;

        $(tableId + ' tbody').append(row);

        var $select = $(tableId + ' tbody tr:last .materialSelect');
        initMaterialSelect2($select, modalId);

        if(material){
            var opt = new Option(materialText, material, true, true);
            $select.append(opt).trigger('change');
        }
    }

    $(document).on('input', '.decimal-input', function () {
        let input = this;
        let cursor = input.selectionStart;

        // Ambil angka mentah
        let raw = input.value
            .replace(/\./g, '')
            .replace(/[^0-9,]/g, '');

        // Pisahkan decimal
        let parts = raw.split(',');
        let integer = parts[0];
        let decimal = parts[1] ? parts[1].substring(0, 2) : '';

        let number = integer;
        if (decimal !== '') number += '.' + decimal;

        if (isNaN(number)) return;

        // Format ke Indonesia
        let formatted = parseFloat(number).toLocaleString('id-ID', {
            minimumFractionDigits: decimal !== '' ? decimal.length : 0,
            maximumFractionDigits: 2
        });

        input.value = formatted;

        // Perbaiki posisi cursor
        let diff = formatted.length - raw.length;
        input.setSelectionRange(cursor + diff, cursor + diff);

        updateTotalRow($(this).closest('tr'));
    });

    $(document).on('blur', '.decimal-input', function () {
        let val = parseDecimalID(this.value);
        this.value = formatDecimalID(val);
    });

    // Update total row
    function updateTotalRow(row){
        let jumlah = parseDecimalID(row.find('.jumlah').val());
        let harga  = parseFloat(cleanRupiah(row.find('.harga').val()));

        if (isNaN(jumlah) || isNaN(harga)) return;

        let total = jumlah * harga;

        row.find('.total').val(formatRupiah(total.toString()));
    }

    $(function(){
        loadPage(1);

        // Inisialisasi select2 add
        initPlantSelect2('#plantAdd', '#poAdd');
        initSupplierSelect2('#supplierAdd', '#poAdd');
        setDefaultSupplier('#supplierAdd', 'CS000001');
        $('#addDetailRowAdd').click(function(){ addDetailRow('#poDetailTableAdd','#poAdd'); });

        // Inisialisasi select2 edit
        initSupplierSelect2('#supplierEdit', '#poEdit');
        $('#addDetailRowEdit').click(function(){ addDetailRow('#poDetailTableEdit','#poEdit'); });

        // Remove row
        $('#poDetailTableAdd, #poDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

        // Update total
        $('#poDetailTableAdd, #poDetailTableEdit').on('input','.jumlah,.harga', function(){ updateTotalRow($(this).closest('tr')); });

        // Submit Add
        $('#fpoAdd').submit(function(e){
            e.preventDefault();
            var DETAIL = [];
            $('#poDetailTableAdd tbody tr').each(function(){
                DETAIL.push({
                    MATERIAL: $(this).find('select').val(),
                    JUMLAH: parseDecimalID($(this).find('.jumlah').val()),
                    BERAT: parseDecimalID($(this).find('.berat').val()),
                    HARGA: cleanRupiah($(this).find('.harga').val()),
                    TOTAL: cleanRupiah($(this).find('.total').val())
                });
            });
            $.post('<?= base_url("po/create"); ?>',{
                PLANT   : $('#hiddenPlantAdd').val(),
                PO_DATE: $('input[name="PO_DATE"]').val(),
                SUPPLIER: $('#hiddensupplierAdd').val(),
                REMARK: $('input[name="REMARK"]').val(),
                DETAIL: DETAIL
            }, function(resp){
                resp = typeof resp==='string'?JSON.parse(resp):resp;
                alert(resp.message);
                if(resp.status){
                    $('#PO_ADD_AUTO').val(resp.po);
                    $('#poAdd').modal('hide');
                    $('#fpoAdd')[0].reset();
                    $('#poDetailTableAdd tbody').empty();
                    loadPage(state.page);
                    $('#plantAdd').val(null).trigger('change');
                    $('#supplierAdd').val(null).trigger('change');
                    $('#hiddenPlantAdd').val('');
                    $('#hiddensupplierAdd').val('');
                    $('#fpoAdd')[0].reset();
                }

            },'json');
        });

        // Click edit
        $(document).on('click','.editBtn', function(){

            let po    = $(this).data('po');
            let plant = $(this).data('plant');

            $.get('<?= base_url("po/edit"); ?>',{ po, plant }, function(resp){
                resp = typeof resp==='string' ? JSON.parse(resp) : resp;

                if(!resp.status){
                    alert('Gagal mengambil data PO');
                    return;
                }

                let h = resp.header;

                $('#PLANT_EDIT').val(h.PLANT);
                $('#PLANT_NAME_EDIT').val(h.AJ_NAME);
                $('#PO_EDIT_AUTO').val(h.PO);
                $('#orig_po').val(h.PO);
                $('#fpoEdit input[name="PO_DATE"]').val(h.PO_DATE);
                $('#fpoEdit input[name="REMARK"]').val(h.REMARK);

                // Supplier
                let opt = new Option(
                    h.SUPPLIER + ' - ' + h.SUPPLIER_NAME,
                    h.SUPPLIER,
                    true,
                    true
                );
                $('#supplierEdit').empty().append(opt).trigger('change');
                $('#hiddensupplierEdit').val(h.SUPPLIER);

                // Detail
                $('#poDetailTableEdit tbody').empty();
                resp.detail.forEach(row => {
                    addDetailRow('#poDetailTableEdit','#poEdit', row);
                });

                $('#poEdit').modal('show');
            });
        });

        // Submit edit
        $('#fpoEdit').submit(function(e){
            e.preventDefault();
            if ($('#poDetailTableEdit tbody tr').length === 0) {
                alert('Minimal 1 material harus diisi');
                return;
            }

            $('#fpoEdit button[type=submit]').prop('disabled', true);

            let DETAIL = [];

            $('#poDetailTableEdit tbody tr').each(function(){
                DETAIL.push({
                    ID      : $(this).find('.detail-id').val(),
                    MATERIAL: $(this).find('select').val(),
                    JUMLAH  : parseDecimalID($(this).find('.jumlah').val()),
                    BERAT   : parseDecimalID($(this).find('.berat').val()),
                    HARGA   : cleanRupiah($(this).find('.harga').val()),
                    TOTAL   : cleanRupiah($(this).find('.total').val())
                });
            });

            $.post('<?= base_url("po/update"); ?>',{
                orig_po : $('#orig_po').val(),
                PLANT   : $('#PLANT_EDIT').val(),
                PO_DATE : $('#poEdit input[name="PO_DATE"]').val(),
                SUPPLIER: $('#hiddensupplierEdit').val(),
                REMARK  : $('#poEdit input[name="REMARK"]').val(),
                DETAIL  : DETAIL
            }, function(resp){
                $('#fpoEdit button[type=submit]').prop('disabled', false);
                resp = typeof resp==='string' ? JSON.parse(resp) : resp;

                alert(resp.message);

                if(resp.status){
                    $('#poEdit').modal('hide');
                    loadPage(state.page);
                }
            },'json');
        });

        $(document).on("click", ".exportPdf", function () {
            let po    = $(this).data("po");
            let plant = $(this).data("plant");

            window.open(
                "<?= base_url('po/print_pdf'); ?>?po=" + po + "&plant=" + plant,
                "_blank"
            );
        });

        $(document).on('click', '.deleteBtn', function () {

            let po    = $(this).data('po');
            let plant = $(this).data('plant');

            if (!confirm(`Yakin ingin menghapus PO ${po} ?`)) return;

            $.ajax({
                url: "<?= base_url('po/remove'); ?>",
                type: "POST",
                data: {
                    po: po,
                    plant: plant
                },
                success: function (res) {
                    res = typeof res === 'string' ? JSON.parse(res) : res;

                    alert(res.message);

                    if (res.status) {
                        loadPage(state.page);
                    }
                },
                error: function () {
                    alert("Gagal menghubungi server");
                }
            });
        });
    });

    $('#poEdit').on('hidden.bs.modal', function () {

        // reset form
        $('#fpoEdit')[0].reset();

        // kosongkan detail
        $('#poDetailTableEdit tbody').empty();

        // reset supplier select2
        $('#supplierEdit').val(null).trigger('change');
        $('#hiddensupplierEdit').val('');

    });
</script>


