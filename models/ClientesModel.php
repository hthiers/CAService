<?php
class ClientesModel extends ModelBase
{
	/*******************************************************************************
	* CLIENTES
	*******************************************************************************/

	//GET ALL CLIENTES
	public function getAllClientes($type = null)
	{
		//realizamos la consulta de todos los segmentos
		if($type == null)
			$consulta = $this->db->prepare("
				SELECT COD_CLIENTE 
                                    , NOM_CLIENTE
                                    , B.COD_BUYER_CLASS
                                    , B.BUYER_CLASS_NAME
                                    , C.COD_CHANNEL
                                    , C.CHANNEL_NAME
                                    , TIPO
                                    , ESTADO 
                                FROM t_cliente A
                                INNER JOIN T_BUYER_CLASS B
                                ON A.COD_BUYER_CLASS = B.COD_BUYER_CLASS
                                INNER JOIN T_CHANNEL C
                                ON A.COD_CHANNEL = C.COD_CHANNEL
                                WHERE ESTADO = 1
                                ORDER BY NOM_CLIENTE");
		else
			$consulta = $this->db->prepare("
				SELECT COD_CLIENTE
                                    , NOM_CLIENTE
                                    , B.COD_BUYER_CLASS
                                    , B.BUYER_CLASS_NAME
                                    , C.COD_CHANNEL
                                    , C.CHANNEL_NAME
                                    , TIPO
                                    , ESTADO 
				FROM t_cliente A
                                INNER JOIN T_BUYER_CLASS B
                                ON A.COD_BUYER_CLASS = B.COD_BUYER_CLASS
                                INNER JOIN T_CHANNEL C
                                ON A.COD_CHANNEL = C.COD_CHANNEL
                                WHERE TIPO = '$type'
                                  AND ESTADO = 1
                                ORDER BY NOM_CLIENTE");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        /**
         * Get cliente por código de cliente
         * @param varchar $code
         * @return PDO 
         */
        public function getClienteByCode($code)
        {
            $consulta = $this->db->prepare("
				SELECT COD_CLIENTE 
                                    , NOM_CLIENTE
                                    , B.COD_BUYER_CLASS
                                    , B.BUYER_CLASS_NAME
                                    , C.COD_CHANNEL
                                    , C.CHANNEL_NAME
                                    , TIPO
                                    , ESTADO 
                                FROM t_cliente A
                                INNER JOIN T_BUYER_CLASS B
                                ON A.COD_BUYER_CLASS = B.COD_BUYER_CLASS
                                INNER JOIN T_CHANNEL C
                                ON A.COD_CHANNEL = C.COD_CHANNEL
                                WHERE A.COD_CLIENTE = '$code'");
            
                $consulta->execute();
		
		return $consulta;
        }
        
        //GET ALL TIPOS
	public function getAllTiposCliente()
	{
		//realizamos la consulta de todos los segmentos
                $consulta = $this->db->prepare("
                        SELECT DISTINCT TIPO
                        FROM t_cliente ORDER BY TIPO ASC
                ");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        //GET LAST CODE
	public function getNewClienteCode()
	{
		$consulta = $this->db->prepare("SELECT COD_CLIENTE FROM t_cliente 
			WHERE COD_CLIENTE NOT LIKE '%N/A%' ORDER BY COD_CLIENTE DESC LIMIT 1");
		$consulta->execute();
		
		return $consulta;
	}
        
        //NUEVA estado
        //VALOR PARA ESTADO POR DEFECTO?????
	public function addNewCliente($code, $name, $buyerclass, $channel, $type, $state = '1')
	{
                require_once 'AdminModel.php';
                $logModel = new AdminModel();
                $sql = "INSERT INTO t_cliente VALUES '$code', '$name'";
            
                $session = FR_Session::singleton();
                
		try
		{
			$consulta = $this->db->prepare("
				INSERT INTO t_cliente 
					(COD_CLIENTE
					, NOM_CLIENTE
                                        , COD_BUYER_CLASS
                                        , COD_CHANNEL
                                        , TIPO
                                        , ESTADO) 
				VALUES 
					('$code'
					,'$name'
                                        ,'$buyerclass'
                                        ,'$channel'
                                        ,'$type'
                                        ,'$state')
				");
				
			$consulta->execute();
                        
                        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                        $logModel->addNewEvent($session->usuario, $sql, 'CLIENTES');
		}
		catch(PDOException $e)
		{
			#echo $e->getMessage();
			return 0;
		}
		
		return $consulta;
	}
        
        //Edit estado
        public function editCliente($code, $buyerclass, $channel, $type)
	{
                require_once 'AdminModel.php';
                $logModel = new AdminModel();
                $sql = "UPDATE t_cliente WHERE COD_CLIENTE = '$code'";
            
                $session = FR_Session::singleton();
                
		try
		{
			$consulta = $this->db->prepare("UPDATE t_cliente
                                    SET 
                                        COD_BUYER_CLASS = '$buyerclass'
                                        , COD_CHANNEL = '$channel'
                                        , TIPO = '$type'
                                    WHERE COD_CLIENTE = '$code'
                                        ");
										  
			$consulta->execute();
                        
                        //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                        $logModel->addNewEvent($session->usuario, $sql, 'CLIENTES');
		}
		catch(PDOException $e)
		{
                    #echo $e->getMessage();
                    return 0;
		}
		
		return $consulta;
	}
        
        
        /*******************************************************************************
	* BUYER CLASS
	*******************************************************************************/

	//GET ALL BUYER CLASSSES
	public function getAllBuyerClass()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("SELECT COD_BUYER_CLASS, BUYER_CLASS_NAME
                    FROM T_BUYER_CLASS ORDER BY BUYER_CLASS_NAME
            ");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}

        //GET LAST CODE
	public function getLastBuyerClass()
	{
            $consulta = $this->db->prepare("SELECT COD_BUYER_CLASS FROM t_buyer_class 
                    WHERE COD_BUYER_CLASS NOT LIKE '%N/A%' ORDER BY COD_BUYER_CLASS DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}

        //NUEVA estado ----TODO
	public function addNewBuyerClass($code, $name)
	{
            try
            {
                $consulta = $this->db->prepare("
                        INSERT INTO t_buyer_class 
                                (COD_BUYER_CLASS
                                , BUYER_CLASS_NAME) 
                        VALUES 
                                ('$code'
                                ,'$name')
                        ");

                $consulta->execute();
            }
            catch(PDOException $e)
            {
                    return 0;
            }

            return $consulta;
	}

        //Edit buyer class
        public function editBuyerClass($code, $name)
	{
            try
            {
                    $consulta = $this->db->prepare("UPDATE t_buyer_class
                                SET 
                                    BUYER_CLASS_NAME = '$name'
                                WHERE COD_BUYER_CLASS = '$code'
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
        
        /*******************************************************************************
	* CHANNELS
	*******************************************************************************/
        
        //GET ALL CUSTOMER CHANNELS
	public function getAllChannels()
	{
		//realizamos la consulta de todo
                $consulta = $this->db->prepare("
                        SELECT COD_CHANNEL, CHANNEL_NAME
                        FROM T_CHANNEL ORDER BY CHANNEL_NAME
                ");

		$consulta->execute();
		
		//devolvemos la coleccion para que la vista la presente.
		return $consulta;
	}
        
        //new channel
        public function addNewChannel($code, $name)
	{
		try
		{
			$consulta = $this->db->prepare("
				INSERT INTO t_channel
					(COD_CHANNEL
					, CHANNEL_NAME) 
				VALUES 
					('$code'
					,'$name')");
				
			$consulta->execute();
		}
		catch(PDOException $e)
		{
			#echo $e->getMessage();
			return 0;
		}
		
		return $consulta;
	}
        
        //Edit channel
        public function editChannel($code, $name)
	{
		try
		{
			$consulta = $this->db->prepare("UPDATE t_channel
                                    SET 
                                        CHANNEL_NAME = '$name'
                                    WHERE COD_CHANNEL = '$code'");
										  
			$consulta->execute();
		}
		catch(PDOException $e)
		{
                    #echo $e->getMessage();
                    return 0;
		}
		
		return $consulta;
	}
        
        //GET LAST CODE
	public function getLastChannelCode()
	{
            $consulta = $this->db->prepare("SELECT COD_CHANNEL FROM t_channel 
                    WHERE COD_CHANNEL NOT LIKE '%N/A%' ORDER BY COD_CHANNEL DESC LIMIT 1");
            $consulta->execute();

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
            $tableName = "t_cliente";
            
            return $tableName;
        }
}
?>