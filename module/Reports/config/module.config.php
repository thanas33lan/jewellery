<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Reports;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'reports-sales' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/reports-sales[/:action][/:id]',
                    'defaults' => [
                        'controller' => Controller\ReportsSalesController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'reports-products' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/reports-products[/:action][/:id]',
                    'defaults' => [
                        'controller' => Controller\ReportsProductsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            // Controller\ReportsSalesController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
