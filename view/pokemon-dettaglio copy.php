<?php

require_once __DIR__ . '/../assets/controller/pokemon.php';
require_once __DIR__ . '/../assets/controller/conoscenze.php';
require_once __DIR__ . '/../assets/controller/categorie_conoscenze.php';
require_once __DIR__ . '/../assets/controller/ranghi.php';
require_once __DIR__ . '/../assets/helper/function.php';
require_once __DIR__ . '/../assets/helper/db.php';

function render_dots(
  int $level,
  int $max = 5,
  bool $compact = false,
  ?string $variant = null,
  ?string $editableKey = null
): string {
  $level = max(0, min($max, $level));
  $class = 'dots';
  if ($compact) {
    $class .= ' dots--compact';
  }
  if ($max === 5 && $compact) {
    $class .= ' dots--track';
  }
  if ($variant === 'attr') {
    $class .= ' dots--attr';
    if ($max > 8) {
      $class .= ' dots--attr-many';
    }
  } elseif ($variant !== null) {
    $class .= ' dots--social dots--social-' . preg_replace('/[^a-z0-9-]/', '', strtolower($variant));
  }
  $attrs = ' role="img" aria-label="' . $level . ' su ' . $max . '"';
  if ($editableKey !== null) {
    $attrs .= ' data-editable-dots data-key="' . htmlspecialchars($editableKey, ENT_QUOTES, 'UTF-8') . '"'
      . ' data-level="' . $level . '" data-max="' . $max . '"';
  }
  $html = '<div class="' . $class . '"' . $attrs . '>';
  for ($i = 1; $i <= $max; $i++) {
    $html .= '<span class="dot' . ($i <= $level ? ' active' : '') . '" data-dot-index="' . $i . '"></span>';
  }
  return $html . '</div>';
}

$pokemonId = isset($_GET['id']) ? intval($_GET['id']) : null;
$pokemon = $pokemonId ? getPokemonById($pokemonId) : null;

// Connessione DB per recuperare le debolezze/resistenze effettive tramite la logica X1/X2
$connection = DB::connect();
$effettivita = [
  'resistenze'  => [],
  'resistenze2' => [],
  'debolezze'   => [],
  'debolezze2'  => [],
  'immunita'    => []
];

if ($pokemon && !empty($pokemon['tipologie'])) {
  $allRows = [];
  foreach ($pokemon['tipologie'] as $tipologia) {
    // Chiamata al metodo statico del Model per prelevare le relazioni di stato
    $rowsDb = Pokemon::getDebolezzeByTipologiaId($connection, $tipologia['id']);
    if (is_array($rowsDb)) {
      $allRows = array_merge($allRows, $rowsDb);
    }
  }

  $conteggioTipi = [];
  foreach ($allRows as $riga) {
    $idTipoDefensivo = $riga['id'];
    $stato = (int)$riga['statoDebolezza'];

    if (!isset($conteggioTipi[$idTipoDefensivo])) {
      $conteggioTipi[$idTipoDefensivo] = [
        'dati' => $riga,
        'resistenze' => 0,
        'debolezze' => 0,
        'immunita' => 0
      ];
    }

    if ($stato === 1) {
      $conteggioTipi[$idTipoDefensivo]['resistenze']++;
    } else if ($stato === 2) {
      $conteggioTipi[$idTipoDefensivo]['debolezze']++;
    } else if ($stato === 3) {
      $conteggioTipi[$idTipoDefensivo]['immunita']++;
    }
  }

  foreach ($conteggioTipi as $idTipo => $info) {
    $r = $info['resistenze'];
    $d = $info['debolezze'];
    $i = $info['immunita'];
    $dati = $info['dati'];

    if ($i > 0) {
      $effettivita['immunita'][] = $dati;
      continue;
    }

    $bilancio = $d - $r;
    if ($bilancio === 0) {
      continue;
    }

    if ($bilancio > 0) {
      if ($bilancio === 1) {
        $effettivita['debolezze'][] = $dati;
      } else if ($bilancio >= 2) {
        $effettivita['debolezze2'][] = $dati;
      }
    } else if ($bilancio < 0) {
      $valoreAssoluto = abs($bilancio);
      if ($valoreAssoluto === 1) {
        $effettivita['resistenze'][] = $dati;
      } else if ($valoreAssoluto >= 2) {
        $effettivita['resistenze2'][] = $dati;
      }
    }
  }
}

$pokemonEvolution = getPokemonChainEvolution($pokemonId);
$ranghi = getAllRanghi();
echo '<pre >';
print_r($pokemon);
echo '</pre>';
$arrayEvoluzioni = [];
foreach ($pokemonEvolution as $evoluzione) {
  $datiEvoluzione = getPokemonById($evoluzione['id']);
  $data = [
    'evoluzioneMetodoCon' => $datiEvoluzione['evoluzioneMetodoCon'],
    'evoluzioneTempo' => $datiEvoluzione['evoluzioneTempo'],
    'evoluzioneStage' => $datiEvoluzione['evoluzioneStage'],
    'evoluzioneMetodoDa' => $datiEvoluzione['evoluzioneMetodoDa'],
    'img' => $evoluzione['id'],
    'name' => $evoluzione['name'],
    'id' => $evoluzione['id']
  ];
  array_push($arrayEvoluzioni, $data);
}

echo '<pre >';
echo '<pre >';
print_r($arrayEvoluzioni);
echo '</pre>';

$conoscenze = getAllConoscenze();
$categorieConoscenze = getAllCategorieConoscenze();

foreach ($categorieConoscenze as &$categoria) {
  $categoria['conoscenze'] = [];
  foreach ($conoscenze as $conoscenza) {
    if (isset($conoscenza['idCategoriaConoscenza']) && $categoria['id'] == $conoscenza['idCategoriaConoscenza']) {
      $categoria['conoscenze'][] = $conoscenza;
    }
  }
}
unset($categoria);

