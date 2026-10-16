<?php

session_start();
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// =====================================================================
//  LES DONNÉES DU DROP (fournies - vous pouvez les modifier pour tester)
// =====================================================================
$marque     = "Street Feet";
$modele     = "Nova Runner";
$coloris    = "Midnight Volt";
$numeroDrop = 1;
$prix       = 189.90;
$couleur    = "#c6ff00";              // changez-la : toute la page change !
$dateDrop   = "2026-10-31 20:00:00";  // mettez une date passée pour ouvrir les ventes

// Les pointures et le stock de chaque pointure.
// ⚠️ Même position = même pointure : $pointures[2] (40) a un stock de $stocks[2] (5).
$pointures = [38, 39, 40, 41, 42, 43, 44, 45, 46];
$stocks    = [0,  2,  5,  8,  12, 9,  3,  1,  0];

// Les grandes étapes de la création de la paire
$etapes = [
    "Croquis à la main",
    "Prototype imprimé en 3D",
    "Tests sur le bitume parisien",
    "Production en série limitée",
    "Le drop",
];

$codePromo      = "DROP10";  // -10 %
$seuilLivraison = 200;       // livraison offerte à partir de ce montant
$fraisLivraison = 9.90;
$maxParPersonne = 2;


// =====================================================================
//  MISSION 1 — Variables et concaténation
// =====================================================================
// TODO 1.1 : $nomComplet doit contenir la marque, un espace, puis le modèle.
//            → "Street Feet Nova Runner"
$nomComplet = "$marque $modele";

// TODO 1.2 : $numeroAffiche doit contenir "#" suivi du numéro sur 3 chiffres.
//            → "#001"
//            Indice : str_pad($numeroDrop, 3, "0", STR_PAD_LEFT)
$numeroAffiche = str_pad($numeroDrop, 3, "0", STR_PAD_LEFT);

// TODO 1.3 : $prixAffiche doit contenir le prix au format français.
//            → "189,90 €"
//            Indice : number_format($prix, 2, ',', ' ')
$prixAffiche = number_format($prix,2, ',', ' ')."€";

// TODO 1.4 : descendez dans le HTML et remplacez chaque commentaire « TODO 1.4 »
//            par un affichage de la bonne variable (avec la balise courte =)
//            → topbar, titre, coloris, prix, récapitulatif, footer…
$annee = date("Y") ;
$date_temps = date("d/m/Y").date("H:i:s");

// =====================================================================
//  MISSION 2 — Le compte à rebours (calculs)
// =====================================================================
// TODO 2.1 : calculez le nombre de secondes entre maintenant et le drop.
//            Indices : strtotime($dateDrop) donne la date du drop en secondes,
//                      time() donne l'instant présent en secondes.
$secondesRestantes = strtotime($dateDrop) - time() ;

$jours = floor($secondesRestantes/86400) ;
$heures = floor((fmod($secondesRestantes, 86400)/3600)) ;
$minutes = floor(fmod($heures,3600)/60) ;

// TODO 2.2 : SI il reste du temps (> 0), calculez :    
//            - $jours   : combien de fois 86400 secondes (1 jour) ?
//            - $heures  : avec le RESTE des jours (%), combien de fois 3600 ?
//            - $minutes : avec le reste des heures, combien de fois 60 ?
//            Indice : floor() arrondit à l'entier inférieur.


// =====================================================================
//  MISSION 3 — Analyse du stock (boucle)
// =====================================================================

$stockTotal       = 0;   // nombre total de paires
$nbPointuresDispo = 0;   // nombre de pointures avec au moins 1 paire
$maxStock         = 0;   // le plus gros stock
$pointureStar     = 0;   // la pointure qui a le plus gros stock

// function analyseStock() {
//     for ($i = 0; $i < count($pointures); $i++) {
//         $stockTotal += $stocks[$i] ;
//         if ($stocks[$i] != 0) {
//             $nbPointuresDispo++;
//             }

//         if ($stocks[$i] > $maxStock) {
//             $maxStock = $stocks[$i];
//             $pointureStar = $pointures[$i];
//             }
// }
// }

// analyseStock() ;

