<?php 
/**
* 
*/
class Product extends \HXPHP\System\Model
{

    static $validates_presence_of = array(
        array('name', 'message' => 'O campo Titulo é um campo obrigatório.'),
        array('price', 'message' => 'O campo Preço é um campo obrigatório.'),
        array('stock', 'message' => 'O campo Quantidade é um campo obrigatório.'),
        array('supplier_id', 'message' => 'O campo Marca é um campo obrigatório.'),
        array('departament_id', 'message' => 'O campo Departamento é um campo obrigatório.'),
        array('description', 'message' => 'O campo Descrição é um campo obrigatório.'),
        array('img1', 'message' => 'O campo Imagem 1 é um campo obrigatório.'),
        array('img2', 'message' => 'O campo Imagem 2 é um campo obrigatório.'),
        array('img3', 'message' => 'O campo Imagem 3 é um campo obrigatório.')
        );

    public static function cadastrar(array $post){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $callbackOBJ->errors = array();
        $cadastrarProduto = self::create($post);    
        if($cadastrarProduto->is_valid()){
            $callbackOBJ->status = true;
            return $callbackOBJ;
        }  

        $errors = $cadastrarProduto->errors->get_raw_errors();

        foreach ($errors as $field => $message) {
            array_push($callbackOBJ->errors, $message[0]);               
        }
        return $callbackOBJ;
    }//fim metodo

    public static function alterar(array $post, $produto_id){
        $callbackOBJ = new \stdclass;
        $callbackOBJ->status = false;
        $callbackOBJ->imgs = array();

        $produto = self::find($produto_id);
        $produto->name = $post['name'];
        $produto->price = $post['price'];
        $produto->stock = $post['stock'];
        $produto->supplier_id = $post['supplier_id'];
        $produto->description = $post['description'];
        $produto->filter = $post['filter'];

        if(isset($post['img1']) && !empty($post['img1'])){
            $img1 = array('img1' => $produto->img1);
            $callbackOBJ->imgs = array_merge($callbackOBJ->imgs, $img1);
            $produto->img1 = $post['img1']; 
        }

        if(isset($post['img2']) && !empty($post['img2'])){
            $img2 = array('img2' => $produto->img2);
            $callbackOBJ->imgs =  array_merge($callbackOBJ->imgs, $img2);
            $produto->img2 = $post['img2']; 
        }

        if(isset($post['img3']) && !empty($post['img3'])){
            $img3 = array('img3' => $produto->img3);
            $callbackOBJ->imgs = array_merge($callbackOBJ->imgs, $img3);
            $produto->img3 = $post['img3'];
        }
        $callbackOBJ->status = true;
        $produto->save(false);
        return $callbackOBJ;
    }

}//fim classe

   /* public static function login(array $post){
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

