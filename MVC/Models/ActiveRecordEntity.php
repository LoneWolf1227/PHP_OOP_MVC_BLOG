<?php


namespace MVC\Models;


use Core\Db;


abstract class ActiveRecordEntity
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    private function getCreatedAt(): string
    {
        return $this->createdAt = self::getById($this->id)->createdAt;
    }

    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query('select * from `'.static::getTableName().'`;', [], static::class);
    }

    abstract protected static function getTableName(): string;

    /**
     * @param int $id
     * @return static\null
     */
    public static function getById(int $id)
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `'.static::getTableName().'` WHERE id = :id',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property)
        {
            $propertyName = $property->getName();
            $mappedProperties[$propertyName] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    public function save()
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();

        if ($this->id !== null)
        {
            $this->update($mappedProperties);
        }else{
            $this->insert($mappedProperties);
        }
    }

    private function update(array $mappedProperties)
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value)
        {
            $param = ':param' . $index;
            $columns2params[] = $column . ' = ' . $param;
            $params2values[$param] = $value;
            ++$index;
        }

        $sql  = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id = ' . $this->id;
        $db = Db::getInstance();
        $res = $db->query($sql, $params2values, static::class);
    }

    private function insert(array $mappedProperties)
    {
        $fproperties = array_filter($mappedProperties);

        $columns = [];
        $paramsNames = [];
        $params2Values = [];

        foreach ($fproperties as $columnName => $value)
        {
            $columns[] = '`' . $columnName . '`';
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            $params2Values[$paramName] = $value;
        }

        $colsViaSemicolons = implode(', ', $columns);
        $paramsViaSemicolons = implode(',', $paramsNames);
        $sql = 'INSERT INTO `'. static::getTableName() . '` (' . $colsViaSemicolons . ') VALUES (' .
            $paramsViaSemicolons . ') ';

        $db = Db::getInstance();
        $db->query($sql, $params2Values, static::class);
        $this->id = $db->getLastInsertId();
        $this->createdAt = $this->getCreatedAt();
    }

    public function delete()
    {
        $db = Db::getInstance();
        $db->query('DELETE FROM `' . static::getTableName() . '` WHERE id = :id', [':id' => $this->id]);
        $result = self::getById($this->id);
        $this->id = null;
        return $result;
    }

    public static function findOneByColumn(string $columnName, $value)
    {
        $db = Db::getInstance();
        $res = $db->query(
                    'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1',
                    [':value' => $value],
                    static::class
                );
        if ($res === [] || $res === null)
        {
            return null;
        }
        return $res[0];
    }
}