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

$page_title = $lang_module['album'];
 
 
if( ACTION_METHOD == 'status' )
{
	$album_id = $nv_Request->get_int( 'album_id', 'post', 0 );
	$mod = $nv_Request->get_string( 'action', 'get,post', '' );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $album_id;

	list( $album_id ) = $db->query( 'SELECT album_id FROM ' . TABLE_PHOTO_NAME . '_album WHERE album_id=' . $album_id )->fetch( 3 );
	if( $album_id > 0 )
	{
		if( $mod == 'status' and ( $new_vid == 0 or $new_vid == 1 ) )
		{
			$sql = 'UPDATE ' . TABLE_PHOTO_NAME . '_album SET status=' . $new_vid . ' WHERE album_id=' . $album_id;
			$db->query( $sql );

			$content = 'OK_' . $album_id;
		}
 
		nv_del_moduleCache( $module_name );
	}
	echo $content;
	exit();

}
 
if( ACTION_METHOD == 'deleterows' )
{
	$info = array();	
	$album_id = $nv_Request->get_int( 'album_id', 'post', 0 );
	$row_id = $nv_Request->get_int( 'row_id', 'post', 0 );
	$token = $nv_Request->get_string( 'token', 'post', '' );
	$token_thumb = $nv_Request->get_string( 'token_thumb', 'post', '' );
	$token_image = $nv_Request->get_string( 'token_image', 'post', '' );
	$thumb = $nv_Request->get_string( 'thumb', 'post', '' );
	$image_url = $nv_Request->get_string( 'image_url', 'post', '' );
	if(  $token == md5( $global_config['sitekey'] . session_id() . $row_id ) )
	{
		$data = $db->query( 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_rows WHERE row_id=' . $row_id . ' AND album_id=' . $album_id )->fetch( );
		if( $data['row_id'] > 0 )
		{
				
			if( $db->query( 'DELETE FROM ' . TABLE_PHOTO_NAME . '_rows WHERE row_id = ' . $row_id . ' AND album_id=' . $album_id ) )
			{
				$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_album SET num_photo = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_rows WHERE album_id = '. $data['album_id'] .') WHERE album_id = '. $data['album_id'] );	
				
				@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $data['file'] );
				@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/thumb/' . $data['thumb'] );
				nv_del_moduleCache( $module_name );
				
				$info['success'] = $lang_module['photo_success_delete'];
			}
		}
	}elseif( empty( $row_id ) &&  $token_image == md5( $global_config['sitekey'] . session_id() . $image_url ) && $token_thumb == md5( $global_config['sitekey'] . session_id() . $thumb ) )
	{
		@nv_deletefile( NV_ROOTDIR . $thumb );
		@nv_deletefile( NV_ROOTDIR . $image_url );
		$info['success'] = $lang_module['photo_success_delete'];
		
	}else{
				
		$info['error'] = $lang_module['photo_error_delete'];
	}
	
	header( 'Content-Type: application/json' );
	echo json_encode( $info );
	exit();

}

