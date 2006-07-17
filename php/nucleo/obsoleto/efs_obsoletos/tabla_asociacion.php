<?
require_once('nucleo/lib/interface/form.php');
/*
* Este objeto no soporta bien a las tablas de mas de una CLAVE (PK)
* MAXIMO hasta ahora: tabla principal 2 , tabla de referencia 1
*/

class tabla_asociacion
{
    var $clave_entrada;         //Clave del registro de la tabla principal de la relacion
    var $tabla_asoc;            //Nombre de la tabla donde se guarda la asociacion
    var $tabla_asoc_fk_p;       //En la tabla de asociaciones, la forgein key a la tabla principal
    var $tabla_asoc_fk_r;       //En la tabla de asociaciones, la forgein key a la tabla de referencia
    var $tabla_ref;             //Nombre de la tabla que funciona como referencia
    var $tabla_ref_clave;       //Clave de la tabla de referencia.
    var $tabla_ref_desc;        //Descripcion de la tabla de referencia
    var $tabla_ref_where;       //WHERE que limita las opciones de la tabla de referencia. Las columnas
											//Todavia esta desprolijo... hay que pasarlas con el identificador 'r.'
    var $fuente;                //Fuente de datos con la que hay que trabajar
	var $form_prefijo;			//Prefijo del form (por si el ITEM posee mas de un form...)
	var $form_nombre;			//Nombre del formulario
	var $form_sumit;			//Nombre del boton de SUBMIT (si hay mas de un form, procesar solo los eventos de este)
	var $form_submit_nombre;	//Etiqueta del boton de submit
	var $titulo_referencia;		//Titulo de la columna donde se escribe la referencia
	var $debug;
    
    function tabla_asociacion($parametros, $debug=false)
    {
			$this->clave_entrada = $parametros["clave_entrada"];
			$this->tabla_asoc = $parametros["tabla_asoc"];
			$this->tabla_asoc_fk_p = $parametros["tabla_asoc_fk_p"];
			$this->tabla_asoc_fk_r= $parametros["tabla_asoc_fk_r"];
			$this->tabla_ref = $parametros["tabla_ref"];
			$this->tabla_ref_clave = $parametros["tabla_ref_clave"];
			$this->tabla_ref_desc = $parametros["tabla_ref_desc"];
			$this->tabla_ref_where = stripslashes($parametros["tabla_ref_where"]);
			$this->fuente = $parametros["fuente"];
			$this->form_prefijo = $parametros["form_prefijo"];
			$this->form_nombre = $this->form_prefijo . "formulario";
			$this->form_submit = $parametros["form_submit"];
			$this->form_submit_nombre = "GRABAR";
			$this->titulo_referencia = $parametros["titulo_referencia"];
			$this->debug = $debug;
		if( (count($this->clave_entrada)!=count($this->tabla_asoc_fk_p)) ||
				(count($this->tabla_ref_clave)!=count($this->tabla_asoc_fk_r)) ){
			//Los arrays que poseen las claves que tengo que machear tienen las 
			//que tener el mismo tamao
			echo ei_mensaje("TABLA-REFERENCIAS: El tamao de los parametros no coincide");
		}
    }
    
	function info()
	{
        $temp["clave_entrada"] = $this->clave_entrada;
		$temp["tabla_asoc"] = $this->tabla_asoc;
		$temp["tabla_asoc_fk_p"] = $this->tabla_asoc_fk_p;
		$temp["tabla_asoc_fk_r"] = $this->tabla_asoc_fk_r;
		$temp["tabla_ref"] = $this->tabla_ref;
		$temp["tabla_ref_clave"] = $this->tabla_ref_clave;
		$temp["tabla_ref_desc"] = $this->tabla_ref_desc;
		$temp["tabla_ref_where"] = $this->tabla_ref_where;
		$temp["fuente"] = $this->fuente;
		$temp["form_prefijo"] = $this->form_prefijo;
		$temp["form_nombre"] = $this->form_nombre;
		$temp["form_submit"] = $this->form_submit;
		$temp["form_submit_nombre"] = $this->form_submit_nombre;
		$temp["titulo_referencia"] = $this->titulo_referencia;
		ei_arbol($temp,"TABLA de ASOCIACIONES");
 	}
	
