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
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" ><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Municipios</span>
                </div>
                <input type="text" name="municipios" required><br>
            </div>
            <div class="cont">
                <div class="esq">
                    <span>Email</span>
                </div>
                <input type="text" name="email" ><br>
            </div>
            <!-- <div class="cont">

                <div class="esq">
                    <span>Centro Regional</span>
                </div>
                <input type="text" name="centro_regional" required><br>
            </div> -->
            <!-- <div class="cont">
                <div class="esq">
                    <span>Funcionário Responsável</span>
                </div>
                <input type="text" name="func_responsavel" ><br>
            </div> -->
           
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" ><br>
            </div> -->
            
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
            <th>Endereco</th>
            <th>Municipios</th>
            <th>Email</th>
            <!-- <th>Centro Regional</th> -->
            <!-- <th>Func. Responsável</th> -->
            <!-- <th>Telefone</th> -->

            <th>Editar</th>
            <th>Deletar</th>
        </tr>
        <?php $i = 1;
        foreach ($employee_list as $index => $employee): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $employee['endereco']; ?></td>
                <td><?php echo $employee['municipios']; ?></td>
                <td><?php echo $employee['email']; ?></td>
                <!-- <td><?php //echo $employee['centro_regional']; ?></td> -->
                <!-- <td><?php //echo $employee['func_responsavel']; ?></td> -->
                <!-- <td><?php //echo $employee['telefone']; ?></td> -->

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
                    <span>Endereço</span>
                </div>
                <input type="text" name="endereco" value="<?php echo $employee_details['endereco']; ?>" ><br>
            </div>    
            <div class="cont">
                <div class="esq">
                    <span>Município</span>
                </div>
                <input type="text" name="municipios" value="<?php echo $employee_details['municipios']; ?>" required><br>
            </div>  
            <div class="cont">
                <div class="esq">
                    <span>E-mail</span>
                </div>
                <input type="text" name="email" value="<?php echo $employee_details['email']; ?>" ><br>
            </div>
            <!-- <div class="cont">
                <div class="esq">
                    <span>Centro Regional</span>
                </div>
                <input type="text" name="centro_regional" value="<?php echo $employee_details['centro_regional']; ?>" ><br>
            </div> -->
            <!-- <div class="cont">
                <div class="esq">
                    <span>Func. Responsável</span>
                </div>
                <input type="text" name="func_responsavel" value="<?php //echo $employee_details['func_responsavel']; ?>" ><br>
            </div> -->
           
            <!-- <div class="cont">
                <div class="esq">
                    <span>Telefone</span>
                </div>
                <input type="text" name="telefone" value="<?php //echo $employee_details['telefone']; ?>" ><br>
            </div> -->
            
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
    
    $table_name = $wpdb->prefix . 'pat';
    
    $sql = $wpdb->prepare(
        "DELETE FROM $table_name WHERE municipios IS NOT NULL AND centro_regional IS NOT NULL AND func_responsavel IS NOT NULL AND endereco IS NOT NULL AND telefone IS NOT NULL AND email IS NOT NULL"
    );
    
    $wpdb->query($sql);
}

