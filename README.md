Varnish Purge Provider for Silex
================================

Easy to use service to purge Varnish keys.

## Description

This provider will create a PURGE request for the predefined Varnish servers, thus invalidating the given url.

https://www.varnish-cache.org/docs/3.0/tutorial/purging.html#http-purges


## Prerequisites

This Provider requires Sylex and Varnish-Cache ~v3.0.
From the Varnish docs:
```VCL
acl purge {
        "localhost";
        "192.168.55.0"/24;
}

sub vcl_recv {
        # allow PURGE from localhost and 192.168.55...

        if (req.request == "PURGE") {
                if (!client.ip ~ purge) {
                        error 405 "Not allowed.";
                }
                return (lookup);
        }
}

sub vcl_hit {
        if (req.request == "PURGE") {
                purge;
                error 200 "Purged.";
        }
}

sub vcl_miss {
        if (req.request == "PURGE") {
                purge;
                error 200 "Purged.";
        }
}
```
## Installation

### Step 1: Download the bundle using composer

Add the following in your composer.json:

```json
{
    "require": {
        "mbence/varnishpurge": "dev-master"
    }
}
```

Then download / update by running the command:

``` bash
$ php composer.phar update mbence/varnishpurge
```

Composer will install the bundle to your project's `vendor/mbence/varnishpurge` directory.

### Step 2: Register the provider

``` php
$app->register(new MBence\VarnishPurge\VarnishPurgeProvider(), array(
    'varnish.options' => array(
        'servers'   => array('127.0.0.1:8080'),
        'purge'     => 'On'
    ),
));
```
You can turn the purge off with 'purge' => 'Off'

### Step 3: Call the service

``` php
$app['varnish']->purge('/hello');
```
