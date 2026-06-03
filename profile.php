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
        <div class="form-grid">

          <div class="form-group">
            <label>Prénom</label>
            <input type="text" value="Prénom" />
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

          <div class="form-group">
            <label>Nom</label>
            <input type="text" value="Nom" />
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" value="monadressemail@example.com" readonly />
          </div>

          <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" value="+33 6 12 34 56 78" />
          </div>

          <div class="form-group full">
            <div class="niveau-label-row">
              <label>Niveau de jeu</label>
              <button type="button" class="btn-aide" id="btn-niveau">?</button>
            </div>
            <select>
              <option>Niveau 1 – Débutant</option>
              <option>Niveau 2 – Perfectionnement</option>
              <option>Niveau 3 – Élémentaire</option>
              <option selected>Niveau 4 – Intermédiaire</option>
              <option>Niveau 5 – Confirmé</option>
              <option>Niveau 6 – Avancé</option>
              <option>Niveau 7 – Expert</option>
              <option>Niveau 8 – Élite</option>
            </select>
          </div>

          <div class="form-group full">
            <label>Présentation</label>
            <textarea rows="3" placeholder="Décrivez-vous en quelques mots…"></textarea>
          </div>

        </div>

        <div class="save-bar">
          <button class="btn-secondary">Annuler</button>
          <button class="btn-primary btn-save">Enregistrer</button>
        </div>
      </div>

    </main>

    <!-- MODALE NIVEAUX -->
    <div class="modal-overlay" id="modal-niveau">
      <div class="modal-content">
        <button class="modal-close" id="modal-close">✕</button>
        <img src="images/niveaupadel.png-1.png" alt="Tableau des niveaux de padel 2025" />
      </div>
    </div>

    <footer class="footer">
      <div class="footer-left">
        <img src="images/logoW.png" class="logo" alt="Logo PadelConnect" />
        <span>PadelConnect</span>
      </div>
      <div class="footer-links">
        <a href="#">CGU</a>
        <a href="#">Confidentialité</a>
        <a href="#">Cookies</a>
      </div>
      <div class="footer-right">© 2026 PadelConnect – All rights reserved</div>
    </footer>

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
        <?php require "footer.php"; ?>
  </body>

</html>