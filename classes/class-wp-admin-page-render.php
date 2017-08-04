<?php

/**
 * Class Name: WPAdminPageRender
 * Class URI: https://github.com/nikolays93/classes.git
 * Description: Create a new custom admin page.
 * Version: 2.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !function_exists('array_filter_recursive') ) {
	function array_filter_recursive($input){
		foreach ($input as &$value) {
			if ( is_array($value) )
				$value = array_filter_recursive($value);
		}

		return array_filter($input);
	}
}

if ( !function_exists('array_map_recursive') ) {
	function array_map_recursive($callback, $array){
		$func = function ($item) use (&$func, &$callback) {
			return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
		};

		return array_map($func, $array);
	}
}

if( class_exists('WPAdminPageRender') )
	return;

class WPAdminPageRender
{
	const ver = '2.0';

	public $page = '';
	public $screen = '';
	public $option_name = '';
	public $tab_sections = array();

	protected $args = array(
		'parent'      => 'options-general.php',
		'title'       => '',
		'menu'        => 'Test page',
		'permissions' => 'manage_options',
		'tab_sections'=> null,
		);
	protected $page_content_cb = '';
	protected $page_valid_cb = '';

	protected $metaboxes = array();

	function __construct( $page_slug, $args, $page_content_cb, $option_name = false, $valid_cb = false ){
		// slug required
		if( !$page_slug )
			wp_die( 'You have false slug in admin page class', 'Slug is false or empty' );

		$this->page = $page_slug;
		if( is_array( $args ) )
			$this->args = array_merge( $this->args, $args );

		if(!empty($this->args['tab_sections']))
			$this->tab_sections = $this->args['tab_sections'];

		$this->page_content_cb = $page_content_cb;
		$this->option_name = ( $option_name ) ? $option_name : $this->page;
		$this->page_valid_cb = ($valid_cb) ? $valid_cb : array($this, 'validate_options');

		add_action('admin_menu', array($this,'add_page'));
		add_action('admin_init', array($this,'register_option_page'));
	}

	/**
	 * Add page wordpress handle
	 *
	 * @see wordpress codex : add_submenu_page()
	 */
	function add_page(){
		$this->screen = add_submenu_page(
			$this->args['parent'],
			$this->args['title'],
			$this->args['menu'],
			$this->args['permissions'],
			$this->page,
			array($this,'render_page'), 10);

		add_action('load-'.$this->screen, array($this,'page_actions'),9);
		add_action('admin_footer-'.$this->screen, array($this,'footer_scripts'));
	}

	function _metabox(){
		foreach ($this->metaboxes as $metabox) {
			extract($metabox);

			add_meta_box( $handle, $label, $render_cb, $this->screen, $position, $priority);
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
	 * Init actions for created page
	 */
	function page_actions(){
		add_action( $this->page . '_inside_page_content', array($this, 'page_render'), 10);

		add_action( $this->page . '_inside_side_container', array($this, 'side_render'), 10 );

		add_action( $this->page . '_inside_normal_container', array($this, 'normal_render'), 10 );
		add_action( $this->page . '_inside_advanced_container', array($this, 'advanced_render'), 10 );

		do_action('add_meta_boxes_'.$this->screen, null);
		do_action('add_meta_boxes', $this->screen, null);

		$columns = apply_filters( $this->page . '_columns', 1 );
		add_screen_option('layout_columns', array('max' => $columns, 'default' => $columns) );

		// Enqueue WordPress' script for handling the metaboxes
		wp_enqueue_script('postbox');
	}

	function page_render(){
		/** @ Experemental ! (tabs) */
		if( is_array($this->page_content_cb) && !empty($this->args['tab_sections']) ){
			if (!empty($_GET['tab'])){
				$current = $_GET['tab'];
			}
			else {
				reset($this->tab_sections);
				$current = key($this->tab_sections);
			}

			echo '<style>#tabs.navs {padding-bottom: 0;margin: 0 0 8px;}</style><h2 id="tabs" class="navs nav-tab-wrapper">';
			foreach ( $this->tab_sections as $tab => $tab_title) {
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab{$class}' href='?page=".$this->page."&tab={$tab}' data-tab='{$tab}'>$tab_title</a>";
			}
			echo '</h2>';

			foreach ($this->page_content_cb as $tab => $render_cb) {
				$class = ($tab == $current) ? '' : ' class="hidden"';
				echo "<div id='{$tab}'{$class}>";
				call_user_func($render_cb);
				echo "</div>";
			}
		}
		else {
			call_user_func($this->page_content_cb);
		}
	}
	function side_render(){
		do_meta_boxes($this->screen,'side',null);
	}
	function normal_render(){
		do_meta_boxes($this->screen,'normal',null);
	}
	function advanced_render(){
		do_meta_boxes($this->screen,'advanced',null);
	}

	function footer_scripts(){

		echo "<script> jQuery(document).ready(function($){ postboxes.add_postbox_toggles(pagenow); });</script>";
		if( !empty($this->args['tab_sections']) ):
		$eg = __('e.g. '); ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
					if($(this).val() == ''){
						$(this).val($(this).attr('placeholder').replace('<?php echo $eg; ?>', '') );
						$(this).select();
					}
				});

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
	function render_page(){
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
							 * @hooked array('WPAdminPageRender', 'page_render') - 10
							 */
							do_action( $this->page . '_inside_page_content');
							?>
						</div>

						<div id="postbox-container-1" class="postbox-container side-container">
							<?php
							/**
							 * $page_slug . _inside_side_container hook.
							 *
							 * @hooked array('WPAdminPageRender', 'side_render') - 10
							 */
							do_action( $this->page . '_inside_side_container');
							?>
						</div>

						<div id="postbox-container-2" class="postbox-container normal-container">
							<?php
							/**
							 * $page_slug . _inside_normal_container hook.
							 *
							 * @hooked array('WPAdminPageRender', 'normal_render') - 10
							 */
							do_action( $this->page . '_inside_normal_container');
							?>
						</div>
						<div id="postbox-container-3" class="postbox-container advanced-container">
							<?php
							/**
							 * $page_slug . _inside_advanced_container hook.
							 *
							 * @hooked array('WPAdminPageRender', 'advanced_render') - 10
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
						settings_fields( $this->option_name );
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
	function register_option_page(){

		register_setting( $this->option_name, $this->option_name, $this->page_valid_cb );
	}
	/**
	 * Validate registred options
	 *
	 * @param  _POST $inputs post data for update
	 * @return array $inputs filtred data for save
	 */
	function validate_options( $inputs ){
		// $debug = array();
		// $debug['before'] = $inputs;

		$inputs = array_map_recursive( 'sanitize_text_field', $inputs );
		$inputs = array_filter_recursive($inputs);

		// $debug['after'] = $inputs;
		// file_put_contents(__DIR__.'/valid.log', print_r($debug, 1));

		return $inputs;
	}
}