/*
modified by Marcus Oettinger 09/2019
 - CSP - capable
 - reworked for smoother Color handling
-------------------------------------------------------------------------

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

function getgraph() {
	$("#funcs").submit();
	intsopen();
	$("#formula1").focus();
                 for (i=0; i<8; i++) {
		changeselfcol(i);
	}
}


function setback() {
	if (confirm('Delete all changes?')) {
		$('#funcs').reset();
		getgraph();
	}
}

function intsopen() {
	if($("#sint1").prop("checked") == true)
         	intshow(1);
         else
         	intclose(1);
	if($("#sint2").prop("checked") == true)
         	intshow(2);
         else
         	intclose(2);
	if($("#sint3").prop("checked") == true)
         	intshow(3);
         else
         	intclose(3);
}


function intshow(x) {
	$("#formula"+x).removeClass("w190");
	$("#intc"+x).removeClass("nodisplay");
	$("#formula"+x).addClass("w120");
	$("#intc"+x).addClass("display");
	$("#cint"+x).focus();
}

function intclose(x) {
	$("#formula"+x).removeClass("w120");
	$("#intc"+x).removeClass("display");
	$("#formula"+x).addClass("w190");
	$("#intc"+x).addClass("nodisplay");
}

/*
 * loadg(): load a graph using the query-string in the textarea
 * at the bottom
 */
