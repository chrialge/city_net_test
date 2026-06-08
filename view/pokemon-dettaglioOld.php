<?php

require_once __DIR__ . '/../assets/controller/pokemon.php';
require_once __DIR__ . '/../assets/controller/conoscenze.php';
require_once __DIR__ . '/../assets/controller/categorie_conoscenze.php';
require_once __DIR__ . '/../assets/controller/ranghi.php';
require_once __DIR__ . '/../assets/helper/function.php';



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



$pokemonEvolution = getPokemonChainEvolution($pokemonId);
$ranghi = getAllRanghi();
echo '<pre>';
print_r($ranghi);
echo '</pre>';






$conoscenze = getAllConoscenze();

$categorieConoscenze = getAllCategorieConoscenze();

// Inizializza array conoscenze in ogni categoria e popola usando riferimento
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
    'coloreSecondario',
    'coloreTerzario',
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

function type_palette(array $types): array
{
  $colors = [];
  $explicitText = '';
  foreach ($types as $type) {
    $typeColors = type_colors($type);
    $added = 0;
    foreach ($typeColors as $color) {
      if (!in_array($color, $colors, true)) {
        $colors[] = $color;
        $added++;
      }
    }
    // Se questa tipologia ha effettivamente contribuito colori, cerca un coloreTesto
    if ($added > 0 && $explicitText === '') {
      foreach (['coloreTesto', 'colore_testo', 'colore_text', 'colore_texto', 'textColor', 'text_color'] as $txtKey) {
        if (isset($type[$txtKey])) {
          $c = safe_color((string) $type[$txtKey]);
          if ($c !== '') {
            $explicitText = $c;
            break;
          }
        }
      }
    }
  }

  if (count($colors) === 0) {
    return [
      'bg' => '#fefce8',
      'bg_deep' => '#fef08a',
      'accent' => '#eab308',
      'accent_dark' => '#a16207',
      'text' => '#422006',
    ];
  }

  $primary = $colors[0];
  $secondary = $colors[1] ?? $primary;
  $tertiary = $colors[2] ?? $secondary;
  $accent = $tertiary;
  $accentExplicit = "";
  // Se è stato fornito esplicitamente un colore testo (solo da tipologie con colori), usalo come priorità
  $text = $explicitText !== '' ? $explicitText : (is_dark_color($primary) ? '#ffffff' : '#111827');
  $accent_foreground = $accentExplicit !== '' ? $accentExplicit : (is_dark_color($accent) ? '#ffffff' : '#111827');

  return [
    'bg' => $primary,
    'bg_deep' => $secondary,
    'accent' => $accent,
    'accent_dark' => is_dark_color($accent) ? '#ffffff' : '#1f2937',
    'accent_foreground' => $accent_foreground,
    'text' => $text,
  ];
}

function header_style_for_types(array $types): string
{
  $colors = [];
  foreach ($types as $type) {
    $typeColors = type_colors($type);
    if (count($types) === 1) {
      // per un solo tipo usiamo tutti i suoi colori disponibili
      $colors = array_merge($colors, $typeColors);
      break;
    }
    if (!empty($typeColors)) {
      $colors[] = $typeColors[0];
    }
  }

  $colors = array_values(array_unique(array_filter($colors)));
  if (count($colors) === 0) {
    return '';
  }
  if (count($colors) === 1) {
    $textColor = is_dark_color($colors[0]) ? '#ffffff' : '#111827';
    return sprintf('background: %s; color: %s;', $colors[0], $textColor);
  }

  $textColor = is_dark_color($colors[0]) ? '#ffffff' : '#111827';
  $stops = [];
  $step = 100 / (count($colors) - 1);
  foreach ($colors as $index => $color) {
    $stops[] = sprintf('%s %d%%', $color, (int) round($index * $step));
  }

  return sprintf('background: linear-gradient(135deg, %s); color: %s;', implode(', ', $stops), $textColor);
}

