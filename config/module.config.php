<?php
return [
    'service_manager' => [
        'factories' => [
           Zend\Db\Adapter\Adapter::class  => \XT\Db\AdapterServiceFactory::class
        ],
    ],
    'admin_plugins' => [
        'database'        => \XT\Db\Admin\Database::class
    ],
];