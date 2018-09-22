<?php
//
// options.inc: 
// create a jquery dialog to change display settings for plotter.
// This one should degrade nicely (and ugly, of course...)
// (uses jquery-ui)
// M. Oettinger 11/2015
//
// 12/2015: added an option to change variable Name (e.g. for use of t instead of x in time domain)
// --- begin options --
?>
<div id="dialog">
  <fieldset><legend style="font-size:1em;">plot properties</legend>
  <table><tr>
	<td>&nbsp;width: </td><td style="text-align:right"><input style="width:50px" id="width" name="width" value="500" > </td><td rowspan="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>&nbsp;weight: </td><td style="text-align:right"><input style="width:50px" id="height" name="height" value="500" ></td>
	<td style="text-align:right;">&nbsp;&nbsp;<label for="frame">border</label><input type="checkbox" class="check" id="frame" name="frame" value="1" ></td>
  </tr><tr>
	<td>&nbsp;x-axis from </td><td style="text-align:right"><input style="width:50px" id="rulex1" name="rulex1" value="-5" > </td><td>&nbsp;to </td>
	<td style="text-align:right"><input style="width:50px" id="rulex2" name="rulex2" value="5" ></td>
  <td></td>
  </tr><tr>
	<td>&nbsp;y-axis from </td><td style="text-align:right"><input style="width:50px" id="ruley1" name="ruley1" value="-5" > </td><td>&nbsp;to </td>
	<td style="text-align:right"><input style="width:50px" id="ruley2" name="ruley2" value="5" ></td>
	<td  style="text-align:right;"><label for="lines">axis lines</label> <input type="checkbox"  class="check" id="lines" name="lines" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;intervals x-axis: </td><td style="text-align:right"><input style="width:50px" id="intervalsx" name="intervalsx" value="10" > </td>
	<td>&nbsp;y-axis: </td><td style="text-align:right"><input style="width:50px" id="intervalsy" name="intervalsy" value="10" ></td>
	<td style="text-align:right;"><label for="numbers">caption</label> <input type="checkbox"  class="check" id="numbers" name="numbers" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;grid lines: x-axis </td><td style="text-align:right"><input style="width:50px" id="gridx" name="gridx" value="20" > </td>
	<td>&nbsp;y-axis </td><td style="text-align:right"><input style="width:50px" id="gridy" name="gridy" value="20" ></td>
	<td style="text-align:right;"><label for="grid">show grid </label><input type="checkbox" class="check" id="grid" name="grid" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;dashes length: x-axis </td><td style="text-align:right"><input style="width:50px" id="linex" name="linex" value="5" > </td>
	<td>&nbsp;y-axis </td><td style="text-align:right"><input style="width:50px" id="liney" name="liney" value="5" ></td>
	<td style="text-align:right;"><label for="dashes">dashes </label><input type="checkbox"  class="check" id="dashes" name="dashes" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;decimal places: </td><td style="text-align:right"><input style="width:50px" id="deci" name="deci" value="3" onchange="document.getElementById('decis').value=document.getElementById('deci').value" > </td>
	<td>&nbsp;gap at origin: </td><td style="text-align:right"><input style="width:50px" id="mid" name="mid" value="0" ></td>
	<td>&nbsp;</td>
  </tr><tr>
	<td>&nbsp;graph thickness: </td><td style="text-align:right"><input name="thick" id="thick" style="width:50px" value="2" > </td><td>&nbsp;</td>
	<td style="text-align:right">&nbsp;variable name:</td><td><input style="width:50px" name="varname" id="varname" value="x"></td>
	<td></td>
  </tr><tr>
	<td colspan="4" style="padding-top:15px;">&nbsp;log. scale x:
	<input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" checked="checked" value="0" >No
	<input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="2" >2
	<input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="M_E" >e
	<input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="10" >10
	<input type="radio" class="check" name="logskx" onclick="document.getElementById('logskix').value=''" value="100" >100
	</td><td colspan="2" style="text-align:center;padding-top:15px;">or <input name="logskix" id="logskix" style="width:50px" onclick="clrlog('x')" ></td>
  </tr><tr>
	<td colspan="4">&nbsp;log. scale y:
	<input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" checked="checked" value="0" >No
	<input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="2" >2
	<input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="M_E" >e
	<input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="10" >10
	<input type="radio" class="check" name="logsk" onclick="document.getElementById('logski').value=''" value="100" >100
	</td><td colspan="2" style="text-align:center">or <input name="logski" id="logski" style="width:50px" onclick="clrlog('')" ></td>
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
  <div class="smalldiv">image type:
	<select id="filetype" name="filetype">
            <option value="0">png</option>
            <option value="1">gif</option>
            <option value="2">jpeg</option>
	</select>
	<label for="anti">antialiasing</label><input type="checkbox" class="check" id="anti" name="anti" value="1" checked="checked" >
	<label for="poles">poles</label><input type="checkbox" class="check" id="pol" name="pol" value="1" checked="checked" >
  </div>
  <div class="smalldiv"><label for="bf">show</label>
	<select id="bf" name="bf">
	<option value="1">Lines in background</option>
	<option value="2">Lines in foreground</option>
	<option value="0">All lines off</option></select>
  </div>
  <div class="smalldiv">
	<label for ="gamma">gamma:</label> <input id="gamma" name="gamma" style="width:40px" value="1" >
	<label for="bri">brightness:</label> <input id="bri" name="bri" style="width:40px" value="0" >
	<label for="cont">contrast:</label> <input id="cont" name="cont" style="width:40px" value="0" >
	<label for="rotate">rotation:</label>  <input id="rotate" name="rotate" style="width:40px" value="0" >&#176;
  </div>
  <div class="smalldiv" style="width:100%;">
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
</div>
<?php
// --- end options --
?>