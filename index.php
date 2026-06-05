<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="PadelConnect vous aide à trouver des partenaires compatibles, réserver un terrain en quelques secondes et profiter d'un jeu fluide, sans prise de tête.">
    <title>PadelConnect – Jouez au padel quand vous voulez</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="./Images/favicon.ico" type="image/x-icon">
</head>

<body>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    function normalizeLevel($level): int
    {
        if (is_numeric($level)) {
            return (int) $level;
        }

        $mapping = [
            'Débutant' => 1,
            'Perfectionnement' => 2,
            'Élémentaire' => 3,
            'Intermédiaire' => 4,
            'Confirmé' => 5,
            'Avancé' => 6,
            'Expert' => 7,
            'Élite' => 8,
        ];

        return $mapping[$level] ?? 0;
    }

    require "navbar.php"; ?>

    <?php if (!isset($_SESSION['user'])): ?>

        <!-- ===========================
         VERSION VISITEUR (non connecté)
    ============================ -->

        <section class="hero">
            <div class="hero-text">
                <h2>Jouez au padel quand vous voulez,<br> avec qui vous voulez</h2>
                <p>PadelConnect vous aide à trouver des partenaires compatibles, à réserver un terrain en quelques secondes et à profiter d’un jeu fluide, sans prise de tête</p>

                <div class="hero-img">
                    <img src="./Images/padelraquette.png" alt="Padel raquette">
                </div>

                <a href="signup.php" class="btn-primary hero-btn">S'inscrire gratuitement</a>
                <p class="micro-text">Aucun engagement — Disponible partout en France</p>
            </div>
        </section>

        <section class="steps">
            <h3>Comment ça marche</h3>

            <div class="steps-body">
                <div class="steps-grid">

                    <div class="step-card">
                        <img src="./Images/matcher.png" class="icon" alt="Matcher">
                        <div>
                            <h4>Matcher</h4>
                            <p>Trouvez des partenaires compatibles</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <img src="./Images/calendar.png" class="icon" alt="Calendrier">
                        <div>
                            <h4>Réserver</h4>
                            <p>Réservez un terrain en quelques secondes</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <img src="./Images/smile.png" class="icon" alt="S'amuser">
                        <div>
                            <h4>S'amuser</h4>
                            <p>Profitez d'un match fluide, équilibré et sans prise de tête</p>
                        </div>
                    </div>

                </div>

                <div class="steps-img">
                    <img src="./Images/padelterrain.png" alt="Joueurs de padel">
                </div>
            </div>
        </section>

        <section class="advantages">
            <h3>Avantages</h3>

            <div class="adv-grid">

                <div class="adv-card">
                    <img src="./Images/equilibre.png" class="icon" alt="Équilibre">
                    <h4>Matchs équilibrés</h4>
                    <p>grâce à un système de niveaux</p>
                </div>

                <div class="adv-card">
                    <img src="./Images/reservation.png" class="icon" alt="Réservation">
                    <h4>Réservation simplifiée</h4>
                    <p>pour réserver un terrain facilement</p>
                </div>

                <div class="adv-card">
                    <img src="./Images/notification.png" class="icon" alt="Notification">
                    <h4>Notifications intelligentes</h4>
                    <p> pour vous notifier dès qu’un match se libère</p>
                </div>

            </div>

            <a href="signup.php" class="btn-primary adv-btn">S'inscrire</a>
        </section>
        <div class="theme-toggle-btn theme-btn">
            <img class="theme-icon" src="./Images/moon.png" alt="Changer de thème">
        </div>


        <a href="#" class="back-to-top" aria-label="Retour en haut">
            <img src="./Images/fleche.png" alt="Flèche vers le haut">
        </a>

    <?php else: ?>
        <!-- =========================== VERSION CONNECTÉE ============================ -->
        <?php
        spl_autoload_register(fn($c) => require "$c.php");
        $controller = new TimeSlotController();
        $mySlots = $controller->getMyMatches($_SESSION['user']['id']);
        ?>

        <main class="matchs-page">
            <h2 class="matchs-greeting">
                Bonjour <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </h2>
            <div class="modalites-box">
                <h3>Modalités</h3>
                <ul>
                    <li>Chaque match se joue exclusivement à 4 joueurs</li>
                    <li>Une fois le match complet (4/4), aucune nouvelle inscription n’est possible</li>
                    <li>Les niveaux affichés permettent d’assurer des matchs équilibrés et cohérents</li>
                    <li>Les créneaux restent disponibles pendant 72 heures, après quoi ils sont automatiquement supprimés</li>
                    <li>L’administration envoie des notifications lorsqu’un match est sur le point d’être complet</li>
                    <li>Le paiement est réparti de manière équitable entre les joueurs de chaque équipe</li>
                    <li>Le détail des joueurs inscrits et le lieu du match sont accessibles en cliquant sur la carte du match</li>
                </ul>
            </div>

            <section class="matchs-section">
                <h3 class="matchs-title">Mes prochains matchs</h3>

                <?php if (empty($mySlots)): ?>
                    <p style="color:var(--text-soft); text-align:center">
                        Vous n'êtes inscrit à aucun match.
                        <a href="search.php" style="color:var(--blue)">Chercher un match →</a>
                    </p>
                <?php else: ?>
                    <div class="matchs-container">
                        <?php foreach ($mySlots as $i => $slot): ?>
                            <div class="match-card <?= $slot->getPlayerCount() == 4 ? 'match-card--active' : '' ?>">
                                <p class="match-date">
                                    <?= $slot->getFormattedDate() ?> – <?= $slot->getFormattedTime() ?>
                                </p>
                                <div class="match-info">
                                    <img src="Images/time.png" alt="Durée">
                                    <span>Durée : <?= $slot->getFormattedDuration() ?></span>
                                </div>

                                <div class="match-info">
                                    <img src="Images/lieu.png" alt="Lieu">
                                    <span><?= htmlspecialchars($slot->getLocation()) ?></span>
                                </div>

                                <a href="players.php?id=<?= $slot->getId() ?>" class="match-info" style="text-decoration:none;">
                                    <img src="Images/player.png" alt="Joueurs">
                                    <span><?= $slot->getPlayerCount() ?>/4 Joueurs</span>
                                </a>

                                <div class="match-info">
                                    <img src="Images/level.png" alt="Niveau">
                                    <span>Niveau : <?= $slot->getLevel() ?></span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="matchs-section" style="margin-top: 48px">
                <h3 class="matchs-title">Matchs disponibles</h3>

                <?php
                $available = $controller->getAvailable($_SESSION['user']['id']);
                if (empty($_SESSION['user']['is_admin'])) {
                    $userLevel = normalizeLevel($_SESSION['user']['level'] ?? '');
                    $minMatchLevel = isset($_SESSION['user']['minLevel']) ? (int)$_SESSION['user']['minLevel'] : 1;
                    $available = array_filter($available, function ($slot) use ($userLevel, $minMatchLevel) {
                        $slotLevel = normalizeLevel($slot->getLevel());
                        return $slotLevel <= $userLevel && $slotLevel >= $minMatchLevel;
                    });
                }
                ?>

                <?php if (empty($available)): ?>
                    <p style="color:var(--text-soft); text-align:center">
                        Aucun match disponible pour le moment.
                    </p>
                <?php else: ?>
                    <div class="matchs-container">
                        <?php foreach ($available as $slot): ?>
                            <div class="match-card">
                                <p class="match-date">
                                    <?= $slot->getFormattedDate() ?> – <?= $slot->getFormattedTime() ?>
                                </p>

                                <div class="match-info">
                                    <img src="Images/lieu.png" alt="Lieu">
                                    <span><?= htmlspecialchars($slot->getLocation()) ?></span>
                                </div>

                                <div class="match-info">
                                    <img src="Images/player.png" alt="Joueurs">
                                    <span><?= $slot->getPlayerCount() ?>/4 Joueurs</span>
                                </div>

                                <div class="match-info">
                                    <img src="Images/level.png" alt="Niveau">
                                    <span>Niveau : <?= $slot->getLevel() ?></span>
                                </div>

                                <div class="match-info">
                                    <img src="Images/level.png" alt="Durée">
                                    <span>Durée : <?= $slot->getFormattedDuration() ?></span>
                                </div>

                                <form method="POST" action="search.php" style="margin-top:8px">
                                    <input type="hidden" name="join_id" value="<?= $slot->getId() ?>">
                                    <button type="submit" class="btn-primary join-btn">Rejoindre</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

    <?php endif; ?>

    <?php require "footer.php"; ?>
</body>

</html>