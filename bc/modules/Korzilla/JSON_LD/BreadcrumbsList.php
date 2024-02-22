<?php

namespace App\modules\Korzilla\JSON_LD;

class BreadcrumbsList
{
    private static $instance;

    private $data = [];

    private $domain;

    private function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public static function getInstance($domain): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($domain);
        }
        return self::$instance;
    }

    // public function setDomen(string $domain): self
    // {
    //     $this->domain = trim($domain, '/');
    //     return $this;
    // }

    public function setData(array $urls, array $names): self
    {
        if (empty($this->domain)) throw new \Exception("Нет домена", 1);

        foreach ($names as $position => $name) {
            $currentPosition = $position + 2;
            if (count($this->data) + 2 != $currentPosition) throw new \Exception("Неправильная позиция", 1);
            if (empty($urls[$position])) throw new \Exception("пустой урл", 1);

            $this->data[] = [
                "@type" => "ListItem",
                "position" => $currentPosition,
                "item" =>
                [
                    "@id" => $this->domain . $urls[$position],
                    "name" => $name
                ]
            ];
        }

        
        $this->data = array_merge([[
            "@type" => "ListItem",
            "position" => 1,
            "item" =>
            [
                "@id" => $this->domain . "/",
                "name" => "Главная"
            ]
        ]], $this->data);

        return $this;
    }

    public function getJsonLd()
    {
        // if (empty($this->data)) throw new \Exception("Не заполнен BreadcrumbsList", 1);
        if (empty($this->data)) return;

        $output = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $this->data
        ];

        return '<script type="application/ld+json">' . json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
