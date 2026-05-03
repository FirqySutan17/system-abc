<div class="container-fluid">

<style>
  .select2-container.select2-container--default.select2-container--open {
    z-index: 10000;
  }
</style>
    <div class="card w-100">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">CD ITEM - BASE DATA</h5>

            <div class="row mb-3" style="margin-top:20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari item / nama / goods..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group ">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button>
                        <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalAddItem">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th rowspan="2" class="border-bottom-0 cursor-pointer" data-order="item" style="text-align: center; vertical-align: middle;">Item <span class="sort-icon"></span></th>
                            <th rowspan="2" class="border-bottom-0 cursor-pointer" data-order="full_name" style="text-align: center; vertical-align: middle">Nama <span class="sort-icon"></span></th>
                            <th rowspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">Goods</th>
                            <th rowspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">Sex</th>
                            <th rowspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">Price Class</th>
                            <th colspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">Packing</th>
                            <th colspan="3" class="border-bottom-0" style="text-align: center; vertical-align: middle">Price</th>
                            <th colspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">COA</th>
                            <th rowspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle">Memo (for Invoice)</th>
                            <th rowspan="2" class="border-bottom-0" style="text-align: center; vertical-align: middle"></th>
                        </tr>
                        <tr>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">Unit</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">Qty</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">Barang</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">Ongkir</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">Vaksin</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">accDR</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle">accCR</th>
                        </tr>
                        
                    </thead>
                    <tbody id="table-body" class="fs-3" style="border: 2px solid #fff;"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD ITEM -->
<div class="modal fade" id="modalAddItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="formAddItem">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Item - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6 mb-1">
                        <label class="form-label">Item *</label>
                        <input name="item" class="form-control" placeholder="Input Item.." required>
                    </div>
                    <div class="col-md-6 mb-1">
                        <label class="form-label">Full Name *</label>
                        <input name="full_name" class="form-control" placeholder="Input Full Name.." required>
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Goods *</label>
                        <select id="goodsAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddengoodsAdd" name="goods">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Sex *</label>
                        <select id="sexAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddensexAdd" name="sex">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Price Class</label>
                        <select id="priceClassAdd" class="form-control"></select>
                        <input type="hidden" id="hiddenpriceClassAdd" name="price_class">
                    </div>
                    <div class="col-md-3 mb-1">
                        <label class="form-label">Packing Unit</label>
                        <select id="packingUnitAdd" class="form-control"></select>
                        <input type="hidden" id="hiddenpackingUnitAdd" name="packing_unit">
                    </div>
                    <div class="col-md-3 mb-1">
                        <label class="form-label">Packing Qty</label>
                        <input name="packing_qty" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Price Goods</label>
                        <input name="price_goods" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Price Delivery</label>
                        <input name="price_delivery" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-2 mb-1">
                        <label class="form-label">Price Vaccine</label>
                        <input name="price_vaccine" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-3 mb-1">
                        <label class="form-label">Acc DR</label>
                        <input name="acc_dr" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-3 mb-1">
                        <label class="form-label">Acc CR</label>
                        <input name="acc_cr" class="form-control" placeholder="Cth : 0">
                    </div>
                    <div class="col-md-12 mb-1">
                        <label class="form-label">Remark</label>
                        <input name="remark" class="form-control"  placeholder="Input disini..">
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

