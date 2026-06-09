<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/autoload.php";

$userController = new UserController();
$message = "";

if ($_POST) {

    $identifier = htmlspecialchars($_POST['identifier']); // email OU téléphone
    $password   = $_POST['password'];

    $user = $userController->login($identifier, $password);

    if ($user) {
        $_SESSION['user'] = [
            "id"        => $user->getId(),
            "firstName" => $user->getFirstName(),
            "lastName"  => $user->getLastName(),
            "name"      => $user->getFullName(),
            "level"     => $user->getLevel(),
            "phone"     => $user->getPhone(),
            "is_admin"  => $user->getIsAdmin()
        ];

        echo "<script>window.location.href='index.php'</script>";
        exit;
    } else {
        $message = "Identifiant ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelConnect – Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">Connexion</h2>

        <?php if (!empty($message)): ?>
            <p class="login-error">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <div class="card card-auth">
            <h3 class="card-title">Identifiants</h3>

            <form method="POST" class="form-grid">

                <div class="form-group full">
                    <label for="identifier">Email ou numéro de téléphone</label>
                    <input type="text" id="identifier" name="identifier" required>
                </div>

                <div class="form-group full">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group full form-group-spaced">
                    <button type="submit" class="btn-primary btn-block">Se connecter</button>
                </div>

            </form>

            <p class="micro-text login-signup-text">
                Pas encore de compte ?
                <a href="signup.php" class="login-signup-link">Créer un compte</a>
            </p>
        </div>

    </main>

    <?php include('footer.php'); ?>

</body>
</html>
