/*
modified by Marcus Oettinger 08/2021
 - added an option to switch transparency in png/gif
 - better parsing of query options
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
 * HTML Element extension to add and remove classes
 * This seems to work in IE9+
 */
HTMLElement = typeof(HTMLElement) != 'undefined' ? HTMLElement : Element;

HTMLElement.prototype.addClass = function(string) {
  if (!(string instanceof Array)) {
    string = string.split(' ');
  }
  for(var i = 0, len = string.length; i < len; ++i) {
  var s = new RegExp('(\\s+|^)' + string[i] + '(\\s+|$)');
  s.test(this.className);
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

/*
 * getgraph(): submit form to reload the graphic part
 */
function getgraph() {
	document.getElementById("funcs").submit();
	intsopen();
	document.getElementById("formula1").focus();
                 for (i=0; i<8; i++) {
		changeselfcol(i);
	}
}

/* 
 * intsopen: check for integral display on load and handle
 * input fields for the functions
 */
function intsopen() {
	for (var n=1; n<4; n++) {
		if(document.getElementById("sint" + n + "i").checked == true)
         		intshow(n);
		else
        	 	intclose(n);
	}
	for(var i=1; i<4; i++) {
		if(document.getElementById("sint"+i+"i").checked == true)
			intshow(i);
		else
			intclose(i);
	}
}

/* integral selected: add a field for integration constant */
function intshow(x) {
	document.getElementById("formula"+x).removeClass("w190");
	document.getElementById("intc"+x).removeClass("nodisplay");
	document.getElementById("formula"+x).addClass("w120");
	document.getElementById("intc"+x).addClass("display");
	document.getElementById("cint"+x).focus();
}

/* integral deselected: remove field for integration constant */
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
	
	/* parse the query string into an assoziative array */
	var query = pp.substring( pp.indexOf("?")+1 );
	var vars = query.split('&');
	var values = [];
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split('=');
		values[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
	}

	document.getElementById( "formula1" ).value = values["a1"];
	document.getElementById( "formula2" ).value = values["a2"];
	document.getElementById( "formula3" ).value = values["a3"];	
	document.getElementById( "term0").checked = (values["a7"] == 1);
	document.getElementById( "term1").checked = (values["a8"] == 1);
	document.getElementById( "term2").checked = (values["a9"] == 1);
	
	document.getElementById( "width" ).value = values["b0"];
	document.getElementById( "height" ).value = values["b1"];
	document.getElementById( "xlimit1" ).value = values["b2"];
	document.getElementById( "xlimit2").value = values["b3"];
	document.getElementById( "ylimit1" ).value = values["b4"];
	document.getElementById( "ylimit2" ).value = values["b5"];
	document.getElementById( "intervalsx" ).value = values["b6"];
	document.getElementById( "intervalsy" ).value = values["b7"];
	document.getElementById( "linex" ).value = values["b8"];
	document.getElementById( "liney" ).value = values["b9"];
	
	document.getElementById( "deci" ).value = values["c0"];
	document.getElementById( "mid" ).value = values["c1"];
	document.getElementById( "lines" ).checked = (values["c2"] == 1);
	document.getElementById( "numbers" ).checked = (values["c3"] == 1);
	document.getElementById( "dashes" ).checked = (values["c4"] == 1);
	document.getElementById( "frame" ).checked = (values["c5"] == 1);
	document.getElementById( "errors" ).checked = (values["c6"] == 1);

	var s1 = values["c7"];
	var s2 = values["c8"];
	var s3 = values["c9"];
	if(!s1) s1 = 0;
	if(!s2) s2 = 0;
	if(!s3) s3 = 0;
	document.getElementsByName("sint1")[s1].checked=1;
	document.getElementsByName("sint2")[s2].checked=1;
	document.getElementsByName("sint3")[s3].checked=1;
	intsopen();

	document.getElementById( "grid" ).checked = (values["d0"] == 1);
	document.getElementById( "gridx" ).value = values["d1"];
	document.getElementById( "gridy" ).value = values["d2"];

	var jslogskx = values["g5"];
	if (jslogskx != 0 && jslogskx != 2 && jslogskx != "M_E" && jslogskx != 10 && jslogskx != 100) {
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

	var jslogsk = values["d3"];
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

	document.getElementById( "ta1").value = values["d4"];
	document.getElementById( "ta2" ).value = values["d5"];
	document.getElementById( "tb1" ).value = values["d6"];
	document.getElementById( "tb2" ).value = values["d7"];
	document.getElementById( "tc1" ).value = values["d8"];
	document.getElementById( "tc2" ).value = values["d9"];
	document.getElementById( "cint1" ).value = values["e0"];
	document.getElementById( "cint2" ).value = values["e1"];
	document.getElementById( "cint3" ).value = values["e2"];
	document.getElementById( "qq" ).value = values["e3"];
	document.getElementById( "selfcol3" ).value = values["e4"];
	document.getElementById( "selfcol6" ).value = values["e5"];
	document.getElementById( "selfcol4" ).value = values["e6"];
	document.getElementById( "selfcol5" ).value = values["e7"];
	document.getElementById( "con0" ).value = values["e8"];
	document.getElementById( "con1" ).value = values["e9"];
	
	document.getElementById( "con2" ).value = values["f0"];
	document.getElementById( "anti").checked = values["f1"];
	document.getElementById( "gamma" ).value = values["f2"];
	document.getElementById( "bri" ).value = values["f3"];
	document.getElementById( "cont" ).value = values["f4"];
	document.getElementById( "emb").checked = (values["f5"] == 1);
	document.getElementById( "blur").checked = (values["f6"] == 1);
	document.getElementById( "neg").checked = (values["f7"] == 1);
	document.getElementById( "gray").checked = (values["f8"] == 1);
	document.getElementById( "mean").checked = (values["f9"] == 1);
	document.getElementById( "edge").checked = (values["g0"] == 1);
	document.getElementById( "bf" ).value = values["g1"];
	document.getElementById( "pol").checked = (values["g2"] == 1);
	document.getElementById( "rotate" ).value = values["g3"];
	document.getElementById( "filetype" ).value = values["g4"];
	document.getElementById( "Y" ).value = values["g6"];
	document.getElementById( "selfcol0" ).value = values["g7"];
	document.getElementById( "selfcol1" ).value = values["g8"];
	document.getElementById( "selfcol2" ).value = values["g9"];
	document.getElementById( "thick" ).value = values["h0"];
	
	/* varname (h1) and further options are optional (for backward compatibility) */
	document.getElementById( "varname" ).value = (values["h1"] == undefined ? "x" : values["h1"]);
	if (values["h2"] == undefined) document.getElementById( "transp" ).checked = true;
	else document.getElementById( "transp" ).checked = (values["h2"] == 1);
	if (values["h3"] == undefined) document.getElementById( "prettyprint" ).checked = true;
	else document.getElementById( "prettyprint" ).checked = (values["h3"] == 1);

	/* points, color is 'p', name 'p'#, x/y are 'x'# and 'y'# */
	/* delete pointlines!! */
	while (nPt > 0) delline()
	document.getElementById( "selfcol7" ).value = (values["p"] == undefined ? "6080a0" : values["p"]);
	var pointindex = 0;
	var pval = "&p" + pointindex + "=";
	/* iterate through point definitions */
	while ( pp.indexOf(pval) > 0) {
		addline();
		document.getElementById( "PName" + (pointindex+1) ).value = values["p"+pointindex];
		document.getElementById( "PX" + (pointindex+1) ).value = values["x"+pointindex];
		document.getElementById( "PY" + (pointindex+1) ).value = values["y"+pointindex];
		pointindex++;
		pval = "&p" + pointindex + "=";
	}

	getgraph();
};


/* set log checkboxes in the dialog to unchecked (for a = "x" or "" (for y) */
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
	if ( confirm('Reset all options to standard values?') ) {
		document.getElementById( "filetype").value = 2;
		/* color inputs */
		document.getElementById( "selfcol0").value = "ff8000";
		document.getElementById( "selfcol1").value = "a0b0c0";
		document.getElementById( "selfcol2").value = "6080a0";
		document.getElementById( "selfcol3").value = "ffffff";
		document.getElementById( "selfcol4").value = "141414";
		document.getElementById( "selfcol5").value = "f2f2f2";
		document.getElementById( "selfcol6").value = "ffffff";
		document.getElementById( "term0").checked = true;
		document.getElementById( "term1").checked = true;
		document.getElementById( "term2").checked = true;
		document.getElementById( "linex").value = 5;
		document.getElementById( "liney").value = 5;
		document.getElementById( "gridx").value = 20;		
		document.getElementById( "gridy").value = 20;		
		document.getElementById( "deci").value = 3;
		document.getElementById( "mid").value = 0;
		document.getElementById( "varname").value = "x";
		document.getElementById( "grid").checked = true;
		document.getElementById( "lines").checked = true;
		document.getElementById( "numbers").checked = true;
		document.getElementById( "dashes").checked = true;
		document.getElementById( "frame").checked = false;
		document.getElementById( "errors").checked = true;
		document.getElementById( "anti").checked = true;
		document.getElementById( "pol").checked = true;
		document.getElementById( "bf").value = 1;
		document.getElementById( "gamma").value = 1;
		document.getElementById( "bri").value = 0;
		document.getElementById( "cont").value = 0;
		document.getElementById( "rotate").value = 0;
		document.getElementById( "emb").checked = false;
		document.getElementById( "blur").checked = false;
		document.getElementById( "neg").checked = false;
		document.getElementById( "gray").checked = false;
		document.getElementById( "mean").checked = false;
		document.getElementById( "edge").checked = false;
		document.getElementById( "width").value = 500;
		document.getElementById( "height").value = 500;
		document.getElementById( "thick").value = 2;
//		clrlog( "x");
//		clrlog( "" );
		document.getElementById( "logskix").value = "";
		document.getElementById( "logski").value = "";
		// class!!
		for (var n=0; n<5; n++) {
			document.getElementsByName("logskx")[n].checked = false;
			document.getElementsByName("logsk")[n].checked = false;
		}
		/*
		document.getElementById( "logskx").value= "0";
		document.getElementById( "logsk").value= "0";
		*/
		document.getElementById( "transp").checked = true;
		document.getElementById( "prettyprint").checked = true;
		getgraph();
	}
}

/*
 * getalign: set ranges for different quadrants
 */
function getalign(x) {
	var v = document.getElementById( "qsize" ).value.replace(",","." );
	document.getElementById( "xlimit1" ).value = -v;
	document.getElementById( "xlimit2" ).value = v;
	document.getElementById( "ylimit1" ).value = -v;
	document.getElementById( "ylimit2" ).value = v;
	if(x=="a0" ) {
		document.getElementById( "xlimit1" ).value = 0;
		document.getElementById( "xlimit2" ).value = 2*v;
		document.getElementById( "ylimit1" ).value = 0;
		document.getElementById( "ylimit2" ).value = 2*v;
	} else if(x=="a1" ) {
		document.getElementById( "xlimit1" ).value =- 2*v;
		document.getElementById( "xlimit2" ).value = 0;
		document.getElementById( "ylimit1" ).value = 0;
		document.getElementById( "ylimit2" ).value = 2*v;
	} else if(x=="a2" ) {
		document.getElementById( "xlimit1" ).value =- 2*v;
		document.getElementById( "xlimit2" ).value = 0;
		document.getElementById( "ylimit1" ).value =- 2*v;
		document.getElementById( "ylimit2" ).value = 0;
	} else if(x=="a3" ) {
		document.getElementById( "xlimit1" ).value = 0;
		document.getElementById( "xlimit2" ).value = 2*v;
		document.getElementById( "ylimit1" ).value =- 2*v;
		document.getElementById( "ylimit2" ).value = 0;
	} else if(x=="b0" ) {
		document.getElementById( "ylimit1" ).value = 0;
		document.getElementById( "ylimit2" ).value = 2*v;
	} else if(x=="b1" ) {
		document.getElementById( "xlimit1" ).value =- 2*v;
		document.getElementById( "xlimit2" ).value = 0;
	} else if(x=="b2" ) {
		document.getElementById( "ylimit1" ).value =- 2*v;
		document.getElementById( "ylimit2" ).value = 0;
	} else if(x=="b3" ) {
		document.getElementById( "xlimit1" ).value = 0;
		document.getElementById( "xlimit2" ).value = 2*v;
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

/*
 * copyURL( id ): copy text from an element with id="id" to the
 * clipboard
 */
function copyURL( id ) {
	if (typeof document.execCommand === "function") {
 		var copyText = document.getElementById(id);
		copyText.select();
		copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		document.execCommand("copy");
	} else {
		alert("Unable to copy text! Bummer...");
	}
}


    
    /* 
     * add a new point to the list of named points:
     *  - add a line to set name and position
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
	/* call accordion.open to resize the first tab */
	accordion.open(1);
    }

    /* 
     * remove the last point (at the bottom of the list)
     */
    function delline() {
	var el = document.getElementById("Pdiv" + nPt);
        el.parentNode.removeChild(el);
	nPt--;
	document.getElementById("addpoint").style.display = "inline";
	if (nPt == 0) document.getElementById("delpoint").style.display = "none";
    }
    
    function togglediv( id ) {
        var el = document.getElementById( id );
        el.style.display = (el.style.display === "block" ? "none" : "block");
    }

    
document.addEventListener("DOMContentLoaded", function(event) {     
	n = 0; // Count Points to display
	getgraph();

	var tooltip = new Tooltip({ theme: "light", distance: 5, delay: 0 });
	var x = document.getElementById("myDIV");

	document.getElementById( "infobutton1" ).addEventListener('click', function() {  
		togglediv( "info1" ); });
	document.getElementById( "closeinfo1" ).addEventListener('click', function() {  
		togglediv( "info1" ); });
	document.getElementById( "infobutton2" ).addEventListener('click', function() {  
		togglediv( "info2" ); });
	document.getElementById( "closeinfo2" ).addEventListener('click', function() {  
		togglediv( "info2" ); });
	document.getElementById( "infobutton3" ).addEventListener('click', function() {  
		togglediv( "info3" ); });
	document.getElementById( "closeinfo3" ).addEventListener('click', function() {  
		togglediv( "info3" ); });
	document.getElementById( "infobutton4" ).addEventListener('click', function() {  
		togglediv( "info4" ); });
	document.getElementById( "closeinfo4" ).addEventListener('click', function() {  
		togglediv( "info4" ); });

	
	accordion = new Accordion({
	    element: 'accordion',
	    openTab: 1,
	    oneOpen: true
	});
	
	document.getElementById( "submit" ).addEventListener('click', function( event ) {
		document.getElementById( "funcs" ).submit(); });
	document.getElementById( "inputReset" ).addEventListener('click', function( event ) {
		standard() ;
//		event.preventDefault();
	});

	
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
	
	/* 
	 * handle input for an integration constant - hide the input if f(x) or
	 * derivative selected
	 */
	for (var n = 1; n<4; n++) {
		var id = "sint" + n + "i";
		!function dummy(n){
			var id = "sint" + n + "i";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { getgraph(); });
			id = "sint" + n + "f";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { getgraph(); });
			id = "sint" + n + "d";
			document.getElementById( id ).addEventListener('click', 
					function( event ) { getgraph(); });
		}(n)
	}
	/* replot graph when select value changes */
	for (var n = 0; n<3; n++) {
		id = "con" + n;
		document.getElementById( id ).addEventListener('change', 
					function( event ) { getgraph(); });
		id = "term" + n;
		document.getElementById( id ).addEventListener('change', 
					function( event ) { getgraph(); });
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
	document.getElementById( "qq" ).addEventListener('change', function() {
		document.getElementById( "qqsingle" ).value = document.getElementById( "qq").value;
	});
	
	/* add/delete a line to the list of points */
	document.getElementById( "addpoint" ).addEventListener('click', function() { addline(); });
	document.getElementById( "delpoint" ).addEventListener('click', function() { delline(); });
	
	/* 
	 * clear contents of the log textbox if a base is selected
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
		el.addEventListener("click", function() { 
			document.getElementById( "logski" ).value = "";
			getgraph();
		});
	});
	
	document.getElementById( "logskix").addEventListener('change', function() { clrlog('x'); });
	document.getElementById( "logski").addEventListener('change', function() { clrlog(''); });
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
				el.addEventListener('click', function() { 
					document.getElementById( "single1" ).value = document.getElementById( "formula" + n ).value;
					document.getElementById( "inpval" ).focus();
				});
		}(n)
	}
	document.getElementById( "res" ).addEventListener('click', function() {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "tbl" ).addEventListener('click', function() {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "csv" ).addEventListener('click', function() {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "latex" ).addEventListener('click', function() {
		 document.getElementById( "singleform" ).submit(); });
	document.getElementById( "calcreset" ).addEventListener('click', function() {
		 document.getElementById( "inpval" ).value = ""; });
	document.getElementById( "posval" ).addEventListener('click', function() { 
		document.getElementById( "inpval").value = "1 2 3 4 5 6 7 8 9 10"; });
	document.getElementById( "negval" ).addEventListener('click', function() { 
		document.getElementById( "inpval" ).value = "-1 -2 -3 -4 -5 -6 -7 -8 -9 -10"; });
	document.getElementById( "urlclear" ).addEventListener('click', function() { 
		document.getElementById( "path").value = "";
	if (document.getElementById( "shortpath" ))
		document.getElementById( "shortpath" ).value = "";
		document.getElementById( "path" ).focus();
	});
	document.getElementById( "graphcopy" ).addEventListener('click', function( event ) { copyURL("path"); });
	if (document.getElementById("urlcopy") !== null)
		document.getElementById( "urlcopy" ).addEventListener('click', function( event ) { copyURL("shortpath"); });
	document.getElementById( "loadgraph").addEventListener('click', function( event ) { loadg(); });

});
