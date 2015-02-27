<!-- BEGIN: main -->
<link href="{NV_BASE_SITEURL}themes/{TEMPLATE}/css/multi-columns-row.css"type="text/css" rel="stylesheet" media="all" />
<link href="{NV_BASE_SITEURL}modules/{MODULE_FILE}/plugins/bxslider/jquery.bxslider.css"type="text/css" rel="stylesheet" media="all" />
<script src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/plugins/bxslider/jquery.bxslider.min.js" type="text/javascript" ></script>
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/lazyload.js" type="text/javascript" ></script>
 
<div id="photo-content" class="rows" itemscope itemtype="http://schema.org/ImageObject">
	<a itemprop="url" href="{SELFURL}" style="display:none"> <span itemprop="name">{ALBUM.name}</span></a>
	<div class="photo-description" itemprop="description" style="display:none"> {ALBUM.description} </div>
	<span class="contentLocation" itemprop="contentLocation" style="display:none">{ALBUM.capturelocal}</span>
	<ul class="bxslider">
		<!-- BEGIN: loop_slide -->
		<li><img itemprop="image" src="{PHOTO.file}" /></li>
		<!-- END: loop_slide -->
	</ul>

	<div id="bx-pager" class="carousel-slide">
	  <!-- BEGIN: loop_thumb -->
	  <a  href="javascript:void(0);" onclick="clicked({PHOTO.num});" data-slide-index="{PHOTO.num}"><img src="{PHOTO.thumb}" /></a>
	  <!-- END: loop_thumb -->
	</div>
	<div class="clear" style="height: 20px"></div>
  	<div class="fb-comments" data-href="{SELFURL}" data-width="100%" data-numposts="20" data-colorscheme="light"></div>
 </div>
<div id="photo-album">
	<div class="box-item multi-columns-row" itemscope itemtype="http://schema.org/ImageObject">
		<!-- BEGIN: loop_album -->
		<div class="col-xs-12 col-sm-6 col-md-4 photo-album">
			<div class="photo-hover">
				<div class="fixabsolute">
					<div class="photo-name">
						<h3><a itemprop="url" href="{OTHER.link}"> <span itemprop="name">{OTHER.name}</span></a></h3>
					</div>
					<div class="photo-description" itemprop="description"> {OTHER.description} </div>
					<span class="contentLocation" itemprop="contentLocation">{OTHER.capturelocal}</span>
				</div>
				<div class="photo-image lazyload">
				  <img itemprop="image" class="lazy" data-src="{OTHER.thumb}">
				</div>
				<meta itemprop="datePublished" content="{OTHER.datePublished}">
			</div>
		</div>
		<!-- END: loop_album -->	
	<div class="clear"></div>
	</div>
</div>
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
<script type="text/javascript" >
var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

var adaptive = false;

if( isMobile.any() ) 
{
   adaptive = true;
}
 

var carousel;
var slider;
$(document).ready(function () {
    carousel = $('.carousel-slide').bxSlider({
        slideWidth: 110,
        minSlides: 2,
        maxSlides: 10,
        moveSlides: 1,
        slideMargin: 0,
        pager: false
     
    });

    slider = $('.bxslider').bxSlider({
        pager: false,
		moveSlides: 1,
		displaySlideQty: 2,
		responsive: true,
		infiniteLoop: true,
		adaptiveHeight: adaptive
    });
});
function clicked(position) {
    slider.goToSlide(position);
}
$(document).ready(function () {
	$('.bxslider li').css('display', 'block');
}) 
</script>
<!-- END: main -->