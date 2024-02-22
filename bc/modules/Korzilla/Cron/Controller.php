<?php

namespace App\modules\Korzilla\Cron;

use App\modules\Korzilla\Cron\Process;

use nc_Core;
use Exception;

class Controller
{
    /**
     * @var nc_Core
     */
    private $nc_core;

    public function __construct()
    {
        $this->setNcCore();
    }

    /**
     * Получить статус задачи
     * 
     * @param int $cron_id id задачи
     * 
     * @return int|false
     */
    public function getStatusTask($cron_id)
    {
        $cron_id += 0;
        $res = $this->nc_core->db->get_row("SELECT `cron_id`, `Checked` FROM Cron_Tasks WHERE `cron_id` = {$cron_id}", ARRAY_A);
        
        if (empty($res)) return false;

        return $res['Checked'];
    }

    /**
     * Получить все задачи которые нужно выполнить 
     * 
     * @return array|false
     */
    private function getTasks()
    {
        $sql = "SELECT 
                        *
                FROM 
                    Cron_Tasks
                 WHERE 
                    (last_update = 0 OR DATE_ADD(last_update, INTERVAL interval_minutes DAY_MINUTE) <= NOW())
                    AND pid = 0
                    AND `Checked` = 1
                    AND (`infinitely` > 0 OR `count` > 0)";

        return $this->nc_core->db->get_results($sql, ARRAY_A);
    }

    /**
     * Получить все задачи в работе
     * 
     * @return array|false
     */
    private function getWorkTasks()
    {
        $sql = "SELECT 
                    `cron_id`,
                    `pid`,
                    `infinitely`,
                    `count`
                FROM 
                    Cron_Tasks
                 WHERE pid != 0 AND pid IS NOT NULL";

        return $this->nc_core->db->get_results($sql, ARRAY_A);
    }

    /**
     * Создания задачи
     * 
     * @param string $cmd Вызываемая команда
     * @param int $interval_minutes Интервал вызова в минутах
     * @param int $catalogue id сайта
     * @param int $count[optional] Количество выполнений. Если прараметр не передан то будет выполняться бесконечно
     * 
     * @return int
     */

    public function setCmd($cmd, $interval_minutes, $catalogue, $count = 0)
    {
        $infinitely = intval(0 >= $count);
        $interval_minutes += 0;
        $catalogue += 0;
        $count += 0;

        if ($interval_minutes < 1) throw new Exception("Min value interval_minutes: 1", 1);
        if (empty($cmd)) throw new Exception("Cmd must not be empty", 1);
        if (empty($catalogue)) throw new Exception("Catalogue_ID must not be empty", 1);

        $sql = "INSERT INTO 
                    Cron_Tasks 
                    (
                        `interval_minutes`,
                        `cmd`,
                        `Catalogue_ID`,
                        `infinitely`,
                        `count`,
                        `Checked`
                    ) 
                VALUES 
                    (
                        {$interval_minutes},
                        '" . addslashes($cmd) . "',
                        {$catalogue},
                        {$infinitely},
                        {$count},
                        1
                    )";

        $this->nc_core->db->query($sql);
        $cron_id = $this->nc_core->db->insert_id;

        if (empty($cron_id)) throw new Exception("Failed to create task", 1);

        return $cron_id;
    }

    /**
     * Постановка задач на выполнения
     * 
     * @return bool
     */
    public function setWork()
    {
        $tasks  = $this->getTasks();

        if (!is_array($tasks)) return false;

        foreach ($tasks as $task) {
            $process = new Process($task['cmd']);
            $processId = $process->getPid() + 0;

            $sql = "UPDATE 
                        Cron_Tasks
                    SET 
                        pid = {$processId},
                        last_update = NOW()
                    WHERE cron_id = '{$task['cron_id']}'";

            $this->nc_core->db->query($sql);
        }
        return true;
    }
    /**
     * Подключения ядра
     * 
     * @return void
     */
    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }

    /**
     * Проверить задачю на выполнения
     * 
     * @return void
     */
    public function checkWorkTasks()
    {
        $tasks = $this->getWorkTasks();

        if (is_array($tasks)) {
            $process = new Process();

            foreach ($tasks as $task) {
                $process->setPid($task['pid']);
                if (!$process->status()) {
                    $count =  (--$task['count'] > 0 ? --$task['count'] : 0);
                    $set[] = "`pid` = 0";
                    $set[] = "`count` = {$count}";

                    if (!$task['infinitely'] && $count == 0) {
                        $set[] = "`Checked` = 0";
                    }

                    $sql = "UPDATE 
                                Cron_Tasks
                            SET 
                                " . implode(',', $set) . "
                            WHERE cron_id = '{$task['cron_id']}'";

                    $this->nc_core->db->query($sql);
                }
            }
        }
    }
    /**
     * Обновить задачю
     * 
     * @param int $cron_id
     * @param array $fields field => value
     * 
     * @return int
     */
    public function updateTask($cron_id, $fields)
    {
        $set = [];
        foreach ($fields as $field => $value) {
            $set[] = "`{$field}` = '" . $this->nc_core->db->escape($value) . "'";
        }
        $sql = "UPDATE
                    Cron_Tasks
                SET
                    " . implode(',', $set) . "
                WHERE
                    cron_id = '{$cron_id}'";
        
        return $this->nc_core->db->query($sql);
    }

    /**
     * Удаления задачи по id
     * 
     * @param int $cron_id id задачи
     * 
     * @return int
     */
    public function deleteTask($cron_id)
    {
        return $this->nc_core->db->query("DELETE FROM Cron_Tasks WHERE `cron_id` = '{$cron_id}'");
    }
}
