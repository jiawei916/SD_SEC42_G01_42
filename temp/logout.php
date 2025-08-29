<?php
session_start();
session_destroy();

// Redirect back to homepage
header("Location: homepage.php");
exit;
?>
