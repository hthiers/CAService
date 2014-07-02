<?php
class TasksController extends ControllerBase
{
    /***************************************************************************
    * PROJECTS
    ***************************************************************************/
    
    /**
     * Show tasks dt
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
//        require_once 'models/ProjectsModel.php';
//        require_once 'models/UsersModel.php';
        require_once 'models/TasksModel.php';

        //Creamos una instancia de nuestro "modelo"
//        $projectsModel = new ProjectsModel();
//        $userModel = new UsersModel();
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
        $data['titulo'] = "Lista de Trabajos";

        $data['controller'] = "tasks";
        $data['action'] = "tasksView";
//        $data['action_b'] = "trabajosDt";

        //Posible error
        $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

        $this->view->show("tasks_dt.php", $data);
    }
    
    public function ajaxTasksDtX(){
        $session = FR_Session::singleton();
        require_once 'models/TasksModel.php';
        $taskModel = new TasksModel();
        
        $result = $taskModel->getAllTasksDynamic($_GET);
        $jsonResult = json_encode($result);
        
        echo json_encode($result);
        
        //mirar valores en pantalla de testing
//        $data['get'] = $_GET;
//        $data['resultado'] = $result;
//        $data['json'] = $jsonResult;
//        $this->view->show("test.php", $data);
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
    public function tasksView()
    {
        $session = FR_Session::singleton();
        $paused_date = null;

        $id_task = $_POST['id_task'];
        $session->id_task = $id_task;

        require_once 'models/TasksModel.php';
        $model = new TasksModel();

        $pdo = $model->getTaskById($session->id_tenant, $id_task);
        
        $values = $pdo->fetch(PDO::FETCH_ASSOC);
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

            #paused time
            if($values['date_pause'] != null){
                $total_progress = $total_progress - $values['time_paused'];
                
                $paused_date = Utils::diffDates($values['date_pause'], $values['date_ini'], 'S', false);
                $paused_date = $paused_date - $values['time_paused'];
            }
            
            #data
            $data['id_task'] = $values['id_task'];
            $data['code_task'] = $values['code_task'];
            $data['id_tenant'] = $values['id_tenant'];
            $data['label_task'] = $values['label_task'];
            $data['date_ini'] = $values['date_ini'];
            $data['date_end'] = $values['date_end'];
            $data['time_total'] = $values['time_total']; 
            $data['desc_task'] = $values['desc_task'];
            $data['date_pause'] = $values['date_pause'];
            $data['time_paused'] = $values['time_paused'];
            $data['status_task'] = $values['status_task'];
            $data['id_project'] = $values['cas_project_id_project'];
            $data['id_customer'] = $values['cas_customer_id_customer'];
            $data['id_user'] = $values['id_user'];
            $data['name_user'] = $values['name_user'];
            
            $data['total_progress'] = $total_progress;
            $data['paused_date'] = $paused_date;
            $data['currentTime'] = $current_date;
        }

        $data['titulo'] = "Tarea #";
        $data['pdo'] = $pdo;
        
        $this->view->show("tasks_view.php", $data);
    }

    /*
     * Show new project form 
     */
    public function tasksNewForm(){
        $session = FR_Session::singleton();

        require_once 'models/ProjectsModel.php';
        require_once 'models/TasksModel.php';
        require_once 'models/UsersModel.php';
        require_once 'models/CustomersModel.php';

        $model = new ProjectsModel();
        $modelTask = new TasksModel();
        $modelUser = new UsersModel();
        $modelCustomer = new CustomersModel();

        $pdo = $modelTask->getLastTask($session->id_tenant);
        $error = $pdo->errorInfo();
        $value = null;
        $value = $pdo->fetch(PDO::FETCH_ASSOC);

        if($error[0] != 00000){
            $new_code = 1;
            $data['error'] = "ERROR: ".$error[2];
        }
        elseif($value != null)
        {
            $last_code = $value['code_task'];
            $new_code = (int) $last_code + 1;
        }
        else
        {
            $new_code = 1;
            $data['error'] = "No hay tareas";
        }

        $data['new_code'] = $new_code;
        $data['titulo'] = "Nueva Tarea #".$new_code;

        $pdoUser = $modelUser->getUserAccountByID($session->id_user);
        $value = null;
        $value = $pdoUser->fetch(PDO::FETCH_ASSOC);

        if($value != null){
            $data['name_user'] = $value['name_user'];
            $data['id_user'] = $value['id_user'];
        }
        else{
            $data['name_user'] = "ERROR";
            $data['id_user'] = 0;
        }

        $pdoCustomer = $modelCustomer->getAllCustomers($session->id_tenant);
        $data['pdoCustomer'] = $pdoCustomer;
        
        $pdoProject = $model->getAllProjectsByTenant($session->id_tenant);
        $data['pdoProject'] = $pdoProject;

        #fecha actual
        $now = date("Y-m-d H:i:s");
        $currentDateTime = new DateTime($now);
        $timezone = new DateTimeZone($session->timezone);
        $currentDateTime = $currentDateTime->setTimezone($timezone);

        $data['current_date'] = $currentDateTime->format("Y-m-d");
        $data['current_time'] = $currentDateTime->format("H:i:s");

        $this->view->show("tasks_new.php", $data);
    }

