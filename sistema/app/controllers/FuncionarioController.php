<?php

class FuncionarioController extends \HXPHP\System\Controller {

    public function __construct($configs) {
        parent::__construct($configs);
        $this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);

        $user_id = $this->auth->getUserId();

        $this->view->setVar('departament', Departament::all());
        $this->view->setVar('produtos', Product::all());
        $this->view->setHeader('headerFuncionario');
    }

    public function produtoAction($id_departament = null) {
        $this->view->setFile('index');
        $status = true;
        if ($id_departament == null) {
            $this->load('Helpers\Alert', $mensagem = array(
                'info',
                '<center>Por Favor selecione uma categoria para filtrar a lista de produtos cadastrados.</center>'));
            $status = false;
        }

        $this->view->setVar('id_departament', $id_departament);
        if ($status == false) {
            $produto = Product::all();
            $this->view->setVar('class', 'disabled');
        } else {
            $produto = Product::find_all_by_departament_id($id_departament);
        }
        $this->view->setVar('produtos', $produto);
    }

    public function zerarProdutoAction($id_produto) {
        $this->view->setFile('index');
        $produto = Product::find($id_produto);
        if (!is_null($produto)) {
            $produto->stock = 0;
            $produto->save();
            $mensagem = array(
                'success',
                'Sucesso',
                'O produto foi zerado do estoque com sucesso para exibir a lista atualizada clique no logo ou no departamento desejado');
        } else {
            $mensagem = array(
                'danger',
                'Erro',
                'Não foi achado produto com esse código. Por favor volte para a home do funcionário!');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function cadastrarAction($id_departament) {
        $this->view->setFile('adicionarProduto');
        $this->view->setVar('id_departament', $id_departament);
        $obs = Departament::find($id_departament);
        $this->view->setVar('obs', $obs->observation_departament);
        $this->view->setVar('supplier', Supplier::all());
    }

    public function cadastrarProdutoAction($id_departament) {
        $this->view->setFile('adicionarProduto');
        $obs = Departament::find($id_departament);
        $this->view->setVar('obs', $obs->observation_departament);
        $this->view->setVar('supplier', Supplier::all());
        if (isset($_FILES['img1']) && !empty($_FILES['img1']) && isset($_FILES['img2']) && !empty($_FILES['img2']) && isset($_FILES['img3']) && !empty($_FILES['img3'])) {
            $post = $this->request->post();
            $id_foreign = array('departament_id' => $id_departament);
            $post = array_merge($post, $id_foreign);
            $dir_path = ROOT_PATH . 'public' . DS . 'img' . DS . 'products';
            $imgs = array('img1' => md5(uniqid()), 'img2' => md5(uniqid()), 'img3' => md5(uniqid()));
            $ext = 'png';
            $status = false;
            $uploadimg1 = new upload($_FILES['img1']);
            if ($uploadimg1->uploaded) {
                $uploadimg1->file_new_name_body = $imgs['img1'];
                $uploadimg1->file_new_name_ext = $ext;
                $uploadimg1->resize = true;
                $uploadimg1->image_x = 800;
                $uploadimg1->image_y = 700;
                $uploadimg1->process($dir_path);
                if ($uploadimg1->processed) {
                    $uploadimg2 = new upload($_FILES['img2']);
                    if ($uploadimg2->uploaded) {
                        $uploadimg2->file_new_name_body = $imgs['img2'];
                        $uploadimg2->file_new_name_ext = $ext;
                        $uploadimg2->resize = true;
                        $uploadimg2->image_x = 800;
                        $uploadimg2->image_y = 700;
                        $uploadimg2->process($dir_path);
                        if ($uploadimg2->processed) {
                            $uploadimg3 = new upload($_FILES['img3']);
                            if ($uploadimg3->uploaded) {
                                $uploadimg3->file_new_name_body = $imgs['img3'];
                                $uploadimg3->file_new_name_ext = $ext;
                                $uploadimg3->resize = true;
                                $uploadimg3->image_x = 800;
                                $uploadimg3->image_y = 700;
                                $uploadimg3->process($dir_path);
                                if ($uploadimg3->processed) {
                                    $status = true;
                                    $imgs['img1'] = $imgs['img1'] . '.' . $ext;
                                    $imgs['img2'] = $imgs['img2'] . '.' . $ext;
                                    $imgs['img3'] = $imgs['img3'] . '.' . $ext;
                                }//fim if 3 processed
                            }//fim if 3 upload 
                        } //fim if segundo processed
                    } //fim if segundo upload
                }//fim if primeiro processed 
            }//fim if primeiro upload
            if ($status === true) {
                $post = array_merge($post, $imgs);
                $cadastrarProduto = Product::cadastrar($post);
            } else {
                $mensagem = array('danger', 'Erro', 'Um ou mais campos de imagem tem formatos inválido. Por favor insira um arquivo com formato de imagem');
            }
            if ($cadastrarProduto->status == true) {
                $this->view->setFile('index');
                $mensagem = array('success', 'Sucesso', 'O produto foi cadastrado com sucesso!');
            } else {
                $mensagem = $cadastrarProduto->errors;
            }
        }//fim if testando file vazio
        else {
            $mensagem = array(
                'danger',
                'Erro',
                'Um ou mais campos de imagem ficaram vazios. Por favor corrija-os');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function editarProdutoAction($produto_id) {
        $this->view->setFile('editaProduto');
        $this->view->setVar('produto_id', $produto_id);
        $this->view->setVar('supplier', Supplier::all());
        $produto = Product::find($produto_id);
        $obs = Departament::find($produto->departament_id);
        $this->view->setVar('obs', $obs->observation_departament);
        $this->view->setVar('produto', $produto);
        $dir_path = ROOT_PATH . 'public' . DS . 'img' . DS . 'products';
        $imgs = array('img1' => md5(uniqid()), 'img2' => md5(uniqid()), 'img3' => md5(uniqid()));
        $ext = 'png';
        $post = $this->request->post();
        if (isset($_FILES['img1']) && !empty($_FILES['img1'])) {
            $uploadimg1 = new upload($_FILES['img1']);
            if ($uploadimg1->uploaded) {
                $uploadimg1->file_new_name_body = $imgs['img1'];
                $uploadimg1->file_new_name_ext = $ext;
                $uploadimg1->resize = true;
                $uploadimg1->image_x = 800;
                $uploadimg1->image_y = 700;
                $uploadimg1->process($dir_path);
                if ($uploadimg1->processed) {
                    $img1 = array('img1' => $imgs['img1'] . '.' . $ext);
                    $post = array_merge($post, $img1);
                }
            }//if primeira imagem
        }//if file primeira imagem
        if (isset($_FILES['img2']) && !empty($_FILES['img2'])) {
            $uploadimg2 = new upload($_FILES['img2']);
            if ($uploadimg2->uploaded) {
                $uploadimg2->file_new_name_body = $imgs['img2'];
                $uploadimg2->file_new_name_ext = $ext;
                $uploadimg2->resize = true;
                $uploadimg2->image_x = 800;
                $uploadimg2->image_y = 700;
                $uploadimg2->process($dir_path);
                if ($uploadimg2->processed) {
                    $img2 = array('img2' => $imgs['img2'] . '.' . $ext);
                    $post = array_merge($post, $img2);
                }
            }
        }
        if (isset($_FILES['img3']) && !empty($_FILES['img3'])) {
            $uploadimg3 = new upload($_FILES['img3']);
            if ($uploadimg3->uploaded) {
                $uploadimg3->file_new_name_body = $imgs['img3'];
                $uploadimg3->file_new_name_ext = $ext;
                $uploadimg3->resize = true;
                $uploadimg3->image_x = 800;
                $uploadimg3->image_y = 700;
                $uploadimg3->process($dir_path);
                if ($uploadimg3->processed) {
                    $img3 = array('img3' => $imgs['img3'] . '.' . $ext);
                    $post = array_merge($post, $img3);
                }
            }
        }
        $alterar = Product::alterar($post, $produto_id);
        if ($alterar->status == true) {
            if (isset($alterar->imgs['img1']) && !empty($alterar->imgs['img1'])) {
                unlink($dir_path . DS . $alterar->imgs['img1']);
            }
            if (isset($alterar->imgs['img2']) && !empty($alterar->imgs['img2'])) {
                unlink($dir_path . DS . $alterar->imgs['img2']);
            }
            if (isset($alterar->imgs['img3']) && !empty($alterar->imgs['img3'])) {
                unlink($dir_path . DS . $alterar->imgs['img3']);
            }
            $mensagem = array('success', 'Sucesso', 'O produto foi Alterado com sucesso!');
            $this->view->setFile('index');
        } else {
            $mensagem = array('danger', 'Erro', 'Erro ao alterar o produto!');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function editaProdutoAction($produto_id) {
        $this->view->setFile('editaProduto');
        $this->view->setVar('produto_id', $produto_id);
        $this->view->setVar('supplier', Supplier::all());
        $produto = Product::find($produto_id);
        $obs = Departament::find($produto->departament_id);
        $this->view->setVar('obs', $obs->observation_departament);
        $this->view->setVar('produto', $produto);
    }

    public function pedidoAction() {
        $this->view->setFile('pedido');
        $busca = Request::all();
        $this->view->setVar('pedidos', $busca);
    }

    public function detalhesPedidoAction($request_id) {
        $this->view->setFile('detalhespedidos');
        $busca = Request::find($request_id);
        $this->view->setVar('pedido', $busca);
        $frete = Freight::find_by_request_id($busca->id);
        $this->view->setVar('frete', $frete);
        $endereco = Address::find($frete->address_id);
        $this->view->setVar('endereco', $endereco);
        $produtos = ItemRequest::find_all_by_request_id($request_id);
        $this->view->setVar('produtos', $produtos);
        $pagamento = Payment::find_by_request_id($busca->id);
        $this->view->setVar('pagamento', $pagamento);
    }

    public function relatorioVendasAction() {
        $this->view->setFile('funcionario');
    }

    public function exibirRelatorioAction() {
        $this->view->setfile('listarelatorio');
        $post = $this->request->post();

        $data_inicio = date("Y-m-d H:i:s", strtotime('+0 days', strtotime($post['dataInicio'])));
        $data_fim = date("Y-m-d H:i:s", strtotime('+0 days', strtotime($post['dataFim'])));

        $sql = "select * from requests where date_begin > '" . $data_inicio . "' and date_begin < '" . $data_fim . "' and status<>'Cancelado' and status<>'Aguardando o Pagamento'";
        $pedidos = Request::find_by_sql($sql);

        $this->view->setVar('pedidos', $pedidos);
    }

    public function devolucaoAction($request_id) {
        $this->view->setFile('detalhespedidos');
        $busca = Request::find($request_id);
        $this->view->setVar('pedido', $busca);
        $frete = Freight::find_by_request_id($busca->id);
        $this->view->setVar('frete', $frete);
        $endereco = Address::find($frete->address_id);
        $this->view->setVar('endereco', $endereco);
        $produtos = ItemRequest::find_all_by_request_id($request_id);
        $this->view->setVar('produtos', $produtos);
        $pagamento = Payment::find_by_request_id($busca->id);
        $this->view->setVar('pagamento', $pagamento);
        if ($busca->status !== 'Cancelado') {
            $pedido = Request::find($request_id);
            $estado = false;
            if ($pedido->status === 'Enviado') {
                $estado = true;
            }
            if ($pedido->status === 'Em Separacao') {
                $estado = true;
            }
            if ($estado === true) {
                $itens = ItemRequest::find_all_by_request_id($pedido->id);
                foreach ($itens as $it) {
                    $produto = Product::find($it->product_id);
                    $qtd = $produto->stock;
                    $qtd = $qtd + $it->amount;
                    $produto->stock = $qtd;
                    $produto->save();
                    $nota = FiscalNote::find_by_request_id($pedido->id);
                    $nota->date_cancel = date('Y-m-d');
                    $nota->reason_cancel = 'Nao quero mais';
                    $nota->save();
                    $mensagem = array('success', 'Sucesso', 'Pedido cancelado com sucesso.');
                }
                //nota fiscal já gerada
            } else {
                $cancelamento = Request::encerrar($pedido->id);

                if ($cancelamento->status = true) {
                    $itens = ItemRequest::find_all_by_request_id($pedido->id);
                    foreach ($itens as $it) {
                        $produto = Product::find($it->product_id);
                        $qtd = $produto->stock;
                        $qtd = $qtd + $it->amount;
                        $produto->stock = $qtd;
                        $produto->save();
                        $mensagem = array('success', 'Sucesso', 'Pedido cancelado com sucesso.');
                    }
                }
            }
        } else {
            $mensagem = array('danger', 'Erro', 'Não é possivel cancelar um pedido cancelado.');
        }
        $this->load('Helpers\Alert', $mensagem);
    }

    public function gerarNotaAction($request_id) {
        $this->view->setFile('detalhespedidos');

        $busca = Request::find($request_id);
        $this->view->setVar('pedido', $busca);
        $frete = Freight::find_by_request_id($busca->id);
        $this->view->setVar('frete', $frete);
        $endereco = Address::find($frete->address_id);
        $this->view->setVar('endereco', $endereco);
        $produtos = ItemRequest::find_all_by_request_id($request_id);
        $this->view->setVar('produtos', $produtos);
        $pagamento = Payment::find_by_request_id($busca->id);
        $this->view->setVar('pagamento', $pagamento);

        if (isset($request_id)) {
            $nota = FiscalNote::find_by_request_id($request_id);
            if (empty($nota)) {
                $p = ['request_id' => $request_id, 'user_id' => $this->auth->getUserId(), 'date_begin' => date('Y-m-d')];
                $nota = FiscalNote::create($p);
                $itens = ItemRequest::find_all_by_request_id($request_id);
                foreach ($itens as $it) {
                    $produto = ['note_id' => $nota->id,
                        'product_id' => $it->product_id,
                        'amount' => $it->amount,
                        'value_unit' => $it->value_unit];
                    $cadastro = ItemNote::create($produto);
                    $redefinir = Request::find($request_id);
                    $redefinir->status = 'Em separacao';
                    $redefinir->save();
                    $mensagem = array('success', 'Sucesso', 'Nota Fiscal gerada com sucesso.');
                }
            } else {
                $mensagem = array('danger', 'Erro', 'Não é possivel gerar uma nota fiscal já gerada.');
            }
            $this->load('Helpers\Alert', $mensagem);
        }
    }

        

        public function imprimirEtiquetaAction($request_id) {
            $this->view->setFile('detalhespedidos');

            $busca = Request::find($request_id);
            $this->view->setVar('pedido', $busca);
            $frete = Freight::find_by_request_id($busca->id);
            $this->view->setVar('frete', $frete);
            $endereco = Address::find($frete->address_id);
            $this->view->setVar('endereco', $endereco);
            $produtos = ItemRequest::find_all_by_request_id($request_id);
            $this->view->setVar('produtos', $produtos);
            $pagamento = Payment::find_by_request_id($busca->id);
            $this->view->setVar('pagamento', $pagamento);
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML('<h3>Destinatário: <br></h3><h4> '.$endereco->street.', '.$endereco->number .' <br> '. $endereco->cep .', '. $endereco->neighbourhood .'<br>'.$endereco->city .', '.$endereco->uf.'</h4>'
                    . '<h3>Remetente: <br></h3><h4>  R. da Grota, 483 <br> 02206-010, Tucuruvi<br>São Paulo, SP </h4><br>');
            $mpdf->Output();
        }

    }


