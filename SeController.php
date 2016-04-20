<?php
   /**
    * Author: dkh
    * Date: 18.02.16
    * Time: 21:44
    * Project: megauniver
    */

   namespace dkhru\socketEvents;


   use yii\console\Controller;

   class SeController extends Controller
   {

      /**
       * Посылает сообщение всем авторизованным пользователям
       * @param $message
       */
      public function actionGlobal($message){
         SE::emit('global',null,['handler'=>'alert','data'=>$message]);
      }

   }