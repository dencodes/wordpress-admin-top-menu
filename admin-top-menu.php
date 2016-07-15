<?php
/*
Plugin Name: Admin Top-Menu by Dencodes
Description: Move any selected items from the admin sidebar menu to the top (admin bar).
Author: Denis Tkach (Dencodes)
Text Domain: dencodes_atp
Domain Path: /languages/
Version: 0.9
*/

include_once( dirname( __FILE__ ) . '/admin-top-menu-settings.php' );

class DencodesAdminTopMenu 
{
    
    private static $obj;
    private $arr_top_menu_items = NULL;
    public $menu;
    public $submenu;
    
    private function __construct() {}
    
    public static function getObj()
    {
        if (empty(self::$obj)) {
            self::$obj = new self;
            self::$obj->local_construct();
        }
        return self::$obj;
    }
    
    function local_construct()
    {
        
        if (is_admin()) {
            add_action( 'admin_head', array( $this, 'top_admin_css' ) );
            add_action( 'admin_menu', array( $this, 'menu_set_items' ), 999999 );
            add_action( 'admin_bar_menu', array( $this, 'bar_create_top_menu' ), 100 );
            add_action( 'submit_after_DencodesAdminTopMenuSettings', array($this,'action_after_save') );
            add_action('load_after_DencodesAdminTopMenuSettings', array($this,'action_after_load'));
            add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this,'add_plugins_settings_link') );
        }
        
    }
    
    function add_plugins_settings_link($links) {
      $settings_link = '<a href="tools.php?page=dcs_admin_top_menu">'.__('Admin Top-Menu Settings', 'dencodes_atp').'</a>';
      array_unshift($links, $settings_link);
      return $links;
    }
    
    public function load_text_domain() {
        load_plugin_textdomain( 'dencodes_atp', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }
    
    
    public function action_after_load()
    {
        echo '<style>#admin-page-framework-form span.pending-count,
#admin-page-framework-form span.plugin-count {
    display: none;
}'
        . '</style>';
    }
    
    
    public function action_after_save()
    {
        header("Location: " . $_SERVER['REQUEST_URI']); exit;
    }
    
    
    public function top_admin_css()
    {
        echo '<link rel="stylesheet" type="text/css" href="'.plugin_dir_url(__FILE__).'admin-top-menu.css">';
    }
    
    
    function menu_set_items()
    {
        
        if ($this->menu === NULL) {
            global $menu, $submenu;
            $this->menu = $menu;
            $this->submenu = $submenu;
        }
        
        $arr_items = $this->bar_get_top_menu_items();
        if (!$arr_items) {return;}
        
        foreach ($arr_items AS $data) {
            remove_menu_page($data[2]);
        }
    }
    
    
    function bar_create_top_menu()
    {
        global $wp_admin_bar;
        
        $bar_id = 'dencodes_admin_top_menu';
        $wp_admin_bar->add_node(array('id' => $bar_id, 'title' => __('Top Menu')));
        
        /*
        $wp_admin_bar->add_node(array(
            'id'=>$bar_id.'-dencodes-admin-top-menu-settings',
            'title' => __('Admin Top Menu Settings'),
            'href'=>menu_page_url('my_first_form', false),
            'parent'=>$bar_id,
        ));
         * 
         */
        
        $arr_items = $this->bar_get_top_menu_items();

        foreach ($arr_items AS $data) {
            
            $item_meta = array();
            $item_group = false;
            $item_id = $bar_id.'_'.$data[1].$data[5];
            
            $item_href = menu_page_url( $data[2], false );
            $item_href = empty( $item_href ) ? $data[2] : $item_href;

            $args = array(
                'parent'    => $bar_id,
                'title'     => $data[0],
                'id'        => $item_id,
                'href'      => $item_href,
                'group'     => $item_group,
                'meta'      => $item_meta

            );
            
            $wp_admin_bar->add_node($args);
            
            if (isset($data['_dcs_submenu'])) {
                
                foreach ($data['_dcs_submenu'] AS $skey=>$sdata) {
                    
                    $sitem_href = menu_page_url( $sdata[2], false );
                    $sitem_href = empty( $sitem_href ) ? $sdata[2] : $sitem_href;
                    
                    $args = array(
                        'parent'    => $item_id,
                        'title'     => $sdata[0],
                        'id'        => $item_id.'-'.$skey,
                        'href'      => $sitem_href,
                        //'group'     => $item_group,
                        //'meta'      => $item_meta

                    );
                    $wp_admin_bar->add_node($args);
                }
                
            }
            
        }
        
    }
    
    
    function bar_get_top_menu_items()
    {
        if ($this->arr_top_menu_items !== NULL) {
            return $this->arr_top_menu_items;
        }
        
        $result = array();
        $arr_selected = $this->menu_get_selected_items();
        
        //echo '<pre>'; print_r($arr_selected); echo '</pre>'; exit;
        
        foreach ($this->menu AS $key=>$data) {
            $setkey = md5($data[2]);
            if (isset($arr_selected[$setkey]) && $arr_selected[$setkey]) {
                $result[$key] = $data;
                
                if (isset($this->submenu[$data[2]])) {
                    $result[$key]['_dcs_submenu'] = $this->submenu[$data[2]];
                }
                
            }
        }
        
        return $this->arr_top_menu_items = $result;
    }


    function menu_get_selected_items()
    {
        
        $data = get_option( 'DencodesAdminTopMenuSettings', array() );
        $result = array();
        if (isset($data['admin_top_items'])) {
            $result = $data['admin_top_items'];
        }
        //echo '<pre>'; print_r($data); echo '</pre>'; exit;
        /*
        $result = array(
            'tools.php',
            'edit-comments.php',
            'edit.php?post_type=sb_modals',
            'plugins.php',
            'users.php',
        );
         * 
         */
        
        return $result;
    }
    
}

$obj = DencodesAdminTopMenu::getObj();