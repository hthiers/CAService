<?php
class ProjectsModel extends ModelBase
{
	/*******************************************************************************
	* Proyects
	*******************************************************************************/
	
	public function getAllProjectsByTenant($id_tenant)
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                        a.id_project
                        , a.code_project
                        , a.id_tenant
                        , a.label_project
                        , a.date_ini
                        , a.date_end
                    FROM  cas_project a
                    WHERE a.id_tenant = $id_tenant
                    ORDER BY a.label_project");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
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
         * Get segment by COD_SEGMENT
         * @param type $code_segment
         * @return type PDO
         */
	public function getSegmentByCode($code_segment)
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
                    WHERE A.COD_SEGMENT = '$code_segment'");

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
	
	//GET LATEST SEGMENT CODE
	public function getNewSegmentCode()
	{
            //get last segment
            $consulta = $this->db->prepare("SELECT COD_SEGMENT FROM t_segment 
                    WHERE COD_SEGMENT NOT LIKE '%N/A%' ORDER BY COD_SEGMENT DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
	
	//ADD SEGMENT
	public function addNewSegment($cod_segment, $name_segment, $cod_gbu)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_segment VALUES '$cod_segment', '$name_segment'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("INSERT INTO t_segment 
                        (COD_SEGMENT, NAME_SEGMENT, COD_GBU) 
                            VALUES 
                        ('$cod_segment','$name_segment','$cod_gbu')");
            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'SEGMENTS');

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