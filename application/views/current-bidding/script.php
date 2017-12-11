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
var contactsRef = dbRef.ref('3/1/1/log');

// load older conatcts as well as any newly added one...
contactsRef.on("child_added", function(snap) {
  $('.bidding-log').prepend(contactHtmlFromObject(snap.val()));
  $('.bid-topbid').text('Rp. ' + addPeriod(snap.val().bid));
});

//save contact
// $('.addValue').on("click", function( event ) {  
// event.preventDefault();
//     if( $('#name').val() != '' || $('#email').val() != '' ){
//         contactsRef.push({
//         name: $('#name').val(),
//         email: $('#email').val(),
//         location: {
//             city: $('#city').val(),
//             state: $('#state').val(),
//             zip: $('#zip').val()
//         }
//         })
//         contactForm.reset();
//     } else {
//         alert('Please fill atlease name or email!');
//     }
// });

// prepare conatct object's HTML

function contactHtmlFromObject(log){
  // console.log(log);
  var html = '<div class="row line-height">'
                +'<div class="col-md-6">'+'Rp. ' + addPeriod(log.bid)+'</div>'
                +'<div class="col-md-6 weight">'+log.type+' Bidder</div>'
              +'</div>';
  return html;
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