<!-- --- Modal Edit --- -->
<div class="modal fade" id="itemEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form id="formEdit">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">CD Item - Edit</h5><button type="button" class="btn-close" data-bs-dismiss="modal" ></button></div>
        <div class="modal-body">
          <input type="hidden" name="orig_item" />
            <div class="row g-2">
              <div class="col-md-6 mb-1">
                  <label class="form-label">Item *</label>
                  <input name="item" class="form-control" placeholder="Input Item.." required>
              </div>
              <div class="col-md-6 mb-1">
                  <label class="form-label">Full Name *</label>
                  <input name="full_name" class="form-control" placeholder="Input Full Name.." required>
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Goods *</label>
                  <select id="goodsEdit" class="form-control" required></select>
                  <input type="hidden" id="hiddengoodsEdit" name="goods">
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Sex *</label>
                  <select id="sexEdit" class="form-control" required></select>
                  <input type="hidden" id="hiddensexEdit" name="sex">
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Price Class</label>
                  <select id="priceClassEdit" class="form-control"></select>
                  <input type="hidden" id="hiddenpriceClassEdit" name="price_class">
              </div>
              <div class="col-md-3 mb-1">
                  <label class="form-label">Packing Unit</label>
                  <select id="packingUnitEdit" class="form-control"></select>
                  <input type="hidden" id="hiddenpackingUnitEdit" name="packing_unit">
              </div>
              <div class="col-md-3 mb-1">
                  <label class="form-label">Packing Qty</label>
                  <input name="packing_qty" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Price Goods</label>
                  <input name="price_goods" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Price Delivery</label>
                  <input name="price_delivery" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-2 mb-1">
                  <label class="form-label">Price Vaccine</label>
                  <input name="price_vaccine" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-3 mb-1">
                  <label class="form-label">Acc DR</label>
                  <input name="acc_dr" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-3 mb-1">
                  <label class="form-label">Acc CR</label>
                  <input name="acc_cr" class="form-control" placeholder="Cth : 0">
              </div>
              <div class="col-md-12 mb-1">
                  <label class="form-label">Remark</label>
                  <input name="remark" class="form-control"  placeholder="Input disini..">
              </div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-primary">Update</button></div>
      </div>
    </form>
  </div>
</div>

<script>
var state = { page:1, limit:10, order:'item', dir:'ASC', search:'' };

