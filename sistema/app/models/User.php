<?php 
/**
* 
*/
class User extends \HXPHP\System\Model
{

   static $validates_presence_of = array(
    array('name', 'message' => 'O campo nome é um campo obrigatório.'),
    array('email', 'message' => 'O campo E-mail é um campo obrigatório.'),

    array('password', 'message' => 'Senhas ou E-mails diferentes. Por favor confira-os.'),

    array('rg', 'message' => 'O campo RG é um campo obrigatório.'),
    array('cpf', 'message' => 'O campo CPF é um campo obrigatório.')
    );
   //Validando campos únicos 
   static $validates_uniqueness_of = array(
    array(
        array('email'), 'message' => 'Já existe um usuário com esse E-mail'),
    array(
        array('cpf'), 'message' => 'Já existe um usuário com esse CPF'),
    array(
        array('rg'), 'message' => 'Já existem um usuário com esse RG')       
    );



   public static function cadastrar(array $post){
    $callbackOBJ = new \stdclass;
    $callbackOBJ->user = null;
    $callbackOBJ->status = false;
    $callbackOBJ->errors = array();
    if($post['confirmpassword'] != $post['password'] && $post['confirmemail'] != $post['email']){        
        $password = array('password' => null, 'salt' => null);
        $post = array_merge($post, $password);
    }
    else{            
        $password = \HXPHP\System\Tools::hashHX($post['password']);
        $post = array_merge($post, $password);
    }
    unset($post['confirmpassword']);
    unset($post['confirmemail']);
    $cadastrar = self::create($post);    
    if($cadastrar->is_valid()){
        $callbackOBJ->user = $cadastrar;
        $callbackOBJ->status = true;
        return $callbackOBJ;
    }
    $errors =  $cadastrar->errors->get_raw_errors();

    foreach ($errors as $field => $message) {
        array_push($callbackOBJ->errors, $message[0]);               
    }
    return $callbackOBJ;
    }//fim metodo

    public static function logar(array $post){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->user = null;
        $callbackOBJ->status = false;
        $callbackOBJ->code = null;
        $user = self::find_by_email($post['email']);
        if(!is_null($user)){
            $password = \HXPHP\System\Tools::hashHX($post['password'], $user->salt);        
            if($password['password'] === $user->password){
              
                $callbackOBJ->user = $user;
                $callbackOBJ->status = true;
                var_dump($user);
            }
            else{
                $callbackOBJ->code = 'dados-incorretos';
                return $callbackOBJ;
            }
        }        
        else{
            $callbackOBJ->code = 'usuario-inexistente';
        }
          return $callbackOBJ;
    }

    public static function pesquisar($email){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $callbackOBJ->code = null;
        if(!is_null($email)){
            $user = self::find_by_email($email);
        }      
        if(!is_null($user)){
            $callbackOBJ->user = $user;
            $callbackOBJ->status = true;
        }   
        else{
            $callbackOBJ->code = "nenhum-usuario-encontrado";
        }     
        return $callbackOBJ;
    }//fim metodo

    public static function redefinirSenha($id, $password){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->user = null;
        $callbackOBJ->status = false;
        $callbackOBJ->code = null;
        $user = self::find_by_id($id);  
        if(!is_null($user)){
            $password = \HXPHP\System\Tools::hashHX($password); 
            $user->password = $password['password'];
            $user->salt = $password['salt'];
            $user->save(false);
            $callbackOBJ->user = $user;
            $callbackOBJ->code = array('success', 'Redenifição feita com sucesso!', 'Senha Alterada com sucesso.');
            $callbackOBJ->status = true;
        }
        else{
            $callbackOBJ->code = array('danger', 'Erro', 'Falha ao alter a senha!');
        }                    
    return $callbackOBJ;
    }

    public static function validar($id, $token){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $callbackOBJ->code = null;
        $usuario = self::find_by_id($id);
        $password = \HXPHP\System\Tools::hashHX($usuario->password, $usuario->salt);  
        if($password['password'] === $token){
            $password = \HXPHP\System\Tools::hashHX($password);
            $usuario->password = $password['password'];
            $usuario->salt = $password['salt'];
            $usuario->save(false);
            $callbackOBJ->user = $usuario;
            $callbackOBJ->status = true;
        }
        else{
            $callbackOBJ->code = "token-invalido";
        }
        return  $callbackOBJ;
    }

    public static function redefinirEmail($id, $email){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $callbackOBJ->code = null;
        $callbackOBJ->user = null;
        $usuario = self::find($id);
        if(!is_null($usuario)){
            $usuario->email = $email;
            $usuario->save(false);
            $callbackOBJ->status = true;
            $callbackOBJ->code = array('success', 'Redenifição feita com sucesso!', 'Email Alterado com sucesso.');
            $callbackOBJ->user = $usuario; 
        }
        else{
            $callbackOBJ->code = array('danger', 'Redefinição não concluida!', 'Usuário não encontrado.');
        }
        return $callbackOBJ;
    }
}
