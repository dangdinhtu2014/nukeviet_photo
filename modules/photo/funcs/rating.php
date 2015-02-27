<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );

if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

$difftimeout = 0;

$json = array();

$rating = $nv_Request->get_float( 'rating', 'post,get', 0 );

$album_id = $nv_Request->get_int( 'album_id', 'post,get', 0 );

$rating = $rating * 5;

$timeout = $nv_Request->get_int( $module_data . '_' . $op . '_' . $album_id, 'cookie', 0 );

if( $timeout == 0 or NV_CURRENTTIME - $timeout > $difftimeout )
{
	$nv_Request->set_Cookie( $module_data . '_' . $op . '_' . $album_id, NV_CURRENTTIME );
	
	$sql = "UPDATE " . TABLE_PHOTO_NAME . "_album SET 
		click_rating = click_rating + 1,
		total_rating = total_rating + " . intval( $rating ) . "
	WHERE album_id=" . $album_id;
	if( $db->query( $sql ) )
	{
		$sql = "SELECT click_rating, total_rating FROM " . TABLE_PHOTO_NAME . "_album WHERE album_id=" . $album_id;
		list( $click_rating, $total_rating ) = $db->query( $sql )->fetch(3);
		
		$width = ( $total_rating * 100 / ( $click_rating * 5 ) ) * 0.01;
		$json['width']  = round( $width, 2);
		$json['ratingValue']  = round( $total_rating/$click_rating, 1);
		$json['reviewCount']  = $click_rating;
		$json['success']  = sprintf( $lang_module['detail_rate_ok'], $rating );
	}else
	{
		$json['error'] = $lang_module['detail_rate_unsuccess'];
	}
	
}else
{
	$timeout = ceil( ( $difftimeout - NV_CURRENTTIME + $timeout ) / 60 );
	$json['error'] = sprintf( $lang_module['detail_rate_timeout'], $timeout );
}

header( 'Content-Type: application/json' );
echo json_encode( $json );
exit(); 