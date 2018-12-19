<?php

function editUser_GET(Web $w) {
    list($user_id) = $w->pathMatch('id');
    if (empty($user_id)) {
        $w->ctx("user", $w->Auth->user());
    } else {
        $w->ctx("user", $w->Auth->getUser($user_id));
    }

    CmfiveScriptComponentRegister::registerComponent('Axios', new CmfiveScriptComponent('/system/templates/js/axios.min.js'));
    CmfiveScriptComponentRegister::registerComponent('ToastJS', new CmfiveScriptComponent('/system/templates/js/Toast.js'));

    CmfiveStyleComponentRegister::registerComponent('ToastSCSS', new CmfiveStyleComponent('/system/templates/css/Toast.scss', ['/system/templates/scss/']));
}