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
  $selectedLevel = $_POST['level'];
  $user->setLevel($selectedLevel);
  $minLevel = (int)($_POST['min_level'] ?? 1);
  $levelNumber = levelToNumber($selectedLevel);

  if ($minLevel > $levelNumber) {
    $message = "Le niveau minimum doit être inférieur ou égal à votre niveau.";
  } else {
    if (!empty($_POST['password'])) {
      if ($_POST['password'] === $_POST['password_confirm']) {
        $user->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
      } else {
        $message = "Les mots de passe ne correspondent pas.";
      }
    }

    if (empty($message)) {
      $user->setMin_level($minLevel);
      $userController->update($user);

      $_SESSION['user']['firstName'] = $user->getFirstName();
      $_SESSION['user']['lastName']  = $user->getLastName();
      $_SESSION['user']['name']      = $user->getFullName();
      $_SESSION['user']['level']     = $user->getLevel();
      $_SESSION['user']['minLevel']  = $user->getMinLevel();

      header("Location: profile.php");
      exit;
    }
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
          <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
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
              <div class="level-label-row">
                <label for="level">Niveau</label>
                <img src="./Images/help.png" alt="Aide" id="btn-level-help" class="help-icon">
              </div>

              <?php
              $selectedLevel = $user->getLevel();
              if (is_numeric($selectedLevel)) {
                foreach ($levels as $text => $info) {
                  if ((string)$info['num'] === (string)$selectedLevel) {
                    $selectedLevel = $text;
                    break;
                  }
                }
              }
              ?>

              <select id="level" name="level">
                <?php foreach ($levels as $text => $info): ?>
                  <option value="<?= e($text) ?>" <?= $selectedLevel === $text ? 'selected' : '' ?>>
                    Niveau <?= $info['num'] ?> – <?= e($info['label']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group full">
              <label for="min_level">Niveau minimum souhaité</label>
              <select id="min_level" name="min_level">
                <?php $currentMin = $user->getMinLevel(); ?>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                  <option value="<?= $i ?>" <?= (int)$currentMin === $i ? 'selected' : '' ?>>
                    <?= $i ?> – <?= $levels[array_keys($levels)[$i - 1]]['label'] ?>
                  </option>
                <?php endfor; ?>
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
              <img src="./Images/help.png" alt="Aide" id="btn-level-help" class="help-icon">
            </div>
            <?php
            $storedLevel = $user->getLevel();
            $levelInfo = null;
            if (isset($levels[$storedLevel])) {
              $levelInfo = $levels[$storedLevel];
            } elseif (is_numeric($storedLevel)) {
              foreach ($levels as $info) {
                if ((string)$info['num'] === (string)$storedLevel) {
                  $levelInfo = $info;
                  break;
                }
              }
            }
            ?>
            <p class="profile-value">
              <?php if ($levelInfo): ?>
                Niveau <?= e($levelInfo['num']) ?> – <?= e($levelInfo['label']) ?>
              <?php else: ?>
                Niveau inconnu
              <?php endif; ?>
            </p>
          </div>

          <div class="form-group full">
            <label>Niveau minimum souhaité</label>
            <p class="profile-value">Niveau <?= e($user->getMinLevel()) ?></p>
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
        <button id="level-close" style="background: none;
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