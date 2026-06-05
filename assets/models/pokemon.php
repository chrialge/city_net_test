<?php


// Class Pokemon
// This class is used to manage the company data and perform operations related to it.
class Pokemon
{
    public $numeroPokedex;
    public $nome;
    public $descrizione;
    public $baseStrenght;
    public $massimoStrenght;
    public $baseDexterity;
    public $massimoDexterity;
    public $baseVitality;
    public $massimoVitality;
    public $baseSpecial;
    public $massimoSpecial;
    public $baseInsight;
    public $massimoInsight;
    public $hp;
    public $idRango;




    // Constructor to initialize the company object with the provided data
    public function __construct($numeroPokedex, $nome, $descrizione, $baseStrenght, $massimoStrenght, $baseDexterity, $massimoDexterity, $baseVitality, $massimoVitality, $baseSpecial, $massimoSpecial, $baseInsight, $massimoInsight, $hp, $idRango)
    {
        $this->numeroPokedex = htmlspecialchars($numeroPokedex);
        $this->nome = htmlspecialchars($nome);
        $this->descrizione = htmlspecialchars($descrizione);
        $this->baseStrenght = htmlspecialchars($baseStrenght);
        $this->massimoStrenght = htmlspecialchars($massimoStrenght);
        $this->baseDexterity = htmlspecialchars($baseDexterity);
        $this->massimoDexterity = htmlspecialchars($massimoDexterity);
        $this->baseVitality = htmlspecialchars($baseVitality);
        $this->massimoVitality = htmlspecialchars($massimoVitality);
        $this->baseSpecial = htmlspecialchars($baseSpecial);
        $this->massimoSpecial = htmlspecialchars($massimoSpecial);
        $this->baseInsight = htmlspecialchars($baseInsight);
        $this->massimoInsight = htmlspecialchars($massimoInsight);
        $this->hp = htmlspecialchars($hp);
        $this->idRango = htmlspecialchars($idRango);
    }


    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO pokemon (numeroPokedex, nome, descrizione, base_streght, massimo_streght, base_dexterity, massimo_dexterity, base_vitality, massimo_vitality, base_special, massimo_special, base_insight, massimo_insight, hp, id_rango) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("sssssssssssssss", $this->numeroPokedex, $this->nome, $this->descrizione, $this->baseStrenght, $this->massimoStrenght, $this->baseDexterity, $this->massimoDexterity, $this->baseVitality, $this->massimoVitality, $this->baseSpecial, $this->massimoSpecial, $this->baseInsight, $this->massimoInsight, $this->hp, $this->idRango);

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
        $query = "SELECT * FROM pokemon INNER JOIN rango ON pokemon.id_rango = rango.id_rango INNER JOIN pokemon_tipologie_pivot ON pokemon.id = pokemon_tipologie_pivot.idPokemon INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id";

        // Execute the query and return the result
        return $connection->query($query);
    }

    public static function getPokemonById($connection, $id)
    {
        $sql = "SELECT * FROM pokemon WHERE id = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $pokemon = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $pokemon;
        }

        return null;
    }

    public static function getShortInfoPokemon($connection, $numeroPokedex)
    {
        $sql = "SELECT pokemon.numeroPokedex, pokemon.nome, GROUP_CONCAT(pokemon_tipologie.nome SEPARATOR ', ') as tipologiaNome, GROUP_CONCAT(pokemon_tipologie.colorePrincipale SEPARATOR ', ') as colorePrincipale FROM pokemon INNER JOIN pokemon_tipologie_pivot ON pokemon.id = pokemon_tipologie_pivot.idPokemon INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id WHERE pokemon.numeroPokedex = ? GROUP BY pokemon.id";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('s', $numeroPokedex);
            $statement->execute();
            $result = $statement->get_result();
            $pokemon = $result ? $result->fetch_assoc() : null;
            $statement->close();
            return $pokemon;
        }

        return null;
    }
}
