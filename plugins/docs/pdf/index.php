<?php



include('../classes/config.inc.php');

include('../classes/class.doc.inc.php');



$Doc = new Doc();

$Doc->Lang = $_GET[lang];

$Doc->DocPath = '../source/';

$Doc->Doc = $_GET[doc];

$Index = $Doc->loadIndex();



include ('../classes/class.ezpdf.php');



class redaxoDoku extends Cezpdf {



	var $LangPath;



	function redaxoDoku($p,$o=''){

		global $Doc;

		$this->$LangPath = $Doc->DocPath;

		$this->Cezpdf($p,$o);

	}



	function dest($info){

	  	$this->addDestination('doc'.$info[p],'FitH',$info['y']+$info['height']);

	}



}



$pdf =& new redaxoDoku('a4','portrait');



$pdf->ezStartPageNumbers(500,28,10,'','',1);



$pdf->ezSetMargins(50,70,50,50);



$all = $pdf->openObject();

$pdf->saveState();

$pdf->setStrokeColor(0,0,0,1);

$pdf->line(20,40,578,40);

$pdf->line(20,822,578,822);

$pdf->addText(50,34,6,'redaxo open source cms - docu - pdf - http://www.redaxo.de');

$pdf->restoreState();

$pdf->closeObject();

$pdf->addObject($all,'all');

$pdf->ezSetDy(-100);



$fontPath = eregi_replace("index.php$","fonts/",__FILE__);

$mainFont = $fontPath.'Helvetica.afm';

$pdf->selectFont($mainFont);



$pages = array();



$cat = 1;

foreach($Index as $K=>$I){



	$DocChapter = $Doc->loadChapter($K,'pdf');



    $pdf->saveState();



    $pdf->setColor(0.666,0.73,0.66);

    $pdf->filledRectangle($pdf->ez['leftMargin'],$pdf->y-$pdf->getFontHeight(14)+$pdf->getFontDecender(14),$pdf->ez['pageWidth']-$pdf->ez['leftMargin']-$pdf->ez['rightMargin'],$pdf->getFontHeight(14));

    $pdf->setColor(0,0,0);

    $pdf->ezText('<C:dest:'.$K.'>'.$cat.'. '.$I,14,array('justification'=>'left','leading'=>15));

    $pdf->restoreState();

    $pdf->ezText("\n",12,array('justification'=>'left','leading'=>13));



    $pages[$K][0] = $pdf->ezWhatPageNumber($pdf->ezGetCurrentPageNumber());



	$chap = 1;

	foreach($DocChapter as $var){

	        $pdf->saveState();

	        $pdf->setColor(0.9,0.9,0.9);

	        $pdf->filledRectangle($pdf->ez['leftMargin'],$pdf->y-$pdf->getFontHeight(12)+$pdf->getFontDecender(12),$pdf->ez['pageWidth']-$pdf->ez['leftMargin']-$pdf->ez['rightMargin'],$pdf->getFontHeight(12));

	        $pdf->setColor(0,0,0);

	        $pdf->ezText('<C:dest:'.$K.'_'.$var[id].'>'.$cat.'.'.$chap.' '.$var[title],12,array('justification'=>'left','leading'=>13));

	        $pdf->restoreState();

	        $pdf->ezText("\n",12,array('justification'=>'left','leading'=>13));



    		$pages[$K][$var[id]] = $pdf->ezWhatPageNumber($pdf->ezGetCurrentPageNumber());



	        $textOptions = array('justification'=>'left');

	        $size = 11;

	        $code = false;

	        foreach($var[text] as $v){

	            if(eregi("{code}",$v)){

	                $pdf->setColor(0.9,0.1,0.1);

	                $v = eregi_replace("{code}","",$v);

	                $textOptions = array('justification'=>'left','left'=>20,'right'=>20);

	                $size=10;

	                $code = true;

	            }

	            if(eregi("{/code}",$v)){

	                $pdf->setColor(0,0,0);

	                $v = eregi_replace("{/code}","",$v);

	                $textOptions = array('justification'=>'left');

	                $size=11;

	                $code = false;

	            }
/* Änderung durch Ronny Grabo (alias komma, email: komma@everymail.net) am 16.09.2004 
SpecialTags {newpage},{bold} und {italic} wurden hinzugefügt
*/
              
             if(eregi("{bold}",$v)){

	            	if($code === false){

	                    preg_match_all("/{bold}(.*){\/bold}/Uism",$v,$match);

	                    for($c=0;$c<count($match[0]);$c++){

	                        $v = str_replace($match[0][$c],"<b>".$match[1][$c]."</b>",$v);

	                    }

	                }

	            }
              
              
             if(eregi("{italic}",$v)){

	            	if($code === false){

	                    preg_match_all("/{italic}(.*){\/italic}/Uism",$v,$match);

	                    for($c=0;$c<count($match[0]);$c++){

	                        $v = str_replace($match[0][$c],"<i>".$match[1][$c]."</i>",$v);

	                    }

	                }

	            }

 if(eregi("{newpage}",$v)){

	            	if($code === false){
                preg_match_all("/{newpage}/Uism",$v,$match);
                  for($c=0;$c<count($match[0]);$c++){
                    $v = str_replace($match[0][$c],"",$v);
	                    }
                      $pdf -> ezNewPage();
                    }
	                }              
/* ENDE : Änderung durch Ronny Grabo am 16.09.2004 */

	            if(eregi("{ilink",$v)){

	            	if($code === false){

	                    preg_match_all("/{ilink (.*)}(.*){\/ilink}/Uism",$v,$match);

	                    for($c=0;$c<count($match[0]);$c++){

	                        $v = str_replace($match[0][$c],"<c:ilink:doc".$match[1][$c]."><u>".$match[2][$c]."</u></c:ilink>",$v);

	                    }

	                }

	            }

	            if(eregi("{link",$v)){

	            	if($code === false){

	                    preg_match_all("/{link (.*)}(.*){\/link}/Uism",$v,$match);

	                    for($c=0;$c<count($match[0]);$c++){

	                        $v = str_replace($match[0][$c],"<c:alink: ".$match[1][$c]."><u>".$match[2][$c]."</u></c:alink>",$v);

	                    }

	                }

	            }
              
       
				if($code === true){

					$v = htmlentities($v);

				}



	            if(preg_match("/{img (.*)}/Uis",$v,$match)){

	            	if($code === false){

	                    $pdf->ezText('',$size,$textOptions);

	                    $path = $Doc->DocPath.$Doc->Doc.'/'.$_GET[lang]."/img/".$match[1];

	                    $width = getimagesize($path);

	                    $pdf->ezImage($path,4,$width[0],'none','left');

	                } else {

						$pdf->ezText($v,$size,$textOptions);

					}

	            } else {

	                $pdf->ezText($v,$size,$textOptions);

	            }

	        }

	        $chap++;

	}



	$cat++;



}



