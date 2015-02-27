<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'block_photo_detail' ) )
{

	/* function nv_block_config_photo_detail( $module, $data_block, $lang_block )
	{
		$html = '<tr>';
		$html .= '	<td>' . $lang_block['numrow'] . '</td>';
		$html .= '	<td><input type="text" name="config_numrow" class="form-control w100" size="5" value="' . $data_block['numrow'] . '"/></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['showtooltip'] . '</td>';
		$html .= '<td>';
		$html .= '<input type="checkbox" value="1" name="config_showtooltip" ' . ( $data_block['showtooltip'] == 1 ? 'checked="checked"' : '' ) . ' /><br /><br />';
		$tooltip_position = array( 'top' => $lang_block['tooltip_position_top'], 'bottom' => $lang_block['tooltip_position_bottom'], 'left' => $lang_block['tooltip_position_left'], 'right' => $lang_block['tooltip_position_right'] );
		$html .= '<span class="text-middle pull-left">' . $lang_block['tooltip_position'] . '&nbsp;</span><select name="config_tooltip_position" class="form-control w100 pull-left">';
		foreach( $tooltip_position as $key => $value )
		{
			$html .= '<option value="' . $key . '" ' . ( $data_block['tooltip_position'] == $key ? 'selected="selected"' : '' ) . '>' . $value . '</option>';
		}
		$html .= '</select>';		
		$html .= '&nbsp;<span class="text-middle pull-left">' . $lang_block['tooltip_length'] . '&nbsp;</span><input type="text" class="form-control w100 pull-left" name="config_tooltip_length" size="5" value="' . $data_block['tooltip_length'] . '"/>';
		$html .= '</td>';
		$html .= '</tr>';
		return $html;
	}

	function nv_block_config_photo_detail_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['showtooltip'] = $nv_Request->get_int( 'config_showtooltip', 'post', 0 );
		$return['config']['tooltip_position'] = $nv_Request->get_string( 'config_tooltip_position', 'post', 0 );
		$return['config']['tooltip_length'] = $nv_Request->get_string( 'config_tooltip_length', 'post', 0 );
		return $return;
	}
 */
	function block_photo_detail( $block_config )
	{
		global $data_album, $module_photo_cat, $client_info, $site_mods, $module_info, $db, $module_config, $global_config, $my_head;

		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		
		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/'. $mod_file .'/module.block_detail.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}
		$xtpl = new XTemplate( 'module.block_detail.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/'. $mod_file .'/' );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'MODULE_FILE', $mod_file );
		$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
		
		$data_album['image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/images/' . $data_album['file'];
		$data_album['thumb'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module . '/thumb/' . $data_album['thumb'];
		
		
		$my_head="<meta name=\"thumbnail\" content=\"".$data_album['thumb']."\"/>";
		$my_head.="<!--";
		$my_head.="  <PageMap>";
		$my_head.="	<DataObject type=\"thumbnail\">";
		$my_head.="	  <Attribute name=\"src\" value=\"http://dangdinhtu.com/uploads/photo/thumb/2015_01/90x72-148-copy.jpg\"/>";
		$my_head.="	  <Attribute name=\"width\" value=\"100\"/>";
		$my_head.="	  <Attribute name=\"height\" value=\"130\"/>";
		$my_head.="	</DataObject>";
		$my_head.="  </PageMap>";
		$my_head.="-->";
		
		$ratingwidth = ( $data_album['total_rating'] > 0 ) ? ( $data_album['total_rating'] * 100 / ( $data_album['click_rating'] * 5 ) ) * 0.01 : 0;
	 
		$xtpl->assign( 'RATINGVALUE', ( $data_album['total_rating'] > 0 ) ? round( $data_album['total_rating']/$data_album['click_rating'], 1) : 0 );
		$xtpl->assign( 'RATINGCOUNT', $data_album['click_rating'] );
		$xtpl->assign( 'REVIEWCOUNT', $data_album['total_rating'] );
		$xtpl->assign( 'RATINGWIDTH', round( $ratingwidth, 2) );
		$xtpl->assign( 'LINK_RATE', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module . '&' . NV_OP_VARIABLE . '=rating&album_id=' . $data_album['album_id'] );
		
		$data_album['capturedate'] = date('d-m-Y', $data_album['capturedate']);
		$xtpl->assign( 'DATA', $data_album );
		
		

		$xtpl->parse( 'main' );
		return $xtpl->text( 'main' );
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name, $global_photo_cat, $module_photo_cat;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
 
		if( $module == $module_name )
		{
			$module_photo_cat = $global_photo_cat;
			unset( $module_photo_cat[0] );
		}
		else
		{
			$module_photo_cat = array();
			$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_catalogs ORDER BY sort_order ASC';
			$list = nv_db_cache( $sql, 'catalogs_id', $module  );
			foreach( $list as $l )
			{
				$module_photo_cat[$l['catalogs_id']] = $l;
				$module_photo_cat[$l['catalogs_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
				
			}
		}
		$content = block_photo_detail( $block_config  );
	}
}