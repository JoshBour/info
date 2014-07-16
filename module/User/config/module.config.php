<?php
namespace User;

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
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password',
                'credential_callable' => 'User\Entity\User::hashPassword'
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'users' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/users',
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
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/admin[/[login]]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Auth',
                        'action' => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/logout',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Auth',
                        'action' => 'logout',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'user_form' => __NAMESPACE__ . '\Factory\UserFormFactory',
            'login_form' => __NAMESPACE__ . '\Factory\LoginFormFactory',
            'Zend\Authentication\AuthenticationService' => __NAMESPACE__ . '\Factory\AuthFactory',
        ),
        'invokables' => array(
            'user_service' => __NAMESPACE__ . '\Service\User',
            'authStorage' => __NAMESPACE__ . '\Model\AuthStorage',
        ),
        'aliases' => array(
            'auth_service' => 'Zend\Authentication\AuthenticationService'
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'user' => __NAMESPACE__ . '\Factory\UserPluginFactory',
        )
    ),
    'view_helpers' => array(
        'factories' => array(
            'user' => __NAMESPACE__ . '\Factory\UserViewHelperFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\Index' => __NAMESPACE__ . '\Controller\IndexController',
            __NAMESPACE__ . '\Controller\Auth' => __NAMESPACE__ . '\Controller\AuthController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
