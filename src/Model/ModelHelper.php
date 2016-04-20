<?php

namespace Fazb\RedBean\Model;

use RedBeanPHP\SimpleModelHelper;
use Fazb\RedBean\Helper\HelperInterface;

/**
* ModelHelper
*/
class ModelHelper extends SimpleModelHelper
{
    protected $helpers = array();

    protected $models = array();

    public function __construct($helpers = array())
    {
        foreach ($helpers as $class => $options) {
            $helper = new $class;
            $helper->configure($options);
            $this->addHelper($helper);
        }
    }

    public function addHelper(HelperInterface $helper)
    {
        $this->helpers[$helper->getName()] = $helper;
    }

    public function addModel($name, $configuration)
    {
        foreach (array_keys($configuration) as $helperName) {
            if (null === $helper = $this->getHelper($helperName)) {
                throw new \RuntimeException(sprintf('No helper registered with that name "%s"', $helperName));
            }

            $configuration[$helperName] = $helper->validate($configuration[$helperName]);
        }

        $this->models[$name] = $configuration;
    }

    public function getHelper($name)
    {
        return isset($this->helpers[$name]) ? $this->helpers[$name] : null;
    }

    public function getModel($name)
    {
        return isset($this->models[$name]) ? $this->models[$name] : null;
    }

    public function onEvent($eventName, $bean)
    {
        $type = $bean->getMeta('type');
        if (isset($this->models[$type])) {
            $model = $this->getModel($type);
            foreach ($model as $helperName => $options) {
                $helper = $this->getHelper($helperName);
                if (!$helper || !isset($options['events'][$eventName])) {
                    continue;
                }

                $helper->apply($bean, $eventName, $options);
            }
        }
    }
}
