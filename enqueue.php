<?php 
function pat_admin_enqueue_css(){
    
    wp_register_style(
        'pat_br_style',
        plugins_url('/assets/css/style.css', PAT_PLUGIN_URL)
    );

    wp_enqueue_style('pat_br_style');
}

