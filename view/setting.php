<?php

require_once   '../assets/helper/db.php';

// Start the session
session_start();

// get the user id and name from the session
$id = $_SESSION['userId'];
$userName = $_SESSION['userName'];

// connect to the database
$mysqli = DB::connect();

// if the connection fails, show an error message
if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
} else {

    // query to get the data of the COMPANY
    $query = "SELECT * FROM companies WHERE id = '$id' AND name_company = '$userName'";

    // execute the query
    $result = $mysqli->query($query);

    // extract the data from the query
    $data = $result->fetch_assoc();

    // get the telephone
    $telephone = $data['telephone'];

    // remove the first 3 characters from the telephone number
    // the first 3 characters are the country code (e.g. +39 for Italy)
    $telephone = substr($telephone, 3, strlen($telephone) - 3);

    // spli the address into an array
    $arrayAddress = explode(',', $data['address']);

    // get the address from the array
    $province = $arrayAddress[0];
    $city = $arrayAddress[1];
    $address = $arrayAddress[2] . ', ' . $arrayAddress[3];
    $cap = $arrayAddress[4];
}



?>
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

    <title>Impostazioni</title>
</head>

<body>

    <!-- site header -->
    <header id="site_header">

        <!-- navbar -->
        <nav class="navbar">

            <!-- left/logo -->
            <div class="left">
                <i class="fa-solid fa-blog"></i>
            </div>

            <!-- right -->
            <div class="right">
                <ul>

                    <!-- Dashboard -->
                    <li>
                        <a href="./dashboard.php">
                            <i class="fa-solid fa-table-columns"></i>
                            <div class="tooltip">
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>

                    <!-- setting -->
                    <li>
                        <a href="./setting.php">
                            <i class="fa-solid fa-gears"></i>
                            <div class="tooltip">
                                <span>Impostazioni</span>
                            </div>
                        </a>
                    </li>

                    <!-- logout -->
                    <li>
                        <a href="../assets/controller/logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <div class="tooltip">
                                <span>Logout</span>
                            </div>
                        </a>
                    </li>


                </ul>
            </div>
        </nav>
    </header>

    <!-- message session php -->
    <?php

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

    <!-- php error message -->
    <?php


    // Check if the session variable is set and display the message
    if (isset($_SESSION['error'])) {

        // Display the message in a div with a class of "alert_message"
        echo "<div class='alert_message' id='message_error'>
                    <span>" . $_SESSION['error'] . "</span>
                    <i class='fa-solid fa-xmark' onclick='close_message_error(event)'></i>
                 </div>";

        // Unset the session variable to avoid showing the message again on refresh
        unset($_SESSION['error']);
    }
    ?>

    <!-- site main -->
    <main id="site_main_setting">

        <div class="container_access">

            <h2>Modifica dati di accesso</h2>

            <form action="../assets/controller/updateLogin.php" method="post" onsubmit="check_form_access(event)">

                <!-- container for input of name_company -->
                <div class="container_input">
                    <label for="name_company">Nome azienda:*</label>
                    <input type="text" id="name_company" name="name_company" value="<?php echo $data['name_company'] ?>" onblur="check_name()" onkeyup="hide_error_name()" required>

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
                    <button class="btn_submit_access" type="submit">Modifica</button>

                    <!-- button loading -->
                    <button class="btn_load_access" disabled>Attendi...</button>
                </div>
            </form>
        </div>

        <!-- error message of php -->
        <?php

        // Check if the session variable is set and not empty
        if (isset($_SESSION['error_array'])) {

            // part 1 of the message
            $part1 = "<div class='alert_message_list' id='message_error'>
            <ul>";

            // part 2 of the message
            $part2 = " </ul>
               <i class='fa-solid fa-xmark' onclick='close_message_error(event)'></i>
            </div>";

            // loop for each message
            foreach ($_SESSION['error_array'] as $message) {
                // add the message to the list
                $part1 .= "<li>" . $message . "</li>";
            }
            // close the list and echo the message
            echo $part1 . $part2;

            // unset the session variable to avoid showing the message again
            unset($_SESSION['error_array']);
        }

        ?>

        <div class="container_data">

            <h2>Modifica dei dati dell'azienda</h2>

            <form action="../assets/controller/updateData.php" method="post" onsubmit="check_form_data(event)">

                <!-- container for input of vat_number -->
                <div class="container_input">
                    <label for="vat_number">Partita Iva:*</label>
                    <input type="text" id="vat_number" name="vat_number" value="<?php echo $data['vat_number'] ?>" onblur="check_vat()" onkeyup="hide_error_vat()" required>

                    <!-- error js -->
                    <span class="error_js" id="error_vat">La partita iva deve essere di 11 carratteri numerici</span>
                </div>

                <!-- container for input of telephone -->
                <div class="container_input">
                    <label for="telephone">Telefono:</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo $telephone ?>" onblur="check_telephone()" onkeyup="hide_error_telephone()">

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
                        <input type="text" id="province" name="province" value="<?php echo $province ?>" onblur="check_address('province')" onkeyup="hide_error_address('province')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_province">
                            La provincia deve essere di 3 carratteri
                        </span>
                    </div>

                    <!-- field of city -->
                    <div class="field_address">
                        <label for="city">Comune:*</label>
                        <input type="text" id="city" name="city" value="<?php echo $city ?>" onblur="check_address('city')" onkeyup="hide_error_address('city')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_city">
                            Il comune deve essere di almeno 3 carratteri
                        </span>
                    </div>

                    <!-- field of address/street -->
                    <div class="field_address">
                        <label for="address">Indirizzo:*</label>
                        <input type="text" id="address" name="address" value="<?php echo $address ?>" onblur="check_address('address')" onkeyup="hide_error_address('address')" required>

                        <!-- error js -->
                        <span class="error_js" id="error_address">
                            l'indirizzo deve essere di almeno 3 carratteri
                        </span>
                    </div>

                    <!--field of cap  -->
                    <div class="field_address">
                        <label for="cap">CAP:*</label>
                        <input type="text" id="cap" name="cap" value="<?php echo $cap ?>" onblur="check_cap()" onkeyup="hide_error_cap()" required>

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
                    <input type="date" id="birth_of_day" name="birth_of_day" value="<?php echo $data['birth_of_day'] ?>">
                </div>

                <!-- container for input of email -->
                <div class="container_input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $data['email'] ?>" onblur="check_email()" onkeyup="hide_error_email()">

                    <!-- error js -->
                    <span class="error_js" id="error_email">Email non valida</span>
                </div>

                <!-- container button -->
                <div class="button_form">

                    <!-- button submit  -->
                    <button class="btn_submit_data" type="submit">Modifica</button>

                    <!-- btn loading -->
                    <button class="btn_load_data" disabled>Attendi...</button>
                </div>
            </form>
        </div>
    </main>


    <script src="../assets/js/function.js"></script>
    <script src="../assets/js/validationUpdate.js"></script>
</body>

</html>