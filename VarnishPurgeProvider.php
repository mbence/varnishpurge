<?php
/**
 * Varnish Purge - a Silex Framework Provider
 *
 * @usage

 // register the Varnish Purge provider
$app->register(new MBence\VarnishPurge\VarnishPurgeProvider(), array(
    'varnish.options' => array(
        'servers'   => array('127.0.0.1:8080'),
        'purge'     => 'On'
    ),
));

// call the service, purge the /hello from Varnish servers
$app['varnish']->purge('/hello');

 */

namespace MBence\VarnishPurge;

use Silex\Application;
use Silex\ServiceProviderInterface;

class VarnishPurgeProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['varnish'] = $app->share(function () use ($app) {
            return new VarnishPurger($app);
        });
    }

    public function boot(Application $app)
    {
    }
}
