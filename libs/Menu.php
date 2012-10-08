<?php
/**
 * System Menu 
 */
class Menu extends ModelBase
{       
        /**
         * Clase CSS para item de menu abierto (current page)
         * @var String
         */
        private $current_class = 'class="current"';
        
        function getCurrentClass($controller, $target, $option = ""){
            if($controller == $target)
                return $this->current_class;
            elseif($controller == $option)
                return $this->current_class;
            else
                return "control: ".$controller.", tar: ".$target.", opt: ".$option;
        }
        
        function getUserAccesByPrivi($privi)
        {
            $consulta = $this->db->prepare("SELECT 
                        A.COD_PRIVILEGIO
                        , A.VER
                        , A.ESCRIBIR
                        , A.EDITAR
                        , B.NAME_MODULO
                        FROM t_privilegios_permisos as A
                        INNER JOIN t_modulos as B
                        ON A.COD_MODULO = B.COD_MODULO
                        INNER JOIN t_privilegios_modulos as C
                        ON (A.COD_PRIVILEGIO = C.COD_PRIVILEGIO
                            AND A.COD_MODULO = C.COD_MODULO)
                        WHERE A.COD_PRIVILEGIO=$privi
                          AND A.VER = 1
                        ORDER BY C.ORDER asc");
            $consulta->execute();
            
            $result = $consulta->rowCount();
            
            if($result > 0)
                return $consulta;
            else
                return null;
        }
	
	public function loadMenu($userSession,$nav,$root,$controller)
	{
            $privi = $userSession->privilegio;
            $access = $this->getUserAccesByPrivi($privi);
            
            if($access != null)
            {
                $menuFullString = '';
                
                if($nav == 'MSIE 6.0')
                    $menuFullString.= '<ul id="navmenu-h">';
                else
                    $menuFullString.= '<ul id="menu">';
                
                while($accessModule = $access->fetch(PDO::FETCH_ASSOC))
                {
                    #$permission = $this->getPriviPermission($privi, $accessModule['COD_MODULO']);
                    $menuFullString.= call_user_func_array(array($this, $accessModule['NAME_MODULO']), array($root, $accessModule['ESCRIBIR'], $controller));
                }
                
                $menuFullString.= '</ul>';
                
                #Print out menu
                print $menuFullString;
            }
            else
            {
                $menuFullString = '';
                if($nav == 'MSIE 6.0')
                    $menuFullString.= '<ul id="navmenu-h">';
                else
                    $menuFullString.= '<ul id="menu">';
                $menuFullString.= '<li>-- empty! --</li>';
                $menuFullString.= '</ul>';
            }
        }
        
        function loadTestMenu()
        {
            $menuFullString = '<ul id="menu">';
                $menuFullString.= '<li><a class="menu_sup" href="?controller=projects&amp;action=projectsDt">TRABAJOS</a>';
                $menuFullString.= '</li>';
                $menuFullString.= '<li><a class="menu_sup" href="?controller=projects&amp;action=projectsNewForm">NUEVO TRABAJO</a>';
                $menuFullString.= '</li>';
                $menuFullString.= '<li><a class="menu_sup" href="#">CLIENTES</a>';
                $menuFullString.= '</li>';
                $menuFullString.= '<li><a class="menu_sup" href="#">NUEVO CLIENTE</a>';
                $menuFullString.= '</li>';
            $menuFullString.= '</ul>';
            
            print $menuFullString;
        }
        
        function mimenu($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "admin", "users").'><a href="#">Menu</a>';
                $code.= '<ul>';
                $code.= $this->administracion($root);
                $code.= '<li><a href="'.$root.'?controller=users&amp;action=userProfile">Mi Cuenta</a></li>';
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}
	
