<?php


// Class Allenatore
// This class is used to manage the allenatore data and perform operations related to it.
class Allenatore
{
    public $nomeAllenatore;
    public $codiceProfilo;
    public $master;
    public $idRango;
    public $strenght;
    public $dexterity;
    public $vitality;
    public $insight;
    public $tought;
    public $beauty;
    public $cool;
    public $cute;
    public $clever;
    public $idRangeEta;




    // Constructor to initialize the company object with the provided data
    public function __construct($nomeAllenatore, $codiceProfilo, $master, $idRango, $strenght, $dexterity, $vitality, $insight, $tought, $beauty, $cool, $cute, $clever, $idRangeEta)
    {

        $this->nomeAllenatore = htmlspecialchars($nomeAllenatore);
        $this->codiceProfilo = htmlspecialchars($codiceProfilo);
        $this->master = htmlspecialchars($master);
        $this->idRango = htmlspecialchars($idRango);
        $this->strenght = htmlspecialchars($strenght);
        $this->dexterity = htmlspecialchars($dexterity);
        $this->vitality = htmlspecialchars($vitality);
        $this->insight = htmlspecialchars($insight);
        $this->tought = htmlspecialchars($tought);
        $this->beauty = htmlspecialchars($beauty);
        $this->cool = htmlspecialchars($cool);
        $this->cute = htmlspecialchars($cute);
        $this->clever = htmlspecialchars($clever);
        $this->idRangeEta = htmlspecialchars($idRangeEta);
    }



    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO  (nomeAllenatore, codiceProfilo, master, idRango, strenght, dexterity, vitality, insight, tought, beauty, cool, cute, clever, idRangeta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ssssssssssssss",  $this->nomeAllenatore, $this->codiceProfilo, $this->master, $this->idRango, $this->strenght, $this->dexterity, $this->vitality, $this->insight, $this->tought, $this->beauty, $this->cool, $this->cute, $this->clever, $this->idRangeEta);

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
        $query = "SELECT * FROM allenatori";

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

    public static function getAllenatoreById($connection, $id)
    {
        $sql = "SELECT allenatori.*, range_eta.nome AS rangeEtaNome, range_eta.attributi AS rangeEtaAttributi, range_eta.attributiSociali AS rangeEtaAttributiSociali, ranghi.nome AS rangoNome, ranghi.attributi AS rangoAttributi, ranghi.attributiSociali AS rangoAttributiSociali, puntiConoscenza, limiteLivelloConoscenza, limitePokemon FROM allenatori  INNER JOIN ranghi ON allenatori.idRango = ranghi.id INNER JOIN range_eta ON allenatori.idRangeEta = range_eta.id WHERE allenatori.id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $categorie = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $categorie;
        }

        return null;
    }

    /**
     * Get all companies from the database
     * @param mysqli $connection The database connection object 
     * @return mysqli_result Returns the result set of the query
     */
    public static function getTeamPokemon($connection, $idAllenatore)
    {
        // Query to select all records from the company table
        $query = "SELECT * FROM pokemon_allenatori where idAllenatore = " . $idAllenatore . "";

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


    public static function catchPokemon($connection, $allenatoreId, $pokemonId)
    {
        $numeroPokedex = "#" . str_pad((string)$pokemonId, 3, '0', STR_PAD_LEFT);

        // Query to select all records from the company table
        $query = "SELECT * FROM pokemon where numeroPokedex = '" . $numeroPokedex . "'";

        // Execute the query and return an array of rows
        $result = $connection->query($query);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO pokemon_allenatori  (idAllenatore, idPokemon, idRango, strenght, dexterity, vitality, special, insight, tought, beauty, cool, cute, clever) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Creiamo una variabile per il valore fisso 1
        $default_stat = 1;

        // Bind the parameters to the SQL statement
        // NOTA: "iiiiiiiiiiii" contiene esattamente 12 "i" per 12 variabili
        $stmt->bind_param(
            "iiiiiiiiiiiii",
            $allenatoreId,
            $rows[0]['id'],
            $rows[0]['idRango'],
            $rows[0]['baseStrenght'],
            $rows[0]['baseDexterity'],
            $rows[0]['baseVitality'],
            $rows[0]['baseSpecial'],
            $rows[0]['baseInsight'], // Ho rimosso baseSpecial che avanzava rispetto ai campi della query
            $default_stat,           // tought
            $default_stat,           // beauty
            $default_stat,           // cool
            $default_stat,           // cute
            $default_stat            // clever
        );

        // Execute the statement and check for success
        if ($stmt->execute()) {

            // return true if the data is saved successfully
            return true;
        } else {

            // return false if there was an error saving the data
            return false;
        }
    }
}
