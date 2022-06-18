<?php

/**
 * Define the Captcha login form decorator which calculate the number of time that the admin login form is submitted.
 *
 * @author    Damien DE SOUSA <dades@gmail.com>
 * @copyright 2020 Damien DE SOUSA
 */

declare(strict_types=1);

namespace Dades\GregwarCaptchaExtensionBundle\Decorator\Security\Admin;

use Dades\GregwarCaptchaExtensionBundle\Security\Admin\Login\Captcha;
use Dades\GregwarCaptchaExtensionBundle\Validator\Captcha\CaptchaValidator;
use Dades\FosUserExtensionBundle\Security\Admin\LoginFormAuthenticator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class CaptchaLoginFormDecorator extends AbstractFormLoginAuthenticator
{
    private bool $isCaptchaValid;

    #[Pure]
    public function __construct(
        private CaptchaValidator $captchaValidator,
        private Captcha $captchaEnabler,
        private LoginFormAuthenticator $loginFormAuthenticator,
    ) {
        $this->isCaptchaValid = true;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool
    {
        $isSupportedRequest = $this->loginFormAuthenticator->supports($request);
        if ($isSupportedRequest) {
            $this->captchaEnabler->setLoginPageDisplayed($request);
        }

        return $isSupportedRequest;
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape([
        'username' => "string",
        'password' => "string",
        'csrf_token' => "string",
        'captchaCode' => "string"
    ])]
    public function getCredentials(Request $request): array
    {
        $credentials = $this->loginFormAuthenticator->getCredentials($request);
        if ($this->captchaEnabler->isCaptchaDisplayed($request)) {
            $credentials['captchaCode'] = $request->request->get('captchaCode');
        }

        return $credentials;
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->loginFormAuthenticator->getUser($credentials, $userProvider);
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $isCredentialsValid = $this->loginFormAuthenticator->checkCredentials($credentials, $user);

        if (isset($credentials['captchaCode'])) {
            $captchaCode = $credentials['captchaCode'];
            $this->isCaptchaValid = $this->captchaValidator->validate($captchaCode);
            $isCredentialsValid = $isCredentialsValid && $this->isCaptchaValid;
        }

        return $isCredentialsValid;
    }

    public function getPassword($credentials): ?string
    {
        return $this->loginFormAuthenticator->getPassword($credentials);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        if (!$this->isCaptchaValid) {
            $exception = new CustomUserMessageAuthenticationException('security.captcha.error.message');
        }

        return $this->loginFormAuthenticator->onAuthenticationFailure($request, $exception);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $this->captchaEnabler->unsetSessionPageDisplayed($request);
        $this->captchaEnabler->removeSessionCaptchaPhrase($request->getSession());

        return $this->loginFormAuthenticator->onAuthenticationSuccess($request, $token, $providerKey);
    }

    protected function getLoginUrl(): string
    {
        return $this->getLoginUrl();
    }
}
