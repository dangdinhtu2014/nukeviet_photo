$(function () {
    var max_upload = parseInt($('#max_upload').val());
    var temid = parseInt($('#temid').val());
    var albumid = parseInt($('#albumid').val());
    var limit = max_upload - temid;

    $('#file_upload').uploadify({
        'swf': swf_link,
        'uploader': upload_link,
        'cancelImg': cancel_link,
        'buttonText': 'CHỌN ẢNH...',
        'removeTimeout': 5,
        'uploadLimit': limit,
        'queueSizeLimit': limit,
        'fileSizeLimit': '5221908 byte',
        'fileTypeExts': '*.jpg;*.jpeg;*.png;*.gif',
        'fileDesc': '.jpg, .jpeg, .png, .gif',
        'removeCompleted': true,
        'formData': {
            'uploadfile': '1',
            'modname': nv_module_name,
            'siteroot': nv_siteroot,
            'module_logo': module_logo,
            'folder': folder,
            'albumid': albumid,
            'limit': temid
        },
        'onUploadSuccess': function (file, data, response) {
            var tem = parseInt($('#temid').val()) + 1;
            $('#temid').val(tem);
            var info = data.split('[NV3]');
            if (info[0] == "OK") {
				var status= "status_" + tem + "";
               var a = '';
                a += '<div id="row_' + tem + '">';
                a += '<div class="loopc">';
				a += '	<div class="img">';
				a += '		<input class="rid" rel="' + tem + '" type="hidden" value="' + info[3] + '" name="rid_' + tem + '" id="rid_' + tem + '" />';
				a += '		<input type="hidden" value="' + info[1] + '" name="thumb_' + tem + '" id="thumb_' + tem + '" />';
				a += '		<span id="del_' + tem + '" onclick="delete_image(' + tem + ', 0, ' +albumid+ ', ' + admin_site + ');" title="Xóa ảnh này" class="delete"></span>';   
				a += '<span  title="Trạng thái" class="status">';
				a += '	<input id="status_' + tem + '" name="status_' + tem + '" onclick="status_image(\'status_' + tem + '\',' + info[3] + ',' + albumid + ');" value="1" checked type="checkbox">';
				a += '</span>';
				a += '<span  title="Chọn làm ảnh mặc định album" class="default">';
				a += '<input id="fileimg_' + tem + '" name="fileimg_' + tem + '" value="' + info[2] + '" type="hidden">';
				a += '<input id="default_' + tem + '" name="default_' + tem + '" onclick="get_image(\'' + tem + '\',\'' + info[3] + '\', \'' +albumid+ '\', \'' + info[4] + '\', \'' + info[5] + '\', \'' + info[6] + '\');" value="1" type="radio">';
				a += '</span>';
				a += '		<a class="duan-thumb" rel="' + info[2] + '" href="' + info[2] + '">';
				a += '			<img width="100px" src="' + info[1] + '" />';
				a += '		</a>';
				a += '	</div>';
				a += '</div>';
                a += '</div>';

                $("#showimage").append(a);
				$('.duan-thumb').lightBox();
            } else {
                $('#file_upload').uploadify('settings', 'queueSizeLimit', tem);
                $('#file_upload').uploadify('settings', 'queueSizeLimit', tem);
                //alert(info[1]);
            }
        }
    });
});

function delete_image(delrow, rid, albumid) {
    if (confirm('Bạn có chắc chắn xóa ảnh này')) {
        $.ajax({
            async: false,
            type: 'POST',
            url: admin_site + 'index.php?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=uploads&del=1',
            data: 'rid=' + rid + '&albumid=' + albumid,
            success: function (a) {
				
                var b = a.split("[NV3]");
                if (b[0] == 'OK') {
					
                    $('#row_' + delrow).remove();
					
                    var i=1;
                    $.each($('.rid'), function () {
                        $('#row_' + $(this).attr('rel')).attr('id', 'row_' + i);
                        tmp = $(this).attr('rel');

                        $('#del_' + tmp).attr('onclick', "delete_image(" + i + ", " + $('#rid_' + tmp).val() + ", " + albumid + ")");
                        $('#del_' + tmp).attr('id', 'del_' + i);


                        $('#picture_' + tmp).attr('name', 'picture_' + i);
                        $('#picture_' + tmp).attr('id', 'picture_' + i);

                        $('#thumb_' + tmp).attr('name', 'thumb_' + i);
                        $('#thumb_' + tmp).attr('id', 'thumb_' + i);

                        $(this).attr('name', 'rid_' + i);
                        $(this).attr('id', 'rid_' + i);
                        $(this).attr('rel', i);
                        ++i;
                    });
                    var tem = parseInt($('#temid').val()) - 1;
                    $('#temid').val(tem);

                } else if (b[0] == 'ERR') {
                    alert(b[1]);
                }
            }
        });

    }
    return false;
}

