<?php namespace System\Modules\Auth;

function listUserGeneralSettings_ALL(\Web $w, $user_id) {
    $user;
    empty($user_id) ? $user = $w->Auth->user() : $user = $w->Auth->getUser($user_id);
    $contact_array = $user->getContact()->toArray();

    $w->ctx('user_array', $user->toArray());
    $w->ctx('contact_array', $contact_array);
}