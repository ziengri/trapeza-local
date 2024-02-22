<?php
/**
 * Класс Morpher выполняет склонение слов
 * при помощи api https://morpher.ru/
 */

namespace Korzilla;

class Morpher
{
    const API_KEY = 'Ga5uyGDacwV';
    const API_URL = 'api.korzilla.ru/morpher/';
    const NO_WORD_KEY = '--n-w--';
    const NO_WORD_CACHE_TIME = 10*86400; # В секундах 86400 = сутки
    const KEYS = [
        'KOGO' => [
            'type' => 'type1',
            'title' => 'Родительный падеж'
        ],
        'KOMY' =>[
            'type' => 'type2',
            'title' => 'Дательный падеж'
        ],
        'B_KOGO' => [
            'type' => 'type3',
            'title' => 'Винительный падеж'
        ],
        'KEM' => [
            'type' => 'type4',
            'title' => 'Творительный падеж'
        ],
        'OKOM' => [
            'type' => 'type5',
            'title' => 'Предложный падеж'
        ],
        'O_OKOM' => [
            'type' => 'type6',
            'title' => 'О/Об Предложный падеж'
        ],
        'GDE' => [
            'type' => 'type7',
            'title' => 'Где?'
        ],
        'KYDA' => [
            'type' => 'type8',
            'title' => 'Куда?'
        ],
        'OTKYDA' => [
            'type' => 'type9',
            'title' => 'Откуда?'
        ]
    ];
    /**
     * @var $cache используется для схранения уже полученных за текущий сеанс слов
     */
    private static $cache = [];

    /**
     * Склонение слова
     * 
     * Логика работы:
     * 1) конвертируем ключ в тип для дальнейшей работы
     * 2) пытаемя получить слово из базы
     * 3) проверяем значение слова из базы, если оно пустое или нужно ли его обновить
     * 4) если 3 п. прошел проверку то получаем слово по API
     * 5) если 3 п. не прошел проверку то очищаем слово
     * 6) записываем слово в кэш для дальнейшего использования
     * 
     * @param string $word склоняемое слово
     * @param string $key ключ склонения
     * 
     * @return string|null
     */
    public static function convert($word, $key)
    {
        if (!$type = self::convsertType($key)) {
            self::writeLog("Неизвестный ключ: {$key}");
            return null;
        }

        if (isset(self::$cache[$word][$type])) {
            $declension = self::$cache[$word][$type];
        } else {
            $dbWord = self::getWordFromDB($word, $type);
            if (empty($dbWord) || self::checkWordUpdate($dbWord)) {
                $declension = self::getWordFromAPI($word, $type);
            } else {
                $declension = self::clearDbWord($dbWord);
            }
            self::$cache[$word][$type] = $declension;
        }
        return $declension;
    }

    /**
     * Очищает слово взятое из базы
     * 
     * @param string|null $word
     * 
     * @return string|null
     */
    private static function clearDbWord($word)
    {        
        return strpos($word, self::NO_WORD_KEY) === 0 ? null : $word;
    }
    
    /**
     * Проверяет необходимость обновления склонения
     * 
     * @param string $word
     * 
     * @return bool
     */
    private static function checkWordUpdate($word)
    {
        $result = false;
        
        if (strpos($word, self::NO_WORD_KEY) === 0) {            
            $timeWord = str_replace(self::NO_WORD_KEY, '', $word);
            $date = new \DateTime();
            $date->setTimestamp($timeWord);
            $date->modify('+'.self::NO_WORD_CACHE_TIME.' second');
            $result = $date->format('U') < date('U');
        }

        return $result;
    }
    /**
     * @param string $type ключ склонения
     * 
     * @return string|null тип склонения
     */
    private static function convsertType($key)
    {
        return isset(self::KEYS[$key]) ? self::KEYS[$key]['type'] : null;
    }

    /**
     * получить склонение слова из базы
     * 
     * @param string $word склоняемое слово
     * @param string $type тип склонения
     * 
     * @return string|null склоненное слово или пустота
     */
    private static function getWordFromDB($word, $type)
    {
        $db = self::getDB();
        $query  = "SELECT `".$db->escape($type)."` FROM `morpher`";
        $query .= " WHERE `word` = '".$db->escape($word)."'";
        return $db->get_var($query);
    }

    /**
     * @param string $word склоняемое слово
     * @param string $type тип склонения
     * 
     * @return string|null склоненное слово или пустота
     */
    private static function getWordFromAPI($word, $type)
    {       
        $declension = self::request($word, $type);
        self::saveWord($word, $declension, $type);
        return $declension;
    }

    /**
     * Сохраняет склонение в базу
     * 
     * @param string $word склоняемое слово
     * @param string $declension склонение
     * @param string $field поле 
     */
    private static function saveWord($word, $declension, $field)
    {
        global $db;

        if (!empty($declension)) {
            $declension = $db->escape($declension);
        } else {
            $declension = self::NO_WORD_KEY.date('U');
        }
        $word = $db->escape($word);
        
        $query  = "INSERT INTO `morpher` (`word`, `{$field}`)";
        $query .= " VALUES ('{$word}', '{$declension}')";
        $query .= " ON DUPLICATE KEY UPDATE `{$field}` = '{$declension}'";
        $db->query($query);
    }

    /**
     * @param string $word склоняемое слово
     * @param string $type тип склонения
     * 
     * @return string|null склоненное слово или пустота
     */
    private static function request($word, $type)
    {
        $post = [
			'auth_key' => self::API_KEY,
			'method' => 'convert',
			'word' => $word,
            'type' => $type
		];
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::API_URL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 6);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);		

		if ($code === 200) {
			$result = json_decode($result, true);
            $result = is_array($result) && isset($result['result']) ? $result['result'] : null;
		} else {
            $result = null;
        }

        curl_close($curl);
        
		return $result;
    }

    private static function writeLog($log)
    {
        global $DOCUMENT_ROOT, $current_catalogue;

        $logPath = $DOCUMENT_ROOT."/trash/morpher.log";

        $log = sprintf("[%s]: сайт - %s; %s \r\n",
            date('d-m-Y H:i:s'), 
            $current_catalogue['Domain'] ?: '', 
            $log ?: ''
        );

        file_put_contents($logPath, $log, FILE_APPEND);
    }

    private static function getDB()
    {
        global $db;
        return $db;
    }
}
