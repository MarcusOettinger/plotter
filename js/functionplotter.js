/*
modified by Marcus Oettinger 06/2020
 - removed jquery/jquery-ui dependencies
 - restructured the code
 - CSP - capable
 - reworked for smoother Color handling
-------------------------------------------------------------------------
http://tobyho.com/2011/11/02/callbacks-in-loops/

Original source: http://rechneronline.de/function-graphs/
#########################################################
Copyright (C) 2011 Juergen Kummer

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

These javascript-functions are needed by the main page, even though
most of the plotter will work without.
*/

/* 
  HTML Element extension to add and remove classes
  This seems to work in IE9+
*/
HTMLElement = typeof(HTMLElement) != 'undefined' ? HTMLElement : Element;

HTMLElement.prototype.addClass = function(string) {
  if (!(string instanceof Array)) {
    string = string.split(' ');
  }
  for(var i = 0, len = string.length; i < len; ++i) {
    if (string[i] && !new RegExp('(\\s+|^)' + string[i] + '(\\s+|$)').test(this.className)) {
      this.className = this.className.trim() + ' ' + string[i];
    }
  }
}

HTMLElement.prototype.removeClass = function(remove) {
    var newClassName = "";
    var i;
    var classes = this.className.split(" ");
    for(i = 0; i < classes.length; i++) {
        if(classes[i] !== remove) {
            newClassName += classes[i] + " ";
        }
    }
    this.className = newClassName;
}

function getgraph() {
	document.getElementById("funcs").submit();
	intsopen();
	document.getElementById("formula1").focus();
                 for (i=0; i<8; i++) {
		changeselfcol(i);
	}
}


function setback() {
	if (confirm('Delete all changes?')) {
		document.getElementById("funcs").reset(); 
		getgraph();
	}
}

/* FIXME: notreached?? */
function intsopen() {
console.log("instopen called and doing nothing!");
/*
	for (var n=1; n<4; n++) {
		if(document.getElementById("sint1").checked == true)
         		intshow(n);
		else
        	 	intclose(n);
	}
	

console.log(document.getElementById("sint1"));	
	if($("#sint1").prop("checked") == true)
         	intshow(1);
         else
         	intclose(1);
	if($("#sint2").prop("checked") == true)
         	intshow(2);
         else
         	intclose(2);
	if
	("#sint3").prop("checked") == true)
         	intshow(3);
         else
         	intclose(3);
        */ 
}


function intshow(x) {
	document.getElementById("formula"+x).removeClass("w190");
	document.getElementById("intc"+x).removeClass("nodisplay");
	document.getElementById("formula"+x).addClass("w120");
	document.getElementById("intc"+x).addClass("display");
	document.getElementById("cint"+x).focus();
}

function intclose(x) {
	document.getElementById("formula"+x).removeClass("w120");
	document.getElementById("intc"+x).removeClass("display");
	document.getElementById("formula"+x).addClass("w190");
	document.getElementById("intc"+x).addClass("nodisplay");
}

/*
 * loadg(): load a graph by reading the query-string in the textarea
 * at the bottom
 */
