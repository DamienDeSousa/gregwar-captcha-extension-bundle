<?php

/**
 * Defines DadesGregwarCaptchaExtensionBundle class.
 *
 * @author Damien DE SOUSA
 */

declare(strict_types=1);

namespace Dades\GregwarCaptchaExtensionBundle;

use Dades\GregwarCaptchaExtensionBundle\DependencyInjection\DadesGregwarCaptchaExtensionExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Defines the bundle.
 */
class DadesGregwarCaptchaExtensionBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface|null
    {
        if (null === $this->extension) {
            $this->extension = new DadesGregwarCaptchaExtensionExtension();
        }

        return $this->extension;
    }
}
