<?php

################################################################
# LinkMap for Redaxo 1.0
# vscope new media
################################################################

echo "<html><head><title>".$REX[SERVERNAME]." - LinkMap</title>
<link rel=stylesheet type=text/css href=css/style.css>
";

?>

<script>

var deltaY = 20
var x0 = 20
var y0 = 60
var defaultTarget = 'examplemain'
var onlyOneOpenFolderProPanel=true
var rememberKnotStatus=true
var resetPanelOnBookmarkClick=false
var clickOnFolderName=false
var clickOnFolderIcon=false
var showFileIcon=0
var iconDir='pics/linkmap_icons/'

window.onError=null

var idx=0
var treeId = new Array()
var treeP_id = new Array()
var treeIsOn = new Array()
var treeTyp = new Array()
var treeName = new Array()
var treeUrl = new Array()
var treeWasOn = new Array()
var treeDeep = new Array()
var treeLastY = new Array()
var treeIsShown = new Array()
var treeTarget = new Array()

function Note( id,p_id,name,url ) {
  treeId[ idx ] = id
  treeP_id[ idx ] = p_id
  treeIsOn[ idx ] = false
  treeTyp[ idx ] = 'f'
  treeName[ idx ] = name
  treeUrl[ idx ] = url
  treeWasOn[ idx ] = false
  treeDeep[ idx ] = 0
  treeLastY[ idx ] = 0
  treeIsShown[ idx ] = false
  treeTarget[ idx ] = (Note.arguments.length>4?Note.arguments[4]:defaultTarget)
  idx++
}

function initDiv ( )
{
  if ( isDOM || isDomIE )
  {
    divPrefix='<div class="sitemap" style="position:absolute; left:'+x0+'px; top:0; visibility:hidden;" id="sitemap'
    divInfo='<div class="sitemap" style="position:absolute; visibility:visible; left:'+x0+'px; top:0;" id="sitemap'
  }
  else
  {
    divPrefix='<div class="sitemap" id="sitemap'
    divInfo='<div class="sitemap" id="sitemap'
  }
  document.writeln( divInfo +  'info">Bitte haben Sie etwas Geduld.<br>&nbsp;<br>Es werden die Eintr&auml;ge aus<br>&nbsp;<br>der Datenbank initialisiert.</div> ' )


  onFocusTxt=' onFocus="if(this.blur) this.blur()"'

  for ( var i=1; i<idx; i++ )
  {
    // linked Name ?
    if ( treeUrl[i] != '' )
      linkedName = '<a id="note_' + treeId[i] + '" class="notef' + (treeTyp[i]=='f'?1:0) + 'l1" href="javascript:void(0);" onClick="sitemapClick(' + treeId[i] + ',1);insertLink(\''+treeUrl[i]+'\',\''+treeName[i]+'\')"' + onFocusTxt + '><img src="' + iconDir + '1w.gif" border="0" width="3">' + treeName[i] + '</a>';
    else
      if (treeTyp[i]=='f' && clickOnFolderName)
        linkedName =  '<a class="notef1l0" href="javascript:sitemapClick(' + treeId[i] + ',1)"' + onFocusTxt + '><img src="' + iconDir + '1w.gif" border="0" width="3">' + treeName[i] + '</a>'
      else
        linkedName =  '<img src="' + iconDir + '1w.gif" border="0" width="3">' + '<font class="notef' + (treeTyp[i]=='f'?1:0) + 'l0">' + treeName[i] + '</font>'

    // folder or bookmark
    if ( treeTyp[i] == 'b' )
    {
      folderImg = ''
      if (showFileIcon==0)
        folderImg = '<img align="bottom" src="' + iconDir + '1w.gif" border="0" height="16" width="1" hspace="0">'
      if (showFileIcon==1 || (showFileIcon==2 && !treeDeep[ i ]))
        folderImg = '<img align="bottom" src="' + iconDir + '1w.gif" border="0" height="16" width="14" hspace="0"><img align="bottom" src="' + iconDir + 'file_icon.gif" border="0" height="16" width="16" hspace="0">'
      if (showFileIcon==2 && treeDeep[ i ])
        folderImg = '<img align="bottom" src="' + iconDir + 'file_icon.gif" border="0" height="16" width="16" hspace="0">'
    }
    else
      if (clickOnFolderIcon && treeUrl[i] != '' )
        folderImg = '<a href="' + treeUrl[i] + '" target="' + treeTarget[i] + '" onClick="sitemapClick(' + treeId[i] + ')" onFocus="if(this.blur) this.blur()"><img align="bottom" src="' + iconDir +'folder_off.gif" border="0" name="folder' + treeId[i] + '" height="16" width="30" hspace="0"></a>'
      else
        folderImg = '<a href="javascript:sitemapClick(' + treeId[i] + ')" onFocus="if(this.blur) this.blur()"><img align="bottom" src="' + iconDir +'folder_off.gif" border="0" name="folder' + treeId[i] + '" height="16" width="30" hspace="0"></a>'

    // which type of file icon should be displayed?
    if ( treeP_id[i] != 0 )
      if ( lastEntryInFolder( treeId[i] ) )
        fileImg = '<img align="bottom" src="' + iconDir + 'file_last.gif" border="0" name="file' + treeId[i] + '" height="16" width="30" hspace="0">'
      else
        fileImg = '<img align="bottom" src="' + iconDir + 'file.gif" border="0" name="file' + treeId[i] + '" height="16" width="30" hspace="0">'
    else
      fileImg = ''

    // travel parents up to root and show vertical lines if parent is not the last entry on this panel
    verticales = ''
    for( var act_id=treeId[i] ; treeDeep[ id2treeIndex[ act_id ] ] > 1;  )
    {
      act_id = treeP_id[ id2treeIndex[ act_id ]]
      if ( lastEntryInFolder( act_id ) )
        verticales = '<img align="bottom" src="' + iconDir + 'file_empty.gif" border="0" height="16" width="30" hspace="0">' + verticales
      else
        verticales = '<img align="bottom" src="' + iconDir + 'file_vert.gif" border="0" height="16" width="30" hspace="0">' + verticales
    }

    document.writeln( divPrefix + treeId[i] + '"><table border="0" cellspacing="0" cellpadding="0"><tr><td nowrap>' + verticales + fileImg + folderImg + "</td><td nowrap>" + linkedName + '</td></tr></table></div><br>'
    )
  }
}

