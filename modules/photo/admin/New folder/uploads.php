<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  7, 26, 2013 17:16
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( $nv_Request->isset_request( 'status', 'get,post' ) )
{
	if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );
	
	$albumid = $nv_Request->get_string( 'albumid', 'post', 0 );
	
	if( ! $albumid )
	{
		die( 'NO' );
	}
	
	$sql = "SELECT  album_name, ab_thumb, status FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid=" . $albumid;
	$query = $db->query( $sql );
	list( $album_name, $ab_thumb, $status ) = $query->fetch( 3 );
	
	
	if(empty($ab_thumb) and $status==0)
	{
		die ($sql."Lỗi: Bạn cần thêm ảnh đại diện cho album mới kích hoạt được album");
	}else
	{
		$status = $status ? 0 : 1;
		$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET status='".$status."' WHERE albumid=" . $albumid;
		if($db->query( $sql ))
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, "Sửa trạng thái", 'albumid: ' . $albumid . ' - album_name: ' . $album_name, $admin_info['userid'] );
			die ("OK");
		}else
		{
			die ("Lỗi: Không thay đổi trạng thái được");
		}
	}
	exit();
}
if( $nv_Request->isset_request( 'type', 'get,post' ) )
{
	if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );
	
	$albumid = $nv_Request->get_string( 'albumid', 'post', 0 );
	
	if( ! $albumid )
	{
		die( 'NO' );
	}
	
	$sql = "SELECT  album_name, type FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid=" . $albumid;
	$query = $db->query( $sql );
	list( $album_name, $type ) = $query->fetch( 3 );
	
		$type = $type ? 0 : 1;
		$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET type='".$type."' WHERE albumid=" . $albumid;
		if($db->query( $sql ))
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, "Sửa dự án mẫu", 'albumid: ' . $albumid . ' - album_name: ' . $album_name, $admin_info['userid'] );
			die ("OK");
		}else
		{
			die ("Lỗi: Không thay đổi trạng thái dự án mẫu");
		}
	
	exit();
}


if( $nv_Request->isset_request( 'upload', 'get,post' ) )
{
	$imgfile = $nv_Request->get_string( 'imgfile', 'post','' );
	$alias_ab = $nv_Request->get_string( 'alias_ab', 'post','' );
	$leftX = $nv_Request->get_int( 'leftX', 'post', 0 );
	$leftY = $nv_Request->get_int( 'leftY', 'post', 0 );
	$newwidth1 = $newwidth = $nv_Request->get_int( 'newwidth', 'post', 0 );
	$newheight1 = $newheight = $nv_Request->get_int( 'newheight', 'post', 0 );
	$imgw = $nv_Request->get_int( 'imgw', 'post', 0 );
	$imgh = $nv_Request->get_int( 'imgh', 'post', 0 );
	$tems = $nv_Request->get_string( 'tems', 'post', 0 );
	$albumid = $nv_Request->get_string( 'albumid', 'post', 0 );
	$rid = $nv_Request->get_string( 'rid', 'post', 0 );
	
	$imgfile = NV_ROOTDIR . "/" . $imgfile;
	
	if( file_exists( $imgfile ) )
	{
		require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );

		$basename = basename( $imgfile );
		$image = new image( $imgfile, NV_MAX_WIDTH, NV_MAX_HEIGHT );
		$thumb_basename = $basename;
		$i = 1;
		while( file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias_ab.'/' . $thumb_basename ) )
		{
			$thumb_basename = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', 'thumb'.$newwidth.'x'.$newheight.'\1_' . $i . '\2', $basename );
			++$i;
		}
		
		$quality = 100;
		if(($imgw * $tems)>=630)
		{
			$leftX = $leftX * $tems;
			$leftY = $leftY * $tems;
			$newwidth = $newwidth*$tems;
			$newheight = $newheight*$tems;
			$image->cropFromLeft( $leftX, $leftY, $newwidth, $newheight );
			$image->resizeXY( 630, 380 );
		}else
		{
			$image->cropFromLeft( $leftX, $leftY, $newwidth, $newheight );
		}
		$image->save( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias_ab.'/', $thumb_basename, $quality );
		$image_info = $image->create_Image_info;
		$ab_thumb = str_replace( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias_ab.'/', '', $image_info['src'] );
		
		$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET ab_thumb='" . $ab_thumb . "', status='1' WHERE albumid=" . $albumid;
		if($db->query( $sql ))
		{
			$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET defaults = '0' WHERE albumid =".$albumid."" );
			$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET defaults = '1' WHERE rid =".$rid."" );
			die('OK[NV3]'.NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias_ab.'/' . $ab_thumb);
		}else
		{
			die('ERR[NV3]'.$sql);
		}
	}else
	{
		die('ERR[NV3]Lỗi không tồn tại đường dẫn ảnh');
	}
	exit();
}



if( $nv_Request->isset_request( 'act', 'get,post' ) )
{
	if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

	$loop = $nv_Request->get_string( 'loop', 'post', '' );
	$rid = $nv_Request->get_int( 'rid', 'post', 0 );
	$albumid = $nv_Request->get_int( 'albumid', 'post', 0 );

	if( empty( $rid ) ) die( 'Stop!!!' );

    $query = "SELECT status FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE rid=" . $rid." AND albumid='" . $albumid."'";
    $result = $db->query( $query );
    $numrows = $result->rowCount();
    if( $numrows != 1 ) die( 'NO' );

    $status = $result->fetchColumn();
    $status = $status ? 0 : 1;

    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET status=" . $status . " WHERE rid=" . $rid;
    $db->query( $sql );
    die( "OK|" . $loop );
}

