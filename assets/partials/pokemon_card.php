                <?php foreach ($arrayPokemon as $pokemon) : ?>
                    <?php
                    $tipologie = array_map('trim', explode(',', $pokemon['tipologiaNome']));
                    $colori = array_map('trim', explode(',', $pokemon['colorePrincipale']));
                    ?>
                    <?php $pokemonId = intval(ltrim($pokemon['numeroPokedex'], '#')); ?>
                    <a href="view/pokemon-dettaglio.php?id=<?= $pokemonId ?>" class="pokemon-card" data-name="<?= $pokemon['nome'] ?>" data-types="<?= $pokemon['tipologiaNome'] ?>" data-number="<?= $pokemon['numeroPokedex'] ?>" data-generation="<?= $pokemon['generazione'] ?>">
                        <div class="card-image-wrap">
                            <img src="<?= $pokemon['img'] ?>" alt="<?= $pokemon['nome'] ?>" loading="lazy">
                        </div>
                        <div class="card-body">
                            <p class="pokemon-title">
                                <span class="pokemon-number"><?= $pokemon['numeroPokedex'] ?></span>
                                <span class="pokemon-name"><?= $pokemon['nome'] ?></span>
                            </p>
                            <div class="type-list">
                                <?php foreach ($tipologie as $idx => $tipologia) : ?>
                                    <span class="type" style="background-color: <?= $colori[$idx] ?? '#6b7280' ?>;"><?= trim($tipologia) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>