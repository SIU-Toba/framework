<?php	
require_once("objeto_ut.php");	
require_once("nucleo/lib/efs_obsoletos/ef.php");//	Elementos de interface

//	ATENCION!! esta clase no maneja claves	duplicadas!!

class	objeto_ut_multicheq extends objeto_ut
/*	
	@@acceso: nucleo
	@@desc: Descripcion
*/	
{
		  var	$estado;	
					 
	function	objeto_ut_multicheq($id)
/*	
	@@acceso: nucleo
	@@desc: Muestra la definicion	del OBJETO
*/	
	{
		parent::objeto_ut($id);	
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		$sql["info_ut_multicheq"]["sql"] =" SELECT		sql as	sql,
										  claves	as					  claves,
										  descripcion as			  descripcion,	
										  chequeado	as				  chequeado,
										  forzar_chequeo as		  forzar_chequeo
									FROM	apex_objeto_multicheq
									WHERE	objeto_multicheq_proyecto = '".$this->id[0]."'
									AND	  objeto_multicheq =	'".$this->id[1]."';" ;
		$sql["info_ut_multicheq"]["tipo"]="1";
		return $sql;
	}
//--------------------------------------------------------------------------------------------

	function	inicializar( $parametros=null	)
/*	
	@@acceso: objeto
	@@desc: Inicializar un valor despues del contructor pero	antes	de	utilizarla
	@@param:	array	| parametros necesarios	para la inicializacion
*/	
	{
		parent::inicializar(	$parametros	);	
	}

	function	cargar_datos($where=null,$from=null)
/*	
	@@acceso: objeto
	@@desc: Inicializar un valor despues del contructor pero	antes	de	utilizarla
	@@param:	array	| parametros necesarios	para la inicializacion
*/	
	{
		global $db,	$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE	= ADODB_FETCH_ASSOC;	

		$sql = $this->info_ut_multicheq["sql"];
		$rs =	$db[$this->info['fuente']][apex_db_con]->Execute($sql);
		if(!$rs){
			$this->registrar_info_proceso("la consulta no devolvio registros"	.	
																	  $db[$this->info['fuente']][apex_db_con]->ErrorMsg(). "	</b> -- SQL: $sql	--");	
		}elseif($rs->EOF){
			$this->registrar_info_proceso("la consulta no devolvio registros");
		}else{
								$datos =	$rs->getArray();
								//ATENCION:	Corregir	para claves	multiples!!!
								$parametros_ef["valor"]=1;	
								$parametros_ef["valor_info"]="SI";
								foreach($datos	as	$dato){
										  $id	= $dato[$this->info_ut_multicheq['claves']];	
										  $ef	=&	new ef_checkbox(	$this->id[1],			
																		  $this->nombre_formulario,
																		  $id,
																		  $dato[$this->info_ut_multicheq['descripcion']],
																		  null,
																		  null,
																		  null,
																		  $parametros_ef);
										 $this->estado[$id] = $ef ;
										 //Estado inicial	
										 //VER : Donde evalua el estado del POST???
										 if( $this->evaluar_chequeo_inicial($dato[$this->info_ut_multicheq['chequeado']]) )	
										 {	$this->estado[$id]->cargar_estado(1);}	
								}
					 }	
	}	
	//-------------------------------------------------------------------------------
		
		  function evaluar_chequeo_inicial(	$control	)
/*	
	@@acceso: objeto
	@@desc: Evalua	si	el	estado inicial	de	la	linea	es	ACTIVADO	o DESACTIVADO
		  @@param: mixed | valor a	evaluar
	@@retorno: boolean |	true si se debe aparecer, false el en caso contrario
*/	
		  {
				if( $control != 0	){	
				  return	true;	
				}else{
					 return false;	
				}
		  }
	//-------------------------------------------------------------------------------

	function	limpiar_interface()
/*	
	@@acceso: objeto
	@@desc: Limpia	la	INTERFACE del UT
*/	
	{
		if(is_array($this->estado)){
					 foreach( array_keys($this->estado)	as	$linea ){
							  $this->estado[$linea]->cargar_estado(null);
					 }	
		}
	}	
	//-------------------------------------------------------------------------------

	function	cargar_post()
/*	
	@@acceso: objeto
	@@desc: Carga los	datos	que maneja la interface	desde	el	HTTP POST
	@@retorno: boolean |	true si se cargo correctamente, false en el caso contrario
*/	
	{
		if(is_array($this->estado)){

					 foreach( array_keys($this->estado)	as	$linea ){
							  $this->estado[$linea]->cargar_estado();	
					 }	
		}
	}	
	//-------------------------------------------------------------------------------

	function	obtener_datos()
/*	
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario	
*/	
	{
		if(is_array($this->estado)){
					 foreach( array_keys($this->estado)	as	$linea ){
							 if( $this->estado[$linea]->obtener_estado()	==	1 ){
								$valor =	true;	
							  }else{	
								$valor =	false;
							  }
							 $temp[$this->estado[$linea]->obtener_id()] = $valor;	
					 }	
					 return $temp;	
		  }
	}
	//-------------------------------------------------------------------------------

	function	obtener_seleccionados()	
/*	
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario	
*/	
	{
		if(is_array($this->estado)){
			foreach( array_keys($this->estado)	as	$linea ){
				if( $this->estado[$linea]->obtener_estado()	==	1 ){
					$temp[] = $this->estado[$linea]->obtener_id();
				}	
			}	
			return $temp;	
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_html()	
/*	
	@@acceso: objeto
	@@desc: Hace un echo	de	la	interface de la UT
*/	
	{
			if(is_array($this->estado)){
				 echo	"<div	align='center'><table class='objeto-base'>";	
					 foreach( array_keys($this->estado)	as	$linea ){
							  echo "<tr><td>";
							  echo $this->estado[$linea]->obtener_html();
							  echo "</td></tr>\n";
					 }	
					 echo	"</table></div>";	
		}else{
			echo ei_mensaje("No hay datos cargados");
			//$this->registrar_info_proceso("No hay datos cargados");
		}
	}	
	//-------------------------------------------------------------------------------

	function	obtener_javascript()	
/*	
	@@acceso: objeto
	@@desc: Hace un echo	del javascript	que necesita la UT. La salida	se	agrega a	la	funcion de validacion
	@@desc: del	<form> del MT
*/	
	{
                //ATENCION: Javascript para seleccionar todas las opcciones
					 if($this->info_ut_multicheq['forzar_chequeo']){
								echo " if(\n";	
							if(is_array($this->estado)){
								foreach(	array_keys($this->estado) as $linea	){	
										 	echo	"(	formulario.". $this->estado[$linea]->obtener_id_form() .	
										 	".checked == false ) && \n";	
									}
									echo " true) {	 alert('Debe chequear al menos una linea');
										  return	false	}\n\n";										 
							}
				}
			}
	//-------------------------------------------------------------------------------
}
?>