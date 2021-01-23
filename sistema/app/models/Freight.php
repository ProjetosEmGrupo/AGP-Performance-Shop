<?php 

class Freight extends \HXPHP\System\Model
{
	public static function cadastrar($p){
		$callbackOBJ = new \stdclass;
		$callbackOBJ->status = true;
		$callbackOBJ->errors = array();
		$cadastro = self::create($p);
		if($cadastro->is_valid()){
			$callbackOBJ->status = true;
		}
		return $callbackOBJ;
	}
}