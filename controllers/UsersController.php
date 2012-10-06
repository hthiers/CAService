<?php
class UsersController extends ControllerBase
{
	public function logIn()
	{
            //Parametros login form
            isset($_POST['txtusername'],$_POST['txtpassword'],$usuario1,$datos,$priv);
            $username = $_POST['txtusername'];
            $password = md5($_POST['txtpassword']);
            
            //Incluye el modelo que corresponde
            require_once 'models/UsersModel.php';

            //Creamos una instancia de nuestro "modelo"
            $account = new UsersModel();

            //Le pedimos al modelo que busque la cuenta de usuario (nombre de usuario y contraseña)
            $result = $account->getUserAccount($username, $password);
            $error = $result->errorInfo();
            $rows = $result->rowCount();

            $values = $result->fetch(PDO::FETCH_ASSOC);
            
            //Segun resultado iniciamos sesion (ir a sistema) o lanzamos error (volver a home)
            if(isset($values['id_user']) == true && $values['id_user'] > 0)
            {
                //Set timezone
                date_default_timezone_set($this->timezone);

                //Start session
                $session = FR_Session::singleton();
                $session->id_user = $values['id_user'];
                $session->id_tenant = $values['id_tenant'];
                $session->id_profile = $values['id_profile'];
                $session->code_user = $values['code_user'];
                $session->name_user = $values['name_user'];

                header("Location: ".$this->root."?controller=Projects&action=projectsDt");
            }
            else
                header("Location: ".$this->root."?controller=index&action=indexErrorLogin&errorCode=".$error[0].", ".$rows.", $values[id_user]");
	}

	public function logOut()
	{
            //Finish session
            $session = FR_Session::singleton();
            $session->destroy();
            
            header("Location: ".$this->root);		
	}

        public function userProfile($error_flag = 0, $message = "")
        {
            $session = FR_Session::singleton();

            $session_id = $session->id;
            $session_user = $session->usuario;
            
            require_once 'models/UsersModel.php';
            $account = new UsersModel();
            
            $data['user_data'] = $account->getUserInfo($session_id, $session_user);
            
            //Titulo pagina
            $data['titulo'] = "MI CUENTA";

            //Controller
            $data['controller'] = "users";
            //Action edit
            $data['action'] = "userProfileEditForm";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

            //Finalmente presentamos nuestra plantilla
            $this->view->show("user_profile.php", $data);
        }
        
        public function userProfileEditForm()
        {
            if($_POST)
            {
                $data['id_user'] = $_POST['id_user'];
                $data['nick_user'] = $_POST['nick_user'];
                $data['name_user'] = $_POST['name_user'];
                $data['apellidop_user'] = $_POST['apellidop_user'];
                $data['apellidom_user'] = $_POST['apellidom_user'];

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "mi cuenta > EDICI&Oacute;N";

                //time value for submit control
                $data['orig_timestamp'] = microtime(true);
                $session = FR_Session::singleton();
                $session->orig_timestamp = $data['orig_timestamp'];
                
                //Controller
                $data['controller'] = "users";
                //Action edit
                $data['action'] = "userProfileEdit";
                //Action back
                $data['action_b'] = "userProfile";

                $this->view->show("user_profile_edit.php", $data);
            }
            else
                $this->userProfile();
        }
        
