import subprocess
import sys

try:
    import requests
except ImportError:
    print("📦 Installation de la bibliothèque 'requests' en cours...")
    subprocess.check_call([sys.executable, "-m", "pip", "install", "requests"])
    import requests
    print("✅ Installation réussie !\n")

from datetime import datetime

clubs = {
    "forest-hill-nanterre-la-defense": "Forest Hill (Nanterre)",
    "sportfield-courbevoie-la-defense": "Sportfield (Courbevoie)"
}

date_choisie = input("Entrez la date souhaitée (AAAA-MM-JJ) : ")

url = "https://www.anybuddyapp.com/api/v1/availabilities"
headers = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
}

print(f"\n--- Recherche des terrains de Padel pour le {date_choisie} ---")

for slug, nom_club in clubs.items():
    params = {
        "clubSlug": slug,
        "dateFrom": date_choisie,
        "dateTo": f"{date_choisie}T23:59",
        "activity": "padel"
    }

    response = requests.get(url, headers=headers, params=params)

    print(f"\n📍 {nom_club} :")

    if response.status_code == 200:
        json_data = response.json()
        slots = json_data.get("data", [])
        
        if not slots:
            print("  ❌ Aucun créneau disponible pour ce club.")
            continue

        creneaux_trouves = False
        for slot in slots:
            full_date = slot.get("startDateTime")
            time_str = full_date.split("T")[1] if "T" in full_date else full_date

            terrains_60_min = [s for s in slot.get("services", []) if s.get("duration") == 60]

            if terrains_60_min:
                creneaux_trouves = True
                nb_terrains = len(terrains_60_min)
                prix = terrains_60_min[0].get("price") / 100
                print(f"  ⏰ {time_str} : {nb_terrains} terrain(s) dispo(s) (1h) - Prix: {prix:.2f}€")
        
        if not creneaux_trouves:
            print("  ❌ Aucun créneau de 1h disponible.")
            
    else:
        print(f"  💥 Échec de la requête ({response.status_code})")