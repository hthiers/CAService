<?php
class TasksController extends ControllerBase
{
    /***************************************************************************
    * PROJECTS
    ***************************************************************************/

    /**
     * Show projects dt
     * @param type $error_flag
     * @param type $message 
     */
    public function tasksDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();
        
        #support global messages
        if(isset($_GET['error_flag']))
            $error_flag = $_GET['error_flag'];
        if(isset($_GET['message']))
            $message = $_GET['message'];
        
        //Incluye el modelo que corresponde
        require_once 'models/ProjectsModel.php';
        require_once 'models/UsersModel.php';
        require_once 'models/TasksModel.php';

        //Creamos una instancia de nuestro "modelo"
        $projectsModel = new ProjectsModel();
        $userModel = new UsersModel();
        $taskModel = new TasksModel();

        //Le pedimos al modelo todos los items
        $pdo = $taskModel->getAllTasksByTenant($session->id_tenant);

        // Obtener permisos de edición
//        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
//        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//            $data['permiso_editar'] = $row['EDITAR'];
//        }
        
        # dates
        $arrayDates = Utils::getMonths();
        $data['arrayDates'] = $arrayDates;
        
        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $pdo;

        //Titulo pagina
        $data['titulo'] = "Lista de Tareas";

        $data['controller'] = "tasks";
        $data['action'] = "tasksView";
//        $data['action_b'] = "trabajosDt";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        $this->view->show("tasks_dt.php", $data);
    }
    
    public function ajaxTasksDt()
    {
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        /*
        * Building dynamic query
        */
        #$sTable = $model->getTableName();
        $sTable = "cas_task";

        $aColumns = array('a.label_task'
                    , 'a.desc_task'
                    , 'a.date_ini'
                    , 'a.date_end'
                    , 'a.time_total'
                    , 'b.id_project'
                    , 'a.id_tenant');

        $sIndexColumn = "code_task";
        $aTotalColumns = count($aColumns);

        /******************** Paging */
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];

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
                                        $_GET['sSortDir_'.$i].", ";
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
                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
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

                $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
            }
        }

        /******************** Custom Filtering */
//        if( isset($_GET['filCliente']) && $_GET['filCliente'] != "")
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
//            $sWhere .= " e.id_customer = '".$_GET['filCliente']."' ";
//        }
        if( isset($_GET['filMes']) && $_GET['filMes'] != "")
        {
            if ( $sWhere == "" )
            {
                    $sWhere = "WHERE ";
            }
            else
            {
                    $sWhere .= " AND ";
            }

            $sWhere .= " MONTH(a.date_ini) = '".$_GET['filMes']."' ";
        }
//        if( isset($_GET['filEstado']) && $_GET['filEstado'] != "")
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
//            $sWhere .= " a.status_project = '".$_GET['filEstado']."' ";
//        }
        
        # TENANT
        if ( $sWhere == "" )
        {
                $sWhere = "WHERE a.id_tenant = ".$session->id_tenant;
        }
        else
        {
                $sWhere .= " AND a.id_tenant = ".$session->id_tenant;
        }
        
        # PATCH
