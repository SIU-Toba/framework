<?php
require_once('nucleo/componentes/objeto.php');
require_once("nucleo/lib/formateo.php");

//error_reporting(E_ALL ^ E_NOTICE);

class objeto_lista extends objeto
{
	var $datos;
	var $clave_vinculo;
	
	function objeto_lista($id)
	{
		parent::objeto($id);
		//Genero las claves
		$this->clave_vinculo = explode(",",$this->info_lista["vinculo_clave"]);
		for($a=0;$a<count($this->clave_vinculo);$a++){
			$this->clave_vinculo[$a] = trim($this->clave_vinculo[$a]);
		}
	}

	function cargar_datos($where=null,$from=null)
	{
		global $db,$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$sql = sql_agregar_clausulas_where($this->info_lista["sql"],$where);
		$sql = sql_agregar_tablas_from($sql,$from);
		//echo $sql . "<br>";
    	$rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$rs){//SQL mal formado
			$this->observar("error","OBJETO LISTA [generar_html] - No se genero un recordset [SQL] $sql - [ERROR] " . 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),true,true,true);
		}
		if($rs->EOF){//NO existe el registro
			$this->observar("info","OBJETO ABMS [obtener_interface_cuadro] - No hay registros");
		}
		$this->datos = $rs->getArray();
		//ei_arbol($this->datos,"DATOS");
	}
//--------------------------------------------------------------------------------------------

	function obtener_html()
	{
		//Array que especifica que columnas son claves
		//Titulos
		$col_titulos = explode(",",$this->info_lista["col_titulos"]);
 		//ei_arbol($col_titulos);
		//Columnas visibles
		eval("\$col_ver = array(".$this->info_lista["col_ver"].");");
		eval("\$col_formato = array(".$this->info_lista["col_formato"].");");
 		//ei_arbol($col_ver);

		$total_columnas = 0;
		//-[2]- ARMO EL CUADRO -----------------------------------------------
		echo "<br>";
		echo "<div align='center'>\n";		
		echo "<table width='{$this->info_lista['ancho']}' class='objeto-base'>";
		//Titulo
		echo"<tr><td>";
		$this->barra_superior();
		echo "</td></tr>";
		//Subtitulo
		if($this->info_lista["subtitulo"]<>"") 
			echo"<tr><td class='lista-subtitulo'>". $this->info_lista["subtitulo"] ."</td></tr>";
		echo"<tr>
			    <td>
				<TABLE width='100%' class='tabla-0'>\n";
		//Genero la cabecera del as columnas
		echo"  <TR>\n";
			foreach($col_titulos as $col){
				echo"<td class='lista-col-titulo'>$col</td>\n";
				$total_columnas++;
			}
			//------- AUTO_VINCULO
			if(trim($this->info_lista["vinculo_indice"])!=""){
				echo"<td class='lista-col-titulo'>&nbsp;</td>\n";
				$total_columnas++;
			}
		echo"  </TR>\n";
				// Genero las FILAS de datos
		if(count($this->datos)>0)
		{
			for ($f=0; $f<count($this->datos);$f++)
			{
				echo"  <TR>\n";
				foreach($col_ver as $col => $estilo)
				{
					//Genero las COLUMAS
					if(isset($col_formato[$col])){
						$funcion = $col_formato[$col];
						//$dato = $funcion($this->datos[$f],$col); //Para el futuro: le paso la fila entera para que juegue!
						$dato = $funcion($this->datos[$f][$col]); //Por ahora solo doy formato al dato en SI!
					}else{
						$dato = $this->datos[$f][$col];
					}
					echo "    <TD class='lista-$estilo'>". $dato . "</TD>\n";
				}
				//-- VINCULACION --
				//Esto agrega un columna al FINAL, que tiene un vinculo a la propia pagina con el ID
				//del registro seleccionado
				if(trim($this->info_lista["vinculo_indice"])!=""){
					//-[1]- Busco la clave de la FILA
					$id_fila = "";
					for($a=0;$a<count($this->clave_vinculo);$a++){
						$id_fila .= $this->datos[$f][$this->clave_vinculo[$a]]. apex_qs_separador;
					}
					$id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));
					echo"<td class='lista-vinculo'>";
					//Le solicito un vinculo al VINCULADOR
					echo $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
																			$this->info_lista["vinculo_indice"],
																			$id_fila,
																			true );
					echo "</td>\n";

 				}
				echo"  </TR>\n";
			}		
		}
		else{
			echo"<tr><td class='lista-e' colspan='$total_columnas'>No hay registros!</td></tr>";
		}
			echo "</table></td>
					</tr>
					</table>";
			echo "</div>";
	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
}
?>
