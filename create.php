<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$controller = new TimeSlotController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location   = $_POST['lieu']   ?? '';
    $date       = $_POST['date']   ?? '';
    $time       = $_POST['heure']  ?? '';
    $exactTime = $_POST['exact_time']  ?? '';
    $level      = (int)($_POST['niveau'] ?? 1);
    $duration   = (int)($_POST['duree'] ?? 90);
    $price      = (float)str_replace(',', '.', $_POST['prix'] ?? '0');
    $autoApply  = !empty($_POST['auto_apply']);

    if ($location && $date && $time) {
        $slotId = $controller->create($location, $date, $time, $level, $duration, $price);
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if ($autoApply && $userId > 0) {
            $controller->join($userId, $slotId);
        }
    }
    header("Location: manage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Créer un match – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="profil-page">
        <h2 class="profil-title">Créer un nouveau match</h2>

        <div class="card">
            <form action="create.php" method="POST">
                <div class="form-grid">

                    <!-- Date -->
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="date-input" required>
                    </div>

                    <!-- Plage horaire -->
                    <div class="form-group">
                        <label>Plage horaire</label>
                        <div class="plage-group">
                            <button type="button" class="plage-btn" data-plage="matin" data-heure="08:00">
                                <span class="plage-label">Matin</span>
                                <span class="plage-hours">7h – 9h</span>
                            </button>
                            <button type="button" class="plage-btn" data-plage="midi" data-heure="12:00">
                                <span class="plage-label">Midi</span>
                                <span class="plage-hours">12h – 14h</span>
                            </button>
                            <button type="button" class="plage-btn active" data-plage="soir" data-heure="18:00">
                                <span class="plage-label">Soir</span>
                                <span class="plage-hours">18h – 22h</span>
                            </button>
                        </div>
                        <!-- champ caché envoyé avec le form -->
                        <input type="hidden" id="heure" name="heure" value="18:00">
                    </div>

                    <div class="form-group">
                        <label>Heure Précise</label>
                        <select id="exact_time" name="exact_time">
                            <option value="any" selected>Peu importe</option>
                            <option value="07:00">07h</option>
                            <option value="08:00">08h</option>
                            <option value="09:00">09h</option>
                            <option value="12:00">12h</option>
                            <option value="13:00">13h</option>
                            <option value="14:00">14h</option>
                            <option value="18:00">18h</option>
                            <option value="19:00">19h</option>
                            <option value="20:00">20h</option>
                            <option value="21:00">21h</option>
                            <option value="22:00">22h</option>
                        </select>

                    </div>

                    <!-- Durée -->
                    <div class="form-group">
                        <label for="duree">Durée</label>
                        <select id="duree" name="duree">
                            <option value="any" selected>Peu importe</option>
                            <option value="60">1h</option>
                            <option value="90">1h30</option>
                            <option value="120">2h</option>
                        </select>
                    </div>

                    <!-- Lieu -->
                    <div class="form-group full">
                        <label for="lieu">Lieu</label>
                        <select id="lieu" name="lieu">
                            <option value="Peu importe">Peu importe</option>
                            <option value="Forest Hill la Défense">Forest Hill (Nanterre)</option>
                            <option value="Sportfield la Défense">Sportfield (Courbevoie)</option>
                        </select>
                    </div>

                    <!-- Niveau requis -->
                    <div class="form-group full">
                        <label for="niveau">Niveau requis</label>
                        <select id="niveau" name="niveau">
                            <option value="1">1 – Débutant</option>
                            <option value="2">2 – Perfectionnement</option>
                            <option value="3">3 – Élémentaire</option>
                            <option value="4">4 – Intermédiaire</option>
                            <option value="5">5 – Confirmé</option>
                            <option value="6">6 – Avancé</option>
                            <option value="7">7 – Expert</option>
                            <option value="8">8 – Élite</option>
                        </select>
                    </div>

                    <!-- Prix (rempli automatiquement ou manuellement) -->
                    <div class="form-group full">
                        <label for="prix">Prix total du terrain (€)</label>
                        <input type="number" id="prix" name="prix" min="0" step="0.01"
                            placeholder="Ex : 24.00" value="0">
                    </div>

                </div>

                <div class="slot-summary" id="slot-summary">
                    <strong>Créneau sélectionné :</strong>
                    <div id="slot-summary-text" class="slot-summary-text"></div>
                </div>
                <div id="slot-auto-apply">
                    <label>
                        <input type="checkbox" id="auto-apply-checkbox" name="auto_apply" value="1">
                        S'inscrire automatiquement
                    </label>
                </div>

                <div class="save-bar">
                    <a href="manage.php" class="btn-secondary">Annuler</a>

                    <!-- Nouveau bouton recherche terrain -->
                    <button type="button" class="btn-search-terrain" id="btn-open-terrain">
                        Rechercher un créneau
                    </button>

                    <button type="submit" class="btn-primary">Créer le match</button>
                </div>
            </form>
        </div>
    </main>

    <!-- ═══════════════════════════════════
         POP-UP RECHERCHE DE TERRAIN
    ═══════════════════════════════════ -->
    <div class="terrain-overlay" id="terrain-overlay">
        <div class="terrain-modal">

            <div class="terrain-modal-header">
                <h3>Rechercher un terrain disponible</h3>
                <button class="terrain-modal-close" id="terrain-close">×</button>
            </div>

            <div class="terrain-modal-body">

                <div id="terrain-results-area">
                    <p class="terrain-info small">
                        Les créneaux sont filtrés automatiquement en fonction de la plage horaire et de la durée choisies.
                    </p>
                </div>

            </div>

            <div class="terrain-modal-footer">
                <button class="btn-secondary" id="terrain-cancel">Fermer</button>
                <button class="btn-primary" id="btn-use-slot" disabled>Utiliser ce créneau</button>
            </div>

        </div>
    </div>

    <?php require "footer.php"; ?>

    <script>
        /* ═══════════════════════════
       PLAGE HORAIRE
    ═══════════════════════════ */
        document.querySelectorAll('.plage-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.plage-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('heure').value = btn.dataset.heure;
            });
        });

        /* ═══════════════════════════
           POPUP TERRAIN
        ═══════════════════════════ */
        const overlay = document.getElementById('terrain-overlay');
        const btnOpen = document.getElementById('btn-open-terrain');
        const btnClose = document.getElementById('terrain-close');
        const btnCancel = document.getElementById('terrain-cancel');
        const btnUseSlot = document.getElementById('btn-use-slot');
        const resultsArea = document.getElementById('terrain-results-area');

        let selectedSlot = null; // { heure, duree, club, prix }

        btnOpen.addEventListener('click', async () => {
            const dateInput = document.getElementById('date');
            const locationSelect = document.getElementById('lieu');
            const durationSelect = document.getElementById('duree');
            const heureInput = document.getElementById('heure');

            const date = dateInput.value;
            const locationValue = locationSelect.value;
            const durationValue = durationSelect.value;
            const plageValue = heureInput.value;

            if (!date) {
                alert('Sélectionne une date d\'abord.');
                return;
            }

            hideSlotSummary();
            overlay.classList.add('open');
            document.body.classList.add('modal-open');
            resultsArea.innerHTML = `
            <div class="terrain-loading">
                <div class="spinner"></div>
                <p>Recherche des créneaux en cours…</p>
            </div>`;
            btnUseSlot.disabled = true;
            selectedSlot = null;

            await fetchSlots(date, locationValue, durationValue, plageValue);
        });

        function closeTerrainOverlay() {
            overlay.classList.remove('open');
            document.body.classList.remove('modal-open');
        }

        [btnClose, btnCancel].forEach(b => b.addEventListener('click', closeTerrainOverlay));

        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeTerrainOverlay();
        });

        async function fetchSlots(date, locationValue, durationValue, plageValue) {
            try {
                const res = await fetch(`http://localhost:8000/?date=${date}`);
                const data = await res.json();
                renderResults(data, date, locationValue, durationValue, plageValue);
            } catch (e) {
                resultsArea.innerHTML = `
                <p class="terrain-error">
                    ❌ Impossible de contacter le serveur local (port 8000).<br>
                    <small>Vérifie que <code>server.py</code> est bien lancé.</small>
                </p>`;
            }
        }

        function parseDuration(value) {
            if (!value || value === 'any') return null;
            if (!Number.isNaN(Number(value))) return Number(value);
            const trimmed = value.toString().trim();
            const match = trimmed.match(/^(\d+)(?:h(?:(\d+))?)?$/);
            if (match) {
                const hours = Number(match[1]) || 0;
                const minutes = Number(match[2] || 0) || 0;
                return hours * 60 + minutes;
            }
            return null;
        }

        function getPlageRange(plageValue) {
            if (plageValue === '08:00') return {
                min: '07:00',
                max: '09:59'
            };
            if (plageValue === '12:00') return {
                min: '12:00',
                max: '14:59'
            };
            return {
                min: '18:00',
                max: '22:59'
            };
        }

        function isTimeInRange(time, min, max) {
            return time >= min && time <= max;
        }

        function renderResults(data, date, locationValue, durationValue, plageValue) {
            const locationMap = {
                'Forest Hill la Défense': 'forest-hill-nanterre-la-defense',
                'Sportfield la Défense': 'sportfield-courbevoie-la-defense',
                'Peu importe': 'both'
            };

            const filterSlug = locationMap[locationValue] || 'both';

            const selectedDuration = parseDuration(durationValue);
            const plageRange = getPlageRange(plageValue);

            let html = '<div class="terrain-results">';
            let totalSlots = 0;

            for (const [nomClub, slots] of Object.entries(data)) {

                // Filtre club
                if (filterSlug !== 'both') {
                    const isForest = filterSlug === 'forest-hill-nanterre-la-defense' && nomClub.includes('Forest');
                    const isSport = filterSlug === 'sportfield-courbevoie-la-defense' && nomClub.includes('Sportfield');
                    if (!isForest && !isSport) continue;
                }

                const availableSlots = Array.isArray(slots) ?
                    slots.filter(slot => {
                        const slotDuration = parseDuration(slot.duree);
                        const slotTime = slot.heure || '';
                        if (selectedDuration !== null && slotDuration !== selectedDuration) return false;
                        if (!isTimeInRange(slotTime, plageRange.min, plageRange.max)) return false;
                        return true;
                    }) : [];

                const venueType = nomClub.includes('Forest') ? 'intérieur' : nomClub.includes('Sportfield') ? 'extérieur' : '';
                html += `<div class="club-section"><h4>${nomClub}</h4>`;
                if (venueType) {
                    html += `<span class="club-venue">Terrains ${venueType}</span>`;
                }

                if (availableSlots.length === 0) {
                    html += `<p class="no-slots">Aucun créneau disponible pour cette plage ou cette durée.</p>`;
                } else {
                    html += `<div class="slots-grid">`;
                    availableSlots.forEach(slot => {
                        const key = `${nomClub}||${slot.heure}||${slot.duree}||${slot.prix}`;
                        html += `
                        <div class="slot-chip"
                             data-key="${encodeURIComponent(key)}"
                             data-club="${encodeURIComponent(nomClub)}"
                             data-heure="${slot.heure}"
                             data-duree="${slot.duree}"
                             data-prix="${slot.prix}"
                             data-date="${date}">
                            <span class="chip-heure">${slot.heure}</span>
                            <span class="chip-duree">${slot.duree}</span>
                            <span class="chip-prix">${slot.prix}€</span>
                        </div>`;
                        totalSlots++;
                    });
                    html += `</div>`;
                }
                html += `</div>`;
            }

            if (totalSlots === 0) {
                html = `<p class="no-slots">Aucun créneau disponible pour cette date, cette plage horaire ou cette durée.</p>`;
            }

            html += '</div>';
            resultsArea.innerHTML = html;

            resultsArea.querySelectorAll('.slot-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    resultsArea.querySelectorAll('.slot-chip').forEach(c => c.classList.remove('selected'));
                    chip.classList.add('selected');
                    selectedSlot = {
                        heure: chip.dataset.heure,
                        duree: chip.dataset.duree,
                        prix: chip.dataset.prix,
                        club: decodeURIComponent(chip.dataset.club),
                        date: chip.dataset.date
                    };
                    btnUseSlot.disabled = false;
                });
            });
        }

        function updateSlotSummary() {
            const summary = document.getElementById('slot-summary');
            const summaryText = document.getElementById('slot-summary-text');
            const autoApplyContainer = document.getElementById('slot-auto-apply');
            if (!selectedSlot || !summary || !summaryText || !autoApplyContainer) return;

            summary.style.display = 'block';
            autoApplyContainer.style.display = 'block';
            summaryText.innerHTML = `
            Date : <strong>${selectedSlot.date}</strong><br>
            Heure : <strong>${selectedSlot.heure}</strong><br>
            Durée : <strong>${selectedSlot.duree}</strong><br>
            Club : <strong>${selectedSlot.club}</strong><br>
            Prix : <strong>${selectedSlot.prix}€</strong>`;
        }

        function hideSlotSummary() {
            const summary = document.getElementById('slot-summary');
            const summaryText = document.getElementById('slot-summary-text');
            const autoApplyContainer = document.getElementById('slot-auto-apply');
            const autoApplyCheckbox = document.getElementById('auto-apply-checkbox');
            if (!summary || !summaryText || !autoApplyContainer || !autoApplyCheckbox) return;
            summary.style.display = 'none';
            summaryText.innerHTML = '';
            autoApplyCheckbox.checked = false;
            autoApplyContainer.style.display = 'none';
        }

        function applySelectedSlot() {
            if (!selectedSlot) return;

            const dateInput = document.getElementById('date');
            dateInput.value = selectedSlot.date;

            const [h] = selectedSlot.heure.split(':').map(Number);
            let plage = 'soir',
                heureValue = '18:00';
            if (h >= 7 && h < 9) {
                plage = 'matin';
                heureValue = '07:00';
            }
            if (h >= 12 && h < 14) {
                plage = 'midi';
                heureValue = '12:00';
            }

            document.querySelectorAll('.plage-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.plage === plage);
            });
            document.getElementById('heure').value = selectedSlot.heure; // heure exacte

            const dureeMap = {
                '1h': '60',
                '1h30': '90',
                '2h': '120'
            };
            const dureeSelect = document.getElementById('duree');
            if (dureeMap[selectedSlot.duree]) dureeSelect.value = dureeMap[selectedSlot.duree];

            const lieuSelect = document.getElementById('lieu');
            [...lieuSelect.options].forEach(opt => {
                if (opt.text.includes('Forest') && selectedSlot.club.includes('Forest')) lieuSelect.value = opt.value;
                if (opt.text.includes('Sportfield') && selectedSlot.club.includes('Sportfield')) lieuSelect.value = opt.value;
            });

            // Remplir le champ prix
            const prixInput = document.getElementById('prix');
            if (prixInput && selectedSlot.prix) prixInput.value = selectedSlot.prix;

            closeTerrainOverlay();
            updateSlotSummary();
        }

        btnUseSlot.addEventListener('click', () => {
            applySelectedSlot();
        });
    </script>
</body>

</html>