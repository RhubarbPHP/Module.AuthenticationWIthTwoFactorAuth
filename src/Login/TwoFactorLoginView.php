<?php

namespace Rhubarb\AuthenticationWithTwoFactorAuth\Login;

use Rhubarb\Leaf\Controls\Common\Buttons\Button;
use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Scaffolds\Authentication\Leaves\LoginView;

/**
 * Class TwoFactorLoginView
 * @package Rhubarb\AuthenticationWithTwoFactorAuth\Login
 */
class TwoFactorLoginView extends LoginView
{
    /** @var TwoFactorLoginModel */
    protected $model;

    public function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            $code = new TextBox('Code'),
            new Button('Confirm', 'Confirm', function () {
                $this->model->verifyCodeEvent->raise();
            }),
            $resend = new Button('Resend', 'Resend code', function () {
                $this->model->resendCodeEvent->raise();
            })
        );

        $code->addHtmlAttribute('autofocus', 'autofocus');
        $resend->addCssClassNames('u-red', 'u-micro', 'u-link');
    }

    final public function printViewContent()
    {
        if ($this->model->promptForCode) {
            $this->printTwoFactorContent();
        } else {
            $this->printLoginContent();
        }
    }

    public function printTwoFactorContent()
    {
        if ($this->model->codeAttempted && !$this->model->twoFactorVerified) {
            print '<div class="c-alert c-alert--error">Sorry, this code does not match the one we sent you, please check and try again.</div>';
        }

        ?>
        <div class="c-alert">A text message with a 6-digit verification code was just sent to your phone. Please
            enter
            the code below. <?= $this->model->verificationCode ?>
        </div>
        <fieldset class="c-form c-form--inline">
            <div class="c-form__group">
                <label class="c-form__label">Verification Code</label>
                <?= $this->leaves['Code']; ?>
            </div>

            <div class="c-form__actions">
                <?= $this->leaves['Confirm']; ?>
                <p class="u-micro u-marg-top u-align-center">
                    <span class="u-lighten">Didn't get a code? <?= $this->leaves['Resend'] ?></span>
                </p>
            </div>
        </fieldset>

        <?php
    }

    public function printLoginContent()
    {
        parent::printViewContent();
    }
}