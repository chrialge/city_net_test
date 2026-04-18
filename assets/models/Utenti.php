<?php


// Class Company
// This class is used to manage the company data and perform operations related to it.
class Utenti
{
    public $nome;
    public $cognome;
    public $password;
    public $email;
    public $confirm_password;




    // Constructor to initialize the company object with the provided data
    public function __construct($nome, $cognome, $password, $confirm_password, $email)
    {
        $this->nome = htmlspecialchars($nome);
        $this->cognome = htmlspecialchars($cognome);
        $this->password = htmlspecialchars($password);
        $this->confirm_password = htmlspecialchars($confirm_password);
        $this->email = htmlspecialchars($email);
    }

    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {
        // Hash the password using md5
        $hashedPassword = md5($this->password);

        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO utenti (nome, cognome, password, email) VALUES (?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ssss", $this->nome, $this->cognome, $hashedPassword, $this->email);

        // Execute the statement and check for success
        if ($stmt->execute()) {

            // return true if the data is saved successfully
            return true;
        } else {

            // return false if there was an error saving the data
            return false;
        }
    }

    /**
     * Get all companies from the database
     * @param mysqli $connection The database connection object 
     * @return mysqli_result Returns the result set of the query
     */
    public static function all($connection)
    {
        // Query to select all records from the company table
        $query = "SELECT * FROM utenti";

        // Execute the query and return the result
        return $connection->query($query);
    }
}
