var Queue = require('firebase-queue');
var admin = require('firebase-admin');
var http = require('http');
var https = require("https");
var querystring = require('querystring');
var url = require('url'); // url parser
var path = require('path');
var serviceAccount = require(path.join(__dirname,'ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json'));

var companyId = process.argv[2];
var scheduleId = process.argv[3];
var lotNum = process.argv[4];
var Interval = process.argv[5];
var startPrice = process.argv[6];
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: 'https://ibid-ams-sample.firebaseio.com'
});
var mainRef = admin.database().ref('company/'+companyId+'/schedule/'+scheduleId+'/lot|stock/'+lotNum);
var allowBidRef = mainRef.child('allowBid');
var lotRef = mainRef.child('lotData');
var durationRef = lotRef.child('duration');
var logsRef = mainRef.child('log');
var tasksRef = mainRef.child('tasks');
var specsRef = mainRef.child('specs');
var queue = new Queue({ tasksRef: tasksRef, specsRef: specsRef }, function(data, progress, resolve, reject) {
  var last = logsRef.orderByKey().limitToLast(1);
  last.once('value', function(snapshot) {
    if (!snapshot.exists()) {
      newbid = parseInt(startPrice);
    } else{
      snapshot.forEach(function(child) {
        newbid = child.val().bid + parseInt(Interval);
      });
    }

    logsRef.push({
      bid: newbid || null,
      type: data.type || null,
      npl: data.npl || null
    });

    sameBid = tasksRef.orderByChild("bid").startAt(newbid).endAt(newbid);
    sameBid.once('value', function(snapshot) {
      let removeTasks = {};
      snapshot.forEach(child => removeTasks[child.key] = null);
      tasksRef.update(removeTasks).then(resolve());
    });
  });
});


(function durationReduction(){
  durationRef.once('value',function(durSnap) {
    if (durSnap.val() > 0) {
    // if (false) {
      durationRef.set((durSnap.val()-1)).then(setTimeout(durationReduction, 1000));
    }else{
      allowBidRef.set(false);
      var last = logsRef.orderByKey().limitToLast(1);
      last.once('value', function(snapshot) {
        if (snapshot.exists()) {
          lotRef.child('LotStatus').set('terjual');
          snapshot.forEach(function(child) {
            winner_npl = logsRef.orderByChild("bid").startAt(child.val().bid).endAt(child.val().bid).limitToFirst(1);
            lotRef.once('value', function(currentStockSnap){
              dataLot = currentStockSnap.val();
              winner_npl.once('value', function(winnerSnapshot) {
                winnerSnapshot.forEach(function(winnerData) {
                  formedPrice = winnerData.val().bid || 0;
                  npl = winnerData.val().npl || '';
                  state = winnerData.val().type || 'Floor';
                  var getProxyUrl = 'http://ibid-ams-autobid.development.net/api/getproxy/'+companyId+"/"+scheduleId+"/"+lotNum+"/"+startPrice;
                  http.get(getProxyUrl, function(res){
                      var body = '';

                      res.on('data', function(chunk){
                          body += chunk;
                      });

                      res.on('end', function(){
                          var data = JSON.parse(body);
                          if (data.status) {
                            topProxy = data.data.top_autobidder;
                            topProxy2nd = data.data.top_autobidder2nd;
                            Interval = parseInt(Interval); 
                            if (winnerData.val().bid < topProxy.nominal) {
                              if (winnerData.val().type != "Proxy") {
                                logsRef.push({
                                  bid: winnerData.val().bid + Interval,
                                  type: "Proxy",
                                  npl: topProxy.npl || null
                                });
                                formedPrice = winnerData.val().bid + Interval;
                              }
                              npl = topProxy.npl || '';
                              state = "Proxy";
                            }
                            if ( !isEmptyObject(topProxy2nd) &&  (winnerData.val().bid <= parseInt(topProxy2nd.nominal)) ) {
                              logsRef.push({
                                bid: parseInt(topProxy2nd.nominal) + Interval,
                                type: "Proxy",
                                npl: topProxy.npl || null
                              });
                              formedPrice = parseInt(topProxy2nd.nominal) + Interval;
                              npl = topProxy.npl || '';
                              state = "Proxy";
                            }
                          }
                          UnitName = dataLot.merk +' '+dataLot.lot
                          winPostData = {UnitName:UnitName,Npl:npl,Lot:dataLot.lot,ScheduleId:scheduleId,Schedule:dataLot.date,Type:1,AuctionItemId:dataLot.stock_id,Price:formedPrice,Va:dataLot.VA,Model:dataLot.model,Merk:dataLot.merk,Tipe:dataLot.tipe,Silinder:dataLot.silinder,Tahun:dataLot.tahun,NoPolisi:dataLot.nopol,winnerState:state};
                          submitWinnerUrl = 'http://ibid-ams-kpl.development.net/api/submitWinner';
                          httpPost(winPostData,submitWinnerUrl);
                      });
                  }).on('error', function(e){
                        console.log("Got an error: ", e);
                  });
                });
              });
            });
          });
        }else{
          lotRef.child('LotStatus').set('tidak terjual');
          lotUnsoldData = {no_lot:lotNum,schedule_id:scheduleId};
          lotUnsoldUrl = 'http://ibid-ams-lot.development.net/api/lotUnSold';
          httpPost(lotUnsoldData,lotUnsoldUrl);
        }
      });

      // queue.shutdown().then(process.exit()); // shutdown queue and end process
    }
  });
})();

function isEmptyObject(obj) {
  return !Object.keys(obj).length > 0;
}

function httpPost(data,postUrl){
  const postData = querystring.stringify(data);

  const url_callback = url.parse(postUrl);
  const options = {
    hostname: url_callback.hostname,
    port: url_callback.port,
    method: 'POST',
    path: url_callback.path,
    headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Content-Length': Buffer.byteLength(postData)
    }
  };

  const req = http.request(options, (res) => {
    console.log(`STATUS: ${res.statusCode}`);
    console.log(`HEADERS: ${JSON.stringify(res.headers)}`);
    res.setEncoding('utf8');
    res.on('data', (chunk) => {
      console.log(`BODY: ${chunk}`);
    });
    res.on('end', () => {
      mainRef.once('value',function(mainSnap){
        mainData = mainSnap.val();
        if (!mainData.allowBid && mainData.lotData.LotStatus != 'tersedia') {
          queue.shutdown().then(process.exit(0))
        }
      });
    });
  });

  req.on('error', (e) => {
    console.error(`problem with request: ${e.message}`);
  });

  req.write(postData);
  req.end();
} 