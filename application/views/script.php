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
    $('<input type="hidden" id="lot_id" value="0"></input').insertAfter('#body');
    getLotData();
    
  });

  function getLotData() {
    var id = $('#lot_id').val();
    id = parseInt(id);
    id = id + 1;
    $('#lot_id').val(id);
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>datalot/"+id,
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('.data-lot').html('');
          var name = data.data.Merk+" "+data.data.Seri+" "+data.data.Silinder;
          var lot = "Lot "+data.data.NoLot;
          $('#item_name').append(name);
          $('#item_lot').append(lot);
          $('#item_color').append(data.data.Warna);
          $('#item_transmisi').append(data.data.Transmisi);
          $('#item_km').append(data.data.Kilometer);
          $('#item_bahanbakar').append(data.data.BahanBakar);
          $('#item_exterior').append(data.data.Exterior);
          $('#item_interior').append(data.data.Interior);
          $('#item_mechanical').append(data.data.Mechanical);
          $('#item_frame').append(data.data.Frame);
        } else {
          $('#btn_next').attr("disabled","disabled");
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

$('#btn_count').on('click', function(){
  var count = $('#count').val();
  if (count < 3) {
    count = parseInt(count) + 1;
    $('#count').val(count);
    if (count == 3) {
      alert('sold');
    }
    // alert('ok');
  } else {
    alert('Item already sold');
  }
});

$('#btn_next').on('click', function(){
  var show = confirm('Jump into next lot?');
  if (show == true) {
    getLotData();
  } else {

  }
});

$('#btn_skip').on('click', function(){
  var valid = true;
  var description = '<div class="form-group"><label for="textarea">Description : </label><textarea class="form-control" id="textarea" rows="6"></textarea></div>'
        $('#skip').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        if($('#skip').val() == ''){
            $('#skip').addClass('is-invalid');
            $('<div class="invalid-feedback">Wajib isi lot.</div>').insertAfter('#skip');
            valid = false;
        }

        if(valid == false){
            return false; //is superfluous, but I put it here as a fallback
        } else {
            $('.modal-title').text('Skip this lot?');
            $('.modal-body').append(description);
            $('#auction_modal').modal('show');
          return true;
        }
});


</script>
<?php $this->load->view($content_modal); ?>
  </body>
</html>