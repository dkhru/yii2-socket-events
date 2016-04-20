/**
 * Author: dkh
 * Created by dkh on 05.02.16.
 * Client for client real-time SocketEvents
 */

var se = (function ($) {
    var pub = {
        conf: {  // конфиг по умолчанию
            socketUrl:undefined,
            globalQKey:undefined
        },
        subs:undefined,
        socket: undefined,
        init: function (conf) {
            if (pub.subs === undefined) { // Не было инициализации
                pub.subs={};
                $.extend(pub.conf, conf);
                if (pub.initConnection()) {
                    window.onbeforeunload = function () { // закрываем соединение при переходе на другую страницу
                        console.log('disconnect on close page');
                        if (pub.socket.connected)
                            pub.socket.close();
                    }
                }
            } else {
                $.error('SocketEvents уже инициализирован');
            }
        },

        registerSelector:function(qkey, name, value){
            sub = pub.subscribe(qkey);
            if($.$isEmpty(sub.selectors[name])) {
                sub.selectors[name] = value;
            }
        },

        unregisterSelector:function(qkey, name){
            sub = pub.subs[qkey];
            delete sub.selectors[name];
            pub.unsubscribe(qkey);
        },

        registerHandler:function(qkey, name, value){
            sub = pub.subscribe(qkey);
            if(sub.handlers[name] && $.isFunction(sub.handlers[name])){
                console.log('Handler '+name+' for '+qkey+' alredy registered');
            }else if($.isFunction(value)) {
                sub.handlers[name] = value;
            }else{
                console.log('Can not register handler '+name+' for '+qkey);
            }
        },

        unregisterHandler:function(qkey, name){
            sub = pub.subs[qkey];
            delete sub.handlers[name];
            pub.unsubscribe(qkey);
        },

        initConnection: function () {
            if (pub.socket === undefined && pub.conf.socketUrl!==undefined) {
                pub.socket = io.connect(pub.conf.socketUrl);
                if(pub.conf.globalQKey!==undefined)
                    pub.subscribe(pub.conf.globalQKey);
                pub.socket.on('reconnect_error', function () {
                    console.log('Не удается соединиться с rtserver');
                });
                pub.socket.on('disconnect', function () {
                    console.log('Disconnected');
                });
                pub.socket.on('connect', function () {
                    console.log('Connected');
                    for(qkey in pub.subs)
                        pub.socket.emit('init-sub', qkey);

                });
                return true;
            }
            return false;
        },
        subscribe:function(qkey){
            if(pub.subs[qkey] === undefined) {
                pub.subs[qkey]={handlers:{},selectors:{}};
                pub.socket.on(qkey, function (data) {
                    pub.onData(data,qkey);
                });
            }
            return pub.subs[qkey];
        },
        unsubscribe:function(qkey){
            sub = pub.subs[qkey];
            if( sub && sub.handlers==={} && sub.selectors==={} ) {
                pub.socket.emit('end-sub',qkey);
                delete pub.subs[qkey];
            }
        },
        onData: function (data,qkey) {
            console.log(data);
            try{
                sub = pub.subs[qkey];
                pdata = $.parseJSON(data);
                if(pdata.selector){
                    if(sub.selectors[pdata.selector]) {
                        $el = $(sub.selectors[pdata.selector]);
                        if ($el) {
                            if (pdata.replace) {
                                $el.html(pdata.replace);
                            } else if (pdata.append) {
                                $el.append(pdata.append);
                            } else if (pdata.prepend) {
                                $el.prepend(pdata.prepend)
                            }
                        }
                    }else{
                        console.log('unregistered selector '+pdata.selector+' for '+qkey);
                    }
                }else if(pdata.handler){
                    if(sub.handlers[pdata.handler]) {
                        if (pdata.data) {
                            sub.handlers[pdata.handler](pdata.data);
                        } else {
                            sub.handlers[pdata.handler]();
                        }
                    }else{
                        console.log('unregistered handler '+pdata.handler+' for '+qkey);
                    }
                }
            }catch(err){
               console.log('onData error:'+err.name+', data:'+data+' for '+qkey);
            }
        }

    };
    return pub;
})(jQuery);
