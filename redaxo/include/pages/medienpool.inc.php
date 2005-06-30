<?

// error_reporting( E_ALL);

// TODO

// Alle Funktionen fuer medienpool nur hier einbauen

// permissions einbauen über
// nur user mit $REX_USER->isValueOf("rights","admin[]"); koennen die ordnerverwaltung starten
// sofern zugriff auf eine categorie dann auch zugriff auf die unterkategorien
// keine speziellen filezugriffseinschraenkungen

// wegen der files in ordner verschieben oder loeschen geschichte wuerde ich gerne
// alles über "echte" submit buttons abschicken lassen und auch die markierten reihen sollten
// "eingefärbt" werden

// verschieben funktionen mit $REX_USER->isValueOf("rights","advancedMode[]");  schuetzen


/**
 * Redaxo - Mediapool v3
 * @author Markus Staab http://www.public-4u.de
 */

// Funktionen nur ausführen wenn der Medienpool als Page aufgerufen wird.
// 
// Falls dieser nur als include in einem anderen Addon dient,
// => NICHTS anzeigen 
// => NUR Klassen zur Verfüung stellen
$params = new rexPoolParams();
 
if ( $_REQUEST['page'] == $params->page) {
    $pool = new rexPool( $params);
    
    // Action verarbeiten
    $pool->handleAction();
}

class rexPoolComponent {
    var $params;
    
    function rexPoolComponent( &$params) {
        $this->params =& $params;
    }
    
    function _dateFormat() {
        return 'd-M-Y | H:i';
    }
    
    function _link( $label, $params = '', $additional = array(), $inlineLink = true) {
        $add = '';
        if ( is_array( $additional)) {
            foreach( $additional as $addName => $addValue) 
            {
                $add .= ' '. $addName . '="'. $addValue . '"';
            }
        }
        
        $remindGlobals = array( 'opener_input_field');
        foreach( $remindGlobals as $var) {
            if ( isset( $_GET[$var])) {
                $params .= '&'. $var .'='. $_GET[$var];
                
            }
        }
        
        if ( $params != '') { 
            if ( $params[0] != '&' && $inlineLink) {
                $params = '&' . $params;
            }
            $params = htmlentities( $params);
        }
        
        if ( $inlineLink) {
            $params = '?page=' .$this->params->page . $params;
        }
        
        return '<a href="'. $params .'"'. $add .'>'. $label .'</a>';
    }
    
    function _title( $subtitle = '', $titleAppendix = '') {
        global $I18N;
        title($I18N->msg('pool_name') . $titleAppendix, '&nbsp;&nbsp;&nbsp;'.$subtitle, 'grey', '100%');
    }
    
    function _imageSrc( $media) {
        global $REX;
        if ( empty( $REX['ABS_REX_ROOT'])) {
            $REX['ABS_REX_ROOT'] = str_replace( "/redaxo/index.php", "",$_SERVER['SCRIPT_NAME']); 
        }
        $src = $REX['ABS_REX_ROOT'] . $REX['WWW_PATH'] .'/files/'. $filename;
        
        return $src;
    }
    
    function _indent( $level, $indentStr = '   ') {
        return str_repeat( $indentStr, $level);
    }
    
    function _message( $stateColspan = 1, $messageColspan = 2) {
        global $I18N;
        
        $s = '';
        $message = $this->params->message;
        
        if ( $message != '') {
			if ( $this->params->messageLevel > 0) {
			
	            // Fehler
				$s .= '
				<tr class="warning">
					<td><img src=pics/warning.gif width=16 height=16></td>
					<td colspan="'. ($stateColspan+$messageColspan) .'">
						'. $I18N->msg( 'pool_error') .' '. $message .'</td>
				</tr>'. "\n";

			} else {

				// Statusmeldung
				$s .= '
				<tr class="warning">
					<td><img src=pics/warning.gif width=16 height=16></td>
					<td colspan="'. ($stateColspan+$messageColspan) .'">
						'. $I18N->msg( 'pool_status') .' '. $message .'</td>
				</tr>'. "\n";
			}
        }
        
        return $s;
    }
}

class rexPoolComponentList extends rexPoolComponent {
    var $columns;
    
    function rexPoolComponentList( &$params, &$columns) {
        parent::rexPoolComponent( $params);
        $this->columns =& $columns;
    } 
    
    function _getColGroup( $indent = 3) {
        $colgroup = "\n". $this->_indent( $indent) .'<colgroup>'. "\n";
        
        foreach ( $this->columns as $columnName => $columnWidth) {
            $colgroup .= $this->_indent( $indent + 1) .'<col width="'. $columnWidth .'"/>'. "\n";
        }
        
        $colgroup .= $this->_indent( $indent) .'</colgroup>'. "\n";
        
        return $colgroup;
    }
    
    function _getColLabels( $indent = 3) {
        $collabels = "\n". $this->_indent( $indent) .'<tr>'. "\n";
        
        foreach ( $this->columns as $columnName => $columnWidth) {
            $collabels .= $this->_indent( $indent + 1) .'<th>'. $columnName .'</th>'. "\n";
        }
        
        $collabels .= $this->_indent( $indent) .'</tr>'. "\n";
        
        return $collabels;
    }
    
    function _getTable( $indent = 2) {
        return "\n". $this->_indent( $indent) .'<table class="rex" cellpadding="5" cellspacing="1">'. "\n";
    }
    
    function _getTableEnd( $indent = 2) {
        return "\n". $this->_indent( $indent) .'</table>'. "\n";
    }
    
    function formatTableHead( $labels = true, $groups = true) {
        $s = $this->_getTable();
        
        if ( $groups) {
            $s .= $this->_getColGroup();
        }
        
        if ( $labels) {
            $s .= $this->_getColLabels();
        }
        
        return $s;
    }
    
    function formatTableEnd() {
        return $this->_getTableEnd();
    }
}


class rexPoolUpload extends rexPoolComponent {
    function rexPoolUpload( $params) {
        parent::rexPoolComponent( $params);
    }
    
    function _getModes() {
        return $modes = array( 'file' => 'pool_upload_file');
    }
    
