<?php

class dao_paises {

 public function get_paises($datos = null) {
	
	$conn = toba::get_db("referencia");
	
	if (isset($datos['nombre']))
		$sql = "SELECT * FROM paises WHERE nombre ILIKE '%{$datos['nombre']}%'";
	else
		$sql = "SELECT * FROM paises";
		
	$rs = $conn->Execute($sql);
	if (!$rs || $rs->EOF)
		return false;
	else
		return $rs->GetArray();
 }

}

?>