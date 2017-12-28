<?php

namespace CDevelopers\tool;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

/**
 * Class Name: WP_Admin_Forms
 * Description: Create a new custom admin page.
 * Version: 3.0.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @todo  : add defaults
 */

class WP_Admin_Forms {
    static $clear_value = false;
    protected $inputs, $args, $is_table, $active;
    protected $hiddens = array();

    public function __construct($data = null, $is_table = true, $args = null)
    {
        if( ! is_array($data) )
            $data = array();

        if( ! is_array($args) )
            $args = array();

        if( isset($data['id']) || isset($data['name']) )
            $data = array($data);

        $args = self::parse_defaults($args, $is_table);
        if( $args['admin_page'] || $args['sub_name'] )
            $data = self::admin_page_options( $data, $args['admin_page'], $args['sub_name'] );

        $this->fields = $data;
        $this->args = $args;
        $this->is_table = $is_table;
    }

    public function render( $return=false )
    {
        $this->get_active();

        $html = $this->args['form_wrap'][0];
        foreach ($this->fields as $field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            // &$field
            $input = self::render_input( $field, $this->active, $this->is_table );
            $html .= self::_field_template( $field, $input, $this->is_table );
        }
        $html .= $this->args['form_wrap'][1];
        $result = $html . "\n" . implode("\n", $this->hiddens);
        if( $return )
            return $result;

        echo $result;
    }

    public function set_active( $active )
    {
        $this->active = $active;
    }

    public static function render_input( &$field, $active, $for_table = false )
    {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => isset($field['desc']) ? $field['desc'] : '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'autocomplete'      => false,
            'id'                => '',
            'name'              => $field['id'],
            // 'class'             => array(),
            'label_class'       => array('label'),
            'input_class'       => array(),
            'options'           => array(),
            'custom_attributes' => array(),
            // 'validate'          => array(),
            'default'           => '',
            'before'            => '',
            'after'             => '',
            'check_active'      => false,
            'value'             => '',
            );

        $field = wp_parse_args( $field, $defaults );

        if( $field['default'] && ! in_array($field['type'], array('checkbox', 'select', 'radio')) ) {
            $field['placeholder'] = $field['default'];
        }

        $field['id'] = str_replace('][', '_', $field['id']);
        $entry = self::parse_entry($field, $active, $field['value']);

