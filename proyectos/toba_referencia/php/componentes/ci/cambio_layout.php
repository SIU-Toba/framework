<?php 
php_referencia::instancia()->agregar(__FILE__);

class pantalla_dos_columnas extends toba_ei_pantalla 
{
    protected function generar_layout()
    {
    	echo "
	    	<style type='text/css'>
	    		.ei-form-base {
	    			border:none;
	    		}
	    	</style>";
        echo "<table>";
        $i = 0;
        foreach($this->get_dependencias() as $dep) {
            $ultimo = ($i == $this->get_cantidad_dependencias() );
            if ($i % 2 == 0) {
                echo "<tr>";    
            }
            echo "<td>";
            $dep->generar_html();
            echo "</td>";
            $i++;            
            if ($i % 2 == 0 || $ultimo) {
                echo "</tr>";    
            }        
        }        
        echo "</table>";
    }	
}

class ci_cambio_layout extends toba_ci
{

	function conf__cuadro1($componente)
	{
		$datos = array();
		$inicio = 1;
		$fin = 31;
		for ($i = $inicio ; $i <= $fin; $i++) {
			$datos[] = array('fecha' => "$i-03-2006", 'importe' => 100-$i);
		}
		if (isset($this->orden)) {
			$ordenamiento = array();
	        foreach ($datos as $fila) { 
	            $ordenamiento[] = $fila[$this->orden['columna']]; 
	        }			
	        $sentido = ($this->orden['sentido'] == "asc") ? SORT_ASC : SORT_DESC;
			array_multisort($ordenamiento, $sentido, $datos); 
		}
		return $datos;
	}
	
	function conf__form2($componente)
	{
		return array(
			array('fecha' => '2006-10-23', 'importe' => 212.25),
			array('fecha' => '2006-10-30', 'importe' => 42),
		);	
	}
	
	function conf__esquema($esquema)
	{
		return "
			digraph G {	
				rankdir=LR;	
				a -> b;
				b -> c;
				c -> a;
			}
		";	
	}
}



?>