function safe_color(string $color): string
{
  $color = trim($color);
  if ($color === '') {
    return '';
  }
  if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color)) {
    return $color;
  }
  if (preg_match('/^(rgb|rgba|hsl|hsla)\(/i', $color)) {
    return $color;
  }
  return '';
}

function hex_to_rgb(string $hex): ?array
{
  $hex = ltrim($hex, '#');
  if (strlen($hex) === 3) {
    $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
  }
  if (strlen($hex) !== 6) {
    return null;
  }
  return [
    hexdec(substr($hex, 0, 2)),
    hexdec(substr($hex, 2, 2)),
    hexdec(substr($hex, 4, 2)),
  ];
}

function is_dark_color(string $color): bool
{
  $rgb = hex_to_rgb($color);
  if ($rgb === null) {
    return false;
  }
  [$r, $g, $b] = $rgb;
  $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
  return $luminance < 0.55;
}

function type_colors(array $type): array
{
  $candidates = [
    'colorePrimario',
    'colorePrincipale',
    'coloreSecondario',
    'coloreTerziario',
  ];

  $colors = [];
  foreach ($candidates as $key) {
    if (!isset($type[$key])) {
      continue;
    }
    $color = safe_color((string) $type[$key]);
    if ($color === '') {
      continue;
    }
    if (!in_array($color, $colors, true)) {
      $colors[] = $color;
    }
  }
  return $colors;
}

if (isset($pokemon['tipologie']) && is_array($pokemon['tipologie'])) {
  usort($pokemon['tipologie'], function ($a, $b) {
    $ordA = isset($a['ordine']) ? intval($a['ordine']) : 99;
    $ordB = isset($b['ordine']) ? intval($b['ordine']) : 99;
    return $ordA <=> $ordB;
  });
}

function get_primary_type_color(array $types): string
{
  if (empty($types)) {
    return '#fefce8';
  }
  $typeColors = type_colors($types[0]);
  return !empty($typeColors) ? $typeColors[0] : '#fefce8';
}

function type_palette(array $types): array
{
  if (empty($types)) {
    return [
      'bg' => '#fefce8',
      'bg_deep' => '#fef08a',
      'accent' => '#eab308',
      'accent_dark' => '#a16207',
      'text' => '#422006',
    ];
  }

  $primaryColor = get_primary_type_color($types);
  $isDark = is_dark_color($primaryColor);

  return [
    'bg' => $primaryColor,
    'bg_deep' => $primaryColor,
    'accent' => $primaryColor,
    'accent_dark' => $isDark ? '#ffffff' : '#1f2937',
    'accent_foreground' => $isDark ? '#ffffff' : '#111827',
    'text' => $isDark ? '#ffffff' : '#111827',
  ];
}

function header_style_for_types(array $types): string
{
  $primaryColor = get_primary_type_color($types);
  $textColor = is_dark_color($primaryColor) ? '#ffffff' : '#111827';
  return sprintf('background: linear-gradient(180deg, %s 0%%, rgba(255,255,255,0) 100%%); color: %s;', $primaryColor, $textColor);
}

function badge_style_for_type(array $type): string
{
  $colors = type_colors($type);
  if (count($colors) === 0) {
    return '';
  }
  $background = $colors[0];
  $textColor = is_dark_color($background) ? '#ffffff' : '#111827';
  return sprintf('background: %s; color: %s; border: 1px solid rgba(0,0,0,0.15);', $background, $textColor);
}

$heroStyle = header_style_for_types($pokemon['tipologie']);
$typePalette = type_palette($pokemon['tipologie']);
$bodyStyle = sprintf(
  '--type-bg:%s; --type-bg-deep:%s; --type-accent:%s; --type-accent-dark:%s; --type-accent-foreground:%s; --type-text:%s;',
  $typePalette['bg'],
  $typePalette['bg_deep'],
  $typePalette['accent'],
  $typePalette['accent_dark'],
  $typePalette['accent_foreground'],
  $typePalette['text']
);

$pokemon_skills = [];
foreach ($conoscenze as $conoscenza) {
  $key = strtolower($conoscenza['nome']);
  $pokemon_skills[$key] = 0;
}

$base_hp = intval($pokemon['hp']);

$pokemon_attrs = [
  'strength' => intval($pokemon['baseStrenght']),
  'limiteStrength' => intval($pokemon['massimoStrenght']),
  'dexterity' => intval($pokemon['baseDexterity']),
  'limiteDexterity' => intval($pokemon['massimoDexterity']),
  'vitality' => intval($pokemon['baseVitality']),
  'limiteVitality' => intval($pokemon['massimoVitality']),
  'special' => intval($pokemon['baseSpecial']),
  'limiteSpecial' => intval($pokemon['massimoSpecial']),
  'insight' => intval($pokemon['baseInsight']),
  'limiteInsight' => intval($pokemon['massimoInsight']),
];

$pokemon_social = [
  'tough' => 1,
  'cool' => 1,
  'beauty' => 1,
  'cute' => 1,
  'clever' => 1,
];

$pokemon_ranks = [
  'starter' => 'Starter',
  'beginner' => 'Beginner',
  'amateur' => 'Amateur',
  'ace' => 'Ace',
  'pro' => 'Pro',
  'master' => 'Master',
  'champion' => 'Champion',
];

$pokemon_rank = strtolower($pokemon['rangoNome']);

$sheet_config = [
  'baseHp' => $base_hp,
  'rank' => $pokemon_rank,
  'attrs' => $pokemon_attrs,
  'skills' => $pokemon_skills,
  'social' => $pokemon_social,
];

$max_attributi_cumulati = 0;
$max_sociali_cumulati = 0;
$max_conoscenze_cumulati = 0;
$limite_singola_conoscenza = 1;

