<?php

/**
 * Defines DadesGregwarCaptchaExtensionExtension class.
 *
 * @author Damien DE SOUSA
 */

declare(strict_types=1);

namespace Dades\GregwarCaptchaExtensionBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Loads config for the bundle.
 */
class DadesGregwarCaptchaExtensionExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    public function getAlias(): string
    {
        return 'dades_greg_captcha_extension';
    }
}
