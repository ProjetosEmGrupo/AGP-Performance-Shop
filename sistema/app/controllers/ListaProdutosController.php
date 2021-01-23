<?php 

class ListaProdutosController extends \HXPHP\System\Controller
{

	public function __construct($configs){
		parent::__construct($configs);
		$this->load('Services\Auth', $configs->auth->after_login, $configs->auth->after_logout, true);
		
		$user_id = $this->auth->getUserId();
		$this->view->setVar('departament', Departament::all());
		$this->view->setVar('produto', Product::find('all', array('order' => 'id desc')));
		$this->view->setFooter('footerListas');
		$this->view->setFile('index');

	}

	public function departamentoAction($id_departament, $supplier_id = null){
		$this->view->setFile('index');
		

		$filtro = 'name';
		$parametro = 'asc';
		if (isset($_GET['filtro']) && isset($_GET['parametro'])){
			$filtro= $_GET['filtro'];
			$parametro = $_GET['parametro'];
		}
		
		$depart = Departament::find($id_departament);
		$this->view->setVar('depart', $depart);
		$this->view->setVar('status', true);	
		$sql_marca = 'select supplier_id from products where departament_id = '. $id_departament .' group by supplier_id';
		$marca = Product::find_by_sql($sql_marca);
		$this->view->setVar('marca' , $marca);
		$sql_filtro = 'select filter from products where departament_id = '. $id_departament .' group by filter';
		$fil = Product::find_by_sql($sql_filtro);
		$this->view->setVar('filter' , $fil);
		$supplier_id++;
		$supplier_id--;

		
		if(isset($supplier_id) && !empty($supplier_id) && is_int($supplier_id)){
			echo 'entrei aqui';
			$sql_produto = 'select * from products where departament_id = '. $id_departament .' and supplier_id = '. $supplier_id.' order by '. $filtro . ' ' . $parametro;
			$produto = Product::find_by_sql($sql_produto);
		}		
		else{
			$sq = 'select * from products where departament_id = '. $id_departament .' order by '. $filtro . ' ' . $parametro;
			$produto = Product::find_by_sql($sq);
		}
		$this->view->setVar('produto', $produto);
	}

	public function departamentAction($id_departament, $filter = null){
		$this->view->setFile('index');


		$filtro = 'name';
		$parametro = 'asc';
		if (isset($_GET['filtro']) && isset($_GET['parametro'])){
			$filtro= $_GET['filtro'];
			$parametro = $_GET['parametro'];
		}


		$depart = Departament::find($id_departament);
		$this->view->setVar('depart', $depart);
		$this->view->setVar('status', true);	
		$sql_filtro = 'select filter from products where departament_id = '. $id_departament .' group by filter';
		$fil = Product::find_by_sql($sql_filtro);
		$this->view->setVar('filter' , $fil);
		$sql_marca = 'select supplier_id from products where departament_id = '. $id_departament .' group by supplier_id';
		$marca = Product::find_by_sql($sql_marca);
		$this->view->setVar('marca' , $marca);


		if(isset($filter) && !empty($filter)){
			$filter = urldecode($filter);
			$sql_produto = 'select * from products where departament_id = '. $id_departament .' and filter = "'. $filter. '"  order by '. $filtro . ' ' . $parametro;
			$produto = Product::find_by_sql($sql_produto);
			

		}		
		else{
			$sq = 'select * from products where departament_id = '. $id_departament .' order by '. $filtro . ' ' . $parametro;
			$produto = Product::find_by_sql($sq);
			
		}

		$this->view->setVar('produto', $produto);
	}


	public function pesquisaAction(){
		$this->view->setFile('index');


		$filtro = 'name';
		$parametro = 'asc';
		if (isset($_GET['filtro']) && isset($_GET['parametro'])){
			$filtro= $_GET['filtro'];
			$parametro = $_GET['parametro'];
		}

		$sql_filtro = "select * from products WHERE name LIKE '%". $_GET['barra'] ."%' order by ". $filtro . " ". $parametro;
		$produto = Product::find_by_sql($sql_filtro);
		$this->view->setVar('produto', $produto);
		$this->view->setVar('pesquisa', $_GET['barra']);
		if(empty($produto)){
			$this->view->setVar('resultado', 'teste' );
		}
	}

	public function listaAction(){

		$this->view->setFile('index');
                
       
		$filtro = 'name';
		$parametro = 'asc';
		if (isset($_GET['filtro']) && isset($_GET['parametro'])){
			$filtro= $_GET['filtro'];
			$parametro = $_GET['parametro'];
		}
		
		$produto = Product::find('all',array('order' => $filtro .' '. $parametro));
		$this->view->setVar('produto', $produto);
	}

}
