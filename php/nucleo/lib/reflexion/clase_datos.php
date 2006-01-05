<?
/*
	Hay que mezclar esto con la clase previa
	 (Esta es la interface consumida por los procesos generadores de datos en formato PHP)

	Importante!!	 
	¿Cual es la sintaxis adecuada para que las definiciones estaticas no se reasignen?
*/

class clase_datos
{
	private $nombre;
	private $generador;
	private $metodos=array();
	
	function __construct($nombre, $generador=null)
	{
		$this->nombre = $nombre;
		$this->generador = $generador;
	}
	
	function agregar_metodo_datos($nombre, $datos)
	{
		$php = "\tstatic function $nombre()\n\t{\n";
		//$php .= dump_array_php($datos, "\t\t\$datos");
		//$php .= "\t\treturn \$datos;\n";
		$php .= "\t\treturn " . var_export( $datos, true) . ";\n";
		$php .= "\t}\n";
		$this->metodos[] = $php;
	}
	
	function generar_php()
	{
		$php = "<?\n";
		//$php .= "//Generacion automatica: ".date("Y/m/d H:i:s")."\n";
		if (isset($this->generador)) {
			$php .= "//Generador: $this->generador\n\n";
		}
		$php .= "class $this->nombre\n{\n";
		foreach ( $this->metodos as $metodo) {
			$php .= $metodo;
			$php .= "\n";
		}
		$php .= "}\n?>";
		return $php;	
	}

	function guardar($archivo)
	{
		file_put_contents($archivo, $this->generar_php() );
	}
}
?>