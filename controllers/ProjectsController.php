<?php
class ProjectsController extends ControllerBase
{
    /***************************************************************************
    * PROJECTS
    ***************************************************************************/

    /**
     * Show projects dt
     * @param type $error_flag
     * @param type $message 
     */
    public function projectsDt($error_flag = 0, $message = "")
    {
        $session = FR_Session::singleton();
        
        //Incluye el modelo que corresponde
        require_once 'models/ProjectsModel.php';
        require_once 'models/UsersModel.php';

        //Creamos una instancia de nuestro "modelo"
        $projectsModel = new ProjectsModel();
        $userModel = new UsersModel();

        //Le pedimos al modelo todos los items
        $listado = $projectsModel->getAllProjectsByTenant($session->id_tenant);

        // Obtener permisos de edición
//        $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
//        if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
//            $data['permiso_editar'] = $row['EDITAR'];
//        }
//
        //Pasamos a la vista toda la información que se desea representar
        $data['listado'] = $listado;

        //Titulo pagina
        $data['titulo'] = "LISTA DE TRABAJOS";

        $data['controller'] = "projects";
//        $data['action'] = "trabajosVer";
//        $data['action_b'] = "trabajosDt";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        $this->view->show("projects_dt.php", $data);
    }

    /**
     * show project info 
     */
    public function projectsView()
    {
        $session = FR_Session::singleton();

        $id_project = $_POST['id_project'];
        $session->id_project = $id_project;

        require_once 'models/ProjectsModel.php';
        $model = new ProjectsModel();

        $pdo = $model->getProjectById($id_project, $session->id_tenant);
//        $values = $pdo->fetch(PDO::FETCH_ASSOC);
//        
//        if(isset($values)){
//            $data['id_project'] = $values['id_project'];
//            $data['code_project'] = $values['code_project'];
//            $data['label_project'] = $values['label_project'];
//            $data['date_ini'] = $values['date_ini'];
//            $data['date_end'] = $values['date_end'];
//        }

        $data['titulo'] = "TRABAJO #".$id_project;
        $data['pdo'] = $pdo;

        $this->view->show("projects_view.php", $data);
    }

    /*
     * Show new project form 
     */
    public function projectsNewForm(){
        $session = FR_Session::singleton();

        require_once 'models/ProjectsModel.php';
        require_once 'models/UsersModel.php';
        require_once 'models/CustomersModel.php';

        $model = new ProjectsModel();
        $modelUser = new UsersModel();
        $modelCustomer = new CustomersModel();

        $pdo = $model->getLastProject($session->id_tenant);
        $value = null;
        $value = $pdo->fetch(PDO::FETCH_ASSOC);

        if($value != null)
        {
            $last_code = $value['code_project'];
            $new_code = (int) $last_code + 1;
        }
        else
        {
            $new_code = 0;
            $data['error'] = "ERROR";
        }

        $data['new_code'] = $new_code;
        $data['titulo'] = "NUEVO TRABAJO #".$new_code;

        $pdoUser = $modelUser->getUserAccountByID($session->id_user);
        $value = null;
        $value = $pdoUser->fetch(PDO::FETCH_ASSOC);

        if($value != null)
            $data['name_user'] = $value['name_user'];
        else
            $data['name_user'] = "ERROR";

        $pdoCustomer = $modelCustomer->getAllCustomersByTenant($session->id_tenant);
        $data['pdoCustomer'] = $pdoCustomer;

        #fecha actual
        $now = date("Y-m-d H:i:s");
        $currentDateTime = new DateTime($now);

        $data['current_date'] = $currentDateTime->format("Y-m-d");
        $data['current_time'] = $currentDateTime->format("H:i");

        $this->view->show("projects_new.php", $data);
    }

