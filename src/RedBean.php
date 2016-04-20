<?php

namespace Fazb\RedBean;

class RedBean
{
    /**
     * Sets up database connection
     *
     * @param array
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->setup($options['dsn'], $options['username'], $options['password']);
        $this->freeze($options['freeze']);
        $this->setAutoResolve(true);
    }

    /**
     * Magic method
     * Passes all calls to RedBean's singleton
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $return = call_user_func_array('\RedBeanPHP\Facade::' . $method, $params);

        return $return;
    }
}