for ($i = 0; $i < count($pointures); $i++) {
        $stockTotal += $stocks[$i] ;
        if ($stocks[$i] != 0) {
            $nbPointuresDispo++;
            }

        if ($stocks[$i] > $maxStock) {
            $maxStock = $stocks[$i];
            $pointureStar = $pointures[$i];
            }
}
// TODO 3 : avec UNE SEULE boucle for qui parcourt les deux tableaux
//          ($pointures et $stocks), calculez les 4 variables ci-dessus.
//          Indice : for ($i = 0; $i < count($pointures); $i++) { ... }
//          Résultat attendu : 40 paires, 7 pointures dispo, pointure star 42.


// =====================================================================
//  MISSION 4 — Le statut du drop (conditions)
// =====================================================================
// TODO 4 : remplissez $statut et $statutTexte :
//   - plus aucune paire en stock  → "soldout" / "Sold out"
//   - sinon, le drop n'a pas commencé → "bientot" / "Bientôt disponible"
//   - sinon                        → "live"    / "En vente maintenant"
//   ⚠️ L'ordre des conditions compte !

if ($stockTotal == 0) {
    $statut = "soldout" ;
    $statutTexte ="Sold out" ;
}
else if ($secondesRestantes != 0) {
    $statut = "bientot" ;
    $statutTexte = "Bientôt disponible";
}
else {
    $statut = "live" ;
    $statutTexte = "En vente maintenant" ;
}

// =====================================================================
//  MISSION 8 — La commande (formulaire POST)
// =====================================================================
$erreurs = [];             // la liste des messages d'erreur
$commandeValidee = false;

// Les valeurs du formulaire (vides au premier affichage)
$prenom = "";
$email = "";
$pointureChoisie = 0;
$quantite = 1;
$code = "";

// Variables du ticket (calculées quand la commande est valide)
$sousTotal = 0;
$remise = 0;
$livraison = 0;
$total = 0;
$numeroCommande = "";

// TODO 8.1 : SI le formulaire a été envoyé en POST
//            (et que la personne n'a pas déjà commandé : mission 9) :
//
//   a) Récupérez les 5 champs : prenom, email, pointure, quantite, code.
//      - pensez à trim() pour le texte et à (int) pour les nombres ;
//      - le code promo doit être mis en majuscules (strtoupper).
//
//   b) Ajoutez un message dans $erreurs ($erreurs[] = "...";) si :
//      - le prénom fait moins de 2 caractères (strlen) ;
//      - l'email n'est pas valide (filter_var($email, FILTER_VALIDATE_EMAIL)) ;
//      - la pointure n'existe pas dans $pointures
//        → Indice : $index = array_search($pointureChoisie, $pointures);
//          renvoie la position de la pointure, ou false si elle n'existe pas ;
//      - la pointure est épuisée ($stocks[$index] vaut 0) ;
//      - la quantité demandée dépasse le stock de cette pointure ;
//      - la quantité n'est pas entre 1 et $maxParPersonne ;
//      - un code promo est saisi mais ce n'est pas $codePromo.
//
//   c) S'il n'y a AUCUNE erreur (count($erreurs) === 0) :
//      - $sousTotal = prix × quantité ;
//      - $remise    = 10 % du sous-total si le code est bon, sinon 0 ;
//      - $livraison = 0 si (sous-total - remise) >= $seuilLivraison, sinon $fraisLivraison ;
//      - $total     = sous-total - remise + livraison ;
//      - $numeroCommande = "SF-" + la date du jour (date('Ymd')) + "-" + un nombre au hasard (rand(1000, 9999)) ;
//      - $commandeValidee = true.
//
//   ✅ Test : 2 paires en 42 avec DROP10 → total 341,82 €, livraison offerte.
//   ✅ Test : 1 paire en 40 sans code   → total 199,80 € (livraison 9,90 €).
//
// TODO 8.2 : descendez dans le HTML (section COMMANDE) pour les TODO 8.2 à 8.5.


// =====================================================================
//  MISSION 9 — Sessions
// =====================================================================
// TODO 9.1 : comptez les visites dans $_SESSION['visites'] (+1 à chaque affichage)
//            et mettez la valeur dans $visites.
$visites = 0;

