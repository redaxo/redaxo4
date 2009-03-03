Molinet Fabien : fabien.molinet@gmail.com
http://fabien-molinet.fr/

Comme beaucoup de personnes j'ai eu le bug d'Internet Explorer lié à sa gestion des SELECT :
si un bloc se trouve au-dessus d'un SELECT, le SELECT passera toujours au-dessus.
Vous pouvez vous amuser avec le z-index, mettre des valeurs exotiques, des valeurs énormes,
rien n'y fera. Internet Explorer n'en tiendra pas compte.

Génant dès que l'on veut utiliser des blocs flottants, des menus déroulants, ...

En fait ce bug est lié à la gestion des SELECT en interne d'Internet Explorer. Ce composant
est créer à partir du composant dropdownlist du système d'exploitation et il outrepasse donc
toute la logique que l'on aurait défini au niveau du navigateur.

Il existe toutefois une parade... mais elle est très sale :)

Il est nécessaire qu'une iframe vienne se placer au-dessus de la balise SELECT.
Dès lors, la balise SELECT sera cachée et l'on pourra gérer la logique au-dessus de l'iframe.

Oui mais ajouter une iframe à mon code HTML pour gérer les problèmes d'IE c'est sale !
De plus cela invaliderait toute validation W3C XHTML et la sémantique serait fortemment affectée...

J'ai réalisé un petit script Javascript nommé Select Fix, qui corrige ce problème d'IE
automatiquement pour vous. Il suffit de l'inclure de la manière suivante via un commentaire conditionnel :

<!--[if lte IE 6.5]>
<script type="text/javascript" src="select_fix.js"></script>
<link media="screen" type="text/css" rel="stylesheet" href="select_fix.css" />
<![endif]-->

En l'incluant ainsi, il ne sera chargé que sur les versions d'Internet Explorer inférieure à la 7.
Ainsi IE 6 et IE5.x devraient être réglés.

Ce script étant non intrusif, il ne devrait donc pas poser de problèmes si vous avez d'autres Javascripts.
En incluant ce script à votre page vous disposez en plus de 3 méthodes publiques :
   1. SelectFix.repairFloatingElement(element,forceResize) :
      - répare l'élement passé en paramètre (va lui ajouter une iframe sous Internet Explorer si nécessaire)
      - si forceResize vaut true, l'élement sera également redimmensionné (utile si la hauteur définie dans l'élément est fausse)
   2. SelectFix.autoRepairFloatingElements(temps_ms) :
      répare tous les x millisecondes tous les élements de la page (utile si l'on incorpore d'autres
	  Javascript qui vont modifier la page après son chargement)
   3. SelectFix.parseFloatingElementsFixed(bool) :
      indique si l'on doit parser les enfants des élements qui ont été réparés (par défaut à false)
   4. SelectFix.isZIndexRequired(bool) :
      indique si un zIndex est nécessaire pour continuer à parser les blocs flottants (par défaut à false, pas de zIndex nécessaire)


- select_fix.css : la CSS à inclure dans votre page via un commentaire conditionnel
- select_fix.js : le Javascript à inclure dans votre page via un commentaire conditionnel
- select_fix.source.js : ce Javascript est le fichier source. Si vous voulez comprendre le code, c'est celui-ci qu'il faut regarder.
- select_fix.prototype.js : si vous utilisez le framework Prototype, incluez plutôt ce Javascript (plus léger).
- select_fix.prototype.source.js : c'est le fichier source de la version pour Prototype du Select Fix

Vous trouverez différents exemples d'utilisation dans le dossier examples.

Note : Internet Explorer 7 corrige le problème du SELECT qui passe au-dessus des blocs
