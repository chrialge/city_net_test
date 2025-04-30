<?php

require_once __DIR__ . '/assets/helper/db.php';

// connect to the database
$mysqli = DB::connect();

// create the table companies if it doesn't exist
$mysqli->query("CREATE TABLE IF NOT EXISTS `companies` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `name_company` VARCHAR(100) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    `vat_number` VARCHAR(11) NOT NULL,
    `telephone` VARCHAR(13) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `birth_of_day` DATE DEFAULT NULL,
    `address` TEXT(1000) NOT NULL,
    `create_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `update_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);");

// disconnect from the database
DB::disconnect($mysqli);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- google font for ROBOTO -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- cdn fontawesone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Registazione</title>
</head>

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
            <h1>Registrazione azienda</h1>

            <!-- link for login page -->
            <a href="./view/login.php">
                hai gia un account? Accedi
            </a>

            <!-- form register -->
            <form action="register.php" method="POST" onsubmit="check_form(event)">

                <!-- container for input of name_company -->
                <div class="container_input">
                    <label for="name_company">Nome azienda:*</label>
                    <input type="text" id="name_company" name="name_company" onblur="check_name()" onkeyup="hide_error_name()" required>

                    <!-- error js  -->
                    <span class="error_js" id="error_name_lower">Nome deve essere di almeno 3 carratteri</span>
                    <span class="error_js" id="name_error_greatest">Nome deve essere massimo di 100 carratteri</span>
                </div>

                <!--  container for input of password-->
                <div class="container_input">
                    <label for="password">Password:*</label>

                    <!-- field for input of password -->
                    <div class="field_password">
                        <input type="password" id="password" name="password" required>
                        <i class="fa-solid fa-eye" onclick="showPassword(event)"></i>
                    </div>
                </div>

                <!-- container for input of confirm_password -->
                <div class="container_input">
                    <label for="confirm_password">Conferma password:*</label>

                    <!-- field for input of confirm_password -->
                    <div class="field_password">
                        <input type="password" id="confirm_password" name="confirm_password" onblur="check_password()" onkeyup="hide_error_password()" required>
                        <i class="fa-solid fa-eye" onclick="showPassword(event)"></i>
                    </div>

                    <!-- error js -->
                    <span class="error_js" id="error_password">Le password non combacciano</span>
                    <span class="error_js" id="error_password_empty">Non puoi lasciare il campo vuoto</span>
                </div>

                <!-- container for input of vat_number -->
                <div class="container_input">
                    <label for="vat_number">Partita Iva:*</label>
                    <input type="text" id="vat_number" name="vat_number" onblur="check_vat()" onkeyup="hide_error_vat()" required>

                    <!-- error js -->
                    <span class="error_js" id="error_vat">La partita iva deve essere di 11 carratteri numerici</span>
                </div>

                <!-- container for input of telephone -->
                <div class="container_input">
                    <label for="telephone">Telefono:</label>
                    <input type="text" id="telephone" name="telephone" onblur="check_telephone()" onkeyup="hide_error_telephone()">

                    <!-- error js -->
                    <span class="error_js" id="error_telephone">
                        Il numero di telefono deve essere di 9/10 carratteri numerici
                    </span>
                </div>

                <!-- container for every data of address -->
                <div class="container_input_address">

                    <!-- field of province -->
                    <div class="field_address">
                        <label for="province">Provincia:*</label>
                        <input type="text" id="province" name="province" onblur="check_address(event, 'province')" onkeyup="hide_error_address('province')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_province">
                            La provincia deve essere di 3 carratteri
                        </span>
                    </div>

                    <!-- field of city -->
                    <div class="field_address">
                        <label for="city">Comune:*</label>
                        <input type="text" id="city" name="city" onblur="check_address(event, 'city')" onkeyup="hide_error_address('city')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_city">
                            Il comune deve essere di almeno 3 carratteri
                        </span>
                    </div>

                    <!-- field of address/street -->
                    <div class="field_address">
                        <label for="address">Indirizzo:*</label>
                        <input type="text" id="address" name="address" onblur="check_address(event, 'address')" onkeyup="hide_error_address('address')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_address">
                            l'indirizzo deve essere di almeno 3 carratteri
                        </span>
                    </div>

                    <!--field of cap  -->
                    <div class="field_address">
                        <label for="cap">CAP:*</label>
                        <input type="text" id="cap" name="cap" onblur="check_cap()" onkeyup="hide_error_cap()" required>

                        <!-- error js -->
                        <span class="error_js" id="error_cap">Il CAP deve essere di 5 carratteri numerici</span>
                    </div>

                    <!-- error js for all address -->
                    <span class="error_js" id="error_address_all">
                        Per favore, compilare tutti i campi di indirizzo completo.
                    </span>

                </div>


                <!-- container for input of birth_of_day -->
                <div class="container_input">
                    <label for="birth_of_day">Data di fondazione:</label>
                    <input type="date" id="birth_of_day" name="birth_of_day">
                </div>

                <!-- container for input of email -->
                <div class="container_input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" onblur="check_email()" onkeyup="hide_error_email()">

                    <!-- error js -->
                    <span class="error_js" id="error_email">Email non valida</span>
                </div>

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