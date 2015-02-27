<!-- BEGIN: main -->
<div id="edit">
<!-- BEGIN: error -->
    <div class="quote" style="width:780px;">
    <blockquote class="error"><span>{error}</span></blockquote>
    </div>
    <div class="clear"></div>
<!-- END: error -->
    <form class="form-inline" action="" enctype="multipart/form-data" method="post">
    <input type="hidden" name ="albumid" value="{DATA.albumid}" />
    <input name="save" type="hidden" value="1" />
    <table class="table table-striped table-bordered table-hover">
		<tbody>
			<tr>
				<td align="right"><strong>{LANG.album_name}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="album_name" type="text" value="{DATA.album_name}" maxlength="255"></td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.model}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="model" type="text" value="{DATA.model}" maxlength="255"/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><strong>{LANG.capturedate}: </strong></td>
				<td>
					<input class="form-control" style="width: 100px" id="capturedate" name="capturedate" type="text" value="{capturedate}" maxlength="10"/>
				</td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.capturelocal}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="capturelocal" type="text" value="{DATA.capturelocal}" maxlength="255"/></td>
			</tr>
		</tbody>
		<tbody >
			<tr>
				<td align="right"><strong>{LANG.keyword}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="keywords" type="text" value="{DATA.keywords}" maxlength="255"/></td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.type}: </strong></td>
				<td align="left"><input name="type" type="checkbox" value="1" {checked}/></td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.cat_sub}: </strong></td>
				<td>
				<div style="padding:4px; height:130px;background:#FFFFFF; overflow:auto; border: 1px solid #CCCCCC">
								<table><tbody style="background:#fff;">
									<!-- BEGIN: catid -->
									<tr style="border: 1px solid #CCCCCC">
										<td>
											<input style="margin-left: {CATS.space}px;" type="checkbox" value="{CATS.catid}" name="catids[]" class="news_checkbox" {CATS.checked}>
											{CATS.title}
										</td>
										<td id="catright_{CATS.catid}" style="{CATS.catiddisplay}">
											<input type="radio" name="catid" value="{CATS.catid}" {CATS.catidchecked}/>
											{LANG.content_checkcat}
										</td>
									</tr>
									<!-- END: catid -->
								</tbody></table>
							</div>
				</td>
			</tr>
		</tbody>
		<!-- <tbody>
			<tr>
				<td align="right"><strong>{LANG.status}: </strong></td>
				<td>
					<input type="checkbox" value="1" name="status" {checked}/>
				</td>
			</tr>
		</tbody> -->
	</table>
	<div class="gray">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<tbody>
					<tr>
						<td><strong>{LANG.bodytext}</strong></td>
					</tr>
					<tr>
						<td>
						<div style="padding:2px; background:#CCCCCC; margin:0; display:block; position:relative">
							{edit_description}
						</div></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
    <center><input class="btn btn-primary" name="submit1" type="submit" value="{LANG.save}" /></center>
</form>
<script type="text/javascript">
	//<![CDATA[
	$("input[name='catids[]']").click(function() {
		var catid = $("input:radio[name=catid]:checked").val();
		var $radios_catid = $("input:radio[name=catid]");
		var catids = [];
		$("input[name='catids[]']").each(function() {
			if($(this).attr('checked')) {
				catids.push($(this).val());
			} else {
				$("#catright_" + $(this).val()).hide();
				if($(this).val() == catid) {
					$radios_catid.filter("[value=" + catid + "]").attr("checked", false);
				}
			}
		});
		if(catids.length > 1) {
			for( i = 0; i < catids.length; i++) {
				$("#catright_" + catids[i]).show();
			};
			catid = parseInt($("input:radio[name=catid]:checked").val() + "");
			if(!catid) {
				alert("{LANG.content_checkcatmsg}");
			}
		}
	});
	$(document).ready(function() {
		$("#capturedate").datepicker({
			showOn : "button",
			dateFormat : "dd/mm/yy",
			changeMonth : true,
			changeYear : true,
			showOtherMonths : true,
			buttonImage : nv_siteroot + "images/calendar.gif",
			buttonImageOnly : true
		});
	});
	//]]>
</script>
</div>
<!-- END: main -->