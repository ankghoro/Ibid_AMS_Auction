var path = require('path');
var projectBasePath = path.join(__dirname,'../../../');
var Queue = require('firebase-queue');
var admin = require('firebase-admin');

require('dotenv').config(projectBasePath+'.env'); // environment declaration

var serviceAccount = require(projectBasePath+process.env.FIREBASE_DATABASE_SERVICE_ACCOUNT);

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

    if (data.bid < newbid) {
      sameBid = tasksRef.orderByChild("bid").startAt(data.bid).endAt(data.bid);
      sameBid.once('value', function(snapshot) {
        let removeTasks = {};
        snapshot.forEach( child => child.val().type != 'Floor' ? removeTasks[child.key] = null : console.log('skip remove task') );
        tasksRef.update(removeTasks).then(resolve());
      });
    }else{
      logsRef.push({
        bid: newbid || null,
        type: data.type || null,
        npl: data.npl || null
      });
      sameBid = tasksRef.orderByChild("bid").startAt(newbid).endAt(newbid);
      sameBid.once('value', function(snapshot) {
        let removeTasks = {};
        snapshot.forEach( child => child.val().type != 'Floor' ? removeTasks[child.key] = null : console.log('skip remove task') );
        tasksRef.update(removeTasks).then(resolve());
      });
    }
  });
});