<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 22 Jul 2013 21:41:59 GMT
 */

if ( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

define( 'NV_IS_MOD_PHOTO', true );

define( 'TABLE_PHOTO_NAME', NV_PREFIXLANG . '_' . $module_data ); 

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) ); 
 
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';  
 
$catalogs_id = 0;
$parent_id = 0;
$alias_cat_url = isset( $array_op[0] ) ? $array_op[0] : '';
$array_mod_title = array();
 
foreach( $global_photo_cat as $_key => $l )
{
	if( $alias_cat_url == $l['alias'] )
	{
		$catalogs_id = $l['catalogs_id'];
		$parent_id = $l['parent_id'];
	}
}

//Xac dinh RSS
if( $module_info['rss'] )
{
	$rss[] = array(  
		'title' => $module_info['custom_title'],  
		'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=rss' //
	);
}


foreach( $global_photo_cat as $catalogs_id_i => $array_cat_i )
{
	if( $catalogs_id_i > 0 and $array_cat_i['parent_id'] == 0 )
	{
		$act = 0;
		$submenu = array();
		if( $catalogs_id_i == $catalogs_id or $catalogs_id_i == $parent_id )
		{
			$act = 1;
			if( ! empty( $global_photo_cat[$catalogs_id_i]['subcatalogs_id'] ) )
			{
				$array_catalogs_id = explode( ',', $global_photo_cat[$catalogs_id_i]['subcatalogs_id'] );
				foreach( $array_catalogs_id as $sub_catalogs_id_i )
				{
					$array_sub_cat_i = $global_photo_cat[$sub_catalogs_id_i];
					$sub_act = 0;
					if( $sub_catalogs_id_i == $catalogs_id )
					{
						$sub_act = 1;
					}
					$submenu[] = array(
						$array_sub_cat_i['title'],
						$array_sub_cat_i['link'],
						$sub_act
					);
				}
			}

		}
		$nv_vertical_menu[] = array(
			$array_cat_i['name'],
			$array_cat_i['link'],
			$act,
			'submenu' => $submenu );
	}

	//Xac dinh RSS
	if( $catalogs_id_i and $module_info['rss'] )
	{
		$rss[] = array(  
			'title' => $module_info['custom_title'] . ' - ' . $array_cat_i['name'],  
			'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=rss/' . $array_cat_i['alias'] //
		);
	}
}
unset( $result, $catalogs_id_i, $parent_id_i, $title_i, $alias_i );
$module_info['submenu'] = 0;

$page = 1;
 
 
$count_op = sizeof( $array_op );


if( ! empty( $array_op ) and $op == 'main' )
{
	
	if( $catalogs_id == 0 )
	{
		$contents = $lang_module['nocatpage'] . $array_op[0];
		if( isset( $array_op[0] ) and substr( $array_op[0], 0, 5 ) == 'page-' )
		{
			$page = intval( substr( $array_op[0], 5 ) );
		}
		elseif( ! empty( $alias_cat_url ) )
		{
			$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '" />';
			nv_info_die( $lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect );
		}
	}
	else
	{
		$op = 'main';
		if( $count_op == 1 or substr( $array_op[1], 0, 5 ) == 'page-' )
		{
			$op = 'viewcat';
			if( $count_op > 1 )
			{
				$page = intval( substr( $array_op[1], 5 ) );
			}
		}
		elseif( $count_op == 2 )
		{
			$array_page = explode( '-', $array_op[1] );
			$album_id = intval( end( $array_page ) );
			$number = strlen( $album_id ) + 1;
			$alias_url = substr( $array_op[1], 0, -$number );
			if( $album_id > 0 and $alias_url != '' )
			{
				$op = 'detail';
			}
		}
 
		$parent_id = $catalogs_id;
		while( $parent_id > 0 )
		{
			$array_cat_i = $global_photo_cat[$parent_id];
			$array_mod_title[] = array(
				'catalogs_id' => $parent_id,
				'title' => $array_cat_i['name'],
				'link' => $array_cat_i['link']
			);
			$parent_id = $array_cat_i['parent_id'];
		}
		sort( $array_mod_title, SORT_NUMERIC );
	}
}
 


function gltJsonResponse( $error = array(), $data = array() )
{
	$contents = array(
		'jsonrpc' => '2.0',
		'error' => $error,
		'data' => $data,
	);
	
	header( 'Content-Type: application/json' ); 
	echo json_encode( $contents ); 
	die();
}

function creatThumb( $file, $dir, $width, $height = 0 )
{
	require_once ( NV_ROOTDIR . '/includes/class/image.class.php' );

	$image = new image( $file, NV_MAX_WIDTH, NV_MAX_HEIGHT );

	if( empty( $height ) )
	{
		$image->resizeXY( $width, NV_MAX_HEIGHT );
	}
	else
	{
		if( ( $width * $image->fileinfo['height'] / $image->fileinfo['width'] ) > $height )
		{
			$image->resizeXY( $width, NV_MAX_HEIGHT );
		}
		else
		{
			$image->resizeXY( NV_MAX_WIDTH, $height );
		}

		$image->cropFromCenter( $width, $height );
	}

	// Kiem tra anh ton tai
	$fileName = $width . 'x' . $height . '-' . basename( $file );
	$fileName2 = $fileName;
	$i = 1;
	while( file_exists( $dir . '/' . $fileName2 ) )
	{
		$fileName2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1-' . $i . '\2', $fileName );
		++$i;
	}
	$fileName = $fileName2;

	// Luu anh
	$image->save( $dir, $fileName );
	$image->close();

	return substr( $image->create_Image_info['src'], strlen( $dir . '/' ) );
}