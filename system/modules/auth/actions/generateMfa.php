<?php

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

function confirmMfa_GET(Web $w) {
    $user = $w->Auth->user();
    if (empty($user)) {
        return;
    }

    $authenticator = new GoogleAuthenticator();
    $user->mfa_secret = $authenticator->generateSecret();
    $user->update();
    $w->ctx('mfa_qr_code', '<img src="' . GoogleQrUrl::generate($user->getFullName(), $user->mfa_secret, '2Pi Software CRM') . '"/>');
}