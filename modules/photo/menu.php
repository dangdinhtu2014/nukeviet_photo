<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $mod_data . "_cat ORDER BY sort ASC";
$result = $db->query( $sql );

While( $row = $result->fetch() )
{
	$t_sp = "";
	
	if ($row['lev'] > 0)
	{
		for( $i = 1; $i <= $row['lev']; ++ $i )
		{
			$t_sp .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		}
	}
	
	$arr_cat[$row['catid']] = array(
		'module' => $module, //
		'key' => $row['catid'], //
		'title' => $t_sp . $row['title'], //
		'alias' => $row['alias'],  //
	);
}

