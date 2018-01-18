var runner	= require("child_process");
var path 	= require('path');
var admin 	= require('firebase-admin');

var companyId = process.argv[2];
var scheduleId = process.argv[3];
var lotNum = process.argv[4];
var Interval = process.argv[5];
var startPrice = process.argv[6];

var phpScriptPath = path.join(__dirname, '../ibid-autobid/index.php proxy getProxy '+companyId+' '+scheduleId+' '+lotNum+' '+Interval);
var serviceAccount = require(path.join(__dirname,'ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json'));

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: 'https://ibid-ams-sample.firebaseio.com'
});

var mainRef = admin.database().ref('company/'+companyId+'/schedule/'+scheduleId+'/lot|stock/'+lotNum);
var logsRef = mainRef.child('log');
var tasksRef = mainRef.child('tasks');
var modeBid = mainRef.child('allowBid');

runner.exec("php " + phpScriptPath, function(err, phpResponse, stderr) {
	if(err) console.log(err); /* log error */
	
	value = JSON.parse(phpResponse);
	topBidder = value.top_autobidder;
	if (Object.size(topBidder) > 0) {
		var proxyInterval =	setInterval(function(){
		  	var last = logsRef.orderByKey().limitToLast(1);
			tasksRef.once('value', function(taskSnapshot) {
			    modeBid.once('value', function(modeSnapshot) {
				  	if (!taskSnapshot.exists() && modeSnapshot.exists() && modeSnapshot.val()) {
						last.once('value', function(snapshot) {
							if (!snapshot.exists()) {
							  newbid = parseInt(startPrice);
							} else {
							  snapshot.forEach(function(child) {
							    newbid = child.val().bid + parseInt(Interval);
							    if (child.val().bid >= parseInt(topBidder.nominal) || newbid >= parseInt(topBidder.nominal)) {
									clearInterval(proxyInterval);			
    								process.exit();
							    }
							  });
							}

							newKey = tasksRef.push({
							  bid: newbid || null,
							  type: "Proxy",
							  npl: topBidder.npl || null
							}).key;

							sameBid = tasksRef.orderByChild("bid").startAt(newbid).endAt(newbid);
							sameBid.once('value', function(snapshot) {
							  let removeTasks = {};
							  snapshot.forEach(child => newKey != child.key ? removeTasks[child.key] = null : console.log('skip remove task'));
							  tasksRef.update(removeTasks);
							});

							tasksRef.push({
							  bid: newbid || null,
							  type: "Proxy",
							  npl: topBidder.npl || null
							});
						});
				  	}
			  	});
			});
		}, 3000);
	}else{
    	process.exit();
	}
});

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};