function sendrating(albumid, point, newscheckss) {
	if(point==1 || point==2 || point==3 || point==4 || point==5){
		nv_ajax('post', nv_siteroot + 'index.php', nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=rating&albumid=' + albumid + '&checkss=' + newscheckss + '&point=' + point, 'stringrating', '');
	}
}
