<?php
require_once(toba_dir() . '/php/3ros/simpletest/unit_tester.php');
require_once(toba_dir() . '/php/3ros/simpletest/mock_objects.php');
//require_once('testing/mocks/hilo_version_test.php');

abstract class test_toba extends UnitTestCase
{
	function tearDown()
	{
		$this->restaurar_estado($this->sentencias_restauracion());	
	}

	protected function sentencias_restauracion()
	{
		return array();
	}
	
	protected function restaurar_estado($sentencias)
	{
		foreach ($sentencias as $sql) {
			try {
				$rs = toba::db('instancia')->ejecutar($sql);
			}catch(toba_error_db $e){
				$this->Fail("Error restaurando estado:\n$sql\n". $e->getMessage());
			}
		}	
	}
	
	function run(&$reporter)
    {
		$this->pre_run();
		parent::run($reporter);
		$this->post_run();
    }
       
    function pre_run()
    {
    	toba_constructor::set_refresco_forzado(true);
    }
    
    function post_run(){}
    
	///---------- MOCK del HILO
	function mentir_hilo()
	{
		global $solicitud;
		$this->hilo_orig = $solicitud->hilo;
		$solicitud->hilo = new hilo_version_test();	
	}	
	
	function restaurar_hilo()
	{
		global $solicitud;
		$solicitud->hilo = $this->hilo_orig;	
	}
	
	
    function assertEqualArray($first, $second, $message = "%s") {
        return $this->assertExpectation(
                new EqualArrayExpectation($first),
                $second,
                $message);
    }
	
	abstract function get_descripcion();
}

class EqualArrayExpectation extends SimpleExpectation
{
    var $_value;


    function EqualArrayExpectation($value, $message = '%s') {
        $this->SimpleExpectation($message);
        $this->_value = $value;
    }

    function es_igual($array1, $array2, $component)
    {
        if (array_key_exists($component,$array1) AND array_key_exists($component,$array2)){

            if (($array2[$component] === NULL) AND ($array1[$component] === NULL)){
                return true;}

            if (($array2[$component] === NULL) OR ($array1[$component] === NULL)){
                return false;}


            if ($array2[$component] != $array1[$component]){
                return false;}
            else{
                return true;}
        }
        else{
            return false;
        }
    }

    function array_diff_assoc_recursive($array1, $array2)
    {
        foreach($array1 as $key => $value) {
             if(is_array($value)){
                  if(!is_array($array2[$key])){
                       $difference[$key] = $value;
                  }
                  else {
                       $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                       if($new_diff != FALSE) {
                            $difference[$key] = $new_diff;
                       }
                   }
              } elseif(! $this->es_igual($array1,$array2,$key)) {
                   $difference[$key] = $value;
              }
        }
        foreach($array2 as $key => $value) {
             if(is_array($value)){
                  if(!is_array($array1[$key])){
                       $difference[$key] = $value;
                  }
                  else {
                       $new_diff = $this->array_diff_assoc_recursive($value, $array1[$key]);
                       if($new_diff != FALSE) {
                            $difference[$key] = $new_diff;
                       }
                   }
              } elseif(! $this->es_igual($array2,$array1,$key)) {
                   $difference[$key] = $value;
              }
        }
        return !isset($difference) ? array() : $difference;
    }

    function test($compare) {
        $diferencias = $this->array_diff_assoc_recursive($this->_value, $compare);
        return (count($diferencias) == 0);
    }

    function testMessage($compare) {
        if ($this->test($compare)) {
            return "Equal Array expectation [" . $this->_dumper->describeValue($this->_value) . "]";
        } else {
            $diferencia = $this->array_diff_assoc_recursive($this->_value, $compare);
            $salida =  "Equal Array expectation fails " .
                    $this->_dumper->describeDifference($this->_value, $compare);
            ob_start();
			echo ". ";
            var_dump($diferencia);
            $salida .= ob_get_contents();
            ob_end_clean();
            return $salida;
        }
    }
}


?>