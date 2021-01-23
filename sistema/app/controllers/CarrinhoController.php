<?php 

class CarrinhoController extends \HXPHP\System\Controller
{
    public function __construct($configs){
            parent::__construct($configs);
            $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);

            $user_id = $this->auth->getUserId();
            if(!empty($user)){
                    $this->view->setVar('user', User::find($user_id));
            }
            $this->view->setVar('departament', Departament::all());	
            $this->view->setVar('produtos', Product::all());	

            $this->view->setFile('index');
            $this->view->setFile('carrinho');
    }

    public function adicionarItemAction($produto_id = null){

            if(isset($produto_id) && !empty($produto_id)){
                    if(isset($_COOKIE['sec_session_id']) && !empty($_COOKIE['sec_session_id'])){


                            $c = array('hash' => $_COOKIE['sec_session_id'] , 'product_id' => $produto_id, 'amount' => $this->request->post('amount'));

                            $cadastro = Cart::create($c);
                    }
            }
            else{
                    //produto id vazio
            }
            //header("Location: http://localhost/sistema/carrinho/");
            $this->view->setFile('carrinho');
    }


    public function removeProdutoAction($i = null){
            $this->view->setFile('carrinho');
            if(isset($i) && !empty($i)){
                    $busca = Cart::find_by_seq($i);
                    $busca->delete();
            }

    }
}