<script src="<?php echo base_url('auction/assets/js/jquery.js'); ?>"></script>
<script src="<?php echo base_url('auction/assets/js/popper.min.js'); ?>" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="<?php echo base_url('auction/assets/js/bootstrap.min.js'); ?>"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.0/firebase.js"></script>
<script type="text/javascript">
  // file: script.js
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyC-ZoZ16SiFPoz76W0yJbqhlLOYpPrMU7I",
    authDomain: "ibid-ams-sample.firebaseapp.com",
    databaseURL: "https://ibid-ams-sample.firebaseio.com",
    projectId: "ibid-ams-sample",
    storageBucket: "",
    messagingSenderId: "493210877814"
  };
  firebase.initializeApp(config);

  // create firebase database reference
  var dbRef = firebase.database();
  var activeCompany = dbRef.ref('company/<?php echo $CompanyId; ?>');
  var liveCount = activeCompany.child('liveCount');
  var onStock = activeCompany.child('currentStock');
  var onLog ;
  var onMode ;

  var start = 0;
  var startProxy = 0;
  var skipLotNo = 0;

  $(document).ready(function(e) {
    var date = new Date();
    var year = date.getFullYear();

    $('#auction_modal').on('shown.bs.modal', function () {
      $('#no').trigger('focus')
    })

    $('#another-modal').on('shown.bs.modal', function () {
      $('#modal-close').trigger('focus')
    })


    $('.site-footer').find('p').prepend('Copyright © '+year);
    $('a#logout').click(function(){
      $('#another-modal-header').removeClass('background-danger');
      $('#another-modal-title').css('color','');
      $('#another-modal-title').html('Konfirmasi Logout');
      $('#another-modal-body').html('Apakah anda yakin ingin keluar ?');
      $('#modal-no').show();
      $('#submit-logout').show();
      $('#modal-close').hide();
      $('#another-modal').modal('toggle');
    });

    $('<input type="hidden" id="count_value" value="0"></input').insertAfter('#body');
    $('<input type="hidden" id="lot_id" value="0"></input').insertAfter('#body');
    $('<input type="hidden" id="start-price" val="">').insertAfter('#body');
    $('<input type="hidden" id="interval" val="">').insertAfter('#body');
    $('<input type="hidden" id="unit_name" val="">').insertAfter('#body');
    $('<input type="hidden" id="unit_grade" val="">').insertAfter('#body');
    $('<input type="hidden" id="schedule_id" val="">').insertAfter('#body');
    $('<input type="hidden" id="stock_id" val="">').insertAfter('#body');
    $('<input type="hidden" id="va" val="">').insertAfter('#body');
    $('<input type="hidden" id="npl" val="">').insertAfter('#body');
    $('<input type="hidden" id="state">').insertAfter('#body');
    $('<input type="hidden" id="date" val="">').insertAfter('#body');
    $('<input type="hidden" id="auction_start" value="0">').insertAfter('#body');
    $('<input type="hidden" id="lot_status" value="">').insertAfter('#body');
    $('<input type="hidden" id="next_lot" value="next">').insertAfter('#body');
    $('<input type="hidden" id="model" value="">').insertAfter('#body');
    $('<input type="hidden" id="merk" value="">').insertAfter('#body');
    $('<input type="hidden" id="tipe" value="">').insertAfter('#body');
    $('<input type="hidden" id="silinder" value="">').insertAfter('#body');
    $('<input type="hidden" id="tahun" value="">').insertAfter('#body');
    $('<input type="hidden" id="nopol" value="">').insertAfter('#body');

    getLotData();
    
    '<button type="button" class="btn btn-success btn-submit" id="submit_winner">Lanjutkan</button>'

    $('#start').on('click', function(){
      var body = 'Apakah anda yakin akan memulai lelang ini?';
        $('#modal-auction-title').html('<i class="fa fa-warning new-alert" style="margin-right: 5px;"></i>Konfirmasi');
        $('#modal-auction-title').css("padding-left",'');
        $('#modal-auction-body').empty();
        $('#modal-auction-body').append(body);
        $('#confirm-skip').hide();
        $('#confirm-next').hide();
        $('#confirm-start').show();
        $('#auction_modal').modal('show');
    }); 

    $('#btn_next').on('click', function(){
      var data_button = $(this).attr('data-button');
      var body = "-";
      if(data_button == 'lot'){
        body = 'Apakah anda yakin akan melanjutkan ke lot selanjutnya ?';
      }else{
        body = 'Apakah anda yakin akan melanjutkan ke jadwal selanjutnya ?';
      }
        $('#modal-auction-title').html('<i class="fa fa-warning new-alert" style="margin-right: 5px;"></i>Konfirmasi');
        $('#modal-auction-body').empty();
        $('#modal-auction-body').append(body);
        $('#confirm-start').hide();
        $('#confirm-skip').hide();
        $('#confirm-next').show();
        $('#auction_modal').modal('show');
    });

    $('#confirm-next').on('click', function(){
      var next = $('#next_lot').val();
      $('#skip').val('');
      reset_count();
      if (next == 'next') {
        nextLot();
        $('#auction_modal').modal('hide');
      } else {
        getLotData();
        $('#auction_modal').modal('hide');
      }
    });

    $('#floor-bid').on('click', function(){
      floorBid();
      console.log(start);
    });

    $('#btn_skip').on('click', function(){
      var valid = true;
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
                checkLot();
              return true;
            }
    });

    $('#confirm-skip').on('click', function(){
        $('#reason').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        skipLot();
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
  liveCount.on("value", function(snapshot) {
    if (snapshot.exists()) {
      count_value = snapshot.val();
      if((count_value == 2) || (count_value == 3)){
        if (onMode != undefined) {
          onMode.set(false);
        }
      }else{
        if (onMode != undefined) {
          onMode.set(true);
        }
      }
      $('#count').val(snapshot.val());
      $('#count_value').val(snapshot.val());
    }else{
      $('#count').val("-");
      $('#count_value').val(0);
    }
  });

  function getLotData() {
    getLotData.called = true;
    var id = $('#lot_id').val();
    id = parseInt(id);
    id = id + 1;
    $('#lot_id').val(id);
    $('#loader').append('<i class="fa fa-spinner fa-pulse fa-lg fa-5x new-loader"></i>');
    $('#content').hide();
    $.ajax({
      type: "GET",
      url: "<?php echo base_url('auction/');?>datalot",
      dataType: "json",
      success: function(data){
        $('#loader').empty();
        $('#content').show();
        if (data.jadwal) {
          if (data.status) {
            $('.data-lot').html('');
            $('#floor-bid').html('');
            $('#harga_kelipatan').html('');
            $('#bid-log').empty();
            $('#top_bid').html('-');
            $('#top_bid_state').html('');
            $('#btn_next').prop("disabled",false);
            var name = data.data.Merk+" "+data.data.Tipe;
            // var lot = "Lot "+data.data.NoLot;
            $('#item_name').append(name+" "+data.data.Silinder+" "+data.data.Model);
            $('#item_lot').append(data.data.NoLot);
            $('#lot_id').val(data.data.NoLot);
            $('#item_color').append(data.data.Warna || '-');
            $('#item_transmisi').append(data.data.Transmisi || '-');
            $('#item_km').append(data.data.Kilometer || '-');
            $('#item_tahun').append(data.data.Tahun || '-');
            $('#item_nopol').append(data.data.NoPolisi || '-');
            $('#item_bahanbakar').append(data.data.BahanBakar || '-');
            $('#item_exterior').append(data.data.Exterior || '-');
            $('#item_interior').append(data.data.Interior || '-');
            $('#item_mechanical').append(data.data.Mesin || '-');
            $('#item_frame').append(data.data.Rangka || '-');
            $('#item_grade').append(data.data.Grade || '-');
            $('#item_startprice').append("Rp. "+addPeriod(data.data.StartPrice) || '-');
            $('#schedule_date').append(data.data.ScheduleDate || '-');
            $('#schedule_company').append(data.data.Company || '-');
            $('#schedule_type').append(data.data.Jenis || '-');
            $('#schedule_time').append(data.data.Waktu || '-');
            $('#lot_total').append(data.data.LotTotal || '-');
            $('#start-price').val(data.data.StartPrice);
            $('#interval').val(data.data.Interval);
            $('#unit_name').val(name);
            $('#unit_grade').val(data.data.Grade);
            $('#stock_id').val(data.data.AuctionItemId);
            $('#schedule_id').val(data.data.ScheduleId);
            $('#model').val(data.data.Model);
            $('#merk').val(data.data.Merk);
            $('#tipe').val(data.data.Tipe);
            $('#silinder').val(data.data.Silinder);
            $('#tahun').val(data.data.Tahun);
            $('#nopol').val(data.data.NoPolisi);
            $('#va').val(data.data.VA);
            $('#floor-bid').append("+"+addPeriod(data.data.Interval));
            $('#harga_kelipatan').append("Harga Kelipatan: Rp. "+addPeriod(data.data.Interval));
            $('#date').val(data.data.Date);
            firstImage = "url("+data.data.Image[Object.keys(data.data.Image)[0]]+")";
            $('.card-img-top').css("background-image",firstImage );

            activeCompany.child('liveOn').set(data.data.ScheduleId+"|"+data.data.NoLot);
            onLot = activeCompany.child('schedule/'+data.data.ScheduleId+'/lot|stock/'+data.data.NoLot);
            onLog = onLot.child('log');
            onMode = onLot.child('allowBid');
            
            value = data.data;
            value.Image = data.data.Image[Object.keys(data.data.Image)[0]];
            onStock.set(value);

            // pause();
            $('#bid-log').empty();
            onLog.on("child_added", function(snap) {
              var state;
              $('#bid-log').prepend(logHtmlFromObject(snap.val()));
              $('#start-price').val(snap.val().bid);
              $('#state').val(snap.val().type);
              $('#npl').val(snap.val().npl ? snap.val().npl : '');
              
              if (snap.val().type == 'Online') {
                state = 'Online Bidder';
              } else if(snap.val().type == 'Floor') {
                state = 'Floor Bidder';
              } else {
                state = 'Proxy Bidder';
              }

              $('#top_bid').html('Rp. '+addPeriod(snap.val().bid));
              $('#top_bid_state').html(state);
            });
            onLog.once("value").then(function(snapshot) {
              lastNumLog = snapshot.numChildren();
              console.log(lastNumLog > 0);
              if (lastNumLog > 0) {
                confirm_start();
              }
            });
            // reset_count();
          } else {
            activeCompany.child('liveOn').set(null);
            liveCount.set(null);
            onStock.set(null);
            if (onMode != undefined) {
              onMode.set(false);
            }
            $.ajax({
              type: "POST",
              url: "<?php echo $this->config->item('ibid_schedule');?>/api/updateStatus/"+data.schedule_id, // Used for Staging
              // url: "http://ibid-kpl.dev/api/submitWinner", //Used on local
              data : {},
              dataType: "json",
              success: function(data){
                if (data.status) {
                  $('#modal').modal('hide');
                  getLotData();
                } 
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  alert('Error get data from ajax');
              },
            });
          }
        } else {
          activeCompany.child('liveOn').set(null);
          liveCount.set(null);
          onStock.set(null); 
          if (onMode != undefined) {
            onMode.set(false);
          }
          $('#modal').modal({
              backdrop: 'static',
              keyboard: false
            })
            var body ='<h5>Semua jadwal hari ini telah selesai dilaksanakan.</h5>'
            $('#modal-title').html('<i class="fa fa-send-o"></i> Pesan');
            $('#modal-body').empty();
            $('#modal-body').append(body);
            $('#proceed-winner').hide();
            $('.card-img-top').css("background-image","url(<?php echo base_url('assets/img/default.png')?>)");
            $('#close').show();

            $('#item_name').text("Tidak ada data");
            $('#item_lot').text("-");
            $('#item_color').text("-");
            $('#item_transmisi').text("-");
            $('#item_km').text("-");
            $('#item_tahun').text("-");
            $('#item_nopol').text("-");
            $('#item_bahanbakar').text("-");
            $('#item_exterior').text("-");
            $('#item_interior').text("-");
            $('#item_mechanical').text("-");
            $('#item_frame').text("-");
            $('#item_grade').text("-");
            $('#item_startprice').text("Rp. "+'-');
            $('#harga_kelipatan').text("Harga Kelipatan: Rp. -");
            $('#schedule_date').text('-');
            $('#schedule_company').text('-');
            $('#schedule_type').text('-');
            $('#schedule_time').text('-');
            $('#floor-bid').text("+");
            $('#lot_total').text('-');
            $('#top_bid').text('-');
            $('#top_bid_state').text('');
            $('#bid-log').empty();

            clearInterval(start);
            $('#btn_count').prop("disabled", true);
            $('#floor-bid').prop("disabled", true);
            $('#modal').modal('show');
        }

        if (data.disable) {
            $('#btn_next').text("Next Schedule");
            $('#btn_next').attr("data-button",'schedule');
            $('#skip').prop("disabled", true);
            $('#btn_skip').prop("disabled", true);
        }else{
            $('#btn_next').text("Next Lot");
            $('#btn_next').attr("data-button",'lot');
            $('#skip').prop("disabled", false);
            $('#btn_skip').prop("disabled", false);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
          $('#loader').empty();
          $('#content').show();
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
    var Model = $('#model').val();
    var Merk = $('#merk').val();
    var Tipe = $('#tipe').val();
    var Silinder = $('#silinder').val();
    var Tahun = $('#tahun').val();
    var NoPolisi = $('#nopol').val();
    $.ajax({
      type: "POST",
      url: "<?php echo $this->config->item('ibid_kpl');?>/api/submitWinner", // Used for Staging
      // url: "http://localhost/ibid-kpl/api/submitWinner", //Used on local
      data : {UnitName:UnitName,Npl:npl,Lot:Lot,ScheduleId:ScheduleId,Schedule:Schedule,Type:Type,AuctionItemId:AuctionItemId,Price:Price,Va:Va,Model:Model,Merk:Merk,Tipe:Tipe,Silinder:Silinder,Tahun:Tahun,NoPolisi:NoPolisi},
      dataType: "json",
      success: function(data){
        if (data.status) {
          $('#modal').modal('hide');
          $('#next_lot').val('');
          // getLotData();
        } 
      },
      error: function (jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
      },
    });
  }

  function skipLot(){
    var description = '<div class="form-group"><label for="textarea">Berikan alasan : </label><textarea class="form-control" id="reason" rows="6"></textarea></div>'
    var loader = '<i class="fa fa-spinner fa-pulse fa-1x fa-fw" id="btn_loader"></i>';
    $('#confirm-skip').prepend(loader);
    $('#confirm-skip').prop("disabled",true);
    var Reason = $('#reason').val();
    if (Reason == '') {
      $('#reason').addClass('is-invalid');
      $('<div class="invalid-feedback">Wajib isi alasan skip lot.</div>').insertAfter('#reason');
      $('#btn_loader').remove();
      $('#confirm-skip').prop("disabled",false);
    } else {
        var SkipRange = $('#skip').val() - 1;
        var ScheduleId = $('#schedule_id').val();
        var Va = $('#va').val();
        var Lot = skipLotNo;
        Lot = parseInt(Lot);
        Lot = Lot + 1;
        $.ajax({
          type: "POST",
          url: "<?php echo base_url('auction/');?>skip",
          data : {Lot:Lot,ScheduleId:ScheduleId,SkipRange:SkipRange,Reason:Reason},
          dataType: "json",
          success: function(data){
          if (data.status) {
            if (Lot < SkipRange) {
              skipLotNo = Lot;
              Lot = Lot+1;
              $('#modal-auction-title').text('Konfirmasi');
              $('#modal-auction-title').css("padding-left",'');
              $('#modal-auction-body').empty();
              $('#modal-auction-body').append(description);
              $('#modal-auction-body').prepend('Apakah anda yakin akan melewati lot '+Lot+' ?');
              $('#btn_loader').remove();
              $('#confirm-skip').prop("disabled",false);
            }  else {
              $('#auction_modal').modal('hide');
              skipLotNo = 0;
            }
          }
        },
          error: function (jqXHR, textStatus, errorThrown) {
            alert('Error get data from ajax');
          },
        });
}
  }

  function checkLot(){
    $('#another-modal-header').removeClass('background-danger');
      var description = '<div class="form-group"><label for="textarea">Berikan alasan : </label><textarea class="form-control" id="reason" rows="6"></textarea></div>'
      var SkipRange = $('#skip').val();
      var CurrentLot = $('#lot_id').val()
        var ScheduleId = $('#schedule_id').val();
        var Va = $('#va').val();
        var Lot = $('#lot_id').val();
        skipLotNo = Lot;
        Lot = parseInt(Lot);
        Lot = Lot + 1;
        if (SkipRange == CurrentLot) {
          $('#another-modal-header').addClass('background-danger');
          $('#another-modal-title').html('<i class="fa fa-warning new-danger" style="margin-right: 5px;"></i>Perhatian');
          $('#another-modal-title').css('color','#f7f7f7');
          $('#another-modal-body').html('Mohon maaf anda tidak dapat melakukan skip pada lot yang sedang berjalan, silahkan klik tombol Next untuk melewati lot ini.');
          $('#modal-no').hide();
          $('#submit-logout').hide();
          $('#modal-close').show();
          $('#another-modal').modal('show');
        } else {
          $.ajax({
              type: "POST",
              url: "<?php echo base_url('auction/');?>checkLot",
              data : {Lot:Lot,ScheduleId:ScheduleId,SkipRange:SkipRange},
              dataType: "json",
              success: function(data){
                if (data.status) {
                  $('#modal-auction-title').text('Konfirmasi');
                  $('#modal-auction-title').css("padding-left",'');
                  $('#modal-auction-body').empty();
                  $('#modal-auction-body').append(description);
                  $('#modal-auction-body').prepend('Apakah anda yakin akan melewati lot '+Lot+' ?');
                  $('#confirm-start').hide();
                  $('#confirm-next').hide();
                  $('#confirm-skip').show();
                  $('#auction_modal').modal('show');
                }  else {
                  $('#skip').addClass('is-invalid');
                  $('<div class="invalid-feedback">Total lot hanya ada '+data.data.total+'.</div>').insertAfter('#skip');
                }
              }
          });
        }
  }

  function getBidLog(){
    var price = $('#start-price').val();
    var interval = $('#interval').val();
    var last = onLog.orderByKey().limitToLast(1);
    var newbid;
    last.once('value', function(snapshot) {
      if (!snapshot.val()) {
        newbid = parseInt(price) + parseInt(interval);
      } else{
        snapshot.forEach(function(child) {
          newbid = child.val().bid + parseInt(interval);
        });
      }

      onMode.once('value', function(modeSnapshot) {
        if (modeSnapshot.exists() && modeSnapshot.val()) {
          onLog.push({
            bid: newbid,
            type: 'Online',
            npl: Math.floor(Math.random() * 1000) + 1
          });
        }
      });
    });
  }

  function logHtmlFromObject(log){
    var html = '<div class="col-xs-4 col-md-4">'+addPeriod(log.bid)+'</div>'
                +'<div class="col-xs-4 col-md-4 weight">'+log.type+' Bid</div>'
                +'<div class="col-xs-4 col-md-4 weight">'+(log.npl ? log.npl : '....')  + '</div>'
    return html;
  }

  function floorBid(){
    var count_value = $('#count_value').val();
    var price = $('#start-price').val();
    var interval = $('#interval').val();
    var last = onLog.orderByKey().limitToLast(1);
    var newbid;
      clearInterval(start);
      start = setInterval( getBidLog, 4000 );
      onMode.set(true);
      if ((count_value == 2) || (count_value == 3)) {
        reset_count();
      }
    
    last.once('value', function(snapshot) {
      if (!snapshot.val()) {
        newbid = parseInt(price) + parseInt(interval);
      } else{
        snapshot.forEach(function(child) {
          newbid = child.val().bid + parseInt(interval);
        });
      }
      onLog.push({
        bid: newbid,
        type: 'Floor'
      });
    });
  }

function nextLot() {
  var ScheduleId = $('#schedule_id').val();
  var Lot = $('#lot_id').val();
  var postData = new FormData();
  $.ajax({
    url: "<?php echo $this->config->item('ibid_lot');?>/api/lotUnSold",
    type: "POST",
    data : {no_lot:Lot,schedule_id:ScheduleId},
    dataType: "json",
    success: function(data){
      if (data.status) {
        getLotData();
        confirm_start();
        // $('#auction_modal').modal('hide');
      } 
    },
  });
}

function addPeriod(nStr){
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

function btn_count() {
  var count_value = $('#count_value').val();
  var count = $('#count').val();
  if (count_value < 3) {
    count_value = parseInt(count_value) + 1;
    liveCount.set(count_value);
    if (count_value == 2) {
      clearInterval(start);
      onMode.set(false);
      // clearInterval(startProxy);
      start = null;
      // startProxy = null;
    }
    if (count_value == 3) {
      onMode.set(false);
      var last = onLog.orderByKey().limitToLast(1);

      last.once('value', function(snapshot) {
        snapshot.forEach(function(child) {
          winner_npl = onLog.orderByChild("bid").startAt(child.val().bid).endAt(child.val().bid).limitToFirst(1);
          winner_npl.once('value', function(winnerSnapshot) {
            winnerSnapshot.forEach(function(winnerData) {
              npl = winnerData.val().npl || '';
              state = winnerData.val().type || 'Floor';
              set_winner(npl,state);
            });
          });
        });
      });

      var winner = $('#npl').val();
      var state = $('#state').val();
      $('#modal').modal({
        backdrop: 'static',
        keyboard: false
      })
      $('#modal-body').empty();
      var lot = $('#lot_id').val();
      var name = $('#unit_name').val();
      var grade = $('#unit_grade').val();
      var price = $('#start-price').val();
      if (state == "Floor") {
        $('#modal-title').text('Selamat, Pemenang '+state+' Bidder');
        var body ='<h4>Detail Unit</h4>'
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
                    +'<h4>Isi Npl Pemenang</h4>'
                    +'<div class="form-group noLot-edit">'
                      +'<div class="col-md-6" style="padding-left:0">'
                        +'<input type="text" name="input_npl" class="form-control" id="input_npl" onkeypress="return isNumberKey(event)">'
                      +'</div>'
                    +'</div>';
          $('#modal-body').append(body);
          $('#close').hide();
          $('#modal').modal('show');
      } else {
          $('#modal-title').text('Selamat, Pemenang '+state);
          $('#modal-body').empty();
          var body ='<h4>Detail Unit</h4>'
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
                    +'<h4>Detail Pemenang</h4>'
                      +'<div class="row">'
                        +'<div class="col-md-12">'
                        +'<div class="card>'
                          +'<div class="card-body">'
                            +'<div class="row">'
                              +'<div class="col-md-3"><b class="pull-left">Peserta</b></div>'
                              +'<div class="col-md-9" id="show_date"> : '+state+'</div>'
                              +'<div class="col-md-3"><b class="pull-left">Npl</b></div>'
                              +'<div class="col-md-9" id="show_company"> : '+winner+'</div>'
                            +'</div>'
                          +'</div>'
                        +'</div>'
                        +'</div>'
                      +'</div>';
                      +'<hr class="custom">'
          $('#modal-body').append(body);
          $('#close').hide();
          $('#modal').modal('show');
      }
    }
    // alert('ok');
  } else {
    alert('Item already sold');
  }
}

function confirm_start(){
  $('#auction_modal').modal('hide');
  $('#floor-bid').prop("disabled", false);
  $('#start').prop("disabled",true); 
  $('#btn_count').prop("disabled",false);
  start = setInterval( getBidLog, 4000 );
  if (onMode != undefined) {
    liveCount.once('value', function(countSnap) {
      if (countSnap.exists() && ( (countSnap.val() == 2) || (countSnap.val() == 3))) {
        onMode.set(false);
      } else {
        onMode.set(true);
      }
    });
  }
  // startProxy = setInterval( getProxyBid, 6000);
  $('#auction_start').val(1);
}

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}  

function reset_count(){
  liveCount.set(null);
}

function set_winner(npl,state){
  $('#npl').val(npl);
  $('#state').val(state);
}

function pause(){
  $('#floor-bid').prop("disabled", true);
  $('#start').prop("disabled",false); 
  $('#btn_count').prop("disabled",true);
  clearInterval(start);
  // startProxy = setInterval( getProxyBid, 6000);
  $('#auction_start').val(0)
}

</script>
<?php $this->load->view($content_modal); ?>
  </body>
</html>