function badge_style_for_type(array $type): string
{
  $colors = type_colors($type);
  if (count($colors) === 0) {
    return '';
  }
  $background = count($colors) === 1
    ? $colors[0]
    : sprintf('linear-gradient(135deg, %s)', implode(', ', $colors));
  // Preferenza per campo coloreTesto nella singola tipologia
  $textColor = '';
  foreach (['coloreTesto', 'colore_testo', 'colore_text', 'colore_texto', 'textColor', 'text_color'] as $txtKey) {
    if (isset($type[$txtKey])) {
      $c = safe_color((string) $type[$txtKey]);
      if ($c !== '') {
        $textColor = $c;
        break;
      }
    }
  }
  if ($textColor === '') {
    $textColor = is_dark_color($colors[0]) ? '#ffffff' : '#111827';
  }
  return sprintf('background: %s; color: %s; border-color: rgba(0,0,0,0.08);', $background, $textColor);
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

// Se chiamiamo render_dots su una skill non inizializzata, usiamo 0 come default.

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

// Calcolo dei massimali cumulativi in base al Rango attuale del Pokémon
$max_attributi_cumulati = 0;
$max_sociali_cumulati = 0;
$max_conoscenze_cumulati = 0;
$limite_singola_conoscenza = 1;

$js_rank_config = [];

echo "<pre>";
print_r($ranghi);
echo "</pre>";

foreach ($ranghi as $rango) {
  $key = strtolower($rango['nome']);

  // Accumuliamo i punti fino al rango corrente del Pokémon
  $max_attributi_cumulati += $rango['attributi'];
  $max_sociali_cumulati += $rango['attributiSociali'];
  $max_conoscenze_cumulati = $rango['puntiConoscenza'];

  // Salviamo la configurazione progressiva da passare a JS
  $js_rank_config[$key] = [
    'max_attr' => $max_attributi_cumulati,
    'max_social' => $max_sociali_cumulati,
    'max_skills' => $max_conoscenze_cumulati,
    'skill_level_cap' => $rango['limiteLivelloConoscenza']
  ];
}

// Struttura di configurazione aggiornata per JavaScript

$sheet_config = [
  'baseHp' => $base_hp,
  'rank' => $pokemon_rank,
  'attrs' => $pokemon_attrs,
  'skills' => $pokemon_skills,
  'social' => $pokemon_social,
  'rank_config' => $js_rank_config // Passiamo tutta la struttura cumulata a JS
];






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
      /* Pallini abilità (5 in riga): dimensione fissa, slot orizzontale */
      --dot-size-fixed: 16px;
      --dot-gap-fixed: 6px;
      --dots-count-fixed: 5;
      --dots-track-w: calc(var(--dots-count-fixed) * var(--dot-size-fixed) + (var(--dots-count-fixed) - 1) * var(--dot-gap-fixed));
      --dots-track-h: var(--dot-size-fixed);
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(180deg, var(--type-bg, #fefce8) 0%, #fffbeb 45%, #ffffff 100%);
      color: var(--type-text, #1f2937);
    }

    .app {
      width: 100%;
      max-width: 100%;
      margin: 0 auto;
      min-height: 100vh;
      min-height: 100dvh;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.9));
      display: flex;
      flex-direction: column;
    }

    .detail-header {

      top: 0;
      z-index: 10;
      background: linear-gradient(180deg, #ffffff 0%, var(--type-bg) 55%, var(--type-bg-deep) 100%);
      color: var(--type-text);
      padding: 12px 16px 22px;
      border-bottom: 3px solid var(--type-accent);
      box-shadow: 0 6px 24px rgba(234, 179, 8, 0.15);
    }

    .header-top {
      margin-bottom: 8px;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--type-accent-dark);
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
    }

    .hero-center {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding-top: 4px;
    }

    .hero-image-nav {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
      max-width: min(340px, 94vw);
      margin-bottom: 8px;
    }

    .hero-nav-btn {
      flex-shrink: 0;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid var(--type-accent-dark);
      background: rgba(255, 255, 255, 0.9);
      color: var(--type-accent-dark);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      font-size: 26px;
      font-weight: bold;
      line-height: 1;
      padding: 0 0 2px;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(234, 179, 8, 0.2);
      transition: background 0.15s, color 0.15s, border-color 0.15s, transform 0.15s;
    }

    .hero-nav-btn:hover,
    .hero-nav-btn:focus-visible {
      background: var(--type-accent);
      border-color: var(--type-accent);
      color: #fff;
      outline: none;
      transform: scale(1.05);
    }

    .hero-nav-btn.is-disabled {
      opacity: 0.35;
      pointer-events: none;
      cursor: default;
      box-shadow: none;
      transform: none;
    }

    .hero-image {
      width: min(220px, 72vw);
      height: min(220px, 72vw);
      object-fit: contain;
      filter: drop-shadow(0 10px 20px rgba(234, 179, 8, 0.35));
      margin-bottom: 8px;
    }

    .pokemon-number {
      margin: 0 0 4px;
      font-size: 15px;
      font-weight: bold;
      color: var(--type-accent-dark);
      letter-spacing: 0.06em;
    }


    .hero-center h1 {
      margin: 0 0 12px;
      font-size: clamp(2rem, 9vw, 2.75rem);
      line-height: 1.05;
      color: var(--type-text, #111827);
    }

    .type-list {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .type {
      font-size: 13px;
      padding: 7px 14px;
      border-radius: 999px;
      font-weight: bold;
    }

    .type-electric {
      background: #facc15;
      color: #1f2937;
      box-shadow: 0 2px 8px rgba(250, 204, 21, 0.45);
    }

    .hero-actions {
      margin-top: 16px;
      width: 100%;
      max-width: 300px;
      padding: 0 8px;
    }

    .hero-actions .btn {
      width: 100%;
    }

    .edit-bar {
      grid-column: 1 / -1;
      background: #fffbeb;
      border: 2px dashed var(--type-bg-deep);
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
      color: var(--type-text, #78350f);
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
      color: var(--type-text);
    }

    .type-fire {
      background: #f97316;
    }

    .type-water {
      background: #38bdf8;
    }

    .type-grass {
      background: #22c55e;
    }

    .type-flying {
      background: #818cf8;
    }

    .rank-bar {
      padding: 12px 14px 14px;
      background: var(--type-bg-deep);
      border-bottom: 2px solid var(--type-accent);
    }

    .rank-bar-title {
      display: block;
      margin: 0 0 10px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--type-text, var(--type-accent-dark));
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
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23a16207' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      border: 2px solid rgba(66, 32, 6, 0.15);
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
    }

    .rank-select:focus {
      outline: none;
      border-color: var(--type-accent);
      box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.35);
    }

    .main-scroll {
      flex: 1;
      padding: 14px 12px 80px;
      padding-bottom: calc(80px + env(safe-area-inset-bottom, 0));
    }

    .section-grid {
      display: grid;
      gap: 12px;
    }

    .profile-top {
      grid-column: 1 / -1;
      display: grid;
      gap: 12px;
      align-items: stretch;
    }

    .profile-side {
      display: flex;
      flex-direction: column;
      gap: 12px;
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
      background: #f9fafb;
      border-radius: 18px;
      padding: 14px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
    }

    .panel-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 12px;
    }

    .panel-title h2 {
      margin: 0;
      font-size: 16px;
      color: #111827;
    }

    .panel-badge {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 5px 9px;
      background: var(--type-bg-deep);
      color: var(--type-accent-dark);
      font-size: 11px;
      font-weight: bold;
    }

    .data-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
    }

    .data-item {
      background: white;
      border-radius: 14px;
      padding: 11px;
      min-height: 64px;
    }

    .data-item strong {
      display: block;
      margin-bottom: 4px;
      font-size: 11px;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .data-item span {
      font-size: 15px;
      font-weight: bold;
      color: #111827;
    }

    .data-item.full {
      grid-column: 1 / -1;
    }

    .stat-list {
      display: grid;
      gap: 11px;
    }

    /* Pallini touch-friendly (mobile-first) */
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
      background: #d1d5db;
      flex-shrink: 0;
      box-sizing: border-box;
    }

    .dot.active {
      background: var(--type-accent);
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

    /* Abilità: riga orizzontale fissa (sinistra → destra), stesso livello in ogni card */
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

    body.is-editing .skill-item .dots--track .dot[data-dot-index]::before {
      content: '';
      position: absolute;
      inset: -10px;
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
      background: linear-gradient(180deg, #5eead4 0%, #2dd4bf 100%);
      border: 2px solid #422006;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35);
    }

    .combat-attr-name {
      display: block;
      margin-bottom: 8px;
      font-size: 12px;
      font-weight: bold;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #134e4a;
    }

    .combat-attr-card .dots {
      justify-content: center;
    }

    .dots--attr {
      gap: 6px;
      max-width: 100%;
    }

    .dots--attr .dot {
      width: 16px;
      height: 16px;
      background: rgba(255, 255, 255, 0.9);
      border: 2px solid rgba(66, 32, 6, 0.28);
    }

    .dots--attr .dot.active {
      background: #0f766e;
      border-color: #134e4a;
    }

    body.is-editing .dots--attr .dot[data-dot-index] {
      width: 20px;
      height: 20px;
    }

    .dots--attr-many {
      gap: 5px;
    }

    .dots--attr-many .dot {
      width: 15px;
      height: 15px;
    }

    body.is-editing .dots--attr-many .dot[data-dot-index] {
      width: 19px;
      height: 19px;
    }

    .attrs-block-title {
      margin: 0 0 10px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--type-accent-dark);
    }

    .attrs-max-hint {
      font-size: 10px;
      font-weight: normal;
      text-transform: none;
      letter-spacing: 0;
      opacity: 0.85;
    }

    .social-attrs {
      display: grid;
      gap: 8px;
    }

    .social-card {
      border-radius: 16px;
      padding: 10px 12px 12px;
      text-align: center;
      border: 2px solid rgba(0, 0, 0, 0.12);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
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

    .dots--social {
      gap: var(--dot-gap-fixed);
    }

    .dots--social .dot {
      width: var(--dot-size-fixed);
      height: var(--dot-size-fixed);
      background: rgba(255, 255, 255, 0.85);
      border: 2px solid rgba(0, 0, 0, 0.2);
    }

    .dots--social-tough .dot.active {
      background: #a16207;
      border-color: #713f12;
    }

    .dots--social-cool .dot.active {
      background: #c2410c;
      border-color: #7c2d12;
    }

    .dots--social-beauty .dot.active {
      background: #4338ca;
      border-color: #312e81;
    }

    .dots--social-cute .dot.active {
      background: #db2777;
      border-color: #9d174d;
    }

    .dots--social-clever .dot.active {
      background: #15803d;
      border-color: #14532d;
    }

    @media (min-width: 640px) {
      .attrs-layout {
        grid-template-columns: minmax(0, 1fr) 200px;
        align-items: start;
      }
    }

    @media (min-width: 900px) {
      .attrs-layout {
        grid-template-columns: minmax(280px, 1fr) 220px;
      }
    }

    /* Desktop: pallini leggermente più compatti */
    @media (min-width: 768px) {
      .dot {
        width: 14px;
        height: 14px;
      }

      body.is-editing [data-editable-dots] .dot[data-dot-index] {
        width: 18px;
        height: 18px;
        margin: 3px;
      }

      .dots--attr .dot {
        width: 13px;
        height: 13px;
      }

      .dots--attr-many .dot {
        width: 12px;
        height: 12px;
      }

      .dots {
        gap: 6px;
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
    }

    .combat-item strong {
      display: block;
      color: #6b7280;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 2px;
    }

    .combat-formula {
      display: block;
      font-size: 9px;
      font-weight: normal;
      color: #9ca3af;
      text-transform: none;
      letter-spacing: 0;
      line-height: 1.25;
      margin-bottom: 4px;
    }

    .combat-value {
      display: block;
      font-size: 18px;
      font-weight: bold;
      color: #111827;
    }

    .skill-groups {
      display: grid;
      gap: 12px;
    }

    .skill-group {
      background: white;
      border-radius: 14px;
      padding: 12px;
    }

    .skill-group-title {
      margin: 0 0 10px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--type-accent-dark);
    }

    .skill-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 8px;
      align-items: stretch;
    }

    .skill-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 12px;
      background: #f9fafb;
      border-radius: 10px;
      min-height: calc(var(--dots-track-h) + 2rem + 14px);
    }

    .skill-item .dots {
      margin: 0;
    }

    .skill-name {
      flex: 1 1 auto;
      font-size: 13px;
      font-weight: bold;
      color: #374151;
      text-transform: capitalize;
      line-height: 1.2;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      padding-right: 8px;
    }

    .skill-row {
      flex-direction: column;
      display: flex;
      align-items: center;
      width: 100%;
      gap: 12px;
    }

    .skill-dots {
      flex: 0 0 auto;
    }

    @media (max-width: 600px) {
      .skill-item {
        flex-direction: column;
        align-items: center;
      }

      .skill-name {
        text-align: center;
        padding-right: 0;
      }
    }

    .skill-group-extra.is-empty {
      display: none;
    }

    @media (min-width: 600px) {
      .skill-groups {
        grid-template-columns: repeat(2, 1fr);
      }

      .skill-group-extra:not(.is-empty) {
        grid-column: 1 / -1;
      }
    }

    .weakness-list {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
    }

    .weakness {
      background: white;
      color: #374151;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      padding: 6px 10px;
      font-size: 12px;
      font-weight: bold;
    }

    .notes {
      margin: 0;
      line-height: 1.5;
      font-size: 14px;
      color: #374151;
    }

    .action-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-top: 12px;
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
      text-decoration: none;
      cursor: pointer;
    }

    .btn-primary {
      background: var(--type-accent);
      color: var(--type-text, #1f2937);
    }

    .btn-light {
      background: var(--type-bg);
      color: var(--type-text, var(--type-accent-dark));
      border: 1px solid var(--type-bg-deep);
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
      padding: 14px 8px;
      text-align: center;
      text-decoration: none;
      color: #6b7280;
      font-size: 13px;
    }

    .bottom-nav a.active {
      color: var(--type-accent-dark);
      font-weight: bold;
    }

    #sectionEvoluzioni {
      scroll-margin-bottom: calc(72px + env(safe-area-inset-bottom, 0));
    }

    .evo-chain {
      display: flex;
      align-items: stretch;
      gap: 6px;
      overflow-x: auto;
      padding-bottom: 4px;
      -webkit-overflow-scrolling: touch;
      scroll-snap-type: x mandatory;
    }

    .evo-card {
      flex: 1 1 0;
      min-width: 100px;
      max-width: 140px;
      scroll-snap-align: start;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 10px 8px;
      background: white;
      border-radius: 14px;
      border: 2px solid #e5e7eb;
      text-decoration: none;
      color: inherit;
      transition: border-color 0.15s, box-shadow 0.15s;
    }

    .evo-card:hover {
      border-color: var(--type-bg-deep);
    }

    .evo-card.is-current {
      border-color: var(--type-accent);
      box-shadow: 0 4px 14px rgba(234, 179, 8, 0.25);
      background: var(--type-bg);
    }

    .evo-card img {
      width: 72px;
      height: 72px;
      object-fit: contain;
      margin-bottom: 4px;
    }

    .evo-number {
      font-size: 11px;
      font-weight: bold;
      color: var(--type-accent-dark);
      margin-bottom: 2px;
    }

    .evo-name {
      font-size: 13px;
      font-weight: bold;
      color: #111827;
      margin-bottom: 4px;
    }

    .evo-method {
      font-size: 10px;
      line-height: 1.3;
      color: #6b7280;
      min-height: 2.6em;
    }

    .evo-arrow {
      flex-shrink: 0;
      align-self: center;
      font-size: 20px;
      font-weight: bold;
      color: var(--type-accent-dark);
      padding: 0 2px;
    }

    @media (min-width: 600px) {
      .app {
        max-width: 720px;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.06);
      }

      .detail-header {
        padding: 18px 24px 22px;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
      }

      .main-scroll {
        padding: 20px 24px 90px;
      }

      .section-grid {
        grid-template-columns: repeat(2, 1fr);
        align-items: start;
      }

      .panel-wide {
        grid-column: 1 / -1;
      }


      .hero-image-nav {
        max-width: 380px;
        gap: 10px;
      }

      .hero-nav-btn {
        width: 44px;
        height: 44px;
      }

      .hero-image {
        width: 260px;
        height: 260px;
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

      .section-grid {
        grid-template-columns: 1.1fr 1fr 1fr;
      }

      .panel-wide {
        grid-column: span 2;
      }

      .bottom-nav {
        max-width: 1100px;
        margin: 0 auto;
        border-radius: 0 0 24px 24px;
      }
    }


    .species-abilities {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px 4px;
      font-size: 15px;
      font-weight: bold;
      color: #111827;
    }

    .species-ability {
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .species-ability-sep {
      color: #9ca3af;
      font-weight: normal;
      margin: 0 2px;
    }

    .ability-info-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 18px;
      height: 18px;
      padding: 0;
      border: 1.5px solid var(--type-accent-dark);
      border-radius: 50%;
      background: #fffbeb;
      color: var(--type-accent-dark);
      font-size: 11px;
      font-weight: bold;
      font-style: italic;
      font-family: Georgia, 'Times New Roman', serif;
      line-height: 1;
      cursor: pointer;
      flex-shrink: 0;
      transition: background 0.15s, color 0.15s, border-color 0.15s;
    }

    .ability-info-btn:hover,
    .ability-info-btn:focus-visible {
      background: var(--type-accent);
      border-color: var(--type-accent);
      color: #fff;
      outline: none;
    }

    .ability-modal {
      position: fixed;
      inset: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      background: rgba(17, 24, 39, 0.55);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.2s, visibility 0.2s;
    }

    .ability-modal.is-open {
      opacity: 1;
      visibility: visible;
    }

    .ability-modal-dialog {
      width: min(420px, 100%);
      max-height: min(85vh, 560px);
      overflow: auto;
      background: #ffffff;
      border-radius: 18px;
      border: 2px solid var(--type-accent);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
      padding: 20px 18px 18px;
      transform: translateY(12px) scale(0.98);
      transition: transform 0.2s;
    }

    .ability-modal.is-open .ability-modal-dialog {
      transform: translateY(0) scale(1);
    }

    .ability-modal-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 14px;
    }

    .ability-modal-title {
      margin: 0;
      font-size: 20px;
      color: #111827;
      line-height: 1.2;
    }

    .ability-modal-subtitle {
      margin: 4px 0 0;
      font-size: 14px;
      color: #6b7280;
      font-weight: normal;
    }

    .ability-modal-close {
      flex-shrink: 0;
      width: 32px;
      height: 32px;
      border: none;
      border-radius: 50%;
      background: #f3f4f6;
      color: #374151;
      font-size: 20px;
      line-height: 1;
      cursor: pointer;
    }

    .ability-modal-close:hover,
    .ability-modal-close:focus-visible {
      background: #e5e7eb;
      outline: none;
    }

    .ability-modal-section {
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px solid #f3f4f6;
    }

    .ability-modal-section:first-of-type {
      margin-top: 0;
      padding-top: 0;
      border-top: none;
    }

    .ability-modal-section h3 {
      margin: 0 0 6px;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #6b7280;
    }

    .ability-modal-section p {
      margin: 0;
      font-size: 14px;
      line-height: 1.5;
      color: #1f2937;
    }

    .section-alert-banner {
      margin: 14px 0;
      padding: 12px 14px;
      background: rgba(255, 255, 255, 0.6);
      border-left: 4px solid var(--type-accent);
      border-radius: 8px;
      font-size: 13px;
      line-height: 1.45;
      color: var(--type-text);
    }

    .section-alert-banner strong {
      color: var(--type-accent-dark);
    }
  </style>
