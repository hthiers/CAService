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
                        , a.label_project
                        , a.date_ini
                        , a.date_end
                    FROM  cas_project a
                    LEFT OUTER JOIN cas_project_has_cas_user b
                    ON a.id_project = b.cas_project_id_project
                    LEFT OUTER JOIN cas_user c
                    ON (b.cas_user_id_user = c.id_user
                        AND
                        c.id_tenant = $id_tenant)
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
                        A.ID_PROJECT
                        , A.CODE_PROJECT
                        , B.ID_TENANT
                        , A.LABEL_PROJECT
                        , A.DATE_INI
                        , A.DATE_END
                        , D.ID_USER
                        , D.NAME_USER
                    FROM  CAS_PROJECT A
                    INNER JOIN CAS_TENANT B
                    ON A.ID_TENANT = B.ID_TENANT
                    INNER JOIN CAS_PROJECT_HAS_CAS_USER C
                    ON A.ID_PROJECT = C.CAS_PROJECT_ID_PROJECT
                    INNER JOIN CAS_USER D
                    ON C.CAS_USER_ID_USER = D.ID_USER
                    WHERE A.ID_PROJECT = $id_project
                      AND B.ID_TENANT = $id_tenant");

            $consulta->execute();

            return $consulta;
	}
        
        
	public function getLastProject($id_tenant)
	{
            //get last segment
            $consulta = $this->db->prepare("
                    SELECT 
                        A.ID_PROJECT
                        , A.CODE_PROJECT
                        , B.ID_TENANT
                        , A.LABEL_PROJECT
                    FROM  cas_project A
                    INNER JOIN cas_tenant B
                    ON A.ID_TENANT = B.ID_TENANT
                    WHERE B.ID_TENANT = $id_tenant
                    ORDER BY A.ID_PROJECT DESC
                    LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        
	public function addNewProject($id_tenant, $new_code, $id_user, $id_customer, $descripcion, $hora_ini, $fecha)
	{
            $consulta = $this->db->prepare("INSERT INTO cas_project 
                        (id_project, code_project, id_tenant, label_project, date_ini) 
                            VALUES 
                        ($new_code, '$new_code', $id_tenant, '$descripcion', '$fecha.$hora_ini')");
            
            $consulta->execute();

            $error = $consulta->errorInfo();
            $rows_n = $consulta->rowCount();

            if($error[0] == 00000){
               if($rows_n > 0){
                   $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_user 
                        (cas_project_id_project, cas_user_id_user) 
                            VALUES 
                        ($new_code, $id_user)");

                   $consulta->execute();
                   
                   $consulta = $this->db->prepare("INSERT INTO cas_project_has_cas_customer 
                        (cas_project_id_project, cas_customer_id_customer) 
                            VALUES 
                        ($new_code, $id_customer)");

                   $consulta->execute();
               }
            }
            
            return $consulta;
	}


        /********************************
         * OLD STUFF
         ********************************
         */
        
        //GET ALL SEGMENTS
	public function getAllSegmentsSimple()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                        DISTINCT
                        a.COD_SEGMENT
                        , a.NAME_SEGMENT
                    FROM  t_segment a
                    ORDER BY a.NAME_SEGMENT");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET SEGMENT BY NAME
	public function getSegmentByName($name_segment)
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                        a.COD_SEGMENT
                        , a.NAME_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_segment a
                    INNER JOIN t_gbu b 
                    ON a.COD_GBU = b.COD_GBU
                    WHERE A.NAME_SEGMENT = '$name_segment'");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        /**
         * Get array of segments by COD_GBU
         * @param string $cod_gbu
         * @return PDO
         */
	public function getAllSegmentsByGbu($cod_gbu)
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                        a.COD_SEGMENT
                        , a.NAME_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_segment a
                    INNER JOIN t_gbu b 
                    ON a.COD_GBU = b.COD_GBU
                    WHERE A.COD_GBU = '$cod_gbu'");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	
	
	
	
	//EDIT SEGMENT
	public function editSegment($cod_segment, $name_segment, $cod_gbu, $old_cod_segment, $old_name_segment, $old_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_segment WHERE '$cod_segment'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_segment
                            SET 
                                NAME_SEGMENT = '$name_segment'
                                , COD_GBU = '$cod_gbu'
                            WHERE COD_SEGMENT = '$old_cod_segment'
                                AND COD_GBU = '$old_gbu'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'SEGMENTS');

            return $consulta;
	}
	
	
	/*******************************************************************************
	* SUB SEGMENTS
	*******************************************************************************/
	
	//GET ALL SUB SEGMENTS
	public function getAllSubSegments()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT a.COD_SUB_SEGMENT
                        , a.NAME_SUB_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_sub_segment AS a
                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	//GET NEW SUB SEGMENT CODE
	public function getNewSubSegmentCode()
	{
            //get last sub segment
            $consulta = $this->db->prepare("SELECT COD_SUB_SEGMENT 
                            FROM t_sub_segment 
                            WHERE COD_SUB_SEGMENT NOT LIKE '%N/A%' 
                            ORDER BY COD_SUB_SEGMENT DESC LIMIT 1");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET SUB SEGMENT BY COD_GBU
	public function getSubSegmentsByGbu($cod_gbu)
	{
            //get last sub segment
            $consulta = $this->db->prepare("
                    SELECT a.COD_SUB_SEGMENT
                        , a.NAME_SUB_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_sub_segment AS a
                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
                    WHERE A.COD_GBU = '$cod_gbu'");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}

	//ADD SUB SEGMENT
	public function addNewSubSegment($code, $name, $cod_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_sub_segment VALUES '$code', '$name'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("INSERT INTO t_sub_segment 
                    (COD_SUB_SEGMENT, NAME_SUB_SEGMENT, COD_GBU) 
                    VALUES ('$code','$name','$cod_gbu')");
            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');

            return $consulta;
	}
	
	//EDIT SUB SEGMENT
	public function editSubSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_sub_segment WHERE '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_sub_segment
                                SET 
                                    NAME_SUB_SEGMENT = '$name'
                                    , COD_GBU = '$cod_gbu'
                                WHERE COD_SUB_SEGMENT = '$old_code'
                                    AND COD_GBU = '$old_gbu'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'SUBSEGMENTS');

            return $consulta;
	}
	
	
	/*******************************************************************************
	* MICRO SEGMENTS
	*******************************************************************************/
	
	//GET ALL MICRO SEGMENTS
	public function getAllMicroSegments()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT a.COD_MICRO_SEGMENT
                        , a.NAME_MICRO_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_micro_segment AS a
                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET ALL MICRO SEGMENTS BY COD_GBU
	public function getAllMicroSegmentsByGbu($cod_gbu)
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT a.COD_MICRO_SEGMENT
                        , a.NAME_MICRO_SEGMENT
                        , b.COD_GBU AS GBU_COD_GBU
                        , b.NAME_GBU AS GBU_NAME_GBU
                    FROM  t_micro_segment AS a
                    INNER JOIN t_gbu AS b ON a.COD_GBU = b.COD_GBU
                    WHERE A.COD_GBU = '$cod_gbu'");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	//GET NEW MICRO SEGMENT CODE
	public function getNewMicroSegmentCode()
	{
            //get last sub segment
            $consulta = $this->db->prepare("SELECT COD_MICRO_SEGMENT FROM t_micro_segment
                        WHERE COD_MICRO_SEGMENT NOT LIKE '%N/A%' 
                        ORDER BY COD_MICRO_SEGMENT DESC LIMIT 1");
            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}

	//ADD MICRO SEGMENT
	public function addNewMicroSegment($code, $name, $cod_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_micro_segment VALUES '$code','$name'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("INSERT INTO t_micro_segment 
                    (COD_MICRO_SEGMENT, NAME_MICRO_SEGMENT, COD_GBU) 
                    VALUES ('$code','$name','$cod_gbu')");
            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');

            return $consulta;
	}
	
	//EDIT MICRO SEGMENT
	public function editMicroSegment($code, $name, $cod_gbu, $old_code, $old_name, $old_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_micro_segment WHERE '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_micro_segment
                        SET 
                                NAME_MICRO_SEGMENT = '$name'
                                , COD_GBU = '$cod_gbu'
                        WHERE COD_MICRO_SEGMENT = '$old_code'
                            AND COD_GBU = '$old_gbu'");

            $consulta->execute();

            //Save log
            $logModel->addNewEvent($session->usuario, $sql, 'MICROSEGMENTS');

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
}
?>