<?php
namespace dvizh\cart;

use dvizh\cart\models\CartElement;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use dvizh\cart\events\Cart as CartEvent;
use dvizh\cart\events\CartElement as CartElementEvent;
use dvizh\cart\events\CartGroupModels;
use yii;

class Cart extends Component
{
    const EVENT_CART_INIT = 'cart_init';
    const EVENT_CART_TRUNCATE = 'cart_truncate';
    const EVENT_CART_COST = 'cart_cost';
    const EVENT_CART_COUNT = 'cart_count';
    const EVENT_CART_PUT = 'cart_put';
    const EVENT_CART_UPDATE = 'cart_update';
    const EVENT_CART_ROUNDING = 'cart_rounding';
    const EVENT_MODELS_ROUNDING = 'cart_models_rounding';
    const EVENT_ELEMENT_COST = 'element_cost';
    const EVENT_ELEMENT_PRICE = 'element_price';
    const EVENT_ELEMENT_ROUNDING = 'element_rounding';
    const EVENT_ELEMENT_COST_CALCULATE = 'element_cost_calculate';
    const EVENT_ELEMENT_BEFORE_DELETE = 'element_before_delete';

    private $cost = 0;
    private $element = null;
    private $cart = null;

    public $currency = NULL;
    public $elementBehaviors = [];
    public $currencyPosition = 'after';
    public $priceFormat = [2, '.', ''];

    public function __construct(interfaces\Cart $Cart, interfaces\Element $Element, $config = [])
    {
        $this->cart = $Cart;
        $this->element = $Element;

        parent::__construct($config);
    }

    public function init()
    {
        $this->trigger(self::EVENT_CART_INIT, new CartEvent(['cart' => $this->cart]));
        $this->update();

        return $this;
    }

    public function put(\dvizh\cart\interfaces\CartElement $model, $count = 1, $options = [], $comment = null)
    {

        /**@var  CartElement $elementModel */
        if (!$elementModel = $this->cart->getElement($model, $options)) {
            $elementModel = new $this->element;
            $elementModel->setCount((int)$count);
            $elementModel->setPrice($model->getCartPrice());
            $elementModel->setItemId($model->getCartId());
            $elementModel->setModel(get_class($model));
            $elementModel->setOptions($options);
            $elementModel->setComment($comment);

            $elementModel->parent_id= $model->event_id;
            $elementModel->current_market_name= $model->market_name;
            $elementModel->result_type_name= $model->result_type_name;


//            yii::error($model->playerfullname);
            $elementModel->gamers_name=$model->playerfullname->event_name;

            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);  //в собитие не возможно передать родительский елемент Cart

            if(!$elementEvent->stop) {
                try {
                    $this->cart->put($elementModel);
                    // чистка дубликатов по группе parent_id
                    $parent_id=$elementModel->parent_id;
                    foreach (    $this->cart->elements as $el) {
                        if($elementModel->id != $el->id){
                            if($parent_id == $el->parent_id){
                                $el->delete();
                            }
                        }
                       // Yii::error([$elementModel->id,$el->id,$parent_id,$el->parent_id]);

                    }

                } catch (Exception $e) {
                    throw new \yii\base\Exception(current($e->getMessage()));
                }
            }
        } else {
            // + обновление полей
            $elementModel->setOptions($options);
            $elementModel->current_market_name= $model->market_name;
            $elementModel->result_type_name= $model->result_type_name;
            $elementModel->gamers_name=$model->playerfullname->event_name;
            $elementModel->countIncrement($count);



        }

        // TODO DRY
        $this->update();
        $elementEvent = new CartEvent([
            'cart' => $this->getElements(),
            'cost' => $this->getCost(),
            'count' => $this->getCount(),
        ]);
        $this->trigger(self::EVENT_CART_UPDATE, $elementEvent);