//        unset($aColumns[5]);    // replace column by group
//        $aColumns[5] = "IFNULL(a.time_total/3600, '') AS time_total";

        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable a
            LEFT OUTER JOIN cas_project_has_cas_task b
            ON a.id_task = b.cas_task_id_task
            $sWhere
            $sOrder
            $sLimit";

        #print($sql);

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

            for($i=0;$i<$aTotalColumns;$i++)
            {
                // FORCE UTF8
                #$row[] = utf8_encode($aRow[ $i ]);
                $row[] = $aRow[$i];
            }

            $output['aaData'][] = $row;

            $k++;
        }

        echo json_encode($output);
    }
    
    public function ajaxTasksList()
    {
        $id_project = $_GET['id_project'];
        
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $taskModel = new TasksModel();

        $pdo = $taskModel->getAllTasksByTenantProject($session->id_tenant, $id_project);
//        $result = $pdo->fetchAll(PDO::FETCH_ASSOC);
        
        
        if($pdo->rowCount() > 0)
            echo json_encode($pdo->fetchAll(PDO::FETCH_ASSOC));
        else
            return false;
    }

    /**
     * show project info 
     */
    public function TasksView()
    {
        $session = FR_Session::singleton();

        $id_task = $_POST['id_task'];
        $session->id_task = $id_task;

        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        $pdo = $model->getTaskById($session->id_tenant, $id_task);
        
        $values = $pdoProject->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            #time
            if($values['time_total'] != null){
                $time_s = round($values['time_total'], 2);
                $time_m = round((float)$time_s / 60, 2);
                $time_h = round((float)$time_m / 60, 2);
                $data['time_s'] = $time_s;
                $data['time_m'] = $time_m;
                $data['time_h'] = $time_h;
            }
            
            #current time
            $now = date("Y-m-d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");
            $data['currentTime'] = $current_date;
            
            #current progress
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S', false);
            $data['total_progress'] = $total_progress;
            
            #data
            $data['id_task'] = $values['id_task'];
            $data['code_task'] = $values['code_task'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_task'] = $values['label_task'];
            $data['date_ini'] = $values['date_ini'];
            $data['date_end'] = $values['date_end'];
            $data['time_total'] = $values['time_total'];
            $data['desc_task'] = $values['desc_task'];
        }

        $data['titulo'] = "Tarea #";
        $data['pdo'] = $pdo;

        $this->view->show("tasks_view.php", $data);
    }

    /*
     * Show new project form 
     */
    public function TasksNewForm(){
        $session = FR_Session::singleton();

        require_once 'models/ProjectsModel.php';
        require_once 'models/UsersModel.php';
        require_once 'models/CustomersModel.php';

        $model = new ProjectsModel();
        $modelUser = new UsersModel();
        $modelCustomer = new CustomersModel();

        $pdo = $model->getLastProject($session->id_tenant);
        $error = $pdo->errorInfo();
        $value = null;
        $value = $pdo->fetch(PDO::FETCH_ASSOC);

        if($error[0] != 00000){
            $new_code = 1;
            $data['error'] = "ERROR: ".$error[2];
        }
        elseif($value != null)
        {
            $last_code = $value['code_project'];
            $new_code = (int) $last_code + 1;
        }
        else
        {
            $new_code = 1;
            $data['error'] = "NO PROJECTS";
        }

        $data['new_code'] = $new_code;
        $data['titulo'] = "Nuevo Trabajo #".$new_code;

        $pdoUser = $modelUser->getUserAccountByID($session->id_user);
        $value = null;
        $value = $pdoUser->fetch(PDO::FETCH_ASSOC);

        if($value != null)
            $data['name_user'] = $value['name_user'];
        else
            $data['name_user'] = "ERROR";

        $pdoCustomer = $modelCustomer->getAllCustomers($session->id_tenant);
        $data['pdoCustomer'] = $pdoCustomer;

        #fecha actual
        $now = date("Y-m-d H:i:s");
        $currentDateTime = new DateTime($now);
        $timezone = new DateTimeZone($session->timezone);
        $currentDateTime = $currentDateTime->setTimezone($timezone);

        
        
        $data['current_date'] = $currentDateTime->format("Y-m-d");
        $data['current_time'] = $currentDateTime->format("H:i:s");

        $this->view->show("projects_new.php", $data);
    }

    /*
     * Add project action
     */
    public function TasksAdd()
    {
        $session = FR_Session::singleton();
        $customer = null;
        $error_user = null;
        $error_cust = null;

        $new_code = $_POST['new_code'];
        $user = $_POST['resp'];
        
        if(isset($_POST['cbocustomer']))
            $customer = $_POST['cbocustomer'];
        
        $desc = $_POST['descripcion'];
        $hora_ini = $_POST['hora_ini'];
        $fecha = $_POST['fecha'];
        $etiqueta = $_POST['etiqueta'];
        $estado = 1; #active by default

        require_once 'models/ProjectsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ProjectsModel();
        $result = $model->addNewProject($session->id_tenant, $new_code, $etiqueta, $hora_ini, $fecha, $desc);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            $id_new_project = $model->getProjectIDByCodeINT($new_code, $session->id_tenant);
            
            $result_user = $model->addUserToProject($id_new_project, $session->id_user);            
            $error_user = $result_user->errorInfo();
            
            if($customer != null){
                $result_cust = $model->addCustomerToProject($id_new_project, $customer);
                $error_cust = $result_cust->errorInfo();
            }
            
            #$this->projectsDt(1);
            header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
            header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
    }

    public function ajaxTaskAdd()
    {   
        $session = FR_Session::singleton();

        $label = $_POST['label'];
        $desc = $_POST['desc'];
        $id_project = $_POST['id_project'];
        $status = 1; // 1 by default
        
        #$code_customer = rand(1, 100);
        #$code_customer = "c".$code_customer;
        
        //Incluye el modelo que corresponde
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new TasksModel();
        
        $result = $model->getLastTask($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code = $values['code_task'];
        $code = (int)$code + 1;
        $new_task[] = null;
        
        $result = $model->addNewTask($session->id_tenant, $code, $label, $hora_ini, $fecha, $desc, $status);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            $result = $model->getLastCustomer($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            $id_customer = $values['id_customer'];
            
            $new_customer[0] = $id_customer;
            $new_customer[1] = $label_customer;
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            $new_customer[0] = "0";
            $new_customer[1] = "No se ha podido ingresar el registro";
        }
        else{
            $new_customer[0] = "0";
            $new_customer[1] = $error[2];
        }

        print json_encode($new_customer);
        
        return true;
    }
    
    /*
     * Stop project action
     */
    public function tasksStop()
    {
        $session = FR_Session::singleton();
        $frm_id_project = null;
        
        if(isset($_POST['id_project']))
            $frm_id_project = $_POST['id_project'];

        if($frm_id_project != null && $session->id_project == $frm_id_project){
            $session->id_project = null;

            #fecha actual
            $now = date("Y-m-d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $currentDateTime = $currentDateTime->setTimezone($timezone);
            $stop_date = $currentDateTime->format("Y-m-d H:i:s");

            require_once 'models/ProjectsModel.php';
            $model = new ProjectsModel();

            #get times diff
            $result = $model->getProjectById($frm_id_project, $session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);

            $init_date = $values['date_ini'];
            $total_time = Utils::diffDates($stop_date, $init_date, 'S', false);

            $result = $model->updateProject($session->id_tenant, $frm_id_project, $values['code_project']
                    , $session->id_user, $values['id_customer'], $values['label_project'], $values['date_ini']
                    , $stop_date, $total_time, $values['desc_project'], 2);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    #$this->projectsDt(1);
                    header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=1");
                }
                else{
                    #$this->projectsDt(10, "Ha ocurrido un error o no se lograron aplicar cambios: ".$error[2]);
                    header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                #$this->projectsDt(10, "Ha ocurrido un error grave!");
                header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=10&message='Ha ocurrido un error grave!");
            }
        }
        else{
            #$this->projectsDt(10, "Error, el proyecto no ha sido encontrado.");
            header("Location: ".$this->root."?controller=Projects&action=projectsDt&error_flag=10&message='Ha ocurrido un error grave!");
        }
    }

    
    
    
    
    
    /******************************
     * OLD STUFF
     * ****************************
     */
    
//    public function segmentsAddForm($error_flag = 0)
//    {
//            //Import models
//            require_once 'models/SegmentsModel.php';
//            require_once 'models/CategoriesModel.php';
//
//            //Models objects
//            $segmentModel = new SegmentsModel();
//            $gbuModel = new CategoriesModel();
//
//            //Extraer solo los GBU necesarios
//            $sql = "
//                SELECT 
//                    A.COD_GBU
//                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                    , A.NAME_GBU
//                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                FROM t_gbu A
//                INNER JOIN t_category B
//                ON A.COD_CATEGORY = B.COD_CATEGORY
//                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                    AND A.NAME_GBU NOT LIKE '%install%'
//                ORDER BY A.NAME_GBU";
//
//            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);
//
//            //Extraer ultimo codigo de segmento existente
//            $segment_code = $segmentModel->getNewSegmentCode();
//
//            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
//            {
//                //Crear un nuevo codigo: anterior+1
//                $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_SEGMENT']);
//                $LETRAS = preg_replace("/[0-9]/", "", $code['COD_SEGMENT']);  
//                $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
//                $LEER = strlen($NUEVO_CODIGO);
//
//                if($LEER > 2)
//                        $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
//                else
//                        $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;
//
//                $data['segment_code'] = $CODIGOFINAL;
//            }
//            else
//            {
//                $data['segment_code'] = "SG001";
//                $data['error'] = $segment_code;
//            }
//
//            //Finalmente presentamos nuestra plantilla
//            $data['titulo'] = "SEGMENTS > NUEVO";
//
//            $data['controller'] = "segments";
//            $data['action'] = "segmentsAdd";
//            $data['action_b'] = "segmentsDt";
//
//            //Posible error
//            $data['error_flag'] = $this->errorMessage->getError($error_flag);
//
//            $this->view->show("segments_new.php", $data);
//    }
//
//    //PROCESS
//    public function segmentsAdd()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros login form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
//                    $cod_segment = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name_segment = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //revisar si existe ya un segmento con ese nombre
//                    $coincidencia = $segmentModel->getSegmentByName($name_segment);
//
//                    //Si no hay coincidencias entonces se puede seguir
//                    if($coincidencia->rowCount() == 0)
//                    {
//                            //Le pedimos al modelo todos los items
//                            $result = $segmentModel->addNewSegment($cod_segment, $name_segment, $cod_gbu);
//
//                            //catch errors
//                            $error = $result->errorInfo();
//
//                            if($error[0] == 00000)
//                                $this->segmentsDt(1);
//                            else
//                                $this->segmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
//                    }
//                    else
//                    {
//                            $this->segmentsDt(10,"El nombre de segmento ya existe!");
//                    }
//
//            }
//            else
//            {
//                    $this->segmentsDt();
//            }
//
//    }
//
//    //SHOW
//    public function segmentsEditForm()
//    {
//            if($_POST)
//            {
//                    $data['cod_segment'] = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $data['name_segment'] = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    require_once 'models/CategoriesModel.php';
//
//                    //Models objects
//                    $gbuModel = new CategoriesModel();
//
//                    //Extraer lista de gbu existentes
//                    #$lista_gbu = $gbuModel->getAllGbu();	
//                    #$data['lista_gbu'] = $lista_gbu;
//
//                    //Extraer solo los GBU necesarios
//                    $sql = "
//                        SELECT 
//                            A.COD_GBU
//                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                            , A.NAME_GBU
//                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                        FROM t_gbu A
//                        INNER JOIN t_category B
//                        ON A.COD_CATEGORY = B.COD_CATEGORY
//                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                        AND A.NAME_GBU NOT LIKE '%install%'
//                        ORDER BY A.NAME_GBU";
//
//                    $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);
//
//                    //Finalmente presentamos nuestra plantilla
//                    $data['titulo'] = "SEGMENTS > EDICI&Oacute;N";
//
//                    $data['controller'] = "segments";
//                    $data['action'] = "segmentsAdd";
//                    $data['action_b'] = "segmentsDt";
//
//                    $this->view->show("segments_edit.php", $data);
//            }
//            else
//            {
//                    $this->segmentsDt(2);
//            }
//    }
//
//    //PROCESS
//    public function segmentsEdit()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_name'], $_POST['old_gbu']);
//                    $cod_segment = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name_segment = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//                    $old_cod_segment = $this->utils->cleanQuery($_POST['old_code']);
//                    $old_name_segment = $this->utils->cleanQuery($_POST['old_name']);
//                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //revisar si existe ya un segmento con ese nombre
//                    $coincidencia_name = $segmentModel->getSegmentByName($name_segment);
//                    #$coincidencia_code = $segmentModel->getSegmentByCode($cod_segment);
//
//                    if($coincidencia_name->rowCount() == 0)
//                    {
//                            //Le pedimos al modelo todos los items
//                            $result = $segmentModel->editSegment($cod_segment, $name_segment, $cod_gbu, $old_cod_segment, $old_name_segment, $old_gbu);
//
//                            //catch errors
//                            $error = $result->errorInfo();
//
//                            if($error[0] == 00000)
//                                $this->segmentsDt(1);
//                            else
//                                $this->segmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
//                    }
//                    else
//                    {
//                            $this->segmentsDt(1);
//                    }
//
//            }
//            else
//            {
//                    $this->segmentsDt();
//            }
//    }
//
//    /**
//        * Get all segments in a serialized array
//        * @return json 
//        */
//    public function listSegmentsJSON()
//    {
//        //Incluye el modelo que corresponde
//        require_once 'models/SegmentsModel.php';
//
//        //Creamos una instancia de nuestro "modelo"
//        $segmentModel = new SegmentsModel();
//
//        if(isset($_GET['gbu']))
//            $listado = $segmentModel->getAllSegmentsByGbu ($_GET['gbu']);
//        else
//            $listado = $segmentModel->getAllSegments();
//
//        $output = array();
//
//        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
//        {
//            $output[$row['COD_SEGMENT']] = utf8_encode($row['NAME_SEGMENT']);
//        }
//
//        $output['selected'] = utf8_encode($_GET['current']);
//
//        echo json_encode( $output );
//    }
//
//
//    /*******************************************************************************
//    * SUB SEGMENTS
//    *******************************************************************************/
//
//    //SHOW
//    public function subSegmentsDt($error_flag = 0, $message = "")
//    {   
//            $session = FR_Session::singleton();
//
//            //Incluye el modelo que corresponde
//            require_once 'models/SegmentsModel.php';
//            require_once 'models/UsersModel.php';
//
//            //Creamos una instancia de nuestro "modelo"
//            $segmentModel = new SegmentsModel();
//            $userModel = new UsersModel();
//
//            //Le pedimos al modelo todos los items
//            $listado = $segmentModel->getAllSubSegments();
//
//            //Pasamos a la vista toda la información que se desea representar
//            $data['listado'] = $listado;
//
//            // Obtener permisos de edición
//            $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
//            if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//                $data['permiso_editar'] = $row['EDITAR'];
//            }
//
//            //Titulo pagina
//            $data['titulo'] = "SUB-SEGMENTS";
//
//            $data['controller'] = "segments";
//            $data['action'] = "subSegmentsEditForm";
//            $data['action_b'] = "subSegmentsDt";
//
//            //Posible error
//            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
//
//            //Finalmente presentamos nuestra plantilla
//            $this->view->show("sub_segments_dt.php", $data);
//    }
//
//    //SHOW
//    public function subSegmentsAddForm($error_flag = 0)
//    {
//            //Import models
//            require_once 'models/SegmentsModel.php';
//            require_once 'models/CategoriesModel.php';
//
//            //Models objects
//            $segmentModel = new SegmentsModel();
//            $gbuModel = new CategoriesModel();
//
//            //Extraer lista de gbu existentes
//            #$lista_gbu = $gbuModel->getAllGbu();	
//            #$data['lista_gbu'] = $lista_gbu;
//
//            //Extraer solo los GBU necesarios
//            $sql = "
//                SELECT 
//                    A.COD_GBU
//                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                    , A.NAME_GBU
//                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                FROM t_gbu A
//                INNER JOIN t_category B
//                ON A.COD_CATEGORY = B.COD_CATEGORY
//                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                    AND A.NAME_GBU NOT LIKE '%install%'
//                ORDER BY A.NAME_GBU";
//
//            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);
//
//            //Extraer ultimo codigo de segmento existente
//            $segment_code = $segmentModel->getNewSubSegmentCode();
//
//            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
//            {
//                    //Crear un nuevo codigo: anterior+1
//                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_SUB_SEGMENT']);
//                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_SUB_SEGMENT']);  
//                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
//                    $LEER = strlen($NUEVO_CODIGO);
//
//                    if($LEER > 2)
//                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
//                    else
//                            $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;
//
//                    $data['newcode'] = $CODIGOFINAL;
//            }
//            else
//                    $data['newcode'] = "SS001";
//
//            //Finalmente presentamos nuestra plantilla
//            $data['titulo'] = "SUB-SEGMENTS > New";
//
//            $data['controller'] = "segments";
//            $data['action'] = "subSegmentsAdd";
//            $data['action_b'] = "subSegmentsDt";
//
//            //Posible error
//            $data['error_flag'] = $this->errorMessage->getError($error_flag);
//
//            $this->view->show("sub_segments_new.php", $data);
//    }
//
//    //PROCESS
//    public function subSegmentsAdd()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
//                    $codigo = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //Le pedimos al modelo todos los items
//                    $result = $segmentModel->addNewSubSegment($codigo, $name, $cod_gbu);
//
//                    //catch errors
//                    $error = $result->errorInfo();
//
//                    if($error[0] == 00000)
//                        $this->subSegmentsDt(1);
//                    else
//                        $this->subSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
//            }
//            else
//            {
//                    $this->subSegmentsDt();
//            }
//
//    }
//
//    //SHOW
//    public function subSegmentsEditForm()
//    {
//            if($_POST)
//            {
//                    $data['code'] = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $data['name'] = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    require_once 'models/CategoriesModel.php';
//
//                    //Models objects
//                    $model = new CategoriesModel();
//
//                    //Extraer lista de gbu existentes
//                    #$lista_gbu = $model->getAllGbu();	
//                    #$data['lista_gbu'] = $lista_gbu;
//
//                    //Extraer solo los GBU necesarios
//                    $sql = "
//                        SELECT 
//                            A.COD_GBU
//                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                            , A.NAME_GBU
//                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                        FROM t_gbu A
//                        INNER JOIN t_category B
//                        ON A.COD_CATEGORY = B.COD_CATEGORY
//                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                        AND A.NAME_GBU NOT LIKE '%install%'
//                        ORDER BY A.NAME_GBU";
//
//                    $data['lista_gbu'] = $model->goCustomQuery($sql);
//
//                    //Finalmente presentamos nuestra plantilla
//                    $data['titulo'] = "SUB-SEGMENTS > EDICI&Oacute;N";
//
//                    $data['controller'] = "segments";
//                    $data['action'] = "subSegmentsEdit";
//                    $data['action_b'] = "subSegmentsDt";
//
//                    $this->view->show("sub_segments_edit.php", $data);
//            }
//            else
//            {
//                    $this->subSegmentsDt(2);
//            }
//    }
//
//    //PROCESS
//    public function subSegmentsEdit()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_nam'], $_POST['old_gbu']);
//                    $code = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//                    $old_code = $this->utils->cleanQuery($_POST['old_code']);
//                    $old_name = $this->utils->cleanQuery($_POST['old_name']);
//                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //Le pedimos al modelo todos los items
//                    $result = $segmentModel->editSubSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu);
//
//                    //catch errors
//                    $error = $result->errorInfo();
//
//                    if($error[0] == 00000)
//                        $this->subSegmentsDt(1);
//                    else
//                        $this->subSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
//            }
//            else
//            {
//                    $this->subSegmentsDt();
//            }
//    }
//
//    /**
//    * Get all sub segments in a serialized array
//    * @return json
//    */
//    public function listSubSegmentsJSON()
//    {
//        //Incluye el modelo que corresponde
//        require_once 'models/SegmentsModel.php';
//
//        //Creamos una instancia de nuestro "modelo"
//        $segmentModel = new SegmentsModel();
//
//        if(isset($_GET['gbu']))
//            $listado = $segmentModel->getSubSegmentsByGbu($_GET['gbu']);
//        else
//            $listado = $segmentModel->getAllSubSegments();
//
//        $output = array();
//
//        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
//        {
//            $output[$row['COD_SUB_SEGMENT']] = utf8_encode($row['NAME_SUB_SEGMENT']);
//        }
//
//        $output['selected'] = utf8_encode($_GET['current']);
//
//        echo json_encode( $output );
//    }
//
//
//    /*******************************************************************************
//    * MICRO SEGMENTS
//    *******************************************************************************/
//
//    //SHOW
//    public function microSegmentsDt($error_flag = 0, $message = "")
//    {
//            //Incluye el modelo que corresponde
//            require_once 'models/SegmentsModel.php';
//            require_once 'models/UsersModel.php';
//
//            //Creamos una instancia de nuestro "modelo"
//            $segmentModel = new SegmentsModel();
//            $userModel = new UsersModel();
//
//            //Le pedimos al modelo todos los items
//            $listado = $segmentModel->getAllMicroSegments();
//
//            //Pasamos a la vista toda la información que se desea representar
//            $data['listado'] = $listado;
//
//            // Obtener permisos de edición
//            $session = FR_Session::singleton();
//
//            $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
//            if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//                $data['permiso_editar'] = $row['EDITAR'];
//            }
//
//            //Titulo pagina
//            $data['titulo'] = "MICRO-SEGMENTS";
//
//            $data['controller'] = "segments";
//            $data['action'] = "microSegmentsEditForm";
//            $data['action_b'] = "microSegmentsDt";
//
//            //Posible error
//            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
//
//            //Finalmente presentamos nuestra plantilla
//            $this->view->show("micro_segments_dt.php", $data);
//    }
//
//    //SHOW
//    public function microSegmentsAddForm($error_flag = 0)
//    {
//            //Import models
//            require_once 'models/SegmentsModel.php';
//            require_once 'models/CategoriesModel.php';
//
//            //Models objects
//            $segmentModel = new SegmentsModel();
//            $gbuModel = new CategoriesModel();
//
//            //Extraer lista de gbu existentes
//            #$lista_gbu = $gbuModel->getAllGbu();	
//            #$data['lista_gbu'] = $lista_gbu;
//
//            //Extraer solo los GBU necesarios
//            $sql = "
//                SELECT 
//                    A.COD_GBU
//                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                    , A.NAME_GBU
//                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                FROM t_gbu A
//                INNER JOIN t_category B
//                ON A.COD_CATEGORY = B.COD_CATEGORY
//                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                    AND A.NAME_GBU NOT LIKE '%install%'
//                ORDER BY A.NAME_GBU";
//
//            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);
//
//            //Extraer ultimo codigo de segmento existente
//            $segment_code = $segmentModel->getNewMicroSegmentCode();
//
//            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
//            {
//                    //Crear un nuevo codigo: actual+1
//                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_MICRO_SEGMENT']);
//                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_MICRO_SEGMENT']);  
//                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
//                    $LEER = strlen($NUEVO_CODIGO);
//
//                    if($LEER > 2)
//                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
//                    else
//                            $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;
//
//                    $data['newcode'] = $CODIGOFINAL;
//            }
//            else
//                    $data['newcode'] = "MS001";
//
//            //Finalmente presentamos nuestra plantilla
//            $data['titulo'] = "MICRO-SEGMENTS > New";
//
//            $data['controller'] = "segments";
//            $data['action'] = "microSegmentsAdd";
//            $data['action_b'] = "microSegmentsDt";
//
//            //Posible error
//            $data['error_flag'] = $this->errorMessage->getError($error_flag);
//
//            $this->view->show("micro_segments_new.php", $data);
//    }
//
//    //PROCESS
//    public function microSegmentsAdd()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
//                    $codigo = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //Le pedimos al modelo todos los items
//                    $result = $segmentModel->addNewMicroSegment($codigo, $name, $cod_gbu);
//
//                    //catch errors
//                    $error = $result->errorInfo();
//
//                    if($error[0] == 00000)
//                        $this->microSegmentsDt(1);
//                    else
//                        $this->microSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");	
//            }
//            else
//            {
//                    $this->microSegmentsDt();
//            }
//    }
//
//    //SHOW
//    public function microSegmentsEditForm()
//    {
//            if($_POST)
//            {
//                    $data['code'] = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $data['name'] = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);
//
//                    require_once 'models/CategoriesModel.php';
//
//                    //Models objects
//                    $gbuModel = new CategoriesModel();
//
//                    //Extraer lista de gbu existentes
//                    #$lista_gbu = $gbuModel->getAllGbu();	
//                    #$data['lista_gbu'] = $lista_gbu;
//
//                    //Extraer solo los GBU necesarios
//                    $sql = "
//                        SELECT 
//                            A.COD_GBU
//                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
//                            , A.NAME_GBU
//                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
//                        FROM t_gbu A
//                        INNER JOIN t_category B
//                        ON A.COD_CATEGORY = B.COD_CATEGORY
//                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
//                        AND A.NAME_GBU NOT LIKE '%install%'
//                        ORDER BY A.NAME_GBU";
//
//                    $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);
//
//                    //Finalmente presentamos nuestra plantilla
//                    $data['titulo'] = "MICRO-SEGMENTS > EDICI&Oacute;N";
//
//                    $data['controller'] = "segments";
//                    $data['action'] = "microSegmentsEdit";
//                    $data['action_b'] = "microSegmentsDt";
//
//                    $this->view->show("micro_segments_edit.php", $data);
//            }
//            else
//            {
//                    $this->microSegmentsDt(2);
//            }
//    }
//
//    //PROCESS
//    public function microSegmentsEdit()
//    {
//            $session = FR_Session::singleton();
//
//            //Parametros form
//            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
//            {
//                    //Avoid resubmit
//                    $session->orig_timestamp = microtime(true);
//
//                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_nam'], $_POST['old_gbu']);
//                    $code = $this->utils->cleanQuery($_POST['txtcodigo']);
//                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
//                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
//                    $old_code = $this->utils->cleanQuery($_POST['old_code']);
//                    $old_name = $this->utils->cleanQuery($_POST['old_name']);
//                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//
//                    //Creamos una instancia de nuestro "modelo"
//                    $segmentModel = new SegmentsModel();
//
//                    //Le pedimos al modelo todos los items
//                    $result = $segmentModel->editMicroSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu);
//
//                    //catch errors
//                    $error = $result->errorInfo();
//
//                    if($error[0] == 00000)
//                        $this->microSegmentsDt(1);
//                    else
//                        $this->microSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
//            }
//            else
//            {
//                    $this->microSegmentsDt();
//            }
//    }
//
//    /**
//    * Get all sub segments in a serialized array
//    * @return json 
//    */
//    public function listMicroSegmentsJSON()
//    {
//        //Incluye el modelo que corresponde
//        require_once 'models/SegmentsModel.php';
//
//        //Creamos una instancia de nuestro "modelo"
//        $segmentModel = new SegmentsModel();
//
//        if(isset($_GET['gbu']))
//            $listado = $segmentModel->getAllMicroSegmentsByGbu ($_GET['gbu']);
//        else
//            $listado = $segmentModel->getAllMicroSegments ();
//
//        $output = array();
//
//        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
//        {
//            $output[$row['COD_MICRO_SEGMENT']] = utf8_encode($row['NAME_MICRO_SEGMENT']);
//        }
//
//        $output['selected'] = utf8_encode($_GET['current']);
//
//        echo json_encode( $output );
//    }
//
//    /*
//    * Verify Segment Name (+ Sub & Micro)
//    * AJAX
//    */
//    public function verifyNameSegment()
//    {
//        if($_REQUEST['txtnombre']){
//            if(isset($_REQUEST['old_name'])){
//                // Edicion
//                if(mysql_real_escape_string($_REQUEST['txtnombre']) != mysql_real_escape_string($_REQUEST['old_name'])){
//                    $input = mysql_real_escape_string($_REQUEST['txtnombre']);
//
//                    if($_REQUEST['target'] == 1)
//                        $sql = "SELECT name_segment FROM t_segment WHERE name_segment = '$input'";
//                    elseif($_REQUEST['target'] == 2)
//                        $sql = "SELECT name_sub_segment FROM t_sub_segment WHERE name_sub_segment = '$input'";
//                    elseif($_REQUEST['target'] == 3)
//                        $sql = "SELECT name_micro_segment FROM t_micro_segment WHERE name_micro_segment = '$input'";
//
//                    //Incluye el modelo que corresponde
//                    require_once 'models/SegmentsModel.php';
//                    $model = new SegmentsModel();
//                    $result = $model->goCustomQuery($sql);
//
//                    if($result->rowCount() > 0)
//                        echo "false";
//                    else
//                        echo "true";
//                }
//                else
//                    echo "true";
//            }
//            else{
//                // Nuevo
//                $input = mysql_real_escape_string($_REQUEST['txtnombre']);
//
//                if($_REQUEST['target'] == 1)
//                    $sql = "SELECT name_segment FROM t_segment WHERE name_segment = '$input'";
//                elseif($_REQUEST['target'] == 2)
//                    $sql = "SELECT name_sub_segment FROM t_sub_segment WHERE name_sub_segment = '$input'";
//                elseif($_REQUEST['target'] == 3)
//                    $sql = "SELECT name_micro_segment FROM t_micro_segment WHERE name_micro_segment = '$input'";
//
//                //Incluye el modelo que corresponde
//                require_once 'models/SegmentsModel.php';
//                $model = new SegmentsModel();
//                $result = $model->goCustomQuery($sql);
//
//                if($result->rowCount() > 0)
//                    echo "false";
//                else
//                    echo "true";
//            }
//        }
//        else
//            echo "false";
//    }
}
?>