    /*
     * Add project action
     */
    public function tasksAdd()
    {
        $session = FR_Session::singleton();
        $customer = null;
        $error_user = null;
        $error_cust = null;
        $id_created_task = null;
        $id_project = null;
        $id_customer = null;

        $new_code = $_POST['new_code'];
        $user = $_POST['resp'];
        $id_user = $_POST['id_user'];
        
        if(isset($_POST['cboprojects'])){
            if(is_numeric($_POST['cboprojects']) && $_POST['cboprojects'] > 0){
                $id_project = $_POST['cboprojects'];
            }
        }
        
//        if(isset($_POST['cbocustomer']))
//            $customer = $_POST['cbocustomer'];
        
        $desc = $_POST['descripcion'];
        $fecha = $_POST['fecha'];
        $hora_ini = $_POST['hora_ini'];
        $etiqueta = $_POST['etiqueta'];
        $estado = 1; #active by default

//        require_once 'models/ProjectsModel.php';
        require_once 'models/TasksModel.php';

//        $model = new ProjectsModel();
        $model = new TasksModel();
//        $result = $model->addNewProject($session->id_tenant, $new_code, $etiqueta, $hora_ini, $fecha, $desc);
        $result = $model->addNewTask($session->id_tenant,$new_code,$etiqueta,$fecha, $hora_ini, null,null,$desc,$estado,$id_project, $id_customer);
        
        $query = $result->queryString;
        
        $error = $result->errorInfo();
        $rows_n = $result->rowCount();

        if($error[0] == 00000 && $rows_n > 0){
//            $id_new_project = $model->getProjectIDByCodeINT($new_code, $session->id_tenant); 
            $result = $model->getTaskIDByCode($session->id_tenant, $new_code);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
//            $result_user = $model->addUserToProject($id_new_project, $session->id_user);            
            $result_user = $model->addUserToTask($values['id_task'], $id_user);
            $error_user = $result_user->errorInfo();
            
            #customer movido a pop-up de nuevo project
//            if($customer != null){
//                $result_cust = $model->addCustomerToProject($id_new_project, $customer);
//                $error_cust = $result_cust->errorInfo();
//            }
            
            #$this->projectsDt(1);
            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=1");
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            #$this->projectsDt(10, "Ha ocurrido un error grave!");
            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=10&message='Ha ocurrido un error grave'");
        }
        else{
            #$this->projectsDt(10, "Ha ocurrido un error: ".$error[2]);
//            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=10&message='error sql: ".$query."'");
            header("Location: ".$this->root."?controller=Tasks&action=tasksDt&error_flag=10&message='Ha ocurrido un error: ".$error[2]."'");
        }
    }

