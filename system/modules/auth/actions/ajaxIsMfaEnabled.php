<?php

function ajaxIsMfaEnabled_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    if (empty($posts->user_login)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to find user'));
        return;
    }

    $is_mfa_enabled = $w->Auth->isMfaEnabled($posts->user_login);
    if ($is_mfa_enabled === null) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to find user'));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse('OK', ['is_mfa_enabled' => $is_mfa_enabled]));
}