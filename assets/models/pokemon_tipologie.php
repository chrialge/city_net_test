<?php


// Class PokemonTipologie
// This class is used to manage the pokemon tipologie data and perform operations related to it.
class PokemonTipologie
{
    public $nome;
    public $descrizione;
    public $colorePrincipale;
    public $coloreSecondario;
    public $coloreTerziario;





    // Constructor to initialize the company object with the provided data
    public function __construct($nome, $descrizione, $colorePrincipale, $coloreSecondario, $coloreTerziario)
    {

        $this->nome = htmlspecialchars($nome);
        $this->descrizione = htmlspecialchars($descrizione);
        $this->colorePrincipale = htmlspecialchars($colorePrincipale);
        $this->coloreSecondario = htmlspecialchars($coloreSecondario);
        $this->coloreTerziario = htmlspecialchars($coloreTerziario);
    }


    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO pokemon_tipologie ( nome, descrizione, colore_principale, colore_secondario, colore_terziario) VALUES (?, ?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("sssss",  $this->nome, $this->descrizione, $this->colorePrincipale, $this->coloreSecondario, $this->coloreTerziario);

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
        $query = "SELECT * FROM pokemon_tipologie";

        // Execute the query and return the result
        return $connection->query($query);
    }

    public static function getPokemonTipologieById($connection, $id)
    {
        $sql = "SELECT * FROM pokemon_tipologie WHERE id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $tipologia = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $tipologia;
        }

        return null;
    }


    public static function getPokemonTipologieByArrayId($connection, $ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM pokemon_tipologie WHERE id IN ($placeholders)";

        if ($statement = $connection->prepare($sql)) {
            $types = str_repeat('i', count($ids));
            $statement->bind_param($types, ...$ids);
            $statement->execute();
            $result = $statement->get_result();
            $tipologie = [];
            while ($row = $result->fetch_assoc()) {
                $tipologie[] = $row;
            }
            $statement->close();
            return $tipologie;
        }

        return null;
    }
}
