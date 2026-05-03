<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">BASE DATA - CD CUSTOMER</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari customer..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button>
                        <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#mcustomerAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th class="border-bottom-0 cursor-pointer" data-order="cust" style="text-align: center; vertical-align: middle;">Cust <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="full_name" style="text-align: center; vertical-align: middle;">Nama <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="cust_class" style="text-align: center; vertical-align: middle;">Class <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="grade" style="text-align: center; vertical-align: middle;">Grade <span class="sort-icon"></span></th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;"></th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="fs-3" style="border: 2px solid #fff;">
                        <!-- filled by AJAX -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL ADD -->
<div class="modal fade" id="mcustomerAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="customerAdd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Customer - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label">Cust *</label>
                        <input name="cust" id="cust" class="form-control" readonly placeholder="Auto generate">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input name="cust_name" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kind</label>
                        <select id="kindAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddenkindAdd" name="cust_kind">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select id="classAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddenclassAdd" name="cust_class">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Owner</label>
                        <input name="chief_name" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input name="mobile_phone" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <input name="address1" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank</label>
                        <select id="bankAdd" class="form-control"></select>
                        <input type="hidden" id="hiddenbankAdd" name="bank_code">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank Account</label>
                        <input name="bank_account" type="number" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank Owner</label>
                        <input name="owner_picture" type="text" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Remark</label>
                        <input name="remark" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="useYnToggleAdd">
                            <label class="form-check-label" id="useYnLabelAdd" style="margin-top: 4px; margin-left: 10px;">NON-AKTIF</label>
                        </div>
                        <input type="hidden" name="status" id="useYnValueAdd" value="N">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="mcustomerEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="customerEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Customer - Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="orig_cust">
                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label">Cust *</label>
                        <input name="cust_edit" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Name *</label>
                        <input name="full_name_edit" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kind</label>
                        <select id="kindEdit" class="form-control" required></select>
                        <input type="hidden" id="hiddenkindEdit" name="cust_kind_edit">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select id="classEdit" class="form-control" required></select>
                        <input type="hidden" id="hiddenclassEdit" name="cust_class_edit">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Owner</label>
                        <input name="chief_name_edit" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Phone</label>
                        <input name="mobile_phone_edit" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <input name="address1_edit" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank</label>
                        <select id="bankEdit" class="form-control"></select>
                        <input type="hidden" id="hiddenbankEdit" name="bank_code_edit">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank Account</label>
                        <input name="bank_account_edit" type="number" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bank Owner</label>
                        <input name="owner_picture_edit" type="text" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Remark</label>
                        <input name="remark_edit" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="useYnToggleEdit">
                            <label class="form-check-label" id="useYnLabelEdit" style="margin-top: 4px; margin-left: 10px;">NON-AKTIF</label>
                        </div>
                        <input type="hidden" name="status_edit" id="useYnValueEdit">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
        </form>
    </div>
</div>


<script>
/* ========================== STATE ========================== */
var state = {
    page: 1,
    limit: 10,
    order: 'cust',
    dir: 'ASC',
    search: ''
};

