/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'krz-font\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-copy2': '&#xe900;',
		'icon-copy4': '&#xe901;',
		'icon-copy4-1': '&#xe902;',
		'icon-untitled': '&#xe903;',
		'icon-untitled2': '&#xe904;',
		'icon-Bookmark': '&#xe905;',
		'icon-Briefcase': '&#xe906;',
		'icon-Calendar': '&#xe907;',
		'icon-Camera': '&#xe908;',
		'icon-Cartcopy4': '&#xe909;',
		'icon-Check16x16copy': '&#xe90a;',
		'icon-Check24x24': '&#xe90b;',
		'icon-chevron-down': '&#xe90c;',
		'icon-chevron-left': '&#xe90d;',
		'icon-chevron-right': '&#xe90e;',
		'icon-chevron-small-down': '&#xe90f;',
		'icon-chevron-small-left': '&#xe910;',
		'icon-chevron-small-right': '&#xe911;',
		'icon-chevron-small-up': '&#xe912;',
		'icon-chevron-thin-down': '&#xe913;',
		'icon-chevron-thin-left': '&#xe914;',
		'icon-chevron-thin-right': '&#xe915;',
		'icon-chevron-thin-up': '&#xe916;',
		'icon-chevron-up': '&#xe917;',
		'icon-Clock': '&#xe918;',
		'icon-Closecopy4': '&#xe919;',
		'icon-Close': '&#xe91a;',
		'icon-Comments': '&#xe91b;',
		'icon-Earth': '&#xe91c;',
		'icon-Envelope': '&#xe91d;',
		'icon-google': '&#xe91e;',
		'icon-Heartcopy2': '&#xe91f;',
		'icon-Heart': '&#xe920;',
		'icon-instagram': '&#xe921;',
		'icon-iPhone': '&#xe922;',
		'icon-Key': '&#xe923;',
		'icon-l': '&#xe924;',
		'icon-Lock': '&#xe925;',
		'icon-Lock-1': '&#xe926;',
		'icon-o': '&#xe927;',
		'icon-Pin': '&#xe928;',
		'icon-Pin-1': '&#xe929;',
		'icon-Questionmark': '&#xe92a;',
		'icon-Rectangle1copy3': '&#xe92b;',
		'icon-Rectangle1copy4': '&#xe92c;',
		'icon-Rectangle1copy5': '&#xe92d;',
		'icon-Rectangle45copy2': '&#xe92e;',
		'icon-Reply': '&#xe92f;',
		'icon-Reply-1': '&#xe930;',
		'icon-RoundedRectangle1copy2': '&#xe931;',
		'icon-s': '&#xe932;',
		'icon-Searchcopy': '&#xe933;',
		'icon-Send': '&#xe934;',
		'icon-Settings': '&#xe935;',
		'icon-s-facebook': '&#xe936;',
		'icon-Shape1copy': '&#xe937;',
		'icon-Shape8copy': '&#xe938;',
		'icon-Shape17': '&#xe939;',
		'icon-Shape18': '&#xe93a;',
		'icon-Shape19': '&#xe93b;',
		'icon-ShoppingCart3copy2': '&#xe93c;',
		'icon-SmallCameraAttributeasDanielBrucefromFlaticon': '&#xe93d;',
		'icon-STARcopy13': '&#xe93e;',
		'icon-Trash': '&#xe93f;',
		'icon-triangle-left': '&#xe940;',
		'icon-triangle-rightcopy': '&#xe941;',
		'icon-triangle-right': '&#xe942;',
		'icon-triangle-up': '&#xe943;',
		'icon-twitter': '&#xe944;',
		'icon-User': '&#xe945;',
		'icon-vk': '&#xe946;',
		'icon-Write': '&#xe947;',
		'icon-y': '&#xe948;',
		'icon-dots-three-horizontal': '&#xe949;',
		'icon-dots-three-vertical': '&#xe94a;',
		'icon-download': '&#xe94b;',
		'icon-resize-100': '&#xe94c;',
		'icon-resize-full-screen': '&#xe94d;',
		'icon-credit-card': '&#xe94e;',
		'icon-home': '&#xe94f;',
		'icon-leaf': '&#xe950;',
		'icon-log-out': '&#xe951;',
		'icon-login': '&#xe952;',
		'icon-new': '&#xe953;',
		'icon-pin': '&#xe954;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
