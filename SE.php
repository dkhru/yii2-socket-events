<?php
   /**
    * Author: dkh
    * Date: 05.02.16
    * Time: 0:40
    */

   namespace dkhru\socketEvents;


   use Yii;
   use yii\base\Component;
   use yii\base\InvalidParamException;

   /**
    * Class SocketEvents
    * @package dkhru\socketEvents
    */
   class SE extends  Component

   {
      const EVENT_SE_DATA = 'event_se_data';
      public $socketUrl;
      public $globalQKey = 'global';

      /**
       * Генерирует ключ подписки из события
       * @param EventSE $event
       *
       * @return string
       */
      public static function qKeyFromEvent($event)
      {
         return (isset($event->id))?$event->object.'_'.$event->id:$event->object;
      }

      /**
       * Генерирует ключ подписки
       * @param      $object
       * @param null $id
       *
       * @return string
       */
      public static function genQKey($object,$id=null)
      {
         return (isset($id))?$object.'_'.$id:$object;
      }

      /**
       * Обработчик события EVENT_SE_DATA
       * можно использовать при необходимости обрабатывать это событие в нескольких местах
       *
       * @param $event EventSE
       */
      public static function onSEData($event){
         if (!(isset($event->object)&&isset($event-> data)))
            throw new InvalidParamException('Event object or data not set');
         $qKey = self::qKeyFromEvent($event);
         $event->success = (Yii::$app->redis->publish($qKey,json_encode($event->data))=='1');
      }


      /**
       * Отправка данных браузеру клиента
       * Пример:
       *    SE::emit('global',[
       *       'handler'=>'alert',
       *        'data'=>'Тест alert'
       *    ]);
       *
       * @param      $object
       * @param null $id
       * @param      $data
       *
       * @return bool
       */
      public static function emit($object,$id=null,$data){
         $qKey = self::genQKey($object,$id);
         return (Yii::$app->redis->publish($qKey,json_encode($data))==='1');
      }

   }