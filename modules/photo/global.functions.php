<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 22 Jul 2013 21:41:59 GMT
 */
  

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

global $global_photo_cat, $photo_config;

$photo_config = array();
$sql = 'SELECT *  FROM ' . NV_PREFIXLANG . '_' . $module_data . '_setting';
$list = nv_db_cache( $sql, 'setting', $module_name );
foreach( $list as $l )
{
	$photo_config[$l['config_name']] = $l['config_value'];
}
unset( $sql, $list );

$global_photo_cat = array();
$sql = 'SELECT * FROM ' . TABLE_PHOTO_NAME . '_catalogs ORDER BY sort_order ASC';
$list = nv_db_cache( $sql, 'catalogs_id', $module_name );
foreach( $list as $l )
{
	$global_photo_cat[$l['catalogs_id']] = $l;
	$global_photo_cat[$l['catalogs_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
	
}
unset( $sql, $list );

