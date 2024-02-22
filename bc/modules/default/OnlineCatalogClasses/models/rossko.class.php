<?php

class Rossko
{
    private $wsldSearch = 'http://api.rossko.ru/service/v2.1/GetSearch';
    private $wsldCheckoutDetails = 'http://api.rossko.ru/service/v2.1/GetCheckoutDetails';
    private $items = [];

    public function getSearchResult($find)
    {
        global $setting, $db;

        $deliveryParams = $this->getCheckoutDetails();

        $soap = new SoapClient($this->wsldSearch, ['connection_timeout' => 1, 'trace' => true]);
        
        $result = $soap->GetSearch([
            'KEY1' => $setting['rossko_key'],
            'KEY2' => $setting['rossko_key2'],
            'text' => $find,
            'delivery_id' => $deliveryParams['delivery'],
            'address_id' => $deliveryParams['addres'],
        ]);

        if ($result->SearchResult->success) {
            $this->sub = $setting['rossko_save_sub'];
            $this->cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = {$this->sub} AND Class_ID = 2001");
            $this->items = $this->setItems($result->SearchResult->PartsList->Part);
        }

        return $this->items;
    }

    private function getCheckoutDetails()
    {
        global $setting;

        $soap = new SoapClient($this->wsldCheckoutDetails, ['connection_timeout' => 1, 'trace' => true]);

        $result = $soap->GetCheckoutDetails([
            'KEY1' => $setting['rossko_key'],
            'KEY2' => $setting['rossko_key2']
        ]);

        $deliveryID = $result->CheckoutDetailsResult->DeliveryAddress->address->Delivery->ids->id[0];
        $addresID = $result->CheckoutDetailsResult->DeliveryAddress->address->id;
        return ['delivery' => $deliveryID, 'addres' => $addresID];
    }

    private function setItems($part, $params = [])
    {
        global $catalogue, $setting;

        $items = [];

        foreach ($part as $item) {
            $id = (string) $item->guid;
            $art = (string) $item->partnumber;
            $name = (string) $item->name;
            $keyword = encodestring(trim($name) . " " . trim($id) . " " . trim($art), 1);
            $stock = $price = 0;
            foreach ($item->stocks->stock as $sklad) {
                if ((int) $sklad->count > $stock && ((float) $sklad->price < $price || $price === 0)) {
                    $stock = (int) $sklad->count;
                    $price = (float) $sklad->price;
                }
            }
            if (isset($item->crosses->Part)) {
                $analogs = $this->setItems($item->crosses->Part, ['art2' => $art]);
                $analogField = array_reduce($analogs, function ($carry, $analog) {
                    $carry[] = $analog['art'];
                    return $carry;
                }, []);

                $items = $items + $analogs;
            }

            $items[$id] = [
                'Subdivision_ID' => $this->sub,
                'Sub_Class_ID' => $this->cc,
                'vendor' => (string) $item->brand,
                'name' => $name,
                'art' => $art,
                'art2' => ($params['art2'] ?: ''),
                'code' => $id,
                'price' => $price * (1 + $setting['rossko_markup'] / 100),
                'stock' => $stock,
                'Keyword' => $keyword,
                'analog' => implode("\r\n", $analogField),
                'Catalogue_ID' => $catalogue,
                'Checked' => 1,
            ];
        }

        return $items;
    }
}
