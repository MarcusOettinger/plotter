<?php
//
// plot.php: a function plotter (mostly), based on
// openPlaG (http://rechneronline.de/openPlaG/) by
// Juergen Kummer (GPL)
// ---------------------------------------------------
// Marcus Oettinger (www.oettinger-physisc.de)
// 06/2015
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
//   <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
// <script src="http://lib.familie-oettinger.net/min/?g=jssmall" type="text/javascript"></script>
//
?>
<!DOCTYPE html><html>
  <head><title>Plotter</title>
  <meta name="robots" content="index,follow">
  <meta name="content-language" content="en">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php echo $extopts; ?>
  <link rel="stylesheet" href="plotstyle.css" type="text/css">
  <script src="js/functionplotter.js" type="text/javascript"></script>
  <script>
    $(function() {
      n = 0; // Count Points to display
      getgraph();
      $( document ).tooltip();
      $( "#infobutton" ).click(function() {  $( "#info" ).toggle( ); });
      $( "#infobutton2" ).click(function() {  $( "#info2" ).toggle( ); });
      $( "#infobutton3" ).click(function() {  $( "#info3" ).toggle( ); });
      $( "#infobutton4" ).click(function() {  $( "#info4" ).toggle( ); });
      $( "#accordion" ).accordion({heightStyle: "content", autoHeight: false });
      $( "#dialog" ).dialog({width:600,autoOpen: false,
        buttons: [{text: "OK",click: function(){$( this ).dialog( "close" );}}],
        title: "Plot Settings", modal: false
      }).parent().appendTo($("#funcs")); // that's a really ugly hack (append Dialog to form, NOT to Body, which is the Default. If you don't, vlues inside the Dialog will not be submitted)
    });
    
    function addline(){
      n++;
      html = "<div id='Pdiv"+n+"' class='pline'>";
      html += "Name: <input style='width:40px' id='PName"+n+"' name='PName"+n+"' value='"+String.fromCharCode(n+79)+"'>";
      html += " at (x=<input style='width:40px' id='PX"+n+"' name='PX"+n+"' value='0'>";
      html += "/y=<input style='width:40px' id='PY"+n+"' name='PY"+n+"' value='0'>)";
      html += "</div>";

      $("#eof").before(html);
      $("#delpoint").css("display", "inline");
      if (n == 10) {
        $("#addpoint").css("display", "none");
      }
    }
    
    function delline() {
      $("#Pdiv"+n).remove();
      n--;
      $("#addpoint").css("display", "inline");
      if (n == 0) {
        $("#delpoint").css("display", "none");
      }
    }
  </script>
</head>
<body><div id="header"><a href="http://www.oettinger-physics.de">oettinger-physics.de</a></div>
<div id="plotpage"><div id="menubar">
    <a href="http://www.oettinger-physics.de">[home]</a> &nbsp;
    <a href="manual.html">[manual]</a> &nbsp;
    <a href="examples.html">[examples]</a></div>
  <h1>The function plotter...&nbsp;<img class="bt" id="infobutton" src="images/iconInfo.png"></h1>
  <div class="info"  id="info"><p>... is a simple tool to draw curves of functions
    (rsp. their derivatives or integrals) in cartesian coordinates. It is able to plot
    many common mathematical functions or expressions on a jpeg, png or gif image.</p>
    <p>Use the lower tab to calculate tables of function values (in csv or text
    format) and get a hyperlink to reproduce an image on the fly (using an URL or QRcode).
