<?php
class ProjectsModel extends ModelBase
{
	/*******************************************************************************
	* Projects
	*******************************************************************************/
	
	public function getAllProjectsByTenant($id_tenant)
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                        a.id_project
                        , a.code_project
                        , a.id_tenant
                        , IFNULL(c.id_user, '') as id_user
                        , IFNULL(c.code_user, '') as code_user
                        , IFNULL(c.name_user, '') as name_user
                        , IFNULL(e.id_customer, '') as id_customer
                        , IFNULL(e.label_customer, '') as name_customer
                        , a.label_project
                        , a.date_ini
                        , a.date_end
                        , a.time_total
                        , a.desc_project
                        , a.status_project
                    FROM  cas_project a
                    LEFT OUTER JOIN cas_project_has_cas_user b
                    ON a.id_project = b.cas_project_id_project
                    LEFT OUTER JOIN cas_user c
                    ON (b.cas_user_id_user = c.id_user
                        AND
                        c.id_tenant = $id_tenant)
                    LEFT OUTER JOIN cas_project_has_cas_customer d
                    ON a.id_project = d.cas_project_id_project
                    LEFT OUTER JOIN cas_customer e
                    ON d.cas_customer_id_customer = e.id_customer
                    WHERE a.id_tenant = $id_tenant
                    ORDER BY a.label_project");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        /**
         * Get project by id and tenant
         * @param type $id_project
         * @param int $id_tenant
         * @return type PDO
         */
	public function getProjectById($id_project, $id_tenant)
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        a.id_project
                        , a.code_project
                        , a.id_tenant
                        , IFNULL(c.id_user, '') as id_user
                        , IFNULL(c.code_user, '') as code_user
                        , IFNULL(c.name_user, '') as name_user
                        , IFNULL(e.id_customer, '') as id_customer
                        , IFNULL(e.label_customer, '') as label_customer
                        , a.label_project
                        , a.date_ini
                        , a.date_end
                        , a.time_total
                        , a.desc_project
                        , a.status_project
                    FROM  cas_project a
                    LEFT OUTER JOIN cas_project_has_cas_user b
                    ON a.id_project = b.cas_project_id_project
                    LEFT OUTER JOIN cas_user c
                    ON (b.cas_user_id_user = c.id_user
                        AND
                        c.id_tenant = $id_tenant)
                    LEFT OUTER JOIN cas_project_has_cas_customer d
                    ON a.id_project = d.cas_project_id_project
                    LEFT OUTER JOIN cas_customer e
                    ON d.cas_customer_id_customer = e.id_customer
                    WHERE a.id_tenant = $id_tenant
                      AND a.id_project = $id_project");

            $consulta->execute();