/* ========================== PAGE LOAD ========================== */
$(function(){
    let timer = null;
    $('#search').on('keyup', function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            state.search = $('#search').val();
            state.page = 1;
            loadPage(1);
        }, 350);
    });

    loadPage(1);

    $('#mainTable thead').on('click','th[data-order]', function(){
        var col = $(this).data('order');
        if (state.order === col) {
            state.dir = state.dir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            state.order = col;
            state.dir = 'ASC';
        }
        loadPage(1);
    });

    /* ========================== MODAL SHOW ========================== */
    $('#mcustomerAdd').on('shown.bs.modal', function() {

        // SELECT2 Class
        if (!$('#classAdd').hasClass('select2-hidden-accessible')) {
            $('#classAdd').select2({
                tags: true,
                placeholder: '- PILIH CLASS -',
                width: '100%',
                dropdownParent: $('#mcustomerAdd'),
                ajax: {
                    url: '<?= base_url("customer/get_class") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenclassAdd').val(e.params.data.text.toUpperCase());
            });
        }

        // SELECT2 Kind
        if (!$('#kindAdd').hasClass('select2-hidden-accessible')) {
            $('#kindAdd').select2({
                tags: true,
                placeholder: '- PILIH KIND -',
                width: '100%',
                dropdownParent: $('#mcustomerAdd'),
                ajax: {
                    url: '<?= base_url("customer/get_kinds") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenkindAdd').val(e.params.data.text.toUpperCase());
            });
        }

        // SELECT2 Bank
        if (!$('#bankAdd').hasClass('select2-hidden-accessible')) {
            $('#bankAdd').select2({
                tags: true,
                placeholder: '- PILIH BANK -',
                width: '100%',
                dropdownParent: $('#mcustomerAdd'),
                ajax: {
                    url: '<?= base_url("customer/get_banks") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenbankAdd').val(e.params.data.text.toUpperCase());
            });
        }

        // Toggle Status
        $('#useYnToggleAdd').on('change', function(){
            if(this.checked){
                $('#useYnLabelAdd').text('AKTIF');
                $('#useYnValueAdd').val('Y');
            } else {
                $('#useYnLabelAdd').text('NON-AKTIF');
                $('#useYnValueAdd').val('N');
            }
        });

    });

    /* ========================== SUBMIT FORM ========================== */
    $('#customerAdd').on('submit', function(e){
        e.preventDefault();

        var data = {
            cust: $('input[name="cust"]').val(),
            full_name: $('input[name="cust_name"]').val(),
            cust_kind: $('#kindAdd').select2('data')[0] ? $('#kindAdd').select2('data')[0].text : '',
            cust_class: $('#classAdd').select2('data')[0] ? $('#classAdd').select2('data')[0].text : '',
            chief_name: $('input[name="chief_name"]').val(),
            mobile_phone: $('input[name="mobile_phone"]').val(),
            address1: $('input[name="address1"]').val(),
            bank_code: $('#bankAdd').select2('data')[0] ? $('#bankAdd').select2('data')[0].text : '',
            bank_account: $('input[name="bank_account"]').val(),
            owner_picture: $('input[name="owner_picture"]').val(),
            remark: $('input[name="remark"]').val(),
            status: $('#useYnValueAdd').val()
        };

        console.log('DATA SUBMIT:', data);

        $.post("<?= base_url('customer/create'); ?>", data, function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success' : 'error', r.message);

            if(r.status){
                $('#mcustomerAdd').modal('hide');
                $('#customerAdd')[0].reset();
                $('#classAdd, #kindAdd, #bankAdd').val(null).trigger('change');
                $('#useYnToggleAdd').prop('checked', false).trigger('change');
                loadPage(1); // pastikan fungsi loadPage menyesuaikan table customer
            }
        }).fail(function(xhr){
            console.error('AJAX ERROR:', xhr.responseText);
            showToast('error', 'Terjadi kesalahan server!');
        });
    });

    // fungsi preload sama persis
    function preloadSelect2Value(selectId, hiddenId, value){
        if(!value) return;

        // Buat option agar Select2 mengenali value
        let option = new Option(value, value, true, true);
        $(selectId).append(option).trigger('change');

        // Isi hidden input agar form submit dapat value
        $(hiddenId).val(value);
    }

    $('#mcustomerEdit').on('shown.bs.modal', function() {
        // SELECT2 Class
        if (!$('#classEdit').hasClass('select2-hidden-accessible')) {
            $('#classEdit').select2({
                tags: true,
                placeholder: '- PILIH CLASS -',
                width: '100%',
                dropdownParent: $('#mcustomerEdit'),
                ajax: {
                    url: '<?= base_url("customer/get_class") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenclassEdit').val(e.params.data.text.toUpperCase());
            });
        }

        // SELECT2 Kind
        if (!$('#kindEdit').hasClass('select2-hidden-accessible')) {
            $('#kindEdit').select2({
                tags: true,
                placeholder: '- PILIH KIND -',
                width: '100%',
                dropdownParent: $('#mcustomerEdit'),
                ajax: {
                    url: '<?= base_url("customer/get_kinds") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenkindEdit').val(e.params.data.text.toUpperCase());
            });
        }

        // SELECT2 Bank
        if (!$('#bankEdit').hasClass('select2-hidden-accessible')) {
            $('#bankEdit').select2({
                tags: true,
                placeholder: '- PILIH BANK -',
                width: '100%',
                dropdownParent: $('#mcustomerEdit'),
                ajax: {
                    url: '<?= base_url("customer/get_banks") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenbankEdit').val(e.params.data.text.toUpperCase());
            });
        }

        // Toggle Status
        if(d.STATUS === "Y"){
            $('#useYnToggleEdit').prop('checked', true);
            $('#useYnLabelEdit').text("AKTIF");
            $('#useYnValueEdit').val("Y");
        } else {
            $('#useYnToggleEdit').prop('checked', false);
            $('#useYnLabelEdit').text("NON-AKTIF");
            $('#useYnValueEdit').val("N");
        }
    });

    // SUBMIT EDIT MATERIAL
    $('#customerEdit').on('submit', function(e){
        e.preventDefault();

        var data = {
            orig_cust: $('input[name="orig_cust"]').val(),
            cust_edit: $('input[name="cust_edit"]').val(),
            full_name_edit: $('input[name="full_name_edit"]').val(),
            cust_kind_edit: $('#hiddenkindEdit').val(),
            cust_class_edit: $('#hiddenclassEdit').val(),
            chief_name_edit: $('input[name="chief_name_edit"]').val(),
            mobile_phone_edit: $('input[name="mobile_phone_edit"]').val(),
            address1_edit: $('input[name="address1_edit"]').val(),       // FIX
            bank_code_edit: $('#hiddenbankEdit').val(),
            bank_account_edit: $('input[name="bank_account_edit"]').val(),
            owner_picture_edit: $('input[name="owner_picture_edit"]').val(),
            remark_edit: $('input[name="remark_edit"]').val(),
            status_edit: $('#useYnValueEdit').val()
        };

        console.log("DATA SUBMIT EDIT:", data);

        $.post("<?= base_url('customer/update'); ?>", data)
        .done(function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success' : 'error', r.message);

            if(r.status){
                $('#mcustomerEdit').modal('hide');
                $('#customerEdit')[0].reset();
                $('#classEdit, #kindEdit, #bankEdit').val(null).trigger('change');
                loadPage(1);
            }
        })
        .fail(function(xhr){
            console.error('AJAX ERROR:', xhr.responseText);
            showToast('error','Terjadi kesalahan server!');
        });
    });

    /* EXPORT */
    $('#exportExcel').on('click', function(){
        window.location = "<?= base_url('material/export_excel'); ?>?search="+state.search+"&order="+state.order+"&dir="+state.dir;
    });
    $('#exportPdf').on('click', function(){
        window.location = "<?= base_url('material/export_pdf'); ?>?search="+state.search+"&order="+state.order+"&dir="+state.dir;
    });
});

