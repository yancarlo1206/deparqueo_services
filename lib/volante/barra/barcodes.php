<?php
//-----------------------------------------------------------------------------------'
//--                        Copyright (C) 2008 - 2011 by                           --'
//--                                                                               --'
//--                    -- TEC-IT Datenverarbeitung GmbH --                 	   --'
//--   -- team for engineering and consulting in information technologies 		 ----'
//--                                                                        	   --'
//--                           All rights reserved.                           	   --'
//--                                                                        	   --'
//--              This is a part of the TEC-IT Standard Software.           	   --'
//--                                                                         	   --'
//--    This source code is only intended as a supplement to the References        --'
//--      and related electronic documentation provided with the product.   	   --'
//--                                                                        	   --'
//--     See these sources for detailed information regarding this product  	   --'
//-----------------------------------------------------------------------------------'

//-----------------------------------------------------------------------------------'
//-- TEC-IT Sample Application		                                               --'
//-- PHP Sample to produce a JPG Bitmap, sent as data stream to the client browser --'
//-- With URL parameters the essential bar code parameters can be set		       --'	
//--                                                                       		   --'
//-- How to choose the barcode type:                                        	   --'
//-- http://localhost/barcode.php?code=Code128                              	   --'
//--                                                                        	   --'
//-- How to enter data which will be encoded into the barcode:              	   --'
//-- http://localhost/barcode.php?text=TEC-IT                               	   --'
//--                                                                        	   --'
//-- How to adjust the DPI (resolution of barcode image):                   	   --'
//-- http://localhost/barcode.php?dpi=200                                   	   --'
//--                                                                        	   --'
//-- How to adjust the barcode width:                                       	   --'
//-- http://localhost/barcode.php?width=100                                 	   --'
//--                                                                        	   --'
//-- How to adjust teh barcode height:                                      	   --'
//-- http://localhost/barcode.php?height=200                                	   --'
//--                                                                        	   --'
//-- How to adjust the module width:                                        	   --'
//-- http://localhost/barcode.php?modulewidth=100                           	   --'
//--                                                                               --'
//-- How to optimize the resolution:                                        	   --'
//-- http://localhost/barcode.php?optresolution=true                        	   --'
//--                                                                        	   --'
//-- How to display the human readable text:                                	   --'
//-- http://localhost/barcode.php?printdatatext=true                        	   --'
//--                                                                        	   --'
//-- How to indicate if escape sequences will be translated:                	   --'
//-- http://localhost/barcode.php?escapesequences=true                      	   --'
//-- Note: Disable magic quotes in php.ini (magic_quotes_gpc = Off)                --'
//--                                                                         	   --'
//-- How to choose the image format (0 = BMP, 4 = JPG, 6 = PNG, 7 = TIFF ):    	   --'
//-- http://localhost/barcode.php?imageformat=4                             	   --'
//--                                                                        	   --'
//-----------------------------------------------------------------------------------'
//--                                                                        	   --'
//-- For more information about parameters please go to..                   	   --'
//-- http://www.tec-it.com/download/PDF/Barcode_Reference_EN.pdf.           	   --'
//-----------------------------------------------------------------------------------'
//-- History:                                                               	   --'
//-- (2006-07-17) : rebuilt and tested                                      	   --'
//-----------------------------------------------------------------------------------'
//-- Free use of this code is granted                                       	   --'
//-----------------------------------------------------------------------------------'
	
// *******************************************************************************
// ***************************** SAMPLE CODE *************************************
// *******************************************************************************

global $text, $code, $imageformat, $dpi, $width, $height;
global $modulewidth, $optresolution, $printdatatext, $escapesequences;
// retrieve get parameters
foreach($_GET as $key => $value)
  $$key = $value;

// Create the barcode object
$obj = new COM("TBarCode10.TBarCode10");

$obj->LicenseMe ("Web: Universidad Simon Bolivar CO-59-102", 3, 1, "2F2CBAC9723ADC055A09F95F293A307D", 2002);

// The text property is transmitted via URL parameters
// To ensure that all characters can be transmitted correctly we use urlencode, ..decode
$text = urldecode($text);

if ($text == "")
	$text = "DEMO DATA 1234";

// assign default value if modulewidth is empty
if ($modulewidth != "")
    $obj->modulewidth = $modulewidth;

// assign default value if optresolution is empty
if ($optresolution != "")
    $obj->optresolution = $optresolution;

// assign default value if printdatatext is empty
if ($printdatatext != "")
    $obj->printdatatext = $printdatatext;

// assign default value if escapesequences is empty
if ($escapesequences != "")
    $obj->escapesequences = $escapesequences;
    
// set image output format to 4 (JPEG)
if ($imageformat == "") $imageformat = 4;