$(function(){
    let timer = null;
    $('#search').on('keyup', function(){ clearTimeout(timer); timer = setTimeout(function(){ state.search = $('#search').val(); state.page = 1; loadPage(1); }, 350); });

    loadPage(1);

    $('#mainTable thead').on('click','th[data-order]', function(){
        var col = $(this).data('order');
        if (state.order === col) { state.dir = state.dir === 'ASC' ? 'DESC' : 'ASC'; } else { state.order = col; state.dir = 'ASC'; }
        loadPage(1);
    });

    $('#modalAddItem').on('shown.bs.modal', function() {
        if (!$('#goodsAdd').hasClass('select2-hidden-accessible')) {
            $('#goodsAdd').select2({
                tags: true,
                placeholder: '- PILIH GOODS -',
                width: '100%',
                dropdownParent: $('#modalAddItem'),
                ajax: {
                    url: '<?= base_url("item/get_goods") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Goods data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddengoodsAdd').val(val);
            });
        }

        if (!$('#sexAdd').hasClass('select2-hidden-accessible')) {
            $('#sexAdd').select2({
                tags: true,
                placeholder: '- PILIH SEX -',
                width: '100%',
                dropdownParent: $('#modalAddItem'),
                ajax: {
                    url: '<?= base_url("item/get_sexs") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Sex data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddensexAdd').val(val);
            });
        }

        if (!$('#priceClassAdd').hasClass('select2-hidden-accessible')) {
            $('#priceClassAdd').select2({
                tags: true,
                placeholder: '- PILIH PRICE CLASS -',
                width: '100%',
                dropdownParent: $('#modalAddItem'),
                ajax: {
                    url: '<?= base_url("item/get_price_classes") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Price class data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddenpriceClassAdd').val(val);
            });
        }

        if (!$('#packingUnitAdd').hasClass('select2-hidden-accessible')) {
            $('#packingUnitAdd').select2({
                tags: true,
                placeholder: '- PILIH PACKING UNIT -',
                width: '100%',
                dropdownParent: $('#modalAddItem'),
                ajax: {
                    url: '<?= base_url("item/get_packing_units") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Packing unit data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddenpackingUnitAdd').val(val);
            });
        }
    });

    // SUBMIT FORM
    $('#formAddItem').on('submit', function(e){
        e.preventDefault();

        var data = {
            item: $('input[name="item"]').val(),
            full_name: $('input[name="full_name"]').val(),
            goods: $('#goodsAdd').select2('data')[0] ? $('#goodsAdd').select2('data')[0].text : '',
            sex: $('#sexAdd').select2('data')[0] ? $('#sexAdd').select2('data')[0].text : '',
            price_class: $('#priceClassAdd').select2('data')[0] ? $('#priceClassAdd').select2('data')[0].text : '',
            packing_unit: $('#packingUnitAdd').select2('data')[0] ? $('#packingUnitAdd').select2('data')[0].text : '',
            packing_qty: $('input[name="packing_qty"]').val(),
            price_goods: $('input[name="price_goods"]').val(),
            price_delivery: $('input[name="price_delivery"]').val(),
            price_vaccine: $('input[name="price_vaccine"]').val(),
            acc_dr: $('input[name="acc_dr"]').val(),
            acc_cr: $('input[name="acc_cr"]').val(),
            remark: $('input[name="remark"]').val()
        };

        console.log('DATA SUBMIT:', data); // 🔹 Debug, pastikan semua terisi

        $.post("<?= base_url('item/create'); ?>", data, function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success':'error', r.message);

            if(r.status){
                $('#modalAddItem').modal('hide');
                $('#formAddItem')[0].reset();
                $('#goodsAdd, #sexAdd, #priceClassAdd, #packingUnitAdd').val(null).trigger('change');
                loadPage(1); // refresh table
            }
        }).fail(function(xhr){
            console.error('AJAX ERROR:', xhr.responseText);
            showToast('error', 'Terjadi kesalahan server!');
        });
    });

    $('#itemEdit').on('shown.bs.modal', function() {
        if (!$('#goodsEdit').hasClass('select2-hidden-accessible')) {
            $('#goodsEdit').select2({
                tags: true,
                placeholder: '- PILIH GOODS -',
                width: '100%',
                dropdownParent: $('#itemEdit'),
                ajax: {
                    url: '<?= base_url("item/get_goods") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Goods data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddengoodsEdit').val(val);
            });
        }

        if (!$('#sexEdit').hasClass('select2-hidden-accessible')) {
            $('#sexEdit').select2({
                tags: true,
                placeholder: '- PILIH SEX -',
                width: '100%',
                dropdownParent: $('#itemEdit'),
                ajax: {
                    url: '<?= base_url("item/get_sexs") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Sex data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddensexEdit').val(val);
            });
        }

        if (!$('#priceClassEdit').hasClass('select2-hidden-accessible')) {
            $('#priceClassEdit').select2({
                tags: true,
                placeholder: '- PILIH PRICE CLASS -',
                width: '100%',
                dropdownParent: $('#itemEdit'),
                ajax: {
                    url: '<?= base_url("item/get_price_classes") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Price class data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddenpriceClassEdit').val(val);
            });
        }

        if (!$('#packingUnitEdit').hasClass('select2-hidden-accessible')) {
            $('#packingUnitEdit').select2({
                tags: true,
                placeholder: '- PILIH PACKING UNIT -',
                width: '100%',
                dropdownParent: $('#itemEdit'),
                ajax: {
                    url: '<?= base_url("item/get_packing_units") ?>',
                    dataType: 'json',
                    processResults: function(data){
                        console.log('Packing unit data:', data);
                        return { results: data };
                    }
                }
            }).on('select2:select', function(e){
                let val = e.params.data.text.toUpperCase();
                $('#hiddenpackingUnitEdit').val(val);
            });
        }
    });

    // edit submit
    $('#formEdit').on('submit', function(e){
        e.preventDefault();
        $.post("<?= base_url('item/update'); ?>", $(this).serialize())
        .done(function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success' : 'error', r.message);
            if (r.status) { $('#itemEdit').modal('hide'); loadPage(state.page); }
        }).fail(function(xhr){ showToast('error','Server error'); console.log(xhr.responseText); });
    });

    // export
    $('#exportExcel').on('click', function(){
        var url = "<?= base_url('item/export_excel'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
        window.location = url;
    });
    $('#exportPdf').on('click', function(){
        var url = "<?= base_url('item/export_pdf'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
        window.location = url;
    });

    
});

function loadPage(page){
    state.page = page;
    $('#table-body').html('<tr><td colspan="13" style="text-align: center">Loading...</td></tr>');
    $.get("<?= base_url('item/load_data'); ?>", { page: page, limit: state.limit, search: state.search, order: state.order, dir: state.dir }, function(res){
        var data = typeof res === 'string' ? JSON.parse(res) : res;
        renderTable(data.rows);
        $('#pagination').html(data.pagination);
        $('#info').text('Menampilkan '+data.rows.length+' dari '+data.total);
        $('#mainTable thead th[data-order]').each(function(){ var col = $(this).data('order'); $(this).find('.sort-icon').text(col===state.order?(state.dir==='ASC'?' ▲':' ▼'):''); });
    }, 'json');
}

