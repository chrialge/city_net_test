<!DOCTYPE html>
<html lang="en">

<?php
$pagina = "Dashboard";
$base_url = "../";
require_once __DIR__ . '\..\assets\partials\head.php';
?>

<body>

    <?php
    require_once __DIR__ . '\..\assets\partials\header.php';
    ?>

    <!-- message session php -->
    <?php
    // Start the session
    session_start();

    // Check if the session variable is set and display the message
    if (isset($_SESSION['message'])) {

        // Display the message in a div with a class of "alert_message"
        echo "<div class='alert_message_succefull'>
                    <span>" . $_SESSION['message'] . "</span>
                    <i class='fa-solid fa-xmark' onclick='close_message_error(event)'></i>
                 </div>";

        // Unset the session variable to avoid showing the message again on refresh
        unset($_SESSION['message']);
    }
    ?>


    <!-- site main -->
    <main id="site_main">
        <div class="container">
            <h1>Dashboard</h1>
            <p>Benvenuto nella tua dashboard!</p>

        </div>
    </main>

    <script src="../assets/js/function.js"></script>
</body>

</html>