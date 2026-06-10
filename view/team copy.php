<?php

require_once __DIR__ . '/../assets/controller/allenatore.php';
require_once __DIR__ . "/../assets/helper/function.php";
require_once __DIR__ . '/../assets/controller/pokemon.php';
require_once __DIR__ . '/../assets/helper/db.php';


session_start();



$allenatoreId = isset($_SESSION['allenatoreId']) ?? 0;
$pokemonID = $_GET['pokemonId'] ?? 0;

if (isset($_SESSION['allenatoreId'])) {
  if (isset($_GET['pokemonId']) > 0 && isset($_SESSION['allenatoreId']) > 0) {


    catchPokemon($allenatoreId, $pokemonID);
  } elseif (isset($_SESSION['pokemonId']) > 0 && isset($_SESSION['allenatoreId']) > 0) {
    catchPokemon($allenatoreId, $_SESSION['pokemonId']);
  } else {

    unset($_SESSION['pokemonId']);
  }
}

$pokemonNew = getPokemonDetailFromPokeAPI(1);



if ($allenatoreId > 0) {
  $allenatore = getAllenatoreId($allenatoreId);

  $teamPokemon = getTeamPokemon($allenatoreId);

  echo "<pre>";
  print_r($teamPokemon);
  echo "</pre>";
}


