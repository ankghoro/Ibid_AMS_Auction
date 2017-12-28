<script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<!-- Include Firebase Library -->
<script src="https://www.gstatic.com/firebasejs/4.8.0/firebase.js"></script>
<script>
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
var auctionLog = dbRef.ref('company/3/schedule/1/lot|stock/1321/log');
var activeCompany = dbRef.ref('company/<?php echo $CompanyId; ?>');
var liveCount = activeCompany.child('liveCount');
var currentStock = activeCompany.child('currentStock');
var onLog;
activeCompany.child('liveOn').on('value', function(snapshot) {
  if (snapshot.exists()) {
    $('.bidding-log').empty();
    $('.bid-topbid').text('Rp. -');
    $('.pull-right').text('-');
    var liveOn = snapshot.val();
    liveOn = liveOn.split('|');
    onLog = activeCompany.child('schedule/'+liveOn[0]+'/lot|stock/'+liveOn[1]+'/log');
    currentStock.once('value', function(stockSnapshot) {
      if (stockSnapshot.exists()) {
        val = stockSnapshot.val();
        var name = val.Merk+" "+val.Tipe;
        $('.main-title').text(name+" "+val.Silinder+" "+val.Model);
        $('.lot-number').text(val.NoLot);
        $('.separator1').find('h5').text(val.Tahun);
        $('#startprice').text(addPeriod(val.StartPrice));
        $('#policenumber').text(val.NoPolisi);
        $('#kilometers').text(val.Kilometer);
        $('#color').text(val.Warna);
        $('#transmission').text(addPeriod(val.Transmisi));
        $('#exterior').text(val.Exterior);
        $('#interior').text(val.Interior);
        $('#mechanical').text(val.Mesin);
        $('#frame').text(val.Rangka);
        $('.grade-alpha').text(val.Grade);
        $('.fold-price').text("Harga Kelipatan: Rp. "+addPeriod(val.Interval));
        firstImage = "url("+val.Image+")";
        $('.card-img-top').css("background-image",firstImage );
      }else{
        reset()
      }
    });
    onLog.on("child_added", function(snap) {
      $('.bidding-log').prepend(logHtmlFromObject(snap.val()));
      $('.bid-topbid').text('Rp. ' + addPeriod(snap.val().bid));
      $('.pull-right').text(snap.val().type + " Bidder");
    });
  }else{
    reset()
  }
});

liveCount.on("value", function(snapshot) {
  $('.bid-count').removeClass("default-wrap green-wrap yellow-wrap red-wrap");
  if (snapshot.exists()) {
    if (snapshot.val() == 1) {
      $('.bid-count').addClass("green-wrap");
    } else if(snapshot.val() == 2) {
      $('.bid-count').addClass("yellow-wrap");
    }else{
      $('.bid-count').addClass("red-wrap");
    }
    $('.bid-count').find('div').text(snapshot.val());
  }else{
    $('.bid-count').addClass("default-wrap");
    $('.bid-count').find('div').text('-');
    // $('#count').val("-");
    // $('#count_value').val(0);
  }
});
// prepare log object's HTML
function logHtmlFromObject(log){
  var html = '<div class="row line-height">'
                +'<div class="col-md-6">'+'Rp. ' + addPeriod(log.bid)+'</div>'
                +'<div class="col-md-6 weight">'+log.type+' Bidder</div>'
              +'</div>';
  return html;
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

function reset(){
  $('.main-title').text("-");
  $('.lot-number').text("-");
  $('.separator1').find('h5').text("-");
  $('#startprice').text("-");
  $('#policenumber').text("-");
  $('#kilometers').text("-");
  $('#color').text("-");
  $('#transmission').text("-");
  $('#exterior').text("-");
  $('#interior').text("-");
  $('#mechanical').text("-");
  $('#frame').text("-");
  $('.grade-alpha').text("-");
  $('.fold-price').text("Harga Kelipatan: Rp. -");
}

</script>