    public function ajaxTaskAdd()
    {   
        $session = FR_Session::singleton();

        $label = $_POST['label'];
        $desc = $_POST['desc'];
        $new_code = $_POST['new_code'];
        $status = 1; // 1 by default
        $project = null;
        
        if(isset($_POST['cboproject']))
            $project = $_POST['cboproject'];
        
        #current time
        $now = date("Y-m-d H:i:s");
        $currentDateTime = new DateTime($now);
        $timezone = new DateTimeZone($session->timezone);
        $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");
        
        #$code_customer = rand(1, 100);
        #$code_customer = "c".$code_customer;
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';

        $modelProject = new ProjectsModel();
        $result = $modelProject->getLastProject($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code = $values['code_project'];
        $code = (int)$code + 1;
        
        $result = $modelProject->addNewProject($session->id_tenant, $code, 'Sin Proyecto #'.$code, null, null, 'Sin Proyecto #'.$code);
        
        $modelTask = new TasksModel();
        
        $result = $modelTask->getLastTask($session->id_tenant);
        $values = $result->fetch(PDO::FETCH_ASSOC);
        $code = $values['code_task'];
        $code = (int)$code + 1;
        $new_task[] = null;
                
        $result = $modelTask->addNewTask($session->id_tenant, $code, $label, $current_date, null, $desc, $status);

        $error = $result->errorInfo();
        $rows_n = $result->rowCount();
        
        if($error[0] == 00000 && $rows_n > 0){
            $result = $modelTask->getLastTask($session->id_tenant);
            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            $id_task = $values['id_task'];
            
            $new_task[0] = $id_task;
            $new_task[1] = $label_task;
        }
        elseif($error[0] == 00000 && $rows_n < 1){
            $new_task[0] = "0";
            $new_task[1] = "No se ha podido ingresar el registro";
        }
        else{
            $new_task[0] = "0";
            $new_task[1] = $error[2];
        }

        print json_encode($new_task);
        
        return true;
    }

    public function tasksPause()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';
        
//        $model = new ProjectsModel();
        $modelTask = new TasksModel();
//        $pdoProject = $model->getProjectById($id_project, $session->id_tenant);
        $pdoTask = $modelTask->getTaskById($session->id_tenant, $id_task);
        $error = null;
        $response = null;
        $total_real_time = null;
        
        $values = $pdoTask->fetch(PDO::FETCH_ASSOC);
        if($values != null && $values != false){
            // current time
            $now = date("Y-m-d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");

            // total time (s)
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S');
            
            // total real time (s)
            if($values['time_paused'] != null && empty($values['time_paused']) == false){
                $total_real_progress = $total_progress - $values['time_paused'];
            }
            else
                $total_real_progress = $total_progress;

            //paused status = 3
            $status = 3;

            //pause project
            $result = $modelTask->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], null, null, $values['desc_task']
                    , $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $current_date, $values['time_paused']);

            if($result != null){
                $error = $result->errorInfo();
                $num_filas = $result->rowCount();
                if($error[0] == 00000){
                    $response[0] = "0";
                    $response[1] = "Exito!";
                    $response[2] = "filas: ".$num_filas;
                    $response[3] = $result->queryString;
                }
                else {
                    $response[0] = $error[0];
                    $response[1] = $error[2];
                    $response[2] = $result->queryString;
                }
            }
            else{
                $response[0] = "1";
                $response[1] = "Error grave al intentar actualizar el proyecto";
            }
        }
        else{
            $response[0] = "2";
            $response[1] = "Error grave al intentar encontrar el proyecto pedido (ID no existe).";
        }

        print json_encode($response);
    }
    
    public function tasksContinue()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        
        require_once 'models/TasksModel.php';
        require_once 'models/ProjectsModel.php';
        
//        $model = new ProjectsModel();
        $model = new TasksModel();