?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PokéRole Dex - Team</title>

  <?php include __DIR__ . "/../assets/partials/head.php"; ?>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f6fa;
      color: #1f2937;
    }

    .app {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
      min-height: 100vh;
      min-height: 100dvh;
      background: #ffffff;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 10;
      background: #ef4444;
      color: white;
      padding: 14px 16px 16px;
      border-bottom-left-radius: 22px;
      border-bottom-right-radius: 22px;
      box-shadow: 0 4px 20px rgba(239, 68, 68, 0.25);
    }

    .topbar h1 {
      margin: 0 0 6px;
      font-size: clamp(1.25rem, 4vw, 1.75rem);
    }

    .topbar-subtitle {
      margin: 0;
      font-size: 13px;
      line-height: 1.35;
      opacity: 0.9;
    }

    .main-scroll {
      flex: 1;
      padding: 12px 12px calc(16px + env(safe-area-inset-bottom, 0));
    }

    .screen {
      display: none;
    }

    .screen.active {
      display: block;
    }

    .login-card,
    .profile-card,
    .team-card,
    .empty-team {
      border-radius: 20px;
      background: #f9fafb;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
    }

    .login-card {
      padding: 18px;
      margin-top: 10px;
    }

    .login-title {
      margin: 0 0 6px;
      font-size: 21px;
      color: #111827;
    }

    .login-text {
      margin: 0 0 18px;
      font-size: 13px;
      line-height: 1.45;
      color: #6b7280;
    }

    .form-group {
      margin-bottom: 12px;
    }

    .form-label {
      display: block;
      margin-bottom: 6px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #ef4444;
    }

    .form-control {
      width: 100%;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 12px 14px;
      font-size: 16px;
      color: #111827;
      outline: none;
      background: white;
      -webkit-appearance: none;
    }

    .form-control:focus {
      border-color: #ef4444;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
    }

    .primary-btn,
    .ghost-btn,
    .danger-btn {
      width: 100%;
      border: none;
      border-radius: 14px;
      padding: 12px 14px;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.1s, opacity 0.15s, background 0.15s;
    }

    .primary-btn:hover,
    .ghost-btn:hover,
    .danger-btn:hover {
      transform: translateY(-1px);
    }

    .primary-btn {
      margin-top: 4px;
      background: #ef4444;
      color: white;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }

    .ghost-btn {
      background: white;
      color: #ef4444;
      border: 1px solid #fee2e2;
    }

    .danger-btn {
      width: auto;
      padding: 9px 12px;
      font-size: 13px;
      background: #fee2e2;
      color: #dc2626;
    }

    .hint-box {
      margin-top: 14px;
      border-radius: 14px;
      padding: 10px 12px;
      background: white;
      border: 1px solid #e5e7eb;
      font-size: 12px;
      line-height: 1.4;
      color: #6b7280;
    }

    .profile-card {
      padding: 14px;
      margin-bottom: 12px;
    }

    .profile-header {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .avatar {
      flex: 0 0 auto;
      width: 56px;
      height: 56px;
      border-radius: 999px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ef4444;
      color: white;
      font-size: 22px;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.24);
    }

    .profile-info {
      min-width: 0;
      flex: 1;
    }

    .profile-name {
      margin: 0;
      font-size: 18px;
      color: #111827;
      line-height: 1.2;
    }

    .profile-role {
      margin: 4px 0 0;
      font-size: 13px;
      color: #6b7280;
    }

    .profile-actions {
      margin-top: 12px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
      margin-top: 12px;
    }

    .stat-box {
      border-radius: 14px;
      padding: 10px 8px;
      background: white;
      border: 1px solid #e5e7eb;
      text-align: center;
    }

    .stat-value {
      display: block;
      font-size: 18px;
      font-weight: bold;
      color: #ef4444;
    }

    .stat-label {
      display: block;
      margin-top: 2px;
      font-size: 11px;
      color: #6b7280;
    }

    .section-title-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin: 16px 2px 10px;
    }

    .section-title {
      margin: 0;
      font-size: 17px;
      color: #111827;
    }

    .team-counter {
      flex: 0 0 auto;
      font-size: 12px;
      font-weight: bold;
      color: #ef4444;
      background: #fee2e2;
      border-radius: 999px;
      padding: 6px 10px;
    }

    .team-list {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    .team-card {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px;
      min-width: 0;
      transition: transform 0.15s, box-shadow 0.15s;
    }

    .team-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .pokemon-sprite {
      flex: 0 0 auto;
      width: 58px;
      height: 58px;
      border-radius: 18px;
      background: white;
      border: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
    }

    .pokemon-info {
      min-width: 0;
      flex: 1;
    }

    .pokemon-name-row {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
      margin-bottom: 5px;
    }

    .pokemon-name {
      margin: 0;
      font-size: 16px;
      color: #111827;
      line-height: 1.2;
    }

    .pokemon-number {
      font-size: 12px;
      color: #9ca3af;
      font-weight: bold;
    }

    .pokemon-meta {
      margin: 0;
      font-size: 12px;
      color: #6b7280;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .type-tags {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
      margin-top: 7px;
    }

    .type-tag {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 4px 8px;
      font-size: 11px;
      font-weight: bold;
      color: #ef4444;
      background: #fee2e2;
    }

    .remove-btn {
      flex: 0 0 auto;
      width: 36px;
      height: 36px;
      border: none;
      border-radius: 999px;
      background: #fee2e2;
      color: #dc2626;
      font-size: 20px;
      line-height: 1;
      font-weight: bold;
      cursor: pointer;
    }

    .empty-team {
      display: none;
      text-align: center;
      padding: 32px 18px;
      color: #6b7280;
      font-size: 14px;
      line-height: 1.45;
    }

    .empty-team.visible {
      display: block;
    }

    .bottom-nav {
      position: sticky;
      bottom: 0;
      z-index: 10;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      background: white;
      border-top: 1px solid #e5e7eb;
      padding-bottom: env(safe-area-inset-bottom, 0);
    }

    .bottom-nav a {
      padding: 14px 6px;
      text-align: center;
      text-decoration: none;
      color: #6b7280;
      font-size: 13px;
    }

    .bottom-nav a.active {
      color: #ef4444;
      font-weight: bold;
    }

    .ability-modal {
      position: fixed;
      inset: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(15, 23, 42, 0.6);
      opacity: 0;
      visibility: hidden;
    }

    .ability-modal.is-open {
      opacity: 1;
      visibility: visible;
    }

    .ability-modal-dialog {
      background: white;
      padding: 20px;
      border-radius: 18px;
      width: 90%;
      max-width: 400px;
    }

    .ability-modal-close {
      float: right;
      cursor: pointer;
      border: none;
      background: none;
      font-size: 20px;
    }



    @media (min-width: 380px) {
      .main-scroll {
        padding: 14px 14px calc(18px + env(safe-area-inset-bottom, 0));
      }

      .pokemon-sprite {
        width: 64px;
        height: 64px;
      }
    }

    @media (min-width: 600px) {
      .app {
        max-width: 720px;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.06);
      }

      .topbar {
        padding: 18px 24px 20px;
        border-bottom-left-radius: 28px;
        border-bottom-right-radius: 28px;
      }

      .main-scroll {
        padding: 20px 24px calc(24px + env(safe-area-inset-bottom, 0));
      }

      .login-card,
      .profile-card {
        padding: 20px;
      }

      .team-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }

      .team-card {
        align-items: flex-start;
      }
    }

    @media (min-width: 1024px) {
      body {
        padding: 24px 16px;
      }

      .app {
        max-width: 1100px;
        border-radius: 24px;
        overflow: hidden;
        min-height: calc(100vh - 48px);
      }

      .main-scroll {
        padding: 24px 28px 28px;
      }

      .team-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
      }

      .bottom-nav {
        max-width: 1100px;
        margin: 0 auto;
        border-radius: 0 0 24px 24px;
      }
    }

    .alert {
      padding: 20px;
      position: relative;
      border: 2px solid;
      border-radius: 10px;
    }

    .alert-error {
      cursor: pointer;
      background-color: #ef444475;
      border-color: #ef4444;
    }

    .alert-success {
      cursor: pointer;
      background-color: #228b227b;
      border-color: #228b22;
    }

    .close_btn {
      position: absolute;
      top: 20px;
      right: 20px;
    }
  </style>
