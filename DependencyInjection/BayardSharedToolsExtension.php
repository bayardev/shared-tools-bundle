<?php

namespace Bayard\Bundle\SharedToolsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class BayardSharedToolsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        //$loader->load('doctrine_migrations.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');
        // determine if DoctrineMigrationsBundle is registered
        if (!isset($bundles['DoctrineMigrationsBundle'])) {
            throw new \Exception("You have to load DoctrineMigrationsBundle in app/AppKernel.php");
        } else {
            // load config for DoctrineMigrationsBundle
            $config = array(
                'dir_name' => "%kernel.root_dir%/../upgrades/schema",
                'namespace' => 'Application\Migrations',
                'table_name' => 'migration_versions',
                'name' => 'Application Migrations',
                'organize_migrations' => false
            );

            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'doctrine_migrations':
                        $container->prependExtensionConfig($name, $config);
                        break;
                }
            }
        }

        // process the configuration of BayardSharedToolsExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        // use the Configuration class to generate a config array with
        // the settings for "doctrine_migrations"
        $config = $this->processConfiguration(new Configuration(), $configs);
    }

}
