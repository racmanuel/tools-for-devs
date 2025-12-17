<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://racmanuel.dev/
 * @since      1.0.0
 *
 * @package    Tools_For_Devs
 * @subpackage Tools_For_Devs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tools_For_Devs
 * @subpackage Tools_For_Devs/includes
 * @author     racmanuel <developer@racmanuel.dev>
 */
class Tools_For_Devs
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Tools_For_Devs_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The unique prefix of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
     */
    protected $plugin_prefix;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        if (defined('TOOLS_FOR_DEVS_VERSION')) {

            $this->version = TOOLS_FOR_DEVS_VERSION;

        } else {

            $this->version = '1.0.0';

        }

        $this->plugin_name = 'tools-for-devs';
        $this->plugin_prefix = 'tools_for_devs_';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Tools_For_Devs_Loader. Orchestrates the hooks of the plugin.
     * - Tools_For_Devs_i18n. Defines internationalization functionality.
     * - Tools_For_Devs_Admin. Defines all hooks for the admin area.
     * - Tools_For_Devs_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * Load Composer's autoloader to register PSR-4 classes and any files defined in `composer.json`.
         *
         * This is essential to load dependencies installed via Composer.
         * The path is relative to this file's directory.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tools-for-devs-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tools-for-devs-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-tools-for-devs-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-tools-for-devs-public.php';

        $this->loader = new Tools_For_Devs_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Tools_For_Devs_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Tools_For_Devs_I18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Tools_For_Devs_Admin($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Tools_For_Devs_Public($this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Shortcode name must be the same as in shortcode_atts() third parameter.
        $this->loader->add_shortcode($this->get_plugin_name() . '-shortcode', $plugin_public, 'tools_for_devs_shortcode_func');

        $this->loader->add_shortcode($this->get_plugin_name() . '-migration-sql', $plugin_public, 'tools_for_devs_shortcode_wp_migration_sql_tool');
        $this->loader->add_shortcode($this->get_plugin_name() . '-db-prefix', $plugin_public, 'tools_for_devs_shortcode_wp_db_prefix_tool');
        $this->loader->add_shortcode($this->get_plugin_name() . '-plugin-header-generator', $plugin_public, 'tools_for_devs_shortcode_wp_plugin_header_generator');
        $this->loader->add_shortcode($this->get_plugin_name() . '-delete-products-sql', $plugin_public, 'tools_for_devs_shortcode_wp_wc_delete_products_sql_tool');
        $this->loader->add_shortcode($this->get_plugin_name() . '-acf-field-generator', $plugin_public, 'tools_for_devs_shortcode_acf_field_generator');
        $this->loader->add_shortcode($this->get_plugin_name() . '-rest-route-generator', $plugin_public, 'tools_for_devs_shortcode_wp_rest_route_generator');
        $this->loader->add_shortcode($this->get_plugin_name() . '-db-crud-generator', $plugin_public, 'tools_for_devs_shortcode_wp_db_crud_generator');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The unique prefix of the plugin used to uniquely prefix technical functions.
     *
     * @since     1.0.0
     * @return    string    The prefix of the plugin.
     */
    public function get_plugin_prefix()
    {
        return $this->plugin_prefix;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Tools_For_Devs_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