$FaktorX   = 2;
$FaktorY   = 2;
$fMaxiCode = 0;
$defaultY  = 100;

$code = urldecode($code);

// assign the bar code type to the object and set some properties
$code = strtoupper($code);

switch ($code) {

	case "CODE25IL" :
    $obj->Barcode = 3; // eBC_2OF5IL
	$FaktorX = 1;
    $FaktorY = 1;
	break;
	
	case "CODE39" :
	$obj->Barcode = 8; // eBC_3OF9
	$FaktorX = 1;
    $FaktorY = 1;
	break;
	
	case "CODE39FULLASCII" :
    $obj->Barcode = 9; // eBC30OF9A
	break;
	
	case "EAN8" :
    $obj->Barcode = 10; // eBC_EAN8
	break;
	
	case "EAN8CCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 10; // eBC_EAN8
	$FaktorX = 2;
    $FaktorY = 2;
	break;
	
	case "EAN13" :
    $obj->Barcode = 13; // eBC_EAN13
	break;
		
	case "EAN13CCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 13; // eBC_EAN13
	$FaktorX = 2;
    $FaktorY = 2;
	break;

	case "UCCEAN128" :
    $obj->Barcode = 16; // eBC_EAN128
	break;
	
	case "UCCEAN128CCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 16; // eBC_EAN128
	break;
	
	case "CODE128" :
	$obj->Barcode = 20; // eBC_Code128
	$FaktorX = 1;
    $FaktorY = 1;
	break;
	
	case "CODE93" :
    $obj->Barcode = 25; // eBC_9OF3
	break;
	
	case "RSS14" :
    $obj->Barcode = 29; // eBC_RSS14
	break;
		
	case "RSS14CCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 29;	// eBC_RSS14
	break;

	case "RSSLIMITED" :
    $obj->Barcode = 30; // eBC_RSSLtd
	break;
	
	case "RSSLIMITEDCCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 30; // eBC_RSSLtd
	break;

	case "RSSEXPANDED" :
    $obj->Barcode = 31; // eBC_RSSExp
	break;
	
	case "RSSEXPANDEDCCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 31; // eBC_RSSExp
	$FaktorX = 2;
    $FaktorY = 2;
	break;
	
	case "UPCA" :
    $obj->Barcode = 34; // eBC_UPCA
	break;
	
	case "UPCACCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 34; // eBC_UPCA
	$FaktorX = 2;
    $FaktorY = 2;
	break;
	
	case "UPCE" :
    $obj->Barcode = 37; // eBC_UPCE
	break;		
	
	case "UPCECCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 37; // eBC_UPCE
	break;
	
	case "POSTNET5" :
    $obj->Barcode = 40; // eBC_PostNet5
    $FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 12;
    break;
	
	case "POSTNET9" :
    $obj->Barcode = 42; // eBC_PostNet9
    $FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 12;
    break;
	
	case "POSTNET11" :
    $obj->Barcode = 44; // eBC_PostNet11
	$FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 12;
	break;
	
	case "MSI" :
    $obj->Barcode = 47; // eBC_MSI
    break;

	case "PDF417" :
    $obj->Barcode = 55; // eBC_PDF417
    $FaktorX = 2;
    $FaktorY = 3;
    break;
    
	case "MAXICODE" :
    $obj->Barcode = 57; // eBC_MAXICODE
    $obj->Modulewidth = 1250;
    $FaktorX = 5;
    $FaktorY = 4.1;
    break;
	
	case "QRCODE" :
    $obj->Barcode = 58; // eBC_QRCode
    $FaktorX = 4;
    $FaktorY = 4;
    break;
    
    case "AUSTRALIANPOST" :
    $obj->Barcode = 63; // eBC_AusPostCustom
	$FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 18;
	break;
	
	case "ROYALMAIL" :
    $obj->Barcode = 70; // eBC_RM4SCC
    $FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 18;
    break;
	
	case "DATAMATRIX" :
    $obj->Barcode = 71; // eBC_DataMatrix
    $FaktorX = 4;
    $FaktorY = 4;
    break;
    
	case "CODABLOCKF" :
    $obj->Barcode = 74; // eBC_CODABLOCKF
    $FaktorX = 2;
    $FaktorY = 20;
    break;
    
	case "JAPANESEPOSTAL" :
    $obj->Barcode = 76; // eBC_JapanesePostal
    $FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 14;
    break;
    	
	case "KOREANPOSTAL" :
    $obj->Barcode = 77; // eBC_KoreanPostal
	$FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 18;
	break;
	
	case "RSS14STACKED" :
    $obj->Barcode = 79; // eBC_RSS14Stacked
    break;
    	
	case "RSS14STACKEDCCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 79; // eBC_RSS14Stacked
	$FaktorX = 3;
    $Faktory = 3;
	break;
	
	case "RSS14STACKEDOMNI" :
    $obj->Barcode = 80; // eBC_RSS14StackedOmni
	break;
	
	case "RSS14STACKEDOMNICCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 80; // eBC_RSS14STackedOmni
	break;	
	
	case "RSSEXPANDEDSTACKED" :
    $obj->Barcode = 81; // eBC_RSSExpStacked
	$FaktorX = 2;
    $FaktorY = 2;
	break;
	
	case "RSSEXPANDEDSTACKEDCCA" :
    $obj->CompositeComponent = 1; // eCC_Auto
    $obj->Barcode = 81; // eBC_RSSExpStacked
	$FaktorX = 2;
    $FaktorY = 2;
	break;
    
    case "PLANETCODE12" :
    $obj->Barcode = 82; // eBC_Planet12
    $FaktorX = 2;
    $FaktorY = 2;
    $defaultY = 12;
    break;

	case "MICROPDF417" :
    $obj->Barcode = 84; // eBC_MicroPDF417
    $FaktorX = 2;
    $FaktorY = 2;
	break;
	
	case "AZTEC" :
    $obj->Barcode = 92; // eBC_Aztec
    $FaktorX = 4;
    $FaktorY = 4;
	break;
	
	case "MICROQRCODE" :
    $obj->Barcode = 97; // eBC_MicroQRCode
    $FaktorX = 4;
    $FaktorY = 4;
	break;

}
// calculate DPI enlarging factor
if ($dpi == "")  $dpi = 96;
$nScale = (Double)($FaktorX * $dpi / 96) / $FaktorX;
if ($nScale < 1) $nScale = 1;

