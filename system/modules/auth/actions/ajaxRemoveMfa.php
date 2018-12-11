<?php

function ajaxRemoveMfa_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    if (empty($posts->user_id)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to remove MFA'));
        return;
    }

    $user = $w->Auth->user();
    if ($user->id !== $posts->user_id) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Not authorization to remove MFA'));
        return;
    }

    $user->mfa_secret = null;
    $user->is_mfa_enabled = false;
    if ($user->update(true)) {
        $w->out((new AxiosResponse())->setSuccessfulResponse('OK', null));
        return;
    }
    $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to remove MFA'));
}