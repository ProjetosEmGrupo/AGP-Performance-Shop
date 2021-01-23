<?php 

class ProdutoController extends \HXPHP\System\Controller
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
		$this->view->setFooter('footerListas');
		$this->view->setFile('index');
	}

	public function DetalhesAction($produto_id){
		$this->view->setFile('index');
		
		if(isset($produto_id) && !empty($produto_id)){
		$produto = Product::find($produto_id);
		if(!empty($produto)){
			$this->view->setVar('produto', $produto);
			$departamento = Departament::find($produto->departament_id);
			$this->view->setVar('dep', $departamento);

			$avaliacao = Review::find_all_by_product_id($produto->id);
			if(!empty($avaliacao)){
			$this->view->setVar('avaliacao' , $avaliacao);
			
		}
		
		}
		else{
			$this->view->setFile('error');
		}
	}
	else{
		$this->view->setFile('error');
	}


	}
}