<?php

namespace App\modules\Korzilla\Yandex\RSS;

class Object182
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
    private $domen ='';
    
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

    public function getItems() {
        foreach ($this->getSubivision() as $subdivision) {
            $text = '';
            $timestamp = 0;

            $objs = $this->db->get_results(
                "SELECT 
                    `Message_ID` AS id, 
                    `name`,
                    `text`,
                    `LastUpdated`
                FROM 
                    Message182 
                WHERE 
                    Subdivision_ID = '{$subdivision['Subdivision_ID']}' 
                    AND Checked = 1", 
                ARRAY_A
            );
            foreach ($objs as $obj) {
                if ($obj['name']) $text .= "<h2>{$obj['name']}</h2>";
                $text .= $obj['text'];
                $timestamp = ($timestamp < strtotime($obj['LastUpdated']) ? strtotime($obj['LastUpdated']) : $timestamp);
            }

            if (!$text) continue;

            $link = $this->domen . $subdivision['Hidden_URL'];
            $this->links[] = ['link' => $link, 'name' => $subdivision['Subdivision_Name']];
            $this->items[] = [
                'link' => $link,
                'title' => $subdivision['Subdivision_Name'],
                'category' => $subdivision['Subdivision_Name'],
                'Subdivision_Name' => $subdivision['Subdivision_Name'],
                'timestamp' => $timestamp,
                'text' => $text,
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

        return $this->items;
    }

    private function getSubivision()
    {
        return $this->db->get_results(
            "SELECT 
                sub.Subdivision_Name,
                sub.Subdivision_ID,
                sub.Hidden_URL
            FROM 
                Subdivision as sub, 
                Sub_Class as cc 
            WHERE 
                sub.Catalogue_ID = '{$this->catalogue}'
                AND sub.Subdivision_ID = cc.Subdivision_ID
                AND cc.Class_ID = 182 
                AND sub.Checked = 1
                AND sub.rss_turbo_yandex = 1",
            ARRAY_A
        ) ?: [];
    }
}