/* ========================== LOAD PAGE ========================== */
function loadPage(page){
    state.page = page;
    $('#table-body').html('<tr><td style="text-align: center" colspan="11">Loading...</td></tr>');
    $.get("<?= base_url('customer/load_data'); ?>", state, function(res){
        let data = typeof res === 'string' ? JSON.parse(res) : res;
        renderTable(data.rows);
        $('#pagination').html(data.pagination);
        $('#info').text('Menampilkan '+data.rows.length+' dari '+data.total);
    }, 'json');
}

/* ========================== RENDER TABLE ========================== */
function renderTable(rows){
    let html = '';
    if (!rows || rows.length === 0) {
        html = '<tr><td style="text-align: center" colspan="11">Tidak ada data</td></tr>';
    } else {
        rows.forEach(r => {
            html += `
                <tr>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.CUST)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.FULL_NAME)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.CUST_CLASS)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.CUST_KIND)}</td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button class="btn btn-sm btn-warning me-1" onclick="showEdit('${r.CUST}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="doDelete('${r.CUST}')">Delete</button>
                    </td>
                </tr>
            `;
        });
    }
    $('#table-body').html(html);
}

/* ========================== DETAIL ========================== */
function showDetail(customer){
    $('#detailBody').html('Loading...');
    $('#modalDetail').modal('show');
    $.get("<?= base_url('customer/detail'); ?>", {customer}, function(res){
        let r = typeof res === 'string' ? JSON.parse(res) : res;
        if (!r.status) return $('#detailBody').html('Error');

        let d = r.data;
        let html = `<table class="table table-bordered">`;
        for (let k in d){
            html += `<tr><th>${k}</th><td>${escapeHtml(d[k]||'')}</td></tr>`;
        }
        html += `</table>`;
        $('#detailBody').html(html);
    });
}