    function _title( $subtitle = '') {
        global $I18N, $pool;
        
        $modes = $this->_getModes();
        $actMode = $this->params->mode;
        
        $first = true;
        foreach( $modes as $modeName => $modeLabelKey) {
            $modeLabel = $I18N->msg( $modeLabelKey);
            
            if ( $first) {
                $first = false;
            } else {
                $subtitle .= ' : ';
            }
            
            if( $modeName != $actMode) {
                $subtitle .= rexPool::_link( $modeLabel, 'action=media_upload&mode='. $modeName);
            } else {
                $subtitle .= $modeLabel;
            } 
        }
        
        if ( $subtitle != '') {
            $subtitle = ' - '. $subtitle;
        }
        
        parent::_title( $pool->_getPath(), $subtitle);        
    }
    
    function &handle( &$file, $register = true) {
        global $REX, $REX_USER, $I18N;
        
        $newFilename = basename( strtolower( str_replace( ' ', '_', $file['name'])));
        
        $result = array();
        $result['title']       = isset( $_POST['mediaTitle']) ? $_POST['mediaTitle'] : '';
        $result['description'] = isset( $_POST['mediaDescription']) ? $_POST['mediaDescription'] : '';
        $result['copyright']   = isset( $_POST['mediaCopyright']) ? $_POST['mediaCopyright']: '';
        $result['cat_id']      = isset( $_POST['cat_id']) ? $_POST['cat_id'] : '';
        
        $result['orgname'] = $file['name'];
        $result['size']    = $file['size'];
        $result['type']    = $file['type'];
        $result['width']   = '';
        $result['height']  = '';

        $result['createdate'] = time();
        $result['createuser'] = $REX_USER->getValue('login');
        $result['updatedate'] = time();
        $result['updateuser'] = $REX_USER->getValue('login');
        
        $result['error'] = '';
            
        if ( $file[ 'name'] == '') {
            $result['error'] .= $I18N->msg('pool_error_miss_file');
        }
        
        if ( $result['error'] == '' && strrpos($newFilename,'.') != '')
        {
            $fname = substr( $newFilename, 0, strrpos( $newFilename, '.'));
            $extension  = OOMedia::_getExtension( $newFilename);
            
            $illegals = array( 'php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'asp', 'aspx', 'cfm', 'sh');
            if ( in_array( $extension, $illegals))
            {
                $extension .= ".txt";
            }

            $result['name'] = $this->_genFileName( $fname .'.'. $extension);
            $absFile = $REX['MEDIAFOLDER'] . '/'. $result['name'];
            
            if ( move_uploaded_file( $file['tmp_name'], $absFile))
            {
            	
            	//  || copy( $file['tmp_name'], $absFile)
				// unter windows werden die rechte zerstoert und eventuell schreibgeschuetzt gesetzt
                if ( $REX['MEDIAFOLDERPERM'] == '') $REX['MEDIAFOLDERPERM'] = 0777;
				chmod( $absFile, 0777);
            }else
            {
                $result['error'] .= $I18N->msg('pool_error_move_failed', $result['orgname']); 
            }
            
            if ( OOMedia::_isImage( $absFile)) {
                if( $size = @getimagesize( $absFile)) {
                    $result['width'] = $size[0];
                    $result['height'] = $size[1];
                }
            }
        }
        else if ( $result['error'] == '')
        {
            $result['error'] .= $I18N->msg('pool_error_miss_file_ext', $result['orgname']);
        }
        
//        var_dump( $absFile);
//        var_dump( $newFilename);
        
//        var_dump( $result);
        
        // Bei Fehlern ist hier schluss
        if( $result['error'] != '') {
            return $result['error'];
        }
        
        // Dateien die nur hochgeladen werden sollen, aber nicht in der DB registriert
        if( !$register) {
            return true;
        }
        
        // Objekt anlegen
        $media = new OOMedia();
        unset( $result['error']);
        
        // Attribute zuweisen
        foreach ( $result as $detail => $value) {
            $detail = '_'. $detail;
            $media->$detail = $value;
        }
        
        // Speichern
        $media->_insert();
        
        return true;
    }
    
    function _genFileName( $filename) {
        global $REX;
        
        $fname = substr( $filename, 0, strrpos( $filename, '.'));
        $extension  = OOMedia::_getExtension( $filename);
        
        // datei schon vorhanden ? wenn ja dann _1
        $t = 1;
        while( file_exists($absFile = $REX['MEDIAFOLDER'] .'/'. $filename))
        {
            $filename = $fname .'_'. $t .'.'. $extension;
            $t++;
        }
        
        return $filename;
    }
} 

/**
 * Main-Class
 */
class rexPool extends rexPoolComponent {
    /**  Parameter des Medienpools */
    var $params;
    
    /**  Die aktuell angezeigte Kategorie */
    var $cat;
    
    /**  Die Kind-Kategorien der aktuellen Kategorie */
    var $catList;
    
    /**  Das aktuell angezeigte Medium */
    var $media;
    
    /**  Die Medien der aktuellen Kategorie */
    var $mediaList;
    
    function rexPool( &$params) {
        parent::rexPoolComponent( $params);
        
        // Evtl. Formular Posts verarbeiten
        $this->handlePosts( $params);
        
        // Liste der anzuzeigenden Kategorien
        $catId = $params->catId;
        
        if( $catId !== '') {
            $ooCurrentCat = OOMediaCategory::getCategoryById( $catId);
            $currentCat = new rexMediaCategory( $params, $ooCurrentCat);
        } else {
            $currentCat = null;
            $ooMediaList = null;
        }
        
        $this->cat =& $currentCat;
        $this->catList =& new rexMediaCategoryList( $params, $ooCurrentCat);
        $this->mediaList =& new rexMediaList( $params, $ooCurrentCat); 
    }
    
    function &_getCat() {
        return $this->cat;
    }
    
    function &_getMedia() {
        if ( $this->media !== null) {
            return $this->media;
        }
        
        if ( !isset( $_GET['media_id'])) {
            if ( !isset( $_GET['file_name'])) {
            } else {
                $mediaName = $_GET['file_name'];
                $this->media = OOMedia::getMediaByName( $mediaName);
            }
        } else {
            $mediaId = $_GET['media_id'];
            $this->media = OOMedia::getMediaById( $mediaId);
        }
        
        return $this->media;
    }
    
    function &_getCatList() {
        return $this->catList;
    }
    
    function &_getMediaList() {
        return $this->mediaList;
    }
    