$js_rank_config = [];
foreach ($ranghi as $rango) {
  $key = strtolower($rango['nome']);
  $max_attributi_cumulati += $rango['attributi'];
  $max_sociali_cumulati += $rango['attributiSociali'];
  $max_conoscenze_cumulati = $rango['puntiConoscenza'];

  $js_rank_config[$key] = [
    'max_attr' => $max_attributi_cumulati,
    'max_social' => $max_sociali_cumulati,
    'max_skills' => $max_conoscenze_cumulati,
    'skill_level_cap' => $rango['limiteLivelloConoscenza']
  ];
}

$sheet_config['rank_config'] = $js_rank_config;

function pokemon_sprite_url(int $id): string
{
  return 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'
    . $id . '.png';
}

$pokemonApi = getPokemonDetailFromPokeAPI($pokemonId);
$pokemon_species_abilities = $pokemon["abilita"];
$pokemon_dex_min = 1;
$pokemon_dex_max = 1025;

$pokemon_prev_id = $pokemonId > $pokemon_dex_min ? $pokemonId - 1 : null;
$pokemon_next_id = $pokemonId < $pokemon_dex_max ? $pokemonId + 1 : null;

// LOGICA PESO: Trasformazione ettogrammi -> chilogrammi (valore / 10)
$pesoFormattato = '6 kg';
if (isset($pokemon['peso'])) {
  // Isola solo il valore numerico pulito (es. se arriva "70 kg" o solo "70")
  $pesoNumerico = floatval(preg_replace('/[^0-9.]/', '', $pokemon['peso']));

  if ($pesoNumerico > 0) {
    $pesoKg = $pesoNumerico / 10; // Converte ettogrammi in kg

    // Se dividendo otteniamo un intero (es. 70 / 10 = 7.0), rimuoviamo il decimale
    if (floor($pesoKg) == $pesoKg) {
      $pesoFormattato = intval($pesoKg) . ' kg'; // Diventa: 7 kg
    } else {
      $pesoFormattato = $pesoKg . ' kg';         // Diventa: 0.1 kg o 6.5 kg
    }
  }
}

