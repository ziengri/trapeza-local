<?php

class Parser
{
    public function __construct($path, $data)
    {
        $this->xml = new SimplexmlElement(file_get_contents($path));
        $this->data = $data;

        $this->main();
    }

    private function main()
    {
        if (@$this->xml->Группы->Группа) $this->grops = $this->xml->Группы->Группа;
        if (@$this->xml->Классификатор->Группы->Группа) $this->grops = $this->xml->Классификатор->Группы->Группа;

        if (@$this->xml->Каталог->Товары->Товар) $this->items = $this->xml->Каталог->Товары->Товар;

        if (@$this->xml->ПакетПредложений->Предложения->Предложение) $this->offersItems = $this->xml->ПакетПредложений->Предложения->Предложение;

        if (isset($this->grops)) $this->parsGroups($this->grops);
        if (isset($this->items))  $this->parsItems($this->items);
        if (isset($this->offersItems))  $this->parsOffersItems($this->offersItems);

    }

    private function parsOffersItems($items)
    {
        foreach ($items as $item) {
            $id = (string) $item->Ид;
            if ($item->Цены->Цена) {
                $this->parsPrice($id, $item->Цены->Цена);
            }
            if ($item->Склады->Склад) {

            } else {
                $this->data['items'][$id]['stock'] = ($item->Количество ? (float) $item->Количество : 0);
            }
        }
    }

    private function parsPrice($idItem, $data)
    {
        foreach ($data as $price) {
            $idPrice = $price->ИдТипаЦены;
            $val = str_replace(',', '.', preg_replace('/[^\d\.\,]/m', '', (string) $price->ЦенаЗаЕдиницу));
            $this->data['items'][$idItem]['price'] = $val;
        }
    }

    private function parsGroups($groups, $parentID = '') {

        foreach ($groups as $group) {
            $id = (string) $group->Ид;
            $this->data['groups'][$id] = ['name' => (string) $group->Наименование, 'ID' => $id, 'parentID' => $parentID];
            if ($group->Группы->Группа) $this->parsGroups($group->Группы->Группа, (string) $group->Ид);
        }
    }

    private function parsItems($items)
    {
        foreach ($items as $item) {
            $id = (string) $item->Ид;
            $this->data['items'][$id]['art'] = (string) $item->Артикул;
            $this->data['items'][$id]['name'] = (string) $item->Наименование;
            $this->data['items'][$id]['edizm'] = (string) $item->БазоваяЕдиница;
            $this->data['items'][$id]['sub'] = (string) $item->Группы->Ид;

            if ($item->ЗначенияРеквизитов->ЗначениеРеквизита) {
                $this->parsAttribute($id, $item->ЗначенияРеквизитов->ЗначениеРеквизита);
            }

        }
    }

    private function parsAttribute($idItem, $data)
    {
        foreach ($data as $value) {
            $name = (string) $value->Наименование;
            $val = (string) $value->Значение;

            switch ($name) {
                case 'Вес':
                    $this->data['items'][$idItem]['ves'] = $val;
                    break;
            }
        }
    }
    public function getResult()
    {
        return $this->data;
    }
}