</p></div>
<div id="accordion">
<!-- 
================= accordion tab 1: function plotter ==========================
-->
  <h3>draw</h3>
  <div>
  <form id="funcs" method="post" action="<?php
      echo "function";
      if ($silent) echo "_silent";
      ?>.php" target="function" autocomplete="off" onsubmit="checkscroll()">
    <div style="display:inline-block; margin:5px;">
    <div id="plotcontrol">
      <input type="hidden" name="c" value="2" >
      <input type="image" src="images/iconPlot.png" title="draw plot" value="Draw" >
      <script type="text/javascript">
          document.write('<input type="image" src="images/iconReset.png" title="set default options" value="set default" onclick="standard()"  >');
      </script>
      <noscript><input type="reset" value="Reset" class="reset" ></noscript>
      <input type="image" src="images/iconGear.png" title="settings/options" value="Options" onclick="$('#dialog').dialog('open');" >
    </div>
    <!-- the plot in an iframe -->
    <script type="text/javascript"> document.write('<iframe id="function" name="function">Function Graph</iframe>');</script>
    <noscript><iframe id="function" name="function" src="function.php">Function Graph</iframe></noscript>
    <div id="quickcontrols">quadrants:
    <input  type="image" src="images/qAll.png" onclick="getalign(0)" >
    &nbsp;
    <input  type="image" src="images/qNE.png" onclick="getalign('a0')" >
    <input  type="image" src="images/qNW.png" onclick="getalign('a1')" >
    <input  type="image" src="images/qSW.png" onclick="getalign('a2')" >
    <input  type="image" src="images/qSE.png" onclick="getalign('a3')" >  
    &nbsp;
    <input  type="image" src="images/qN.png" onclick="getalign('b0')" > 
    <input  type="image" src="images/qW.png" onclick="getalign('b1')" > 
    <input  type="image" src="images/qS.png" onclick="getalign('b2')" > 
    <input  type="image" src="images/qE.png" onclick="getalign('b3')" > 
    &nbsp;
    size: <input style="width:50px" id="qsize" value="5" >
    </div>
    </div>

    <!-- right column -->
  <div style="text-align:center; display:inline-block;margin-top:25px;margin-left:auto;margin-right:auto;">
  <div style="display:block;vertical-align:top;text-align: left;">
  <fieldset class="function odd"><legend id="fl0">f(x)</legend>
    <input style="width:150px" id="formula1" name="formula1" value="x^2" >
    <span style="display:none" id="intc1">+C:<input id="cint1" name="cint1" style="width:50px" ></span>
    color #<input id="selfcol0" name="selfcol0" style="width:60px" value="ff8000" onchange="changeselfcol(0)" >
    <input style="width:50px;border-width:0px" readonly="readonly" id="selfcolbg0" >
     <div class="fndiv">show:
    <input type="radio" class="check" name="sint1" checked="checked" onclick="intclose(1)" value="0" >f(x)&nbsp;&nbsp;
    <input type="radio" class="check" name="sint1" onclick="intclose(1)" value="1">df/dx&nbsp;&nbsp;
    <input type="radio" class="check" name="sint1" onclick="intshow(1)" value="2" >&int;f(x)dx
    </div><div class="fndiv">
    range: <input id="ta1" name="ta1" style="width:30px" > to <input id="ta2" name="ta2" style="width:30px" >
  &nbsp;&nbsp;&nbsp;
    <?php linetype("con0"); ?>
    <input type="checkbox"  class="check" id="term1" name="term1" value="1" checked="checked" > legend
    </div>
  </fieldset>

  <fieldset class="function even"><legend id="fl1">g(x)</legend>
  <input style="width:150px" id="formula2" name="formula2" value="" >
  <span style="display:none" id="intc2">+C:<input id="cint2" name="cint2" style="width:50px" ></span>
  color #<input id="selfcol1" name="selfcol1" style="width:60px" value="a0b0c0" onchange="changeselfcol(1)">
  <input style="width:50px;border-width:0px" readonly="readonly" id="selfcolbg1">
  <div class="fndiv">show:
    <input type="radio" class="check" name="sint2" checked="checked" onclick="intclose(2)" value="0" >g(x)&nbsp;&nbsp;
    <input type="radio" class="check" name="sint2" onclick="intclose(2)" value="1">dg/dx&nbsp;&nbsp;
    <input type="radio" class="check" name="sint2" onclick="intshow(2)" value="2" >&int;g(x)dx
    </div><div class="fndiv">
  range: <input id="tb1" name="tb1" style="width:30px" > to <input id="tb2" name="tb2" style="width:30px" >
  &nbsp;&nbsp;&nbsp;
  <?php linetype("con1"); ?>
  <input type="checkbox"  class="check" id="term2" name="term2" value="1" checked="checked" > legend
  </div></fieldset>

  <fieldset class="function odd"><legend id="fl2">h(x)</legend>
  <input style="width:150px" id="formula3" name="formula3" value="" >
  <span style="display:none" id="intc3">+C:<input id="cint3" name="cint3" style="width:50px" ></span>
  color #<input id="selfcol2" name="selfcol2" style="width:60px" value="6080a0" onchange="changeselfcol(2)" >
  <input style="width:50px;border-width:0px" readonly="readonly" id="selfcolbg2" >
    <div class="fndiv">show:
    <input type="radio" class="check" name="sint3" checked="checked" onclick="intclose(3)" value="0" >h(x)&nbsp;&nbsp;
    <input type="radio" class="check" name="sint3" onclick="intclose(3)" value="1">dh/dx&nbsp;&nbsp;
    <input type="radio" class="check" name="sint3" onclick="intshow(3)" value="2" >&int;h(x)dx
    </div><div class="fndiv">
  range: <input id="tc1" name="tc1" style="width:30px" > to <input id="tc2" name="tc2" style="width:30px" >
  &nbsp;&nbsp;&nbsp;
  <?php linetype("con2"); ?>  
  <input type="checkbox"  class="check" id="term3" name="term3" value="1" checked="checked" > legend
  </div>
  </fieldset>
    <div class="smalldiv" style="width:380px;margin-top:20px;margin-bottom:10px;">
    <div style="float:right;">
    hull function= <input name="Y" id="Y" style="width:200px" value="Y">
    <input type="image" src="images/iconClose.png" onclick="document.getElementById('Y').value='Y';document.getElementById('Y').focus()" >
    </div>
  <div style="float:right;">substitute Q= <input id="qq" name="qq" style="width:200px" onchange="document.getElementById('qqsingle').value=document.getElementById('qq').value" >
  <input type="image" src="images/iconClose.png" onclick="document.getElementById('qq').value='';document.getElementById('qq').focus()" >
  </div>
  </div>
   

  <fieldset class="function"><legend id="pl">points&nbsp;<img class="bt" id="infobutton2" src="images/iconInfo.png"></legend>
  <div class="info" id="info2">Define named points to be displayed in the graph:<br>add or remove points
   with +/-. </div>
  <div style="text-align:right;margin:10px;">
    color #<input id="selfcol7" name="selfcol7" style="width:60px" value="335555" onchange="changeselfcol(7)" >
    <input style="width:50px;border-width:0px" readonly="readonly" id="selfcolbg7" >
    <input id="addpoint" type="image" src="images/iconPlus.png" value="Add" onclick="addline();">
    <input id="delpoint" type="image" src="images/iconMinus.png" value="Kill last" onclick="delline();">
  </div>
  <div id="eof" style="clear:both;margin:5px;">
  </div>
  </fieldset>
  </div> <!-- EOF lcolumn -->
  </div>

  
  <?php 
  //  ---------------------------------------------------------------------------------------------------------------
  // jquery-ui dialog to set options 
  //
  ?>
  <div id="dialog">
  <fieldset><legend style="font-size:1em;">plot properties</legend>
  <table>
  <tr>
  <td class="dtable">&nbsp;Width: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="width" name="width" value="500" > </td><td rowspan="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
  <td class="dtable">&nbsp;Height: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="height" name="height" value="500" ></td>
  <td style="text-align:right;" class="dtable">&nbsp;&nbsp;<label for="frame">border</label><input type="checkbox" class="check" id="frame" name="frame" value="1" ></td>
