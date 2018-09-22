<?php
//
// mainplot.inc: 
// This is the main part of the GUI: display a plot in an <img> on the left,
// the most common controls and a alist of points to plot on the right.
// This one should degrade nicely (and ugly, of course...)
// M. Oettinger 11/2015
//
// 12/2015: added an option to change variable Name (e.g. for use of t instead of x in time domain)
// --- begin options --
?>

<form id="funcs" method="post" action="<?php
      echo "function";
      if ($silent) echo "_silent";
      ?>.php" target="function" autocomplete="off" onsubmit="checkscroll()">

<?php 
// the left column containing the plot in an iframe, main buttons and quadrant control
?>
  <div style="display:inline-block; margin:5px;">
  <div id="plotcontrol">
  <input type="hidden" name="c" value="2" >
  <input type="image" src="images/iconPlot.png" title="draw plot" value="Draw" >
  <script type="text/javascript">document.write('<input type="image" src="images/iconReset.png" title="set default options" value="set default" onclick="standard()"  >');</script>
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

<? 
// the right column with function inputs
?>
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
   

  <fieldset class="function"><legend id="pl"><img class="bt" id="infobutton2" src="images/iconInfo.png"> points</legend>
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
</div>