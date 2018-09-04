<?php

namespace XT\Db\tables;


use XT\Core\System\RBAC_PER_DESCRIPTION;
use XT\Core\System\RBAC_PERMISSION;
use XT\Db\DDL;

use Zend\Db\Sql\Ddl\Column\Datetime;


use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\Index\Index;


class usersTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {
        $ddl->correctTable('users', 'user_id', $engine = DDL::engine_innodb);


        $ddl
            ->correctColumn(new Varchar('user_login', 60, false, ''),
                [
                    new UniqueKey('user_login', 'user_login_unique'),
                    new Index('user_login', 'user_login_index')
                ])
            ->correctColumn(new Varchar('user_pass', 255, false, ''))
            ->correctColumn(new Varchar('user_email', 100, false, ''), [new Index('user_email', 'user_email')])
            ->correctColumn(new Varchar('user_url', 500, false, ''))
            ->correctColumn(new Datetime('user_registered', false, '0000-01-01 00:00:00'))
            ->correctColumn(new Varchar('user_activation_key', 255, false, ''))
            ->correctColumn(new Integer('user_status', false, 0))
            ->correctColumn(new Varchar('display_name', 255, false, ''));

        $ddl->deltebulkrows();

    }
}
