<?php
session_start();
spl_autoload_register(function ($class) {
  require $class . ".php";
});

$userController = new UserController();
$user = $userController->read($_SESSION['user']['id']);

if (!$user) {
  session_destroy();
  header("Location: login.php");
  exit;
}

$mode = $_GET['mode'] ?? 'view';

function e($v)
{
  return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
$levels = [
  "Débutant"        => ["num" => 1, "label" => "Débutant"],
  "Perfectionnement" => ["num" => 2, "label" => "Perfectionnement"],
  "Élémentaire"     => ["num" => 3, "label" => "Élémentaire"],
  "Intermédiaire"   => ["num" => 4, "label" => "Intermédiaire"],
  "Confirmé"        => ["num" => 5, "label" => "Confirmé"],
  "Avancé"          => ["num" => 6, "label" => "Avancé"],
  "Expert"          => ["num" => 7, "label" => "Expert"],
  "Élite"           => ["num" => 8, "label" => "Élite"]
];

if ($_POST) {
  $user->setFirst_name($_POST['prenom']);
  $user->setLast_name($_POST['nom']);
  $user->setEmail($_POST['email']);
  $user->setLevel($_POST['niveau']);

  $userController->update($user);

  header("Location: profile.php");
  exit;
}

?>
<!doctype html>
<html lang="fr-FR">

<head>
  <meta charset="UTF-8">
  <title>Mon profil</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php require "navbar.php"; ?>

  <main class="profil-page">
    <h2>Mon profil</h2>

    <div class="card">
      <p class="card-title">Informations personnelles</p>

      <?php if ($mode === "edit") : ?>

        <!-- MODE ÉDITION -->
        <form method="POST">
          <div class="form-grid">

            <div class="form-group">
              <label for="prenom">Prénom</label>
              <input type="text" id="prenom" name="prenom" value="<?= e($user->getFirstName()) ?>">
            </div>

            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" id="nom" name="nom" value="<?= e($user->getLastName()) ?>">
            </div>

            <div class="form-group full">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" class="input-large" value="<?= e($user->getEmail()) ?>">
            </div>

            <div class="form-group full">
              <div class="level-label-row">
                <label for="level">Niveau</label>
                <img src="./Images/help.png" alt="Aide" id="btn-level-help" class="help-icon">
              </div>

              <select id="level" name="level">
                <?php foreach ($levels as $text => $info): ?>
                  <option value="<?= $text ?>" <?= $user->getLevel() == $text ? 'selected' : '' ?>>
                    Niveau <?= $info["num"] ?> – <?= $info["label"] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group full">
              <label for="password">Nouveau mot de passe</label>
              <input type="password" id="password" name="password" class="input-large">
            </div>

            <div class="form-group full">
              <label for="password_confirm">Confirmer le mot de passe</label>
              <input type="password" id="password_confirm" name="password_confirm" class="input-large">
            </div>
          </div>

          <div class="save-bar">
            <a href="profile.php" class="btn-secondary">Annuler</a>
            <button class="btn-primary">Enregistrer</button>
          </div>
        </form>

      <?php else : ?>

        <!-- MODE AFFICHAGE -->
        <div class="form-grid">

          <div class="form-group">
            <label>Prénom</label>
            <p class="profil-value"><?= e($user->getFirstName()) ?></p>
          </div>

          <div class="form-group">
            <label>Nom</label>
            <p class="profil-value"><?= e($user->getLastName()) ?></p>
          </div>

          <div class="form-group">
            <label>Email</label>
            <p class="profil-value"><?= e($user->getEmail()) ?></p>
          </div>

          <div class="form-group full">
            <div class="level-label-row">
              <label>Niveau</label>
              <img src="./Images/help.png" alt="Aide" class="help-icon">
            </div>
            <p class="profile-value">
              Niveau <?= e($levels[$user->getLevel()]["num"]) ?> – <?= e($levels[$user->getLevel()]["label"]) ?>
            </p>
          </div>
        </div>
        <div class="save-bar">
          <a href="logout.php" class="btn-secondary">Se déconnecter</a>
          <a href="profile.php?mode=edit" class="btn-primary">Modifier mon profil</a>
        </div>

      <?php endif; ?>

    </div>
  </main>

  <?php require "footer.php"; ?>
</body>

</html>