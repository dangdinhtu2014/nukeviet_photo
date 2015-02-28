<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );
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

if( ! nv_function_exists( 'nv_block_album_new' ) )
{
	function nv_block_config_album_new( $module, $data_block, $lang_block )
	{
		global $site_mods;

		$html .= '<tr>';
		$html .= '<td>' . $lang_block['numrow'] . '</td>';
		$html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
		$html .= '</tr>';
		return $html;
	}

	function nv_block_config_album_new_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
 		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		return $return;
	}

	function nv_block_album_new( $block_config )
	{
		global $module_photo_category, $module_info, $site_mods, $module_config, $global_config, $db;
		
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
 
  
		$db->sqlreset()
			->select( 'a.album_id, a.category_id, a.name, a.alias, a.capturelocal, a.description, a.num_photo, a.date_added, r.file, r.thumb' )
			->from( NV_PREFIXLANG . '_' . $mod_data . '_album a LEFT JOIN  ' . NV_PREFIXLANG . '_' . $mod_data . '_rows r ON ( a.album_id = r.album_id )' )
			->where( 'a.status= 1 AND r.defaults = 1' )
			->order( 'a.date_added DESC' )
			->limit( $block_config['numrow'] );
		
		$list = nv_db_cache( $db->sql(), 'album_id', $module );

		if( ! empty( $list ) )
		{
			if( file_exists( NV_ROOTDIR . '/themes/' . $global_config['module_theme']  . '/modules/photo/block_album_new.tpl' ) )
			{
				$block_theme = $global_config['module_theme'] ;
			}
			else
			{
				$block_theme = 'default';
			}

			$xtpl = new XTemplate( 'block_album_new.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/photo' );
			foreach( $list as $album )
			{
				$album['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_photo_category[$album['category_id']]['alias'] . '/' . $album['alias'] . '-' . $album['album_id'] . $global_config['rewrite_exturl'];
				$album['description'] = strip_tags( nv_clean60( $album['description'], 100 ) );
				$album['datePublished'] = date( 'Y-m-d', $album['date_added'] );
				$album['thumb'] = creat_thumbs( $album['album_id'], $album['file'], $module, 270, 210, 90 );
				$album['file'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/images/' . $album['file'];

				$xtpl->assign( 'ALBUM', $album );
 				$xtpl->parse( 'main.loop_album' );
			}
 

			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $global_array_cat, $module_photo_category;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		if( $module == $module_name )
		{
			$module_photo_category = $global_array_cat;
			unset( $module_photo_category[0] );
		}
		else
		{
			$module_photo_category = array();
			$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_category ORDER BY sort_order ASC';
			$list = nv_db_cache( $sql, 'category_id', $module );
			foreach( $list as $l )
			{
				$module_photo_category[$l['category_id']] = $l;
				$module_photo_category[$l['category_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
			}
		}
		$content = nv_block_album_new( $block_config );
	}
}
