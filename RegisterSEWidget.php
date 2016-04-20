<?php
   /**
    * Author: dkh
    * Date: 05.02.16
    * Time: 16:31
    */

   namespace dkhru\socketEvents;


   use yii\base\InvalidConfigException;
   use yii\base\Widget;

   /**
    * Class RegisterSEWidget
    *
    * Подгружает неообходимые для соединения с rtserver библиотеки
    * Инициализирует se на клиенте
    * Регистрирует обработчики и селекторы SocketEvents
    * для дальнейшего использования
    *
    * Пример регистрации:
    *
    *     RegisterSEWidget::widget([
    *        'object'=>'global',
    *        'handlers'=>[
    *           'alert'=>'alert'
    *        ],
    *     ]);
    *
    *     RegisterSEWidget::widget([
    *        'object'=>'test',
    *        'id'=>1,
    *        'handlers'=>[
    *           'alert'=>'alert'
    *        ],
    *        'selectors'=>[
    *           'visa'=>'#visa'
    *        ]
    *     ]);
    *
    * Пример отправки:
    *
    *     SE::emit('global',[
    *       'handler'=>'alert',
    *        'data'=>'Тест alert'
    *     ]);
    *
    *     SE::emit('test',[
    *       'handler'=>'alert',
    *        'data'=>'Тест alert'
    *     ],1);
    *
    *     SE::emit('test',[
    *       'selector'=>'visa',
    *        'append'=>'Тест аppend'
    *     ],1);
    *
    * @package dkhru\socketEvents
    */
   class RegisterSEWidget extends Widget
   {
      public $object;         // используется для создания имени подписки
      public $id=null;        // используется для создания имени подписки если !=null

      /**
       * Регистрируемые для подписки селекторы
       * [
       *   'имя для вызова селектора'=><jquery селектор>,*
       * ]
       *
       * @var array
       */
      public $selectors=[];   // регистрирует селекторы
      /**
       * Регистрируемые для подписки обработчики
       * [
       *   'имя для обработчика'=><jquery селектор>,*
       * ]
       *
       * @var array
       */
      public $handlers=[];

      /**
       * @var SE
       */
      private $se;
      /**
       * @var string
       */
      private $qkey;

      public function init()
      {
         if ( !isset(\Yii::$app->components['se']) )
            throw new InvalidConfigException('Yii::$app->se is not configured');
         $this->se = \Yii::$app->se;
         if(empty($this->object))
            throw new InvalidConfigException('Object field can not be empty');
         $this->qkey = SE::genQKey($this->object,$this->id);
      }

      private function registerAssets(){
         SEAssets::register($this->getView());
      }

      private function registerSelector($s, $v)
      {
         $k=$this->qkey;
         \Yii::$app->redis->hset($k,$s,$v);
         $this->view->registerJs("se.registerSelector('$k','$s','$v');");
      }

      private function registerHandler($h, $v)
      {
         $k=$this->qkey;
         \Yii::$app->redis->hset($k,$h,$v);
         $jst = preg_match('/^function\(/',$v)
            ?'se.registerHandler(\'%s\',\'%s\',%s);'
            :'se.registerHandler(\'%s\',\'%s\',function(data){%s(data);});';
         $this->view->registerJs(sprintf($jst,$k,$h,$v));
      }

      public function run()
      {
         if(! \Yii::$app->user->isGuest ){
            $this->registerAssets();
            foreach($this->selectors as $s=>$v)
               $this->registerSelector($s,$v);
            foreach($this->handlers as $h=>$v){
               $this->registerHandler($h,$v);
            }
         }
         return parent::run();
      }

   }