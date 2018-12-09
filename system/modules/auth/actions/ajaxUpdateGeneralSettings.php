<?php

function ajaxUpdateGeneralSettings_POST(Web $w) {
    $w->setLayout(null);
    $posts = json_decode(file_get_contents('php://input'));
    $errors = [];

    if (!preg_match('/^[0-9+\- ]*$/', $posts->contact->home_phone)) {
        $errors[] = 'Invalid home phone number';
    }

    if (!preg_match('/^[0-9+\- ]*$/', $posts->contact->work_phone)) {
        $errors[] = 'Invalid work phone number';
    }

    if (!preg_match('/^[0-9+\- ]*$/', $posts->contact->private_mobile)) {
        $errors[] = 'Invalid private mobile number';
    }

    if (!preg_match('/^[0-9+\- ]*$/', $posts->contact->work_mobile)) {
        $errors[] = 'Invalid work mobile number';
    }

    if (!filter_var($posts->contact->email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address';
    }

    if (!preg_match('/^[0-9+\- ]*$/', $posts->contact->fax)) {
        $errors[] = 'Invalid fax number';
    }

    $contact = $w->Auth->getContact($posts->contact->id);
    if (empty($contact)) {
        $errors[] = 'Unable to find user';
    }

    $user = $contact->getUser();
    if (empty($user)) {
        $errors[] = 'Unable to find user';
    }

    if ($w->Auth->user()->id != $user->id || !$w->Auth->user()->is_admin) {
        $errors[] = 'Not authorized to update user';
    }

    if (count($errors) != 0) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ['errors' => $errors]));
        return;
    }

    $contact->firstname = $posts->contact->first_name;
    $contact->lastname = $posts->contact->last_name;
    $contact->homephone = $posts->contact->home_phone;
    $contact->workphone = $posts->contact->work_phone;
    $contact->priv_mobile = $posts->contact->private_mobile;
    $contact->mobile = $posts->contact->work_mobile;
    $contact->email = $posts->contact->email;
    $contact->fax = $posts->contact->fax;
    
    if ($contact->update()) {
        $w->out((new AxiosResponse())->setSuccessfulResponse('OK', null));
        return;
    }
    $w->out((new AxiosResponse())->setErrorResponse(null, ['errors' => 'Failed to update user']));
}