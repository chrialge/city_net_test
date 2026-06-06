<?php

require_once __DIR__ . '/../assets/controller/pokemon.php';
require_once __DIR__ . '/../assets/controller/conoscenze.php';
require_once __DIR__ . '/../assets/controller/categorie_conoscenze.php';


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


$conoscenze = getAllConoscenze();

$categorieConoscenze = getAllCategorieConoscenze();

echo '<pre>';
print_r($categorieConoscenze);
echo '</pre>';

echo '<pre>';
print_r($conoscenze);
echo '</pre>';
die();


// Limite pallini attributi combattimento (varia per specie — da DB)
$attr_dots_max = 12;
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

$pokemon_skills = [
  'brawl' => 0,
  'channel' => 0,
  'clash' => 0,
  'evasion' => 0,
  'alert' => 0,
  'athletic' => 0,
  'nature' => 0,
  'stealth' => 0,
  'allure' => 0,
  'etiquette' => 0,
  'intimidate' => 0,
  'perform' => 0,
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

$pokemon_rank = 'beginner';

$sheet_config = [
  'attrDotsMax' => $attr_dots_max,
  'baseHp' => $base_hp,
  'rank' => $pokemon_rank,
  'attrs' => $pokemon_attrs,
  'skills' => $pokemon_skills,
  'social' => $pokemon_social,
];

// Catena evolutiva (da DB / PokéAPI — esempio Pikachu)
$pokemon_evolutions = [
  ['id' => 172, 'name' => 'Pichu', 'method' => 'Alta amicizia'],
  ['id' => 25, 'name' => 'Pikachu', 'current' => true],
  ['id' => 26, 'name' => 'Raichu', 'method' => 'Pietratuono'],
];

function pokemon_sprite_url(int $id): string
{
  return 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'
    . $id . '.png';
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
      background: #fffbeb;
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

    .detail-header {
      position: sticky;
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
      color: #111827;
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
      background: #422006;
      color: #fef08a;
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
      color: var(--type-accent-dark);
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
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 10px 8px 12px;
      background: #f9fafb;
      border-radius: 10px;
      min-height: calc(var(--dots-track-h) + 2rem + 14px);
    }

    .skill-item .dots {
      margin: 0;
    }

    .skill-name {
      width: 100%;
      font-size: 13px;
      font-weight: bold;
      color: #374151;
      text-transform: capitalize;
      line-height: 1.2;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
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
      color: #1f2937;
    }

    .btn-light {
      background: var(--type-bg);
      color: var(--type-accent-dark);
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
  </style>
</head>

<body>

  <main class="app">

    <header class="detail-header type-electric">
      <div class="header-top">
        <a href="pokemon.php" class="back-link">← Torna al Dex</a>
      </div>

      <div class="hero-center">
        <img
          class="hero-image"
          src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png"
          alt="Pikachu">
        <p class="pokemon-number"><?= $pokemon['numeroPokemon'] ?></p>
        <h1><?= $pokemon['nome'] ?></h1>
        <div class="type-list">
          <?php foreach ($pokemon['tipologie'] as $type): ?>
            <span class="type" style="background-color: var(--type-<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>); color: var(--type-text); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
              <?= htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8'); ?>
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
              <span class="panel-badge">PokéAPI · PokéRole</span>
            </div>

            <div class="data-grid">
              <div class="data-item full">
                <strong>Abilità specie</strong>
                <span><?php foreach ($pokemon['abilita'] as $ability): ?>
                    <?= htmlspecialchars(ucfirst($ability), ENT_QUOTES, 'UTF-8'); ?>
                  <?php endforeach; ?></span>
              </div>

              <div class="data-item">
                <strong>Altezza</strong>
                <span>0.4 m</span>
              </div>

              <div class="data-item">
                <strong>Peso</strong>
                <span>6 kg</span>
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
                <h2>Descrizione GDR</h2>
                <span class="panel-badge">Note</span>
              </div>

              <p class="notes">
                Vive in piccoli gruppi nelle foreste e tende a restare nascosto.
                Accumula elettricità nelle sacche sulle guance e usa la coda per
                scaricare l’energia in eccesso. Può essere testardo e diffidente
                verso gli sconosciuti.
              </p>
            </section>

            <section class="panel profile-weakness">
              <div class="panel-title">
                <h2>Debolezze</h2>
                <span class="panel-badge">Tipo</span>
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
            <span class="panel-badge">PokéRole</span>
          </div>

          <div class="skill-groups">

            <div class="skill-group">
              <?php foreach ($categorieConoscenze as $categoria): ?>
                <?php if($categoria['visibilePokemon'] ==1): ?>
                                <h3 class="skill-group-title"><?php echo htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <div class="skill-list">
                <?php foreach($conoscenza as $conoscenza) ?>
                <?php if($conoscenza[''])
                <div class="skill-item">
                  <span class="skill-name">Brawl</span>
                  <?php echo render_dots($pokemon_skills['brawl'], 5, true, null, 'brawl'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Channel</span>
                  <?php echo render_dots($pokemon_skills['channel'], 5, true, null, 'channel'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Clash</span>
                  <?php echo render_dots($pokemon_skills['clash'], 5, true, null, 'clash'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Evasion</span>
                  <?php echo render_dots($pokemon_skills['evasion'], 5, true, null, 'evasion'); ?>
                </div>
              </div>
                  <?php endif; ?>
              <?php endforeach; ?>
                  
                

              <h3 class="skill-group-title">Fight</h3>
              <div class="skill-list">
                <div class="skill-item">
                  <span class="skill-name">Brawl</span>
                  <?php echo render_dots($pokemon_skills['brawl'], 5, true, null, 'brawl'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Channel</span>
                  <?php echo render_dots($pokemon_skills['channel'], 5, true, null, 'channel'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Clash</span>
                  <?php echo render_dots($pokemon_skills['clash'], 5, true, null, 'clash'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Evasion</span>
                  <?php echo render_dots($pokemon_skills['evasion'], 5, true, null, 'evasion'); ?>
                </div>
              </div>
            </div>

            <div class="skill-group">
              <h3 class="skill-group-title">Survival</h3>
              <div class="skill-list">
                <div class="skill-item">
                  <span class="skill-name">Alert</span>
                  <?php echo render_dots($pokemon_skills['alert'], 5, true, null, 'alert'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Athletic</span>
                  <?php echo render_dots($pokemon_skills['athletic'], 5, true, null, 'athletic'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Nature</span>
                  <?php echo render_dots($pokemon_skills['nature'], 5, true, null, 'nature'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Stealth</span>
                  <?php echo render_dots($pokemon_skills['stealth'], 5, true, null, 'stealth'); ?>
                </div>
              </div>
            </div>

            <div class="skill-group">
              <h3 class="skill-group-title">Social</h3>
              <div class="skill-list">
                <div class="skill-item">
                  <span class="skill-name">Allure</span>
                  <?php echo render_dots($pokemon_skills['allure'], 5, true, null, 'allure'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Etiquette</span>
                  <?php echo render_dots($pokemon_skills['etiquette'], 5, true, null, 'etiquette'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Intimidate</span>
                  <?php echo render_dots($pokemon_skills['intimidate'], 5, true, null, 'intimidate'); ?>
                </div>
                <div class="skill-item">
                  <span class="skill-name">Perform</span>
                  <?php echo render_dots($pokemon_skills['perform'], 5, true, null, 'perform'); ?>
                </div>
              </div>
            </div>

            <!-- Rimuovere la classe is-empty se ci sono abilità extra -->
            <div class="skill-group skill-group-extra is-empty">
              <h3 class="skill-group-title">Extra</h3>
              <div class="skill-list">
                <!-- es. <div class="skill-item">...</div> -->
              </div>
            </div>

          </div>
        </section>

        <section class="panel panel-wide" id="sectionAttributi">
          <div class="panel-title">
            <h2>Attributi</h2>
            <span class="panel-badge">Base · Sociali</span>
          </div>

          <div class="attrs-layout">

            <div class="attrs-base">
              <h3 class="attrs-block-title">Combattimento <span class="attrs-max-hint">(max <?php echo (int) $attr_dots_max; ?> pallini)</span></h3>
              <div class="combat-attrs">
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Strength</span>
                  <?php echo render_dots($pokemon_attrs['strength'], $attr_dots_max, false, 'attr', 'strength'); ?>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Dexterity</span>
                  <?php echo render_dots($pokemon_attrs['dexterity'], $attr_dots_max, false, 'attr', 'dexterity'); ?>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Vitality</span>
                  <?php echo render_dots($pokemon_attrs['vitality'], $attr_dots_max, false, 'attr', 'vitality'); ?>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Special</span>
                  <?php echo render_dots($pokemon_attrs['special'], $attr_dots_max, false, 'attr', 'special'); ?>
                </div>
                <div class="combat-attr-card">
                  <span class="combat-attr-name">Insight</span>
                  <?php echo render_dots($pokemon_attrs['insight'], $attr_dots_max, false, 'attr', 'insight'); ?>
                </div>
              </div>
            </div>

            <div class="attrs-social">
              <h3 class="attrs-block-title">Sociali</h3>
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
            <span class="panel-badge">Calcolato</span>
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
            <span class="panel-badge">Catena</span>
          </div>

          <div class="evo-chain">
            <?php foreach ($pokemon_evolutions as $index => $evo): ?>
              <?php if ($index > 0): ?>
                <span class="evo-arrow" aria-hidden="true">→</span>
              <?php endif; ?>
              <a
                href="pokemon-dettaglio.php?id=<?php echo (int) $evo['id']; ?>"
                class="evo-card<?php echo !empty($evo['current']) ? ' is-current' : ''; ?>"
                <?php echo !empty($evo['current']) ? 'aria-current="page"' : ''; ?>>
                <img
                  src="<?php echo htmlspecialchars(pokemon_sprite_url((int) $evo['id']), ENT_QUOTES, 'UTF-8'); ?>"
                  alt="<?php echo htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?>"
                  loading="lazy">
                <span class="evo-number">#<?php echo str_pad((string) $evo['id'], 3, '0', STR_PAD_LEFT); ?></span>
                <span class="evo-name"><?php echo htmlspecialchars($evo['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="evo-method"><?php
                                          if (!empty($evo['current'])) {
                                            echo 'In scheda';
                                          } elseif (!empty($evo['method'])) {
                                            echo htmlspecialchars($evo['method'], ENT_QUOTES, 'UTF-8');
                                          } else {
                                            echo '—';
                                          }
                                          ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </section>

      </div>

    </div>

    <nav class="bottom-nav">
      <a href="pokemon.php">Dex</a>
      <a href="#sectionEvoluzioni">Evoluzioni</a>
      <a href="#">Team</a>
    </nav>

  </main>

  <script>
    const sheetState = <?php echo json_encode($sheet_config, JSON_UNESCAPED_UNICODE); ?>;

    const btnToggleEdit = document.getElementById('btnToggleEdit');
    const editHint = document.getElementById('editHint');

    function getLevel(key) {
      if (key in sheetState.attrs) return sheetState.attrs[key];
      if (key in sheetState.skills) return sheetState.skills[key];
      if (key in sheetState.social) return sheetState.social[key];
      return 0;
    }

    function setLevel(key, level) {
      if (key in sheetState.attrs) {
        sheetState.attrs[key] = Math.max(0, Math.min(sheetState.attrDotsMax, level));
      } else if (key in sheetState.skills) {
        sheetState.skills[key] = Math.max(0, Math.min(5, level));
      } else if (key in sheetState.social) {
        sheetState.social[key] = Math.max(0, Math.min(5, level));
      }
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
    }

    function recalcCombat() {
      const a = sheetState.attrs;
      const s = sheetState.skills;
      const values = {
        hp: sheetState.baseHp + a.vitality,
        will: 2 + a.insight,
        init: a.dexterity + s.alert,
        evasion: a.dexterity + s.evasion,
        clashPhys: a.strength + s.brawl,
        clashSpec: a.special + s.brawl,
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
      btnToggleEdit.setAttribute('aria-pressed', on ? 'true' : 'false');
      btnToggleEdit.textContent = on ? 'Fine modifica' : 'Modifica scheda';
      editHint.hidden = !on;
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
        setLevel(key, current === index ? index - 1 : index);
        updateDotsUI(key);
        if (key in sheetState.attrs || key in sheetState.skills) {
          recalcCombat();
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
      });
    }

    recalcCombat();
  </script>

</body>

</html>