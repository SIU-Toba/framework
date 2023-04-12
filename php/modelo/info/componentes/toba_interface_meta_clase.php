<?php
/**
 * Conoce como es la composicion interna de una clase del ambiente
 * Es utilizada para
 * @package Centrales
 */
interface toba_meta_clase
{
    public function get_molde_subclase();
    public function get_clase_nombre();
    public function get_clase_archivo();
    public function get_punto_montaje();
    public function get_subclase_nombre();
    public function get_subclase_archivo();
}
