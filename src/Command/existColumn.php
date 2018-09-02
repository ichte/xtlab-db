<?php

namespace XT\Db\Command;



use XT\Db\Adapter;
use Zend\Db\Sql\Predicate\Expression;

use Zend\Db\Sql\TableIdentifier;

class existColumn
{
    /**
     * @param Adapter $db
     * @param array $params
     * @return bool
     */
    public static function execute($db, $params)
    {
        $col        = $params[0];
        $table      = $params[1];
        $database   = $db->getCurrentSchema();
        $tb         = new TableIdentifier('COLUMNS', 'information_schema');

        $select = $db->sql->select();
        $select
            ->from($tb)
            ->columns([new Expression('COUNT(1) as T')])
            ->where([ 'COLUMN_NAME'=>$col, 'TABLE_NAME' => $table, 'TABLE_SCHEMA'=>$database]);

        return $db->get_row_select($select)['T'];

    }
}