function initStyles ( )
{
  document.writeln( '<style type="text/css">' + "\n" + '<!--' )
  for ( var i=1,y=y0; i<idx; i++ )
  {
    document.writeln( '#sitemap' + treeId[i] + ' {POSITION: absolute; VISIBILITY: hidden; LEFT: '+x0+'px; TOP: '+y0+'px}' )
    if ( treeIsOn[ id2treeIndex[ treeP_id[i] ] ] )
      y += deltaY
  }
  document.writeln( '#sitemapinfo {POSITION: absolute; VISIBILITY: visible; LEFT: '+x0+'px; TOP: '+y0+'px}' )
  document.writeln( '//-->' + "\n" + '</style>' )
}


function marActiveKnote(id) {
  if ( isDOM ) {
    if ( lastActiveId && lastActiveId != id  )
      document.getElementById( 'note_' + lastActiveId ).style.fontWeight="normal"
    document.getElementById( 'note_' + id ).style.fontWeight="bold"
  } else if ( isDomIE ) {
    if ( lastActiveId && lastActiveId != id  )
      document.all[ 'note_' + lastActiveId ].style.fontWeight="normal"
    document.all[ 'note_' + id ].style.fontWeight="bold"
  }
  lastActiveId = id
}


function sitemapClick( id )
{
  var from = 0
  var i = id2treeIndex[ id ]

  if (sitemapClick.arguments.length==2)
    from = sitemapClick.arguments[1]

  if (treeUrl[i]!='' && (from==1 || from==0 && clickOnFolderIcon ) )
    marActiveKnote( id )

  if ( resetPanelOnBookmarkClick && treeTyp[i]=='b' ) {
    closeFolderOnPanel( id, false )
    return
  }

  if ( resetPanelOnBookmarkClick && treeTyp[i]=='f' && ( from==1 && clickOnFolderName && treeUrl[i]!='' || from==0 && clickOnFolderIcon && treeUrl[i]!='' ) ) {
    closeFolderOnPanel( id, true )
  }

  if ( treeTyp[i]=='f' && from==1 && !clickOnFolderName )
    return

  if ( treeTyp[i]=='b')
    return

  if ( treeIsOn[ i ] )
  // close directory
  {
    // mark node as invisible
    treeIsOn[ i ]=false
    // mark all sons as invisible
    actDeep = treeDeep[ i ]
    for( var j=i+1; j<idx && treeDeep[j] > actDeep; j++ )
    {
      if (rememberKnotStatus)
        treeWasOn[ j ] = treeIsOn[ j ]
      else
      {
        if (treeIsOn[j] )
          gif_off( treeId[j] )
        treeWasOn[ j ] = false
      }
      treeIsOn[ j ]=false
    }
    gif_off( id )
  }
  else
  // open directory
  {
    treeIsOn[ i ]=true
    // remember and restore old status
    actDeep = treeDeep[ i ]
    for( var j=i+1; j<idx && treeDeep[j] > actDeep; j++ )
    {
      treeIsOn[ j ] = treeWasOn[ j ]
    }
    gif_on( id )
    if ( onlyOneOpenFolderProPanel )
      closeFolderOnPanel( id, true )
  }
  showTree()
}

