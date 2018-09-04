<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 04-Sep-18
 * Time: 9:24 AM
 */

namespace XT\Db;


use Zend\Db\Sql\Ddl\Column\Column;

class EnumColunm extends Column
{
    protected $type = '';

    public function __construct($name = null, $nullable = false, $default = null, $options = [], $type = 'INTEGER')
    {
        $this->type = $type;
        parent::__construct($name, $nullable, $default, $options);
    }


}