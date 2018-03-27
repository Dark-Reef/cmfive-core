<?php

function import_GET(Web $w) {

	$w->ctx('title',"Form Import");

	$_form = [
		'Select application zip file' => [
			[(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")], // ["File", "file", "file"]
			[[" Application title override (Optional)", "text", "title_override"]]
		]
	];

	$w->ctx("form", Html::multiColForm($_form, "/form-application/import", "POST", "Save"));
}

function import_POST(Web $w) {


	if(isset($_FILES['file'])) {
	    $filename = $_FILES['file']['name'];
	    $source = $_FILES['file']['tmp_name'];
	    $type = $_FILES['file']['type']; 
	     
	    $name = explode('.', $filename); 
	    //check for form dir in uploads
	    if (!is_dir(ROOT_PATH .'/uploads/form')) {
	    	mkdir(ROOT_PATH .'/uploads/form/');
	    }
	    $target = ROOT_PATH .'/uploads/form/' . $name[0] . '-' . time() . '/';  
	     
	    // Ensures that the correct file was chosen
	    $accepted_types = array('application/zip', 
	                                'application/x-zip-compressed', 
	                                'multipart/x-zip', 
	                                'application/s-compressed');
	 
	    foreach($accepted_types as $mime_type) {
	        if($mime_type == $type) {
	            $okay = true;
	            break;
	        } 
	    }
	       
	  //Safari and Chrome don't register zip mime types. Something better could be used here.
	    $okay = strtolower($name[1]) == 'zip' ? true: false;
	 
	    if(!$okay) {
	          $w->error("Please choose a zip file","/form-application");       
	    }
	    
	    mkdir($target);
	    $saved_file_location = $target . $filename;
	     
	    if(move_uploaded_file($source, $saved_file_location)) {
	        $zip = new ZipArchive();
		    $x = $zip->open($saved_file_location);
		    if($x === true) {
		        $zip->extractTo($target);
		        $zip->close();
		         
		        unlink($saved_file_location);
		    } else {
		    	$w->error('failed opening zip',"/form");
		    }
	    } else {
	        $w->error("Failed to save file upload","/form");
	    }
	     
	    $content = json_decode(file_get_contents($target.$name[0]));
	    if (empty($content)) {
	    	$w->error('no content found. PLease ensure that your zip filename matches your application name');
	    }

	    //delete file upload from directory
	    unlink($target.$name[0]);
	    rmdir($target);
	//echo $target.$name[0]; die;
	    //create form structure from $content
	    if (!empty($content)) {
	    	//check for form title override and check title
	    	if (isset($_POST['title_override']) && !empty($_POST['title_override'])) {
	    		$new_title = $_POST['title_override'];
	    	} else {
	    		$new_title = $content->title;
	    	}
	    	$new_title = $w->Form->checkImportedApplicationTitle($new_title);

	    	$new_application = new FormApplication($w);
	    	$new_application->title = $new_title;
	    	$new_application->description = $content->description;
	    	$new_application->is_active = 1;
	    	$new_application->insert();

	    	if (!empty($content->forms)) {
	    		foreach ($content->forms as $form) {
	    			$new_form_title = $form->form_title;
	    			$new_form_title = $w->Form->checkImportedFormTitle($new_form_title);
	    			//var_dump($content);
			    	$new_form = new Form($w);
			    	$new_form->title = $new_form_title;
			    	$new_form->description = $form->description;
			    	$new_form->header_template = $form->header_template;
			    	$new_form->row_template = $form->row_template;
			    	$new_form->summary_template = $form->summary_template;
			    	$new_form->insert();
			    	
			    	//set up the form fields
			    	if (!empty($form->form_fields)) {
			    		foreach ($form->form_fields as $field) {
			    			$new_field = new FormField($w);
			    			$new_field->form_id = $new_form->id;
			    			$new_field->name = $field->field_name;
			    			$new_field->technical_name = $field->technical_name;
			    			$new_field->interface_class = $field->interface_class;
			    			$new_field->type = $field->type;
			    			$new_field->mask = $field->mask;
			    			$new_field->ordering = $field->ordering;
			    			$new_field->insert();
			    			//set up field metadata
			    			if (!empty($field->field_metadata)) {
			    				foreach ($field->field_metadata as $metadata) {
			    					$new_metadata = new FormFieldMetadata($w);
			    					$new_metadata->form_field_id = $new_field->id;
			    					$new_metadata->meta_key = $metadata->meta_key;
			    					$new_metadata->meta_value = $metadata->meta_value;
			    					$new_metadata->insert();
			    				}
			    			}
			    		}
			    	}

			    	//set up the form mapping
			    	if (!empty($form->form_mappings)) {
			    		foreach ($form->form_mappings as $mapping) {
			    			$new_mapping = new FormMapping($w);
			    			$new_mapping->form_id = $new_form->id;
			    			$new_mapping->object = $mapping;
			    			$new_mapping->insert();
			    		}
			    	}

			    	//attach the form to the application
			    	$app_mapping = new FormApplicationMapping($w);
			    	$app_mapping->form_id = $new_form->id;
			    	$app_mapping->application_id = $new_application->id;
			    	$app_mapping->insert();
	    		}
	    	}

	    	
	    }
	    $w->msg('Application import completed','/form-application');
	} else {
		$w->error('No upload found','/form-application');
	}
}