function loadg() {
	var pp = document.getElementById( "path" ).value;
	if(!pp) return false;

	document.getElementById( "formula1" ).value = decodeURIComponent(pp.substring(pp.indexOf("&a1=")+4,pp.indexOf("&a2=")));
	document.getElementById( "formula2" ).value = decodeURIComponent(pp.substring(pp.indexOf("&a2=")+4,pp.indexOf("&a3=")));
	document.getElementById( "formula3" ).value = decodeURIComponent(pp.substring(pp.indexOf("&a3=")+4,pp.indexOf("&a7=")));
	document.getElementById( "term1").checked = (pp.substring(pp.indexOf("&a7=")+4,pp.indexOf("&a8=")) == 1);
	document.getElementById( "term2").checked = (pp.substring(pp.indexOf("&a8=")+4,pp.indexOf("&a9=")) == 1);
	document.getElementById( "term3").checked = (pp.substring(pp.indexOf("&a9=")+4,pp.indexOf("&b0=")) == 1);
	document.getElementById( "width" ).value = pp.substring(pp.indexOf("&b0=")+4,pp.indexOf("&b1="));
	document.getElementById( "height" ).value = pp.substring(pp.indexOf("&b1=")+4,pp.indexOf("&b2="));
	document.getElementById( "rulex1" ).value = pp.substring(pp.indexOf("&b2=")+4,pp.indexOf("&b3="));
	document.getElementById( "rulex2").value = pp.substring(pp.indexOf("&b3=")+4,pp.indexOf("&b4="));
	document.getElementById( "ruley1" ).value = pp.substring(pp.indexOf("&b4=")+4,pp.indexOf("&b5="));
	document.getElementById( "ruley2" ).value = pp.substring(pp.indexOf("&b5=")+4,pp.indexOf("&b6="));
	document.getElementById( "intervalsx" ).value = pp.substring(pp.indexOf("&b6=")+4,pp.indexOf("&b7="));
	document.getElementById( "intervalsy" ).value = pp.substring(pp.indexOf("&b7=")+4,pp.indexOf("&b8="));
	document.getElementById( "linex" ).value = pp.substring(pp.indexOf("&b8=")+4,pp.indexOf("&b9="));
	document.getElementById( "liney" ).value = pp.substring(pp.indexOf("&b9=")+4,pp.indexOf("&c0="));
	document.getElementById( "deci" ).value = pp.substring(pp.indexOf("&c0=")+4,pp.indexOf("&c1="));
	document.getElementById( "mid" ).value = pp.substring(pp.indexOf("&c1=")+4,pp.indexOf("&c2="));
	document.getElementById( "lines" ).checked = (pp.substring(pp.indexOf("&c2=")+4,pp.indexOf("&c3=")) == 1);
	document.getElementById( "numbers" ).checked = (pp.substring(pp.indexOf("&c3=")+4,pp.indexOf("&c4=")) == 1);
	document.getElementById( "dashes" ).checked = (pp.substring(pp.indexOf("&c4=")+4,pp.indexOf("&c5=")) == 1);
	document.getElementById( "frame" ).checked = (pp.substring(pp.indexOf("&c5=")+4,pp.indexOf("&c6=")) == 1);
	document.getElementById( "errors" ).checked = (pp.substring(pp.indexOf("&c6=")+4,pp.indexOf("&c7=")) == 1);

	var s1=pp.substring(pp.indexOf("&c7=")+4,pp.indexOf("&c8="));
	var s2=pp.substring(pp.indexOf("&c8=")+4,pp.indexOf("&c9="));
	var s3=pp.substring(pp.indexOf("&c9=")+4,pp.indexOf("&d0="));
	if(!s1) s1=0;
	if(!s2) s2=0;
	if(!s3) s3=0;
	document.getElementsByName("sint1")[s1].checked=1;
	document.getElementsByName("sint2")[s2].checked=1;
	document.getElementsByName("sint3")[s3].checked=1;
	intsopen();

	document.getElementById( "grid" ).checked = (pp.substring(pp.indexOf("&d0=")+4,pp.indexOf("&d1=")) == 1);
	document.getElementById( "gridx" ).value = pp.substring(pp.indexOf("&d1=")+4,pp.indexOf("&d2="));
	document.getElementById( "gridy" ).value = pp.substring(pp.indexOf("&d2=")+4,pp.indexOf("&d3="));

	var jslogskx=pp.substring(pp.indexOf("&g5=")+4,pp.indexOf("&g6="));
	if(jslogskx!=0 && jslogskx!=2 && jslogskx!="M_E" && jslogskx!=10 && jslogskx!=100) {
		document.getElementById( "logskix ").value = jslogskx;
		clrlog('x');
	} else {
		document.getElementById( "logskix").value = "";
		if(jslogskx==0)
			document.getElementsByName("logskx")[0].checked=1;
		else if(jslogskx==2)
			document.getElementsByName("logskx")[1].checked=1;
		else if(jslogskx=="M_E")
			document.getElementsByName("logskx")[2].checked=1;
		else if(jslogskx==10)
			document.getElementsByName("logskx")[3].checked=1;
		else if(jslogskx==100)
			document.getElementsByName("logskx")[4].checked=1;
	}

	var jslogsk=pp.substring(pp.indexOf("&d3=")+4,pp.indexOf("&d4="));
	if(jslogsk!=0 && jslogsk!=2 && jslogsk!="M_E" && jslogsk!=10 && jslogsk!=100) {
		document.getElementById( "logski" ).value = jslogsk;
		clrlog('');
	} else {
		document.getElementById("logski").value="";
		if(jslogsk==0)
			document.getElementsByName("logsk")[0].checked=1;
		else if(jslogsk==2)
			document.getElementsByName("logsk")[1].checked=1;
		else if(jslogsk=="M_E")
			document.getElementsByName("logsk")[2].checked=1;
		else if(jslogsk==10)
			document.getElementsByName("logsk")[3].checked=1;
		else if(jslogsk==100)
			document.getElementsByName("logsk")[4].checked=1;
	}

	document.getElementById( "ta1").value=pp.substring(pp.indexOf("&d4=")+4,pp.indexOf("&d5="));
	document.getElementById( "ta2" ).value = pp.substring(pp.indexOf("&d5=")+4,pp.indexOf("&d6="));
	document.getElementById( "tb1" ).value = pp.substring(pp.indexOf("&d6=")+4,pp.indexOf("&d7="));
	document.getElementById( "tb2" ).value = pp.substring(pp.indexOf("&d7=")+4,pp.indexOf("&d8="));
	document.getElementById( "tc1" ).value = pp.substring(pp.indexOf("&d8=")+4,pp.indexOf("&d9="));
	document.getElementById( "tc2" ).value = pp.substring(pp.indexOf("&d9=")+4,pp.indexOf("&e0="));
	document.getElementById( "cint1" ).value = pp.substring(pp.indexOf("&e0=")+4,pp.indexOf("&e1="));
	document.getElementById( "cint2" ).value = pp.substring(pp.indexOf("&e1=")+4,pp.indexOf("&e2="));
	document.getElementById( "cint3" ).value = pp.substring(pp.indexOf("&e2=")+4,pp.indexOf("&e3="));
	document.getElementById( "qq" ).value = pp.substring(pp.indexOf("&e3=")+4,pp.indexOf("&e4="));
	document.getElementById( "selfcol3" ).value = pp.substring(pp.indexOf("&e4=")+4,pp.indexOf("&e5="));
	document.getElementById( "selfcol6" ).value = pp.substring(pp.indexOf("&e5=")+4,pp.indexOf("&e6="));
	document.getElementById( "selfcol4" ).value = pp.substring(pp.indexOf("&e6=")+4,pp.indexOf("&e7="));
	document.getElementById( "selfcol5" ).value = pp.substring(pp.indexOf("&e7=")+4,pp.indexOf("&e8="));
	document.getElementById( "con0" ).value = pp.substring(pp.indexOf("&e8=")+4,pp.indexOf("&e9="));
	document.getElementById( "con1" ).value = pp.substring(pp.indexOf("&e9=")+4,pp.indexOf("&f0="));
	document.getElementById( "con2" ).value = pp.substring(pp.indexOf("&f0=")+4,pp.indexOf("&f1="));
	document.getElementById( "anti").checked = (pp.substring(pp.indexOf("&f1=")+4,pp.indexOf("&f2=")) == 1);
	document.getElementById( "gamma" ).value = pp.substring(pp.indexOf("&f2=")+4,pp.indexOf("&f3="));
	document.getElementById( "bri" ).value = pp.substring(pp.indexOf("&f3=")+4,pp.indexOf("&f4="));
	document.getElementById( "cont" ).value = pp.substring(pp.indexOf("&f4=")+4,pp.indexOf("&f5="));
	document.getElementById( "emb").checked = (pp.substring(pp.indexOf("&f5=")+4,pp.indexOf("&f6=")) == 1);
	document.getElementById( "blur").checked = (pp.substring(pp.indexOf("&f6=")+4,pp.indexOf("&f7=")) == 1);
	document.getElementById( "neg").checked = (pp.substring(pp.indexOf("&f7=")+4,pp.indexOf("&f8=")) == 1);
	document.getElementById( "gray").checked = (pp.substring(pp.indexOf("&f8=")+4,pp.indexOf("&f9=")) == 1);
	document.getElementById( "mean").checked = (pp.substring(pp.indexOf("&f9=")+4,pp.indexOf("&g0=")) == 1);
	document.getElementById( "edge").checked = (pp.substring(pp.indexOf("&g0=")+4,pp.indexOf("&g1=")) == 1);
	document.getElementById( "bf" ).value = pp.substring(pp.indexOf("&g1=")+4,pp.indexOf("&g2="));
	document.getElementById( "pol").checked = (pp.substring(pp.indexOf("&g2=")+4,pp.indexOf("&g3=")) == 1);
	document.getElementById( "rotate" ).value = pp.substring(pp.indexOf("&g3=")+4,pp.indexOf("&g4="));
	document.getElementById( "filetype" ).value = pp.substring(pp.indexOf("&g4=")+4,pp.indexOf("&g5="));
	document.getElementById( "Y" ).value = pp.substring(pp.indexOf("&g6=")+4,pp.indexOf("&g7="));
	document.getElementById( "selfcol0" ).value = pp.substring(pp.indexOf("&g7=")+4,pp.indexOf("&g8="));
	document.getElementById( "selfcol1" ).value = pp.substring(pp.indexOf("&g8=")+4,pp.indexOf("&g9="));
	document.getElementById( "selfcol2" ).value = pp.substring(pp.indexOf("&g9=")+4,pp.indexOf("&h0="));
	/* 
	 * h1 (variable name) and everything from pc on (dynamic list of points) is optional.
	 * Caution: order of args in the query string is vital!
	 */
	if (pp.indexOf("&h1") > 0) {
		document.getElementById( "thick" ).value = pp.substring(pp.indexOf("&h0=")+4,pp.indexOf("&h1"));
	} else {
		document.getElementById( "thick" ).value = pp.substring(pp.indexOf("&h0=")+4,pp.indexOf("&z"));
	}
	if (pp.indexOf("&pc") > 0) {
		document.getElementById( "varname" ).value = pp.substring(pp.indexOf("&h1=")+4,pp.indexOf("&pc"));
	} else {
		document.getElementById( "varname" ).value = pp.substring(pp.indexOf("&h1=")+4,pp.indexOf("&z"));
	}
	
	
	/* points, color is 'pc', name 'P'#, x/y are 'x'# and 'y'# */
	/* delete pointlines!! */
	while (nPt > 0) {
		delline()
	}
	var pos = pp.indexOf("&pc=")
	if ( pos > 0) {
		document.getElementById( "selfcol7" ).value = pp.substring(pos+4,pos+10);
		var pointindex = 0;
		var pval = "&p" + pointindex + "=";
		/* iterate through point definitions */
		while ( pp.indexOf(pval) > 0) {
			addline();
			document.getElementById( "PName" + (pointindex+1) ).value = 
				pp.substring(pp.indexOf("&p" + pointindex + "=")+4,
				             pp.indexOf("&x" + pointindex +"="));
			document.getElementById( "PX" + (pointindex+1) ).value = 
				pp.substring(pp.indexOf("&x" + pointindex + "=")+4,
				             pp.indexOf("&y" + pointindex +"="));
			if ( pp.indexOf("&p" + (pointindex +1) + "=") >0) {            
				document.getElementById( "PY" + (pointindex+1) ).value = 
					pp.substring(pp.indexOf("&y" + pointindex + "=")+4,
					             pp.indexOf("&p" + (pointindex+1) +"="));
			} else {
				document.getElementById( "PY" + (pointindex+1) ).value = 
					pp.substring(pp.indexOf("&y" + pointindex + "=")+4,
					             pp.indexOf("&z"));
			}
			pointindex++;
			pval = "&p" + pointindex + "=";
		}
	}

	getgraph();
};


