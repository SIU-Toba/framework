<?php

interface RDILog
{
	function add_debug($etiqueta, $valor_a_dumpear, $tabla=false);
	function add_error($excepcion);
}
