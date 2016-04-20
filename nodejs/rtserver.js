/**
 * Author: dkh
 * Created by dkh on 07.02.16.
 * Server for client real-time SocketEvents
 */
require('console-stamp')(console, 'yyyy-mm-dd HH:MM:ss.l');
var config = require(__dirname + '/config.js');
var app = require('express')();
var privateKey = require('fs').readFileSync(config.key);
var certificate = require('fs').readFileSync(config.cert);
var credentials = {key: privateKey, cert: certificate};
var server = require('https').Server(credentials, app);
var redis = require('redis');
var io = require('socket.io')(server);
server.listen(config.port);
console.log("Server started on "+ config.port);
io.on('connection', function (socket) {
    var rc = redis.createClient({url:'redis://'+config.redisHost+':'+config.redisPort+'/'+config.redisDbNum});
    rc.on("error", function (err) {
        console.log("Redis Error: " + err);
    });
    // при получении сообщения - передаем клиенту
    rc.on('message', function (qkey, data) {
        io.sockets.connected[socket.id].emit(qkey, data);
        console.log('emit     qkey:' + qkey + ' socket:' + socket.id);
    });
    socket.on('init-sub', function (qkey) {
        console.log("init-sub qkey:" + qkey + ' socket:' + socket.id);
        rc.subscribe(qkey); // подписываемся
    });
    socket.on('end-sub', function (qkey) {
        console.log("end-sub qkey:" + qkey + ' socket:' + socket.id);
        rc.unsubscribe(qkey); // отписываемся
    });
    //При ошибке прибиваем соединение с клиентом
    socket.on('error', function (e) {
        console.log('Error: ' + e);
        socket.disconnect(true);
    });
    //При разрыве соединения закрываем соединение с редисом
    socket.on('disconnect', function () {
        console.log('disconnect: socket:' + socket.id);
        //При разрыве соединения закрываем соединение с редисом
        if (rc.connected) {
            console.log('close redis connection');
            rc.quit();
        }
    });
});