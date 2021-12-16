<?php
    //includes to generate an anonymous menu
    require_once("php/page.class.php");
    $page = new Page(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $page->displayHead(); ?>
    <title>getwhisky homepage</title>
    <link rel="stylesheet" href="style/css/index.css">
    <link rel="stylesheet" href="owl-carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="owl-carousel/owl.theme.default.min.css">
</head>
<body>
    <?php
        echo $page->displayHeader();
        echo $page->displayProductMenu();
    ?>
    <main>
        <div class="hero">
            <img src="/assets/getwhisky-banner.png" alt="" srcset="">
        </div>


    
        <?php echo $page->displayFeaturedProductsOwl();?>
            
        
        <div class="about-section">
            <div class="about-section-content">
                <h2>About getwhisky</h2>
                <div class="about-paragraph">
                    <p>We started out as a single malt specialists shop in Thurso and have been operating for nearly 70 years. Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repellendus, adipisci? Lorem ipsum dolor sit amet consectetur adipisicing.</p>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vero esse quia, iste itaque unde quos exercitationem rem sit nobis doloribus magnam facere est sequi ratione quaerat officiis velit veritatis fuga.</p>
                    <p><a href="/about.php">Read more <i class='fas fa-chevron-right' style='font-size:1.2rem;'></i></a></p>
                </div>
            </div>
        </div>

        <?php echo $page->displayFeaturedBannerSection(); ?>
    </main>
</body>
<script src="js/classes/alert.class.js"></script>
<script src="js/functions.js"></script>
<script src="owl-carousel/owl.carousel.min.js"></script>
<script src="js/home.js"></script>

<script>
    document.onreadystatechange = function() {
        if(document.readyState==="complete") {
            prepareMenu();
            prepareHomepage();
        }
    }
</script>
</html>