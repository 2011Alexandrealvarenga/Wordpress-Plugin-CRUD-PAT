<?php 
function pat_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'PAT';
    $sql = "DROP TABLE IF EXISTS $table_name;
            CREATE TABLE $table_name(
            id mediumint(11) NOT NULL AUTO_INCREMENT,

            municipios varchar(50) NOT NULL,
            centro_regional varchar(50) NOT NULL,
            func_responsavel varchar(50) NOT NULL,
            endereco varchar (250) NOT NULL,
            telefone varchar (250) NOT NULL,
            email varchar (50) NOT NULL,

            PRIMARY KEY id(id)
            )$charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function pat_da_display_esm_menu()
{
    add_menu_page('PAT Unidades', 'PAT Unidades', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback','', 8);
    add_submenu_page('pat-emp-list', 'PAT - Lista', 'PAT - Lista', 'manage_options', 'pat-emp-list', 'da_PAT_list_callback');
    add_submenu_page(null, 'PAT Atualiza', 'PAT Atualiza', 'manage_options', 'update-pat', 'pat_da_emp_update_call');
    add_submenu_page(null, 'Delete Employee', 'Delete Employee', 'manage_options', 'delete-pat', 'pat_da_emp_delete_call');
}

//[employee_list]
// add_shortcode('employee_list', 'da_PAT_list_callback');

function da_PAT_list_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'PAT';
    $msg = '';
    if (isset($_REQUEST['submit'])) {
        $wpdb->insert("$table_name", [
            "municipios" => $_REQUEST['municipios'],
            'centro_regional' => $_REQUEST['centro_regional'],
            'func_responsavel' => $_REQUEST['func_responsavel'],
            'endereco' => $_REQUEST['endereco'],
            'telefone' => $_REQUEST['telefone'],
            'email' => $_REQUEST['email']
        ]);

        if ($wpdb->insert_id > 0) {
            $msg = "Gravado com sucesso!";
        } else {
            $msg = "Falha ao gravar!";
        }
    }

    ?>
    <div class="content-pat">
        <h1 class="title">PAT - Unidades</h1>
        <h2 class="subtitle">Cadastro de Unidade</h2>
        <form method="post">
            <div class="cont">
                <div class="esq">
                    <span>Municipios</span>
                </div>
                <input type="text" name="municipios" required><br>
            </div>
            <div class="cont">

                <div class="esq">
                    <span>Centro Regional</span>
                </div>
                <input type="text" name="centro_regional" required><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Funcionário Responsável</span>
                </div>
                <input type="text" name="func_responsavel" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Email</span>
                </div>
                <input type="text" name="email" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <h4 id="msg" class="alert"><?php echo $msg; ?></h4>
                    <button class="btn-pat" type="submit" name="submit">CADASTRAR</button>

                </div>
            </div>           
        </form>
    </div>
    <?php 

    $table_name = $wpdb->prefix . 'PAT';
    $employee_list = $wpdb->get_results($wpdb->prepare("select * FROM $table_name ORDER BY municipios asc "), ARRAY_A);
    if (count($employee_list) > 0): ?>  

        <div class="busca">
            <h3 class="subtitle">Realize a busca da unidade</h3>
            <input type="text" class="form-control" id="live_search" autocomplete="off" placeholder="Ex.: Município, CEP, Endereço ...">
        </div>   
        <div id="searchresult" style="margin: 24px 10px 0 0; display: block;"></div>
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
        <div id="registros-todos-dados-tabela" style="margin: 24px 10px 0 0;">
            <?php resultado_busca($employee_list);?>
        </div>
    <?php else:echo "<h2>Não há Informação</h2>";endif;
}


function resultado_busca($employee_list){?>
    <table border="1" cellpadding="5" width="100%">
        <tr>
            <th>ID</th>
            <th>Municipios</th>
            <th>Centro Regional</th>
            <th>Func. Responsável</th>
            <th>endereco</th>
            <th>Telefone</th>
            <th>Email</th>

            <th>Editar</th>
            <th>Deletar</th>
        </tr>
        <?php $i = 1;
        foreach ($employee_list as $index => $employee): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $employee['municipios']; ?></td>
                <td><?php echo $employee['centro_regional']; ?></td>
                <td><?php echo $employee['func_responsavel']; ?></td>
                <td><?php echo $employee['endereco']; ?></td>
                <td><?php echo $employee['telefone']; ?></td>
                <td><?php echo $employee['email']; ?></td>

                <td><a href="admin.php?page=update-pat&id=<?php echo $employee['id']; ?>" class="btn-editar">Editar</a></td>
                <td><a href="admin.php?page=delete-pat&id=<?php echo $employee['id']; ?>" class="btn-deletar">Deletar</a></td>
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
    
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A); ?>
   <div class="content-pat">
        <h1 class="title">PAT - Unidades</h1>
        <h2 class="subtitle">Atualização de Cadastro de Unidade</h2>
        <form method="post">     
            <div class="cont">
                <div class="esq">
                    <span>Local</span>
                </div>
                <input type="text" name="municipios" value="<?php echo $employee_details['local']; ?>" required><br>
            </div>  
            <div class="cont">
                <div class="esq">
                    <span>Municipio</span>
                </div>
                <input type="text" name="centro_regional" value="<?php echo $employee_details['municipio']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>CEP</span>
                </div>
                <input type="text" name="func_responsavel" value="<?php echo $employee_details['cep']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" value="<?php echo $employee_details['endereco']; ?>" ><br>
            </div> 
            <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" value="<?php echo $employee_details['telefone']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>E-mail</span>
                </div>
                <input type="text" name="email" value="<?php echo $employee_details['telefone']; ?>" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <button class="btn-pat" type="submit" name="update">ATUALIZAR</button>
                </div>
            </div>
            <div class="cont">
                <div class="esq">
                    <?php                     
                        if (isset($_REQUEST['update'])) {
                            if (!empty($id)) {
                                $wpdb->update("$table_name", [
                                    "municipios" => $_REQUEST['municipios'], 
                                    "centro_regional" => $_REQUEST['centro_regional'],
                                    "func_responsavel" => $_REQUEST['func_responsavel'],
                                    'endereco' => $_REQUEST['endereco'], 
                                    'telefone' => $_REQUEST['telefone'],
                                    'email' => $_REQUEST['email']            
                            ], ["id" => $id]);
                                $msg = 'Atualização realizada!';
                                echo '<h4 class="alert">    '. $msg .'</h4>';
                                echo '<a href="'. $urlvoltar.'" class="link-back">Voltar para a lista</a>';
                            }
                        }
                    ?>
                    
                </div>
            </div> 
            
            


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
        <script>location.href = "<?php echo site_url(); ?>/wp-admin/admin.php?page=pat-emp-list";</script>
    <?php } ?>
    <form method="post">
        <div class="content-pat">
            <h1 class="title">PAT - Unidades</h1>
            <h2 class="subtitle">Exclusão de cadastro de Unidade</h2>

            <h3 class="description">Deseja realmente apagar?</h3 >
            <input type="radio" name="conf" value="yes" checked>Sim
            <input type="radio" name="conf" value="no" >Não  <br><br>      
        
            <button class="btn-pat" type="submit" name="delete">Apagar</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </div>        
    </form>
<?php }