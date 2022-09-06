<?php 
function pat_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'PAT';
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
            id mediumint(11) NOT NULL AUTO_INCREMENT,
            local varchar(50) NOT NULL,
            endereco varchar (250) NOT NULL,
            municipio varchar (250) NOT NULL,
            telefone varchar (250) NOT NULL,
            cep varchar (10) NOT NULL,
            PRIMARY KEY id(id)
            )$charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


function pat_da_display_esm_menu()
{

    add_menu_page('PAT Unidades', 'PAT Unidades', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback');
    add_submenu_page('pat-emp-list', 'PAT - Lista', 'PAT - Lista', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback');
    add_submenu_page(null, 'PAT Atualiza', 'PAT Atualiza', 'manage_options', 'update-pat', 'pat_da_emp_update_call');
    add_submenu_page(null, 'Delete Employee', 'Delete Employee', 'manage_options', 'delete-pat', 'pat_da_emp_delete_call');
    // add_submenu_page('pat-emp-list', 'PAT - Lista Shortcode', 'PAT - Lista Shortcode', 'edit_others_posts', 'emp-shotcode', 'pat_da_emp_shortcode_call');

}





//[employee_list]
add_shortcode('employee_list', 'da_PAT_list_callback');


function da_PAT_list_callback()
{
    global $wpdb;
    // add registro
    $table_name = $wpdb->prefix . 'PAT';
    $msg = '';
    if (isset($_REQUEST['submit'])) {

        $wpdb->insert("$table_name", [
            "local" => $_REQUEST['local'],
            'endereco' => $_REQUEST['endereco'],
            'municipio' => $_REQUEST['municipio'],
            'telefone' => $_REQUEST['telefone'],
            'cep' => $_REQUEST['cep']
        ]);

        if ($wpdb->insert_id > 0) {
            $msg = "Gravado com sucesso!";
        } else {
            $msg = "Falha ao gravar!";
        }
    }

    ?>
    <div class="content-pat">

        <h1 class="title">PAT - Unidades</h1><br>
        <h4 id="msg"><?php echo $msg; ?></h4>
        <form method="post">
            <p><label>Local</label><input type="text" name="local" placeholder="" required></p>
            <p><label>Endereço</label><input type="text" name="endereco" placeholder=""></p>
            <p><label>Municipio</label><input type="text" name="municipio" placeholder=""></p>
            <p><label>Telefone</label><input type="text" name="telefone" placeholder=""></p>
            <p><label>CEP</label><input type="text" name="cep" placeholder=""></p>
            <p><button type="submit" name="submit">Cadastrar</button></p>
        </form>
    </div>
    <?php 
    // lista de registro
    // receber o numero da pagina
    $pagina_atual = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual))? $pagina_atual : 1;

    // setar a quantidade de itens por pagina
    $qnt_result_pg = 10;

    // calcular o inicio visualizacao
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    $table_name = $wpdb->prefix . 'PAT';
    $employee_list = $wpdb->get_results($wpdb->prepare("select * FROM $table_name ORDER BY local asc LIMIT $inicio, $qnt_result_pg"), ARRAY_A);
    if (count($employee_list) > 0): ?>  

        <div class="busca">
            <input type="text" class="form-control" id="live_search" autocomplete="off" placeholder="Ex.: Cidade, CEP, rua ...">
        </div>   
        <div id="searchresult"></div>
        <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $("#live_search").keyup(function(){
                    var input = $(this).val();
                    // alert(input);

                    var url_search =  "<?php echo site_url(); ?>/wp-content/plugins/Wordpress-Plugin-CRUD-PAT/busca-resultado.php";
                    
                    if(input != ""){
                        $.ajax({                      
                            url:url_search,
                            method: "POST",
                            data:{input:input},

                            success:function(data){
                                $("#searchresult").html(data);
                                $("#searchresult").css('display','block');
                                $("#registros-todos-dados-tabela").css('display','none');
                            }
                        });
                    }else{
                        $("#searchresult").css("display","none");
                        $("#registros-todos-dados-tabela").css('display','block');
                    }
                });
            });
        </script>   
        <div id="registros-todos-dados-tabela" style="margin-top: 40px;">
            <?php resultado_busca($employee_list);?>
        </div>
    <?php else:echo "<h2>Não há Informação</h2>";endif;
}


