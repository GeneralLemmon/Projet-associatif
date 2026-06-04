<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/autoload.php";


$userController = new UserController();
$message = "";

if ($_POST) {
    $email    = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $user = $userController->login($email, $password);

    if ($user) {

        $_SESSION['user'] = [
            "id"      => $user->getId(),
            "firstName" => $user->getFirstName(),
            "lastName"  => $user->getLastName(),
            "name"      => $user->getFullName(),
            "level"     => $user->getLevel(),
            "is_admin" => $user->getIsAdmin()
        ];
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
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
    <title>PadelConnect - Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <?php if (!empty($message)): ?>
        <p style="color: red; text-align: center;"><?= $message ?></p>
    <?php endif; ?>

    <main>
        <h2>Connexion</h2>

        <form method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Se connecter</button>
        </form>

        <p>Pas encore de compte ? <a href="./signup.php">S'inscrire</a></p>
    </main>

    <?php include('footer.php'); ?>

</body>

</html>