    function _getPath() {
        global $I18N;
        
        $currentCat =& $this->_getCat();
        
        // cat wurde nicht übergeben, dann vom aktuellen media holen
        if ( $currentCat == null) {
            $ooMedia = $this->_getMedia();
            if ( $ooMedia !== null) {
                $currentCat = new rexMediaCategory( $this->params, $ooMedia->getCategory());
            }
        }
        
        // Pfad der aktuellen Kategorie anzeigen
        $path = 'Pfad: '. rexPool::_link( $I18N->msg('pool_default_cat'), '');
        
//        var_dump( $currentCat);
        
        if( $currentCat != null) {
            $currentOOCat =& $currentCat->_getOOCat();
            
            $pathList = explode( '|', $currentOOCat->getPath());
            
            // Pfad zur aktuellen Kategorie
            foreach ( $pathList as $pathCatId) {
                if( $pathCatId == '') {
                    continue;
                }
                
                $pathCat = OOMediaCategory::getCategoryById( $pathCatId);
                $path .= ' : '.$this->_link( $pathCat->getName(), 'cat_id='. $pathCat->getId());
            }
            
            // Aktuelle Kategorie
            $path .= ' : '. $this->_link( $currentOOCat->getName(), 'cat_id='. $currentOOCat->getId());
        }
        
        return $path;
    }
    
    function listMedia() {
        global $I18N;
        
        $currentCat =& $this->_getCat();
        $this->_title( $this->_getPath());
        
        $catList =& $this->_getCatList();
        if ( $catList !== null) {
            echo $catList->format();
        }

        // In der Root-Kategorie keine Medias anzeigen        
        if( $currentCat != null) {
            $mediaList =& $this->_getMediaList();
            if ( $mediaList !== null) {
                echo $mediaList->format();
            }
        }
    }
    
    function mediaDetails() {
        global $I18N;
        
        $this->_title( $this->_getPath(), ' - '. $I18N->msg('pool_media_detail_title'));
        
        $ooMedia = $this->_getMedia();
        if ( $ooMedia === null) {
            $this->params->miss( 'media_id or file_name');
        }
        
        $rexMedia = new rexMedia( $this->params, $ooMedia);
        
        echo '       <table class="rex" cellpadding="5" cellspacing="1">
             '. $rexMedia->formatDetailed() .'
                     </table>';
    }
    
    function handleAction() {
        // Ausgabe des Seitenkopfes
        $this->_header();
        
        switch ( $this->params->action) {
            case 'cat_details'   : $this->catDetails();   break;
            case 'media_details' : $this->mediaDetails(); break;
            case 'media_upload'  : $this->uploadMedia(); break;
        //    case 'media_search'  : rexPool::mediaDetails(); break;
            default              : $this->listMedia();
        }
        
        // Ausgabe des Seitenfußes
        $this->_footer();
    }
    
    function handlePosts( &$params) {
        global $REX, $REX_USER, $I18N;
        
        if ( !isset ( $_POST)) {
            return;
        }
        
        // Kategorie anlegen/speichern
        if ( isset( $_POST['saveCatButton']) || isset( $_POST['addCatButton']))
        {
            // Id der Kategorie in der sich die zu editieren de Kategorie befindet (ParentId)
            $catId = $this->params->catId;
            // Id der zu editierenden Kategorie
            $catModId = $this->params->catModId;
            
            // Action parameter resetten, damit nach Anlegen das Formular ausgeblendet ist             
            $this->params->action = '';
            
            if( $catModId !== '') {
                $cat = OOMediaCategory::getCategoryById( $catModId);
            } else {
                $cat = new OOMediaCategory();
                $cat->_parent_id = $catId;
                $cat->_createdate = time();
                $cat->_createuser = $REX_USER->getValue('login');
                
                if ( $cat->hasParent()) {
                    $parent = $cat->getParent();
                    $cat->_path = $parent->getPath() . $parent->getId() . '|';
                } else {
                    $cat->_path = '|'; 
                }
            }
            
            $cat->_updatedate = time();
            $cat->_updateuser = $REX_USER->getValue('login');
            $cat->_name = $_POST['catName'];
            
            $error = $cat->_save();
            
            // Fehlerbehandlung
            if ( $error != '')
            {
                if( strpos( $error, 'Duplicate entry') !== false) {
                    $message = $I18N->msg( 'pool_error_categoryname_exists');
                } else {
                    $message = $I18N->msg( 'pool_error_external', $error);
                } 
                $this->params->error( $message);
            } else {
                $this->params->message( $I18N->msg( 'pool_category_created', $_POST['catName']));
            }
            
            
            // Speicher freigeben
            unset( $cat);
        }
        // Kategorie löschen
        else if ( isset( $_POST['deleteCatButton'])) 
        {
            // Id der zu löschenden Kategorie
            $catModId = $this->params->catModId;
            
            // Kategorie holen
            $cat = OOMediaCategory::getCategoryById( $catModId);
            
            if ( $cat->countChildren() > 0) {
                // Fehlermeldung ausgeben            
                $this->params->error( $I18N->msg( 'pool_category_not_deleted_childs', $cat->getName()));
            } else {
                // Statusmeldung ausgeben            
                $this->params->message( $I18N->msg( 'pool_category_deleted', $cat->getName()));
                // Kategorie löschen
                $cat->_delete();
            }
            
            // Speicher freigeben
            unset( $cat);
        } 
        // Medium löschen
        else if ( isset( $_POST['deleteMediaButton'])) 
        {
            // Id des zu löschenden Mediums
            $mediaId = $this->params->mediaId;
            
            // Hier die action resetten, damit die Kategorie des gelöschten Mediums angezeigt wird
            $this->params->action = '';
            
            // Medium holen
            $media = OOMedia::getMediaById( $mediaId);

            // Medium löschen
            if ($media->isInUse())
			{
				$error = $I18N->msg( 'pool_media_not_deleted', $error);
				$this->params->error( $I18N->msg( 'pool_error_external', $error));
			}else
			{
				$error = $media->_delete();

	            // Fehlerbehandlung
	            if ( $error != '')
	            {
	                // Fehlermeldung ausgeben            
	                // $this->params->error( $I18N->msg( 'pool_error_external', $error));
	                
	            } else {
	                // Statusmeldung ausgeben            
	                $this->params->message( $I18N->msg( 'pool_media_deleted', $media->getFileName()));
	            }
			}
            
            
            // Speicher freigeben
            unset( $media);
        }
        // Medium bearbeiten
        else if ( isset( $_POST['saveMediaButton'])) 
        {
            // Id des zu löschenden Mediums
            $mediaId = $this->params->mediaId;
            $this->params->catId = $_POST['mediaCatId'];
            
            // Hier die action resetten, damit die Kategorie des gelöschten Mediums angezeigt wird
            $this->params->action = '';
            
            // Medium holen
            $media = OOMedia::getMediaById( $mediaId);
            
            $result = array();
            $result['title']       = isset( $_POST['mediaTitle']) ? $_POST['mediaTitle'] : '';
            $result['description'] = isset( $_POST['mediaDescription']) ? $_POST['mediaDescription'] : '';
            $result['copyright']   = isset( $_POST['mediaCopyright']) ? $_POST['mediaCopyright']: '';
            $result['cat_id']      = isset( $_POST['cat_id']) ? $_POST['cat_id'] : '';
            
            $result['updatedate'] = time();
            $result['updateuser'] = $REX_USER->getValue('login');
            
            // Medium ersetzen
			if ($_FILES["mediaFile"]["name"] != "")
			{
				$file = $_FILES["mediaFile"];
				$newFilename = basename( strtolower( str_replace( ' ', '_', $file['name'])));
				$newExtension = OOMedia::_getExtension( $newFilename);
				$oldExtension = $media->getExtension();
				
				if ($newExtension != $oldExtension)
				{
					$error = "Die Dateiendungen muessen identisch sein, damit das Medium ersetzt werden kann";					
				}else
				{
					// Datei ersetzen !
					$absFile = $REX['MEDIAFOLDER'] . '/'. $media->getFileName();


					if ( move_uploaded_file( $file['tmp_name'], $absFile) )
					{
						// || copy( $file['tmp_name'], $absFile)
						// unter windows werden die rechte zerstoert und eventuell schreibgeschuetzt gesetzt
						if ( $REX['MEDIAFOLDERPERM'] == '') $REX['MEDIAFOLDERPERM'] = 0777;
						chmod( $absFile, 0777);

						$result['orgname'] = $file['name'];
						$result['size']    = $file['size'];

						if ( OOMedia::_isImage( $absFile)) {
			                if( $size = @getimagesize( $absFile)) {
			                    $result['width'] = $size[0];
			                    $result['height'] = $size[1];
			                }
			            }
					}else
					{
						$error .= $I18N->msg('pool_error_move_failed', $result['orgname']); 
					}
				}
			}
            
            // Attribute zuweisen
            foreach ( $result as $detail => $value) {
                $detail = '_'. $detail;
                $media->$detail = $value;
            }
            
            // Speichern
            $error = $media->_update().$error;
            
            // Fehlerbehandlung
            if ( $error != '')
            {
                // Fehlermeldung ausgeben            
                $this->params->error( $I18N->msg( 'pool_error_external', $error));
            } else {
                // Statusmeldung ausgeben            
                $this->params->message( $I18N->msg( 'pool_media_changed', $media->getFileName()));
            }
            
            // Speicher freigeben
            unset( $media);
        }
    }
    