</head>

<body>

  <main class="app">

    <section class="topbar">
      <h1>Pokèrole - Team</h1>
      <p class="topbar-subtitle">Accedi al tuo profilo, consulta la squadra e rimuovi i Pokémon che non vuoi più nel team.</p>
    </section>

    <div class="main-scroll">



      <?php if (isset($_SESSION['loginResult']) && $_SESSION['loginResult'] == false): ?>
        <div class="alert alert-error" id="alert_error">
          <h3>Messaggio di errore</h3>
          <div class="close_btn">
            <i class="fa-close fa-solid"></i>
          </div>

          <p><?= $_SESSION['message'] ?></p>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['loginResult']) && $_SESSION['loginResult'] == true): ?>
        <div class="alert alert-success" id="alert_success">
          <h3>Messaggio di successo</h3>
          <div class="close_btn">
            <i class="fa-close fa-solid"></i>
          </div>

          <p><?= $_SESSION['message'] ?></p>
        </div>
      <?php endif; ?>



      <section class="screen <?= $allenatoreId != 0 ? "" : "active" ?>" id="loginScreen" aria-label="Accesso profilo">
        <article class="login-card">
          <h2 class="login-title">Accedi al profilo</h2>
          <p class="login-text">Inserisci il nome allenatore per visualizzare il tuo team. Questa versione è pronta per essere collegata a PHP/MySQL.</p>

          <form action="../assets/controller/allenatore.php" id="loginForm" method="POST">
            <?php if ($pokemonID > 0) : ?>
              <input type="hidden" name="pokemonId" value="<?= $pokemonID ?>">
            <?php endif; ?>
            <div class="form-group">
              <label class="form-label" for="nomeAllenatore">Nome allenatore</label>
              <input class="form-control" type="text" id="trainerName" name="nomeAllenatore" placeholder="Es. Ash" autocomplete="name" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="codiceProfilo">Codice profilo</label>
              <input class="form-control" type="password" id="trainerCode" name="codiceProfilo" placeholder="Codice o password" autocomplete="current-password">
            </div>

            <button class="primary-btn" type="submit">Entra nel team</button>
          </form>

          <div class="hint-box">
            Per ora il login è dimostrativo: dopo l'accesso vengono mostrati Pokémon di esempio. Nel database potrai sostituirli con quelli collegati all'utente.
          </div>
        </article>
      </section>

      <section class="screen <?= $allenatoreId > 0 ? "active" : "" ?>" id="profileScreen" aria-label="Profilo e team">
        <article class="profile-card">
          <div class="profile-header">
            <div class="avatar" id="trainerAvatar"><?= strtoupper($allenatore['nomeAllenatore'][0]) ?></div>
            <div class="profile-info">
              <h2 class="profile-name" id="profileName"><?= $allenatore['nomeAllenatore'] ?></h2>
              <div class="profile-badges" style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;">
                <span class="type-tag" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                  Rango: <?= $allenatore['rangoNome'] ?>
                </span>
                <span class="type-tag" style="background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff;">
                  Età: <?= $allenatore['rangeEtaNome'] ?>
                </span>
              </div>
            </div>
          </div>


          <div class="stats-row">
            <div class="stat-box">
              <span class="stat-value" id="teamCount"><?= count($teamPokemon) ?></span>
              <span class="stat-label">Team</span>
            </div>
            <div class="stat-box">
              <span class="stat-value"><?= $allenatore['limitePokemon'] ?></span>
              <span class="stat-label">Max slot</span>
            </div>
            <div class="stat-box">
              <span class="stat-value" id="freeSlots"><?= ($allenatore['limitePokemon'] - count($teamPokemon)) ?></span>
              <span class="stat-label">Liberi</span>
            </div>
          </div>

          <div class="profile-actions">

            <form action="../assets/controller/logout.php">
              <button class="ghost-btn" type="submit" id="logoutBtn">Esci</button>
            </form>

          </div>
        </article>

        <div class="section-title-row">
          <h2 class="section-title">Pokémon nel team</h2>
          <span class="team-counter" id="teamCounter"><?= count($teamPokemon) ?> / <?= $allenatore['limitePokemon'] ?></span>
        </div>

        <section class="team-list" id="teamList">
          <?php foreach ($teamPokemon as $pokemon) : ?>
            <article class="team-card" data-id="25">
              <div class="pokemon-sprite" aria-hidden="true">
                <img src="<?= $pokemon['img'] ?>" alt="immagine di <?= $pokemon['nome'] ?>">
              </div>
              <div class="pokemon-info">
                <div class="pokemon-name-row">
                  <h3 class="pokemon-name"><?= $pokemon['nome'] ?></h3>
                  <span class="pokemon-number"><?= $pokemon['numeroPokedex'] ?></span>
                </div>
                <p class="pokemon-meta"><?= $pokemon['rangoNome'] ?></p>
                <div class="type-tags">
                  <?php foreach ($pokemon['tipologie'] as $tipologia): ?>
                    <span class="type-tag" style="background-color: <?= $tipologia['colorePrincipale'] ?>; color: <?= $tipologia['coloreTesto'] ?>"><?= $tipologia['nome'] ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
              <button class="remove-btn remove-pokemon" type="button" data-pokemon="<?= $pokemon['id'] ?>" aria-label="Rimuovi <?= $pokemon['nome'] ?> dal team">×</button>
            </article>
          <?php endforeach; ?>

        </section>

        <div class="empty-team" id="emptyTeam">
          Il tuo team è vuoto. Puoi aggiungere Pokémon dal Pokédex oppure collegare questa pagina alla tabella del database.
        </div>
      </section>

    </div>

    <nav class="bottom-nav">
      <a href="../pokedex.php">Dex</a>
      <a href="nature.php">Nature</a>
      <a href="team.php" class="active">Team</a>
    </nav>

  </main>

  <div class="remove-pokemon-modal" id="removePokemonModal" role="dialog" aria-modal="true" hidden>
    <div class="remove-pokemon-modal-dialog">
      <button type="button" class="remove-pokemon-modal-close" id="removePokemonModalClose">&times;</button>
      <h2 id="removePokemonModalTitle"></h2>
      <p id="removePokemonModalSubtitle" style="color:#64748b; font-size:14px; margin:4px 0 12px;"></p>
      <div id="removePokemonDescContainer">
        <p id="removePokemonModalDescription"></p>
      </div>
      <div id="removePokemonModalBtn" style="margin-top:10px; padding-top:10px; border-top:1px solid #e2e8f0;">

      </div>
    </div>
  </div>

  <script>
    (function() {
      var loginScreen = document.getElementById('loginScreen');
      var profileScreen = document.getElementById('profileScreen');
      var loginForm = document.getElementById('loginForm');
      var trainerName = document.getElementById('trainerName');
      var profileName = document.getElementById('profileName');
      var trainerAvatar = document.getElementById('trainerAvatar');
      var teamList = document.getElementById('teamList');
      var teamCount = document.getElementById('teamCount');
      var freeSlots = document.getElementById('freeSlots');
      var teamCounter = document.getElementById('teamCounter');
      var emptyTeam = document.getElementById('emptyTeam');
      var logoutBtn = document.getElementById('logoutBtn');
      var addDemoBtn = document.getElementById('addDemoBtn');
      var alertError = document.getElementById("alert_error");
      var alertSuccess = document.getElementById("alert_success");
      var maxTeamSize = 6;

      var demoPokemon = [{
          id: 7,
          icon: '💧',
          name: 'Squirtle',
          number: '#007',
          meta: 'Lv. 9 · Maschio',
          types: ['Acqua']
        },
        {
          id: 39,
          icon: '🎵',
          name: 'Jigglypuff',
          number: '#039',
          meta: 'Lv. 8 · Femmina',
          types: ['Normale', 'Folletto']
        },
        {
          id: 133,
          icon: '⭐',
          name: 'Eevee',
          number: '#133',
          meta: 'Lv. 13 · Femmina',
          types: ['Normale']
        }
      ];

      alertError.addEventListener('click', function() {
        <?php unset($_SESSION['loginResult']); ?>
        alertError.style.display = "none";
      })

      alertSuccess.addEventListener('click', function() {
        <?php unset($_SESSION['loginResult']); ?>
        alertSuccess.style.display = "none";
      })


      function updateTeamStats() {
        var cards = teamList.querySelectorAll('.team-card');
        var total = cards.length;

        teamCount.textContent = total;
        freeSlots.textContent = Math.max(maxTeamSize - total, 0);
        teamCounter.textContent = total + ' / ' + maxTeamSize;
        emptyTeam.classList.toggle('visible', total === 0);
      }

      function bindRemoveButton(card) {
        var button = card.querySelector('.remove-btn');
        if (!button) return;

        button.addEventListener('click', function() {
          card.remove();
          updateTeamStats();
        });
      }

      function createPokemonCard(pokemon) {
        var card = document.createElement('article');
        card.className = 'team-card';
        card.setAttribute('data-id', pokemon.id);

        var tags = pokemon.types.map(function(type) {
          return '<span class="type-tag">' + type + '</span>';
        }).join('');

        card.innerHTML =
          '<div class="pokemon-sprite" aria-hidden="true">' + pokemon.icon + '</div>' +
          '<div class="pokemon-info">' +
          '<div class="pokemon-name-row">' +
          '<h3 class="pokemon-name">' + pokemon.name + '</h3>' +
          '<span class="pokemon-number">' + pokemon.number + '</span>' +
          '</div>' +
          '<p class="pokemon-meta">' + pokemon.meta + '</p>' +
          '<div class="type-tags">' + tags + '</div>' +
          '</div>' +
          '<button class="remove-btn" type="button" aria-label="Rimuovi ' + pokemon.name + ' dal team">×</button>';

        bindRemoveButton(card);
        return card;
      }


      logoutBtn.addEventListener('click', function() {
        profileScreen.classList.remove('active');
        loginScreen.classList.add('active');
        trainerName.focus();
      });

      addDemoBtn.addEventListener('click', function() {
        var total = teamList.querySelectorAll('.team-card').length;
        if (total >= maxTeamSize) return;

        var pokemon = demoPokemon[total % demoPokemon.length];
        teamList.appendChild(createPokemonCard(pokemon));
        updateTeamStats();
      });

      teamList.querySelectorAll('.team-card').forEach(bindRemoveButton);
      updateTeamStats();

      const removePokemonModal = document.getElementById('removePokemonModal');
      const removePokemonModalTitle = document.getElementById('removePokemonModalTitle');
      const removePokemonModalSubtitle = document.getElementById('removePokemonModalSubtitle');
      const removePokemonModalDescription = document.getElementById('removePokemonModalDescription');
      const removePokemonModalEffect = document.getElementById('removePokemonModalBtn');
      const removePokemonModalClose = document.getElementById('removePokemonModalClose');


      function openRemovePokemonModal(nome) {
        console.log(nome);
        document.getElementById('abilityModalDescContainer').hidden = false;
        document.getElementById('abilityModalEffectContainer').hidden = false;
        removePokemonModal.classList.add('is-open');
      }

      function openWarningModal(msg) {
        document.getElementById('abilityModalDescContainer').hidden = false;
        document.getElementById('abilityModalEffectContainer').hidden = true;
        removePokemonModal.classList.add('is-open');
      }

      function closeRemovePokemonModal() {
        removePokemonModal?.classList.remove('is-open');
      }
      document.querySelectorAll('.ability-info-btn').forEach(b => b.addEventListener('click', () => openRemovePokemonModal(b.dataset.pokemon)));
      removePOkemonModalClose?.addEventListener('click', closeRemovePokemonModal);
      removePokemonModal?.addEventListener('click', (e) => {
        if (e.target === removePokemonModal) closeRemovePokemonModal();
      });
    })();
  </script>

</body>

</html>