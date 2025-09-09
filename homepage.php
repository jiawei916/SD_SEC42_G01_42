<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";

if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'customer';
}
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>VetGroom Hub — Home</title>
    <meta name="description" content="VetGroom Hub - Pet grooming and veterinary care services">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
/* Dropdown container */
.dropdown {
    position: relative;
    display: inline-block;
}

/* Use existing .header-btn styling */
.dropdown > .header-btn {
    display: inline-block;
    text-align: center;
}

/* Dropdown box */
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: #fff;
    width: 100%;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border-radius: 6px;
    z-index: 1000;
}

/* Dropdown links */
.dropdown-content a {
    color: #333;
    padding: 10px 14px;
    text-decoration: none;
    display: block;
    transition: background 0.2s ease;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

/* Show dropdown on hover */
.dropdown:hover .dropdown-content {
    display: block;
}
</style>

<body>
    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/logo2.png" alt="VetGroom Hub">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader End -->
    <header>
        <!-- Header Start -->
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="homepage.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav> 
                                        <ul id="navigation">
                                            <li><a href="homepage.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li class="active"><a href="viewService.php">Services</a></li>
                                            <li><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
<div class="header-right-btn f-right d-none d-lg-block ml-30">
    <div class="dropdown">
        <a href="#" class="header-btn">
            <?php echo $isLoggedIn ? "Welcome, " . htmlspecialchars($userName) : "Welcome, Guest"; ?> ▼
        </a>
        <div class="dropdown-content">
            <?php if ($isLoggedIn): ?>
                <a href="profile.html">Profile</a>
                <a href="signOut.php">Sign Out</a>
            <?php elseif ($userRole == 'admin'): ?>
                <a href="profile.html">Profile</a>
                <a href="viewDashboardAdmin.php">Dashboard</a>
                <a href="viewFeedBack.php">View Feedback</a>
                <a href="viewCustomer.php">View Customer</a>
                <a href="viewStaff.php">View Staff</a>
                <a href="signOut.php">Sign Out</a>              
            <?php elseif ($userRole == 'staff'): ?>
                <a href="profile.html">Profile</a>
                <a href="viewDashboardStaff.php">Dashboard</a>
                <a href="viewFeedBack.php">View Feedback</a>
                <a href="signOut.php">Sign Out</a> 
            <?php else: ?>
                <a href="signIn.php">Sign In</a>
                <a href="registerGuest.php">Register</a>
            <?php endif ?>
        </div>
    </div>
</div>
                            </div>
                        </div>   
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>
    <main> 
        <!-- Slider Area Start-->
        <div class="slider-area">
            <div class="slider-active dot-style">
                <!-- Slider Single -->
                <div class="single-slider d-flex align-items-center slider-height">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-7 col-lg-8 col-md-10 ">
                                <!-- Video icon -->
                                <div class="video-icon">
                                    <a class="popup-video btn-icon" href="https://www.youtube.com/watch?v=up68UAfH0d0" data-animation="bounceIn" data-delay=".4s">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                                <div class="hero__caption">
                                    <span data-animation="fadeInUp" data-delay=".3s">Professional pet care in one place</span>
                                    <h1 data-animation="fadeInUp" data-delay=".3s">VetGroom Hub — We Care for Your Pets</h1>
                                    <p data-animation="fadeInUp" data-delay=".6s">Full grooming, veterinary services and convenient appointment bookings for dogs and cats.</p>
                                    <a href="index.php" class="hero-btn" data-animation="fadeInLeft" data-delay=".3s">Book Appointment<i class="ti-arrow-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>   
                <!-- Slider Single (duplicate slide retained from template for visual continuity) -->
                <div class="single-slider d-flex align-items-center slider-height">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-7 col-lg-8 col-md-10 ">
                                <div class="video-icon">
                                    <a class="popup-video btn-icon" href="https://www.youtube.com/watch?v=1aP-TXUpNoU" data-animation="bounceIn" data-delay=".4s">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                                <div class="hero__caption">
                                    <span data-animation="fadeInUp" data-delay=".3s">Safe — Gentle — Professional</span>
                                    <h1 data-animation="fadeInUp" data-delay=".3s">Trusted grooming & veterinary care</h1>
                                    <p data-animation="fadeInUp" data-delay=".6s">Experienced groomers and qualified veterinarians working together for your pet's health and comfort.</p>
                                    <a href="services.php" class="hero-btn" data-animation="fadeInLeft" data-delay=".3s">Our Services<i class="ti-arrow-right"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>   
            </div>
            <!-- slider Social -->
            <div class="button-text d-none d-md-block">
            <span>Scroll</span>
            </div>
        </div>
        <!-- Slider Area End -->
        <!--? Our Services Start -->
        <div class="our-services section-padding30">
            <div class="container">
                <div class="row justify-content-sm-center">
                    <div class="cl-xl-7 col-lg-8 col-md-10">
                        <!-- Section Tittle -->
                        <div class="section-tittle text-center mb-70">
                            <span>Our Professional Services</span>
                            <h2>Best Pet Care Services</h2>
                        </div> 
                    </div>
                </div>
                <div class="row">
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services text-center mb-30">
                            <div class="services-ion">
                                <span class="flaticon-animal-kingdom"></span>
                            </div>
                            <div class="services-cap">
                                <h5><a href="services.php#grooming">Pet Grooming</a></h5>
                                <p>Full grooming packages: bath, haircut, nail trim and ear cleaning tailored to your pet.</p>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services text-center mb-30">
                            <div class="services-ion">
                                <span class="flaticon-animals"></span>
                            </div>
                            <div class="services-cap">
                                <h5><a href="services.php#vet">Veterinary Care</a></h5>
                                <p>Consultations, minor procedures and on-site diagnostics provided by our licensed vets.</p>
                            </div>
                        </div>
                    </div>
                    <div class=" col-lg-4 col-md-6 col-sm-6">
                        <div class="single-services text-center mb-30">
                            <div class="services-ion">
                                <span class="flaticon-animals-1"></span>
                            </div>
                            <div class="services-cap">
                                <h5><a href="services.php#vaccination">Vaccinations</a></h5>
                                <p>Core vaccinations and preventive care plans to keep your pet healthy year-round.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Our Services End -->
        <!--? About Area Start-->
        <div class="about-area fix">
            <!--Right Contents  -->
            <div class="about-img">
            </div>
            <!-- left Contents -->
            <div class="about-details">
                <div class="right-caption">
                    <!-- Section Tittle -->
                    <div class="section-tittle mb-50">
                        <h2>About VetGroom Hub<br> Your pet's health is our priority</h2>
                    </div>
                    <div class="about-more">
                        <p class="pera-top">VetGroom Hub provides professional grooming and veterinary services with a focus on safety and comfort for every pet.</p>
                        <p class="mb-65 pera-bottom">Our team includes certified veterinarians and experienced groomers. We offer appointment bookings, preventive care plans, and grooming packages suitable for dogs and cats of all sizes.</p>
                        <a href="about.php" class="btn">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About Area End-->
        <!--? Gallery Area Start -->
        <div class="gallery-area section-padding30">
            <div class="container fix">
                <div class="row justify-content-sm-center">
                    <div class="cl-xl-7 col-lg-8 col-md-10">
                        <!-- Section Tittle -->
                        <div class="section-tittle text-center mb-70">
                            <span>Our Recent Photos</span>
                            <h2>Pets Photo Gallery</h2>
                        </div> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="single-gallery mb-30">
                            <div class="gallery-img size-img" style="background-image: url(assets/img/gallery/gallery1.png);"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6 col-sm-6">
                        <div class="single-gallery mb-30">
                            <div class="gallery-img size-img" style="background-image: url(assets/img/gallery/gallery2.png);"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6 col-sm-6">
                        <div class="single-gallery mb-30">
                            <div class="gallery-img size-img" style="background-image: url(assets/img/gallery/gallery3.png);"></div>
                        </div>
                    </div>
                    <div class="col-lg-4  col-md-6 col-sm-6">
                        <div class="single-gallery mb-30">
                            <div class="gallery-img size-img" style="background-image: url(assets/img/gallery/gallery4.png);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gallery Area End -->
        <!--? Contact form Start -->
        <div class="contact-form-main pb-top">
            <div class="container">
                <div class="row justify-content-md-end">
                    <div class="col-xl-7 col-lg-7">
                        <div class="form-wrapper">
                            <!--Section Tittle  -->
                            <div class="form-tittle">
                                <div class="row ">
                                    <div class="col-xl-12">
                                        <div class="section-tittle section-tittle2 mb-70">
                                            <h2>Book an appointment with VetGroom Hub</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End Section Tittle  -->
                            <form id="contact-form" action="contact.php" method="POST">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-box user-icon mb-30">
                                            <input type="text" name="name" placeholder="Full name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-box email-icon mb-30">
                                            <input type="text" name="phone" placeholder="Phone" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 mb-30">
                                        <div class="select-itms">
                                            <select name="service" id="select2">
                                                <option value="grooming">Pet Grooming</option>
                                                <option value="vet">Veterinary Consultation</option>
                                                <option value="vaccination">Vaccination</option>
                                                <option value="boarding">Pet Boarding</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-box subject-icon mb-30">
                                            <input type="email" name="email" placeholder="Email" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-box message-icon mb-65">
                                            <textarea name="message" id="message" placeholder="Tell us about your pet / request" required></textarea>
                                        </div>
                                        <div class="submit-info">
                                            <button class="btn submit-btn2" type="submit">Send Request</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- shape-dog -->
                                <div class="shape-dog">
                                    <img src="assets/img/gallery/shape1.png" alt="">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- contact left Img-->
            <div class="from-left d-none d-lg-block">
                <img src="assets/img/gallery/contact_form.png" alt="Contact VetGroom Hub">
            </div>
        </div>
        <!-- Contact form End -->
        <!--? Team Start -->
        <div class="team-area section-padding30">
            <div class="container">
                <div class="row justify-content-sm-center">
                    <div class="cl-xl-7 col-lg-8 col-md-10">
                        <!-- Section Tittle -->
                        <div class="section-tittle text-center mb-70">
                            <span>Our Professional members </span>
                            <h2>Our Team Members</h2>
                        </div> 
                    </div>
                </div>
                <div class="row">
                    <!-- single Tem -->
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-">
                        <div class="single-team mb-30">
                            <div class="team-img">
                                <img src="assets/img/gallery/team1.png" alt="">
                            </div>
                            <div class="team-caption">
                                <span>Dr. Mike Janathon</span>
                                <h3><a href="#">Veterinarian</a></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-">
                        <div class="single-team mb-30">
                            <div class="team-img">
                                <img src="assets/img/gallery/team2.png" alt="">
                            </div>
                            <div class="team-caption">
                                <span>Jane Smith</span>
                                <h3><a href="#">Head Groomer</a></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-">
                        <div class="single-team mb-30">
                            <div class="team-img">
                                <img src="assets/img/gallery/team3.png" alt="">
                            </div>
                            <div class="team-caption">
                                <span>Pule W Smith</span>
                                <h3><a href="#">Veterinary Nurse</a></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Team End -->
        <!--? Testimonial Start -->
        <div class="testimonial-area testimonial-padding section-bg" data-background="assets/img/gallery/section_bg03.png">
            <div class="container">
                <!-- Testimonial contents -->
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-8 col-lg-8 col-md-10">
                        <div class="h1-testimonial-active dot-style">
                            <!-- Single Testimonial -->
                            <div class="single-testimonial text-center">
                                <div class="testimonial-caption ">
                                    <!-- founder -->
                                    <div class="testimonial-founder">
                                        <div class="founder-img mb-40">
                                            <img src="assets/img/gallery/testi-logo.png" alt="">
                                            <span>Margaret Lawson</span>
                                            <p>Pet Owner</p>
                                        </div>
                                    </div>
                                    <div class="testimonial-top-cap">
                                        <p>“VetGroom Hub took great care of my dog — friendly staff and a clean facility.”</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Additional testimonials retained from template for layout -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Why Choose Us End -->
        <!--? Testimonials Start -->
        <div class="home_blog-area section-padding30">
            <div class="container">
                <div class="row justify-content-sm-center">
                    <div class="cl-xl-7 col-lg-8 col-md-10">
                        <!-- Section Tittle -->
                        <div class="section-tittle text-center mb-70">
                            <span>What our clients say</span>
                            <h2>Happy Pet Owners</h2>
                        </div> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="single-blogs mb-30">
                            <div class="blog-img">
                                <img src="assets/img/gallery/blog1.png" alt="Happy dog">
                            </div>
                            <div class="blogs-cap">
                                <div class="date-info">
                                    <span>Dog Owner</span>
                                    <p>Jan 15, 2023</p>
                                </div>
                                <h4>"Best grooming service my Golden Retriever has ever had!"</h4>
                                <p class="testimonial-text">The team at VetGroom Hub is amazing. They're gentle, professional, and my dog actually gets excited for grooming appointments now!</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="single-blogs mb-30">
                            <div class="blog-img">
                                <img src="assets/img/gallery/blog2.png" alt="Happy cat">
                            </div>
                            <div class="blogs-cap">
                                <div class="date-info">
                                    <span>Cat Owner</span>
                                    <p>Mar 22, 2023</p>
                                </div>
                                <h4>"Finally found a vet that understands anxious cats"</h4>
                                <p class="testimonial-text">My cat used to be terrified of vet visits until we found VetGroom Hub. Their patience and expertise made all the difference.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="single-blogs mb-30">
                            <div class="blog-img">
                                <img src="assets/img/gallery/blog3.png" alt="Multiple pets">
                            </div>
                            <div class="blogs-cap">
                                <div class="date-info">
                                    <span>Multiple Pets</span>
                                    <p>Nov 30, 2023</p>
                                </div>
                                <h4>"Managing care for my three dogs has never been easier"</h4>
                                <p class="testimonial-text">The scheduling system is a lifesaver! I can book all my dogs' appointments at once and get reminders for each one.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonials End -->
        <!--? contact-animal-owner Start -->
        <div class="contact-animal-owner section-bg" data-background="assets/img/gallery/section_bg04.png">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="contact_text text-center">
                            <div class="section_title text-center">
                                <h3>Need urgent advice? Call us!</h3>
                                <p>Our team is available to answer booking and care questions during business hours.</p>
                            </div>
                            <div class="contact_btn d-flex align-items-center justify-content-center">
                                <a href="contact.php" class="btn white-btn">Contact Us</a>
                                <p>Or <a href="tel:+60123456789"> +60 12-345 6789</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- contact-animal-owner End -->
    </main>
    <footer>
        <!-- Footer Start-->
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                       <div class="single-footer-caption mb-50">
                         <div class="single-footer-caption mb-30">
                              <!-- logo -->
                             <div class="footer-logo mb-25">
                                 <a href="homepage.php"><img src="assets/img/logo/logo2.png" alt="VetGroom Hub"></a>
                             </div>
                             <div class="footer-tittle">
                                 <div class="footer-pera">
                                     <p>Professional grooming and veterinary services for your beloved pets.</p>
                                </div>
                             </div>
                             <!-- social -->
                             <div class="footer-social">
                                 <a href="#"><i class="fab fa-facebook-square"></i></a>
                                 <a href="#"><i class="fab fa-twitter-square"></i></a>
                                 <a href="#"><i class="fab fa-linkedin"></i></a>
                                 <a href="#"><i class="fab fa-pinterest-square"></i></a>
                             </div>
                         </div>
                       </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Company</h4>
                                <ul>
                                    <li><a href="homepage.php">Home</a></li>
                                    <li><a href="aboutUs.php">About</a></li>
                                    <li><a href="feedback.php">Feedback</a></li>
                                    <li><a href="contact.php">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-7">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Services</h4>
                                <ul>
                                    <li><a href="services.php#grooming">Pet Grooming</a></li>
                                    <li><a href="services.php#vet">Veterinary Care</a></li>
                                    <li><a href="services.php#vaccination">Vaccination</a></li>
                                    <li><a href="services.php#boarding">Pet Boarding</a></li>
                                    <li><a href="services.php#packages">Packages</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Get in Touch</h4>
                                <ul>
                                 <li><a href="tel:+60123456789">+60 12-345 6789</a></li>
                                 <li><a href="mailto:info@vetgroomhub.com">info@vetgroomhub.com</a></li>
                                 <li><a href="#">Kuala Lumpur, Malaysia</a></li>
                             </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer-bottom area -->
        <div class="footer-bottom-area">
            <div class="container">
                <div class="footer-border">
                     <div class="row d-flex align-items-center">
                         <div class="col-xl-12 ">
                             <div class="footer-copy-right text-center">
                                 <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> VetGroom Hub. All rights reserved.</p>
                             </div>
                         </div>
                     </div>
                </div>
            </div>
        </div>
        <!-- Footer End-->
    </footer>
    <!-- Scroll Up -->
    <div id="back-top" >
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>

    <!-- JS here -->
    
    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="./assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/animated.headline.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>

    <!-- Nice-select, sticky -->
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>
    
    <!-- contact js -->
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
    
    <!-- Jquery Plugins, main Jquery -->    
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>
        
    </body>
</html>

