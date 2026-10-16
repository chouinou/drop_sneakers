# TP PHP - « DROP » : la page de lancement d'une sneaker

## Le contexte

**Street Feet** sort sa première paire en édition limitée : la **Nova Runner**, coloris *Midnight Volt*. Seulement 40 paires, une seule production, et une page de lancement qui doit faire monter la hype.

Le designer a livré la page : **le HTML et le CSS sont terminés**. Mais toutes les infos sont vides : c'est **PHP** qui doit les calculer et les afficher.

👉 **Votre mission : compléter le PHP de `index.php`.**

---

## Les fichiers

```
drop/
├── index.php    → VOTRE fichier de travail (cherchez les « TODO »)
├── style.css    → le design (NE PAS MODIFIER)
└── ENONCE.md    → ce document
```

**Lancer la page :** placez le dossier dans `www/` (Laragon) ou `htdocs/` (XAMPP), ou bien :

```bash
cd drop
php -S localhost:8000
```

> 💡 La page fonctionne **dès le départ** (avec des zéros et des trous). Rechargez-la après chaque mission : vous verrez la page prendre vie au fur et à mesure.

---

## Les données fournies

Tout en haut de `index.php`, les données du drop sont déjà écrites :

| Variable | Valeur | Rôle |
|---|---|---|
| `$marque`, `$modele`, `$coloris` | `"Street Feet"`, `"Nova Runner"`, `"Midnight Volt"` | les textes |
| `$numeroDrop` | `1` | le numéro du drop |
| `$prix` | `189.90` | le prix d'une paire |
| `$couleur` | `"#c6ff00"` | 🎨 la couleur de **toute** la page |
| `$dateDrop` | `"2026-10-31 20:00:00"` | ⏰ la date de sortie |
| `$pointures` | `[38, 39, 40, …, 46]` | les pointures |
| `$stocks` | `[0, 2, 5, …, 0]` | le stock **de chaque pointure** |
| `$etapes` | 5 textes | l'histoire de la paire |
| `$codePromo`, `$seuilLivraison`, `$fraisLivraison`, `$maxParPersonne` | | les règles de la commande |

### ⚠️ Deux tableaux qui vont ensemble

```php
$pointures = [38, 39, 40, 41, 42, 43, 44, 45, 46];
$stocks    = [0,  2,  5,  8,  12, 9,  3,  1,  0];
```

**Même position = même pointure.** La pointure `$pointures[2]` (40) a un stock de `$stocks[2]` (5). Pour les parcourir ensemble, on utilise **le même `$i`** :

```php
for ($i = 0; $i < count($pointures); $i++) {
    echo "Pointure " . $pointures[$i] . " : " . $stocks[$i] . " paires";
}
```

---

## Les missions

Faites-les **dans l'ordre**. Chaque mission est détaillée dans les commentaires `TODO` du fichier.

| # | Mission | Notions | Difficulté |
|---|---|---|---|
| 1 | Variables et concaténation | variables, `.`, `echo` / `<?= ?>` | ⭐ |
| 2 | Le compte à rebours | calculs, `%`, `floor()` | ⭐⭐ |
| 3 | Analyse du stock | boucle `for` sur deux tableaux | ⭐⭐ |
| 4 | Le statut du drop | `if` / `elseif` / `else` | ⭐⭐ |
| 5 | La bande défilante | boucle `for` dans du HTML | ⭐ |
| 6 | Les cartes de pointures | boucle + conditions + classes CSS | ⭐⭐ |
| 7 | L'histoire de la paire | `foreach` avec index | ⭐ |
| 8 | La commande | formulaire `POST`, validation, calculs | ⭐⭐⭐ |
| 9 | Une commande par personne | sessions | ⭐⭐ |
| 10 | Bonus | `rand()` et thème | ⭐ |

### ✅ Résultats attendus

| Mission | Vous devez voir… |
|---|---|
| 1 | « Street Feet Nova Runner », « #001 », « 189,90 € » partout dans la page |
| 2 | le nombre de jours, heures et minutes avant le 31 octobre à 20 h |
| 3 | **40** paires · **7/9** pointures dispo · pointure star **42** |
| 4 | le badge « Bientôt disponible » (testez aussi une date passée → « En vente maintenant ») |
| 5 | la bande jaune qui défile en haut |
| 6 | 9 cartes : 38 et 46 barrées, 39, 44 et 45 avec 🔥, la 42 encadrée |
| 7 | 5 étapes numérotées de 1 à 5 |
| 8 | 2 paires en 42 + code `DROP10` → **341,82 €**, livraison offerte |
| 8 | 1 paire en 40 sans code → **199,80 €** (dont 9,90 € de livraison) |
| 9 | après une commande : « Tu as déjà passé la commande… » ; le compteur de visites augmente |

### 🧪 Tests à faire sur la commande

- Envoyer le formulaire **vide** → plusieurs erreurs.
- Pointure **38** → « épuisée ».
- **2 paires en 45** (stock : 1) → « Il ne reste que 1 paire ».
- Code **FAUX** → « Le code promo n'existe pas ».
- Prénom **`<b>Imed</b>`** → doit s'afficher en texte, **pas en gras** (`htmlspecialchars`).
- Commander, puis recharger → « déjà commandé ». Le lien « réinitialiser » permet de recommencer.

### 🎨 Pour finir en beauté

- Changez `$couleur` (`"#ff3d7f"`, `"#00e5ff"`, `"#ff8a00"`…) : **toute la page change de couleur**.
- Mettez toutes les valeurs de `$stocks` à `0` : la page doit passer en **Sold out**.
- Mettez une `$dateDrop` passée : la vente doit **s'ouvrir**.

---

## Aide-mémoire

| Besoin | PHP |
|---|---|
| Afficher une variable dans le HTML | `<?= $variable ?>` |
| Coller des textes | `$a . " " . $b` |
| Nombre d'éléments d'un tableau | `count($tableau)` |
| Ajouter dans un tableau | `$erreurs[] = "message";` |
| Arrondir à l'entier inférieur | `floor(7.8)` → `7` |
| Reste d'une division | `90000 % 86400` → `3600` |
| Formater un prix | `number_format(341.82, 2, ',', ' ')` → `341,82` |
| Position d'une valeur dans un tableau | `array_search(42, $pointures)` → `4` (ou `false`) |
| Sécuriser un texte saisi | `htmlspecialchars($prenom)` |
| Condition dans le HTML | `<?php if (...): ?> … <?php else: ?> … <?php endif; ?>` |
| Boucle dans le HTML | `<?php for (...): ?> … <?php endfor; ?>` |
