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
    $query = "SELECT * FROM utenti WHERE id = '$id' AND email = '$userName'";

    // execute the query
    $result = $mysqli->query($query);

    // extract the data from the query
    $data = $result->fetch_assoc();


    $nome = $data['nome'];
    $cognome = $data['cognome'];
}



?>
<!DOCTYPE html>
<html lang="en">

<?php
$pagina = "Impostazioni";
$base_url = "../";
require_once __DIR__ . '\..\assets\partials\head.php';
?>

<body>

    <?php
    require_once __DIR__ . '\..\assets\partials\header.php';
    ?>
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

            <h5>Modifica dati di accesso</h5>

            <form action="../assets/controller/updateLogin.php" method="post" onsubmit="check_form_access(event)">

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


                <!-- container buttons -->
                <div class="button_form">

                    <!-- button submit -->
                    <button class="btn_submit_access" type="submit">Modifica</button>

                    <!-- button loading -->
                    <button class="btn_load_access" disabled>Attendi...</button>
                </div>
            </form>
        </div>




    </main>


    <script src="<?php echo $base_url; ?>assets/js/function.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>