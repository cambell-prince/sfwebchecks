<?php
require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

class AllMapperRapumaTests extends TestSuite {
	
    function __construct() {
        parent::__construct();
 		$this->addFile(TestPath . 'mapper/rapuma/RapumaDecoder_Test.php');
 		$this->addFile(TestPath . 'mapper/rapuma/RapumaEncoder_Test.php');
    	$this->addFile(TestPath . 'mapper/rapuma/RapumaMapper_Test.php');
    }

}

?>