// TODO 9.2 : une seule commande par personne !
//   - Quand une commande est validée (mission 8), enregistrez son numéro
//     dans $_SESSION['numeroCommande'].
//   - Ne traitez PAS le formulaire si $_SESSION['numeroCommande'] existe déjà (isset).
//   - Dans le HTML (TODO 9.3), affichez le message « déjà commandé ».


// =====================================================================
//  MISSION 10 — Bonus
// =====================================================================
// TODO 10 : un nombre au hasard entre 120 et 480 (rand) pour faire monter la pression.
$spectateurs = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nomComplet ?> — Drop <?= $numeroDrop ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body style="--accent: #c6ff00"> 

<header class="topbar">
    <a href="index.php" class="logo"><?= $marque ?> <span>Drops</span></a>
    <div class="topbar__infos">
        <span class="live-dot"><?= $spectateurs ?> personnes regardent</span>
        <span>
            Visite n°<?= $visites ?>
            <?php // TODO 9.1 : SI la personne est venue 3 fois ou plus, afficher « — la hype est réelle 🔥 » ?>
        </span>
    </div>
</header>

<!-- ================= MARQUEE (MISSION 5) ================= -->
<div class="marquee" aria-hidden="true">
    <div class="marquee__track">
        <?php
            for ($i = 1; $i <= 10; $i++) {
                echo ("<span class='marquee__item'>Drop $numeroAffiche ✦ $modele ✦ $coloris ✦</span>") ;
            }
        ?>
    </div>
</div>

<!-- ================= HERO ================= -->
<section class="hero">
    <div>
        <p class="hero__kicker">Drop <strong><?= $numeroAffiche ?></strong> · Édition limitée</p>
        <h1 class="hero__title">
            <?= $marque ?>
            <span class="outline"><?= $modele ?></span>
        </h1>

        <p class="hero__colorway">Coloris <strong><?= $coloris ?></strong></p>
        <p class="hero__price"><?= $prixAffiche ?></p>

        <span class="status status--<?= $statut ?>"><?= $statutTexte ?></span>

        <?php if ($statut == "bientot"): ?>
            <div class="countdown">
                <div class="countdown__box"><span class="countdown__value"><?= $jours ?></span><span class="countdown__label">jours</span></div>
                <div class="countdown__box"><span class="countdown__value"><?= $heures ?></span><span class="countdown__label">heures</span></div>
                <div class="countdown__box"><span class="countdown__value"><?= $minutes ?></span><span class="countdown__label">minutes</span></div>
            </div>
        <?php endif; ?>
        <?php if ($statut != "soldout"): ?>
            <a href="#commande" class="cta"><?php echo ($statut == 'live') ? 'Pré-commander' : 'Commander';?></a>
        <?php endif; ?>
     </div>

    <div class="visual">
        <div class="visual__ring"></div>
        <div class="visual__orb"></div>
        <div class="visual__shoe">👟</div>
        <div class="visual__tag">Seulement <?= $stockTotal ?> paires</div>
    </div>
</section>

<!-- ================= STATS (MISSION 3) ================= -->
<section class="section">
    <div class="section__head">
        <div>
            <p class="section__num">01 — Le stock</p>
            <h2 class="section__title">En chiffres</h2>
        </div>
        <p class="section__desc">Une seule production, aucun réassort. Quand c'est fini, c'est fini.</p>
    </div>

    <div class="stats">
        <div class="stat"><p class="stat__value accent"><?= $stockTotal ?></p><p class="stat__label">paires au total</p></div>
        <div class="stat"><p class="stat__value"><?= $nbPointuresDispo ?>/<?= count($pointures) ?></p><p class="stat__label">pointures dispo</p></div>
        <div class="stat"><p class="stat__value"><?= $pointureStar ?></p><p class="stat__label">la mieux fournie</p></div>
        <div class="stat"><p class="stat__value"><?= $maxParPersonne ?></p><p class="stat__label">paires max / personne</p></div>
    </div>
</section>