</tr><tr>
  <td class="dtable">&nbsp;x-axis from </td><td style="text-align:right" class="dtable"><input style="width:50px" id="rulex1" name="rulex1" value="-5" > </td><td class="dtable">&nbsp;to </td>
  <td style="text-align:right" class="dtable"><input style="width:50px" id="rulex2" name="rulex2" value="5" ></td>
  <td></td>
</tr><tr>
  <td class="dtable">&nbsp;y-axis from </td><td style="text-align:right" class="dtable"><input style="width:50px" id="ruley1" name="ruley1" value="-5" > </td><td class="dtable">&nbsp;to </td>
  <td style="text-align:right" class="dtable"><input style="width:50px" id="ruley2" name="ruley2" value="5" ></td>
  <td  style="text-align:right;" class="dtable"><label for="lines">axis lines</label> <input type="checkbox"  class="check" id="lines" name="lines" value="1" checked="checked" ></td>
</tr><tr>
  <td class="dtable">&nbsp;Intervals x-axis: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="intervalsx" name="intervalsx" value="10" > </td>
  <td class="dtable">&nbsp;y-axis: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="intervalsy" name="intervalsy" value="10" ></td>
  <td style="text-align:right;" class="dtable"><label for="numbers">caption</label> <input type="checkbox"  class="check" id="numbers" name="numbers" value="1" checked="checked" ></td>
