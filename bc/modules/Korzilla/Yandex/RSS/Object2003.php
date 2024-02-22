<?php

namespace App\modules\Korzilla\Yandex\RSS;

class Object2003
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
            $news = $this->db->get_results(
                "SELECT
                    *
                FROM
                    Message2003
                WHERE
                    Subdivision_ID = '{$subdivision['Subdivision_ID']}'
                    AND Checked = 1
                    AND (
                            `textfull` != '' 
                            OR `text` != ''
                        )",
                ARRAY_A
            );

            foreach ($news as $new) {
                $imgPath = $this->db->get_var(
                    "SELECT 
                        `Preview` 
                    FROM 
                        Multifield 
                    WHERE 
                        Message_ID = '{$new['Message_ID']}' 
                    LIMIT 1"
                );

                $img = ($imgPath ? $this->domen . $imgPath : null);

                $link = $this->domen . nc_message_link($new['Message_ID'], 2003);

                $this->links[] = ['link' => $link, 'name' => $new['name']];
                $this->items[] = [
                    'link' => $link,
                    'title' =>  $new['name'],
                    'category' => $subdivision['Subdivision_Name'],
                    'Subdivision_Name' => $subdivision['Subdivision_Name'],
                    'timestamp' => strtotime($new['date']),
                    'text' => ($new['textfull'] ?: "<div>" . $new['text'] . "</div>"),
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
                AND cc.Class_ID = 2003 
                AND sub.Checked = 1 
                AND sub.rss_turbo_yandex = 1",
            ARRAY_A
        ) ?: [];
    }
}
