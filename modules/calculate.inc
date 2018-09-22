<h3>Calculate, Link &amp; Save</h3><div>
  <div id="calculate">
  <p>Enter an expression and some variable values below and hit calculate to produce
  your data</p>
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
  <fieldset><legend><img class="bt" id="infobutton3" src="images/iconInfo.png"> calculate discrete values: </legend>
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
  <fieldset><legend><img class="bt" id="infobutton4" src="images/iconInfo.png"> Load &amp; save: </legend>
  <p>In case you want to link to the graph or cannot use
  one of the image formats provided, the graph in the plotter
  can be reproduced any time by the following URL</p>
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