<!-- ================= POINTURES (MISSION 6) ================= -->
<section class="section">
    <div class="section__head">
        <div>
            <p class="section__num">02 — Les tailles</p>
            <h2 class="section__title">Trouve ta pointure</h2>
        </div>
        <p class="section__desc">🔥 = dernières paires. Les pointures barrées sont épuisées.</p>
    </div>

    <div class="sizes">
        <?php
        for ($i = 0; $i < count($pointures); $i++) {
            if ($stocks[$i] == 0) {
                $class_infos = "class='size--soldout'" ;
                $size_infos_text = "Epuisé" ;
            }
            else if ($stocks[$i] <= 3 && $stocks[$i] >= 1) {
                $class_infos = "class='size--low'" ;
                $size_infos_text = "Plus que $stocks[$i]" ;
            }
            else {
                $class_infos = null ;
                $size_infos_text = "Disponible" ;
            }

            ($pointures[$i] == $pointureStar) ? $class_star = "class=\"size--star\"" : $class_star = null ;

            echo ('<div class="size">
                        <p class="size__eu">'.$pointures[$i].'</p>
                        <p '.$class_infos.' '.$class_star.'>'.$size_infos_text.'</p>
                    </div>') ;
        }
        // TODO 6 : avec une boucle, affichez UNE carte par pointure.
        //
        //   Modèle d'une carte :
        //   <div class="size CLASSE">
        //       <p class="size__eu">42</p>
        //       <p class="size__label">TEXTE</p>
        //   </div>
        //
        //   Selon le stock de la pointure :
        //   - stock = 0          → CLASSE "size--soldout", TEXTE "Épuisé"
        //   - stock entre 1 et 3 → CLASSE "size--low",     TEXTE "Plus que 2" (avec le vrai stock)
        //   - sinon              → pas de classe,          TEXTE "Disponible"
        //   Bonus : la pointure star (mission 3) reçoit EN PLUS la classe "size--star".
        ?>
    </div>
</section>

<!-- ================= HISTOIRE (MISSION 7) ================= -->
<section class="section">
    <div class="section__head">
        <div>
            <p class="section__num">03 — L'histoire</p>
            <h2 class="section__title">Du croquis au drop</h2>
        </div>
        <p class="section__desc"><?php // TODO 7 : le nombre d'étapes (count) ?> étapes pour créer la <?= $modele ?>.</p>
    </div>

    <div class="timeline">
        <?php
        // TODO 7 : avec un foreach, affichez une carte par étape, numérotée à partir de 1.
        //
        //   Modèle d'une carte :
        //   <div class="step">
        //       <div class="step__num">1</div>
        //       <p class="step__text">Croquis à la main</p>
        //   </div>
        //
        //   Indice : foreach ($etapes as $index => $etape) — $index commence à 0 !
        ?>
    </div>
</section>

<!-- ================= COMMANDE (MISSION 8) ================= -->
<section class="section" id="commande">
    <div class="section__head">
        <div>
            <p class="section__num">04 — La commande</p>
            <h2 class="section__title">Pré-commander</h2>
        </div>
        <p class="section__desc">Une seule commande par personne. Code promo du drop : demandez-le à votre formateur 😉</p>
    </div>

    <?php
    // TODO 8.2 : cette section doit afficher UN SEUL des 4 blocs ci-dessous :
    //   1. le TICKET           si $commandeValidee est vrai ;
    //   2. le message « DÉJÀ COMMANDÉ » si $_SESSION['numeroCommande'] existe (mission 9) ;
    //   3. le message « SOLD OUT »      si le statut est "soldout" ;
    //   4. le FORMULAIRE       dans tous les autres cas.
    //   Utilisez if / elseif / else / endif autour des blocs.
    ?>

    <!-- ===== BLOC 1 : LE TICKET ===== -->
    <div class="ticket">
        <span class="ticket__badge">Commande confirmée</span>
        <p class="ticket__title">Merci <?php // TODO 8.5 : le prénom (sécurisé avec htmlspecialchars) ?> !</p>
        <p>Commande <span class="ticket__num"><?= $numeroCommande ?></span> — confirmation envoyée à <?php // TODO 8.5 : l'email (sécurisé) ?></p>

        <div class="ticket__lines">
            <div class="ticket__line"><span>Nova Runner · EU <?= $pointureChoisie ?> × <?= $quantite ?></span><span><?= $sousTotal ?> €</span></div>
            <?php // TODO 8.5 : n'afficher la ligne de remise QUE si $remise > 0 ?>
            <div class="ticket__line"><span>Code DROP10</span><span>− <?= $remise ?> €</span></div>
            <?php // TODO 8.5 : afficher « Offerte » si la livraison vaut 0, sinon son prix ?>
            <div class="ticket__line"><span>Livraison</span><span><?= $livraison ?> €</span></div>
            <div class="ticket__line ticket__total"><span>Total</span><span><?= $total ?> €</span></div>
            <?php // TODO 8.5 : formatez tous les montants avec number_format (ex : 341,82) ?>
        </div>
    </div>

    <!-- ===== BLOC 2 : DÉJÀ COMMANDÉ ===== -->
    <div class="notice">
        <p>✋ Tu as déjà passé la commande <strong><?php // TODO 9.3 : le numéro enregistré en session ?></strong>. Une seule paire par personne, c'est la règle du drop.</p>
        <p class="muted">Pour tester à nouveau : <a href="index.php?reset=1">réinitialiser ma session</a></p>
    </div>

    <!-- ===== BLOC 3 : SOLD OUT ===== -->
    <div class="notice"><p>😢 Toutes les paires sont parties. Rendez-vous au prochain drop.</p></div>

    <!-- ===== BLOC 4 : LE FORMULAIRE ===== -->
    <div class="order">
        <form method="post" action="index.php#commande" class="form" novalidate>

            <?php
            // TODO 8.3 : SI il y a des erreurs, affichez-les dans cette liste
            //            (un <li> par erreur, avec un foreach) :
            //   <ul class="errors">
            //       <li>Message d'erreur</li>
            //   </ul>
            ?>

            <?php
            // TODO 8.4 : les 3 champs texte doivent GARDER leur valeur après une erreur :
            //            remplissez leur attribut value avec htmlspecialchars($prenom), etc.
            ?>
            <div class="form__row">
                <div class="field">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="">
                </div>
            </div>

            <div class="form__row">
                <div class="field">
                    <label for="pointure">Pointure</label>
                    <select id="pointure" name="pointure">
                        <option value="">-- Choisir --</option>
                        <?php
                        // TODO 8.4 : une <option> par pointure, avec une boucle :
                        //   <option value="42">EU 42</option>
                        //   - si la pointure est épuisée : ajoutez l'attribut disabled
                        //     et le texte " — épuisé" ;
                        //   - si c'est la pointure choisie avant l'erreur : ajoutez selected.
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label for="quantite">Quantité</label>
                    <select id="quantite" name="quantite">
                        <?php
                        // TODO 8.4 : une <option> de 1 à $maxParPersonne (boucle for),
                        //            avec selected sur la quantité choisie.
                        ?>
                        <option value="1">1</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="code">Code promo (facultatif)</label>
                <input type="text" id="code" name="code" value="">
            </div>

            <button type="submit" class="btn">Valider ma pré-commande</button>
        </form>

        <aside class="recap">
            <div class="recap__line"><span class="muted">Modèle</span><strong><?= $nomComplet ?></strong></div>
            <div class="recap__line"><span class="muted">Coloris</span><strong><?= $coloris ?></strong></div>
            <div class="recap__line"><span class="muted">Prix</span><strong class="accent"><?= $prixAffiche?></strong></div>
            <div class="recap__perks">
                <p>🚚 Livraison offerte dès <?= $seuilLivraison ?> €</p>
                <p>🔁 Retours gratuits sous 30 jours</p>
                <p>🏷️ Code -10 % disponible</p>
            </div>
        </aside>
    </div>

    <?php // TODO 8.2 : fin de la condition (endif) ?>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <p class="footer__big"><?= $modele ?></p>
    <span>© <?= $annee ?> <?= $marque ?> — Projet pédagogique PHP</span>
    <span>Page générée le <?= $date_temps ?></span>
</footer>

</body>
</html>
