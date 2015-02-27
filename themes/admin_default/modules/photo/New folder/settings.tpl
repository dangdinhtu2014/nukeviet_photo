<!-- BEGIN: main -->
<form class="form-inline" action="{NV_BASE_ADMINURL}index.php" method="post">
	<input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
	<input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<tfoot>
				<tr>
					<td style="text-align: left;padding-left:100px" colspan="2">
					<input class="btn btn-primary" type="submit" value="{LANG.save}" name="Submit1" />
					<input type="hidden" value="1" name="savesetting" /></td>
				</tr>        
			</tfoot>
			<tbody>
				<tr>
					<td style="width:300px"><strong>Số ảnh tối đa trong albums:</strong></td>
					<td>
						<select class="form-control" name="num_upload">
							<!-- BEGIN: num_upload -->
							<option value="{num_upload.key}"{num_upload.selected}>{num_upload.title}</option>
							<!-- END: num_upload -->
						</select>
					</td>
				</tr>
				<tr>
					<td style="width:300px"><strong>Số ảnh trên một trang trình diễn:</strong></td>
					<td>
						<select class="form-control" name="per_page">
							<!-- BEGIN: per_page -->
							<option value="{PER_PAGE.key}"{PER_PAGE.selected}>{PER_PAGE.title}</option>
							<!-- END: per_page -->
						</select>
					</td>
				</tr>
				<tr class="second">
					<td><strong>Logo đóng dấu ảnh</strong></td>
					<td>
						<input class="form-control" name="module_logo" id="module_logo" value="{MODULE_LOGO}" style="width:340px;" type="text"/>
						<input style="width:100px;" value="{GLANG.browse_image}" name="selectimg" type="button" />
					</td>
				</tr>
				<tr>
					<td><strong>Dung lượng tối đa của ảnh dự thi tải lên::</strong></td>
					<td>
						<select class="form-control" name="maxupload">
							<!-- BEGIN: size1 -->
							<option value="{SIZE1.key}"{SIZE1.selected}>{SIZE1.title}</option>
							<!-- END: size1 -->
						</select>
						({LANG.sys_max_size}: {SYS_MAX_SIZE})
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<script type="text/javascript">
//<![CDATA[
$("input[name=selectimg]").click(function(){
	var area = "module_logo";
	var type= "image";
	var path= "{PATH}";
	var currentpath= "{CURRENTPATH}";
	nv_open_browse("{NV_BASE_ADMINURL}index.php?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", "850", "420","resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
//]]>
</script>

<!-- END: main -->