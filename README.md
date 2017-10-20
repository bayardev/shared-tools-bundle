Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require bayardev/shared-tools-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.


Step 2: Create VERSION file
---------------------------

Create an empty file named VERSION in root dir of the project


Step 3: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php
// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Bayard\Bundle\SharedToolsBundle\BayardSharedToolsBundle(),
        );
        // ...
    }
    // ...
}
```

## Use of ScriptHandler::checkDoctrineMigrations

Before you can put this ScriptHandler in composer.json of your project
ensure that you have in bundles list in APPKernel.php **DoctrineMigrationsBundle** :

```php
<?php
// app/AppKernel.php
// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        );
        // ...
    }
    // ...
}
```

Than you can add this in composer.json like this :

```json
{
    /*...*/
    "scripts": {
            /*...*/
            "post-install-cmd": [
                "@symfony-scripts",
                "Bayard\\Bundle\\SharedToolsBundle\\Composer\\ScriptHandler::checkDoctrineMigrations"
            ]
             /*...*/
        }
    /*...*/
}
```

## Use of syslog handler

Just add in your config.yml or/and in config_dev.yml, config_prod.yml theses parameters :

```yaml
monolog:
    handlers:
        #...
        syslog:
            type: syslog
            level: error
            ident: app
            facility: local7
            formatter: bayardlog.formatter.syslog_line
```

Be free to chnage **level** parameter but **you must leave intouched the others parameters for all Bayard Projects** ...

