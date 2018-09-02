<?php

namespace XT\Db;


use XT\Db\Command\executeDDL;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\SqlInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorInterface;

/***
 * Class Adapter
 * @package XT\Db
 * @method executeDDL executeDDL($table)
 */

class Adapter extends \Zend\Db\Adapter\Adapter
{
    /***
     * @var \ArrayObject
     */
    public $ArrayPrototype;

    /***
     * @var \Zend\Hydrator\ArraySerializable
     */
    public $HydratingResultSetOneArray;
    /**
     * @var Sql
     */
    public $sql;

    public function __construct($driver)
    {
        parent::__construct($driver);
        $this->sql = new Sql($this);

        $this->HydratingResultSetOneArray = new \Zend\Hydrator\ArraySerializable();
        $this->ArrayPrototype = new \ArrayObject();
    }

    public function __call($name, $arguments)
    {
        $class = "\\XT\Db\\Command\\{$name}";
        if (class_exists($class, true)) {
            return $class::execute($this, $arguments);
        }


        throw new \Exception($class." not found in magic of : ".__CLASS__);
    }
    /**
     * execute
     * @param $string_query string
     * @return ResultSet|StatementInterface
     */
    public function execute($string_query, $resultPrototype = null)
    {
        if ($resultPrototype != null) {
            if (!($resultPrototype instanceof  ResultSetInterface)) {
                $resultPrototype = new HydratingResultSet(new ClassMethods(), $resultPrototype);
            }
        }
        return $this->query($string_query, Adapter::QUERY_MODE_EXECUTE, $resultPrototype);
    }

    /**
     * executeSqlObject
     * @param SqlInterface $sqlObject
     * @return ResultInterface
     */
    public function executeSqlObject(SqlInterface $sqlObject) {
        return $this->sql->prepareStatementForSqlObject($sqlObject)
            ->execute();
    }


    /***
     * @param $table string
     * @return bool
     */
    public function existable($table) {
        return ($this->execute("show tables like '$table'")->count() > 0);
    }


    /***
     * @param $select Select
     * @return null|\ArrayObject
     */
    public function get_row_select($select)
    {
        $stmt = $this->sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();

        if ($row = $result->current())
            $row = $this->HydratingResultSetOneArray->hydrate($row, new \ArrayObject());

        unset($result, $stmt);

        return $row;
    }

    /**
     * @param $select Select
     * @param null $where
     * @param null $order
     * @return ResultInterface
     */
    public function get_rows_select($select, $where = null, $order = null, HydratorInterface $hydrator = null, $objectPrototype = null)
    {

        if ($where)
            $select->where($where);
        if ($order)
            $select->order($order);
        $result = $this->sql->prepareStatementForSqlObject($select)->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            if ($objectPrototype != null) {
                $hydrator = $hydrator ?? new ClassMethods();
                $resultSet = new HydratingResultSet($hydrator, $objectPrototype);

                $resultSet->initialize($result);
                return $resultSet;
            }
        }
        return $result;
    }


    /**
     * @param array $value
     * @param string $table
     * @return int|null
     * @throws \Exception
     */
    public function insert(array $value, string $table)
    {
        $insert = new Insert($table);
        $insert->values($value);
        $result = $this->executeSqlObject($insert);


        if ($result instanceof ResultInterface) {
            if ($newId = $result->getGeneratedValue())
            {
                return (int)$newId;
            }
            else return null;
        }

        throw new \Exception("Database error");
    }

    /**
     * @findrow
     * @param $where
     * @return true,false
     */
    public function existrow($where = null, $table = null)
    {
        $sql = $this->sql;
        $select = $sql->select($table);

        $select->columns(['num' => new \Zend\Db\Sql\Expression('COUNT(1)')]);


        if ($where) $select->where($where);


        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $rs = $result->current();
        if ($rs['num'] != 0) return $rs['num'];
        return false;

    }


}