<!-- BEGIN: main -->
<div id="content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="Save"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="Cancel"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-category" class="form-horizontal">
				<input type="hidden" name ="category_id" value="{DATA.category_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
 
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-name">{LANG.category_name}</label>
					<div class="col-sm-20">
						<input type="text" name="name" value="{DATA.name}" placeholder="{LANG.category_name}" id="input-name" class="form-control" />
						<!-- BEGIN: error_name --><div class="text-danger">{error_name}</div><!-- END: error_name -->
					</div>
				</div>
				<div class="form-group required">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.category_alias}</label>
                    <div class="col-sm-20">
						<div class="input-group">
							<input class="form-control" name="alias" placeholder="{LANG.category_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							<div class="input-group-addon fixaddon">
								&nbsp;<em class="fa fa-refresh fa-lg fa-pointer text-middle" onclick="get_alias( );">&nbsp;</em>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.category_sub_sl}</label>
					<div class="col-sm-20">
						<select class="form-control" name="parent_id">
							<!-- BEGIN: parent_loop -->
							<option value="{pcatid_i}" {pselect}>{ptitle_i}</option>
							<!-- END: parent_loop -->
						</select>
					</div>
				</div>
				
                <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-description">{LANG.category_description} </label>
                     <div class="col-sm-20">
                          <textarea name="description" rows="2" placeholder="{LANG.category_description}" id="input-description" class="form-control">{DATA.description}</textarea>
						  <!-- <span class="text-middle"> {GLANG.length_characters}: <span id="descriptionlength" class="red">0</span>. {GLANG.description_suggest_max} </span> -->            
                      </div>
                 </div>
                 <div class="form-group required">
						<label class="col-sm-4 control-label" for="input-meta-title">{LANG.category_meta_title}</label>
						<div class="col-sm-20">
							<input type="text" name="meta_title" value="{DATA.meta_title}" placeholder="{LANG.category_meta_title}" id="input-meta-title" class="form-control" />
							<!-- BEGIN: error_meta_title--><div class="text-danger">{error_meta_title}</div><!-- END: error_meta_title -->
						</div>
                 </div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-description">{LANG.category_meta_description}</label>
					<div class="col-sm-20">
						<textarea name="meta_description" rows="2" placeholder="{LANG.category_meta_description}" id="input-meta-description" class="form-control">{DATA.meta_description}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-meta-keyword">{LANG.category_meta_keyword}</label>
					<div class="col-sm-20">
						<textarea name="meta_keyword" rows="2" placeholder="{LANG.category_meta_keyword}" id="input-meta-keyword" class="form-control">{DATA.meta_keyword}</textarea>
					</div>
				</div>
    
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.category_layout}</label>
					<div class="col-sm-20">
						<select class="form-control" name="layout">
							<option value="">{LANG.defaults}</option>
							<!-- BEGIN: layout -->
							<option value="{LAYOUT.key}" {LAYOUT.selected}>{LAYOUT.key}</option>
							<!-- END: layout -->
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-keyword"> {GLANG.groups_view}</label>
					<div class="col-sm-20">
						<!-- BEGIN: groups_view -->
						 
						<label><input name="groups_view[]" type="checkbox" value="{GROUPS_VIEW.value}" {GROUPS_VIEW.checked} />{GROUPS_VIEW.title}</label>
						 
						<!-- END: groups_view -->
					</div>
				</div>	 
                <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-inhome">{LANG.category_show_inhome}</label>
                     <div class="col-sm-20">
						<select name="inhome" id="input-inhome" class="form-control">
							<!-- BEGIN: inhome -->
                            <option value="{INHOME.key}" {INHOME.selected}>{INHOME.name}</option>
							<!-- END: inhome -->
						</select>
					</div>
				</div>
                        	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.category_show_status}</label>
					<div class="col-sm-20">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>
                         
				<div align="center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-primary" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>
                     
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/footer.js"></script>
<script type="text/javascript">
$('button[type=\'submit\']').on('click', function() {
	$("form[id*='form-']").submit();
});

</script>
<!-- BEGIN: getalias -->
<script type="text/javascript">
//<![CDATA[
$("#input-name").change(function() {
	get_alias('category', {DATA.category_id});
});
//]]>
</script>
<!-- END: getalias -->
<!-- END: main -->