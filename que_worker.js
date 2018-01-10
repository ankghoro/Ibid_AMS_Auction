var Queue = require('firebase-queue');
var admin = require('firebase-admin');

var companyId = process.argv[2];
var scheduleId = process.argv[3];
var lotNum = process.argv[4];
var Interval = process.argv[5];
var startPrice = process.argv[6];
admin.initializeApp({
  credential: admin.credential.cert({
  	projectId: "ibid-ams-sample",
    clientEmail: "firebase-adminsdk-b6oyv@ibid-ams-sample.iam.gserviceaccount.com",
  	privateKey: "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDx4pZCCaV55w7Y\nEBO1nETukA1mhY7wNBJPK7FXXj7BddtdS+AiiSg6wfp7qZFLdQapeRvERaiSqoXp\niqcuoYq84ggWLzp8pKAEdcdUnSH9Ga7vSZuwq8W8vLbOgBrsC8HmL7pmkTQw/9KY\n9ODSpXDp3EHGay11GHbU4iehMNC53YcVimgApsSClE5tWS7t69TJxRJev/AJuBhJ\nKsHYav/0Vln2d4HKrNC+XjsJRrnpJzzn4xX3qDUIHWpb7AOEzF6FuqdM/8sThcfU\nTgSgioDV/RaUBP4SvXOJxY1qZ3vFbkDIi27KqeG2ij3K6O2OcC9E+wM/endvqL/x\n0AbhEWx3AgMBAAECggEAHJocBWwJlqwVS8Q+6GM1nXYIN57EY6/smFME0d1xk0PB\n8DrIIp1QVf5ZBaVr6l3Ir2KEP4WzJMCoPBDqqu9sLeCWbzUfd9yplU1uCBnaioiu\ncFomYFI3fBVeknWAeXf8ciEjq/wwjTi66N5P9WidvPGQD4vd4LJHXu3tHLjefZOz\nCVzh/HDXqfqrJU7Y8dQeSsxoFLkZNWac8veQmW7/Ks/1apI5HPGzTR1U7Qs7GLT+\n3rB4ivGj/BQPag22UINaLLaD8duOSFdtzBvrASA6foWZ21StXSGKF+YqC5vVmkgL\n0fK1C+a8XNEPeZ1EWF3H+Hx/t0bSSxWf++MR6wd/3QKBgQD85sOLejIou9VOhhX3\nRxESpTQxAQnY+raw0WAqi7z9HKV3+r22MahuKUo3ZUcdsMYuEnkd7fdFZUyCQYMK\nugKQ5tpV3arf5Ir0s53oUF/mO7i9+q/EheTSWucIzLEFyFMo9i/enPlBA8BdNnI7\ncFf9obmuqelboI+zxE14VQziywKBgQD02UUbemMYyMy84Js6qynWtaFAu2hL3TZH\n2fPdg6EXYTV941u5SoiY3SvdHDBdRncbGY15I5m/kFC3ifJM1zhhLt3q+6D1j0jY\n7Ik5sh9JIav6nk2vwOle4pWUdquJOjbr0fGfQU2U+C4LXsZX0EtaYHXuVW3ofj+2\nSbg2C2arhQKBgQCYeiVhvZ3qUz1LJ6qsuQtBG5u5A/BFAvwM5V++pxud25ykFug0\npgHv0TMu7QAQlZkXBApEEkpoa6fSTN9OI1ISvSzcYlZ4wNlKqdTF0VfQfydmW3OH\n4FZSwX3UH12Hp/0DkFLSPABHw5RCXuZGkfhrMgu6lDTfCpI5h1xR9a1cOwKBgQDm\nMisd1We7knWikgx/ERMp0OOFB2zb/mMdFFWJCkr2vybab1n6D4/zH+UwYWM7Hpe2\nO8TdglH3X0fz9tZ91c4k2Do9xUsj8w1LHL87JjLxv51/5zskpNsDoUV+Kj/FqZyf\nA2gGERBOoTIw8G7LeoKNuqjFZT4K2j8uM4rkDL3/PQKBgQCK7m9oqCdCFk1M6aV4\n1B1ZNlnXOSz7ouHlWBjNwg7nyrOkV1LStxZBS+cs5D+QvvJ7eYJjLnbW6vcGVrBW\nrIY4zfdeJnkIxO4OylJJ1OlhqqdW/I8y6UMT+EYt8QlyY39CNpgdDB7Qk+n9McUF\noXsTQcVLMPh1ZFFR5hnAhvRoaw==\n-----END PRIVATE KEY-----\n",
  }),
  databaseURL: 'https://ibid-ams-sample.firebaseio.com'
});
var mainRef = admin.database().ref('company/'+companyId+'/schedule/'+scheduleId+'/lot|stock/'+lotNum);
var logsRef = mainRef.child('log');
var tasksRef = mainRef.child('tasks');
var specsRef = mainRef.child('specs');
var queue = new Queue({ tasksRef: tasksRef, specsRef: specsRef }, function(data, progress, resolve, reject) {
  // Read and process task data

  var last = logsRef.orderByKey().limitToLast(1);
  // console.log(data.npl);
  last.once('value', function(snapshot) {
    if (!snapshot.exists()) {
      newbid = parseInt(startPrice) + parseInt(Interval);
    } else{
      snapshot.forEach(function(child) {
        newbid = child.val().bid + parseInt(Interval);
      });
    }

    // console.log(newbid);
    logsRef.push({
      bid: newbid || null,
      type: data.type || null,
      npl: data.npl || null
    }).then(resolve());
  }).then(resolve());

  // Finish the task asynchronously
  // setTimeout(function() {
  //   resolve();
  // }, 1000);
});