<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";

// Determine user role based on session or default to guest
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    // Check if role is stored in session (you should set this during login)
    if (isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
    } else {
        // Default role for logged-in users without a specific role
        $userRole = 'customer';
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "vetgroomlist";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<script>alert('Database connection failed: " . $conn->connect_error . "'); window.location.href='feedback.php';</script>");
    }

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $subject  = $_POST['subject'];
    $rating   = isset($_POST['feedback']) ? $_POST['feedback'] : '';
    $message  = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, subject, rating, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $subject, $rating, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you! Your feedback has been submitted.'); window.location.href='feedback.php';</script>";
    } else {
        echo "<script>alert('Error submitting feedback: " . $stmt->error . "'); window.location.href='feedback.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>VetGroom Hub | Feedback</title>
    <meta name="description" content="Provide feedback for VetGroom Hub services">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

   <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
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
        
        /* Navigation adjustments */
        .main-menu nav ul li a[href="feedback.php"] {
            color: #3aa9e4;
            font-weight: 600;
        }
        
        /* Update footer content to match VetGroom Hub */
        .footer-tittle h4 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 18px;
        }
        button{
          color: #333;
        }
        .emoji-feedback {
          display: flex;
          flex-direction: column;
          gap: 15px;
        }

        .emoji-btn {
          font-size: 22px;
          padding: 12px 30px 12px 30px;
          border: 1px solid #ddd;
          border-radius: 12px;
          background-color: #f9f9f9;
          cursor: pointer;
          transition: all 0.3s ease;
          text-align: left;
        }

        .emoji-btn:hover {
          background-color: #e5e5e5ff;
          border-color: #dadadaff;
          transform: scale(1.05);
        }
        
        .emoji-btn.selected {
          background-color: none;
          box-shadow: rgba(26, 255, 53, 1) 0 0 6px;
        }

    </style>
</head>
<body>
    <!-- Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/logo2.png" alt="VetGroom Hub Logo">
                </div>
            </div>
        </div>
    </div>
  <!-- Site title -->
  <header>
        <!--? Header Start -->
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
                                            <?php else: ?>
                                                <a href="signIn.php">Sign In</a>
                                                <a href="registerGuest.php">Register</a>
                                            <?php endif; ?>
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
         <!-- Hero Area Start -->
         <div class="slider-area2 slider-height2 d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap text-center pt-50">
                            <h2>We Value Your Feedback</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero Area End -->
        
        <!-- ================ contact section start ================= -->
        <section class="contact-section">
            <div class="container">
                
                <div class="row">
                    <div class="col-12">
                        <h2 class="contact-title">Your thoughts help us improve our services.</h2>
                    </div>
                    <div class="col-lg-8">
                        <form class="form-contact contact_form" action="feedback.php" method="post" id="contactForm">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="name" id="name" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your name'" placeholder="Enter your name" value="<?php echo $isLoggedIn ? htmlspecialchars($userName) : ''; ?>" <?php echo $isLoggedIn ? 'readonly' : ''; ?> required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="email" id="email" type="email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email address'" placeholder="Email" value="<?php echo $isLoggedIn && isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>" <?php echo $isLoggedIn && isset($_SESSION['user_email']) ? 'readonly' : ''; ?> required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Subject'" placeholder="Enter Subject" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="message" id="message" cols="30" rows="9" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Message'" placeholder="Enter Message" required><?php echo isset($_SESSION['user_name']) ? "Hello, I would like to provide feedback about..." : ""; ?></textarea>
                                    </div>
                                </div>
                                <!-- Hidden input for feedback rating -->
                                <input type="hidden" name="feedback" id="feedbackInput" value="">
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="button button-contactForm boxed-btn">Submit Feedback</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-3 offset-lg-1">
                      <div class="emoji-feedback">
                        <button type="button" class="emoji-btn" data-value="Terrible">😡 Terrible</button>
                        <button type="button" class="emoji-btn" data-value="Bad">🙁 Bad</button>
                        <button type="button" class="emoji-btn" data-value="Okay">😐 Okay</button>
                        <button type="button" class="emoji-btn" data-value="Good">😊 Good</button>
                        <button type="button" class="emoji-btn" data-value="Satisfied">😍 Satisfied</button>
                      </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ================ contact section end ================= -->
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
    
    <!-- Nice-select, sticky -->
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>

    <!-- contact js -->
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
    
    <!-- Jquery Plugins, main Jquery -->	
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>
    <script>
    // Feedback button input
    document.addEventListener("DOMContentLoaded", function () {
      const buttons = document.querySelectorAll(".emoji-btn");
      const feedbackInput = document.getElementById("feedbackInput");

      buttons.forEach(button => {
        button.addEventListener("click", () => {
          // Remove 'selected' class from all buttons
          buttons.forEach(btn => btn.classList.remove("selected"));
          
          // Add 'selected' class to clicked button
          button.classList.add("selected");
          
          // Save value to hidden input
          feedbackInput.value = button.getAttribute("data-value");
        });
      });
      
      // Form validation to ensure a rating is selected
      document.getElementById("contactForm").addEventListener("submit", function(e) {
        if (!feedbackInput.value) {
          e.preventDefault();
          alert("Please select a rating by clicking one of the emoji buttons.");
        }
      });
    });
    </script>
</body>
</html>