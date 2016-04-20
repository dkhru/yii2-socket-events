<?php
   /**
    * Author: dkh
    * Date: 06.02.16
    * Time: 1:27
    */

   namespace dkhru\socketEvents;
   use yii\base\Event;


   /**
    * Class EventSE
    * Для того, чтобы браузер начал получать событие
    * при рендере страницы необходимо зарегистрировать объект
    * @package common\components\socketEvents
    */
   class EventSE extends Event
   {
      /**
       * Имя объекта используется для создания имени подписки
       * @var string
       */
      public $object='global';
      /**
       *
       * @var int
       */
      public $id;

      /**
       * Данные для обновления
       * [
       *   'selector'=><имя зарегистрированного jquery селектора>,
       *   'replace'=><заменяет контент элемента найденного по селектору>
       * ]
       * [
       *   'selector'=><имя зарегистрированного jquery селектора>,
       *   'append'=><добавляется в конец контента элемента найденного по селектору>
       * ]
       * [
       *   'selector'=><имя зарегистрированного jquery селектора>,
       *   'prepend'=><добавляется в начало контента элемента найденного по селектору>
       * ]
       * [
       *   'handler'=><имя зарегистрированного  js обработчика>,
       *   'data'=><строка json передаваемая в обработчик>
       * ]
       * @var array
       */
      public $data;

      public $success=false;
   }