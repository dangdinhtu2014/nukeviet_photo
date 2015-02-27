<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 18:49
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$catid = $nv_Request->get_int( 'catid', 'post', 0 );
$contents = "NO_" . $catid;

list( $catid, $parentid, $title ) = $db->query( "SELECT catid, parentid, title FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE catid=" . intval( $catid ) )->fetch( 3 );
if( $catid > 0 )
{

	$delallcheckss = $nv_Request->get_string( 'delallcheckss', 'post', "" );
	$check_parentid = $db->query( "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE parentid = '" . $catid . "'" )->fetchColumn();
	if( intval( $check_parentid ) > 0 )
	{
		$contents = "ERR_CAT_" . sprintf( $lang_module['delcat_msg_cat'], $check_parentid );
	}
	else
	{
		// kiem tra xoa chuyen mục mà tồn tại ảnh
		$a=1;
	}
	if( $contents == "NO_" . $catid )
	{
		if( $delallcheckss == md5( $catid . session_id() ) )
		{
			$sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE catid=" . $catid;
			if( $db->query( $sql ) )
			{
				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['delcatandrows'], $title, $admin_info['userid'] );
				//$xxx->closeCursor();
				nv_fix_cat_order();
				$contents = "OK_" . $parentid;
			}
			nv_del_moduleCache( $module_name );
		}
		else
		{
			$contents = "CONFIRM_" . $catid . "_" . md5( $catid . session_id() );
		}
	}

}

if( defined( 'NV_IS_AJAX' ) )
{
	include NV_ROOTDIR . '/includes/header.php';
	echo $contents;
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=cat" );
	die();
}

