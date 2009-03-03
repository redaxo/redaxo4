Molinet Fabien : fabien.molinet@gmail.com
http://fabien-molinet.fr/

Like many people I had the infamous bug in Internet Explorer related to its management of SELECT:
if a block is located above a SELECT, the SELECT is always above. You can play with the z-index,
putting strange values, huge values, nothing will work. Internet Explorer will still ignore them.

That's really boring when you want to use floating blocks, pull-down menus, ...

In fact this bug is related to the internal management of SELECT by Internet Explorer.
This component is created from the dropdownlist component of the operating system and it doesn't
take into account any logic defined at the browser level.

However, there is a parade ... But it is very dirty :)

It is necessary to put an iframe on top of the tag SELECT. Therefore, the SELECT tag will be hidden,
and we will be able to manage the logic above the iframe.

Yes, but adding an iframe to the HTML code in order to manage the problems of IE is dirty!
Moreover it would invalidate any W3C's XHTML validation and semantics would be greatly affected...

I realized a little Javascript, that I named Select Fix. It corrects the problem under IE
automatically for you. You just have to include it using conditional comments :

<!--[if lte IE 6.5]>
<script type="text/javascript" src="select_fix.js"></script>
<link media="screen" type="text/css" rel="stylesheet" href="select_fix.css" />
<![endif]-->

By including it as well, it will be loaded on versions of Internet Explorer less than 7.
Thus IE 6 and IE 5.x should be resolved.

The script is non-intrusive, it should not pose a problem if you have other JavaScripts.
By including this script to your page you have in addition 3 public methods:
   1. SelectFix.repairFloatingElement(element) :
      - repair the element passed as a parameter (it will add an iframe in Internet Explorer if necessary)
      - if forceResize equals true, the element will be resized (useful when the height is incorrectly defined)
   2. SelectFix.autoRepairFloatingElements(temps_ms) :
      repairs every x milliseconds all the elements on the page (useful if it incorporates other
	  Javascripts that will change the page after its loading)
   3. SelectFix.parseFloatingElementsFixed(bool) :
      Indicates whether to parse the children of elements that have been repaired (defaults to false)
   4. SelectFix.isZIndexRequired(bool) :
      Indicates whether a zIndex is required in order to parse floating elements (defaults to false, no zIndex required)


- select_fix.css : CSS to include in your page using a conditional comment
- select_fix.js : Javascript to include in your page using a conditional comment
- select_fix.source.js : Javascript source file. If you want to understand the code, you should look here.
- select_fix.prototype.js : If you're using the Prototype framework, you should include this Javascript (smaller version).
- select_fix.prototype.source.js : Source file for the Prototype version of the Select Fix

Vous trouverez différents exemples d'utilisation dans le dossier examples.

Note : Internet Explorer 7 corrects the issue of SELECT passing over blocks 
