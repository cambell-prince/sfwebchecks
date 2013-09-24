<?php
namespace models\mapper\Rapuma;

use models\mapper\JsonEncoder;

class RapumaEncoder extends JsonEncoder {
	
	/**
	 * Sets key/values in the array from the public properties of $model
	 * @param object $model
	 * @return array
	 */
	public static function encode($model, $inputStrings = array()) {
		$encoder = new RapumaEncoder();
		$outputArray = $encoder->_encode($model);
		$inputArray = array();
		if (!empty($inputStrings)) {
			$inputArray = RapumaDecoder::parse($inputStrings);
		}
// 		var_dump($outputArray);
		$outputStrings = $encoder->outputStrings($outputArray, $inputArray);
// 		var_dump($outputStrings);
		return $outputStrings;
	}
	
	public function outputStrings($outputArray, $inputArray) {
		$this->output = [];
		$depth = 0;
		//var_dump($outputArray);
		foreach ($inputArray as $inKey => $inValue) {
			if (array_key_exists($inKey, $outputArray)) {
				$this->outputItem($depth, $inKey, $outputArray[$inKey], $inValue);
				unset($outputArray[$inKey]);
			} else {
				$this->outputItem($depth, $inKey, $inValue, $inValue);
			}
		}
		foreach ($outputArray as $outKey => $outValue) {
			$this->outputItem($depth, $outKey, $outValue, $outValue);
		}
		return $this->output;
	}
	
	/**
	 * 
	 * @param int $depth
	 * @param string $key
	 * @param string | array $outValue
	 * @param string | array $inValue
	 */
	public function outputItem($depth, $key, $outValue, $inValue) {
// 		var_dump('---', $depth, $key, $outValue, $inValue);
		if (is_array($outValue) || is_array($inValue)) {
			if (!empty($outValue) && key_exists('id', $outValue)) {
				$key = $outValue['id'];
				unset($outValue['id']);
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
			foreach ($inValue as $inKey1 => $inValue1) {
				if (array_key_exists($inKey1, $outValue)) {
					$this->outputItem($depth + 1, $inKey1, $outValue[$inKey1], $inValue1);
					unset($outValue[$inKey1]);
				} else {
					$this->outputItem($depth + 1, $inKey1, $inValue1, $inValue1);
				}
			}
			foreach ($outValue as $outKey1 => $outValue1) {
				$this->outputItem($depth + 1, $outKey1, $outValue1, $outValue1);
			}
			return true;
		}
		if ($depth == 0 && $key == 'id') {
			return;
		}
		$lead = '';
		for ($i = 0; $i < $depth; $i++) {
			$lead .= "\t";
		}
		$this->output[] = $lead . $key . ' = ' . $outValue;
		
		return true;
	}
	
}


?>