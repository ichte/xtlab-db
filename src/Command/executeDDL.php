<?php
namespace XT\Db\Command;


use XT\Db\Adapter;
use Zend\Db\Sql\SqlInterface;

class executeDDL
{
    /**
     * @param Adapter $db
     * @param array $params
     * @return ResultSet
     */
    public static function execute($db, $params)
    {
        /**
         * @var SqlInterface $sqlObject
         */
        $sqlObject = $params[0];

        return $db->query($db->sql->getSqlStringForSqlObject($sqlObject),
            Adapter::QUERY_MODE_EXECUTE);

        

    }
}