function knotDeep( id )
{
  var deep=0
  while ( true )
    if ( treeP_id[ id2treeIndex[id] ] == 0 )
      return deep
    else
    {
      ++deep
      id = treeP_id[ id2treeIndex[id] ]
    }
  return deep
}

function lastEntryInFolder( id )
{
  var i = id2treeIndex[id]
  if ( i == idx-1 )
    return true
  if ( treeTyp[i] == 'b' )
  {
    if ( treeP_id[i+1] != treeP_id[i] )
      return true
    else
      return false
  }
  else
  {
    var actDeep = treeDeep[i]
    for( var j=i+1; j<idx && treeDeep[j] > actDeep ; j++ )
    ;
    if ( j<idx && treeDeep[j] == actDeep )
      return false
    else
      return true
  }
}

function closeFolderOnPanel( id , skip_id )
{
  var i = id2treeIndex[id]
  var p_id = treeP_id[ i ]
  var deep=treeDeep[ i ]

  for( var j=(skip_id?i-1:i); j>0 && treeDeep[j]>=deep; j-- )
    if ( treeP_id[ j ] == p_id && treeIsOn[ j ] )
      sitemapClick( treeId[j], 2 )
  for( var j=i+1; j<idx && treeDeep[j]>=deep; j++ )
    if ( treeP_id[ j ] == p_id && treeIsOn[ j ] )
      sitemapClick( treeId[j], 2 )
}

function showTree()
{
  for( var i=1, y=y0, x=x0; i<idx; i++ )
  {
    if ( treeIsOn[ id2treeIndex[ treeP_id[i] ] ] )
    {
      // show current node
      if ( !(y == treeLastY[i] && treeIsShown[i] ) )
      {
        showLayer( "sitemap"+ treeId[i] )
        setyLayer( "sitemap"+ treeId[i], y )
        treeIsShown[i] = true
      }
      treeLastY[i] = y
      y += deltaY
    }
    else
    {
      // hide current node and all sons
      if ( treeIsShown[ i ] )
      {
        hideLayer( "sitemap"+ treeId[i] )
        treeIsShown[i] = false
      }
    }
  }
}

function initIndex() {
  for( var i=0; i<idx; i++ )
    id2treeIndex[ treeId[i] ] = i
}

function gif_name (name, width, height) {
  this.on = new Image (width, height)
  this.on.src = iconDir + name + "_on.gif"
  this.off = new Image (width, height)
  this.off.src = iconDir + name + "_off.gif"
}

function load_gif (name, width, height) {
  gif_name [name] = new gif_name (name,width,height)
}

function load_all () {
  load_gif ('folder',30,16)
  file_last = new Image( 30,16 )
  file_last.src = iconDir + "file_last.gif"
  file_middle = new Image( 30,16 )
  file_middle.src = iconDir + "file.gif"
  file_vert = new Image( 30,16 )
  file_vert.src = iconDir + "file_vert.gif"
  file_empty = new Image( 30,16 )
  file_empty = iconDir + "file_empty.gif"
}

function gif_on ( id ) {
  eval("document['folder" + id + "'].src = gif_name['folder'].on.src")
}

function gif_off ( id ) {
  eval("document['folder" + id + "'].src = gif_name['folder'].off.src")
}

var browserName = navigator.appName
var browserVersion = parseInt(navigator.appVersion)
var isIE = false
var isNN = false
var isDOM = false
var isDomIE = false
var isDomNN = false
var layerok = false

var isIE = browserName.indexOf("Microsoft Internet Explorer" )==-1?false:true
var isNN = browserName.indexOf("Netscape")==-1?false:true
var isOpera = browserName.indexOf("Opera")==-1?false:true
var isDOM = document.getElementById?true:false
var isDomNN = document.layers?true:false
var isDomIE = document.all?true:false

if ( isNN && browserVersion>=4 ) layerok=true
if ( isIE && browserVersion>=4 ) layerok=true
if ( isOpera && browserVersion>=5 ) layerok=true

var lastActiveId=0


function hideLayer(layerName) {
  if (isDOM)
    document.getElementById(layerName).style.visibility="hidden"
  else if (isDomIE)
    document.all[layerName].style.visibility="hidden"
  else if (isDomNN)
    document.layers[layerName].visibility="hidden"
}

