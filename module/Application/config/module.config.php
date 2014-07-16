<?php
namespace Application;

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
            'change_language' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/change-language/:lang',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'change-language',
                        'lang' => 'el'
                    ),
                ),
            ),
            'about' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/about',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\About',
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
            'partners' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/partners',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Partner',
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
            'slides' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/slides',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Slide',
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
            'contents' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/admin/contents',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Content',
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
                )
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'home',
                    ),
                ),
            ),
            'sitemap_direct' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/sitemap.xml',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'sitemap' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/sitemap[/:type[/:index]]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'sitemap',
                    ),
                ),
            ),
            'upload-file' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/upload-file',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'upload-file'
                    )
                )
            ),
            'download-file' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/download/:fileName',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'download-file'
                    )
                )
            ),
            'about_index' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/about[/:category]',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\About',
                        'action' => 'index',
                        'category' => 'first',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/contact',
                    'defaults' => array(
                        'controller' => __NAMESPACE__ . '\Controller\Index',
                        'action' => 'contact',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'partner_form' => __NAMESPACE__ . '\Factory\PartnerFormFactory',
            'about_category_form' => __NAMESPACE__ . '\Factory\AboutCategoryFormFactory',
            'slide_form' => __NAMESPACE__ . '\Factory\SlideFormFactory',
            'contact_form' => __NAMESPACE__ . '\Factory\ContactFormFactory',
        ),
        'invokables' => array(
            'partner_service' => __NAMESPACE__ . '\Service\Partner',
            'about_category_service' => __NAMESPACE__ . '\Service\AboutCategory',
            'slide_service' => __NAMESPACE__ . '\Service\Slide',
            'content_service' => __NAMESPACE__ . '\Service\Content',
            'file_util_service' => __NAMESPACE__ . '\Service\FileUtils',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'translate' => __NAMESPACE__ . '\Factory\TranslatePluginFactory',
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'routeName' => __NAMESPACE__ . '\Factory\ActionNameHelperFactory'
        ),
        'invokables' => array(
            'mobile' => __NAMESPACE__ . '\View\Helper\Mobile',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\About' => 'Application\Controller\AboutController',
            'Application\Controller\Slide' => 'Application\Controller\SlideController',
            'Application\Controller\Partner' => 'Application\Controller\PartnerController',
            'Application\Controller\Content' => 'Application\Controller\ContentController'
        ),
        'initializers' => array(
            'entityManager' => __NAMESPACE__ . '\Factory\EntityManagerInitializer',
            'vocabulary' => __NAMESPACE__ . '\Factory\VocabularyInitializer'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/admin' => __DIR__ . '/../view/layout/admin-layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'header' => __DIR__ . '/../view/partial/header.phtml',
            'header_admin' => __DIR__ . '/../view/partial/header-admin.phtml',
            'footer' => __DIR__ . '/../view/partial/footer.phtml',
            'footer_admin' => __DIR__ . '/../view/partial/footer-admin.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
);
