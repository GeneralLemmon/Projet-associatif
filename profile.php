<!doctype html>
<html lang="fr-FR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PadelConnect</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header class="navbar">
      <div class="nav-left">
        <img src="images/logoW.png" alt="Logo PadelConnect" class="logo" />
        <h1>PadelConnect</h1>
      </div>

      <div class="nav-right">
        <a href="#">Se connecter</a>
        <a href="#" class="btn-primary">S'inscrire</a>
      </div>
    </header>


    <script src="script.js"></script>
    <div class="card">
      <p class="card-title">Informations personnelles</p>
      <div class="form-grid">
        <div class="form-group">
          <label>Prénom</label>
          <input type="text" value="Prénom" />
        </div>
        <div class="form-group">
          <label>Nom</label>
          <input type="text" value="Nom" />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" value="monadressmail@example.com" readonly />
        </div>
        <div class="form-group">
          <label>Téléphone</label>
          <input type="tel" value="+33 6 12 34 56 78" />
        </div>
        <div class="form-group">
          <label>Niveau de jeu</label>
          <select>
            <option>Niveau 1 – Débutant</option>
            <option>Niveau 2 – Perfectionnement</option>
            <option>Niveau 3 – Élémentaire</option>
            <option>Niveau 4 – Intermédiaire</option>
            <option>Niveau 5 – Confirmé</option>
            <option>Niveau 6 – Avancé</option>
            <option>Niveau 7 – Expert</option>
            <option>Niveau 8 – Élite</option>
          </select>
          <div class="niveau-info">
            <img
              src="images/niveaupadel.png-1.png"
              alt="Tableau des niveaux de padel 2025"
              class="niveau-img"
            />
          </div>
        </div>
        <div class="form-group full">
          <label>Présentation</label>
          <textarea rows="3">Présentation</textarea>
        </div>
      </div>
      <br />
      <div class="save-bar">
        <button class="btn-outline">Annuler</button>
        <button class="btn-filled">Enregistrer</button>
      </div>
    </div>
        <?php require "footer.php"; ?>
  </body>

</html>