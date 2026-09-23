<?php
/**
 * Root Router & Landing Entry Point
 */
session_start();

// If user is already authenticated, redirect to their respective dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Head') {
        header("Location: auth_1/home.php");
        exit();
    } elseif ($_SESSION['role'] === 'Faculty') {
        header("Location: auth_2/home.php");
        exit();
    }
}

// Default to landing page
header("Location: landing/index.php");
exit();
?>

