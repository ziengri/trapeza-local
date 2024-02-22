<?php

namespace App\modules\Korzilla\Yandex\RSS;

class Object2021
{
    private $db;
    /**
     * catalogue
     *
     * @var int
     */
    private $catalogue = 0;

    /**
     * items
     *
     * @var array
     */
    private $items = [];

    /**
     * domen
     *
     * @var string
     */
    private $domen = '';

    /**
     * links
     *
     * @var array
     */
    private $links = [];

    public function __construct($db, $catalogue, $domain)
    {
        $this->db = $db;
        $this->catalogue = $catalogue;
        $this->domen = $domain;
    }

    public function getItems(): array
    {
        foreach ($this->getSubdivisions() as $subdivision) {
            $objs = $this->db->get_results(
                "SELECT
                    *
                FROM
                    Message2021
                WHERE
                    Subdivision_ID = '{$subdivision['Subdivision_ID']}'
                    AND Checked = 1
                    AND (
                            `textfull` != '' 
                            OR `text` != ''
                            OR `textfull_bottom` != ''
                        )",
                ARRAY_A
            );

            foreach ($objs as $obj) {
                $imgPath = $this->db->get_var(
                    "SELECT 
                        `Preview` 
                    FROM 
                        Multifield 
                    WHERE 
                        Message_ID = '{$obj['Message_ID']}' 
                    LIMIT 1"
                );

                $img = ($imgPath ? $this->domen . $imgPath : null);

                $link = $this->domen . nc_message_link($obj['Message_ID'], 2021);

                $this->links[] = ['link' => $link, 'name' => $obj['name']];
                $text = $obj['textfull'] . $obj['textfull_bottom'];
                $this->items[] = [
                    'link' => $link,
                    'title' =>  $obj['name'],
                    'category' => $subdivision['Subdivision_Name'],
                    'Subdivision_Name' => $subdivision['Subdivision_Name'],
                    'timestamp' => strtotime($obj['LastUpdated']),
                    'text' => ($text ?: "<div>" . $obj['text'] . "</div>"),
                    'img' => $img,
                ];
            }

            if (count($this->links) > 0) {
                $menu = '<menu>';
                foreach ($this->links as $link) {$menu .= "<a href='{$link['link']}'>{$link['name']}</a>";}
                $menu .= '</menu>';
    
                for ($i=0; $i < count($this->items); $i++) { 
                    $this->items[$i]['menu'] = $menu;
                }
            }
        }
        return $this->items;
    }

    private function getSubdivisions()
    {
        return $this->db->get_results(
            "SELECT 
                sub.Subdivision_Name,
                sub.Subdivision_ID
            FROM 
                Subdivision as sub, 
                Sub_Class as cc 
            WHERE 
                sub.Catalogue_ID = '{$this->catalogue}'
                AND sub.Subdivision_ID = cc.Subdivision_ID 
                AND cc.Class_ID = 2021
                AND sub.Checked = 1 
                AND sub.rss_turbo_yandex = 1",
            ARRAY_A
        ) ?: [];
    }
}
