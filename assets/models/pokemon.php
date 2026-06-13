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
    public $generazione;




    // Constructor to initialize the company object with the provided data
    public function __construct($numeroPokedex, $nome, $descrizione, $baseStrenght, $massimoStrenght, $baseDexterity, $massimoDexterity, $baseVitality, $massimoVitality, $baseSpecial, $massimoSpecial, $baseInsight, $massimoInsight, $hp, $idRango, $generazione)
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
        $this->generazione = htmlspecialchars($generazione);
    }


    /**
     * * Save the company data to the database
     * @param mysqli $connection The database connection object 
     * @return bool Returns true if the data is saved successfully, false otherwise
     */
    public function save($connection)
    {


        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO pokemon (numeroPokedex, nome, descrizione, base_streght, massimo_streght, base_dexterity, massimo_dexterity, base_vitality, massimo_vitality, base_special, massimo_special, base_insight, massimo_insight, hp, id_rango, generazione) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters to the SQL statement
        $stmt->bind_param("sssssssssssssss", $this->numeroPokedex, $this->nome, $this->descrizione, $this->baseStrenght, $this->massimoStrenght, $this->baseDexterity, $this->massimoDexterity, $this->baseVitality, $this->massimoVitality, $this->baseSpecial, $this->massimoSpecial, $this->baseInsight, $this->massimoInsight, $this->hp, $this->idRango, $this->generazione);

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
        $query = "SELECT * FROM pokemon INNER JOIN ranghi ON pokemon.idRango = ranghi.id INNER JOIN pokemon_tipologie_pivot ON pokemon.id = pokemon_tipologie_pivot.idPokemon INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id";

        // Execute the query and return the result
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

    public static function getPokemonById($connection, $id)
    {
        $sql = "SELECT pokemon.*, ranghi.nome as rangoNome, ranghi.descrizione as rangoDescrizione, ranghi.attributi, ranghi.attributiSociali, ranghi.puntiConoscenza, ranghi.limiteLivelloConoscenza, ranghi.limitePokemon FROM pokemon LEFT JOIN ranghi ON pokemon.idRango = ranghi.id WHERE pokemon.id = ?";

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

    public static function getPokemonTypesById($connection, $id)
    {
        $sql = "SELECT pokemon_tipologie.* FROM pokemon_tipologie_pivot INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id WHERE pokemon_tipologie_pivot.idPokemon = ? ORDER BY pokemon_tipologie_pivot.ordine ASC";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $types = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $types[] = $row;
                }
            }
            $statement->close();
            return $types;
        }

        return [];
    }

    public static function getPokemonAbilitiesById($connection, $id)
    {
        $possibleColumns = [
            'idPokemonAbilita',
        ];

        foreach ($possibleColumns as $column) {
            $sql = "SELECT pokemon_abilita.* FROM pokemon_abilita_pivot INNER JOIN pokemon_abilita ON pokemon_abilita_pivot.$column = pokemon_abilita.id WHERE pokemon_abilita_pivot.idPokemon = ?";
            $statement = @$connection->prepare($sql);
            if ($statement === false) {
                continue;
            }

            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $abilities = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $abilities[] = $row;
                }
            }
            $statement->close();

            if (!empty($abilities)) {
                return $abilities;
            }
        }

        return [];
    }

    public static function getShortInfoPokemon($connection, $numeroPokedex)
    {
        $sql = "SELECT pokemon.numeroPokedex, pokemon.nome, pokemon.generazione, GROUP_CONCAT(pokemon_tipologie.nome SEPARATOR ', ') as tipologiaNome, GROUP_CONCAT(pokemon_tipologie.colorePrincipale SEPARATOR ', ') as colorePrincipale FROM pokemon INNER JOIN pokemon_tipologie_pivot ON pokemon.id = pokemon_tipologie_pivot.idPokemon INNER JOIN pokemon_tipologie ON pokemon_tipologie_pivot.idTipologiaPokemon = pokemon_tipologie.id WHERE pokemon.numeroPokedex = ? GROUP BY pokemon.id";

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

    public static function getDebolezzeByTipologiaId($connection, $idTipologia)
    {
        $sql = "SELECT pokemon_tipologie.*, debolezze_stati.statoDebolezza 
            FROM debolezze_stati 
            INNER JOIN debolezze_tipologie ON debolezze_tipologie.idDebolezzaStati = debolezze_stati.id 
            INNER JOIN pokemon_tipologie ON pokemon_tipologie.id = debolezze_tipologie.idPokemonTipologia 
            WHERE debolezze_stati.idPokemonTipologia = ?";

        if ($statement = $connection->prepare($sql)) {
            $statement->bind_param('i', $idTipologia); // 'i' perché l'ID è un intero
            $statement->execute();
            $result = $statement->get_result();

            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            $statement->close();
            return $rows;
        }

        return [];
    }
}
