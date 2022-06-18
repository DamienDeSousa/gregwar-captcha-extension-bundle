<?php

namespace Dades\GregwarCaptchaExtensionBundle;

use Dades\GregwarCaptchaExtensionBundle\DependencyInjection\DadesGregwarCaptchaExtensionExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
