

<?php

require_once __DIR__ . "/lingue.php";


// Class Nature
// This class is used to manage the pokemon nature data and perform operations related to it.
class Nature
{
    public $nome;
    public $descrizione;
    public $idLingue = [];
    public $confidenza;
    public $nomi = [];





    // Constructor to initialize the company object with the provided data
    public function __construct($nome, $descrizione, $idLingue, $confidenza, $nomi)
    {

        $this->nome = htmlspecialchars($nome);
        $this->descrizione = htmlspecialchars($descrizione);
        $this->idLingue = $idLingue;
        $this->confidenza = htmlspecialchars($confidenza);
        $this->nomi = $nomi;
    }



    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO nature (nome, descrizione, confidenza) VALUES (?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("ssi", $this->nome, $this->descrizione, $this->confidenza);

        // Execute the statement and check for success
        if ($stmt->execute()) {

            foreach ($this->idLingue as $index => $idLingua) {
                // Prepare the SQL statement to insert the company data into the database
                $stmt = $connection->prepare("INSERT INTO nature_testi (idNatura, idLingua, nome) VALUES (?, ?, ?)");

                // Bind the parameters to the SQL statement
                $stmt->bind_param("ssi", $idNatura, $idLingua, $this->nomi[$index]);

                if ($stmt->execute()) {
                    return true;
                } else {
                    return false;
                }
            }
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

        // Costruisce l'ordine dei linguaggi (es. id 1,2,3) e lo usa dentro GROUP_CONCAT
        $lingue = Lingue::all($connection);
        if ($lingue && count($lingue) > 0) {
            $ids = array_map(function ($l) {
                return intval($l['id']);
            }, $lingue);
            $fieldList = implode(',', $ids);
            $orderInside = " ORDER BY FIELD(nature_testi.idLingua, $fieldList)";
        } else {
            $orderInside = " ORDER BY nature_testi.idLingua ASC";
        }

        // Query to select all records from the company table
        $query = "SELECT *,  GROUP_CONCAT(DISTINCT nature_testi.nome" . $orderInside . " SEPARATOR '/') AS nomi FROM nature INNER JOIN nature_testi ON nature.id = nature_testi.idNatura GROUP BY nature.id";

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

    public static function getNatureById($connection, $id)
    {
        $sql = "SELECT *, GROUP_CONCAT(nature_testi.nome, ',') as nomi FROM nature INNER JOIN nature_testi ON nature.id = nature_testi.idNatura GROUP BY nature.id WHERE nature.id = ?";

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