$pdf->ezStopPageNumbers(1,1);

$pdf->ezInsertMode(1,1,'before');

$pdf->ezNewPage();



$pdf->setColor(0.666,0.73,0.66);

$pdf->ezText("<u>\n\nRedaxo ".ucfirst($Doc->Doc)." Documentation</u>",18,array('justification'=>'center'));

$pdf->setColor(0,0,0);

$pdf->ezText("redaxo cms http://www.redaxo.de\n\n",15,array('justification'=>'center'));





$cat = 1;

foreach($Index as $K=>$I){



	$chapter[0][name] = $cat.". ".$I;

	$pdf->ezTable($chapter,array('name'=>''),'',

	array('shadeCol2'=>array(0.666,0.73,0.66),'showLines'=>0,'showHeadings'=>0,'shaded'=>2,'xPos'=>'30','xOrientation'=>'right','width'=>510,

	'cols'=>array('name'=>array('justification'=>'left')))

	);

	$pdf->ezText('',4,array('justification'=>'center'));



	$Titles = $Doc->loadChapterTitles($K);

	$c=0;

	unset($data);

	foreach($Titles as $key=>$var){

		$data[$c][name] = '<c:ilink:doc'.$K.'_'.$key.'>'.$cat.'.'.($c+1).' '.$var.'</c:ilink>';

		$data[$c][page] = $pages[$K][$key];

	    $c++;

	}





	$pdf->ezTable($data,array('name'=>'','page'=>''),'',

	array('shadeCol2'=>array(0.94,0.93,0.92),'showLines'=>0,'showHeadings'=>0,'shaded'=>2,'xPos'=>'90','xOrientation'=>'right','width'=>450,

	'cols'=>array('name'=>array('justification'=>'left'),'page'=>array('width'=>100,'justification'=>'right')))

	);



	$pdf->ezText('',4,array('justification'=>'center'));



	$cat++;

}



$docName = "redaxo_".$_GET[doc].'_'.$Doc->Lang;



$pdf->ezDownload($options=array('filename'=>$docName));



/*

$pdfcode = $pdf->output();

$fp = @fopen($docName,'w') or die ("Error!! Can not write doc File: ".$docName);

fwrite($fp,$pdfcode);

fclose($fp);

*/



exit;





?>