# Projet d'API RESTful pour les Problèmes Zabbix

## Table des matières
- [Lancer le projet](#lancer-le-projet)
- [Conception](#conception)
- [Remarques](#remarques)
- [Références](#références)

## Lancer le projet

### Service Web (API)
1. Placez les fichiers de l'API dans votre répertoire web
2. Configurez votre serveur web pour gérer les fichiers PHP
3. URL d'entrée : `http://localhost/api/problems`

### Client
1. Exécutez le script client : `php client.php`
2. Le client va automatiquement :
   - Récupérer tous les problèmes de niveau warning
   - Les passer en niveau high
   - Journaliser les changements dans `update_problems_severity.log`

## Conception

| Ressource | URL | Méthodes HTTP | Paramètres d'URL/Variations | Commentaires |
|-----------|-----|---------------|---------------------------|--------------|
| Collection de problèmes | `/api/problems` | GET | `?severity={1,2,3}` | Liste tous les problèmes. Filtre optionnel par sévérité |
| Problème unique | `/api/problems/{eventid}` | GET, PUT | - | Obtenir ou mettre à jour un problème spécifique |
| Sévérité d'un problème | `/api/problems/{eventid}/severity` | PUT | - | Mettre à jour uniquement la sévérité d'un problème |

### Format de réponse
L'API utilise le format HAL+JSON pour toutes les réponses, avec des liens hypermedia appropriés.

### Codes de statut
- 200 : Succès
- 201 : Créé
- 400 : Mauvaise requête
- 404 : Non trouvé
- 405 : Méthode non autorisée
- 415 : Type de média non supporté

### Types de contenu
- Requête : `application/x-www-form-urlencoded`
- Réponse : `application/hal+json`

## Remarques
L'implémentation se concentre sur la fourniture d'une façade RESTful par-dessus l'API RPC de Zabbix. Les fonctionnalités clés incluent :
- Réponses conformes HAL avec contrôles hypermedia appropriés
- Utilisation correcte des méthodes HTTP
- Fonctionnement sans état
- Structure d'URL propre

## Références
1. Modèle de maturité de Richardson
2. Spécification HAL (https://stateless.group/hal_specification.html)
3. REST in Practice (Livre)
4. Spécification HTTP/1.1 (RFC 7231)

# Projet d'API RESTful pour les Problèmes Zabbix

## Références

1. Documentation officielle PHP sur les en-têtes HTTP et la gestion des requêtes
   - https://www.php.net/manual/fr/function.header.php
   - https://www.php.net/manual/fr/reserved.variables.server.php

2. Spécification HAL (Hypertext Application Language)
   - https://stateless.group/hal_specification.html
   - Guide d'implémentation : https://stateless.co/hal_specification.html

3. Tutoriel REST API Best Practices de Philippe Herlin
   - https://blog.octo.com/designer-une-api-rest/

4. Documentation PHP sur le traitement des fichiers JSON
   - https://www.php.net/manual/fr/book.json.php
   - https://www.php.net/manual/fr/function.json-encode.php

5. Guide sur la mise en œuvre des codes de statut HTTP
   - https://developer.mozilla.org/fr/docs/Web/HTTP/Status

6. Documentation de cURL en PHP pour les requêtes HTTP
   - https://www.php.net/manual/fr/book.curl.php

7. Guide de bonnes pratiques sur la journalisation en PHP
   - https://www.loggly.com/ultimate-guide/php-logging-basics/

8. Tutorial OpenClassrooms sur la création d'API REST
   - https://openclassrooms.com/fr/courses/6573181-adoptez-les-api-rest-pour-vos-projets-web
   - https://itds.fr/creer-une-api-rest-en-php/
9. Documentation sur les standards PSR (PHP Standards Recommendations)
   - https://www.php-fig.org/psr/

10. Tutoriel sur l'architecture REST en français
    - https://openclassrooms.com/fr/courses/6573181-adoptez-les-api-rest-pour-vos-projets-web

Ces ressources m'ont aidé à :
- Comprendre et implémenter correctement le format HAL
- Gérer proprement les requêtes HTTP en PHP
- Structurer le code selon les bonnes pratiques
- Mettre en place la journalisation
- Implémenter correctement les codes de statut HTTP
- Concevoir une API REST cohérente