/* set log checkboxes in the dialog to unchecked (for a = "x" or "y" */
function clrlog(a) {
	var nm = "logsk" + a;
	var mylist = document.getElementsByName( nm );
	Array.prototype.forEach.call(mylist, function( el ) {
		el.checked = false;
	});
}


/*
 * standard(): reset plot options to defaults
 */
function standard() {
// TODO: colors!!
	if ( confirm('Set to standard display values?') ) {
		document.getElementById( "filetype").value = 0;
		/* color inputs */
		document.getElementById( "selfcol0").value = "ff8000";
		document.getElementById( "selfcol1").value = "a0b0c0";
		document.getElementById( "selfcol2").value = "6080a0";
		document.getElementById( "selfcol3").value = "ffffff";
		document.getElementById( "selfcol4").value = "141414";
		document.getElementById( "selfcol5").value = "f2f2f2";
		document.getElementById( "selfcol6").value = "ffffff";
		document.getElementById( "linex").value = 5;
		document.getElementById( "liney").value = 5;
		document.getElementById( "gapc").value = 14;
		document.getElementById( "grid").checked = true;
		document.getElementById( "lines"). checked = true;
		document.getElementById( "numbers"). checked = true;
		document.getElementById( "dashes"). checked = true;
		document.getElementById( "frame").checked = false;
		document.getElementById( "errors"). checked = true;
		document.getElementById( "anti"). checked = true;
		document.getElementById( "pol"). checked = true;
		document.getElementById( "bf").value = 1;
		document.getElementById( "gamma").value = 1;
		document.getElementById( "bri").value = 0;
		document.getElementById( "cont").value = 0;
		document.getElementById( "rotate").value = 0;
		document.getElementById( "emb").checked = false;
		document.getElementById( "blur"). checked = false;
		document.getElementById( "neg"). checked = false;
		document.getElementById( "gray"). checked = false;
		document.getElementById( "mean"). checked = false;
		document.getElementById( "edge"). checked = false;
		document.getElementById( "width").value = 500;
		document.getElementById( "height").value = 500;
		document.getElementById( "thick").value = 1;
		getgraph();
	}
}

