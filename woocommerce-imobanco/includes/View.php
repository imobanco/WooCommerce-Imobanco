<?php

namespace WoocommerceImobanco\Includes;

class View {

    public static $instance;

    private $plugin_screen_name;

    private $settings = [];

    public static function GetInstance() {

        if (!isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function PluginMenu() {

        $this->my_plugin_screen_name = add_menu_page(
            $this->settings['menu_title'],
            $this->settings['tab_title'],
            $this->settings['capability'] ?? 'manage_options',
            __FILE__,
            array($this, 'RenderPage'),
            $this->settings['icon'] ?? ''
        );
    }

    public static function RenderPage() {

        require WOO_IMOBANCO_PLUGIN_DIR . 'includes/forms/admin.php';
    }

    public function InitPlugin($settings = []) {

        $this->settings = $settings;

        add_action('admin_menu', array($this, 'PluginMenu'));
    }
}