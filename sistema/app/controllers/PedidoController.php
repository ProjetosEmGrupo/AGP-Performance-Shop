<?php

use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Agente;
class PedidoController extends \HXPHP\System\Controller {

    public function __construct($configs) {
        parent::__construct($configs);
        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
        $this->auth->redirectCheck(false);
        $this->view->setFile('endereco');
        $this->load('Modules\Messages', 'password-recovery');
        $this->messages->setBlock('alerts');
    }

    public function enderecoEntregaAction($address_id = null) {
        $this->view->setFile('endereco');
        $sql = 'select * from addresses where user_id = ' . $this->auth->getUserId() . ' and cep < 99999999';
        $enderecos = Address::find_by_sql($sql);            // 02344040

        $this->view->setVar('enderecos', $enderecos);
        if (isset($address_id) && !empty($address_id)) {

            $endereco = Address::find($address_id);
            $this->view->setVar('endereco', $endereco);
            $f = $endereco->uf;
            if ($f === 'SP') {
                $fretes = array('pac' => 20.40, 'sedex' => 21.50);
            } elseif ($f === 'DF' or $f === 'ES' OR $f === 'GO' OR $f === 'MS' OR $f === 'MG' OR $f === 'PR' OR $f === 'RJ' OR $f === 'RS' OR $f === 'SC') {
                $fretes = array('pac' => 22.60, 'sedex' => 33.30);
            } elseif ($f === 'MT' or $f === 'BA') {
                $fretes = array('pac' => 27.20, 'sedex' => 44.90);
            } elseif ($f === 'CE' or $f === 'PA' OR $f === 'PE' OR $f === 'TO') {
                $fretes = array('pac' => 30.00, 'sedex' => 56.40);
            } elseif ($f === 'AL' or $f === 'AP' OR $f === 'AM' OR $f === 'MA' OR $f === 'PB' OR $f === 'PI' OR $f === 'RN' OR $f === 'RO' OR $f === 'SE') {
                $fretes = array('pac' => 32.70, 'sedex' => 65.10);
            } elseif ($f === 'RR' or $f === 'AC') {
                $fretes = array('pac' => 37.30, 'sedex' => 76.70);
            }
            $this->view->setVar('fretes', $fretes);
        }
    }

    public function pagamentoAction($address_id = null, $request_id = null) {
        $this->view->setFile('opcaopagamento');
        $frete = $this->request->post();


        if (isset($address_id) && !empty($address_id) && isset($frete['frete']) && !empty($frete['frete'])) {
            
            $p = array('user_id' => $this->auth->getUserId(), 'status' => 'Aguardando o Pagamento');
            $pedido = Request::cadastrar($p);
            if ($pedido->status === true) {
                $parametro = array('cookie' => $_COOKIE['sec_session_id'],
                    'request_id' => $pedido->request['max(id)']);

                $itens = ItemRequest::cadastrar($parametro);
                if ($itens->status == true) {
                    $teste = explode('-', $this->request->post('frete'));
                    $v = explode(',', $teste[1]);
                    $f = array('request_id' => $parametro['request_id'], 'address_id' => $address_id, 'send_mode' => $teste[0], 'value_freight' => implode('.', $v));
                    $frete = Freight::cadastrar($f);
                    $request_id = $parametro['request_id'];
                }
            }
        } else{
        }
        $this->view->setVar('request_id', $request_id);
    }

    public function pagarAction($request_id, $parcelas = null) {
        $tipoPagamento = $this->request->post();

        if ($tipoPagamento['pagamento'] === 'cartao' or isset($parcelas) or ! empty($parcelas)) {
            $this->view->setFile('cartao');
            if (isset($parcelas) && !empty($parcelas)) {
                $this->view->setVar('parcela', $parcelas);
            }

            $this->view->setVar('request_id', $request_id);
        } elseif($tipoPagamento['pagamento'] === 'boleto') {
            $this->view->setFile('resumocompra');
            
             $p = array('request_id' => $request_id,
                    'type_payment' => 'boleto',
                    'number_validation' => rand(1000000, 9999999),
                    'identification' => 'Banco do Brasil');
                $cadastro = Payment::cadastrar($p);
                $frete = Freight::find_by_request_id($request_id);
                $endereco = Address::find($frete->address_id);
                $produtos = ItemRequest::find_all_by_request_id($request_id);
                $pedido = Request::find($request_id);
                $pedido->status = "Pago";
                $pedido->save();
                $pagamento = Payment::find_by_request_id($request_id);
                $this->view->setVar('pagamento', $pagamento);
                $this->view->setVar('frete', $frete);
                $this->view->setVar('endereco', $endereco);
                $this->view->setVar('produtos', $produtos);
                
        }
    }
    
