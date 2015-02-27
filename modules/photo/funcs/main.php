<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 22 Jul 2013 21:41:59 GMT
 */

if( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];
 
if( $photo_config['home_view'] == 'home_view_grid_by_cat' )
{
	$array_cat = array();
	if( ! empty( $global_photo_cat ) )
	{ 
		$key = 0;
		foreach( $global_photo_cat as $_catalogs_id => $catalogs  )
		{
			if( $catalogs['parent_id'] == 0 and $catalogs['inhome'] == 1 )
			{
				$array_cat[$key] = $catalogs;
				$sql = 'SELECT a.album_id, a.catalogs_id, a.name, a.alias, a.capturelocal, a.description, a.num_photo, a.date_added, r.file, r.thumb FROM ' . TABLE_PHOTO_NAME . '_album a 
						LEFT JOIN  ' . TABLE_PHOTO_NAME . '_rows r ON ( a.album_id = r.album_id )
						WHERE a.status= 1 AND a.catalogs_id=' . $_catalogs_id . ' AND r.defaults = 1 
						ORDER BY a.date_added DESC 
						LIMIT 0 , ' . $catalogs['numlinks'];
				$result = $db->query( $sql );

				while( $item = $result->fetch() )
				{
					$item['link'] = $global_photo_cat[$_catalogs_id]['link'] . '/' . $item['alias'] . '-' . $item['album_id'] . $global_config['rewrite_exturl'];
			
					$array_cat[$key]['content'][] = $item;
				}
				$result->closeCursor();
				
				++$key;
			}
		}
	}
 
	$contents = home_view_grid_by_cat( $array_cat );
	
}
elseif( $photo_config['home_view'] == 'home_view_grid_by_album' )
{
	
	
}


// $numalbum = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_album' )->fetchColumn();


include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
