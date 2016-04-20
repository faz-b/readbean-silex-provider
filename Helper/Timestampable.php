<?php

namespace Fazb\RedBean\Helper;

use Fazb\RedBean\Helper\HelperInterface;
use RedBeanPHP\OODBBean;

/**
* Timestampable
*/
class Timestampable implements HelperInterface
{
    protected $defaults = array(
        'events' => array(
            'dispense'  => array('created_at'),
            'update'    => array('updated_at')
        ),
        'options' => array(
            'dateFormat' => 'Y-m-d H:i:s'
        )
    );

    /**
     * {@inheritdoc}
     */
    public function configure($defaults = array())
    {
        $this->defaults = array_merge(
            $this->defaults,
            $defaults
        );
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OODBBean $bean, $eventName, array $options)
    {
        $fields     = $options['events'][$eventName];
        $dateFormat = $options['options']['dateFormat'];

        foreach ($fields as $name) {
            $bean->$name = (new \DateTime())->format($dateFormat);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsName()
    {
        return array_keys($this->defaults['events']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'timestampable';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $configuration)
    {
        if (!isset($configuration['events'])) {
            $configuration['events'] = $this->defaults['events'];
        }

        if (!isset($configuration['options']['dateFormat'])) {
            $configuration['options']['dateFormat'] = $this->defaults['options']['dateFormat'];
        }

        return $configuration;
    }
}
