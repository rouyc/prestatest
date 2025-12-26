# Mon Site PrestaShop

Projet PrestaShop 9.0.2 pour développement personnel.

## Installation

### Prérequis
- Docker Desktop
- Git

### Démarrage

```bash
# Cloner le projet
git clone https://github.com/votre-username/prestatest.git
cd prestatest

# Démarrer Docker
docker-compose up -d

# Accéder au site
# Front-office: http://localhost:8001
# Back-office: http://localhost:8001/admin-dev
```

### Identifiants par défaut

- Email: `demo@prestashop.com`
- Mot de passe: `Correct Horse Battery Staple`

## Structure du projet

- `/modules/votremodule/` - Vos modules personnalisés
- `/themes/votretheme/` - Votre thème personnalisé
- `/override/` - Vos overrides PrestaShop
- `/.docker/` - Configuration Docker

## Développement

### Créer un module

```bash
# Créer la structure du module
mkdir -p modules/monmodule
# Développer votre module...
```

### Créer un thème

```bash
# Dupliquer un thème existant
cp -r themes/classic themes/montheme
# Personnaliser votre thème...
```

## Commandes utiles

```bash
# Arrêter Docker
docker-compose down

# Voir les logs
docker logs prestatest-prestashop-git-1 -f

# Redémarrer
docker-compose restart
```

## Notes

Ce projet versionne UNIQUEMENT vos modifications personnelles.
Le core PrestaShop est ignoré par Git (.gitignore).