	function mimenuuser($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "admin", "users").'><a href="#">Menu</a>';
            $code.= '<ul>';
            $code.= '<li><a href="'.$root.'?controller=users&amp;action=userProfile">Mi Cuenta</a></li>';
            $code.= '</ul>';
            $code.= '</li>';
            return $code;
	}
	
	function tienda($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "tiendas").'><a href="'.$root.'?controller=tiendas&action=tiendasDt">Tienda</a>';
            $code.= '<ul>';
            $code.= '<li><a href="'.$root.'?controller=tiendas&action=tiendasDt">Tienda</a>';
                if($permiso == 1){
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=tiendas&action=tiendasAddForm">Nueva</a></li>';
                    $code.= '</ul>';
                }
            $code.= '</li>';
            $code.= '<li><a href="'.$root.'?controller=tiendas&action=zonasDt">Zona</a>';
                if($permiso == 1){
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=tiendas&action=zonasAddForm">Nueva</a></li>';
                    $code.= '</ul>';
                }
            $code.= '</li>';
            $code.= '<li><a href="'.$root.'?controller=tiendas&action=tiposDt">Tipo</a>';
                if($permiso == 1){
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=tiendas&action=tiposAddForm">Nuevo</a></li>';
                    $code.= '</ul>';
                }
            $code.= '</li>';
            $code.= '<li><a href="'.$root.'?controller=tiendas&action=agrupacionesDt">Agrupacion</a>';
                if($permiso == 1){
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=tiendas&action=agrupacionesAddForm">Nueva</a></li>';
                    $code.= '</ul>';
                }
            $code.= '</li>';
            $code.= '<li><a href="'.$root.'?controller=tiendas&action=estadosDt">Estado</a>';
                if($permiso == 1){
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=tiendas&action=estadosAddForm">Nuevo</a></li>';
                    $code.= '</ul>';
                }
            $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}

	function administracion($root)
	{
            $code = '<li><a href="#">Administracion</a>';
            $code.= '<ul>';
                $code.= '<li><a href="'.$root.'?controller=admin&action=usersDt">Usuarios</a>';
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=admin&action=userAddForm">Nuevo Usuario</a></li>';
                    $code.= '</ul>';
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=admin&action=privilegiosPanel">Perfiles</a>';
                    $code.= '<ul>';
                    $code.= '<li><a href="'.$root.'?controller=admin&action=privilegiosAddForm">Nuevo Perfil</a></li>';
                    $code.= '</ul>';
                $code.= '</li>';
		$code.= '<li><a href="'.$root.'?controller=admin&action=eventsDt">Historial de eventos</a></li>';
            $code.= '</ul>';
            
            return $code;
	}

	function cliente($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "clientes").'><a href="'.$root.'?controller=clientes&action=clientesDt">Cliente</a>';
                $code.= '<ul>';
                $code.= '<li><a href="'.$root.'?controller=clientes&action=clientesDt">Cliente</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=clientes&action=clientesAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=clientes&action=buyerClassDt">Buyer Class</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=clientes&action=buyerClassAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=clientes&action=channelDt">Channels</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=clientes&action=channelAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}
	
	function region($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "lugares").'><a href="'.$root.'?controller=lugares&action=regionesDt">Region</a>';
                $code.= '<ul>';
                $code.= '<li><a href="'.$root.'?controller=lugares&action=regionesDt">Region</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="#">Nueva</a></li>';	
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=lugares&action=ciudadesDt">Ciudad</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=lugares&action=ciudadesAddForm">Nueva</a></li>';	
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=lugares&action=comunasDt">Comuna</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=lugares&action=comunasAddForm">Nueva</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';		
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}
	
	function modelos($root, $permiso, $controller)
	{	 
            $code = '<li '. $this->getCurrentClass($controller, "models").'><a href="'.$root.'?controller=models&action=modelsDt">Modelos</a>';
                $code.= '<ul>';
                $code.= '<li><a href="'.$root.'?controller=models&action=modelsDt">Modelos</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="#">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '<li><a href="'.$root.'?controller=models&action=estadosDt">Estado</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=models&action=estadosAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}
	
	function brand($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "brands").'><a href="'.$root.'?controller=brands&action=brandsDt">Brand</a>';
                $code.= '<ul>';
                $code.= '<li><a href="'.$root.'?controller=brands&action=brandsDt">Brand</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=brands&action=brandsAddForm">Nueva</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}
	
	function bu($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "categories").'><a href="'.$root.'?controller=categories&action=buDt">BU</a>';
                $code.= '<ul>';
                
                $code.= '<li><a href="'.$root.'?controller=categories&action=buDt">BU</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=categories&action=buAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '<li><a href="'.$root.'?controller=categories&action=categoryDt">Category</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=categories&action=categoryAddForm">Nueva</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '<li><a href="'.$root.'?controller=categories&action=gbuDt">GBU</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=categories&action=gbuAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
        }
	
	function segment($root, $permiso, $controller)
	{
            $code = '<li '. $this->getCurrentClass($controller, "segments").'><a href="'.$root.'?controller=segments&amp;action=segmentsDt">Segment</a>';
                $code.= '<ul>';

                $code.= '<li><a href="'.$root.'?controller=segments&amp;action=segmentsDt">Segment</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=segments&amp;action=segmentsAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '<li><a href="'.$root.'?controller=segments&amp;action=subSegmentsDt">Sub Segment</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=segments&amp;action=subSegmentsAddForm">Nuevo</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '<li><a href="'.$root.'?controller=segments&amp;action=microSegmentsDt">Micro Segment</a>';
                    if($permiso == 1){
                        $code.= '<ul>';
                        $code.= '<li><a href="'.$root.'?controller=segments&amp;action=microSegmentsAddForm">Nueva</a></li>';
                        $code.= '</ul>';
                    }
                $code.= '</li>';

                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
	}	
		
	function sellout($root, $controller)
	{ 	
            $code = '<li><a href="#">Precios</a>';
            $code.= '<ul class="subnav">';
                $code.= '<li><a href="'.$root.'#">Nuevo Precio</a></li>';
                #$code.= '<li><a href="'.$root.'#">Editar Precios</a></li>';
                #$code.= '<li><a href="'.$root.'#">Carga Masiva</a></li>';
                $code.= '</ul>';
            $code.= '</li>';
            
            return $code;
        }
	 
	function indicadores($root, $controller)
	{ 
            $code = '<li><a href="'.$root.'#">Indicadores</a>';
            $code.= '</li>';
            
            return $code;
	}
        
        public function test($root, $controller)
	{ 
            $code = '<li><a href="'.$root.'#">Test</a>';
            $code.= '</li>';
            
            return $code;
	}
        
        public function exportar($root, $controller)
	{ 
            /*
             * SOLO POR LOGICA DE SISTEMA
             */
            $code = '';            
            return $code;
	}
}
?>