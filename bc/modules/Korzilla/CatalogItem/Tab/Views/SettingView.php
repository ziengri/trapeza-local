<?php

namespace App\modules\Korzilla\CatalogItem\Tab\Views;

class SettingView
{
    public function generate($content_view, $data = []): string
    {
        if (file_exists($content_view)) {
            ob_start();
            include $content_view;
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        } else {
            throw new \Exception(sprintf('Файл %s не найден.', $content_view));
        }
    }
}
