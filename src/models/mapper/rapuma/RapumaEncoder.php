<?php
namespace models\mapper\Rapuma;

use models\mapper\JsonEncoder;

class RapumaEncoder extends JsonEncoder {
	
	/**
	 * Sets key/values in the array from the public properties of $model
	 * @param object $model
	 * @return array
	 */
	public static function encode($model) {
		$encoder = new RapumaEncoder();
		$outputArray = $encoder->_encode($model);
// 		var_dump($outputArray);
		$outputStrings = $encoder->outputStrings($outputArray);
// 		var_dump($outputStrings);
		return $outputStrings;
	}
	
	public function outputStrings($outputArray) {
		$this->output = [];
		$depth = 0;
		//var_dump($outputArray);
		$it = new \ArrayIterator($outputArray);
		iterator_apply($it, array($this, 'outputItem'), array($it, $depth));
		return $this->output;
	}
	
	/**
	 * @param \Iterator $it
	 * @param int $depth
	 */
	public function outputItem(\Iterator $it, $depth) {
		$key = $it->key();
		$item = $it->current();
		//var_dump($key, $item, $depth);
		if (is_array($item)) {
			if (key_exists('id', $item)) {
				$key = $item['id'];
				unset($item['id']);
			} else {
				$key{0} = strtoupper($key{0});
			}
			$lead = '';
			$start = '[';
			$end = ']';
			for ($i = 0; $i < $depth; $i++) {
				$lead .= "\t";
				$start .= '[';
				$end .= ']';
			}
			$this->output[] = $lead . $start . $key . $end; 
			$it2 = new \ArrayIterator($item);
			iterator_apply($it2, array($this, 'outputItem'), array($it2, $depth + 1));
			return true;
		}
		$lead = '';
		for ($i = 0; $i < $depth; $i++) {
			$lead .= "\t";
		}
		$this->output[] = $lead . $key . ' = ' . $item;
		
		return true;
	}
	
}


?>