function resultado_busca($employee_list){?>
    <table border="1" cellpadding="10" width="90%">
        <tr>
            <th>ID</th>
            <th>Local</th>
            <th>Endereço</th>
            <th>Municipio</th>
            <th>CEP</th>
            <th>Telefone</th>
            <th>Ação</th>
        </tr>
        <?php $i = 1;
        foreach ($employee_list as $index => $employee): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $employee['local']; ?></td>
                <td><?php echo $employee['endereco']; ?></td>
                <td><?php echo $employee['municipio']; ?></td>
                <td><?php echo $employee['cep']; ?></td>
                <td><?php echo $employee['telefone']; ?></td>
                <td>
                    <a href="admin.php?page=update-pat&id=<?php echo $employee['id']; ?>">Editar</a>
                    <a href="admin.php?page=delete-pat&id=<?php echo $employee['id']; ?>">Deletar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php }

function pat_da_emp_update_call()
{
    global $wpdb;
    
    $url = site_url();
    $url2 = '/wp-admin/admin.php?page=pat-emp-list';
    $urlvoltar = $url.$url2;

    $table_name = $wpdb->prefix . 'PAT';
    $msg = '';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    if (isset($_REQUEST['update'])) {
        if (!empty($id)) {
            $wpdb->update("$table_name", [
                "local" => $_REQUEST['local'], 
                'endereco' => $_REQUEST['endereco'], 
                'municipio' => $_REQUEST['municipio'], 
                'telefone' => $_REQUEST['telefone'],
                'cep' => $_REQUEST['cep']            
        ], ["id" => $id]);
            $msg = 'Data updated';
            echo '<a href="'. $urlvoltar.'">Voltar para a lista</a>';
        }
    }
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A); ?>
   
    <h4><?php echo $msg; ?></h4>
    <form method="post">
        <p>
            <label>Local</label>
            <input type="text" name="local" placeholder="" value="<?php echo $employee_details['local']; ?>"
                   required>
        </p>

        <p>
            <label>Endereço</label>
            <input type="text" name="endereco" placeholder=""
                   value="<?php echo $employee_details['endereco']; ?>" >
        </p>
        <p>
            <label>Município</label>
            <input type="text" name="municipio" placeholder=""
                   value="<?php echo $employee_details['municipio']; ?>" >
        </p>
        <p>
            <label>Telefone</label>
            <input type="text" name="telefone" placeholder=""
                   value="<?php echo $employee_details['telefone']; ?>" >
        </p>
        <p>
            <label>CEP</label>
            <input type="text" name="cep" placeholder=""
                   value="<?php echo $employee_details['cep']; ?>" >
        </p>
        <p>
            <button type="submit" name="update">Atualizar</button>
        </p>
    </form>
<?php }

function pat_da_emp_delete_call()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'PAT';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    if (isset($_REQUEST['delete'])) {
        if ($_REQUEST['conf'] == 'yes') {
            $row_exits = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
            if (count($row_exits) > 0) {
                $wpdb->delete("$table_name", array('id' => $id,));
            }
        } ?>
        <script>
            location.href = "<?php echo site_url(); ?>/wp-admin/admin.php?page=pat-emp-list";
        </script>
    <?php } ?>
    <form method="post">
        <p>
            <label>Deseja realmente apagar?</label><br>
            <input type="radio" name="conf" value="yes">Sim
            <input type="radio" name="conf" value="no" checked>Não
        </p>
        <p>
            <button type="submit" name="delete">Apagar</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </p>
    </form>

<?php }