function showLayer(layerName) {
  if (isDOM)
    document.getElementById(layerName).style.visibility="visible"
  else if (isDomIE)
    document.all[layerName].style.visibility="visible"
  else if (isDomNN)
    document.layers[layerName].visibility="visible"
}

function setyLayer(layerName, y) {
  if (isDOM)
    document.getElementById(layerName).style.top=y
  else if (isDomIE)
    document.all[layerName].style.top=y
  else if (isDomNN)
    document.layers[layerName].top=y
}

var id2treeIndex = new Array()


function initArray()
{

<?php

	###################### FILL THE LINKMAP ARRAY #########################################

    print "Note(0,-1,'','')\n";

	foreach (OOCategory::getRootCategories(true) as $cat){

        $parent = $cat->getId() * 9999;
	    print "Note(".$parent.",0,'".ereg_replace("\n|\r|\"|'","",$cat->getName())."','')\n";

	    foreach($cat->getArticles(false) as $art){
	         print "Note(".$art->getId().",".$parent.",'".ereg_replace("\n|\r|\"|'","",$art->getName())."','redaxo://".$art->getId()."')\n";
	    }

	    foreach ($cat->getChildren(true) as $sub1){

        	$parent1 = $sub1->getId() * 9999;
	        print "Note(".$parent1.",".$parent.",'".ereg_replace("\n|\r|\"|'","",$sub1->getName())."','')\n";

	        foreach($sub1->getArticles(false) as $art){
	            print "Note(".$art->getId().",".$parent1.",'".ereg_replace("\n|\r|\"|'","",$art->getName())."','redaxo://".$art->getId()."')\n";
	        }

	        foreach ($sub1->getChildren(true) as $sub2){

                $parent2 = $sub2->getId() * 9999;
	            print "Note(".$parent2.",".$parent1.",'".ereg_replace("\n|\r|\"|'","",$sub2->getName())."','')\n";

	            foreach($sub2->getArticles(false) as $art){
	                print "Note(".$art->getId().",".$parent2.",'".ereg_replace("\n|\r|\"|'","",$art->getName())."','redaxo://".$art->getId()."')\n";
	            }

	        }

	    }

	}

	?>

  treeTyp[0] = 'f'
  treeIsOn[0] = true
  treeWasOn[0] = true
}

function preOpen()
{
  var self_url=location.href
  var rexep = /[&?]id=(\d+(,\d+)*)/
  if (rexep.test(self_url))
  {
    rexep.exec(self_url)
    var a_id=RegExp.$1.split(',')
    for ( i=0; i<a_id.length; i++ )
    {
      id=a_id[i]
  		while ( id )
      {
        treeIsOn[id2treeIndex[id]] = true
        treeWasOn[id2treeIndex[id]] = true
        treeIsShown[id2treeIndex[id]] = true
			    if (treeTyp[id2treeIndex[id]] == 'f') gif_on(id)
        id=treeP_id[id2treeIndex[id]]
      }
    }
  }
}


var idx=0
initArray()
initIndex()
load_all()
for( i=1; i<idx; i++ )
{
  treeDeep[i] = knotDeep( treeId[i] )
  treeTyp[i] = (i==idx-1||treeP_id[i+1]!=treeId[i]?'b':'f')
  if ( treeDeep[i] == 0 )
    treeIsShown[i] = true
}
if ( isDomNN )
  initStyles()

</script>

<?php

echo '
</head><body bgcolor=#ffffff onLoad="if (layerok) { preOpen(); showTree(); }">
<table border=0 cellpadding=5 cellspacing=0 width=100%>
<tr><td colspan=3 class=grey align=right>'.$REX[SERVERNAME].'</td></tr>
<tr><td class=greenwhite><b>Linkmap</b></td></tr></table>

<table border=0 cellpadding=5 cellspacing=0 width=100%><tr><td class=lgrey>

<SCRIPT language="JavaScript1.2">
<!--

  	initDiv();
  	hideLayer("sitemapinfo");

	function insertLink(link,name){
';

if($_GET[HTMLArea]!=''){
		if($_GET[HTMLArea]=='TINY'){
			print 'window.opener.tinyMCE.insertLink(link,"_self");';
		} else {
	        print 'window.opener.'.$_GET[HTMLArea].'.surroundHTML("<a href="+link+">","</a>");';
	    }
}
if($_GET[opener_input_field]!=''){
			print "linkid = link.replace('redaxo://','');\n";
	        print "opener.document.REX_FORM['LINK[".$_GET[opener_input_field]."]'].value = linkid;\n";
	        print "opener.document.REX_FORM['LINK_NAME[".$_GET[opener_input_field]."]'].value = name;\n";
}

echo '
	        self.close();

	}

//-->
</SCRIPT>

</td></tr></table>

';



?>
