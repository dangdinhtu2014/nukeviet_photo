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

$page_title = $lang_module['category'];

if( in_array( ACTION_METHOD, array(
	'weight',
	'inhome',
	'numlinks',
	'viewcat' ) ) )
{
	$category_id = $nv_Request->get_int( 'category_id', 'post', 0 );
	$mod = $nv_Request->get_string( 'action', 'get,post', '' );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $category_id;

	list( $category_id, $parent_id, $numsubcat ) = $db->query( 'SELECT category_id, parent_id, numsubcat FROM ' . TABLE_PHOTO_NAME . '_category WHERE category_id=' . $category_id )->fetch( 3 );
	if( $category_id > 0 )
	{
		if( $mod == 'weight' and $new_vid > 0 )
		{
			$sql = 'SELECT category_id FROM ' . TABLE_PHOTO_NAME . '_category WHERE category_id!=' . $category_id . ' AND parent_id=' . $parent_id . ' ORDER BY weight ASC';
			$result = $db->query( $sql );

			$weight = 0;
			while( $row = $result->fetch() )
			{
				++$weight;
				if( $weight == $new_vid ) ++$weight;
				$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET weight=' . $weight . ' WHERE category_id=' . intval( $row['category_id'] );
				$db->query( $sql );
			}

			$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET weight=' . $new_vid . ' WHERE category_id=' . $category_id;
			$db->query( $sql );

			nv_fix_cat_order();
			$content = 'OK_' . $parent_id;
		}
		elseif( $mod == 'inhome' and ( $new_vid == 0 or $new_vid == 1 ) )
		{
			$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET inhome=' . $new_vid . ' WHERE category_id=' . $category_id;
			$db->query( $sql );

			$content = 'OK_' . $parent_id;
		}
		elseif( $mod == 'numlinks' and $new_vid >= 0 and $new_vid <= 10 )
		{
			$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET numlinks=' . $new_vid . ' WHERE category_id=' . $category_id;
			$db->query( $sql );
			$content = 'OK_' . $parent_id;
		}
		elseif( $mod == 'viewcat' and $nv_Request->isset_request( 'new_vid', 'post' ) )
		{
			$viewcat = $nv_Request->get_title( 'new_vid', 'post' );

			//$array_viewcat = ( $numsubcat > 0 ) ? $array_viewcat_full : $array_viewcat_nosub;
			if( ! array_key_exists( $viewcat, $array_viewcat ) )
			{
				$viewcat = 'viewcat_grid';
			}

			$stmt = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET viewcat= :viewcat WHERE category_id=' . $category_id );
			$stmt->bindParam( ':viewcat', $viewcat, PDO::PARAM_STR );
			$stmt->execute();

			$content = 'OK_' . $parent_id;
		}
 
		nv_del_moduleCache( $module_name );
	}
	echo $content;
	exit();

}
if( ACTION_METHOD == 'delete' )
{
	$info = array();

	$category_id = $nv_Request->get_int( 'category_id', 'post', 0 );

	$token = $nv_Request->get_title( 'token', 'post', '', 1 );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );

	if( $listid != '' and md5( $global_config['sitekey'] . session_id() ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $global_config['sitekey'] . session_id() . $category_id ) )
	{
		$del_array = array( $category_id );
	}

	if( ! empty( $del_array ) )
	{

		$_del_array = array();

		$a = 0;
		foreach( $del_array as $category_id )
		{
 
			if( $rows_total =  $db->query( 'SELECT COUNT(*) total FROM ' . TABLE_RAOVAT_NAME . '_album WHERE category_id = ' . ( int )$category_id )->fetchColumn() )
			{
				$info['error'] = sprintf( $lang_module['category_error_album'], $rows_total );
			}
			else
			{
				$db->query( 'DELETE FROM ' . TABLE_RAOVAT_NAME . '_category WHERE category_id = ' . ( int )$category_id );
				
				$info['id'][$a] = $category_id;

				$_del_array[] = $category_id;

				++$a;
			}
		}

		$count = sizeof( $_del_array );

		if( $count )
		{
			nv_fix_cat_order();

			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_category', implode( ', ', $_del_array ), $admin_info['userid'] );

			nv_del_moduleCache( $module_name );

			$info['success'] = $lang_module['category_delete_success'];
		}
		 

	}
	else
	{
		$info['error'] = $lang_module['category_error_security'];
	}

	echo json_encode( $info );
	exit();

}


if( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	$selectthemes = ( ! empty( $site_mods[$module_name]['theme'] ) ) ? $site_mods[$module_name]['theme'] : $global_config['site_theme'];
	$layout_array = nv_scandir( NV_ROOTDIR . '/themes/' . $selectthemes . '/layout', $global_config['check_op_layout'] );

	$cat_form_exit = array();
	$_form_exit = scandir( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	foreach( $_form_exit as $_form )
	{
		if( preg_match( '/^cat\_form\_([a-zA-Z0-9\-\_]+)\.tpl$/', $_form, $m ) )
		{
			$cat_form_exit[] = $m[1];
		}
	}
	
	$groups_list = nv_groups_list();
	
	$data = array(
		'category_id' => 0,
		'parent_id' => 0,
		'name' => '',
		'alias' => '',
		'description' => '',
		'meta_title' => '',
		'meta_description' => '',
		'meta_keyword' => '',
		'weight' => '',
		'sort_order' => '',
		'lev' => '',
		'layout' => '',
		'viewcat' => 'viewcat_grid',
		'numsubcat' => '',
		'subcatid' => '',
		'inhome' => 1,
		'status' => 1,
		'numlinks' => '',
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME,
		'groups_view' => 6,
	);
	 
	$error = array();
 
	$data['category_id'] = $nv_Request->get_int( 'category_id', 'get,post', 0 );
	$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'get,post', 0 );
	if( $data['category_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_PHOTO_NAME . '_category  
		WHERE category_id=' . $data['category_id'] )->fetch();
 
		$caption = $lang_module['category_edit'];
	}
	else
	{
		$caption = $lang_module['category_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
		$data['parentid_old'] = $nv_Request->get_int( 'parentid_old', 'post', 0 );
		$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
		$data['inhome'] = $nv_Request->get_int( 'inhome', 'post', 0 );
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
		$data['name'] = nv_substr( $nv_Request->get_title( 'name', 'post', '', '' ), 0, 255 );
		$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 255 );
		$data['description'] = $nv_Request->get_textarea( 'description', 'post', '', 'br', 1 );
		$data['meta_title'] = nv_substr( $nv_Request->get_title( 'meta_title', 'post', '', '' ), 0, 255 );
		$data['meta_description'] = nv_substr( $nv_Request->get_title( 'meta_description', 'post', '', '' ), 0, 255 );
		$data['meta_keyword'] = nv_substr( $nv_Request->get_title( 'meta_keyword', 'post', '', '' ), 0, 255 );
		$data['layout'] = nv_substr( $nv_Request->get_title( 'layout', 'post', '', '' ), 0, 255 );
  
		
		if( empty( $data['name'] ) )
		{
			$error['name'] = $lang_module['category_error_name'];	
		}
		if( empty( $data['meta_title'] ) )
		{
			$error['meta_title'] = $lang_module['category_error_meta_title'];	
		}
		
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['category_error_warning'];
		}
 
		$_groups_post = $nv_Request->get_array( 'groups_view', 'post', array() );
		$data['groups_view'] = ! empty( $_groups_post ) ? implode( ',', nv_groups_post( array_intersect( $_groups_post, array_keys( $groups_list ) ) ) ) : '';
 
		$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_category WHERE category_id !=' . $data['category_id'] . ' AND alias= :alias' );
		$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
		$stmt->execute();
		$check_alias = $stmt->fetchColumn();

		if( $check_alias and $data['parent_id'] > 0 )
		{
			$parentid_alias = $db->query( 'SELECT  FROM ' . TABLE_PHOTO_NAME . ' WHERE category_id=' . $data['parent_id'] )->fetchColumn();
			$data['alias'] = $parentid_alias . '-' . $data['alias'];
		}
		$data['alias'] = strtolower( $data['alias'] );
		if( empty( $error ) )
		{
			if( $data['category_id'] == 0 )
			{

				$stmt = $db->prepare( 'SELECT max(weight) FROM ' . TABLE_PHOTO_NAME . '_category WHERE parent_id= ' . intval( $data['parent_id'] ) );
				$stmt->execute();
				$weight = $stmt->fetchColumn();

				$weight = intval( $weight ) + 1;

				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_PHOTO_NAME . '_category SET 
					parent_id = ' . intval( $data['parent_id'] ) . ', 
					weight = ' . intval( $weight ) . ', 
					inhome=' . intval( $data['inhome'] ) . ', 
					status=' . intval( $data['status'] ) . ', 
					date_added=' . intval( $data['date_added'] ) . ',  
					date_modified=' . intval( $data['date_modified'] ) . ', 
					sort_order = 0,
					lev = 0,
					numlinks=4,  
					numsubcat=0, 
					name =:name,
					alias =:alias,
					description =:description,
					meta_title =:meta_title,
					meta_description =:meta_description,
					meta_keyword =:meta_keyword,
					layout = :layout,
					viewcat = :viewcat,
					subcatid=:subcatid, 
					groups_view=:groups_view ' );
				
				$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
				$stmt->bindParam( ':layout', $data['layout'], PDO::PARAM_STR );
				$stmt->bindParam( ':viewcat', $data['viewcat'], PDO::PARAM_STR );
				$stmt->bindParam( ':subcatid', $data['subcatid'], PDO::PARAM_STR );
				$stmt->bindParam( ':groups_view', $data['groups_view'], PDO::PARAM_STR );
				$stmt->execute();

				if( $data['category_id'] = $db->lastInsertId() )
				{
		
					nv_fix_cat_order();
					
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A Category', 'category_id: ' . $data['category_id'], $admin_info['userid'] );	 
					
					$nv_Request->set_Session( $module_data . '_success', $lang_module['category_insert_success'] );
				}
				else
				{
					$error['warning'] = $lang_module['category_error_save'];

				}
				$stmt->closeCursor();

			}
			else
			{
				try
				{
					
					$stmt = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET 
						parent_id = ' . intval( $data['parent_id'] ) . ', 
						weight = ' . intval( $weight ) . ', 
						inhome=' . intval( $data['inhome'] ) . ', 
						status=' . intval( $data['status'] ) . ', 
						date_modified=' . intval( $data['date_modified'] ) . ', 
						name =:name,
						alias =:alias,
						description =:description,
						meta_title =:meta_title,
						meta_description =:meta_description,
						meta_keyword =:meta_keyword,
						layout = :layout,
						groups_view=:groups_view 
						WHERE category_id=' . $data['category_id'] );
				
					$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
					$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
					$stmt->bindParam( ':layout', $data['layout'], PDO::PARAM_STR );
 					$stmt->bindParam( ':groups_view', $data['groups_view'], PDO::PARAM_STR );
					
					if( $stmt->execute() )
					{

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A Category', 'category_id: ' . $data['category_id'], $admin_info['userid'] );
						
						if( $data['parent_id'] != $data['parentid_old'] )
						{
							$stmt = $db->prepare( 'SELECT max(weight) FROM ' . TABLE_PHOTO_NAME . '_category WHERE parent_id= :parent_id ' );
							$stmt->bindParam( ':parent_id', $data['parent_id'], PDO::PARAM_INT );
							$stmt->execute();
							
							$weight = $stmt->fetchColumn();
							
							$weight = intval( $weight ) + 1;
							$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET weight=' . $weight . ' WHERE category_id=' . intval( $data['category_id'] );
							$db->query( $sql );
							
							nv_fix_cat_order();
						}
						
						$nv_Request->set_Session( $module_data . '_success', $lang_module['category_update_success'] );
						 
					}
					else
					{
						$error['warning'] = $lang_module['category_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{ 
					$error['warning'] = $lang_module['category_error_save'];
					 var_dump($e);
				}

			}

		}
		if( empty( $error ) )
		{
			nv_del_moduleCache( $module_name );
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=category&parent_id=' . $data['parent_id'] );
			die();
		}

	}
 
	$xtpl = new XTemplate( 'category_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'THEME', $global_config['site_theme'] );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
 
	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['name'] ) )
	{
		$xtpl->assign( 'error_name', $error['name'] );
		$xtpl->parse( 'main.error_name' );
	}
	
	if( isset( $error['meta_title'] ) )
	{
		$xtpl->assign( 'error_meta_title', $error['meta_title'] );
		$xtpl->parse( 'main.error_meta_title' );	
	}

	$sql = 'SELECT category_id, name, lev FROM ' . TABLE_PHOTO_NAME . '_category WHERE category_id !=' . $data['category_id'] . ' ORDER BY sort_order ASC';

	$result = $db->query( $sql );
	
	$array_cat_list = array();
	
	$array_cat_list[0] = array( '0', $lang_module['category_sub_sl'] );

	while( list( $catid_i, $title_i, $lev_i ) = $result->fetch( 3 ) )
	{
		$xtitle_i = '';
		if( $lev_i > 0 )
		{
			$xtitle_i .= '&nbsp;';
			for( $i = 1; $i <= $lev_i; $i++ )
			{
				$xtitle_i .= '---';
			}
		}
		$xtitle_i .= $title_i;
		$array_cat_list[] = array( $catid_i, $xtitle_i );
	}
 
	foreach( $array_cat_list as $rows_i )
	{
		$sl = ( $rows_i[0] == $data['parent_id'] ) ? " selected=\"selected\"" : "";
		$xtpl->assign( 'pcatid_i', $rows_i[0] );
		$xtpl->assign( 'ptitle_i', $rows_i[1] );
		$xtpl->assign( 'pselect', $sl );
		$xtpl->parse( 'main.parent_loop' );
	}

	foreach( $layout_array as $value )
	{
		$value = preg_replace( $global_config['check_op_layout'], '\\1', $value );
		$xtpl->assign( 'LAYOUT', array( 'key' => $value, 'selected' => ( $data['layout'] == $value ) ? ' selected="selected"' : '' ) );
		$xtpl->parse( 'main.layout' );
	}

	foreach( $array_status as $key => $name )
	{
		$xtpl->assign( 'INHOME', array( 'key'=> $key, 'name'=> $name, 'selected'=> ( $key == $data['inhome'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.inhome' );
	}
	
	foreach( $array_status as $key => $name )
	{
		$xtpl->assign( 'STATUS', array( 'key'=> $key, 'name'=> $name, 'selected'=> ( $key == $data['status'] ) ? 'selected="selected"' : '' ) );
		$xtpl->parse( 'main.status' );
	}

	$groups_view = explode( ',', $data['groups_view'] );
	foreach( $groups_list as $_group_id => $_title )
	{
		$xtpl->assign( 'GROUPS_VIEW', array(
			'value' => $_group_id,
			'checked' => in_array( $_group_id, $groups_view ) ? ' checked="checked"' : '',
			'title' => $_title ) );
		$xtpl->parse( 'main.groups_view' );
	}

	if( empty( $data['alias'] ) )
	{
		$xtpl->parse( 'main.getalias' );
	}
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

	exit();
}

/*show list category*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$parent_id = $nv_Request->get_int( 'parent_id', 'get', 0 );

$sql = TABLE_PHOTO_NAME . '_category WHERE  parent_id = ' . $parent_id;

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array( 'name', 'sort_order' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= " ORDER BY " . $sort;
}
else
{
	$sql .= " ORDER BY sort_order";
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= " DESC";
}
else
{
	$sql .= " ASC";
}

$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=category&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'category.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $global_config['sitekey'] . session_id() ) );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
$xtpl->assign( 'URL_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=name&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_SORT', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=sort_order&amp;order=' . $order2 . '&amp;parent_id=' . $parent_id . '&amp;per_page=' . $per_page );

$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&action=add&amp;parent_id=" . $parent_id );
if( $nv_Request->get_string( $module_data . '_success', 'session' ) )
{
	$xtpl->assign( 'SUCCESS', $nv_Request->get_string( $module_data . '_success', 'session' ) );

	$xtpl->parse( 'main.success' );

	$nv_Request->unset_request( $module_data . '_success', 'session' );

} 
if( $parent_id > 0 )
{
	$parentid_i = $parent_id;
	$array_cat_title = array();
	$a = 0;

	while( $parentid_i > 0 )
	{
		list( $category_id_i, $parentid_i, $title_i ) = $db->query( 'SELECT category_id, parent_id, name FROM ' . TABLE_PHOTO_NAME . '_category 
 		WHERE category_id=' . intval( $parentid_i ) )->fetch( 3 );

		$array_cat_title[] = "<a href=\"" . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&amp;parent_id=" . $category_id_i . "\"><strong>" . $title_i . "</strong></a>";

		++$a;
	}

	for( $i = $a - 1; $i >= 0; $i-- )
	{
		$xtpl->assign( 'CAT_NAV', $array_cat_title[$i] . ( $i > 0 ? " &raquo; " : "" ) );
		$xtpl->parse( 'main.catnav.loop' );
	}

	$xtpl->parse( 'main.catnav' );
}

if( ! empty( $array ) )
{
	foreach( $array as $item )
	{
		//$array_viewcat = ( $item['numsubcat'] > 0 ) ? $array_viewcat_full : $array_viewcat_nosub;
		if( ! array_key_exists( $item['viewcat'], $array_viewcat ) )
		{
			$viewcat = 'viewcat_grid';
			$stmt = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_category SET viewcat= :viewcat WHERE category_id=' . $item['category_id'] );
			$stmt->bindParam( ':viewcat', $viewcat, PDO::PARAM_STR );
			$stmt->execute();
		}

		$item['token'] = md5( $global_config['sitekey'] . session_id() . $item['category_id'] );

		$item['link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&parent_id=" . $item['category_id'];
		$item['edit'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=category&action=edit&token=" . $item['token'] . "&category_id=" . $item['category_id'] . '&parent_id=' . $item['parent_id'];

		$item['numsubcat'] = $item['numsubcat'] > 0 ? ' <span style="color:#FF0101;">(' . $item['numsubcat'] . ')</span>' : '';

		$xtpl->assign( 'LOOP', $item );

		for( $i = 1; $i <= $num_items; ++$i )
		{
			$xtpl->assign( 'WEIGHT', array( 'w' => $i, 'selected' => ( $i == $item['weight'] ) ? ' selected="selected"' : '' ) );

			$xtpl->parse( 'main.loop.weight' );
		}
		foreach( $array_status as $key => $val )
		{
			$xtpl->assign( 'INHOME', array(
				'key' => $key,
				'title' => $val,
				'selected' => $key == $item['inhome'] ? ' selected=\'selected\'' : '' ) );
			$xtpl->parse( 'main.loop.inhome' );
		}

		foreach( $array_viewcat as $key => $val )
		{
			$xtpl->assign( 'VIEWCAT', array(
				'key' => $key,
				'title' => $val,
				'selected' => $key == $item['viewcat'] ? ' selected=\'selected\'' : '' ) );
			$xtpl->parse( 'main.loop.viewcat' );
		}

		for( $i = 0; $i <= 10; $i++ )
		{
			$xtpl->assign( 'NUMLINKS', array(
				'key' => $i,
				'title' => $i,
				'selected' => $i == $item['numlinks'] ? ' selected=\'selected\'' : '' ) );
			$xtpl->parse( 'main.loop.numlinks' );
		}
 
		$xtpl->parse( 'main.loop' );
	}

}
 
$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
if( ! empty( $generate_page ) )
{
	$xtpl->assign( 'GENERATE_PAGE', $generate_page );
	$xtpl->parse( 'main.generate_page' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