</head>

<body style="<?= htmlspecialchars($bodyStyle, ENT_QUOTES, 'UTF-8'); ?>">

  <main class="app">

    <header class="detail-header" style="<?= htmlspecialchars($heroStyle, ENT_QUOTES, 'UTF-8'); ?>">
      <div class="hero-center">
        <div class="hero-image-nav">
          <?php if ($pokemon_prev_id !== null): ?>
            <a
              href="pokemon-dettaglio.php?id=<?php echo (int) $pokemon_prev_id; ?>"
              class="hero-nav-btn hero-nav-prev"
              aria-label="Pokémon precedente">&lsaquo;</a>
          <?php else: ?>
            <span class="hero-nav-btn hero-nav-prev is-disabled" aria-hidden="true">&lsaquo;</span>
          <?php endif; ?>
          <img
            class="hero-image"
            src="<?= $pokemonApi['img'] ?? '' ?>"
            alt="Immagine ufficiale di <?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?>">
          <?php if ($pokemon_next_id !== null): ?>
            <a
              href="pokemon-dettaglio.php?id=<?php echo (int) $pokemon_next_id; ?>"
              class="hero-nav-btn hero-nav-next"
              aria-label="Pokémon successivo">&rsaquo;</a>
          <?php else: ?>
            <span class="hero-nav-btn hero-nav-next is-disabled" aria-hidden="true">&rsaquo;</span>
          <?php endif; ?>
        </div>
        <p class="pokemon-number"><?= $pokemon['numeroPokedex'] ?></p>
        <h1><?= htmlspecialchars($pokemon['nome'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="type-list">
          <?php foreach ($pokemon['tipologie'] as $type): ?>
            <span class="type" style="<?= htmlspecialchars(badge_style_for_type($type), ENT_QUOTES, 'UTF-8'); ?>">
              <?= htmlspecialchars($type['nome'], ENT_QUOTES, 'UTF-8') ?>
            </span>
          <?php endforeach; ?>
        </div>

        <div class="hero-actions">
          <button type="button" class="btn btn-light" id="btnAddTeam">Aggiungi al Team</button>
        </div>
      </div>
    </header>

    <section class="rank-bar" aria-labelledby="rankBarTitle">
      <label class="rank-bar-title" id="rankBarTitle" for="pokemonRank">Rango</label>
      <select class="rank-select" id="pokemonRank" name="pokemon_rank" aria-label="Seleziona rango">
        <?php foreach ($pokemon_ranks as $rankKey => $rankLabel): ?>
          <option
            value="<?php echo htmlspecialchars($rankKey, ENT_QUOTES, 'UTF-8'); ?>"
            <?php echo $pokemon_rank === $rankKey ? 'selected' : ''; ?>><?php echo htmlspecialchars($rankLabel, ENT_QUOTES, 'UTF-8'); ?></option>
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
                <span><?php foreach ($pokemon['abilita'] as $index => $ability): ?>
                    <?= $ability['nomeItaliano'] ?>
                    <?php if ($index > 0): ?>
                      <span class="species-ability-sep" aria-hidden="true">/</span>
                    <?php endif; ?>
                    <span class="species-ability">
                      <?php echo htmlspecialchars($ability['nome'], ENT_QUOTES, 'UTF-8'); ?>
                      <button
                        type="button"
                        class="ability-info-btn"
                        data-ability="<?php echo htmlspecialchars($ability['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                        aria-label="Info su <?php echo htmlspecialchars($ability['nomeItaliano'], ENT_QUOTES, 'UTF-8'); ?>">i</button>
                    </span>
                  <?php endforeach; ?></span>
              </div>

              <div class="data-item">
                <strong>Altezza</strong>
                <span><?= number_format(($pokemonApi['altezza'] ?? 0) / 10, 1, '.', '') ?> m</span>
              </div>

              <div class="data-item">
                <strong>Peso</strong>
                <span><?= $pokemonApi['peso'] ?? '6 kg' ?></span>
              </div>

              <div class="data-item">
                <strong>Base HP</strong>
                <span><?php echo (int) $base_hp; ?></span>
              </div>
            </div>
          </section>

          <div class="profile-side">
            <section class="panel profile-desc">
              <div class="panel-title">
                <h2>Descrizione</h2>

              </div>

              <p class="notes">
                <?php echo nl2br(htmlspecialchars($pokemon['descrizioni'], ENT_QUOTES, 'UTF-8')); ?>
              </p>
            </section>

            <section class="panel profile-weakness">
              <div class="panel-title">
                <h2>Debolezze</h2>
              </div>

              <div class="weakness-list">
                <span class="weakness">Terra</span>
              </div>
            </section>
          </div>
        </div>

        <div class="edit-bar">
          <button type="button" class="btn btn-primary" id="btnToggleEdit" aria-pressed="false">
            Modifica scheda
          </button>
          <p class="edit-hint" id="editHint" hidden>
            Tocca i pallini per modificare attributi e abilità. I valori di combattimento si aggiornano in automatico.
          </p>
        </div>

        <section class="panel panel-wide" id="sectionAbilita">
          <div class="panel-title">
            <h2>Abilità</h2>

          </div>
          <div class="section-alert-banner">
            <span><strong>Punti Conoscenza spesi:</strong> 0/14 (Limite Singolo: 3)</span>
          </div>

          <div class="skill-groups">


            <?php foreach ($categorieConoscenze as $categoria): ?>
              <?php if ($categoria['visibilePokemon'] == 1 && count($categoria['conoscenze']) > 0): ?>
                <div class="skill-group">
                  <h3 class="skill-group-title"><?php echo htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <div class="skill-list">
                    <?php foreach ($categoria['conoscenze'] as $conoscenza): ?>

                      <?php if ($conoscenza['visibilePokemon'] == 1): ?>
                        <div class="skill-item">

                          <div class="skill-row">
                            <span class="skill-name"><?php echo $conoscenza['nomi'] ?></span>
                            <div class="skill-dots"><?php echo render_dots($pokemon_skills[strtolower($conoscenza['nome'])] ?? 0, 5, true, null, strtolower($conoscenza['nome'])); ?></div>
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
              <div class="section-alert-banner">
                <span><strong>Punti Sociali extra spesi:</strong> 0/4</span>
              </div>
              <div class="combat-attrs">
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Strength</span>
                  <div class="combat-attr-dots">
                    <?php echo render_dots($pokemon_attrs['strength'], $pokemon_attrs['limiteStrength'], false, 'attr', 'strength'); ?>
                  </div>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Dexterity</span>
                  <div class="combat-attr-dots">
                    <?php echo render_dots($pokemon_attrs['dexterity'], $pokemon_attrs['limiteDexterity'], false, 'attr', 'dexterity'); ?>
                  </div>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Vitality</span>
                  <div class="combat-attr-dots">
                    <?php echo render_dots($pokemon_attrs['vitality'], $pokemon_attrs['limiteVitality'], false, 'attr', 'vitality'); ?>
                  </div>

                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Special</span>
                  <div class="combat-attr-dots">
                    <?php echo render_dots($pokemon_attrs['special'], $pokemon_attrs['limiteSpecial'], false, 'attr', 'special'); ?>
                  </div>

                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Insight</span>
                  <div class="combat-attr-dots">
                    <?php echo render_dots($pokemon_attrs['insight'], $pokemon_attrs['limiteInsight'], false, 'attr', 'insight'); ?>
                  </div>

                </div>
              </div>
            </div>

            <div class="attrs-social">
              <h3 class="attrs-block-title">Sociali</h3>
              <div class="section-alert-banner">
                <span><strong>Punti Sociali extra spesi:</strong> 0/4</span>
              </div>
              <div class="social-attrs">
                <div class="social-card social-card--tough">
                  <span class="social-name">Tough</span>
                  <?php echo render_dots($pokemon_social['tough'], 5, false, 'tough', 'tough'); ?>
                </div>
                <div class="social-card social-card--cool">
                  <span class="social-name">Cool</span>
                  <?php echo render_dots($pokemon_social['cool'], 5, false, 'cool', 'cool'); ?>
                </div>
                <div class="social-card social-card--beauty">
                  <span class="social-name">Beauty</span>
                  <?php echo render_dots($pokemon_social['beauty'], 5, false, 'beauty', 'beauty'); ?>
                </div>
                <div class="social-card social-card--cute">
                  <span class="social-name">Cute</span>
                  <?php echo render_dots($pokemon_social['cute'], 5, false, 'cute', 'cute'); ?>
                </div>
                <div class="social-card social-card--clever">
                  <span class="social-name">Clever</span>
                  <?php echo render_dots($pokemon_social['clever'], 5, false, 'clever', 'clever'); ?>
                </div>
              </div>
            </div>

          </div>
        </section>

        <section class="panel panel-wide" id="sectionCombattimento">
          <div class="panel-title">
            <h2>Combattimento</h2>
          </div>

          <div class="combat-grid">
            <div class="combat-item">
              <strong>HP</strong>
              <span class="combat-formula">HP base + Vitality</span>
              <span class="combat-value" data-combat="hp">6</span>
            </div>

            <div class="combat-item">
              <strong>Will</strong>
              <span class="combat-formula">2 + Insight</span>
              <span class="combat-value" data-combat="will">4</span>
            </div>

            <div class="combat-item">
              <strong>Iniziativa</strong>
              <span class="combat-formula">Dexterity + Alert</span>
              <span class="combat-value" data-combat="init">5</span>
            </div>

            <div class="combat-item">
              <strong>Evasione</strong>
              <span class="combat-formula">Dexterity + Evasion</span>
              <span class="combat-value" data-combat="evasion">6</span>
            </div>

            <div class="combat-item">
              <strong>Clash fisico</strong>
              <span class="combat-formula">Strength + Brawl</span>
              <span class="combat-value" data-combat="clashPhys">2</span>
            </div>

            <div class="combat-item">
              <strong>Clash speciale</strong>
              <span class="combat-formula">Special + Brawl</span>
              <span class="combat-value" data-combat="clashSpec">4</span>
            </div>

            <div class="combat-item">
              <strong>Difesa</strong>
              <span class="combat-formula">Vitality</span>
              <span class="combat-value" data-combat="def">2</span>
            </div>

            <div class="combat-item">
              <strong>Difesa speciale</strong>
              <span class="combat-formula">Insight</span>
              <span class="combat-value" data-combat="sdef">2</span>
            </div>
          </div>
        </section>

        <section class="panel panel-wide" id="sectionEvoluzioni">
          <div class="panel-title">
            <h2>Evoluzioni</h2>

          </div>

          <div class="evo-chain">
            <?php foreach ($pokemonEvolution as $index => $evo): ?>
              <?php if ($index > 0): ?>
                <span class="evo-arrow" aria-hidden="true">→</span>
              <?php endif; ?>
              <a
                href="pokemon-dettaglio.php?id=<?php echo (int) $evo['id']; ?>"
                class="evo-card<?php echo $evo['id'] === $pokemonId ? ' is-current' : ''; ?>"
                <?php echo $evo['id'] === $pokemonId ? 'aria-current="page"' : ''; ?>>
                <img
                  src="<?php echo $evo['img'] ?>"
                  alt="<?php echo htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?>"
                  loading="lazy">
                <span class="evo-number">#<?php echo str_pad((string) $evo['id'], 3, '0', STR_PAD_LEFT); ?></span>
                <span class="evo-name"><?php echo htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="evo-method"><?php
                                          if (!empty($evo['id']) == $pokemonId) {
                                            echo 'In scheda';
                                          }
                                          ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

      </div>

    </div>

    <nav class="bottom-nav">
      <a href="../pokedex.php">Dex</a>
      <a href="#sectionEvoluzioni">Evoluzioni</a>
      <a href="#">Team</a>
    </nav>

  </main>

  <div class="ability-modal" id="abilityModal" role="dialog" aria-modal="true" aria-labelledby="abilityModalTitle" hidden>
    <div class="ability-modal-dialog">
      <div class="ability-modal-header">
        <div>
          <h2 class="ability-modal-title" id="abilityModalTitle"></h2>
          <p class="ability-modal-subtitle" id="abilityModalSubtitle"></p>
        </div>
        <button type="button" class="ability-modal-close" id="abilityModalClose" aria-label="Chiudi">&times;</button>
      </div>
      <div class="ability-modal-section">
        <h3>Descrizione</h3>
        <p id="abilityModalDescription"></p>
      </div>
      <div class="ability-modal-section">
        <h3>Effetti aggiuntivi</h3>
        <p id="abilityModalEffect"></p>
      </div>
    </div>

    <script>
      const sheetState = <?php echo json_encode($sheet_config, JSON_UNESCAPED_UNICODE); ?>;
      console.log(sheetState)
      const speciesAbilities = <?php echo json_encode($pokemon_species_abilities, JSON_UNESCAPED_UNICODE); ?>;


      const btnToggleEdit = document.getElementById('btnToggleEdit');
      const editHint = document.getElementById('editHint');
      const selectRango = document.getElementById("pokemonRank");

      // Funzioni di utilità per calcolare i punti attualmente spesi sulla scheda
      function getSpentPoints(group) {
        return Object.values(sheetState[group]).reduce((sum, val) => sum + val, 0);
      }

      const attrBase = Object.keys(sheetState.attrs)
        .filter(k => !k.startsWith('limite'))
        .reduce((sum, k) => sum + sheetState.attrs[k], 0);

      const attrSocialBase = getSpentPoints('social');

      function getLevel(key) {
        if (key in sheetState.attrs) return sheetState.attrs[key];
        if (key in sheetState.skills) return sheetState.skills[key];
        if (key in sheetState.social) return sheetState.social[key];
        return 0;
      }

      // Logica di controllo dinamica dei limiti dei Ranghi
      function setLevel(key, level) {
        const currentRankData = sheetState.rank_config[sheetState.rank];
        if (!currentRankData) return false;

        // --- CASO 1: CONOSCENZE / ABILITÀ ---
        if (key in sheetState.skills) {
          const currentLevel = sheetState.skills[key];
          const diff = level - currentLevel;

          // Controllo limite sul SINGOLO livello della conoscenza (limiteLivelloConoscenza)
          if (level > currentRankData.skill_level_cap) {
            alert(`Il livello massimo per una singola Abilità al rango ${sheetState.rank.toUpperCase()} è ${currentRankData.skill_level_cap}!`);
            return false;
          }

          // Controllo limite GLOBALE dei punti conoscenza spendibili
          if (diff > 0) {
            const totalSpent = getSpentPoints('skills');
            if (totalSpent + diff > currentRankData.max_skills) {
              alert(`Hai esaurito i punti Abilità globali per il rango ${sheetState.rank.toUpperCase()}! (Max: ${currentRankData.max_skills})`);
              return false;
            }
          }
          sheetState.skills[key] = Math.max(0, Math.min(5, level));
        }

        // --- CASO 2: ATTRIBUTI FISICI ---
        else if (key in sheetState.attrs) {
          // Escludiamo le chiavi di "limite" dai conteggi se presenti
          if (key.startsWith('limite')) return false;

          const currentLevel = sheetState.attrs[key];
          const diff = level - currentLevel;

          if (diff > 0) {
            const totalSpent = Object.keys(sheetState.attrs)
              .filter(k => !k.startsWith('limite'))
              .reduce((sum, k) => sum + sheetState.attrs[k], 0);

            // Calcoliamo i punti base nativi del Pokémon (la somma iniziale degli attributi a Rango Starter)
            // Nota: Nel tuo DB a Rango Starter gli attributi aggiuntivi sono 0, quindi tutto ciò che eccede la base è un bonus
            const baseTotal = sheetState.rank_config['starter'] ? 0 : 0; // Gestibile se hai una somma base fissa

            if (totalSpent + diff > currentRankData.max_attr + 10) { // +10 ipotizzando 2 punti base automatici per i 5 attributi
              alert(`Non hai abbastanza punti Attributo per il rango ${sheetState.rank.toUpperCase()}!`);
              return false;
            }
          }

          // Controllo che non superi il limite massimo specifico della specie (es. massimoVitality)
          const limitKey = 'limite' + key.charAt(0).toUpperCase() + key.slice(1);
          const maxLimit = sheetState.attrs[limitKey] || 5;
          if (level > maxLimit) {
            alert(`Questo Pokémon non può superare il valore di ${maxLimit} in questo Attributo.`);
            return false;
          }

          sheetState.attrs[key] = Math.max(0, level);
        }

        // --- CASO 3: ATTRIBUTI SOCIALI ---
        else if (key in sheetState.social) {
          const currentLevel = sheetState.social[key];
          const diff = level - currentLevel;

          if (diff > 0) {
            // Calcola il totale attuale (Sottraiamo 5 perché ogni attributo parte da 1 di base fissa)
            const totalSpent = getSpentPoints('social') - 5;
            if (totalSpent + diff > currentRankData.max_social) {
              alert(`Hai esaurito i punti Sociali aggiuntivi per il rango ${sheetState.rank.toUpperCase()}! (Max Bonus: ${currentRankData.max_social})`);
              return false;
            }
          }
          sheetState.social[key] = Math.max(0, Math.min(5, level));
        }

        return true;
      }

      function updateDotsUI(key) {
        const wrap = document.querySelector('[data-editable-dots][data-key="' + key + '"]');
        if (!wrap) return;
        const level = getLevel(key);
        const max = parseInt(wrap.dataset.max, 10);
        wrap.dataset.level = level;
        wrap.setAttribute('aria-label', level + ' su ' + max);
        wrap.querySelectorAll('.dot').forEach((dot, i) => {
          dot.classList.toggle('active', i < level);
        });

        updateHintText();
      }

      // Aggiorna il testo informativo mostrando i tre tetti di spesa separati
      function updateHintText() {
        if (!document.body.classList.contains('is-editing')) return;
        const currentRankData = sheetState.rank_config[sheetState.rank];
        if (!currentRankData) return;

        const spentSkills = getSpentPoints('skills');
        const spentSocial = getSpentPoints('social') - attrSocialBase;
        const spentAttr = getSpentPoints('attrs') - attrBase;

        console.log(currentRankData)
        console.log(spentSkills)
        console.log(spentAttr)
        // Rimuoviamo la base di 1 punto per card

        editSocial = `<strong>Rango attuale: ${sheetState.rank.toUpperCase()}</strong><br>` +
          `Punti Conoscenza spesi: ${spentSkills}/${currentRankData.max_skills} (Limite Singolo: ${currentRankData.skill_level_cap})<br>` +
          `Punti Sociali extra spesi: ${spentSocial}/${currentRankData.max_social}`;

        editattr = `<strong>Rango attuale: ${sheetState.rank.toUpperCase()}</strong><br>` +
          `Punti Conoscenza spesi: ${spentSkills}/${currentRankData.max_skills} (Limite Singolo: ${currentRankData.skill_level_cap})<br>` +
          `Punti Sociali extra spesi: ${spentSocial}/${currentRankData.max_social}`;

        editHint.innerHTML = `<strong>Rango attuale: ${sheetState.rank.toUpperCase()}</strong><br>` +
          `Punti Conoscenza spesi: ${spentSkills}/${currentRankData.max_skills} (Limite Singolo: ${currentRankData.skill_level_cap})<br>` +
          `Punti Sociali extra spesi: ${spentSocial}/${currentRankData.max_social}`;
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
        console.log(on)
        if (on == true) {
          selectRango.setAttribute("disabled", true);
        } else {
          selectRango.removeAttribute("disabled", true);
        }
        btnToggleEdit.setAttribute('aria-pressed', on ? 'true' : 'false');
        btnToggleEdit.textContent = on ? 'Fine modifica' : 'Modifica scheda';
        editHint.hidden = !on;
        if (on) updateHintText();
      }

      btnToggleEdit.addEventListener('click', () => {
        setEditMode(!document.body.classList.contains('is-editing'));
      });

      document.querySelectorAll('[data-editable-dots]').forEach((wrap) => {
        wrap.addEventListener('click', (e) => {
          if (!document.body.classList.contains('is-editing')) return;
          const dot = e.target.closest('.dot[data-dot-index]');
          if (!dot) return;

          const key = wrap.dataset.key;
          const index = parseInt(dot.dataset.dotIndex, 10);
          const current = getLevel(key);
          const targetLevel = (current === index) ? index - 1 : index;

          const success = setLevel(key, targetLevel);

          if (success !== false) {
            updateDotsUI(key);
            if (key in sheetState.attrs || key in sheetState.skills) {
              recalcCombat();
            }
          }
        });
      });

      document.getElementById('btnAddTeam')?.addEventListener('click', () => {
        alert('Funzione «Aggiungi al Team» — da collegare al salvataggio.');
      });

      const rankSelect = document.getElementById('pokemonRank');
      if (rankSelect) {
        rankSelect.addEventListener('change', () => {
          sheetState.rank = rankSelect.value;
          updateHintText();
        });
      }

      recalcCombat();

      const abilityModal = document.getElementById('abilityModal');
      const abilityModalTitle = document.getElementById('abilityModalTitle');
      const abilityModalSubtitle = document.getElementById('abilityModalSubtitle');
      const abilityModalDescription = document.getElementById('abilityModalDescription');
      const abilityModalEffect = document.getElementById('abilityModalEffect');
      const abilityModalClose = document.getElementById('abilityModalClose');
      let abilityModalTrigger = null;

      const abilitiesBySlug = Object.fromEntries(
        speciesAbilities.map((ability) => [ability.nome, ability])
      );

      console.log(abilitiesBySlug)

      function openAbilityModal(nome, trigger) {
        const ability = abilitiesBySlug[nome];
        console.log(ability)
        if (!ability || !abilityModal) return;

        abilityModalTitle.textContent = ability.nomeItaliano;
        abilityModalSubtitle.textContent = ability.nome;
        abilityModalDescription.textContent = ability.descrizione;
        abilityModalEffect.textContent = ability.effettiAggiuntivi;

        abilityModal.hidden = false;
        abilityModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        abilityModalTrigger = trigger || null;
        abilityModalClose.focus();
      }

      function closeAbilityModal() {
        if (!abilityModal) return;

        abilityModal.classList.remove('is-open');
        abilityModal.hidden = true;
        document.body.style.overflow = '';
        if (abilityModalTrigger) {
          abilityModalTrigger.focus();
          abilityModalTrigger = null;
        }
      }

      document.querySelectorAll('.ability-info-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          openAbilityModal(btn.dataset.ability, btn);
        });
      });

      abilityModalClose?.addEventListener('click', closeAbilityModal);

      abilityModal?.addEventListener('click', (e) => {
        if (e.target === abilityModal) {
          closeAbilityModal();
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && abilityModal?.classList.contains('is-open')) {
          closeAbilityModal();
        }
      });
    </script>

</body>

</html>