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
        <div class="container_credentials">
            <h1>Registrazione azienda</h1>

            <form action="register.php" method="POST">

                <div class="container_input">
                    <label for="name_company">Nome azienda:</label>

                    <input type="text" id="name_company" name="name_company" onblur="check_name()" onkeyup="hide_error_name()" required>



                    <span class="error_js" id="error_name_lower">Nome deve essere di almeno 3 carratteri</span>
                    <span class="error_js" id="name_error_greatest">Nome deve essere massimo di 100 carratteri</span>
                </div>

                <div class="container_input">
                    <label for="password">Password:</label>
                    <div class="field_password">
                        <input type="password" id="password" name="password" required>
                        <i class="fa-solid fa-eye" onclick="showPassword(event)"></i>
                    </div>

                </div>

                <div class="container_input">
                    <label for="confirm_password">Conferma password:</label>

                    <div class="field_password">
                        <input type="password" id="confirm_password" name="confirm_password" onblur="check_password()" onkeyup="hide_error_password()" required>
                        <i class="fa-solid fa-eye" onclick="showPassword(event)"></i>
                    </div>

                    <span class="error_js" id="error_password">Le password non combacciano</span>
                    <span class="error_js" id="error_password_empty">Non puoi lasciare il campo vuoto</span>
                </div>

                <div class="container_input">
                    <label for="vat_number">Partita Iva</label>
                    <input type="text" id="vat_number" name="vat_number" required>
                </div>

                <div class="container_input">
                    <label for="telephone">Telefono:</label>
                    <input type="text" id="telephone" name="telephone" required>
                </div>

                <div class="container_input">
                    <label for="address">Indirizzo:</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="container_input">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>
                </div>

                <div class="button_form">
                    <button type="submit">Registrarti</button>
                </div>

            </form>
        </div>
    </div>



    <script src="./assets/js/script.js"></script>
</body>

</html>