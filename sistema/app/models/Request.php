<?php 

class Request extends \HXPHP\System\Model
{
	public static function cadastrar($p){
		$callbackOBJ = new \stdclass;
		$callbackOBJ->request = null;
		$callbackOBJ->status = true;
		$callbackOBJ->errors = array();

		$pdo = new PDO("mysql:host=localhost;dbname=agp","root","");
		$insert = $pdo->prepare("INSERT INTO `requests`(`user_id`, `date_begin`, `data_end`, `status`) VALUES (:user_id, NOW(), :data_end, :status)");
		$insert->bindValue(':user_id', $p['user_id']);
	
		$insert->bindValue(':data_end', null);
		$insert->bindValue(':status', $p['status']);
		$insert->execute();

		$find = $pdo->prepare("select max(id) from requests where user_id=:user_id");
		$find->bindValue(':user_id', $p['user_id']);
		$find->execute();
		$retorno = $find->fetch(PDO::FETCH_ASSOC);
		

		$callbackOBJ->request = $retorno;

		return $callbackOBJ;
	}

	public static function encerrar($id){
		$callbackOBJ = new \stdclass;
		
		$callbackOBJ->status = true;
		$callbackOBJ->errors = array();

		$pdo = new PDO("mysql:host=localhost;dbname=agp","root","");
		$encerra = $pdo->prepare("UPDATE `requests` SET `data_end`= NOW() ,`status`='Cancelado' WHERE id=:id");


		$encerra->bindValue(':id', $id);
		$encerra->execute();

		$callbackOBJ->status == true;

		return $callbackOBJ;


	}

}