# Mediatekformation
## Présentation
Ce site, développé avec Symfony 6.4, permet d'accéder aux vidéos d'auto-formation proposées par une chaîne de médiathèques et qui sont aussi accessibles sur YouTube.<br> 
Voici le lien du dépôt d'origine contenant la présentation de l'application d'origine : https://github.com/CNED-SLAM/mediatekformation
## Fonctionnalités ajoutées
Une page d'administration a été ajoutée, permettant la gestion des formations, des playlists ainsi que des catégories.<br>
Une colonne permettant le tri sur le nombre de formations d'une playlist a également été ajoutée, ainsi que cette même information sur la page d'une playlist.<br>
![Sans-titre](https://github.com/user-attachments/assets/adef19dd-25ff-4c0f-aaa3-b48e355ce3fe)
## Les différentes pages
### Page 1 : Playlists, front office
Cette page a été modifiée afin d'ajouter une colonne permettant le tri sur le nombre de formations d'une playlist.<br>
![1](https://github.com/user-attachments/assets/fea43361-dab9-456e-b779-6fdfeba5d6f7)
### Page 2 : Authentification
Cette page permet à l'administrateur de se connecter à la page d'administration du site en utilisant son couple login password.<br>
Elle est accessible en ajoutant /login à l'URL.<br>
![2](https://github.com/user-attachments/assets/61cf5a0b-02cc-4339-8bfc-205d13ba52d0)
### Page 3 : Formations, back office
Cette page est accessible depuis le menu de navigation de la partie back office. Elle reprend l'interface front office, ajoutant un bouton d'ajout de formation, un bouton de modification et un bouton de suppression par formation, ainsi qu'un bouton de déconnexion.
Cliquer sur le bouton de suppression affiche un message de validation.<br>
![3](https://github.com/user-attachments/assets/c3cbdde2-4952-4d95-a3d7-8294791756f9)
### Page 4 : Formulaire d'ajout/modification d'une formation
Ce formulaire apparaît lorsque l'administrateur clique sur le bouton d'ajout ou de modification de formation (la seule différence étant que, lors d'une modification, les champs sont préremplis). Il peut alors remplir les champs correspondants, seules la description et la catégorie n'étant pas obligatoires.
Le bouton enregistrer permet la validation des saisies, tandis que le bouton retour à la liste permet d'annuler les modifications.<br>
![4](https://github.com/user-attachments/assets/3fd3ecc4-cac8-45b9-9bc9-091cefafe8b0)
### Page 5 : Playlists, back office
Cette page est accessible depuis le menu de navigation de la partie back office. Elle reprend l'interface front office, ajoutant les mêmes boutons et fonctionnalités que sur la page des formations.<br>
![5](https://github.com/user-attachments/assets/b7ea6e66-8c37-4afe-befe-783bc6da86b7)
### Page 6 : Formulaire d'ajout/modification d'une playlist
Ce formulaire apparaît lorsque l'administrateur clique sur le bouton d'ajout ou de modification de formation. Cette fois, la modification affiche aussi la liste des formations qu'elle contient, en plus des champs préremplis.
Le seul champ obligatoire est le champ nom, le bouton enregistrer permet la validation des saisies, tandis que le bouton retour à la liste permet d'annuler les modifications.<br>
![6](https://github.com/user-attachments/assets/fe61a4de-0788-46ea-87f7-8d2afe6e18bb)
### Page 7 : Catégories, back office
Cette page est accessible depuis le menu de navigation de la partie back office. Elle reprend l'interface front office, ajoutant un bouton de suppression par catégorie (uniquement cliquable si la catégorie ne contient pas de formations), ainsi qu'un mini formulaire d'ajout de catégorie, contenant un champ nom et un bouton d'ajout.<br>
![7](https://github.com/user-attachments/assets/87b31019-fc92-4ae3-82a1-7e59e47e3b3e)
## Test de l'application en local
- Vérifier que Composer, Git et Wampserver (ou équivalent) sont installés sur l'ordinateur.
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent) puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode admin, se positionner dans le dossier du projet et taper "composer install" pour reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL en root sans mot de passe et créer la BDD 'mediatekformation'.<br>
- Récupérer le fichier mediatekformation.sql en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- De préférence, ouvrir l'application dans un IDE professionnel. L'adresse pour la lancer est : http://localhost/mediatekformation/public/index.php<br>
## Test de l'application en ligne
L'application est disponible à l'adresse présente sur la fiche descriptive de l'atelier, la page d'authentification est disponible en ajoutant /login à cette dernière.<br>
La documentation est disponible en y ajoutant plutôt /mediatekformation/Documentation .
