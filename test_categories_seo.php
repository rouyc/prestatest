<?php
define('_PS_ADMIN_DIR_', '/var/www/html/admin-dev');
require_once(__DIR__ . '/config/config.inc.php');

if (!Context::getContext()->employee || !Context::getContext()->employee->id) {
    Context::getContext()->employee = new Employee(1);
}

$module = Module::getInstanceByName('seogenerator');

if ($module) {
    echo "Module trouvé, génération SEO catégories...\n";
    $count = $module->generateAllCategoriesSEO();
    echo "SEO généré pour $count catégorie(s)\n";
} else {
    echo "Module non trouvé\n";
}
