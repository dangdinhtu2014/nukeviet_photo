<?php

/**
 * @Project NUKEVIET 4.x
 * @Author ĐẶNG ĐÌNH TỨ (dlinhvan@gmail.com)
 * @Copyright (C) 2010 webdep24.com All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 10/08/2012 08:00
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

if( empty( $global_photo_cat ) )
{
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=cat" );
	die();
}

if( defined( 'NV_EDITOR' ) )
{
	require_once ( NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php' );
}

$error = array();

$page_title = $lang_module['add_ab'];

$catid = $nv_Request->get_int( 'catid', 'get', 0 );
$parentid = $nv_Request->get_int( 'parentid', 'get', 0 );

$data = array(
	"albumid" => 0,
	"catid" => $catid,
	"listcatid" => "" . $catid . "," . $parentid . "",
	"numphoto" => 0,
	"view" => 0,
	"album_name" => "",
	"alias" => "",
	"status" => 1,
	"type" => 0,
	"model" => "",
	"capturedate" => "",
	"capturelocal" => "",
	"description" => "",
	"keywords" => "",
	"add_time" => NV_CURRENTTIME,
	"edit_time" => NV_CURRENTTIME );

$data['albumid'] = $nv_Request->get_int( 'albumid', 'get,post', 0 );
if( $data['albumid'] > 0 )
{
	$data = $db->query( "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_album where albumid=" . $data['albumid'] )->fetch();
	$page_title = $lang_module['edit_ab'];
}

if( $nv_Request->get_int( 'save', 'post' ) == 1 )
{
	$data['album_name'] = $nv_Request->get_title( 'album_name', 'post', '', 1 );

	$data['alias'] = strtolower( change_alias( $data['album_name'] ) );

	$data['model'] = $nv_Request->get_title( 'model', 'post', '', 1 );
	$data['capturelocal'] = $nv_Request->get_title( 'capturelocal', 'post', '', 1 );
	$data['keyword'] = $nv_Request->get_title( 'keyword', 'post', '', 1 );

	$catids = array_unique( $nv_Request->get_typed_array( 'catids', 'post', 'int', array() ) );

	$data['catid'] = $nv_Request->get_int( 'catid', 'post', 0 );
	$data['type'] = $nv_Request->get_int( 'type', 'post', 0 );

	$data['listcatid'] = implode( ",", $catids );

	$capturedate = $nv_Request->get_title( 'capturedate', 'post', '' );
	if( ! empty( $capturedate ) and preg_match( "/^([0-9]{1,2})\\/([0-9]{1,2})\/([0-9]{4})$/", $capturedate, $m ) )
	{

		$data['capturedate'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
	}
	else
	{
		$data['capturedate'] = 0;
	}

	$description = $nv_Request->get_string( 'description', 'post', '' );
	$data['description'] = defined( 'NV_EDITOR' ) ? nv_nl2br( $description, '' ) : nv_nl2br( nv_htmlspecialchars( strip_tags( $description ) ), '<br />' );

	$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );

	$data['keywords'] = $nv_Request->get_title( 'keywords', 'post', '' );

	if( empty( $data['album_name'] ) )
	{
		$error[] = $lang_module['error_album_name'];
	}
	elseif( empty( $data['listcatid'] ) )
	{
		$error[] = $lang_module['error_cat'];
	}
	if( empty( $error ) )
	{
		$data['catid'] = in_array( $data['catid'], $catids ) ? $data['catid'] : $catids[0];

		if( $data['albumid'] == 0 )
		{
			$sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_album VALUES 
                (NULL, 
                " . intval( $data['catid'] ) . ",
				" . $db->quote( $data['listcatid'] ) . ",
				'',
                " . intval( $data['numphoto'] ) . ",
                " . intval( $data['view'] ) . ",
                " . $db->quote( $data['album_name'] ) . ",
                " . $db->quote( $data['alias'] ) . ",
				0,
				" . intval( $data['type'] ) . ",
                " . $db->quote( $data['model'] ) . ",
                " . $db->quote( $data['capturedate'] ) . ",
                " . $db->quote( $data['capturelocal'] ) . ",
                " . $db->quote( $data['keywords'] ) . ",
                " . $db->quote( $data['description'] ) . ",
				0, 0,
                " . intval( $data['add_time'] ) . ",
                " . intval( $data['edit_time'] ) . "
				)";

			$data['albumid'] = $db->insert_id( $sql );
			if( $data['albumid'] > 0 )
			{
				$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET  numalbum = numalbum + 1 WHERE catid =" . $data['catid'] . "" );

				if( ! empty( $data['alias'] ) and ! is_dir( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $data['alias'] ) )
				{
					nv_mkdir( NV_UPLOADS_REAL_DIR . '/' . $module_name, $data['alias'] );
				}

				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['add_ab'], $data['album_name'], $admin_info['userid'] );
				//$xxx->closeCursor();
				nv_del_moduleCache( $module_name );
				Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=uploads&albumid=" . $data['albumid'] . "" );
				die();
			}
			else
			{
				$error[] = $lang_module['errorsave_album'];
			}
			//$xxx->closeCursor();
		}
		else
		{
			$data_old = $db->query( "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_album where albumid=" . $data['albumid'] . "" )->fetch();

			$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_album SET 
			   catid=" . intval( $data['catid'] ) . ", 
			   type=" . intval( $data['type'] ) . ", 
			   listcatid=" . $db->quote( $data['listcatid'] ) . ", 
			   album_name=" . $db->quote( $data['album_name'] ) . ", 
			   alias=" . $db->quote( $data['alias'] ) . ", 
			   model=" . $db->quote( $data['model'] ) . ", 
			   capturedate=" . $db->quote( $data['capturedate'] ) . ", 
			   capturelocal=" . $db->quote( $data['capturelocal'] ) . ", 
			   keywords=" . $db->quote( $data['keywords'] ) . ", 
			   description=" . $db->quote( $data['description'] ) . ", 
			   edit_time=UNIX_TIMESTAMP() 
			WHERE albumid =" . $data['albumid'];

			$db->query( $sql );

			if( $db->sql_affectedrows() > 0 )
			{

				if( $data_old['alias'] != $data['alias'] )
				{
					rename( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $data_old['alias'], NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $data['alias'] );
				}
				if( $data_old['catid'] != $data['catid'] )
				{
					$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET  numalbum = numalbum - 1 WHERE catid =" . $data_old['catid'] . "" );
					$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET  numalbum = numalbum + 1 WHERE catid =" . $data['catid'] . "" );
				}

				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['edit_ab'], $data['album_name'], $admin_info['userid'] );
				nv_del_moduleCache( $module_name );
				//$xxx->closeCursor();
				Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
				die();

			}
			else
			{
				$error[] = $lang_module['errorsave_album'];
			}
			//$xxx->closeCursor();

		}
	}
}

foreach( $global_photo_cat as $catid_i => $array_value )
{
	$lev_i = $array_value['lev'];
	$xtitle_i = "";
	if( $lev_i > 0 )
	{
		$xtitle_i .= "&nbsp;&nbsp;&nbsp;|";
		for( $i = 1; $i <= $lev_i; ++$i )
		{
			$xtitle_i .= "---";
		}
		$xtitle_i .= ">&nbsp;";
	}
	$xtitle_i .= $array_value['title'];
	$sl = "";
	if( $catid_i == $data['catid'] )
	{
		$sl = " selected=\"selected\"";
	}
	$array_cat[] = array(
		"value" => $catid_i,
		"selected" => $sl,
		"title" => $xtitle_i );

}

if( ! empty( $data['description'] ) ) $data['description'] = nv_htmlspecialchars( $data['description'] );

if( $data['capturedate'] == 0 )
{
	$capturedate = "";
}
else
{
	$capturedate = date( "d/m/Y", $data['capturedate'] );
}

$array_catid_in_row = explode( ",", $data['listcatid'] );

$contents = "";
$my_head = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . NV_BASE_SITEURL . "js/jquery/jquery.autocomplete.css\" />\n";
$my_head .= "<link type=\"text/css\" href=\"" . NV_BASE_SITEURL . "js/ui/jquery.ui.core.css\" rel=\"stylesheet\" />\n";
$my_head .= "<link type=\"text/css\" href=\"" . NV_BASE_SITEURL . "js/ui/jquery.ui.theme.css\" rel=\"stylesheet\" />\n";
$my_head .= "<link type=\"text/css\" href=\"" . NV_BASE_SITEURL . "js/ui/jquery.ui.datepicker.css\" rel=\"stylesheet\" />\n";

$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/jquery/jquery.autocomplete.js\"></script>\n";
$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/ui/jquery.ui.core.min.js\"></script>\n";
$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/ui/jquery.ui.datepicker.min.js\"></script>\n";
$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . "js/language/jquery.ui.datepicker-" . NV_LANG_INTERFACE . ".js\"></script>\n";

$xtpl = new XTemplate( "add_ab.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'albumid', $data['albumid'] );
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );

if( defined( 'NV_EDITOR' ) and function_exists( 'nv_aleditor' ) )
{
	$edits = nv_aleditor( 'description', '100%', '300px', $data['description'] );
}
else
{
	$edits = "<textarea style=\"width: 100%\" name=\"description\" id=\"description\" cols=\"20\" rows=\"15\">" . $data['description'] . "</textarea>";
}
$xtpl->assign( 'edit_description', $edits );

foreach( $global_photo_cat as $catid_i => $array_value )
{

	$space = intval( $array_value['lev'] ) * 30;
	$catiddisplay = ( sizeof( $array_catid_in_row ) > 1 and ( in_array( $catid_i, $array_catid_in_row ) ) ) ? '' : ' display: none;';
	$temp = array(
		'catid' => $catid_i,
		"space" => $space,
		"title" => $array_value['title'],
		"checked" => ( in_array( $catid_i, $array_catid_in_row ) ) ? " checked=\"checked\"" : "",
		"catidchecked" => ( $catid_i == $data['catid'] ) ? " checked=\"checked\"" : "",
		"catiddisplay" => $catiddisplay );
	$xtpl->assign( 'CATS', $temp );
	$xtpl->parse( 'main.catid' );
}

/* if( ! empty( $array_cat ) )
{

foreach( $array_cat as $cat )
{
$xtpl->assign( 'cat', $cat );
$xtpl->parse( 'main.cat' );
}
} */

$checked = ( $data['type'] ) ? "  checked=\"checked\"" : "";
$xtpl->assign( 'checked', $checked );

$xtpl->assign( 'capturedate', $capturedate );

if( ! empty( $error ) )
{
	$xtpl->assign( 'error', implode( "<br />", $error ) );
	$xtpl->parse( 'main.error' );
}

$xtpl->parse( 'main' );
$contents .= $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
