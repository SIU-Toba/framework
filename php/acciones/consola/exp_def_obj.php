<?

//Hay que tomar en cuenta como se comportan las subclases que no poseen
//Un archivo propio de definicion... (Prohibir las definiciones en ACTIVIDADES
//Seria lo IDEAL!!!)

	//Tengo que emular el ambiente WEB
	include_once("nucleo/consola/emular_web.php");
	
	$proyecto = "toba";

	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	$sql = "SELECT 	o.proyecto as proyecto, 
							o.objeto as 				objeto, 
							o.nombre as 				nombre,
							o.subclase as 				subclase,
							o.subclase_archivo as 	subclase_archivo,
							c.archivo as 				archivo,
							c.clase as 					clase
				FROM 		apex_objeto o, 
							apex_clase c
				WHERE (o.proyecto = '$proyecto')
				AND	o.clase = c.clase
				AND	o.clase_proyecto = c.proyecto
			 	-- AND	o.objeto > 10 -- Esto tiene sentido en el proyecto toba
			 	-- AND	o.objeto > 100 AND o.objeto < 112 -- Esto tiene sentido en el proyecto toba
				ORDER BY o.objeto";

	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if($rs) {
	if(!$rs->EOF){
		echo "\n\nExportacion de OBJETOS\n\n";
		//------------------------- Exportacion OBJETO
		while(!$rs->EOF)
		{
			$objeto = null;
			echo "<" . $rs->fields["objeto"] . "> (".$rs->fields["clase"].") ". $rs->fields["nombre"] . "\n";
		
			if(isset($rs->fields["subclase"])){
				if(isset($rs->fields["subclase_archivo"])){
					include_once($rs->fields["subclase_archivo"]);					
					$inicializacion = "\$objeto = new ".$rs->fields["subclase"]."(array(\$proyecto,".$rs->fields["objeto"]."),\$this);";
				}else{
					//Tiene que haber un mecanismo que permita persistir las clases que
					//se vayan consumiendo.
					echo "****  NO es posible INSTANCIAR clases generadas en ACTIVIDADES\n";
				}
			}else{
				//CLASE COMUN
				include_once($rs->fields["archivo"]);
				$inicializacion = "\$objeto = new ".$rs->fields["clase"]."(array(\$proyecto,".$rs->fields["objeto"]."),\$this);";
				//echo $inicializacion . "\n";
			}
			eval($inicializacion);
			$objeto->exportar_definicion_php();
			$rs->movenext();	
		}
	}else{
		echo "No hay OBJETOS\n";
	}
}else{
	echo "ERROR: $sql\n";
}

?>