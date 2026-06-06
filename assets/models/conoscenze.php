<?php

require_once __DIR__ . '/../models/categorie_conoscenze.php';
require_once __DIR__ . '/../models/lingue.php';


// Class PokemonTipologie
// This class is used to manage the pokemon tipologie data and perform operations related to it.
class Conoscenze
{
    public $idCategoriaConoscenza;
    public $nomeLingua1;
    public $descrizione;
    public $visibileAllenatore;
    public $visibilePokemon;
    public $idLingua;
    public $nomeLingua2;






    // Constructor to initialize the company object with the provided data
    public function __construct($idCategoriaConoscenza, $nomeLingua1, $descrizione, $visibileAllenatore, $visibilePokemon, $idLingua, $nomeLingua2)
    {

        $this->idCategoriaConoscenza = htmlspecialchars($idCategoriaConoscenza);
        $this->nomeLingua1 = htmlspecialchars($nomeLingua1);
        $this->descrizione = htmlspecialchars($descrizione);
        $this->visibileAllenatore = htmlspecialchars($visibileAllenatore);
        $this->visibilePokemon = htmlspecialchars($visibilePokemon);
        $this->idLingua = htmlspecialchars($idLingua);
        $this->nomeLingua2 = htmlspecialchars($nomeLingua2);
    }


    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO conoscenze (idCategoriaConoscenza, nome, descrizione, visibileAllenatore, visibilePokemon) VALUES (?,?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("sssss",  $this->idCategoriaConoscenza, $this->nomeLingua1, $this->descrizione, $this->visibileAllenatore, $this->visibilePokemon);

        // Execute the statement and check for success
        if ($stmt->execute()) {

            $query = "SELECT id FROM conoscenze WHERE idCategoriaConoscenza = ? AND nome = ? AND descrizione = ? AND visibileAllenatore = ? AND visibilePokemon = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sssss",  $this->idCategoriaConoscenza, $this->nomeLingua1, $this->descrizione, $this->visibileAllenatore, $this->visibilePokemon);
            $stmt->execute();
            $result = $stmt->get_result();
            $idConoscenza = $result->fetch_assoc()['id'];
            $lingue = Lingue::all($connection);
            foreach ($lingue as $lingua) {
                $query = "INSERT INTO conoscenze_lingua (idConoscenza, idLingua, nome) VALUES (?,?, ?)";
                $stmt = $connection->prepare($query);
                if ($lingua['id'] == $this->idLingua) {
                    $stmt->bind_param("sss",  $idConoscenza, $lingua['id'], $this->nomeLingua2);
                } else {
                    $stmt->bind_param("sss",  $idConoscenza, $lingua['id'], $this->nomeLingua1);
                }
                $stmt->execute();
            }





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
        $query = "SELECT conoscenze.*, GROUP_CONCAT(DISTINCT conoscenze_testi.nome SEPARATOR ', ') AS nomi, conoscenze.nome AS categoriaNome FROM conoscenze INNER JOIN conoscenze_testi ON conoscenze.id = conoscenze_testi.idConoscenza INNER JOIN lingue ON conoscenze_testi.idLingua = lingue.id GROUP BY conoscenze.id";

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

    public static function getConoscenzeById($connection, $id)
    {
        $sql = "SELECT * FROM conoscenze WHERE id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $conoscenza = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $conoscenza;
        }

        return null;
    }


    public static function getConoscenzeByArrayId($connection, $ids)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM conoscenze WHERE id IN ($placeholders)";

        if ($statement = $connection->prepare($sql)) {
            $types = str_repeat('i', count($ids));
            $statement->bind_param($types, ...$ids);
            $statement->execute();
            $result = $statement->get_result();
            $conoscenze = [];
            while ($row = $result->fetch_assoc()) {
                $conoscenze[] = $row;
            }
            $statement->close();
            return $conoscenze;
        }

        return null;
    }
}
