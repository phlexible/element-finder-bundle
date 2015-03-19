PhlexibleElementFinderBundle
============================

The PhlexibleElementFinderBundle adds support for a finder field in phlexible.

Installation
------------

Installation is a 5 step process:

1. Download PhlexibleElementFinderBundle using composer
2. Enable the Bundle
3. Import PhlexibleElementFinderBundle routing
4. Enable puli resources
5. Update your database schema


### Step 1: Download PhlexibleElementFinderBundle using composer

Add PhlexibleElementFinderBundle by running the command:

``` bash
$ php composer.phar require phlexible/element-finder-bundle "~1.0.0"
```

Composer will install the bundle to your project's `vendor/phlexible` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Phlexible\Bundle\PhlexibleElementFinderBundle(),
    );
}
```

### Step 3: Import PhlexibleElementFinderBundle routing files

Now that you have activated the bundle, you have to import the PhlexibleElementFinderBundle routes.

In YAML:

``` yaml
# app/config/routing.yml
phlexible_element_finder_render:
    resource: "@PhlexibleElementFinderBundle/Controller/RenderController.php"
    type:     annotation
```

``` yaml
# app/config/admin_routing.yml
phlexible_element_finder_catch:
    resource: "@PhlexibleElementFinderBundle/Controller/CatchController.php"
    type:     annotation
```

### Step 4: Enable puli resources

Now that the bundle is configured, we have the tell phlexible how to load the resource that the the PhlexibleElementFinderBundle provides. phlexible uses puli types to manage resources.

``` bash
$ bin/puli bind --enable d80e5f # enable PhlexibleElementFinderBundle scripts
$ bin/puli bind --enable 0ccc61 # enable PhlexibleElementFinderBundle styles
$ bin/puli bind --enable 6ce2d4 # enable PhlexibleElementFinderBundle icons
```

### Step 5: Update your database schema

Now that the bundle is set up, the last thing you need to do is update your database schema because the element finder includes entities that need to be installed in your database.

For ORM run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```
