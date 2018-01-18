var Queue = require('firebase-queue');
var admin = require('firebase-admin');
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