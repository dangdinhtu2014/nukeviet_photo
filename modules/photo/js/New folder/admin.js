function nv_chang_cat(catid, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + catid, 5000);
	var new_vid = document.getElementById('id_' + mod + '_' + catid).options[document.getElementById('id_' + mod + '_' + catid).selectedIndex].value;
	nv_ajax("post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=change_cat&catid=' + catid + '&mod=' + mod + '&new_vid=' + new_vid + '&num=' + nv_randomPassword(8), '', 'nv_chang_cat_result');
	return;
}

// ---------------------------------------

function nv_chang_cat_result(res) {
	var r_split = res.split("_");
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
	}
	clearTimeout(nv_timer);
	var parentid = parseInt(r_split[1]);
	nv_show_list_cat(parentid);
	return;
}

// ---------------------------------------

function nv_show_list_cat(parentid) {
	if (document.getElementById('module_show_list')) {
		nv_ajax("get", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=list_cat&parentid=' + parentid + '&num=' + nv_randomPassword(8), 'module_show_list');
	}
	return;
}

// ---------------------------------------

function nv_del_cat(catid) {
	nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=del_cat&catid=' + catid, '', 'nv_del_cat_result');
	return false;
}

// ---------------------------------------

function nv_del_cat_result(res) {
	var r_split = res.split("_");
	if (r_split[0] == 'OK') {
		var parentid = parseInt(r_split[1]);
		nv_show_list_cat(parentid);
	} else if (r_split[0] == 'CONFIRM') {
		if (confirm(nv_is_del_confirm[0])) {
			var catid = r_split[1];
			var delallcheckss = r_split[2];
			nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=del_cat&catid=' + catid + '&delallcheckss=' + delallcheckss, '', 'nv_del_cat_result');
		}
	} else if (r_split[0] == 'ERR' && r_split[1] == 'CAT') {
		alert(r_split[2]);
	} else {
		alert(nv_is_del_confirm[2]);
	}
	return false;
}


function nv_chang_type( albumid )
{
   var nv_timer = nv_settimeout_disable( 'change_type_' + albumid, 5000 );
   nv_ajax( "post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=uploads&type=1&albumid=' + albumid + '&num=' + nv_randomPassword( 8 ), '', 'nv_chang_type_res' );
   return;
}

function nv_chang_type_res( res )
{
   if( res != 'OK' )
   {
      alert( nv_is_change_act_confirm[2] );
      window.location.href = window.location.href;
   }
   return;
}
function nv_chang_status( albumid )
{
   var nv_timer = nv_settimeout_disable( 'change_status_' + albumid, 5000 );
   nv_ajax( "post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=uploads&status=1&albumid=' + albumid + '&num=' + nv_randomPassword( 8 ), '', 'nv_chang_status_res' );
   return;
}

function nv_chang_status_res( res )
{
   if( res != 'OK' )
   {
      alert( nv_is_change_act_confirm[2] );
      window.location.href = window.location.href;
   }
   return;
}



// ---------------------------------------

function res_keywords(res) {
	if (res != "n/a") {
		document.getElementById('keywords').value = res;
	} else {
		document.getElementById('keywords').value = '';
	}
	return false;
}

//---------------------------------------
function get_alias(mod,id) {
	var title = strip_tags(document.getElementById('idtitle').value);
	if (title != '') {
		nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=alias&title=' + encodeURIComponent(title)+'&mod='+mod+'&id='+id, '', 'res_get_alias');
	}
	return false;
}

function res_get_alias(res) {
	if (res != "") {
		document.getElementById('idalias').value = res;
	} else {
		document.getElementById('idalias').value = '';
	}
	return false;
}

function chkAlbumValue(slected_value,vali)
{
	if(slected_value == 'new')
	{
		$('#selAlbumId_'+vali).css('display', 'none');
		$('#selAlbumName_'+vali).css('display', 'block');
		$('#selAlbumOk_'+vali).css('display', 'block');
		$('#selAlbumNewCancel_'+vali).css('display', 'block');
		$('#photo_album_'+vali).val('');
	}else
	{
		$('#albumid').val(slected_value);
	}
}


/*, them, sua, xoa album*/
var album_name = '';
var valueofi = '';
var totalphoto = '';
function savePublicAlbum(albumeval,vali,total_photo,base_admin)
{
	if(albumeval=='')
	{
		alert('Vui lòng nhập tên Album.');
		return false;
	}
	var url= base_admin+ 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=create';
	url  = url+'&new_album_name='+albumeval;
		
	var loadingImage = "<img src='"+nv_siteroot+"images/load_bar.gif"+"' alt='loading'\/>";
	$('#selLoadImg_'+vali).css('display', 'block');
	$('#selLoadImg_'+vali).html(loadingImage);
	valueofi = vali;
	totalphoto = total_photo;
	nv_ajax('post', base_admin + 'index.php', nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=create&new_album_name='+albumeval+'&totalphoto='+totalphoto+'&valueofi='+valueofi, '', 'storeAlbumValue');
	

}
function storeAlbumValue(data) {
	data = data.split("|");
	if(data[0] == 'OK')
	{
		$('#selLoadImg_'+data[3]).css('display', 'none');
		$('#selAlbumOk_'+data[3]).css('display', 'none');
		$('#selAlbumNewCancel_'+data[3]).css('display', 'none');
		$('#albumid').val(data[1]);
		
		var photo_album = document.getElementById( 'photo_album_'+data[3] );
		photo_album.disabled = true;

		var select = $('#album_id_'+data[3]).get(0);
		select.options[select.options.length] = new Option(data[2], data[1]);

	}
	else
	{
		$('#selLoadImg_'+data[3]).css('display', 'none');
		
		alert(data[5]);

	}
	return false;
}
function cancelCreateNewAlbum(vali)
 {
 	$('#selAlbumId_'+vali).css('display', 'block');
 	$('#album_id_'+vali).val('');
 	$('#selAlbumName_'+vali).css('display', 'none');
	$('#selAlbumOk_'+vali).css('display', 'none');
	$('#selAlbumNewCancel_'+vali).css('display', 'none');
 }