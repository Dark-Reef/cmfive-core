<?php namespace System\Modules\Admin;

function listUserGeneralSettings_ALL(\Web $w, $user_id) {
    empty($user_id) ? $w->ctx("user_array", $w->Auth->user()->toArray()) : $w->ctx("user_array", $w->Auth->getUser($user_id)->toArray());
}