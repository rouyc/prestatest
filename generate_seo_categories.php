<?php
define('_PS_ADMIN_DIR_', '/var/www/html/admin-dev');
require_once(__DIR__ . '/config/config.inc.php');

if (!Context::getContext()->employee || !Context::getContext()->employee->id) {
    Context::getContext()->employee = new Employee(1);
}

echo "=== Génération SEO pour les catégories ===\n\n";

$categories = Category::getCategories(1, false);
$languages = Language::getLanguages(false);
$shopName = Configuration::get('PS_SHOP_NAME');
$count = 0;

foreach ($categories as $categoryData) {
    foreach ($categoryData as $cat) {
        if (!isset($cat['id_category'])) {
            continue;
        }

        $category = new Category((int)$cat['id_category']);

        if (!$category->id || $category->id <= 2) {
            continue;
        }

        $updated = false;

        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];

            if (empty($category->meta_title[$id_lang])) {
                $categoryName = $category->name[$id_lang];
                $category->meta_title[$id_lang] = sprintf('%s | %s', $categoryName, $shopName);
                $updated = true;
            }

            if (empty($category->meta_description[$id_lang])) {
                $categoryName = $category->name[$id_lang];
                $description = sprintf('Découvrez notre sélection de %s. Livraison rapide et garantie satisfait ou remboursé.', $categoryName);
                $category->meta_description[$id_lang] = mb_substr($description, 0, 160);
                $updated = true;
            }
        }

        if ($updated) {
            $category->save();
            $count++;
            echo "✓ Catégorie #{$category->id}: {$category->name[1]}\n";
        } else {
            echo "- Catégorie #{$category->id}: SEO déjà présent\n";
        }
    }
}

echo "\n=== Résultat: SEO généré pour $count catégorie(s) ===\n";
