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
6. Clear the symfony cache

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

### Step 3: Import PhlexibleElementFinderBundle routing

Import the PhlexibleElementFinderBundle routing.

For frontend:

``` yaml
# app/config/routing.yml
phlexible_element_finder_render:
    resource: "@PhlexibleElementFinderBundle/Controller/RenderController.php"
    type:     annotation
```

For administration backend:

``` yaml
# app/config/admin_routing.yml
phlexible_element_finder_catch:
    resource: "@PhlexibleElementFinderBundle/Controller/CatchController.php"
    type:     annotation
```

### Step 4: Enable puli resources

The PhlexibleElementFinderBundle provides puli bindings for phlexible scripts, styles and icons. These need to be activated.

``` bash
$ bin/puli bind --enable d80e5f # enable binding for phlexible/scripts
$ bin/puli bind --enable 0ccc61 # enable binding for phlexible/styles
$ bin/puli bind --enable 6ce2d4 # enable binding for phlexible/icons
```

### Step 5: Update your database schema

Now that the bundle is set up, the last thing you need to do is update your database schema because the element finder includes entities that need to be installed in your database.

For ORM run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 6: Clear the symfony cache

``` bash
$ php app/console cache:clear --env=prod
```
