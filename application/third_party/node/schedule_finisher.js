var path = require('path');
var projectBasePath = path.join(__dirname,'../../../');

require('dotenv').config(projectBasePath+'.env'); // environment declaration

var Queue = require('firebase-queue');
var admin = require('firebase-admin');
var http = require('http');
var https = require("https");
var querystring = require('querystring');
var url = require('url'); // url parser

var serviceAccount = require(projectBasePath+process.env.FIREBASE_DATABASE_SERVICE_ACCOUNT);

var companyId = process.argv[2];
var scheduleId = process.argv[3];
admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: 'https://ibid-ams-sample.firebaseio.com'
});
var mainRef = admin.database().ref('company/'+companyId+'/schedule/'+scheduleId+'/lot|stock');
finisher();

function finisher() {
	let countUnAvailable = 0;
	let countAvailable = 0;
	let done = false;
	mainRef.once('value', function(mainSnap){
		if (mainSnap.exists()) {
			mainData = mainSnap.val();
			mainData.forEach(value => value.lotData.LotStatus == 'tersedia' ? countAvailable++ : countUnAvailable++);
			done = countAvailable > 0 ? false : true;
			if (done) {
				updateScheduleUrl = process.env.API_SCHEDULE+'api/updateStatus/'+scheduleId;
				httpPost({},updateScheduleUrl);
			}
		}
		setTimeout(finisher, 30000);
	});
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

  const protocol = url_callback.protocol == "https:" ? https : http;
  const req = protocol.request(options, (res) => {
    console.log(`STATUS: ${res.statusCode}`);
    console.log(`HEADERS: ${JSON.stringify(res.headers)}`);
    res.setEncoding('utf8');
    res.on('data', (chunk) => {
      console.log(`BODY: ${chunk}`);
    });
    res.on('end', () => {
    	process.exit();
    });
  });

  req.on('error', (e) => {
    console.error(`problem with request: ${e.message}`);
  });

  req.write(postData);
  req.end();
} 