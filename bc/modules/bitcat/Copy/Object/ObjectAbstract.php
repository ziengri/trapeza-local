<?php

namespace App\modules\bitcat\Copy\Object;

use nc_Core;
use Exception;

abstract class ObjectAbstract
{
    /**
     * @var nc_Core
     */
    protected $nc_core;
    /**
     * @var int
     */
    public $classId;
    /**
     * @var object
     */
    protected $object;

    public function __construct(int $objectId)
    {
        $this->setNcCore();
        $this->setClassId();
        $this->setObject($objectId);
    }

    protected function setObject(int $objectId)
    {
        $sql = "SELECT * FROM `Message{$this->classId}` WHERE `Message_ID` = {$objectId}";
        $this->object = $this->nc_core->db->get_row($sql);
    }

    public function getProperty($property)
    {
        return $this->object->$property;
    }

    public function setProperty($property, $value)
    {
        $this->object->$property = $value;
    }
    
    public function unsetProperty($property)
    {
        unset($this->object->$property);
    }
    
    public function save()
    {
        if ($this->isExists()) {
            $this->update();
        } else {
            $this->object->Message_ID = $this->insert();
        }
    }

    protected function isExists(): bool
    {
        if (!isset($this->object->Message_ID)) return false;
        
        $sql = "SELECT EXISTS (SELECT * FROM `Message{$this->classId}` WHERE `Message_ID` = {$this->object->Message_ID})";
        return (bool) $this->nc_core->db->get_var($sql);
    }

    protected function insert(): int
    {
        $fields = $values = '';

        foreach ($this->object as $field => $value) {
            $fields .= $fields ? ',' : '';
            $fields .= "`{$field}`";

            $values .= $values ? ',' : '';
            $values .= "'{$value}'";
        }

        $sql = "INSERT INTO `Message{$this->classId}` ($fields) VALUES ($values)";
        $this->nc_core->db->query($sql);

        if ($this->nc_core->db->is_error) {
            throw new Exception($this->nc_core->db->last_error);
        }

        return (int) $this->nc_core->db->insert_id;
    }

    protected function update()
    {
        $set = '';
        foreach ($this->object as $field => $value) {
            $set .= $set ? ',' : '';
            $set .= "`{$field}` = ".($field === 'Keyword' && empty($value) ? 'NULL' : "'{$value}'");
        }

        $sql = "UPDATE `Message{$this->classId}` SET {$set} WHERE `Message_ID` = {$this->object->Message_ID}";

        $this->nc_core->db->query($sql);
    }

    private function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }

    /**
     * Установить свойство classId - id компонента
     */
    abstract protected function setClassId();
}
