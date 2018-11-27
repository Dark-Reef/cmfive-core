<?php

Config::set('auth', array(
    'migration_version' => '0.8.0',		
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'dependencies' => [
        'sonata-project/google-authenticator' => "^2.2"
    ],
	'hooks' => [
		'admin'
	]
));