      public function imprimirBoletoAction($request_id){
          $this->view->setFile('blank');
          $this->view->setTemplate(false);
          $cliente = User::find( $this->auth->getUserId());
          $e = Freight::find_by_request_id($request_id);
          $end = Address::find($e->address_id);
          $pagamento = Payment::find_by_request_id($request_id);
           $sacado = new Agente($cliente->name, $cliente->cpf, ' ',  $end->cep, $end->city, $end->uf);
        $cedente = new Agente('AGP Performace Shop', '09.386.496/0001-03', 'R. da Grota, 483 - Tucuruvi ', '02206-010', 'São Paulo', 'SP');
       $data = date('Y-m-d', strtotime('+10 days', strtotime($pagamento->data_payment)));
       $total = ItemRequest::find_all_by_request_id($request_id);
                $valor = 0;

                foreach ($total as $t) {
                    $valor = $valor + $t->value_unit * $t->amount;
                }
                $valor = $valor + $e->value_freight;
                
       $boleto = new BancoDoBrasil(array(
            'dataVencimento' => new DateTime($data), 'valor' => $valor,
            'sequencial' => $pagamento->number_validation, // Para gerar o nosso número
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => 1724, // Até 4 dígitos
            'carteira' => 18,
            'conta' => 10403005, // Até 8 dígitos
            'convenio' => 1234, // 4, 6 ou 7 dígitos
        ));
        echo $boleto->getOutput();
      }
    
    public function conclusaoAction($request_id, $parcelas = null) {
        $this->view->setFile('resumocompra');
        $post = $this->request->post();
        $this->view->setVar('request_id', $request_id);
        if ($post['ano'] > date('y')) {
            if ($post['ano'] === date('y') && $post['mes'] > date('m')) {
                $this->view->setFile('cartao');
                if (isset($parcelas) && !empty($parcelas)) {
                    $this->view->setVar('parcela', $parcelas);
                    $this->load('Helpers\Alert', array(
                        'danger',
                        'Erro', 'Cartão usado está expirado.Tente novamente com outro cartão.'));
                }
            } else {
                $p = array('request_id' => $request_id,
                    'type_payment' => 'cartao',
                    'number_validation' => $post['numero'],
                    'identification' => $post['cartao'] . '-' . $parcelas);
                $cadastro = Payment::cadastrar($p);
                $frete = Freight::find_by_request_id($request_id);
                $endereco = Address::find($frete->address_id);
                $produtos = ItemRequest::find_all_by_request_id($request_id);
                $pedido = Request::find($request_id);
                $pedido->status = "Pago";
                $pedido->save();
                $pagamento = Payment::find_by_request_id($request_id);
                $this->view->setVar('pagamento', $pagamento);
                $this->view->setVar('frete', $frete);
                $this->view->setVar('endereco', $endereco);
                $this->view->setVar('produtos', $produtos);
            }
        } else {
            $this->view->setFile('cartao');
            if (isset($parcelas) && !empty($parcelas)) {
                $this->view->setVar('parcela', $parcelas);
                $this->load('Helpers\Alert', array(
                    'danger',
                    'Erro', 'Cartão usado está expirado.Tente novamente com outro cartão.'));
            }
        }
    }

    public function detalhesAction($request_id) {
        $this->view->setFile('detalhespedido');
        $frete = Freight::find_by_request_id($request_id);
        $endereco = Address::find($frete->address_id);
        $produtos = ItemRequest::find_all_by_request_id($request_id);
        $pagamento = Payment::find_by_request_id($request_id);
        $pedido = Request::find($request_id);

        $this->view->setVar('pagamento', $pagamento);
        $this->view->setVar('pedido', $pedido);
        $this->view->setVar('frete', $frete);
        $this->view->setVar('endereco', $endereco);
        $this->view->setVar('produtos', $produtos);
    }

    public function avaliarProdutoAction($product_id) {
        $this->view->setFile('avalia');
        $this->view->setVar('produto_id', $product_id);
        $post = $this->request->post();
        $teste = Review::find(array('conditions' => array('product_id = ? and user_id=?', $product_id, $this->auth->getUserId())));
        if (!empty($teste)) {
            $this->view->setVar('mensagem', $teste->message);
            $this->view->setVar('nota', $teste->note);
        }
        if (isset($post) && !empty($post)) {
            $teste = Review::find(array('conditions' => array('product_id = ? and user_id=?', $product_id, $this->auth->getUserId())));


            if (empty($teste)) {
                $p = array('product_id' => $product_id, 'user_id' => $this->auth->getUserId(), 'message' => $post['mensagem'], 'note' => $post['nota']);
                $cadastrar = Review::create($p);
                $this->load('Helpers\Alert', array(
                    'success',
                    'Sucesso', 'Avaliação efetuada com sucesso para voltar para pedidos clique no botão voltar.'));
            } else {
                $this->view->setVar('mensagem', $teste->message);
                $this->view->setVar('nota', $teste->note);
                $teste->message = $post['mensagem'];
                $teste->note = $post['nota'];
                $teste->save();

                $this->load('Helpers\Alert', array(
                    'success',
                    'Sucesso', 'Avaliação Alterada com sucesso para voltar para pedidos clique no botão voltar.'));
            }
        }
    }

}
