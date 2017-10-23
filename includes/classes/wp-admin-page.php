<?php

namespace CDevelopers\tool;

/**
 * Class Name: WP_Admin_Page
 * Class URI: https://github.com/nikolays93/WPAdminPage
 * Description: Create a new custom admin page.
 * Version: 2.2
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @todo  : add method for tab_sections ( add tab section )
 */

class WP_Admin_Page
{
	public $page = '';
	public $screen = '';

	protected $args;
	protected $metaboxes = array();
	protected $tab_sections = array();

	function __construct( $page_slug = false )
	{
		$this->page = $page_slug;
	}

	public function set_args( $deprecated, $args = array() )
	{
		if( $this->page ) {
			$args = $deprecated;
		}
		else {
			$this->page = $deprecated;
		}

		// slug required
		if( ! $this->page ){
			wp_die( 'You have false slug in admin page class in ' . __FILE__, 'Slug is false or empty' );
		}

		$this->args = wp_parse_args( $args, array(
			'parent'      => 'options-general.php',
			'title'       => '',
			'menu'        => 'New Page',
			'menu_pos'    => 50,
			'callback'    => array($this, 'not_set_callback'),
			'validate'    => array($this, 'validate_options'),
			'permissions' => 'manage_options',
			'tab_sections'=> null,
			'columns'     => 1,
			'icon_url'    => '',
			) );

		add_action('admin_menu', array($this,'_add_page'));
		add_action('admin_init', array($this,'register_option_page'));
	}

	public function set_assets( $callback )
	{
		if( isset($_GET['page']) && $_GET['page'] == $this->page ) {
			add_action( 'admin_enqueue_scripts', $callback );
		}
	}

	public function add_metabox( $handle, $label, $render_cb, $position = 'normal', $priority = 'high'){
		$this->metaboxes[] = array(
			'handle' => $handle,
			'label' => $label,
			'render_cb' => $render_cb,
			'position' => $position,
			'priority' => $priority
			);
	}

	public function set_metaboxes(){
		add_action( 'add_meta_boxes', array($this, '_metabox') );
	}

	/**
	 * Empty callback arg placeholder
	 * @return die with error if WP_DEBUG
	 */
	function not_set_callback()
	{
		if( WP_DEBUG ) {
			wp_die( "Callback param not defined! @see more https://github.com/nikolays93/WPAdminPage" );
		}
	}

	/**
	 * Add page wordpress handle
	 *
	 * @see wordpress codex : add_submenu_page()
	 */
	function _add_page()
	{
		if( $this->args['parent'] ) {
			$this->screen = add_submenu_page(
				$this->args['parent'],
				$this->args['title'],
				$this->args['menu'],
				$this->args['permissions'],
				$this->page,
				array($this,'render_page'),
				$this->args['menu_pos']
				);
		}
		else {
			$this->screen = add_menu_page(
				$this->args['title'],
				$this->args['menu'],
				$this->args['permissions'],
				$this->page,
				array($this,'render_page'),
				$this->args['icon_url'],
				$this->args['menu_pos']
				);
		}

		add_action('load-'.$this->screen, array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->screen, array($this,'footer_scripts'));
	}

	function _metabox()
	{
		if( ! $this->screen ) return;

		foreach ($this->metaboxes as $m) {
			add_meta_box( $m['handle'], $m['label'], $m['render_cb'], $this->screen, $m['position'], $m['priority']);
		}
	}

	/**
	 * Init actions for created page
	 */
	function page_actions()
	{
		add_action( $this->page . '_inside_page_content', array($this, 'page_render'), 10);

		add_action( $this->page . '_inside_side_container', array($this, 'side_render'), 10 );

		add_action( $this->page . '_inside_normal_container', array($this, 'normal_render'), 10 );
		add_action( $this->page . '_inside_advanced_container', array($this, 'advanced_render'), 10 );

		do_action('add_meta_boxes_'.$this->screen, null);
		do_action('add_meta_boxes', $this->screen, null);

		add_screen_option('layout_columns', array(
			'max' => $this->args['columns'],
			'default' => $this->args['columns'])
		);

		// Enqueue WordPress' script for handling the metaboxes
		wp_enqueue_script('postbox');
	}

	private static function tabs_render($sections, $callbacks)
	{
		if ( ! empty( $_GET['tab'] ) ) {
			$current = sanitize_text_field( $_GET['tab'] );
		}
		else {
			reset( $callbacks );
			$current = key( $callbacks );
		}

		echo '<style>#tabs.navs {padding-bottom: 0;margin: 0 0 8px;}</style>';
		echo '<h2 id="tabs" class="navs nav-tab-wrapper">';

		$host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		foreach ($sections as $tab_section) {
			if( is_array($tab_section) ) {
				$section_key = key($tab_section);
				$section_value = current($tab_section);
			}
			else {
				$section_key = key( $callbacks );
				$section_value = $tab_section;
				next( $callbacks );
			}

			$get = array();
			foreach ($_GET as $key => $value) {
				if( $key !== 'tab' ) {
					$get[] = $key . '=' . $value;
				}
			}
			$get[] = 'tab=' . $section_key;

			$href = $host . '?' . implode('&', $get);
			$class = $section_key == $current ? 'nav-tab nav-tab-active' : 'nav-tab';

			echo sprintf('<a href="%s" class="%s" data-tab="%s">%s</a>',
				esc_url( $href ),
				$class,
				esc_attr( $section_key ),
				esc_html( $section_value )
				);
		}
		echo '</h2>';

		foreach ($callbacks as $tab => $render_cb) {
			echo sprintf('<div id="%s" class="%s">',
				esc_attr( $tab ),
				$tab !== $current ? 'hidden' : ''
				);
			call_user_func($render_cb);
			echo "</div>";
		}
	}

