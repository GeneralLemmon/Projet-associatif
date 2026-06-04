<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/autoload.php";

$userController = new UserController();
$message = "";

if ($_POST) {
    $passwordHashed = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $newUser = new User([
        'last_name'  => htmlspecialchars($_POST['last_name']),
        'first_name' => htmlspecialchars($_POST['first_name']),
        'level'      => htmlspecialchars($_POST['level']),
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
            "is_admin" => $user->getIsAdmin()
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
    <title>PadelConnect - Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <?php if (!empty($message)): ?>
        <p style="color: red; text-align: center;"><?= $message ?></p>
    <?php endif; ?>

    <main>
        <h2>Formulaire d'inscription</h2>

        <form method="POST">
            <label for="last_name">Nom :</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required><br><br>

            <label for="first_name">Prénom :</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required><br><br>

            <label for="level">Niveau :</label>
            <select class="form-control" id="level" name="level">
                <option value="Débutant">1. Débutant</option>
                <option value="Perfectionnement">2. Perfectionnement</option>
                <option value="Élémentaire">3. Élémentaire</option>
                <option value="Intermédiaire">4. Intermédiaire</option>
                <option value="Confirmé">5. Confirmé</option>
                <option value="Avancé">6. Avancé</option>
                <option value="Expert">7. Expert</option>
                <option value="Élite">8. Élite</option>
            </select><br><br>

            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" name="email" required><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" class="form-control" id="password" name="password" required><br><br>

            <input type="submit" class="btn btn-success mt-3" value="S'inscrire">
        </form>

        <p>Déjà un compte ? <a href="./login.php">Se connecter</a></p>
    </main>

    <?php require "footer.php" ?>

</body>

</html>