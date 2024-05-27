<?php 
function pat_table_creator()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'pat';
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
    $table_name = $wpdb->prefix . 'pat';
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
            <!-- <div class="cont">
                <div class="esq">
                    <span>Funcionário Responsável</span>
                </div>
                <input type="text" name="func_responsavel" ><br>
            </div> -->
            <div class="cont">
                <div class="esq">
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" ><br>
            </div>
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" ><br>
            </div> -->
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

    $table_name = $wpdb->prefix . 'pat';
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
            <!-- <th>Func. Responsável</th> -->
            <th>endereco</th>
            <!-- <th>Telefone</th> -->
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
                <!-- <td><?php //echo $employee['func_responsavel']; ?></td> -->
                <td><?php echo $employee['endereco']; ?></td>
                <!-- <td><?php //echo $employee['telefone']; ?></td> -->
                <td><?php echo $employee['email']; ?></td>

                <td><a href="admin.php?page=update-pat&id=<?php echo $employee['id']; ?>" class="btn-editar">EDITAR</a></td>
                <td><a href="admin.php?page=delete-pat&id=<?php echo $employee['id']; ?>" class="btn-deletar">DELETAR</a></td>
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

    $table_name = $wpdb->prefix . 'pat';
    $msg = '';
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : "";
    
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A); ?>
   <div class="content-pat">
        <h1 class="title">PAT - Unidades</h1>
        <h2 class="subtitle">Atualização de Cadastro de Unidade</h2>
        <form method="post">     
            <div class="cont">
                <div class="esq">
                    <span>Município</span>
                </div>
                <input type="text" name="municipios" value="<?php echo $employee_details['municipios']; ?>" required><br>
            </div>  
            <div class="cont">
                <div class="esq">
                    <span>Centro Regional</span>
                </div>
                <input type="text" name="centro_regional" value="<?php echo $employee_details['centro_regional']; ?>" ><br>
            </div>
            <!-- <div class="cont">
                <div class="esq">
                    <span>Func. Responsável</span>
                </div>
                <input type="text" name="func_responsavel" value="<?php //echo $employee_details['func_responsavel']; ?>" ><br>
            </div> -->
            <div class="cont">
                <div class="esq">
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" value="<?php echo $employee_details['endereco']; ?>" ><br>
            </div> 
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" value="<?php //echo $employee_details['telefone']; ?>" ><br>
            </div> -->
            <div class="cont">
                <div class="esq">
                    <span>E-mail</span>
                </div>
                <input type="text" name="email" value="<?php echo $employee_details['email']; ?>" ><br>
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
    $table_name = $wpdb->prefix . 'pat';
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
        
            <button class="btn-pat" type="submit" name="delete">OK</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </div>        
    </form>
<?php 
}

function cwpai_exclude_data_from_xyztable() {
    global $wpdb;
    
    // Define the table name
    $table_name = $wpdb->prefix . 'pat';
    
    // SQL query to delete rows from the table where municipios, centro_regional, func_responsavel, endereco, telefone and email are not null
    $sql = $wpdb->prepare(
        "DELETE FROM $table_name WHERE municipios IS NOT NULL AND centro_regional IS NOT NULL AND func_responsavel IS NOT NULL AND endereco IS NOT NULL AND telefone IS NOT NULL AND email IS NOT NULL"
    );
    
    // Execute the query
    $wpdb->query($sql);
}