    function procesar($debug=false)
    {
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if( ($_SERVER["REQUEST_METHOD"]=="POST") 
				&& ($_POST[$this->form_submit]==$this->form_submit_nombre) )
		//SI hay un POST, y estuvo disparado por este formulario
		{
			//-[1]- Armo un array con las claves que tengo que INSERTAR
			foreach($_POST as $etiqueta => $valor)
			{
				if(((substr($etiqueta,0,strlen($this->form_prefijo)))==$this->form_prefijo)
					&& ($etiqueta != $this->form_submit)){
					$claves[] = trim(substr($etiqueta,strlen($this->form_prefijo)));
				}
			}
			//ei_arbol($claves,"CLAVES");
			//Armo un ARRAY columna-valor correspondiente a la parte del INSERT de la tabla principal
			for($a=0;$a<count($this->tabla_asoc_fk_p);$a++)	{
				$datos_p[$this->tabla_asoc_fk_p[$a]]=$this->clave_entrada[$a];
			}
			//ei_arbol($datos_p,"PRINCIPAL");
			//return;
			//ATENCION!!! --> la sintaxis de la transaccion no es multi-motor!
			//-[2]- Ejecuto la transaccion.
			$db[$this->fuente][apex_db_con]->Execute("BEGIN TRANSACTION");
			//1) Borro las ASOCIACIONES existentes
			for($a=0;$a<count($this->clave_entrada);$a++){
				$where[] = "(" . $this->tabla_asoc_fk_p[$a] ."= '". $this->clave_entrada[$a] . "')";
			}
			$sql = "DELETE FROM " . $this->tabla_asoc ." WHERE ".  implode(" AND ", $where) .";\n";
			if($this->debug) echo "<pre>" . $sql . "</pre>";
			if($db[$this->fuente][apex_db_con]->Execute($sql) === false)
			{
				echo ei_mensaje("Ha ocurrido un error ELIMINANDO ASOCIACIONES - " .$db[$this->fuente][apex_db_con]->ErrorMsg());
				$rs = $db["apl"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
			}
 			else{
				$ok = true;
				if(isset($claves)){
					if(is_array($claves)){
						foreach($claves as $clave){
							$datos_a = array();
							//Armo la clave de la tabla asociada (ARRAY clave-valor)
							$clave_a = explode(apex_sql_separador,$clave);
							//ei_arbol($clave_a,"DATOS");	
							//ei_arbol($this->tabla_asoc_fk_r,"CLAVES");	
							for($a=0;$a<count($this->tabla_asoc_fk_r);$a++)	{
								$datos_a[$this->tabla_asoc_fk_r[$a]]=$clave_a[$a];
							}
							//ei_arbol($datos_a,"DATOS ASOCIADOS");
							//Comprimo los arrays de datos a grabar para eliminar duplicados
							//(puede ser que una misma columna se utilice para dos FK)
							$datos = array_merge($datos_a, $datos_p);
							$datos = array_unique($datos);
							//ei_arbol($datos,"DATOS A INSERTAR");
							//2) Inserto las ASOCICACIONES vigentes
							$sql = "INSERT INTO " . $this->tabla_asoc 
								." (". implode(",",array_keys($datos)) .")".
								" VALUES ('". implode("','",array_values($datos)) ."');\n";
							if($this->debug) echo "<pre>" . $sql . "</pre>";
							if($db[$this->fuente][apex_db_con]->Execute($sql) === false)
							{
								echo ei_mensaje("Ha ocurrido un error ELIMINANDO ASOCIACIONES - " .$db[$this->fuente][apex_db_con]->ErrorMsg());
								$rs = $db[$this->fuente][apex_db_con]->Execute("ROLLBACK TRANSACTION");
								$ok = false;
								break;//Salgo del foreach
							}
						}
					}
				}
				//COMMIT
				if($ok){
					echo ei_mensaje("Las ASOCIACIONES han sido actualizadas correctamente");
					$rs = $db[$this->fuente][apex_db_con]->Execute("COMMIT TRANSACTION");
				}
			}
 		}
    }

    function generar_html($get=null)
    {
		global $ADODB_FETCH_MODE, $db, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$vinculo = toba::get_vinculador()->generar_solicitud(null,null,$get,true);
		//Obtengo el recorset con la informacion de la asocicacion
        $sql = $this->generar_sql_interface();
        $rs = $db[$this->fuente][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("OBJETO TABLA ASOCIACION: No se genero el recordset. -- " . $db[$this->fuente][apex_db_con]->ErrorMsg()." -- SQL: $sql -- ");
		}
		if($rs->EOF){
			throw new excepcion_toba("RECORDSET VACIO. ". $db[$this->fuente][apex_db_con]->ErrorMsg()." -- SQL: $sql -- ");
		}
		//Construyo la interface
		//-[1]- Funcion para marcar y desmarcar todos.
?>
<script languaje='javascript'>
function marcar(estado)
{
	var formulario, elemento;
	formulario = document.<? echo $this->form_nombre ?>;
	prefijo = '<? echo $this->form_prefijo ?>';
	for (x=0 ; x < formulario.elements.length ; x++)	
	{
		if(formulario.elements[x].type=="checkbox")
		{
			elemento = formulario.elements[x].name;
			if (elemento.search(prefijo) != -1)
			{
				formulario.elements[x].checked = estado;
			}
		}
	}
}
</script>
<?
		//-[2]- Formulario
		echo form::abrir($this->form_nombre, $vinculo);
		echo "<div  align='center'><table width='300' class='cat-item'>\n";
		echo "<tr>";
		echo "<td  class='cat-item-categ1' colspan='2' >&nbsp;".$this->titulo_referencia . "</td>";
		echo "</tr>\n";
		echo "<tr>";
		echo "<td  class='cat-arbol' colspan='2' height='30'>&nbsp;";
		echo form::button("boton","Marcar","onclick=\"marcar(true);return false;\"","abm-input");
		echo form::button("boton","Desmarcar","onclick=\"marcar(false);return false;\"","abm-input");
		echo "</td>";
		echo "</tr>\n";
		while(!$rs->EOF)
		{
			echo "<tr>";
			echo "<td class='cat-item-botones2' width='1%'>";
			echo form::checkbox($this->form_prefijo.$rs->fields["clave"],$rs->fields["seteado"],$rs->fields["clave"]);
			echo "</td>";
			echo "<td class='cat-item-dato1'>&nbsp;".$rs->fields["descripcion"]."</td>";
			echo "</tr>\n";
			$rs->movenext();
		}
		echo "<tr>";
		echo "<td  class='cat-arbol' colspan='2' height='30'>&nbsp;";
		echo form::submit($this->form_submit, $this->form_submit_nombre);
		echo "</td>";
		echo "</tr>\n";
		echo "</table></div>\n";
		echo "<br>\n";
		echo form::cerrar();
    }
   
    function generar_sql_interface()
    {
		$where_ref = "";
		if(trim($this->tabla_ref_where)!=""){
			$where_ref = " WHERE (". $this->tabla_ref_where . ") ";
		}
		//Todas las claves son ARRAYs NUMERICOS!
		for($a=0;$a<count($this->clave_entrada);$a++){
			$where1[] = "(a." . $this->tabla_asoc_fk_p[$a] ."= '". $this->clave_entrada[$a] . "')";
		}
		for($a=0;$a<count($this->tabla_ref_clave);$a++){
			$join[] = "(r.". $this->tabla_ref_clave[$a] . " = a.". $this->tabla_asoc_fk_r[$a] .")";
			$temp1[] = "r.". $this->tabla_ref_clave[$a];
			$temp2[] = "a.". $this->tabla_asoc_fk_r[$a];
		}
		$clave = implode(" || '".apex_sql_separador."' || ",$temp1);
		$valor = implode(" || '".apex_sql_separador."' || ",$temp2);
        $sql= "SELECT ". $clave . " as clave, " .
                     "\n r.". $this->tabla_ref_desc . " as descripcion, " .
                     "\n " . $valor . " as seteado " .
                "\n FROM " . $this->tabla_ref . " r".
				"\n LEFT OUTER JOIN " . $this->tabla_asoc ." a".
				"\n ON " . implode(" AND ",$join) .
				"\n AND (". implode(" AND ",$where1) . ") ".
				"\n $where_ref ;";
		if($this->debug) echo "<pre>" . $sql . "</pre><br>";
		return $sql;
    }
}
?>
