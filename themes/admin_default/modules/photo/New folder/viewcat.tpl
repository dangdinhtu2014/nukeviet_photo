<!-- BEGIN: main -->
<form class="form-inline" name="myform" id="myform" method="post" action="{del_link}">
  <table class="table table-striped table-bordered table-hover">
    <caption>
		{LANG.list_ab}
    </caption>
    <thead>
      <tr>
        <td style="width:30px" class="text-center">ID</td>
        <td>Tên Album</td>
        <td>Người mẫu</td>
        <td>Địa điểm chụp</td>
        <td class="text-center">Số ảnh</td>
        <td class="text-center">Ngày chụp</td>
        <td class="text-center">Ngày đăng</td>
        <td class="text-center">Trạng thái</td>
        <td width="200" class="text-center">Chức năng</td>
      </tr>
    </thead>
	<!-- BEGIN: loop -->
    <tbody>
      <tr>
        <td><input type="checkbox" class="ck" value="{albumid}" /></td>
        <td><a href="{link_add}">{DATA.album_name}</a></td>
        <td>{DATA.model}</td>
        <td>{DATA.capturelocal}</td>
        <td class="text-center">{DATA.numphoto}</td>
        <td class="text-center">{DATA.capturedate}</td>
        <td class="text-center">{DATA.add_time}</td>
        <td style="text-align: center">
			<input type="checkbox" name="status" id="change_status_{DATA.albumid}" value="{DATA.albumid}" {DATA.checked} onclick="nv_chang_status({DATA.albumid});"/>
		</td>
		<td class="text-center">
			<span class="add_icon"><a href="{link_add}">{LANG.manager_img}</a></span>
			&nbsp;&nbsp;-&nbsp;&nbsp;
			<span class="edit_icon"><a href="{link_edit}">{LANG.edit}</a></span>
			&nbsp;&nbsp;-&nbsp;&nbsp;
			<span class="delete_icon"><a class="deleteone" href="{link_del}">{LANG.delete}</a></span>
		</td>
      </tr>
    <!-- END: loop -->
	<tbody>
	<tbody>
	<tr class="tfoot_box">
		<td colspan="4">
			<span>
			<a name="checkall" id="checkall" href="javascript:void(0);"><strong>{LANG.checkall}</strong></a>
			&nbsp;&nbsp;-&nbsp;&nbsp; <a name="uncheckall" id="uncheckall" href="javascript:void(0);"><strong>{LANG.uncheckall}</strong></a>&nbsp;&nbsp;
			</span>
			-
			<span class="delete_icon">
				<a class="delete" href="{URL_DEL}"><strong>{LANG.delete}</strong></a>
			</span>
		</td>
		<td colspan="4" class="text-center"><!-- BEGIN: generate_page -->{GENERATE_PAGE}<!-- END: generate_page --></td>
	</tr>
	</tbody>

	</table>
</form>
<div style="margin-top:20px" class="text-center" id="msgshow">&nbsp;</div>
<script type='text/javascript'>
    $(function(){
        $('#checkall').click(function(){
            $('input:checkbox').each(function(){
                $(this).attr('checked', 'checked');
            });
        });
        $('#uncheckall').click(function(){
            $('input:checkbox').each(function(){
                $(this).removeAttr('checked');
            });
        });
        $('.delete').click(function(){
			event.preventDefault();
            if (confirm("Bạn có chắc chắn muốn xóa ?")) {
                var listall = [];
                $('input.ck:checked').each(function(){
                    listall.push($(this).val());
                });
                if (listall.length < 1) {
                    alert("Bạn cần chọn ít nhất một function dể xóa");
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: '{URL_DEL}',
                    data: 'listall=' + listall,
                    success: function(data){
                      window.location = '{URL_DEL_BACK}';
                    }
                });
            }
        });
		
        $('a.deleteone').click(function(event){
            event.preventDefault();
            if (confirm("Nếu bạn xóa album này tất cả dữ liệu, ảnh liên quan tới album cũng sẽ bị xóa ?")) {
                var href = $(this).attr('href');
                $.ajax({
                    type: 'POST',
                    url: href,
                    data: '',
                    success: function(data){
						var r_split = data.split("_");
						if(r_split[0]="OK")
                        {
							window.location = '{URL_DEL_BACK}';
						}else
						{
							alert(r_split[1]);
						}
                    }
                });
            }
        });
    });
</script>
<!-- END: main -->