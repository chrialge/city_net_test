<?php


// Class PokemonTipologie
// This class is used to manage the pokemon tipologie data and perform operations related to it.
class ranghi
{
    public $nome;
    public $descrizione;
    public $attributi;
    public $attributiSociali;
    public $puntiConoscenza;
    public $limiteLivelloConoscenza;
    public $limitePokemon;




    // Constructor to initialize the company object with the provided data
    public function __construct($nome, $descrizione, $attributi, $attributiSociali, $puntiConoscenza, $limiteLivelloConoscenza, $limitePokemon)
    {

        $this->nome = htmlspecialchars($nome);
        $this->descrizione = htmlspecialchars($descrizione);
        $this->attributi = htmlspecialchars($attributi);
        $this->attributiSociali = htmlspecialchars($attributiSociali);
        $this->puntiConoscenza = htmlspecialchars($puntiConoscenza);
        $this->limiteLivelloConoscenza = htmlspecialchars($limiteLivelloConoscenza);
        $this->limitePokemon = htmlspecialchars($limitePokemon);
    }



    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO ranghi (nome, descrizione, attributi, attributiSociali, puntiConoscenza, limiteLivelloConoscenza, limitePokemon) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ssssss",  $this->nome, $this->descrizione, $this->attributi, $this->attributiSociali, $this->puntiConoscenza, $this->limiteLivelloConoscenza, $this->limitePokemon);

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
        $query = "SELECT * FROM ranghi";

        // Execute the query and return an array of rows
        $result = $connection->query($query);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }
        return $rows;
    }

    public static function getRanghiId($connection, $id)
    {
        $sql = "SELECT * FROM ranghi WHERE id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $ranghi = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $ranghi;
        }

        return null;
    }


    public static function getRanghiByArrayId($connection, $ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM ranghi WHERE id IN ($placeholders)";

        if ($statement = $connection->prepare($sql)) {
            $types = str_repeat('i', count($ids));
            $statement->bind_param($types, ...$ids);
            $statement->execute();
            $result = $statement->get_result();
            $ranghi = [];
            while ($row = $result->fetch_assoc()) {
                $ranghi[] = $row;
            }
            $statement->close();
            return $ranghi;
        }

        return null;
    }
}
