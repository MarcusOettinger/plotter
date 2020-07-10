<?php
//
// options.inc: 
// a modal dialog to change display settings for plotter.
// This one should degrade nicely (and ugly, of course...)
// 
// M. Oettinger 2020
// 07/2020: added an option to use transparency in png/gif
// 06/2020: get rid of jquery and jquery-ui
// 09/2019: CSP capable
// 12/2015: added an option to change variable Name (e.g. for use of t instead of x in time domain)
// --- begin options --
//  <div id="dialog">
?>
<dialog role="dialog"><h2 id="header">Options</h2>
<button id="close-dialog">Close</button>
<button id="close-icon">X</button>
  <fieldset><legend class="text1em">plot properties</legend>
  <table><tr>
	<td>&nbsp;width: </td><td class="ralign"><input class="w50" id="width" name="width" value="500" > </td><td rowspan="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>&nbsp;height: </td><td class="ralign"><input class="w50" id="height" name="height" value="500" ></td>
	<td class="ralign">&nbsp;&nbsp;<label for="frame">border</label><input type="checkbox" class="check" id="frame" name="frame" value="1" ></td>
  </tr><tr>
	<td>&nbsp;x-axis from </td><td class="ralign"><input class="w50" id="rulex1" name="rulex1" value="-5" > </td><td>&nbsp;to </td>
	<td class="ralign"><input class="w50" id="rulex2" name="rulex2" value="5" ></td>
  <td></td>
  </tr><tr>
	<td>&nbsp;y-axis from </td><td class="ralign"><input class="w50" id="ruley1" name="ruley1" value="-5" > </td><td>&nbsp;to </td>
	<td class="ralign"><input class="w50" id="ruley2" name="ruley2" value="5" ></td>
	<td  class="ralign"><label for="lines">axis lines</label> <input type="checkbox"  class="check" id="lines" name="lines" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;intervals x-axis: </td><td class="ralign"><input class="w50" id="intervalsx" name="intervalsx" value="10" > </td>
	<td>&nbsp;y-axis: </td><td class="ralign"><input class="w50" id="intervalsy" name="intervalsy" value="10" ></td>
	<td class="ralign"><label for="numbers">caption</label> <input type="checkbox"  class="check" id="numbers" name="numbers" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;grid lines: x-axis </td><td class="ralign"><input class="w50" id="gridx" name="gridx" value="20" > </td>
	<td>&nbsp;y-axis </td><td class="ralign"><input class="w50" id="gridy" name="gridy" value="20" ></td>
	<td class="ralign"><label for="grid">show grid </label><input type="checkbox" class="check" id="grid" name="grid" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;dashes length: x-axis </td><td class="ralign"><input class="w50" id="linex" name="linex" value="5" > </td>
	<td>&nbsp;y-axis </td><td class="ralign"><input class="w50" id="liney" name="liney" value="5" ></td>
	<td class="ralign"><label for="dashes">dashes </label><input type="checkbox"  class="check" id="dashes" name="dashes" value="1" checked="checked" ></td>
  </tr><tr>
	<td>&nbsp;decimal places: </td><td class="ralign"><input class="w50" id="deci" name="deci" value="3"> </td>
	<td>&nbsp;gap at origin: </td><td class="ralign"><input class="w50" id="mid" name="mid" value="0" ></td>
	<td>&nbsp;</td>
  </tr><tr>
	<td>&nbsp;graph thickness: </td><td class="ralign"><input name="thick" id="thick" class="w50" value="2" > </td><td>&nbsp;</td>
	<td class="ralign">&nbsp;variable name:</td><td><input class="w50" name="varname" id="varname" value="x"></td>
	<td></td>
  </tr><tr>
	<td colspan="4" class="pt15">&nbsp;log. scale x:
	<input type="radio" class="check logskx" name="logskx" checked="checked" value="0" >No
	<input type="radio" class="check logskx" name="logskx" value="2" >2
	<input type="radio" class="check logskx" name="logskx" value="M_E" >e
	<input type="radio" class="check logskx" name="logskx" value="10" >10
	<input type="radio" class="check logskx" name="logskx" value="100" >100
	</td><td colspan="2" class="pt15 centertext">or <input name="logskix" id="logskix" class="w50"></td>
  </tr><tr>
	<td colspan="4">&nbsp;log. scale y:
	<input type="radio" class="check logsk" name="logsk" checked="checked" value="0" >No
	<input type="radio" class="check logsk" name="logsk" value="2" >2
	<input type="radio" class="check logsk" name="logsk" value="M_E" >e
	<input type="radio" class="check logsk" name="logsk" value="10" >10
	<input type="radio" class="check logsk" name="logsk" value="100" >100
	</td><td colspan="2" class="centertext">or <input name="logski" id="logski" class="w50"></td>
  </tr>
  </table>
  <div class="smalldiv"><label for="errors">show errors in image</label>
    <input type="checkbox"  class="check" id="errors" name="errors" value="1" checked="checked" >
  </div>
  </fieldset>

  <fieldset><legend class="text1em">colors</legend>
  <div class="smalldiv">
      background #<input id="selfcol3" name="selfcol3" class="w60" value="ffffff"> <input class="coldisplay" readonly="readonly" id="selfcolbg3" >
      <input type="checkbox"  class="check" id="transp" name="transp" value="1" checked="checked" ><label for="transp">transparent background (gif and png)</label><br>
      caption #<input id="selfcol4" name="selfcol4" class="w60" value="141414"> <input class="coldisplay" readonly="readonly" id="selfcolbg4" >
      grid #<input id="selfcol5" name="selfcol5" class="w60" value="f2f2f2"> <input class="coldisplay" readonly="readonly" id="selfcolbg5" >
      gap #<input id="selfcol6" name="selfcol6" class="w60" value="ffffff"> <input class="coldisplay" readonly="readonly" id="selfcolbg6" >
  </div></fieldset>

  <div class="lalign">
  <fieldset><legend>image properties</legend>
  <div class="smalldiv">image type:
	<select id="filetype" name="filetype">
            <option value="0">jpeg</option>
            <option value="1">gif</option>
            <option value="2" selected>png</option>
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
	<label for ="gamma">gamma:</label> <input id="gamma" name="gamma" class="w40" value="1" >
	<label for="bri">brightness:</label> <input id="bri" name="bri" class="w40" value="0" >
	<label for="cont">contrast:</label> <input id="cont" name="cont" class="w40" value="0" >
	<label for="rotate">rotation:</label>  <input id="rotate" name="rotate" class="w40" value="0" >&#176;
  </div>
  <div class="smalldiv fullwidth">
  <fieldset><legend>apply filter</legend>
  <label for="emb">emboss</label><input type="checkbox" class="check" id="emb" name="emb" value="1" >
  <label for="blur">blur</label><input type="checkbox" class="check" id="blur" name="blur" value="1" >
  <label for="neg">negative</label><input type="checkbox" class="check" id="neg" name="neg" value="1" >
  <label for="gray">greyscale</label><input type="checkbox" class="check" id="gray" name="gray" value="1" >
  <label for="mean">sketchy</label><input type="checkbox" class="check" id="mean" name="mean" value="1" >
  <label for="edge">edges</label><input type="checkbox" class="check" id="edge" name="edge" value="1" >
  </fieldset>
  </div>
  </fieldset>
  </div><!-- align -->
  <div id="spacer"></div>
  </div>
</dialog><!-- dialog -->
</form>
<?php
// --- end options --
?>
