<!doctype html>
<html lang="fr-FR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PadelConnect – Mon profil</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>

    <header class="navbar">
      <div class="nav-left">
        <img src="images/logoW.png" class="logo" alt="Logo PadelConnect" />
        <h1>PadelConnect</h1>
      </div>
      <div class="nav-right">
        <a href="#">Se connecter</a>
        <a href="#" class="btn-primary">S'inscrire</a>
      </div>
    </header>

    <main class="profil-page">

      <h2 class="profil-title">Mon profil</h2>

      <div class="card">
        <p class="card-title">Informations personnelles</p>

        <form action="profil.php" method="POST">
          <div class="form-grid">

            <div class="form-group">
              <label for="prenom">Prénom</label>
              <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required />
            </div>

            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" id="nom" name="nom" placeholder="Votre nom" required />
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="Votre adresse email" required />
            </div>

            <div class="form-group">
              <label for="telephone">Téléphone</label>
              <input type="tel" id="telephone" name="telephone" placeholder="Votre numéro de téléphone" />
            </div>

            <div class="form-group full">
              <div class="niveau-label-row">
                <label for="niveau">Niveau de jeu</label>
                <button type="button" class="btn-aide" id="btn-niveau">?</button>
              </div>
              <select id="niveau" name="niveau">
                <option value="1">Niveau 1 – Débutant</option>
                <option value="2">Niveau 2 – Perfectionnement</option>
                <option value="3">Niveau 3 – Élémentaire</option>
                <option value="4">Niveau 4 – Intermédiaire</option>
                <option value="5">Niveau 5 – Confirmé</option>
                <option value="6">Niveau 6 – Avancé</option>
                <option value="7">Niveau 7 – Expert</option>
                <option value="8">Niveau 8 – Élite</option>
              </select>
            </div>

            <div class="form-group full">
              <label for="presentation">Présentation</label>
              <textarea id="presentation" name="presentation" rows="3" placeholder="Décrivez-vous en quelques mots…"></textarea>
            </div>

          </div>

          <div class="save-bar">
            <a href="index.php" class="btn-secondary">Annuler</a>
            <button type="submit" class="btn-primary btn-save">Enregistrer</button>
          </div>

        </form>
      </div>

    </main>

    <!-- MODALE NIVEAUX -->
    <div class="modal-overlay" id="modal-niveau">
      <div class="modal-content">
        <button class="modal-close" id="modal-close">✕</button>
        <img src="images/niveaupadel1.png" alt="Tableau des niveaux de padel 2025" />
      </div>
    </div>

    <?php require "footer.php"; ?>

    <script src="script.js"></script>
    <script>
      const btnNiveau = document.getElementById('btn-niveau');
      const modal     = document.getElementById('modal-niveau');
      const btnClose  = document.getElementById('modal-close');

      btnNiveau.addEventListener('click', () => modal.classList.add('open'));
      btnClose.addEventListener('click',  () => modal.classList.remove('open'));
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('open');
      });
    </script>

  </body>
</html>