<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$controller = new TimeSlotController();

$id = (int)($_GET['id'] ?? 0);
$slot = $controller->read($id);

if (!$slot) {
    header("Location: manage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location  = $_POST['lieu']   ?? '';
    $date      = $_POST['date']   ?? '';
    // On priorise l'heure précise si elle est définie et différente de 'any'
    $exactTime = $_POST['exact_time'] ?? 'any';
    $time      = ($exactTime !== 'any') ? $exactTime : ($_POST['heure'] ?? '');

    $level     = (int)($_POST['niveau'] ?? 1);
    $duration  = (int)($_POST['duree'] ?? 90);
    $price     = (float)str_replace(',', '.', $_POST['prix'] ?? '0');
    $autoApply = !empty($_POST['auto_apply']);

    $controller->update($id, $location, $date, $time, $level, $duration, $price);
    if ($autoApply && isset($_SESSION['user']['id'])) {
        $controller->join($_SESSION['user']['id'], $id);
    }
    header("Location: manage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Modifier le match – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="profil-page">
        <h2 class="profil-title">Modifier le match</h2>

        <div class="card">
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="date-input"
                            value="<?= htmlspecialchars($slot->getDate()) ?>" required>
                    </div>

                    <?php
                    $slotTime = $slot->getTime(); // ex: "18:30" ou "08:00"
                    $currentHour = (int)substr($slotTime, 0, 2);
                    $activePlage = ($currentHour >= 7 && $currentHour < 9)
                        ? 'matin'
                        : (($currentHour >= 12 && $currentHour < 14) ? 'midi' : 'soir');

                    // Liste des heures précises pour le select
                    $hoursList = ["07:00", "08:00", "09:00", "12:00", "13:00", "14:00", "18:00", "19:00", "20:00", "21:00", "22:00"];
                    // Si l'heure actuelle correspond pile à une option, on la sélectionne, sinon "any"
                    $isExactMatch = in_array($slotTime, $hoursList);
                    ?>

                    <div class="form-group">
                        <label>Plage horaire</label>
                        <div class="plage-group">
                            <button type="button" class="plage-btn <?= $activePlage === 'matin' ? 'active' : '' ?>" data-plage="matin" data-heure="08:00">
                                <span class="plage-label">Matin</span>
                                <span class="plage-hours">7h – 9h</span>
                            </button>
                            <button type="button" class="plage-btn <?= $activePlage === 'midi' ? 'active' : '' ?>" data-plage="midi" data-heure="12:00">
                                <span class="plage-label">Midi</span>
                                <span class="plage-hours">12h – 14h</span>
                            </button>
                            <button type="button" class="plage-btn <?= $activePlage === 'soir' ? 'active' : '' ?>" data-plage="soir" data-heure="18:00">
                                <span class="plage-label">Soir</span>
                                <span class="plage-hours">18h – 22h</span>
                            </button>
                        </div>
                        <input type="hidden" id="heure" name="heure" value="<?= htmlspecialchars($slotTime) ?>">
                    </div>

                    <div class="form-group">
                        <label for="exact_time">Heure Précise</label>
                        <select id="exact_time" name="exact_time">
                            <option value="any" <?= !$isExactMatch ? 'selected' : '' ?>>Peu importe</option>
                            <?php foreach ($hoursList as $h): ?>
                                <option value="<?= $h ?>" <?= ($isExactMatch && $slotTime === $h) ? 'selected' : '' ?>>
                                    <?= substr($h, 0, 2) ?>h
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="duree">Durée</label>
                        <select id="duree" name="duree">
                            <option value="60" <?= $slot->getDuration() === 60  ? 'selected' : '' ?>>1h</option>
                            <option value="90" <?= $slot->getDuration() === 90  ? 'selected' : '' ?>>1h30</option>
                            <option value="120" <?= $slot->getDuration() === 120 ? 'selected' : '' ?>>2h</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="lieu">Lieu</label>
                        <select id="lieu" name="lieu">
                            <?php foreach (['Puteaux Île', 'Forest Hill la Défense', 'Sportfield la Défense'] as $lieu): ?>
                                <option value="<?= $lieu ?>" <?= $slot->getLocation() === $lieu ? 'selected' : '' ?>>
                                    <?= $lieu ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="niveau">Niveau requis</label>
                        <select id="niveau" name="niveau">
                            <?php
                            $niveauxLabels = [
                                1 => "1 – Débutant",
                                2 => "2 – Perfectionnement",
                                3 => "3 – Élémentaire",
                                4 => "4 – Intermédiaire",
                                5 => "5 – Confirmé",
                                6 => "6 – Avancé",
                                7 => "7 – Expert",
                                8 => "8 – Élite"
                            ];
                            foreach ($niveauxLabels as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (int)$slot->getLevel() === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Prix -->
                    <div class="form-group full">
                        <label for="prix">Prix total du terrain (€)</label>
                        <input type="number" id="prix" name="prix" min="0" step="0.01"
                            value="<?= number_format($slot->getPrice(), 2, '.', '') ?>">
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
                    <button type="button" class="btn-search-terrain" id="btn-open-terrain">
                        Changer l'horaire
                    </button>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </main>

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
        document.querySelectorAll('.plage-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.plage-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('heure').value = btn.dataset.heure;
            });
        });

        const overlay = document.getElementById('terrain-overlay');
        const btnOpen = document.getElementById('btn-open-terrain');
        const btnClose = document.getElementById('terrain-close');
        const btnCancel = document.getElementById('terrain-cancel');
        const btnUseSlot = document.getElementById('btn-use-slot');
        const resultsArea = document.getElementById('terrain-results-area');

        let selectedSlot = null;

        btnOpen.addEventListener('click', async () => {
            const dateInput = document.getElementById('date');
            const locationSelect = document.getElementById('lieu');
            const durationSelect = document.getElementById('duree');
            const heureInput = document.getElementById('heure');
            const exactTimeSelect = document.getElementById('exact_time');

            const date = dateInput.value;
            const locationValue = locationSelect.value;
            const durationValue = durationSelect.value;

            const plageValue = (exactTimeSelect && exactTimeSelect.value !== 'any') ? exactTimeSelect.value : heureInput.value;

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
            if (plageValue.includes(':') && plageValue !== '08:00' && plageValue !== '12:00' && plageValue !== '18:00') {
                return {
                    min: plageValue,
                    max: plageValue
                };
            }
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

            document.getElementById('heure').value = selectedSlot.heure;

            const exactTimeSelect = document.getElementById('exact_time');
            if (exactTimeSelect) {
                const optionExists = [...exactTimeSelect.options].some(opt => opt.value === selectedSlot.heure);
                exactTimeSelect.value = optionExists ? selectedSlot.heure : 'any';
            }

            const [h] = selectedSlot.heure.split(':').map(Number);
            let plage = 'soir';
            if (h >= 7 && h < 9) {
                plage = 'matin';
            }
            if (h >= 12 && h < 14) {
                plage = 'midi';
            }

            document.querySelectorAll('.plage-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.plage === plage);
            });

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