<?php 
function table_creator()
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


function da_display_esm_menu()
{

    add_menu_page('PAT Unidades', 'PAT Unidades', 'manage_options', 'emp-list', 'da_PAT_list_callback');
    add_submenu_page('emp-list', 'Employee List', 'Employee List', 'manage_options', 'emp-list', 'da_PAT_list_callback');
    add_submenu_page(null, 'Update Employee', 'Update Employee', 'manage_options', 'update-emp', 'da_emp_update_call');
    add_submenu_page(null, 'Delete Employee', 'Delete Employee', 'manage_options', 'delete-emp', 'da_emp_delete_call');
    add_submenu_page('emp-list', 'Employee List Shortcode', 'Employee List Shortcode', 'edit_others_posts', 'emp-shotcode', 'da_emp_shortcode_call');

}

function da_emp_shortcode_call()
{ ?>

    <p>
        <label>Shortcode</label>
        <input type="text" value="[employee_list]">
    </p>
<?php }



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
            $msg = "Saved Successfully";
        } else {
            $msg = "Failed to save data";
        }
    }

    ?>
    <div class="content-pat">

        <h1 class="title">PAT - Unidades</h1><br>
        <h4 id="msg"><?php echo $msg; ?></h4>
        <form method="post">

            <p>
                <label>Local</label>
                <input type="text" name="local" placeholder="Local" required>

            </p>

            <p>
                <label>Endereço</label>
                <input type="text" name="endereco" placeholder="Endereço" required>

            </p>
            <p>
                <label>Municipio</label>
                <input type="text" name="municipio" placeholder="Município" required>
            </p>
            <p>
                <label>Telefone</label>
                <input type="text" name="telefone" placeholder="Telefone" required>
            </p>
            <p>
                <label>CEP</label>
                <input type="text" name="cep" placeholder="cep" required>
            </p>

            <p>
                <button type="submit" name="submit">Cadastrar</button>
            </p>
        </form>
    </div>
    <?php 
    // lista de registro
    $table_name = $wpdb->prefix . 'PAT';
    $employee_list = $wpdb->get_results($wpdb->prepare("select * FROM $table_name", ""), ARRAY_A);
    if (count($employee_list) > 0): ?>        
        <div style="margin-top: 40px;">
            <table border="1" cellpadding="10" width="90%">
                <tr>
                    <th>ID</th>
                    <th>Local</th>
                    <th>Endereço</th>
                    <th>Municipio</th>
                    <th>CEP</th>
                    <th>Telefone</th>
                    <?php if (is_admin()): ?>
                        <th>Ação</th>
                    <?php endif; ?>
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

                        <?php if (is_admin()): ?>
                            <td>
                                <a href="admin.php?page=update-emp&id=<?php echo $employee['id']; ?>">Editar</a>
                                <a href="admin.php?page=delete-emp&id=<?php echo $employee['id']; ?>">Deletar</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
    <?php else:echo "<h2>Não há Informação</h2>";endif;
}



function da_emp_update_call()
{
    global $wpdb;
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
            echo '<a href="">Voltar para a lista</a>';
        }
    }
    $employee_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name where id = %d", $id), ARRAY_A); ?>
    <h4><?php echo $msg; ?></h4>
    <form method="post">
        <p>
            <label>Local</label>
            <input type="text" name="local" placeholder="Local" value="<?php echo $employee_details['local']; ?>"
                   required>
        </p>

        <p>
            <label>Endereço</label>
            <input type="text" name="endereco" placeholder="Endereço"
                   value="<?php echo $employee_details['endereco']; ?>" required>
        </p>
        <p>
            <label>Município</label>
            <input type="text" name="municipio" placeholder="Município"
                   value="<?php echo $employee_details['municipio']; ?>" required>
        </p>
        <p>
            <label>Telefone</label>
            <input type="text" name="telefone" placeholder="Telefone"
                   value="<?php echo $employee_details['telefone']; ?>" required>
        </p>
        <p>
            <label>CEP</label>
            <input type="text" name="cep" placeholder="CEP"
                   value="<?php echo $employee_details['cep']; ?>" required>
        </p>
        <p>
            <button type="submit" name="update">Atualize</button>
        </p>
    </form>
<?php }


function da_emp_delete_call()
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
            location.href = "<?php echo site_url(); ?>/wp-admin/admin.php?page=emp-list";
        </script>
    <?php } ?>
    <form method="post">
        <p>
            <label>Você realmente apagar?</label><br>
            <input type="radio" name="conf" value="yes">Sim
            <input type="radio" name="conf" value="no" checked>Não
        </p>
        <p>
            <button type="submit" name="delete">Apagar</button>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </p>
    </form>

<?php }