function preloadSelect2Value(selectId, hiddenId, value){
    if(!value) return;

    // Buat option agar Select2 mengenali value
    let option = new Option(value, value, true, true);
    $(selectId).append(option).trigger('change');

    // Isi hidden input agar form submit dapat value
    $(hiddenId).val(value);
}

function showEdit(cust){
    $.get("<?= base_url('customer/edit'); ?>", { cust: cust }, function(res){
        if(typeof res === 'string') res = JSON.parse(res);
        if(!res.status){ alert('Error'); return; }

        let d = res.data;
        let f = $('#customerEdit');

        // Isi input biasa
        f.find('input[name="orig_cust"]').val(d.CUST);
        f.find('input[name="cust_edit"]').val(d.CUST);
        f.find('input[name="full_name_edit"]').val(d.FULL_NAME);
        f.find('input[name="chief_name_edit"]').val(d.CHIEF_NAME);
        f.find('input[name="mobile_phone_edit"]').val(d.MOBILE_PHONE);
        f.find('input[name="address1_edit"]').val(d.ADDRESS1);
        f.find('input[name="bank_account_edit"]').val(d.BANK_ACCOUNT);
        f.find('input[name="owner_picture_edit"]').val(d.OWNER_PICTURE);
        f.find('input[name="remark_edit"]').val(d.REMARK);

        // Preload Select2 values
        preloadSelect2Value('#classEdit','#hiddenclassEdit', d.CUST_CLASS);
        preloadSelect2Value('#kindEdit','#hiddenkindEdit', d.CUST_KIND);
        preloadSelect2Value('#bankEdit','#hiddenbankEdit', d.BANK_CODE);

        // Status toggle
        if(d.STATUS === "Y"){
            $('#useYnToggleEdit').prop('checked', true);
            $('#useYnLabelEdit').text("AKTIF");
            $('#useYnValueEdit').val("Y");
        } else {
            $('#useYnToggleEdit').prop('checked', false);
            $('#useYnLabelEdit').text("NON-AKTIF");
            $('#useYnValueEdit').val("N");
        }

        $('#mcustomerEdit').modal('show');
    });
}

/* ========================== DELETE ========================== */
function doDelete(cust){
    if (!confirm("Yakin ingin menghapus data?")) return;

    $.post("<?= base_url('customer/remove'); ?>", { cust: cust }, function(res){
        let r = typeof res === 'string' ? JSON.parse(res) : res;
        alert(r.message);
        if (r.status) loadPage(state.page);
    }, 'json');
}

/* ========================== UTIL ========================== */
function escapeHtml(text){
    return $('<div/>').text(text).html();
}

function showToast(type, message){
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000
    });
    Toast.fire({ icon: type, title: message });
}
</script>

<script>
$('#useYnToggleAdd').on('change', function(){
    $('#useYnValueAdd').val(this.checked ? 'Y' : 'N');
    $('#useYnLabelAdd').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
});

$('#useYnToggleEdit').on('change', function(){
    $('#useYnValueEdit').val(this.checked ? 'Y' : 'N');
    $('#useYnLabelEdit').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
});
</script>
