<?php

/**
 * @Project NUKEVIET 4.x
 * @Author ĐẶNG ĐÌNH TỨ (dlinhvan@gmail.com)
 * @Copyright (C) 2010 webdep24.com All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 29/08/2014 17:00
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );
 
// Khong cho phep cache
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// Cross domain
// header("Access-Control-Allow-Origin: *");

// Kiem tra phien lam viec
$token = $nv_Request->get_title( 'token', 'get', '' );
if( $token != md5( $nv_Request->session_id . $global_config['sitekey'] ) )
{
	gltJsonResponse( array( 'code' => 200, 'message' => $lang_module['uploadErrorSess'] ) );
}

// Chi admin moi co quyen upload
if( ! defined( 'NV_IS_MODADMIN' ) )
{
	gltJsonResponse( array( 'code' => 200, 'message' => $lang_module['uploadErrorPermission'] ) );
}

// Tang thoi luong phien lam viec
if( $sys_info['allowed_set_time_limit'] )
{
	set_time_limit( 5 * 3600 );
}

// Get request value
$folder = $nv_Request->get_title( 'folder', 'post', '' );
$fileName = $nv_Request->get_title( 'name', 'post', '' );
$fileExt = nv_getextension( $fileName );
$fileName = change_alias( substr( $fileName, 0, -( strlen( $fileExt ) + 1 ) ) ) . '.' . $fileExt;

$chunk = $nv_Request->get_int( 'chunk', 'post', 0 );
$chunks = $nv_Request->get_int( 'chunks', 'post', 0 );

if( empty( $fileName ) or empty( $fileExt ) )
{
	gltJsonResponse( array( 'code' => 200, 'message' => $lang_module['uploadErrorFile'] ) );
}

// Kiem tra file ton tai
$fileName2 = $fileName;
$i = 1;
while ( file_exists( NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $fileName2 ) )
{
    $fileName2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1-' . $i . '\2', $fileName );
    ++$i;
}
$fileName = $fileName2;
$filePath = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . $fileName;

// Open temp file
if( ! $out = @fopen( "{$filePath}.part", $chunks ? "ab" : "wb" ) )
{
	gltJsonResponse( array( 'code' => 102, 'message' => "Failed to open output stream." ) );
}

if( ! empty( $_FILES ) )
{
	if( $_FILES["file"]["error"] || ! is_uploaded_file( $_FILES["file"]["tmp_name"] ) )
	{
		gltJsonResponse( array( 'code' => 103, 'message' => "Failed to move uploaded file." ) );
	}

	// Read binary input stream and append it to temp file
	if( ! $in = @fopen( $_FILES["file"]["tmp_name"], "rb" ) )
	{
		gltJsonResponse( array( 'code' => 101, 'message' => "Failed to open input stream." ) );
	}
}
else
{
	if( ! $in = @fopen( "php://input", "rb" ) )
	{
		gltJsonResponse( array( 'code' => 101, 'message' => "Failed to open input stream." ) );
	}
}

while( $buff = fread( $in, 4096 ) )
{
	fwrite( $out, $buff );
}

@fclose( $out );
@fclose( $in );

// Check if file has been uploaded
if( ! $chunks || $chunk == $chunks - 1 )
{
	// Strip the temp .part suffix off
	$check = @rename( "{$filePath}.part", $filePath );
	
	if( empty( $check ) )
	{
		gltJsonResponse( array( 'code' => 200, 'message' => $lang_module['uploadErrorRenameFile'] ) );
	}
}
 
//$image_info = nv_is_image( $filePath );

$thumb = NV_BASE_SITEURL . NV_TEMP_DIR . '/' . creatThumb( $filePath, NV_ROOTDIR . '/' . NV_TEMP_DIR, 90, 72 );

$image_url = str_replace( NV_ROOTDIR, '', $filePath );
$token_image = md5( $global_config['sitekey'] . session_id() . $image_url );
$token_thumb = md5( $global_config['sitekey'] . session_id() . $thumb );
$token = md5( $global_config['sitekey'] . session_id() );
gltJsonResponse( array(), array( 'row_id' => 0, 'token' => $token, 'token_image' => $token_image, 'token_thumb' => $token_thumb, 'filePath' => $filePath, 'basename' => $fileName, 'image_url' => $image_url, 'thumb' => $thumb, 'ext' => $fileExt ) );

