<?php
class AdminModel extends ModelBase
{
    /*******************************************************************************
    * ADMINISTRATION MODULE MODEL
    *******************************************************************************/

    /**
        * Get all log events
        * @return PDO
        */
    public function getAllEvents()
    {
        $consulta = $this->db->prepare("
                SELECT 
                    USUARIO
                    , FECHA
                    , MODIFICACION
                    , IP_CLIENTE
                    , HOST_NAME
                    , MODULO
                FROM t_seguimiento ORDER BY FECHA DESC
        ");

        $consulta->execute();

        return $consulta;
    }

    /**
        * Get number of all log events
        * @return PDO 
        */
    public function getEventsTotalNumber()
    {
        $consulta = $this->db->prepare("
                SELECT
                    COUNT(ID)
                FROM t_seguimiento
        ");

        $consulta->execute();

        return $consulta;
    }

    /**
        * Add new log event
        * @param string $usuario
        * @param string $modificacion
        * @param string $modulo
        * @return int 
        */
    public function addNewEvent($usuario, $modificacion, $modulo)
    {
        $fecha = date('Y-m-d H:i:s');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $log_sql = str_replace("'"," ",$modificacion);
        $host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        try
        {
                $consulta = $this->db->prepare("
                        INSERT INTO t_seguimiento 
                                (USUARIO
                                , FECHA
                                , MODIFICACION
                                , IP_CLIENTE
                                , HOST_NAME
                                , MODULO) 
                        VALUES 
                                ('$usuario'
                                ,'$fecha'
                                ,'$log_sql'
                                ,'$ip_cliente'
                                ,'$host_name'
                                ,'$modulo')
                        ");

                $consulta->execute();
        }
        catch(PDOException $e)
        {
                return 0;
        }

        return $consulta;
    }

    /**
        * Get PDO object from custom sql query
        * NOTA: Esta función impide tener un control de la consulta sql (depende desde donde se llame).
        * @param string $sql
        * @return PDO 
        */
    public function goCustomQuery($sql)
    {
        $consulta = $this->db->prepare($sql);

        $consulta->execute();

        return $consulta;
    }

    /**
        * Get database table name linked to this model
        * NOTA: Solo por lógica modelo = tabla
        * @return string 
        */
    public function getTableName()
    {
        #TABLA POR MODELO???
        $tableName = "t_seguimiento";

        return $tableName;
    }

    /**
        * Get database table column names
        * NOTA: Solo por lógica modelo = tabla
        * @return array
        */
    public function getTableColumnNames()
    {
        $columns = array('USUARIO'
            , 'FECHA'
            , 'MODIFICACION'
            , 'IP_CLIENTE'
            , 'HOST_NAME'
            , 'MODULO'
        );

        return $columns;
    }

    public function updatePrivilegePermission($privi,$module,$ver_value, $write_value, $edit_value)
    {
        $sql = "UPDATE t_privilegios_permisos for ".$privi." in ".$module;

        $session = FR_Session::singleton();

        try
        {
            $consulta = $this->db->prepare("UPDATE t_privilegios_permisos
                            SET 
                                VER = $ver_value
                                , ESCRIBIR = $write_value
                                , EDITAR = $edit_value
                            WHERE COD_PRIVILEGIO = $privi
                                AND COD_MODULO = $module");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $this->addNewEvent($session->usuario, $sql, 'PRIVILEGIOS');
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
    
    /**
     * Extraer todos los permisos de acceso por codigo de privilegio
     * @param int $privi
     * @return PDO 
     */
    public function getAllPrivilegePermissions($privi)
    {
        try
        {
            $consulta = $this->db->prepare("SELECT
                            A.COD_PRIVILEGIO
                            , A.VER
                            , A.ESCRIBIR
                            , A.EDITAR
                            , A.COD_MODULO
                            , B.NAME_MODULO
                            , B.LABEL_MODULO
                            FROM t_privilegios_permisos as A
                            INNER JOIN t_modulos as B
                            ON A.COD_MODULO = B.COD_MODULO
                            WHERE A.COD_PRIVILEGIO = $privi
                            ORDER BY COD_MODULO ASC");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
   
    /**
     * Extraer todos los permisos de acceso por codigo de privilegio y
     * de estado de modulo
     * @param int $privi
     * @param int $status
     * @return PDO
     */
    public function getAllPrivilegePermissionsByModuleStatus($privi, $status)
    {
        try
        {
            $consulta = $this->db->prepare("SELECT
                            A.COD_PRIVILEGIO
                            , A.VER
                            , A.ESCRIBIR
                            , A.EDITAR
                            , A.COD_MODULO
                            , B.NAME_MODULO
                            , B.LABEL_MODULO
                            FROM t_privilegios_permisos as A
                            INNER JOIN t_modulos as B
                            ON A.COD_MODULO = B.COD_MODULO
                            WHERE A.COD_PRIVILEGIO = $privi
                              AND B.ESTADO = $status
                            ORDER BY COD_MODULO ASC");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
            #echo $e->getMessage();
            return 0;
        }

        return $consulta;
    }
    
    public function getAllPrivileges()
    {
        try {
            $consulta = $this->db->prepare("
                        SELECT 
                            COD_PRIVILEGIO,
                            NAME_PRIVILEGIO
                        FROM t_privilegios");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;

        } catch (PDOException $exc) {
            return $exc->getCode();
        }

        return null;
    }

    public function getLastPrivilegeCode()
    {
        try
        {
            //get last segment
                $consulta = $this->db->prepare("SELECT MAX(COD_PRIVILEGIO) AS COD_PRIVILEGIO FROM t_privilegios");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }

    /**
        * Agregar un nuevo privilegio.
        * Por modelo de datos al crear un nuevo privilegio es necesario
        * crear también los registros de permisos y accesos.
        * 
        * @param type $cod_privilegio
        * @param type $name_privilegio
        * @return int 
        */
    public function addNewPrivilege($cod_privilegio, $name_privilegio)
    {
        $session = FR_Session::singleton();

        $modulesPDO = $this->getAllModules();
        
        $resultStatus = true; 

        try
        {
            $this->db->beginTransaction();
            
            $consulta = $this->db->prepare("INSERT INTO t_privilegios
                            (COD_PRIVILEGIO, NAME_PRIVILEGIO)
                            VALUES
                            ('$cod_privilegio', '$name_privilegio')");

            $consulta->execute();

            $error = $consulta->errorInfo();

            #echo "error: ".$error[0].", ".$error[2];

            if($error[0] == 00000){
                $i = 2;
                while($row = $modulesPDO->fetch(PDO::FETCH_ASSOC)){

                    // Accesos
                    if($row['COD_MODULO'] == 10 || $row['COD_MODULO'] == 11){
                        $consulta = $this->db->prepare("
                                INSERT INTO t_privilegios_modulos
                                (`COD_PRIVILEGIO`, `COD_MODULO`, `ORDER`)
                                VALUES
                                ('$cod_privilegio', '$row[COD_MODULO]', '1')");
                    }
                    elseif($row['COD_MODULO'] == 12){
                        $consulta = $this->db->prepare("
                                INSERT INTO t_privilegios_modulos
                                (`COD_PRIVILEGIO`, `COD_MODULO`, `ORDER`)
                                VALUES
                                ('$cod_privilegio', '$row[COD_MODULO]', '0')");
                    }
                    else{
                        $consulta = $this->db->prepare("
                                INSERT INTO t_privilegios_modulos
                                (`COD_PRIVILEGIO`, `COD_MODULO`, `ORDER`)
                                VALUES
                                ('$cod_privilegio', '$row[COD_MODULO]', '$i')");
                    }

                    $consulta->execute();
                    $error = $consulta->errorInfo();

                    #echo "error: ".$error[0].", ".$error[2];

                    if($error[0] == 00000){
                        // Permisos
                        $consulta = $this->db->prepare("INSERT INTO t_privilegios_permisos
                                            (COD_PRIVILEGIO, VER, ESCRIBIR, EDITAR, COD_MODULO)
                                            VALUES
                                            ('$cod_privilegio', 0, 0, 0, '$row[COD_MODULO]')");

                        $consulta->execute();

                        $i++;
                    }
                }

                if($error[0] == 00000){
                    //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                    $sql = "CREACION DE NUEVO PRIVILEGIO DE CODIGO: '$cod_privilegio'";
                    $this->addNewEvent($session->usuario, $sql, 'ADMIN');
                }
            }
            
            $this->db->commit();
        }
        catch(PDOException $e)
        {
            $this->db->rollback();
            return $consulta;
        }

        return $consulta;
    }
    
    /**
     * Extract all modules
     * @return PDO
     */
    public function getAllModules()
    {
        try
        {
            $consulta = $this->db->prepare("SELECT
                            A.COD_MODULO
                            , A.NAME_MODULO
                            , A.LABEL_MODULO
                            FROM t_modulos as A
                            ");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
    
    /**
     * Extract all modules
     * @return PDO
     */
    public function getAllModulesByStatus($status)
    {
        try
        {
            $consulta = $this->db->prepare("SELECT
                            A.COD_MODULO
                            , A.NAME_MODULO
                            , A.LABEL_MODULO
                            FROM t_modulos as A
                            WHERE A.ESTADO = $status
                            ");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
    
    public function getModuleCodeByName($name_modulo)
    {
        try
        {
            $consulta = $this->db->prepare("SELECT
                            A.COD_MODULO
                            , A.NAME_MODULO
                            , A.LABEL_MODULO
                            FROM t_modulos as A
                            WHERE A.NAME_MODULO = '$name_modulo'
                            ");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
    
    public function getLastModuleCode()
    {
        try
        {
            //get last segment
                $consulta = $this->db->prepare("SELECT MAX(COD_MODULO) FROM t_modulos");

            $consulta->execute();
        }
        catch(PDOException $e)
        {
                #echo $e->getMessage();
                return 0;
        }

        return $consulta;
    }
}