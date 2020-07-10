<?php
//
// mainplot.inc: 
// This is the main part of the GUI: display a plot in an <img> on the left,
// the most common controls and a a list of named points to plot on the right.
// This one should degrade nicely (BUT ugly, of course...)
// M. Oettinger
// 06/2020: conversion of tooltips/accordion to get rid of jquery-ui
// 09/2019: removed inline scripts and styles
// 12/2015: added an option to change variable Name (e.g. for use of t instead of x in time domain)
//
?>
<form id="funcs" method="post" action="<?php
      echo "function";if ($silent) echo "_silent";?>.php"
      target="function" autocomplete="off">

<?php 
//
// the left column containing the plot in an iframe, 
// main buttons and quadrant controls
// --------------------------------------------------
?>
<div id="lcol">
  <div id="plotcontrol">
  <input type="hidden" name="c" value="2" >
  <input id="submit" type="image" src="images/iconPlot.png" data-tooltip="redraw plot" value="Draw" >
  <input type="image" id="inputReset" src="images/iconReset.png" data-tooltip="reset to default settings" value="set default">
  <input type="image" id="open-dialog" src="images/iconGear.png" data-tooltip="settings/options" value="Options">
  </div>
  <iframe id="function" name="function">Function Graph</iframe>
  <div id="quickcontrols">quadrants:
    <input type="image" id="areset" src="images/qAll.png">
    &nbsp;
    <input type="image" id="a0" src="images/qNE.png">
    <input type="image" id="a1" src="images/qNW.png">
    <input type="image" id="a2" src="images/qSW.png">
    <input type="image" id="a3" src="images/qSE.png">  
    &nbsp;
    <input type="image" id="b0" src="images/qN.png"> 
    <input type="image" id="b1" src="images/qW.png"> 
    <input type="image" id="b2" src="images/qS.png"> 
    <input type="image" id="b3" src="images/qE.png"> 
    &nbsp;
    size: <input class="w30" id="qsize" value="5" >
  </div>
</div><!-- lcol -->
<? 
//  
// the right column with function inputs
// -----------------------------------------
?>
<div id="rcol">
  <div class="displayblock topleftalign">
  <fieldset class="function odd"><legend id="fl0">f(x)</legend>
    <input class="w190" id="formula1" data-tooltip="function f(x)" name="formula1" value="x^2" >
    <span class="nodisplay" id="intc1">+C:<input class="w50" id="cint1" name="cint1"></span>
    color #<input id="selfcol0" name="selfcol0" class="w60" value="ff8000">
    <input class="w50" readonly="readonly" id="selfcolbg0" >
     <div class="fndiv">show:
    <input type="radio" class="check" id="sint1f" name="sint1" checked="checked" value="0" >f(x)&nbsp;&nbsp;
    <input type="radio" class="check" id="sint1d" name="sint1" value="1">df/dx&nbsp;&nbsp;
    <input type="radio" class="check" id="sint1i" name="sint1" value="2" >&int;f(x)dx
    </div><div class="fndiv">
    range: <input id="ta1" name="ta1" class="w30"> to <input id="ta2" name="ta2" class="w30" >
  &nbsp;&nbsp;&nbsp;
    <?php linetype("con0"); ?>
    <input type="checkbox"  class="check" id="term0" name="term0" value="1" checked="checked" > legend
    </div>
  </fieldset>

  <fieldset class="function even"><legend id="fl1">g(x)</legend>
  <input class="w190" id="formula2" name="formula2" data-tooltip="function g(x)" value="" >
  <span class="nodisplay" id="intc2">+C:<input class="w50" id="cint2" name="cint2"></span>
  color #<input id="selfcol1" name="selfcol1" class="w60" value="a0b0c0">
  <input class="w50" readonly="readonly" id="selfcolbg1">
  <div class="fndiv">show:
    <input type="radio" class="check" id="sint2f" name="sint2" checked="checked" value="0" >g(x)&nbsp;&nbsp;
    <input type="radio" class="check" id="sint2d" name="sint2" value="1">dg/dx&nbsp;&nbsp;
    <input type="radio" class="check" id="sint2i" name="sint2" value="2" >&int;g(x)dx
  </div><div class="fndiv">range:
    <input id="tb1" name="tb1" class="w30"> to <input id="tb2" name="tb2" class="w30">
    &nbsp;&nbsp;&nbsp;
    <?php linetype("con1"); ?>
    <input type="checkbox"  class="check" id="term1" name="term1" value="1" checked="checked" > legend
  </div>
  </fieldset>

  <fieldset class="function odd"><legend id="fl2">h(x)</legend>
  <input class="w190" id="formula3" name="formula3" data-tooltip="function h(x)" value="" >
  <span class="nodisplay" id="intc3">+C:<input class="w50" id="cint3" name="cint3"></span>
  color #<input id="selfcol2" name="selfcol2" class="w60" value="6080a0">
  <input class="w50" readonly="readonly" id="selfcolbg2" >
  <div class="fndiv">show:
    <input type="radio" class="check" id="sint3f" name="sint3" checked="checked" value="0" >h(x)&nbsp;&nbsp;
    <input type="radio" class="check" id="sint3d" name="sint3" value="1">dh/dx&nbsp;&nbsp;
    <input type="radio" class="check" id="sint3i" name="sint3" value="2" >&int;h(x)dx
  </div><div class="fndiv">range:
    <input id="tc1" name="tc1" class="w30"> to <input id="tc2" name="tc2" class="w30">
    &nbsp;&nbsp;&nbsp;
    <?php linetype("con2"); ?>  
    <input type="checkbox"  class="check" id="term2" name="term2" value="1" checked="checked" > legend
  </div>
  </fieldset>
  <div class="smalldiv w380">
    <div class="rfloat">
    hull function= <input name="Y" id="Y" class="w200" value="Y">
    <input type="image" src="images/iconClose.png" id="clearhull">
    </div>
    <div class="rfloat">substitute Q= <input id="qq" name="qq" class="w200">
    <input type="image" src="images/iconClose.png" id="clearsubst">
    </div>
  </div>

  <fieldset class="function"><legend id="pl"><img class="bt" id="infobutton2" src="images/iconInfo.png"> points</legend>
    <div class="info" id="info2">Define named points to be plotted
    into the graph:<br>add or remove points with +/-.
    </div>
  <div class="ralign">
    color #<input id="selfcol7" name="selfcol7" class="w60" value="335555">
    <input class="w50" readonly="readonly" id="selfcolbg7" >
    <input id="addpoint" type="image" src="images/iconPlus.png" value="Add">
    <input id="delpoint" type="image" src="images/iconMinus.png" value="Kill last">
  </div>
  </fieldset> 
  <div id="eof"></div>
</div><!-- rcol -->
