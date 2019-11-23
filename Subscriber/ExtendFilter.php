<?php

namespace LocherCustomFilter\Subscriber;

use Enlight\Event\SubscriberInterface;

$GLOBALS["customFilter"] = null;

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
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Order' => 'onOrderPostDispatch',
            'Shopware_Controllers_Backend_Order::getListAction::before' => 'before_getListAction',
            'Shopware_Controllers_Backend_Order::getListAction::after' => 'after_getListAction'
        ];
    }

    public function onOrderPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Customer $controller */
        $controller = $args->getSubject();

        $view = $controller->View();
        $request = $controller->Request();

        $view->addTemplateDir($this->pluginDirectory . '/Resources/views');

        if ($request->getActionName() == 'load') {
            $view->extendsTemplate('backend/locher_custom_filter/view/list/filter.js');
        }
    }

    public function before_getListAction(\Enlight_Hook_HookArgs $args) {
        $request = $args->getSubject()->Request();
        $filter = $request->getParam('filter');
        $GLOBALS["customFilter"] = array_values(array_slice($filter, -1))[0];
        if ($GLOBALS["customFilter"]['property'] == 'customFilter') {
            $GLOBALS["customFilter"] = $GLOBALS["customFilter"]['value'];
            array_pop($filter);
        } else {
            $GLOBALS["customFilter"] = null;
        }
        $request->setParam('filter', $filter);
    }

    public function after_getListAction(\Enlight_Hook_HookArgs $args) {
        if ($GLOBALS["customFilter"] != null) {
            $list = $args->getSubject()->View()->getAssign();
            $list = $this->applyCustomFilter($list);
            $args->getSubject()->View()->assign($list);
        }
    }

    private function applyCustomFilter($list) {
        $filteredList = [];
        foreach($list['data'] as $individualOrder) {
            $keepItem = true;
            switch($GLOBALS["customFilter"]) {
                case 'Nur Fertig-PCs':
                case 'Only Fertig-PCs':
                    $keepItem = $this->has_StuecklistenArtikel($individualOrder);
                    break;
                case 'Nur Indi-PCs':
                case 'Only Indi-PCs':
                    if ($this->has_StuecklistenArtikel($individualOrder) == true || $this->has_KomplettService($individualOrder) == false) {
                        $keepItem = false;
                    }
                    break;
                case 'Nur Einzelteile':
                case 'Only Einzelteile':
                    if ($this->has_StuecklistenArtikel($individualOrder) == true || $this->has_KomplettService($individualOrder) == true) {
                        $keepItem = false;
                    }
                    break;
            }
            if ($keepItem) {
                $filteredList[] = $individualOrder;
            }
        }
        $returnList['data'] = $filteredList;
        $returnList['success'] = $list['success'];
        $returnList['total'] = count($filteredList);
        return $returnList;
    }

    private function has_StuecklistenArtikel($individualOrder) {
        foreach($individualOrder['details'] as $product) {
            if(strpos($product['articleName'], "Stücklisten-Artikel") === 0) return true;
        }
        return false;
    }

    private function has_KomplettService($individualOrder) {
        foreach($individualOrder['details'] as $product) {
            if($product['articleNumber'] == 'SW2000999') return true;
        }
        return false;
    }
}