<?php
namespace models\mapper\rapuma;

use libraries\palaso\CodeGuard;
use models\mapper\ArrayOf;
use models\mapper\Id;
use models\mapper\IdReference;

class RapumaDecoder {
	
	/**
	 * @param array $array
`	 * @return bool
	 */
	public static function is_assoc($array) {
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
	
	/**
	 * Sets the public properties of $model to values from $values[propertyName]
	 * @param object $model
	 * @param array $values A mixed array of JSON (like) data.
	 */
	public static function decode($model, $strings, $id = '') {
		$decoder = new RapumaDecoder();
		// TODO Parse array of strings into array of property -> value then call _decode.
		$values = self::parse($strings);
		$decoder->_decode($model, $values, $id);
	}
	
	public static function parse($strings) {
		$result = array();
		$current =& $result;
		$currentGroup =& $current;
		foreach ($strings as $string) {
			$string = trim($string);
			if ($string == '') {
				continue;
			}
			if ($string[0] == '#') {
				continue;
			}
			if ($string[0] == '[') {
				$matches = array();
				preg_match('/(\[+)([^\]]+)(\]+)/', $string, $matches);
				if (count($matches) != 4) {
					throw new \Exception("Broken group '$string'");
				}
				$bracketCount = strlen($matches[1]);
				$name = $matches[2];
				$name{0} = strtolower($name{0});
				if ($bracketCount == 1) {
					$current =& $result;
				} else if ($bracketCount == 2) {
					$current =& $currentGroup;
				}
				$current[$name] = array();
				$current =& $current[$name];
				if ($bracketCount == 1) {
					$currentGroup =& $current;
				}
			} else {
				// Its a property so add to the current container.
				$matches = array();
				preg_match('/(\w+)\s*=\s*(.+)/', $string, $matches);
				if (count($matches) != 3) {
					throw new \Exception("Broken property '$string'");
				}
				$name = $matches[1];
				$name{0} = strtolower($name{0});
				$current[$name] = trim($matches[2], '"');
			}
		}
		return $result;
	}
	
	/**
	 * Sets the public properties of $model to values from $values[propertyName]
	 * @param object $model
	 * @param array $values A mixed array of JSON (like) data.
	 * @param bool $isRootDocument true if this is the root document, false if a sub-document. Defaults to true
	 */
	protected function _decode($model, $values, $id) {
		CodeGuard::checkTypeAndThrow($values, 'array');
		$properties = get_object_vars($model);
		foreach ($properties as $key => $value) {
			if (is_a($value, 'models\mapper\IdReference')) {
				if (array_key_exists($key, $values)) {
					$this->decodeIdReference($key, $model, $values);
				}
			} else if (is_a($value, 'models\mapper\Id')) {
			     $this->decodeId($key, $model, $values, $id);
			} else if (is_a($value, 'models\mapper\ArrayOf')) {
				if (array_key_exists($key, $values)) {
					$this->decodeArrayOf($key, $model->$key, $values[$key]);
				}
			} else if (is_a($value, 'models\mapper\MapOf')) {
				if (array_key_exists($key, $values)) {
					$this->decodeMapOf($key, $model->$key, $values[$key]);
				}
			} else if (is_a($value, 'DateTime')) {
				if (array_key_exists($key, $values)) {
					$this->decodeDateTime($key, $model->$key, $values[$key]);
				}
			} else if (is_a($value, 'models\mapper\ReferenceList')) {
				if (array_key_exists($key, $values)) {
					$this->decodeReferenceList($model->$key, $values[$key]);
				}
			} else if (is_object($value)) {
				if (array_key_exists($key, $values)) {
					$this->_decode($model->$key, $values[$key], '');
				}
			} else {
				if (!array_key_exists($key, $values)) {
					// oops // TODO Add to list, throw at end CP 2013-06
					continue;
				}
				if (is_array($values[$key])) {
					throw new \Exception("Must not decode array in '" . get_class($model) . "->" . $key . "'");
				}
				$model->$key = $values[$key];
			}
		}
		$this->_id = null;
		$this->postDecode($model);
	}
	
	protected function postDecode($model) {
	}

	/**
	 * @param string $key
	 * @param object $model
	 * @param array $values
	 */
	public function decodeIdReference($key, $model, $values) {
		$model->$key = new IdReference($values[$key]);
	}
	
	/**
	 * @param string $key
	 * @param object $model
	 * @param array $values
	 * @param string $id
	 */
	public function decodeId($key, $model, $values, $id) {
		$model->$key = new Id($id);
	}
	
	/**
	 * @param ArrayOf $model
	 * @param array $data
	 * @throws \Exception
	 */
	public function decodeArrayOf($key, $model, $data) {
		CodeGuard::checkTypeAndThrow($data, 'array');
		$model->data = array();
		foreach ($data as $key => $item) {
			if ($model->getType() == ArrayOf::OBJECT) {
				$object = $model->generate($key);
				$this->_decode($object, $item, $key);
				$model->data[] = $object;
			} else if ($model->getType() == ArrayOf::VALUE) {
				if (is_array($item)) {
					throw new \Exception("Must not decode array for value type '$key'");
				}
				$model->data[] = $item;
			}
		}
	}
	
	/**
	 * @param MapOf $model
	 * @param array $data
	 * @throws \Exception
	 */
	public function decodeMapOf($key, $model, $data) {
		CodeGuard::checkTypeAndThrow($data, 'array');
		$model->data = array();
		foreach ($data as $itemKey => $item) {
			if ($model->hasGenerator()) {
				$object = $model->generate($item);
				$this->_decode($object, $item, false);
				$model->data[$itemKey] = $object;
			} else {
				if (is_array($item)) {
					throw new \Exception("Must not decode array for value type '$key'");
				}
				$model->data[$itemKey] = $item;
			}
		}
	}
	
	/**
	 * Decodes the mongo array into the ReferenceList $model
	 * @param ReferenceList $model
	 * @param array $data
	 * @throws \Exception
	 */
	public function decodeReferenceList($model, $data) {
		$model->refs = array();
		if (array_key_exists('refs', $data)) {
			// This likely came from an API client, who shouldn't be sending this.
			return;
		}
		$refsArray = $data;
		foreach ($refsArray as $objectId) {
			CodeGuard::checkTypeAndThrow($objectId, 'string');
			array_push($model->refs, new Id((string)$objectId));
		}
	}
	
	/**
	 * @param string $key
	 * @param object $model
	 * @param string $data
	 */
	public function decodeDateTime($key, $model, $data) {
		$model = new \DateTime($data);
	}
	
	
}

?>