<?php
class CustomersController extends ControllerBase
{
    /*******************************************************************************
    * CLIENTES
    *******************************************************************************/
        
    //DT
    public function customersDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();

        //Le pedimos al modelo todos los items
        $listado = $model->getAllCustomers($session->id_tenant);

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;

        // Obtener permisos de edición
//        require_once 'models/UsersModel.php';
//        $userModel = new UsersModel();

//        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 2);
//        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//            $data['permiso_editar'] = $row['EDITAR'];
//        }

        //Titulo pagina
        $data['titulo'] = "Clientes";

        //Controller
        $data['controller'] = "customers";
        $data['action'] = "customersEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("customers_dt.php", $data);
    }

    /**
    * Get customers for ajax dynamic query
    * AJAX
    * @return json
    */
    public function ajaxCustomersDt()
    {
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();

        /*
        * Build up dynamic query
        */
        $sTable = $model->getTableName();

        $aColumns = array('a.id_customer'
                    , 'a.code_customer'
                    , 'b.id_tenant'
                    , 'a.label_customer'
                    , 'a.detail_customer');
        $sIndexColumn = "id_customer";

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

        /******************** Custom Filtering */
//        if( isset($_GET['filResponsable']) && $_GET['filResponsable'] != "")
//        {
//            if ( $sWhere == "" )
//            {
//                    $sWhere = "WHERE ";
//            }
//            else
//            {
//                    $sWhere .= " AND ";
//            }
//
//            $sWhere .= " A.TIPO LIKE '%".mysql_real_escape_string($_GET['filTipo'])."%' ";
//        }

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable a
            INNER JOIN cas_tenant b
            ON a.id_tenant = b.id_tenant
            $sWhere
            $sOrder
            $sLimit";

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
    
    //NEW
    public function customersAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/CustomersModel.php';

        //Models objects
//        $model = new ClientesModel();
//
//        //lista tipos
//        $data['lista_tipos'] = $model->getAllTiposCliente();
//        $data['lista_channels'] = $model->getAllChannels();
//        $data['lista_buyerclass'] = $model->getAllBuyerClass();
//
//        //codigo manual
//        $data['new_code'] = "";
//
        //Finalmente presentamos nuestra plantilla
        $data['titulo'] = "Nuevo Cliente";
//
//        $data['controller'] = "clientes";
//        $data['action'] = "clientesAdd";
//        $data['action_b'] = "clientesDt";
//
//        //Posible error
//        $data['error_flag'] = $this->errorMessage->getError($error_flag);

        $this->view->show("customers_new.php", $data);
    }
    
    public function customersAdd()
    {   
        $session = FR_Session::singleton();

        $label_customer = $this->utils->cleanQuery($_POST['customer_name']);
        $detail_customer = $this->utils->cleanQuery($_POST['customer_detail']);
        #$code_customer = rand(1, 100);
        #$code_customer = "c".$code_customer;
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();
        
        $result = $model->getLastCustomer($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code_customer = $values['code_customer'];
        $code_customer = (int)$code_customer + 1;
        
        //Le pedimos al modelo todos los items
        $result = $model->addNewCustomer(null, $code_customer, $session->id_tenant, $label_customer, $detail_customer);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            #$this->projectsDt(1);
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
            header("Location: ".$this->root."?controller=Customers&action=customersDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
    }
    
    public function ajaxCustomersAdd()
    {   
        $session = FR_Session::singleton();

        $label_customer = $this->utils->cleanQuery($_POST['name']);
        $detail_customer = $this->utils->cleanQuery($_POST['contact']);
        #$code_customer = rand(1, 100);
        #$code_customer = "c".$code_customer;
        
        //Incluye el modelo que corresponde
        require_once 'models/CustomersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new CustomersModel();
        
        $result = $model->getLastCustomer($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code_customer = $values['code_customer'];
        $code_customer = (int)$code_customer + 1;
        
        //Le pedimos al modelo todos los items
        $result = $model->addNewCustomer(null, $code_customer, $session->id_tenant, $label_customer, $detail_customer);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            $result = $model->getLastCustomer($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            $id_customer = $values['id_customer'];
            
            $new_customer[0] = $id_customer;
            $new_customer[1] = $label_customer;
            
            #$this->projectsDt(1);
            print json_encode($new_customer);
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            #print "No se ha podido agregar el cliente";
            return null;
        }
        else{
            #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
            #print "Ha ocurrido un error grave: ".$error[2];
            return null;
        }
        
        return true;
    }

    
    
    /*************************
    * OLD STUFF
    *************************/

    //PROCESS
    public function clientesAdd()
    {        
        $session = FR_Session::singleton();

        //Parametros login form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            $name = $this->utils->cleanQuery($_POST['name']);
            $buyerclass = $this->utils->cleanQuery($_POST['buyerclass']);
            $channel = $this->utils->cleanQuery($_POST['channel']);
            $type = $this->utils->cleanQuery($_POST['type']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->addNewCliente($code, $name, $buyerclass, $channel, $type);

            if($result->rowCount() > 0)
            {
                $this->clientesDt(1);
            }
            else
            {
                $this->clientesDt(2);
            }
        }
        else
        {
            $this->clientesDt();
        }
    }

    //SHOW
    public function clientesEditForm()
    {
        if($_POST)
        {
            $code = $this->utils->cleanQuery($_POST['cod_cliente']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $clienteObj = $model->getClienteByCode($code);
            $data['code'] = "";
            $data['name'] = "";
            $data['type'] = "";
            $data['buyerchannel'] = "";
            $data['channel'] = "";

            if($clienteVal = $clienteObj->fetch(PDO::FETCH_ASSOC))
            {
                $data['code'] = $clienteVal['COD_CLIENTE'];
                $data['name'] = $clienteVal['NOM_CLIENTE'];
                $data['type'] = $clienteVal['TIPO'];
                $data['buyerclass'] = $clienteVal['COD_BUYER_CLASS'];
                $data['channel'] = $clienteVal['COD_CHANNEL'];
            }

            $data['lista_tipos'] = $model->getAllTiposCliente();
            $data['lista_buyerclass'] = $model->getAllBuyerClass();
            $data['lista_channels'] = $model->getAllChannels();

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "clientes > EDICI&Oacute;N";

            $data['controller'] = "clientes";
            $data['action'] = "clientesEdit";
            $data['action_b'] = "clientesDt";

            $this->view->show("clientes_edit.php", $data);
        }
        else
        {
            $this->clientesDt(2);
        }
    }

    //PROCESS
    public function clientesEdit()
    {
        $session = FR_Session::singleton();

        //Parametros form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            #$name = $this->utils->cleanQuery($_POST['name']);
            $buyerclass = $this->utils->cleanQuery($_POST['buyerclass']);
            $channel = $this->utils->cleanQuery($_POST['channel']);
            $type = $this->utils->cleanQuery($_POST['type']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->editCliente($code, $buyerclass, $channel, $type);

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->clientesDt(1);
            else
                $this->clientesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
                $this->clientesDt();
        }
    }

    /*
        * Verify Customer Code
        * AJAX
        */
    public function verifyCodCliente()
    {
        if($_REQUEST['code'])
        {
            $input = mysql_real_escape_string($_REQUEST['code']);

            $sql = "SELECT cod_cliente FROM t_cliente WHERE cod_cliente = '$input'";

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';
            $model = new ClientesModel();
            $result = $model->goCustomQuery($sql);

            if($result->rowCount() > 0)
                echo "false";
            else
                echo "true";
        }
        else
            echo "false";
    }

    /*
        * Verify Customer Name
        * AJAX
        */
    public function verifyNameCliente()
    {
        if($_REQUEST['name'])
        {
            $input = mysql_real_escape_string($_REQUEST['name']);

            $sql = "SELECT nom_cliente FROM t_cliente WHERE nom_cliente = '$input'";

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';
            $model = new ClientesModel();
            $result = $model->goCustomQuery($sql);

            if($result->rowCount() > 0)
                echo "false";
            else
                echo "true";
        }
        else
            echo "false";
    }

    /*******************************************************************************
    * BUYER CLASS
    *******************************************************************************/

    public function buyerClassDt($error_flag = 0, $message = "")
    {
        //Incluye el modelo que corresponde
        require_once 'models/ClientesModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ClientesModel();

        //Le pedimos al modelo todos los items
        $listado = $model->getAllBuyerClass();

        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;

        // Obtener permisos de edición
        require_once 'models/UsersModel.php';
        $userModel = new UsersModel();

        $session = FR_Session::singleton();

        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 2);
        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
            $data['permiso_editar'] = $row['EDITAR'];
        }

        //Titulo pagina
        $data['titulo'] = "buyer class clientes";

        //Controller
        $data['controller'] = "clientes";
        $data['action'] = "buyerClassEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("clientes_buyerclass_dt.php", $data);
    }

    //SHOW
    public function buyerClassAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/ClientesModel.php';

        //Session object
        $session = FR_Session::singleton();

        //Models objects
        $model = new ClientesModel();

        //Extraer ultimo codigo de segmento existente
        $buyer_class_code = $model->getLastBuyerClass();

        if($code = $buyer_class_code->fetch(PDO::FETCH_ASSOC))
        {
            //Crear un nuevo codigo: anterior+1
            $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_BUYER_CLASS']);
            $LETRAS = preg_replace("/[0-9]/", "", $code['COD_BUYER_CLASS']);  
            $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
            $LEER = strlen($NUEVO_CODIGO);

            if($LEER > 2)
                    $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
            else
                    $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

            $data['buyer_class_code'] = $CODIGOFINAL;
        }
        else
        {
            $data['buyer_class_code'] = "BY001";
            $data['error'] = $buyer_class_code;
        }

        //Finalmente presentamos nuestra plantilla
        $data['titulo'] = "buyer class clientes > nuevo";

        $data['controller'] = "clientes";
        $data['action'] = "buyerClassAdd";
        $data['action_b'] = "buyerClassDt";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag);

        $this->view->show("clientes_buyerclass_new.php", $data);
    }

    //PROCESS
    public function buyerClassAdd()
    {        
        $session = FR_Session::singleton();

        //Parametros login form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            $name = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->addNewBuyerClass(strtoupper($code), strtoupper($name));

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->buyerClassDt(1);
            else
                $this->buyerClassDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
            $this->buyerClassDt();
        }
    }

    //SHOW
    public function buyerClassEditForm()
    {
        if($_POST)
        {
            $data['code'] = $this->utils->cleanQuery($_POST['code']);
            $data['name'] = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "buyer class clientes > EDICI&Oacute;N";

            $data['controller'] = "clientes";
            $data['action'] = "buyerClassEdit";
            $data['action_b'] = "buyerClassDt";

            $this->view->show("clientes_buyerclass_edit.php", $data);
        }
        else
        {
            $this->buyerClassDt(2);
        }
    }

    //PROCESS
    public function buyerClassEdit()
    {
        $session = FR_Session::singleton();

        //Parametros form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            $name = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->editBuyerClass($code, strtoupper($name));

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->buyerClassDt(1);
            else
                $this->buyerClassDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
                $this->buyerClassDt();
        }
    }


    /*******************************************************************************
    * CHANNEL
    *******************************************************************************/

    public function channelDt($error_flag = 0, $message = "")
    {
        //Incluye el modelo que corresponde
        require_once 'models/ClientesModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ClientesModel();

        //Le pedimos al modelo todos los items
        $listado = $model->getAllChannels();
        $data['listado'] = $listado;

        // Obtener permisos de edición
        require_once 'models/UsersModel.php';
        $userModel = new UsersModel();

        $session = FR_Session::singleton();

        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 2);
        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
            $data['permiso_editar'] = $row['EDITAR'];
        }

        //Titulo pagina
        $data['titulo'] = "channel clientes";

        //Controller
        $data['controller'] = "clientes";
        $data['action'] = "channelEditForm";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag,$message);

        //Finalmente presentamos nuestra plantilla
        $this->view->show("clientes_channel_dt.php", $data);
    }

    //SHOW
    public function channelAddForm($error_flag = 0)
    {
        //Import models
        require_once 'models/ClientesModel.php';

        //Models objects
        $model = new ClientesModel();

        //Extraer ultimo codigo de segmento existente
        $channel_code = $model->getLastChannelCode();

        if($code = $channel_code->fetch(PDO::FETCH_ASSOC))
        {
            //Crear un nuevo codigo: anterior+1
            $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_CHANNEL']);
            $LETRAS = preg_replace("/[0-9]/", "", $code['COD_CHANNEL']);  
            $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
            $LEER = strlen($NUEVO_CODIGO);

            if($LEER > 2)
                    $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
            else
                    $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

            $data['channel_code'] = $CODIGOFINAL;
        }
        else
        {
            $data['channel_code'] = "CH001";
            $data['error'] = $channel_code;
        }

        //Finalmente presentamos nuestra plantilla
        $data['titulo'] = "channel clientes > nuevo";

        $data['controller'] = "clientes";
        $data['action'] = "channelAdd";
        $data['action_b'] = "channelDt";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag);

        $this->view->show("clientes_channel_new.php", $data);
    }

    //PROCESS
    public function channelAdd()
    {        
        $session = FR_Session::singleton();

        //Parametros login form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            $name = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->addNewChannel(strtoupper($code), strtoupper($name));

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->channelDt(1);
            else
                $this->channelDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
            $this->channelDt();
        }
    }

    //SHOW
    public function channelEditForm()
    {
        if($_POST)
        {
            $data['code'] = $this->utils->cleanQuery($_POST['code']);
            $data['name'] = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "channel clientes > EDICI&Oacute;N";

            $data['controller'] = "clientes";
            $data['action'] = "channelEdit";
            $data['action_b'] = "channelDt";

            $this->view->show("clientes_channel_edit.php", $data);
        }
        else
        {
            $this->channelDt(2);
        }
    }

    //PROCESS
    public function channelEdit()
    {
        $session = FR_Session::singleton();

        //Parametros form
        if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
        {
            //Avoid resubmit
            $session->orig_timestamp = microtime(true);

            $code = $this->utils->cleanQuery($_POST['code']);
            $name = $this->utils->cleanQuery($_POST['name']);

            //Incluye el modelo que corresponde
            require_once 'models/ClientesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ClientesModel();

            //Le pedimos al modelo todos los items
            $result = $model->editChannel($code, strtoupper($name));

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->channelDt(1);
            else
                $this->channelDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
        }
        else
        {
                $this->channelDt();
        }
    }
}
?>