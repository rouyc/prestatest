# PrestaShop - Projet Personnalisé

Projet PrestaShop 9.0.2 configuré pour le développement et déploiement avec Docker.

## 📋 Table des matières

- [Architecture](#architecture)
- [Prérequis](#prérequis)
- [Installation locale](#installation-locale)
- [Développement](#développement)
- [Déploiement](#déploiement)
- [Structure du projet](#structure-du-projet)
- [Commandes utiles](#commandes-utiles)
- [Contribution](#contribution)
- [License](#license)

---

## 🏗️ Architecture

### Architecture du projet

Ce projet utilise une **approche hybride** qui sépare le core PrestaShop (non versionné) de vos modifications personnelles (versionnées sur Git).

```
┌─────────────────────────────────────────────────────────────────┐
│                    DÉVELOPPEMENT LOCAL                           │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ PrestaShop 9.0.2 (complet)                                 │ │
│  │  ├── Core PrestaShop (ignoré par Git)                      │ │
│  │  │   ├── /classes, /src, /controllers                      │ │
│  │  │   ├── /vendor, /node_modules                            │ │
│  │  │   └── ...                                                │ │
│  │  │                                                           │ │
│  │  └── VOS MODIFICATIONS (versionnées)                        │ │
│  │      ├── /modules/votre-module/     ✓ Git                  │ │
│  │      ├── /themes/votre-theme/       ✓ Git                  │ │
│  │      ├── /override/                 ✓ Git                  │ │
│  │      ├── /.docker/                  ✓ Git                  │ │
│  │      └── docker-compose.yml         ✓ Git                  │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                            │
                            │ git push
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                         GITHUB                                   │
│  Contient UNIQUEMENT vos modifications personnelles :            │
│  ✓ .docker/                                                      │
│  ✓ docker-compose.yml                                            │
│  ✓ modules/votre-module/                                         │
│  ✓ themes/votre-theme/                                           │
│  ✓ override/                                                     │
│  ✓ .gitignore, README.md                                         │
└─────────────────────────────────────────────────────────────────┘
                            │
                            │ git clone + deploy
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    SERVEUR DE PRODUCTION                         │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │ 1. Installation PrestaShop de base (Composer)              │ │
│  │ 2. Merge avec vos modifications (GitHub)                   │ │
│  │ 3. Résultat = PrestaShop complet + vos modifs              │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### Pourquoi cette approche ?

✅ **Repo Git léger** : ~10 Mo au lieu de ~500 Mo
✅ **Séparation claire** : Vos modifs vs Core PrestaShop
✅ **Mises à jour faciles** : Updater PrestaShop sans conflits Git
✅ **Collaboration simple** : Les devs clonent seulement vos modifs
✅ **Sécurité** : Pas de vendor/ ni de secrets dans Git

---

## 📦 Prérequis

### Environnement local

- **Docker Desktop** (Windows/Mac) ou **Docker Engine** (Linux)
- **Git**
- **Composer** (optionnel, Docker l'inclut)

### Serveur de production

- **Docker + Docker Compose** OU **LAMP/LEMP stack classique**
- **PHP 8.1+** avec extensions : `gd`, `intl`, `zip`, `curl`, `xml`, `mbstring`, `sodium`
- **MySQL 8.0+** ou **MariaDB 10.4+**
- **Nginx** ou **Apache**

---

## 🚀 Installation locale

### 1. Cloner le projet

```bash
# Cloner le repository
git clone https://github.com/VOTRE-USERNAME/prestatest.git
cd prestatest
```

### 2. Installer PrestaShop

```bash
# Télécharger PrestaShop avec Composer
composer create-project prestashop/prestashop . --no-install

# Installer les dépendances
composer install
```

### 3. Démarrer Docker

```bash
# Lancer les conteneurs
docker-compose up -d

# Vérifier que tout fonctionne
docker-compose ps
```

### 4. Accéder au site

- **Front-office** : http://localhost:8001
- **Back-office** : http://localhost:8001/admin-dev
- **MailDev** (emails de test) : http://localhost:1080

### Identifiants par défaut

- **Email** : `demo@prestashop.com`
- **Mot de passe** : `Correct Horse Battery Staple`

---

## 💻 Développement

### Workflow de développement

```bash
# 1. Créer une branche pour votre fonctionnalité
git checkout -b feature/ma-nouvelle-fonctionnalite

# 2. Développer (modules, thèmes, overrides)
# ... coder ...

# 3. Tester localement
docker-compose restart

# 4. Commiter vos modifications
git add .
git commit -m "Add: nouvelle fonctionnalité XYZ"

# 5. Pusher vers GitHub
git push origin feature/ma-nouvelle-fonctionnalite

# 6. Créer une Pull Request sur GitHub
```

### Créer un module personnalisé

```bash
# 1. Créer la structure du module
mkdir -p modules/monmodule
cd modules/monmodule

# 2. Créer le fichier principal
cat > monmodule.php <<'EOF'
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MonModule extends Module
{
    public function __construct()
    {
        $this->name = 'monmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Votre Nom';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Mon Module');
        $this->description = $this->l('Description de mon module');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
}
EOF

# 3. Mettre à jour .gitignore pour versionner VOTRE module
# Remplacer dans .gitignore:
#   !modules/votremodule/
# Par:
#   !modules/monmodule/

# 4. Versionner
git add modules/monmodule/
git add .gitignore
git commit -m "Add: custom module monmodule"
git push
```

### Créer un thème personnalisé

```bash
# 1. Dupliquer un thème existant
cp -r themes/classic themes/montheme

# 2. Modifier le fichier de configuration
# Éditer themes/montheme/config/theme.yml

# 3. Personnaliser (templates, CSS, JS)
# ...

# 4. Mettre à jour .gitignore
# Remplacer: !themes/votretheme/
# Par:       !themes/montheme/

# 5. Versionner
git add themes/montheme/
git add .gitignore
git commit -m "Add: custom theme montheme"
git push
```

### Créer un override

```bash
# Exemple: Override du ProductController

# 1. Créer la structure
mkdir -p override/controllers/front

# 2. Créer votre override
cat > override/controllers/front/ProductController.php <<'EOF'
<?php
class ProductController extends ProductControllerCore
{
    public function initContent()
    {
        parent::initContent();

        // Votre logique personnalisée ici
    }
}
EOF

# 3. Versionner (déjà configuré dans .gitignore)
git add override/
git commit -m "Override: ProductController for custom logic"
git push

# 4. Régénérer le cache des classes sur PrestaShop
# Back-office > Paramètres avancés > Performances > Vider le cache
```

---

## 🌐 Déploiement

### Méthode 1 : Déploiement avec Docker (Recommandé)

Cette méthode utilise Docker pour un déploiement rapide et isolé.

#### Sur le serveur de production

```bash
# 1. Installer Docker et Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# 2. Cloner votre projet
cd /var/www
git clone https://github.com/VOTRE-USERNAME/prestatest.git
cd prestatest

# 3. Installer PrestaShop
composer create-project prestashop/prestashop . --no-install
composer install --no-dev --optimize-autoloader

# 4. Configurer les variables d'environnement pour production
cp .env.dist .env
# Éditer .env avec vos paramètres de production

# 5. Configurer docker-compose pour la production
cat > docker-compose.prod.yml <<'EOF'
version: '3'

services:
  prestashop:
    image: prestashop/prestashop:9.0-apache
    ports:
      - "80:80"
      - "443:443"
    environment:
      DB_SERVER: mysql
      DB_NAME: prestashop_prod
      DB_USER: prestashop
      DB_PASSWD: ${DB_PASSWORD}
      PS_DOMAIN: ${DOMAIN}
      PS_DEV_MODE: 0
      PS_ENABLE_SSL: 1
    volumes:
      - ./:/var/www/html
    depends_on:
      - mysql
    restart: unless-stopped

  mysql:
    image: mysql:8.4
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: prestashop_prod
      MYSQL_USER: prestashop
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql
    restart: unless-stopped

volumes:
  mysql-data:
EOF

# 6. Lancer en production
docker-compose -f docker-compose.prod.yml up -d

# 7. Configurer le domaine (Nginx reverse proxy ou Apache)
# Voir section "Configuration serveur web" ci-dessous
```

#### Mise à jour du site en production

```bash
cd /var/www/prestatest

# Pull des dernières modifications
git pull origin main

# Redémarrer les conteneurs
docker-compose -f docker-compose.prod.yml restart prestashop

# Vider le cache PrestaShop
docker-compose -f docker-compose.prod.yml exec prestashop rm -rf var/cache/*
```

### Méthode 2 : Déploiement traditionnel (LAMP/LEMP)

#### Installation manuelle sur serveur

```bash
# 1. Installer PrestaShop de base
cd /var/www/html
composer create-project prestashop/prestashop monsite --no-dev
cd monsite

# 2. Cloner vos modifications
cd /tmp
git clone https://github.com/VOTRE-USERNAME/prestatest.git

# 3. Copier vos modifications
rsync -av /tmp/prestatest/modules/ /var/www/html/monsite/modules/
rsync -av /tmp/prestatest/themes/ /var/www/html/monsite/themes/
rsync -av /tmp/prestatest/override/ /var/www/html/monsite/override/

# 4. Configurer les permissions
sudo chown -R www-data:www-data /var/www/html/monsite
sudo chmod -R 755 /var/www/html/monsite

# 5. Configurer la base de données
mysql -u root -p <<'EOF'
CREATE DATABASE prestashop_prod;
CREATE USER 'prestashop'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
GRANT ALL PRIVILEGES ON prestashop_prod.* TO 'prestashop'@'localhost';
FLUSH PRIVILEGES;
EOF

# 6. Lancer l'installation PrestaShop via navigateur
# http://votre-domaine.com/install-dev

# 7. Après installation, supprimer le dossier install
rm -rf /var/www/html/monsite/install-dev
```

#### Configuration Nginx (exemple)

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/html/monsite;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

### Méthode 3 : Déploiement automatisé (CI/CD)

Créez `.github/workflows/deploy.yml` :

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Deploy via SSH
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/prestatest
            git pull origin main
            rsync -av modules/ /var/www/html/monsite/modules/
            rsync -av themes/ /var/www/html/monsite/themes/
            rsync -av override/ /var/www/html/monsite/override/
            rm -rf /var/www/html/monsite/var/cache/*
            docker-compose -f docker-compose.prod.yml restart prestashop
```

---

## 📁 Structure du projet

```
prestatest/
│
├── .docker/                    # Configuration Docker personnalisée
│   ├── Dockerfile             # Image Docker custom (si nécessaire)
│   ├── docker_run_git.sh      # Script de démarrage
│   └── wait-for-it.sh         # Script d'attente MySQL
│
├── docker-compose.yml          # Orchestration Docker (dev)
├── docker-compose.prod.yml     # Orchestration Docker (prod) - non versionné
│
├── modules/                    # Modules PrestaShop
│   ├── [modules core]         # ❌ Non versionnés (ignorés)
│   └── votre-module/          # ✅ Vos modules custom (versionnés)
│
├── themes/                     # Thèmes PrestaShop
│   ├── classic/               # ❌ Non versionné
│   ├── _core/                 # ✅ Core thèmes (versionné)
│   └── votre-theme/           # ✅ Votre thème custom (versionné)
│
├── override/                   # Overrides PrestaShop
│   └── *.php                  # ✅ Vos overrides (versionnés)
│
├── .gitignore                 # Configuration Git (ignore le core)
├── README.md                  # Ce fichier
└── LICENSE                    # License du projet

# Dossiers ignorés par Git (core PrestaShop)
├── /src, /classes, /controllers, /app      # Core PHP
├── /vendor                                  # Dépendances Composer
├── /node_modules                            # Dépendances npm
├── /var, /cache                             # Cache
└── ... (voir .gitignore pour la liste complète)
```

---

## 🛠️ Commandes utiles

### Docker

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Redémarrer un service
docker-compose restart prestashop

# Voir les logs
docker-compose logs -f prestashop

# Accéder au shell du conteneur
docker-compose exec prestashop bash

# Vider le cache PrestaShop
docker-compose exec prestashop rm -rf var/cache/*
```

### Git

```bash
# Créer une nouvelle branche
git checkout -b feature/ma-fonctionnalite

# Voir les modifications
git status
git diff

# Commiter
git add .
git commit -m "Description de la modification"

# Pousser
git push origin feature/ma-fonctionnalite

# Mettre à jour depuis main
git checkout main
git pull origin main
```

### PrestaShop CLI

```bash
# Vider le cache
php bin/console cache:clear

# Lister les modules
php bin/console prestashop:module list

# Installer un module
php bin/console prestashop:module install nom_module

# Régénérer les assets
php bin/console prestashop:assets:compile
```

---

## 🤝 Contribution

Les contributions sont les bienvenues !

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commiter vos changements (`git commit -m 'Add: AmazingFeature'`)
4. Pusher vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

### Convention de commits

Utilisez des commits clairs et descriptifs :

- `Add: nouvelle fonctionnalité`
- `Fix: correction de bug`
- `Update: mise à jour de fonctionnalité existante`
- `Refactor: refactorisation de code`
- `Docs: mise à jour documentation`

---

## 📄 License

Ce projet est sous license MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

**Note** : PrestaShop lui-même est sous [Open Software License v3.0](https://opensource.org/licenses/OSL-3.0).

---

## 📞 Support

- **Documentation PrestaShop** : https://devdocs.prestashop-project.org/
- **Forum PrestaShop** : https://www.prestashop.com/forums/
- **GitHub Issues** : [Créer une issue](https://github.com/VOTRE-USERNAME/prestatest/issues)

---

## 🔒 Sécurité

### Avant de passer en production

- [ ] Supprimer le dossier `/install-dev`
- [ ] Renommer le dossier `/admin-dev`
- [ ] Changer les identifiants par défaut
- [ ] Activer HTTPS (SSL)
- [ ] Configurer les sauvegardes automatiques
- [ ] Mettre en place un WAF (ModSecurity, Cloudflare, etc.)
- [ ] Vérifier les permissions fichiers (755 dossiers, 644 fichiers)

### Variables sensibles

Ne JAMAIS commiter :
- Mots de passe
- Clés API
- Fichiers `.env` avec données de production
- Certificats SSL

Utilisez `.env.dist` comme template et `.env` (ignoré par Git) pour les vraies valeurs.

---

**Généré avec ❤️ pour PrestaShop 9.0.2**
