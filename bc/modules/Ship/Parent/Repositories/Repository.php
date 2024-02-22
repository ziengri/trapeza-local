<?php

namespace App\modules\Ship\Parent\Repositories;

use App\modules\Ship\Parent\Models\Model;
use nc_Db;

abstract class Repository
{   

    /**
     * Возвращает имя таблицы в БД
     *
     * @return string
     */
    abstract protected function getTableName();

    /**
     * Возвращает имя поля в БД которое являеться первичным ключом
     *
     * @return string
     */
    abstract protected function getIdName();

    /**
     * Возвращает namespace класса модели
     *
     * @return string
     */
    abstract protected function getModelClassName();

    protected $db;

    public function __construct(nc_Db $db)
    {
        $this->db = $db;
    }
    public function save(Model $model): bool
    {
        $idName = $this->getIdName();

        if (!$model->$idName){
            $this->insert($model);
        }else{
            $this->update($model);
        }
        return !(bool) $this->db->is_error;
    }

    private function update($model): void
    {
        $idName = $this->getIdName();

        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s = %d",
            $this->getTableName(),
            $this->modelToUpdate($model),
            $this->mapPropertyToDb($this->getIdName()),
            $model->$idName
        );

        $this->db->query($sql);
    }

    private function modelToUpdate($model): string
    {
        $set = [];

        foreach ($model as $prop => $value) {
            if ($prop === $this->getIdName())
                continue;

            $set[] = sprintf('%s = %s', $this->mapPropertyToDb($prop), $this->mapValueToDb($value));
        }

        return implode(',', $set);
    }

    private function insert($model)
    {   
        $idName = $this->getIdName();
        echo "<br>insert<br>";

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $this->getTableName(),
            ...$this->modelToInsert($model)
        );
        echo "<br>".$sql."<br>";

        $this->db->query($sql);

        $insertID = $this->db->insert_id;
        echo "<br> insertID: ".$insertID."<br>";
        echo "<br> idName: ".$idName."<br>";

        $model->$idName = $insertID;

        var_dump($model);
    }

    private function modelToInsert($model): array
    {
        $fields = [];
        $values = [];

        foreach ($model as $prop => $value) {
            if ($prop === $this->getIdName())
                continue;

            $fields[] = $this->mapPropertyToDb($prop);
            $values[] = $this->mapValueToDb($value);
        }

        return [
            implode(',', $fields),
            implode(',', $values),
        ];
    }

    private function mapPropertyToDb(string $property): string
    {
        return sprintf('%s.`%s`', $this->getTableName(), $property);
    }

    private function mapValueToDb($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_int($value)) {
            return "{$value}";
        }

        return "'{$value}'";
    }

    public function getAll(array $params = [])
    {
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE %s",
            $this->getTableName(),
            $this->wherePreparation($params)
        );
        var_dump($sql);
        if (!$results = $this->db->get_results($sql)) {
            return null;
        }

        foreach ($results as $index => $result) {
            $results[$index] = $this->mapRowToModel($result);
        }

        return $results;
    }

    public function getRow(array $params = [])
    {
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE %s",
            $this->getTableName(),
            $this->wherePreparation($params)
        );
        
        if (!$result = $this->db->get_row($sql)) {
            return null;
        }



        return $this->mapRowToModel($result);
    }

    protected function wherePreparation(array $params): string
    {
        if (empty($params))
            return '1';

        $set = [];

        foreach ($params as $param) {

            $set[] = sprintf(
                '%s %s %s',
                $this->mapPropertyToDb($param[0]),
                isset($param[2]) ? $param[2] : '=',
                $this->mapValueToDb($param[1])
            );
        }

        return implode(' AND ', $set);
    }

    public function get(int $id)
    {
        $sql = sprintf(
            "SELECT * FROM `%s` WHERE %s = %d",
            $this->getTableName(),
            $this->mapPropertyToDb($this->getIdName()),
            $id
        );

        if (!$row = $this->db->get_row($sql)) {
            return null;
        }

        return $this->mapRowToModel($row);
    }

    protected function mapRowToModel(\stdClass $row)
    {
        $model = $this->getModel();

        foreach ($row as $field => $value) {
            $model->$field = $value;
        }

        return $model;
    }

    protected function getModel()
    {
        $class = $this->getModelClassName();

        if (!is_a($class, Model::class, true)) {
            throw new \Exception('Модель должна наследоваться от Model');
        }

        return new $class;
    }

    public function getInsertId(): ?int
    {
        return $this->db->insert_id;
    }

    /**
     * delete
     *
     * @param  int $id
     * @return int|bool
     */
    public function delete(int $id): int
    {
        $sql = sprintf("DELETE FROM `%s` WHERE %s = %d", $this->getTableName(), $this->getIdName(), $id);
        return $this->db->query($sql);
    }

    public function getLastError(): ?string
    {
        return $this->db->last_error;
    }

    public function getPagination(array $params = [], int $limit, int $offset): array
    {
        $sql = sprintf(
            "SELECT SQL_CALC_FOUND_ROWS 
                * 
            FROM 
                `%s` 
            WHERE %s LIMIT %d,%d",
            $this->getTableName(),
            $this->wherePreparation($params),
            $offset,
            $limit
        );
        if (!$results = $this->db->get_results($sql)) {
            return [];
        }

        foreach ($results as $index => $result) {
            $results[$index] = $this->mapRowToModel($result);
        }

        return $results;
    }

    public function getFoundRow(): int
    {
        return $this->db->get_var("SELECT FOUND_ROWS()") + 0;
    }
}
