<?php
	include_once("normal_arriba.inc.php");
	$this->contexto['get'] = apex_hilo_qs_edpd;
	if(isset($_GET[$this->contexto['get']])){//Por ahora la convencion es que la lista entregra el ID por el GET
		$this->contexto['elemento']=$_GET[$this->contexto['get']];
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	*
					FROM	apex_usuario_perfil_datos
					WHERE	usuario_perfil_datos='".$this->contexto['elemento']."';";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new toba_error("BARRA-OBJETO: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
		}elseif($rs->EOF){
			echo "<table width='100%' class='tabla-0'><tr>";
			echo "	<td width='90%' class='barra-obj-tit'>&nbsp;NUEVO</td>";
			echo "</tr></table>";
			$this->contexto['cargado_ok'] = false;
		}else{
			$this->contexto['info_elemento'] = current($rs->getArray()); //Obtengo la posicion 0
			echo "<table width='100%' class='tabla-0'><tr>";

//--------- INICIO

 			echo "<td  class='barra-obj-id' width='50'>&nbsp;</td>";
			echo "	<td width='1' class='barra-obj-id'>";
			$this->vinculador->obtener_link_item("/info/visor");
			echo "</td>";

			echo "	<td width='20%' class='barra-obj-id'>&nbsp;&nbsp;".$this->contexto['elemento']."&nbsp;</td>";

			echo "	<td width='90%' class='barra-obj-tit'>&nbsp;".$rs->fields["descripcion"]."</td>";

//--------- LINKS objeto extendido ---------------

 			echo "<td  class='barra-item-link' width='1'>";
 	 		echo "<a href='" . $this->vinculador->generar_url("/admin/usuarios/perfil",array($this->contexto['get']=>$this->contexto['elemento'])) ."'>".
	  			toba_recurso::imagen_toba("usuarios/perfil.gif",true). "</a>";
			echo "</td>";
			
//----------- FIN
 			echo "<td  class='barra-obj-tit' width='50'>&nbsp;</td>";
			echo "</tr></table>";
		$this->contexto['cargado_ok'] = true;
		}
	}else{
		echo "<table width='100%' class='tabla-0'><tr>";
		echo "	<td width='90%' class='barra-obj-tit'>&nbsp;NUEVO</td>";
		echo "</tr></table>";
		$this->contexto['cargado_ok'] = false;
	}
	//$this->info();
?>