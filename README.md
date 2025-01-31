Job Portal
Prérequis
Laragon ou un serveur local similaire pour le backend.
Node.js et npm pour le frontend.
Étapes d'installation
1. Backend
Téléchargez le fichier job_portal.sql et importez-le dans phpMyAdmin pour créer la base de données.
Placez les fichiers du backend dans le dossier www/offre (vous avez cree la doussier offre sur www) de Laragon (ou le répertoire correspondant si vous utilisez un autre serveur).
2. Frontend
Clonez le projet depuis GitHub :
git clone https://github.com/Taha3301/stageDePerfectionnement.git
Accédez au dossier du projet cloné :
cd stageDePerfectionnement
Installez les dépendances du frontend avec npm :
npm install
3. Lancer le projet
Démarrez votre serveur Laragon (ou un autre serveur local).
Exécutez le frontend Vue.js en utilisant la commande suivante dans votre terminal (VS Code ou autre) :
npm run dev
Cela devrait être suffisant pour démarrer l'application. Vous pouvez maintenant accéder à l'interface et interagir avec le portail d'offres d'emploi !
