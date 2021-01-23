<?php


class ContaController extends \HXPHP\System\Controller {

    public function __construct($configs) {
        parent::__construct($configs);
        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
        $this->auth->redirectCheck();
        $user_id = $this->auth->getUserId();
        $this->view->setVar('user', User::find($user_id));
    }

    public function editarEmailAction() {
        $this->view->setFile('editarEmail');
        $post = $this->request->post();
        if (!is_null($post)) {
            $this->request->setCustomFilters(array('email' => FILTER_VALIDATE_EMAIL, 'confirmemail' => FILTER_VALIDATE_EMAIL));
            if ($post['confirmEmail'] == $post['email']) {
                $altera = User::redefinirEmail($this->auth->getUserId(), $post['email']);
                if ($altera->status === true) {
                    $this->auth->login($altera->user->id, $altera->user->email);
                }
                $mensagem = $altera->code;
            } else {
                $mensagem = array('danger', 'Emails Diferentes!',
                    'Por favor digite os emails novamente para redefinir.');
            }
        } else {
            $mensagem = array('danger', 'Erro no sistema!',
                'Por favor contate o administrador do sistema para poder fazer a alteração.');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function editarSenhaAction() {
        $this->view->setFile('editarSenha');
        $post = $this->request->post();
        if (!is_null($post)) {
            if ($post['confirmpassword'] == $post['password']) {
                $altera = User::redefinirSenha($this->auth->getUserId(), $post['password']);
                if ($altera->status === true) {
                    $this->view->setFile('index');
                }
                $mensagem = $altera->code;
            } else {
                $mensagem = array('danger', 'Senhas Diferentes!',
                    'Por favor digite as Senhas novamente para redefinir.');
            }
        } else {
            $mensagem = array(
                'danger',
                'Erro no sistema!',
                'Por favor contate o administrador do sistema para poder fazer a alteração.');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function informacaoAction() {
        $this->view->setFile('index');
    }

    public function pedidoAction() {
        $this->view->setFile('pedido');

        $busca = Request::find_all_by_user_id($this->auth->getUserId());
        $this->view->setVar('pedidos', $busca);
    }

    public function enderecoAction() {
        $this->view->setFile('endereco');
    }

    public function redefinirEmailAction() {
        $this->view->setFile('editarEmail');
    }

    public function redefinirSenhaAction() {
        $this->view->setFile('editarSenha');
    }

}
