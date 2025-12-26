# SEO Generator - Module PrestaShop

Module PrestaShop pour générer automatiquement les meta tags SEO (title, description) pour vos produits et catégories.

> **Note** : Compatible PrestaShop 9.x - Les meta keywords ont été supprimés (Google ne les utilise plus depuis 2009).

## 🎯 Fonctionnalités

- ✅ **Génération automatique** lors de la sauvegarde de produits/catégories
- ✅ **Génération en masse** pour tout le catalogue existant
- ✅ **Multi-langues** : Génère le SEO pour toutes les langues actives
- ✅ **Personnalisable** : Options configurables (prix, marque, etc.)
- ✅ **Intelligent** : Utilise les données du produit (nom, référence, catégories, marque)

## 📦 Installation

### Via le back-office PrestaShop

1. Compresser le dossier `seogenerator` en ZIP
2. Aller dans **Modules > Module Manager**
3. Cliquer sur **"Uploader un module"**
4. Sélectionner le fichier ZIP
5. Cliquer sur **"Installer"**

### Manuel

1. Copier le dossier `seogenerator` dans `/modules/`
2. Aller dans **Modules > Module Manager**
3. Chercher "SEO Generator"
4. Cliquer sur **"Installer"**

## ⚙️ Configuration

Après installation, accédez à la configuration du module :

**Modules > Module Manager > SEO Generator > Configurer**

### Options disponibles

| Option | Description |
|--------|-------------|
| **Génération automatique (Produits)** | Active/désactive la génération auto lors de la sauvegarde d'un produit |
| **Génération automatique (Catégories)** | Active/désactive la génération auto lors de la sauvegarde d'une catégorie |
| **Inclure le prix** | Ajoute le prix dans la meta description |
| **Inclure la marque** | ~~Ajoute la marque/fabricant dans les meta keywords~~ (Obsolète en PS 9) |

### Génération en masse

Deux boutons permettent de générer le SEO pour :
- **Tous les produits** existants
- **Toutes les catégories** existantes

## 📝 Format des meta tags générés

### Produits

**Meta Title :**
```
Nom du produit - Référence | Nom de la boutique
Exemple : iPhone 15 Pro - APL123 | Ma Boutique
```

**Meta Description :**
```
Description courte du produit (max 155 car). Prix: XX.XX€
Exemple : Le dernier iPhone avec puce A17... Prix: 1299.00€
```

~~**Meta Keywords :**~~ (Supprimé en PrestaShop 9)
```
Non généré - Google n'utilise plus ce champ depuis 2009
```

### Catégories

**Meta Title :**
```
Nom de la catégorie | Nom de la boutique
Exemple : Smartphones | Ma Boutique
```

**Meta Description :**
```
Découvrez notre sélection de [catégorie]. Livraison rapide...
```

~~**Meta Keywords :**~~ (Supprimé en PrestaShop 9)
```
Non généré
```

## 🚀 Utilisation

### Automatique

1. Activez les options de génération automatique
2. Créez ou modifiez un produit/catégorie
3. Le SEO est généré automatiquement si les champs sont vides

### Manuelle (masse)

1. Allez dans la configuration du module
2. Cliquez sur **"Générer SEO - Produits"** ou **"Générer SEO - Catégories"**
3. Attendez la confirmation

## 🔧 Personnalisation

Pour personnaliser les règles de génération, modifiez les méthodes dans `seogenerator.php` :

```php
// Meta title produit
private function generateProductMetaTitle($product, $id_lang)

// Meta description produit
private function generateProductMetaDescription($product, $id_lang)

// Meta title catégorie
private function generateCategoryMetaTitle($category, $id_lang)

// Meta description catégorie
private function generateCategoryMetaDescription($category, $id_lang)

// Note: Les fonctions meta_keywords ont été supprimées (obsolètes)
```

## 📊 Bonnes pratiques SEO

### Longueurs recommandées

- **Meta Title** : 50-60 caractères (max 70)
- **Meta Description** : 150-160 caractères (max 160)
- ~~**Meta Keywords**~~ : Obsolète (Google ne les utilise plus)

### Éléments à inclure

**Title :**
- Nom du produit/catégorie
- Marque ou référence
- Nom de la boutique

**Description :**
- Description courte et claire
- Prix (optionnel)
- Avantages (livraison, garantie)
- Call-to-action

~~**Keywords :**~~ (Obsolète)
- Non utilisé par les moteurs de recherche modernes

## 🛠️ Compatibilité

- **PrestaShop** : 8.0 - 9.0+
- **PHP** : 7.4+
- **Multi-boutique** : Oui
- **Multi-langue** : Oui

## 📄 License

Ce module est sous license MIT.

Copyright (c) 2025 Clément ROUY

## 👤 Auteur

**Clément ROUY**

## 🐛 Support

Pour toute question ou problème :
- Créez une issue sur GitHub
- Consultez la documentation PrestaShop : https://devdocs.prestashop-project.org/

## 📝 Changelog

### Version 1.0.0 (2025-12-26)
- ✨ Version initiale compatible PrestaShop 9.x
- ✅ Génération automatique produits (meta_title, meta_description)
- ✅ Génération automatique catégories (meta_title, meta_description)
- ✅ Génération en masse
- ✅ Configuration via back-office
- 🔧 Suppression de meta_keywords (obsolète depuis Google 2009)
- 🐛 Correction des warnings avec hooks PrestaShop 9
