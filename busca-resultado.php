<?php
 
global $wpdb;
require_once('../../../wp-config.php');
if(isset($_POST['input'])){
    $input = $_POST['input'];
    $query = $wpdb->get_results("SELECT * FROM wp_pat WHERE 
        local LIKE '%{$input}%' OR 
        endereco LIKE '%{$input}%' OR
        municipio LIKE '%{$input}%' OR 
        cep LIKE '%{$input}%' OR 
        telefone LIKE '%{$input}%'
        LIMIT 5
    ");

    if($query > 0){
        
        ?>
        <table class="table table-bordered table-striped mt-4" border="1" cellpadding="10" width="90%">
            <p>resultado da busca</p>
            <thead>
                <tr>
                    <th>Local</th>  
                    <th>Local</th>  
                    <th>Endereço</th>
                    <th>Município</th>
                    <th>CEP</th>
                    <th>Telefone</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($query as $row){?>
                <tr>
                    <td><?php echo $row->id;?></td>
                    <td><?php echo $row->local;?></td>
                    <td><?php echo $row->endereco;?></td>
                    <td><?php echo $row->municipio;?></td>
                    <td><?php echo $row->cep;?></td>
                    <td><?php echo $row->telefone;?></td>
                        <td>
                            <a href="admin.php?page=update-pat&id=<?php echo $row->id;?>">Editar</a>
                            <a href="admin.php?page=delete-pat&id=<?php echo $row->id;?>">Deletar</a>
                        </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    <?php
    }else{
        echo "<h6 class='text-danger text-center mt-3'>Não foi encontrado informações</h6>";
    }
}
