<?php

include_once( dirname( __FILE__ ) . '/library/apf/admin-page-framework.php' );

class DencodesAdminTopMenuSettings extends AdminPageFramework {
    
    public function setUp() {

        $this->setRootMenuPage( 'Settings' ); 
        
        $this->addSubMenuItem(
            array(
                'title'        => __( 'Admin Top-Menu', 'dencodes_atp' ),
                'page_slug'    => 'dcs_admin_top_menu'
            )
        );


    }
    
    
    public function content( $sHTML ) {
        return '<p>'.__('"Settings / Admin Top-Menu" - is the location of this page.', 'dencodes_atp').'</p>' . $sHTML;
    }
    
    public function load_dcs_admin_top_menu( $oAdminPage ) {

        $this->addSettingSections(    
            array(
                'section_id'    => 'dcs_atp_section',    
                'page_slug'     => 'dcs_admin_top_menu',    
            )
        );
        
        $attr = array();
        $default = array();
        $label = array();
        
        $topmenu = DencodesAdminTopMenu::getObj();
        
        foreach ($topmenu->menu AS $key=>$data) {
            if ($data[0]) {
                //echo '<pre>'; print_r($data); echo '</pre>';
                $label[md5($data[2])] = '<a href="'.$data[2].'" target="_blank">'.$data[0].'</a>';
            }
        }
        
        $this->addSettingFields(
            array(    
                'field_id'      => 'admin_top_label',
                'title'         => __('Top-Menu Name', 'dencodes_atp'),
                'type'          => 'text',
                'default'       => __('Top Menu', 'dencodes_atp'),
            ),
            array(
                'field_id'      => 'admin_top_items',
                'title'         => __('Move to the Top-Menu', 'dencodes_atp'),
                'type'          => 'checkbox',
                'label'         => $label,
                'default'       => $default,
                'attributes'    => $attr,
                'after_label'   => '<br />',
            ),
            array(    
                'field_id'      => 'submit',
                'type'          => 'submit',
                'redirect_url'  => $_SERVER['REQUEST_URI'],
            )
        );

    }
    
}

new DencodesAdminTopMenuSettings;