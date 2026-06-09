<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/autoload.php";

$userController = new UserController();
$message = "";

if ($_POST) {

    $passwordHashed = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $newUser = new User([
        'last_name'  => htmlspecialchars($_POST['last_name']),
        'first_name' => htmlspecialchars($_POST['first_name']),
        'level'      => htmlspecialchars($_POST['level']),
        'phone'      => htmlspecialchars($_POST['phone']),
        'email'      => htmlspecialchars($_POST['email']),
        'password'   => $passwordHashed,
    ]);

    // Vérifier email unique
    if ($userController->readByEmail($newUser->getEmail())) {
        $message = "Cet email est déjà utilisé.";
    }
    // Vérifier téléphone unique
    elseif ($userController->readByPhone($newUser->getPhone())) {
        $message = "Ce numéro de téléphone est déjà utilisé.";
    } else {
        // Création
        $userController->create($newUser);

        // Récupérer l'utilisateur créé
        $user = $userController->readByEmail($newUser->getEmail());

        // Notifications existantes marquées comme lues
        $notificationController = new NotificationController();
        $notificationController->markExistingNotificationsReadForUser($user->getId());

        // Session
        $_SESSION['user'] = [
            "id"        => $user->getId(),
            "firstName" => $user->getFirstName(),
            "lastName"  => $user->getLastName(),
            "name"      => $user->getFullName(),
            "level"     => $user->getLevel(),
            "phone"     => $user->getPhone(),
            "is_admin"  => $user->getIsAdmin()
        ];

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelConnect – Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">Créer un compte</h2>

        <?php if (!empty($message)): ?>
            <p style="color:var(--blue); text-align:center; margin-bottom:20px;">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <div class="card">
            <h3 class="card-title">Informations personnelles</h3>

            <form method="POST" class="form-grid">

                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group full">
                    <div class="level-label-row">
                        <label for="level">Niveau</label>
                        <div class="level-label-row">
                            <label for="image">Merci de consulter l'aide des niveaux</label>
                            <pre> </pre>
                            <img src="./Images/help.png" alt="Aide" class="help-icon">
                        </div>
                    </div>
                    <select id="level" name="level" required>

                        <option value="Débutant">
                            1 – Débutant • J’apprends les bases • Pas classé
                        </option>

                        <option value="Perfectionnement">
                            2 – Perfectionnement • Échanges courts • Pas classé
                        </option>

                        <option value="Élémentaire">
                            3 – Élémentaire • Jeu loisir • Pas classé
                        </option>

                        <option value="Intermédiaire">
                            4 – Intermédiaire • Jeu avec vitres • P25–P100 (fin tableau)
                        </option>

                        <option value="Confirmé">
                            5 – Confirmé • Service-volée, smashs • P100 (milieu) / P250 (fin)
                        </option>

                        <option value="Avancé">
                            6 – Avancé • Jeu rapide, effets • P100 (top 4) / P250 / P500 • Top 600–900 FR
                        </option>

                        <option value="Expert">
                            7 – Expert • Bandeja, vibora • P500 / P1000 • Top 450–2000 FR
                        </option>

                        <option value="Élite">
                            8 – Élite • Très haute intensité • P1000–P2000 • Top 150–1000 FR
                        </option>

                    </select>


                </div>

                <div class="form-group full">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group full">
                    <label for="phone">Numéro de téléphone</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        required
                        pattern="^(?:\+33|0|0033)[67]\d{8}$"
                        title="Numéro français valide : 06, 07, +336, +337, 00336, 00337">
                </div>

                <div class="form-group full">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group full" style="margin-top:20px;">
                    <button type="submit" class="btn-primary" style="width:100%;">Créer mon compte</button>
                </div>

            </form>

            <p class="micro-text" style="margin-top:20px; text-align:center;">
                Déjà un compte ? <a href="login.php" style="color:var(--blue)">Se connecter</a>
            </p>
        </div>

    </main>
    <div id="level-overlay" class="level-overlay-wrapper">
        <div class="notif-panel">
            <div class="notif-panel-header">
                <h4>Niveaux de jeu</h4>
                <button id="level-close" style="
        background: none;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
        color: var(--text-soft);
        line-height: 1;
        padding: 0 4px;
      ">✕</button>
            </div>

            <div class="notif-panel-body">
                <img src="./Images/niveau-padel-Padel-Speak.jpg" alt="Niveaux">
            </div>
        </div>
    </div>


    <?php include('footer.php'); ?>

</body>

</html>