<?php

require_once __DIR__ . '/assets/helper/db.php';

$pagina = "Registrazione";
$base_url = "";


// connect to the database
$mysqli = DB::connect();


// disconnect from the database
DB::disconnect($mysqli);
?>


<!DOCTYPE html>
<html lang="it">
<?php
require_once __DIR__ . '\assets\partials\head.php';
?>

<body>
    <div class="page_register">

        <!-- error message of php -->
        <?php

        // Start the session
        session_start();

        // Check if the session variable is set and not empty
        if (isset($_SESSION['message'])) {

            // part 1 of the message
            $part1 = "<div class='alert_message_list' id='message_error'>
                    <ul>";

            // part 2 of the message
            $part2 = " </ul>
                       <i class='fa-solid fa-xmark' onclick='close_message_error()'></i>
                    </div>";

            // loop for each message
            foreach ($_SESSION['message'] as $message) {
                // add the message to the list
                $part1 .= "<li>" . $message . "</li>";
            }
            // close the list and echo the message
            echo $part1 . $part2;

            // unset the session variable to avoid showing the message again
            unset($_SESSION['message']);

            // destroy the session if you want to clear all session data
            session_destroy();
        }

        ?>

        <!-- container credentails-->
        <div class="container_credentials">

            <!-- title card -->
            <h5>Registrazione</h5>


            <!-- form register -->
            <form action="./assets/controller/register.php" method="POST" onsubmit="check_form(event)">

                <!-- container for input of name_company -->


                <div class="input-container">
                    <input type="text" id="nome" name="nome" required="true" onblur="check_name()" onkeyup="hide_error_name()">
                    <label for="nome" class="label" id="label-nome">Nome</label>
                    <div id="underline-nome" class="underline"></div>
                </div>
                <!-- error js  -->
                <span class="error_js" id="error_name_lower">Nome deve essere di almeno 3 carratteri</span>
                <span class="error_js" id="name_error_greatest">Nome deve essere massimo di 100 carratteri</span>

                <div class="input-container">
                    <input type="text" id="cognome" name="cognome" required="true" onblur="check_cognome()" onkeyup="hide_error_cognome()">
                    <label for="cognome" class="label" id="label-cognome">Cognome</label>
                    <div id="underline-cognome" class="underline"></div>
                </div>
                <!-- error js  -->
                <span class="error_js" id="error_cognome_lower">Cognome deve essere di almeno 3 carratteri</span>
                <span class="error_js" id="error_cognome_greatest">Cognome deve essere massimo di 100 carratteri</span>

                <div class="input-container">
                    <input type="text" id="email" name="email" required="true" onblur="check_email()" onkeyup="hide_error_email()">
                    <label for="email" class="label" id="label-email">Email</label>
                    <div id="underline-email" class="underline"></div>
                </div>
                <span class="error_js" id="error_email">l'email non è valida</span>


                <div class="input-container">
                    <input type="password" id="password" name="password" required="true" style="padding-right: 30px;">
                    <label for="password" class="label" id="label-password">Password</label>
                    <i class="fa-solid fa-eye" id="icon-eye" onclick="showPassword(event)"></i>
                    <div class="underline" id="underline-password"></div>
                </div>

                <div class="input-container">
                    <input type="password" id="confirm_password" name="confirm_password" required="true" style="padding-right: 30px;" onblur="check_password()" onkeyup="hide_error_password()">
                    <label for="confirm_password" class="label" id="label-confirm-password">Conferma Password</label>
                    <i class="fa-solid fa-eye" id="icon-eye" onclick="showPassword(event)"></i>
                    <div class="underline" id="underline-confirm-password"></div>
                </div>
                <!-- error js -->
                <span class="error_js" id="error_password">Le password non combacciano</span>
                <span class="error_js" id="error_password_empty">Non puoi lasciare il campo vuoto</span>







                <!-- link for login page -->
                <a href="./view/login.php" class="login-registraztion-link">
                    Non
                    hai gia un account?
                </a>

                <!-- container button -->
                <div class="button_form">

                    <!-- button submit  -->
                    <button class="btn_submit" type="submit">Registrarti</button>

                    <!-- btn loading -->
                    <button class="btn_load" disabled>Attendi...</button>
                </div>

            </form>
        </div>
    </div>



    <script src="./assets/js/script.js"></script>
</body>

</html>