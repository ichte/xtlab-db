<?php

namespace XT\Db\Admin;

use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Common\Common;
use XT\Core\ToolBox\MessageBox;
use XT\Db\DDL;
use XT\Core\System\RBAC_CONST;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\I18n\Filter\Alpha;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class Database extends AbstractPlugin
{

    protected $nameplugin = 'Databases';

    protected $description = 'Update / Fix / Create table and view';

     

    function index($i)
    {

        if (!$this->ctrl->isGranted(RBAC_CONST::DB_ALTER))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(),
                Common::translate('Not permission granted').':'.RBAC_CONST::DB_ALTER
                );


        $database = $this->ctrl->params()->fromQuery('tb',null);
        $rt = [];
        $rt['database'] = $database;
        if ($database != null)
        {
            $ddl = new DDL($this->dbAdapter);
            $rt['cols'] = $ddl->fix($database);

            return MessageBox::redirectMgs("Table $database is repaired!",
                $this->url('database'), '', 5);

        }

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);

        $view->setVariables($rt);
        return $view;
    }


}