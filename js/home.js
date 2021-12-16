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
            576:{
                items:2,
            },
            768:{
                items:3,
            },
            1200:{
                items:4,
            }
        }

    });
    // Using FA icons for navigation mapped to owl nav
    // Go to the next item
    $('.owl-nav-right').click(function() {
        $(".owl-carousel").trigger('next.owl.carousel');
    });
    // Go to the previous item
    $('.owl-nav-left').click(function() {
        $(".owl-carousel").trigger('prev.owl.carousel');
    });
}