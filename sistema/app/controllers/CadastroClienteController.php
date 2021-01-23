<?php

class CadastroClienteController extends \HXPHP\System\Controller {

    public function __construct($configs) {
        parent::__construct($configs);
        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
        $this->auth->redirectCheck(true);
    }

    public function cadastrarAction() {
        $this->view->setFile('index');

        if (!empty($this->request->post())) {
            $this->request->setCustomFilters(['email' => FILTER_VALIDATE_EMAIL,
                'confirmemail' => FILTER_VALIDATE_EMAIL]);
        }

        $cadastrarUsuario = User::cadastrar($this->request->post());

        if ($cadastrarUsuario->status == false) {
            $this->load('Helpers\Alert', array(
                'danger',
                'Não Foi possivel efetuar seu cadastro. Verifique os erros abaixo.',
                $cadastrarUsuario->errors));
        } else {
            $this->auth->login($cadastrarUsuario->user->id, $cadastrarUsuario->user->email, null);
        }
    }

}
