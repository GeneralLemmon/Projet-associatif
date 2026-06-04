import http.server
import socketserver
import json
import urllib.parse
import urllib.request

clubs = {
    "forest-hill-nanterre-la-defense": "Forest Hill (Nanterre)",
    "sportfield-courbevoie-la-defense": "Sportfield (Courbevoie)"
}
url_api = "https://www.anybuddyapp.com/api/v1/availabilities"
user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"

class PadelRequestHandler(http.server.BaseHTTPRequestHandler):
    def do_GET(self):
        parsed_url = urllib.parse.urlparse(self.path)
        query_params = urllib.parse.parse_qs(parsed_url.query)
        
        if 'date' in query_params:
            date_choisie = query_params['date'][0]
            results = {}

            for slug, nom_club in clubs.items():
                params = {
                    "clubSlug": slug,
                    "dateFrom": date_choisie,
                    "dateTo": f"{date_choisie}T23:59",
                    "activity": "padel"
                }
                full_url = f"{url_api}?{urllib.parse.urlencode(params)}"
                
                try:
                    req = urllib.request.Request(full_url, headers={"User-Agent": user_agent})
                    with urllib.request.urlopen(req) as response:
                        if response.status == 200:
                            json_data = json.loads(response.read().decode("utf-8"))
                            slots = json_data.get("data", [])
                            club_slots = []
                            
                            for slot in slots:
                                full_date = slot.get("startDateTime", "")
                                time_str = full_date.split("T")[1][:5] if "T" in full_date else full_date
                                
                                try:
                                    heures, minutes = map(int, time_str.split(":"))
                                    minutes_depuis_minuit = heures * 60 + minutes
                                except ValueError:
                                    continue
                                
                                valide = (
                                    (7 * 60 <= minutes_depuis_minuit < 9 * 60) or
                                    (12 * 60 <= minutes_depuis_minuit < 14 * 60) or
                                    (18 * 60 <= minutes_depuis_minuit < 22 * 60)
                                )
                                
                                if not valide:
                                    continue
                                
                                # Parcourir TOUTES les durées proposées (60, 90, 120...)
                                services = slot.get("services", [])
                                durees_traitees = set()
                                
                                for s in services:
                                    duree_min = s.get("duration", 60)
                                    prix_reel = s.get("price", 0) / 100
                                    
                                    cle_unique = f"{duree_min}_{prix_reel}"
                                    if cle_unique in durees_traitees:
                                        continue
                                    durees_traitees.add(cle_unique)
                                    
                                    # Formatage de la durée
                                    if duree_min == 60:
                                        texte_duree = "1h"
                                    elif duree_min == 90:
                                        texte_duree = "1h30"
                                    elif duree_min == 120:
                                        texte_duree = "2h"
                                    else:
                                        texte_duree = f"{duree_min}min"
                                        
                                    club_slots.append({
                                        "heure": time_str,
                                        "duree": texte_duree,
                                        "prix": prix_reel
                                    })
                                    
                            results[nom_club] = club_slots
                        else:
                            results[nom_club] = "erreur"
                except Exception as e:
                    results[nom_club] = "erreur"

            self.send_response(200)
            self.send_header("Content-Type", "application/json")
            self.send_header("Access-Control-Allow-Origin", "*")
            self.end_headers()
            self.wfile.write(json.dumps(results).encode("utf-8"))
        else:
            self.send_response(400)
            self.end_headers()

    def log_message(self, format, *args):
        return

PORT = 8000
socketserver.TCPServer.allow_reuse_address = True
with socketserver.TCPServer(("", PORT), PadelRequestHandler) as httpd:
    print(f"🚀 Serveur Multi-Durées actif sur le port {PORT} !")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n👋 Serveur arrêté.")