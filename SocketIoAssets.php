<?php
   /**
    * Author: dkh
    * Date: 04.02.16
    * Time: 23:00
    */

   namespace dkhru\socketEvents;


   use Yii;
   use yii\web\AssetBundle;

   class SocketIoAssets extends AssetBundle
   {
      public $js=[
         'https://cdn.socket.io/socket.io-1.4.5.js',
      ];

      public $depends= [
         'yii\web\JqueryAsset',
      ];

      public static function register($view)
      {

         $bundle = parent::register($view);
         /** @var SE $se */
         if(! \Yii::$app->user->isGuest ){
            $se=Yii::$app->get('se');
            $conf=json_encode(
               [
                  'socketUrl'=>$se->socketUrl,
                  'globalQKey'=>$se->globalQKey,
               ]);
            $view->registerJs("se.init($conf);");
         }
         return $bundle;
      }
   }