    function uploadMedia() {
        global $REX,$I18N;
        
        $upload = new rexPoolUpload( $this->params);
        
        echo $upload->_title();
        
        // handle file upload(s)
        if( !empty($_POST)) {
            
            $this->params->message = $I18N->msg( 'pool_media_uploaded');
            
            foreach( $_FILES as $file) {
                if (( $result = $upload->handle( $file)) !== true) {
                    $this->params->error( $result, false, true);
                    break;
                }
            }
        }
        
        echo rexMedia::formatForm();
    }
    
    function _header() {
        global $I18N, $REX;
    ?>
    <html>
       <head>
          <title><?php echo $REX['SERVERNAME'] .' - '. $I18N->msg('pool_name'); ?></title>
          <link rel=stylesheet type=text/css href=css/style.css>
          <script language=Javascript>
          <!--
          var redaxo = true;
          <?php if ( $this->params->openerFieldName != '') : ?>
          function selectMedia(filename)
          {
             opener.document.REX_FORM.<?php echo $this->params->openerFieldName ?>.value = filename;
             self.close();
          }
          <?php endif; ?>
          
          function insertImage(src, alt, width, height)
          {
             var image = '<img src="'+ src +'" alt="'+ alt +'" width="'+ width +'" height="'+ height +'" vspacing="5" hspacing="5" align="left" border="0"/>';
             insertHTML( image);
          }
          
          function insertHTML(html){
             window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
             self.close();
          }
    
          function checkBoxes(FormName, FieldName, CheckValue)
          {
             // alert( 'Checkvalue ' + CheckValue);
             if(!document.forms[FormName]) {
                // alert( 'Form gibts nicht');
                return;
             }
             
             var objCheckBoxes = document.forms[FormName].elements[FieldName];
             
             if(!objCheckBoxes) {
                // alert( 'Boxen gibts nicht');
                return;
             }
             
             var countCheckBoxes = objCheckBoxes.length;
             if(!countCheckBoxes) {
                objCheckBoxes.checked = CheckValue;
             } else {
                // set the check value for all check boxes
                for(var i = 0; i < countCheckBoxes; i++) {
                   objCheckBoxes[i].checked = CheckValue;
                }
             }
          }
          //-->
          </script>
       </head>
    <body>
    
       <table class="rexHeader" style="width: 100%;" cellpadding="5" cellspacing="0">
       
          <tr>
             <th colspan="3"><?php echo $I18N->msg('pool_name') .' '. $REX['SERVERNAME']; ?></th>
          </tr>
    
          <tr>
             <td>
               <?php echo rexPool::_link( $I18N->msg('pool_file_list'), 'cat_id='. $this->params->catId, array( 'class' => 'white')) ?> |
               <?php echo rexPool::_link( $I18N->msg('pool_file_upload'), 'action=media_upload&mode=file&cat_id='. $this->params->catId, array( 'class' => 'white')) ?>
               <!-- <?php echo " | ".rexPool::_link( $I18N->msg('pool_file_search'), 'action=media_search', array( 'class' => 'white')) ?> -->
             </td>
          </tr>
          
       </table>
       
       <form name="poolForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" style="display: inline;" enctype="multipart/form-data">
       
          <input type="hidden" name="page" value="medienpool"/>
          <input type="hidden" name="action" value="<?php echo $this->params->action ?>"/>
          <input type="hidden" name="mode" value="<?php echo $this->params->mode ?>"/>
          <input type="hidden" name="cat_id" value="<?php echo $this->params->catId ?>"/>
          <input type="hidden" name="cat_modid" value="<?php echo $this->params->catModId ?>"/>
          <input type="hidden" name="media_id" value="<?php echo $this->params->mediaId ?>"/>
          <input type="hidden" name="opener_input_field" value="<?php echo $this->params->openerFieldName ?>"/>
          
    <?php
    }
    
