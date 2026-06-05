🎾 Contexte et Objectif

Au sein d'ACENSI, les collaborateurs souhaitent organiser des parties de padel. Cependant, la planification de ces rencontres se heurte à deux contraintes majeures :  

- L'organisation et les joueurs : Il est difficile de s'organiser efficacement et d'avoir le nombre exact de joueurs requis.
- Le niveau des participants : Une partie de padel nécessite exactement 4 joueurs. Afin de garantir le plaisir de chacun, il est essentiel que les niveaux des joueurs soient équilibrés pour éviter des écarts trop importants qui rendraient le jeu moins agréable.

Ce projet consiste à développer une plateforme simple où un organisateur peut proposer des créneaux avec des horaires accessibles, et où les personnes intéressées peuvent s'inscrire.

🛠️ Fonctionnalités Principales 

- Gestion des créneaux : L'application permet d'annoncer les dates et heures disponibles en fonction des calendriers des complexes alentours.
- Inscription aux matchs : Les utilisateurs peuvent s'inscrire sur un créneau ouvert. Le match est complet dès que 4 joueurs sont enregistrés.
- Correspondance des niveaux : En s'inscrivant à un créneau, l'utilisateur n'indique pas son niveau. Ce sont les créneaux directement proposés qui correspondent au niveau de chacun et à ses critères.
- Gestion des paiements : L'application ne gère pas les paiements. En revanche, elle redirige l'organisateur vers le site de réservation pour finaliser l'opération.

💻 Spécificités Techniques

- Pour gérer l'hébergement de la base de données, il faudra modifier les informations suivantes dans le fichier Database.php :
$dbName = "padelconnect";
$port = 3306;
$username = "root";
$password = "";
- Pour se mettre admin, mettre 1 dans la base de donnée dans la colonne is_admin de la table user
