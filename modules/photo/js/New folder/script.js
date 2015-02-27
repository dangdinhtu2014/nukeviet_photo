jQuery(document).ready(function ($) {
    var $container = jQuery('#portfolio-grid'),
        filters = {};

    $container.imagesLoaded(function () {
        setColumnWidth();
        $container.isotope({
            itemSelector: '.portfolio_item',
            resizable: false,
            transformsEnabled: true,
            layoutMode: 'fitRows'
        });
    });

    function getNumColumns() {

        var $folioWrapper = jQuery('#portfolio-grid').data('cols');

        if ($folioWrapper == '1col') {
            var winWidth = jQuery("#portfolio-grid").width();
            var column = 1;
            return column;
        }

        if ($folioWrapper == '2cols') {
            var winWidth = jQuery("#portfolio-grid").width();
            var column = 2;
            if (winWidth < 380) column = 1;
            return column;
        } else if ($folioWrapper == '3cols') {
            var winWidth = jQuery("#portfolio-grid").width();
            var column = 3;
            if (winWidth < 500) column = 1;
            else if (winWidth >= 500 && winWidth < 788) column = 2;
            else if (winWidth >= 788 && winWidth < 1160) column = 3;
            else if (winWidth >= 1160) column = 3;
            return column;
        } else if ($folioWrapper == '4cols') {
            var winWidth = jQuery("#portfolio-grid").width();
            var column = 4;
            if (winWidth < 380) column = 1;
            else if (winWidth >= 380 && winWidth < 788) column = 2;
            else if (winWidth >= 788 && winWidth < 1160) column = 3;
            else if (winWidth >= 1160) column = 4;
            return column;
        }
    }

    function setColumnWidth() {
        var columns = getNumColumns();

        var containerWidth = jQuery("#portfolio-grid").width();
        var postWidth = containerWidth / columns;
        postWidth = Math.floor(postWidth);

        jQuery(".portfolio_item").each(function (index) {
            jQuery(this).css({
                "width": postWidth + "px"
            });
        });
    }

    function arrange() {
        setColumnWidth();
        $container.isotope('reLayout');
    }

    jQuery(window).on("debouncedresize", function (event) {
        arrange();
    });


    // Filter projects
    $('.filters a').click(function (e) {
		e.preventDefault();
        var $this = $(this).parent('li');
        // don't proceed if already active
        if ($this.hasClass('active')) {
            return;
        }

        var $optionSet = $this.parents('.filters');
        // change active class
        $optionSet.find('.active').removeClass('active');
        $this.addClass('active');

        var group = $optionSet.attr('data-filter-group');
        filters[group] = $this.find('a').attr('data-filter');
        // convert object into array
        var isoFilters = [];
        for (var prop in filters) {
            isoFilters.push(filters[prop])
        }
        var selector = isoFilters.join('');
        $container.isotope({
            filter: selector
        });

        return false;
    });
});