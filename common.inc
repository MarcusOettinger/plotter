<?php
//
// plot.php: a function plotter (mostly) based on
// openPlaG (http://rechneronline.de/openPlaG/) by
// Juergen Kummer (GPL)
// ---------------------------------------------------
// Marcus Oettinger (www.oettinger-physisc.de)
// 08/2021
//  * adding a close button to info divs
//  * adding CSP headers, removing library dependencies
//  * removed onchange/onclick entries to allow for reasonable CSP-settings
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
?>
<!DOCTYPE html><html><head><title>Plotter</title>
  <meta name="robots" content="index,follow">
  <meta name="content-language" content="en">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <meta name="description" lang="en" content="Plotter: a function plotter running at oettinger-physics.de. Create curves of mathematical functions on the fly.">
  <meta name="keywords" lang="en" content="oettinger-physics, plotter, function, curves, mathematics">
  <meta name="description" lang="de" content="Plotter: Kurven von Funktionen bei oettinger-physics.de. Erstellung mathematischer Kurven.">
  <meta name="keywords" lang="de"  content="oettinger-physics, plotter, Funktionen, Kurven, Mathematik">
  <?php echo $extopts; ?>
  <link rel="stylesheet" href="include/plotstyle.css" type="text/css">
  <link rel="stylesheet" href="include/plotterui.css" type="text/css">
  <script src="js/functionplotter.js" type="text/javascript"></script>
  <script src="js/plotterui.js" type="text/javascript"></script>
</head>
<body><div id="header"><?php echo $pagetitle; ?></div>
<div id="plotpage"><div id="menubar">
    <a href="https://www.oettinger-physics.de/index.html">[home]</a> &nbsp;
    <a href="manual.html">[manual]</a> &nbsp;
    <a href="examples.html">[examples]</a>
  </div><!-- menubar -->
  <h1 class="noprinting">The function plotter...&nbsp;<img class="bt" id="infobutton1" src="images/iconInfo.png"></h1>
  <div class="info" id="info1">
    <img class="bt" id="closeinfo1" src="images/iconClose.png">
    <p>... is a simple tool to plot curves of functions
    (rsp. their derivatives or integrals) in cartesian coordinates. Use it to to display
    common mathematical functions or expressions as jpeg, png or gif image.</p>
    <p>The lower tab allows to calculate tables of function values (in csv, text or laTeX
    format) and get a hyperlink to reproduce an image on the fly (using an URL or QRcode).
    </p>
  </div><!-- info1 -->

<div id="accordion">
<button class="acccordionbutton">draw</button>
<div><!-- accordion content -->
<?php
// ------------ begin of accordion ------------
// This is the first accordion tab displaying the plot and the most common controls.
//
// main page: simple plot controls and point interface
  include("modules/mainplot.inc");
//
// jquery-ui dialog to set further options 
  include("modules/options.inc");
//
?>
</div><!-- accordion tab 1 -->
<button class="acccordionbutton">Calculate, Link &amp; Save</button>
<div>
<?php
// lower accordion tab: calculate, load, save 
  include("modules/calculate.inc"); 
// ------------ end of accordion ------------ ?>
</div><!-- accordion tab2 -->
</div><!-- accordion -->
</div><!-- #plotpage -->
</body></html>
