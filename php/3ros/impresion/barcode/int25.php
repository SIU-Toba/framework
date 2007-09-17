<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2001 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Marcelo Subtil Marcal <jason@unleashed.com.br>               |
// +----------------------------------------------------------------------+
//
// $Id: int25.php 361 2004-09-22 21:25:53Z seba $
//


/**
 * Class to create a Interleaved 2 of 5 barcode
 *
 * @author Marcelo Subtil Marcal <jason@conectiva.com.br>
 */

class Image_Barcode_int25
{
    /**
     * Barcode type
     * @var string
     */
    var $_type = 'int25';

    /**
     * Barcode height
     *
     * @var integer
     */
    var $_barcodeheight = 50;

    /**
     * Bar thin width
     *
     * @var integer
     */
    var $_barthinwidth = 1;

    /**
     * Bar thick width
     *
     * @var integer
     */
    var $_barthickwidth = 3;

    /**
     * Coding map
     * @var array
     */
    var $_coding_map = array(
           '0' => '00110',
           '1' => '10001',
           '2' => '01001',
           '3' => '11000',
           '4' => '00101',
           '5' => '10100',
           '6' => '01100',
           '7' => '00011',
           '8' => '10010',
           '9' => '01010'
        );

    function draw($text, $imgtype = 'png', $archivo)
    {

        $text = trim($text);

        if (!preg_match("/[0-9]/",$text)) return;

        // if odd $text lenght adds a '0' at string beginning
        $text = strlen($text) % 2 ? '0' . $text : $text;

        // Calculate the barcode width
        $barcodewidth = (strlen($text)) * (3 * $this->_barthinwidth + 2 * $this->_barthickwidth) +
            (strlen($text)) * 2.5 +
            (7 * $this->_barthinwidth + $this->_barthickwidth) + 3;

        // Create the image
        $img = ImageCreate($barcodewidth, $this->_barcodeheight);

        // Alocate the black and white colors
        $black = ImageColorAllocate($img, 0, 0, 0);
        $white = ImageColorAllocate($img, 255, 255, 255);

        // Fill image with white color
        imagefill($img, 0, 0, $white);

        // Initiate x position
        $xpos = 0;

        // Draws the leader
        for ($i=0; $i < 2; $i++) {
            $elementwidth = $this->_barthinwidth;
            imagefilledrectangle($img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black);
            $xpos += $elementwidth;
            $xpos += $this->_barthinwidth;
            $xpos ++;
        }

        // Draw $text contents
        for ($idx = 0; $idx < strlen($text); $idx += 2) {       // Draw 2 chars at a time
            $oddchar  = substr($text, $idx, 1);                 // get odd char
            $evenchar = substr($text, $idx + 1, 1);             // get even char

            // interleave
            for ($baridx = 0; $baridx < 5; $baridx++) {

                // Draws odd char corresponding bar (black)
                $elementwidth = (substr($this->_coding_map[$oddchar], $baridx, 1)) ?  $this->_barthickwidth : $this->_barthinwidth;
                imagefilledrectangle($img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black);
                $xpos += $elementwidth;

                // Left enought space to draw even char (white)
                $elementwidth = (substr($this->_coding_map[$evenchar], $baridx, 1)) ?  $this->_barthickwidth : $this->_barthinwidth;
                $xpos += $elementwidth; 
                $xpos ++;
            }
        }


        // Draws the trailer
        $elementwidth = $this->_barthickwidth;
        imagefilledrectangle($img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black);
        $xpos += $elementwidth;
        $xpos += $this->_barthinwidth;
        $xpos ++;
        $elementwidth = $this->_barthinwidth;
        imagefilledrectangle($img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black);

        // Send image to browser
        switch($imgtype) {

            case 'gif':
//                header("Content-type: image/gif");
                imagegif($img, $archivo);
                imagedestroy($img);
            break;

            case 'jpg':
//                header("Content-type: image/jpg");
                imagejpeg($img, $archivo);
                imagedestroy($img);
            break;

            default:
//                header("Content-type: image/png");
                imagepng($img, $archivo);
                imagedestroy($img);
            break;

        }
        return;

    } // function create

} // class

?>