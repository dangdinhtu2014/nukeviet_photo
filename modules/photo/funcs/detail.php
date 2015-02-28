<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );

$contents = '';
$date_added = 0;

// kiem tra tu cach xem album
if( nv_user_in_groups( $global_photo_cat[$catalogs_id]['groups_view'] ) )
{	
	// truy van lay thong tin album
	//$query = $db->query( 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_album WHERE album_id = ' . $album_id );
	$query = $db->query( 'SELECT a.*, r.file, r.thumb FROM ' . TABLE_PHOTO_NAME . '_album a 
						LEFT JOIN  ' . TABLE_PHOTO_NAME . '_rows r ON ( a.album_id = r.album_id )
						WHERE a.status= 1 AND r.defaults = 1 AND a.album_id = ' . $album_id );
	
	$album = $query->fetch();
 
	if( $album['album_id'] > 0 )
	{
		
		if( defined( 'NV_IS_MODADMIN' ) or ( $album['status'] == 1 ) )
		{
			// cap nhat luot xem
			$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $album_id, 'session' );
			if( empty( $time_set ) )
			{
				$nv_Request->set_Session( $module_data . '_' . $op . '_' . $album_id, NV_CURRENTTIME );

				$db->query( 'UPDATE ' . TABLE_PHOTO_NAME . '_album SET viewed=viewed+1 WHERE album_id=' . $album_id );
			}

			if( $alias_url == $album['alias'] )
			{
				$date_added = intval( $album['date_added'] );
			}
		}

	}
	
	// xac thuc lien ket co dung chuan khong 
	if( $date_added == 0 )
	{
		$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
		nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
	}
	
	// rewrite link
	$base_url_rewrite = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_photo_cat[$album['catalogs_id']]['alias'] . '/' . $album['alias'] . '-' . $album['album_id'] . $global_config['rewrite_exturl'], true );
	if( $_SERVER['REQUEST_URI'] != $base_url_rewrite )
	{
		Header( 'Location: ' . $base_url_rewrite );
		die();
	}
	
	// link duy nhat tranh trung lap tren google
	$canonicalUrl = NV_MAIN_DOMAIN . $base_url_rewrite;
	
	// anh trong album
	$array_photo = array();
	$db->sqlreset()
		->select( '*' )
		->from( TABLE_PHOTO_NAME . '_rows' )
		->where( 'status=1 AND album_id=' . $album['album_id'] )
		->order( 'date_added ASC' );

	$photo = $db->query( $db->sql() );
	while( $row = $photo->fetch() )
	{
		$array_photo[] = $row;
	}
	$photo->closeCursor();
	
	// album cung chu de
	$sql = 'SELECT a.album_id, a.catalogs_id, a.name, a.alias, a.capturelocal, a.description, a.num_photo, a.date_added, r.file, r.thumb FROM ' . TABLE_PHOTO_NAME . '_album a 
		LEFT JOIN  ' . TABLE_PHOTO_NAME . '_rows r ON ( a.album_id = r.album_id )
		WHERE a.status= 1 AND a.catalogs_id=' . $album['catalogs_id'] . ' AND r.defaults = 1 AND a.album_id != '. $album['album_id'] .' 
		ORDER BY a.date_added DESC 
		LIMIT 0, 6';
	$result = $db->query( $sql );
	$other_catalogs_album = array();
	while( $item = $result->fetch() )
	{
		$item['link'] = $global_photo_cat[$album['catalogs_id']]['link'] . '/' . $item['alias'] . '-' . $item['album_id'] . $global_config['rewrite_exturl'];
			
		$other_catalogs_album[] = $item;
	}
	$result->closeCursor();
 
	// truyen bien sang module block detail
	global $data_album;
	$data_album = $album;
	
	
	
	// goi ham xu ly giao dien 
	$contents = detail_album( $album, $array_photo, $other_catalogs_album );
	
	// truyen thong tin seo
	$page_title = $album['meta_title'];

	$key_words = $album['meta_keyword'];

	$description = $album['meta_description'];
}
else
{
	// khong co quyen xem album
	$contents = no_permission( $global_photo_cat[$catalogs_id]['groups_view'] );
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
