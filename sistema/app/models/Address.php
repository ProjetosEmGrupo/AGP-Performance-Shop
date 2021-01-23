<?php 

class Address extends \HXPHP\System\Model
{

   static $validates_presence_of = array(
    array('cep', 'message' => 'O campo nome é um campo obrigatório.'),
    array('street', 'message' => 'O campo rua é um campo obrigatório.'),
    array('number', 'message' => 'O campo numero é um campo obrigatório.'),
    array('city', 'message' => 'O campo cidade é um campo obrigatório.'),
    array('uf', 'message' => 'O campo estado é um campo obrigatório.'),
    array('neighbourhood', 'message' => 'O campo bairro é um campo obrigatório.')
    );
   //Validando campos únicos 
  
   public static function cadastrar(array $post, $id){
    $callbackOBJ = new \stdclass;
    $callbackOBJ->address = null;
    $callbackOBJ->status = false;
    $callbackOBJ->errors = array();
        $user = array('user_id' => $id);
      $post = array_merge($post, $user);
    $cadastrar = self::create($post);    
    if($cadastrar->is_valid()){
        $callbackOBJ->adress = $cadastrar;
        $callbackOBJ->status = true;
        return $callbackOBJ;
    }
    $errors =  $cadastrar->errors->get_raw_errors();

    foreach ($errors as $field => $message) {
        array_push($callbackOBJ->errors, $message[0]);               
    }
    return $callbackOBJ;
    }//fim metodo

    public static function excluir($id, $user_id){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $teste = self::find_by_id($id);
        if($teste->user_id == $user_id){
            $teste->cep = $teste->cep .'00';
            $teste->save();
            $callbackOBJ->status = true;
        }
        return $callbackOBJ;
    }


    public static function alterar($post, $id){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;

        if(!is_null($post)){
            $alterar = self::find($id);
            $alterar->neighbourhood = $post['neighbourhood'];
            $alterar->city = $post['city'];
            $alterar->uf = $post['uf'];
            $alterar->number = $post['number'];
            $alterar->complement = $post['complement'];
            $alterar->cep = $post['cep'];
            $alterar->street = $post['street'];
            $alterar->save(true);
            $callbackOBJ->status = true;
            }
        return $callbackOBJ;
    }
/*
    public static function login(array $post){
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
            $callbackOBJ->code = array('danger', 'Redefinição não concluida!', 'Usuário não encontrado.');
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
    */
}
