<?php namespace System\Modules\Admin;

function listUserSecuritySettings_ALL(\Web $w, $user_id) {
    \VueComponentRegister::registerComponent('Modal', new \VueComponent('Modal', '/system/templates/vue-components/modal.vue.js'));

    $user;
    empty($user_id) ? $user = $w->Auth->user() : $user = $w->Auth->getUser($user_id);
    $w->ctx('user_array', $user->toArray());
}