$obj->Text    = $text; 	 // Set the text property (content) of the bar code

$fnt = new COM("StdFont");
$fnt->name = "Arial";
$fnt->size = (Double)(8 * $nScale);
$obj->Font = $fnt;

// get matrix sizes for 2D symbology
$COLS = $obj->Get2DXCols();
$ROWS = $obj->Get2DXRows();

// get number of modules for linear symbology
$CM = $obj->CountModules;
$CR = $obj->CountRows;


if ($COLS > 0 && $ROWS > 0) 
{
    // 2D symbology should use the values of XCols/XRows
    $xsize = (int) ($FaktorX * $COLS);
    $ysize = (int) ($FaktorY * $ROWS);
}    
elseif ($CM > 0 && $CR > 0) 
{
    // linear bar code
    $xsize = (int) ($FaktorX * $CM / $CR);
    $ysize = (int) ($FaktorY * $CR);
        
    if ($CR < 2) $ysize = $defaultY;
}
else
{
	// error: no barcode was created
	$xsize = 0;
	$ysize = 0; 
}

// set default value if width is empty
if ($width == "")
    $xsize = $xsize * $nScale;
else
    $xsize = $width;


// set default value if height is empty
if ($height == "")
    $ysize = $ysize * $nScale;
else
    $ysize = $height;


// add plain text height if bar code is valid
if ($obj->LastErrorNo == 0)
    $ysize = $ysize + $obj->GetTextAreaHeightImg(1);


// reduce oversized input parameters
if ($xsize > 25400) $xsize = 25400;
if ($ysize > 25400) $ysize = 25400;


header ("Content-Transfer-Encoding:binary");

// specify the MIME type of the data stream
if ($imageformat==0) 
{
	header("Content-type:image/BMP");
	$Format="bmp";
}
elseif ($imageformat==3) 
{
	header("Content-type:image/GIF");
	$Format="gif";
}
elseif ($imageformat==4) 
{
	header("Content-type:image/JPEG");
	$Format="jpg";
}
elseif ($imageformat==6) 
{
	header("Content-type:image/PNG");
	$Format="png";
}
elseif ($imageformat==7) 
{
	header("Content-type:image/TIFF");
	$Format="tif";
}

// save barcode to file temporarily
$unique_filename = "tmp~" . uniqid(rand()) . "." . $Format;
$path = realpath(".") . "\\" . $unique_filename;

// if error occurs, then generate another bar code with errortext
$Errortext = "";

if ($obj->LastErrorNo == 0)
{
    $obj->SaveImage ($path, $imageformat, $xsize, $ysize, 96 * $nScale, 96 * $nScale);  // create and save the bar code (96 dpi screen res.)

}
else
{
    $Errortext = $obj->LastError;
    $obj->BarCode = 20;
    $obj->CompositeComponent = 0;
    $obj->Text = $Errortext;
    $obj->SaveImage ($path, $imageformat, 405, 100, 300, 300);

}

// read the whole file and send it to the browser
$fp=fopen($path, "rb"); 
fpassthru($fp); 
flush();
fclose ($fp);

// hint: we didn't use readfile($path); because we had problems with binary files on apache

// deletes the file
//unlink ($path);

?>