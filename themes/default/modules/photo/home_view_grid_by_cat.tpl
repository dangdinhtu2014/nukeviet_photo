<!-- BEGIN: main -->
<link href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/multi-columns-row.css"type="text/css" rel="stylesheet" media="all" />
<script type="text/javascript">
$(document).ready(function(){
	$(".photo-hover").hover(function(){
		$(this).find( '.fixabsolute' ).addClass('bgc');
		$(this).find( '.photo-image' ).addClass('bgc');
	},function(){
		$(this).find( '.fixabsolute' ).removeClass('bgc');
		$(this).find( '.photo-image' ).removeClass('bgc');
	});
});
 
</script>
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/lazyload.js" type="text/javascript" ></script>
<div id="photo-{OP}"> 
	<div class="fixed">
		<div id="photo-album">
		<!-- BEGIN: loop_catalog -->
		<div class="box-item" itemscope itemtype="http://schema.org/ImageObject">
			<div class="category">
				<h2 itemprop="name"><a href="{CATALOG.link}" title="{CATALOG.name}">{CATALOG.name} ({CATALOG.num_album})</a></h2>
				<div class="clear"></div> 
			</div>
			<div class="row2 multi-columns-row">
				<!-- BEGIN: loop_album -->
				<div class="col-xs-12 col-sm-6 col-md-4 photo-album">
					<div class="photo-hover">
						<div class="fixabsolute">
							<div class="photo-name">
								<h3><a itemprop="url" href="{ALBUM.link}"> <span itemprop="name">{ALBUM.name}</span></a></h3>
							</div>
							<div class="photo-description" itemprop="description"> {ALBUM.description} </div>
							<span class="contentLocation" itemprop="contentLocation">{ALBUM.capturelocal}</span>
						</div>
						<div class="photo-image lazyload">
							<img itemprop="image" class="lazy" data-src="{ALBUM.thumb}">
						</div>

						<meta itemprop="datePublished" content="{ALBUM.datePublished}">
					</div>
				</div>
				<!-- END: loop_album -->
			</div>
			<div class="clear"></div>
		</div>
		<!-- END: loop_catalog -->
		</div>
	</div> 
</div>

<!-- END: main -->