function cwpai_insert_data_into_pat_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pat';

    $sql = $wpdb->prepare(
        "INSERT INTO $table_name (municipios, centro_regional, func_responsavel, endereco, telefone, email)
        VALUES

('Adamantina','','','CEP:17800000 - AVENIDA DA SAUDADE, 1072 - Vila Endo - ADAMANTINA - SP','','patadamantina@sde.sp.gov.br'),
('Aguaí','','','CEP:13860000 Logradouro - PRAÇA PRESIDENTE TANCREDO NEVES,Número:23Bairro:CENTROMunicípio:AGUAIUF:SP','','pataguai@sde.sp.gov.br'),
('Alumínio','','','CEP:18125000 - RUA ENIO FABIANE, 49 - SANTA LUZIA - ALUMINIO - SP','','pataluminio@sde.sp.gov.br'),
('Americana','','','CEP:13465-000 - RUA ANHANGUERA, 40 - Centro - AMERICANA - SP','','patamericana@sde.sp.gov.br'),
('Américo Brasiliense','','','CEP:14820000 Logradouro - RUA BENEDITO STORANINúmero:661Bairro:CENTROMunicípio:AMERICO BRASILIENSEUF:SP','','patamericobrasiliense@sde.sp.gov.br'),
('Amparo','','','CEP:13900410 - AV BERNARDINO DE CAMPOS, 705 - Campinas - AMPARO - SP','','patamparo@sde.sp.gov.br'),
('Andradina','','','CEP:16901007 - AVENIDA BANDEIRANTES, 665 - Centro - ANDRADINA - SP','','patandradina@sde.sp.gov.br'),
('Aparecida','','','CEP:12570000 - AV PAPA JOAO PAULO II, 287 - Centro - APARECIDA - SP','','pataparecida@sde.sp.gov.br'),
('Apiaí','','','CEP:18320000 - RUA LEOPOLDO LEME VERNECK, 260 - Centro - APIAI - SP','','pat_apiai@sde.sp.gov.br'),
('Araçatuba','','','CEP:16010210 - RUA ALMIRANTE BARROSO, 47 - Centro - ARACATUBA - SP','','pataracatuba@sde.sp.gov.br'),
('Araraquara','','','CEP:14802634 - R IVO ANTONIO MAGNANI, 200 - FONTE LUMINOSA - ARARAQUARA - SP','','patararaquara@sde.sp.gov.br'),
('Araras','','','CEP:13601020 - AV ZURITA, 681 - JARDIM BELVEDERE - ARARAS - SP','','patararas@sde.sp.gov.br'),
('Artur Nogueira','','','CEP:13160102 - Logradouro: RUA ALICE PEREIRA MANSURNúmero:51Bairro:VILA QUEIROZMunicípio:ARTUR NOGUEIRAUF:SP','','patarturnogueira@sde.sp.gov.br'),
('Arujá','','','CEP:7400000 - RUA PROFESSOR JOAO FELICIANO, 75 - BARBOSA - ARUJA - SP','','pataruja@sde.sp.gov.br'),
('Assis','','','CEP:19802082 - RUA MAUA, 127 - Centro - ASSIS - SP','','patassis@sde.sp.gov.br'),
('Atibaia','','','CEP:12942653 - Avenida Joviano Alvim, 112 - Alvinópolis - Atibaia / SP','','patatibaia@sde.sp.gov.br'),
('Avaré','','','CEP:18705120 - Rua Bahia, 1580 - Centro, Avaré','','patavare@sde.sp.gov.br'),
('Bariri','','','CEP:17250000 - RUA CAMILO RESEGUE, 68 - Centro - BARIRI - SP','','patbariri@sde.sp.gov.br'),
('Barra Bonita','','','CEP:17340000 - RUA 14 DE DEZEMRO, 1193 - Centro - BARRA BONITA - SP','','patbarrabonita@sde.sp.gov.br'),
('Barretos','','','CEP:14780900 - RUA TRINTA, 564 - Centro - BARRETOS - SP','','patbarretos@sde.sp.gov.br'),
('Barueri','','','CEP:6401160 - AV HENRIQUETA MENDES GUERRA, 550 - JD SAO PEDRO - BARUERI - SP','','patbarueri@sde.sp.gov.br'),
('Bastos','','','CEP:17690000 - Rua Campos Salles, 178 - Centro - BASTOS - SP','','patbastos@sde.sp.gov.br'),
('Batatais','','','CEP:14315704 - Avenida Liberdade, 10 - Vila Cruzeiro - Batatais - SP','','patbatatais@sde.sp.gov.br'),
('Bebedouro','','','CEP:14701210 - Avenida Hercules Pereira Hortal, 1367 Jardim São Sebastião - BEBEDOURO - SP','','patbebedouro@sde.sp.gov.br'),
('Bertioga','','','CEP:11250000 - Av. Dezenove de Maio, 694 - Jardim Albatroz - BERTIOGA - SP','','patbertioga@sde.sp.gov.br'),
('Birigui','','','CEP:16200242 - R WAGIH RAHAL, 65 - CENTRO - BIRIGUI - SP','','patbirigui@sde.sp.gov.br'),
('Boituva','','','CEP:18550000 - Rua Cel. Eugênio Motta, 985 - Vila Ferriello - BOITUVA - SP','','patboituva@sde.sp.gov.br'),
('Borborema','','','CEP:14955000 - Rua Stélio Loureiro Machado, 31 - Centro - BORBOREMA - SP','','patborborema@sde.sp.gov.br'),
('Botucatu','','','CEP:18600070 - Rua Rangel Pestana, S/N - Centro - BOTUCATU - SP','','patbotucatu@sde.sp.gov.br'),
('Bragança Paulista','','','CEP:12900002 - R CORONEL TEOFILO LEME, 1240 - CENTRO - BRAGANCA PAULISTA - SP','','patbraganca@sde.sp.gov.br'),
('Brotas','','','CEP:17380037- Rua José Martinelli, 15 - Centro - BROTAS - SP','','patbrotas@sde.sp.gov.br'),
('Cabreúva','','','CEP:13315000 - Rua Antônio Furquin, 220 - Jacaré - Cabreúva - CENTRO - SP','','patcabreuva@sde.sp.gov.br'),
('Caçapava','','','CEP:12282350 - Avenida Manoel Inocencio, 179 - 1 andar - Centro - Caçapava - SP','','pat.cacapava@sde.sp.gov.br'),
('Cachoeira Paulista','','','CEP:12630000 - Rua Costa Junior, 99 - Centro - Cachoeira Paulista - SP','','pat.cpaulista@sde.sp.gov.br'),
('Caieiras','','','CEP:07700000 - Avenida Professor Carvalho Pinto, 207 - 1º andar - Paço Municipal - CAIEIRAS - SP','','patcaieiras@sde.sp.gov.br'),
('Cajamar','','','CEP:7770000 - Avenida Tenente Marques, 55 - Polvilho - CAJAMAR - SP','','patcajamar@sde.sp.gov.br'),
('Campo Limpo Paulista','','','CEP:13230060 - Avenida Alfried Krupp, 1025 - Jardim América - CAMPO LIMPO PAULISTA - SP','','patclimpopaulista@sde.sp.gov.br'),
('Campos do Jordão','','','CEP:12460000 - Rua Manoel Pereira, S/N - Centro - CAMPOS DO JORDAO - SP','','patcampos.jordao@sde.sp.gov.br'),
('Cândido Mota','','','CEP:19880000 - RUA FADLO JABUR, 931 - Centro - CANDIDO MOTA - SP','','patcmota@sde.sp.gov.br'),
('Capão Bonito','','','CEP:18305505 - Av. Gov. Lucas Nogueira Garcês, 134 - Jardim Cruzeiro - CAPAO BONITO - SP','','patcapaobonito@sde.sp.gov.br'),
('Capivari','','','CEP:13360000 - Rua Tiradentes, 283 - Centro - CAPIVARI - SP','','patcapivari@sde.sp.gov.br'),
('Caraguatatuba','','','CEP:11660060 - Rua Taubate, 520 - Sumare - CARAGUATATUBA - SP','','patcaraguatatuba@sde.sp.gov.br'),
('Carapicuíba','','','CEP:06382260 - Estr. Ernestina Vieira, 149 - Vila Silviania - CARAPICUIBA - SP','','patcarapicuiba@sde.sp.gov.br'),
('Casa Branca','','','CEP:13700000 - Rua Capitão Horta, 758 - Centro - CASA BRANCA - SP','','patcasabranca@sde.sp.gov.br'),
('Catanduva','','','CEP:15800610 - AV COMENDADOR ANTONIO STOCCO, 537 - JOAQUIM LOPES - CATANDUVA - SP','','patcatanduva@sde.sp.gov.br'),
('São Paulo','','','CEP:5858001 - ESTRADA DE ITAPECERICA, 8887 - CAPAO REDONDO - SAO PAULO - SP','','patfeiticodavila@sde.sp.gov.br'),
('São Paulo','','','CEP:8131310 - RUA PADRE VIRGILIO CAMPELO, 150, Endereço:ENCOSTANORTE - CENTRO - SAO PAULO - SP','','patitaim@sde.sp.gov.br'),
('São Paulo','','','CEP:2991000 - ESTRADA DE TAIPAS, 990 - JD PANAMERICANO - SAO PAULO - SP','','patgrajau@sde.sp.gov.br'),
('Colina','','','CEP:14770000 - Av. Angelo Martins Tristão, 125 - Centro - COLINA - SP','','patcolina@sde.sp.gov.br'),
('Conchal','','','CEP:13835000 - Rua Alvaro Ribeiro, 300 - Sala 01 - Centro - CONCHAL - SP','','patconchal@sde.sp.gov.br'),
('Cordeirópolis','','','CEP:13490000 - Avenida Presidente Vargas, 663 Vila Nova Brasília - CORDEIROPOLIS - SP','','patcordeiropolis@sde.sp.gov.br'),
('Cotia','','','CEP:06716900 - Rua Jorge Caixe, 306A - Jardim Nomura - COTIA - SP','','patcotia@sde.sp.gov.br'),
('Cravinhos','','','CEP:14140000 - R DR JOSE EDUARDO VIEIRA PALMA, 52 - CENTRO - CRAVINHOS - SP','','patcravinhos@sde.sp.gov.br'),
('Cruzeiro','','','Rua Dr. Celestino 1620, Vila Canevari - Cruzeiro','','patcruzeiro@sde.sp.gov.br'),
('Cubatão','','','CEP:11510310- Avenida Doutor Fernando Costa, 1096 - Vila Couto - CUBATAO - SP','','patcubatao@sde.sp.gov.br'),
('Descalvado','','','CEP:13690000 - Rua Coronel Arthur Whitacker, 137 - Centro - DESCALVADO - SP','','patdescalvado@sde.sp.gov.br'),
('Dois Córregos','','','CEP:17300000 - Praça Prefeito Oswaldo Casonato, 305 - Centro - DOIS CORREGOS - SP','','patdoiscorregos@sde.sp.gov.br'),
('Dracena','','','CEP:17900000 - RUA MARACAJU, 1149 - CENTRO - DRACENA - SP','','patdracena@sde.sp.gov.br'),
('Embu-Guaçu','','','CEP:06900000 - Rua Dagmar Antonio Bueno, 86 - Vila Louro - EMBU-GUACU - SP','','patembu.guacu@sde.sp.gov.br'),
('Embu das Artes','','','CEP:06810240 - Av. Rotary, 3483 - Parque Industrial Ramos de Freitas - EMBU DAS ARTES - SP','','patembudasartes@sde.sp.gov.br'),
('Espírito Santo do Pinhal','','','CEP:13990000 - Avenida Quirino dos Santos, 152 - Largo São João - ESPIRITO SANTO DO PINHAL - SP','','patespinhal@sde.sp.gov.br'),
('Fartura','','','CEP:18870000 - Rua Luiz Ribeiro Salgado, 20 - Centro - FARTURA - SP','','patfartura@sde.sp.gov.br'),
('Fernandópolis','','','CEP:15600000 - Rua São Paulo - Bairro Coester - FERNANDOPOLIS - SP','','patfernandopolis@sde.sp.gov.br'),
('Ferraz de Vasconcelos','','','CEP:8527052 - RUA AMERICO TRUFELI, 60 - CONJ RES ESCRIV - FERRAZ DE VASCONCELOS - SP','','patferraz@sde.sp.gov.br'),
('Franca','','','CEP:14400710 - RUA CAMPOS SALES, 1495 - CENTRO - FRANCA - SP','','patfranca@sde.sp.gov.br'),
('Francisco Morato','','','CEP:7909150 - RUA TABATINGUERA, 45 - CENTRO - FRANCISCO MORATO - SP','','patf.morato@sde.sp.gov.br'),
('Franco da Rocha','','','CEP:7851120 - CORYPHEU DE AZEVEDO MARQUES, 63 - CENTRO - FRANCO DA ROCHA - SP','','patfrancodarocha@sde.sp.gov.br'),
('Garça','','','CEP:17400082 - Rua Barão do Rio Branco, 295 - Ferrarópolis - GARCA - SP','','patgarca@sde.sp.gov.br'),
('Gavião Peixoto','','','CEP:14813970 - ALAMEDA ESTEVO, 386 - CENTRO - GAVIAO PEIXOTO - SP','','patgaviaopeixoto@sde.sp.gov.br'),
('General Salgado','','','CEP:15300000 - Av. Plinio Ribeiro do Val, 1054 - Centro - GENERAL SALGADO - SP','','patgeneralsalgado@sde.sp.gov.br'),
('Guaíra','','','CEP:14790000 - RUA OITO, 221 - CENTRO - GUAIRA - SP','','patguaira@sde.sp.gov.br'),
('Guaratinguetá','','','CEP:14030030 - Praça Rotary, S/N - Terminal Rodoviário Quinzinho Fernandes - Box 11- GUARATINGUETA - SP','','patguaratingueta@sde.sp.gov.br'),
('Guariba','','','CEP:14840000 - RUA RUI BARBOSA,, 245 - CENTRO - GUARIBA - SP','','patguariba@sde.sp.gov.br'),
('Guarujá','','','CEP:11460002 - Av. Santos Dumont, 1586 - PAECARA - GUARUJA - SP','','patguaruja@sde.sp.gov.br'),
('Hortolândia','','','CEP:13184792- Rua Projetada 12, nº 100 - JARDIM METROPOLITAN - HORTOLANDIA - SP','','pathortolandia@sde.sp.gov.br'),
('Ibitinga','','','CEP:14940055 - Rua Tiradentes 1045 - CENTRO - IBITINGA - SP','','patibitinga@sde.sp.gov.br'),
('Ibiúna','','','CEP:18150000- Rua Raimundo Santiago nº 30 - CENTRO - IBIUNA - SP','','patibiuna@sde.sp.gov.br'),
('Ilha Solteira','','','CEP:15385000 - Avenida Atlântica - Ilha Shopping, 1659 Compl. Endereço:BOX 3 E 4 - INTERLAGOS - ILHA SOLTEIRA - SP','','patilhasolteira@sde.sp.gov.br'),
('Ilhabela','','','CEP:11630000 - Rua Prefeito Mariano Procópio de Araújo Carvalho, 100 - PEREQUÊ - ILHABELA - SP','','patilhabela@sde.sp.gov.br'),
('Indaiatuba','','','CEP:13330060 - Rua Vinte e Quatro de Maio, 1670 - CENTRO - INDAIATUBAUF - SP','','patindaiatuba@sde.sp.gov.br'),
('Iperó','','','CEP:18560000 - Rua Costa e Silva, 195 - CENTRO - IPERO - SP','','patipero@sde.sp.gov.br'),
('Iracemápolis','','','CEP:13495220- Rua Duque de Caxias, 520 - CENTRO - IRACEMAPOLIS - SP','','patiracemapolis@sde.sp.gov.br'),
('Itaí','','','CEP:18730000- Rua Sete de Setembro, 1445 - CENTRO - ITAI - SP','','patitai@sde.sp.gov.br'),
('Itajobi','','','CEP: 15840-000- Rua Pedro Toledo , 1433, Centro, Itajobi - SP','','patitajobi@sde.sp.gov.br'),
('Itanhaém','','','CEP:11740000 - Rua dos Fundadores, 565 - BELAS ARTES - ITANHAEM - SP','','patitanhaem@sde.sp.gov.br'),
('Itapecerica da Serra','','','CEP:6850840 - Rua Treze de Maio, 100 - CENTRO - ITAPECERICA DA SERRA - SP','','patitapecerica.serra@sde.sp.gov.br'),
('Itapetininga','','','CEP:18200009 - Rua Monsenhor Soares, 251 - CENTRO - ITAPETININGA - SP','','patitapetininga@sde.sp.gov.br'),
('Itapeva','','','CEP:18400340 - Rua Lucas de Camargo, 290 - CENTRO - ITAPEVA - SP','','patitapeva@sde.sp.gov.br'),
('Itapevi','','','CEP:06693005 - Rua José Michelotti, 88 - CIDADE DA SAÚDE - ITAPEVI - SP','','patitapevi@sde.sp.gov.br'),
('Itapira','','','CEP:13970070 - Avenida Rio Branco 99 - CENTRO - ITAPIRAUF - SP','','patitapira@sde.sp.gov.br'),
('Itápolis','','','CEP:14900000 - AV. FLORENCIO TERRA, 399 - CENTRO - ITAPOLIS - SP','','patitapolis@sde.sp.gov.br'),
('Itaquaquecetuba','','','CEP:8570110 - RUA DOM THOMAZ FREY, 89 - CENTRO - ITAQUAQUECETUBA - SP','','patitaquaquecetuba@sde.sp.gov.br'),
('Itararé','','','CEP:18460000 - RUA PRUDENTE DE MORAES, 1131 - CENTRO - ITARARE - SP','','patitarare@sde.sp.gov.br'),
('Itatiba','','','CEP:13256900 - AV VINTE E NOVE DE ABRIL, 35 - CENTRO - ITATIBA - SP','','patitatiba@sde.sp.gov.br'),
('Itu','','','CEP:13309640 - AVENIDA ITU 400 ANOS, 111 - ITU NOVO CENTRO - ITU - SP','','patitu@sde.sp.gov.br'),
('Itupeva','','','CEP:13295000Logradouro:RUA JULIANA DE OLIVEIRA BORGESNúmero:90Bairro:JARDIM PRIMAVERAMunicípio:ITUPEVAUF:SP','','patitupeva@sde.sp.gov.br'),
('Ituverava','','','CEP:14500000 - RUA CORONEL IRLANDINO BARBOSA SANDOVAL, 10 - CENTRO - ITUVERAVA - SP','','patituverava@sde.sp.gov.br'),
('Jaborandi','','','CEP:14775000 - RUA COLINA, 900 - CENTRO - JABORANDI - SP','','patjaborandi@sde.sp.gov.br'),
('Jaboticabal','','','CEP:14870900 - ESPLANADA DO LAGO,, 160 - VILA SERRA - JABOTICABAL - SP','','patjaboticabal@sde.sp.gov.br'),
('Jacareí','','','CEP:12308001 - RUA BARAO DE JACAREI, 839 - CENTRO - JACAREI - SP','','patjacarei@sde.sp.gov.br'),
('Jaguariúna','','','CEP:13910009 - R CORONEL AMANCIO BUENO, 810 - CENTRO - JAGUARIUNA - SP','','patjaguariuna@sde.sp.gov.br'),
('Jales','','','CEP:15700062 - R 6 -, 2163 - CENTRO - JALES - SP','','patjales@sde.sp.gov.br'),
('Jardinópolis','','','CEP:14680000 - PRACA MARIO LINS, 150 - CENTRO - JARDINOPOLIS - SP','','patjardinopolis@sde.sp.gov.br'),
('Jaú','','','CEP:17201330 - RUA PAISSANDU, 671 - CENTRO - JAU - SP','','patjau@sde.sp.gov.br'),
('José Bonifácio','','','CEP:15200000Logradouro:AVENIDA RIO BRANCONúmero:331Bairro:CENTROMunicípio:JOSE BONIFACIOUF:SP','','patjbonifacio@sde.sp.gov.br'),
('Jundiaí','','','CEP:13201800 - R ZACARIAS DE GOES, 530Compl. Endereço:SALA 6/7 - CENTRO - JUNDIAI - SP','','patjundiai@sde.sp.gov.br'),
('Juquitiba','','','CEP:6950000 - AV JUSCELINO KUBITSCHEK, 130 - CENTRO - JUQUITIBA - SP','','patjuquitiba@sde.sp.gov.br'),
('Laranjal Paulista','','','CEP:18500000 - RUA BARAO DO RIO BRANCO, 107 - CENTRO - LARANJAL PAULISTA - SP','','patlaranjalpta@sde.sp.gov.br'),
('Leme','','','CEP:6310100 - AV PRESIDENTE VARGAS, 280 - VILA CALDAS - CARAPICUIBA - SP','','patleme@sde.sp.gov.br'),
('Lençóis Paulista','','','CEP:18682970 - RUA CORONEL JOAQUIM GABRIEL, 11 - CENTRO - LENCOIS PAULISTA - SP','','patlencois.paulista@sde.sp.gov.br'),
('Limeira','','','CEP:13480083 - RUA TIRADENTES, 1366, 1366Compl. Endereço:SHOP PATIO - CENTRO - LIMEIRA - SP','','patlimeira@sde.sp.gov.br'),
('Lins','','','CEP:16400075 - RUA OLAVO BILAC, 640 - CENTRO - LINS - SP','','patlins@sde.sp.gov.br'),
('Lorena','','','CEP:12607020 - AVENIDA CAPITAO MESSIAS RIBEIRO, 211 - OLARIA - LORENA - SP','','patlorena@sde.sp.gov.br'),
('Mairinque','','','CEP:18120000 - AV FRANCIS DE ASSIS PINTO OLIVEIRA, 214 - CENTRO - MAIRINQUE - SP','','patmairinque@sde.sp.gov.br'),
('Mairiporã','','','CEP:7600000 - AVENIDA TABELIAO PASSARELI, 348 - CENTRO - MAIRIPORA - SP','','patmairipora@sde.sp.gov.br'),
('Marília','','','CEP:17501000 - AV CARLOS GOMES -, 137 - MARILIA - MARILIA - SP','','patmarilia@sde.sp.gov.br'),
('Matão','','','CEP:15990180 - AV VINTE E OITO DE AGOSTO, 651 - CENTRO - MATAO - SP','','patmatao@sde.sp.gov.br'),
('Mirassol','','','CEP:15130000 - RUA PADRE HERNERTO, 2147 - CENTRO - MIRASSOL - SP','','patmirassol@sde.sp.gov.br'),
('Mococa','','','CEP 13730250 -Rua Visconte do Rio Branco, 741 - Centro - Mococa','','patmococa@sde.sp.gov.br'),
('Mogi das Cruzes','','','CEP:8780210Logradouro:AVENIDA DOUTOR CANDIDO XAVIER DE ALMEIDA E SOUZANúmero:133Bairro:CENTROMunicípio:MOGI DAS CRUZESUF:SP','','patm.cruzes@sde.sp.gov.br'),
('Mogi Guaçu','','','CEP:13844000 - RUA SAO JOSE, 49 - VILA JULIA - MOGI GUACU - SP','','patmogiguacu@sde.sp.gov.br'),
('Mogi Mirim','','','CEP:13800010 - AVENIDA PROFESSOR ADIB CHAIB, 2250 - CENTRO - MOGI MIRIM - SP','','patmogimirim@sde.sp.gov.br'),
('Mongaguá','','','CEP:11730000 - PRACA JACOUB KOUKDJEAN - ESPACO CIDADAO, 167Compl. Endereço:3 PISO - CENTRO - MONGAGUA','','pat.mongagua@sde.sp.gov.br'),
('Monte Alto','','','CEP:15910000 - RUA SABINO CAMARGO, 604 - CENTRO - MONTE ALTO - SP','','patmontealto@sde.sp.gov.br'),
('Monte Aprazível','','','CEP:15150000 - RUA DUQUE DE CAXIAS, 520 - MONTE APRAZIVEL - MONTE APRAZIVEL - SP','','patmaprazivel@sde.sp.gov.br'),
('Monte Mor','','','CEP:13190000 - RUA SIQUEIRA CAMPOS, 65 - CENTRO - MONTE MOR - SP','','patmontemor@sde.sp.gov.br'),
('Nova Granada','','','CEP:15440000Logradouro:AVENIDA ADOLFO RODRIGUESNúmero:868Compl. Endereço:1º ANDARBairro:CENTROMunicípio:NOVA GRANADAUF:SP','','patngranada@sde.sp.gov.br'),
('Novo Horizonte','','','CEP:14960000 - RUA SETE DE SETEMBRO, 711 - CENTRO - NOVO HORIZONTE - SP','','patnovohorizonte@sde.sp.gov.br'),
('Olímpia','','','CEP:15400403 - AVENIDA HARRY GIANNECCHINI, 1691 - JARDIM TOLEDO - OLIMPIA - SP','','patolimpia@sde.sp.gov.br'),
('Orlândia','','','CEP:14620000 - AVENIDA DO CAFE, 1040 - CENTRO - ORLANDIA - SP','','patorlandia@sde.sp.gov.br'),
('Ourinhos','','','CEP:19900041 - RUA DOS EXPEDICIONÃÂÃÂRIOS, 389 - CENTRO - OURINHOS - SP','','patourinhos@sde.sp.gov.br'),
('Paraguaçu Paulista','','','CEP:19700000 - RUA XV DE NOVEMBRO, 496 - CENTRO - PARAGUACU PAULISTA - SP','','patparaguacupta@sde.sp.gov.br'),
('Piraju','','','CEP:18800000 - RUA MAJOR MARIANO, 000 - CENTRO - PIRAJU - SP','','patpiraju@sde.sp.gov.br'),
('Pirangi','','','CEP:15820000 - PIRANGI, 988 - CENTRO - PIRANGI - SP','','patpirangi@sde.sp.gov.br'),
('Pederneiras','','','CEP:17280000 - TRAVESSA ANCHIETA, 82 - CENTRO - PEDERNEIRAS - SP','','patpederneiras@sde.sp.gov.br'),
('Pedreira','','','CEP:13920000 - RUA MIGUEL SARKIS, 61 - PARQUE INDUSTRIAL - PEDREIRA - SP','','patpedreira@sde.sp.gov.br'),
('Penápolis','','','CEP:16300000 - RODOVIA SARGENTO LUCIANO ARNALDO COVOLAN - PARQUE IND., S/NCompl. Endereço:POUPA TEMPO. - CENTRO - PENAPOLIS - SP','','patpenapolis@sde.sp.gov.br'),
('Pereira Barreto','','','CEP:15370459Logradouro:PRAÇA DA BANDEIRA COMENDADOR JORGE TANAKANúmero:80Bairro:CENTROMunicípio:PEREIRA BARRETOUF:SP','','patpereirabarreto@sde.sp.gov.br'),
('Peruíbe','','','R. Jaçanã, 31 - Centro, Peruíbe - SP, 11750-000','','patperuibe@sde.sp.gov.br'),
('Piedade','','','CEP:18170000 - AV TENENTE PROCOPIO TENORIO, 26 - CENTRO - PIEDADE - SP','','patpiedade@sde.sp.gov.br'),
('Pilar do Sul','','','CEP:18185000 - AV ANTONIO LACERDA, 308 - SANTA CECILIA - PILAR DO SUL - SPTelefone','','pat.pdosul@sde.sp.gov.br'),
('Pindamonhangaba','','','CEP:12410030 - AV ALBUQUERQUE LINS, 138 - S BENEDITO - PINDAMONHANGABA','','patpindamonhangaba@sde.sp.gov.br'),
('Piquete','','','CEP:12620000 - AV ALBUQUERQUE LINS, 33 - S BENEDITO - PIQUETE - SP','','patpiquete@sde.sp.gov.br'),
('Pirassununga','','','CEP:13631002 - RUA GALICIO DEL NERO, 51 - CENTRO - PIRASSUNUNGA - SP','','patpirassununga@sde.sp.gov.br'),
('Piratininga','','','CEP:17490000 - RUA MANOEL PEDRO CARNEIRO, 100 - CENTRO - PIRATININGA - SP','','patpiratininga@sde.sp.gov.br'),
('Poá','','','CEP:8562140 - RUA 26 DE MARCO, 72, 72 - CENTRO - POA - SP','','patpoa@sde.sp.gov.br'),
('Pontal','','','CEP:14180000 - RUA GUILHERME SILVA, 316 - CENTRO - PONTAL - SP','','patpontal@sde.sp.gov.br'),
('Porto Ferreira','','','CEP:13660000 - RUA DIJALMA FORJAZ, 460 - CENTRO - PORTO FERREIRA - SP','','patportoferreira@sde.sp.gov.br'),
('Potim','','','CEP:12525000 - RUA PEDRO ANDRINI, 71 - CENTRO - POTIM - SP','','patpotim@sde.sp.gov.br'),
('Potirendaba','','','CEP:15105000 - RUA CONEGO THEODORO BÃÂÃÂA, 1455 - CENTRO - POTIRENDABA - SP','','patpotirendaba@sde.sp.gov.br'),
('Bauru','','','Rua Inconfidência, 50, quadra 04 - Centro - Bauru - SP - CEP: 17010-070','',''),
('Praia Grande','','','CEP:11719150 - AV MINISTRO MARCOS FREIRE, 6650 - VILA TUPI - PRAIA GRANDE - SP','','patp.grande@sde.sp.gov.br'),
('Presidente Epitácio','','','CEP:19470000 - RUA SAO PAULO, 545 - CENTRO - PRESIDENTE EPITACIO - SP','','pat.pepitacio@sde.sp.gov.br'),
('Presidente Prudente','','','CEP:19030030 - RUA RIO GRANDE DO SUL, 37 - VILA MARCONDES - PRESIDENTE PRUDENTE - SP','','patp.prudente@sde.sp.gov.br'),
('Presidente Venceslau','','','CEP:19400015Logradouro:TRAVESSA TENENTE OSVALDO BARBOSANúmero:42Bairro:CENTROMunicípio:PRESIDENTE VENCESLAUUF:SP','','pat.pvenceslau@sde.sp.gov.br'),
('Registro','','','CEP:11900000 - RUA CLARA GIANOTTI DE SOUZA, 1115 - REGISTRO - REGISTRO - SP','','crregistro@sde.sp.gov.br'),
('Ribeirão Pires','','','CEP:9400080 - R CAPITAO JOSE GALLO, 55 - CENTRO - RIBEIRAO PIRES - SP','','patribeiraopires@sde.sp.gov.br'),
('Ribeirão Preto','','','CEP:14091000Logradouro:AVENIDA DR. FRANCISCO JUNQUEIRANúmero:2625Bairro:CAMPOS ELÍSIOSMunicípio:RIBEIRAO PRETOUF:SP','','pat.rpreto@sde.sp.gov.br'),
('Rincão','','','CEP:14830000 - AVENIDA BARAO DO RIO BRANCO, 963 - CENTRO - RINCAO - SP','','patrincao@sde.sp.gov.br'),
('Rio Claro','','','CEP:13500391 - RUA 6, 676 - CENTRO - RIO CLARO - SP','','patrioclaro@sde.sp.gov.br'),
('Rio das Pedras','','','CEP:13390000 - RUA LACERDA FRANCO, 45 - CENTRO - RIO DAS PEDRAS - SP','','patriodaspedras@sde.sp.gov.br'),
('Rio Grande da Serra','','','CEP:9450000Logradouro:RUA DO PROGRESSONúmero:700Compl. Endereço:BLOCO DBairro:CENTROMunicípio:RIO GRANDE DA SERRAUF:SP','','patrgserra@sde.sp.gov.br'),
('Rosana','','','CEP:19273000 - AV JOSE LAURINDO, 1540 - CENTRO - ROSANA - SP','','patrosana@sde.sp.gov.br'),
('Salto de Pirapora','','','CEP:18160000 - RUA PEDRO ALEIXO DOS SANTOS, 75, 75 - CENTRO - SALTO DE PIRAPORA - SP','','pat.spirapora@sde.sp.gov.br'),
('Salto','','','CEP:13320020 - R JOSE REVEL, 270 - CENTRO - SALTO - SP','','patsalto.captacao@sde.sp.gov.br'),
('Santa Cruz do Rio Pardo','','','CEP:18900000 - RUA CATARINA ETSUCO UMEZU, 404 - SÃO JOSÉ - SANTA CRUZ DO RIO PARDO - SP','','patsanta.crpardo@sde.sp.gov.br'),
('Santa Fé do Sul','','','CEP:15775000 - RUA ONZE, SANTA FE DO SUL 1220, 1220 - CENTRO - SANTA FE DO SUL - SP','','pat.sfedosul@sde.sp.gov.br'),
('Santa Isabel','','','CEP:7500000 - PRACA FERNANDO LOPES, 32-34 - CENTRO - SANTA ISABEL - SP','','patsantaisabel@sde.sp.gov.br'),
('Santa Rita do Passa Quatro','','','CEP:13670000 - RUA DUQUE DE CAXIAS, 614 - CENTRO - SANTA RITA DO PASSA QUATRO - SP','','patsrpquatro@sde.sp.gov.br'),
('Santa Rosa de Viterbo','','','CEP:14270000 - PRACA DOUTOR GUIDO MAESTRELLO-CENTRO, 180 - VILA BARROS - SANTA ROSA DE VITERBO - SP','','pat.stviterbo@sde.sp.gov.br'),
('Santana de Parnaíba','','','CEP:6529001 - AVENIDA TENENTE MARQUES, 5720 - FAZENDINHA - SANTANA DE PARNAIBA - SP','','pats.parnaiba@sde.sp.gov.br'),
('São Caetano do Sul','','','CEP:9530000 - R MAJOR CARLOS DEL PRETE, 651 - CENTRO - SAO CAETANO DO SUL - SP','','patscaetano@sde.sp.gov.br'),
('São João da Boa Vista','','','CEP:13870009 - PRAÇA DA CATEDRAL, 07 - CENTRO - SAO JOAO DA BOA VISTA - SP','','patsjboavista@sde.sp.gov.br'),
('São José do Rio Pardo','','','CEP:13720000Logradouro:RUA JOSÃÂÃÂÃÂÃÂ ANDREOLINúmero:132Bairro:CENTROMunicípio:SAO JOSE DO RIO PARDOUF:SP','','patsjriopardo@sde.sp.gov.br'),
('São José do Rio Preto','','','CEP:15025010 - R BOA VISTA, 666 - BOA VISTA - SAO JOSE DO RIO PRETO - SP','','patsjriopreto@sde.sp.gov.br'),
('São José dos Campos','','','CEP:12210090 - PC AFONSO PENA, 175 - CENTRO - SAO JOSE DOS CAMPOS - SP','','patsjcampos@sde.sp.gov.br'),
('São Manuel','','','CEP:18650000 - RUA EPITACIO PESSOA, 689 - CENTRO - SAO MANUEL - SP','','patsaomanuel@sde.sp.gov.br'),
('São Miguel Arcanjo','','','CEP:18230000 - RUA COMENDADOR DANTE CARRARO, 819 - CENTRO - SAO MIGUEL ARCANJO - SP','','patsmarcanjo@sde.sp.gov.br'),
('São Paulo','','','CEP:1014000 - R BOA VISTA - LADO PAR, 170Compl. Endereço:01 ANDAR - CENTRO - SAO PAULO - SP','','pat.imigrante@sde.sp.gov.br'),
('São Pedro','','','CEP:13520000 - RUA MALAQUIAS GUERRA, 925 - CENTRO - SAO PEDRO - SP','','patsaopedro@sde.sp.gov.br'),
('São Roque','','','CEP:18130440 - RUA RUI BARBOSA, 693 - CENTRO SAO ROQUE - SAO ROQUE - SP','','patsaoroque@sde.sp.gov.br'),
('São Sebastião','','','CEP:11600000 - RUA JOAO CUPPERTINO DOS SANTOS, 245 - CENTRO - SAO SEBASTIAO - SP','','patsaosebastiao@sde.sp.gov.br'),
('São Vicente','','','CEP:11310201 - AV CAPITAO-MOR AGUIAR, 695 - CENTRO - SAO VICENTE - SP','','patsaovicente@sde.sp.gov.br'),
('Serra Negra','','','CEP:13930000 - RUA JOSE BONIFACIO, 283 - CENTRO - SERRA NEGRA - SP','','patserranegra@sde.sp.gov.br'),
('Serrana','','','CEP:14150000 - RUA SAO SEBASTIAO, 459 - . - SERRANA - SP','','patserrana@sde.sp.gov.br'),
('Sertãozinho','','','CEP:14160180 - RUA EPITACIO PESSOA, 1429 - CENTRO - SERTAOZINHO - SP','','patsertaozinho@sde.sp.gov.br'),
('Sumaré','','','CEP:13170310 - AV BRASILSL 65JD SEMIN?RIO, 1111 - NOVA VENEZA - SUMARE - SP','','patsumare@sde.sp.gov.br'),
('Suzano','','','CEP:8675230 - AV PAULO PORTELA, 210 - JARDIM PAULISTA - SUZANO - SP','','patsuzano@sde.sp.gov.br'),
('Taboão da Serra','','','CEP:6763080 - RUA CESARIO DAU, 535 - JD.MARIA ROSA - TABOAO DA SERRA - SP','','pattaboaodaserra@sde.sp.gov.br'),
('Taquaritinga','','','CEP:15900000 - R. MARECHAL DEODORO, 1130 - CENTRO - TAQUARITINGA - SP','','pattaquaritinga@sde.sp.gov.br'),
('Taquarituba','','','CEP:18740000 - RUA 13 DE MAIO, 560 - CENTRO - TAQUARITUBA - SP','','pattaquarituba@sde.sp.gov.br'),
('Tarumã','','','CEP:19820000Logradouro:RUA GIRASSOLNúmero:201Bairro:CENTROMunicípio:TARUMAUF:SP','','pat.taruma@sde.sp.gov.br'),
('Tatuí','','','CEP:18270340 - R TREZE DE FEVEREIRO, 396 - CENTRO - TATUI - SP','','pat.tatui@sde.sp.gov.br'),
('Taubaté','','','CEP:12020190 - PRQ DOUTOR BARBOSA DE OLIVEIRA, 0 - CENTRO - TAUBATE - SPTelefone:(012) 36323984','','pat.taubate@sde.sp.gov.br'),
('Teodoro Sampaio','','','CEP:19280000 - RUA JOSE MIGUEL CASTRO ANDRADE, 1087 - CENTRO - TEODORO SAMPAIO - SP','','pat.tsampaio@sde.sp.gov.br'),
('Tupã','','','CEP:17600260 - AV TAPUIAS, 907 - CENTRO - TUPA - SP','','pat.tupa@sde.sp.gov.br'),
('Valinhos','','','CEP:13270210 - AV DOS ESPORTES, 303 - CENTRO - VALINHOS - SP','','patvalinhos@sde.sp.gov.br'),
('Vargem Grande do Sul','','','CEP:13880000 - PRACA WASHINGTON LUIS, 643 - CENTRO - VARGEM GRANDE DO SUL - SP','','patvargemgsul@sde.sp.gov.br'),
('Vargem Grande Paulista','','','CEP:6730000 - RUA BENEDITA MACIEL DE ALMEIDA, 85 - CENTRO - VARGEM GRANDE PAULISTA - SP','','patvargemgrandepta@sde.sp.gov.br'),
('Várzea Paulista','','',' Rua João Povoa, 97, Jardim do Lar, Várzea Paulista-SP / CEP: 13220-224','','patvarzeapaulista@sde.sp.gov.br'),
('Vinhedo','','','CEP:13280000 - RUA MONTEIRO DE BARROS, 101 - CENTRO - VINHEDO - SP','','patvinhedo@sde.sp.gov.br'),
('Viradouro','','','CEP:14740000 - PRAÇA FRANCISCO BRAGA, 54 - CENTRO - VIRADOURO - SP','','patviradouro@sde.sp.gov.br'),
('Votorantim','','','CEP:18110013 - AV VEREADOR NEWTON VIEIRA SOARES, 475 - VILA ALBERTINA-CENTRO - VOTORANTIM - SP','','patvotorantim@sde.sp.gov.br'),
('Votuporanga','','','CEP:15500055 - RUA BARAO DO RIO BRANCO, 4497 - SAO JUDAS TADEO - VOTUPORANGA - SP','','patvotuporanga@sde.sp.gov.br')


        
        ");

     $wpdb->query($sql);    
}

