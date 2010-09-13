<?php
class ci_ejemplo_4 extends toba_ci
{
		/**
		* Para el tipo de gráfico 'otro' hay que especificar todo lo referente a
		* jpgraph. Desde la inclusión de los archivos necesarios hasta la instanciación
		* de todas las componentes que esta necesita para generar un gráfico.
		* Lo único que hay que hacer es 'avisarle' al gráfico de toba cuál es el
		* canvas que se tiene que dibujar. Todo el resto es legal y bonito
		*
		* @param toba_ei_grafico $grafico
		*/
	function conf__grafico(toba_ei_grafico $grafico)
	{
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph.php');
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph_contour.php');

		$data = array(
					array (0.5,1.1,1.5,1,2.0,3,3,2,1,0.1),
					array (1.0,1.5,3.0,5,6.0,2,1,1.2,1,4),
					array (0.9,2.0,2.1,3,6.0,7,3,2,1,1.4),
					array (1.0,1.5,3.0,4,6.0,5,2,1.5,1,2),
					array (0.8,2.0,3.0,3,4.0,4,3,2.4,2,3),
					array (0.6,1.1,1.5,1,4.0,3.5,3,2,3,4),
					array (9.0,1.5,3.0,5,6.0,2,1,1.2,2.7,4),
					array (9.8,9.0,3.0,3,5.5,6,3,2,1,1.4),
					array (9.0,1.5,3.0,4,6.0,5,2,1,0.5,0.2)
			);


		// Setup a basic graph context with some generous margins to be able
		// to fit the legend
		$canvas = new Graph(650, 300);
		$canvas->SetMargin(40,140,60,40);

		$canvas->title->Set('Uso avanzado de la librería');
		$canvas->title->SetFont(FF_ARIAL,FS_BOLD,14);

		// For contour plots it is custom to use a box style ofr the axis
		$canvas->legend->SetPos(0.05,0.5,'right','center');
		$canvas->SetScale('intint');
		$canvas->SetAxisStyle(AXSTYLE_BOXOUT);
		$canvas->xgrid->Show();
		$canvas->ygrid->Show();


		// A simple contour plot with default arguments (e.g. 10 isobar lines)
		$cp = new ContourPlot($data);

		// Display the legend
		$cp->ShowLegend();

		// Make the isobar lines slightly thicker
		$cp->SetLineWeight(2);
		$canvas->Add($cp);

		// Con esta llamada informamos al gráfico cuál es el gráfico que se tiene
		// que dibujar
		$grafico->conf()->canvas__set($canvas);
    }
}
?>