<?php 

class RecuperarController extends \HXPHP\System\Controller
{
	public function __construct($configs){
		parent::__construct($configs);
		$this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
		$this->auth->redirectCheck(true);
		$this->view->setFile('index');
		$this->load('Modules\Messages', 'password-recovery');
		$this->messages->setBlock('alerts');
	}


	public function solicitarAction(){
		$this->view->setFile('index');
		$this->load('Modules\Messages', 'password-recovery');
		$this->messages->setBlock('alerts');
		$this->request->setCustomFilters(array('email' => FILTER_VALIDATE_EMAIL));
		$email = $this->request->post('email');
		$error = null;
		$sucess = null;
		
			$usuario = User::pesquisar($email);
			if($usuario->status === false){
				$error = $this->messages->getByCode($usuario->code);
			}
			else{

				$this->load('Services\PasswordRecovery', $this->configs->site->url . $this->configs->baseURI . 'recuperar/alterar/' . $usuario->user->id .'/' . $usuario->user->password);		
				

				$message = $this->messages->messages->getByCode('link-enviado', array('message' => array($usuario->user->name, $this->passwordrecovery->link, $this->passwordrecovery->link)));

				$this->load('Services\Email');
				$enviodoemail = $this->email->send($usuario->user->email, 
					null, 
					$message['message'], 
					array('email' => $this->configs->mail->from_mail, 'remetente' => $this->configs->mail->from));
				$sucess = $this->messages->getByCode('link-enviado'); 
			}	
		if(!is_null($error)){
			$this->load('Helpers\Alert', $error);
		}
		else{
			$this->view->setFile('blank');
			$this->load('Helpers\Alert', $sucess);
			$teste = $this->configs->mail->from;
			
			echo $message['message'];
		}
	}

	public function redefinirAction($id){
		$this->view->setFile('redefinir');
		$password = $this->request->post();

		
			if($password['password'] == $password['confirmpassword'] && $password['password'] !== ""){
				$alteracao = User::redefinirSenha($id, $password['password']);			
										
				$this->view->setFile('blank');
				$mensagem = array(
                'success',
                'Sucesso',
                'O senha redefinida com sucesso');
                
                $this->load('Helpers\Alert', $mensagem);

			}
			else{
				$this->view->setVar('id', $id);
				$mensagem = array(
                'danger',
                'Erro',
                'O senhas não coincidem. Por favor digite as senhas novamente');
                
                $this->load('Helpers\Alert', $mensagem);
				 
			}
			
			
			$this->load('Messages\password-recovery', $mensagem);
	}

	public function alterarAction($id, $token){
		$this->view->setFile('redefinir');
		$password = $this->request->post();
		
		if(is_null($password)){	
		$teste = User::find_by_id($id);
			if($teste->password === $token){
				$this->view->setVar('id', $id);
			}
			else{
				$mensagem = array(
                'danger',
                'Erro',
                'O link de redefinição de senha é inválido');
                $this->view->setFile('blank');
                $this->load('Helpers\Alert', $mensagem);
			}	
		}
 	}

 	
}
