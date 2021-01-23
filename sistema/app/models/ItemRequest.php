<?php 

class ItemRequest extends \HXPHP\System\Model
{
	public static function cadastrar($p){
		$callbackOBJ = new \stdclass;
		$callbackOBJ->status = true;
		$callbackOBJ->errors = array();

		$carrinho = Cart::find_all_by_hash($p['cookie']);
		foreach ($carrinho as $c) {
			$find = Product::find($c->product_id);
			$d = array('product_id' => $c->product_id , 'amount' => $c->amount, 'value_unit' => $find->price, 'request_id' => $p['request_id']);
			
			$item = self::create($d);
			if($item->is_valid()){
				
				$qtd = $find->stock ;
				$qtd = $qtd - $c->amount;

				$find->stock = $qtd;
				$find->save();			
				
				$callbackOBJ->status = true;
			}
		}	
		Cart::delete_all(array('conditions'=>array('hash=?',$p['cookie'])));


		return $callbackOBJ;
	}
}