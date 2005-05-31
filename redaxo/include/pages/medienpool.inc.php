<?

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

// Funktionen nur ausführen wenn der als Page aufgrufen wird.
// 
// Falls dieser nur als include in einem anderen Addon dient,
// => NICHTS anzeigen 
// => NUR Klassen zur Verfüung stellen 
if ( $_GET['page'] == rexPoolParam::page()) {
    
    // Evtl. Formular Posts verarbeiten
    rexPool::handlePosts();
    
    // Ausgabe des Seitenkopfes
    rexPool::_header();
    
    switch ( rexPoolParam::action()) {
        case 'cat_details'   : rexPool::catDetails();   break;
        case 'media_details' : rexPool::mediaDetails(); break;
        case 'media_upload'  : rexPool::mediaUpload(); break;
    //    case 'media_search'  : rexPool::mediaDetails(); break;
        default              : rexPool::mediaList();
    }
    
    // Ausgabe des Seitenfußes
    rexPool::_footer();
    
}

/**
 * Main-Class
 * All Methods are static!
 */
class rexPool {
    
    function rexPool() {
        die( 'class-instantiation not allowed for class "' .__CLASS__ .'"');
    }
    
    function mediaList() {
        global $I18N;
        
        // Liste der anzuzeigenden Kategorien
        $catId = rexPoolParam::catId();
        
        if( $catId !== '') {
            $currentCat = OOMediaCategory::getCategoryById( $catId);
            $catList = $currentCat->getChildren(); 
        } else {
            // test
            // $currentCat = OOMediaCategory::getCategoryById( 2);
            $currentCat = null;
            $catList = OOMediaCategory::getRootCategories();
        }
        
        // Pfad der aktuellen Kategorie anzeigen
        $path = 'Pfad: '. rexPool::_link( $I18N->msg('pool_default_cat'), '');
        if( $currentCat != null) {
            
            $pathList = explode( '|', $currentCat->getPath());
            
            // Pfad zur aktuellen Kategorie
            foreach ( $pathList as $pathCatId) {
                if( $pathCatId == '') {
                    continue;
                }
                
                $pathCat = OOMediaCategory::getCategoryById( $pathCatId);
                $path .= ' : '.rexPool::_link( $pathCat->getName(), 'cat_id='. $pathCat->getId());
            }
            
            // Aktuelle Kategorie
            $path .= ' : '. rexPool::_link( $currentCat->getName(), 'cat_id='. $currentCat->getId());
        }
        
        rexPool::_title( $path);
        
        echo rexMediaCategory::formatList( $catList);
            
        if ( $currentCat !== null) {
            echo rexMedia::formatList( $currentCat->getFiles());
        }
    }
    
    function mediaDetails() {
        global $I18N;
        
        rexPool::_title( $I18N->msg('pool_file_detail'));
        
        if ( !isset( $_GET['media_id'])) {
            rexParam::miss( 'media_id');
        }
        
        $mediaId = $_GET['media_id'];
        $media = OOMedia::getMediaById( $mediaId);
        
        echo '       <table class="rex" cellpadding="5" cellspacing="1">
             '. rexMedia::formatDetailed( $media) .'
                     </table>';
    }
    
    function handlePosts() {
        global $REX_USER;
        
        if ( !isset ( $_POST)) {
            return;
        }
        
        // Kategorie anlegen/speichern
        if ( isset( $_POST['saveCatButton']))
        {
            // Id der Kategorie in der sich die zu editieren de Kategorie befindet (ParentId)
            $catId = rexPoolParam::catId();
            // Id der zu editierenden Kategorie
            $catModId = rexPoolParam::catModId();
            
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
            
            $cat->_save();
            
            // Speicher freigeben
            unset( $cat);
        }
        // Kategorie löschen
        else if ( isset( $_POST['deleteCatButton'])) 
        {
            // Id der zu löschenden Kategorie
            $catModId = rexPoolParam::catModId();
            $cat = OOMediaCategory::getCategoryById( $catModId);
            
            $cat->_delete();
            
            // Speicher freigeben
            unset( $cat);
        }
    }
    