function renderTable(rows){
    var html = '';
    if (!rows || rows.length === 0) {
        html = '<tr><td colspan="13" style="text-align: center">Tidak ada data</td></tr>';
    } else {
        rows.forEach(function(r){
            html += '<tr>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.ITEM)+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.FULL_NAME)+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.GOODS||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.SEX||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PRICE_CLASS||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PACKING_UNIT||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PACKING_QTY||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PRICE_GOODS||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PRICE_DELIVERY||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.PRICE_VACCINE||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.ACC_DR||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.ACC_CR||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.REMARK||'')+'</td>';
            html += '<td style="text-align: center; vertical-align: middle">';
            html += '<button class="btn btn-sm btn-warning me-1" onclick="showEdit(\''+r.ITEM+'\')">Edit</button>';
            html += '<button class="btn btn-sm btn-danger" onclick="doDelete(\''+r.ITEM+'\')">Delete</button>';
            html += '</td>';
            html += '</tr>';
        });
    }
    $('#table-body').html(html);
}

function showDetail(item){
    $('#detailBody').html('Loading...');
    $('#modalDetail').modal('show');
    $.get("<?= base_url('item/edit'); ?>", { item: item }, function(res){
        var r = typeof res === 'string' ? JSON.parse(res) : res;
        
        if (!r.status) { $('#detailBody').html('Error'); return; }
        var d = r.data;
        var html = '<table class=\"table table-bordered\">';
        for (var k in d) { html += '<tr><th style=\"width:30%\">'+k+'</th><td>'+escapeHtml(d[k]||'')+'</td></tr>'; }
        html += '</table>';
        $('#detailBody').html(html);
    }, 'json');
    
}

function preloadSelect2Value(selectId, hiddenId, value){
    if (!value) return;

    // Buat option agar select2 mengenali value-nya
    let option = new Option(value, value, true, true);
    $(selectId).append(option).trigger('change');

    // Isi hidden inputnya
    $(hiddenId).val(value);
}

function showEdit(item){
    $.get("<?= base_url('item/edit'); ?>", { item: item }, function(res){
        var r = typeof res === 'string' ? JSON.parse(res) : res;
        console.log(r.data);
        if (!r.status) { alert('Error'); return; }

        var d = r.data;
        var f = $('#formEdit');

        f.find('input[name="orig_item"]').val(d.ITEM);
        f.find('input[name="item"]').val(d.ITEM);
        f.find('input[name="full_name"]').val(d.FULL_NAME);

        // ===========================
        // PRELOAD SELECT2 (WAJIB!)
        // ===========================
        preloadSelect2Value('#goodsEdit', '#hiddengoodsEdit', d.GOODS);
        preloadSelect2Value('#sexEdit', '#hiddensexEdit', d.SEX);
        preloadSelect2Value('#priceClassEdit', '#hiddenpriceClassEdit', d.PRICE_CLASS);
        preloadSelect2Value('#packingUnitEdit', '#hiddenpackingUnitEdit', d.PACKING_UNIT);

        // ===========================
        // INPUT LAIN
        // ===========================
        f.find('input[name="packing_qty"]').val(d.PACKING_QTY);
        f.find('input[name="price_goods"]').val(d.PRICE_GOODS);
        f.find('input[name="price_delivery"]').val(d.PRICE_DELIVERY);
        f.find('input[name="price_vaccine"]').val(d.PRICE_VACCINE);
        f.find('input[name="acc_dr"]').val(d.ACC_DR);
        f.find('input[name="acc_cr"]').val(d.ACC_CR);
        f.find('input[name="remark"]').val(d.REMARK);

        $('#itemEdit').modal('show');
    });
}

function doDelete(item){
    if (!confirm('Yakin ingin dihapus?')) return;
    $.post("<?= base_url('item/remove'); ?>", { item: item }, function(res){
        var r = typeof res === 'string' ? JSON.parse(res) : res;
        showToast(r.status ? 'success' : 'error', r.message);
        if (r.status) loadPage(state.page);
    }, 'json');
}

function escapeHtml(text){
    if (text === null || text === undefined) return '';
    return $('<div/>').text(text).html();
}

function showToast(type, message){
    const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
    Toast.fire({ icon:type, title:message });
}
</script>

<script>
  $(document).ready(function() {
    console.log("DOM Ready, initialising Select2...");

    $('.select2').select2({
        width: '100%'
    });
});
</script>