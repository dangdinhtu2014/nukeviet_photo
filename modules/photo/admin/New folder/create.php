<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );
$valueofi = $nv_Request->get_int( 'valueofi', 'post', 0 );
$totalphoto = $nv_Request->get_int( 'totalphoto', 'post', 0 );
$new_album = $nv_Request->get_title( 'new_album_name', 'post' );
$id = 0;

if( $new_album != "" )
{
	$sqll = "SELECT albumid FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE alias='".change_alias($new_album)."'";
	$resultl = $db->query( $sqll );
	$album = $resultl->fetch();
	$album_id = $album['albumid'];

	if( $album_id > 0 )
	{
		echo "ERR|" . $id . "|" . $new_album . "|" . $valueofi . "|" . $totalphoto . "|Lỗi Album này đã tồn tại";
	}
	else
	{
		$sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_album (albumid, userid, title, alias, numphoto, add_time, edit_time) VALUES (NULL, " . $admin_info['userid'] . ", " . $db->quote( $new_album ) . ", " . $db->quote( change_alias( $new_album ) ) . ", 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
		if( $id = $db->insert_id( $sql ) )
		{
			$alias = change_alias($new_album);
			if( ! empty( $alias ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $module_name. '/' . $alias ) )
			{
				nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module_name, change_alias($new_album) );
			}
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_add_album', " ", $admin_info['userid'] );
			//$xxx->closeCursor();
			echo "OK|" . $id . "|" . $new_album . "|" . $valueofi . "|" . $totalphoto . "";
		}
		else
		{
			echo "ERR|" . $id . "|" . $new_album . "|" . $valueofi . "|" . $totalphoto . "|Lỗi không tạo được Album";

		}
	}
}
else
{

	echo "ERR|" . $id . "|" . $new_album . "|" . $valueofi . "|" . $totalphoto . "|Vui lòng nhập tên Album";
}

