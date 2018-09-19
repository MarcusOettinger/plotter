<?php
//
// plot.php: a function plotter (mostly), based on
// openPlaG (http://rechneronline.de/openPlaG/) by
// Juergen Kummer (GPL)
// ---------------------------------------------------
// Marcus Oettinger (www.oettinger-physisc.de)
// 09/2018
//  * polished UI (options in a jquery-ui dialog, colors and icons)
//  * replaced some tables by divs to get the interface more responsive
//  * replaced color setting mechanism
//  * restructured display (grouping elements by some sort of logic)
// Functional changes:
//  * added the possibility to plot up to 10 named points (e.g. maxima or such)
//  * added a shortlink (a la tinyurl) for URL with the bloated query string
//  * added a workaround for gdlib packages w/o imageantialias (debian, ubuntu
//    maybe others?)
// License: GPL
//
//
//
?>
<!DOCTYPE html><html>
  <head><title>Plotter</title>
  <meta name="robots" content="index,follow">
  <meta name="content-language" content="en">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php echo $extopts; ?>
  <link rel="stylesheet" href="include/plotstyle.min.css" type="text/css">
  <script src="js/functionplotter.min.js" type="text/javascript"></script>
  <script>
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
    });
    
    /* add a new point - set name and Position
     */
    function addline(){
	n++;
	html = "<div id='Pdiv"+n+"' class='pline'>";
	html += "Name: <input style='width:40px' id='PName"+n+"' name='PName"+n+"' value='"+String.fromCharCode(n+79)+"'>";
	html += " at (x=<input style='width:40px' id='PX"+n+"' name='PX"+n+"' value='0'>";
	html += "/y=<input style='width:40px' id='PY"+n+"' name='PY"+n+"' value='0'>)";
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
  </script>
</head>
<body><div id="header"><?php echo $pagetitle; ?></div>
<div id="plotpage"><div id="menubar">
    <a href="http://www.oettinger-physics.de">[home]</a> &nbsp;
    <a href="manual.html">[manual]</a> &nbsp;
    <a href="examples.html">[examples]</a></div>
  <h1>The function plotter...&nbsp;<img class="bt" id="infobutton1" src="images/iconInfo.png"></h1>
  <div class="info"  id="info1"><p>... is a simple tool to plot curves of functions
    (rsp. their derivatives or integrals) in cartesian coordinates. Use it to to display
    common mathematical functions or expressions as jpeg, png or gif image.</p>
    <p>The lower tab allows to calculate tables of function values (in csv, text or laTeX
    format) and get a hyperlink to reproduce an image on the fly (using an URL or QRcode).
</p></div>
<div id="accordion"><h3>draw</h3><div>
<?php
// ------------ begin of accordion ------------
  // This is the first accordion tab displaying the plot itself and the most common controls.
  //
  // main page: simple plot controls and points
  include("modules/mainplot.inc");
  //
  // jquery-ui dialog to set further options 
  include("modules/options.inc");
  //
  // lower accordion tab: calculate, load, save 
  include("modules/calculate.inc"); 
// ------------ end of accordion ------------ ?>
</div>
</div><!-- #plotpage -->
</body></html>