<?php 
set_time_limit(1);
require_once '../logger.php';
m_log("Reach Main Page ".json_encode($_POST)." ".json_encode($_GET));

session_start();

if(isset($_SESSION["paying_session"])) {
    header('Location: ../payment-page/payment-confirm.php?auth='.$_SESSION["paying_session"].'&decision=ACCEPT&download=true');
    unset($_SESSION["paying_session"]);
    die();
}


 ?><?php
if ($_SERVER['HTTP_X_FORWARDED_PROTO']=='http') {
    // Request is not using SSL, redirect to HTTPS
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>白石高球練習場 Riverside Whitehead Golf Club</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<?php 
require_once '../import-notice.php';
 ?>
<?php 
// echo ($_SERVER['HTTP_X_FORWARDED_PROTO']=='http');

 ?>


    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <div class="container-fluid bg-light p-0">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>香港 新界 馬鞍山 1950地段</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Mon - Sun : 08:00 AM - 10.00 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>+852 2777 1813</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0" style="z-index: 1;">
            <img src="rivergolf_logo_s.jpeg" style="width:250px;">
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a target="_blank" href="../price_display.php" class="nav-item nav-link">價格表<br>Price Table</a>
                <a target="_blank" href="../configuration-administraion.php" class="nav-item nav-link">行政<br>Management</a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0 pb-5">
        <div class="owl-carousel header-carousel">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="../4c2dd3a4-9b6f-4a23-bb55-f2d0ab71397a.jpg" alt="">
                
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(53, 53, 53, .7);">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-8 text-center">
                                <h5 class="text-white text-uppercase mb-3 animated slideInDown">歡迎來到 白石高球練習場</h5>
                                <h1 class="display-3 text-white animated slideInDown mb-4">携手共創美好高球明天</h1>
                                <p class="fs-5 fw-medium text-white mb-4 pb-2">備有六十三條球道，深度達二百餘碼，有充足的空間令練習時更清楚高爾夫球的飛行軌跡。球道所用的擊球墊、球座、練習球等均定期更換，保持高質的練習空間。而且所有球道均設有停車位置於正後方，極為方便。</p>
                                <a href="../terms_booking.php?type=golf" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">打球道預訂<br>Fairway Booking</a>
                                <a href="../terms_booking.php?type=pickleball" class="btn btn-light py-md-3 px-md-5 animated slideInRight">匹克球預訂<br>Pickleball Fairway</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Wait a moment for the theme's JS to initialize Owl Carousel
    setTimeout(function () {
        if (window.jQuery) {
            const $ = window.jQuery;
            const carousel = $('.header-carousel');

            // Destroy Owl Carousel completely
            carousel.trigger('destroy.owl.carousel');

            // Remove Owl wrapper markup so it becomes static HTML
            carousel.find('.owl-stage-outer').children().unwrap();
            carousel.removeClass('owl-carousel owl-loaded');
        }
    }, 1);
});
</script>



    <!-- About Start -->
    <div class="container-fluid bg-light overflow-hidden my-5 px-lg-0">
        <div class="container about px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6 ps-lg-0" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute img-fluid w-100 h-100" src="img/9762_P_1434441995378.jpg" style="object-fit: cover;" alt="">
                    </div>
                </div>
                <div class="col-lg-6 about-text py-5 wow fadeIn" data-wow-delay="0.5s">
                    <div class="p-lg-5 pe-lg-0">


                        <div class="section-title text-start">
                            <h1 class="display-5 mb-4">白石高球練習場 介紹</h1>
                        </div>
                        <p class="mb-4 pb-2">白石高球練習場 位於馬鞍山一個半島之上，球道遠眺馬鞍山背靠吐露港，景觀開揚環境優雅，練習場佔地四十多萬呎，擁有六十三條球道，設施齊備，包括更衣室、休息室等等一應俱全，務求提供一個舒適的練習及休閒環境。</p>

                        <div class="section-title text-start">
                             <h1 class="display-5 mb-4"> Introduction to the Riverside Whitehead Golf Club</h1>
                         </div>
                         <p class="mb-4 pb-2">The Riverside Whitehead Golf Club is located on a peninsula in Ma On Shan. The fairway overlooks Ma On Shan with Tolo Harbour in the background. The landscape is open and the environment is elegant. The driving range covers an area of more than 400,000 square feet. It has more than 63 fairways and complete facilities, including locker rooms, golf equipment shops, coffee shops, lounges, etc., in order to provide a comfortable practice and leisure environment. </p>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Address</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>香港 新界 馬鞍山 1950地段</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+852 2777 1813</p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Riverside Whitehead Golf Club</a>, All Right Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a class="border-bottom" href="../terms.html">免責條款 及 私隱政策</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->
    

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/isotope/isotope.pkgd.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script type="text/javascript">
<?php 


 ?>

<?php 

require_once '../account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 ?>
<?php

$sql = "SELECT `id`, `boardcast-message` FROM `golf-boardcast` WHERE 1 order by id asc;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
    echo "alert('".str_replace("\r", "", str_replace("\n", "\\n", base64_decode($row["boardcast-message"])))."');\n";
  }
}

 ?>
<?php

$conn->close();
 ?>

        
    </script>


<?php 

$root_url = "https://riversidegolf.com.hk/GolfBooking/";
 ?>
<iframe 
    style="height: 3px;" 
    src="<?php echo $root_url; ?>download_report.php?S=1"></iframe>
<iframe 
    style="height: 3px;" 
    src="<?php echo $root_url; ?>clear_record_api.php?check_api_index=1"></iframe>
<iframe 
    style="height: 3px;" 
    src="<?php echo $root_url; ?>clear_record_api.php?check_api_index=2"></iframe>
<iframe 
    style="height: 3px;" 
    src="<?php echo $root_url; ?>clear_record_api.php?check_api_index=3"></iframe>



</body>

</html>