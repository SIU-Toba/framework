<?

define("apex_cuadro_get_ordenar","cgo");

class cuadro
{
	var $titulo;
	var $descripcion;
	var $datos;
	var $col_titulos;		//Titulos de las columnas mostrables.
	var	$col_formato;
	var $col_ver;			//Columnas mostrables con su respectivo estilo
	var $ancho;
	var $ordenar;
    var $mensaje_error;
	var $n_filas;
	var $n_columnas;
	var $orden_columna;
	var $orden_sentido;
	
	function cuadro($parametros)
	{
		$this->titulo = 		$parametros["titulo"];
		$this->descripcion = 	$parametros["descripcion"];
		$this->datos = 			$parametros["datos"];
		$this->col_titulos = 	explode(",",$parametros["col_titulos"]);
		$this->col_formato = 	$parametros["col_formato"];
		$this->col_ver = 		$parametros["col_ver"];
		$this->ancho = 			$parametros["ancho"];
        $this->mensaje_error =  $parametros["mensaje_error"];
  		$this->ordenar = 		$parametros["ordenar"];
		//Obtengo cantidad de filas y columnas
        if(is_array($this->datos)){
	    	$this->n_filas = count($this->datos);
            if(isset($this->datos[0]) && is_array($this->datos[0]) ){
        		$this->n_columnas = count($this->datos[0]);
            }else{
                //echo ei_mensaje("CUADRO - ERROR: El parametro DATOS[x] debe ser un ARRAY");
            }
        }else{
            //echo ei_mensaje("CUADRO - ERROR: El parametro DATOS debe ser un ARRAY.");
        }
		//Ordeno los datos
		//$this->ordenar = false;//----------->  DESACTIVADO
 		//if($this->ordenar)$this->ordenar();
		//ei_arbol($parametros,"PARAMETROS");
	}
	
	function ordenar()
	//Ordenamiento de array de dos dimensiones
	{
		if(!$this->orden_columna=$_GET['columna'])$this->orden_columna=0;
		//El patron del ordenamiento
		foreach ($this->datos as $fila) { 
			$ordenamiento[] = $fila[$this->orden_columna]; 
		}
		//Ordeno segun el sentido
		if(!$this->orden_sentido=$_GET['sentido']) $this->orden_sentido = "asc";
		if($this->orden_sentido == "asc"){
			array_multisort($ordenamiento, SORT_ASC , $this->datos);
		} elseif ($this->orden_sentido == "des"){
			array_multisort($ordenamiento, SORT_DESC , $this->datos);
		}
	}

//--------------------------------------------------------------------------------
//------------------------------------ Salida HTML -------------------------------
//--------------------------------------------------------------------------------

	function cabecera_columnas($titulo,$col)
	{
		if($this->ordenar)
		{
			global $solicitud;
			$sentido[0][0]="asc";
			$sentido[0][1]="Orden ascendente";
			$sentido[1][0]="des";
			$sentido[1][1]="Orden descendente";

			echo  "<table width='100%' class='tabla-0'>\n";
			echo  "<tr>\n";
			echo  "<td width='95%' align='center' class='lista-col-titulo'>&nbsp;" . $titulo . "&nbsp;</td>\n";
			echo  "<td width='5%'>";
			foreach($sentido as $sen){
				$sel="";
				if (($col==$this->orden_columna)&&($sen[0]==$this->orden_sentido)) $sel = "_sel";//orden ACTIVO
				echo  "<a href='".$solicitud->vinculador->generar_url(null,array("columna"=>$col,"sentido"=>$sen[0]))."'>";
				echo  recurso::imagen_apl("sentido_". $sen[0] . $sel . ".gif",true,null,null,$sen[1]);
                echo "</a>";
			}
			echo  "</td>\n";		
			echo  "</tr>\n";
			echo  "</table>\n";
		}else{
			echo $titulo;
		}
	}

	//--------------------------------------------------------------------------------
	function generar_html()
	{
		if(count($this->datos)<1){
			echo ei_mensaje($this->mensaje_error);
			return;
		}
		echo "<table width='{$this->ancho}' align='center'  class='tabla-0'>";
		if($this->titulo<>"") echo "<tr><td class='lista-titulo'>". $this->titulo ."&nbsp;&nbsp;</td></tr>";
		if($this->descripcion<>"") echo "<tr><td class='lista-titulo'>". $this->descripcion ."&nbsp;&nbsp;</td></tr>";
		echo "<tr>
			    <td>
				<TABLE width='100%' class='tabla-0'>\n";
		//Genero la cabecera del as columnas
		echo "  <TR>\n";
        for($posicion=0;$posicion<count($this->col_titulos);$posicion++){
			echo "<td class='lista-col-titulo'>" ;
            $this->cabecera_columnas($this->col_titulos[$posicion],$posicion);
            echo "</td>\n";
		}
		echo "  </TR>\n";
		// Genero las FILAS de datos
		for ($f=0; $f<($this->n_filas);$f++)
		{
			echo "  <TR>\n";
			foreach($this->col_ver as $col => $estilo)
			{
				//Genero las COLUMAS
				if(isset($this->col_formato[$col])){
					$funcion = $this->col_formato[$col];
					$dato = $funcion($this->datos[$f],$col);
				}else{
					$dato = $this->datos[$f][$col];
				}
				echo  "    <TD class='lista-$estilo'>". $dato . "</TD>\n";
			}
			echo "  </TR>\n";		
		}		
		echo  "</table></td>
				</tr>
				</table>";
	}
} 

//###########################################################################################
//###########################################################################################

class cuadro_db extends cuadro
//Muestra un cuadro a partir de un recordeset
//El titulo de cada columna esta dado por es AS '...' del SELECT
//El caracter '_' se reemplaza por ' ' y se pone la primera letra de cada palabra en mayuscula
{
	var $sql;
	var $fuente;

	function cuadro_db($parametros,$fuente,$sql)
	{
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$this->fuente = $fuente;
		$this->sql = $sql;
		$rs = $db[$this->fuente][apex_db_con]->Execute($this->sql);
		if($rs->EOF){
			$parametros["datos"] = array();		
		}elseif(!$rs){
			$parametros["mensaje_error"] = "No se ha generado un RECORDSET.<br><br> ". $db[$this->fuente][apex_db_con]->ErrorMsg();
			$parametros["datos"] = array();		
		}else{
			//Cargo el ARRAY de datos
			$parametros["datos"] = $rs->getArray();
            //Si no se especifica que columnas ver, incluyo todas...
			if(!isset($parametros["col_ver"])){
				for ($i=0;$i<count($parametros["datos"][0]);$i++){
					$parametros["col_ver"][]="t";
				}
			}
			//SI no se especifica array de titulos, utilizo los nombres de las columnas del RS
			if(!isset($parametros["col_titulos"])){
				for ($i=0;$i<count($parametros["datos"][0]);$i++){
					$col = $rs->FetchField($i);
					$temp[] = $col->name;
				}
				$parametros["col_titulos"]=implode(",",$temp);
			}
		}
        //ei_arbol($parametros,"PARAMETROS");
		parent::cuadro($parametros);
	}
}
//###########################################################################################
//###########################################################################################


?>