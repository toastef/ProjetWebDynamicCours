
## Installation

Utiliser cette commende pour installer le projet .

```bash
#Installation du projet
composer install 
#Installation service Email
npm i 
```


## Lancement de l'application et création base de donnée
Création de la base de donnée Art2022, modifier le fichier .env L31 si pas de mot de passe en localhost, retirer le 2eme root dans le lien
```bash
php bin/console d:d:c

# Migration
php bin/console make:migration

# Migrer la migration
php bin/console d:m:m 

# Fixtures
php bin/console d:f:l

# lancement du serveur symfony 
symfony serve 
```

## Authentification ROLE_ADMIN

`Email: stef.toad@gmail.com` <br>
`Pass: password`

## SUPER_ADMIN <br>
`Email: patmar@gmail.com` <br>
`Pass: password`


## Server Email dans terminaux séparés
```bash
#lancement du server
maildev --hide-extensions STARTTLS  

#transfert async des mail
php bin/console messenger:consume async 
```
## phpDocumentor
[Ouvrir](http://localhost:63342/Gallery/Gallery/doc/api/index.html)