function loadg() {
	var pp=$("#path").val();
	if(!pp) return false;

	$("#formula1").val(decodeURIComponent(pp.substring(pp.indexOf("&a1=")+4,pp.indexOf("&a2="))));
	$("#formula2").val(decodeURIComponent(pp.substring(pp.indexOf("&a2=")+4,pp.indexOf("&a3="))));
	$("#formula3").val(decodeURIComponent(pp.substring(pp.indexOf("&a3=")+4,pp.indexOf("&a7="))));
	$("#term1").prop("checked", pp.substring(pp.indexOf("&a7=")+4,pp.indexOf("&a8=")) == 1);
	$("#term2").prop("checked", pp.substring(pp.indexOf("&a8=")+4,pp.indexOf("&a9=")) == 1);
	$("#term3").prop("checked", pp.substring(pp.indexOf("&a9=")+4,pp.indexOf("&b0=")) == 1);
	$("#width").val(pp.substring(pp.indexOf("&b0=")+4,pp.indexOf("&b1=")));
	$("#height").val(pp.substring(pp.indexOf("&b1=")+4,pp.indexOf("&b2=")));
	$("#rulex1").val(pp.substring(pp.indexOf("&b2=")+4,pp.indexOf("&b3=")));
	$("#rulex2").val(pp.substring(pp.indexOf("&b3=")+4,pp.indexOf("&b4=")));
	$("#ruley1").val(pp.substring(pp.indexOf("&b4=")+4,pp.indexOf("&b5=")));
	$("#ruley2").val(pp.substring(pp.indexOf("&b5=")+4,pp.indexOf("&b6=")));
	$("#intervalsx").val(pp.substring(pp.indexOf("&b6=")+4,pp.indexOf("&b7=")));
	$("#intervalsy").val(pp.substring(pp.indexOf("&b7=")+4,pp.indexOf("&b8=")));
	$("#linex").val(pp.substring(pp.indexOf("&b8=")+4,pp.indexOf("&b9=")));
	$("#liney").val(pp.substring(pp.indexOf("&b9=")+4,pp.indexOf("&c0=")));
	$("#deci").val(pp.substring(pp.indexOf("&c0=")+4,pp.indexOf("&c1=")));
	$("#mid").val(pp.substring(pp.indexOf("&c1=")+4,pp.indexOf("&c2=")));
	$("#lines").prop("checked", pp.substring(pp.indexOf("&c2=")+4,pp.indexOf("&c3=")) == 1);
	$("#numbers").prop("checked", pp.substring(pp.indexOf("&c3=")+4,pp.indexOf("&c4=")) == 1);
	$("#dashes").prop("checked", pp.substring(pp.indexOf("&c4=")+4,pp.indexOf("&c5=")) == 1);
	$("#frame").prop("checked", pp.substring(pp.indexOf("&c5=")+4,pp.indexOf("&c6=")) == 1);
	$("#errors").prop("checked", pp.substring(pp.indexOf("&c6=")+4,pp.indexOf("&c7=")) == 1);

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

	$("#grid").prop("checked", pp.substring(pp.indexOf("&d0=")+4,pp.indexOf("&d1=")) == 1);
	$("#gridx").val(pp.substring(pp.indexOf("&d1=")+4,pp.indexOf("&d2=")));
	$("#gridy").val(pp.substring(pp.indexOf("&d2=")+4,pp.indexOf("&d3=")));

	var jslogskx=pp.substring(pp.indexOf("&g5=")+4,pp.indexOf("&g6="));
	if(jslogskx!=0 && jslogskx!=2 && jslogskx!="M_E" && jslogskx!=10 && jslogskx!=100) {
		$("#logskix").val(jslogskx);
		clrlog('x');
	} else {
		$("#logskix").val("");
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
		$("#logski").val(jslogsk);
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

	$("#ta1").value=pp.substring(pp.indexOf("&d4=")+4,pp.indexOf("&d5="));
	$("#ta2").val(pp.substring(pp.indexOf("&d5=")+4,pp.indexOf("&d6=")));
	$("#tb1").val(pp.substring(pp.indexOf("&d6=")+4,pp.indexOf("&d7=")));
	$("#tb2").val(pp.substring(pp.indexOf("&d7=")+4,pp.indexOf("&d8=")));
	$("#tc1").val(pp.substring(pp.indexOf("&d8=")+4,pp.indexOf("&d9=")));
	$("#tc2").val(pp.substring(pp.indexOf("&d9=")+4,pp.indexOf("&e0=")));
	$("#cint1").val(pp.substring(pp.indexOf("&e0=")+4,pp.indexOf("&e1=")));
	$("#cint2").val(pp.substring(pp.indexOf("&e1=")+4,pp.indexOf("&e2=")));
	$("#cint3").val(pp.substring(pp.indexOf("&e2=")+4,pp.indexOf("&e3=")));
	$("#qq").val(pp.substring(pp.indexOf("&e3=")+4,pp.indexOf("&e4=")));
	$("#bg").val(pp.substring(pp.indexOf("&e4=")+4,pp.indexOf("&e5=")));
	$("#gapc").val(pp.substring(pp.indexOf("&e5=")+4,pp.indexOf("&e6=")));
	$("#capt").val(pp.substring(pp.indexOf("&e6=")+4,pp.indexOf("&e7=")));
	$("#linec").val(pp.substring(pp.indexOf("&e7=")+4,pp.indexOf("&e8=")));
	$("#con0").val(pp.substring(pp.indexOf("&e8=")+4,pp.indexOf("&e9=")));
	$("#con1").val(pp.substring(pp.indexOf("&e9=")+4,pp.indexOf("&f0=")));
	$("#con2").val(pp.substring(pp.indexOf("&f0=")+4,pp.indexOf("&f1=")));
	$("#anti").prop("checked", pp.substring(pp.indexOf("&f1=")+4,pp.indexOf("&f2=")) == 1);
	$("#gamma").val(pp.substring(pp.indexOf("&f2=")+4,pp.indexOf("&f3=")));
	$("#bri").val(pp.substring(pp.indexOf("&f3=")+4,pp.indexOf("&f4=")));
	$("#cont").val(pp.substring(pp.indexOf("&f4=")+4,pp.indexOf("&f5=")));
	$("#emb").prop("checked", pp.substring(pp.indexOf("&f5=")+4,pp.indexOf("&f6=")) == 1);
	$("#blur").prop("checked", pp.substring(pp.indexOf("&f6=")+4,pp.indexOf("&f7=")) == 1);
	$("#neg").prop("checked", pp.substring(pp.indexOf("&f7=")+4,pp.indexOf("&f8=")) == 1);
	$("#gray").prop("checked", pp.substring(pp.indexOf("&f8=")+4,pp.indexOf("&f9=")) == 1);
	$("#mean").prop("checked", pp.substring(pp.indexOf("&f9=")+4,pp.indexOf("&g0=")) == 1);
	$("#edge").prop("checked", pp.substring(pp.indexOf("&g0=")+4,pp.indexOf("&g1=")) == 1);
	$("#bf").val(pp.substring(pp.indexOf("&g1=")+4,pp.indexOf("&g2=")));
	$("#pol").prop("checked", pp.substring(pp.indexOf("&g2=")+4,pp.indexOf("&g3=")) == 1);
	$("#rotate").val(pp.substring(pp.indexOf("&g3=")+4,pp.indexOf("&g4=")));
	$("#filetype").val(pp.substring(pp.indexOf("&g4=")+4,pp.indexOf("&g5=")));
	$("#Y").val(pp.substring(pp.indexOf("&g6=")+4,pp.indexOf("&g7=")));
	$("#selfcol0").val(pp.substring(pp.indexOf("&g7=")+4,pp.indexOf("&g8=")));
	$("#selfcol1").val(pp.substring(pp.indexOf("&g8=")+4,pp.indexOf("&g9=")));
	$("#selfcol2").val(pp.substring(pp.indexOf("&g9=")+4,pp.indexOf("&h0=")));
	$("#thick").val(pp.substring(pp.indexOf("&h0=")+4,pp.indexOf("&z")));

	getgraph();
};

function clrlog(a) {
	for(var i=0;i<5;i++)
		$("#logsk"+a)[i].checked=false;
}


/*
 * standard(): reset plot options to defaults
 */
function standard() {
// TODO: colors!!
	if (confirm('Set to standard display values?')) {
		$("#filetype").val(0);
		$("#bg").val(14);
		$("#capt").val(13);
		$("#linec").val(12);
		$("#gapc").val(14);
		$("#grid").checked=true;
		$("#lines").prop("checked", true);
		$("#numbers").prop("checked", true);
		$("#dashes").prop("checked", true);
		$("#frame").prop("checked", false);
		$("#errors").prop("checked", true);
		$("#anti").prop("checked", true);
		$("#pol").prop("checked", true);
		$("#bf").val(1);
		$("#gamma").val(1);
		$("#bri").val(0);
		$("#cont").val(0);
		$("#rotate").val(0);
		$("#emb").prop("checked", false);
		$("#blur").prop("checked", false);
		$("#neg").prop("checked", false);
		$("#gray").prop("checked", false);
		$("#mean").prop("checked", false);
		$("#edge").prop("checked", false);
		$("#width").val(500);
		$("#height").val(500);
		$("#thick").val(1);
		getgraph();
	}
}

/*
 * getalign: set ranges for different quadrants
 */
function getalign(x) {
	var v = $("#qsize").val().replace(",",".");
	$("#rulex1").val(-v);
	$("#rulex2").val(v);
	$("#ruley1").val(-v);
	$("#ruley2").val(v);
	if(x=="a0") {
		$("#rulex1").val(0);
		$("#rulex2").val(2*v);
		$("#ruley1").val(0);
		$("#ruley2").val(2*v);
	} else if(x=="a1") {
		$("#rulex1").val(-2*v);
		$("#rulex2").val(0);
		$("#ruley1").val(0);
		$("#ruley2").val(2*v);
	} else if(x=="a2") {
		$("#rulex1").val(-2*v);
		$("#rulex2").val(0);
		$("#ruley1").val(-2*v);
		$("#ruley2").val(0);
	} else if(x=="a3") {
		$("#rulex1").val(0);
		$("#rulex2").val(2*v);
		$("#ruley1").val(-2*v);
		$("#ruley2").val(0);
	} else if(x=="b0") {
		$("#ruley1").val(0);
		$("#ruley2").val(2*v);
	} else if(x=="b1") {
		$("#rulex1").val(-2*v);
		$("#rulex2").val(0);
	} else if(x=="b2") {
		$("#ruley1").val(-2*v);
		$("#ruley2").val(0);
	} else if(x=="b3") {
		$("#rulex1").val(0);
		$("#rulex2").val(2*v);
	}
	getgraph();
}

function changeselfcol(x) {
	$("#selfcolbg"+x).css("backgroundColor", '#' + $("#selfcol"+x).val());
	if (x<3) {
		$("#fl"+x).css("color", '#' + $("#selfcol"+x).val());
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

    
    /* add a new point - set name and Position
     */
    function addline(){
	n++;
	html = "<div id='Pdiv"+n+"' class='pline'>";
	html += "Name: <input class='w40' id='PName"+n+"' name='PName"+n+"' value='"+String.fromCharCode(n+79)+"'>";
	html += " at (x=<input class='w40' id='PX"+n+"' name='PX"+n+"' value='0'>";
	html += "/y=<input class='w40' id='PY"+n+"' name='PY"+n+"' value='0'>)";
	html += "</div>";

	$("#eof").before(html);
	$("#delpoint").css("display", "inline");
	if (n == 10) { $("#addpoint").css("display", "none"); }
    }

    /* remove the last point (bottom of the list)
   */
    function delline() {
	$("#Pdiv"+n).remove();
	n--;
	$("#addpoint").css("display", "inline");
	if (n == 0) { $("#delpoint").css("display", "none"); }
    }
    
$(function() {
	n = 0; // Count Points to display
	getgraph();
	$( document ).tooltip();
	$( "#infobutton1" ).click(function() {  $( "#info1" ).toggle( ); });
	$( "#infobutton2" ).click(function() {  $( "#info2" ).toggle( ); });
	$( "#infobutton3" ).click(function() {  $( "#info3" ).toggle( ); });
	$( "#infobutton4" ).click(function() {  $( "#info4" ).toggle( ); });
	$( "#accordion" ).accordion({heightStyle: "content", autoHeight: false });
	$( "#dialog" ).dialog({width:600,autoOpen: false,
	        buttons: [{text: "OK",click: function(){$( this ).dialog( "close" );}}],
	        title: "Plot options", modal: false
	}).parent().appendTo($("#funcs")); 
	/*
	 *  that's a really ugly hack (append dialog to form, NOT to body, 
	 * which is the default. If you don't, values inside the dialog will not be submitted!)
	 */
	
	$( "#submit" ).click(function( event ) { $( "#funcs" ).submit(); });
	$( "#inputReset" ).click(function( event ) {
		standard() ;
		event.preventDefault();
	});
	$( "#opendialog" ).click(function( event ) { 
		$('#dialog').dialog('open');
		event.preventDefault();
	});
	
	/* switch between quadrants displayed */
	$( "#areset" ).click(function( event ) { getalign( 0 ); });
	$( "#a0" ).click(function( event ) { getalign( 'a0' ); });
	$( "#a1" ).click(function( event ) { getalign( 'a1' ); });
	$( "#a2" ).click(function( event ) { getalign( 'a2' ); });
	$( "#a3" ).click(function( event ) { getalign( 'a3' ); });
	$( "#b0" ).click(function( event ) { getalign( 'b0' ); });
	$( "#b1" ).click(function( event ) { getalign( 'b1' ); });
	$( "#b2" ).click(function( event ) { getalign( 'b2' ); });
	$( "#b3" ).click(function( event ) { getalign( 'b3' ); });
	
	/* handle integration constant - hide the input if f(x) or
	 * derivative selected */
	$( "#sint1f" ).click(function( event ) { intclose(1); });
	$( "#sint1d" ).click(function( event ) { intclose(1); });
	$( "#sint1i" ).click(function( event ) { intshow(1); });
	$( "#sint2f" ).click(function( event ) { intclose(2); });
	$( "#sint2d" ).click(function( event ) { intclose(2); });
	$( "#sint2i" ).click(function( event ) { intshow(2); });
	$( "#sint3f" ).click(function( event ) { intclose(3); });
	$( "#sint3d" ).click(function( event ) { intclose(3); });
	$( "#sint3i" ).click(function( event ) { intshow(3); });
	
	$( "#selfcol0" ).change(function( event ) { changeselfcol(0); });
	$( "#selfcol1" ).change(function( event ) { changeselfcol(1); });
	$( "#selfcol2" ).change(function( event ) { changeselfcol(2); });
	$( "#selfcol3" ).change(function( event ) { changeselfcol(3); });
	$( "#selfcol4" ).change(function( event ) { changeselfcol(4); });
	$( "#selfcol5" ).change(function( event ) { changeselfcol(5); });
	$( "#selfcol6" ).change(function( event ) { changeselfcol(6); });
	$( "#selfcol7" ).change(function( event ) { changeselfcol(7); });
	
	$( "#clearhull" ).click(function( event ) {
		$("#Y").val("Y");
		$("#Y").focus();
	});
	$( "#clearsubst" ).click(function( event ) {
		$("#qq").val("");
		$("#qq").focus();
	});
	$( "#qq" ).change(function( event ) {
		$("#qqsingle").val( $("#qq").val() );
	});
	
	/* add/delete a line to the list of points */
	$( "#addpoint" ).click(function( event ) { addline(); });
	$( "#delpoint" ).click(function( event ) { delline(); });
	
	/* clear contents of the log textbox if a base is selected */
	$(".logskx").each(function () {
		var cb = this;
		cb.on( "click", function() {
			$('#logskix').val("");
		});
	});
	$(".logsk").each(function () {
		var cb = this;
		cb.on( "click", function() {
			$('#logski').val("");
		});
	});
	$("#logskix").change(function( event ) { clrlog('x'); });
	$("#logski").change(function( event ) { clrlog(''); });
	$("#deci").change(function( event ) { $('#decis').val($('#deci').val()); });
	
	/*
	 * controls in the lower accordion tab: calculations,
	 * clear/select/load graph via url
	 */
	$( "#calc1" ).click(function( event ) {
		$( "#single1" ).val( $("#formula1").val());
		$("#inpval").focus();
	});
	$( "#calc2" ).click(function( event ) {
		$( "#single1" ).val( $("#formula2").val());
		$("#inpval").focus();
	});
	$( "#calc3" ).click(function( event ) {
		$( "#single1" ).val( $("#formula3").val());
		$("#inpval").focus();
	});

	$("#calcreset").click(function( event ) { $("#single1").focus(); });
	$("#posval").click(function( event ) { $("#inpval").val("1 2 3 4 5 6 7 8 9 10"); });
	$("#negval").click(function( event ) { $("#inpval").val("-1 -2 -3 -4 -5 -6 -7 -8 -9 -10"); });
	$("#urlclear").click(function( event ) { 
		$("#path").val("");
		$("#shortpath").val("");
		$("#path").focus();
	});
	$("#urlselect").click(function( event ) {
		$("#path").focus();
		$("#path").select(); 
	});
	$("#urlcopy").click(function( event ) {	copyURL(); });
	$("#loadgraph").click(function( event ) { loadg(); });

});