        return self::_input_template( $field, $entry, $for_table );
    }

    public function get_active()
    {
        if( ! $this->active ) {
            $this->active = $this->_active();
        }

        return $this->active;
    }

    /**
     * EXPEREMENTAL!
     * Get ID => Default values from $render_data
     * @param  array() $render_data
     * @return array(array(ID=>default),ar..)
     */
    public static function defaults( $render_data ){
        $defaults = array();
        if( empty($render_data) ) {
          return $defaults;
        }

        if( isset($render_data['id']) ) {
            $render_data = array($render_data);
        }

        foreach ($render_data as $input) {
            if(isset($input['default']) && $input['default']){
                $input['id'] = str_replace('][', '_', $input['id']);
                $defaults[$input['id']] = $input['default'];
            }
        }

        return $defaults;
    }

    /**
     * EXPEREMENTAL!
     *
     * @return array installed options
     */
    private function _active()
    {
        if( $this->args['postmeta'] ){
            global $post;

            if( ! $post instanceof WP_Post ) {
                return false;
            }

            $active = array();
            if( $sub_name = $this->args['sub_name'] ) {
                $active = get_post_meta( $post->ID, $sub_name, true );
            }
            else {
                foreach ($this->fields as $field) {
                    $active[ $field['id'] ] = get_post_meta( $post->ID, $field['id'], true );
                }
            }
        }
        else {
            $active = get_option( $this->args['admin_page'], array() );

            if( $sub_name = $this->args['sub_name'] ) {
                $active = isset($active[ $sub_name ]) ? $active[ $sub_name ] : false;
            }
        }

        /** if active not found */
        if( ! is_array($active) || $active === array() ) {
            return false;
        }

        /**
         * @todo: add recursive handle
         */
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

    /******************************** Templates *******************************/
    private function _field_template( $field, $input, $for_table )
    {
        // if ( $field['required'] ) {
        //     $field['class'][] = 'required';
        //     $required = ' <abbr class="required" title="' . esc_attr__( 'required' ) . '">*</abbr>';
        // } else {
        //     $required = '';
        // }

        $html = array();

        $desc = '';
        if( $field['description'] ){
            if( isset($this->args['hide_desc']) && $this->args['hide_desc'] === true )
                $desc = "<div class='description' style='display: none;'>{$field['description']}</div>";
            else
                $desc = "<span class='description'>{$field['description']}</span>";
        }

        $template = $field['before'] . $this->args['item_wrap'][0];
        $template.= $input;
        $template.= $this->args['item_wrap'][1] . $field['after'];
        $template.= $desc;

        if( ! $this->is_table ){
            $html[] = $template;
        }
        elseif( $field['type'] == 'hidden' ){
            $this->hiddens[] = $input;
        }
        elseif( $field['type'] == 'html' ){
            $html[] = $this->args['form_wrap'][1];
            $html[] = $input;
            $html[] = $this->args['form_wrap'][0];
        }
        else {
            $lc = implode( ' ', $field['label_class'] );
            $html[] = "<tr id='{$field['id']}'>";
            // @todo : add required symbol
            $html[] = "  <{$this->args['label_tag']} class='label'>";
            $html[] = "    {$field['label']}";
            $html[] = "  </{$this->args['label_tag']}>";

            $html[] = "  <td>";
            $html[] = "    " . $template;
            $html[] = "  </td>";
            $html[] = "</tr>";
        }

        return implode("\n", $html);
    }

    private static function _input_template( $field, $entry, $for_table = false )
    {
        $name = 'name="' . esc_attr( $field['name'] ) . '"';
        $id   = 'id="' . esc_attr( $field['id'] ) . '"';

        $class = '';
        if( is_array($field['input_class']) ) {
            $class = esc_attr( implode( ' ', $field['input_class'] ) );
        }
        elseif( is_string($field['input_class']) ) {
            $class = ' ' . esc_attr( $field['input_class'] );
        }

        $ph           = 'placeholder="' . esc_attr( $field['placeholder'] ) . '"';

        $custom_attributes = array();
        if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
            foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }
        $attrs = implode( ' ', $custom_attributes );

        $maxlength = ( $field['maxlength'] ) ?
            'maxlength="' . absint( $field['maxlength'] ) . '"' : '';
        $autocomplete = ( $field['autocomplete'] ) ?
            'autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : '';

        $label = ( ! $for_table && $field['label'] ) ?
            "<label for='".esc_attr($field['id'])."'> {$field['label']} </label>" : '';

        $input = '';
        switch ($field['type']) {
            // @todo : add fieldset
            case 'html' :
                $input .= $field['value'];
                break;
            case 'textarea' :
                $rows = ! empty( $field['custom_attributes']['rows'] ) ?
                    ' rows="'.$field['custom_attributes']['rows'].'"' : ' rows="5"';
                $cols = ! empty( $field['custom_attributes']['cols'] ) ?
                    ' cols="'.$field['custom_attributes']['cols'].'"' : ' cols="40"';

                $input .= $label;
                $input .= "<textarea ";
                $input .= "{$name} {$id}{$cols}{$rows} {$ph} {$attrs} {$autocomplete} {$maxlength}";
                $input .= "class='input-text{$class}'>";
                $input .= esc_textarea( $entry );
                $input .= '</textarea>';
                break;
            case 'checkbox' :
                $val = $field['value'] ? $field['value'] : 1;
                $checked = checked( $entry, true, false );
                // if( $field['default'] ) {
                //     if( ! $entry ) {
                //         $checked = checked( in_array($entry, array('true', '1', 'on')), true, false );
                //     }
                //     $clear_value = 'false';
                // }



                // if $clear_value === false dont use defaults (couse default + empty value = true)
                if( isset($clear_value) || false !== ($clear_value = self::$clear_value) ) {
                    $input .= "<input type='hidden' {$name} value='{$clear_value}'>\n";
                }

                $input .= "<input type='checkbox' {$name} {$id} {$attrs} value='{$val}'";
                $input .= " class='input-checkbox{$class}' {$checked} />";
                $input .= $label;
                break;
            case 'hidden' :
            case 'password' :
            case 'text' :
            case 'email' :
            case 'tel' :
            case 'number' :
                $type = sprintf('type="%s"', esc_attr( $field['type'] ));
                $val = $field['value'] ? esc_attr( $field['value'] ) : esc_attr( $entry );

                $input .= $label;
                $input .= "<input {$type} {$name} {$id} {$ph} {$maxlength} {$autocomplete}";
                $input .= " class='input-text{$class}' value='{$val}' {$attrs} />";
                break;
            case 'select' :
                $options = '';

                if ( ! empty( $field['options'] ) ) {
                    $input .= $label;

                    // if( $field['value'] || $field['value'] === '' ) {
                    //     $entry = $field['value'];
                    // }

                    foreach ( $field['options'] as $option_key => $option_text ) {
                        if ( '' === $option_key ) {
                            if ( empty( $field['placeholder'] ) )
                                $field['placeholder'] = $option_text ?
                                    $option_text : __( 'Choose an option' );

                            // $custom_attributes[] = 'data-allow_clear="true"';
                        }

                        if( ! is_array( $option_text ) ){
                            $options .= '<option value="' . esc_attr( $option_key ) . '" ' .
                                selected( $entry, $option_key, false ) . '>' .
                                esc_attr( $option_text ) . '</option>';
                        }
                        else {
                            $options .= "<optgroup label='{$option_key}'>";
                            foreach ($option_text as $sub_option_key => $sub_option_text) {
                                $options .= '<option value="' . esc_attr( $sub_option_key ) . '" ' .
                                    selected( $entry, $sub_option_key, false ) . '>' .
                                    esc_attr( $sub_option_text ) . '</option>';
                            }
                            $options .= "</optgroup>";
                        }
                    }
                    $input .= "<select {$name} {$id} class='select{$class}' {$attrs}";
                    $input .= " {$autocomplete}>{$options}</select>";
                }
                break;
            // case 'radio' :

            //     $label_id = current( array_keys( $field['options'] ) );

            //     if ( ! empty( $field['options'] ) ) {
            //         foreach ( $field['options'] as $option_key => $option_text ) {
            //             $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $field['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $field['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
            //             $field .= '<label for="' . esc_attr( $field['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $field['label_class'] ) .'">' . $option_text . '</label>';
            //         }
            //     }

            //     break;
            }
        return $input;
    }

    /********************************** Utils *********************************/
    private static function parse_defaults($args, $is_table)
    {
        $defaults = array(
            'admin_page'  => true, // set true for auto detect
            'item_wrap'   => array('<p>', '</p>'),
            'form_wrap'   => array('', ''),
            'label_tag'   => 'th',
            'hide_desc'   => false,
            'postmeta'    => false,
            'sub_name'    => '',
        );

        if( $is_table )
            $defaults['form_wrap'] = array('<table class="table form-table"><tbody>', '</tbody></table>');

        if( ( isset($args['admin_page']) && $args['admin_page'] !== false ) ||
            !isset($args['admin_page']) && is_admin() && !empty($_GET['page']) )
            $defaults['admin_page'] = $_GET['page'];

        $args = wp_parse_args( $args, $defaults );

        if( ! is_array($args['item_wrap']) )
            $args['item_wrap'] = array('', '');

        if( ! is_array($args['form_wrap']) )
            $args['form_wrap'] = array('', '');

        if( false === $is_table )
            $args['label_tag'] = 'label';

        return $args;
    }

    private static function parse_entry($field, $active)
    {
        if( ! is_array($active) || sizeof($active) < 1 )
            return false;

        $active_key = $field['check_active'] ? $field[$field['check_active']] : str_replace('[]', '', $field['name']);
        $active_value = isset($active[$active_key]) ? $active[$active_key] : false;

        if($field['type'] == 'checkbox' || $field['type'] == 'radio'){
            $entry = self::is_checked( $field, $active_value );
        }
        elseif($field['type'] == 'select'){
            $entry = ($active_value) ? $active_value : $field['default'];
        }
        else {
            // if text, textarea, number, email..
            $entry = $active_value;
        }
        return $entry;
    }

    private static function is_checked( $field, $active )
    {
        // if( $active === false && $value )
          // return true;

        $checked = ( $active === false ) ? false : true;
        if( $active === 'false' || $active === 'off' || $active === '0' )
            return false;

        if( $active === 'true'  || $active === 'on'  || $active === '1' )
            return true;

        if( $active || $field['default'] ){
            if( $field['value'] ){
                if( is_array($active) ){
                    if( in_array($field['value'], $active) )
                        return true;
                }
                else {
                    if( $field['value'] == $active || $field['value'] === true )
                        return true;
                }
            }
            else {
                if( $active || (!$checked && $field['default']) )
                    return true;
            }
        }

        return false;
    }

    private static function admin_page_options( $fields, $option_name, $sub_name = false )
    {
        foreach ($fields as &$field) {
            if ( ! isset($field['id']) && ! isset($field['name']) )
                continue;

            if( $option_name ) {
                if( isset($field['name']) ) {
                    $field['name'] = ($sub_name) ?
                        "{$option_name}[{$sub_name}][{$field['name']}]" : "{$option_name}[{$field['name']}]";
                }
                else {
                    $field['name'] = ($sub_name) ?
                        "{$option_name}[{$sub_name}][{$field['id']}]" : "{$option_name}[{$field['id']}]";
                }

                if( !isset($field['check_active']) )
                    $field['check_active'] = 'id';
            }
        }

        return $fields;
    }
}
