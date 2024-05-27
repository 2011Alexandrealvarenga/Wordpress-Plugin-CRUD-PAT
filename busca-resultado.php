<?php
 
global $wpdb;
require_once('../../../wp-config.php');
if(isset($_POST['input'])){
    $input = $_POST['input'];
    $query = $wpdb->get_results("SELECT * FROM wp_pat WHERE 
        municipios LIKE '%{$input}%' OR 
        centro_regional LIKE '%{$input}%' OR
        -- func_responsavel LIKE '%{$input}%' OR 
        endereco LIKE '%{$input}%' OR 
        -- telefone LIKE '%{$input}%' OR 
        email LIKE '%{$input}%'
        LIMIT 5
    ");

    if($query > 0){
        
        ?>
        <div class="content-pat">

            <table class="table table-bordered table-striped mt-4" border="1" cellpadding="10" width="90%">
                <thead>
                    <tr>
                        <th>ID</th>  
                        <th>Municípios</th>  
                        <th>Centro Regional</th>
                        <!-- <th>Func Responsável</th> -->
                        <th>Endereço</th>
                        <!-- <th>Telefone</th> -->
                        <th>E-mail</th>
                        <th>Editar</th>
                        <th>Deletar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($query as $row){?>
                    <tr>
                        <td><?php echo $row->id;?></td>
                        <td><?php echo $row->municipios;?></td>
                        <td><?php echo $row->centro_regional;?></td>
                        <!-- <td><?php //echo $row->func_responsavel;?></td> -->
                        <td><?php echo $row->endereco;?></td>
                        <!-- <td><?php //echo $row->telefone;?></td> -->
                        <td><?php echo $row->email;?></td>
                        <td><a href="admin.php?page=update-pat&id=<?php echo $row->id;?>" class="btn-editar">EDITAR</a></td>
                        <td><a href="admin.php?page=delete-pat&id=<?php echo $row->id;?>" class="btn-deletar">DELETAR</a></td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    <?php
    }else{
        echo "<h6 class='text-danger text-center mt-3'>Não foi encontrado informações</h6>";
    }
}
