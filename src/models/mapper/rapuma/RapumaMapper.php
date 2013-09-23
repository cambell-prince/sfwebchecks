<?php
namespace models\mapper\rapuma;

use models\mapper\Rapuma\RapumaEncoder;

use libraries\palaso\CodeGuard;

class RapumaMapper
{
	
	/**
	 * @var string
	 */
	protected $_basePath;
	
	/**
	 * @param string $basePath
	 */
	public function __construct($basePath) {
		if (substr($basePath, -1) != DIRECTORY_SEPARATOR) {
			$basePath .= DIRECTORY_SEPARATOR;
		}
		$this->_basePath = $basePath;
		if (!file_exists($this->_basePath)) {
			throw new \Exception("Base path '$basePath' does not exist.");
		}
	}
	
	/**
	 * Private clone to prevent copies of the singleton.
	 */
	private function __clone() {
	}

	protected function filePath($id) {
		return $this->_basePath . $id;
	}
	
	/**
	 * @param string $id
	 */
	public function exists($id) {
		CodeGuard::checkTypeAndThrow($id, 'string');
		return file_exists(self::filePath($id));
	}
	
	/**
	 * @param function $modelGenerator
	 * @param string $id
	 */
	public function read($modelGenerator, $id) {
		CodeGuard::checkTypeAndThrow($id, 'string');
		$filePath = $this->filePath($id);
		if (!file_exists($filePath)) {
			throw new \Exception("Could not find id '$id'in '$this->_basePath'");
		}
		$string = file_get_contents($filePath);
		$strings = explode("\n", $string);
		try {
			$model = $modelGenerator($strings);
			RapumaDecoder::decode($model, $strings, $id);
		} catch (\Exception $ex) {
			throw new \Exception("Exception thrown while reading '$id'", $ex->getCode(), $ex);
		}
	}
	
	/**
	 * @param object $model
	 * @param string $id
	 * @return string
	 */
	public function write($model, $id) {
		CodeGuard::checkTypeAndThrow($id, 'string');
		$strings = RapumaEncoder::encode($model);
		$filePath = $this->filePath($id);
		$string = implode("\n", $strings);
		file_put_contents($filePath, $string);
		return $id;
	}

	/**
	 * Removes the file $id (relative to the base path). 
	 * @param string $id
	 * @return boolean
	 */
	public function remove($id) {
		CodeGuard::checkTypeAndThrow($id, 'string');
		$filePath = self::filePath($id);
		if (file_exists($filePath)) {
			unlink($filePath);
		}
		return true;
	}

}

?>