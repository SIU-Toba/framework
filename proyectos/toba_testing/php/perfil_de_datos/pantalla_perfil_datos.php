<?php 

class pantalla_perfil_datos extends toba_ei_pantalla
{
	protected $sql = array(
								'SELECT categoria_1, categoria_2, c.descripcion
									FROM 	escalafon e,
											categoria c
									WHERE c.escalafon_1 = e.escalafon_1
									AND c.escalafon_2 = e.escalafon_2;',
								'SELECT categoria_1, categoria_2 FROM categoria;',
								'SELECT escalafon_1, escalafon_2 FROM escalafon;',
								'SELECT c.cargo, c.descripcion
									FROM cargo c, categoria cc
									WHERE c.categoria_1 = cc.categoria_1
									AND c.categoria_2 = cc.categoria_2;',
								'SELECT * FROM persona p, cargo c, categoria cc
									WHERE p.persona = c.persona
									AND c.categoria_1 = cc.categoria_1 AND c.categoria_2 = cc.categoria_2;',
								'SELECT * FROM persona;',
								'SELECT * FROM persona_extra;'
							);
	protected $sql2 = array(	'SELECT * FROM ref_deportes;',
								'SELECT * FROM ref_deportes WHERE id < 5;',
								'SELECT * FROM ref_deportes d, ref_persona_deportes p WHERE p.deporte = d.id;',
								'SELECT * FROM ref_deportes d, ref_persona_deportes WHERE ref_persona_deportes.deporte = d.id;',
								'SELECT * FROM ref_persona_deportes, ref_deportes d WHERE ref_persona_deportes.deporte = d.id;',
								'SELECT * FROM ref_juegos ORDER BY id;',
								'SELECT * FROM ref_deportes WHERE id > 2;',
								'SELECT * FROM ref_persona_deportes WHERE persona = 1;',
								'SELECT * FROM ref_persona_juegos;',
								'SELECT * FROM ref_persona_juegos WHERE persona = 1;'
							);

	function tabla($tabla,$titulo=null)
	{
		$fila = (current($tabla));
		echo "<div style=' color:black'>\n";
		echo "<table style='BORDER-COLLAPSE: collapse;
							empty-cells: show; 
							background-color: white;
							border: 2px solid black;
							color:black;
							'>\n";
		if($titulo){
			echo "<tr>\n";
			echo "<td style='font-size: 12px;  ' colspan='".(count($fila)+1)."'>$titulo</td>\n";
			echo "</tr>\n";
		}
		//Cuerpo
		if($tabla) {
			echo "<tr>\n";
			echo "<td></td>\n";
			//Titulos de columnas
			$estilo_titulo = "border: 1px solid red; color:red; background-color: yellow; text-align: center;";
			foreach (array_keys($fila) as $titulo){
				echo "<td style='$estilo_titulo'>$titulo</td>\n";
			}
			echo "</tr>\n";
			//Filas
			foreach ($tabla as $id => $fila){
				echo "</tr>\n";
				echo "<td style='$estilo_titulo'>$id</td>\n";
				foreach ($fila as $valor){
					echo "<td style='border: 1px solid gray; padding: 2px;' >$valor</td>\n";
				}
				echo "</tr>\n";
	
			}
		} else {
			echo "<tr>\n";
			echo "<td style='border: 1px solid gray; padding: 2px;' colspan='".(count($fila)+1)."'>No hay DATOS!</td>\n";
			echo "</tr>\n";
		}
		echo "</table>";
		echo "</div>";		
	}
}
?>