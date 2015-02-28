<!-- BEGIN: main -->
<div class="photo-detail">
	<h2>{DATA.name}</h2>
	<div><span class="title">{LANG.album_model}: </span><span>{DATA.model}</span></div>
	<div><span class="title">{LANG.album_capturelocal}: </span><span>{DATA.capturelocal}</span></div>
	<div><span class="title">{LANG.album_capturedate}: </span><span>{DATA.capturedate}</span></div>
	<div><span class="title">{LANG.album_rating}: </span> 
		<div class="starbox small ghosting"> </div>
		<div itemscope itemtype="http://data-vocabulary.org/Review-aggregate">
			<span itemprop="itemreviewed"> </span>
			<img itemprop="photo" src="{DATA.image}" style="display: none"/>
			<span itemprop="rating" itemscope itemtype="http://data-vocabulary.org/Rating">
			  <span itemprop="average">{RATINGVALUE}</span> /<span itemprop="best">5</span>
			</span>
			<span itemprop="votes" style="display: none" id="vote_score">{REVIEWCOUNT}</span>
			(<span id="vote_count" itemprop="count">{RATINGCOUNT}</span> đánh giá)
		  </div>    
 
	 </div>
	<div class="fb-like" data-href="{SELFURL}" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/rating.js"></script>
<script type="text/javascript">
$(function() {
    $('.starbox').starbox({
		average: {RATINGWIDTH},
		stars: 5,
		buttons: 5,
		changeable: 'once',
		autoUpdateAverage: false,
		ghosting: true
	}).bind('starbox-value-changed', function(event, value) {
        $.ajax({
            url: '{LINK_RATE}&nocache=' + new Date().getTime(),
            type: 'post',
            data: {
                rating: value
            },
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    $('.starbox').starbox('setOption', 'average', json.width);
                    $('#vote_score').html(json['ratingValue']);
                    $('#vote_count').html(json['reviewCount']);

                    alert(json['success']);
                }
                if (json['error']) {
                    alert(json['error']);
                }
            }

        });
    });
});
</script>
<!-- END: main -->