<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 18:49
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$name = $nv_Request->get_title( 'name', 'post', '' );
$alias = change_alias( $name );

$id = $nv_Request->get_int( 'id', 'post', 0 );
$mod = $nv_Request->get_string( 'mod', 'post', '' );

if( $mod == 'catalogs' )
{
	$tab = TABLE_PHOTO_NAME . '_catalogs';
	$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . $tab . ' WHERE catalogs_id!=' . $id . ' AND alias= :alias' );
	$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
	$stmt->execute();
	$nb = $stmt->fetchColumn();
	if( ! empty( $nb ) )
	{
		$nb = $db->query( 'SELECT MAX(catalogs_id) FROM ' . $tab )->fetchColumn();

		$alias .= '-' . ( intval( $nb ) + 1 );
	}
}
elseif( $mod == 'folder' )
{
	$tab = TABLE_PHOTO_NAME . '_album';
	$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . $tab . ' WHERE album_id !=' . $id . ' AND folder= :folder' );
	$stmt->bindParam( ':folder', $alias, PDO::PARAM_STR );
	$stmt->execute();
	$nb = $stmt->fetchColumn();
	if( ! empty( $nb ) )
	{
		$nb = $db->query( 'SELECT MAX(album_id) FROM ' . $tab )->fetchColumn();

		$alias .= '-' . ( intval( $nb ) + 1 );
	}
}
 

include NV_ROOTDIR . '/includes/header.php';
echo strtolower( $alias );
include NV_ROOTDIR . '/includes/footer.php';