            return $consulta;
	}
        
        
	public function getLastProject($id_tenant)
	{
            //get last segment
            $consulta = $this->db->prepare("
                    SELECT 
                        A.id_project
                        , A.code_project
                        , B.id_tenant
                        , A.label_project
                        , A.desc_project
                        , A.status_project
                    FROM  cas_project A
                    INNER JOIN cas_tenant B
                    ON A.id_tenant = B.id_tenant
                    WHERE B.id_tenant = $id_tenant
                    ORDER BY A.code_project DESC
                    LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        public function getProjectByCode($code_project, $id_tenant)
        {
            $consulta = $this->db->prepare("
                    SELECT 
                        A.id_project
                        , A.code_project
                        , B.id_tenant
                        , A.label_project
                        , A.desc_project
                        , A.status_project
                    FROM  cas_project A
                    INNER JOIN cas_tenant B
                    ON A.id_tenant = B.id_tenant
                    WHERE B.id_tenant = $id_tenant
                      AND A.code_project = $code_project
                    LIMIT 1");

            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Get project ID by project CODE
         * @param type $code_project
         * @param type $id_tenant
         * @return PDO
         */
        public function getProjectIDByCode($code_project, $id_tenant)
        {
            $consulta = $this->db->prepare("
                    SELECT 
                        A.id_project
                    FROM  cas_project A
                    INNER JOIN cas_tenant B
                    ON A.id_tenant = B.id_tenant
                    WHERE B.id_tenant = $id_tenant
                      AND A.code_project = $code_project
                    LIMIT 1");

            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Get project ID by CODE
         * @param type $code_project
         * @param type $id_tenant
         * @return Integer
         */
        public function getProjectIDByCodeINT($code_project, $id_tenant)
        {
            $consulta = $this->db->prepare("
                    SELECT 
                        A.id_project
                    FROM  cas_project A
                    INNER JOIN cas_tenant B
                    ON A.id_tenant = B.id_tenant
                    WHERE B.id_tenant = $id_tenant
                      AND A.code_project = $code_project
                    LIMIT 1");

            $consulta->execute();

            $row = $consulta->fetch(PDO::FETCH_ASSOC);
            
            return $row['id_project'];
        }
        
        /**
         * Add new project
         * @param type $id_tenant
         * @param type $new_code
         * @param type $etiqueta
         * @param type $hora_ini
         * @param type $fecha
         * @param type $descripcion
         * @param type $estado
         * @return PDO
         */
	public function addNewProject($id_tenant, $new_code, $etiqueta
                , $hora_ini, $fecha, $descripcion, $estado = 1)
	{
            $consulta = $this->db->prepare("INSERT INTO cas_project 
                        (id_project, code_project, id_tenant, label_project, date_ini, desc_project
                        , status_project) 
                            VALUES 
                        (NULL, '$new_code', $id_tenant, '$etiqueta', '$fecha. .$hora_ini', '$descripcion', $estado)");

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Asign existent task to existent project
         * @param int $id_project
         * @param int $id_task
         */
        public function addTaskToProject($id_project, $id_task)
        {
            $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_task 
                        (cas_project_id_project, cas_task_id_task) 
                            VALUES 
                        ($id_project, $id_task)");
            
            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Update a project
         * @param int $id_tenant
         * @param int $id_project
         * @param type $code_project
         * @param type $etiqueta
         * @param type $init_date
         * @param type $stop_date
         * @param type $total_time
         * @param type $desc
         * @param type $estado
         * @param type $pause_date
         * @param type $paused_time
         * @return pdo
         */
        public function updateProject($id_tenant, $id_project, $code_project, $etiqueta
                , $init_date, $stop_date, $total_time, $desc, $estado, $pause_date, $paused_time)
        {
            // force null values
            $stop_date = empty($stop_date) ? "NULL" : "'$stop_date'";
            $total_time = empty($total_time) ? "NULL" : "'$total_time'";
            $pause_date = empty($pause_date) ? "NULL" : "'$pause_date'";
            $paused_time = empty($paused_time) ? "NULL" : "'$paused_time'";
            
            $consulta = $this->db->prepare("UPDATE cas_project 
                        SET
                        code_project = '$code_project'
                        , label_project = '$etiqueta'
                        , date_ini = '$init_date'
                        , date_end = $stop_date
                        , time_total = $total_time
                        , desc_project = '$desc'
                        , status_project = '$estado'
                        , date_pause = $pause_date
                        , time_paused = $paused_time
                        WHERE id_tenant = $id_tenant
                          AND id_project = $id_project");
            
            $consulta->execute();

            return $consulta;
        }
        
        public function addUserToProject($id_project, $id_user)
        {
            $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_user 
                    (cas_project_id_project, cas_user_id_user) 
                        VALUES 
                    ($id_project, $id_user)");
            
            $consulta->execute();

            return $consulta;
        }
        
        public function addCustomerToProject($id_project, $id_customer)
        {
            $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_customer 
                    (cas_project_id_project, cas_customer_id_customer) 
                        VALUES 
                    ($id_project, $id_customer)");
            
            $consulta->execute();

            return $consulta;
        }
        
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
        
//        //GET ALL SEGMENTS
//	public function getAllSegmentsSimple()
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT 
//                        DISTINCT
//                        a.COD_SEGMENT
//                        , a.NAME_SEGMENT
//                    FROM  t_segment a
//                    ORDER BY a.NAME_SEGMENT");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//        
//        //GET SEGMENT BY NAME
//	public function getSegmentByName($name_segment)
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT 
//                        a.COD_SEGMENT
//                        , a.NAME_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_segment a
//                    INNER JOIN t_gbu b 
//                    ON a.COD_GBU = b.COD_GBU
//                    WHERE A.NAME_SEGMENT = '$name_segment'");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//        
//        /**
//         * Get array of segments by COD_GBU
//         * @param string $cod_gbu
//         * @return PDO
//         */
//	public function getAllSegmentsByGbu($cod_gbu)
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT 
//                        a.COD_SEGMENT
//                        , a.NAME_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_segment a
//                    INNER JOIN t_gbu b 
//                    ON a.COD_GBU = b.COD_GBU
//                    WHERE A.COD_GBU = '$cod_gbu'");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//	
//	
//	
//	
//	
//	//EDIT SEGMENT
//	public function editSegment($cod_segment, $name_segment, $cod_gbu, $old_cod_segment, $old_name_segment, $old_gbu)
//	{
//            require_once 'AdminModel.php';
//            $logModel = new AdminModel();
//            $sql = "UPDATE t_segment WHERE '$cod_segment'";
//
//            $session = FR_Session::singleton();
//
//            $consulta = $this->db->prepare("UPDATE t_segment
//                            SET 
//                                NAME_SEGMENT = '$name_segment'
//                                , COD_GBU = '$cod_gbu'
//                            WHERE COD_SEGMENT = '$old_cod_segment'
//                                AND COD_GBU = '$old_gbu'");
//
//            $consulta->execute();
//
//            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//            $logModel->addNewEvent($session->usuario, $sql, 'SEGMENTS');
//
//            return $consulta;
//	}
//	
//	
//	/*******************************************************************************
//	* SUB SEGMENTS
//	*******************************************************************************/
//	
//	//GET ALL SUB SEGMENTS
//	public function getAllSubSegments()
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT a.COD_SUB_SEGMENT
//                        , a.NAME_SUB_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_sub_segment AS a
//                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//	
//	//GET NEW SUB SEGMENT CODE
//	public function getNewSubSegmentCode()
//	{
//            //get last sub segment
//            $consulta = $this->db->prepare("SELECT COD_SUB_SEGMENT 
//                            FROM t_sub_segment 
//                            WHERE COD_SUB_SEGMENT NOT LIKE '%N/A%' 
//                            ORDER BY COD_SUB_SEGMENT DESC LIMIT 1");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//        
//        //GET SUB SEGMENT BY COD_GBU
//	public function getSubSegmentsByGbu($cod_gbu)
//	{
//            //get last sub segment
//            $consulta = $this->db->prepare("
//                    SELECT a.COD_SUB_SEGMENT
//                        , a.NAME_SUB_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_sub_segment AS a
//                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
//                    WHERE A.COD_GBU = '$cod_gbu'");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//
//	//ADD SUB SEGMENT
//	public function addNewSubSegment($code, $name, $cod_gbu)
//	{
//            require_once 'AdminModel.php';
//            $logModel = new AdminModel();
//            $sql = "INSERT INTO t_sub_segment VALUES '$code', '$name'";
//
//            $session = FR_Session::singleton();
//
//            $consulta = $this->db->prepare("INSERT INTO t_sub_segment 
//                    (COD_SUB_SEGMENT, NAME_SUB_SEGMENT, COD_GBU) 
//                    VALUES ('$code','$name','$cod_gbu')");
//            $consulta->execute();
//
//            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//            $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');
//
//            return $consulta;
//	}
//	
//	//EDIT SUB SEGMENT
//	public function editSubSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
//	{
//            require_once 'AdminModel.php';
//            $logModel = new AdminModel();
//            $sql = "UPDATE t_sub_segment WHERE '$code'";
//
//            $session = FR_Session::singleton();
//
//            $consulta = $this->db->prepare("UPDATE t_sub_segment
//                                SET 
//                                    NAME_SUB_SEGMENT = '$name'
//                                    , COD_GBU = '$cod_gbu'
//                                WHERE COD_SUB_SEGMENT = '$old_code'
//                                    AND COD_GBU = '$old_gbu'");
//
//            $consulta->execute();
//
//            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//            $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');
//
//            return $consulta;
//	}
//	
//	
//	/*******************************************************************************
//	* MICRO SEGMENTS
//	*******************************************************************************/
//	
//	//GET ALL MICRO SEGMENTS
//	public function getAllMicroSegments()
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT a.COD_MICRO_SEGMENT
//                        , a.NAME_MICRO_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_micro_segment AS a
//                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//        
//        //GET ALL MICRO SEGMENTS BY COD_GBU
//	public function getAllMicroSegmentsByGbu($cod_gbu)
//	{
//            //realizamos la consulta de todos los segmentos
//            $consulta = $this->db->prepare("
//                    SELECT a.COD_MICRO_SEGMENT
//                        , a.NAME_MICRO_SEGMENT
//                        , b.COD_GBU AS GBU_COD_GBU
//                        , b.NAME_GBU AS GBU_NAME_GBU
//                    FROM  t_micro_segment AS a
//                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
//                    WHERE A.COD_GBU = '$cod_gbu'");
//
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//	
//	//GET NEW MICRO SEGMENT CODE
//	public function getNewMicroSegmentCode()
//	{
//            //get last sub segment
//            $consulta = $this->db->prepare("SELECT COD_MICRO_SEGMENT FROM t_micro_segment
//                        WHERE COD_MICRO_SEGMENT NOT LIKE '%N/A%' 
//                        ORDER BY COD_MICRO_SEGMENT DESC LIMIT 1");
//            $consulta->execute();
//
//            //devolvemos la coleccion para que la vista la presente.
//            return $consulta;
//	}
//
//	//ADD MICRO SEGMENT
//	public function addNewMicroSegment($code, $name, $cod_gbu)
//	{
//            require_once 'AdminModel.php';
//            $logModel = new AdminModel();
//            $sql = "INSERT INTO t_micro_segment VALUES '$code','$name'";
//
//            $session = FR_Session::singleton();
//
//            $consulta = $this->db->prepare("INSERT INTO t_micro_segment 
//                    (COD_MICRO_SEGMENT, NAME_MICRO_SEGMENT, COD_GBU) 
//                    VALUES ('$code','$name','$cod_gbu')");
//            $consulta->execute();
//
//            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
//            $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');
//
//            return $consulta;
//	}
//	
//	//EDIT MICRO SEGMENT
//	public function editMicroSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
//	{
//            require_once 'AdminModel.php';
//            $logModel = new AdminModel();
//            $sql = "UPDATE t_micro_segment WHERE '$code'";
//
//            $session = FR_Session::singleton();
//
//            $consulta = $this->db->prepare("UPDATE t_micro_segment
//                        SET 
//                                NAME_MICRO_SEGMENT = '$name'
//                                , COD_GBU = '$cod_gbu'
//                        WHERE COD_MICRO_SEGMENT = '$old_code'
//                            AND COD_GBU = '$old_gbu'");
//
//            $consulta->execute();
//
//            //Save log
//            $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');
//
//            return $consulta;
//	}
}
?>