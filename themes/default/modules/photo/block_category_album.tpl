<!-- BEGIN: main -->
<style type="text/css">
ul.category-album{list-style: none; padding:0; margin: 0}
ul.category-album li{border-bottom: 1px #ccc solid; margin-bottom: 4px}
ul.category-album li a{display: block;}
ul.category-album li a.title{padding-left: 4px}

</style>
<ul class="category-album">
	<!-- BEGIN: loop_album -->
	<li>
		<a class="fl images" href="{ALBUM.link}"><img itemprop="image" src="{ALBUM.thumb}" width="100"></a>
		<a class="fl title" href="{ALBUM.link}">{ALBUM.name}</a>
		<div style="clear:both"></div>
	</li>
	<!-- END: loop_album -->
</ul>
<!-- END: main -->