        public function userProfileEdit()
        {
            $session = FR_Session::singleton();
            
            if($_POST['form_timestamp'] != $session->orig_timestamp)
            {
                //Avoid resubmit
                $session->orig_timestamp = $_POST['form_timestamp'];

                $id_user = $this->utils->cleanQuery($_POST['id_user']);
                $nick_user = $this->utils->cleanQuery($_POST['nick_user']);
                $name_user = $this->utils->cleanQuery($_POST['name_user']);
                $apellidop_user = $this->utils->cleanQuery($_POST['apellidop_user']);
                $apellidom_user = $this->utils->cleanQuery($_POST['apellidom_user']);
                $current_pass = $this->utils->cleanQuery($_POST['password_actual']);
                $new_pass = $this->utils->cleanQuery($_POST['password_nuevo_b']);

                //Incluye el modelo que corresponde
                require_once 'models/UsersModel.php';
                $model = new UsersModel();

                //Le pedimos al modelo todos los items
                $result = $model->editUserInfo($id_user, $nick_user, $name_user, $apellidop_user, $apellidom_user);
                $error = $result->errorInfo();

                if($error[0] == 00000){
                    if($current_pass != "" || $new_pass != ""){
                        $info = $model->getUserInfoPassword($id_user, $nick_user);
                        $values = $info->fetch(PDO::FETCH_ASSOC);    
                        
                        if(md5($current_pass) == $values['CLAVE']){
                            $result = $model->editUserPassword($id_user, $nick_user, $new_pass);
                            $error_pass = $result->errorInfo();
                            
                            if($error_pass[0] == 00000)
                                $this->userProfile(1);
                            else
                                $this->userProfile(10, "Ha ocurrido un error: <i>".$error_pass[2]."</i>");
                        }
                        else
                            $this->userProfile(10, "La contrase&ntilde;a actual es incorrecta!");
                    }
                    else
                        $this->userProfile(1);
                }
                else
                    $this->userProfile(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                $this->userProfile();
            }
        }
        
        public function userPasswordEditForm()
        {
            if(isset($_GET['id']) == true && isset($_GET['usuario']) == true)
            {
                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "mi perfil > cambio contrase&ntilde;a";

                $data['id_user'] = $_GET['id'];
                $data['nick_user'] = $_GET['usuario'];
                
                //time value for submit control
                $data['orig_timestamp'] = microtime(true); //debug
                $session = FR_Session::singleton();
                $session->orig_timestamp = $data['orig_timestamp'];
                
                //Controller
                $data['controller'] = "users";
                //Action edit
                $data['action'] = "userPasswordEdit";
                //Action back
                $data['action_b'] = "userProfile";

                $this->view->show("user_password_edit.php", $data);
            }
            else
                $this->userProfile(2);
        }
        
        public function userPasswordEdit()
        {
            $session = FR_Session::singleton();
            
            if($_POST['form_timestamp'] != $session->orig_timestamp)
            {
                    //Avoid resubmit
                    $session->orig_timestamp = $_POST['form_timestamp'];
                
                    $id_user = $this->utils->cleanQuery($_POST['id_user']);
                    $nick_user = $this->utils->cleanQuery($_POST['nick_user']);
                    $password_actual = $this->utils->cleanQuery($_POST['password_actual']);
                    $password_nuevo_a = $this->utils->cleanQuery($_POST['password_nuevo_a']);
                    $password_nuevo_b = $this->utils->cleanQuery($_POST['password_nuevo_b']);

                    //Incluye el modelo que corresponde
                    require_once 'models/UsersModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $model = new UsersModel();

                    //VALIDATION CASES
                    $password_real = $model->getUserInfoPassword($id_user, $nick_user);
                    $values = $password_real->fetch(PDO::FETCH_ASSOC);                    
                    
                    if(md5($password_actual) == $values['CLAVE'])
                    {
                        if($password_nuevo_a == $password_nuevo_b)
                        {
                            //Le pedimos al modelo todos los items
                            $result = $model->editUserPassword($id_user, $nick_user, $password_nuevo_b);

                            //catch errors
                            $error = $result->errorInfo();

                            if($error[0] == 00000)
                                $this->userProfile(1);
                            else
                                $this->userProfile(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        }
                        else
                        {
                                //Destroy POST
                                unset($_POST);

                                //error password no coinciden
                                $this->userProfile(5);
                        }
                    }
                    else
                    {
                            //Destroy POST
                            unset($_POST);

                            //error password invalido
                            $this->userProfile(4);
                    }
            }
            else
            {
                //access error
                $this->userProfile();
            }
        }
        
}
?>
