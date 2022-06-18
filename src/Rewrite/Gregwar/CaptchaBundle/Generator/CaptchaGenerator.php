<?php

/**
 * Defines the CaptchaGenerator class.
 *
 * @author Damien DE SOUSA
 */

declare(strict_types=1);

namespace Dades\GregwarCaptchaExtensionBundle\Rewrite\Gregwar\CaptchaBundle\Generator;

use Dades\GregwarCaptchaExtensionBundle\Security\Admin\Login\Captcha;
use Gregwar\Captcha\CaptchaBuilderInterface;
use Gregwar\Captcha\PhraseBuilderInterface;
use Gregwar\CaptchaBundle\Generator\CaptchaGenerator as BaseCaptchaGenerator;
use Gregwar\CaptchaBundle\Generator\ImageFileHandler;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Generate captcha code and set it into session.
 */
class CaptchaGenerator extends BaseCaptchaGenerator
{
    #[Pure]
    public function __construct(
        private Captcha $captcha,
        RouterInterface $router,
        CaptchaBuilderInterface $builder,
        PhraseBuilderInterface $phraseBuilder,
        ImageFileHandler $imageFileHandler,
        private SessionInterface $session
    ) {
        parent::__construct($router, $builder, $phraseBuilder, $imageFileHandler);
    }

    public function getCaptchaCode(array &$options): string
    {
        $this->builder->setPhrase($this->getPhrase($options));

        $this->captcha->setSessionCaptchaPhrase($this->session, $options['phrase']);

        // Randomly execute garbage collection and returns the image filename
        if ($options['as_file']) {
            $this->imageFileHandler->collectGarbage();

            return $this->generate($options);
        }

        // Returns the image generation URL
        if ($options['as_url']) {
            return $this->router->generate(
                'gregwar_captcha.generate_captcha',
                ['key' => $options['session_key'], 'n' => md5(microtime(true) . mt_rand())]
            );
        }

        return 'data:image/jpeg;base64,' . base64_encode($this->generate($options));
    }
}