function status_image(checkbox_id, rid, albumid ) {
	if (confirm(nv_is_change_act_confirm[0])) {
		var nv_timer = nv_settimeout_disable(checkbox_id, 5000);
		nv_ajax("post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=uploads&act=1&rid=' + rid + '&albumid=' + albumid + '&loop=' + checkbox_id + '&num=' + nv_randomPassword(8), '', 'status_image_res');
	} else {
		var sl = document.getElementById(checkbox_id);
		if (sl.checked == true)
			sl.checked = false;
		else
			sl.checked = true;
	}
	return;
}

// ---------------------------------------

function status_image_res(res) {
	var r_split = res.split("|");
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
		var sl = document.getElementById(r_split[1]);
		if (sl.checked == true)
			sl.checked = false;
		else
			sl.checked = true;
		clearTimeout(nv_timer);
		sl.disabled = true;
	}
	return false;
}

function reset_content() {
    $('#crop-img').hide();
    return false;
};


function updateCoords(c) {
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
};
function checkCoords() {
    if (parseInt($('#w').val())) {
        var x = $('#x').val();
        var y = $('#y').val();
        var w = $('#w').val();
        var h = $('#h').val();
		var targ_w = 402;
        var targ_h = 254;
        var imgw = $('#imgw').val();
        var imgh = $('#imgh').val();
        var imgfile = $('#imgfile').val();
        var alias_ab = $('#alias_ab').val();
        var rid = $('#rid').val();
        var albumid = $('#albumid').val();
        var tems = $('#tems').val();
        $.ajax({
            url: admin_site + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=uploads&upload=1&nocache=' + new Date().getTime(),
            type: "POST",
            data: 'leftX=' + x + '&leftY=' + y + '&newwidth=' + w + '&newheight=' + h + '&imgw=' + imgw + '&imgh=' + imgh + '&tems=' + tems + '&imgfile=' + imgfile+ '&alias_ab=' + alias_ab+ '&rid=' + rid+ '&albumid=' + albumid,
            success: function (res) {
				var r_split = res.split("[NV3]");
				
				if (r_split[0] == 'OK') 
				{
					document.getElementById("contentimg1").innerHTML = '<img src="' + r_split[1] + '" width="220" height="139" >';
					$('#frontimg').val(r_split[1]);
				}else
				{
					alert(r_split[1]);
				}
				$('#crop-img').hide();
				$('#response').hide();
            }
        });
    } else {
        alert('Hãy chọn vùng ảnh trước khi bấm lưu vùng ảnh đã chọn');
    }
    return false;
};


function get_image(i,rid,albumid,w,h,tems)
{
	var img = document.getElementById('fileimg_'+i).value;
	$('#imgfile').val(img);
	$('#rid').val(rid);
	$('#imgw').val(w);
	$('#imgh').val(h);
	$('#tems').val(tems);
	$('#albumid').val(albumid);
	document.getElementById("response").innerHTML = '<img id="cropbox" src="' + img + '" width="' + w + '" height="' + h + '">';
	$('#cropbox').Jcrop({
		//aspectRatio: 1,
		bgFade: true,
		bgOpacity: .2,
		minSize: [402, 254],
		maxSize: [630, 380],
		aspectRatio: 630 / 380,
		onSelect: updateCoords
	});
	$('#crop-img').show();
}
