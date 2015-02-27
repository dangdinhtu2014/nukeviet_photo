<!-- BEGIN: main -->
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/lazyload.js" type="text/javascript" ></script>
<script type="text/javascript">
$(window).load(function(){
	var h = 0;
    $.each( $('.photo-hover'), function(k,v){
		var sh = $(this).height();
		if( sh > h )
		{
			h = sh;
		}
    });
	$('.photo-hover').css('height', parseInt(h) + 'px' ); 
});
</script>
<div id="photo-{OP}"> 
	<div class="fixed">
		<div id="photo-album">
			<div class="box-item multi-columns-row" itemscope itemtype="http://schema.org/ImageObject">
				<div class="catalogs">
					<h2 itemprop="name"><a href="{CATALOG.link}" title="{CATALOG.name}">{CATALOG.name} ({CATALOG.num_album})</a></h2>
					<div class="clear"></div> 
				</div>
				<div class="row">
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
								<img itemprop="image" src="{ALBUM.thumb}" class="lazy" data-src="{ALBUM.thumb}">
							</div>

							<meta itemprop="datePublished" content="{ALBUM.datePublished}">
						</div>
					</div>
					<!-- END: loop_album -->
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<!-- BEGIN: generate_page -->
		<div id="generate_page" class="text-center">
			{GENERATE_PAGE}
		</div>
		<!-- END: generate_page -->
	</div> 
</div>

<!-- END: main -->