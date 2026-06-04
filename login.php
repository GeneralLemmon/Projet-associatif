<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/autoload.php";

$userController = new UserController();
$message = "";

if ($_POST) {
    $email    = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $user = $userController->login($email, $password);

    if ($user) {
        $_SESSION['user'] = [
            "id"        => $user->getId(),
            "firstName" => $user->getFirstName(),
            "lastName"  => $user->getLastName(),
            "name"      => $user->getFullName(),
            "level"     => $user->getLevel(),
            "is_admin"  => $user->getIsAdmin()
        ];
        echo "<script>window.location.href='index.php'</script>";
    } else {
        $message = "Email ou mot de passe incorrect.";
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
            <p style="color:var(--blue); text-align:center; margin-bottom:20px;">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <div class="card card-auth">
            <h3 class="card-title">Identifiants</h3>

            <form method="POST" class="form-grid">

                <div class="form-group full">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group full">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group full" style="margin-top:20px;">
                    <button type="submit" class="btn-primary" style="width:100%;">Se connecter</button>
                </div>

            </form>

            <p class="micro-text" style="margin-top:20px; text-align:center;">
                Pas encore de compte ?
                <a href="signup.php" style="color:var(--blue)">Créer un compte</a>
            </p>
        </div>

    </main>


    <?php include('footer.php'); ?>

</body>

</html>