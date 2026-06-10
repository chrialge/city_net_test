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
        $query = "SELECT pokemon.*, ranghi.nome as rangoNome, ranghi.descrizione as rangoDescrizione, pokemon_allenatori.*  FROM pokemon_allenatori INNER JOIN ranghi ON pokemon_allenatori.idRango = ranghi.id INNER JOIN pokemon ON pokemon_allenatori.idPokemon = pokemon.id where idAllenatore = " . $idAllenatore . "";

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


        // 1. Prepariamo la query (16 parametri in totale)
        $stmt = $connection->prepare("INSERT INTO pokemon_allenatori (idAllenatore, idPokemon, idRango, strenght, dexterity, vitality, special, insight, tought, beauty, cool, cute, clever, felicita, lealta, idNatura) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // 2. Creiamo le variabili per i valori fissi (obbligatorio per il pass-by-reference)
        $default_stat = 1;
        $default_zero = 0;
        $default_natura = 1; // Sostituisci con l'ID natura corretto o la variabile corretta

        // 3. Eseguiamo il bind con 16 "i" e 16 variabili reali
        $stmt->bind_param(
            "iiiiiiiiiiiiiiii",
            $allenatoreId,          // 1
            $rows[0]['id'],         // 2
            $rows[0]['idRango'],    // 3
            $rows[0]['baseStrenght'], // 4
            $rows[0]['baseDexterity'], // 5
            $rows[0]['baseVitality'], // 6
            $rows[0]['baseSpecial'], // 7
            $rows[0]['baseInsight'], // 8
            $default_stat,           // 9  (tought)
            $default_stat,           // 10 (beauty)
            $default_stat,           // 11 (cool)
            $default_stat,           // 12 (cute)
            $default_stat,           // 13 (clever)
            $default_stat,           // 14 (felicita)
            $default_zero,           // 15 (lealta) -> Ora è una variabile!
            $default_natura          // 16 (idNatura) -> Aggiunto perché mancava
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

    public static function removePokemonTeam($connection, $allenatoreId, $pokemonId)
    {
        // Query to select all records from the company table
        $query = "DELETE FROM pokemon_allenatori where idPokemon = $pokemonId AND idAllenatore = $allenatoreId";

        // Execute the query and return an array of rows
        $result = $connection->query($query);

        return $result;
    }


    public static function checkPokemonGiaEsistenteTeam($connection, $allenatoreId, $pokemonId)
    {
        // Query to select all records from the company table
        $query = "SELECT id FROM pokemon_allenatori where idPokemon = $pokemonId AND idAllenatore = $allenatoreId";

        // Execute the query and return an array of rows
        $result = $connection->query($query);

        $result = $connection->query($query);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }

        return $result;
    }


    public static function checkPokemonTeam($connection, $allenatoreId, $limite)
    {
        $query = "SELECT COUNT(id) AS numeroPokemon FROM pokemon_allenatori where idAllenatore = $allenatoreId";

        // Execute the query and return an array of rows
        $result = $connection->query($query);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }


        if ($limite > $rows[0]['numeroPokemon']) {
            return true;
        } else {
            return false;
        }
    }
}
