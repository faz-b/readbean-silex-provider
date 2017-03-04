<?php

namespace Fazb\RedBean;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Silex\Application;
use Fazb\RedBean\RedBean;
use Fazb\RedBean\Model\ModelHelper;
use Fazb\RedBean\Logger;

/**
 * RedBeanServiceProvider
 */
class RedBeanServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers this service on the given app
     *
     * @param Pimple\Container $app Container instance
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['rb'] = function () use ($app) {
            $options = $app['rb.options'];
            if ($namespace = isset($options['namespace']) ? $options['namespace'] : '') {
                $this->setModelNamespace($namespace);
                unset($options['namespace']);
            }

            $options = array_merge(array(
                    'dsn'      => null,
                    'username' => null,
                    'password' => null,
                    'freeze'   => false
                ), $options);

            $rb = new RedBean($options);

            $logger = new Logger($app['logger']);
            $db = $rb->getDatabaseAdapter()->getDatabase();
            $db->setLogger($logger);
            $db->setEnableLogging(true);

            if (isset($app['redbean.helpers']) && isset($app['redbean.model.helpers'])) {
                $modelHelper = new ModelHelper($app['redbean.helpers']);
                $modelHelper->attachEventListeners($rb->getRedBean());

                foreach ($app['redbean.model.helpers'] as $model => $options) {
                    $modelHelper->addModel($model, $options);
                }

                $app['redbean.model_helper'] = function () use ($modelHelper) {
                    return $modelHelper;
                };
            }

            return $rb;
        };
    }

    /**
     * Bootstraps the application
     *
     * @return void
     */
    public function boot(Application $app)
    {
    }

    /**
     * Sets RedBean Model namespace
     *
     * @return void
     */
    protected function setModelNamespace($namespace)
    {
        if (!defined('REDBEAN_MODEL_PREFIX')) {
            define('REDBEAN_MODEL_PREFIX', $namespace);
        }
    }
}
