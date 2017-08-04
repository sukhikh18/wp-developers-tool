<?php
/**
 * Class Name: WPForm ( :: render )
 * Class URI: https://github.com/nikolays93/WPForm
 * Description: render forms as wordpress fields
 * Version: 1.6
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if( ! function_exists('_isset_default') ){
  function _isset_default(&$var, $default, $unset = false){
    $result = $var = isset($var) ? $var : $default;
    if($unset) $var = FALSE;
    return $result;
  }
}
if( ! function_exists('_isset_false') ){
  function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
}
if( ! function_exists('_isset_empty') ){
  function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }
}

if( class_exists('WPForm') )
  return;

class WPForm {
  const ver = '1.6';

  static protected $clear_value;

  /**
   * EXPEREMENTAL!
   * Get ID => Default values from $render_data
   * @param  array() $render_data
   * @return array(array(ID=>default),ar..)
   */
  public static function defaults( $render_data ){
    $defaults = array();
    if(empty($render_data))
      return $defaults;

    if( isset($render_data['id']) )
        $render_data = array($render_data);

    foreach ($render_data as $input) {
      if(isset($input['default']) && $input['default']){
        $input['id'] = str_replace('][', '_', $input['id']);
        $defaults[$input['id']] = $input['default'];
      }
    }

    return $defaults;
  }

  /**
   * @todo: add recursive handle
   *
   * @param  string   $option_name
   * @param  string   $sub_name         $option_name[$sub_name]
   * @param  boolean  $is_admin_options recursive split value array key with main array
   * @param  int|bool $postmeta         int = post_id for post meta, true = get post_id from global post
   * @return array                      installed options
   */
  public static function active($option, $sub_name = false, $is_admin_options = false, $postmeta = false){

    global $post;

    /** get active values */
    if( is_string($option) ){
      if( $postmeta ){
        if( !is_int($postmeta) && !isset($post->ID) )
          return false;

        $post_id = ($postmeta === true) ? $post->ID : $postmeta;

        $active = get_post_meta( $post_id, $option, true );
      }
      else {
        $active = get_option( $option, array() );
      }
    }
    else {
      $active = $option;
    }

    /** get subvalue */
    if( $sub_name && isset($active[$sub_name]) && is_array($active[$sub_name]) )
      $active = $active[$sub_name];
    elseif( $sub_name && !isset($active[$sub_name]) )
      return false;

    /** if active not found */
    if( !isset($active) || !is_array($active) )
        return false;

    /** sanitize admin values */
    if( $is_admin_options === true ){
      $result = array();
      foreach ($active as $key => $value) {
        if( is_array($value) ){
          foreach ($value as $key2 => $value2) {
            $result[$key . '_' . $key2] = $value2;
          }
        }
        else {
          $result[$key] = $value;
        }
      }

      return $result;
    }

    return $active;
  }

  protected static function set_defaults( $args, $is_table ){
    $default_args = array(
      'admin_page'  => false, // set true for auto detect
      'item_wrap'   => array('<p>', '</p>'),
      'form_wrap'   => array('<table class="table form-table"><tbody>', '</tbody></table>'),
      'label_tag'   => 'th',
      'hide_desc'   => false,
      'clear_value' => false,
      );
    $args = array_merge($default_args, $args);

    self::$clear_value = $args['clear_value'];

    if( $args['item_wrap'] === false )
      $args['item_wrap'] = array('', '');

    if($args['form_wrap'] === false)
      $args['form_wrap'] = array('', '');

    if( $args['label_tag'] == 'th' && $is_table == false )
      $args['label_tag'] = 'label';

    return $args;
  }

  /**
   * EXPEREMENTAL!
   * change names for wordpress options
   * @param  array  $inputs      rendered inputs
   * @param  string $option_name name of wordpress option ( @see get_option() )
   * @return array               filtred inputs
   */
  protected static function admin_page_options( $inputs, $option_name = false ){
    if( ! is_string( $option_name ) && !empty($_GET['page']) )
      $option_name = $_GET['page'];

    if( ! $option_name )
      return $inputs;

    foreach ( $inputs as &$input ) {
      if( isset($input['name']) )
        $input['name'] = "{$option_name}[{$input['name']}]";
      else
        $input['name'] = "{$option_name}[{$input['id']}]";

      if( !isset($input['check_active']) )
        $input['check_active'] = 'id';
    }
    return $inputs;
  }

  protected static function get_function_name( $type ){
    switch ( $type ) {
      case 'text':
      case 'hidden':
      case 'submit':
      case 'button':
      case 'number':
      case 'email':
        $func = 'render_text';
      break;

      case 'fieldset':
        $func = 'render_fieldset';
      break;

      default:
        $func = 'render_' . $type;
      break;
    }
    return $func;
  }

  /**
   * Render form items
   * @param  boolean $render_data array with items ( id, name, type, options..)
   * @param  array   $active      selected options from form items
   * @param  boolean $is_table    is a table
   * @param  array   $args        array of args (item_wrap, form_wrap, label_tag, hide_desc) @see $default_args
   * @param  boolean $is_not_echo true = return, false = echo
   * @return html                 return or echo
   */
  public static function render(
    $render_data = false,
    $active = array(),
    $is_table = false,
    $args = array(),
    $is_not_echo = false){

    $html = $hidden = array();

    if( empty($render_data) ){
      if( function_exists('is_wp_debug') && is_wp_debug() )
        echo '<pre> Параметры формы не были переданы </pre>';
      return false;
    }

    if( isset($render_data['id']) )
        $render_data = array($render_data);

    if( ! $active  )
      $active = array();

    $args = self::set_defaults( $args, $is_table );
    if( $args['admin_page'] )
      $render_data = self::admin_page_options( $render_data, $args['admin_page'] );

    /**
     * Template start
     */
    if($is_table)
        $html[] = $args['form_wrap'][0];

    foreach ( $render_data as $input ) {
      $label   = _isset_false($input['label'], 1);
      $before  = _isset_empty($input['before'], 1);
      $after   = _isset_empty($input['after'], 1);
      $default = _isset_false($input['default'], 1);
      $value   = _isset_false($input['value']);
      $check_active = _isset_false($input['check_active'], 1);

      if( $input['type'] != 'checkbox' && $input['type'] != 'radio' )
        _isset_default( $input['placeholder'], $default );

      $desc = _isset_false($input['desc'], 1);
      if( ! $desc )
        $desc = _isset_false($input['description'], 1);

      if( !isset($input['name']) )
          $input['name'] = _isset_empty($input['id']);

      $input['id'] = str_replace('][', '_', $input['id']);

      /**
       * set values
       */
      $active_name = $check_active ? $input[$check_active] : str_replace('[]', '', $input['name']);

      $active_value = false;
      if( is_array($active) && sizeof($active) > 0 ){
        if( isset($active[$active_name]) )
          $active_value = $active[$active_name];
        // if( in_array($active_name, $active) )
        //   $active_value = $active_name;
      }

      $entry = '';
      if($input['type'] == 'checkbox' || $input['type'] == 'radio'){
        $entry = self::is_checked( $value, $active_value, $default );
      }
      elseif( $input['type'] == 'select' ){
        $entry = ($active_value) ? $active_value : $default;
      }
      else {
        // if text, textarea, number, email..
        $entry = $active_value;
        $placeholder = $default;
      }

      $func = self::get_function_name( $input['type'] );
      $input_html = self::$func($input, $entry, $is_table, $label);

      if( $desc ){
        // todo: set tooltip
        if( isset($args['hide_desc']) && $args['hide_desc'] === true )
          $desc_html = "<div class='description' style='display: none;'>{$desc}</div>";
        else
          $desc_html = "<span class='description'>{$desc}</span>";
      } else {
        $desc_html = '';
      }

      if(!$is_table){
        $html[] = $before . $args['item_wrap'][0] . $input_html . $args['item_wrap'][1] . $after . $desc_html;
      }
      elseif( $input['type'] == 'hidden' ){
        $hidden[] = $before . $input_html . $after;
      }
      elseif( $input['type'] == 'html' ){
        $html[] = $args['form_wrap'][1];
        $html[] = $before . $input_html . $after;
        $html[] = $args['form_wrap'][0];
      }
      else {
        $item = $before . $args['item_wrap'][0]. $input_html .$args['item_wrap'][1] . $after;

        $html[] = "<tr id='{$input['id']}'>";
        $html[] = "  <{$args['label_tag']} class='label'>{$label}</{$args['label_tag']}>";
        $html[] = "  <td>";
        $html[] = "    " .$item;
        $html[] = $desc_html;
        $html[] = "  </td>";
        $html[] = "</tr>";
      }
    } // endforeach
    if($is_table)
      $html[] = $args['form_wrap'][1];

    $result = implode("\n", $html) . "\n" . implode("\n", $hidden);
    if( $is_not_echo )
      return $result;
    else
      echo $result;
  }

  /**
   * check if is checked ( called( $value, $active_value, $default ) )
   * @param  mixed         $value   ['value'] array setting (string || boolean)(!isset ? false)
   * @param  string||false $active  value from $active option
   * @param  mixed         $default ['default'] array setting (string || boolean)(!isset ? false)
   *
   * @return boolean       checked or not
   */
  private static function is_checked( $value, $active, $default ){
    // if( $active === false && $value )
      // return true;

    $checked = ( $active === false ) ? false : true;
    if( $active === 'false' || $active === 'off' || $active === '0' )
      $active = false;

    if( $active === 'true'  || $active === 'on'  || $active === '1' )
      $active = true;

    if( $active || $default ){
      if( $value ){
        if( is_array($active) ){
          if( in_array($value, $active) )
            return true;
        }
        else {
          if( $value == $active || $value === true )
            return true;
        }
      }
      else {
        if( $active || (!$checked && $default) )
          return true;
      }
      return false;
    }
  }

  public static function render_checkbox( $input, $checked, $is_table, $label = '' ){
    $result = '';

    if( empty($input['value']) )
      $input['value'] = 'on';

    if( $checked )
      $input['checked'] = 'true';

    // if $clear_value === false dont use defaults (couse default + empty value = true)
    $cv = self::$clear_value;
    if( false !== $cv )
      $result .= "<input name='{$input['name']}' type='hidden' value='{$cv}'>\n";

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    return $result;
  }

  public static function render_fieldset( $input, $entry, $is_table, $label = '' ){
    $result = '';

    // <legend>Работа со временем</legend>

    foreach ($input['fields'] as $field) {
      if( !isset($field['name']) )
        $field['name'] = _isset_empty($field['id']);

      $field['id'] = str_replace('][', '_', $field['id']);

      $f_name = self::get_function_name($field['type']);
      $result .= self::$f_name( $field, $entry, $is_table, $label );
    }
    return $result;
  }

  public static function render_select( $input, $active_id, $is_table, $label = '' ){
    $result = '';
    $options = _isset_false($input['options'], 1);
    if(! $options )
      return false;

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<select";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";
    foreach ($options as $value => $option) {
      $active_str = ($active_id == $value) ? " selected": "";
      if( !is_array($option) ){
        $result .= "<option value='{$value}'{$active_str}>{$option}</option>";
      }
      else {
        $result .= "<optgroup label='$value'>";
        foreach ($option as $subvalue => $suboption) {
          $active_str = ($active_id == $subvalue) ? " selected": "";
          $result .= "<option value='{$subvalue}'{$active_str}>{$suboption}</option>";
        }
        $result .= "";
        $result .= "</optgroup>";
      }
    }
    $result .= "</select>";

    return $result;
  }

  public static function render_textarea( $input, $entry, $is_table, $label = '' ){
    $result = '';
    // set defaults
    _isset_default($input['rows'], 5);
    _isset_default($input['cols'], 40);

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";

    $result .= "<textarea";
    foreach ($input as $attr => $val) {
      if($val){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">{$entry}</textarea>";

    return $result;
  }

  public static function render_text( $input, $entry, $is_table, $label = '' ){
    $result = '';

    if(!$is_table && $label)
      $result .= "<label for='{$input['id']}'> {$label} </label>";
    if( $entry )
      $input['value'] = $entry;

    $result .= "<input";
    foreach ($input as $attr => $val) {
      if( $val ){
        $attr = esc_attr($attr);
        $val  = esc_attr($val);
        $result .= " {$attr}='{$val}'";
      }
    }
    $result .= ">";

    return $result;
  }

  public static function render_html( $input, $entry, $is_table, $label = '' ){

    return $input['value'];
  }
}
