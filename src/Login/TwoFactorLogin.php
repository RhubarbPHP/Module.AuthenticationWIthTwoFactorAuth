<?php

namespace Rhubarb\AuthenticationWithTwoFactorAuth\Login;

use Rhubarb\AuthenticationWithTwoFactorAuth\LoginProviders\TwoFactorLoginProvider;
use Rhubarb\Crown\Events\Event;
use Rhubarb\Scaffolds\Authentication\Leaves\Login;

/**
 * Class TwoFactorLogin
 * @package Rhubarb\AuthenticationWithTwoFactorAuth\Login
 * @property TwoFactorLoginModel $model
 */
class TwoFactorLogin extends Login
{
    protected $codePrompt = false;

    protected function createModel()
    {
        return new TwoFactorLoginModel();
    }

    protected function onModelCreated()
    {
        parent::onModelCreated();
        $this->model->loginProvider = TwoFactorLoginProvider::class;
        $this->model->verifyCodeEvent = new Event();
        $this->model->verifyCodeEvent->attachHandler(function () {
            /** @var TwoFactorLoginProvider $loginProviderClass */
            $loginProvider = TwoFactorLoginProvider::singleton();
            $loginProvider->validateCode($this->model->Code);
            $this->onSuccess();
        });
    }


    protected function getViewClass()
    {
        return TwoFactorLoginView::class;
    }

    protected function onSuccess()
    {
        /** @var TwoFactorLoginProvider $loginProviderClass */
        $loginProviderClass = $this->loginProviderClassName;
        $loginProvider = $loginProviderClass::singleton();
        if (!$loginProvider->isTwoFactorVerified()) {
            $loginProvider->createAndSendCode();
            $this->model->promptForCode = true;
            return clone $this;
            // rerender with code input view
        } else {
            parent::onSuccess();
        }
    }
}