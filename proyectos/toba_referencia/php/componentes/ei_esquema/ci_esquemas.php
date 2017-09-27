<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_esquemas extends toba_ci
{
	protected $fuente = '
digraph G {

        subgraph cluster_0 {
                style=filled;
                color=lightgrey;
                node [style=filled,color=white];
                a0 -> a1 -> a2 -> a3;
                label = "process #1";
        }

        subgraph cluster_1 {
                node [style=filled];
                b0 -> b1 -> b2 -> b3;
                label = "process #2";
                color=blue
        }
        start -> a0;
        start -> b0;
        a1 -> b3;
        b2 -> a3;
        a3 -> a0;
        a3 -> end;
        b3 -> end;

        start [shape=Mdiamond];
        end [shape=Msquare];
}
		';
	function ini()
	{
		$this->set_propiedades_sesion(array('fuente'));
	}
		
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__fuente__modificacion($datos)
	{
		$this->fuente = $datos['fuente'];
	}
	
	function conf__fuente()
	{
		return array('fuente' => $this->fuente);
	}
	
	function conf__dirigido()
	{
		return $this->fuente;
	}
	
	
	function conf__svg()
	{
		return "digraph G {
					A [URL=\"javascript: alert('Este es A')\"];
            		B [URL=\"javascript: alert('Este es B')\"];
            		C [URL=\"javascript: alert('Este es C')\"];
    				A -> B ;
            		B ->C -> A;
				}
		";
	}

}

?>