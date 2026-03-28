<?php
// 1. Start the session to access it
session_start();

// 2. Unset all session variables (clears the data)
$_SESSION = array();

// 3. Destroy the actual session file on the server
session_destroy();

// 4. Redirect the user back to the login page or home page
header("Location: index.php");
exit;
?>