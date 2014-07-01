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
                    , b.id_project
                    , b.label_project
                    , c.id_customer
                    , c.label_customer
                    , e.id_user
                    , e.name_user
                FROM  cas_task a
                LEFT OUTER JOIN cas_project b
                ON (a.cas_project_id_project = b.id_project
                    AND 
                    a.id_tenant = b.id_tenant)
                LEFT OUTER JOIN cas_customer c
                ON (a.cas_customer_id_customer = c.id_customer
                    AND 
                    a.id_tenant = b.id_tenant)
                LEFT OUTER JOIN cas_task_has_cas_user d
                ON a.id_task = d.cas_task_id_task
                LEFT OUTER JOIN cas_user e
                ON d.cas_user_id_user = e.id_user
                WHERE a.id_tenant = $id_tenant");

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
                    , c.id_user
                    , c.name_user
                    , a.date_pause
                    , a.time_paused
                FROM  cas_task a
                LEFT OUTER JOIN cas_task_has_cas_user b
                ON a.id_task = b.cas_task_id_task
                LEFT OUTER JOIN cas_user c
                ON b.cas_user_id_user = c.id_user
                WHERE a.id_tenant = ?
                  AND a.id_task = ?
                ORDER BY a.label_task
                LIMIT 1");

        if($consulta->execute(array($id_tenant, $id_task)))
            return $consulta;
        else
            return null;
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
            , $date_ini, $hora_ini, $date_end, $time_total, $descripcion
            , $estado = 1, $id_project, $id_customer)
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
                        , '$date_ini. .$hora_ini', $date_end, $time_total, '$descripcion'
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
            , $init_date, $stop_date, $total_time, $desc, $status, $id_project, $id_customer
            , $date_pause, $time_paused)
    {
        // force null values
        $stop_date = empty($stop_date) ? "NULL" : "'$stop_date'";
        $total_time = empty($total_time) ? "NULL" : "'$total_time'";
        $id_project = empty($id_project) ? "NULL" : "'$id_project'";
        $id_customer = empty($id_customer) ? "NULL" : "'$id_customer'";
        $date_pause = empty($date_pause) ? "NULL" : "'$date_pause'";
        $time_paused = empty($time_paused) ? "NULL" : "'$time_paused'";
        
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
                    , date_pause = $date_pause
                    , time_paused = $time_paused
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

    public function getAllTasksDynamic($request){
        //Tabla en BBDD
        $table = 'cas_task';

        //Llave primaria
        $primaryKey = 'id_task';
        
        //Columnas a Exportar
        $columns = array(
            array( 'db' => 'a.label_task', 'dt' => 0 ),
            array( 'db' => 'c.label_customer',  'dt' => 1 ),
            array( 'db' => 'e.name_user',   'dt' => 2 ),
            array( 'db' => 'b.label_project',     'dt' => 3 ),
            array( 'db' => 'a.date_ini',     'dt' => 4 ),
            array( 'db' => 'a.date_end',     'dt' => 5 ),
            array( 'db' => 'a.time_total',     'dt' => 6 ),
            array( 'db' => 'a.id_task',     'dt' => 7 ),
            array( 'db' => 'a.id_tenant',     'dt' => 8 ),
            array( 'db' => 'b.id_project',     'dt' => 9 ),
            array( 'db' => 'c.id_customer',     'dt' => 10 ),
            array( 'db' => 'e.id_user',     'dt' => 11 )
        );
        
        //Tablas para join
        $joinString = "LEFT OUTER JOIN cas_project b
            ON (a.cas_project_id_project = b.id_project
                    AND 
                a.id_tenant = b.id_tenant)
            LEFT OUTER JOIN cas_customer c
            ON (a.cas_customer_id_customer = c.id_customer
                    AND 
                a.id_tenant = b.id_tenant)
            LEFT OUTER JOIN cas_task_has_cas_user d
            ON a.id_task = d.cas_task_id_task
            LEFT OUTER JOIN cas_user e
            ON d.cas_user_id_user = e.id_user";
        
        require_once 'AjaxModelsModel.php';
        $ajaxModel = new AjaxModelModel();
        
        $sql = $ajaxModel->buildQueryString($request, $table, $columns, $joinString);
        #$sql = $ajaxModel->test($request);

        //Get data
        $pdoData = $this->db->prepare($sql);
        $pdoData->execute();
        #$resultData = $pdoData->fetch(PDO::FETCH_BOTH);
        $resultData = $pdoData->fetch(PDO::FETCH_NUM);
        #$error = $result->errorInfo();
        #$value = $result->fetch(PDO::FETCH_ASSOC);

        //Get found by filters
        $pdoFound = $this->db->prepare("SELECT FOUND_ROWS()");
        $pdoFound->execute();
        $resFilterLength = $pdoFound->fetch(PDO::FETCH_BOTH);
        $recordsFiltered = $resFilterLength[0];

        //Get total data
        $pdoTotal = $this->db->prepare("SELECT COUNT($primaryKey) FROM $table");
        $pdoTotal->execute();
        $resTotalLength = $pdoTotal->fetch(PDO::FETCH_BOTH);
        $recordsTotal = $resTotalLength[0];

        $result_array = array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => $ajaxModel->dataOutputX(count($columns), $pdoData)
        );
        
        #$ajaxModel->dataOutput($columns, $resultData)

        return $result_array;
        #return $sql;

        // Main query to actually get the data
        /*$data = SSP::sql_exec( $db, $bindings,
            "SELECT SQL_CALC_FOUND_ROWS '".implode("', '", SSP::pluck($columns, 'db'))."'
            FROM '$table' a
            $joins
            $where
            $order
            $limit"
        );*/

        // Data set length after filtering
        #$resFilterLength = SSP::sql_exec($db, "SELECT FOUND_ROWS()");
        #$recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        #$resTotalLength = SSP::sql_exec($db, "SELECT COUNT('{$primaryKey}') FROM '$table'");
        #$recordsTotal = $resTotalLength[0][0];

        // Output
        /*return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => SSP::data_output( $columns, $data )
        );*/
    }
}
?>