<script src="<?php echo base_url('assets/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-duration-picker-debug.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.maskMoney.min.js'); ?>"></script>
<!-- <script src="<?php echo base_url('assets/js/menu.js')?>"></script> -->
<script type="text/javascript">
  $(document).ready(function(e) {
    $('a#logout').click(function(){
      if(confirm('Are you sure to logout')) {
          return true;
      }

      return false;
    });

    table = $('#schedule-table').DataTable({
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": '<?php echo site_url('/lot/datatable'); ?>',
          "type": "POST"
      },
      //Set column definition initialisation properties.
      "columns": [
          {"data": "no_lot"},
          {"data": "unit"},
          {"data": "action"},
      ],
      "columnDefs": [
        { "orderable": false, "targets": [2] }
      ],
      "bLengthChange": false,
    });

    $('#interval').maskMoney({
      prefix:'Rp. ',
      thousands:'.',
      decimal:',',
      precision:0
    });

    $('#schedule-table_wrapper').find(".col-md-6:first").remove();
    
    $('#schedule-table_wrapper').find(".row:first").append('<div class="col-sm-12 col-md-4"><div id="schedule-table_add_btn" class="dataTables-schedule_add_btn"><a href="<?php echo base_url('lot/edit')?>" class="btn btn-sm btn-add btn-success" style="margin-right:15px;">Edit</a><a href="<?php echo base_url('lot/add');?>"class="btn btn-sm btn-add btn-success">Tambah</a></div></div>');
    
    $('#schedule-table_wrapper').find(".col-md-6").addClass('col-md-3');
    
    $('#schedule-table_wrapper').find(".col-md-3").removeClass('col-md-6');
    
    $('<div class="col-sm-12 col-md-3"><div class="dataTables_schedule" id="schedule-table_schedule"><label>Schedule <select class="form-control form-control-sm" id="filter-schedule" aria-controls="schedule-table" data-provide="selectpicker" data-live-search="true"></select></label></div></div>').insertAfter(".col-md-3:first");
    
    $('<div class="col-sm-12 col-md-2"><div class="dataTables_type" id="schedule-table_type"><label>Type <select data-provide="selectpicker" class="form-control form-control-sm" id="type-filter" aria-controls="schedule-table"><option value="">All</option><option value="1">Online</option><option value="0">Live</option></select></label></div></div>').insertBefore(".col-md-4:first");
    
    schedule_filter();

    $('#filter-schedule').on('keyup change', function(){
       table.column(2).search(this.value).draw();   
    });

    // $('#type-filter').on('keyup change', function(){
    //    table.column(1).search(this.value).draw();   
    // });
    
    $('#duration').durationPicker({
        translations: {
          day: 'hari',
          hour: 'jam',
          minute: 'menit',
          second: 'detik',
          days: 'hari',
          hours: 'jam',
          minutes: 'menit',
          seconds: 'detik',
        },
        showSeconds: false,
        showDays: false,
        onChanged: function (value, isInitializing) {
          $('input[name="duration"]').val(value);
        }
    });

    $("#bdp-hours-label").prependTo(".bdp-hours");
    $("#bdp-minutes-label").prependTo(".bdp-minutes");

    $('#category_id').on('change', function() {
      if ( $(this).val() == '0')
      {
        $("#input-limit").show();
      } else {
        $("#input-limit").hide();
        $('#schedule-form').find('input#limit').val('');
      }
    });
    
    $('#type').on('change', function() {
      if ( $(this).val() == '1')
      {
        $("#input-duration").show();
      }
      else
      {
        $("#input-duration").hide();
        $('input[name="duration"]').val('');
      }
    });
    
    $('[data-tables=true]').on('click', '.actDelete', function(e) {
      var id = $(this).attr('data-id');
      $("#ScheduleModal2-body").empty();
      $("#ScheduleModal2-body").append('<input type="hidden" id="deletedID">Apakah Anda yakin akan menghapusnya?');
      $('#ScheduleModal2-title').text('Confirm Delete');
      $('#deletedID').val(id);
      $('#ScheduleModal2-footer').show();
      $('#ScheduleModal2').modal('show');
    });

    $('[data-tables=true]').on('click', '.btn-edit', function(e) {
      var id = $(this).attr('data-id');
      $.ajax({
        type: "GET",
        url: "<?php echo base_url('lot/');?>getdata/"+id,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(data){
          $('#schedule-form').find('input#id').remove();
          $('<input type="hidden" value="'+id+'" name="id" id="id">').insertBefore('input#date');
          $('#schedule-form').find('input#date').datepicker('setDate',data.date);
          $('#schedule-form').find('input#interval').val(data.interval);
          $('#schedule-form').find('input#waktu').val(data.waktu);
          $('#schedule-form').find('select#type').val(data.type);
          $('#schedule-form').find('select#place_id').val(data.place_id);
          $('#schedule-form').find('select#category_id').val(data.category_id);
          $('#schedule-form').find('input.input-smhours').val(data.hour);
          $('#schedule-form').find('input.input-smminutes').val(data.minute);
          if (data.category_id == '1') {
            $("#input-limit").hide();
            $('#schedule-form').find('input#limit').val('');
          } else {
            $("#input-limit").show();
            $('#schedule-form').find('input#limit').val(data.limit);
          }
          if ( data.type == '1')
          {
            $('input[name="duration"]').val(data.duration);
            $("#input-duration").show();
          }
          else
          {
            $("#input-duration").hide();
            $('input[name="duration"]').val(0);
          }
          $(".invalid-feedback").remove();
          $('#schedule-form').find('input').removeClass('is-invalid');
          $('#schedule-form').find('select').removeClass('is-invalid');
          $('#ScheduleModal').modal('show');
        },
        error: function(response) {
          alert(error);
        },
      });
    });

    $('#submit').on('click',function(){
      if($('#schedule-form').find('input#id').length > 0) {
        var id = $('#schedule-form').find('input#id').val();
        $.ajax({
          type: "POST",
          url: "<?php echo base_url('lot/update/');?>"+id,
          data: new FormData($("#schedule-form")[0]),
          dataType: "json",
          processData: false,
          contentType: false,
          success: function(data){

            if(data.success == true){
                $('#schedule-form').find('input').val('');
                $('#schedule-form').find('select').val(1);
                $('#ScheduleModal').modal('hide');
                $('<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Success!</strong> Data Schedule berhasil diupdate.</div>').insertBefore('#schedule-table_wrapper');
                refresh();
                $('#schedule-filter').selectpicker('refresh');
            } else if(data.success == false){
                $('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Error!</strong> Terjadi kasalahan.</div>').insertBefore('.form-group.date');
            }
        
          },
          error: function(response) {
            alert(error);
          },
        });
      } else {
        $.ajax({
          type: "POST",
          url: "<?php echo base_url('lot/submit');?>",
          data: new FormData($("#schedule-form")[0]),
          dataType: "json",
          processData: false,
          contentType: false,
          success: function(data){
            if(data.success == true){
                $('#schedule-form').find('input').val('');
                $('#schedule-form').find('select').val(1);
                $('#ScheduleModal').modal('hide');
                $('<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Success!</strong> Data Schedule berhasil ditambahkan.</div>').insertBefore('#schedule-table_wrapper');
                refresh();
                $('#schedule-filter').selectpicker('refresh');
            } else {
              $(".invalid-feedback").remove();
              $('#schedule-form').find('input').removeClass('is-invalid');
              $('#schedule-form').find('select').removeClass('is-invalid');
              for (var i = 0; i < data.inputerror.length; i++) 
              {
                if (data.inputerror[i] == 'duration') {
                  $('.input-smhours').addClass('is-invalid');
                  $('.input-smminutes').addClass('is-invalid');
                  $("#input-duration").find('.col-md-9').append('<div class="invalid-feedback block" style="display: block;">'+data.error_string[i]+'</div>');
                } else {
                  $('[name="'+data.inputerror[i]+'"]').addClass('is-invalid');
                  $('<div class="invalid-feedback">'+data.error_string[i]+'</div>').insertAfter('[name="'+data.inputerror[i]+'"]');
                }
              }
            }
        
          },
          error: function(response) {
            alert(error);
          },
        });
      }
    });

    // $('.btn-add').on('click',function(){
    //   $('#modal-title').html('Lot Management');
    //   $('#schedule-form').find('input#id').remove();
    //   $('#schedule-form').find('input').val('');
    //   $('#schedule-form').find('select').val(0);
    //   $('#schedule-form').find('select#category_id').val(1);
    //   $('#schedule-form').find('input[name="duration"]').val(0);
    //   $("#input-limit").hide();
    //   $("#input-duration").hide();
    //   $(".invalid-feedback").remove();
    //   $('#schedule-form').find('input').removeClass('is-invalid');
    //   $('#schedule-form').find('select').removeClass('is-invalid');
    //   $('#ScheduleModal').modal('show');
    // });

    $('#submit-delete').on('click', function(){
      var id = $('#deletedID').val();
      $.ajax({
          type: "POST",
          url: "<?php echo base_url('lot/delete/');?>"+id,
          dataType: "json",
          processData: false,
          contentType: false,
          success: function(data){
            if(data.success == true){
                $('#ScheduleModal2').modal('hide');
                $('<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Success!</strong> Data Schedule berhasil dihapus.</div>').insertBefore('#schedule-table_wrapper');
                refresh();
                $('#schedule-filter').selectpicker('refresh');
            } else if(data.success == false){
                $('#ScheduleModal').modal('hide');
                $('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><strong>Error!</strong> Terjadi kasalahan.</div>').insertBefore('#schedule-table_wrapper');
            }
          },
          error: function(response) {
            alert(error);
          },
      });
    });
  });

  function schedule_filter() {
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('lot/');?>getSchedule",
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('#filter-schedule').append('<option value="">All</option>');
          $.each(data.data, function( index, val ) {
            $('#filter-schedule').append('<option value="'+val.id+'">'+val.date+'</option>');
          });

          $('#filter-schedule').selectpicker('refresh');
        } else {
          $('#filter-schedule').append('<option value="">All</option>');
          $('#filter-schedule').selectpicker('refresh');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function detail_item(id)
  {
      $('#stock-detail-title').text('Detail Schedule');
      var detailElement = '<div class="row"><div class="col-md-6"><div class="card bg-pale-secondary" style="min-height: 100%;"><div class="card-body"><div class="row"><div class="col-md-4"><b class="pull-right">Jadwal</b></div><div class="col-md-8" id="show_date"> : </div><div class="col-md-4"><b class="pull-right">Tempat</b></div><div class="col-md-8" id="show_company"> : </div><div class="col-md-4"><b class="pull-right">Tipe</b></div><div class="col-md-8" id="show_type"> : </div><div class="col-md-4"><b class="pull-right">No. lot</b></div><div class="col-md-8" id="show_lot"> : </div></div></div></div></div><div class="col-md-6"><div class="card bg-pale-secondary" style="min-height: 100%;"><div class="card-body"><div class="row"><div class="col-md-5"><b class="pull-right">Item</b></div><div class="col-md-7" id="show_item"> : </div><div class="col-md-5"><b class="pull-right">Virtual Account</b></div><div class="col-md-7" id="show_va"> : </div><div class="col-md-5"><b class="pull-right">Status</b></div><div class="col-md-7" id="show_status"> : </div></div></div></div></div></div>';

      $.ajax({
          url: "<?php echo base_url('lot/getdata');?>/"+id,
          type: "GET",
          dataType: "JSON",
          success: function(data)
          {
            var tipe = data.data.type == 0 ? "Live" : "Online";
            var status = data.data.status == 0 ? "Belum terjual" : "Terjual";
            $("#modal-body").empty();
            $('#modal-body').append(detailElement);
            $('#show_date').append(data.data.date);
            $('#show_company').append(data.data.CompanyName);
            $('#show_type').append(tipe);
            $('#show_lot').append(data.data.no_lot);
            $('#show_item').append(data.data.stock_name);
            $('#show_va').append(data.data.no_va);
            $('#show_status').append(status);
            $('#modal-title').text('Lot Detail'); // Set title to Bootstrap modal title
            $('#lot_detail').find('#submit').remove();
            $('#lot_detail').modal('show');
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error get data from ajax');
          }
      });
  }

  function addPeriod(nStr)
  {
      nStr += '';
      x = nStr.split('.');
      x1 = x[0];
      x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
          x1 = x1.replace(rgx, '$1' + '.' + '$2');
      }
      return x1 + x2;
  }

  function refresh() {
    schedule_filter();
    table.ajax.reload(null,false); //reload datatable ajax 
  }

</script>
<?php $this->load->view($content_modal); ?>
  </body>
</html>