</tr><tr>
  <td class="dtable">&nbsp;Grid lines: x-axis </td><td style="text-align:right" class="dtable"><input style="width:50px" id="gridx" name="gridx" value="20" > </td>
  <td class="dtable">&nbsp;y-axis </td><td style="text-align:right" class="dtable"><input style="width:50px" id="gridy" name="gridy" value="20" ></td>
  <td style="text-align:right;" class="dtable"><label for="grid">show grid </label><input type="checkbox" class="check" id="grid" name="grid" value="1" checked="checked" ></td>
</tr><tr>
  <td class="dtable">&nbsp;Dashes length: x-axis </td><td style="text-align:right" class="dtable"><input style="width:50px" id="linex" name="linex" value="5" > </td>
  <td class="dtable">&nbsp;y-axis </td><td style="text-align:right" class="dtable"><input style="width:50px" id="liney" name="liney" value="5" ></td>
  <td style="text-align:right;" class="dtable"><label for="dashes">dashes </label><input type="checkbox"  class="check" id="dashes" name="dashes" value="1" checked="checked" ></td>
</tr><tr>
  <td class="dtable">&nbsp;Decimal places: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="deci" name="deci" value="3" onchange="document.getElementById('decis').value=document.getElementById('deci').value" > </td>
  <td class="dtable">&nbsp;Gap at origin: </td><td style="text-align:right" class="dtable"><input style="width:50px" id="mid" name="mid" value="0" ></td>
  <td></td>
</tr><tr>
  <td class="dtable">&nbsp;Graph thickness: </td><td style="text-align:right" class="dtable"><input name="thick" id="thick" style="width:50px" value="2" > </td><td class="dtable">&nbsp;</td><td style="text-align:right" class="dtable">&nbsp;</td>
  <td></td>
  <td></td>
