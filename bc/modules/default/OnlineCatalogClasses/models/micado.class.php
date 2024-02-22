<?php

class Micado
{
    public function getSearchResult($find)
    {
        global $setting, $db, $catalogue;

        $cc = $db->get_var("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID = {$setting['mikado_save_sub']} AND Class_ID = 2001");

        if (!$setting['mikado_save_sub'] || !$find) {
            return [];
        }
        $respon = $this->curlPost([
            'Search_Code' => $find,
            'ClientID' => $setting['mikado_login'],
            'Password' => $setting['mikado_passwor'],
            'FromStockOnly' => 0
        ]);
        $xml = new SimpleXMLElement($respon);

        $result = $analog = [];
        $oreginalKey = $oreginalArt = '';
        $typeItem = ['Aftermarket' => 'original', 'OEM' => 'analog', 'Analog' => 'analog', 'AnalogOEM' => 'analog'];

        if ($xml->List->Code_List_Row) {
            foreach ($xml->List->Code_List_Row as $row) {
                $id = (string) $row->ZakazCode;
                $type = (string) $row->CodeType;
                $count = 0;

                if ($row->OnStocks->StockLine) {
                    foreach ($row->OnStocks->StockLine as $stock) {
                        $count += (int) preg_replace('/\D/', '', (string) $stock->StockQTY);
                    };
                }

                if ($typeItem[$type] != 'original' && $count == 0) {
                    continue;
                }

                $art = (string) $row->ProducerCode;
                $name = (string) $row->Name;
                $price = ((double) $row->PriceRUR) * (1 + $setting['mikado_markup'] / 100);
                if ($typeItem[$type] == 'analog') {
                    $analog[] = $art;
                } else {
                    $oreginalKey = $id;
                    $oreginalArt = $art;
                }

                $result[$id] = [
                    'Subdivision_ID' => $setting['mikado_save_sub'],
                    'Sub_Class_ID' => $cc,
                    'art' => $art,
                    'art2' => ($id != $oreginalKey ? $oreginalArt : ''),
                    'vendor' => (string) $row->Brand,
                    'name' => $name,
                    'stock' => $count,
                    'price' => $price,
                    'Keyword' => encodestring(trim($name) . " " . trim($art), 1),
                    'Catalogue_ID' => $catalogue,
                    'Checked' => 1,
                    'code' => $id
                ];
            }
        }
        if (!empty($oreginalKey)) {
            $result[$oreginalKey]['analog'] = implode("\r\n", $analog);
        }
        return $result ;
    }

    private function curlPost($data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://mikado-parts.ru/ws1/service.asmx/Code_Search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