    function _footer() {
        global $I18N;
    ?>
       </form>
       
       <a name="bottom"></a>
       <br/>
       <table class="rexFooter" style="width: 100%" cellpadding="5" cellspacing="0">
       
          <tr>
             <th colspan="2">&nbsp;</th>
          </tr>
          
          <tr>
             <td>
                <a href="http://www.pergopa.de" target="_blank" class="black">pergopa kristinus gbr</a> |
                <a href="http://www.redaxo.de" target="_blank" class="black">redaxo.de</a> |
                <a href="http://forum.redaxo.de">?</a>
             </td>
             <td style="text-align: right;">
                <?php echo showScripttime() ?> sec | <?php echo strftime($I18N->msg("dateformat"))?>
             </td>
          </tr>
             
          </table>
    
       </body>
    </html>
    <?php
    }
} 

/**
 * Class which provides getter functions for all needed pool-parameters
 * All Methods are static!
 */
class rexPoolParams {
    var $page;
    
    var $catId;
    var $catModId;
    
    var $mediaId;
    
    var $action;
    var $mode;
    
    var $openerFieldName;
    var $isEditorMode;
    
    /**  Fehler/Statusmeldung */
    var $message;
    var $messageLevel;
    
    function rexPoolParams() {
        $this->page = 'medienpool';
         
        $this->catId = !empty( $_REQUEST['cat_id']) ? (int) $_REQUEST['cat_id'] : '';
        $this->catModId = !empty( $_REQUEST['cat_modid']) ? (int) $_REQUEST['cat_modid'] : '';
        
        $this->mediaId = !empty( $_REQUEST['media_id']) ? (int) $_REQUEST['media_id'] : '';
        
        $this->action = !empty( $_REQUEST['action']) ? $_REQUEST['action'] : '';
        $this->mode = !empty( $_REQUEST['mode']) ? $_REQUEST['mode'] : '';
        
        $this->openerFieldName = !empty( $_REQUEST['opener_input_field']) ? $_REQUEST['opener_input_field'] : '';
        $this->isEditorMode = $this->openerFieldName != '';
    }
    
    function miss( $paramName) {
        global $I18N;
        trigger_error( '<p>'. $I18N->msg('pool_error_miss_param', $paramName) .'</p>', E_USER_ERROR);
    }
    
    function error( $errormsg, $append = false, $overwrite = false) {
        $this->_message( $errormsg, 1, $append, $overwrite);
    }
    
    function message( $message, $append = false, $overwrite = false) {
        $this->_message( $message, 0, $append, $overwrite);
    }
    
    function _message( $message, $msgLevel, $append = false, $overwrite = false) {
        
        if ( $this->message != '' && !($append || $overwrite)) {
            return;
        }
        
        
        if ( $append) {
            $this->message .= $message;
        } else if( $this->message == '' || $overwrite) {
            $this->message = $message;
        }
        
        $this->messageLevel = $msgLevel;
    }
}

// user mit media[all] kann alle ordner sehen und bearbeiten + kategorien erstellen/bearbeiten ...
// user mit media[10] kann in kat 10 alles

// user mit media_add[all] darf adden
// user mit media_edit[all] darf editieren
// user mit media_delete[all] darf löschen
// user mit media_get[all] darf jedes bild selektieren

// user mit media_add[10] darf in kat 10 adden
// user mit media_edit[10] darf in kat 10 editieren
// user mit media_delete[10] darf in kat 10 löschen
// user mit media_get[10] darf in kat 10 jedes bild selektieren

/**
 * Class which provides all functions for permission purposes
 * All Methods are static!
 */
class rexPoolPerm {
    function rexPoolPerm() {
        die( 'class-instantiation not allowed for class "' .__CLASS__ .'"');
    }
    
    function hasPerm( $perm) {
//        var_dump( $perm);
        global $REX_USER;
        return $REX_USER->isValueOf( 'rights', $perm);
    }

    function hasMediaPerm( $sub, &$cat) {
        $valids = array( '', '_add', '_edit', '_delete', '_get');
        if ( !in_array( $sub, $valids)) {
            return false;
        }
        $catId = $cat->getId();
        
        if( rexPoolPerm::isAdmin() ||
            rexPoolPerm::isPoolAdmin() ||
            rexPoolPerm::isOwner( $cat->getCreateUser()) ||
            rexPoolPerm::hasCatPerm( $catId)) 
        {
            return true;
        }
        
        return rexPoolPerm::hasPerm( 'media'. $sub .'[all]') ||
               rexPoolPerm::hasPerm( 'media'. $sub .'['. $catId .']');
    }
    
    function isOwner( $userId) {
        global $REX_USER;
        return $REX_USER->isValueOf( 'user_id', $userId);
    }
    
    function isAdvanced() {
        return rexPoolPerm::hasPerm( 'advancedMode[]');
    }
    
    function isAdmin() {
        return rexPoolPerm::hasPerm( 'admin[]');
    }

    function isPoolAdmin() {
        return rexPoolPerm::hasPerm( 'media[all]');
    }
    
    function hasCatPerm( &$cat) {
        return rexPoolPerm::hasMediaPerm( '', $cat);
    }

    function hasAddPerm( &$cat) {
        return rexPoolPerm::hasMediaPerm( '_add', $cat);
    }

    function hasEditPerm( &$cat) {
        return rexPoolPerm::hasMediaPerm( '_edit', $cat);
    }
    
    function hasDelPerm( &$cat) {
        return rexPoolPerm::hasMediaPerm( '_delete', $cat);
    }
    
    function hasGetPerm( &$cat) {
        return rexPoolPerm::hasMediaPerm( '_get', $cat);
    }
}

class rexMediaCategoryList extends rexPoolComponentList  {
    
