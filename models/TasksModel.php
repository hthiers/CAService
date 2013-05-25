<?php
class TasksModel extends ModelBase
{
    /*******************************************************************************
    * Tasks
    *******************************************************************************/

    /**
     * Get all tasks under a project by tenant
     * @param type $id_tenant
     * @param type $id_project
     * @return PDO
     */
    public function getAllTasksByTenantProject($id_tenant, $id_project)
    {
        //realizamos la consulta de todos los segmentos
        $consulta = $this->db->prepare("
                SELECT 
                    a.id_task
                    , b.cas_project_id_project
                    , a.code_task
                    , a.id_tenant
                    , a.label_task
                    , IFNULL(a.date_ini, 'n/a') as date_ini
                    , IFNULL(a.date_end, 'n/a') as date_end
                    , IFNULL(a.time_total, 'n/a') as time_total
                    , IFNULL(a.desc_task, 'n/a') as desc_task
                    , a.status_task
                    , a.cas_project_id_project
                    , a.cas_customer_id_customer
                FROM  cas_task a
                INNER JOIN cas_project_has_cas_task b
                ON a.id_task = b.cas_task_id_task
                WHERE a.id_tenant = $id_tenant
                  AND b.cas_project_id_project = $id_project
                ORDER BY a.label_task");

        $consulta->execute();

        //devolvemos la coleccion para que la vista la presente.
        return $consulta;
    }

    /**
     * Get all tasks by tenant
     * @param type $id_tenant
     * @return PDO
     */
    public function getAllTasksByTenant($id_tenant)
    {
        $consulta = $this->db->prepare("
                SELECT 
                    a.id_task
                    , a.code_task
                    , a.id_tenant
                    , a.label_task
                    , a.date_ini
                    , a.date_end
                    , a.time_total
                    , a.desc_task
                    , a.status_task
                    , a.cas_project_id_project
                    , a.cas_customer_id_customer
                FROM  cas_task a
                WHERE a.id_tenant = $id_tenant
                ORDER BY a.label_task");

        $consulta->execute();

        //devolvemos la coleccion para que la vista la presente.
        return $consulta;
    }

    /**
     * Get a task by ID
     * @param type $id_tenant
     * @param type $id_task
     * @return PDO
     */
    public function getTaskById($id_tenant, $id_task)
    {
        $consulta = $this->db->prepare("
                SELECT 
                    a.id_task
                    , a.code_task
                    , a.id_tenant
                    , a.label_task
                    , a.date_ini
                    , a.date_end
                    , a.time_total
                    , a.desc_task
                    , a.status_task
                    , a.cas_project_id_project
                    , a.cas_customer_id_customer
                FROM  cas_task a
                WHERE a.id_tenant = $id_tenant
                  AND a.id_task = $id_task
                ORDER BY a.label_task
                LIMIT 1");

        $consulta->execute();

        return $consulta;
    }

    /**
     * Get last existent task by tenant
     * @param type $id_tenant
     * @return PDO
     */
    public function getLastTask($id_tenant)
    {
        //get last segment
        $consulta = $this->db->prepare("
                SELECT 
                   a.id_task
                    , a.code_task
                    , a.id_tenant
                    , a.label_task
                    , a.date_ini
                    , a.date_end
                    , a.time_total
                    , a.desc_task
                    , a.status_task
                    , a.cas_project_id_project
                    , a.cas_customer_id_customer
                FROM  cas_task a
                INNER JOIN cas_tenant b
                ON a.id_tenant = b.id_tenant
                WHERE b.id_tenant = $id_tenant
                ORDER BY a.id_task DESC
                LIMIT 1");

        $consulta->execute();

        return $consulta;
    }

    /**
     * Get a task by its code and tenant
     * @param type $id_tenant
     * @param type $code_task
     * @return PDO
     */
    public function getTaskByCode($id_tenant, $code_task)
    {
        $consulta = $this->db->prepare("
                SELECT 
                     a.id_task
                    , a.code_task
                    , a.id_tenant
                    , a.label_task
                    , a.date_ini
                    , a.date_end
                    , a.time_total
                    , a.desc_task
                    , a.status_task
                    , a.cas_project_id_project
                    , a.cas_customer_id_customer
                FROM  cas_task A
                INNER JOIN cas_tenant B
                ON A.id_tenant = B.id_tenant
                WHERE B.id_tenant = $id_tenant
                  AND A.code_task = $code_task
                LIMIT 1");

        $consulta->execute();

        return $consulta;
    }

    /**
     * Get a task ID by its code and tenant
     * @param type $id_tenant
     * @param type $code_task
     * @return PDO
     */
    public function getTaskIDByCode($id_tenant, $code_task)
    {
        $consulta = $this->db->prepare("
                SELECT 
                    A.id_task
                FROM  cas_task A
                INNER JOIN cas_tenant B
                ON A.id_tenant = B.id_tenant
                WHERE B.id_tenant = $id_tenant
                  AND A.code_task = $code_task
                LIMIT 1");

        $consulta->execute();

        return $consulta;
    }

    /**
     * Get a task ID value by its code and tenant
     * @param type $id_tenant
     * @param type $code_task
     * @return int
     */
    public function getPTaskIDByCodeINT($id_tenant, $code_task)
    {
        $consulta = $this->db->prepare("
                SELECT 
                    A.id_task
                FROM  cas_task A
                INNER JOIN cas_tenant B
                ON A.id_tenant = B.id_tenant
                WHERE B.id_tenant = $id_tenant
                  AND A.code_task = $code_task
                LIMIT 1");

        $consulta->execute();

        $row = $consulta->fetch(PDO::FETCH_ASSOC);

        return $row['id_task'];
    }

    /**
     * Add new task
     * @param type $id_tenant
     * @param type $new_code
     * @param type $etiqueta
     * @param type $date_ini
     * @param type $date_end
     * @param type $time_total
     * @param type $descripcion
     * @param type $estado
     * @param type $id_project
     * @param type $id_customer
     * @return PDO
     */
    public function addNewTask($id_tenant, $new_code, $etiqueta
            , $date_ini, $date_end, $time_total, $descripcion, $estado = 1, $id_project, $id_customer)
    {
        // force null values
        $date_end = empty($date_end) ? "NULL" : "'$date_end'";
        $time_total = empty($time_total) ? "NULL" : "'$time_total'";
        $id_project = empty($id_project) ? "NULL" : "'$id_project'";
        $id_customer = empty($id_customer) ? "NULL" : "'$id_customer'";
        
        $consulta = $this->db->prepare("INSERT INTO cas_task 
                    (id_task, code_task, id_tenant, label_task
                    , date_ini, date_end, time_total, desc_task
                    , status_task, cas_project_id_project, cas_customer_id_customer) 
                        VALUES 
                    (NULL, '$new_code', $id_tenant, '$etiqueta'
                        , '$date_ini', $date_end, $time_total, '$descripcion'
                        , $estado, $id_project, $id_customer)");

        $consulta->execute();

        return $consulta;
    }
    
    /**
     * Add user to task (allows multiple users in one task)
     * @param type $id_task
     * @param type $id_user
     * @return type
     */
    public function addUserToTask($id_task, $id_user)
    {
        $consulta = $this->db->prepare("INSERT INTO cas_task_has_cas_user 
                (cas_task_id_task, cas_user_id_user) 
                    VALUES 
                ($id_task, $id_user)");

        $consulta->execute();

        return $consulta;
    }

    /**
     * Update existent task
     * @param type $id_tenant
     * @param type $id_task
     * @param type $code_task
     * @param type $etiqueta
     * @param type $init_date
     * @param type $stop_date
     * @param type $total_time
     * @param type $desc
     * @return PDO
     */
    public function updateTask($id_tenant, $id_task, $code_task, $etiqueta
            , $init_date, $stop_date, $total_time, $desc, $status, $id_project, $id_customer)
    {
        // force null values
        $stop_date = empty($stop_date) ? "NULL" : "'$stop_date'";
        $total_time = empty($total_time) ? "NULL" : "'$total_time'";
        $id_project = empty($id_project) ? "NULL" : "'$id_project'";
        $id_customer = empty($id_customer) ? "NULL" : "'$id_customer'";
        
        $consulta = $this->db->prepare("UPDATE cas_task 
                    SET
                    code_task = '$code_task'
                    , label_task = '$etiqueta'
                    , date_ini = '$init_date'
                    , date_end = $stop_date
                    , time_total = $total_time
                    , desc_task = '$desc'
                    , status_task = '$status'
                    , cas_project_id_project = $id_project
                    , cas_customer_id_customer = $id_customer
                    WHERE id_tenant = $id_tenant
                      AND id_task = $id_task");

        $consulta->execute();

        return $consulta;
    }

//        public function addUserToProject($id_project, $id_user)
//        {
//            $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_user 
//                    (cas_project_id_project, cas_user_id_user) 
//                        VALUES 
//                    ($id_project, $id_user)");
//            
//            $consulta->execute();
//
//            return $consulta;
//        }

//        public function addCustomerToProject($id_project, $id_customer)
//        {
//            $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_customer 
//                    (cas_project_id_project, cas_customer_id_customer) 
//                        VALUES 
//                    ($id_project, $id_customer)");
//            
//            $consulta->execute();
//
//            return $consulta;
//        }


    /**
     * Get PDO object from custom sql query
     * NOTA: Esta función permite seguir el patrón de modelo.
     * @param string $sql
     * @return PDO
     */
    public function goCustomQuery($sql)
    {
        $consulta = $this->db->prepare($sql);

        $consulta->execute();

        return $consulta;
    }


    /********************************
     * OLD STUFF
     ********************************
     */

//    //GET ALL SEGMENTS
//    public function getAllSegmentsSimple()
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT 
//                    DISTINCT
//                    a.COD_SEGMENT
//                    , a.NAME_SEGMENT
//                FROM  t_segment a
//                ORDER BY a.NAME_SEGMENT");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //GET SEGMENT BY NAME
//    public function getSegmentByName($name_segment)
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT 
//                    a.COD_SEGMENT
//                    , a.NAME_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_segment a
//                INNER JOIN t_gbu b 
//                ON a.COD_GBU = b.COD_GBU
//                WHERE A.NAME_SEGMENT = '$name_segment'");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    /**
//     * Get array of segments by COD_GBU
//     * @param string $cod_gbu
//     * @return PDO
//     */
//    public function getAllSegmentsByGbu($cod_gbu)
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT 
//                    a.COD_SEGMENT
//                    , a.NAME_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_segment a
//                INNER JOIN t_gbu b 
//                ON a.COD_GBU = b.COD_GBU
//                WHERE A.COD_GBU = '$cod_gbu'");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//
//
//
//
//    //EDIT SEGMENT
//    public function editSegment($cod_segment, $name_segment, $cod_gbu, $old_cod_segment, $old_name_segment, $old_gbu)
//    {
//        require_once 'AdminModel.php';
//        $logModel = new AdminModel();
//        $sql = "UPDATE t_segment WHERE '$cod_segment'";
//
//        $session = FR_Session::singleton();
//
//        $consulta = $this->db->prepare("UPDATE t_segment
//                        SET 
//                            NAME_SEGMENT = '$name_segment'
//                            , COD_GBU = '$cod_gbu'
//                        WHERE COD_SEGMENT = '$old_cod_segment'
//                            AND COD_GBU = '$old_gbu'");
//
//        $consulta->execute();
//
//        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//        $logModel->addNewEvent($session->usuario, $sql, 'SEGMENTS');
//
//        return $consulta;
//    }
//
//
//    /*******************************************************************************
//    * SUB SEGMENTS
//    *******************************************************************************/
//
//    //GET ALL SUB SEGMENTS
//    public function getAllSubSegments()
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT a.COD_SUB_SEGMENT
//                    , a.NAME_SUB_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_sub_segment AS a
//                INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //GET NEW SUB SEGMENT CODE
//    public function getNewSubSegmentCode()
//    {
//        //get last sub segment
//        $consulta = $this->db->prepare("SELECT COD_SUB_SEGMENT 
//                        FROM t_sub_segment 
//                        WHERE COD_SUB_SEGMENT NOT LIKE '%N/A%' 
//                        ORDER BY COD_SUB_SEGMENT DESC LIMIT 1");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //GET SUB SEGMENT BY COD_GBU
//    public function getSubSegmentsByGbu($cod_gbu)
//    {
//        //get last sub segment
//        $consulta = $this->db->prepare("
//                SELECT a.COD_SUB_SEGMENT
//                    , a.NAME_SUB_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_sub_segment AS a
//                INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
//                WHERE A.COD_GBU = '$cod_gbu'");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //ADD SUB SEGMENT
//    public function addNewSubSegment($code, $name, $cod_gbu)
//    {
//        require_once 'AdminModel.php';
//        $logModel = new AdminModel();
//        $sql = "INSERT INTO t_sub_segment VALUES '$code', '$name'";
//
//        $session = FR_Session::singleton();
//
//        $consulta = $this->db->prepare("INSERT INTO t_sub_segment 
//                (COD_SUB_SEGMENT, NAME_SUB_SEGMENT, COD_GBU) 
//                VALUES ('$code','$name','$cod_gbu')");
//        $consulta->execute();
//
//        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//        $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');
//
//        return $consulta;
//    }
//
//    //EDIT SUB SEGMENT
//    public function editSubSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
//    {
//        require_once 'AdminModel.php';
//        $logModel = new AdminModel();
//        $sql = "UPDATE t_sub_segment WHERE '$code'";
//
//        $session = FR_Session::singleton();
//
//        $consulta = $this->db->prepare("UPDATE t_sub_segment
//                            SET 
//                                NAME_SUB_SEGMENT = '$name'
//                                , COD_GBU = '$cod_gbu'
//                            WHERE COD_SUB_SEGMENT = '$old_code'
//                                AND COD_GBU = '$old_gbu'");
//
//        $consulta->execute();
//
//        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//        $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');
//
//        return $consulta;
//    }
//
//
//    /*******************************************************************************
//    * MICRO SEGMENTS
//    *******************************************************************************/
//
//    //GET ALL MICRO SEGMENTS
//    public function getAllMicroSegments()
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT a.COD_MICRO_SEGMENT
//                    , a.NAME_MICRO_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_micro_segment AS a
//                INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //GET ALL MICRO SEGMENTS BY COD_GBU
//    public function getAllMicroSegmentsByGbu($cod_gbu)
//    {
//        //realizamos la consulta de todos los segmentos
//        $consulta = $this->db->prepare("
//                SELECT a.COD_MICRO_SEGMENT
//                    , a.NAME_MICRO_SEGMENT
//                    , b.COD_GBU AS GBU_COD_GBU
//                    , b.NAME_GBU AS GBU_NAME_GBU
//                FROM  t_micro_segment AS a
//                INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
//                WHERE A.COD_GBU = '$cod_gbu'");
//
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //GET NEW MICRO SEGMENT CODE
//    public function getNewMicroSegmentCode()
//    {
//        //get last sub segment
//        $consulta = $this->db->prepare("SELECT COD_MICRO_SEGMENT FROM t_micro_segment
//                    WHERE COD_MICRO_SEGMENT NOT LIKE '%N/A%' 
//                    ORDER BY COD_MICRO_SEGMENT DESC LIMIT 1");
//        $consulta->execute();
//
//        //devolvemos la coleccion para que la vista la presente.
//        return $consulta;
//    }
//
//    //ADD MICRO SEGMENT
//    public function addNewMicroSegment($code, $name, $cod_gbu)
//    {
//        require_once 'AdminModel.php';
//        $logModel = new AdminModel();
//        $sql = "INSERT INTO t_micro_segment VALUES '$code','$name'";
//
//        $session = FR_Session::singleton();
//
//        $consulta = $this->db->prepare("INSERT INTO t_micro_segment 
//                (COD_MICRO_SEGMENT, NAME_MICRO_SEGMENT, COD_GBU) 
//                VALUES ('$code','$name','$cod_gbu')");
//        $consulta->execute();
//
//        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//        $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');
//
//        return $consulta;
//    }
//
//    //EDIT MICRO SEGMENT
//    public function editMicroSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
//    {
//        require_once 'AdminModel.php';
//        $logModel = new AdminModel();
//        $sql = "UPDATE t_micro_segment WHERE '$code'";
//
//        $session = FR_Session::singleton();
//
//        $consulta = $this->db->prepare("UPDATE t_micro_segment
//                    SET 
//                            NAME_MICRO_SEGMENT = '$name'
//                            , COD_GBU = '$cod_gbu'
//                    WHERE COD_MICRO_SEGMENT = '$old_code'
//                        AND COD_GBU = '$old_gbu'");
//
//        $consulta->execute();
//
//        //Save log
//        $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');
//
//        return $consulta;
//    }
}
?>