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
    $('<input type="hidden" id="unit_name" val="">').insertAfter('#body');
    $('<input type="hidden" id="unit_grade" val="">').insertAfter('#body');
    $('<input type="hidden" id="schedule_id" val="">').insertAfter('#body');
    $('<input type="hidden" id="stock_id" val="">').insertAfter('#body');
    $('<input type="hidden" id="va" val="">').insertAfter('#body');
    $('<input type="hidden" id="npl" val="">').insertAfter('#body');
    $('<input type="hidden" id="date" val="">').insertAfter('#body');

    getLotData();

    var start = 0;
    var startProxy = 0;
    '<button type="button" class="btn btn-success btn-submit" id="submit_winner">Lanjutkan</button>'

    $('#start').on('click', function(){
        $('#modal-auction-title').text('Mulai lelang untuk lot ini?');
        $('#modal-auction-body').empty();
        $('#confirm-skip').hide();
        $('#confirm-next').hide();
        $('#confirm-start').show();
        $('#auction_modal').modal('show');
    });

    $('#confirm-start').on('click', function(){
        $('#auction_modal').modal('hide');
        $('#floor-bid').prop("disabled", false);
        $('#start').prop("disabled",true); 
        $('#btn_count').prop("disabled",false);
        start = setInterval( getBidLog, 4000 );
        startProxy = setInterval( getProxyBid, 6000);
    });

    $('#btn_next').on('click', function(){
        $('#modal-auction-title').text('Jump into next lot?');
        $('#modal-auction-body').empty();
        $('#confirm-start').hide();
        $('#confirm-skip').hide();
        $('#confirm-next').show();
        $('#auction_modal').modal('show');
    });

    $('#confirm-next').on('click', function(){
        getLotData();
        $('#auction_modal').modal('hide');
    });

    $('#floor-bid').on('click', function(){
      floorBid();
      if(start == null && startProxy == null){
        start = setInterval( getBidLog, 2000 );
        startProxy = setInterval( getProxyBid, 6000);
      }
    });

    $('#btn_skip').on('click', function(){
      var valid = true;
      var description = '<div class="form-group"><label for="textarea">Berikan alasan : </label><textarea class="form-control" id="reason" rows="6"></textarea></div>'
            $('#skip').removeClass('is-invalid');
            $('#reason').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            if($('#skip').val() == ''){
                $('#skip').addClass('is-invalid');
                $('<div class="invalid-feedback">Wajib isi lot.</div>').insertAfter('#skip');
                valid = false;
            }

            if(valid == false){
                return false; //is superfluous, but I put it here as a fallback
            } else {
                $('#modal-auction-title').text('Skip lot');
                $('#modal-auction-body').empty();
                $('#modal-auction-body').append(description);
                $('#confirm-start').hide();
                $('#confirm-next').hide();
                $('#confirm-skip').show();
                $('#auction_modal').modal('show');
              return true;
            }
    });

    $('#confirm-skip').on('click', function(){
        $('#reason').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        skipLot();
    });

    $('#btn_count').on('click', function(){
      var count = $('#count').val();
      if (count < 3) {
        count = parseInt(count) + 1;
        $('#count').val(count);
        if (count == 2) {
          clearInterval(start);
          clearInterval(startProxy);
          start = null;
          startProxy = null;
        }
        if (count == 3) {
          var winner = $('#npl').val();
          $('#floor-bid').prop("disabled", true);
          $('#modal').modal({
            backdrop: 'static',
            keyboard: false
          })
          var lot = $('#lot_id').val();
          var name = $('#unit_name').val();
          var grade = $('#unit_grade').val();
          var price = $('#start-price').val();
          if (winner == '') {
            $('#modal-title').text('Selamat, Pemenang Floor Bidder');
            var body ='<h4 id="modal-header">Detail Unit</h4>'
                      +'<div class="row">'
                          +'<div class="col-md-12">'
                          +'<div class="card>'
                            +'<div class="card-body">'
                              +'<div class="row">'
                                +'<div class="col-md-3"><b class="pull-left">No.Lot</b></div>'
                                +'<div class="col-md-9" id="show_date"> : '+lot+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Unit Name</b></div>'
                                +'<div class="col-md-9" id="show_company"> : '+name+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Grade</b></div>'
                                +'<div class="col-md-9" id="show_type"> : '+grade+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Harga</b></div>'
                                +'<div class="col-md-9" id="show_lot"> : Rp. '+addPeriod(price)+'</div>'
                              +'</div>'
                            +'</div>'
                          +'</div>'
                          +'</div>'
                        +'</div>'
                        +'<hr class="custom">'
                        +'<h4 id="modal-header">Isi Npl Pemenang</h4>'
                        +'<div class="form-group noLot-edit">'
                          +'<div class="col-md-6" style="padding-left:0">'
                            +'<input type="text" name="input_npl" class="form-control" id="input_npl">'
                          +'</div>'
                        +'</div>';
              $('#modal-body').append(body);
              $('#modal').modal('show');
          } else {
              $('#modal-title').text('Selamat, Pemenang Online Bidder');
              var body ='<h4 id="modal-header">Detail Unit</h4>'
                        +'<div class="row">'
                          +'<div class="col-md-12">'
                          +'<div class="card>'
                            +'<div class="card-body">'
                              +'<div class="row">'
                                +'<div class="col-md-3"><b class="pull-left">No.Lot</b></div>'
                                +'<div class="col-md-9" id="show_date"> : '+lot+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Unit Name</b></div>'
                                +'<div class="col-md-9" id="show_company"> : '+name+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Grade</b></div>'
                                +'<div class="col-md-9" id="show_type"> : '+grade+'</div>'
                                +'<div class="col-md-3"><b class="pull-left">Harga</b></div>'
                                +'<div class="col-md-9" id="show_lot"> : Rp. '+addPeriod(price)+'</div>'
                              +'</div>'
                            +'</div>'
                          +'</div>'
                          +'</div>'
                        +'</div>'
                        +'<hr class="custom">'
                        +'<h4 id="modal-header">Detail Pemenang</h4>'
                          +'<div class="row">'
                            +'<div class="col-md-12">'
                            +'<div class="card>'
                              +'<div class="card-body">'
                                +'<div class="row">'
                                  +'<div class="col-md-3"><b class="pull-left">Peserta</b></div>'
                                  +'<div class="col-md-9" id="show_date"> : Online Bidder</div>'
                                  +'<div class="col-md-3"><b class="pull-left">Npl</b></div>'
                                  +'<div class="col-md-9" id="show_company"> : '+winner+'</div>'
                                +'</div>'
                              +'</div>'
                            +'</div>'
                            +'</div>'
                          +'</div>';
                          +'<hr class="custom">'
              $('#modal-body').append(body);
              $('#modal').modal('show');
          }
        }
        // alert('ok');
      } else {
        alert('Item already sold');
      }
    });

    $('#proceed-winner').on('click', function(){
      $('input').removeClass('is-invalid');
      $('.invalid-feedback').remove();
      var npl = $('#npl').val();
      if (npl == '') {
        if ($('#input_npl').val() == '') {
          $('[name="input_npl"]').addClass('is-invalid');
          $('<div class="invalid-feedback">Wajib isi NPL pemenang</div>').insertAfter('[name="input_npl"]');
          // alert('isi npl!!');
        } else {
          npl = $('#input_npl').val();
          submitWinner(npl);
        }
      } else {
        submitWinner(npl);
      }
      
      
    });
    
  });

  function getLotData() {
    var id = $('#lot_id').val();
    id = parseInt(id);
    id = id + 1;
    $('#lot_id').val(id);
    $('#loader').append('<i class="fa fa-spinner fa-pulse fa-4x fa-fw new-loader"></i>');
    $('#content').hide();
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>datalot/"+id,
      dataType: "json",
      success: function(data){
        $('#loader').empty();
        $('#content').show();
        if (data.jadwal) {
          if (data.status) {
            $('.data-lot').html('');
            $('#floor-bid').html('');
            $('#bid-log').empty();
            $('#btn_next').prop("disabled",false);
            var name = data.data.Merk+" "+data.data.Tipe;
            var lot = "Lot "+data.data.NoLot;
            $('#item_name').append(name+" "+data.data.Silinder);
            $('#item_lot').append(lot);
            $('#lot_id').val(data.data.NoLot);
            $('#item_color').append(data.data.Warna || '-');
            $('#item_transmisi').append(data.data.Transmisi || '-');
            $('#item_km').append(data.data.Kilometer || '-');
            $('#item_bahanbakar').append(data.data.BahanBakar || '-');
            $('#item_exterior').append(data.data.Exterior || '-');
            $('#item_interior').append(data.data.Interior || '-');
            $('#item_mechanical').append(data.data.Mesin || '-');
            $('#item_frame').append(data.data.Rangka || '-');
            $('#item_grade').append(data.data.Grade || '-');
            $('#item_startprice').append("Rp. "+addPeriod(data.data.StartPrice) || '-');
            $('#start-price').val(data.data.StartPrice);
            $('#interval').val(data.data.Interval);
            $('#unit_name').val(name);
            $('#unit_grade').val(data.data.Grade);
            $('#stock_id').val(data.data.AuctionItemId);
            $('#schedule_id').val(data.data.ScheduleId);
            $('#va').val(data.data.VA);
            $('#floor-bid').append("+"+addPeriod(data.data.Interval));
            $('#date').val(data.data.Date);
            $('#floor-bid').prop("disabled",true);
            $('#start').prop("disabled",false);
            $('#btn_count').prop("disabled",true);
            
          } else {
            $('#modal').modal({
              backdrop: 'static',
              keyboard: false
            })
            $('#modal-title').text('Data lot tidak tersedia..');
            $('#proceed-winner').hide();
            $('#modal').modal('show');
          }
        } else {
          $('#modal').modal({
              backdrop: 'static',
              keyboard: false
            })
            $('#modal-title').text('Tidak ada jadwal yang tersedia..');
            $('#proceed-winner').hide();
            $('#modal').modal('show');
        }

        if (data.disable) {
            $('#btn_next').prop("disabled",true);
        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function submitWinner(npl){
    var UnitName = $('#unit_name').val();
    var AuctionItemId = $('#stock_id').val();
    var ScheduleId = $('#schedule_id').val();
    var Va = $('#va').val();
    var Lot = $('#lot_id').val();
    var Schedule = $('#date').val();
    var Type = 0;
    var Price = $('#start-price').val();
      $.ajax({
        type: "POST",
        url: "<?php echo $this->config->item('ibid_kpl');?>/api/submitWinner", // Used for Staging
        // url: "http://ibid-kpl.dev/api/submitWinner", //Used on local
        data : {UnitName:UnitName,Npl:npl,Lot:Lot,ScheduleId:ScheduleId,Schedule:Schedule,Type:Type,AuctionItemId:AuctionItemId,Price:Price,Va:Va},
        dataType: "json",
        success: function(data){
          if (data.status) {
            $('#modal').modal('hide');
          } 
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
        },
      });
  }

  function skipLot(){
    var Reason = $('#reason').val();
    if (Reason == '') {
      $('#reason').addClass('is-invalid');
      $('<div class="invalid-feedback">Wajib isi alasan skip lot.</div>').insertAfter('#reason');
    } else {
        var SkipRange = $('#skip').val();
        var ScheduleId = $('#schedule_id').val();
        
        var Va = $('#va').val();
        var Lot = $('#lot_id').val();
            Lot = parseInt(Lot);
            Lot = Lot + 1;
          $.ajax({
            type: "POST",
            url: "<?php echo base_url('auction/');?>skip",
            data : {Lot:Lot,ScheduleId:ScheduleId,SkipRange:SkipRange,Reason:Reason},
            dataType: "json",
            success: function(data){
              if (data.status) {
                $('#auction_modal').modal('hide');
              }  else {
                $('#skip').addClass('is-invalid');
                $('<div class="invalid-feedback">Total lot hanya ada '+data.data.total+'.</div>').insertAfter('#skip');
                $('#auction_modal').modal('hide');
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            },
          });
    }
    
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
          $('#npl').val(data.data.No);
          $('#bid-log').prepend('<div class="col-xs-4 col-md-4">'+addPeriod(data.data.Nominal)+'</div><div class="col-xs-5 col-md-5 weight">'+data.data.State+'</div><div class="col-xs-3 col-md-3 weight">'+data.data.No+'</div>');
          
                                              
                                              
          $('#start-price').val(data.data.Nominal);
        } 
      },
    });
  }

  function getProxyBid(){
    var price = $('#start-price').val();
    var interval = $('#interval').val();
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>proxyBidExample/"+price+"/"+interval,
      dataType: "json",
      success: function(data){
        if (data.status) {
          // $('#bid-log').empty();
          $('#npl').val(data.data.No);
          $('#bid-log').prepend('<div class="col-xs-4 col-md-4">'+addPeriod(data.data.Nominal)+'</div><div class="col-xs-5 col-md-5 weight">'+data.data.State+'</div><div class="col-xs-3 col-md-3 weight">'+data.data.No+'</div>');
          
                                              
                                              
          $('#start-price').val(data.data.Nominal);
        } 
      }
    });
  }

  function floorBid(){
    var count = $('#count').val();
    var price = $('#start-price').val();
    var interval = $('#interval').val();
      if (count == 2) {
        count = 1;
        $('#count').val(count);
      }
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>floorBidExample/"+price+"/"+interval,
      dataType: "json",
      success: function(data){
        if (data.status) {
          // $('#bid-log').empty();
          $('#npl').val('');
          $('#bid-log').prepend('<div class="col-xs-4 col-md-4">'+addPeriod(data.data.Nominal)+'</div><div class="col-xs-5 col-md-5 weight">'+data.data.State+'</div><div class="col-xs-3 col-md-3 weight">....</div>');
          $('#start-price').val(data.data.Nominal);
        } 
      },
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


</script>
<?php $this->load->view($content_modal); ?>
  </body>
</html>