    function mediaUpload() {
        global $REX,$I18N;
        
        $message = '';
        $messageLevel = 0;
         
        rexPool::_uploadTitle();
        
        // handle file upload(s)
        if( !empty($_POST)) {
            
            include_once $REX['INCLUDE_PATH'].'/classes/class.archive.inc.php';
                        
            if( count( $_FILES) > 1) {
                $message = $I18N->msg( 'pool_archive_uploaded');
            } else {
                $message = $I18N->msg( 'pool_media_uploaded');
            }
            
            foreach( $_FILES as $file) {
                $isArchive = File_Archive::isKnownExtension( OOMedia::_getExtension( $file['name']));
                // Register only files - no archives
                $result = rexPool::_handleUpload( $file, !$isArchive);
                $isError = !is_array( $result);
                
                if( !$isError && $isArchive) {
                    // IMPORTANT: trailing slash!
                    $archivename = $REX['MEDIAFOLDER'] .'/'. $result['name'] .'/';
                    $tmp = $REX['MEDIAFOLDER']. '/tmp';
                    
                    
                    // Archiv temporär entpacken
                    File_Archive::extract( File_Archive::read( $archivename),
                                           File_Archive::toFiles( $tmp));
                                           
                    $handle = opendir( $tmp);
                    if ( $handle) {
                        while( $tmp_file = readdir( $handle)) {
                            
                            if ( $tmp_file == '.' || $tmp_file == '..') {
                                continue;
                            }
                            
                            $up_file = $tmp .'/'. $tmp_file;
                            $upload = array();
                            $upload['name'] = $tmp_file;
                            $upload['tmp_name'] = $up_file;
                            $upload['size'] = filesize( $up_file);
                            $upload['type'] = '';
                            
                            if( function_exists( 'mime_content_type')) {
                                $upload['type'] = mime_content_type( $up_file);
                            } else if ( class_exists( 'MIME_Type') && extension_loaded( 'mime_magic')) {
                                $upload['type'] = MIME_Type::autoDetect( $up_file);
                            }
                            
                            $result = rexPool::_handleUpload( $upload);
                            @unlink( $up_file);
                        }
                        
                        closedir( $handle);
                        @rmdir( $tmp);
                    }
                }
                
                if ( $isError) {
                    $message = $result;
                    $messageLevel = 1;
                    break;
                }
            }
        }
        
        echo rexMedia::formatForm( $message);
    }
    
    function _title( $title = '') {
        global $I18N;
        title($I18N->msg('pool_name'), '&nbsp;&nbsp;&nbsp;'.$title, 'grey', '100%');
    }
    
    function _uploadTitle( $modes = array( 'file' => 'pool_upload_file')) {
        global $I18N;
        
        $subtitle = '';
        $actMode = rexPoolParam::mode( 'file');
        
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
        
        rexPool::_title( $subtitle);        
    }
    
    function _dateFormat() {
        return 'd-M-Y | H:i';
    }
    
    function _link( $label, $params = '', $additional = array()) {
        $add = '';
        if ( is_array( $additional)) {
            foreach( $additional as $addName => $addValue) 
            {
                $add .= ' '. $addName . '="'. $addValue . '"';
            }
        }
        
        if ( $params != '') { 
            if ( $params[0] != '&') {
                $params = '&' . $params;
            }
            $params = htmlentities( $params);
        }
        return '<a href="?page='. rexPoolParam::page() . $params .'"'. $add .'>'. $label .'</a>';
    }
    
    function _imageSrc( $media) {
        global $REX;
        if ( empty( $REX['ABS_REX_ROOT'])) {
            $REX['ABS_REX_ROOT'] = str_replace( "/redaxo/index.php", "",$_SERVER['SCRIPT_NAME']); 
        }
        $src = $REX['ABS_REX_ROOT'] . $REX['WWW_PATH'] .'/files/'. $filename;
        
        return $src;
    }
    
