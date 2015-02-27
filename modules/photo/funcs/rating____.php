<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );
if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

$contents = "";
$array_point = array(
	1,
	2,
	3,
	4,
	5 );

$albumid = $nv_Request->get_int( 'albumid', 'post', 0 );
$point = $nv_Request->get_int( 'point', 'post', 0 );
$checkss = $nv_Request->get_title( 'checkss', 'post' );

$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $albumid, 'session', 0 );

if( $albumid > 0 and in_array( $point, $array_point ) and $checkss == md5( $albumid . $client_info['session_id'] . $global_config['sitekey'] ) )
{
	if( ! empty( $time_set ) )
	{
		die( $lang_module['rating_error2'] );
	}

	$nv_Request->set_Session( $module_data . '_' . $op . '_' . $albumid, NV_CURRENTTIME );
	$query = $db->query( "SELECT total_rating, click_rating FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid = " . $albumid . " AND status=1" );
	$row = $query->fetch();
	if( true )
	{
		$query = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET total_rating=total_rating+" . $point . ", click_rating=click_rating+1 WHERE albumid=" . $albumid;
		$db->query( $query );
		$contents = sprintf( $lang_module['stringrating'], $row['total_rating'] + $point, $row['click_rating'] + 1 );
		die( $contents );
	}
}

die( $lang_module['rating_error1'] );

