<?php

/**
 * @Project NUKEVIET 4.x
 * @Author ĐẶNG ĐÌNH TỨ (dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate Jan 11, 2013 10:47:41 PM
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );


$albumid = $nv_Request->get_int( 'albumid', 'get,post', 0 );
if($albumid>0)
{
	$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid='".$albumid ."'";

	$result = $db->query( $sql );

	$xtpl = new XTemplate( "view_ab.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'URL_BACK', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );


	$row = $db->sql_fetchrow( $result , 2 );
	if(!empty($row))
	{

		$xtpl->assign( 'DATA', $row );
		$xtpl->parse( 'main.content' );
	}else
	{
		$xtpl->parse( 'main.none' );
	}
}
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';

