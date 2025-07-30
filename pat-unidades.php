<?php
/*
 * Plugin Name: PAT - Unidades
 * Description: Plugin para Inserir, atualizar, ler e deletar Unidade PAT
 * Version: 1.0
 * Author: Alexandre Alvarenga
 * Plugin URI: 
 * Author URI: 
 */

if(!function_exists('add_action')){
    echo 'Opa! Eu sou só um plugin, não posso ser chamado diretamente!';
    exit;
}

// setup
define('PAT_PLUGIN_URL', __FILE__);

register_activation_hook(PAT_PLUGIN_URL, 'pat_table_creator');
register_uninstall_hook(PAT_PLUGIN_URL, 'pat_plugin');

// includes
include('functions.php');
include('enqueue.php');


add_action('admin_menu', 'pat_da_display_esm_menu');
add_action('admin_enqueue_scripts', 'pat_admin_enqueue_css');



// exclui dados do banco de dados
register_deactivation_hook(__FILE__, 'cwpai_exclude_data_from_xyztable');



// insere dados no banco
register_activation_hook(__FILE__, 'cwpai_insert_data_into_pat_table');


