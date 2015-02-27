<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  7, 26, 2013 16:19
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$albumid = $nv_Request->get_int( 'albumid', 'post,get', 0 );
$checkss = $nv_Request->get_string( 'checkss', 'post,get', 0 );

$contents = "NO_" . $albumid;

if( $albumid > 0 and $checkss == md5( $albumid . $global_config['sitekey'] . session_id() ) )
{

	list( $alias, $album_name ) = $db->query( "SELECT alias, album_name FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid = '" . $albumid . "'" )->fetch( 3 );
	if( $alias != strtolower( change_alias( $album_name ) ) )
	{
		$alias = strtolower( change_alias( $album_name ) );
	}
	
	//$xxx->closeCursor();
	$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid=" . $albumid . "" );

	if( $db->sql_affectedrows() > 0 )
	{
		//$xxx->closeCursor();
		$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE albumid=" . $albumid . "" );
		if( $db->sql_affectedrows() > 0 )
		{
			nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $alias, true );
			$contents = "OK_" . $albumid;
		}
		
		//$xxx->closeCursor();
	}
}
elseif( $nv_Request->isset_request( 'listall', 'post,get' ) )
{

	$listall = $nv_Request->get_string( 'listall', 'post,get' );
	$array_albumid = explode( ',', $listall );
	$listalbum = array();
	foreach( $array_albumid as $order )
	{
		$arr_order = explode( "_", $order );
		$listalbum[] = intval( $arr_order[0] );
	}

	$listalbum = implode( ',', $listalbum );
	$list = array();
	$sql = "SELECT albumid, album_name, alias FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid IN(" . $listalbum . ")";
	$result = $db->query( $sql );
	while( list( $albumid, $album_name, $alias ) = $result->fetch( 3 ) )
	{

		$list[$albumid] = $alias;

	}
	//$xxx->closeCursor();
	foreach( $array_albumid as $order_i )
	{
		$arr_order_i = explode( "_", $order_i );
		$albumid = intval( $arr_order_i[0] );
		$checkss = trim( $arr_order_i[1] );

		if( $albumid > 0 and $checkss == md5( $albumid . $global_config['sitekey'] . session_id() ) )
		{
			$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid=" . $albumid . "" );

			if( $db->sql_affectedrows() > 0 )
			{
				@nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $list[$albumid], true );

			}

			//$xxx->closeCursor();

		}
	}

	$contents = "OK_0";
}

nv_del_moduleCache( $module_name );

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';

