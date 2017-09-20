<?php

/**
 * Display User edit form
 *
 * @param <type> $w
 */
function useredit_GET(Web &$w) {
	$p = $w->pathMatch("id", "box");
	$user = $w->Auth->getObject("User", $p["id"]);
	$w->ctx('availableLocales', $w->getAvailableLanguages());

	if ($user) {
		$w->Admin->navigation($w, "Administration - Edit User - " . $user->login);
	} else {
		if (!$p['box']) {
			$w->error("User " . $w->ctx("id") . " does not exist.", "/admin/users");
		}
	}
	$w->ctx("user", $user);

	// no layout if displayed in a box
	if ($p['box']) {
		$w->setLayout(null);
	}
}

/**
 * Handle User Edit form submission
 *
 * @param <type> $w
 */
function useredit_POST(Web &$w) {
	$w->pathMatch("id");
	$errors = $w->validate(array(
		array("login", ".+", "Login is mandatory"),
	));
	if ($_REQUEST['password'] && ($_REQUEST['password'] != $_REQUEST['password2'])) {
		$error[] = "Passwords don't match";
	}
	$user = $w->Auth->getObject("User", $w->ctx('id'));
	if (!$user) {
		$errors[] = "User does not exist";
	}
	if (sizeof($errors) != 0) {
		$w->error(implode("<br/>\n", $errors), "/admin/useredit/" . $w->ctx("id"));
	}
	$user->login = $_REQUEST['login'];

	$user->fill($_REQUEST);
	if ($_REQUEST['password']) {
		$user->setPassword($_REQUEST['password']);
	} else {
		$user->password = null;
	}
	$user->is_admin = isset($_REQUEST['is_admin']) ? 1 : 0;
	$user->is_active = isset($_REQUEST['is_active']) ? 1 : 0;
	$user->update();

	$contact = $user->getContact();
	if ($contact) {
		$lookup_exists = false;
		$contact->fill($_REQUEST);
		$contact->private_to_user_id = null;
		$contact->title = $_POST['acp_title'];
		if ($_POST['acp_title']) {
			foreach ($w->Admin->getAllLookup(["type" => 'title']) as $lookup) {
				if ($lookup->title == $_POST['acp_title']) {
					$lookup_exists = true;
					break; // found one, don't make a new lookup
				}
			} 
			if (!$lookup_exists) {
				$lookup = new Lookup($w);
				$lookup->fill(['type'=>'title', 'code'=>$contact->title, 'title'=>$contact->title]);
				$lookup->insert();
			}
		}
		$contact->update();
	}
	$w->callHook("admin", "account_changed", $user);

	$w->msg("<div id='saved_record_id' data-id='" . $user->id . "' >User " . $user->login . " updated.</div>", "/admin/users");
}
