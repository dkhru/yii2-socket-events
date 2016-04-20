yii2-socket-event
=====================
Best way to send data to client other websocket on yii2 application

Author
------
Dmitriy Khristianov (dkh)

Require
--------

yii2, npm, node, redis


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dkhru/yii2-socket-events "*"
```

or add

```
"dkhru/yii2-socket-events": "*"
```

to the require section of your `composer.json` file.


Configure and run rtserver
--------------------------

Once the extension is installed, `cd vendor/dkhru/nodejs` and install node modules `npm install console-stamp express redis socket.io`
Generate SSL certificates for rtserver.
Copy `config.js.example` to `config.js`

For testing simple run in console `node rtserver.js`
On linux server use init script example in `rtserver.initd`

Usage
-----

In yii2 configure SocketEvent component
```
...
'components'=>[
...
'se'=>[
            'class'=>\dkhru\socketEvents\SE::className(),
            'socketUrl'=>https://127.0.0.1:8089,
         ],
...
]
...
```

Now, you can create you server driven widgets from `RegisterSEWidget`
 
Simple example:

```
...
class RestWidget extends RegisterSEWidget
{
      public $rest;
      public function init()
      {
         if( \Yii::$app->user->isGuest )
            throw new ForbiddenHttpException();
         $this->object='user';
         $this->id=\Yii::$app->user->id;
         $restJs=<<<JS
function(data){
    if(data['rest']){
        el = $('div.rest);
        if(el.length)
            el.html(data.rest);
    }
}
JS;
         $this->handlers=[  
            'rest'=>$restJs // добавляем обработчик
         ];
         parent::init();
      }

      public function run()
      {
         parent::run();
         return Html::tag('div',$this->rest.'&#8381;',['class'=>'rest']);
      }

   }   
```

After add widget to view you can update user rest in client browser from server.
```
SE::emit('user',$user_id,['handler'=>'rest','data'=>['rest'=>200.00]);
```
