<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://racmanuel.dev/
 * @since      1.0.0
 *
 * @package    Tools_For_Devs
 * @subpackage Tools_For_Devs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Tools_For_Devs
 * @subpackage Tools_For_Devs/public
 * @author     racmanuel <developer@racmanuel.dev>
 */
class Tools_For_Devs_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct($plugin_name, $plugin_prefix, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tools-for-devs-public.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-migration-sql-tool', plugin_dir_url(__FILE__) . 'css/wp-migration-sql-tool.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-db-prefix-tool', plugin_dir_url(__FILE__) . 'css/wp-db-prefix-tool.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-plugin-header-generator', plugin_dir_url(__FILE__) . 'css/wp-plugin-header-generator.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wc-delete-products-sql-tool', plugin_dir_url(__FILE__) . 'css/wc-delete-products-sql-tool.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-acf-field-generator', plugin_dir_url(__FILE__) . 'css/wp-acf-field-generator.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-rest-route-generator', plugin_dir_url(__FILE__) . 'css/wp-rest-route-generator.css', array(), $this->version, 'all');
		wp_register_style($this->plugin_name . '-wp-db-crud-generator', plugin_dir_url(__FILE__) . 'css/wp-db-crud-generator.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tools-for-devs-public.js', array('jquery'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-migration-sql-tool', plugin_dir_url(__FILE__) . 'js/wp-migration-sql-tool.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-db-prefix-tool', plugin_dir_url(__FILE__) . 'js/wp-db-prefix-tool.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-plugin-header-generator', plugin_dir_url(__FILE__) . 'js/wp-plugin-header-generator.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wc-delete-products-sql-tool', plugin_dir_url(__FILE__) . 'js/wc-delete-products-sql-tool.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-acf-field-generator', plugin_dir_url(__FILE__) . 'js/wp-acf-field-generator.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-rest-route-generator', plugin_dir_url(__FILE__) . 'js/wp-rest-route-generator.js', array('jquery', 'wp-i18n'), $this->version, true);
		wp_register_script($this->plugin_name . '-wp-db-crud-generator', plugin_dir_url(__FILE__) . 'js/wp-db-crud-generator.js', array('jquery', 'wp-i18n'), $this->version, true);
	}

	/**
	 * Example of Shortcode processing function.
	 *
	 * Shortcode can take attributes like [tools-for-devs-shortcode attribute='123']
	 * Shortcodes can be enclosing content [tools-for-devs-shortcode attribute='123']custom content[/tools-for-devs-shortcode].
	 *
	 * @see https://developer.wordpress.org/plugins/shortcodes/enclosing-shortcodes/
	 *
	 * @since    1.0.0
	 * @param    array  $atts    ShortCode Attributes.
	 * @param    mixed  $content ShortCode enclosed content.
	 * @param    string $tag    The Shortcode tag.
	 */
	public function tools_for_devs_shortcode_func($atts)
	{

		/**
		 * Combine user attributes with known attributes.
		 *
		 * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
		 *
		 * Pass third paramter $shortcode to enable ShortCode Attribute Filtering.
		 * @see https://developer.wordpress.org/reference/hooks/shortcode_atts_shortcode/
		 */
		$atts = shortcode_atts(
			array(
				'attribute' => 123,
			),
			$atts,
			$this->plugin_prefix . 'shortcode'
		);

		/**
		 * Build our ShortCode output.
		 * Remember to sanitize all user input.
		 * In this case, we expect a integer value to be passed to the ShortCode attribute.
		 *
		 * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/
		 */
		$out = intval($atts['attribute']);

		/**
		 * If the shortcode is enclosing, we may want to do something with $content
		 */
		if (!is_null($content) && !empty($content)) {
			$out = do_shortcode($content);// We can parse shortcodes inside $content.
			$out = intval($atts['attribute']) . ' ' . sanitize_text_field($out);// Remember to sanitize your user input.
		}

		// ShortCodes are filters and should always return, never echo.
		return $out;

	}

	/**
	 * Render shortcode output.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Enclosed content (optional).
	 * @param string $tag     Shortcode tag.
	 * @return string
	 */
	public function tools_for_devs_shortcode_wp_migration_sql_tool($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'from' => 'http://',
				'to' => 'http://',
				'prefix' => 'wp_',
			),
			$atts,
			$tag
		);

		// Enqueue assets only when shortcode is rendered.
		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-sql',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);
		wp_enqueue_style($this->plugin_name . '-wp-migration-sql-tool');
		wp_enqueue_script($this->plugin_name . '-wp-migration-sql-tool');

		$uid = 'wp-mig-' . wp_generate_uuid4();

		// Sanitize only for display in inputs.
		$from = esc_attr($atts['from']);
		$to = esc_attr($atts['to']);
		$prefix = esc_attr($atts['prefix']);

		ob_start();
		?>
		<div class="wp-mig-wrap" id="<?php echo esc_attr($uid); ?>" data-wp-mig="1">

			<p class="wp-mig-desc">
				<?php
				echo esc_html__(
					'Generate SQL queries to update a WordPress site domain in the database. This tool helps when migrating a site to a new domain or protocol. Always back up your database before executing the generated queries.',
					'tools-for-devs'
				);
				?>
			</p>

			<div class="wp-mig-row">
				<div class="wp-mig-field">
					<label for="<?php echo esc_attr($uid); ?>-from">
						<?php echo esc_html__('From', 'tools-for-devs'); ?>
					</label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-from" value="<?php echo $from; ?>"
						placeholder="<?php echo esc_attr__('http://oldsite.com', 'tools-for-devs'); ?>" />
				</div>

				<div class="wp-mig-field">
					<label for="<?php echo esc_attr($uid); ?>-to">
						<?php echo esc_html__('To', 'tools-for-devs'); ?>
					</label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-to" value="<?php echo $to; ?>"
						placeholder="<?php echo esc_attr__('http://newsite.com', 'tools-for-devs'); ?>" />
				</div>

				<div class="wp-mig-field wp-mig-field--prefix">
					<label for="<?php echo esc_attr($uid); ?>-prefix">
						<?php echo esc_html__('Prefix', 'tools-for-devs'); ?>
					</label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-prefix" value="<?php echo $prefix; ?>"
						placeholder="<?php echo esc_attr__('wp_', 'tools-for-devs'); ?>" />
				</div>
			</div>

			<div class="wp-mig-actions">
				<button class="wp-mig-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate queries', 'tools-for-devs'); ?>
				</button>
			</div>

			<div class="wp-mig-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your SQL queries will appear here...', 'tools-for-devs'); ?>"></textarea>

				<div class="wp-mig-help">
					<strong><?php echo esc_html__('Tip:', 'tools-for-devs'); ?></strong>
					<?php echo esc_html__(
						'Always create a database backup before running SQL queries. REPLACE() may break serialized data.',
						'tools-for-devs'
					); ?>
				</div>

				<div class="wp-mig-toast" data-toast style="display:none;">
					<?php echo esc_html__('Queries copied to clipboard.', 'tools-for-devs'); ?>
				</div>
			</div>
		</div>
		<?php

		$output = ob_get_clean();

		if (!empty($content)) {
			$output .= do_shortcode($content);
		}

		return $output;
	}


	public function tools_for_devs_shortcode_wp_db_prefix_tool($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'old_prefix' => 'wp_',
				'new_prefix' => '',
			),
			$atts,
			$tag
		);

		// Enqueue assets only when shortcode is rendered.
		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-sql',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);
		wp_enqueue_style($this->plugin_name . '-wp-db-prefix-tool');
		wp_enqueue_script($this->plugin_name . '-wp-db-prefix-tool');

		$uid = 'dbp-' . wp_generate_uuid4();
		$old_prefix = esc_attr($atts['old_prefix']);
		$new_prefix = esc_attr($atts['new_prefix']);

		ob_start();
		?>
		<div class="tfd-dbp-wrap" id="<?php echo esc_attr($uid); ?>" data-tfd-dbp="1">

			<p class="tfd-dbp-desc">
				<?php
				echo esc_html__(
					'Generate SQL queries to safely change the WordPress database table prefix. This can help harden security and is useful when migrating or standardizing database setups. Always create a full database backup before running these queries.',
					'tools-for-devs'
				);
				?>
			</p>

			<div class="tfd-dbp-row">
				<div class="tfd-dbp-field">
					<label
						for="<?php echo esc_attr($uid); ?>-old"><?php echo esc_html__('Old prefix', 'tools-for-devs'); ?></label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-old" value="<?php echo $old_prefix; ?>"
						placeholder="wp_" />
				</div>

				<div class="tfd-dbp-field">
					<label
						for="<?php echo esc_attr($uid); ?>-new"><?php echo esc_html__('New prefix', 'tools-for-devs'); ?></label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-new" value="<?php echo $new_prefix; ?>"
						placeholder="wpime_" />
				</div>

				<div class="tfd-dbp-field tfd-dbp-field--wide tfd-dbp-sites" style="display:none;">
					<label
						for="<?php echo esc_attr($uid); ?>-sites"><?php echo esc_html__('Site IDs, comma-separated', 'tools-for-devs'); ?></label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-sites" value="" placeholder="1, 2, 5, 12" />
				</div>
			</div>

			<div class="tfd-dbp-checks">
				<label class="tfd-dbp-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-woo" />
					<span><?php echo esc_html__('WooCommerce', 'tools-for-devs'); ?></span>
				</label>

				<label class="tfd-dbp-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-ms" />
					<span><?php echo esc_html__('Multisite', 'tools-for-devs'); ?></span>
				</label>
			</div>

			<div class="tfd-dbp-actions">
				<button class="tfd-dbp-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate queries', 'tools-for-devs'); ?>
				</button>

				<span class="tfd-dbp-toast" data-toast style="display:none;">
					<?php echo esc_html__('Copied to clipboard.', 'tools-for-devs'); ?>
				</span>
			</div>

			<div class="tfd-dbp-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your SQL queries will appear here...', 'tools-for-devs'); ?>"></textarea>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}


	public function tools_for_devs_shortcode_wp_plugin_header_generator($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'plugin_name' => '',
				'plugin_uri' => '',
				'version' => '1.0.0',
				'text_domain' => '',
				'domain_path' => '/languages',
				'license' => 'GPL v2 or later',
				'license_uri' => 'https://www.gnu.org/licenses/gpl-2.0.html',
				'author' => '',
				'author_uri' => '',
				'description' => '',
				'network' => 'false',
			),
			$atts,
			$tag
		);

		// Code editor (WordPress built-in CodeMirror).
		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-php',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);

		wp_enqueue_style($this->plugin_name . '-wp-plugin-header-generator');
		wp_enqueue_script($this->plugin_name . '-wp-plugin-header-generator');

		$uid = 'phg-' . wp_generate_uuid4();

		$plugin_name = esc_attr($atts['plugin_name']);
		$plugin_uri = esc_attr($atts['plugin_uri']);
		$version = esc_attr($atts['version']);
		$text_domain = esc_attr($atts['text_domain']);
		$domain_path = esc_attr($atts['domain_path']);
		$license = esc_attr($atts['license']);
		$license_uri = esc_attr($atts['license_uri']);
		$author = esc_attr($atts['author']);
		$author_uri = esc_attr($atts['author_uri']);
		$description = esc_attr($atts['description']);
		$network = esc_attr($atts['network']);

		ob_start();
		?>
		<div class="tfd-phg-wrap" id="<?php echo esc_attr($uid); ?>" data-tfd-phg="1">

			<p class="tfd-phg-desc">
				<?php echo esc_html__(
					'Generate a WordPress plugin header (plugin file comment block). Fill the fields, then click “Generate header” to copy the result.',
					'tools-for-devs'
				); ?>
			</p>

			<div class="tfd-phg-row">
				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-name"><?php echo esc_html__('Plugin Name', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-name" type="text" value="<?php echo $plugin_name; ?>"
						placeholder="<?php echo esc_attr__('My Plugin', 'tools-for-devs'); ?>">
				</div>

				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-uri"><?php echo esc_html__('Plugin URL', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-uri" type="text" value="<?php echo $plugin_uri; ?>"
						placeholder="https://example.com/my-plugin">
				</div>

				<div class="tfd-phg-field tfd-phg-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-version"><?php echo esc_html__('Version', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-version" type="text" value="<?php echo $version; ?>"
						placeholder="1.0.0">
				</div>
			</div>

			<div class="tfd-phg-row">
				<div class="tfd-phg-field tfd-phg-field--wide">
					<label
						for="<?php echo esc_attr($uid); ?>-desc"><?php echo esc_html__('Description', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-desc" type="text" value="<?php echo $description; ?>"
						placeholder="<?php echo esc_attr__('A short description of what your plugin does', 'tools-for-devs'); ?>">
				</div>
			</div>

			<div class="tfd-phg-row">
				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-author"><?php echo esc_html__('Author', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-author" type="text" value="<?php echo $author; ?>"
						placeholder="John Doe">
				</div>

				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-author-uri"><?php echo esc_html__('Author URL', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-author-uri" type="text" value="<?php echo $author_uri; ?>"
						placeholder="https://example.com">
				</div>

				<div class="tfd-phg-field tfd-phg-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-network"><?php echo esc_html__('Network', 'tools-for-devs'); ?></label>
					<select id="<?php echo esc_attr($uid); ?>-network">
						<option value="false" <?php selected($network, 'false'); ?>>
							<?php echo esc_html__('No', 'tools-for-devs'); ?>
						</option>
						<option value="true" <?php selected($network, 'true'); ?>>
							<?php echo esc_html__('Yes', 'tools-for-devs'); ?>
						</option>
					</select>
				</div>
			</div>

			<div class="tfd-phg-row">
				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-license"><?php echo esc_html__('License', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-license" type="text" value="<?php echo $license; ?>"
						placeholder="GPL v2 or later">
				</div>

				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-license-uri"><?php echo esc_html__('License URL', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-license-uri" type="text" value="<?php echo $license_uri; ?>"
						placeholder="https://www.gnu.org/licenses/gpl-2.0.html">
				</div>
			</div>

			<div class="tfd-phg-row">
				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-text-domain"><?php echo esc_html__('Text Domain', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-text-domain" type="text" value="<?php echo $text_domain; ?>"
						placeholder="my-plugin">
				</div>

				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-domain-path"><?php echo esc_html__('Domain Path', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-domain-path" type="text" value="<?php echo $domain_path; ?>"
						placeholder="/languages">
				</div>

				<div class="tfd-phg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-required"><?php echo esc_html__('Required Plugins', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-required" type="text" value=""
						placeholder="<?php echo esc_attr__('e.g. woocommerce, wp-crontrol', 'tools-for-devs'); ?>">
				</div>
			</div>

			<div class="tfd-phg-checks">
				<label class="tfd-phg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-woo">
					<span><?php echo esc_html__('WooCommerce', 'tools-for-devs'); ?></span>
				</label>
			</div>

			<div class="tfd-phg-actions">
				<button class="tfd-phg-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate header', 'tools-for-devs'); ?>
				</button>

				<span class="tfd-phg-toast" data-toast style="display:none;">
					<?php echo esc_html__('Copied to clipboard.', 'tools-for-devs'); ?>
				</span>
			</div>

			<div class="tfd-phg-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your plugin header will appear here...', 'tools-for-devs'); ?>"></textarea>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}


	public function tools_for_devs_shortcode_wp_wc_delete_products_sql_tool($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'prefix' => 'wp_',
			),
			$atts,
			$tag
		);

		// Enqueue assets only when shortcode is rendered.
		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-sql',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);

		wp_enqueue_style($this->plugin_name . '-wc-delete-products-sql-tool');
		wp_enqueue_script($this->plugin_name . '-wc-delete-products-sql-tool');

		$uid = 'wc-del-' . wp_generate_uuid4();
		$prefix = esc_attr($atts['prefix']);

		ob_start();
		?>
		<div class="wc-del-wrap" id="<?php echo esc_attr($uid); ?>" data-wc-del="1">

			<p class="wc-del-desc">
				<?php
				echo esc_html__(
					'Generate SQL queries to permanently delete all WooCommerce products (including variations) and related data. Always create a full database backup before running these queries.',
					'tools-for-devs'
				);
				?>
			</p>

			<div class="wc-del-row">
				<div class="wc-del-field wc-del-field--prefix">
					<label
						for="<?php echo esc_attr($uid); ?>-prefix"><?php echo esc_html__('Prefix', 'tools-for-devs'); ?></label>
					<input type="text" id="<?php echo esc_attr($uid); ?>-prefix" value="<?php echo $prefix; ?>"
						placeholder="<?php echo esc_attr__('wp_', 'tools-for-devs'); ?>" />
				</div>

				<label class="wc-del-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-truncate" />
					<span><?php echo esc_html__('Use TRUNCATE for WooCommerce lookup tables (faster)', 'tools-for-devs'); ?></span>
				</label>
			</div>

			<div class="wc-del-actions">
				<button class="wc-del-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate query', 'tools-for-devs'); ?>
				</button>

				<span class="wc-del-toast" data-toast style="display:none;">
					<?php echo esc_html__('Queries copied to clipboard.', 'tools-for-devs'); ?>
				</span>
			</div>

			<div class="wc-del-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your SQL queries will appear here...', 'tools-for-devs'); ?>"></textarea>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}


	public function tools_for_devs_shortcode_acf_field_generator($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'field_name' => 'my_field',
				'field_label' => 'My Field',
				'text_domain' => 'my-plugin',
			),
			$atts,
			$tag
		);

		// Code editor (WordPress built-in CodeMirror).
		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-php',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);

		wp_enqueue_style($this->plugin_name . '-wp-acf-field-generator');
		wp_enqueue_script($this->plugin_name . '-wp-acf-field-generator');

		$uid = 'acfgen-' . wp_generate_uuid4();
		$field_name = esc_attr($atts['field_name']);
		$field_label = esc_attr($atts['field_label']);
		$text_domain = esc_attr($atts['text_domain']);

		ob_start();
		?>
		<div class="acfgen-wrap" id="<?php echo esc_attr($uid); ?>" data-acfgen="1">

			<p class="acfgen-desc">
				<?php echo esc_html__(
					'Generate boilerplate code for a custom field compatible with ACF / SCF. Fill the settings and click “Generate” to copy the result.',
					'tools-for-devs'
				); ?>
			</p>

			<div class="acfgen-row">
				<div class="acfgen-field">
					<label
						for="<?php echo esc_attr($uid); ?>-name"><?php echo esc_html__('Field Name (slug)', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-name" type="text" value="<?php echo $field_name; ?>"
						placeholder="my_field">
				</div>

				<div class="acfgen-field">
					<label
						for="<?php echo esc_attr($uid); ?>-label"><?php echo esc_html__('Field Label', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-label" type="text" value="<?php echo $field_label; ?>"
						placeholder="My Field">
				</div>

				<div class="acfgen-field acfgen-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-category"><?php echo esc_html__('Category', 'tools-for-devs'); ?></label>
					<select id="<?php echo esc_attr($uid); ?>-category">
						<option value="basic">basic</option>
						<option value="content">content</option>
						<option value="choice">choice</option>
						<option value="relational">relational</option>
						<option value="layout">layout</option>
						<option value="jquery">jquery</option>
					</select>
				</div>
			</div>

			<div class="acfgen-row">
				<div class="acfgen-field acfgen-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-class"><?php echo esc_html__('PHP Class Name', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-class" type="text" value="" placeholder="CFB_Field_My_Field">
				</div>

				<div class="acfgen-field">
					<label
						for="<?php echo esc_attr($uid); ?>-textdomain"><?php echo esc_html__('Text Domain', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-textdomain" type="text" value="<?php echo $text_domain; ?>"
						placeholder="my-plugin">
				</div>

				<div class="acfgen-field acfgen-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-versionconst"><?php echo esc_html__('Version Constant', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-versionconst" type="text" value="MY_PLUGIN_VERSION"
						placeholder="MY_PLUGIN_VERSION">
				</div>
			</div>

			<div class="acfgen-checks">
				<label class="acfgen-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-hasjs" checked>
					<span><?php echo esc_html__('Include JS (acf.Field.extend)', 'tools-for-devs'); ?></span>
				</label>

				<label class="acfgen-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-hascss">
					<span><?php echo esc_html__('Include CSS', 'tools-for-devs'); ?></span>
				</label>

				<label class="acfgen-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-supportsrest" checked>
					<span><?php echo esc_html__('Show in REST', 'tools-for-devs'); ?></span>
				</label>

				<label class="acfgen-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-supportsrequired" checked>
					<span><?php echo esc_html__('Supports Required', 'tools-for-devs'); ?></span>
				</label>
			</div>

			<div class="acfgen-actions">
				<button class="acfgen-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate', 'tools-for-devs'); ?>
				</button>

				<span class="acfgen-toast" data-toast style="display:none;">
					<?php echo esc_html__('Copied to clipboard.', 'tools-for-devs'); ?>
				</span>
			</div>

			<div class="acfgen-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your generated code will appear here...', 'tools-for-devs'); ?>"></textarea>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}

	public function tools_for_devs_shortcode_wp_rest_route_generator($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'namespace' => 'my-plugin/v1',
				'route' => '/items/(?P<id>\\d+)',
			),
			$atts,
			$tag
		);

		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-php',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);

		wp_enqueue_style($this->plugin_name . '-wp-rest-route-generator');
		wp_enqueue_script($this->plugin_name . '-wp-rest-route-generator');

		$uid = 'rrg-' . wp_generate_uuid4();
		$namespace = esc_attr($atts['namespace']);
		$route = esc_attr($atts['route']);

		ob_start();
		?>
		<div class="rrg-wrap" id="<?php echo esc_attr($uid); ?>" data-rrg="1">

			<p class="rrg-desc">
				<?php echo esc_html__(
					'Generate WordPress REST API boilerplate with register_rest_route(), optional Controller Class mode, automatic path parameter detection, dynamic args table, and curl examples.',
					'tools-for-devs'
				); ?>
			</p>

			<div class="rrg-row">
				<div class="rrg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-ns"><?php echo esc_html__('Namespace', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-ns" type="text" value="<?php echo $namespace; ?>"
						placeholder="my-plugin/v1">
				</div>

				<div class="rrg-field">
					<label
						for="<?php echo esc_attr($uid); ?>-route"><?php echo esc_html__('Route', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-route" type="text" value="<?php echo $route; ?>"
						placeholder="/items/(?P<id>\d+)">
					<div class="rrg-hint">
						<?php echo esc_html__('Tip: Use path params like (?P<id>\\d+) and we will auto-detect them.', 'tools-for-devs'); ?>
					</div>
				</div>

				<div class="rrg-field rrg-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-method"><?php echo esc_html__('Method', 'tools-for-devs'); ?></label>
					<select id="<?php echo esc_attr($uid); ?>-method">
						<option value="GET">GET</option>
						<option value="POST">POST</option>
						<option value="PUT">PUT</option>
						<option value="PATCH">PATCH</option>
						<option value="DELETE">DELETE</option>
					</select>
				</div>
			</div>

			<div class="rrg-row">
				<div class="rrg-field rrg-field--sm">
					<label
						for="<?php echo esc_attr($uid); ?>-perm"><?php echo esc_html__('Permission', 'tools-for-devs'); ?></label>
					<select id="<?php echo esc_attr($uid); ?>-perm">
						<option value="public"><?php echo esc_html__('Public', 'tools-for-devs'); ?></option>
						<option value="logged_in"><?php echo esc_html__('Logged-in users', 'tools-for-devs'); ?></option>
						<option value="capability"><?php echo esc_html__('Capability', 'tools-for-devs'); ?></option>
					</select>
				</div>

				<div class="rrg-field rrg-field--wide rrg-cap" style="display:none;">
					<label
						for="<?php echo esc_attr($uid); ?>-cap"><?php echo esc_html__('Capability', 'tools-for-devs'); ?></label>
					<input id="<?php echo esc_attr($uid); ?>-cap" type="text" value="manage_options"
						placeholder="manage_options">
				</div>

				<label class="rrg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-controller">
					<span><?php echo esc_html__('Generate as Controller Class', 'tools-for-devs'); ?></span>
				</label>
			</div>

			<div class="rrg-args-head">
				<strong><?php echo esc_html__('Parameters (args)', 'tools-for-devs'); ?></strong>
				<button type="button" class="rrg-mini-btn"
					data-action="add-arg"><?php echo esc_html__('+ Add param', 'tools-for-devs'); ?></button>
			</div>

			<div class="rrg-args-table" data-args-table>
				<div class="rrg-args-row rrg-args-row--head">
					<div><?php echo esc_html__('Name', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('In', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Type', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Required', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Sanitize', 'tools-for-devs'); ?></div>
					<div></div>
				</div>

				<!-- JS will insert rows here -->
			</div>

			<div class="rrg-actions">
				<button class="rrg-btn" type="button"
					data-action="generate"><?php echo esc_html__('Generate', 'tools-for-devs'); ?></button>
				<span class="rrg-toast" data-toast
					style="display:none;"><?php echo esc_html__('Copied to clipboard.', 'tools-for-devs'); ?></span>
			</div>

			<div class="rrg-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your generated code will appear here...', 'tools-for-devs'); ?>"></textarea>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}

	public function tools_for_devs_shortcode_wp_db_crud_generator($atts, $content = '', $tag = ''): string
	{

		$atts = shortcode_atts(
			array(
				'entity' => 'item',
				'table' => 'tfd_items',
				'namespace' => 'tools-for-devs/v1',
				'capability' => 'manage_options',
				'option_key' => 'tfd_db_version',
				'version_const' => 'TOOLS_FOR_DEVS_VERSION',
			),
			$atts,
			$tag
		);

		wp_enqueue_code_editor(
			array(
				'type' => 'text/x-php',
				'codemirror' => array(
					'lineNumbers' => true,
					'lineWrapping' => true,
					'readOnly' => true,
				),
			)
		);

		wp_enqueue_style($this->plugin_name . '-wp-db-crud-generator');
		wp_enqueue_script($this->plugin_name . '-wp-db-crud-generator');

		$uid = 'dbcg-' . wp_generate_uuid4();

		ob_start();
		?>
		<div class="dbcg-wrap" id="<?php echo esc_attr($uid); ?>" data-dbcg="1">

			<div class="dbcg-intro">
				<h3 class="dbcg-title"><?php echo esc_html__('DB CRUD Generator (Custom Tables)', 'tools-for-devs'); ?></h3>

				<p class="dbcg-desc">
					<?php echo esc_html__(
						'This generator creates a complete CRUD stack for a custom WordPress database table using $wpdb: an installer (dbDelta + versioned upgrades), a repository (create/read/update/delete/list), and an optional REST API controller. Click Generate to get copy-paste ready code.',
						'tools-for-devs'
					); ?>
				</p>

				<div class="dbcg-quickstart" role="note"
					aria-label="<?php echo esc_attr__('Quick start', 'tools-for-devs'); ?>">
					<div class="dbcg-quickstart-title">
						<?php echo esc_html__('3-step Quick start', 'tools-for-devs'); ?>
					</div>
					<ol class="dbcg-quickstart-steps">
						<li><?php echo esc_html__('Fill the form', 'tools-for-devs'); ?></li>
						<li><?php echo esc_html__('Click Generate (auto-copy)', 'tools-for-devs'); ?></li>
						<li><?php echo esc_html__('Paste into my-plugin.php, zip, upload', 'tools-for-devs'); ?></li>
					</ol>
				</div>

				<ul class="dbcg-bullets">
					<li><?php echo esc_html__('Best for data that should not live in wp_posts/wp_postmeta (logs, queues, tickets, picking, sync tables, etc.).', 'tools-for-devs'); ?>
					</li>
					<li><?php echo esc_html__('Includes safe defaults: column allow-list, prepared queries, and upgrade checks.', 'tools-for-devs'); ?>
					</li>
					<li><?php echo esc_html__('Always back up your database before deploying schema changes.', 'tools-for-devs'); ?>
					</li>
				</ul>
			</div>

			<div class="dbcg-grid">

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-entity"><?php echo esc_html__('Entity name', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('Used to build class names and the REST route base.', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-entity" type="text"
						value="<?php echo esc_attr($atts['entity']); ?>" placeholder="ticket">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>ticket</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ REST: /ticket, PHP classes: Ticket_Repository', 'tools-for-devs'); ?></span>
					</div>
				</div>

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-table"><?php echo esc_html__('Table name (without wp_ prefix)', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('The table name suffix. WordPress prefix is added automatically via $wpdb->prefix.', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-table" type="text" value="<?php echo esc_attr($atts['table']); ?>"
						placeholder="my_plugin_tickets">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>my_plugin_tickets</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ Real table: wp_my_plugin_tickets (or wp_2_... on multisite)', 'tools-for-devs'); ?></span>
					</div>
				</div>

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-ns"><?php echo esc_html__('REST namespace', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('The REST API namespace used under /wp-json/{namespace}/...', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-ns" type="text" value="<?php echo esc_attr($atts['namespace']); ?>"
						placeholder="my-plugin/v1">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>my-plugin/v1</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ Base: /wp-json/my-plugin/v1/ticket', 'tools-for-devs'); ?></span>
					</div>
				</div>

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-cap"><?php echo esc_html__('Write capability', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('Users must have this capability to create/update/delete via REST. Reads are public by default.', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-cap" type="text"
						value="<?php echo esc_attr($atts['capability']); ?>" placeholder="manage_options">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>manage_options</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ Admin-only writes', 'tools-for-devs'); ?></span>
					</div>
				</div>

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-opt"><?php echo esc_html__('DB version option key', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('Stored in wp_options to track installed schema version and run upgrades safely.', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-opt" type="text"
						value="<?php echo esc_attr($atts['option_key']); ?>" placeholder="my_plugin_db_version">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>my_plugin_db_version</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ Used by Installer::maybe_upgrade()', 'tools-for-devs'); ?></span>
					</div>
				</div>

				<div class="dbcg-field">
					<div class="dbcg-label">
						<label
							for="<?php echo esc_attr($uid); ?>-vconst"><?php echo esc_html__('Version constant', 'tools-for-devs'); ?></label>

						<span class="dbcg-tipwrap">
							<button type="button" class="dbcg-tipbtn"
								aria-label="<?php echo esc_attr__('What is this?', 'tools-for-devs'); ?>">
								<span aria-hidden="true">ⓘ</span>
							</button>
							<span class="dbcg-tooltip" role="tooltip">
								<strong><?php echo esc_html__('What is this?', 'tools-for-devs'); ?></strong><br>
								<?php echo esc_html__('Constant used as the “target version” for schema upgrades (usually your plugin version constant).', 'tools-for-devs'); ?>
							</span>
						</span>
					</div>

					<input id="<?php echo esc_attr($uid); ?>-vconst" type="text"
						value="<?php echo esc_attr($atts['version_const']); ?>" placeholder="MY_PLUGIN_VERSION">

					<div class="dbcg-example">
						<?php echo esc_html__('Example:', 'tools-for-devs'); ?>
						<code>MY_PLUGIN_VERSION</code>
						<span
							class="dbcg-example-muted"><?php echo esc_html__('→ When version changes, schema upgrades can run', 'tools-for-devs'); ?></span>
					</div>
				</div>

			</div>

			<div class="dbcg-checks">
				<label class="dbcg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-timestamps" checked>
					<span><?php echo esc_html__('Add created_at / updated_at', 'tools-for-devs'); ?></span>
					<em
						class="dbcg-check-hint"><?php echo esc_html__('Adds timestamps and auto-updates updated_at on updates.', 'tools-for-devs'); ?></em>
				</label>

				<label class="dbcg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-charset" checked>
					<span><?php echo esc_html__('Use $wpdb charset/collate', 'tools-for-devs'); ?></span>
					<em
						class="dbcg-check-hint"><?php echo esc_html__('Recommended for UTF-8 and consistent collations.', 'tools-for-devs'); ?></em>
				</label>

				<label class="dbcg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-repo" checked>
					<span><?php echo esc_html__('Generate Repository CRUD', 'tools-for-devs'); ?></span>
					<em
						class="dbcg-check-hint"><?php echo esc_html__('Creates PHP methods: create/get/list/update/delete.', 'tools-for-devs'); ?></em>
				</label>

				<label class="dbcg-check">
					<input type="checkbox" id="<?php echo esc_attr($uid); ?>-rest" checked>
					<span><?php echo esc_html__('Generate REST CRUD Controller', 'tools-for-devs'); ?></span>
					<em
						class="dbcg-check-hint"><?php echo esc_html__('Creates /wp-json/{namespace}/{entity} endpoints.', 'tools-for-devs'); ?></em>
				</label>
			</div>

			<div class="dbcg-head">
				<strong><?php echo esc_html__('Columns', 'tools-for-devs'); ?></strong>
				<span class="dbcg-subhead">
					<?php echo esc_html__('Add your table columns below. The generator will ensure an id primary key exists.', 'tools-for-devs'); ?>
				</span>
				<button type="button" class="dbcg-mini-btn" data-action="add-col">
					<?php echo esc_html__('+ Add column', 'tools-for-devs'); ?>
				</button>
			</div>

			<div class="dbcg-table" data-cols>
				<div class="dbcg-rowh">
					<div><?php echo esc_html__('Name', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('SQL Type', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Null', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Default', 'tools-for-devs'); ?></div>
					<div><?php echo esc_html__('Extra', 'tools-for-devs'); ?></div>
					<div></div>
				</div>
			</div>

			<div class="dbcg-actions">
				<button class="dbcg-btn" type="button" data-action="generate">
					<?php echo esc_html__('Generate CRUD', 'tools-for-devs'); ?>
				</button>
				<span class="dbcg-toast" data-toast style="display:none;">
					<?php echo esc_html__('Copied to clipboard.', 'tools-for-devs'); ?>
				</span>
			</div>

			<div class="dbcg-out">
				<textarea readonly id="<?php echo esc_attr($uid); ?>-out"
					placeholder="<?php echo esc_attr__('Your generated code will appear here...', 'tools-for-devs'); ?>"></textarea>

				<div class="dbcg-tip">
					<strong><?php echo esc_html__('Tip:', 'tools-for-devs'); ?></strong>
					<?php echo esc_html__(
						'After generating, paste the code into my-plugin.php, zip it, and upload it as a plugin. Always test on staging first.',
						'tools-for-devs'
					); ?>
				</div>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}



}
