<?php

namespace Fazb\RedBean\Helper;

use RedBeanPHP\OODBBean;

interface HelperInterface
{
    /**
     * Apply behavior
     *
     * @param  OODBBean   $bean
     * @param  string $eventName
     */
    public function apply(OODBBean $bean, $eventName, array $options);

    /**
     * Sets options for a model to apply to
     *
     * @param  array  $options
     */
    public function configure($options = array());

    /**
     * Returns a list of events name
     *
     * @return array
     */
    public function getEventsName();

    /**
     * Returns a name
     *
     * @return string
     */
    public function getName();

    /**
     * Validate configuration and/or set defaults
     *
     * @param  array $configuration
     * @return array $configuration
     */
    public function validate(array $configuration);
}
