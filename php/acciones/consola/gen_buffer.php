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
	function __construct(\$fuente=null, \$max_registros=0, \$min_registros=0)
	{
";
		$php .= dump_array_php($definicion_buffer,"		\$def");
		//$php .= var_export($definicion_buffer,true);
		$php .= "		parent::__construct( \$def, \$fuente, \$max_registros, \$min_registros);
	}	
	
	function cargar_datos_clave(\$id)
	{
		/*
			LEER y BORRAR
			-------------
			El proposito de este metodo es utilizar el parametro \$id para generar un array de sentencias WHERE de SQL
			que cargan a este db_registros. De esta manerase puede ocultar la utilizacion de SQL por fuera del mismo.
			Lo que sigue es un ejemplo:
		*/
		\$where[] = \"xxxxxxx = '\$id'\";
		\$this->cargar_datos(\$where);
	}
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