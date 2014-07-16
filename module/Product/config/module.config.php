<?php
namespace Product;

return array(
    'doctrine' => array(
        'driver' => array(
            'entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => 'entity',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'attributes' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/attributes',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Attribute',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add',
                            ),
                        )
                    ),
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/update',
                            'defaults' => array(
                                'action' => 'update',
                            ),
                        )
                    ),
                ),
            ),
            'products_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/products',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'product_categorised' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/:categoryUrl/:productNumber[/:tab]',
                            'defaults' => array(
                                'action' => 'view',
                                'tab' => 'description'
                            ),
                            'constraints' => array(
                                'tab' => 'description|specifications'
                            )
                        )
                    ),
                    'product_uncategorised' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/view/:productNumber[/:tab]',
                            'defaults' => array(
                                'action' => 'view',
                                'tab' => 'description'
                            ),
                            'constraints' => array(
                                'tab' => 'description|specifications'
                            )
                        )
                    ),
                    'category' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/:categoryUrl',
                            'defaults' => array(
                                'controller' => __NAMESPACE__ . '\Controller\Category',
                                'action' => 'index'
                            )
                        )
                    ),
                    'search' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/search/:value',
                            'defaults' => array(
                                'action' => 'search'
                            )
                        )
                    ),
                )
            ),
            'products' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/products',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'action' => 'remove'
                            )
                        )
                    )
                )
            ),
            'categories' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/categories',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Category',
                        'action' => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'save' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/save',
                            'defaults' => array(
                                'action' => 'save'
                            )
                        )
                    ),
                    'add' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add'
                            )
                        )
                    ),
                    'remove' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'action' => 'remove'
                            )
                        )
                    )
                )
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'product_form' => __NAMESPACE__ . '\Factory\ProductFormFactory',
            'category_form' => __NAMESPACE__ . '\Factory\CategoryFormFactory',
        ),
        'invokables' => array(
            'product_service' => __NAMESPACE__ . '\Service\Product',
            'category_service' => __NAMESPACE__ . '\Service\Category',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController',
            __NAMESPACE__ . '\Controller\Category' => __NAMESPACE__ . '\Controller\CategoryController',
            __NAMESPACE__ . '\Controller\Attribute' => __NAMESPACE__ . '\Controller\AttributeController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'product' => __DIR__ . '/../view/partial/product.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
