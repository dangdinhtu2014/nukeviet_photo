<!-- BEGIN: main -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<tbody id="hiden_upload" style="background:#E4E4E4">
	      <tr>
	        <td colspan="2">
				<div class="upload_note" style="padding-top:10px">
					<p><strong>File định dạng JPEG hoặc JPG,</strong> có dung lượng <strong>tối đa là 6MB, độ phân giải lớn nhất theo chiều ngang và chiều dọc là 1500 pixel.</strong></p>
				</div>
			  </td>
	      </tr>
		  <tr>
	        <td colspan="2">
				<div id="info_success" class="info_success" style="margin-top:10px">
					<div style="padding:10px" id="fileupload">
					  <input type="hidden" id="albumid" name="albumid" value="{albumid}"/>
					  <input type="hidden" id="temid" name="temid" value="{tem}"/>
					  <input type="hidden" id="max_upload" name="max_upload" value="{num_upload}"/>
					  <input style="display:none" type="file" name="file_upload" id="file_upload" />
					</div>
					<div id="frontimg"></div>
					<div style="margin-bottom: 2px;" id="contentimg1"><img src="{data.frontimg1}" /></div>
					<div id="response"></div>
					<div style="clear:both"></div>
					<form class="form-inline" style="display:none" id="crop-img" method="post" onSubmit="return checkCoords();">
					  <input type="hidden" id="x" name="leftX" />
					  <input type="hidden" id="y" name="leftY" />
					  <input type="hidden" id="w" name="newwidth" />
					  <input type="hidden" id="h" name="newheight" />
					  <input type="hidden" id="tems" name="tems" />
					  <input type="hidden" id="imgw" name="imgw" value=""/>
					  <input type="hidden" id="imgh" name="imgh"value="" />
					  <input type="hidden" id="rid" name="rid" value=""/>
					  <input type="hidden" id="alias_ab" name="alias_ab" value="{folder}"/>
					  <input type="hidden" id="imgfile" name="imgfile" value=""/>
					  <input type="submit" value="Lưu vùng ảnh đã chọn" class="btn btn-large btn-inverse" />
					  <a href="javascript:void(0);" onclick="reset_content()" class="btn btn-large btn-inverse">Hủy bỏ<a/>
					</form>
				</div>
			  </td>
	      </tr>
	    </tbody>
	</table>
</div>
<div style="clear:both"></div>
<div id="showimage">
<!-- BEGIN: loop -->
<div id="row_{loop.i}" >
	<div class="loopc">
		<div class="img">
			<input class="rid" rel="{loop.i}" type="hidden" value="{loop.rid}" name="rid_{loop.i}" id="rid_{loop.i}" />
			<input type="hidden" value="{loop.thumb}" name="thumb_{loop.i}" id="thumb_{loop.i}" />
			<span id="del_{loop.i}" onclick="delete_image({loop.i},{loop.rid},{loop.albumid});" title="Xóa ảnh này" class="delete"></span>   
			<span  title="Trạng thái" class="status">
				<input id="status_{loop.i}" name="status_{loop.i}" onclick="status_image('status_{loop.i}',{loop.rid},{loop.albumid});" value="1" {status_checked} type="checkbox">
			</span>   
			<span  title="Chọn làm ảnh mặc định album" class="default">
				<input id="fileimg_{loop.i}" name="fileimg_{loop.i}" value="{loop.pictures}" type="hidden">
				<input id="default_{loop.i}" name="default_{loop.i}" onclick="get_image('{loop.i}',{loop.rid},{loop.albumid},{loop.w},{loop.h},{tems});" value="1" {default_checked} type="radio">
			</span>   
			<a class="duan-thumb" rel="{loop.pictures}" href="{loop.pictures}">
				<img width="100px" src="{loop.thumbs}" />
			</a>
		</div>
	</div>
</div>
<!-- END: loop -->
</div>

<script type="text/javascript">var admin_site = '{NV_BASE_ADMINURL}';</script> 
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/uploads.js"></script> 
<!-- END: main -->