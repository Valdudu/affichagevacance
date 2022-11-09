<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Affichagevacance extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'affichagevacance';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Valentin Duplan';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Affichage vacances');
        $this->description = $this->l('Permet de plannifier et de definir un message de vacance sur la page daccueil');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        //include(__DIR__ . '/sql/install.php');
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        //include(__DIR__ . '/sql/uninstall.php');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitAffichagevacanceModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAffichagevacanceModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Paramétrage affichage vacances'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 6,
                        'type' => 'textarea',
                        'label' => $this->l('Message vacances'),
                        'name' => 'AFFICHAGEVACANCE_TEXT',
                        'tinymce' => true,
                        'cols' => 50,
                        'rows' => 10,
                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'datetime',
                        //'prefix' => '<i class="icon icon-envelope"></i>',
                        'label' => $this->l('Date debut affichage'),
                        'name' => 'AFFICHAGEVACANCE_FROM',
                        'required' => true,
                    ),
                    array(
                        'type' => 'datetime',
                        //'prefix' => '<i class="icon icon-envelope"></i>',
                        'label' => $this->l('Date fin affichage'),
                        'name' => 'AFFICHAGEVACANCE_TO',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        //'prefix' => '<i class="icon icon-envelope"></i>',
                        'label' => $this->l('Adresse ip pour les tests'),
                        'name' => 'AFFICHAGEVACANCE_IP_LIST',
                        'desc' => $this->l('il faut séparer les adresses IP par une virgule'),
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $varr = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'vacances')[0];
        return array(
            'AFFICHAGEVACANCE_TEXT' => $varr['text'],
            'AFFICHAGEVACANCE_FROM' => $varr['date_from'],
            'AFFICHAGEVACANCE_TO' => $varr['date_to'],
            'AFFICHAGEVACANCE_IP_LIST' => $varr['ip_list']
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        Db::getInstance()->execute(
            'update ' . _DB_PREFIX_ . 'vacances set 
            text = \'' . Tools::getValue('AFFICHAGEVACANCE_TEXT') . '\', 
            date_from=\'' . Tools::getValue('AFFICHAGEVACANCE_FROM') . '\', 
            date_to=\'' . Tools::getValue('AFFICHAGEVACANCE_TO') . '\',
            ip_list=\'' . str_replace(" ", "", Tools::getValue('AFFICHAGEVACANCE_IP_LIST')) . '\''
        );
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        $config = Db::getInstance()->executeS('select * from ' . _DB_PREFIX_ . 'vacances')[0];
        if (Module::isEnabled($this->name)) {
            $this->context->smarty->assign(
                [
                    'vacance' =>
                    [
                        'text' => $config['text'],
                        'from' => $config['date_from'],
                        'to' => $config['date_to'],
                        'ip_list' => explode(",", $config['ip_list']),
                    ]
                ]
            );
            return $this->display(__FILE__, 'views/templates/front/hook/home.tpl');
        }
    }
}