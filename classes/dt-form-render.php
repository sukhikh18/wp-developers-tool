<?php
if(! function_exists('_isset_false') ){
  function _isset_false(&$var, $unset = false){
    $result = $var = isset($var) ? $var : false;
    if($unset)
      $var = FALSE;
    return $result;
  }
}

if(! function_exists('_isset_empty') ){
  function _isset_empty(&$var, $unset = false){
    $result = $var = isset($var) ? $var : '';
    if($unset)
      $var = FALSE;
    return $result;
  }
}

if(! function_exists('options_page_render')){
  function options_page_render($field, $page_name, $active){
    $field['name'] = $page_name . "[" . $field['id'] . "]";

    if( isset($field['desc']) ){
      $field['label'] = $field['desc'];
      unset( $field['desc'] );
    } else {
      unset( $field['label'] );
    }

    if( isset($active[$field['id']]) ){
      if($field['type'] == 'checkbox')
        $field['checked'] = 'checked';
      $field['value'] = $active[$field['id']];
    }

    return $field;
  }
  add_filter( 'dtwp_options_page_render', 'options_page_render', 10, 3 );
}

class DTForm
{
  private static function is_checked( $name, $value, $active, $default ){
        if( $active ){
          if( $value ){
            if( is_array($active) ){
              if( in_array($value, $active) )
                return true;
            }
            else {
              if( $value == $active )
                return true;
            }
          }
          else {
            if( $default || $active != '')
              return true;
          }
        }
        return false;
  }

  public static function render(
    $render_data = false,
    $active = array(),
    $is_table = false,
    $item_wrap = array('<p>', '</p>'),
    $form_wrap = array('<table class="table form-table"><tbody>', '</tbody></table>', 'th'),
    $is_not_echo = false){
    $html = array();
    if( empty($render_data) ){
      if( function_exists('is_wp_debug') && is_wp_debug() )
        echo '<pre> Принятая форма пуста </pre>';
      return false;
    }

    if( isset($render_data['type']) )
        $render_data = array($render_data);

    if(! isset($item_wrap[1]) )
      $item_wrap = array('', '');

    if(! isset($form_wrap[1]) )
      $form_wrap = array('', '', 'th');

    /**
     * Template start
     */
    if($is_table)
        $html[] = $form_wrap[0];

    foreach ( $render_data as $input ) {
      $label   = _isset_false($input['label'], 1);
      $desc    = _isset_false($input['desc'], 1);
      $before  = _isset_empty($input['before'], 1);
      $after   = _isset_empty($input['after'], 1);
      $default = _isset_false($input['default'], 1);
      $value   = _isset_false($input['value']);
      if( !isset($input['name']) )
          $input['name'] = isset($input['id']) ? $input['id'] : '';
      
      /**
       * get values
       */
      $name = str_replace('[]', '', $input['name']);
      $entry = '';
      if($input['type'] == 'checkbox' || $input['type'] == 'radio'){
        $entry = self::is_checked( $name, $value, _isset_false($active[$name]), $default );
      }
      elseif( $input['type'] == 'select'){
        if( isset($active[$name]) ){
          $entry = $active[$name];
        }
        elseif( $default ){
          $entry = $default;
        }
      }
      else {
        // if text, textarea, number, email..
        if( isset( $active[$name] ) ){
          $entry = $active[$name];
        }
        elseif( $default ){
          if( !isset($input['placeholder']) ){
            $input['placeholder'] = $input['default'];
          }
          else {
            $entry = $default;
          }
        }

        $input['value'] = $entry;
      }

      $func = 'render_' . $input['type'];
      $input_html = self::$func($input, $entry, $is_table, $label);

      $item = $before . $item_wrap[0]. $input_html .$item_wrap[1] . $after;
      if(!$is_table){
        $html[] = $item;
      }
      else {
        $col = $form_wrap[2];
        $html[] = "<tr id='{$input['id']}'>";
        $html[] = "  <{$col} class='name'>{$label}</{$col}>";
        $html[] = "  <td>";
        $html[] = "    " .$item;
        if($desc)
          $html[] = "    <div class='description'>{$desc}</div>";
        $html[] = "  </td>";
        $html[] = "</tr>";
      }
    } // endforeach
    if($is_table)
      $html[] = $form_wrap[1];

    $result = implode("\n", $html);
    if( $is_not_echo )
      return $result;
    else
      echo $result;
  }
  
  public static function render_checkbox( $input, $checked, $is_table, $label = '' ){
    $result = '';
    if( $input['value'] === false )
      $input['value'] = 'on';

    if( $checked )
      $input['checked'] = 'true';

    if(isset( $input['data-clear']) && $input['data-clear'] == 'true' )
      $result .= "<input name='{$input['name']}' type='hidden' value=''>\n";

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
      $result .= "<option value='{$value}'{$active_str}>{$option}</option>";
    }
    $result .= "</select>";

    return $result;
  }

  public static function render_textarea( $input, $entry, $is_table, $label = '' ){
    $result = '';
    // set defaults
    if(!isset($input['rows'])) $input['rows'] = 5;
    if(!isset($input['cols'])) $input['cols'] = 40;

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

  static function render_number($input, $entry, $is_table, $label = ''){

    self::render_text($input, $entry, $is_table, $label);
  }
  static function render_email($input, $entry, $is_table, $label = ''){

    self::render_text($input, $entry, $is_table, $label);
  }
}

/* DTForm::render(
    $render_data = array(),
    $active = array(),
    $is_table = false,
    $item_wrap = array('<p>', '</p>'),
    $form_wrap = array('<table class="table form-table"><tbody>', '</tbody></table>', 'th'),
    $is_not_echo = false) */