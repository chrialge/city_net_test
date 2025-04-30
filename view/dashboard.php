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

    <title>Dashboard</title>
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