        return $elementModel;
    }

    public function putWithPrice(\dvizh\cart\interfaces\CartElement $model, $price = 0, $count = 1, $options = [], $comment = null)
    {
        if (!$elementModel = $this->cart->getElement($model, $options, $price)) {
            $elementModel = $this->element;
            $elementModel->setCount((int)$count);
            $elementModel->setPrice($price);
            $elementModel->setItemId($model->getCartId());
            $elementModel->setModel(get_class($model));
            $elementModel->setOptions($options);
            $elementModel->setComment($comment);

            $elementEvent = new CartElementEvent(['element' => $elementModel]);
            $this->trigger(self::EVENT_CART_PUT, $elementEvent);

            if(!$elementEvent->stop) {
                try {
                    $this->cart->put($elementModel);
                } catch (Exception $e) {
                    throw new \yii\base\Exception(current($e->getMessage()));
                }
            }
        } else {
            $elementModel->countIncrement($count);
        }

        // TODO DRY
        $this->update();
        $elementEvent = new CartEvent([
            'cart' => $this->getElements(),
            'cost' => $this->getCost(),
            'count' => $this->getCount(),
        ]);
        $this->trigger(self::EVENT_CART_UPDATE, $elementEvent);


        return $elementModel;
    }

    public function getElements()
    {
        return $this->cart->elements;
    }

    public function getHash()
    {
        $elements = $this->elements;

        return md5(implode('-', ArrayHelper::map($elements, 'id', 'id')).implode('-', ArrayHelper::map($elements, 'count', 'count')));
    }

    public function getCount()
    {
        $count = $this->cart->getCount();

        $cartEvent = new CartEvent(['cart' => $this->cart, 'count' => $count]);
        $this->trigger(self::EVENT_CART_COUNT, $cartEvent);
        $count = $cartEvent->count;

        return $count;
    }

    public function getCost($withTriggers = true)
    {
        $elements = $this->cart->elements;

        $pricesByModels = [];

        foreach($elements as $element) {
            $price = $element->getCost($withTriggers);

            if (!isset($pricesByModels[$element->model])) {
                $pricesByModels[$element->model] = 0;
            }

            $pricesByModels[$element->model] += $price;
        }

        $cost = 0;

        foreach($pricesByModels as $model => $price) {
            $cartGroupModels = new CartGroupModels(['cart' => $this->cart, 'cost' => $price, 'model' => $model]);
            $this->trigger(self::EVENT_MODELS_ROUNDING, $cartGroupModels);
            $cost += $cartGroupModels->cost;
        }

        $cartEvent = new CartEvent(['cart' => $this->cart, 'cost' => $cost]);

        if($withTriggers) {
            $this->trigger(self::EVENT_CART_COST, $cartEvent);
            $this->trigger(self::EVENT_CART_ROUNDING, $cartEvent);
        }

        $cost = $cartEvent->cost;

        $this->cost = $cost;

        return $this->cost;
    }

    public function getCostFormatted()
    {
        $price = number_format($this->getCost(), $this->priceFormat[0], $this->priceFormat[1], $this->priceFormat[2]);

        if ($this->currencyPosition == 'after') {
            return "<span>$price</span>{$this->currency}";
        } else {
            return "<span>{$this->currency}</span>$price";
        }
    }

    public function getElementsByModel(\dvizh\cart\interfaces\CartElement $model)
    {
        return $this->cart->getElementByModel($model);
    }

    public function getElementById($id)
    {
        return $this->cart->getElementById($id);
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function truncate()
    {
        $this->trigger(self::EVENT_CART_TRUNCATE, new CartEvent(['cart' => $this->cart]));
        $truncate = $this->cart->truncate();
        $this->update();

        return $truncate;
    }

    public function deleteElement($element)
    {
        $eventBeforeDelete = new CartElementEvent([
            'element' => $element,
        ]);
        $this->trigger(self::EVENT_ELEMENT_BEFORE_DELETE, $eventBeforeDelete);

        if ($element->delete()) {

            // TODO DRY
            $this->update();
            $elementEvent = new CartEvent([
                'cart' => $this->getElements(),
                'cost' => $this->getCost(),
                'count' => $this->getCount(),
            ]);
            $this->trigger(self::EVENT_CART_UPDATE, $elementEvent);

            return true;
        } else {
            return false;
        }
    }

    private function update()
    {
        $this->cart = $this->cart->my();
        $this->cost = $this->cart->getCost();

        return true;
    }

    /** @var Cart $cartModel */

//    public function getMarketName(){
////        yii::error($this->cart->element->current_market_name);
//        yii::error($this->cart->element);
//        return $this->element->cart_id;
//        return 'asdfg';
//        return $this->element->current_market_name;
//
//    }



}