	function page_render()
	{
		if( is_array($this->args['callback']) && !empty($this->args['tab_sections']) ){
			self::tabs_render($this->args['tab_sections'], $this->args['callback']);
		}
		else {
			call_user_func($this->args['callback']);
		}
	}

	function side_render()
	{
		do_meta_boxes($this->screen,'side',null);
	}

	function normal_render()
	{
		do_meta_boxes($this->screen,'normal',null);
	}

	function advanced_render()
	{
		do_meta_boxes($this->screen,'advanced',null);
	}

	function footer_scripts()
	{

		echo "<script> jQuery(document).ready(function($){ postboxes.add_postbox_toggles(pagenow); });</script>";
		if( !empty($this->args['tab_sections']) ):
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {

					$('a.nav-tab').on('click', function(e){
						e.preventDefault();
						if($(this).hasClass('nav-tab-active'))
							return false;

						var loc = window.location.href.split('&tab')[0] + '&tab=' + $(this).attr('data-tab');
						history.replaceState(null, null, loc);
						$('input[name="_wp_http_referer"]').val(loc + '&settings-updated=true');

						$(this).closest('div').find('#' + $('.nav-tab-active').attr('data-tab')).addClass('hidden');
						$('.nav-tab-active').removeClass('nav-tab-active');

						$(this).closest('div').find('#' + $(this).attr('data-tab') ).removeClass('hidden');
						$(this).addClass('nav-tab-active');
					});
				});
			</script>
		<?php
		endif;
	}

	/**
	 * View html on added page
	 *
	 * @has_hooks:
	 * $pageslug . _after_title (default empty hook)
	 * $pageslug . _before_form_inputs (default empty hook)
	 * $pageslug . _inside_page_content
	 * $pageslug . _inside_side_container
	 * $pageslug . _inside_advanced_container
	 * $pageslug . _after_form_inputs (default empty hook)
	 * $pageslug . _after_page_wrap (default empty hook)
	 *
	 * @has_fiters
	 * $pageslug . _form_action
	 * $pageslug . _form_method
	 */
	function render_page()
	{
		?>

		<div class="wrap">

			<?php screen_icon(); ?>
			<h2> <?php echo esc_html($this->args['title']);?> </h2>

			<?php do_action( $this->page . '_after_title'); ?>

			<?php
				$action = apply_filters( $this->page . '_form_action', 'options.php');
				$method = apply_filters( $this->page . '_form_method', 'post');
			?>

			<form id="options" enctype="multipart/form-data" action="<?php echo $action; ?>" method="<?php echo $method; ?>">
				<?php do_action( $this->page . '_before_form_inputs'); ?>

				<div id="poststuff">

					<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

						<div id="post-body-content">
							<?php
							/**
							 * $page_slug . _inside_page_content hook.
							 *
							 * @hooked array('WPAdminPage', 'page_render') - 10
							 */
							do_action( $this->page . '_inside_page_content');
							?>
						</div>

						<div id="postbox-container-1" class="postbox-container side-container">
							<?php
							/**
							 * $page_slug . _inside_side_container hook.
							 *
							 * @hooked array('WPAdminPage', 'side_render') - 10
							 */
							do_action( $this->page . '_inside_side_container');
							?>
						</div>

						<div id="postbox-container-2" class="postbox-container normal-container">
							<?php
							/**
							 * $page_slug . _inside_normal_container hook.
							 *
							 * @hooked array('WPAdminPage', 'normal_render') - 10
							 */
							do_action( $this->page . '_inside_normal_container');
							?>
						</div>
						<div id="postbox-container-3" class="postbox-container advanced-container">
							<?php
							/**
							 * $page_slug . _inside_advanced_container hook.
							 *
							 * @hooked array('WPAdminPage', 'advanced_render') - 10
							 */
							do_action( $this->page . '_inside_advanced_container');
							?>
						</div>

					</div> <!-- #post-body -->
				</div> <!-- #poststuff -->

				<?php
					/* Used to save closed metaboxes and their order */
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					// add hidden settings
					if($action == 'options.php')
						settings_fields( $this->page );
				?>

				<?php do_action( $this->page . '_after_form_inputs'); ?>
			</form>

		</div><!-- .wrap -->

		<div class="clear" style="clear: both;"></div>

		<?php do_action( $this->page . '_after_page_wrap'); ?>

		<?php
	}

	/**
	 * Register page settings
	 */
	function register_option_page()
	{
		register_setting( $this->page, $this->page, $this->args['validate'] );
	}

	/**
	 * Validate registred options
	 *
	 * @param  _POST $inputs post data for update
	 * @return array $inputs filtred data for save
	 */
	function validate_options( $inputs )
	{
		// $debug = array();
		// $debug['before'] = $inputs;
		$inputs = self::array_map_recursive( 'sanitize_text_field', $inputs );
		$inputs = self::array_filter_recursive($inputs);
		// $debug['after'] = $inputs;
		// file_put_contents(__DIR__.'/valid.log', print_r($debug, 1));

		return $inputs;
	}

	public static function array_filter_recursive($input)
	{
		foreach ($input as &$value) {
			if ( is_array($value) )
				$value = self::array_filter_recursive($value);
		}

		return array_filter($input);
	}

	public static function array_map_recursive($callback, $array)
	{
		$func = function ($item) use (&$func, &$callback) {
			return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
		};

		return array_map($func, $array);
	}
}
