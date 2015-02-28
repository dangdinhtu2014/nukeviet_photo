<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if ( ! defined( 'NV_IS_MOD_PHOTO' ) ) die( 'Stop!!!' );

$page = 1;
$per_page = 200;

$count_op = sizeof( $array_op );
 
if( $count_op == 1 or substr( $array_op[1], 0, 5 ) == 'page-' )
{
	if( $count_op > 1 )
	{
		$page = intval( substr( $array_op[1], 5 ) );
	}
}

if( empty( $count_op ) )
{
	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( NV_PREFIXLANG . '_' . $module_data . '_album' )
		->where( 'status=1' );

	$num_items = $db->query( $db->sql() )->fetchColumn();

	$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sitemap-image'; 
	
	$total_pages = ceil( $num_items / $per_page );

	$content="";
	$content.="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	$content.="<?xml-stylesheet type=\"text/xsl\" href=\"/themes/default/css/sitemapindex.xsl\" ?>\n";
	$content.="<sitemapindex xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd\">\n";
	for( $pages = 1; $pages<= $total_pages; ++$pages )
	{
		$link = NV_MAIN_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=sitemap-image/page-'. $pages, true ); 	
		$content.="        <sitemap>\n";
		$content.="            <loc>" . $link . "</loc>\n";
		$content.="        </sitemap>\n";
	}
	$content.="</sitemapindex>\n";
	echo $content;
	die();
}
else
{
	$array_url = array();
	$cacheFile = NV_LANG_DATA . '_sitemap_image_'. $page  .'_' . NV_CACHE_PREFIX . '.cache';

	$pa = NV_CURRENTTIME - 7200;

	if( ( $cache = nv_get_cache( $module_name, $cacheFile ) ) != false and filemtime( NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module_name . '/' . $cacheFile ) >= $pa )
	{
		$array_url = unserialize( $cache );
	}
	else
	{
		
		$db->sqlreset()
			->select( 'album_id, catalogs_id, name, alias, meta_title' )
			->from( NV_PREFIXLANG . '_' . $module_data . '_album' )
			->where( 'status=1' )
			->order( 'date_added ASC' )
			->limit( $per_page )->offset( ( $page - 1 ) * $per_page );
		$albums = $db->query( $db->sql() );
		
		while( $album = $albums->fetch() )
		{
			$album['link'] = NV_MAIN_DOMAIN . nv_url_rewrite( $global_photo_cat[$album['catalogs_id']]['link'] . '/' . $album['alias'] . '-' . $album['album_id'] . $global_config['rewrite_exturl'], true );
 
			$album['photo'] = array();
			$db->sqlreset()
				->select( 'row_id, file' )
				->from( NV_PREFIXLANG . '_' . $module_data . '_rows' )
				->where( 'status=1 AND album_id=' . $album['album_id'] );	
			
			$photo = $db->query( $db->sql() );
			while( $row = $photo->fetch() )
			{
				$album['photo'][] = array(
					'image' => NV_MAIN_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/images/' . $row['file'],
					'row_id' => $row['row_id'],  
					//'caption' => $album['meta_title'],  
					//'title' => $album['name'],   
				);
			}
			$photo->closeCursor();
			
			$array_url[] = $album;
		}
	 
		
		$cache = serialize( $array_url );
		nv_set_cache( $module_name, $cacheFile, $cache );
	}
	$content ="";
	$content.="<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\" xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">\n";
    foreach( $array_url as $data )
	{
		$content.="<url>\n";
		$content.="    <loc>".$data['link']."</loc>\n";
		foreach( $data['photo'] as $array )
		{
			$content.="    <image:image>\n";
			$content.="        <image:loc>". $array['image']. "</image:loc>\n";
			$content.="        <image:title>".$data['name']." ". $array['row_id'] ."</image:title>\n";
			$content.="        <image:caption>".$data['meta_title']." ". $array['row_id'] ."</image:caption>\n";
			$content.="    </image:image>\n";
		}
		
		$content.="</url>\n";
	}
	$content.="</urlset>\n";
	echo $content;
	die();
	
	
}
