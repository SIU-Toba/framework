<?
require_once("estructura_objeto.php");
/*
	FALTA
		
		- Acceso a la moficiacion de las cosas centrales del ITEM

*/

class estructura_item
{
	private $datos;
	private $dependencias;

	function __construct($proyecto, $item)
	{
		$sql = "	SELECT 	proyecto, 
							item, 
							solicitud_tipo, 
							pagina_tipo_proyecto, 
							pagina_tipo, 
							nombre, 
							descripcion, 
							actividad_buffer_proyecto, 
							actividad_buffer, 
							actividad_patron_proyecto, 
							actividad_patron, 
							actividad_accion
					FROM 	apex_item 
					WHERE 	( proyecto = '$proyecto' ) 
					AND 	(item = '$item' ) ;";
		$this->datos = current( consultar_fuente($sql) );
		$sql = "	SELECT 	proyecto, 
							objeto
					FROM	apex_item_objeto 
					WHERE 	( proyecto = '$proyecto' ) 
					AND 	(item = '$item' ) ;";
		$this->dependencias = consultar_fuente($sql);
	}
		
	function generar_html()
	{
		$target = apex_frame_centro;//apex_frame_lista		
		echo "<table class='tabla-0' width='100%'>";
		echo "<tr>";
		//------------- ACCESO A EDITORES ---------------
		echo "<td  class='barra-obj-id' width='1%'>";
		echo "<a  target='$target'  href='" . toba::get_vinculador()->generar_solicitud(
									"toba","/admin/items/propiedades",
									array(apex_hilo_qs_zona=>$this->datos["proyecto"]
										.apex_qs_separador. $this->datos["item"]) ) ."'>".
			recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM consumidor"). "</a>";
		echo "</td>\n";
		
		echo "<td  class='barra-obj-id' width='1%'>";
		echo "<a  target='$target'  href='" . toba::get_vinculador()->generar_solicitud($this->datos["proyecto"],$this->datos["item"]) ."'>".
			recurso::imagen_apl("items/instanciar.gif",true,null,null,"Instanciar el ITEM consumidor"). "</a>";
		echo "</td>\n";		
		//------------ PROPIEDADES ---------------------
		echo "<td class='barra-obj-io' width='100%'>";
		echo $this->datos['nombre'];
		echo "</td>";
		//------------ DEPENDENCIAS ---------------------
		echo "</tr>";
		if(isset($this->dependencias))
		{
			$a=1;
			echo "<tr><td  class='barra-obj-link' colspan='3'>";
			echo "<table  class='tabla-0' width='100%'>";
			foreach($this->dependencias as $dep)
			{
				echo "<tr><td  class='barra-obj-tit' width='20'> $a </td>";
				echo "<td>";
				$objeto = new estructura_objeto($dep['proyecto'],$dep['objeto']);
				$objeto->generar_html();
				echo "</td></tr>";
				$a++;
			}
			echo "</td></tr>";
			echo "</table>";
		}
		echo "</table>";
	}
}
?>