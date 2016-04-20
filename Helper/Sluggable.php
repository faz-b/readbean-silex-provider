<?php

namespace Fazb\RedBean\Helper;

use Fazb\RedBean\Helper\HelperInterface;
use RedBeanPHP\OODBBean;

/**
* Sluggable
*/
class Sluggable implements HelperInterface
{
    protected $defaults = array(
        'events' => array(
            'update' => array()
        ),
        'options' => array(
            'fieldName' => 'slug'
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
        $fields = $options['events'][$eventName];
        $parts  = array();
        foreach ($fields as $name) {
            $parts[] = $bean[$name];
        }

        $fieldName = $options['options']['fieldName'];

        if ($parts) {
            $bean->setMeta(sprintf('cast.%s', $fieldName), 'string');
            $bean->$fieldName = $this->sluggify(join(' ', $parts));
        }
    }

    /*
     * Sluggify
     *
     * @param string
     *
     * @return string
     */
    public function sluggify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n-a';
        }

        return $text;
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
        return 'sluggable';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $configuration)
    {
        if (!isset($configuration['events'])) {
            throw new \RuntimeException(sprintf('You must specify an "events" key, with the targetted fields for the "%s" helper', $this->getName()));
        }

        if (!isset($configuration['options']['fieldName'])) {
            $configuration['options']['fieldName'] = $this->defaults['options']['fieldName'];
        }

        return $configuration;
    }
}