    function &_handleUpload( &$file, $register = true) {
        global $REX, $REX_USER, $I18N;
        
        $newFilename = basename( strtolower( str_replace( ' ', '_', $file['name'])));
        
        $result = array();
        $result['title']       = isset( $_POST['mediaTitle']) ? $_POST['mediaTitle'] : '';
        $result['description'] = isset( $_POST['mediaDescription']) ? $_POST['mediaDescription'] : '';
        $result['copyright']   = isset( $_POST['mediaCopyright']) ? $_POST['mediaCopyright']: '';
        $result['cat_id']      = isset( $_POST['mediaCatId']) ? $_POST['mediaCatId'] : '';
        
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
            
        if (strrpos($newFilename,'.') != '')
        {
            $fname = substr( $newFilename, 0, strrpos( $newFilename, '.'));
            $extension  = OOMedia::_getExtension( $newFilename);
            
            $illegals = array( 'php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'asp', 'aspx', 'cfm', 'sh');
            if ( in_array( $extension, $illegals))
            {
                $extension .= ".txt";
            }

            $result['name'] = rexPool::_genFileName( $fname .'.'. $extension);
            $absFile = $REX['MEDIAFOLDER'] . '/'. $result['name'];
            
            if ( move_uploaded_file( $file['tmp_name'], $absFile) || 
                 copy( $file['tmp_name'], $absFile))
            {
                if ( $REX['MEDIAFOLDERPERM'] == '') {
                     $REX['MEDIAFOLDERPERM'] = '0777';
                }
                
                chmod( $absFile, $REX['MEDIAFOLDERPERM']);
            } 
            else
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
        else
        {
            $result['error'] .= $I18N->msg('pool_error_miss_file_ext', $result['orgname']);
        }
        
//        var_dump( $absFile);
//        var_dump( $newFilename);
        
//        var_dump( $result);
        
        // Exit on error
        if( $result['error'] != '') {
            return $result['error'];
        }
        
        // Files which shouldn`t be registered stop here
        if( !$register) {
            return $result;
        }
        
        // Create database entry
        $media = new OOMedia();
        unset( $result['error']);
        
        // Assign attributes
        foreach ( $result as $detail => $value) {
            $detail = '_'. $detail;
            $media->$detail = $value;
        }
        
        $media->_insert();
        
        return $result;
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
    
    function _header() {
        global $I18N, $REX;
        // TODO HIER NOCH FÜLLEN
        $opener_input_field = 'IRGENDWAS';
    ?>
    <html>
       <head>
          <title><?php echo $REX['SERVERNAME'] .' - '. $I18N->msg('pool_name'); ?></title>
          <link rel=stylesheet type=text/css href=css/style.css>
          <script language=Javascript>
          <!--
          var redaxo = true;
          
          function selectMedia(filename)
          {
             opener.document.REX_FORM.<?php echo $opener_input_field ?>.value = filename;
             self.close();
          }
          
          function openImage(image){
             window.open('index.php?page=medienpool&amp;popimage='+image,'popview','width=123,height=111');
          }
          
          function insertHTMLArea(html){
             window.opener.tinyMCE.execCommand('mceInsertContent', false, html);
             self.close();
          }
    
          function fileListFunc(func)  {
             document.rex_file_list.media_method.value=func;
             document.rex_file_list.submit();
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
             <th colspan="3"><?php echo $I18N->msg('pool_media') .' '. $REX['SERVERNAME']; ?></th>
          </tr>
    
          <tr>
             <td>
               <?php echo rexPool::_link( $I18N->msg('pool_file_list'), '', array( 'class' => 'white')) ?> |
               <?php echo rexPool::_link( $I18N->msg('pool_file_upload'), 'action=media_upload&mode=file', array( 'class' => 'white')) ?> |
               <?php echo rexPool::_link( $I18N->msg('pool_file_search'), 'action=media_search', array( 'class' => 'white')) ?>
             </td>
          </tr>
          
       </table>
       
       <form name="poolForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" style="display: inline;" enctype="multipart/form-data">
       
          <input type="hidden" name="page" value="medienpool"/>
          <input type="hidden" name="action" value="<?php echo rexPoolParam::action() ?>"/>
          <input type="hidden" name="mode" value="<?php echo rexPoolParam::mode() ?>"/>
          <input type="hidden" name="cat_id" value="<?php echo rexPoolParam::catId() ?>"/>
          <input type="hidden" name="cat_modid" value="<?php echo rexPoolParam::catModId() ?>"/>
          <input type="hidden" name="opener_input_field" value="<?php echo rexPoolParam::editorName() ?>"/>
          
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
class rexPoolParam {
    function rexPoolParam() {
        die( 'class-instantiation not allowed for class "' .__CLASS__ .'"');
    }
    
    function page() {
        return 'medienpool';
    }
    
    function catId( $default = '') {
        return !empty( $_REQUEST['cat_id']) ? (int) $_REQUEST['cat_id'] : $default;
    }
    
    function catModId( $default = '') {
        return !empty( $_REQUEST['cat_modid']) ? (int) $_REQUEST['cat_modid'] : $default;
    }
    
    function mediaId( $default = '') {
        return !empty( $_REQUEST['media_id']) ? (int) $_REQUEST['media_id'] : $default;
    }
    
    function action( $default = '') {
        return !empty( $_REQUEST['action']) ? $_REQUEST['action'] : $default;
    }
    
    function mode( $default = '') {
        return !empty( $_REQUEST['mode']) ? $_REQUEST['mode'] : $default;
    }
    
    function miss( $paramName) {
        global $I18N;
        exit( '<p>'. $I18N->msg('pool_error_miss_param', $paramName) .'</p>');
    }
    
    function editorName( $default = '') {
        return !empty( $_REQUEST['opener_input_field']) ? $_REQUEST['opener_input_field'] : $default;
    }
    
    function isEditorMode() {
        return rexPoolParam::editorName() != '';
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
        
        if( rexPoolPerm::isAdmin()) return true;
        
        if( rexPoolPerm::isPoolAdmin()) return true;
        
        if( rexPoolPerm::isOwner( $cat)) return true;
        
        if( rexPoolPerm::hasCatPerm( $catId)) return true;
        
        return rexPoolPerm::hasPerm( 'media'. $sub .'[all]') ||
               rexPoolPerm::hasPerm( 'media'. $sub .'['. $catId .']');
    }
    
    function isOwner( &$cat) {
        global $REX_USER;
        return $REX_USER->isValueOf( 'user_id', $cat->getCreateUser());
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
 
/**
 * Category-Class
 * All Methods are static!
 */
class rexMediaCategory {
    
    function rexMediaCategory() {
        die( 'class-instantiation not allowed for class "' .__CLASS__ .'"');
    }
    
    function _formatColGroup() {
        static $formatCategoryColGroup;
        
        if( !isset( $formatCategoryColGroup)) {
        $formatCategoryColGroup = ' 
              <colgroup>
                 <col width="30px"/>
                 <col width="30px"/>
                 <col width="*"/>
                 <col width="190px"/>
                 <col width="190px"/>
              </colgroup>'. "\n";
        }
        
        return $formatCategoryColGroup;
    }
    
    function _formatAddLink() {
        global $I18N;
        $s = '';
        $cat = null;
        $catId = rexPoolParam::catId();
        
        if ( $catId !== '') {
            $cat = OOMediaCategory::getCategoryById( $catId);
        }
        
        if ( $cat === null || rexPoolPerm::hasAddPerm( $cat)) {
            $s .=  rexPool::_link( '<img src="pics/folder_plus.gif" style="width: 16px; height:16px" alt="'. $I18N->msg('pool_add_category') .'">' , 'action=cat_add&cat_id='. $catId);
        }
        
        return $s;
    }
    
    function _formatHeader() {
        global $I18N;
        $formatCategoryHeader = ' 
              <tr>
                 <th>'. rexMediaCategory::_formatAddLink() .'</th>
                 <th><input type="checkbox" onchange="checkBoxes( \'poolForm\', \'cat_id[]\', this.checked)"/></th>
                 <th>'. $I18N->msg('pool_colhead_category') .'</th>
                 <th>'. $I18N->msg('pool_colhead_details') .'</th>
                 <th>'. $I18N->msg('pool_colhead_edit') .'</th>
              </tr>'. "\n";
        
        return $formatCategoryHeader;
    }
    
    // Call by reference to improve performance
    function _formatParent( &$catList) {
        if ( !is_array( $catList)) {
            return '';
        }
        
        if ( OOMediaCategory::isValid( $catList[0])) {
            $cat =& $catList[0];
            if ( !$cat->hasParent()) {
                return '';
            }
            $catId = $cat->getParentId();
        } else {
            $catId = '';
        }
        
        $formatCategoryParent = ' 
              <tr>
                 <td></td>
                 <td colspan="4">'. rexPool::_link( '..', 'cat_id='. $catId) .'</td>
              </tr>'. "\n";
              
        return $formatCategoryParent;
    }
    
    // Call by reference to improve performance
    function format( &$cat) {
        $s = '
              <tr>
                 <td><img src="pics/folder.gif" style="width: 16px; height:16px; margin: auto;"></td>
                 <td><input type="checkbox" name="cat_id[]" value="'. $cat->getId() .'"/></td>
                 <td>'. rexMediaCategory::_formatName( $cat) .'</td>
                 <td>'. rexMediaCategory::_formatDetails( $cat) .'</td>
                 <td>'. rexMediaCategory::_formatActions( $cat) .'</td>
              </tr>'. "\n";
              
        return $s;
    }
    
    function _formatName( &$cat) {
        global $REX_USER;
        
        $name = rexPool::_link( $cat->getName(), 'cat_id='. $cat->getId());
        
        if ( rexPoolPerm::isAdvanced()) {
            $name .= ' ['. $cat->getId() .']';
        } 
        
        return $name;        
    }
    
    // Call by reference to improve performance
    function _formatActions( &$cat) {
        global $I18N;
        $catId = $cat->getId();
        
        if ( !rexPoolPerm::hasDelPerm( $cat) && !rexPoolPerm::hasEditPerm( $cat)) {
            return '';
        }
        
        
        return rexPool::_link( $I18N->msg('pool_cat_action'), 'cat_id='. rexPoolParam::catId() .'&cat_modid='. $catId);;
    }
    
    // Call by reference to improve performance
    function _formatDetails( &$cat) {
        global $I18N;
        
        $s = $I18N->msg('pool_subcats').': '. $cat->countChildren() . '<br/>'.
             $I18N->msg('pool_files').': '. $cat->countFiles();
        
        return $s;
    }
    
    // Call by reference to improve performance
    function formatList( &$catList) {
        if ( !is_array( $catList)) {
            return '';
        }
        
        $s = "\n".
             '       <table class="rex" cellpadding="5" cellspacing="1">
             '. rexMediaCategory::_formatColGroup()
              . rexMediaCategory::_formatHeader();
              
        if ( rexPoolParam::catId() !== '') {
            $s .= rexMediaCategory::_formatParent( $catList);;
        }
              
        $action = rexPoolParam::action();
        
        // Eingabeformular für neu anlegen von Kategorien 
        if ( $action == 'cat_add') {
            $s .= rexMediaCategory::formatForm();
        }
        
        $catModId = rexPoolParam::catModId();
        
        foreach( $catList as $cat) {
            if ( !rexPoolPerm::hasCatPerm( $cat)) {
                continue;
            }
            
            if( empty( $_POST) && $cat->getId() == $catModId) {
                $s .= rexMediaCategory::formatForm( $catModId);
            } else {
                $s .= rexMediaCategory::format( $cat);
            }
        }
        
        $s .= "\n".
              '       </table>'.
              "\n";
        
        return $s;
    }
    
    function formatForm( $catId = '') {
        $cat = null;
        $catName = '';
        
        // ggf. defaultwerte für Kategorie laden
        if ( $catId !== '' ) {
            $cat = OOMediaCategory::getCategoryById( $catId);
            $catName = $cat->getName();
        }
        
        $s = '
              <tr>
                 <td><img src="pics/folder.gif" style="width: 16px; height:16px; margin: auto;"></td>
                 <td><input type="checkbox" name="cat_id[]" value="'. $catId .'"/></td>
                 <td><input type="text" name="catName" value="'. $catName .'" style="width: 100%"/></td>
                 <td colspan="2">'. rexMediaCategory::_formatFormButtons( $cat) .'</td>
              </tr>'. "\n";
              
        return $s;
    }
    
    function _formatFormButtons( $cat) {
        global $I18N;
        
        $s = '';
        
        if ( $cat === null || rexPoolPerm::hasEditPerm( $cat)) {
            $s .= '<input type="submit" name="saveCatButton" value="'. $I18N->msg( 'save_category') .'"/>';
        }
        
        if ( $cat === null || rexPoolPerm::hasDelPerm( $cat)) {
            $s .= '<input type="submit" name="deleteCatButton" value="'. $I18N->msg( 'delete_category') .'"/>';
        } 
        
        return $s;
    }
}

/**
 * Media-Class
 * All Methods are static!
 */
class rexMedia {
    
    function rexMedia() {
        die( 'class-instantiation not allowed for class "' .__CLASS__ .'"');
    }
    
    function _formatColGroup() {
        static $formatMediaColGroup;
        
        if ( !isset( $formatMediaColGroup)) {
            $formatMediaColGroup = ' 
              <colgroup>
                 <col width="50px"/>
                 <col width="30px"/>
                 <col width="90px"/>
                 <col width="130px"/>
                 <col width="*"/>';
                 
            if ( rexPoolParam::isEditorMode()) {
                $formatMediaColGroup .= '<col width="80px"/>'. "\n";
            }
            
            $formatMediaColGroup .= '</colgroup>'. "\n";
        }
        
        return $formatMediaColGroup;
    }
    
    function _formatHeader() {
        global $I18N;
        static $formatMediaHeader;
        
        if ( !isset( $formatMediaHeader)) {
            $formatMediaHeader = ' 
              <tr>
                 <th>Typ</th>
                 <th><input type="checkbox" onchange="checkBoxes( \'poolForm\', \'media_id[]\', this.checked)"/></th>
                 <th>'. $I18N->msg('pool_colhead_preview') .'</th>
                 <th>'. $I18N->msg('pool_colhead_filedetails') .'</th>
                 <th>'. $I18N->msg('pool_colhead_description') .'</th>';
            if ( rexPoolParam::isEditorMode()) {
                $formatMediaHeader .= '<th>'. $I18N->msg('pool_colhead_functions') .'</th>';
            }
                 
            $formatMediaHeader .= '</tr>'. "\n";
        }
                  
        return $formatMediaHeader;
    }
    
    // Call by reference to improve performance
    function _formatPreview( &$media) {
        
        $params = array(
            'resize' => true,
            'width'  => '80px',
            'class'  => 'preview',
            'path'   => '../'
        );
        
        return rexPool::_link( $media->toImage( $params),
                              'action=media_details&media_id='. $media->getId());
    }
    
    // Call by reference to improve performance
    function _formatDetails( &$media) {
        global $I18N;
        
        $s = '';
        $date = '';
        $dateFormat = rexPool::_dateFormat();
        
        $updatedate = $media->getUpdateDate( $dateFormat);
        $createdate = $media->getCreateDate( $dateFormat);
        
        if ( $updatedate != $createdate) {
            $date .= $I18N->msg('pool_colhead_updated') .':<br/>' . $updatedate . '<br/>';
        }
        $date .= $I18N->msg('pool_colhead_created') .':<br/>' . $createdate;
        
        $s = rexPool::_link( $media->getTitle(), 'action=media_details&media_id='. $media->getId())
             .'<br/><br/>'
             .$media->getFileName() .'<br/>'
             .$media->getFormattedSize().'<br/><br/>'
             .$date;
        
        return $s;
    }
    
    // Call by reference to improve performance
    function _formatDescription( &$media) {
        return nl2br( $media->getDescription());
    }
    
    // Call by reference to improve performance
    function _formatActions( &$media) {
        return '';
    }
    
    // Call by reference to improve performance
    function _formatIcon( &$media) {
        return $media->toIcon();
    }
    
    // Call by reference to improve performance
    function formatListed( &$media) {
        
        $s = '
              <tr>
                 <td>'. rexMedia::_formatIcon( $media) .'</td>
                 <td><input type="checkbox" name="media_id[]" value="'. $media->getId() .'"/></td>
                 <td>'. rexMedia::_formatPreview( $media) .'</td>
                 <td>'. rexMedia::_formatDetails( $media) .'</td>
                 <td>'. rexMedia::_formatDescription( $media) .'</td>';
        if ( rexPoolParam::isEditorMode()) {
            $s .=  '<td>'. rexMedia::_formatActions( $media) .'</td>';
        }
                 
        $s .= '</tr>'. "\n";
              
        return $s;
    }
    
    // Call by reference to improve performance
    function formatDetailed( &$media) {
        global $I18N;
        
        $isImage = $media->isImage();
        $dateFormat = rexPool::_dateFormat();
        $rowspan = 7;
        
        if ( $isImage) {
            // 2 Zeilen zusätzlich
            $rowspan += 2;
        }
        
        $s = '
              <tr>
                 <th colspan="3">'. $I18N->msg('pool_headline_mediadetails') .'</th>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_title') .'</td>
                 <td>'. $media->getTitle() .'</td>
                 <td style="text-align: center" rowspan="'. $rowspan .'">'. $media->toImage( array( 'path' => '../')) .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_category') .'</td>
                 <td>'. $media->getCategoryName() .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_description') .'</td>
                 <td>'. $media->getDescription() .'</td>
              </tr>


              <tr>
                 <td>'. $I18N->msg('pool_colhead_copyright') .'</td>
                 <td>'. $media->getCopyright() .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_filename') .'</td>
                 <td>'. $media->getFileName() .'</td>
              </tr>'. "\n";
              
          if ( $isImage) {
                $s .= '
              <tr>
                 <td>'. $I18N->msg('pool_colhead_width') .'</td>
                 <td>'. $media->getWidth() .'</td>
              </tr>

              <tr>
                 <td>'. $I18N->msg('pool_colhead_height') .'</td>
                 <td>'. $media->getHeight() .'</td>
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
              '. "\n";
              
        return $s;
    }
    
    function formatList( $mediaList) {
        if ( !is_array( $mediaList)) {
            return '';
        }
        
        $cat = null;
        $catId = rexPoolParam::catId();
        if ( $catId !== '') { 
            $cat = OOMediaCategory::getCategoryById( $catId);
        }
        
        $s = "\n".
             '       <table class="rex" cellpadding="5" cellspacing="1">
             '. rexMedia::_formatColGroup()
              . rexMedia::_formatHeader();
        
        foreach( $mediaList as $media) {
            if ( $cat === null || !rexPoolPerm::hasGetPerm( $cat)) {
                continue;
            }
            
            $s .= rexMedia::formatListed( $media);
        }
        
        $s .= "\n".
              '       </table>'.
              "\n";
        
        return $s;
    }
    
    function formatForm( $message = '', $messageLevel = 0) {
        global $I18N;
        
        $catSelect = new rexMediaCatSelect();
        $catSelect->set_style( 'width:100%;');
        $catSelect->set_name( 'mediaCatId');
        
        $titleKey = rexPoolParam::mode() == 'archive' ?  'pool_headline_mediaarchiveupload' : 'pool_headline_mediaupload';
        
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
              
        if ( $message != '') {
            // Fehler
            if ( $messageLevel > 0) {
          $s .= '
              <tr class="warning">
                 <td>'. $I18N->msg( 'pool_error') .'</td>
                 <td colspan="2">
                    '. $message .'
                 </td>
              </tr>'. "\n";
            } else {
            // Statusmeldung
          $s .= '
              <tr class="status">
                 <td>'. $I18N->msg( 'pool_status') .'</td>
                 <td colspan="2">
                    '. $message .'
                 </td>
              </tr>'. "\n";
            }
        }
        
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