//        $pdoProject = $model->getProjectById($id_project, $session->id_tenant);
        $pdoModel = $model->getTaskById($session->id_tenant, $id_task);
        $error = null;
        $response = null;
        
        if($pdoModel != null){
            $values = $pdoModel->fetch(PDO::FETCH_ASSOC);
            if($values != false){
                // current time
                $now = date("Y-m-d H:i:s");
                $currentDateTime = new DateTime($now);
                $timezone = new DateTimeZone($session->timezone);
                $current_date = $currentDateTime->setTimezone($timezone)->format("Y-m-d H:i:s");

                // current progress
                $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S', false);

                // paused progress
                $paused_progress = Utils::diffDates($current_date, $values['date_pause'], 'S', false);
                if($values['time_paused'] != null)
                    $paused_progress += $values['time_paused'];

                //normal status = 1
                $status = 1;

    //            print(Utils::formatTime($total_progress));
    //            print("<br>");
    //            print(Utils::formatTime($paused_progress));

                //pause project
                $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                        , $values['label_task'], $values['date_ini'], null
                        , null, $values['desc_task'], $status, null, null
                        , $values['date_pause'], $paused_progress);

                if($result != null){
                    $error = $result->errorInfo();
                    if($error[0] == 00000){
                        $response[0] = "0";
                        $response[1] = "Exito!";
                    }
                    else {
                        $response[0] = $error[0];
                        $response[1] = $error[2];
                    }
                }
                else{
                    $response[0] = "1";
                    $response[1] = "Error grave al intentar actualizar el proyecto";
                }
            }
            else{
                $errorSearch = $pdoModel->errorInfo();
                $response[0] = "2";
                $response[1] = "Error FETCH: ".print_r($values);
            }
        }
        else{
            $response[0] = "2";
            $response[1] = "Error PDO NULO";
        }

        print json_encode($response);
    }
    
    /*
     * Stop task progress
     */
    public function tasksStop()
    {
        $session = FR_Session::singleton();
        $id_task = $_REQUEST['id_task'];
        $id_project = $_REQUEST['id_project'];
//        $total_real_time = null;
        
        if($id_task != null){
            require_once 'models/TasksModel.php';
            $model = new TasksModel();

            $pdoTask = $model->getTaskById($session->id_tenant, $id_task);
            $values = $pdoTask->fetch(PDO::FETCH_ASSOC);

            // current time
            $now = date("Y-m-d H:i:s");
            $currentDateTime = new DateTime($now);
            $timezone = new DateTimeZone($session->timezone);
            $currentDateTime = $currentDateTime->setTimezone($timezone);
            $current_date = $currentDateTime->format("Y-m-d H:i:s");
            
            // total time (s)
            $total_progress = Utils::diffDates($current_date, $values['date_ini'], 'S');
            
            // total real time (s)
            if($values['time_paused'] != null && empty($values['time_paused']) == false){
                $total_progress = $total_progress - $values['time_paused'];
            }
            else
                $total_progress = $total_progress;
            
            #tiempo pausa
//            $paused_time = Utils::diffDates($stop_date, $values['date_pause'], 'S', FALSE);
//            if($values['time_paused'] != null)
//                $paused_time += $values['time_paused'];

            #tiempo total
//            $last_time = Utils::diffDates($stop_date, $values['date_ini'], 'S', FALSE);
//            $total_time = $last_time - $paused_time;
            
            //stop status
            $status = 2;
            
            #stop tarea
            $result = $model->updateTask($session->id_tenant, $id_task, $values['code_task']
                    , $values['label_task'], $values['date_ini'], $current_date, $total_progress
                    , $values['desc_task'], $status, $values['cas_project_id_project'], $values['cas_customer_id_customer']
                    , $values['date_pause'], $values['time_paused']);

            if($result != null){
                $error = $result->errorInfo();
                $numr = $result->rowCount();

                if($error[0] == 00000 && $numr > 0){
                    #$this->projectsDt(1);
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=1");
                }
                else{
                    #$this->projectsDt(10, "Ha ocurrido un error o no se lograron aplicar cambios: ".$error[2]);
                    header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='No se lograron aplicar cambios: ".$error[2]."'");
                }
            }
            else{
                #$this->projectsDt(10, "Ha ocurrido un error grave!");
                header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: actualizacion fallida!");
            }
        }
        else{
            #$this->projectsDt(10, "Error, el proyecto no ha sido encontrado.");
            header("Location: ".$this->root."?controller=tasks&action=tasksDt&error_flag=10&message='Error: no existe tarea!");
        }
    }
    
}
?>