<?php

include_once( dirname( __FILE__ ) . '/library/apf/admin-page-framework.php' );

class DencodesAdminTopMenuSettings extends AdminPageFramework {
    
    public function setUp() {

        //$this->setRootMenuPageBySlug( 'dcs_admin_top_menu' );    // create a root page
        $this->setRootMenuPage( 'Tools' ); 
        
        $this->addSubMenuItem(
            array(
                'title'        => __( 'Admin Top-Menu Settings', 'dencodes_atp' ),
                'page_slug'    => 'dcs_admin_top_menu'
            )
        );


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
                $label[md5($data[2])] = $data[0];
            }
        }
        
        $this->addSettingFields(
            array(
                'field_id'      => 'admin_top_items',
                'title'         => __('Move items to the Top-Menu', 'dencodes_atp'),
                'type'          => 'checkbox',
                'label'         => $label,
                'default'       => $default,
                'attributes'    => $attr,
                'after_label'   => '<br />',
            ),
            array(    
                'field_id'      => 'submit',
                'type'          => 'submit',
                'label'         => __( 'Save', 'dencodes_atp' )
            )
        );

    }
    
}

new DencodesAdminTopMenuSettings;