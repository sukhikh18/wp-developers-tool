<?php
class AssetsEnqueuer // extends AnotherClass
{
  protected $suffix = '';
  public $settings;

  function __construct() {
    if( function_exists('is_wp_debug') && is_wp_debug() !== false )
      $this->suffix = '.min';

    $this->settings = get_option( DT_PLUGIN_NAME );
    $this->add_assets();

    add_filter( 'remove_cyrillic', array($this, 'remove_cyrillic_filter'), 10, 1 );
  }

  function remove_cyrillic_filter($str){
    $pattern = "/[\x{0410}-\x{042F}]+.*[\x{0410}-\x{042F}]+/iu";
    $str = preg_replace( $pattern, "", $str );
    
    return $str;
  }
          
  function assets(){
    extract( $this->settings );
    $suffix = $this->suffix;

    if( isset( $smooth_scroll ) )
      add_action('wp_footer', array($this, 'init_scroll'), 99 );

    // sticky
    if( isset( $sticky ) ){
      wp_enqueue_script('sticky', DT_ASSETS_URL . 'jquery.sticky'.$suffix.'.js', array('jquery'), '1.0.4', true);
      add_action('wp_footer', array($this, 'init_sticky'), 99 );
    }

    // animate
    if( isset( $animate ) )
      wp_enqueue_style('animate', DT_ASSETS_URL . 'animate'.$suffix.'.css', array(), '3.5.1');
    
    // font-awesome
    if( isset( $font_awesome ) )
      wp_enqueue_style('font_awesome', DT_ASSETS_URL . 'font-awesome/css/font-awesome'.$suffix.'.css', array(), '4.7.0');

    // fancybox
    if( isset( $fancybox ) ){
      wp_deregister_style('gllr_fancybox_stylesheet');
      foreach (array('gllr_fancybox_js', 'fancybox-script', 'fancybox', 'jquery.fancybox', 'jquery_fancybox', 'jquery-fancybox') as $value) { wp_deregister_script($value); }

      wp_enqueue_style( 'fancybox', DT_ASSETS_URL . 'fancybox/jquery.fancybox'.$suffix.'.css');
      wp_enqueue_script('fancybox', DT_ASSETS_URL . 'fancybox/jquery.fancybox'.$suffix.'.js',
        array('jquery'), '1.6', true);

      if(isset($fancybox_thumb)){
        wp_enqueue_style( 'fancybox-thumb',
          DT_ASSETS_URL . 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.css', array(), '1.0.7' );
        wp_enqueue_script('fancybox-thumb',
          DT_ASSETS_URL . 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.js', array('jquery'), '1.0.7', true);
      }

      if(isset($fancybox_mousewheel))
        wp_enqueue_script('mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js', array('jquery'), '3.1.13', true);

      add_action('wp_footer', array($this, 'init_fancybox'), 99 );
    }

    if( isset( $use_scss ) )
      $this->use_scss();
    
    if( isset( $use_bootstrap ) ){

      wp_enqueue_style('bootstrap4', DT_ASSETS_URL . 'bootstrap/bootstrap.css',array(),'4.0alpha6');
    }

    if( isset( $use_bootstrap_js ) ){
      wp_enqueue_script('Tether', 'https://www.atlasestateagents.co.uk/javascript/tether.min.js', array(), null, true);
      wp_enqueue_script('bootstrap-script', DT_ASSETS_URL . 'bootstrap/js/bootstrap'.$suffix.'.js', array('jquery'), '4.0alpha6', true);
    }
  }

  private function add_assets(){

    add_action('wp_enqueue_scripts', array($this, 'assets'), 999 ); 
  }

  function init_sticky(){ //has html
    if(!isset($this->settings['sticky_selector']))
      return;

    $value = $this->settings['sticky'];
    $selector = $this->settings['sticky_selector'];

    if ( (wp_is_mobile() && $value == 'phone_only' ) || $value == 'forever' ):
      if( function_exists('is_admin_bar_showing') && is_admin_bar_showing() )
          echo "<style>.admin-bar .is-sticky {$selector} { top: 32px !important; }</style>";
      ?>
      <script type="text/javascript">
        jQuery(document).ready(function($) {
          var $container = $("<?=$selector;?>");
          $container.sticky({topSpacing:0,zIndex:666});
          $container.parent(".sticky-wrapper").css("margin-bottom", $container.css("margin-bottom") );
        });
      </script>
    <?php endif;
  }
  function init_fancybox(){ // has html
    $selector = $this->settings['fancybox'];
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('<?=$selector;?>').fancybox({
          nextEffect : "none",
          prevEffect : "none",
          helpers:  {
            title : {
              type : "inside"
            },
            thumbs : {
              width: 120,
              height: 80
            }
          }
        });
      });
    </script>
    <?php
  }
  function init_scroll(){
    $top = $this->settings['smooth_scroll'];
    // Прокрутка после загрузки страницы по параметру scroll
    // К пр.: http://mydomain.ru/?scroll=primary
    // Пролистает за $top пикселя до начала объекта #primary
    // Внимание! параметр scroll указывается без "#" и прокручивает только до объекта с ID.
    $scroll_el = !empty($_GET['scroll']) ? esc_attr($_GET['scroll']) : false;
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        function scrollTo($elem, returnTop=<?=$top;?>, delay=500){
          $("html, body").animate({ scrollTop: $elem.offset().top - returnTop }, delay);
        }
        $("a[href^=\'#\']").click( function(){
          if( $(this).attr("rel") != "noScroll" ){
            var scrollEl = $(this).attr("href");
            if (scrollEl.length > 1) {
              scrollTo($(scrollEl));
              return false;
            }
          }
        });
        <?php
          if($scroll_el) // scroll from timeOut after load';
            echo 'setTimeout(function(){ scrollTo($("#'.$scroll_el.'")) }, 200);';
        ?>
      });
    </script>
    <?php
  }
  function use_scss(){
    $scss_cache = get_option( 'scss_cache' );
    $out_file = '/assets/style'.$this->suffix.'.css';

    $role = isset(wp_get_current_user()->roles[0]) ? wp_get_current_user()->roles[0] : '';
    if($role == 'administrator'){
      $file = get_template_directory() . '/style.css';

      if (file_exists( $file ) && filemtime($file) !== $scss_cache){
        $scss = new scssc();
        $scss->setImportPaths(function($path) {
          if (!file_exists(get_template_directory() . '/assets/scss/'.$path)) return null;
          return get_template_directory() . '/assets/scss/'.$path;
        });

        if(!is_wp_debug())
          $scss->setFormatter('scss_formatter_compressed');

        $compiled = $scss->compile( apply_filters( 'remove_cyrillic', file_get_contents($file) ) );
        if(!empty($compiled)){
          file_put_contents(get_template_directory().$out_file, $compiled );
          update_option( 'scss_cache', filemtime($file) );
          $scss_cache = filemtime($file);
        }
      }
      } // is user admin
    wp_enqueue_style('scss-style', get_template_directory_uri() . $out_file, array(), $scss_cache, 'all');
  }
}