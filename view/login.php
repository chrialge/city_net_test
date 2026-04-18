<!DOCTYPE html>
<html lang="en">

<?php
$pagina = "Login";
$base_url = "../";
require_once __DIR__ . '\..\assets\partials\head.php';
?>


<body>
    <div class="page_login">

        <!-- php error message -->
        <?php

        // Start the session
        session_start();

        // Check if the session variable is set and display the message
        if (isset($_SESSION['message'])) {

            // Display the message in a div with a class of "alert_message"
            echo "<div class='alert_message' id='message_error'>
                    <span>" . $_SESSION['message'] . "</span>
                    <i class='fa-solid fa-xmark' onclick='close_message_error()'></i>
                 </div>";

            // Unset the session variable to avoid showing the message again on refresh
            unset($_SESSION['message']);

            // Destroy the session if you want to clear all session data
            session_destroy();
        }
        ?>

        <!-- container credentials -->
        <div class="container_credentials">

            <!-- title -->
            <h5>Accedi</h5>



            <!-- form for login -->
            <form action="../assets/controller/login.php" method="POST" onsubmit="check_form(event)">

                <div class="input-container">
                    <input type="text" id="email" name="email" required="true" onblur="check_email()" onkeyup="hide_error_email()">
                    <label for="email" class="label" id="label-email">Email</label>
                    <div id="underline-email" class="underline"></div>
                </div>
                <span class="error_js" id="error_email">l'email non è valida</span>

                <div class="input-container">
                    <input type="password" id="password" name="password" required="true" style="padding-right: 30px;">
                    <label for="password" class="label">Password</label>
                    <i class="fa-solid fa-eye" id="icon-eye" onclick="showPassword(event)"></i>
                    <div class="underline"></div>
                </div>

                <span class="error_js" id="error_password">Devi mettere la password</span>





                <!-- link for register page  -->
                <a href="../index.php" class="login-registraztion-link">
                    Non hai gia un account?
                </a>
                <!-- container buttons -->
                <div class="button_form">

                    <!-- button submit -->
                    <button class="btn_submit" type="submit">Accedi</button>

                    <!-- button loading -->
                    <button class="btn_load" disabled>Attendi...</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>

</html>