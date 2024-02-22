<?php

namespace App\modules\Korzilla\Cart\Model;

class Order
{

    /**
     * idInOrder
     *
     * @var string
     */
    protected $idInOrder = '';

    /**
     * order
     *
     * @var array
     */
    public $order = [];

    /**
     * Item
     *
     * @var Class2001
     */
    protected $Item;

    /**
     * params
     *
     * @var array
     */
    protected $params = [];

    /**
     * setting
     *
     * @var array
     */
    protected $setting = [];

    protected $typeOrder = '';

    protected $error = '';

    public function __construct($typeOrder = 'buy_one_click')
    {
        global $setting;

        $this->typeOrder = $typeOrder;
        $this->order = $this->getOrderInSession($this->typeOrder);
        $this->setting = $setting;
    }

    public function setDelivery($params)
    {
        $deliveryInfo = \Class2005::getListName('delivery', $params['delivery']);

        if (!isset($deliveryInfo['name'])) {
            unset($this->order['delivery']);
            return;
        }

        if ($params['assistDelivery'] && $params['assistDelivery']['value']) {
            $type = $params['assistDelivery']['type'];
            $value = $params['assistDelivery']['value'];
            $deliveryInfo['sum'] = $this->order['delivery'][$type]['list'][$value]['price'];
            $this->order['delivery']['assist'] = ['type' => $type, 'key' => $value];
        }
        $this->order['delivery']['id'] = $params['delivery'];
        $this->order['delivery']['name'] = str_replace('"', '', $deliveryInfo['name'] . ($deliveryInfo['text'] ? ' - ' . $deliveryInfo['text'] : ''));
        $this->order['delivery']['sum'] = (float) ($deliveryInfo['sum'] > 0 ? $deliveryInfo['sum'] : "0");
        $this->order['delivery']['totsumfree'] = (float) $deliveryInfo['totsumfree'];
    }

    public function recalculationCount($Item, $params)
    {
        $this->Item = $Item;
        $this->params = $params;
        $this->setIdInOrder();

        $this->order['items'][$this->idInOrder]['count'] = (int) $this->getStock($this->params['count']);
        $this->order['items'][$this->idInOrder]['price'] = (float) $this->getPrice();
        $this->order['items'][$this->idInOrder]['sum'] = (float) $this->order['items'][$this->idInOrder]['price'] * $this->order['items'][$this->idInOrder]['count'];
    }

    public function addItemOrder($Item, $params)
    {
        $this->Item = $Item;
        $this->params = $params;
        $this->setIdInOrder();

        if (!$this->Item || !$this->Item->id) {
            $this->error = "Ошибка добавления в корзин";
            return;
        }

        if (!isset($this->params['name']) || !$this->params['name']) {
            $this->error = "Нет наименования товара";
            return;
        }

        if ($this->typeOrder == 'buy_one_click') $this->order = [];

        $this->order['items'][$this->idInOrder] = [
            'id' => (int) $this->Item->id,
            'name' => $this->params['name'],
            'sub' => $this->params['sub'],
            'art' => $this->Item->art,
            'count' => (int) $this->getStock($this->params['count']) ?: 1,
            'edizm' => $this->Item->edizm
        ];

        if (isset($this->params['colornum']) && is_numeric($this->params['colornum'])) {
            $this->order['items'][$this->idInOrder]['colorname'] = $this->params['colorname'];
            $this->order['items'][$this->idInOrder]['colorcode'] = $this->params['colorcode'];
            $this->order['items'][$this->idInOrder]['colornum'] = $this->params['colornum'];
        }

        $this->order['items'][$this->idInOrder]['price'] = (float) $this->getPrice();
        $this->order['items'][$this->idInOrder]['sum'] = (float) $this->order['items'][$this->idInOrder]['price'] * $this->order['items'][$this->idInOrder]['count'];
        return $this->order;
    }

    public function setTotalSum()
    {
        $this->order['totalsum'] = 0;
        $this->order['discont'] = 0; // Доп скидок нет, но остатки логики есть ???

        foreach ($this->order['items'] as $item) {
            $this->order['totalsum'] += $item['sum'];
        }

        $this->order['totaldelsum'] = $this->order['totalSumDiscont'] = $this->order['totalsum'] - $this->order['discont'];

        if (!isset($this->order['delivery']['sum']) || $this->order['delivery']['sum'] == 0) return;

        $this->order['delivery']['sum_result'] = $this->order['delivery']['sum'] ?: 0;
        $this->order['delivery']['sum_free'] = false;
        $this->order['delivery']['sum_freevisible'] = false;
        # добрал до нужной суммы
        if ($this->setting['freedelivery'] && $this->order['delivery']['totsumfree'] > 0) {
            if ($this->order['delivery']['totsumfree'] <= $this->order['totalSumDiscont']) {
                $this->order['delivery']['sum_result'] = 0;
                $this->order['delivery']['sum_free'] = true;
            }
            $this->order['delivery']['sum_nothave'] = max($this->order['delivery']['totsumfree'] - $this->order['totalSumDiscont'], 0);
            $this->order['delivery']['sum_freevisible'] = true;
        }

        $this->order['totaldelsum'] = $this->order['totalSumDiscont'] + $this->order['delivery']['sum_result'];
    }

    /**
     * setIdInOrder
     *
     * @return void
     */
    public function setIdInOrder()
    {
        $this->idInOrder = $this->Item->id;

        if (is_numeric($this->params['colornum'])) $this->idInOrder .= "_clr{$this->params['colornum']}";
    }

    public function setOrderInSession()
    {
        switch ($this->typeOrder) {
            case 'buy_one_click':
                $_SESSION['buy_one_click'] = $this->order;
                break;
            default: throw new \Exception("Invalid type order !!!", 404);
        }
    }

    public function getOrderInSession($type)
    {
        switch ($type) {
            case 'buy_one_click':
                return $_SESSION['buy_one_click'] ?: [];
            default:
                throw new \Exception("Invalid type order !!!", 404);
        }
    }

    public function getResult()
    {
        return ['order' => $this->order, 'error' => $this->error];
    }

    private function getPrice()
    {
        if (isset($this->params['colornum']) && is_numeric($this->params['colornum']) && $this->Item->colors != '') {
            $colors = orderArray($this->Item->colors);
            return $colors[$this->params['colornum']]['price'];
        }

        return $this->Item->getPriceByCountInBasket($this->order['items'][$this->idInOrder]['count']);
    }

    private function getStock(int $stock)
    {
        if (!isset($stock) || !is_numeric($stock)) {
            $this->error = "Нет количества товара";
            return;
        }

        if ($this->setting['stockbuy'] && $stock > $this->Item->stock) {
            $this->error = "В наличии имеется только {$this->Item->stock} единиц товара.";
            $stock = $this->Item->stock;
        }

        return $stock;
    }

    public function getFormBuyOneClick($order)
    {
        
        $params['data'] = $this->setting['oneClickForm'] ? orderArray($this->setting['oneClickForm']) : [];
        $params['order'] = $order;
        return k_renderTemplate(__DIR__ . '/../Vuew/oneClickForm.html', $params);
    }
}
