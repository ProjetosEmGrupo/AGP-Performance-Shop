<?php 

class EnderecoController extends \HXPHP\System\Controller
{
	public function __construct($configs){
		parent::__construct($configs);
		$this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
		$this->auth->redirectCheck();
		$user_id = $this->auth->getUserId();
		$this->view->setVar('user', User::find($user_id));
		
	}


	public function cadastrarEnderecoAction(){
		$this->view->setFile('index');
		$post = $this->request->post();
		if(!is_null($post)){
			$cadastra = Address::cadastrar($post, $this->auth->getUserId());
			if($cadastra->status === true){
				$mensagem = array('success', 'Endereço cadastrado!', 'O endereço foi cadastrado com sucesso.');
			}
			else{
				$mensagem = $cadastra->errors;
			}
		}		
		else{
			$mensagem = array(
                'danger',
                'Um mais campos Vazios!',
                'Por favor preencha todos os campos corretamente para cadastrar.');
		}
		$this->load('Helpers\Alert', $mensagem);
	}

	public function exibirEnderecoAction(){
		$this->view->setFile('listaEnderecos');
		$user_id = $this->auth->getUserId();
		$sql = 'select * from addresses where user_id = '. $user_id. ' and cep < 99999999';
		$teste = Address::find_by_sql($sql);								  // 02344040
		
		$this->view->setVar('enderecos', $teste);
	}

	public function excluirAction($id){
		$this->view->setFile('listaEnderecos');
		$var = Address::excluir($id, $this->auth->getUserId());
		if($var->status == true){
			$this->view->setFile('index');
			$mensagem = array(
                'success',
                'Endereço Excluído com sucesso',
                'O endenreço foi excluído com sucesso caso deseje visualizar os endereços disponiveis basta clicar em Exibir Endereços');
		}
		else{
			$mensagem = array(
                'danger',
                'Erro na exclusão',
                'Não foi possível excluir o endereço. Por Favor contate o Administrador.');
		}
		$this->load('Helpers\Alert', $mensagem);

	}

	public function alterarAction($id){
		$this->view->setFile('alterar');
		$endereco = Address::find($id);
		//var_dump($endereco);
		$this->view->setVars(['endereco' => $endereco, 'id' => $id]);	


	}	

	public function redefinirEnderecoAction($id){
		$this->view->setFile('alterar');
		$post = $this->request->post();
		$alterar = Address::alterar($post, $id);

		if($alterar->status === true){
			$mensagem = array(
                'success',
                'Sucesso na alteração',
                'Endereço alterado com sucesso.');
			$this->view->setFile('index');

		}
		else{
			$mensagem = array(
                'danger',
                'Erro na alteração',
                'Não foi possível alterar o endereço. Por favor preencha os campos de maneira correta..');

		}

	$this->load('Helpers\Alert', $mensagem);	
	}
	
}