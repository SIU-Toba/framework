<?
include_once("nucleo/consola/emular_web.php");
include_once("nucleo/lib/buffer_db_s_i.php");

/*
	Atencion: los campos que son secuencias no tienen que aparecer 
			en las entradas no_duplicado y no_nulo.
*/

	$this->registrar_parametros();
	//Elijo FUENTE. Si la fuente es de un proyecto no toba,
	//debe expresarse: "proyecto,fuente"
	if(isset($this->parametros["-f"])){
		if(ereg(",",$this->parametros["-f"])!==false){
			$x = explode(",",$this->parametros["-f"]);
			$fuente = $x[1];		
			$fuente_proyecto = $x[0];
		}else{
			$fuente = $this->parametros["-f"];		
			$fuente_proyecto = null;
		}
	}else{
		$fuente = "instancia";
		$fuente_proyecto = null;
	}
	
	//Elijo prefijo TABLA
	if(isset($this->parametros["-t"])){
		$tabla = $this->parametros["-t"];
	}else{
		echo "Es necesario explicitar un prefijo '-t'\n";
		exit(0);
	}

	abrir_fuente_datos($fuente, $fuente_proyecto);

	$tablas = $db[$fuente][apex_db]->obtener_tablas_prefijo($tabla);
	if(count($tablas)==0){
		echo "No existen tablas con ese prefijo";
		exit(0);
	}
	
	foreach($tablas as $tabla_x)
	{
		//1)Busco la definicion
		$tabla = $tabla_x['relname'];
		$buffer =& new buffer_db_s_i("x",$tabla,$fuente);
		$definicion_buffer = $buffer->obtener_definicion();
		
		//2)Creo el PHP de buffer 
		$php = "<?
//Generacion: ". date("j-m-Y H:i:s") ."
//Fuente de datos: '$fuente'
require_once('nucleo/lib/buffer_db_s.php');

class buffer_$tabla extends buffer_db_s
//buffer especifico de la tabla '$tabla'
{
	function buffer_$tabla(\$id, \$fuente)
	{
";
		$php .= dump_array_php($definicion_buffer,"		\$definicion");
		$php .= "		parent::__construct(\$id, \$definicion, \$fuente);
	}	
}
?>
";
		//echo $php;
		//2)Genero el archivo
		$nombre = "buffer_$tabla.php";
		echo "Generando buffer '" . $nombre . "' para la tabla '$tabla'.\n";
		$pa = fopen($nombre,"w");
		fwrite($pa,$php);
		fclose($pa);
	}

?>