<?
include_once("nucleo/consola/emular_web.php");
include_once("nucleo/persistencia/db_registros_s_i.php");

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
		$buffer =& new db_registros_s_i("x",$tabla,$fuente);
		$definicion_buffer = $buffer->get_definicion();
		
		//2)Creo el PHP de buffer 
		$php = "<?
//Generacion: ". date("j-m-Y H:i:s") ."
//Fuente de datos: '$fuente'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_$tabla extends db_registros_s
//db_registros especifico de la tabla '$tabla'
{
	function __construct(\$fuente=null, \$min_registros=0, \$max_registros=0 )
	{
";
		$php .= dump_array_php($definicion_buffer,"		\$def");
		//$php .= var_export($definicion_buffer,true);
		$php .= "		parent::__construct( \$def, \$fuente, \$min_registros, \$max_registros);
	}	
	
	function cargar_datos_clave(\$id)
	{
";
	$claves = $buffer->get_clave();
	
	if(count($claves)==0){
		echo("La tabla no tiene clave... este 'db_registros' tiene un futuro incierto\n");
	}elseif(count($claves)==1){
		$id = $claves[0];
		$php .= "		\$where[] = \"$id = '\$id'\";\n";
		$php .= "		\$this->cargar_datos(\$where);\n";
	}else{
		foreach($claves as $clave){
			$php .= "		\$where[] = \"$clave = '{\$id['$clave']}'\";\n";
		}
		$php .= "		\$this->cargar_datos(\$where);\n";
	}
		$php .= "	}
}
?>";
		//echo $php;
		//2)Genero el archivo
		$nombre = "dbr_$tabla.php";
		echo "Generando db_registros '" . $nombre . "' para la tabla '$tabla'.\n";
		$pa = fopen($nombre,"w");
		fwrite($pa,$php);
		fclose($pa);
	}

?>