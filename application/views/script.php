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
    $('<input type="hidden" id="start-price" val="">').insertAfter('#body');
    $('<input type="hidden" id="interval" val="">').insertAfter('#body');

    getLotData();

    var start = 0;

    $('#start').on('click', function(){
      var conf = confirm('Mulai lelang untuk lot ini?');
      if (conf == true) {
        $('#floor-bid').prop("disabled", false);
        $('#start').prop("disabled",true);
        $('#btn_next').prop("disabled",true); 
        start = setInterval( getBidLog, 2000 );
      } 
    });

    $('#floor-bid').on('click', function(){
      floorBid();
    });

    $('#btn_count').on('click', function(){
      var count = $('#count').val();
      if (count < 3) {
        count = parseInt(count) + 1;
        $('#count').val(count);
        if (count == 3) {
          $('#floor-bid').prop("disabled", true);
          $('#btn_next').prop("disabled",false); 
          alert('sold');
          clearInterval(start);
        }
        // alert('ok');
      } else {
        alert('Item already sold');
      }
    });
    
    // getBidLog();
    
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
          $('#bid-log').empty();
          $('#btn_next').prop("disabled",false);
          var name = data.data.Merk+" "+data.data.Tipe+" "+data.data.Silinder;
          var lot = "Lot "+data.data.NoLot;
          $('#item_name').append(name);
          $('#item_lot').append(lot);
          $('#item_color').append(data.data.Warna || '-');
          $('#item_transmisi').append(data.data.Transmisi || '-');
          $('#item_km').append(data.data.Kilometer || '-');
          $('#item_bahanbakar').append(data.data.BahanBakar || '-');
          $('#item_exterior').append(data.data.Exterior || '-');
          $('#item_interior').append(data.data.Interior || '-');
          $('#item_mechanical').append(data.data.Mesin || '-');
          $('#item_frame').append(data.data.Rangka || '-');
          $('#item_startprice').append("Rp. "+addPeriod(data.data.StartPrice) || '-');
          $('#start-price').val(data.data.StartPrice);
          $('#interval').val(500000);
          $('#floor-bid').prop("disabled",true);
          $('#start').prop("disabled",false);
          if (data.disable) {
            $('#btn_next').prop("disabled",true);
          }

        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function getBidLog(){
    var price = $('#start-price').val();
    var interval = $('#interval').val();
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>bidLogExample/"+price+"/"+interval,
      dataType: "json",
      success: function(data){
        if (data.status) {
          // $('#bid-log').empty();
          $('#bid-log').prepend('<option value="">'+addPeriod(data.data.Nominal)+' '+data.data.State+' '+data.data.No+'</option>');
          $('#start-price').val(data.data.Nominal);
        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function floorBid(){
    var price = $('#start-price').val();
    var interval = $('#interval').val();
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>floorBidExample/"+price+"/"+interval,
      dataType: "json",
      success: function(data){
        if (data.status) {
          // $('#bid-log').empty();
          $('#bid-log').prepend('<option value="">'+addPeriod(data.data.Nominal)+' '+data.data.State+' ....</option>');
          $('#start-price').val(data.data.Nominal);
        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }



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


</script>
<?php $this->load->view($content_modal); ?>
  </body>
</html>