    var $cat;
    var $cats;
    
    function rexMediaCategoryList( &$params, &$ooCat) {
        global $I18N;
        
        $this->cat =& $ooCat;
        // Parameter hier schon als klassen-variable setzten,
        // da diese unten schon vorm parent-konstruktor aufruf 
        // gebraucht werden (_formatAddLink())
        $this->params =& $params;
        
        if ( $ooCat === null) {
            $ooCatList =& OOMediaCategory::getRootCategories();
        } else {
            $ooCatList =& $ooCat->getChildren();
        }
        
        // hier darf kein foreach stehen wegen problemem mit den referenzen!
        for( $i = 0; $i < count( $ooCatList); $i++) {
            $ooCat =& $ooCatList[ $i];
            $this->cats[] =& new rexMediaCategory( $params, $ooCat); 
        }
        
//            var_dump( $this->cats);
        $columns = array( 
            $this->_formatAddLink() => '50px',
//            '<input type="checkbox" onchange="checkBoxes( \'poolForm\', \'cat_id[]\', this.checked)"/>' => '30px',
            $I18N->msg('pool_colhead_category') => '200', 
            $I18N->msg('pool_colhead_details') => '200px',
            $I18N->msg('pool_colhead_edit') => '*'
        );
        
        parent::rexPoolComponentList( $params, $columns);
    } 
    
    function _formatAddLink() {
        global $I18N;
        $s = '';
        $cat = null;
        $catId = $this->params->catId;
        
        if ( $catId !== '') {
            $cat = OOMediaCategory::getCategoryById( $catId);
        }
        
        if ( $cat === null || rexPoolPerm::hasAddPerm( $cat)) {
            $s .=  $this->_link( '<img src="pics/folder_plus.gif" style="width: 16px; height:16px" alt="'. $I18N->msg('pool_add_category') .'">' , 'action=cat_add&cat_id='. $catId);
        }
        
        return $s;
    }
    
    function _formatParent( $indent = 3) {
        // In den RootCategories kein ParentLink anzeigen
        if ( $this->cat === null) {
            return;
        }
        
        $formatCategoryParent = "\n";
        $formatCategoryParent .= $this->_indent( $indent) . '<tr>'. "\n";
        $formatCategoryParent .= $this->_indent( $indent + 1) . '<td></td>'. "\n"; 
        $formatCategoryParent .= $this->_indent( $indent + 1) . '<td colspan="4">'. $this->_link( '..', 'cat_id='. $this->cat->getParentId()) .'</td>'. "\n"; 
        $formatCategoryParent .= $this->_indent( $indent) . '</tr>'. "\n";
              
        return $formatCategoryParent;
    }
    
    function format( $indent = 3) {
        $s = '';
        $catModId = $this->params->catModId;
        
        $s .= $this->formatTableHead();
        
        //Evtl Fehlerausgabe      
        $s .= $this->_message( 2, 3);

        // Link zur Parent-Kat        
        $s .= $this->_formatParent();
        
        // Evtl. eingabe Formular
        if( $this->params->action == 'cat_add') {
            $s .= rexMediaCategory::formatForm();
        }
        
        if ( $this->cats !== null) {
            foreach( $this->cats as $rexCat) {
                $ooCat =& $rexCat->_getOOCat();
                
                if ( !rexPoolPerm::hasCatPerm( $ooCat)) {
                    continue;
                }
                
                if( empty( $_POST) && $ooCat->getId() == $catModId) {
                    $s .= $rexCat->formatForm( $indent);
                } else {
                    $s .= $rexCat->format( $indent);
                }
            }
        }
        
        $s .= $this->formatTableEnd();
        
        return $s;
    }
}
 
/**
 * Category-Class
 * All Methods are static!
 */
class rexMediaCategory extends rexPoolComponent {
    
    var $ooCat;
    
    function rexMediaCategory( &$params, &$ooCat) {
        parent::rexPoolComponent( $params);
        $this->ooCat =& $ooCat;
    }
    
    function format( $indent) {
        $cat =& $this->_getOOCat();
        
        $s = "\n";
        $s .= $this->_indent( $indent) .'<tr>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td align=center><img src="pics/folder.gif" width=16 height=16></td>'. "\n";
        // $s .= $this->_indent( $indent + 1) .'<td><input type="checkbox" name="cat_id[]" value="'. $cat->getId() .'"/></td>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td>'. $this->_formatName() .'</td>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td>'. $this->_formatDetails() .'</td>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td>'. $this->_formatActions() .'</td>'. "\n";
        $s .= $this->_indent( $indent) .'</tr>'. "\n";
                 
        return $s;
    }
    
    function _formatName() {
        global $REX_USER;
        $cat =& $this->_getOOCat();
        
        $name = rexPool::_link( $cat->getName(), 'cat_id='. $cat->getId());
        
        // Im AdvancedMode IDs der Kategorien anzeigen
        if ( rexPoolPerm::isAdvanced()) {
            $name .= ' ['. $cat->getId() .']';
        } 
        
        return $name;        
    }
    
    function _formatActions() {
        global $I18N;
        $OOCat =& $this->_getOOCat();
        $OOCatId = $OOCat->getId();
        
        // Prüfen der Berechtigungen
        if ( !rexPoolPerm::hasDelPerm( $OOCat) && !rexPoolPerm::hasEditPerm( $OOCat)) {
            return '';
        }
        
        return rexPool::_link( $I18N->msg('pool_cat_action'), 'cat_id='. $this->params->catId .'&cat_modid='. $OOCatId);;
    }
    
    function _formatDetails() {
        global $I18N;
        $cat =& $this->_getOOCat();
        
        $s = $I18N->msg('pool_subcats').': '. $cat->countChildren() . '<br/>'.
             $I18N->msg('pool_files').': '. $cat->countFiles();
        
        return $s;
    }
    
