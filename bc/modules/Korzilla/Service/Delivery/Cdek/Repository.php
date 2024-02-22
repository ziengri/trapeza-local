<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

use Exception;

class Repository
{
    const SITE_DIR = '/cdek';
    const PUBLIC_DIR = __DIR__.'/tmp';

    /**
     * @var string путь от корня системы до папки сайта 
     */
    private $siteDir;
    
    /**
     * @param string путь от корня системы до папки сайта
     */
    public function __construct($siteDir)
    {
        $this->siteDir = $siteDir;
        $this->initDir();
    }

    /**
     * Получить путь от корня системы до директории cdek в папке сайта
     * 
     * @return string
     */
    public function getSiteDir()
    {
        return $this->siteDir.self::SITE_DIR;
    }

    /**
     * Получить путь от корня системы до общей директории cdek
     * 
     * @return string
     */
    public function getPublicDir()
    {
        return self::PUBLIC_DIR;
    }

    /**
     * Инициализировать директорию CDEK
     */
    private function initDir()
    {   
        $path = '';
        foreach (explode('/', $this->getSiteDir()) as $dir) {
            $path .= "/{$dir}";
            if (!file_exists($path) && !mkdir($path)) {
                throw new Exception('Неудалось инициализировать дирекуторию для CDEK в папке сайта '.$path);
            }
        }

        $path = '';
        foreach (explode('/', $this->getPublicDir()) as $dir) {
            $path .= "/{$dir}";
            if (!file_exists($path) && !mkdir($path)) {
                throw new Exception('Неудалось инициализировать публичную дирекуторию для CDEK '.$path);
            }
        }
    }
}