</tr>
  <tr>
  <td colspan="4" style="padding-top:15px;">&nbsp;Log. scale x:
  <input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" checked="checked" value="0" >No
  <input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="2" >2
  <input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="M_E" >e
  <input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="10" >10
  <input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="100" >100
  </td>
  <td colspan="2" class="dtable" style="text-align:center;padding-top:15px;">or <input name="logskix" id="logskix" style="width:50px" onclick="clrlog('x')" ></td>
  </tr>
  <tr>
  <td colspan="4" class="dtable">&nbsp;Log. scale y:
  <input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" checked="checked" value="0" >No
  <input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="2" >2
  <input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="M_E" >e
  <input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="10" >10
  <input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="100" >100
  </td>
  <td colspan="2" class="dtable" style="text-align:center">or <input name="logski" id="logski" style="width:50px" onclick="clrlog('')" ></td>
  </tr>
  </table>
  </fieldset>

  <fieldset><legend style="font-size:1em;">colors</legend>
  <div class="smalldiv">
      background #<input id="selfcol3" name="selfcol3" style="width:60px" value="ffffff" onchange="changeselfcol(3)" > <input class="coldisplay" readonly="readonly" id="selfcolbg3" >
      caption #<input id="selfcol4" name="selfcol4" style="width:60px" value="141414" onchange="changeselfcol(4)" > <input class="coldisplay" readonly="readonly" id="selfcolbg4" >
      grid #<input id="selfcol5" name="selfcol5" style="width:60px" value="f2f2f2" onchange="changeselfcol(5)" > <input class="coldisplay" readonly="readonly" id="selfcolbg5" >
      gap #<input id="selfcol6" name="selfcol6" style="width:60px" value="ffffff" onchange="changeselfcol(6)" > <input class="coldisplay" readonly="readonly" id="selfcolbg6" >
  </div></fieldset>
  <div class="topborder" style="text-align:left;">
  <fieldset><legend>image properties</legend>
  <div class="smalldiv">
         Image type:<select id="filetype" name="filetype">
            <option value="0">png</option>
            <option value="1">gif</option>
            <option value="2">jpeg</option>
      </select>
      <label for="anti">antialiasing</label><input type="checkbox" class="check" id="anti" name="anti" value="1" checked="checked" >
      <label for="poles">poles</label><input type="checkbox" class="check" id="pol" name="pol" value="1" checked="checked" >
  </div><div class="smalldiv"> as
  <select id="bf" name="bf">
  <option value="1">Lines in background</option>
  <option value="2">Lines in foreground</option>
  <option value="0">All lines off</option></select>
  </div>
  <div class="smalldiv">
  <label>Gamma:</label> <input id="gamma" name="gamma" style="width:40px" value="1" >
  <label>Brightness:</label> <input id="bri" name="bri" style="width:40px" value="0" >
  <label>Contrast:</label> <input id="cont" name="cont" style="width:40px" value="0" >
  <label>Rotation:</label>  <input id="rotate" name="rotate" style="width:40px" value="0" >&#176;
  </div><div class="smalldiv" style="width:100%;">
  <fieldset style="border:0px;border-top:1px solid #eeeeee;"><legend style="font-weight:normal;">apply filter</legend>
  <label for="emb">emboss</label><input type="checkbox" class="check" id="emb" name="emb" value="1" >
  <label for="blur">blur</label><input type="checkbox" class="check" id="blur" name="blur" value="1" >
  <label for="neg">negative</label><input type="checkbox" class="check" id="neg" name="neg" value="1" >
  <label for="gray">greyscale</label><input type="checkbox" class="check" id="gray" name="gray" value="1" >
  <label for="mean">sketchy</label><input type="checkbox" class="check" id="mean" name="mean" value="1" >
  <label for="edge">edges</label><input type="checkbox" class="check" id="edge" name="edge" value="1" >
  </fieldset>
  </div>
  <div class="smalldiv"><label for="errors">show errors in image</label><input type="checkbox"  class="check" id="errors" name="errors" value="1" checked="checked" >
  </div>

  </fieldset>
  </div></div>

  </form>
