<script src="<?php echo base_url('assets/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-duration-picker-debug.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.maskMoney.min.js'); ?>"></script>
<!-- <script src="<?php echo base_url('assets/js/menu.js')?>"></script> -->
<script src="<?php echo base_url('assets/js/multiselect.min.js')?>"></script>

<script type="text/javascript">
$('a#logout').click(function(){
  if(confirm('Are you sure to logout')) {
      return true;
  }

  return false;
});
$('#multiselect').multiselect();

schedule_filter();
// stock_data();

$('#schedule-filter').on('keyup change', function(){
  if ($(this).val() != '') {
    var id = $(this).val();
    var name;
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('lot/');?>getStockData/"+id,
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('#multiselect').empty();
          $('#schedule_id').remove();
          $('#date').remove();
          $('#form_body').append('<input type="hidden" value="'+data.schedule_id+'" name="schedule_id" id="schedule_id">');
          $('#form_body').append('<input type="hidden" value="'+data.date+'" name="date" id="date">');

          $.each(data.data, function( index, val ) {
            name = val.Merk+' '+val.Seri;
            $('#multiselect').append('<option value="'+val.AuctionItemId+'.'+name+'">'+name+'</option>');
          });

          $('#company-filter').selectpicker('refresh');
        } else {
          $('#multiselect').empty();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  } else {
    $('#multiselect').empty();
  }
});

$("#form_body").submit(function(e) {
        var valid = true;
        
        $('#multiselect_to').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        if($('#multiselect_to').children('option').length == 0){
            $('#multiselect_to').addClass('is-invalid');
            $('<div class="invalid-feedback">Belum ada data yang terpilih.</div>').insertAfter('#multiselect_to');
            valid = false;
        }

        if(valid == false){
            return false; //is superfluous, but I put it here as a fallback
        } else {
          $("#form_body").attr("method","POST");
          $("#form_body").attr("action","<?php echo base_url('lot/submit')?>");
          return true;
        }
    });

function schedule_filter() {
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('lot/');?>getLotSchedule/",
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('#schedule-filter').append('<option value="">-Select-</option');
          $.each(data.data, function( index, val ) {
            $('#schedule-filter').append('<option value="'+val.id+'.'+val.item_id+'.'+val.date+'">'+val.date+' | '+val.CompanyName+'</option>');
          });

          $('#schedule-filter').selectpicker('refresh');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function stock_data() {
    $.ajax({
      type: "GET",
      url: "<?php echo $this->config->item('ibid_stock');?>/api/getallStock",
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('#multiselect').empty();


          $.each(data.data, function( index, val ) {
            $('#multiselect').append('<option value="'+val.AuctionItemId+'">'+val.Merk+' '+val.Seri+'</option>');
          });

          // $('#company-filter').selectpicker('refresh');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }
</script>
