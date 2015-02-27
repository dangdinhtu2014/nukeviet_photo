<!-- BEGIN: main -->
<div id="content"> 
	<!-- BEGIN: catnav -->
	<div class="divbor1" style="margin-bottom: 10px">
		<!-- BEGIN: loop -->
		{CAT_NAV}
		<!-- END: loop -->
	</div>
	<!-- END: catnav -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.catalogs_list}</h3> 
			 <div class="pull-right">
				<a href="{ADD_NEW}" data-toggle="tooltip" data-placement="top" title="{LANG.add_new}" class="btn btn-primary"><i class="fa fa-plus"></i></a>
				<button type="button" data-toggle="tooltip" data-placement="top" title="{LANG.delete}" class="btn btn-danger" id="button-delete">
					<i class="fa fa-trash-o"></i>
				</button>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="#" method="post" enctype="multipart/form-data" id="form-category">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="col-md-1 text-center" style="width:80px" ><a href="{URL_WEIGHT}">{LANG.weight}</a></td>
								<td class="col-md-5 text-left"><a href="{URL_NAME}">{LANG.catalogs_name}</a> </td>
								<td class="col-md-1 text-center"> <strong>{LANG.catalogs_inhome} </strong></td>
								<td class="col-md-2 text-center"> <strong>{LANG.catalogs_viewcat} </strong></td>
								<td class="col-md-1 text-center"> <strong>{LANG.catalogs_numlinks} </strong></td>
								<td class="col-md-2 text-right"> <strong>{LANG.action} </strong></td>
							</tr>
						</thead>
						<tbody>
							 <!-- BEGIN: loop --> 
							<tr id="group_{LOOP.catalogs_id}">
								<td class="text-center">
									<select id="id_weight_{LOOP.catalogs_id}" onchange="nv_change_catalogs('{LOOP.catalogs_id}','weight');" class="form-control">
									<!-- BEGIN: weight -->
									<option value="{WEIGHT.w}"{WEIGHT.selected}>{WEIGHT.w}</option>
									<!-- END: weight -->
									</select>
								</td>
								<td class="text-left"><a href="{LOOP.link}"> <strong>{LOOP.name}</strong> </a> {LOOP.numsubcat}</td>
								<td class="text-center">
									<select class="form-control" id="id_inhome_{LOOP.catalogs_id}" onchange="nv_change_catalogs('{LOOP.catalogs_id}','inhome');">
										<!-- BEGIN: inhome -->
										<option value="{INHOME.key}"{INHOME.selected}>{INHOME.title}</option>
										<!-- END: inhome -->
									</select>
								</td>
								
								<td align="left">
									<select class="form-control" id="id_viewcat_{LOOP.catalogs_id}" onchange="nv_change_catalogs('{LOOP.catalogs_id}','viewcat');">
										<!-- BEGIN: viewcat -->
										<option value="{VIEWCAT.key}"{VIEWCAT.selected}>{VIEWCAT.title}</option>
										<!-- END: viewcat -->
									</select>
								</td>
								<td class="text-center">
										<select class="form-control" id="id_numlinks_{LOOP.catalogs_id}" onchange="nv_change_catalogs('{LOOP.catalogs_id}','numlinks');">
											<!-- BEGIN: numlinks -->
											<option value="{NUMLINKS.key}"{NUMLINKS.selected}>{NUMLINKS.title}</option>
											<!-- END: numlinks -->
										</select>
								</td>
								<td class="text-right">
									<a href="{LOOP.edit}" data-toggle="tooltip" title="{LANG.edit}" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
									&nbsp;&nbsp;
									<a href="javascript:void(0);" onclick="delete_catalogs('{LOOP.catalogs_id}', '{LOOP.token}')" data-toggle="tooltip" title="{LANG.delete}" class="btn btn-danger"><i class="fa fa-trash-o"></i>
								
								
								</td>
							</tr>
							 <!-- END: loop -->
						</tbody>
					</table>
				</div>
			</form>
			<!-- BEGIN: generate_page -->
			<div class="row">
				<div class="col-sm-12 text-left">
				
				<div style="clear:both"></div>
				{GENERATE_PAGE}
				
				</div>
				 
			</div>
			<!-- END: generate_page -->
		</div>
		<div id="cat-delete-area">&nbsp;</div>
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/footer.js"></script>

<script type="text/javascript">

$('button[type=\'submit\']').on('click', function() {
	$("form[id*='form-']").submit();
});


function nv_change_catalogs(catalogs_id, mod) {
	var nv_timer = nv_settimeout_disable('id_'+mod+'_' + catalogs_id, 5000);
	var new_vid = $('#id_'+mod+'_' + catalogs_id).val();
	$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=catalogs&action='+mod+'&nocache=' + new Date().getTime(), 'catalogs_id=' + catalogs_id + '&new_vid=' + new_vid, function(res) {
		var r_split = res.split("_");
		if (r_split[0] != 'OK') {
			alert(nv_is_change_act_confirm[2]);
			clearTimeout(nv_timer);
		} else {
			window.location.href = window.location.href;
		}
	});
	return;
}
</script>

<!-- END: main -->