

<?php


// Class PokemonTipologie
// This class is used to manage the pokemon tipologie data and perform operations related to it.
class Lingue
{
    public $nome;
    public $attivo;





    // Constructor to initialize the company object with the provided data
    public function __construct($nome, $attivo)
    {

        $this->nome = htmlspecialchars($nome);
        $this->attivo = htmlspecialchars($attivo);
    }



    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO lingue (nome, attivo) VALUES (?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ss",  $this->nome, $this->attivo);

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
        $query = "SELECT * FROM lingue";

        // Execute the query and return the result
        return $connection->query($query);
    }

    public static function getLingueById($connection, $id)
    {
        $sql = "SELECT * FROM lingue WHERE id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $lingue = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $lingue;
        }

        return null;
    }
}