if( ACTION_METHOD == 'delete' )
{
	$info = array();
	$album_id = $nv_Request->get_int( 'album_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '' );
	
	$listid = $nv_Request->get_string( 'listid', 'post', '' );
	if( $listid != '' and md5( $global_config['sitekey'] . session_id() ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $global_config['sitekey'] .  session_id() . $album_id ) )
	{
		$del_array = array( $album_id );
	}
 
	if( ! empty( $del_array ) )
	{
		$a = 0;
		foreach( $del_array as $album_id )
		{
 
			$album = $db->query( 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_album WHERE album_id=' . (int)$album_id )->fetch();
			
			$delete = $db->prepare('DELETE FROM ' . TABLE_PHOTO_NAME . '_album WHERE album_id=' . (int)$album['album_id'] );
			$delete->execute();
			
			if( $delete->rowCount() )
			{ 
				$result = $db->query( 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_rows WHERE album_id=' . (int)$album['album_id'] );
				while( $data = $result->fetch( ) )
				{
					if( $db->query( 'DELETE FROM ' . TABLE_PHOTO_NAME . '_rows WHERE row_id = ' . $data['row_id'] ) )
					{
 
						@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/'. $module_name . '/images/' . $data['file'] );
						@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/'. $module_name . '/thumb/' . $data['thumb'] );	
					}
				}
 		
				@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $album['folder'] );
				@rmdir( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/' . $album['folder'] );
						
				$nv_Request->unset_request( $module_data . '_success', 'session' );
				nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_album', $album['album_id'], $admin_info['userid'] );
				nv_del_moduleCache( $module_name );
				
				$info['id'][$a] = $album_id;
 
				++$a;
			}
 	
		}
		if( !empty( $a ) )
		{
			$info['success'] = $lang_module['album_success_delete'] ;
		}
		
	}else
	{
		$info['error'] = $lang_module['album_error_delete'];
	}
	echo json_encode( $info );
	exit();
}
  
 
if( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
 	$array_structure_image = array();
	$array_structure_image[''] = $module_name;
	$array_structure_image['Y'] = $module_name . '/images/' . date( 'Y' );
	$array_structure_image['Ym'] = $module_name . '/images/' . date( 'Y_m' );
	$array_structure_image['Y_m'] = $module_name . '/images/' . date( 'Y/m' );
	$array_structure_image['Ym_d'] = $module_name . '/images/' . date( 'Y_m/d' );
	$array_structure_image['Y_m_d'] = $module_name . '/images/' . date( 'Y/m/d' );
	 
	$structure_upload = isset( $photo_config['structure_upload'] ) ? $photo_config['structure_upload'] : 'Ym';
	$currentpath = isset( $array_structure_image[$structure_upload] ) ? $array_structure_image[$structure_upload] : '';
  
	
	if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpath ) )
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
	}
	else
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_name;
		$e = explode( '/', $currentpath );
		if( ! empty( $e ) )
		{
			$cp = '';
			foreach( $e as $p )
			{
				if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
				{
					$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
					if( $mk[0] > 0 )
					{
						$upload_real_dir_page = $mk[2];
						$db->query( "INSERT IGNORE INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
					}
				}
				elseif( ! empty( $p ) )
				{
					$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
				}
				$cp .= $p . '/';
			}
		}
		$upload_real_dir_page = str_replace( '\\', '/', $upload_real_dir_page );
	}
 
	$currentpath = str_replace( NV_ROOTDIR . '/', '', $upload_real_dir_page );
 	$imagepath = str_replace( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/images/', '', $upload_real_dir_page );
 
	/*Folder thumb*/
	$array_structure_thumb = array();
	$array_structure_thumb[''] = $module_name.'/thumb';
	$array_structure_thumb['Y'] = $module_name . '/thumb/' . date( 'Y' );
	$array_structure_thumb['Ym'] = $module_name . '/thumb/' . date( 'Y_m' );
	$array_structure_thumb['Y_m'] = $module_name . '/thumb/' . date( 'Y/m' );
	$array_structure_thumb['Ym_d'] = $module_name . '/thumb/' . date( 'Y_m/d' );
	$array_structure_thumb['Y_m_d'] = $module_name . '/thumb/' . date( 'Y/m/d' );
	 
	$structure_upload = isset( $photo_config['structure_upload'] ) ? $photo_config['structure_upload'] : 'Ym';
	$currentpaththumb = isset( $array_structure_thumb[$structure_upload] ) ? $array_structure_thumb[$structure_upload] : '';
 
	if( file_exists( NV_UPLOADS_REAL_DIR . '/' . $currentpaththumb ) )
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpaththumb;
	}
	else
	{
		$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_name;
		$e = explode( '/', $currentpaththumb );
		if( ! empty( $e ) )
		{
			$cp = '';
			foreach( $e as $p )
			{
				if( ! empty( $p ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $cp . $p ) )
				{
					$mk = nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $cp, $p );
					if( $mk[0] > 0 )
					{
						$upload_real_dir_page = $mk[2];
						$db->query( "INSERT IGNORE INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $cp . $p . "', 0)" );
					}
				}
				elseif( ! empty( $p ) )
				{
					$upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
				}
				$cp .= $p . '/';
			}
		}
		$upload_real_dir_page = str_replace( '\\', '/', $upload_real_dir_page );
	}

	$currentpaththumb = str_replace( NV_ROOTDIR . '/', '', $upload_real_dir_page );
 
 
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
		'album_id' => 0,
		'category_id' => 0,
		'name' => '',
		'alias' => '',
		'description' => '',
		'meta_title' => '',
		'meta_description' => '',
		'meta_keyword' => '',
		'model' => '',
		'capturedate' => 0,
		'capturelocal' => '',
		'folder' => '',
		'thumb' => '',
		'layout' => '',
		'num_photo' => 0,
		'layout' => '',
		'viewed' => 0,
		'weight' => '',
		'total_rating' => 0,
		'click_rating' => 0,
		'status' => 1,
		'groups_view' => 6,
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME,
		'albums' => array(),
	);
	 
	$error = array();
 
	$data['album_id'] = $nv_Request->get_int( 'album_id', 'get,post', 0 );
 	if( $data['album_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_PHOTO_NAME . '_album  
		WHERE album_id=' . $data['album_id'] )->fetch();
		$data['old_category_id'] = $data['category_id'];
		$folder = explode('/', $data['folder']);
		$data['folder'] = end( $folder );
		
		$array_photo = $db->query( 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_rows WHERE album_id=' . $data['album_id'] )->fetchAll();
		foreach( $array_photo as $photo )
		{
			$data['albums'][] =  array(
				'row_id'=> $photo['row_id'], 
				'token'=>  md5( $global_config['sitekey'] . session_id() . $photo['row_id'] ),
				'token_image'=> '',
				'token_thumb'=> '',
				'basename'=> '',
				'filePath'=> '',
				'thumb'=> NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/thumb/' . $photo['thumb'],
				'image_url'=> NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $photo['file'],
				'defaults'=> ( $photo['defaults'] == 1 ) ? 'checked="checked"' : '',
				'name'=> $photo['name'],
				'description'=> $photo['description'],
			);
		}
 
		$caption = $lang_module['album_edit'];
	}
	else
	{
		$caption = $lang_module['album_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['album_id'] = $nv_Request->get_int( 'album_id', 'post', 0 );
 		$data['name'] = nv_substr( $nv_Request->get_title( 'name', 'post', '', '' ), 0, 255 );
		$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 255 );
		$data['folder'] = nv_substr( $nv_Request->get_title( 'folder', 'post', '', '' ), 0, 255 );
		$data['folder'] = strtolower(change_alias( $data['folder'] ) );
		$data['category_id'] = $nv_Request->get_int( 'category_id', 'post', 0 );
 		$data['description'] = $nv_Request->get_textarea( 'description', 'post', '', 'br', 1 );
		$data['meta_title'] = nv_substr( $nv_Request->get_title( 'meta_title', 'post', '', '' ), 0, 255 );
		$data['meta_description'] = nv_substr( $nv_Request->get_title( 'meta_description', 'post', '', '' ), 0, 255 );
		$data['meta_keyword'] = nv_substr( $nv_Request->get_title( 'meta_keyword', 'post', '', '' ), 0, 255 );
		$data['layout'] = nv_substr( $nv_Request->get_title( 'layout', 'post', '', '' ), 0, 255 );
		$data['model'] = nv_substr( $nv_Request->get_title( 'model', 'post', '', '' ), 0, 255 );
		$data['capturelocal'] = nv_substr( $nv_Request->get_title( 'capturelocal', 'post', '', '' ), 0, 255 );
		$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
		
		$data['capturedate'] = nv_substr( $nv_Request->get_title( 'capturedate', 'post', '', '' ), 0, 10 );
		if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['capturedate'], $m ) )
		{
			$data['capturedate'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
		}
		else
		{
			$data['capturedate'] = 0;
		}
		
		$data['albums'] = $nv_Request->get_typed_array( 'albums', 'post', '', array() );
		
		if( empty( $data['name'] ) )
		{
			$error['name'] = $lang_module['album_error_name'];	
		}
		if( empty( $data['folder'] ) )
		{
			$error['folder'] = $lang_module['album_error_folder'];	
		}
		if( empty( $data['category_id'] ) )
		{
			$error['category'] = $lang_module['album_error_category'];	
		}
		if( empty( $data['meta_title'] ) )
		{
			$error['meta_title'] = $lang_module['album_error_meta_title'];	
		}
		
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['album_error_warning'];
		}
 
		$_groups_post = $nv_Request->get_array( 'groups_view', 'post', array() );
		$data['groups_view'] = ! empty( $_groups_post ) ? implode( ',', nv_groups_post( array_intersect( $_groups_post, array_keys( $groups_list ) ) ) ) : '';

		
		if( !empty( $data['folder'] ) && ! is_dir( NV_ROOTDIR . '/' . $currentpath . '/'. $data['folder'] ) )
		{
			$mkdir = nv_mkdir( NV_ROOTDIR . '/' . $currentpath, $data['folder'] );
			if( $mkdir[0] == 0 )
			{
				$error['warning'] = $lang_module['album_error_create_folder']; 
			}else
			{
				$db->query( "INSERT IGNORE INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . $currentpath . '/'. $data['folder'] . "', 0)" );		
			}
		}
		
		$data['alias'] = strtolower( $data['alias'] );
		
		if( empty( $error ) )
		{
			$mime = nv_parse_ini_file( NV_ROOTDIR . '/includes/ini/mime.ini', true );
	
			if( $data['album_id'] == 0 )
			{
 
				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_PHOTO_NAME . '_album SET 
					category_id = ' . intval( $data['category_id'] ) . ', 
					status=' . intval( $data['status'] ) . ', 
					date_added=' . intval( $data['date_added'] ) . ',  
					date_modified=' . intval( $data['date_modified'] ) . ', 
					name =:name,
					alias =:alias,
					description =:description,
					meta_title =:meta_title,
					meta_description =:meta_description,
					meta_keyword =:meta_keyword,
					model = :model,
					capturedate = :capturedate,
					capturelocal = :capturelocal,
					folder = :folder,
					layout = :layout,
					groups_view=:groups_view ' );
				
				$folder =  $imagepath . '/' . $data['folder']; 
				
				$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
				$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
				$stmt->bindParam( ':model', $data['model'], PDO::PARAM_STR );
  				$stmt->bindParam( ':capturedate', $data['capturedate'], PDO::PARAM_INT );
  				$stmt->bindParam( ':capturelocal', $data['capturelocal'], PDO::PARAM_STR );
  				$stmt->bindParam( ':folder', $folder, PDO::PARAM_STR );
  				$stmt->bindParam( ':layout', $data['layout'], PDO::PARAM_STR );
  				$stmt->bindParam( ':groups_view', $data['groups_view'], PDO::PARAM_STR );
				$stmt->execute();

				if( $data['album_id'] = $db->lastInsertId() )
				{
					try
					{
						$count = 0;	
						foreach( $data['albums'] as $key => $photo )
						{
 							$photo['row_id'] = isset( $photo['row_id'] ) ? $photo['row_id'] : 0;	
							$photo['name'] = isset( $photo['name'] ) ? $photo['name'] : '';	
							$photo['filePath'] = isset( $photo['filePath'] ) ? $photo['filePath'] : $photo['filePath'];	
							$photo['image_url'] = isset( $photo['image_url'] ) ? $photo['image_url'] : $photo['image_url'];	
							$photo['thumb'] = isset( $photo['thumb'] ) ? $photo['thumb'] : $photo['thumb'];	
 							
							$photo['description'] = isset( $photo['description'] ) ? $photo['description'] : '';	
							$photo['description'] = nv_nl2br( nv_htmlspecialchars( strip_tags( $photo['description'] ) ), '<br />' );
 							$photo['defaults'] = isset( $photo['defaults'] ) ? $photo['defaults'] : 0;	
 
							// Kiem tra anh hop le
	
							$image_info = nv_is_image( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/'  . $photo['basename'] );

							if( empty( $image_info ) or ! isset( $mime['images'][$image_info['ext']] ) )
							{
								$error['error_image'][] = $lang_module['album_error_mime'] . ' ' . $photo['basename'];
								
								@nv_deletefile( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $photo['basename'] );
							
								unset( $data['albums'][$key] );
							}
							else
							{
								$photo['width'] = $image_info['width'];
								$photo['height'] = $image_info['height'];
								$photo['mime'] = $image_info['mime'];
								$photo['size'] = filesize( $image_info['src'] );
							}
 
							$folder_album = NV_ROOTDIR . '/' . $currentpath . '/'. $data['folder'];
							
							if( is_dir( $folder_album ) )
							{
								
								// Copy file anh goc
								$basename = $photo['basename'];
								$basename2 = $basename;
								$i = 1;
								while ( file_exists( NV_ROOTDIR . '/' . $currentpath . '/' . $basename2 ) )
								{
									$basename2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $basename );
									++$i;
								}
								$basename = $basename2;
								$filePath = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $photo['basename'];
								$newFilePath = $folder_album . '/' . $basename;
								
								$rename = nv_copyfile( $filePath, $newFilePath );
								if( ! $rename )
								{
									$error['warning'] = $lang_module['album_error_copy_photo'] . basename( $filePath );
									unset( $data['albums'][$key] );
								}
								else
								{
							
									// Xoa anh tam
									@nv_deletefile( $filePath );
									
									
									$upload_logo = '';
									if( file_exists( NV_ROOTDIR . '/' . $photo_config['module_logo'] ) && $photo_config['active_logo'] == 1 )
									{
										$upload_logo = $photo_config['module_logo'];
									} 
									if( ! empty( $upload_logo ) )
									{
										$logo_size = getimagesize( NV_ROOTDIR . '/' . $upload_logo );
 
										if( $photo['width'] <= 150 )
										{
											$w = ceil( $logo_size[0] * $photo_config['autologosize1'] / 100 );
										}
										elseif( $photo['width'] < 350 )
										{
											$w = ceil( $logo_size[0] * $photo_config['autologosize2'] / 100 );
										}
										else
										{
											if( ceil( $photo['width'] * $photo_config['autologosize3'] / 100 ) > $logo_size[0] )
											{
												$w = $logo_size[0];
											}
											else
											{
												$w = ceil( $photo['width'] * $photo_config['autologosize3'] / 100 );
											}
										}

										$h = ceil( $w * $logo_size[1] / $logo_size[0] );
										$x = $photo['width'] - $w - 5;
										$y = $photo['height'] - $h - 5;

										$config_logo = array();
										$config_logo['x'] = $photo['width'] - $w - 5;
										$config_logo['y'] = $photo['height'] - $h - 5;
										$config_logo['w'] = $w;
										$config_logo['h'] = $h;

										require_once NV_ROOTDIR . '/includes/class/image.class.php';
										$createImage = new image( $newFilePath, NV_MAX_WIDTH, NV_MAX_HEIGHT );
										$createImage->addlogo( NV_ROOTDIR . '/' . $upload_logo, '', '', $config_logo );
										$createImage->save( $folder_album, $basename );
										 
									}
 	
									$photo['file'] = substr( $newFilePath, strlen( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/images/' ) );
									
									
									// Copy file thumb
									//$thum_folder  = floor( $data['album_id'] / 1000 );
									$thumbName = $fileName = substr( $photo['thumb'], strlen( NV_BASE_SITEURL . NV_TEMP_DIR . '/' ) );
									$fileName2 = $fileName;
									$i = 1;
									while ( file_exists( NV_ROOTDIR . '/' . $currentpaththumb . '/' .  $fileName2 ) )
									{
										$fileName2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $fileName );
										++$i;
									}
									$fileName = $fileName2;
									$filePath = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $thumbName;
									$newFilePath = NV_ROOTDIR . '/' . $currentpaththumb . '/' . $fileName;
								
									$rename = nv_copyfile( $filePath, $newFilePath );
									if( ! $rename )
									{
										$error .= $lang_module['album_error_copy_photo'] . basename( $filePath ) ;
										unset( $data['albums'][$key] );
									}
									else
									{
										// Xoa anh tam
										@nv_deletefile( $filePath );
										$photo['thumb'] = substr( $newFilePath, strlen( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/thumb/' ) );
										
										$sth = $db->prepare( 'INSERT INTO ' . TABLE_PHOTO_NAME . '_rows SET 
											album_id = ' . (int)$data['album_id'] . ', 
											defaults = ' . (int)$photo['defaults'] . ', 
											size = ' . (int)$photo['size'] . ', 
											width = ' . (int)$photo['width'] . ', 
											height = ' . (int)$photo['height'] . ', 
											status=' . intval( 1 ) . ', 
											date_added=' . intval( NV_CURRENTTIME ) . ',  
											date_modified=' . intval( NV_CURRENTTIME ) . ', 
											name = :name,
											description = :description,
											file = :file,
											thumb = :thumb,
											mime = :mime' );

										$sth->bindParam( ':name', $photo['name'], PDO::PARAM_STR );
										$sth->bindParam( ':description', $photo['description'], PDO::PARAM_STR );
										$sth->bindParam( ':file', $photo['file'], PDO::PARAM_STR );
										$sth->bindParam( ':thumb', $photo['thumb'], PDO::PARAM_STR );
										$sth->bindParam( ':mime', $photo['mime'], PDO::PARAM_STR );
										$sth->execute();
										//$row_id = $db->lastInsertId();
										$sth->closeCursor();
										++$count;
									}
								}
							}
						}
						if( $count > 0 )
						{
							$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_album SET num_photo = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_rows WHERE album_id = '. $data['album_id'] .') WHERE album_id = '. $data['album_id'] );
						}
					}
					catch ( PDOException $e )
					{ 
						$error['warning'] = $lang_module['album_error_save'];
						//var_dump($e);
					}
					
					$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_category SET num_album = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_album WHERE category_id = '. $data['category_id'] .') WHERE category_id = '. $data['category_id'] );
							
					
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A Album', 'album_id: ' . $data['album_id'], $admin_info['userid'] );	 

				}
				else
				{
					$error['warning'] = $lang_module['album_error_save'];

				}
				$stmt->closeCursor();

			}
			else
			{
				
				try
				{
						
					$stmt = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_album SET 
						category_id = ' . intval( $data['category_id'] ) . ', 
						status=' . intval( $data['status'] ) . ', 
						date_added=' . intval( $data['date_added'] ) . ',  
						date_modified=' . intval( $data['date_modified'] ) . ', 
						name =:name,
						alias =:alias,
						description =:description,
						meta_title =:meta_title,
						meta_description =:meta_description,
						meta_keyword =:meta_keyword,
						model = :model,
						capturedate = :capturedate,
						capturelocal = :capturelocal,
						folder = :folder,
						layout = :layout,
						groups_view=:groups_view 
						WHERE album_id=' . $data['album_id'] );
						
					$folder =  $imagepath . '/' . $data['folder']; 
					
					$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
					$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
					$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_title', $data['meta_title'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_description', $data['meta_description'], PDO::PARAM_STR );
					$stmt->bindParam( ':meta_keyword', $data['meta_keyword'], PDO::PARAM_STR );
					$stmt->bindParam( ':model', $data['model'], PDO::PARAM_STR );
					$stmt->bindParam( ':capturedate', $data['capturedate'], PDO::PARAM_STR );
					$stmt->bindParam( ':capturelocal', $data['capturelocal'], PDO::PARAM_STR );
					$stmt->bindParam( ':folder', $folder, PDO::PARAM_STR );
					$stmt->bindParam( ':layout', $data['layout'], PDO::PARAM_STR );
					$stmt->bindParam( ':groups_view', $data['groups_view'], PDO::PARAM_STR );
 	 
					if( $stmt->execute() )
					{
						try
						{
							$count = 0;	
							foreach( $data['albums'] as $key => $photo )
							{
								$photo['row_id'] = isset( $photo['row_id'] ) ? $photo['row_id'] : 0;	
 								$photo['name'] = isset( $photo['name'] ) ? $photo['name'] : '';	
								$photo['filePath'] = isset( $photo['filePath'] ) ? $photo['filePath'] : $photo['filePath'];	
								$photo['image_url'] = isset( $photo['image_url'] ) ? $photo['image_url'] : $photo['image_url'];	
								$photo['thumb'] = isset( $photo['thumb'] ) ? $photo['thumb'] : $photo['thumb'];	
 								
								$photo['description'] = isset( $photo['description'] ) ? $photo['description'] : '';	
								$photo['description'] = nv_nl2br( nv_htmlspecialchars( strip_tags( $photo['description'] ) ), '<br />' );
 								$photo['defaults'] = isset( $photo['defaults'] ) ? $photo['defaults'] : 0;	
								
								if( $photo['row_id'] == 0 )
								{
 
									// Kiem tra anh hop le
			
									$image_info = nv_is_image( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/'  . $photo['basename'] );

									if( empty( $image_info ) or ! isset( $mime['images'][$image_info['ext']] ) )
									{
										$error['error_image'][] = $lang_module['album_error_mime'] . ' ' . $photo['basename'];
										
										@nv_deletefile( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $photo['basename'] );
									
										unset( $data['albums'][$key] );
									}
									else
									{
										$photo['width'] = $image_info['width'];
										$photo['height'] = $image_info['height'];
										$photo['mime'] = $image_info['mime'];
										$photo['size'] = filesize( $image_info['src'] );
									}
		 
									$folder_album = NV_ROOTDIR . '/' . $currentpath . '/'. $data['folder'];
									var_dump($folder_album);
									if( is_dir( $folder_album ) )
									{
										// Copy file anh goc
										$basename = basename( $photo['basename'] );
										$basename2 = $basename;
										$i = 1;
										while ( file_exists( NV_ROOTDIR . '/' . $currentpath . '/' . $basename2 ) )
										{
											$basename2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $basename );
											++$i;
										}
										$basename = $basename2;
										$filePath = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $photo['basename'];
										$newFilePath = $folder_album . '/' . $basename;
										
										$rename = nv_copyfile( $filePath, $newFilePath );
										
										if( ! $rename )
										{
											$error['warning'] = $lang_module['album_error_copy_photo'] . basename( $filePath );
											unset( $data['albums'][$key] );
										}
										else
										{
									
											// Xoa anh tam
											@nv_deletefile( $filePath );
											
											
											$upload_logo = '';
											if( file_exists( NV_ROOTDIR . '/' . $photo_config['module_logo'] ) && $photo_config['active_logo'] == 1 )
											{
												$upload_logo = $photo_config['module_logo'];
											} 
											if( ! empty( $upload_logo ) )
											{
												$logo_size = getimagesize( NV_ROOTDIR . '/' . $upload_logo );
		 
												if( $photo['width'] <= 150 )
												{
													$w = ceil( $logo_size[0] * $photo_config['autologosize1'] / 100 );
												}
												elseif( $photo['width'] < 350 )
												{
													$w = ceil( $logo_size[0] * $photo_config['autologosize2'] / 100 );
												}
												else
												{
													if( ceil( $photo['width'] * $photo_config['autologosize3'] / 100 ) > $logo_size[0] )
													{
														$w = $logo_size[0];
													}
													else
													{
														$w = ceil( $photo['width'] * $photo_config['autologosize3'] / 100 );
													}
												}

												$h = ceil( $w * $logo_size[1] / $logo_size[0] );
												$x = $photo['width'] - $w - 5;
												$y = $photo['height'] - $h - 5;

												$config_logo = array();
												$config_logo['x'] = $photo['width'] - $w - 5;
												$config_logo['y'] = $photo['height'] - $h - 5;
												$config_logo['w'] = $w;
												$config_logo['h'] = $h;

												require_once NV_ROOTDIR . '/includes/class/image.class.php';
												$createImage = new image( $newFilePath, NV_MAX_WIDTH, NV_MAX_HEIGHT );
												$createImage->addlogo( NV_ROOTDIR . '/' . $upload_logo, '', '', $config_logo );
												$createImage->save( $folder_album, $basename );
												 
											}
			
											$photo['file'] = substr( $newFilePath, strlen( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/images/' ) );
											
											
											// Copy file thumb
											//$thum_folder  = floor( $data['album_id'] / 1000 );
											$thumbName = $fileName = substr( $photo['thumb'], strlen( NV_BASE_SITEURL . NV_TEMP_DIR . '/' ) );
											$fileName2 = $fileName;
											$i = 1;
											while ( file_exists( NV_ROOTDIR . '/' . $currentpaththumb . '/' .  $fileName2 ) )
											{
												$fileName2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $fileName );
												++$i;
											}
											$fileName = $fileName2;
											$filePath = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $thumbName;
											$newFilePath = NV_ROOTDIR . '/' . $currentpaththumb . '/' . $fileName;
										
											$rename = nv_copyfile( $filePath, $newFilePath );
											if( ! $rename )
											{
												$error .= $lang_module['album_error_copy_photo'] . basename( $filePath ) ;
												unset( $data['albums'][$key] );
											}
											else
											{
												// Xoa anh tam
												@nv_deletefile( $filePath );
												$photo['thumb'] = substr( $newFilePath, strlen( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/thumb/' ) );
												
												$sth = $db->prepare( 'INSERT INTO ' . TABLE_PHOTO_NAME . '_rows SET 
													album_id = ' . (int)$data['album_id'] . ', 
													defaults = ' . (int)$photo['defaults'] . ', 
													size = ' . (int)$photo['size'] . ', 
													width = ' . (int)$photo['width'] . ', 
													height = ' . (int)$photo['height'] . ', 
													status=' . intval( 1 ) . ', 
													date_added=' . intval( NV_CURRENTTIME ) . ',  
													date_modified=' . intval( NV_CURRENTTIME ) . ', 
													name = :name,
													description = :description,
													file = :file,
													thumb = :thumb,
													mime = :mime' );

												$sth->bindParam( ':name', $photo['name'], PDO::PARAM_STR );
												$sth->bindParam( ':description', $photo['description'], PDO::PARAM_STR );
												$sth->bindParam( ':file', $photo['file'], PDO::PARAM_STR );
												$sth->bindParam( ':thumb', $photo['thumb'], PDO::PARAM_STR );
												$sth->bindParam( ':mime', $photo['mime'], PDO::PARAM_STR );
												$sth->execute();
												$sth->closeCursor();
												++$count;
											}
										}
									}
									
 
								}else
								{
									$sth = $db->prepare( 'UPDATE ' . TABLE_PHOTO_NAME . '_rows SET 
											defaults = ' . (int)$photo['defaults'] . ',  
											date_modified=' . intval( NV_CURRENTTIME ) . ', 
											name = :name,
											description = :description 
											WHERE row_id=' . $photo['row_id'] );

									$sth->bindParam( ':name', $photo['name'], PDO::PARAM_STR );
									$sth->bindParam( ':description', $photo['description'], PDO::PARAM_STR );
									$sth->execute();
									$sth->closeCursor();
									++$count;
								}
							}
							
							if( $count > 0 )
							{
								$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_album SET num_photo = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_rows WHERE album_id = '. $data['album_id'] .') WHERE album_id = '. $data['album_id'] );
							}
							
							if( $data['old_category_id'] == $data['category_id'] )
							{
								$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_category SET num_album = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_album WHERE category_id = '. $data['category_id'] .') WHERE category_id = '. $data['category_id'] );
							}else
							{
								$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_category SET num_album = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_album WHERE category_id = '. $data['category_id'] .') WHERE category_id = '. $data['category_id'] );
								$db->query('UPDATE ' . TABLE_PHOTO_NAME . '_category SET num_album = (SELECT COUNT(*) FROM ' . TABLE_PHOTO_NAME . '_album WHERE category_id = '. $data['old_category_id'] .') WHERE category_id = '. $data['old_category_id'] );
							}
							
							
						}
						catch ( PDOException $e )
						{ 
							$error['warning'] = $lang_module['album_error_save'];
							//var_dump($e);
						}
 
						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A Album', 'album_id: ' . $data['album_id'], $admin_info['userid'] );
						
					}
					else
					{
						$error['warning'] = $lang_module['album_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{ 
					$error['warning'] = $lang_module['album_error_save'];
					// var_dump($e);
				}

			}

		}
		
		if( empty( $error ) )
		{
			// nv_del_moduleCache( $module_name );
			// Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=album' );
			// die();
		}

	}
	
	
	$data['capturedate'] = !empty( $data['capturedate'] ) ? date('d/m/Y', $data['capturedate']) : '';

	$xtpl = new XTemplate( 'album_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'UPLOAD_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=upload&token=' . md5( $nv_Request->session_id . $global_config['sitekey'] ) );

	
	foreach( $global_photo_cat as $catid_i => $array_value )
	{
		$lev_i = $array_value['lev'];
 
		$xtitle_i = '';
		if( $lev_i > 0 )
		{
			$xtitle_i .= '&nbsp;&nbsp;&nbsp;|';
			for( $i = 1; $i <= $lev_i; ++$i )
			{
				$xtitle_i .= '---';
			}
			$xtitle_i .= '>&nbsp;';
		}
		$xtitle_i .= $array_value['name'];
		$sl = '';
		if( $catid_i == $data['category_id'] )
		{
			$sl = ' selected="selected"';
		}
		$xtpl->assign( 'CATALOG', array(
			'key' => $catid_i,
			'selected' => $sl,
			'name' => $xtitle_i ) );
		$xtpl->parse( 'main.category' );
 	 
	}
	
	$num_row = 0;
	if( !empty( $data['albums'] ) )
	{
		foreach( $data['albums'] as $key => $photo )
		{
			$photo['key'] = $key;
			$xtpl->assign( 'PHOTO', $photo );
			$xtpl->parse( 'main.photo' );
			
			++$num_row;
		}
	}
	$xtpl->assign( 'num_row', $num_row );
	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}
	
	if( isset( $error['folder'] ) )
	{
		$xtpl->assign( 'error_folder', $error['folder'] );
		$xtpl->parse( 'main.error_folder' );
	}
	
	if( isset( $error['category'] ) )
	{
		$xtpl->assign( 'error_category', $error['category'] );
		$xtpl->parse( 'main.error_category' );
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
 
	foreach( $layout_array as $value )
	{
		$value = preg_replace( $global_config['check_op_layout'], '\\1', $value );
		$xtpl->assign( 'LAYOUT', array( 'key' => $value, 'selected' => ( $data['layout'] == $value ) ? ' selected="selected"' : '' ) );
		$xtpl->parse( 'main.layout' );
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


if( ACTION_METHOD == 'get_album' )
{
	$name = $nv_Request->get_string( 'filter_name', 'get', '' );
	$info = array();

	$and = '';
	if( ! empty( $name ) )
	{
		$and .= ' AND name LIKE :name ';
	}

	$sql = 'SELECT album_id, name FROM ' . TABLE_PHOTO_NAME . '_album  
	WHERE 1 ' . $and . '
	ORDER BY name DESC LIMIT 0, 10';

	$sth = $db->prepare( $sql );

	if( ! empty( $name ) )
	{
		$sth->bindValue( ':name', '%' . $name . '%' );
	}
	$sth->execute();
	while( list( $album_id, $name ) = $sth->fetch( 3 ) )
	{
		$info[] = array( 'album_id' => $album_id, 'name' => nv_htmlspecialchars( $name ) );
	}
	header( 'Content-Type: application/json' );
	echo json_encode( $info );
	exit();
}

/*show list album*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$data['filter_status'] = $nv_Request->get_string( 'filter_status', 'get', '' );
$data['filter_name'] = strip_tags( $nv_Request->get_string( 'filter_name', 'get', '' ) );
$data['filter_date_added'] = $nv_Request->get_string( 'filter_date_added', 'get', '' );
$data['filter_category'] = $nv_Request->get_int( 'filter_category', 'get', 0 );

$sort = $nv_Request->get_string( 'sort', 'get', '' );
$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';
 
 
$sql = TABLE_PHOTO_NAME . '_album WHERE 1';
 
if( ! empty( $data['filter_name'] ) )
{
	$sql .= " AND name LIKE '" . $db->dblikeescape( $data['filter_name'] ) . "%'";
}
 
if( $data['filter_category'] > 0 )
{
	$sql .= " AND category_id = " . ( int )$data['filter_category'];
}
 
if( isset( $data['filter_status'] ) && is_numeric( $data['filter_status'] ) )
{
	$sql .= " AND status = " . ( int )$data['filter_status'];
}

if( preg_match( '/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $data['filter_date_added'], $m ) )
{
	$date_added_start = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	$date_added_end = $date_added_start + 86399;

	$sql .= " AND date_added BETWEEN " . $date_added_start . " AND " . $date_added_end . "";
}
$sort_data = array( 'name', 'category_id', 'date_added' );
if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= " ORDER BY " . $sort;
}
else
{
	$sql .= " ORDER BY date_added";
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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=album&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );
 
$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'album.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'TOKEN', md5( $global_config['sitekey'] . session_id() ) );
$xtpl->assign( 'URL_SEARCH', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=get_album' );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
$xtpl->assign( 'URL_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=name&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_WEIGHT', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=weight&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_category', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=category_id&amp;order=' . $order2 . '&amp;per_page=' . $per_page );

$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op . "&action=add" );
 
/*search*/

foreach( $global_photo_cat as $key => $value )
{
	$xtitle_i = '';
	if( $value['lev'] > 0 )
	{
		$xtitle_i .= '&nbsp;&nbsp;&nbsp;|';
		for( $i = 1; $i <= $value['lev']; ++$i )
		{
			$xtitle_i .= '---';
		}
		$xtitle_i .= '>&nbsp;';
	}
	$xtitle_i .= $value['name'];	
	$xtpl->assign( 'category', array( 'key'=> $key, 'name'=> $xtitle_i, 'selected'=> ( $key == $data['filter_category'] ) ? 'selected="selected"': '' ) );
	$xtpl->parse( 'main.filter_category' );
	
}
foreach( $array_status as $key => $name )
{
	$xtpl->assign( 'STATUS', array( 'key'=> $key, 'name'=> $name, 'selected'=> ( $key == $data['filter_status'] && is_numeric( $data['filter_status'] ) ) ? 'selected="selected"': '' ) );
	$xtpl->parse( 'main.filter_status' );
}
 
 
if( ! empty( $array ) )
{
	foreach( $array as $item )
	{
 

		$item['category'] = isset( $global_photo_cat[$item['category_id']] ) ? $global_photo_cat[$item['category_id']]['name'] : 'N/A';
		$item['category_link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=album&filter_category=" . $item['category_id'];
		$item['date_added'] = nv_date( 'd/m/Y', $item['date_added'] );
		$item['token'] = md5( $global_config['sitekey'] . session_id() . $item['album_id'] );
		
		$item['link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=album&action=view&token=" . $item['token'] . "&album_id=" . $item['album_id'];
		$item['edit'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=album&action=edit&token=" . $item['token'] . "&album_id=" . $item['album_id'];

 
		$xtpl->assign( 'LOOP', $item );
		
		foreach( $array_status as $key => $name )
		{
			$xtpl->assign( 'STATUS', array( 'key'=> $key, 'name'=> $name, 'selected'=> ( $key == $item['status'] ) ? 'selected="selected"': '' ) );
			 $xtpl->parse( 'main.loop.status' );
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
