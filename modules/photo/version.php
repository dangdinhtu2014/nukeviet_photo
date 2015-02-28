<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$module_version = array(  
	"name" => "Photo", 
	"modfuncs" => "main, detail, viewcat", 
	"submenu" => "main, detail, viewcat", 
	"is_sysmod" => 0, 
	"virtual" => 1,  
	"version" => "4.0.01",  
	"date" => "Wed, 21 Jan 2015 14:00:59 GMT",  
	"author" => "DANGDINHTU (dlinhvan@gmail.com)",  
	"uploads_dir" => array(
		$module_name,
		$module_name."/images",
		$module_name."/thumbs",
		$module_name."/temp"
	),  
	"note" => "" 
);