?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dettaglio Pokémon - PokéRole Dex</title>

  <style>
    * {
      box-sizing: border-box;
    }

    :root {
      --type-bg: #fefce8;
      --type-bg-deep: #fef08a;
      --type-accent: #eab308;
      --type-accent-dark: #a16207;
      --type-text: #422006;
      --dot-size-fixed: 16px;
      --dot-gap-fixed: 6px;
      --dots-count-fixed: 5;
      --dots-track-w: calc(var(--dots-count-fixed) * var(--dot-size-fixed) + (var(--dots-count-fixed) - 1) * var(--dot-gap-fixed));
      --dots-track-h: var(--dot-size-fixed);
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f8fafc;
      color: #1e293b;
    }

    .app {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: #ffffff;
    }

    .detail-header {
      position: relative;
      padding: 24px 20px 40px;
      text-align: center;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .header-navigation {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      max-width: 600px;
      margin: 0 auto 16px;
    }

    .nav-arrow-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(0, 0, 0, 0.1);
      color: #334155;
      text-decoration: none;
      font-size: 20px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.2s ease;
    }

    .nav-arrow-link:hover {
      background: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .nav-arrow-link.is-disabled {
      opacity: 0.25;
      pointer-events: none;
    }

    .header-main-info {
      text-align: center;
    }

    .pokemon-number {
      display: inline-block;
      font-size: 14px;
      font-weight: 800;
      background: rgba(0, 0, 0, 0.07);
      color: #334155;
      padding: 4px 10px;
      border-radius: 30px;
      margin: 0 0 8px 0;
      letter-spacing: 0.05em;
    }

    .detail-header h1 {
      margin: 0 0 12px;
      font-size: clamp(2.2rem, 8vw, 3.2rem);
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 1.1;
      color: #0f172a;
      text-transform: capitalize;
    }

    .type-list {
      display: flex;
      gap: 8px;
      justify-content: center;
      margin-bottom: 24px;
    }

    .type {
      font-size: 13px;
      padding: 6px 16px;
      border-radius: 30px;
      font-weight: bold;
      letter-spacing: 0.02em;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .hero-artwork-container {
      position: relative;
      width: min(240px, 65vw);
      height: min(240px, 65vw);
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
      z-index: 2;
      filter: drop-shadow(0 12px 20px rgba(0, 0, 0, 0.15));
    }

    .hero-actions {
      margin-top: 24px;
      display: flex;
      justify-content: center;
    }

    .hero-actions .btn {
      min-width: 200px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .edit-bar {
      grid-column: 1 / -1;
      background: #fffbeb;
      border: 2px dashed #fef08a;
      border-radius: 18px;
      padding: 14px;
    }

    .edit-bar .btn {
      width: 100%;
    }

    .edit-hint {
      margin: 10px 0 0;
      font-size: 13px;
      line-height: 1.45;
      color: #78350f;
      text-align: center;
    }

    body.is-editing .dot[data-dot-index] {
      cursor: pointer;
      -webkit-tap-highlight-color: transparent;
    }

    body.is-editing [data-editable-dots] .dot[data-dot-index]:active {
      transform: scale(1.08);
    }

    .btn.is-active {
      background: var(--type-accent-dark);
      color: #fff;
    }

    .rank-bar {
      padding: 12px 14px 14px;
      background: #f1f5f9;
      border-bottom: 1px solid #e2e8f0;
    }

    .rank-bar-title {
      display: block;
      margin: 0 0 8px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #475569;
    }

    .rank-select {
      width: 100%;
      min-height: 48px;
      padding: 12px 40px 12px 16px;
      font-size: 16px;
      font-weight: bold;
      font-family: inherit;
      color: #1f2937;
      background-color: #ffffff;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23475569' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
    }

    .rank-select:focus {
      outline: none;
      border-color: #94a3b8;
    }

    .main-scroll {
      flex: 1;
      padding: 20px 12px 80px;
    }

    .section-grid {
      display: grid;
      gap: 16px;
    }

    .profile-top {
      grid-column: 1 / -1;
      display: grid;
      gap: 16px;
      align-items: stretch;
    }

    .profile-side {
      display: flex;
      flex-direction: column;
      gap: 16px;
      min-width: 0;
    }

    .profile-desc .notes {
      flex: 1;
    }

    @media (min-width: 640px) {
      .profile-top {
        grid-template-columns: 1fr 1fr;
      }
    }

    .panel {
      background: #f8fafc;
      border-radius: 18px;
      padding: 16px;
      border: 1px solid #e2e8f0;
    }

    .panel-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 14px;
    }

    .panel-title h2 {
      margin: 0;
      font-size: 16px;
      color: #0f172a;
      font-weight: 800;
    }

    .data-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
    }

    .data-item {
      background: white;
      border-radius: 14px;
      padding: 12px;
      min-height: 64px;
      border: 1px solid #e2e8f0;
    }

    .data-item strong {
      display: block;
      margin-bottom: 4px;
      font-size: 11px;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .data-item span {
      font-size: 15px;
      font-weight: bold;
      color: #0f172a;
    }

    .data-item.full {
      grid-column: 1 / -1;
    }

    .dots {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      flex-shrink: 0;
      justify-content: center;
    }

    .dot {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #cbd5e1;
      flex-shrink: 0;
    }

    .dot.active {
      background: #3b82f6;
    }

    body.is-editing [data-editable-dots] .dot[data-dot-index] {
      width: 22px;
      height: 22px;
      margin: 4px;
    }

    .dots--compact {
      gap: var(--dot-gap-fixed);
    }

    .dots--compact .dot {
      width: var(--dot-size-fixed);
      height: var(--dot-size-fixed);
    }

    .skill-item .dots--track {
      flex-direction: row;
      flex-wrap: nowrap;
      width: var(--dots-track-w);
      min-width: var(--dots-track-w);
      max-width: var(--dots-track-w);
      height: var(--dots-track-h);
      justify-content: flex-start;
      align-items: center;
      flex-shrink: 0;
      margin: 0 auto;
    }

    body.is-editing .skill-item .dots--track .dot[data-dot-index] {
      width: var(--dot-size-fixed);
      height: var(--dot-size-fixed);
      margin: 0;
    }

    .skill-item .dots--track .dot {
      position: relative;
    }

    .attrs-layout {
      display: grid;
      gap: 18px;
    }

    .combat-attrs {
      display: grid;
      gap: 8px;
    }

    .combat-attr-card {
      border-radius: 16px;
      padding: 12px 10px 14px;
      text-align: center;
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
    }

    .combat-attr-name {
      display: block;
      margin-bottom: 8px;
      font-size: 12px;
      font-weight: bold;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #334155;
    }

    .combat-attr-card .dots {
      justify-content: center;
    }

    .dots--attr .dot {
      width: 16px;
      height: 16px;
      background: #e2e8f0;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .dots--attr .dot.active {
      background: #475569;
    }

    .social-attrs {
      display: grid;
      gap: 8px;
    }

    .social-card {
      border-radius: 16px;
      padding: 10px 12px 12px;
      text-align: center;
      border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .social-name {
      display: block;
      margin-bottom: 8px;
      font-size: 12px;
      font-weight: bold;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #1f2937;
    }

    .social-card .dots {
      justify-content: center;
      flex-wrap: nowrap;
    }

    .social-card--tough {
      background: #fef3c7;
    }

    .social-card--cool {
      background: #ffedd5;
    }

    .social-card--beauty {
      background: #e0e7ff;
    }

    .social-card--cute {
      background: #fce7f3;
    }

    .social-card--clever {
      background: #dcfce7;
    }

    .dots--social .dot {
      width: var(--dot-size-fixed);
      height: var(--dot-size-fixed);
      background: rgba(255, 255, 255, 0.85);
    }

    .dots--social-tough .dot.active {
      background: #a16207;
    }

    .dots--social-cool .dot.active {
      background: #c2410c;
    }

    .dots--social-beauty .dot.active {
      background: #4338ca;
    }

    .dots--social-cute .dot.active {
      background: #db2777;
    }

    .dots--social-clever .dot.active {
      background: #15803d;
    }

    @media (min-width: 640px) {
      .attrs-layout {
        grid-template-columns: minmax(0, 1fr) 200px;
        align-items: start;
      }
    }

    .combat-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .combat-item {
      background: white;
      border-radius: 14px;
      padding: 10px 8px;
      text-align: center;
      border: 1px solid #e2e8f0;
    }

    .combat-item strong {
      display: block;
      color: #64748b;
      font-size: 11px;
      text-transform: uppercase;
      margin-bottom: 2px;
    }

    .combat-formula {
      display: block;
      font-size: 9px;
      color: #94a3b8;
    }

    .combat-value {
      display: block;
      font-size: 18px;
      font-weight: bold;
      color: #0f172a;
    }

    .skill-groups {
      display: grid;
      gap: 12px;
    }

    .skill-group {
      background: white;
      border-radius: 14px;
      padding: 12px;
      border: 1px solid #e2e8f0;
    }

    .skill-group-title {
      margin: 0 0 10px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      color: #475569;
    }

    .skill-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
    }

    .skill-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 12px;
      background: #f8fafc;
      border-radius: 10px;
    }

    .skill-name {
      font-size: 13px;
      font-weight: bold;
      color: #334155;
      text-transform: capitalize;
    }

    .skill-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
    }

    @media (max-width: 600px) {
      .skill-list {
        grid-template-columns: 1fr;
      }
    }

    .weakness-group-title {
      display: block;
      font-size: 11px;
      font-weight: bold;
      color: #64748b;
      text-transform: uppercase;
      margin: 8px 0 4px;
      letter-spacing: 0.02em;
    }

    .weakness-list {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      margin-bottom: 12px;
    }

    .weakness {
      font-size: 12px;
      font-weight: bold;
      padding: 4px 12px;
      border-radius: 20px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .weakness-empty {
      font-size: 13px;
      color: #94a3b8;
      font-style: italic;
    }

    .btn {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      min-height: 44px;
      border: none;
      border-radius: 14px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-primary {
      background: #3b82f6;
      color: white;
    }

    .btn-light {
      background: #f1f5f9;
      color: #334155;
      border: 1px solid #e2e8f0;
    }

    .bottom-nav {
      position: sticky;
      bottom: 0;
      z-index: 10;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      background: white;
      border-top: 1px solid #e2e8f0;
    }

    .bottom-nav a {
      padding: 14px 8px;
      text-align: center;
      text-decoration: none;
      color: #64748b;
      font-size: 13px;
    }

    .evo-chain {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 8px 4px;
      overflow-x: auto;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
    }

    .evo-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 0 0 130px;
      padding: 14px 10px;
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      text-align: center;
      text-decoration: none;
      color: #1e293b;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
      transition: all 0.2s ease-in-out;
    }

    .evo-card:hover {
      transform: translateY(-4px);
      border-color: #cbd5e1;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .evo-card.is-current {
      border-color: #3b82f6;
      background: #f0f9ff;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }

    .evo-card img {
      width: 72px;
      height: 72px;
      object-fit: contain;
      margin-bottom: 8px;
      filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
    }

    .evo-number {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: #94a3b8;
      margin-bottom: 2px;
    }

    .evo-name {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: #0f172a;
      text-transform: capitalize;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .evo-method {
      display: block;
      margin-top: 6px;
      font-size: 10px;
      font-weight: 800;
      color: #3b82f6;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      min-height: 15px;
    }

    .evo-arrow {
      font-size: 20px;
      color: #94a3b8;
      font-weight: bold;
      user-select: none;
      flex-shrink: 0;
    }

    @media (min-width: 1024px) {
      body {
        padding: 24px 16px;
      }

      .app {
        max-width: 1100px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      }

      .section-grid {
        grid-template-columns: 1.1fr 1fr 1fr;
      }

      .panel-wide {
        grid-column: span 2;
      }
    }

    .species-abilities {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 4px;
    }

    .ability-info-btn {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 1px solid #64748b;
      background: #f1f5f9;
      font-size: 11px;
      cursor: pointer;
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

    .section-alert-banner {
      margin-bottom: 12px;
      padding: 10px;
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      border-radius: 4px;
      font-size: 13px;
    }
  </style>
</head>

<body>

  <main class="app">

    <header class="detail-header" style="<?= htmlspecialchars($heroStyle, ENT_QUOTES, 'UTF-8'); ?>">

      <div class="header-navigation">
        <?php if ($pokemon_prev_id !== null): ?>
          <a href="pokemon-dettaglio.php?id=<?= (int)$pokemon_prev_id; ?>" class="nav-arrow-link" aria-label="Precedente">&lsaquo;</a>
        <?php else: ?>
          <span class="nav-arrow-link is-disabled" aria-hidden="true">&lsaquo;</span>
        <?php endif; ?>

        <div class="header-main-info">
          <span class="pokemon-number"><?= $pokemon['numeroPokedex'] ?></span>
          <h1><?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>

        <?php if ($pokemon_next_id !== null): ?>
          <a href="pokemon-dettaglio.php?id=<?= (int)$pokemon_next_id; ?>" class="nav-arrow-link" aria-label="Successivo">&rsaquo;</a>
        <?php else: ?>
          <span class="nav-arrow-link is-disabled" aria-hidden="true">&rsaquo;</span>
        <?php endif; ?>
      </div>

      <div class="type-list">
        <?php foreach ($pokemon['tipologie'] as $type): ?>
          <span class="type" style="<?= htmlspecialchars(badge_style_for_type($type), ENT_QUOTES, 'UTF-8'); ?>">
            <?= htmlspecialchars($type['nome'], ENT_QUOTES, 'UTF-8') ?>
          </span>
        <?php endforeach; ?>
      </div>

      <div class="hero-artwork-container">
        <img class="hero-image" src="<?= $pokemonApi['img'] ?? '' ?>" alt="Artwork di <?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="hero-actions">
        <a href="team.php?pokemonId=<?= $pokemonId ?>" class="btn btn-light" id="btnAddTeam">Aggiungi al Team</a>
      </div>

    </header>

    <section class="rank-bar" aria-labelledby="rankBarTitle">
      <label class="rank-bar-title" id="rankBarTitle" for="pokemonRank">Rango</label>
      <select class="rank-select" id="pokemonRank" name="pokemon_rank">
        <?php foreach ($pokemon_ranks as $rankKey => $rankLabel): ?>
          <option value="<?= htmlspecialchars($rankKey, ENT_QUOTES, 'UTF-8'); ?>" <?= $pokemon_rank === $rankKey ? 'selected' : ''; ?>><?= htmlspecialchars($rankLabel, ENT_QUOTES, 'UTF-8'); ?></option>
        <?php endforeach; ?>
      </select>
    </section>

    <div class="main-scroll">
      <div class="section-grid">

        <div class="profile-top">
          <section class="panel profile-dati">
            <div class="panel-title">
              <h2>Dati base</h2>
            </div>
            <div class="data-grid">
              <div class="data-item full">
                <strong>Abilità specie</strong>
                <span>
                  <?php foreach ($pokemon['abilita'] as $index => $ability): ?>
                    <?= $ability['nomeItaliano'] ?>
                    <?php if ($index > 0): ?><span class="species-ability-sep" aria-hidden="true">/</span><?php endif; ?>
                    <span class="species-ability">
                      <button type="button" class="ability-info-btn" data-ability="<?= htmlspecialchars($ability['nome'], ENT_QUOTES, 'UTF-8'); ?>">i</button>
                    </span>
                  <?php endforeach; ?>
                </span>
              </div>
              <div class="data-item"><strong>Altezza</strong><span><?= number_format(($pokemonApi['altezza'] ?? 0) / 10, 1, '.', '') ?> m</span></div>
              <div class="data-item"><strong>Peso</strong><span><?= htmlspecialchars($pesoFormattato, ENT_QUOTES, 'UTF-8') ?></span></div>
              <div class="data-item"><strong>Base HP</strong><span><?= (int)$base_hp; ?></span></div>
            </div>
          </section>

          <div class="profile-side">
            <section class="panel profile-desc">
              <div class="panel-title">
                <h2>Descrizione</h2>
              </div>
              <p class="notes"><?= nl2br(htmlspecialchars($pokemon['descrizioni'], ENT_QUOTES, 'UTF-8')); ?></p>
            </section>

            <section class="panel profile-weakness">
              <div class="panel-title">
                <h2>Efficacia Tipi</h2>
              </div>

              <?php
              $hasRows = !empty($effettivita['debolezze2']) || !empty($effettivita['debolezze']) || !empty($effettivita['resistenze']) || !empty($effettivita['resistenze2']) || !empty($effettivita['immunita']);
              ?>

              <?php if (!$hasRows): ?>
                <span class="weakness-empty">Nessuna debolezza o resistenza particolare.</span>
              <?php else: ?>

                <?php if (!empty($effettivita['debolezze2'])): ?>
                  <span class="weakness-group-title">Debolezze X2</span>
                  <div class="weakness-list">
                    <?php foreach ($effettivita['debolezze2'] as $tipo): ?>
                      <span class="weakness" style="<?= htmlspecialchars(badge_style_for_type($tipo), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($tipo['nome'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <?php if (!empty($effettivita['debolezze'])): ?>
                  <span class="weakness-group-title">Debolezze X1</span>
                  <div class="weakness-list">
                    <?php foreach ($effettivita['debolezze'] as $tipo): ?>
                      <span class="weakness" style="<?= htmlspecialchars(badge_style_for_type($tipo), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($tipo['nome'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <?php if (!empty($effettivita['resistenze'])): ?>
                  <span class="weakness-group-title">Resistenze X1</span>
                  <div class="weakness-list">
                    <?php foreach ($effettivita['resistenze'] as $tipo): ?>
                      <span class="weakness" style="<?= htmlspecialchars(badge_style_for_type($tipo), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($tipo['nome'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <?php if (!empty($effettivita['resistenze2'])): ?>
                  <span class="weakness-group-title">Resistenze X2</span>
                  <div class="weakness-list">
                    <?php foreach ($effettivita['resistenze2'] as $tipo): ?>
                      <span class="weakness" style="<?= htmlspecialchars(badge_style_for_type($tipo), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($tipo['nome'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <?php if (!empty($effettivita['immunita'])): ?>
                  <span class="weakness-group-title">Immunità</span>
                  <div class="weakness-list">
                    <?php foreach ($effettivita['immunita'] as $tipo): ?>
                      <span class="weakness" style="<?= htmlspecialchars(badge_style_for_type($tipo), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($tipo['nome'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

              <?php endif; ?>
            </section>
          </div>
        </div>

        <div class="edit-bar">
          <button type="button" class="btn btn-primary" id="btnToggleEdit">Modifica scheda</button>
          <p class="edit-hint" id="editHint" hidden>Tocca i pallini per modificare i valori.</p>
        </div>

        <section class="panel panel-wide" id="sectionAbilita">
          <div class="panel-title">
            <h2>Abilità</h2>
          </div>
          <div class="section-alert-banner" id="alertAbilita" hidden></div>
          <div class="skill-groups">
            <?php foreach ($categorieConoscenze as $categoria): ?>
              <?php if ($categoria['visibilePokemon'] == 1 && count($categoria['conoscenze']) > 0): ?>
                <div class="skill-group">
                  <h3 class="skill-group-title"><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <div class="skill-list">
                    <?php foreach ($categoria['conoscenze'] as $conoscenza): ?>
                      <?php if ($conoscenza['visibilePokemon'] == 1): ?>
                        <div class="skill-item">
                          <div class="skill-row">
                            <span class="skill-name"><?= $conoscenza['nomi'] ?></span>
                            <div class="skill-dots"><?= render_dots($pokemon_skills[strtolower($conoscenza['nome'])] ?? 0, 5, true, null, strtolower($conoscenza['nome'])); ?></div>
                          </div>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="panel panel-wide" id="sectionAttributi">
          <div class="panel-title">
            <h2>Attributi</h2>
          </div>
          <div class="attrs-layout">
            <div class="attrs-base">
              <h3 class="attrs-block-title">Combattimento</h3>
              <div class="section-alert-banner" id="alertAttributiCombattimento" hidden></div>
              <div class="combat-attrs">
                <?php foreach (['strength', 'dexterity', 'vitality', 'special', 'insight'] as $attr): ?>
                  <div class="combat-attr-card">
                    <span class="combat-attr-name"><?= ucfirst($attr) ?></span>
                    <?= render_dots($pokemon_attrs[$attr], $pokemon_attrs['limite' . ucfirst($attr)], false, 'attr', $attr); ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="attrs-social">
              <h3 class="attrs-block-title">Sociali</h3>
              <div class="section-alert-banner" id="alertAttributiSociali" hidden></div>
              <div class="social-attrs">
                <?php foreach (['tough', 'cool', 'beauty', 'cute', 'clever'] as $soc): ?>
                  <div class="social-card social-card--<?= $soc ?>">
                    <span class="social-name"><?= ucfirst($soc) ?></span>
                    <?= render_dots($pokemon_social[$soc], 5, false, $soc, $soc); ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </section>

        <section class="panel panel-wide" id="sectionCombattimento">
          <div class="panel-title">
            <h2>Combattimento</h2>
          </div>
          <div class="combat-grid">
            <div class="combat-item"><strong>HP</strong><span class="combat-formula">HP base + Vitality</span><span class="combat-value" data-combat="hp">0</span></div>
            <div class="combat-item"><strong>Will</strong><span class="combat-formula">2 + Insight</span><span class="combat-value" data-combat="will">0</span></div>
            <div class="combat-item"><strong>Iniziativa</strong><span class="combat-formula">Dexterity + Alert</span><span class="combat-value" data-combat="init">0</span></div>
            <div class="combat-item"><strong>Evasione</strong><span class="combat-formula">Dexterity + Evasion</span><span class="combat-value" data-combat="evasion">0</span></div>
            <div class="combat-item"><strong>Clash fisico</strong><span class="combat-formula">Strength + Brawl</span><span class="combat-value" data-combat="clashPhys">0</span></div>
            <div class="combat-item"><strong>Clash speciale</strong><span class="combat-formula">Special + Brawl</span><span class="combat-value" data-combat="clashSpec">0</span></div>
            <div class="combat-item"><strong>Difesa</strong><span class="combat-formula">Vitality</span><span class="combat-value" data-combat="def">0</span></div>
            <div class="combat-item"><strong>Difesa speciale</strong><span class="combat-formula">Insight</span><span class="combat-value" data-combat="sdef">0</span></div>
          </div>
        </section>

        <section class="panel panel-wide" id="sectionEvoluzioni">
          <div class="panel-title">
            <h2>Evoluzioni</h2>
          </div>
          <div class="evo-chain">
            <?php foreach ($pokemonEvolution as $index => $evo): ?>
              <?php if ($index > 0): ?><span class="evo-arrow" aria-hidden="true">→</span><?php endif; ?>
              <a href="pokemon-dettaglio.php?id=<?= (int)$evo['id']; ?>" class="evo-card<?= $evo['id'] === $pokemonId ? ' is-current' : ''; ?>">
                <img src="<?= $evo['img'] ?>" alt="Artwork di <?= htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                <span class="evo-number">#<?= str_pad((string)$evo['id'], 3, '0', STR_PAD_LEFT); ?></span>
                <span class="evo-name"><?= htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="evo-method"><?= ($evo['id'] == $pokemonId) ? 'In scheda' : ''; ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

      </div>
    </div>

    <nav class="bottom-nav">
      <a href="../pokedex.php">Dex</a>
      <a href="./nature.php">Nature</a>
      <a href="./team.php">Team</a>
    </nav>

  </main>

  <div class="ability-modal" id="abilityModal" role="dialog" aria-modal="true" hidden>
    <div class="ability-modal-dialog">
      <button type="button" class="ability-modal-close" id="abilityModalClose">&times;</button>
      <h2 id="abilityModalTitle"></h2>
      <p id="abilityModalSubtitle" style="color:#64748b; font-size:14px; margin:4px 0 12px;"></p>
      <div id="abilityModalDescContainer">
        <p id="abilityModalDescription"></p>
      </div>
      <div id="abilityModalEffectContainer" style="margin-top:10px; padding-top:10px; border-top:1px solid #e2e8f0;">
        <p id="abilityModalEffect"></p>
      </div>
    </div>
  </div>

  <script>
    const sheetState = <?= json_encode($sheet_config, JSON_UNESCAPED_UNICODE); ?>;
    const speciesAbilities = <?= json_encode($pokemon_species_abilities, JSON_UNESCAPED_UNICODE); ?>;

    const btnToggleEdit = document.getElementById('btnToggleEdit');
    const editHint = document.getElementById('editHint');
    const selectRango = document.getElementById("pokemonRank");
    const alertAbilita = document.getElementById('alertAbilita');
    const alertAttributiCombattimento = document.getElementById('alertAttributiCombattimento');
    const alertAttributiSociali = document.getElementById('alertAttributiSociali');

    function getSpentPoints(group) {
      return Object.values(sheetState[group]).reduce((sum, val) => sum + val, 0);
    }

    function getRealCombatAttrTotal() {
      return Object.keys(sheetState.attrs)
        .filter(k => !k.startsWith('limite'))
        .reduce((sum, k) => sum + sheetState.attrs[k], 0);
    }

    const attrBase = getRealCombatAttrTotal();
    const attrSocialBase = getSpentPoints('social');

    function getLevel(key) {
      if (key in sheetState.attrs) return sheetState.attrs[key];
      if (key in sheetState.skills) return sheetState.skills[key];
      if (key in sheetState.social) return sheetState.social[key];
      return 0;
    }

    function setLevel(key, level) {
      const currentRankData = sheetState.rank_config[sheetState.rank];
      if (!currentRankData) return false;

      if (key in sheetState.skills) {
        const currentLevel = sheetState.skills[key];
        const diff = level - currentLevel;
        if (level > currentRankData.skill_level_cap) {
          openWarningModal(`Livello massimo per Abilità al rango ${sheetState.rank.toUpperCase()} è ${currentRankData.skill_level_cap}!`);
          return false;
        }
        if (diff > 0 && (getSpentPoints('skills') + diff > currentRankData.max_skills)) {
          openWarningModal(`Punti Abilità esauriti per il rango ${sheetState.rank.toUpperCase()}!`);
          return false;
        }
        sheetState.skills[key] = Math.max(0, Math.min(5, level));
      } else if (key in sheetState.attrs) {
        if (key.startsWith('limite')) return false;
        const currentLevel = sheetState.attrs[key];
        const diff = level - currentLevel;
        if (diff > 0 && (getRealCombatAttrTotal() + diff > currentRankData.max_attr + attrBase)) {
          openWarningModal(`Punti Attributo insufficienti per il rango ${sheetState.rank.toUpperCase()}!`);
          return false;
        }
        const maxLimit = sheetState.attrs['limite' + key.charAt(0).toUpperCase() + key.slice(1)] || 5;
        if (level > maxLimit) {
          openWarningModal(`Questo Pokémon non può superare il valore di ${maxLimit} in questo Attributo.`);
          return false;
        }
        sheetState.attrs[key] = Math.max(0, level);
      } else if (key in sheetState.social) {
        const currentLevel = sheetState.social[key];
        const diff = level - currentLevel;
        if (diff > 0 && (getSpentPoints('social') - 5 + diff > currentRankData.max_social)) {
          openWarningModal(`Punti Sociali aggiuntivi esauriti per il rango ${sheetState.rank.toUpperCase()}!`);
          return false;
        }
        sheetState.social[key] = Math.max(0, Math.min(5, level));
      }
      return true;
    }

    function updateDotsUI(key) {
      const wrap = document.querySelector('[data-editable-dots][data-key="' + key + '"]');
      if (!wrap) return;
      const level = getLevel(key);
      wrap.dataset.level = level;
      wrap.querySelectorAll('.dot').forEach((dot, i) => {
        dot.classList.toggle('active', i < level);
      });
      updateHintText();
    }

    function updateHintText() {
      const currentRankData = sheetState.rank_config[sheetState.rank];
      if (!currentRankData) return;
      if (alertAbilita) alertAbilita.innerHTML = `<span><strong>Punti Conoscenza:</strong> ${getSpentPoints('skills')}/${currentRankData.max_skills} (Limite Singolo: ${currentRankData.skill_level_cap})</span>`;
      if (alertAttributiCombattimento) alertAttributiCombattimento.innerHTML = `<span><strong>Punti Attributo extra:</strong> ${getRealCombatAttrTotal() - attrBase}/${currentRankData.max_attr}</span>`;
      if (alertAttributiSociali) alertAttributiSociali.innerHTML = `<span><strong>Punti Sociali extra:</strong> ${getSpentPoints('social') - attrSocialBase}/${currentRankData.max_social}</span>`;
    }

    function recalcCombat() {
      const a = sheetState.attrs;
      const s = sheetState.skills;
      const values = {
        hp: sheetState.baseHp + a.vitality,
        will: 2 + a.insight,
        init: a.dexterity + (s.alert || 0),
        evasion: a.dexterity + (s.evasion || 0),
        clashPhys: a.strength + (s.brawl || 0),
        clashSpec: a.special + (s.brawl || 0),
        def: a.vitality,
        sdef: a.insight,
      };
      Object.entries(values).forEach(([key, val]) => {
        const el = document.querySelector('[data-combat="' + key + '"]');
        if (el) el.textContent = val;
      });
    }

    function setEditMode(on) {
      document.body.classList.toggle('is-editing', on);
      btnToggleEdit.classList.toggle('is-active', on);
      if (on) selectRango.setAttribute("disabled", true);
      else selectRango.removeAttribute("disabled");
      btnToggleEdit.textContent = on ? 'Fine modifica' : 'Modifica scheda';
      editHint.hidden = !on;
      if (alertAbilita) alertAbilita.hidden = !on;
      if (alertAttributiCombattimento) alertAttributiCombattimento.hidden = !on;
      if (alertAttributiSociali) alertAttributiSociali.hidden = !on;
      if (on) updateHintText();
    }

    btnToggleEdit.addEventListener('click', () => setEditMode(!document.body.classList.contains('is-editing')));

    document.querySelectorAll('[data-editable-dots]').forEach((wrap) => {
      wrap.addEventListener('click', (e) => {
        if (!document.body.classList.contains('is-editing')) return;
        const dot = e.target.closest('.dot[data-dot-index]');
        if (!dot) return;
        const key = wrap.dataset.key;
        const index = parseInt(dot.dataset.dotIndex, 10);
        const current = getLevel(key);
        const targetLevel = (current === index) ? index - 1 : index;
        if (setLevel(key, targetLevel) !== false) {
          updateDotsUI(key);
          if (key in sheetState.attrs || key in sheetState.skills) recalcCombat();
        }
      });
    });

    selectRango?.addEventListener('change', () => {
      sheetState.rank = selectRango.value;
      updateHintText();
    });

    recalcCombat();

    const abilityModal = document.getElementById('abilityModal');
    const abilityModalTitle = document.getElementById('abilityModalTitle');
    const abilityModalSubtitle = document.getElementById('abilityModalSubtitle');
    const abilityModalDescription = document.getElementById('abilityModalDescription');
    const abilityModalEffect = document.getElementById('abilityModalEffect');
    const abilityModalClose = document.getElementById('abilityModalClose');
    const abilitiesBySlug = Object.fromEntries(speciesAbilities.map((a) => [a.nome, a]));

    function openAbilityModal(nome) {
      const a = abilitiesBySlug[nome];
      if (!a || !abilityModal) return;
      abilityModalTitle.textContent = a.nomeItaliano;
      abilityModalSubtitle.textContent = a.nome;
      abilityModalDescription.textContent = a.descrizione;
      abilityModalEffect.textContent = a.effettiAggiuntivi;
      document.getElementById('abilityModalDescContainer').hidden = false;
      document.getElementById('abilityModalEffectContainer').hidden = false;
      abilityModal.classList.add('is-open');
    }

    function openWarningModal(msg) {
      if (!abilityModal) return;
      abilityModalTitle.textContent = "Attenzione";
      abilityModalSubtitle.textContent = "Validazione Scheda";
      abilityModalDescription.textContent = msg;
      document.getElementById('abilityModalDescContainer').hidden = false;
      document.getElementById('abilityModalEffectContainer').hidden = true;
      abilityModal.classList.add('is-open');
    }

    function closeAbilityModal() {
      abilityModal?.classList.remove('is-open');
    }
    document.querySelectorAll('.ability-info-btn').forEach(b => b.addEventListener('click', () => openAbilityModal(b.dataset.ability)));
    abilityModalClose?.addEventListener('click', closeAbilityModal);
    abilityModal?.addEventListener('click', (e) => {
      if (e.target === abilityModal) closeAbilityModal();
    });
  </script>
</body>

</html>