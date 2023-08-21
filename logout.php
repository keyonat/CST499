<?php
error_reporting(E_ALL ^ E_NOTICE);
if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
unset($_SESSION['logged_in']);

// destroy the session
session_destroy();

// redirect the user to the login page
header('Location: login.php');
exit;
?>