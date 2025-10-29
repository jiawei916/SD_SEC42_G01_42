<?php
session_start();
session_destroy();

// Redirect back to homepage
header("Location: index.php");
exit;
?>