</div>
<!-- 
================= accordion tab 2: calc ==============================
-->
<h3>Calculate, Link &amp; Save</h3><div>
  <div id="calculate">
  <p>Enter an expression and some variable values below and hit calculate to produce
  your data <img class="bt" id="infobutton3" src="images/iconInfo.png"></p>
  <div class="info"  id="info3">
  <p>Enter some variable values and a formula to be calculated, 
       functions plotted above can be used by the buttons labeled f(x), g(x) and h(x).
       Variables will usually be simple numbers, separate multiple input values (x)
       by spaces.</p>
  <p>Select one of the checkboxes to chose a format for the results:</p>
  <ul style="text-align:left;"><li>results - a string of function values, separated by spaces</li>
         <li>table - a simple html table including variable values</li>
         <li>csv - colon separated values including variable values</li>
         <li>latex - code to layout a table including variable values in laTeX</li>
  </ul></div>

  <div class="width" style="height:10px;"></div>
  <form method="post" action="single.php" id="singleform" target="single" autocomplete="off" style="padding:5px 0px;margin:0px;margin-left:auto;margin-right:auto;" class="width">
  <fieldset><legend>calculate discrete values: </legend>
  <table class="width"> <tr>
  <td style="vertical-align:middle">&nbsp; Function: </td>
  <td style="text-align:right;"><input style="width:250px" id="single1" name="single1" value="" ></td><td  style="text-align:right;">
  <input type="button" value=" f(x) " onclick="document.getElementById('single1').value=document.getElementById('formula1').value;document.getElementById('inpval').focus()" >
  <input type="button" value=" g(x) " onclick="document.getElementById('single1').value=document.getElementById('formula2').value;document.getElementById('inpval').focus()" >
  <input type="button" value=" h(x) " onclick="document.getElementById('single1').value=document.getElementById('formula3').value;document.getElementById('inpval').focus()" >
  </td></tr><tr>
  <td style="text-align:right;vertical-align:middle" title="Separate multiple values with spaces"> 
    Input value(s):</td>
  <td  style="text-align:right;"> <input style="width:250px" id="inpval" name="inpval" value="" >
  </td><td>
      <input type="reset" value=" C " onclick="document.getElementById('single1').focus()" > &nbsp;
      <input type="button" value="+10" onclick="document.getElementById('inpval').value='1 2 3 4 5 6 7 8 9 10'" >
      <input type="button" value="-10" onclick="document.getElementById('inpval').value='-1 -2 -3 -4 -5 -6 -7 -8 -9 -10'" >
  </td></tr>
  </table>
  <input type="hidden" name="decis" id="decis" value="3" >
      <div style="text-align:left">
      <div class="smalldiv"><input type="submit" value=" Calculate " class="draw" > &nbsp;</div>
      <div class="smalldiv">
      <label for="res">results</label>
      <input type="radio" class="check" name="format" id="res" value="0" >
      <label for="tbl">table</label>
      <input type="radio" class="check" name="format" id="tbl" value="1" checked="checked" >
      <label for="csv">csv</label>
      <input type="radio" class="check" name="format" id="csv" value="2" >      
      <label for="latex">latex</label>
      <input type="radio" class="check" name="format" id="latex" value="3" >      
      </div>
      </div>
  <iframe class="calcframe" name="single" src="single.php">Result display</iframe> &nbsp;
  <input type="hidden" name="c" value="2" >
  <input type="hidden" id="qqsingle" name="qqsingle" value="" >
  </fieldset></form>
  <br>
  <fieldset><legend>Load &amp; save: </legend>
  <p>Use the plotter above to draw a function, format it as needed
  and enjoy your shiny new Image file.</p>
  <p>In case you want to link to the graph or cannot use
  one of the image formats provided, the graph in the plotter
  can be reproduced any time by the following URL 
  <img class="bt" id="infobutton4" src="images/iconInfo.png"></p>
  <div class="info"  id="info4"><p>
    Every image drawn by plotter can be dynamically reproduced by a simple
    call to plotter's image creating part (graph.php). All the information needed is placed
    in a query string appended to its URL.</p>
    <?php echo $short_msg; ?>
    <p>Of course, this Information can be used
    to load an existing plot, e.g. to modify some of its Settings - that is what the button
    labeled <i>Load graph</i> is for.
  </p></div>
  <table style="width:100%;">
  <?php if (isset($useShortening) && $useShortening == true) {  ?>
         <tr><td><textarea id="shortpath" cols="60" rows="1"></textarea>
         <?php if (isset($useQR) && $useQR = true) {  ?>
            <img src="" id="QRcode" alt="URI QR-encoded"></td></tr>    
          <?php } ?>
  <?php } ?>
   <tr><td>full URL to your image file<br>
   <textarea id="path" class="calcframe"></textarea><br>
        <input type="button" value=" Clear " class="reset" onclick="document.getElementById('path').value='';document.getElementById('shortpath').value='';document.getElementById('path').focus()">
        <input type="button" value=" Select " class="reset" onclick="document.getElementById('path').focus();document.getElementById('path').select()">
    <input type="button" value=" Load Graph " class="draw" onclick="loadg()" >
    </td>
  </tr></table></fieldset>
  </div>
</div><?php //-- end of tab 3 --- ?>
</div>
<?php // ------------ end of accordion ------------ ?>
</div><!-- #plotpage -->
</body></html>
