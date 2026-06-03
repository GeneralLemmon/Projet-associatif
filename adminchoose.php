<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Padel Scanner</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>
    <div class="container">
        <h1>🎾 Recherche de Créneaux Padel</h1>

        <div class="search-form">
            <label style="font-weight: bold;">Choisir une date :</label>
            <input type="date" id="dateInput" required>
            <button onclick="rechercher()">Rechercher</button>
        </div>

        <div class="grid" id="resultsGrid" style="display: none;"></div>
    </div>

    <script>
        document.getElementById('dateInput').value = new Date().toISOString().split('T')[0];

        function obtenirPlageHoraire(heureStr) {
            const heure = parseInt(heureStr.split(':')[0], 10);
            if (heure >= 7 && heure < 9) return "MATIN";
            if (heure >= 12 && heure < 14) return "MIDI";
            if (heure >= 18 && heure < 22) return "SOIR";
            return "AUTRE";
        }

        async function rechercher() {
            const date = document.getElementById('dateInput').value;
            if (!date) return;

            const grid = document.getElementById('resultsGrid');
            grid.style.display = 'grid';
            grid.innerHTML = '<p class="no-slot" style="grid-column: span 2; text-align: center;">Connexion au script Python...</p>';

            try {
                const response = await fetch(`http://localhost:8000/?date=${date}`);
                const data = await response.json();

                grid.innerHTML = '';

                for (const [nomClub, creneaux] of Object.entries(data)) {
                    let typeClub = "";
                    if (nomClub.includes("Sportfield")) {
                        typeClub = "Terrain extérieur";
                    } else if (nomClub.includes("Forest Hill")) {
                        typeClub = "Terrain intérieur";
                    }

                    let clubHtml = `
                    <div class="club-card">
                        <div class="club-title">📍 ${nomClub}</div>
                        <span class="club-type">${typeClub}</span>
                `;

                    if (creneaux === "erreur") {
                        clubHtml += `<p class="no-slot" style="color: #c0392b;">Erreur de l'API Anybuddy.</p>`;
                    } else if (creneaux.length === 0) {
                        clubHtml += `<p class="no-slot">Aucun créneau disponible dans vos tranches horaires.</p>`;
                    } else {
                        let plagePrecedente = null;

                        creneaux.forEach(c => {
                            const plageActuelle = obtenirPlageHoraire(c.heure);
                            let classeSeparateur = "";

                            if (plagePrecedente !== null && plagePrecedente !== plageActuelle) {
                                classeSeparateur = " slot-separator";
                            }

                            clubHtml += `
                            <div class="slot${classeSeparateur}">
                                <span class="slot-time">⏰ ${c.heure}</span>
                                <span class="slot-info">⏱️ ${c.duree} | <b>${c.prix.toFixed(2)}€</b></span>
                            </div>
                        `;

                            plagePrecedente = plageActuelle;
                        });
                    }

                    clubHtml += `</div>`;
                    grid.innerHTML += clubHtml;
                }

            } catch (error) {
                grid.innerHTML = '<p class="no-slot" style="color: #c0392b; grid-column: span 2; text-align: center;">❌ Erreur : Relance server.py dans VS Code !</p>';
            }
        }
    </script>
    <?php require "footer.php"; ?>
</body>

</html>