    function formatForm( $indent = 3) {
        global $I18N;
        
        // Defaultwerte für Kategorie laden, wenn nicht-statisch aufgerufen!
        if ( is_a($this, 'rexMediaCategory')) {
            $ooCat = $this->_getOOCat();
            $catId = $ooCat->getId();
            $catName = $ooCat->getName();
            $buttons = $this->_formatFormButtons();
        } else {
            $catId = '';
            $catName = '';
            $buttons = '<input type="submit" name="addCatButton" value="'. $I18N->msg( 'add_category') .'"/>'; 
        }
        
        $s = "\n";
        $s .= $this->_indent( $indent) .'<tr>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td><img src="pics/folder.gif" style="width: 16px; height:16px; margin: auto;"></td>'. "\n";
        // $s .= $this->_indent( $indent + 1) .'<td><input type="checkbox" name="cat_id[]" value="'. $catId .'"/></td>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td><input type="text" name="catName" value="'. $catName .'" style="width: 100%"/></td>'. "\n";
        $s .= $this->_indent( $indent + 1) .'<td colspan="2">'. $buttons .'</td>'. "\n";
        $s .= $this->_indent( $indent) .'</tr>'. "\n";
              
        return $s;
    }
    
    function _formatFormButtons() {
        global $I18N;
        
        $cat =& $this->_getOOCat();
        $s = '';
        
        if ( $cat === null || rexPoolPerm::hasEditPerm( $cat)) {
            $s .= '<input type="submit" name="saveCatButton" value="'. $I18N->msg( 'save_category') .'"/>';
        }
        
        if ( $cat === null || rexPoolPerm::hasDelPerm( $cat)) {
            $s .= '<input type="submit" name="deleteCatButton" value="'. $I18N->msg( 'delete_category') .'"/>';
        } 
        
        return $s;
    }
    
    function &_getOOCat() {
        return $this->ooCat;
    }
}

class rexMediaList extends rexPoolComponentList {
    var $medias;
    /** current OOCat */
    var $cat;
    
    function rexMediaList( &$params, &$ooCat) {
        global $I18N;
        
        if ( $ooCat === null) {
            return;
        }
        
        $columns = array( 
            $I18N->msg('pool_colhead_type') => '50px',
//          '<input type="checkbox" onchange="checkBoxes( \'poolForm\', \'media_id[]\', this.checked)"/>' => '30px',
            $I18N->msg('pool_colhead_preview') => '200px',
            $I18N->msg('pool_colhead_filedetails') => '200px',
            $I18N->msg('pool_colhead_description')=> '*'
        );
        
        if ( $params->isEditorMode) {
            $columns[$I18N->msg('pool_colhead_functions')] = '80px';
        }
        
        parent::rexPoolComponentList( $params, $columns);
        
        $ooMedias = $ooCat->getFiles();
        // hier darf kein foreach stehen wegen problemem mit den referenzen!
        for( $i = 0; $i < count( $ooMedias); $i++) {
            $ooMedia =& $ooMedias[ $i];
            $this->medias[] = new rexMedia( $params, $ooMedia); 
        }
        
        $this->cat =& $ooCat;
    }
    
    function format() {
        $s = '';
        
        // Berechtigung prüfen, ob medien selektiert werden dürfen        
        if ( !rexPoolPerm::hasGetPerm( $this->cat)) {
            return $s;
        }

        if ( $this->medias === null) {
            return $s;
        }
        
        // Kopf nur anzeigen, wenn auch Medien da sind
        $s .= $this->formatTableHead();
        
        foreach( $this->medias as $rexMedia) {
            $s .= $rexMedia->formatListed();
        }
        
        $s .= $this->formatTableEnd();
        
        return $s;
    }
}

/**
 * Media-Class
 * All Methods are static!
 */
class rexMedia extends rexPoolComponent {
    
    var $ooMedia;
    
    function rexMedia( &$params, &$ooMedia) {
        parent::rexPoolComponent( $params);
        $this->ooMedia =& $ooMedia;
    }
    
    function _formatPreview() {
        
        $params = array(
            'resize' => true,
            'width'  => '80px',
            'class'  => 'preview',
            'path'   => '../'
        );
        
        return $this->_link( $this->ooMedia->toImage( $params),
                             'action=media_details&cat_id='. $this->ooMedia->getCategoryId() .'&media_id='. $this->ooMedia->getId());
    }
    
    function _formatDetailedView() {
        $params = array(
            'resize' => true,
            'width'  => '250px',
            'class'  => 'detailed',
            'path'   => '../'
        );
        
        return $this->_link( $this->ooMedia->toImage( $params),
                             '../files/'. $this->ooMedia->getFileName(),
                             array( 'target' => '_blank'),
                             false);
    }
    
    function _formatDetails() {
        global $I18N;
        
        $media =& $this->ooMedia;
        
        $s = '';
        $date = '';
        $dateFormat = $this->_dateFormat();
        
        $updatedate = $media->getUpdateDate( $dateFormat);
        $createdate = $media->getCreateDate( $dateFormat);
        
        if ( $updatedate != $createdate) {
            $date .= $I18N->msg('pool_colhead_updated') .':<br/>' . $updatedate . '<br/>';
        }
        $date .= $I18N->msg('pool_colhead_created') .':<br/>' . $createdate;
        
        $s = $this->_link( $media->getTitle(), 'action=media_details&cat_id='. $this->ooMedia->getCategoryId() .'&media_id='. $media->getId())
             .'<br/><br/>'
             .$media->getFileName() .'<br/>'
             .$media->getFormattedSize().'<br/><br/>'
             .$date;
        
        return $s;
    }
    
    function _formatDescription() {
        return nl2br( $this->ooMedia->getDescription());
    }
    
    function _formatActions() {
        $ooMedia = $this->_getOOMedia();
        
        return $ooMedia->toInsertLink( $this->params->isEditorMode);
    }
    
    function _formatIcon() {
        return $this->ooMedia->toIcon();
    }
    
    function formatListed() {
        $media =& $this->ooMedia;
        
        $s = '
         <tr>
            <td>'. $this->_formatIcon() .'</td>
<!--            <td><input type="checkbox" name="media_id[]" value="'. $media->getId() .'"/></td> -->
            <td>'. $this->_formatPreview() .'</td>
            <td>'. $this->_formatDetails() .'</td>
            <td>'. $this->_formatDescription() .'</td>'. "\n";
        if ( $this->params->isEditorMode) {
            $s .=  '<td>'. $this->_formatActions() .'</td>'. "\n";
        }
                 
        $s .= '         </tr>'. "\n";
              
        return $s;
    }
    