/*
=========================================================================================================
*/
// Function to insert values into the 'pat' table upon plugin activation
function cwpai_insert_data_into_pat_table() {
    global $wpdb;

    // Define the table name
    $table_name = $wpdb->prefix . 'pat';

    // Define the data to be inserted
    $sql = $wpdb->prepare(
        "INSERT INTO $table_name (municipios, centro_regional, func_responsavel, endereco, telefone, email)
        VALUES
('Andradina','Araçatuba','R. José Augusto de Carvalho, 1173 - Centro, Andradina - SP, 16901-015','patandradina@sde.sp.gov.br'),
('Araçatuba','Araçatuba','Rua Almirante Barroso, 47 - Centro - Araçatuba - 16015-085','pataracatuba@sde.sp.gov.br'),
('Araçatuba - Poupatempo','Araçatuba','Rua Tenente Alcides Theodoro dos Santos, 70 - Aviação - Araçatuba - SP - 16055-557','-'),
('Birigui','Araçatuba','Rua Wagih Rahal, 65 – Morumbi – Birigui  -  16200-242','patbirigui@sde.sp.gov.br'),
('General Salgado','Araçatuba','Av. Plínio Ribeiro do Val, 1.054 - Centro - General Salgado, SP, 15300-000','patgeneralsalgado@sde.sp.gov.br'),
('Ilha Solteira','Araçatuba','Av. Atlântica, 1659 - Nova Ilha, Ilha Solteira - SP, 15385-000,Box 3 e 4 ','patilhasolteira@sde.sp.gov.br'),
('Penápolis','Araçatuba','Rod. Srg. Luciano Arnaldo Covolan, 1055 - Parque Industrial, Penápolis - SP, 16306-550','patpenapolis@sde.sp.gov.br'),
('Pereira Barreto','Araçatuba','Praça da Bandeira Comendador Jorge Tanaka, 80 Pereira Barreto - SP, 15370-459','patpereirabarreto@sde.sp.gov.br'),
('Barretos','Barretos','Via Conselheiro Antonio Prado, 1400, Bairro Pedro Cavalini - 14784-222','patbarretos@sde.sp.gov.br'),
('Bebedouro','Barretos','Av. Pref. Hércules Pereira Hortal, 1367 - Centro, Bebedouro - SP, 14701-210','patbebedouro@sde.sp.gov.br'),
('Colina','Barretos','Av. Ângelo Martins Tristão, 125 - Centro, Colina - SP, 14770-000 ','patcolina@sde.sp.gov.br'),
('Guaíra','Barretos','R. Oito, 221 - Centro, Guaíra - SP, 14790-000','patguaira@sde.sp.gov.br'),
('Jaborandi','Barretos','Rua Colina 900, Centro - Jaborandi/SP - 14775-000','patjaborandi@sde.sp.gov.br'),
('Olímpia','Barretos','Av. Harry Giannecchini - Jardim Toledo, Olímpia - SP, 15400403','patolimpia@sde.sp.gov.br'),
('Pirangi','Barretos','Av. da Saudade, 988, Pirangi - SP, 15820-000','patpirangi@sde.sp.gov.br'),
('Viradouro','Barretos','Praça Francisco Braga 56 - Centro - Viradouro - 14740-000','patviradouro@sde.sp.gov.br'),
('Bariri','Bauru','Rua Camilo Resegue, 68 - sala 1, Centro - 17250-105','patbariri@sde.sp.gov.br'),
('Barra Bonita','Bauru','R. XIV de dezembro, 1193 - Centro Barra Bonita/SP - 17340-000','patbarrabonita@sde.sp.gov.br'),
('Bauru','Bauru','Rua Inconfidência, 50, quadra 04 - Centro - Bauru - SP - 17010-070','-'),
('Dois Córregos','Bauru','Pr. Pref. Oswaldo Casonato, 305 - Dois Córregos, SP, 17300-000 - sala 8','patdoiscorregos@sde.sp.gov.br'),
('Jaú','Bauru','Rua Treze de Maio, 347 - Centro - Jaú - São Paulo-17201-420','patjau@sde.sp.gov.br'),
('Lençóis Paulista','Bauru','Rua Coronel Joaquim Gabriel, nº11 - Centro - Lençóis Paulista - 18680-090','patlencois.paulista@sde.sp.gov.br'),
('Lins','Bauru','Rua Olavo Bilac, 640 - Centro - Lins/SP - 16400-075','patlins@sde.sp.gov.br'),
('Pederneiras','Bauru','Tv. Anchieta, 82 - Vila Ruiz, Pederneiras - SP, 17280-000','patpederneiras@sde.sp.gov.br'),
('Piratininga','Bauru','Rua Manoel Pedro Carneiro, 100 - Centro - Piratininga - 17490-082','patpiratininga@sde.sp.gov.br'),
('Aguaí','Campinas','Praça Tancredo Neves, 23 - Centro - Aguaí, 13860-044','pataguai@sde.sp.gov.br'),
('Americana','Campinas','R. Anhanguera, 40 - Centro, Americana - SP, 13466-060','patamericana@sde.sp.gov.br'),
('Amparo','Campinas','Av. Bernardino de Campos, 705 - Centro, Amparo - SP, 13900-000','patamparo@sde.sp.gov.br'),
('Araras','Campinas','Avenida Zurita, 681 – Belvedere - 13601-020','patararas@sde.sp.gov.br'),
('Artur Nogueira','Campinas','Rua Alice Pereira Mansur, 51- Vila Queiroz- Artur Nogueira-SP, 13160-102.','patarturnogueira@sde.sp.gov.br'),
('Atibaia','Campinas','Avenida Joviano Alvim, 112 - Alvinópolis - Atibaia - 12942-653','patatibaia@sde.sp.gov.br'),
('Bragança Paulista','Campinas','Rua Cel. Teófilo Leme, 1.240  - Centro - Bragança Paulista - 12900-000','patbraganca@sde.sp.gov.br; captacao.braganca@sde.sp.gov.br'),
('Brotas','Campinas','Av. Ângelo Piva, 390 - Centro, Brotas - SP, 17380-017','patbrotas@sde.sp.gov.br'),
('Cabreúva','Campinas','Rua Antônio Furquim 220 Galeria Melville - Centro Administrativo  13318-108','patcabreuva@sde.sp.gov.br'),
('Campinas','Campinas','Rua Jacy Teixeira Camargo, 940 - Jardim do Lago - Campinas - SP - CEP: 13050-913','-'),
('Campo Limpo Paulista','Campinas','Avenida Alfried Krupp, 1025 - Jardim America, Campo Limpo Paulista - SP, 13230-060','patclimpopaulista@sde.sp.gov.br'),
('Capivari','Campinas','R. Tiradentes, 283 - Centro, Capivari - SP - 13360-097','patcapivari@sde.sp.gov.br'),
('Casa Branca','Campinas','R. Capitão Horta, 758 - Jardim Paulista, Casa Branca - SP, 13700-000','patcasabranca@sde.sp.gov.br'),
('Conchal','Campinas','R. Álvaro Ribeiro, 300 - Centro, Conchal - SP, 13835-000','patconchal@sde.sp.gov.br'),
('Cordeirópolis','Campinas','Av. Pres. Vargas, 663 - Vila Nova Brasilia, Cordeirópolis - SP, 13490-154','patcordeiropolis@sde.sp.gov.br'),
('Espírito Santo do Pinhal','Campinas','Av. Quirino Dos Santos, 152 - Largo São João - Espírito Santo do Pinhal - SP, 13990-000','patespinhal@sde.sp.gov.br'),
('Hortolândia','Campinas','R. Argolino de Morães, 405 - Vila Sao Francisco, Hortolândia - SP, 13184-230','pathortolandia@sde.sp.gov.br'),
('Indaiatuba','Campinas','R. Vinte e Quatro de Maio, 1670 - Centro, Indaiatuba - SP, 13330-060','patindaiatuba@sde.sp.gov.br'),
('Iracemápolis','Campinas','Rua Duque de Caxias, 520 - Centro - Iracemápolis - 13495-029','patiracemapolis@sde.sp.gov.br'),
('Itapira','Campinas','Rua Saldanha Marinho, s/n°, Itapira-SP, 13970-070','patitapira@sde.sp.gov.br'),
('Itatiba','Campinas','Av. Nair Soares de Macedo Fatori, 200 - Vila Sta Clara - Itatiba -  13256-001','patitatiba@sde.sp.gov.br'),
('Itupeva','Campinas','R. Juliana de Oliveira Borges, 90 - Jardim Primavera, Itupeva - SP, 13295-000','patitupeva@sde.sp.gov.br'),
('Jaguariúna','Campinas','Rua Coronel Amâncio Bueno, 810 - Santa Maria - Jaguariúna -13911-262','patjaguariuna@sde.sp.gov.br'),
('Jundiaí','Campinas','Av. Antonio Frederico Ozanam, 6000 - Vila Rio Branco, Jundiaí - SP, 13215-276 - PISO G3','patjundiai@sde.sp.gov.br'),
('Jundiaí - Poupatempo','Campinas','Avenida União dos Ferroviários, 1760 - Centro - Jundiaí - SP - 13201-160','-'),
('Leme','Campinas','R. Dr. Armando Salles Oliveira, 1085 - Centro, Leme - SP, 13610-220','patleme@sde.sp.gov.br'),
('Limeira','Campinas','R. Tiradentes, 1366 - 1º Andar, Centro - Limeira - São Paulo - 13480-081','patlimeira@sde.sp.gov.br'),
('Mococa','Campinas','Rua Visconte do Rio Branco, 741 - Centro - Mococa - 13730-250','patmococa@sde.sp.gov.br'),
('Mogi Guaçu','Campinas','R. São José, 49 - Vila Julia, Mogi Guaçu - SP, 13845-232','patmogiguacu@sde.sp.gov.br'),
('Mogi Mirim','Campinas','Rua Doutor José Alves, 55 - Centro - Mogi Mirim - 13800-050','patmogimirim@sde.sp.gov.br'),
('Monte Mor','Campinas','Praça Coronel Domingos Ferreira, 95 - Centro - Monte Mor -13190-000','patmontemor@sde.sp.gov.br'),
('Pedreira','Campinas','R. Miguel Sarkis, 61, Pedreira - SP, 13920-000','patpedreira@sde.sp.gov.br'),
('Piracicaba','Campinas','Praça José Bonifácio, 700 - Centro - Piracicaba - SP -13400-340','-'),
('Pirassununga','Campinas','R. dos Lemes, 971 - Centro, Pirassununga - SP, 13630-137 - Rodoviária - Box 3','patpirassununga@sde.sp.gov.br'),
('Rio Claro','Campinas','Rua 3, número 1636 - Centro - Rio Claro- Sp CEP: 13500-161','patrioclaro@sde.sp.gov.br'),
('Rio Claro - Poupatempo','Campinas','Av. Conde Francisco Matarazzo Júnior, 205 - Vila Paulista - Rio Claro - SP - CEP: 13506-845','-'),
('Rio das Pedras','Campinas','R. Ladeira José Leite de Negreiros, 10 - Centro, Rio das Pedras - SP, 13390-000','patriodaspedras@sde.sp.gov.br'),
('São João da Boa Vista','Campinas','Praça da Catedral, 07 - Centro, São João da Boa Vista - SP, 13870-009 ','patsjboavista@sde.sp.gov.br'),
('São José do Rio Pardo','Campinas','Av Independencia , 279, Centro - São José do Rio Pardo 13720-000','patsjriopardo@sde.sp.gov.br'),
('São Pedro','Campinas','Av. dos Imigrantes, 688 - Vale do Sol - São Pedro - SP - 13521-000','patsaopedro@sde.sp.gov.br'),
('Serra Negra','Campinas','Praça João Pessoa s/n centro ao lado da rodoviária Serra Negra SP-13930-000','patserranegra@sde.sp.gov.br'),
('Sumaré','Campinas','Rua Justino França, 143 - Jardim São Carlos - Sumaré - São Paulo - 13170-050 ','patsumare@sde.sp.gov.br'),
('Valinhos','Campinas','Dr. Cândido Ferreira, 45 - Centro, Valinhos - SP, 13270-040 ','patvalinhos@sde.sp.gov.br'),
('Vargem Grande do Sul','Campinas','R. Cel. Lúcio, 925 - Centro, Vargem Grande do Sul - SP, 13880-000 ','patvargemgsul@sde.sp.gov.br'),
('Várzea Paulista','Campinas','R. João Póvoa, 97 - Jardim do Lar - Várzea Paulista - SP, 13220-224','patvarzeapaulista@sde.sp.gov.br'),
('Vinhedo','Campinas','R. Monteiro de Barros, 17, Centro, Vinhedo/SP, 13280-081','patvinhedo@sde.sp.gov.br'),
('Batatais','Franca','Av. Liberdade, 10 - Vila Cruzeiro,Batatais/SP, 14315-704','patbatatais@sde.sp.gov.br'),
('Franca','Franca','Rua Professor Laerte Barbosa Cintra, 712, Residencial Baldassari - 14401-269','patfranca@sde.sp.gov.br'),
('Franca - Poupatempo','Franca','Rua Ouvidor Freire, 1986 - Centro - Franca - SP - CEP: 14400-630','-'),
('Ituverava','Franca','R. Cel. Dionísio Barbosa Sandoval, 957 - Ituverava, SP, 14500-000','patituverava@sde.sp.gov.br'),
('Orlândia','Franca','Rua Um, 29 - Centro, Orlândia - SP, 14620-000 ','patorlandia@sde.sp.gov.br'),
('Apiaí','Itapeva','Av. Leopoldo Leme Verneque, 268 - Centro, Apiaí - SP, 18320-000','pat_apiai@sde.sp.gov.br'),
('Capão Bonito','Itapeva','Avenida Governardor Lucas Nogueira Garcez, nº134 - Centro Capão Bonito /SP CEP 18.305-550','patcapaobonito@sde.sp.gov.br'),
('Fartura','Itapeva','Rua Luiz Ribeiro Salgado, 20 - Centro - Fartura -  18870-056','patfartura@sde.sp.gov.br'),
('Itaí','Itapeva','R. XV de Novembro, 1038 - Centro, Itaí - SP, 18730-959','patitai@sde.sp.gov.br'),
('Itapeva','Itapeva','R. Lucas de Camargo, 290 - Centro, Itapeva - SP, 18400-340','patitapeva@sde.sp.gov.br'),
('Itararé','Itapeva','R. Itararé, 387 - Jardim Claudina, Itararé - SP, 18460-033','patitarare@sde.sp.gov.br'),
('Piraju','Itapeva','R. São Vicente de Paula, 95 - Vila Pedreiro - Piraju - SP, 18800-045','patpiraju@sde.sp.gov.br'),
('Taquarituba','Itapeva','R. Treze de Maio, 560 - Centro, Taquarituba - SP, 18740-000','pattaquarituba@sde.sp.gov.br'),
('Assis','Marília','Av. Armando Sales de Oliveira, 1170 - Vila Moraes Pinto, Assis - SP, 19802-082','patassis@sde.sp.gov.br'),
('Bastos','Marília','R. Campos Salles, 178 - Centro, Bastos - SP, 17690-000','patbastos@sde.sp.gov.br'),
('Cândido Mota','Marília','R. Fadlo Jabur, 931 - Centro, Cândido Mota - SP, 19880-000','patcmota@sde.sp.gov.br'),
('Garça','Marília','R. Barão do Rio Branco, 295 - Jardim Paulista, Garça - SP, 17400-352','patgarca@sde.sp.gov.br'),
('Marília','Marília','Av. das Indústrias, 294 - Marília SP, 17509-051','patmarilia@sde.sp.gov.br'),
('Marília - Poupatempo','Marília','Avenida das Indústrias, 430 - Palmital - Marília - SP - 17509-051','-'),
('Ourinhos','Marília','R. Cardoso Ribeiro, 290 - Centro, Ourinhos - SP, 19900-103','patourinhos@sde.sp.gov.br'),
('Paraguaçu Paulista','Marília','R. XV de Novembro, 496 - Vila Affini, Paraguaçu Paulista - SP, 19700-000','patparaguacupta@sde.sp.gov.br'),
('Santa Cruz do Rio Pardo','Marília','R. Catarina Etsuco Umezu, 404 - Bairro São José, Santa Cruz do Rio Pardo - SP, 18900-000','patsanta.crpardo@sde.sp.gov.br'),
('Tarumã','Marília','R. Girassol, 201 - Centro, Tarumã - SP, 19820-000','pat.taruma@sde.sp.gov.br'),
('Tupã','Marília','Av. Tapuias, 907 - Centro, Tupã - SP, 17600-260','pat.tupa@sde.sp.gov.br'),
('Adamantina','Presidente Prudente','Av. da Saudade, 1072 - Vila Endo, Adamantina - SP, 17800-000','patadamantina@sde.sp.gov.br'),
('Dracena','Presidente Prudente','Av. José Bonifácio, 1.430 - Centro, Dracena - SP, 17900-000','patdracena@sde.sp.gov.br'),
('Presidente Epitácio','Presidente Prudente','Av. Pres. Vargas, 14 82 - Vila Verde, Pres. Epitácio - SP, 19470-000','pat.pepitacio@sde.sp.gov.br'),
('Presidente Prudente','Presidente Prudente','Rua Rio Grande do Sul, nº 37 - Vila Marcondes - Presidente Prudente SP - 19030-130','patp.prudente@sde.sp.gov.br'),
('Presidente Prudente - Poupatempo','Presidente Prudente','Avenida Brasil, 1383 - Vila São Jorge - Presidente Prudente - SP - 19013-000','-'),
('Presidente Venceslau','Presidente Prudente','Travessa Tenente Osvaldo Barbosa, 42 - Centro, Pres. Venceslau - SP, 19400-015','pat.pvenceslau@sde.sp.gov.br'),
('Rosana','Presidente Prudente','Av. José Laurindo, 1540 - Centro, Rosana - SP, 19273-000','patrosana@sde.sp.gov.br'),
('Teodoro Sampaio','Presidente Prudente','R. Vitório Scapin, 963, Centro, Teodoro Sampaio, SP, 19280-000 ','pat.tsampaio@sde.sp.gov.br'),
('Américo Brasiliense','Região Administrativa Central','R. Benedito Storani, 661, Américo Brasiliense - SP, 14820-000','patamericobrasiliense@sde.sp.gov.br'),
('Araraquara','Região Administrativa Central','R. São Bento, 840 - Araraquara, SP, 14801-300','patararaquara@sde.sp.gov.br'),
('Araraquara - Poupatempo','Região Administrativa Central','Av. Maria Antonia Camargo de Oliveira, 261 - Centro, Araraquara - SP, 14800-370','-'),
('Borborema','Região Administrativa Central','R. Stélio Loureiro Machado, 31, Borborema - SP, 14955-000','patborborema@sde.sp.gov.br'),
('Descalvado','Região Administrativa Central','Rua Coronel Arthur Whitacker, 137 - Centro - Descalvado - 13690-000','patdescalvado@sde.sp.gov.br'),
('Gavião Peixoto','Região Administrativa Central','Alameda Estevo, 386, Gavião Peixoto - SP, 14813-000','patgaviaopeixoto@sde.sp.gov.br'),
('Ibitinga','Região Administrativa Central','R. Tiradentes, 1145 - Centro, Ibitinga - SP, 14940-000','patibitinga@sde.sp.gov.br'),
('Itápolis','Região Administrativa Central','Av. Pres. Valentim Gentil, 735 - Jardim Maria M Castro, Itápolis - SP, 14900-000','patitapolis@sde.sp.gov.br'),
('Matão','Região Administrativa Central','R. Rui Barbosa, 825 - Centro, Matão - SP, 15990-030','patmatao@sde.sp.gov.br'),
('Porto Ferreira','Região Administrativa Central','R. Perondi Igínio, 321, Porto Ferreira - SP, 13660-000','patportoferreira@sde.sp.gov.br'),
('Rincão','Região Administrativa Central','R. XXI de Novembro, 491, Rincão - SP, 14830-000','patrincao@sde.sp.gov.br'),
('Santa Rita do Passa Quatro','Região Administrativa Central','R. Vítor Meireles, 373 - Centro, Santa Rita do Passa Quatro - SP, 13670-000','patsrpquatro@sde.sp.gov.br'),
('São Carlos','Região Administrativa Central','Rua Roberto Símonsen, 51 - Centro - São Carlos - SP - CEP: 13574-022','-'),
('Taquaritinga','Região Administrativa Central','Rua romeo Marsico, 200 - Centro  - Taquaritinga- 15900-072','pattaquaritinga@sde.sp.gov.br'),
('Arujá','Região Metropolitana de São Paulo','Rua Ademar de Barros, 60 - Centro - Arujá -  07401-290','pataruja@sde.sp.gov.br'),
('Barueri','Região Metropolitana de São Paulo','Av. Henriqueta Mendes Guerra, 550 - Jardim Sao Pedro, Barueri - SP, 06401-160','patbarueri@sde.sp.gov.br'),
('Caieiras','Região Metropolitana de São Paulo','Av. Prof. Carvalho Pinto, 207 - Centro, Caieiras - SP, 07700-000 - 1° andar','patcaieiras@sde.sp.gov.br'),
('Cajamar','Região Metropolitana de São Paulo','Av. Ten. Marques, 55 - Jardim Santana, Cajamar - SP, 07750-000','patcajamar@sde.sp.gov.br'),
('Carapicuíba','Região Metropolitana de São Paulo','149 Subsolo, Estr. Ernestina Vieira - Vila Dirce, Carapicuíba - SP, 06382-260','patcarapicuiba@sde.sp.gov.br'),
('Cotia','Região Metropolitana de São Paulo','R. Monsenhor Ladeira, 38 - Vila Sao Francisco de Assis, Cotia - SP, 06717-127','patcotia@sde.sp.gov.br'),
('Embu das Artes','Região Metropolitana de São Paulo','Av. Rotary, 3483 - Parque Industrial Ramos de Freitas, Embu das Artes - SP, 06810-240','patembudasartes@sde.sp.gov.br'),
('Embu-Guaçu','Região Metropolitana de São Paulo','R. Dagmar Antonio Bueno, 86 - Vila Louro, Embu-Guaçu - SP, 06900-000','patembu.guacu@sde.sp.gov.br'),
('Ferraz de Vasconcelos','Região Metropolitana de São Paulo','Rua Américo Trufelli, 60 - Parque Dourado, Ferraz de Vasconcelos - SP, 08527-052','patferraz@sde.sp.gov.br'),
('Francisco Morato','Região Metropolitana de São Paulo','R. Tabatinguera, 45 - Centro, Francisco Morato - SP, 07909-150','patf.morato@sde.sp.gov.br'),
('Franco da Rocha','Região Metropolitana de São Paulo','Av. dos Expedicionários, 77 - Companhia Fazenda Belem, Franco da Rocha - SP, 07803-010','patfrancodarocha@sde.sp.gov.br'),
('Guarulhos','Região Metropolitana de São Paulo','Rodovia Presidente Dutra, Km 225 - s/n - Vila Itapegica - Guarulhos - SP - CEP: 07034-911 - Internacional Shopping Guarulhos','-'),
('Itapecerica da Serra','Região Metropolitana de São Paulo','Rua Treze de Maio, 100 – Centro – Itapecerica da Serra / SP - 06850-840','patitapecerica.serra@sde.sp.gov.br'),
('Itapevi','Região Metropolitana de São Paulo','R. José Michelotti, 88 - Cidade da Saude, Itapevi - SP, 06693-005','patitapevi@sde.sp.gov.br'),
('Itaquaquecetuba','Região Metropolitana de São Paulo','R. Dom Tomás Frei, 89 - Centro, Itaquaquecetuba - SP, 08579-100 ','patitaquaquecetuba@sde.sp.gov.br'),
('Juquitiba','Região Metropolitana de São Paulo','R. Antônio Cândido de Assis, 25 - Centro - Juquitiba - SP, 06950-000','patjuquitiba@sde.sp.gov.br'),
('Mairiporã','Região Metropolitana de São Paulo','Rua XV de novembro, 101 - Centro - Mairiporã - 07600-057 ','patmairipora@sde.sp.gov.br'),
('Mogi das Cruzes','Região Metropolitana de São Paulo','Av. Doutor Candido Xavier de Almeida e Souza, 133, Centro, Mogi das Cruzes - 08780-210 - Centro Cívico','patm.cruzes@sde.sp.gov.br'),
('Mogi das Cruzes - Poupatempo','Região Metropolitana de São Paulo','Avenida Vereador Narciso Yague Guimarães, 1000 - Centro Cívico - Mogi das Cruzes - SP - CEP: 08780-000','-'),
('Poá','Região Metropolitana de São Paulo','Av. Prefeito Jorge Francisco Correia Allen, 87 - Centro - Poá -  08562-000','patpoa@sde.sp.gov.br'),
('Ribeirão Pires','Região Metropolitana de São Paulo','R. Cap. José Galo, 55 - Centro - Ribeirão Pires - SP, 09400-080','patribeiraopires@sde.sp.gov.br'),
('Rio Grande da Serra','Região Metropolitana de São Paulo','Rua José Carlos Carlson, 280 - Centro - Rio Grande da Serra  - São Paulo - 09450-000','patrgserra@sde.sp.gov.br'),
('Santa Isabel','Região Metropolitana de São Paulo','Praça Fernando Lopes, 32 - Centro, Santa Isabel - SP, 07500-000','patsantaisabel@sde.sp.gov.br'),
('Santana de Parnaíba','Região Metropolitana de São Paulo','Av. Ten. Marques, 5297 - Fazendinha, Santana de Parnaíba - SP, 06529-001','pats.parnaiba@sde.sp.gov.br'),
('São Bernardo do Campo','Região Metropolitana de São Paulo','Rua Nicolau Filizola. número 100 - Centro - São Bernardo do Campo - SP - CEP: 09725-760','-'),
('São Caetano do Sul','Região Metropolitana de São Paulo','R Major Carlo del Prete, 651 - Centro - São Caetano do Sul - 09530-000','patscaetano@sde.sp.gov.br'),
('São Paulo - CIC do Imigrante ','Região Metropolitana de São Paulo','Rua Barra Funda 1020 - Barra Funda - São Paulo - 01152-000','pat.imigrante@sde.sp.gov.br'),
('São Paulo - CIC Grajaú','Região Metropolitana de São Paulo','R. Pinheiro Chagas, 17 - Jardim Sao Jose, São Paulo - SP, 04837-030','patgrajau@sde.sp.gov.br'),
('São Paulo - CIC Jabaquara','Região Metropolitana de São Paulo','Rod. dos Imigrantes, km 11,5 casa 19 - Vila Guarani, São Paulo - SP, 04329-000','pat.jabaquara@sde.sp.gov.br'),
('São Paulo - Feitiço da Vila','Região Metropolitana de São Paulo','Estr. de Itapecerica, 8887 - Parque Fernanda, São Paulo - SP, 05858-002','patfeiticodavila@sde.sp.gov.br'),
('São Paulo - Poupatempo Cidade Ademar','Região Metropolitana de São Paulo','Av. Cupecê, 5497 - Jardim Miriam, São Paulo - SP, 04366-000','-'),
('São Paulo - Poupatempo Itaquera','Região Metropolitana de São Paulo','Av. do Contorno, 60 - Cidade Antônio Estêvão de Carvalho, São Paulo - SP, 08220-380','-'),
('São Paulo - Poupatempo Lapa','Região Metropolitana de São Paulo','Rua do Curtume, s/n - Lapa, São Paulo - SP, 05033-002','-'),
('São Paulo - Poupatempo Santo Amaro','Região Metropolitana de São Paulo','R. Amador Bueno, 229 - 2º andar - Santo Amaro, São Paulo - SP, 04752-005','-'),
('São Paulo - Poupatempo Sé','Região Metropolitana de São Paulo','R. do Carmo, s/nº - Sé, São Paulo - SP, 01019-200','-'),
('Suzano','Região Metropolitana de São Paulo','Av. Paulo Portela, 210 - Jardim Paulista, Suzano - SP, 08675-230','patsuzano@sde.sp.gov.br'),
('Taboão da Serra','Região Metropolitana de São Paulo','Rua Cesário Dau, 535, Jardim Maria Rosa - Taboão da Serra -  06763-080','pattaboaodaserra@sde.sp.gov.br'),
('Vargem Grande Paulista','Região Metropolitana de São Paulo','Rua Benedita Maciel De Almeida , 85 - Centro - Vargem Grande Paulista - 06730-000','patvargemgrandepta@sde.sp.gov.br'),
('Registro','Registro','Av. Wild José de Souza, 456 - Vila Tupy, Registro - SP, 11900-000','crregistro@sde.sp.gov.br'),
('Cravinhos','Ribeirão Preto','R. Dr. José Eduardo Viêira Palma, 52, Cravinhos - SP, 14140-000','patcravinhos@sde.sp.gov.br'),
('Guariba','Ribeirão Preto','Rua Rui Barbosa, 345 - Centro - Guariba, 14840-000','patguariba@sde.sp.gov.br'),
('Jaboticabal','Ribeirão Preto','Esplanada do Lago Carlos Rodrigues Serra, 160 - Vila Serra, Jaboticabal - SP, 14870-090','patjaboticabal@sde.sp.gov.br'),
('Jardinópolis','Ribeirão Preto','Praça Dr. Mario Lins, 150 - Centro, Jardinópolis - SP, 14680-000','patjardinopolis@sde.sp.gov.br'),
('Monte Alto','Ribeirão Preto','R. Dr. Raul da Rocha Medeiros, 1565 - Centro, Monte Alto - SP, 15910-000','patmontealto@sde.sp.gov.br'),
('Pontal','Ribeirão Preto','R. Ananias da Costa Freitas, 571 - Jardim Aparecida, Pontal - SP, 14180-000','patpontal@sde.sp.gov.br'),
('Ribeirão Preto','Ribeirão Preto','Av. Dr. Francisco Junqueira, 2625 - Campos Elísios, Ribeirão Preto - SP, 14091-000','pat.rpreto@sde.sp.gov.br'),
('Ribeirão Preto - Poupatempo','Ribeirão Preto','Avenida Presidente Kennedy, número 1.500 - Ribeirânia - Ribeirão Preto - SP - CEP: 14096-340','-'),
('Santa Rosa de Viterbo','Ribeirão Preto','Praça Doutor Guido Maestrelo, 180, Santa Rosa do Viterbo, SP, 14270-000','pat.stviterbo@sde.sp.gov.br'),
('Serrana','Ribeirão Preto','Av. Arsênio Ramos Martins, 207, Serrana - SP, 14150-000','patserrana@sde.sp.gov.br'),
('Sertãozinho','Ribeirão Preto','Rua Voluntário Otto Gomes Martins, 1380 - Centro - Sertãozinho SP - 14160-105','patsertaozinho@sde.sp.gov.br'),
('Bertioga','Santos','Avenida Dezenove de Maio, 684 - Jardim Albatroz, Bertioga - SP, 11250-767','patbertioga@sde.sp.gov.br'),
('Cubatão','Santos','Avenida Dr. Fernando Costa, 1096 - Vila Couto - Cubatão - 11510-310 ','patcubatao@sde.sp.gov.br'),
('Guarujá','Santos','Av. Santos Dumont, 1586 - Paecara, Guarujá - SP, 11460-001','patguaruja@sde.sp.gov.br'),
('Itanhaém','Santos','R. Antonio Olivio de Araujo, 5 - Centro, Itanhaém - SP, 11740-000','patitanhaem@sde.sp.gov.br'),
('Mongaguá','Santos','Av. São Paulo, 1580 - 3° andar - Centro, Mongaguá - SP, 11730-000','pat.mongagua@sde.sp.gov.br'),
('Peruíbe','Santos','R. Jaçanã, 31 - Centro, Peruíbe - SP, 11750-000','patperuibe@sde.sp.gov.br'),
('Praia Grande','Santos','Litoral Plaza Shopping, Av. Ayrton Senna da Silva, 1511 - Xixová, Praia Grande - SP, 11726-900','patp.grande@sde.sp.gov.br'),
('Santos','Santos','Rua João Pessoa, 246 - Centro - Santos - SP - CEP: 11013-002','-'),
('São Vicente','Santos','Av. Presidente Wilson, 1126 - Itararé - São Vicente/SP - 11320-010','patsaovicente@sde.sp.gov.br'),
('Catanduva','São José do Rio Preto','Avenida Comendador Antônio Stocco, 537 - Parque Joaquim Lopes, Catanduva - SP, 15800-610','patcatanduva@sde.sp.gov.br'),
('Fernandópolis','São José do Rio Preto','Rua São Paulo, 2570, Coester, Fernandopolis, 15603-084','patfernandopolis@sde.sp.gov.br'),
('Itajobi','São José do Rio Preto','Rua Pedro de Toledo , 1433 - Centro - Itajobi - 15840-000','patitajobi@sde.sp.gov.br'),
('Jales','São José do Rio Preto','Av. da Integração, 2689 - Jardim Trianon, Jales - SP, 15703-118','patjales@sde.sp.gov.br'),
('José Bonifácio','São José do Rio Preto','Avenida Rio Branco, 331 - Centro - José Bonifácio','patjbonifacio@sde.sp.gov.br'),
('Mirassol','São José do Rio Preto','R. Quintino Bocaiúva, 2138 - Centro, Mirassol - SP, 15130-000','patmirassol@sde.sp.gov.br'),
('Monte Aprazível','São José do Rio Preto','R. Osvaldo Aranha, 1080 - Monte Aprazível, SP, 15150-000','patmaprazivel@sde.sp.gov.br'),
('Nova Granada','São José do Rio Preto','Av. Adolfo Rodrigues, 868 - 1 Andar - Centro - Nova Granada - 15440-000','patngranada@sde.sp.gov.br'),
('Novo Horizonte','São José do Rio Preto','R. Sete de Setembro, 736 - Novo Horizonte, SP, 14960-000','patnovohorizonte@sde.sp.gov.br'),
('Potirendaba','São José do Rio Preto','Av. Mirassolândia, 1775 - Solo Sagrado - São José do Rio Preto - SP, 15045-000','patpotirendaba@sde.sp.gov.br'),
('Santa Fé do Sul','São José do Rio Preto','Rua Onze, 1220, Centro , Santa Fé do Sul - SP, 15775-000','pat.sfedosul@sde.sp.gov.br'),
('São José do Rio Preto','São José do Rio Preto','Rua Boa Vista, 666 - Bairro Boa Vista - São Jose do Rio Preto - 15025-010 ','patsjriopreto@sde.sp.gov.br'),
('São José do Rio Preto - Poupatempo','São José do Rio Preto','Rua Antônio de Godoy, 3033 - Centro - São José do Rio Preto - SP - CEP: 15013-310','-'),
('Votuporanga','São José do Rio Preto','R. Barão do Rio Branco, 4497 - Recanto dos Esportes, Votuporanga - SP, 15500-055','patvotuporanga@sde.sp.gov.br'),
('Aparecida','São José dos Campos','Avenida Papa João Paulo II (Avenida Monumental), 287 - Centro, Aparecida - SP, 12575-050','pataparecida@sde.sp.gov.br'),
('Caçapava','São José dos Campos','Ladeira São José, 90 - Centro - Caçapava 12281-505','pat.cacapava@sde.sp.gov.br'),
('Cachoeira Paulista','São José dos Campos','R. José da Silveira Mendes, 227-155, Cachoeira Paulista - SP, 12630-000','pat.cpaulista@sde.sp.gov.br'),
('Campos do Jordão','São José dos Campos','Rua Manoel Pereira Alves, s/n, no Polo de Estacionamento, em Vila Abernéssia - 12460-000','patcampos.jordao@sde.sp.gov.br'),
('Caraguatatuba','São José dos Campos','Rua Taubaté, 520 - Sumaré - Caraguatatuba - 11661-060','patcaraguatatuba@sde.sp.gov.br'),
('Caraguatatuba - Poupatempo','São José dos Campos','Avenida Rio Branco, 955 - Indaiá - Caraguatatuba - SP - CEP: 11665-600','-'),
('Cruzeiro','São José dos Campos','Rua Dr. Othon Barcellos, 101 - Vila Paulista, Cruzeiro - SP, 12701-080','patcruzeiro@sde.sp.gov.br'),
('Guaratinguetá','São José dos Campos','Praça Rotary, S/Nº - Quinzinho Fernandes - Guaratinguetá - 12501-250 - Box 11','patguaratingueta@sde.sp.gov.br'),
('Ilhabela','São José dos Campos','Av. Princesa Isabel, 1333 - Ilhabela, SP, 11630-000','patilhabela@sde.sp.gov.br'),
('Jacareí','São José dos Campos','Rua Barão de Jacareí, 839 - Centro - Jacareí - SP - 12308-270','patjacarei@sde.sp.gov.br'),
('Lorena','São José dos Campos','Av. Cap. Messías Ribeiro, 211 - Olaria, Lorena - SP, 12607-020','patlorena@sde.sp.gov.br'),
('Pindamonhangaba','São José dos Campos','Rua João Batista Goffi,79 centro Pindamonhangaba-Sp - 12400-310                     ','patpindamonhangaba@sde.sp.gov.br'),
('Piquete','São José dos Campos','Praça José Vieira Soares, 81 - Vila São José - Piquete - 12620-000 ','patpiquete@sde.sp.gov.br'),
('Potim','São José dos Campos','R. Antônio de Oliveira Portes, 149, Potim - SP, 12525-000','patpotim@sde.sp.gov.br'),
('São José dos Campos','São José dos Campos','Pça Afonso Pena, 175 - Centro, São José dos Campos - SP, 12210-090','patsjcampos@sde.sp.gov.br'),
('São José dos Campos - Poupatempo','São José dos Campos','Rua Andorra, 500, Shopping Jardim Oriente - Jardim América - São José dos Campos - SP - CEP: 12235050','-'),
('São Sebastião','São José dos Campos','Av. Guarda Mor Lobo Viana, nº 435 - loja 2 - Centro, São Sebastião - SP, 11600-000','patsaosebastiao@sde.sp.gov.br'),
('Taubaté','São José dos Campos','Rua Benedito da Silveira Moraes s/n Jardim  Emília - Rodoviária Nova Taubaté- Cep : 12070-290','pat.taubate@sde.sp.gov.br'),
('Taubaté - Poupatempo','São José dos Campos','Avenida Bandeirantes, 808 - Jardim Maria Augusta, Taubaté - SP, 12072-000','-'),
('Alumínio','Sorocaba','R. Ênio Fabiani, 49 - Vila Santa Luzia, Alumínio - SP, 18125-000','pataluminio@sde.sp.gov.br'),
('Avaré','Sorocaba','R. Bahia, 348-1 - Alto, Avaré - SP, 18700-000','patavare@sde.sp.gov.br'),
('Boituva','Sorocaba','Rua Coronel Eugênio Mota, 985, Centro, Boituva, SP, CEP 18550-103','patboituva@sde.sp.gov.br'),
('Botucatu','Sorocaba','Rua Rangel Pestana, s/n - Centro - BOTUCATU - 18600-070','patbotucatu@sde.sp.gov.br'),
('Ibiúna','Sorocaba','R. Raimundo Santiago, 30 - Centro, Ibiúna - SP, 18150-000 ','patibiuna@sde.sp.gov.br'),
('Iperó','Sorocaba','R. Costa e Silva, 195 - Jardim Santa Cruz, Iperó - SP, 18560-000','patipero@sde.sp.gov.br'),
('Itapetininga','Sorocaba','Rua monsenhor Soares, 251 centro - 18200-090','patitapetininga@sde.sp.gov.br'),
('Itu','Sorocaba','Avenida Itu 400 Anos, 111 - Novo Centro, Itu - SP, 13303-500','patitu@sde.sp.gov.br'),
('Laranjal Paulista','Sorocaba','R. Delfino de Melo, 63 - Bairro Matadouro, Laranjal Paulista - SP, 18500-000','patlaranjalpta@sde.sp.gov.br'),
('Mairinque','Sorocaba','Av. 27 de Outubro, 371 - Vila Sorocabana, Mairinque - SP, 18120-000','patmairinque@sde.sp.gov.br'),
('Piedade','Sorocaba','R. Benjamin Constant, 336 - Centro, Piedade - SP, 18170-000','patpiedade@sde.sp.gov.br'),
('Pilar do Sul','Sorocaba','Avenida Antonio Lacerda,308 - Santa Cecilia - Pilar do Sul - 18500-000','pat.pdosul@sde.sp.gov.br'),
('Salto','Sorocaba','Rua José Revel, 270 - Centro, Salto - SP, 13320-020','patsalto.captacao@sde.sp.gov.br'),
('Salto de Pirapora','Sorocaba','Rua Ovídio Leme dos Santos 196 - Centro -Salto de Pirapora CEP:18160-000','pat.spirapora@sde.sp.gov.br'),
('São Manuel','Sorocaba','Rua Epitacio Pessoa, 1100 - Centro, São Manuel - SP, 18650-055','patsaomanuel@sde.sp.gov.br'),
('São Miguel Arcanjo','Sorocaba','Av. João Paulino da Silva, 160A - Vila Nova, São Miguel Arcanjo - SP, 18230-000','patsmarcanjo@sde.sp.gov.br'),
('São Roque','Sorocaba','R. Rui Barbosa, 693 - Centro, São Roque - SP, 18130-440','patsaoroque@sde.sp.gov.br'),
('Sorocaba','Sorocaba','R. Coronel Cavalheiros, 353 - Centro, Sorocaba - SP, 18035-640','patsorocaba@sde.sp.gov.br'),
('Sorocaba - Poupatempo','Sorocaba','Rua Leopoldo Machado, 525 - Centro - Sorocaba - SP - CEP: 18035-075','-'),
('Tatuí','Sorocaba','Praça Martinho Guedes, 12 - Centro, Tatuí - SP, 18270-370','pat.tatui@sde.sp.gov.br'),
('Votorantim','Sorocaba','Av. Ver. Newton Vieira Soares, 475 - Centro, Votorantim - SP, 18110-013','patvotorantim@sde.sp.gov.br')

        
        ");

     // Execute the query
     $wpdb->query($sql);    
}

// Hook the function to the activation process