    /*
     * Add project action
     */
    public function projectsAdd()
    {
        $session = FR_Session::singleton();

        $new_code = $_POST['new_code'];
        $user = $_POST['resp'];
        $customer = $_POST['cbocustomer'];
        $desc = $_POST['descripcion'];
        $hora_ini = $_POST['hora_ini'];
        $fecha = $_POST['fecha'];
        $etiqueta = $_POST['etiqueta'];
        $estado = 1; #active by default

        require_once 'models/ProjectsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new ProjectsModel();
        #$result = $model->addNewProject($session->id_tenant, $new_code, $session->id_user, $customer, $desc, $hora_ini, $fecha);
        $result = $model->addNewProject($session->id_tenant, $new_code, $session->id_user, $customer, $etiqueta, $hora_ini, $fecha, $desc);

        if($result != null){
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->projectsDt(1);
            else
                $this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);   
        }
        else
            $this->projectsDt(10, "Ha ocurrido un error grave!");   
    }

    /*
     * Stop project action
     */
    public function projectsStop()
    {
        $session = FR_Session::singleton();
        $frm_id_project = $_POST['id_project'];
        
        if($session->id_project == $frm_id_project){
            $session->id_project = null;
            
            #fecha actual
            $now = date("Y-m-d H:i:s");
            $currentDateTime = new DateTime($now);
            $stop_date = $currentDateTime->format("Y-m-d H:i:s");

            require_once 'models/ProjectsModel.php';
            $model = new ProjectsModel();
            
            #get times diff
            $result = $model->getProjectById($frm_id_project, $session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);

            /*
             * TRABAJANDO AQUI!!!!!!!
             * 
             * TOMANDO VALORES DE PROYECTO EN VISTA PARA TOMAR DIFERENCIA DE FECHAS
             * Y ACTUALIZAR PROYECTO COMO FINALIZADO, INDICANDO FECHA DE FIN Y
             * HORAS (Y MINUTOS) DE DURACION.
             */

            $result = $model->addNewProject($session->id_tenant, $new_code, $session->id_user, $customer, $etiqueta, $hora_ini, $fecha, $desc);
            
            if($result != null){
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->projectsDt(1);
            else
                $this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
            }
            else
                $this->projectsDt(10, "Ha ocurrido un error grave!");
        }
        else
            $this->projectsDt(10, "Ha ocurrido un error grave!");
    }
    
    
    
    
    /******************************
     * OLD STUFF
     * ****************************
     */
    
    public function segmentsAddForm($error_flag = 0)
    {
            //Import models
            require_once 'models/SegmentsModel.php';
            require_once 'models/CategoriesModel.php';

            //Models objects
            $segmentModel = new SegmentsModel();
            $gbuModel = new CategoriesModel();

            //Extraer solo los GBU necesarios
            $sql = "
                SELECT 
                    A.COD_GBU
                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
                    , A.NAME_GBU
                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                FROM t_gbu A
                INNER JOIN t_category B
                ON A.COD_CATEGORY = B.COD_CATEGORY
                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                    AND A.NAME_GBU NOT LIKE '%install%'
                ORDER BY A.NAME_GBU";

            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);

            //Extraer ultimo codigo de segmento existente
            $segment_code = $segmentModel->getNewSegmentCode();

            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
            {
                //Crear un nuevo codigo: anterior+1
                $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_SEGMENT']);
                $LETRAS = preg_replace("/[0-9]/", "", $code['COD_SEGMENT']);  
                $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                $LEER = strlen($NUEVO_CODIGO);

                if($LEER > 2)
                        $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                else
                        $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                $data['segment_code'] = $CODIGOFINAL;
            }
            else
            {
                $data['segment_code'] = "SG001";
                $data['error'] = $segment_code;
            }

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "SEGMENTS > NUEVO";

            $data['controller'] = "segments";
            $data['action'] = "segmentsAdd";
            $data['action_b'] = "segmentsDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag);

            $this->view->show("segments_new.php", $data);
    }

    //PROCESS
    public function segmentsAdd()
    {
            $session = FR_Session::singleton();

            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
                    $cod_segment = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name_segment = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //revisar si existe ya un segmento con ese nombre
                    $coincidencia = $segmentModel->getSegmentByName($name_segment);

                    //Si no hay coincidencias entonces se puede seguir
                    if($coincidencia->rowCount() == 0)
                    {
                            //Le pedimos al modelo todos los items
                            $result = $segmentModel->addNewSegment($cod_segment, $name_segment, $cod_gbu);

                            //catch errors
                            $error = $result->errorInfo();

                            if($error[0] == 00000)
                                $this->segmentsDt(1);
                            else
                                $this->segmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                    }
                    else
                    {
                            $this->segmentsDt(10,"El nombre de segmento ya existe!");
                    }

            }
            else
            {
                    $this->segmentsDt();
            }

    }

    //SHOW
    public function segmentsEditForm()
    {
            if($_POST)
            {
                    $data['cod_segment'] = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $data['name_segment'] = $this->utils->cleanQuery($_POST['txtnombre']);
                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);

                    require_once 'models/CategoriesModel.php';

                    //Models objects
                    $gbuModel = new CategoriesModel();

                    //Extraer lista de gbu existentes
                    #$lista_gbu = $gbuModel->getAllGbu();	
                    #$data['lista_gbu'] = $lista_gbu;

                    //Extraer solo los GBU necesarios
                    $sql = "
                        SELECT 
                            A.COD_GBU
                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
                            , A.NAME_GBU
                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                        FROM t_gbu A
                        INNER JOIN t_category B
                        ON A.COD_CATEGORY = B.COD_CATEGORY
                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                        AND A.NAME_GBU NOT LIKE '%install%'
                        ORDER BY A.NAME_GBU";

                    $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);

                    //Finalmente presentamos nuestra plantilla
                    $data['titulo'] = "SEGMENTS > EDICI&Oacute;N";

                    $data['controller'] = "segments";
                    $data['action'] = "segmentsAdd";
                    $data['action_b'] = "segmentsDt";

                    $this->view->show("segments_edit.php", $data);
            }
            else
            {
                    $this->segmentsDt(2);
            }
    }

    //PROCESS
    public function segmentsEdit()
    {
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_name'], $_POST['old_gbu']);
                    $cod_segment = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name_segment = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
                    $old_cod_segment = $this->utils->cleanQuery($_POST['old_code']);
                    $old_name_segment = $this->utils->cleanQuery($_POST['old_name']);
                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //revisar si existe ya un segmento con ese nombre
                    $coincidencia_name = $segmentModel->getSegmentByName($name_segment);
                    #$coincidencia_code = $segmentModel->getSegmentByCode($cod_segment);

                    if($coincidencia_name->rowCount() == 0)
                    {
                            //Le pedimos al modelo todos los items
                            $result = $segmentModel->editSegment($cod_segment, $name_segment, $cod_gbu, $old_cod_segment, $old_name_segment, $old_gbu);

                            //catch errors
                            $error = $result->errorInfo();

                            if($error[0] == 00000)
                                $this->segmentsDt(1);
                            else
                                $this->segmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                    }
                    else
                    {
                            $this->segmentsDt(1);
                    }

            }
            else
            {
                    $this->segmentsDt();
            }
    }

    /**
        * Get all segments in a serialized array
        * @return json 
        */
    public function listSegmentsJSON()
    {
        //Incluye el modelo que corresponde
        require_once 'models/SegmentsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $segmentModel = new SegmentsModel();

        if(isset($_GET['gbu']))
            $listado = $segmentModel->getAllSegmentsByGbu ($_GET['gbu']);
        else
            $listado = $segmentModel->getAllSegments();

        $output = array();

        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
        {
            $output[$row['COD_SEGMENT']] = utf8_encode($row['NAME_SEGMENT']);
        }

        $output['selected'] = utf8_encode($_GET['current']);

        echo json_encode( $output );
    }


    /*******************************************************************************
    * SUB SEGMENTS
    *******************************************************************************/

    //SHOW
    public function subSegmentsDt($error_flag = 0, $message = "")
    {   
            $session = FR_Session::singleton();

            //Incluye el modelo que corresponde
            require_once 'models/SegmentsModel.php';
            require_once 'models/UsersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $segmentModel = new SegmentsModel();
            $userModel = new UsersModel();

            //Le pedimos al modelo todos los items
            $listado = $segmentModel->getAllSubSegments();

            //Pasamos a la vista toda la información que se desea representar
            $data['listado'] = $listado;

            // Obtener permisos de edición
            $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
            if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                $data['permiso_editar'] = $row['EDITAR'];
            }

            //Titulo pagina
            $data['titulo'] = "SUB-SEGMENTS";

            $data['controller'] = "segments";
            $data['action'] = "subSegmentsEditForm";
            $data['action_b'] = "subSegmentsDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

            //Finalmente presentamos nuestra plantilla
            $this->view->show("sub_segments_dt.php", $data);
    }

    //SHOW
    public function subSegmentsAddForm($error_flag = 0)
    {
            //Import models
            require_once 'models/SegmentsModel.php';
            require_once 'models/CategoriesModel.php';

            //Models objects
            $segmentModel = new SegmentsModel();
            $gbuModel = new CategoriesModel();

            //Extraer lista de gbu existentes
            #$lista_gbu = $gbuModel->getAllGbu();	
            #$data['lista_gbu'] = $lista_gbu;

            //Extraer solo los GBU necesarios
            $sql = "
                SELECT 
                    A.COD_GBU
                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
                    , A.NAME_GBU
                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                FROM t_gbu A
                INNER JOIN t_category B
                ON A.COD_CATEGORY = B.COD_CATEGORY
                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                    AND A.NAME_GBU NOT LIKE '%install%'
                ORDER BY A.NAME_GBU";

            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);

            //Extraer ultimo codigo de segmento existente
            $segment_code = $segmentModel->getNewSubSegmentCode();

            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
            {
                    //Crear un nuevo codigo: anterior+1
                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_SUB_SEGMENT']);
                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_SUB_SEGMENT']);  
                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                    $LEER = strlen($NUEVO_CODIGO);

                    if($LEER > 2)
                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                    else
                            $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                    $data['newcode'] = $CODIGOFINAL;
            }
            else
                    $data['newcode'] = "SS001";

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "SUB-SEGMENTS > New";

            $data['controller'] = "segments";
            $data['action'] = "subSegmentsAdd";
            $data['action_b'] = "subSegmentsDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag);

            $this->view->show("sub_segments_new.php", $data);
    }

    //PROCESS
    public function subSegmentsAdd()
    {
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
                    $codigo = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //Le pedimos al modelo todos los items
                    $result = $segmentModel->addNewSubSegment($codigo, $name, $cod_gbu);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->subSegmentsDt(1);
                    else
                        $this->subSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                    $this->subSegmentsDt();
            }

    }

    //SHOW
    public function subSegmentsEditForm()
    {
            if($_POST)
            {
                    $data['code'] = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $data['name'] = $this->utils->cleanQuery($_POST['txtnombre']);
                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);

                    require_once 'models/CategoriesModel.php';

                    //Models objects
                    $model = new CategoriesModel();

                    //Extraer lista de gbu existentes
                    #$lista_gbu = $model->getAllGbu();	
                    #$data['lista_gbu'] = $lista_gbu;

                    //Extraer solo los GBU necesarios
                    $sql = "
                        SELECT 
                            A.COD_GBU
                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
                            , A.NAME_GBU
                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                        FROM t_gbu A
                        INNER JOIN t_category B
                        ON A.COD_CATEGORY = B.COD_CATEGORY
                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                        AND A.NAME_GBU NOT LIKE '%install%'
                        ORDER BY A.NAME_GBU";

                    $data['lista_gbu'] = $model->goCustomQuery($sql);

                    //Finalmente presentamos nuestra plantilla
                    $data['titulo'] = "SUB-SEGMENTS > EDICI&Oacute;N";

                    $data['controller'] = "segments";
                    $data['action'] = "subSegmentsEdit";
                    $data['action_b'] = "subSegmentsDt";

                    $this->view->show("sub_segments_edit.php", $data);
            }
            else
            {
                    $this->subSegmentsDt(2);
            }
    }

    //PROCESS
    public function subSegmentsEdit()
    {
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_nam'], $_POST['old_gbu']);
                    $code = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
                    $old_code = $this->utils->cleanQuery($_POST['old_code']);
                    $old_name = $this->utils->cleanQuery($_POST['old_name']);
                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //Le pedimos al modelo todos los items
                    $result = $segmentModel->editSubSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->subSegmentsDt(1);
                    else
                        $this->subSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                    $this->subSegmentsDt();
            }
    }

    /**
    * Get all sub segments in a serialized array
    * @return json
    */
    public function listSubSegmentsJSON()
    {
        //Incluye el modelo que corresponde
        require_once 'models/SegmentsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $segmentModel = new SegmentsModel();

        if(isset($_GET['gbu']))
            $listado = $segmentModel->getSubSegmentsByGbu($_GET['gbu']);
        else
            $listado = $segmentModel->getAllSubSegments();

        $output = array();

        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
        {
            $output[$row['COD_SUB_SEGMENT']] = utf8_encode($row['NAME_SUB_SEGMENT']);
        }

        $output['selected'] = utf8_encode($_GET['current']);

        echo json_encode( $output );
    }


    /*******************************************************************************
    * MICRO SEGMENTS
    *******************************************************************************/

    //SHOW
    public function microSegmentsDt($error_flag = 0, $message = "")
    {
            //Incluye el modelo que corresponde
            require_once 'models/SegmentsModel.php';
            require_once 'models/UsersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $segmentModel = new SegmentsModel();
            $userModel = new UsersModel();

            //Le pedimos al modelo todos los items
            $listado = $segmentModel->getAllMicroSegments();

            //Pasamos a la vista toda la información que se desea representar
            $data['listado'] = $listado;

            // Obtener permisos de edición
            $session = FR_Session::singleton();

            $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 7);
            if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                $data['permiso_editar'] = $row['EDITAR'];
            }

            //Titulo pagina
            $data['titulo'] = "MICRO-SEGMENTS";

            $data['controller'] = "segments";
            $data['action'] = "microSegmentsEditForm";
            $data['action_b'] = "microSegmentsDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

            //Finalmente presentamos nuestra plantilla
            $this->view->show("micro_segments_dt.php", $data);
    }

    //SHOW
    public function microSegmentsAddForm($error_flag = 0)
    {
            //Import models
            require_once 'models/SegmentsModel.php';
            require_once 'models/CategoriesModel.php';

            //Models objects
            $segmentModel = new SegmentsModel();
            $gbuModel = new CategoriesModel();

            //Extraer lista de gbu existentes
            #$lista_gbu = $gbuModel->getAllGbu();	
            #$data['lista_gbu'] = $lista_gbu;

            //Extraer solo los GBU necesarios
            $sql = "
                SELECT 
                    A.COD_GBU
                    , B.COD_CATEGORY AS CAT_COD_CATEGORY
                    , A.NAME_GBU
                    , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                FROM t_gbu A
                INNER JOIN t_category B
                ON A.COD_CATEGORY = B.COD_CATEGORY
                WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                    AND A.NAME_GBU NOT LIKE '%install%'
                ORDER BY A.NAME_GBU";

            $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);

            //Extraer ultimo codigo de segmento existente
            $segment_code = $segmentModel->getNewMicroSegmentCode();

            if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
            {
                    //Crear un nuevo codigo: actual+1
                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_MICRO_SEGMENT']);
                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_MICRO_SEGMENT']);  
                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                    $LEER = strlen($NUEVO_CODIGO);

                    if($LEER > 2)
                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                    else
                            $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                    $data['newcode'] = $CODIGOFINAL;
            }
            else
                    $data['newcode'] = "MS001";

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "MICRO-SEGMENTS > New";

            $data['controller'] = "segments";
            $data['action'] = "microSegmentsAdd";
            $data['action_b'] = "microSegmentsDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag);

            $this->view->show("micro_segments_new.php", $data);
    }

    //PROCESS
    public function microSegmentsAdd()
    {
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
                    $codigo = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //Le pedimos al modelo todos los items
                    $result = $segmentModel->addNewMicroSegment($codigo, $name, $cod_gbu);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->microSegmentsDt(1);
                    else
                        $this->microSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");	
            }
            else
            {
                    $this->microSegmentsDt();
            }
    }

    //SHOW
    public function microSegmentsEditForm()
    {
            if($_POST)
            {
                    $data['code'] = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $data['name'] = $this->utils->cleanQuery($_POST['txtnombre']);
                    $data['cod_gbu'] = $this->utils->cleanQuery($_POST['txtgbu']);

                    require_once 'models/CategoriesModel.php';

                    //Models objects
                    $gbuModel = new CategoriesModel();

                    //Extraer lista de gbu existentes
                    #$lista_gbu = $gbuModel->getAllGbu();	
                    #$data['lista_gbu'] = $lista_gbu;

                    //Extraer solo los GBU necesarios
                    $sql = "
                        SELECT 
                            A.COD_GBU
                            , B.COD_CATEGORY AS CAT_COD_CATEGORY
                            , A.NAME_GBU
                            , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                        FROM t_gbu A
                        INNER JOIN t_category B
                        ON A.COD_CATEGORY = B.COD_CATEGORY
                        WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                        AND A.NAME_GBU NOT LIKE '%install%'
                        ORDER BY A.NAME_GBU";

                    $data['lista_gbu'] = $gbuModel->goCustomQuery($sql);

                    //Finalmente presentamos nuestra plantilla
                    $data['titulo'] = "MICRO-SEGMENTS > EDICI&Oacute;N";

                    $data['controller'] = "segments";
                    $data['action'] = "microSegmentsEdit";
                    $data['action_b'] = "microSegmentsDt";

                    $this->view->show("micro_segments_edit.php", $data);
            }
            else
            {
                    $this->microSegmentsDt(2);
            }
    }

    //PROCESS
    public function microSegmentsEdit()
    {
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu'], $_POST['old_code'], $_POST['old_nam'], $_POST['old_gbu']);
                    $code = $this->utils->cleanQuery($_POST['txtcodigo']);
                    $name = $this->utils->cleanQuery($_POST['txtnombre']);
                    $cod_gbu = $this->utils->cleanQuery($_POST['txtgbu']);
                    $old_code = $this->utils->cleanQuery($_POST['old_code']);
                    $old_name = $this->utils->cleanQuery($_POST['old_name']);
                    $old_gbu = $this->utils->cleanQuery($_POST['old_gbu']);

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $segmentModel = new SegmentsModel();

                    //Le pedimos al modelo todos los items
                    $result = $segmentModel->editMicroSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->microSegmentsDt(1);
                    else
                        $this->microSegmentsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                    $this->microSegmentsDt();
            }
    }

    /**
    * Get all sub segments in a serialized array
    * @return json 
    */
    public function listMicroSegmentsJSON()
    {
        //Incluye el modelo que corresponde
        require_once 'models/SegmentsModel.php';

        //Creamos una instancia de nuestro "modelo"
        $segmentModel = new SegmentsModel();

        if(isset($_GET['gbu']))
            $listado = $segmentModel->getAllMicroSegmentsByGbu ($_GET['gbu']);
        else
            $listado = $segmentModel->getAllMicroSegments ();

        $output = array();

        while ($row = $listado->fetch(PDO::FETCH_ASSOC))
        {
            $output[$row['COD_MICRO_SEGMENT']] = utf8_encode($row['NAME_MICRO_SEGMENT']);
        }

        $output['selected'] = utf8_encode($_GET['current']);

        echo json_encode( $output );
    }

    /*
    * Verify Segment Name (+ Sub & Micro)
    * AJAX
    */
    public function verifyNameSegment()
    {
        if($_REQUEST['txtnombre']){
            if(isset($_REQUEST['old_name'])){
                // Edicion
                if(mysql_real_escape_string($_REQUEST['txtnombre']) != mysql_real_escape_string($_REQUEST['old_name'])){
                    $input = mysql_real_escape_string($_REQUEST['txtnombre']);

                    if($_REQUEST['target'] == 1)
                        $sql = "SELECT name_segment FROM t_segment WHERE name_segment = '$input'";
                    elseif($_REQUEST['target'] == 2)
                        $sql = "SELECT name_sub_segment FROM t_sub_segment WHERE name_sub_segment = '$input'";
                    elseif($_REQUEST['target'] == 3)
                        $sql = "SELECT name_micro_segment FROM t_micro_segment WHERE name_micro_segment = '$input'";

                    //Incluye el modelo que corresponde
                    require_once 'models/SegmentsModel.php';
                    $model = new SegmentsModel();
                    $result = $model->goCustomQuery($sql);

                    if($result->rowCount() > 0)
                        echo "false";
                    else
                        echo "true";
                }
                else
                    echo "true";
            }
            else{
                // Nuevo
                $input = mysql_real_escape_string($_REQUEST['txtnombre']);

                if($_REQUEST['target'] == 1)
                    $sql = "SELECT name_segment FROM t_segment WHERE name_segment = '$input'";
                elseif($_REQUEST['target'] == 2)
                    $sql = "SELECT name_sub_segment FROM t_sub_segment WHERE name_sub_segment = '$input'";
                elseif($_REQUEST['target'] == 3)
                    $sql = "SELECT name_micro_segment FROM t_micro_segment WHERE name_micro_segment = '$input'";

                //Incluye el modelo que corresponde
                require_once 'models/SegmentsModel.php';
                $model = new SegmentsModel();
                $result = $model->goCustomQuery($sql);

                if($result->rowCount() > 0)
                    echo "false";
                else
                    echo "true";
            }
        }
        else
            echo "false";
    }
}
?>