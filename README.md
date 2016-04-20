# redbean-silex-provider
A RedBean service provider for Silex

## Provider

    // src/app.php

    use Fazb\RedBean\RestServiceProvider

    $app['data_dir'] = __DIR__ . '/../var/data';

    $app['rb.options'] = array(
        'dsn'       => sprintf('sqlite://%s/app.db', $app['data_dir']),
        'namespace' => 'Site\\Model\\',
        'freeze'    => !$app['debug']
    );


    $app->register(new RedBeanServiceProvider());

## Model

    <?php

    namespace Site\Model;

    use Fazb\RedBean\Model\BaseModel;

    /**
    * Contact
    */
    class Contact extends BaseModel
    {
        public function fullName()
        {
            return sprintf('%s %s', $this->firstName, $this->lastName);
        }
    }

## Model Helper

sluggable, timestampable event based

    $app['redbean.model.helpers'] = array(
        'article' => array(
            'sluggable' => array(
                'events' => array(
                    'update' => array('title')
                ),
                // Default
                'options' => array(
                    'fieldName' => 'slug'
                )
            ),
            'timestampable' => array(
                // Default
                'events' => array(
                    'dispense'  => array('created_at'),
                    'update'    => array('updated_at')
                ),
                'options' => array(
                    'dateFormat' => 'Y-m-d H:i:s'
                )
            )
        )
    );
