<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	$redirect_url = $w->request("redirect_url");
	$object_class = $w->request("object_class");
	$object_id = $w->request("object_id");
	
	if (empty($form_id) && empty($p['id'])) {
		$w->msg("Form instance data missing");
		return;
	}
	
	$instance = null;
	$form = null;
	if (!empty($p['id'])) {
		$instance = $w->Form->getFormInstance($p['id']);
		$form = $instance->getForm();
	} else {
		$form = $w->Form->getForm($form_id);
		$instance = new FormInstance($w);
		$instance->form_id = $form_id;
	}
	
	$w->out(Html::multiColForm($instance->getEditForm($form), 
			'/form-instance/edit/' . $instance->id . "?form_id=" . $form_id . "&redirect_url=" . $redirect_url . "&object_class=" . $object_class . "&object_id=" . $object_id));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	$redirect_url = $w->request("redirect_url");
	$object_class = $w->request("object_class");
	$object_id = $w->request("object_id");
	$form=null;
	if (empty($form_id) && empty($p['id'])) {
		$w->msg("Form instance data missing");
		return;
	}

	// Remove CSRF if it exists
	if (array_key_exists(CSRF::getTokenID(), $_POST)) {
		unset($_POST[CSRF::getTokenID()]);
	}

	try {
		$form_instance = $w->Form->saveForm($form_id,$_POST,$_FILES,$p['id'],$object_class,$object_id);
		
	} catch (Exception $e) {
		echo "<pre>";
		var_dump($e); die;
	}
	$form = $form_instance->getForm();
	
	// $instance = null;
	// $form = null;
	// if (!empty($p['id'])) {
	// 	$instance = $w->Form->getFormInstance($p['id']);
	// 	$form = $instance->getForm();
	// } else {
	// 	$form = $w->Form->getForm($form_id);
	// 	$instance = new FormInstance($w);
	// 	$instance->form_id = $form_id;
	// }
	
	// $instance->object_class = $object_class;
	// $instance->object_id = $object_id;
	// $instance->insertOrUpdate();
	
	// // Remove CSRF if it exists
	// if (array_key_exists(CSRF::getTokenID(), $_POST)) {
	// 	unset($_POST[CSRF::getTokenID()]);
	// }

	// // Get existing values to update
	// $instance_values = $instance->getSavedValues();
	// if (!empty($instance_values)) {
	// 	foreach($instance_values as $instance_value) {
	// 		$field = $instance_value->getFormField();
			
	// 		if (array_key_exists($field->technical_name, $_POST)) {
	// 			$instance_value->value = $_POST[$field->technical_name];
	// 			$instance_value->update();
	// 			unset($_POST[$field->technical_name]);
	// 		} else if (array_key_exists($field->technical_name, $_FILES)) {
	// 			// Used for attachment field types
	// 			// Trigger update to allow the modifyForPersistance to take care of attachment uploads
	// 			$instance_value->update();
	// 			unset($_FILES[$field->technical_name]);
	// 		} else {
	// 			$instance_value->delete();
	// 		}
	// 	}
	// }

	// // Add new POST values
	// if (!empty($_POST)) {
	// 	foreach($_POST as $key => $value) {
	// 		$field = $w->Form->getFormFieldByFormIdAndTitle($form->id, $key);
	// 		// if post variables don't match form fields, ignore them
	// 		if (!empty($field)) {
	// 			$instance_value = new FormValue($w);
	// 			$instance_value->form_instance_id = $instance->id;
	// 			$instance_value->form_field_id = $field->id;
	// 			$instance_value->value = $value;
	// 			$instance_value->insert();
	// 		}
	// 	}
	// }

	// // Add new FILE values
	// if (!empty($_FILES)) {
	// 	foreach($_FILES as $key => $value) {
	// 		$field = $w->Form->getFormFieldByFormIdAndTitle($form->id, $key);
	// 		// if post variables don't match form fields, ignore them
	// 		if (!empty($field)) {
	// 			$instance_value = new FormValue($w);
	// 			$instance_value->form_instance_id = $instance->id;
	// 			$instance_value->form_field_id = $field->id;
	// 			$instance_value->value = ''; // Attachment types will set the value in the Interface
	// 			$instance_value->insert();
	// 		}
	// 	}
	// }

	$w->msg($form->title . (!empty($p['id']) ? " updated" : " created"), $redirect_url . "#".toSlug($form->title));
}