/*
 * getalign: set ranges for different quadrants
 */
function getalign(x) {
	var v = document.getElementById( "qsize" ).value.replace(",","." );
	document.getElementById( "rulex1" ).value = -v;
	document.getElementById( "rulex2" ).value = v;
	document.getElementById( "ruley1" ).value = -v;
	document.getElementById( "ruley2" ).value = v;
	if(x=="a0" ) {
		document.getElementById( "rulex1" ).value = 0;
		document.getElementById( "rulex2" ).value = 0;
		document.getElementById( "ruley1" ).value = 0;
		document.getElementById( "ruley2" ).value = 2*v;
	} else if(x=="a1" ) {
		document.getElementById( "rulex1" ).value =- 2*v;
		document.getElementById( "rulex2" ).value = 0;
		document.getElementById( "ruley1" ).value = 0;
		document.getElementById( "ruley2" ).value = 2*v;
	} else if(x=="a2" ) {
		document.getElementById( "rulex1" ).value =- 2*v;
		document.getElementById( "rulex2" ).value = 0;
		document.getElementById( "ruley1" ).value =- 2*v;
		document.getElementById( "ruley2" ).value = 0;
	} else if(x=="a3" ) {
		document.getElementById( "rulex1" ).value = 0;
		document.getElementById( "rulex2" ).value = 2*v;
		document.getElementById( "ruley1" ).value =- 2*v;
		document.getElementById( "ruley2" ).value = 0;
	} else if(x=="b0" ) {
		document.getElementById( "ruley1" ).value = 0;
		document.getElementById( "ruley2" ).value = 2*v;
	} else if(x=="b1" ) {
		document.getElementById( "rulex1" ).value =- 2*v;
		document.getElementById( "rulex2" ).value = 0;
	} else if(x=="b2" ) {
		document.getElementById( "ruley1" ).value =- 2*v;
		document.getElementById( "ruley2" ).value = 0;
	} else if(x=="b3" ) {
		document.getElementById( "rulex1" ).value = 0;
		document.getElementById( "rulex2" ).value = 2*v;
	}
	getgraph();
}

