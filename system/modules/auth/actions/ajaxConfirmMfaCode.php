<?php

function ajaxConfirmMfaCode_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    if (empty($posts->user_id) || empty($posts->mfa_code)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to confirm MFA Code'));
        return;
    }

    $user = $w->Auth->user();
    if ($user->id !== $posts->user_id) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Not authorization to add MFA'));
        return;
    }

    $secret = $w->Auth->user()->mfa_secret;
    if (empty($secret)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to confirm MFA Code'));
        return;
    }

    $authenticator = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    if ($authenticator->checkCode($secret, $posts->mfa_code)) {
        $user->is_mfa_enabled = true;
        if ($user->update()) {
            $w->out((new AxiosResponse())->setSuccessfulResponse('OK', null));
            return;
        }
    }
    $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to confirm MFA Code'));
}