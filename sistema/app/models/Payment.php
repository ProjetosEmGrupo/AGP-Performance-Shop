<?php 

class Payment extends \HXPHP\System\Model
{
	

	public static function cadastrar($p){
		$callbackOBJ = new \stdclass;
		$callbackOBJ->payment = null;
		$callbackOBJ->status = false;
		$callbackOBJ->errors = array();



		$pdo = new PDO("mysql:host=localhost;dbname=agp","root","");
		$insert = $pdo->prepare("INSERT INTO `payments`(`request_id`, `data_payment`, `type_payment`, `number_validation`, `identification`) VALUES (:request_id, NOW(), :type_payment, :number_validation, :identification)");

		$insert->bindValue(':request_id', $p['request_id']);	
		$insert->bindValue(':type_payment', $p['type_payment']);
		$insert->bindValue(':number_validation', $p['number_validation']);
		$insert->bindValue(':identification', $p['identification']);
		$insert->execute();
		$teste = $insert;
		

		
	}
}