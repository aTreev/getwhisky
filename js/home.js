function prepareHomepage() {
    prepareOwlCarousel();
}


function prepareOwlCarousel() {
    $(".owl-carousel").owlCarousel({
        loop:true,
        items:4,
        margin: 10,
        nav:true,
        lazyLoad:true,
        //animateIn:true,
        responsive:{
            0:{
                items:1,
            },
            480:{
                items:2,
            },
            600:{
                items:3,
            },
            1000:{
                items:4,
            }
        }

    });
    $('.owl-nav-right').click(function() {
        $(".owl-carousel").trigger('next.owl.carousel');
    })
    // Go to the previous item
    $('.owl-nav-left').click(function() {
        // With optional speed parameter
        // Parameters has to be in square bracket '[]'
        $(".owl-carousel").trigger('prev.owl.carousel', [300]);
    });
}