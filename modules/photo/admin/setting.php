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

$page_title = $lang_module['setting'];

$savesetting = $nv_Request->get_int( 'savesetting', 'post', 0 );
if( ! empty( $savesetting ) )
{
	$photo_config = array();
	$photo_config['per_page_album'] = $nv_Request->get_int( 'per_page_album', 'post', 0 );
	$photo_config['per_page_photo'] = $nv_Request->get_int( 'per_page_photo', 'post', 20 );
	$photo_config['home_view'] = $nv_Request->get_title( 'home_view', 'post', '', 0 );
	$photo_config['album_view'] = $nv_Request->get_title( 'album_view', 'post', '', 0 );
	$photo_config['module_logo'] = $nv_Request->get_title( 'module_logo', 'post', '', 0 );
	$photo_config['active_logo'] = $nv_Request->get_int( 'active_logo', 'post', 0 );
	$photo_config['autologosize1'] = $nv_Request->get_int( 'autologosize1', 'post', 50 );
	$photo_config['autologosize2'] = $nv_Request->get_int( 'autologosize2', 'post', 40 );
	$photo_config['autologosize3'] = $nv_Request->get_int( 'autologosize3', 'post', 30 );
	$photo_config['structure_upload'] = $nv_Request->get_title( 'structure_upload', 'post', '', 0 );
	$photo_config['maxupload'] = $nv_Request->get_int( 'maxupload', 'post', 0 );
	$photo_config['maxupload'] = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ), $photo_config['maxupload']);
	
	if( ! nv_is_url( $photo_config['module_logo'] ) and file_exists( NV_DOCUMENT_ROOT . $photo_config['module_logo'] ) )
	{
		$lu = strlen( NV_BASE_SITEURL );
		$photo_config['module_logo'] = substr( $photo_config['module_logo'], $lu );
	}
	elseif( ! nv_is_url( $photo_config['module_logo'] ) )
	{
		$photo_config['module_logo'] = $global_config['site_logo'];
	}
 
	$sth = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_setting SET config_value = :config_value WHERE config_name = :config_name');
	foreach( $photo_config as $config_name => $config_value )
	{
		$sth->bindParam( ':config_name', $config_name, PDO::PARAM_STR );
		$sth->bindParam( ':config_value', $config_value, PDO::PARAM_STR );
		$sth->execute();
	}
	$sth->closeCursor();

	nv_del_moduleCache( $module_name );
	
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&rand=' . nv_genpass() );
	die();
}


$module_logo = ( isset( $photo_config['module_logo'] ) ) ? $photo_config['module_logo'] : '';
$module_logo = ( ! nv_is_url( $module_logo ) && !empty( $photo_config['module_logo'] ) ) ? NV_BASE_SITEURL . $module_logo : $module_logo;

$photo_config['active_logo'] = ( $photo_config['active_logo'] == 1 ) ? 'checked="checked"': '';

$xtpl = new XTemplate( 'settings.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'DATA', $photo_config );
$xtpl->assign( 'MODULE_LOGO', $module_logo );
$xtpl->assign( 'PATH', defined( 'NV_IS_SPADMIN' ) ? '' : NV_UPLOADS_DIR . '/' . $module_name );
$xtpl->assign( 'CURRENTPATH', defined( 'NV_IS_SPADMIN' ) ? 'images' : NV_UPLOADS_DIR . '/' . $module_name );
$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );




foreach( $array_home_view as $key => $title )
{
	$xtpl->assign( 'HOME_VIEW', array( 'key' => $key, 'title' => $title, 'selected' => $key == $photo_config['home_view'] ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.home_view' );	
	
}
foreach( $array_album_view as $key => $title )
{
	$xtpl->assign( 'ALBUM_VIEW', array( 'key' => $key, 'title' => $title, 'selected' => $key == $photo_config['album_view'] ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.album_view' );	
	
}
// So bai viet tren mot trang
for( $i = 5; $i <= 60; ++ $i )
{
	$xtpl->assign( 'PER_PAGE_ALBUM', array( 'key' => $i, 'title' => $i, 'selected' => $i == $photo_config['per_page_album'] ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.per_page_album' );
}
for( $i = 5; $i <= 60; ++ $i )
{
	$xtpl->assign( 'PER_PAGE_PHOTO', array( 'key' => $i, 'title' => $i, 'selected' => $i == $photo_config['per_page_photo'] ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.per_page_photo' );
}


$array_structure_image = array();
$array_structure_image[''] = NV_UPLOADS_DIR . '/' . $module_name;
$array_structure_image['Y'] = NV_UPLOADS_DIR . '/' . $module_name . '/' . date( 'Y' );
$array_structure_image['Ym'] = NV_UPLOADS_DIR . '/' . $module_name . '/' . date( 'Y_m' );
$array_structure_image['Y_m'] = NV_UPLOADS_DIR . '/' . $module_name . '/' . date( 'Y/m' );
$array_structure_image['Ym_d'] = NV_UPLOADS_DIR . '/' . $module_name . '/' . date( 'Y_m/d' );
$array_structure_image['Y_m_d'] = NV_UPLOADS_DIR . '/' . $module_name . '/' . date( 'Y/m/d' );
 
$structure_image_upload = isset( $module_config[$module_name]['structure_upload'] ) ? $module_config[$module_name]['structure_upload'] : "Ym";

// Thu muc uploads
foreach( $array_structure_image as $type => $dir )
{
	$xtpl->assign( 'STRUCTURE_UPLOAD', array(
		'key' => $type,
		'title' => $dir. '/' .$lang_module['setting_dir_album'],
		'selected' => $type == $structure_image_upload ? ' selected="selected"' : ''
	) );
	$xtpl->parse( 'main.structure_upload' );
}


$sys_max_size = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ) );
$p_size = $sys_max_size / 100;

$xtpl->assign( 'SYS_MAX_SIZE', nv_convertfromBytes( $sys_max_size ) );

$data_maxupload = min( nv_converttoBytes( ini_get( 'upload_max_filesize' ) ), nv_converttoBytes( ini_get( 'post_max_size' ) ), $photo_config['maxupload']);
for ( $index = 100; $index > 0; --$index )
{
    $size1 = floor( $index * $p_size );
	
	$xtpl->assign( 'SIZE1', array(
		'key' => $size1,
		'title' => nv_convertfromBytes( $size1 ),
		'selected' => ( $data_maxupload == $size1 ) ? ' selected=\'selected\'' : ''
	) );
	
	$xtpl->parse( 'main.size1' );
}
 
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';