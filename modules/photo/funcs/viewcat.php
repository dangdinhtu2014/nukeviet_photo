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

$cache_file = '';
$contents = '';
$viewcat = $global_photo_cat[$category_id]['viewcat'];

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_photo_cat[$category_id]['alias'];
if( $page > 1 )
{
	$base_url_rewrite .= '/page-' . $page;
}
$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );
if( $_SERVER['REQUEST_URI'] != $base_url_rewrite )
{
	Header( 'Location: ' . $base_url_rewrite );
	die();
}

if( ! defined( 'NV_IS_MODADMIN' ) and $page < 5 )
{
	if( $set_view_page )
	{
		$cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $category_id . '_page_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
	}
	else
	{
		$cache_file = NV_LANG_DATA . '_' . $module_info['template'] . '_' . $op . '_' . $category_id . '_' . $page . '_' . NV_CACHE_PREFIX . '.cache';
	}
	if( ( $cache = nv_get_cache( $module_name, $cache_file ) ) != false )
	{
		$contents = $cache;
	}
}

$page_title = ( ! empty( $global_photo_cat[$category_id]['meta_title'] ) ) ? $global_photo_cat[$category_id]['meta_title'] : $global_photo_cat[$category_id]['name'];

$key_words = $global_photo_cat[$category_id]['meta_keyword'];

$description = $global_photo_cat[$category_id]['meta_description'];

$per_page = $photo_config['per_page_album'];

if( empty( $contents ) )
{
	$array_catpage = array();

	$base_url = $global_photo_cat[$category_id]['link'];

	if( $viewcat == 'viewcat_grid' )
	{

		$db->sqlreset()->select( 'COUNT(*)' )->from( TABLE_PHOTO_NAME . '_album a LEFT JOIN  ' . TABLE_PHOTO_NAME . '_rows r ON ( a.album_id = r.album_id )' )->where( 'a.status= 1 AND a.category_id=' . $category_id . ' AND r.defaults = 1' );

		$num_items = $db->query( $db->sql() )->fetchColumn();

		$db->select( 'a.album_id, a.category_id, a.name, a.alias, a.capturelocal, a.description, a.num_photo, a.date_added, r.file, r.thumb' )->order( 'a.date_added DESC' )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );
		$result = $db->query( $db->sql() );
		while( $item = $result->fetch() )
		{

			$item['link'] = $global_photo_cat[$category_id]['link'] . '/' . $item['alias'] . '-' . $item['album_id'] . $global_config['rewrite_exturl'];
			$array_catpage[] = $item;
		}

		$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

		$contents = viewcat_grid( $array_catpage, $generate_page );
	}

	if( ! defined( 'NV_IS_MODADMIN' ) and $contents != '' and $cache_file != '' )
	{
		nv_set_cache( $module_name, $cache_file, $contents );
	}

}
if( $page > 1 )
{
	$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
	$description .= ' ' . $page;
}
include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