if( $nv_Request->isset_request( 'del', 'get,post' ) )
{
	$del = $nv_Request->get_int( 'del', 'get,post', 0 );
	$contents = "OK_2";
	if( $del == 1 )
	{
		$rid = $nv_Request->get_int( 'rid', 'post', 0 );
		$albumid = $nv_Request->get_int( 'albumid', 'post', 0 );
		list( $albumid, $alias ) = $db->query( "SELECT albumid, alias FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE rid=" . $rid . " AND albumid=" . $albumid )->fetch( 3 );
		if( $albumid > 0 )
		{
			list( $picture, $thumb ) = $db->query( "SELECT picture, thumb FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE rid=" . $rid . " AND albumid=" . $albumid )->fetch( 3 );
		
			$sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE rid=" . $rid . " AND albumid=" . $albumid . "";
			if( $db->query( $sql ) )
			{
				$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET numphoto = numphoto - 1 WHERE albumid =".$albumid."" );
				nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $alias."/" . $picture, true );
				nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $alias."/" . $thumb, true );

				$contents = "OK[NV3]1";
			}
			else
			{
				$contents = "ERR[NV3]Lỗi không xóa được ảnh này vui lòng liên hệ bản quản trị sửa lỗi này";
			}
		}
		else
		{
			$contents = "OK[NV3]0";
		}
	}
	die($contents);
}




$albumid = $nv_Request->get_int( 'albumid', 'post,get', 0 );
$sql = "SELECT albumid, album_name, alias FROM " . NV_PREFIXLANG . "_" . $module_data . "_album WHERE albumid = '" . $albumid . "'";
$result = $db->query( $sql );
$num = $result->rowCount();

if( $num > 0 )
{
	list( $albumid, $album_name, $alias ) = $result->fetch( 3 );
	$alias = $alias;

	$page_title = $lang_module['uploads'] . " " . $album_name;

	$my_head = "";
	$my_head .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . NV_BASE_SITEURL . "modules/" . $module_file . "/js/crop.css\" />\n";
	$my_head .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . NV_BASE_SITEURL . "modules/" . $module_file . "/uploadify/uploadify.css\" />\n";
	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "modules/" . $module_file . "/js/jquery.Jcrop.min.js\"></script>\n";
	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "modules/" . $module_file . "/uploadify/jquery.uploadify.min.js\"></script>\n";

	$my_head .= "<script type=\"text/javascript\">
		var swf_link = '" . NV_BASE_SITEURL . "modules/" . $module_file . "/uploadify/uploadify.swf';
		var upload_link = '" . NV_BASE_SITEURL . "modules/" . $module_file . "/uploadify/uploadify.php?lang=" . NV_LANG_DATA . "';
		var cancel_link = '" . NV_BASE_SITEURL . "modules/" . $module_file . "/uploadify/cancel.png';
		var folder = '" . $alias . "';
		var albumid = '" . $albumid . "';
		var module_logo = '" . $config['module_logo'] . "';
		</script>\n";
	$my_head.= "<script type=\"text/javascript\">var module_file = '".$module_file."'</script>";
	$my_head.= "<script type=\"text/javascript\" src=\"".NV_BASE_SITEURL."modules/".$module_file."/js/lightbox.js\"></script>";
	$my_head.="	<script type=\"text/javascript\">";
	$my_head.="	$(document).ready(function(){	";	
	$my_head.="		$('.duan-thumb').lightBox();";
	$my_head.="	})";
	$my_head.="	</script>";
	
	
	$xtpl = new XTemplate( "uploads.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
	$xtpl->assign( 'folder', $alias );
	$xtpl->assign( 'albumid', $albumid );
	$xtpl->assign( 'num_upload', $config['num_upload'] );

	$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE albumid=" . $albumid . "";
	$result = $db->query( $sql );
	$i = 1;
	$tem = 0;
	
	while( $item = $result->fetch() )
	{
		
		if( ! nv_is_url( $item['picture'] ) and ! file_exists( NV_DOCUMENT_ROOT . "/" . NV_UPLOADS_DIR . "/" . $module_name . "/".$alias."/" . $item['picture'] ) )
		{

			if($db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE rid=" . $item['rid'] ))
			{
				@nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . "/".$alias."/" . $item['picture'] );
				@nv_deletefile( NV_UPLOADS_REAL_DIR . '/' . $module_name . "/".$alias."/" . $item['thumb'] );
			}
		}
		else
		{
			$item['i']  = $i;
			$item['thumbs'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias.'/' . $item['thumb'];
			$item['pictures'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/'.$alias.'/' . $item['picture'];
			
			$status_checked = ( $item['status'] ) ? "  checked=\"checked\"" : "";
			$xtpl->assign( 'status_checked', $status_checked );
			
			$default_checked = ( $item['default'] ) ? "  checked=\"checked\"" : "";
			$xtpl->assign( 'default_checked', $default_checked );
			
			$w_h = explode('x', $item['w_h']);
			if($w_h[0]>=630)
			{
				$tems = $w_h[0]/630;
				$item['w'] = $w_h[0]/$tems;
				$item['h'] = $w_h[1]/$tems;
			}
			else
			{
				$tems = 0; 
				$item['w'] = $w_h[0];
				$item['h'] = $w_h[1];
			}
			$xtpl->assign( 'tems', $tems );
			$xtpl->assign( 'loop', $item );
			$xtpl->parse( 'main.loop' );
			++$i;
			++$tem;
		}

	}
	$xtpl->assign( 'tem', $tem );
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
}
else
{
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "" );
	die();
}
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';

