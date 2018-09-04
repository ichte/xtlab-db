<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 21-Aug-17
 * Time: 1:24 AM
 */

namespace XT\Db\tables;


use XT\Core\System\RBAC_PER_DESCRIPTION;
use XT\Core\System\RBAC_PERMISSION;
use XT\Core\System\RBAC_ROLE;
use XT\Db\DDL;


use Zend\Db\Sql\Ddl\Column\Integer;


use Zend\Db\Sql\Ddl\Constraint\ForeignKey;

use Zend\Db\Sql\Ddl\Index\Index;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class userroleTable
{
    /**
     * @param DDL $ddl
     */
    public static function execute($ddl) {

        roleTable::execute($ddl);
        permissionTable::execute($ddl);



        $ddl->dropFk('user_role_user_id_fk', 'user_role');
        $ddl->dropFk('user_role_role_id_fk', 'user_role');


        $ddl->correctTable('user_role', 'id', $engine = DDL::engine_innodb);

        $ddl

            ->correctColumn(new Integer('user_id'),
                [
                    new Index('user_id', 'user_id_index'),
                    new ForeignKey('user_role_user_id_fk', 'user_id','users', 'user_id', 'CASCADE', 'CASCADE'),

                ]
            )
            ->correctColumn(new Integer('role_id'),
                [
                   new Index('role_id', 'role_id_index'),
                   new ForeignKey('user_role_role_id_fk', 'role_id','role', 'id', 'CASCADE', 'CASCADE')
                ]);




        $ddl->deltebulkrows();

    }
}
