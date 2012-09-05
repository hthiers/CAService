<?php
class AdminController extends ControllerBase
{
    /*******************************************************************************
    * ADMINISTRATION MODULE CONTROLLER
    *******************************************************************************/

    //SHOW EVENTS PAGE
    public function eventsDt($error_flag = 0, $message = "")
    {
            //Incluye el modelo que corresponde
            //require_once 'models/AdminModel.php';

            //Creamos una instancia de nuestro "modelo"
            //$model = new AdminModel();

            //Le pedimos al modelo todos los items
            //$listado = $model->getAllEvents();

            //Pasamos a la vista toda la información que se desea representar
            //$data['listado'] = $listado;

            //Titulo pagina
            $data['titulo'] = "ADMINISTRACI&Oacute;N > HISTORIAL DE EVENTOS";

            $data['controller'] = "admin";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

            //Finalmente presentamos nuestra plantilla
            $this->view->show("admin_events_dt.php", $data);
    }

    /**
        * Carga de historial de eventos por sql dinamico para datatable
        * AJAX
        * @return json
        */
    public function ajaxEventsDt()
    {
        //Incluye el modelo que corresponde
        require_once 'models/AdminModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new AdminModel();


        /*
        * Build up dynamic query
        */

        $sTable = $model->getTableName();
        $aColumns = $model->getTableColumnNames();
        $sIndexColumn = "ID";

        /******************** Paging */
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".mysql_real_escape_string( $_GET['iDisplayLength'] );

        /******************** Ordering */
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) )
        {
                $sOrder = "ORDER BY  ";
                for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
                {
                        if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                        {
                                $sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
                                        mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
                        }
                }

                $sOrder = substr_replace( $sOrder, "", -2 );
                if ( $sOrder == "ORDER BY" )
                {
                        $sOrder = "";
                }
        }

        /******************** Filtering */
        $sWhere = "";

        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            }

            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /********************* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }

                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
            }
        }

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable
            $sWhere
            $sOrder
            $sLimit
            ";

        $result_data = $model->goCustomQuery($sql);

        $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");

        $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable");

        /*
        * Output
        */
        $iTotal = $total_rows->fetch(PDO::FETCH_NUM);
        $iTotal = $iTotal[0];

        $iFilteredTotal = $found_rows->fetch(PDO::FETCH_NUM);
        $iFilteredTotal = $iFilteredTotal[0];

        $output = array(
                "sEcho" => intval($_GET['sEcho']),
                "iTotalRecords" => $iTotal,
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
        );

        $k = 1;
        while($aRow = $result_data->fetch(PDO::FETCH_NUM))
        {
                $row = array();

                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $row[] = utf8_encode($aRow[ $i ]);
                }

                $output['aaData'][] = $row;

                $k++;
        }

        echo json_encode( $output );
    }

    public function usersDt($error_flag = 0, $message = "")
    {
        //Incluye el modelo que corresponde
        require_once 'models/UsersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new UsersModel();

        //Le pedimos al modelo todos los items
        $resultado = $model->getAllUserAccounts();

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $resultado;

        //Titulo pagina
        $data['titulo'] = "ADMINISTRACI&Oacute;N > USUARIOS";

        $data['controller'] = "admin";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("admin_users_dt.php", $data);
    }

    public function userEditForm()
    {            
        if($_POST || $_GET)
        {
            if(isset($_POST['id_user']))
                $id_user = $this->utils->cleanQuery($_POST['id_user']);
            else
                $id_user = $this->utils->cleanQuery($_GET['id_user']);

            require_once 'models/AdminModel.php';
            require_once 'models/UsersModel.php';
            $model = new AdminModel();
            $modelUser = new UsersModel();

            //Get user data
            $pdoUser = $modelUser->getUserAccountByID($id_user);

            //Get to form only if user extraction is done
            if($resultUser = $pdoUser->fetch(PDO::FETCH_ASSOC))
            {
                $session = FR_Session::singleton();

                $data['id_user'] = $resultUser['ID'];
                $data['name_user'] = $resultUser['NOMBRE'];
                $data['nick_user'] = $resultUser['USUARIO'];
                $data['apellidop_user'] = $resultUser['APELLIDOP'];
                $data['apellidom_user'] = $resultUser['APELLIDOM'];
                $data['pass_user'] = $resultUser['CLAVE'];
                $data['priv_user'] = $resultUser['PRIVILEGIO'];

                $data['lista_privs'] = $model->getAllPrivileges();

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "administraci&Oacute;n > edicion usuario";

                //Controller
                $data['controller'] = "admin";
                //Action edit
                $data['action'] = "userEdit";
                //Action back
                $data['action_b'] = "usersDt";
                
                $data['error_flag'] = "";

                $this->view->show("admin_users_edit.php", $data);
            }
            else
                $this->usersDt(10,"Error: No se ha logrado ubicar la cuenta.");
        }
        else
            $this->usersDt(10,"Ha ocurrido un error grave!");
    }

    public function userEdit()
    {
        $session = FR_Session::singleton();

        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $id_user = $this->utils->cleanQuery($_POST['id_user']);
            $nick_user = $this->utils->cleanQuery($_POST['nick_user']);
            $name_user = $this->utils->cleanQuery($_POST['name_user']);
            $apellidop_user = $this->utils->cleanQuery($_POST['apellidop_user']);
            $apellidom_user = $this->utils->cleanQuery($_POST['apellidom_user']);
            $priv_user = $this->utils->cleanQuery($_POST['cbo_priv']);
            $password_nuevo_a = $this->utils->cleanQuery($_POST['password_nuevo_a']);
            $password_nuevo_b = $this->utils->cleanQuery($_POST['password_nuevo_b']);
            
            //Incluye el modelo que corresponde
            require_once 'models/UsersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new UsersModel();

            if($password_nuevo_a == $password_nuevo_b)
            {
                $result = $model->editUserAccount($id_user, $nick_user, $name_user, $apellidop_user, $apellidom_user, $priv_user, $password_nuevo_a);

                $error = $result->errorInfo();
                
                if($error[0] == '00000')
                    $this->usersDt(11,"El usuario ha sido modificado con &eacute;xito");
                else
                    $this->usersDt(10,"Ha ocurrido un error! <i>".$error[2]."</i>");
            }
            else
                $this->usersDt(5);
        }
        else
        {

            $this->usersDt();
        }
    }

    public function userAddForm($error_flag = 0)
    {
        $session = FR_Session::singleton();

        //Finalmente presentamos nuestra plantilla
        $data['titulo'] = "Administraci&Oacute;n > nuevo usuario";

        //Controller
        $data['controller'] = "admin";
        //Action edit
        $data['action'] = "userAdd";
        //Action back
        $data['action_b'] = "usersDt";

        require_once 'models/AdminModel.php';
        $model = new AdminModel();
        $data['lista_privs'] = $model->getAllPrivileges();
        
        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag);

        $this->view->show("admin_users_new.php", $data);
    }

    public function userAdd()
    {
        $session = FR_Session::singleton();

        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $name_user = $_POST['name_user'];
            $apellidop_user = $_POST['apellidop_user'];
            $apellidom_user = $_POST['apellidom_user'];
            $nick_user = $_POST['nick_user'];
            $password_a = $_POST['password_a'];
            $password_b = $_POST['password_b'];
            $privi_user = $_POST['privi_user'];

            if($password_a == $password_b)
            {
                //Incluye el modelo que corresponde
                require_once 'models/UsersModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new UsersModel();

                $result = $model->addNewUser($name_user, $apellidop_user, $apellidom_user, $nick_user, $password_b, $privi_user);

                if($result->rowCount() > 0)
                {
                        $this->usersDt(1);
                }
                else
                {
                        //error general
                        $this->userAddForm(2);
                }
            }
            else
            {
                //error password no coinciden
                $this->userAddForm(5);
            }
        }
        else
        {
            //access error (relocate)
            $this->usersDt();
        }
    }

    public function userDelete()
    {
        $session = FR_Session::singleton();
        $id_user = $_GET['id_user'];
        $orig_timestamp = $_GET['form_timestamp'];

        if(strval($orig_timestamp) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            //Incluye el modelo que corresponde
            require_once 'models/UsersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new UsersModel();

            $result = $model->deleteUserAccount($id_user);

            if($result->errorCode() == '00000')
                $this->usersDt(11,"Usuario eliminado");
            else
                $this->usersDt(10,"Ha ocurrido un error: ".$result->errorInfo());
        }
        else
        {
            $this->usersDt();
        }
    }

    public function privilegiosPanel($error_flag = 0, $message = "", $preset_privi = 1)
    {
        //Titulo pagina
        $data['titulo'] = "ADMINISTRACI&Oacute;N > PERFILES";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        //Incluye el modelo que corresponde
        require_once 'models/AdminModel.php';
        $model = new AdminModel();
        
        $data['lista_privilegios'] = $model->getAllPrivileges();
        
        if(isset($_GET['privilegio'])){
            $data['lista_permisos'] = $model->getAllPrivilegePermissionsByModuleStatus($_GET['privilegio'], 1);
            $data['default_privilegio'] = $_GET['privilegio'];
        }
        else{
            $data['lista_permisos'] = $model->getAllPrivilegePermissionsByModuleStatus($preset_privi, 1);
            $data['default_privilegio'] = $preset_privi;
        }
        
        $data['controller'] = "admin";
        $data['action'] = "privilegiosChange";
        
        //Finalmente presentamos nuestra plantilla
        $this->view->show("admin_privileges.php", $data);
    }
    
    public function privilegiosAddForm()
    {
        //Finalmente presentamos nuestra plantilla
        $data['titulo'] = "Administraci&Oacute;n > nuevo perfil";
        
        //Controller
        $data['controller'] = "admin";
        //Action edit
        $data['action'] = "privilegiosAdd";
        //Action back
        $data['action_b'] = "privilegiosPanel";

        require_once 'models/AdminModel.php';
        $model = new AdminModel();
        
        $data['lista_modulos'] = $model->getAllModulesByStatus(1);
        
        $result = $model->getLastPrivilegeCode();
        
        if($code = $result->fetch(PDO::FETCH_ASSOC))
        {
            //Crear un nuevo codigo: actual+1
            $NUEVO_CODIGO = (int)$code['COD_PRIVILEGIO'] + 1;
            $data['new_code'] = $NUEVO_CODIGO;
        }
        else
            $data['new_code'] = "2";

        //Posible error
        #$data['error_flag'] = $this->errorMessage->getError($error_flag);

        $this->view->show("admin_privileges_new.php", $data);
    }

    public function privilegiosAdd()
    {
        $session = FR_Session::singleton();

        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $cod_privilegio = $_POST['cod_privilegio'];
            $name_privilegio = $_POST['name_privilegio'];

            require_once 'models/AdminModel.php';
            $model = new AdminModel();
            $result = $model->addNewPrivilege($cod_privilegio, $name_privilegio);

            $error = $result->errorInfo();
            /*
             * Continuar aplicando permisos de acceso 
             * solo si el privilegio se agrego correctamente
             */
            if($error[0] == 00000){
                $ver_tienda = 0;
                $write_tienda = 0;
                $edit_tienda = 0;
                $ver_cliente = 0;
                $write_cliente = 0;
                $edit_cliente = 0;
                $ver_modelos = 0;
                $write_modelos = 0;
                $edit_modelos = 0;
                $ver_segment = 0;
                $write_segment = 0;
                $edit_segment = 0;
                $ver_region = 0;
                $write_region = 0;
                $edit_region = 0;
                $ver_brand = 0;
                $write_brand = 0;
                $edit_brand = 0;
                $ver_bu = 0;
                $write_bu = 0;
                $edit_bu = 0;
                
                $exportar = 0;

                if(isset($_POST['chkver_tienda']))
                    $ver_tienda = 1;
                if(isset($_POST['chkescribir_tienda']))
                    $write_tienda = 1;
                if(isset($_POST['chkeditar_tienda']))
                    $edit_tienda = 1;
                if(isset($_POST['chkver_cliente']))
                    $ver_cliente = 1;
                if(isset($_POST['chkescribir_cliente']))
                    $write_cliente = 1;
                if(isset($_POST['chkeditar_cliente']))
                    $edit_cliente = 1;
                if(isset($_POST['chkver_modelos']))
                    $ver_modelos = 1;
                if(isset($_POST['chkescribir_modelos']))
                    $write_modelos = 1;
                if(isset($_POST['chkeditar_modelos']))
                    $edit_modelos = 1;
                if(isset($_POST['chkver_segment']))
                    $ver_segment = 1;
                if(isset($_POST['chkescribir_segment']))
                    $write_segment = 1;
                if(isset($_POST['chkeditar_segment']))
                    $edit_segment = 1;
                if(isset($_POST['chkver_region']))
                    $ver_region = 1;
                if(isset($_POST['chkescribir_region']))
                    $write_region = 1;
                if(isset($_POST['chkeditar_region']))
                    $edit_region = 1;
                if(isset($_POST['chkver_brand']))
                    $ver_brand = 1;
                if(isset($_POST['chkescribir_brand']))
                    $write_brand = 1;
                if(isset($_POST['chkeditar_brand']))
                    $edit_brand = 1;
                if(isset($_POST['chkver_bu']))
                    $ver_bu = 1;
                if(isset($_POST['chkescribir_bu']))
                    $write_bu = 1;
                if(isset($_POST['chkeditar_bu']))
                    $edit_bu = 1;
                
                if(isset($_POST['chk_exportar']))
                    $exportar = 1;

                $user_type = $_POST['radio_menu'];

                // Update permisos de privilegios
                $model->updatePrivilegePermission($cod_privilegio, 1, $ver_tienda, $write_tienda, $edit_tienda);
                $model->updatePrivilegePermission($cod_privilegio, 2, $ver_cliente, $write_cliente, $edit_cliente);
                $model->updatePrivilegePermission($cod_privilegio, 4, $ver_modelos, $write_modelos, $edit_modelos);
                $model->updatePrivilegePermission($cod_privilegio, 7, $ver_segment, $write_segment, $edit_segment);
                $model->updatePrivilegePermission($cod_privilegio, 3, $ver_region, $write_region, $edit_region);
                $model->updatePrivilegePermission($cod_privilegio, 5, $ver_brand, $write_brand, $edit_brand);
                $model->updatePrivilegePermission($cod_privilegio, 6, $ver_bu, $write_bu, $edit_bu);
                $model->updatePrivilegePermission($cod_privilegio, 12, $exportar, $exportar, $exportar);
                
                // Update permisos del tipo de usuario
                $result = $model->updatePrivilegePermission($cod_privilegio, $user_type, 1, 1, 1);
                
                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->privilegiosPanel(11,"Cambios guardados en forma exitosa.",$cod_privilegio);
                else
                    $this->privilegiosPanel(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
                $this->privilegiosPanel(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
            //access error (relocate)
            $this->privilegiosPanel();
        }
    }
    
    public function privilegiosChange()
    {
        $session = FR_Session::singleton();
        $orig_timestamp = $_POST['form_timestamp'];
        
        if(strval($orig_timestamp) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);
            
            if(isset($_POST['cod_privilegio']))
            {
                $privi = $_POST['cod_privilegio'];

                #$modulos_activados = array();

                $ver_tienda = 0;
                $write_tienda = 0;
                $edit_tienda = 0;
                $ver_cliente = 0;
                $write_cliente = 0;
                $edit_cliente = 0;
                $ver_modelos = 0;
                $write_modelos = 0;
                $edit_modelos = 0;
                $ver_segment = 0;
                $write_segment = 0;
                $edit_segment = 0;
                $ver_region = 0;
                $write_region = 0;
                $edit_region = 0;
                $ver_brand = 0;
                $write_brand = 0;
                $edit_brand = 0;
                $ver_bu = 0;
                $write_bu = 0;
                $edit_bu = 0;
                $exportar = 0;

                if(isset($_POST['chkver_tienda']))
                    $ver_tienda = 1;
                if(isset($_POST['chkescribir_tienda']))
                    $write_tienda = 1;
                if(isset($_POST['chkeditar_tienda']))
                    $edit_tienda = 1;
                if(isset($_POST['chkver_cliente']))
                    $ver_cliente = 1;
                if(isset($_POST['chkescribir_cliente']))
                    $write_cliente = 1;
                if(isset($_POST['chkeditar_cliente']))
                    $edit_cliente = 1;
                if(isset($_POST['chkver_modelos']))
                    $ver_modelos = 1;
                if(isset($_POST['chkescribir_modelos']))
                    $write_modelos = 1;
                if(isset($_POST['chkeditar_modelos']))
                    $edit_modelos = 1;
                if(isset($_POST['chkver_segment']))
                    $ver_segment = 1;
                if(isset($_POST['chkescribir_segment']))
                    $write_segment = 1;
                if(isset($_POST['chkeditar_segment']))
                    $edit_segment = 1;
                if(isset($_POST['chkver_region']))
                    $ver_region = 1;
                if(isset($_POST['chkescribir_region']))
                    $write_region = 1;
                if(isset($_POST['chkeditar_region']))
                    $edit_region = 1;
                if(isset($_POST['chkver_brand']))
                    $ver_brand = 1;
                if(isset($_POST['chkescribir_brand']))
                    $write_brand = 1;
                if(isset($_POST['chkeditar_brand']))
                    $edit_brand = 1;
                if(isset($_POST['chkver_bu']))
                    $ver_bu = 1;
                if(isset($_POST['chkescribir_bu']))
                    $write_bu = 1;
                if(isset($_POST['chkeditar_bu']))
                    $edit_bu = 1;
                
                if(isset($_POST['chk_exportar']))
                    $exportar = 1;

                //Incluye el modelo que corresponde
                require_once 'models/AdminModel.php';
                $model = new AdminModel();
                $model->updatePrivilegePermission($privi, 1, $ver_tienda, $write_tienda, $edit_tienda);
                $model->updatePrivilegePermission($privi, 2, $ver_cliente, $write_cliente, $edit_cliente);
                $model->updatePrivilegePermission($privi, 4, $ver_modelos, $write_modelos, $edit_modelos);
                $model->updatePrivilegePermission($privi, 7, $ver_segment, $write_segment, $edit_segment);
                $model->updatePrivilegePermission($privi, 3, $ver_region, $write_region, $edit_region);
                $model->updatePrivilegePermission($privi, 5, $ver_brand, $write_brand, $edit_brand);
                $model->updatePrivilegePermission($privi, 6, $ver_bu, $write_bu, $edit_bu);
                $model->updatePrivilegePermission($privi, 12, $exportar, $exportar, $exportar);

                $this->privilegiosPanel(11,"Cambios guardados en forma exitosa.",$privi);
            }
        }
        else
            $this->privilegiosPanel();
    }
    
    /*
    * Verify Segment Name (+ Sub & Micro)
    * AJAX
    */
    public function verifyNamePrivilege()
    {
        if($_REQUEST['name_privilegio'])
        {
            $input = mysql_real_escape_string($_REQUEST['name_privilegio']);

            $sql = "SELECT NAME_PRIVILEGIO FROM t_privilegios WHERE NAME_PRIVILEGIO = '$input'";

            require_once 'models/AdminModel.php';
            $model = new AdminModel();
            $result = $model->goCustomQuery($sql);

            if($result->rowCount() > 0)
                echo "false";
            else
                echo "true";
        }
        else
            echo "false";
    }
}
?>