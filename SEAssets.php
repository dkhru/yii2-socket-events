<?php
   /**
    * Author: dkh
    * Date: 05.02.16
    * Time: 16:02
    */

   namespace dkhru\socketEvents;


   use yii\base\InvalidConfigException;
   use yii\web\AssetBundle;
   use yii\web\View;

   class SEAssets extends AssetBundle
   {
      public function init()
      {
         if ( !isset(\Yii::$app->components['se']) )
            throw new InvalidConfigException('Yii::$app->se is not configured');
         $this->jsOptions['position']=View::POS_HEAD;
         $this->sourcePath =\Yii::getAlias('@dkhru/socketEvents/js');
         parent::init();
      }

      public $js=[
         'socket.events.js',
      ];

      public $depends= [
         'yii\web\JqueryAsset',
         'dkhru\socketEvents\SocketIoAssets'
      ];
      public static function register($view)
      {

         $bundle = parent::register($view);
         /** @var SE $se */
         if(! \Yii::$app->user->isGuest ){
            $se=\Yii::$app->get('se');
            $conf=json_encode(
               [
                  'socketUrl'=>$se->socketUrl,
                  'globalQKey'=>$se->globalQKey,
               ]);
            $view->registerJs("se.init($conf);");
            $view->registerJs("se.registerHandler('global','alert',function(data){alert(data);});");
         }
         return $bundle;
      }

   }