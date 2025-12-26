<?php
/**
 * SEO Generator Module for PrestaShop
 *
 * @author    Clément ROUY
 * @copyright 2025 Clément ROUY
 * @license   MIT License
 * @version   1.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SeoGenerator extends Module
{
    public function __construct()
    {
        $this->name = 'seogenerator';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->author = 'Clément ROUY';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SEO Generator');
        $this->description = $this->l('Génère automatiquement les meta tags SEO pour vos produits et catégories');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    /**
     * Installation du module
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('actionProductSave')
            && $this->registerHook('actionCategorySave')
            && Configuration::updateValue('SEOGEN_AUTO_PRODUCT', true)
            && Configuration::updateValue('SEOGEN_AUTO_CATEGORY', true)
            && Configuration::updateValue('SEOGEN_INCLUDE_PRICE', true)
            && Configuration::updateValue('SEOGEN_INCLUDE_BRAND', true);
    }

    /**
     * Désinstallation du module
     */
    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('SEOGEN_AUTO_PRODUCT')
            && Configuration::deleteByName('SEOGEN_AUTO_CATEGORY')
            && Configuration::deleteByName('SEOGEN_INCLUDE_PRICE')
            && Configuration::deleteByName('SEOGEN_INCLUDE_BRAND');
    }

    /**
     * Hook appelé lors de la sauvegarde d'un produit
     */
    public function hookActionProductSave($params)
    {
        if (!Configuration::get('SEOGEN_AUTO_PRODUCT')) {
            return;
        }

        // Vérifier que l'objet existe dans les paramètres
        if (!isset($params['object'])) {
            return;
        }

        $product = $params['object'];

        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];

            // Générer meta_title si vide
            if (empty($product->meta_title[$id_lang])) {
                $product->meta_title[$id_lang] = $this->generateProductMetaTitle($product, $id_lang);
            }

            // Générer meta_description si vide
            if (empty($product->meta_description[$id_lang])) {
                $product->meta_description[$id_lang] = $this->generateProductMetaDescription($product, $id_lang);
            }

            // Note: meta_keywords supprimé en PrestaShop 9
        }

        $product->save();
    }

    /**
     * Hook appelé lors de la sauvegarde d'une catégorie
     */
    public function hookActionCategorySave($params)
    {
        if (!Configuration::get('SEOGEN_AUTO_CATEGORY')) {
            return;
        }

        // Vérifier que l'objet existe dans les paramètres
        if (!isset($params['object'])) {
            return;
        }

        $category = $params['object'];

        if (!Validate::isLoadedObject($category) || $category->id_category <= 2) {
            return; // Skip Home et Root
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $id_lang = (int)$lang['id_lang'];

            if (empty($category->meta_title[$id_lang])) {
                $category->meta_title[$id_lang] = $this->generateCategoryMetaTitle($category, $id_lang);
            }

            if (empty($category->meta_description[$id_lang])) {
                $category->meta_description[$id_lang] = $this->generateCategoryMetaDescription($category, $id_lang);
            }

            // Note: meta_keywords supprimé en PrestaShop 9
        }

        $category->save();
    }

    /**
     * Générer le meta title d'un produit
     */
    private function generateProductMetaTitle($product, $id_lang)
    {
        $shopName = Configuration::get('PS_SHOP_NAME');
        $productName = $product->name[$id_lang];
        $reference = $product->reference;

        if (!empty($reference)) {
            return sprintf('%s - %s | %s', $productName, $reference, $shopName);
        }

        return sprintf('%s | %s', $productName, $shopName);
    }

    /**
     * Générer la meta description d'un produit
     */
    private function generateProductMetaDescription($product, $id_lang)
    {
        $description = $product->description_short[$id_lang];

        // Nettoyer le HTML
        $description = strip_tags($description);
        $description = trim($description);

        // Limiter à 155 caractères
        if (mb_strlen($description) > 155) {
            $description = mb_substr($description, 0, 152) . '...';
        }

        // Ajouter prix si activé
        if (Configuration::get('SEOGEN_INCLUDE_PRICE')) {
            $price = Product::getPriceStatic($product->id, true);
            if ($price > 0) {
                $description .= sprintf(' Prix: %.2f€', $price);
            }
        }

        return $description;
    }

    /**
     * Note: generateProductMetaKeywords supprimé - meta_keywords n'existe plus en PrestaShop 9
     */

    /**
     * Générer le meta title d'une catégorie
     */
    private function generateCategoryMetaTitle($category, $id_lang)
    {
        $shopName = Configuration::get('PS_SHOP_NAME');
        $categoryName = $category->name[$id_lang];

        return sprintf('%s | %s', $categoryName, $shopName);
    }

    /**
     * Générer la meta description d'une catégorie
     */
    private function generateCategoryMetaDescription($category, $id_lang)
    {
        $categoryName = $category->name[$id_lang];

        $description = sprintf(
            'Découvrez notre sélection de %s. Livraison rapide et garantie satisfait ou remboursé.',
            $categoryName
        );

        return mb_substr($description, 0, 160);
    }

    /**
     * Note: generateCategoryMetaKeywords supprimé - meta_keywords n'existe plus en PrestaShop 9
     */

    /**
     * Générer SEO pour tous les produits
     */
    public function generateAllProductsSEO()
    {
        $products = Product::getProducts(
            (int)Context::getContext()->language->id,
            0,
            0,
            'id_product',
            'ASC'
        );

        $count = 0;
        $languages = Language::getLanguages(false);

        foreach ($products as $productData) {
            $product = new Product((int)$productData['id_product']);

            foreach ($languages as $lang) {
                $id_lang = (int)$lang['id_lang'];

                $product->meta_title[$id_lang] = $this->generateProductMetaTitle($product, $id_lang);
                $product->meta_description[$id_lang] = $this->generateProductMetaDescription($product, $id_lang);
                // meta_keywords supprimé - n'existe plus en PrestaShop 9
            }

            $product->save();
            $count++;
        }

        return $count;
    }

    /**
     * Générer SEO pour toutes les catégories
     */
    public function generateAllCategoriesSEO()
    {
        // Récupérer toutes les catégories directement depuis la base
        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE id_category > 2 AND active = 1';
        $categoryIds = Db::getInstance()->executeS($sql);

        $count = 0;
        $languages = Language::getLanguages(false);

        foreach ($categoryIds as $catData) {
            $category = new Category((int)$catData['id_category']);

            if (!Validate::isLoadedObject($category)) {
                continue;
            }

            foreach ($languages as $lang) {
                $id_lang = (int)$lang['id_lang'];

                $category->meta_title[$id_lang] = $this->generateCategoryMetaTitle($category, $id_lang);
                $category->meta_description[$id_lang] = $this->generateCategoryMetaDescription($category, $id_lang);
                // meta_keywords supprimé - n'existe plus en PrestaShop 9
            }

            $category->save();
            $count++;
        }

        return $count;
    }

    /**
     * Configuration du module
     */
    public function getContent()
    {
        $output = '';

        // Traitement du formulaire
        if (Tools::isSubmit('submitSeoGeneratorConfig')) {
            Configuration::updateValue('SEOGEN_AUTO_PRODUCT', Tools::getValue('SEOGEN_AUTO_PRODUCT'));
            Configuration::updateValue('SEOGEN_AUTO_CATEGORY', Tools::getValue('SEOGEN_AUTO_CATEGORY'));
            Configuration::updateValue('SEOGEN_INCLUDE_PRICE', Tools::getValue('SEOGEN_INCLUDE_PRICE'));
            Configuration::updateValue('SEOGEN_INCLUDE_BRAND', Tools::getValue('SEOGEN_INCLUDE_BRAND'));

            $output .= $this->displayConfirmation($this->l('Configuration mise à jour'));
        }

        // Génération en masse produits
        if (Tools::isSubmit('generateProducts') || Tools::isSubmit('submitgenerateProducts')) {
            $count = $this->generateAllProductsSEO();
            $output .= $this->displayConfirmation(
                sprintf($this->l('SEO généré pour %d produits'), $count)
            );
        }

        // Génération en masse catégories
        if (Tools::isSubmit('generateCategories') || Tools::isSubmit('submitgenerateCategories')) {
            $count = $this->generateAllCategoriesSEO();
            $output .= $this->displayConfirmation(
                sprintf($this->l('SEO généré pour %d catégories'), $count)
            );
        }

        return $output . $this->renderForm();
    }

    /**
     * Afficher le formulaire de configuration
     */
    public function renderForm()
    {
        $fields_form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Configuration'),
                        'icon' => 'icon-cogs',
                    ],
                    'input' => [
                        [
                            'type' => 'switch',
                            'label' => $this->l('Génération automatique (Produits)'),
                            'name' => 'SEOGEN_AUTO_PRODUCT',
                            'desc' => $this->l('Générer automatiquement le SEO lors de la création/modification d\'un produit'),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Activé'),
                                ],
                                [
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Désactivé'),
                                ],
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Génération automatique (Catégories)'),
                            'name' => 'SEOGEN_AUTO_CATEGORY',
                            'desc' => $this->l('Générer automatiquement le SEO lors de la création/modification d\'une catégorie'),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Activé'),
                                ],
                                [
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Désactivé'),
                                ],
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Inclure le prix'),
                            'name' => 'SEOGEN_INCLUDE_PRICE',
                            'desc' => $this->l('Inclure le prix dans la meta description'),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Oui'),
                                ],
                                [
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Non'),
                                ],
                            ],
                        ],
                        [
                            'type' => 'switch',
                            'label' => $this->l('Inclure la marque'),
                            'name' => 'SEOGEN_INCLUDE_BRAND',
                            'desc' => $this->l('Inclure la marque/fabricant dans les meta keywords'),
                            'is_bool' => true,
                            'values' => [
                                [
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Oui'),
                                ],
                                [
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Non'),
                                ],
                            ],
                        ],
                    ],
                    'submit' => [
                        'title' => $this->l('Sauvegarder'),
                    ],
                ],
            ],
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Génération en masse'),
                        'icon' => 'icon-refresh',
                    ],
                    'description' => $this->l('Générer ou régénérer le SEO pour tous les produits et catégories existants.'),
                    'buttons' => [
                        [
                            'type' => 'submit',
                            'name' => 'generateProducts',
                            'title' => $this->l('Générer SEO - Produits'),
                            'icon' => 'process-icon-refresh',
                            'class' => 'btn btn-warning pull-left',
                        ],
                        [
                            'type' => 'submit',
                            'name' => 'generateCategories',
                            'title' => $this->l('Générer SEO - Catégories'),
                            'icon' => 'process-icon-refresh',
                            'class' => 'btn btn-info pull-right',
                        ],
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSeoGeneratorConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => [
                'SEOGEN_AUTO_PRODUCT' => Configuration::get('SEOGEN_AUTO_PRODUCT'),
                'SEOGEN_AUTO_CATEGORY' => Configuration::get('SEOGEN_AUTO_CATEGORY'),
                'SEOGEN_INCLUDE_PRICE' => Configuration::get('SEOGEN_INCLUDE_PRICE'),
                'SEOGEN_INCLUDE_BRAND' => Configuration::get('SEOGEN_INCLUDE_BRAND'),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fields_form);
    }
}
