<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 22 Jul 2013 21:41:59 GMT
 */

if( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'creat_thumbs' ) )
{
	function creat_thumbs( $id, $file, $module_name, $width = 200, $height = 150, $quality = 90 )
	{
		if( $width >= $height ) $rate = $width / $height;
		else  $rate = $height / $width;

		$image = NV_UPLOADS_REAL_DIR . '/' . $module_name . '/images/' . $file;
 
		if( $file != '' and file_exists( $image ) )
		{
			$imgsource = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $file;
			$imginfo = nv_is_image( $image );

			$basename = $module_name . $width . 'x' . $height . '-' . $id . '-' . md5_file( $image ) . '.' . $imginfo['ext'];

			if( file_exists( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename ) )
			{
				$imgsource = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
			}
			else
			{
				require_once NV_ROOTDIR . '/includes/class/image.class.php';

				$_image = new image( $image, NV_MAX_WIDTH, NV_MAX_HEIGHT );

				if( $imginfo['width'] <= $imginfo['height'] )
				{
					$_image->resizeXY( $width, 0 );

				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) < $rate )
				{
					$_image->resizeXY( $width, 0 );
				}
				elseif( ( $imginfo['width'] / $imginfo['height'] ) >= $rate )
				{
					$_image->resizeXY( 0, $height );
				}

				$_image->cropFromCenter( $width, $height );

				$_image->save( NV_ROOTDIR . '/' . NV_TEMP_DIR, $basename, $quality );

				if( file_exists( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $basename ) )
				{
					$imgsource = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . $basename;
				}
			}
		}
		elseif( nv_is_url( $file ) )
		{
			$imgsource = $file;
		}
		else
		{
			$imgsource = '';
		}
		return $imgsource;
	}
}


/**
 * home_view_grid_by_cat()
 * 
 * @param mixed $array_data
 * @return
 */
function home_view_grid_by_cat( $array_cat )
{
	global $global_config, $global_photo_cat, $module_name, $module_file, $lang_module, $photo_config, $module_info, $op;

	$xtpl = new XTemplate( 'home_view_grid_by_cat.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'OP', $op );
	if( ! empty( $global_photo_cat ) )
	{
		foreach( $array_cat as $key => $catalog )
		{
			if( isset( $array_cat[$key]['content'] ) )
			{
				$xtpl->assign( 'CATALOG', $catalog );
				
				foreach( $array_cat[$key]['content'] as $album )
				{
					
					$album['description'] = strip_tags( nv_clean60( $album['description'], 100 ) );
					$album['datePublished'] = date( 'Y-m-d', $album['date_added'] );
					$album['thumb'] = creat_thumbs( $album['album_id'], $album['file'], $module_name, 270, 210, 90 );
					$album['file'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $album['file'];
					
					$xtpl->assign( 'ALBUM', $album );
					$xtpl->parse( 'main.loop_catalog.loop_album' );
					$xtpl->set_autoreset();
				}
				$xtpl->parse( 'main.loop_catalog' );
			}
		}

	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
/**
 * viewcat_grid()
 * 
 * @param mixed $array_data
 * @return
 */
function viewcat_grid( $array_catpage, $generate_page )
{
	global $global_config, $catalogs_id, $global_photo_cat, $client_info, $module_name, $module_file, $lang_module, $photo_config, $module_info, $op;

	$xtpl = new XTemplate( 'viewcat_grid.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CATALOG', $global_photo_cat[$catalogs_id] );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	if( ! empty( $array_catpage ) )
	{
		foreach( $array_catpage as $album )
		{
			
			$album['description'] = strip_tags( nv_clean60( $album['description'], 100 ) );
			$album['datePublished'] = date( 'Y-m-d', $album['date_added'] );
			$album['thumb'] = creat_thumbs( $album['album_id'], $album['file'], $module_name, 270, 210, 90 );
			$album['file'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $album['file'];
					
			$xtpl->assign( 'ALBUM', $album );
			$xtpl->parse( 'main.loop_album' );		 
		}

	}
	if( ! empty( $generate_page ) )
	{
		 
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

/**
 * detail_album()
 * 
 * @param mixed $album
 * @return
 */
function detail_album( $album, $array_photo, $other_catalogs_album )
{
	global $global_config, $catalogs_id, $client_info, $global_photo_cat, $module_name, $module_file, $lang_module, $photo_config, $module_info, $op;

	$xtpl = new XTemplate( 'detail_album.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CATALOG', $global_photo_cat[$catalogs_id] );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	
	if( ! empty( $album ) )
	{
			
		// $ratingwidth = ( $album['total_rating'] > 0 ) ? ( $album['total_rating'] * 100 / ( $album['click_rating'] * 5 ) ) * 0.01 : 0;
	 
		// $xtpl->assign( 'RATINGVALUE', ( $album['total_rating'] > 0 ) ? round( $album['total_rating']/$album['click_rating'], 1) : 0 );
		// $xtpl->assign( 'RATINGCOUNT', $album['total_rating'] );
		// $xtpl->assign( 'RATINGWIDTH', round( $ratingwidth, 2) );
		// $xtpl->assign( 'LINK_RATE', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=rating&album_id=' . $album['album_id'] );
		
	
		$album['description'] = strip_tags( nv_clean60( $album['description'], 100 ) );
		$album['datePublished'] = date( 'Y-m-d', $album['date_added'] );
 
		
					
		$xtpl->assign( 'ALBUM', $album );
		$num = 0;
		if( ! empty( $array_photo ) )
		{
			foreach( $array_photo as $photo )
			{
				//$photo['thumb'] = creat_thumbs( $photo['row_id'], $photo['file'], $module_name, 300, 210, 90 );
				$photo['thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/thumb/' . $photo['thumb'];
				$photo['file'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $photo['file'];
				$photo['num'] = $num;
				$xtpl->assign( 'PHOTO', $photo );
				$xtpl->parse( 'main.loop_slide' );
				$xtpl->parse( 'main.loop_thumb' );
				++$num;
			}
		}
  
	}
	if( !empty( $other_catalogs_album ) )
	{
		$key = 1;
		foreach( $other_catalogs_album as $other )
		{
			$other['description'] = strip_tags( nv_clean60( $other['description'], 100 ) );
			$other['datePublished'] = date( 'Y-m-d', $other['date_added'] );
			$other['thumb'] = creat_thumbs( $other['album_id'], $other['file'], $module_name, 270, 210, 90 );
			$other['file'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $other['file'];
			$other['key'] =	$key;	
			$xtpl->assign( 'OTHER', $other );
			$xtpl->parse( 'main.loop_album' );		 
			++$key;
		}
	}
 
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function no_permission( $groups_view )
{
	return '';	
	
}
 