/*
 * changeselfcol(int x): callback for change of color values
 * via input with id 'selfcol'+x
 */
function changeselfcol(x) {
	var id = "selfcol" + x, bgid = "selfcolbg" + x;
	var colstr = "#" + document.getElementById( id ).value;
	document.getElementById( bgid ).style.backgroundColor = colstr;
	
	/* update color of function names (f(x), g(x), h(x)) */
	if (x<3) {
		var id2 = "fl" + x;
		document.getElementById( id2 ).style.color = colstr;
	}
}

function copyURL() {
	if (typeof document.execCommand === "function") {
 		var copyText = document.getElementById("shortpath");
		copyText.select();
		copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		document.execCommand("copy");
	} else {
		console.log("Unable to copy text!");
	}
}

    
    /* 
     * add a new point to the list - set name and Position
     */
    var nPt = 0;
    function addline(){
	nPt++;
	html = "<div id='Pdiv"+nPt+"' class='pline'>";
	html += "Name: <input class='w40' id='PName"+nPt+"' name='PName"+nPt+"' value='"+String.fromCharCode(nPt+79)+"'>";
	html += " at (x=<input class='w40' id='PX"+nPt+"' name='PX"+nPt+"' value='0'>";
	html += "/y=<input class='w40' id='PY"+nPt+"' name='PY"+nPt+"' value='0'>)";
	html += "</div>";

	var el = document.getElementById('eof');
	el.insertAdjacentHTML('beforeend', html);
	document.getElementById("delpoint").style.display = "inline";
	if (nPt == 10) { document.getElementById("addpoint").style.display = "none"; }
    }

    /* 
     * remove the last point (bottom of the list)
     */
    function delline() {
	var el = document.getElementById("Pdiv" + nPt);
        el.parentNode.removeChild(el);
	nPt--;
	document.getElementById("addpoint").style.display = "inline";
	if (nPt == 0) { document.getElementById("delpoint").style.display = "none"; }
    }
    
    function togglediv( id ) {
        var el = document.getElementById( id );
        el.style.display = (el.style.display === "block" ? "none" : "block");
    }
    
    
