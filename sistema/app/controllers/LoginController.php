<?php

class LoginController extends \HXPHP\System\Controller {

    public function __construct($configs) {
        parent::__construct($configs);
        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
    }

    public function indexAction() {
        $this->auth->redirectCheck(true);
    }

    public function logarAction() {
        $this->auth->redirectCheck(true);
        $this->view->setFile('index');

        //if(!empty($this->request->post()))
        //$this->request->setCustomFilters(array('email' => FILTER_VALIDATE_EMAIL));
        $post = $this->request->post();
        if (!empty($post))
            $logarUsuario = User::logar($post);

        if ($logarUsuario->status === true) {
            $this->auth->login($logarUsuario->user->id, $logarUsuario->user->email, null);
        } else {
            $this->load('Modules\Messages', 'auth');
            $this->messages->setBlock('alerts');
            $error = $this->messages->getByCode($logarUsuario->code);
            $this->load('Helpers\Alert', $error);
        }

        //if($logarUsuario->status == false){
        //			$this->load('Helpers\Alert', array(
        //				'danger',
        //				'Não Foi possivel efetuar seu cadastro. Verifique os erros abaixo.',
        //				$logarUsuario->errors));
        //		}
        //		else{
        //			$this->auth->login($logarUsuario->user->id, $logarUsuario->user->email, null);
        //		}
    }

    public function sairAction() {
        return $this->auth->logout();
    }

}
