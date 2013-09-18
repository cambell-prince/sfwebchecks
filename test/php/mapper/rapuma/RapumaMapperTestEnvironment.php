<?php

require_once(TestPath . 'common/TemporaryDirectory.php');

class RapumaMapperTestEnvironment extends TemporaryDirectory
{
	
	public function __construct() {
		parent::__construct('rapuma_mapper_test');
	}
	
	
}

?>