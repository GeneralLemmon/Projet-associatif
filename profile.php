<?php
session_start();
spl_autoload_register(function ($class) {
  require $class . ".php";
});

$userController = new UserController();
/** @var User $user */
$user = $userController->read($_SESSION['user']['id']);

if (!$user) {
  session_destroy();
  header("Location: login.php");
  exit;
}

$mode = $_GET['mode'] ?? 'view';
$message = "";

function e($v)
{
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
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

$levelDetails = [
  "Débutant" => "J’apprends les bases • Pas classé",
  "Perfectionnement" => "Échanges courts • Pas classé",
  "Élémentaire" => "Jeu loisir • Pas classé",
  "Intermédiaire" => "Jeu avec vitres • P25–P100 (fin tableau)",
  "Confirmé" => "Service-volée, smashs • P100 (milieu) / P250 (fin)",
  "Avancé" => "Jeu rapide, effets • P100 (top 4) / P250 / P500 • Top 600–900 FR",
  "Expert" => "Bandeja, vibora • P500 / P1000 • Top 450–2000 FR",
  "Élite" => "Très haute intensité • P1000–P2000 • Top 150–1000 FR"
];

if ($_POST) {

  // Mise à jour des champs
  $user->setFirst_name($_POST['prenom']);
  $user->setLast_name($_POST['nom']);
  $user->setEmail($_POST['email']);
  $user->setPhone($_POST['phone']);
  $user->setLevel($_POST['level']);

  // Mot de passe
  if (!empty($_POST['password'])) {
    if ($_POST['password'] === $_POST['password_confirm']) {
      $user->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
    } else {
      $message = "Les mots de passe ne correspondent pas.";
    }
  }

  if (empty($message)) {
    $userController->update($user);

    // Mise à jour session
    $_SESSION['user']['firstName'] = $user->getFirstName();
    $_SESSION['user']['lastName']  = $user->getLastName();
    $_SESSION['user']['name']      = $user->getFullName();
    $_SESSION['user']['level']     = $user->getLevel();
    $_SESSION['user']['phone']     = $user->getPhone();

    header("Location: profile.php");
    exit;
  }
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

      <?php if (!empty($message)): ?>
        <p style="color:#DC2626; text-align:center; margin-bottom:20px; font-weight:700;">
          <?= e($message) ?>
        </p>
      <?php endif; ?>

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
              <label for="phone">Téléphone</label>
              <input type="tel" id="phone" name="phone" class="input-large" value="<?= e($user->getPhone()) ?>">
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
                <option value="Débutant" <?= $user->getLevel() === "Débutant" ? 'selected' : '' ?>>
                  1 – Débutant • J’apprends les bases • Pas classé
                </option>

                <option value="Perfectionnement" <?= $user->getLevel() === "Perfectionnement" ? 'selected' : '' ?>>
                  2 – Perfectionnement • Échanges courts • Pas classé
                </option>

                <option value="Élémentaire" <?= $user->getLevel() === "Élémentaire" ? 'selected' : '' ?>>
                  3 – Élémentaire • Jeu loisir • Pas classé
                </option>

                <option value="Intermédiaire" <?= $user->getLevel() === "Intermédiaire" ? 'selected' : '' ?>>
                  4 – Intermédiaire • Jeu avec vitres • P25–P100 (fin tableau)
                </option>

                <option value="Confirmé" <?= $user->getLevel() === "Confirmé" ? 'selected' : '' ?>>
                  5 – Confirmé • Service-volée, smashs • P100 (milieu) / P250 (fin)
                </option>

                <option value="Avancé" <?= $user->getLevel() === "Avancé" ? 'selected' : '' ?>>
                  6 – Avancé • Jeu rapide, effets • P100 (top 4) / P250 / P500 • Top 600–900 FR
                </option>

                <option value="Expert" <?= $user->getLevel() === "Expert" ? 'selected' : '' ?>>
                  7 – Expert • Bandeja, vibora • P500 / P1000 • Top 450–2000 FR
                </option>

                <option value="Élite" <?= $user->getLevel() === "Élite" ? 'selected' : '' ?>>
                  8 – Élite • Très haute intensité • P1000–P2000 • Top 150–1000 FR
                </option>
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

          <div class="form-group">
            <label>Téléphone</label>
            <p class="profil-value"><?= e($user->getPhone()) ?></p>
          </div>

          <div class="form-group full">
            <div class="level-label-row">
              <label>Niveau</label>
              <img src="./Images/help.png" alt="Aide" class="help-icon">
            </div>
            <p class="profile-value">
              Niveau <?= e($levels[$user->getLevel()]['num']) ?> – <?= e($levels[$user->getLevel()]['label']) ?>
              <br>
              <span style="color: var(--text-soft); font-size: 0.9rem;">
                <?= e($levelDetails[$user->getLevel()]) ?>
              </span>
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

  <?php require "footer.php"; ?>
</body>

</html>