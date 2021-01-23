<?php  
class EstaticoController extends \HXPHP\System\Controller
{
	public function __construct($configs){
		parent::__construct($configs);
		$this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
		$this->auth->redirectCheck(true);
	}

	public function entregaAction(){
		$this->view->setFile('entrega');
	}
	public function formasPagamentoAction(){
		$this->view->setFile('formasdepagamento');
	}
	public function garantiaAction(){
		$this->view->setFile('garantia');
	}
	public function missaoAction(){
		$this->view->setFile('mvv');
	}
	public function politicaPrivacidadeAction(){
		$this->view->setFile('politicaprivacidade');
	}
	public function quemSomosAction(){
		$this->view->setFile('quemsomos');
	}
	public function trocaAction(){
		$this->view->setFile('troca');
	}
}


