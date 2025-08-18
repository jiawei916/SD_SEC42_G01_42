<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Out - VetGroom Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ===== Top Header ===== -->
<header>
    <h1>VetGroom Hub</h1>
</header>

<!-- ===== Navigation Bar ===== -->
<nav>
    <a href="homepage.php">Home</a>
    <a href="registerGuest.html">Register</a>
    <a href="signIn.html">Sign In</a>
    <a href="signOut.php">Sign Out</a>
</nav>

<!-- ===== Main Content ===== -->
<main>
    <div class="container">
        <h2>Sign Out</h2>
        <p>Click the button below to log out of your account.</p>
        <form method="post" action="logout.php">
            <button type="submit">Sign Out</button>
        </form>
    </div>
</main>

</body>
</html>
