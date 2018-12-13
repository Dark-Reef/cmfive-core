<?php

function ajaxGetMfaQrCode_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    if (empty($posts->user_id)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to find user'));
        return;
    }

    if ($w->Auth->user()->id != $posts->user_id || !$w->Auth->user()->is_admin) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Not authorized to update user'));
        return;
    }

    $authenticator = new \Google\Authenticator\GoogleAuthenticator();
    $secret = $authenticator->generateSecret();

    $user = $w->Auth->getUser($posts->user_id);
    $user->mfa_secret = $secret;
    $user->update();

    $link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($user->getFullName(), $secret, '2Pi Software CRM');
    $w->out((new AxiosResponse())->setSuccessfulResponse('OK', ['mfa_qr_code' => '<img src="'. $link .'" />']));
}