<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- google font for ROBOTO -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- cdn fontawesone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Login</title>
</head>

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
            <h1>Accedi</h1>

            <!-- link for register page  -->
            <a href="../index.php">
                Non hai gia un account? Registrati
            </a>

            <!-- form for login -->
            <form action="../assets/controller/login.php" method="POST" onsubmit="check_form(event)">

                <!-- container for input of name_company -->
                <div class="container_input">
                    <label for="name_company">Nome azienda:*</label>
                    <input type="text" id="name_company" name="name_company" onblur="check_name()" onkeyup="hide_error_name()" required>

                    <!-- error js -->
                    <span class="error_js" id="error_name_lower">Nome deve essere di almeno 3 carratteri</span>
                    <span class="error_js" id="name_error_greatest">Nome deve essere massimo di 100 carratteri</span>
                </div>

                <!-- container for input of password -->
                <div class="container_input">
                    <label for="password">Password:*</label>

                    <!-- field of password -->
                    <div class="field_password">
                        <input type="password" id="password" name="password" required>
                        <i class="fa-solid fa-eye" onclick="showPassword(event)"></i>
                    </div>

                    <!-- error js-->
                    <span class="error_js" id="error_password">Devi mettere la password</span>
                </div>

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

    <script src="../assets/js/validationLogin.js"></script>
</body>

</html>