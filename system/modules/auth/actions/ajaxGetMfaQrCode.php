<?php

function ajaxGetMfaQrCode_GET(Web $w) {
    $w->setLayout(null);

    $user = $w->Auth->user();

    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse('User not logged in.'));
        return;
    }

    $authenticator = new \Google\Authenticator\GoogleAuthenticator();
    $secret = $authenticator->generateSecret();

    $user = $w->Auth->user();
    $user->mfa_secret = $secret;
    $user->update();

    $link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($user->getFullName(), $secret, '2Pi Software CRM');
    $w->out((new AxiosResponse())->setSuccessfulResponse('OK', ['mfa_qr_code' => '<img src="'. $link .'" />']));
}