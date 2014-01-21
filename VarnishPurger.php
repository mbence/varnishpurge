<?php

namespace MBence\VarnishPurge;

use Silex\Application;

class VarnishPurger
{
    private $app;
    private $options;

    /**
     * Dependency Injection
     *
     * @param \Silex\Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
        $this->options = !empty($app['varnish.options']) ? $app['varnish.options'] : array();
    }

    public function purge($url)
    {
        if (!empty($this->options['servers'])) {
            if (!is_array($this->options['servers'])) {
                $this->options['servers'] = array($this->options['servers']);
            }
            // purge all varnish servers
            foreach ($this->options['servers'] as $server) {
                // find the host and port
                if (false !== strpos($server, ':')) {
                    list($hostname, $port) = explode(':', $server, 2);
                }
                else {
                    $hostname = $server;
                    $port = 80;
                }
                // purge
                $this->purgeURL( $hostname, $port, $url, true );
            }
        }
        return $url;
    }

    private function purgeURL( $hostname, $port, $purgeURL, $debug = false )
    {
        $finalURL = sprintf(
            "http://%s:%d%s", $hostname, $port, $purgeURL
        );

        print( "Purging ${finalURL}\n" );

        $curlOptionList = array(
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CUSTOMREQUEST     => 'PURGE',
            CURLOPT_HEADER            => true ,
            CURLOPT_NOBODY            => true,
            CURLOPT_URL               => $finalURL,
            CURLOPT_CONNECTTIMEOUT_MS => 2000
        );

        $fd = false;
        if( $debug == true ) {
            print "\n---- Curl debug -----\n";
            $fd = fopen("php://output", 'w+');
            $curlOptionList[CURLOPT_VERBOSE] = true;
            $curlOptionList[CURLOPT_STDERR]  = $fd;
        }

        $curlHandler = curl_init();
        curl_setopt_array( $curlHandler, $curlOptionList );
        curl_exec( $curlHandler );
        curl_close( $curlHandler );
        if( $fd !== false ) {
            fclose( $fd );
        }
    }
}