    function formatDetailed() {
        global $I18N;
        
        $media =& $this->ooMedia;
        $isImage = $media->isImage();
        $dateFormat = rexPool::_dateFormat();
        $rowspan = 7;
        
        $catSelect = new rexMediaCatSelect();
        $catSelect->set_style( 'width:100%;');
        $catSelect->set_name( 'mediaCatId');
        $catSelect->set_selected( $media->getCategoryId());
        
        if ( $isImage) {
            // 2 Zeilen zusätzlich
            $rowspan += 2;
        }
        
        $s = '
              <colgroup>
                 <col width="150px"/>
                 <col width="150px"/>
                 <col width="*"/>
              </colgroup>

              <tr>
                 <th colspan="3">'. $I18N->msg('pool_headline_mediadetails') .'</th>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_title') .'</td>
                 <td><input class="inp100" type="text" name="mediaTitle" value="'. $media->getTitle() .'"/></td>
                 <td style="text-align: center" rowspan="'. $rowspan .'">'. $this->_formatDetailedView() .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_category') .'</td>
                 <td>'. $catSelect->out() .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_description') .'</td>
                 <td><input class="inp100" type="text" name="mediaDescription" value="'. $media->getDescription() .'"/></td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_copyright') .'</td>
                 <td><input class="inp100" type="text" name="mediaCopyright" value="'. $media->getCopyright() .'"/></td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_filename') .'</td>
                 <td>'. $media->getFileName() .'</td>
              </tr>'. "\n";
              
          if ( $isImage) {
                $s .= '
              <tr>
                 <td>'. $I18N->msg('pool_colhead_width') .'</td>
                 <td>'. $media->getWidth() .'px</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_height') .'</td>
                 <td>'. $media->getHeight() .'px</td>
              </tr>'. "\n";
          }

              $s .='
              <tr>
                 <td>'. $I18N->msg('pool_colhead_updated') .'</td>
                 <td>'. $media->getUpdateDate( $dateFormat) .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_created') .'</td>
                 <td>'. $media->getCreateDate( $dateFormat) .'</td>
              </tr>
              
              <tr>
                 <td>'. $I18N->msg('pool_colhead_newfile') .'</td>
                 <td colspan=2><input type="file"  name="mediaFile"  /></td>
              </tr>

              <tr>
                 <td colspan="2" style="text-align: right;">
                    <input type="submit" name="saveMediaButton" value="'. $I18N->msg('pool_media_apply') .'"/>
                 </td>
                 <td style="text-align: right;">
                    <input type="submit" name="deleteMediaButton" value="'. $I18N->msg('pool_media_delete') .'"/>
                 </td>
              </tr>
              '. "\n";
              
        return $s;
    }
    
    function formatForm() {
        global $I18N;
        
        $catSelect = new rexMediaCatSelect();
        $catSelect->set_style( 'width:100%;');
        $catSelect->set_name( 'cat_id');
        $catSelect->set_selected( $this->params->catId);
        
        $titleKey = $this->params->mode == 'archive' ?  'pool_headline_mediaarchiveupload' : 'pool_headline_mediaupload';
        
        $s = '
           <table class="rex" cellpadding="5" cellspacing="1">

              <colgroup>
                 <col width="150px"/>
                 <col width="*"/>
                 <col width="100px"/>
              </colgroup>

              <tr>
                 <th colspan="3">'. $I18N->msg( $titleKey) .'</th>
              </tr>'. "\n";
        
        //Evtl Fehlerausgabe      
        $s .= $this->_message();
                
        $s .= '
              <tr>
                 <td>'.$I18N->msg("pool_media_title").'</td>
                 <td colspan="2"><input type="text" name="mediaTitle" class="inp100"/></td>
              </tr>'. "\n";
        $s .= '
              <tr>
                 <td>'.$I18N->msg("pool_media_category").'</td>
                 <td colspan="2">'. $catSelect->out() .'</td>
              </tr>'. "\n";

        $s .= '
              <tr>
                 <td>'.$I18N->msg("pool_media_description").'</td>
                 <td colspan="2"><textarea class="inp100" name="mediaDescription"></textarea></td>
              </tr>'. "\n";

        $s .= '
              <tr>
                 <td>'.$I18N->msg("pool_media_copyright").'</td>
                 <td colspan="2"><input type="text" class="inp100" name="mediaCopyright"/></td>
              </tr>'. "\n";
              
        $s .= '
              <tr>
                 <td>'.$I18N->msg("pool_media_location").'</td>
                 <td><input type="file"  name="mediaFile"/></td>
                 <td><input type="submit" name="uploadMediaButton" value="'.$I18N->msg("pool_upload_button").'" class="inp100"/></td>
              </tr>' ."\n";
           
        $s .= '
           </table>'. "\n";
           
        return $s;
    }
    
    function &_getOOMedia() {
        return $this->ooMedia;
    }
}


/**
 * HTML-Selectbox which shows all categories
 */
class rexMediaCatSelect extends select {
    function rexMediaCatSelect( $cat = null) {
        $selectCats = null;
        if ( is_int( $cat)) {
            $selectCats = array( OOMediaCategory::getCategoryById( $cat));
        } else if ( OOMediaCategory::isValid( $cat)) {
            $selectCats = array( $cat);
        } else {
            $selectCats = OOMediaCategory::getRootCategories();
        }
        
        foreach ( $selectCats as $selectCat) {
            $this->add_cat_option( $selectCat);
        }
    }
    
    function add_cat_option( &$cat, $groupName = '') {
        if( empty( $cat)) {
            return;
        }
        
        $this->add_option($cat->getName(), $cat->getId(), $groupName);
        
        if ( $cat->hasChildren()) {
            $childs = $cat->getChildren();
      
            foreach ( $childs as $child) {
                $this->add_cat_option( $child, $cat->getName());
            }
        }
    }
}

/**
 * Not in use!
 * mediapool-v2-functions
 * @author vscope
 */

function media_resize($FILE,$width,$height,$make_copy=false){
    global $REX;
    
    if ($REX[IMAGEMAGICK])
    {
        $magick = $REX[IMAGEMAGICK_PATH];
        $sizer = '';
        if($width>0){
            $sizer = "-geometry ".$width;
        }else if($height>0){
            $sizer = "-geometry x".$height;
        }else if($width>0 && $height!=""){
            $sizer = "-geometry ".$width."x".$height."!";
        }
        $system = $magick." ".$FILE." ".$sizer." -colorspace rgb -density 72 ".$FILE;
        system($system);
    }else
    {
        return false;
    }
}
?>