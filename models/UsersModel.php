<?php
class UsersModel extends ModelBase
{
	public function getUserAccount($username, $password)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user
                            WHERE name_user='$username'");
                              #AND password_user='$password'");
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        public function getUserAccountByID($id)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                 id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user WHERE id_user='$id'");
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        public function getUserAccountByCode($code_user)
	{
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT 
                                 id_user
                                , code_user
                                , id_tenant
                                , name_user
                                , id_profile
                            FROM cas_user WHERE code_user='$code_user'");
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}

        public function getUserInfo($id, $username)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID,USUARIO,NOMBRE,APELLIDOP, APELLIDOM,PRIVILEGIO
                            FROM t_usuario 
                            WHERE ID = '$id'
                              AND USUARIO='$username'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}
        
        public function getUserProfileByID($id)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID,USUARIO,PRIVILEGIO
                            FROM t_usuario 
                            WHERE ID = '$id'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}
        
        public function getUserModulePrivilegeByModule($id, $cod_modulo)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("
                            SELECT 
                                ID
                                ,USUARIO
                                ,PRIVILEGIO
                                ,C.VER
                                ,C.ESCRIBIR
                                ,C.EDITAR
                            FROM t_usuario A
                            INNER JOIN t_privilegios B
                            ON A.PRIVILEGIO = B.COD_PRIVILEGIO
                            INNER JOIN t_privilegios_permisos C
                            ON B.COD_PRIVILEGIO = C.COD_PRIVILEGIO
                            WHERE ID = '$id'
                              AND C.COD_MODULO = '$cod_modulo'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                return $e->getMessage();
            }
		
            return $consulta;
	}

        public function getUserInfoPassword($id, $username)
	{
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("SELECT ID, USUARIO, CLAVE
                            FROM t_usuario
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
	}
        
        public function editUserInfo($id, $username, $name, $apellidop, $apellidom)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario WHERE '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("UPDATE t_usuario
                            SET NOMBRE = '$name'
                                , APELLIDOP = '$apellidop'
                                , APELLIDOM = '$apellidom'
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
        }
        
        public function editUserAccount($id, $username, $name, $apellidop, $apellidom, $privilegio, $password)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario WHERE '$username'";
            
            //Asegurar que session esta abierta
            $session = FR_Session::singleton();
            
            //New pass
            if($password != "")
                $new_password = md5($password);
            else
                $new_password = "";
   
            //password change requested
            if($new_password != "")
            {
                //realizamos la consulta de todos los segmentos
                $consulta = $this->db->prepare("UPDATE t_usuario
                        SET NOMBRE = '$name'
                            , APELLIDOP = '$apellidop'
                            , APELLIDOM = '$apellidom'
                            , USUARIO = '$username'
                            , PRIVILEGIO = '$privilegio'
                            , CLAVE = '$new_password'
                        WHERE ID = '$id'");
            }
            else
            {
                //realizamos la consulta de todos los segmentos
                $consulta = $this->db->prepare("UPDATE t_usuario
                        SET NOMBRE = '$name'
                            , APELLIDOP = '$apellidop'
                            , APELLIDOM = '$apellidom'
                            , USUARIO = '$username'
                            , PRIVILEGIO = '$privilegio'
                        WHERE ID = '$id'");
            }

            $consulta->execute();
            
            //Save to events log
            $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            
            return $consulta;
        }
        
        public function editUserPassword($id, $username, $password)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_usuario PASSW WHERE '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
                $new_password = md5($password);
                
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("UPDATE t_usuario
                            SET CLAVE = '$new_password'
                            WHERE ID = '$id'
                              AND USUARIO = '$username'");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                return $e->getCode();
                #return 0;
            }
		
            return $consulta;
        }
        
        public function addNewUser($nombre, $apellidop, $apellidom, $username, $password, $privilegio)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_usuario VALUES '$username'";
            
            $session = FR_Session::singleton();
            
            try
            {
                $new_password = md5($password);
                
		//realizamos la consulta de todos los segmentos
		$consulta = $this->db->prepare("INSERT INTO t_usuario
                                (NOMBRE, APELLIDOP, APELLIDOM, USUARIO, CLAVE, PRIVILEGIO)
                                VALUES
                                ('$nombre', '$apellidop', '$apellidom', '$username', '$new_password', $privilegio)");
                
		$consulta->execute();
                
                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'USERS');
            }
            catch(PDOException $e)
            {
                #return $e->getMessage();
                return 0;
            }
		
            return $consulta;
        }
        
        public function getAllUserAccounts()
	{
            try {
                $consulta = $this->db->prepare("
                    SELECT 
                        ID
                        ,USUARIO
                        ,NOMBRE
                        ,APELLIDOP
                        ,APELLIDOM
                        ,PRIVILEGIO
                        ,B.NAME_PRIVILEGIO
                    FROM t_usuario as A
                    INNER JOIN t_privilegios as B
                    ON A.PRIVILEGIO = B.COD_PRIVILEGIO");
                
		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
                
            } catch (PDOException $exc) {
                return $exc->getCode();
            }
            
            return null;
	}
        
        public function deleteUserAccount($id_user)
        {
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            
            $session = FR_Session::singleton();
            
            $consulta = $this->db->prepare("DELETE FROM t_usuario WHERE ID = $id_user");
                
            if($consulta->execute()){
                if($consulta->rowCount() > 0){
                    //Save to events log
                    $sql = "DELETE FROM t_usuario WHERE '$id_user'";
                    $logModel->addNewEvent($session->usuario, $sql, 'USERS');
                }
            }
            
            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
        }
}
?>