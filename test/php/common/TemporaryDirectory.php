<?php

class TemporaryDirectory
{
	
	/**
	 * @var string
	 */
	private $_path;
	
	public function __construct($path) {
		$this->_path = $path;
		$this->ensureTestPathExists();
	}
	
	public function __destruct() {
// 		$this->clean();
	}
	
	public function clean() {
		$dir = $this->testPath();
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->getFilename() === '.' || $file->getFilename() === '..') {
				continue;
			}
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
	}
	
	public function cleanFolder() {
		$this->clean();
		rmdir($dir);
	}

	protected function ensureTestPathExists() {
		$testPath = $this->testPath();
		if (!file_exists($testPath)) {
			mkdir($testPath, 0755, true);
		}
	}
	
	protected function testPath() {
		if (substr($this->_path, 0, 1) == '/') {
			// We've got an absolute path so just return that.
			return $this->_path;
		}
		$tempDirectory = sys_get_temp_dir();
		var_dump($tempDirectory);
		return $tempDirectory . '/' . $this->_path . '/';
	}
	
	public function filePath($fileName) {
		return $this->testPath() . $fileName;
	}
	
}

?>