<?php
/*=======================================================================
// File: 	JPGRAPH_ERROR.PHP
// Description:	Error plot extension for JpGraph
// Created: 	2001-01-08
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph_error.php,v 1.1.1.1 2004/06/15 19:27:26 cvs Exp $
//
// License:	This code is released under QPL
// Copyright (C) 2001,2002 Johan Persson
//========================================================================
*/

//===================================================
// CLASS ErrorPlot
// Description: Error plot with min/max value for
// each datapoint
//===================================================
class ErrorPlot extends Plot {
    var $errwidth=2;
//---------------
// CONSTRUCTOR
    function ErrorPlot(&$datay,$datax=false) {
	$this->Plot($datay,$datax);
	$this->numpoints /= 2;
    }
//---------------
// PUBLIC METHODS
	
    // Gets called before any axis are stroked
    function PreStrokeAdjust(&$graph) {
	if( $this->center ) {
	    $a=0.5; $b=0.5;
	    ++$this->numpoints;			
	} else {
	    $a=0; $b=0;
	}
	$graph->xaxis->scale->ticks->SetXLabelOffset($a);
	$graph->SetTextScaleOff($b);						
	$graph->xaxis->scale->ticks->SupressMinorTickMarks();
    }
	
    // Method description
    function Stroke(&$img,&$xscale,&$yscale) {
	$numpoints=count($this->coords[0])/2;
	$img->SetColor($this->color);
	$img->SetLineWeight($this->weight);	

	if( isset($this->coords[1]) ) {
	    if( count($this->coords[1])!=$numpoints )
		JpGraphError::Raise("Number of X and Y points are not equal. Number of X-points:".count($this->coords[1])." Number of Y-points:$numpoints");
	    else
		$exist_x = true;
	}
	else 
	    $exist_x = false;

	if( $exist_x )
	    $xs=$this->coords[1][0];
	else
	    $xs=0;

		
	for( $i=0; $i<$numpoints; ++$i) {
	    if( $exist_x ) $x=$this->coords[1][$i];
	    else $x=$i;
	    $xt = $xscale->Translate($x);
	    $yt1 = $yscale->Translate($this->coords[0][$i*2]);
	    $yt2 = $yscale->Translate($this->coords[0][$i*2+1]);
	    $img->Line($xt,$yt1,$xt,$yt2);
	    $img->Line($xt-$this->errwidth,$yt1,$xt+$this->errwidth,$yt1);
	    $img->Line($xt-$this->errwidth,$yt2,$xt+$this->errwidth,$yt2);
	}			
	return true;
    }
} // Class


//===================================================
// CLASS ErrorLinePlot
// Description: Combine a line and error plot
//===================================================
class ErrorLinePlot extends ErrorPlot {
    var $line=null;
//---------------
// CONSTRUCTOR
    function ErrorLinePlot(&$datay,$datax=false) {
	$this->ErrorPlot($datay);
	// Calculate line coordinates as the average of the error limits
	for($i=0; $i < count($datay); $i+=2 ) {
	    $ly[]=($datay[$i]+$datay[$i+1])/2;
	}		
	$this->line=new LinePlot($ly);
    }

//---------------
// PUBLIC METHODS
    function Legend(&$graph) {
	if( $this->legend != "" )
	    $graph->legend->Add($this->legend,$this->color);
	$this->line->Legend($graph);
    }
			
    function Stroke(&$img,&$xscale,&$yscale) {
	parent::Stroke($img,$xscale,$yscale);
	$this->line->Stroke($img,$xscale,$yscale);
    }
} // Class

/* EOF */
?>