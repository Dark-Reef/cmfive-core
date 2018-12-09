<?php

function ajaxChangePassword_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));

    // Check that none of the post data is empty.
    if (empty($posts->user_id) || empty($posts->user_login) ||
        empty($posts->old_password) || empty($posts->new_password) || empty($posts->confirm_new_password)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to find user'));
        return;
    }

    // Check that the new password and confirm new password are the same.
    if ($posts->new_password !== $posts->confirm_new_password) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'New passwords do not match'));
        return;
    }

    // Check that the new password not an empty string.
    if (strlen($posts->new_password) == 0) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Password cannot be empty'));
        return;
    }

    // Check that the logged in User is authorized to edit this User.
    if ($w->Auth->user()->id != $posts->user_id || !$w->Auth->user()->is_admin) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Not authorized to change password'));
        return;
    }

    // Get User from the database.
    $user = $w->Auth->getUserForLogin($posts->user_login);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Unable to find user'));
        return;
    }

    // Check if the old password matches the one in the database.
    if ($user->encryptPassword($posts->old_password) !== $user->password) {
        $w->out((new AxiosResponse())->setErrorResponse(null, 'Old passwords do not match'));
        return;
    }

    // Change password and send response.
    $user->setPassword($posts->new_password);
    if ($user->update()) {
        $w->out((new AxiosResponse())->setSuccessfulResponse('OK', null));
        return;
    }
    $w->out((new AxiosResponse())->setErrorResponse(null, ['errors' => 'Failed to update user']));
}