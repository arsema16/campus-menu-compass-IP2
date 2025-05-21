<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the logged out page
header("Location: logged_out.html");
exit();
?>