document.addEventListener("DOMContentLoaded", function(event) {     
//$(function() {
	n = 0; // Count Points to display
	getgraph();

	var tooltip = new Tooltip({ theme: "light", distance: 5, delay: 0 });
	var x = document.getElementById("myDIV");

	document.getElementById( "infobutton1" ).addEventListener('click', function() {  
		togglediv( "info1" ); });
	document.getElementById( "infobutton2" ).addEventListener('click', function() {  
		togglediv( "info2" ); });
	document.getElementById( "infobutton3" ).addEventListener('click', function() {  
		togglediv( "info3" ); });
	document.getElementById( "infobutton4" ).addEventListener('click', function() {  
		togglediv( "info4" ); });
	
//	$( "#accordion" ).accordion({heightStyle: "content", autoHeight: false });
	var accordion = new Accordion({
	    element: 'accordion',
	    openTab: 1,
 	   oneOpen: true
	});
/*
	$( "#dialog" ).dialog({width:600,autoOpen: false,
	        buttons: [{text: "OK",click: function(){$( this ).dialog( "close" );}}],
	        title: "Plot options", modal: false
	}).parent().appendTo($("#funcs")); 
*/
	/*
	 *  that's a really ugly hack (append dialog to form, NOT to body, 
	 * which is the default. If you don't, values inside the dialog will not be submitted!)
	 */
	
	document.getElementById( "submit" ).addEventListener('click', function( event ) {
		document.getElementById( "funcs" ).submit(); });
	document.getElementById( "inputReset" ).addEventListener('click', function( event ) {
		standard() ;
		event.preventDefault();
	});
/*
	document.getElementById( "opendialog" ).addEventListener('click', function( event ) { 
		$('#dialog').dialog('open');
		event.preventDefault();
	});
*/

	
	/* switch between quadrants displayed */
	document.getElementById( "areset" ).addEventListener('click', function( event ) { getalign( 0 ); });
	document.getElementById( 'a0' ).addEventListener('click', function( event ) { getalign( 'a0' ); });
	document.getElementById( 'b0' ).addEventListener('click', function( event ) { getalign( 'b0' ); });
	document.getElementById( 'a1' ).addEventListener('click', function( event ) { getalign( 'a1' ); });
	document.getElementById( 'b1' ).addEventListener('click', function( event ) { getalign( 'b1' ); });
	document.getElementById( 'a2' ).addEventListener('click', function( event ) { getalign( 'a2' ); });
	document.getElementById( 'b2' ).addEventListener('click', function( event ) { getalign( 'b2' ); });
	document.getElementById( 'a3' ).addEventListener('click', function( event ) { getalign( 'a3' ); });
	document.getElementById( 'b3' ).addEventListener('click', function( event ) { getalign( 'b3' ); });
	
	/* handle input for an integration constant - hide the input if f(x) or
	 * derivative selected */
	for (var n = 1; n<4; n++) {
		var id = "sint" + n + "i";
		!function dummy(n){
			var id = "sint" + n + "i";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { intshow( n ); });
			id = "sint" + n + "f";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { intclose( n ); });
			id = "sint" + n + "d";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { intclose( n ); });
		}(n)
	}

	/* set color via textinputs */
	for (var n = 0; n<8; n++) {
		var id = "selfcol" + n;
		!function dummy(n){
			if ( el = document.getElementById( id ))
				el.addEventListener('change', 
					function( event ) { changeselfcol( n ); });
		}(n)
	}
	
	document.getElementById( "clearhull" ).addEventListener('click', function( event ) {
		document.getElementById( "Y").value = "Y";
		document.getElementById( "Y" ).focus();
	});
	document.getElementById( "clearsubst" ).addEventListener('click', function( event ) {
		document.getElementById( "qq" ).value = "";
		document.getElementById( "qq" ).focus();
	});
	/* substitution term changes */
	document.getElementById( "qq" ).addEventListener('change', function( event ) {
		document.getElementById( "qqsingle" ).value = document.getElementById( "qq").value;
	});
	
	/* add/delete a line to the list of points */
	document.getElementById( "addpoint" ).addEventListener('click', 
		function( event ) { addline(); });
	document.getElementById( "delpoint" ).addEventListener('click', 
		function( event ) { delline(); });
	
	/* clear contents of the log textbox if a base is selected
	 * in one of the checkboxes
	 */
	var mylist = document.getElementsByName( "logskx" );
	Array.prototype.forEach.call(mylist, function( el ) {
		el.addEventListener("click", function( event ) { 
			document.getElementById( "logskix" ).value = "";
			getgraph();
		});
	});
	var mylist = document.getElementsByName( "logsk" );
	Array.prototype.forEach.call(mylist, function( el ) {
		el.addEventListener("click", function( event ) { 
			document.getElementById( "logski" ).value = "";
			getgraph();
		});
	});
	
	document.getElementById( "logskix").addEventListener('change', function( event ) { clrlog('x'); });
	document.getElementById( "logski").addEventListener('change', function( event ) { clrlog(''); });
	document.getElementById( "deci").addEventListener('change', function( event ) { 
		/* set number of decimal places for value tables */
		var tmpval = document.getElementById( "deci" ).value;
		document.getElementById( "decis").value = tmpval; 
	});
	
	/*
	 * controls in the lower accordion tab: calculations,
	 * clear/select/load graph via url
	 */
	for (var n = 0; n<4; n++) {
		var id = "calc" + n;
		!function dummy(n){
			if ( el = document.getElementById( id ))
				el.addEventListener('click', function( event ) { 
					document.getElementById( "single1" ).value = document.getElementById( "formula" + n ).value;
					document.getElementById( "inpval" ).focus();
				});
		}(n)
	}
	document.getElementById( "res" ).addEventListener('click', function( event ) {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "tbl" ).addEventListener('click', function( event ) {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "csv" ).addEventListener('click', function( event ) {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "latex" ).addEventListener('click', function( event ) {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "calcreset" ).addEventListener('click', function( event ) {
		 document.getElementById( "inpval" ).value = ""; });
	document.getElementById( "posval" ).addEventListener('click', function( event ) { 
		document.getElementById( "inpval").value = "1 2 3 4 5 6 7 8 9 10"; });
	document.getElementById( "negval" ).addEventListener('click', function( event ) { 
		document.getElementById( "inpval" ).value = "-1 -2 -3 -4 -5 -6 -7 -8 -9 -10"; });
	document.getElementById( "urlclear" ).addEventListener('click', function( event ) { 
		document.getElementById( "path").value = "";
		if (document.getElementById( "shortpath" )) 
			document.getElementById( "shortpath" ).value = "";
		document.getElementById( "path" ).focus();
	});
	document.getElementById( "urlselect" ).addEventListener('click', function( event ) {
		document.getElementById( "path" ).focus();
		document.getElementById( "path" ).select(); 
	});
//	document.getElementById( "urlcopy" ).addEventListener('click', function( event ) { copyURL(); });
	document.getElementById( "loadgraph").addEventListener('click', function( event ) { loadg(); });

});
