<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/autoload.php";

function levelToNumber(string $level): int
{
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

    return is_numeric($level) ? (int)$level : ($mapping[$level] ?? 0);
}

$userController = new UserController();
$message = "";

if ($_POST) {
    $passwordHashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $level = htmlspecialchars($_POST['level']);
    $minLevel = (int)($_POST['min_level'] ?? 1);
    $levelNumber = levelToNumber($level);

    if ($minLevel > $levelNumber) {
        $message = "Le niveau minimum doit être inférieur ou égal à votre niveau.";
    } else {
        $newUser = new User([
        'last_name'  => htmlspecialchars($_POST['last_name']),
        'first_name' => htmlspecialchars($_POST['first_name']),
        'level'      => htmlspecialchars($_POST['level']),
        'min_level'  => (int)($_POST['min_level'] ?? 1),
        'email'      => htmlspecialchars($_POST['email']),
        'password'   => $passwordHashed,
    ]);

    $existing = $userController->readByEmail($newUser->getEmail());
    if ($existing) {
        $message = "Cet email est déjà utilisé.";
    } else {
        $userController->create($newUser);
        $user = $userController->readByEmail($newUser->getEmail());

        $_SESSION['user'] = [
            "id"        => $user->getId(),
            "firstName" => $user->getFirstName(),
            "lastName"  => $user->getLastName(),
            "name"      => $user->getFullName(),
            "level"     => $user->getLevel(),
            "minLevel"  => $user->getMinLevel(),
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
                        <img src="./Images/help.png" alt="Aide" class="help-icon">
                    </div>
                    <select id="level" name="level">
                        <option value="Débutant">1 – Débutant</option>
                        <option value="Perfectionnement">2 – Perfectionnement</option>
                        <option value="Élémentaire">3 – Élémentaire</option>
                        <option value="Intermédiaire">4 – Intermédiaire</option>
                        <option value="Confirmé">5 – Confirmé</option>
                        <option value="Avancé">6 – Avancé</option>
                        <option value="Expert">7 – Expert</option>
                        <option value="Élite">8 – Élite</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="min_level">Niveau minimum souhaité</label>
                    <select id="min_level" name="min_level">
                        <option value="1">1 – Débutant</option>
                        <option value="2">2 – Perfectionnement</option>
                        <option value="3">3 – Élémentaire</option>
                        <option value="4">4 – Intermédiaire</option>
                        <option value="5">5 – Confirmé</option>
                        <option value="6">6 – Avancé</option>
                        <option value="7">7 – Expert</option>
                        <option value="8">8 – Élite</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required>
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