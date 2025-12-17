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

}
