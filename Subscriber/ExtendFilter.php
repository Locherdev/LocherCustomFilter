<?php

namespace LocherCustomFilter\Subscriber;

use Enlight\Event\SubscriberInterface;

class ExtendFilter implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDirectory;

    /**
     * @param $pluginDirectory
     */
    public function __construct($pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend' => 'onOrderPostDispatch'
        ];
    }

    public function onOrderPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Customer $controller */
        $controller = $args->getSubject();

        $view = $controller->View();
        $request = $controller->Request();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views');

        if ($request->getActionName() == 'index') {
            $view->extendsTemplate('backend/locher_custom_filter/app.js');
        }

        if ($request->getActionName() == 'load') {
            $view->extendsTemplate('backend/locher_custom_filter/view/detail/window.js');
            $view->extendsTemplate('backend/locher_custom_filter/view/list/filter.js');
        }
    }
}