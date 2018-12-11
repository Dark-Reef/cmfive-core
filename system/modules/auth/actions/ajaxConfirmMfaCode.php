<?php

function ajaxConfirmMfaCode_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    if (empty($posts->user_id) || empty($posts->mfa_code)) {
        $w->out((new AxiosResponse())->setErrorResponse('Unable to confirm MFA Code'));
        return;
    }

    if ($w->Auth->user()->id !== $posts->user_id) {
        $w->out((new AxiosResponse())->setErrorResponse('Not authorization to add MFA'));
        return;
    }

    $secret = $w->Auth->user()->mfa_secret;
    if (empty($secret)) {
        $w->out((new AxiosResponse())->setErrorResponse('Unable to confirm MFA Code'));
        return;
    }

    $authenticator = new \GoogleAuthenticator\GoogleAuthenticator();
    if ($authenticator->checkCode($secret, $posts->mfa_code)) {
        
    }
}