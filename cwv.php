<?php
/**
 * Sample PHP file to load a crossword
 * also includes workarounds to deal with keyboard issues in mobile devices
 * License: LGPL
 * Copyright 2020 Fernando Martinez
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/lgpl.html
 */
// available sample data:
$data = array('celebrities' => 'data/celebrities.xml', 'countries' => 'data/countries.xml');
$chosenCw = $data[$_GET['cw']];
$cwtitle = ucfirst($_GET['cw']);
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="oss crossword viewer in html5 canvas">
  <meta name="author" content="Fernando">
  <title>Open source html canvas crossword viewer</title>
  <!-- Bootstrap Core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>  
<section class="content-section bg-light" id="about">
	<div class="container text-center">
		<div class="row">
			<div class="col-lg-10 mx-auto">
				<h2>Crossword - <?php echo($cwtitle);?></h2>
				<p class="lead mb-5">Your crossword is ready. Use your mouse and keyboard to interact with it (Shift-click changes writing direction)</p>
				<p>Backspace won't work on Chrome for mobile devices as the (always On) auto-suggest interferes. </p>
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-lg-8">
				<div id="myParent"></div>
				<input id="kb" type="text" autocomplete="off" style="position:fixed;left:-1700px;top:0px";>
			</div>
			<div class="col-6 col-lg-4">
				<div class="row">
					<div class="col-12 text-left" id="messages">Press on a cell to view hints here. Use Shift-click to change writing direction</div>
					<div class="col-12">
						<button class="btn btn-info" value="solve" onclick="cw.solve();">View solution</button>
						<button class="btn btn-info" value="evaluate" onclick="cw.evaluate();">Check for mistakes</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/cwviewer.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var cwidth = 500;
        if (window.screen.width<500) {
			cwidth = window.screen.width-25;
		}
        var canvas = '<canvas id="cwCanvas" width="'+cwidth+'" height="'+cwidth+'" style="border:1px solid #0b0b0b"></canvas>';
        $('#myParent').html(canvas);
        crossword('cwCanvas', '<?php echo($chosenCw);?>', function(data) { //cwhtml5/cw
            $('#messages').html(data);
        });
        // after so many years of having html5 canvas around, all browsers, mobile or for larger devices, should be able to provide keyboard events for it in the same way: 
		// a standard, unified way, it is feasible. But that is not the case, and lots of ugly workarounds are necessary to make keyboards visible and get pressed keys on
		// mobile. The worst seems to be Chrome mobile, as apart from the same issues as others, also have the auto-suggest feature interfering, and
		// it seems impossible to turn off
        var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
        if (!isMobile) { // workaround because window.matchMedia fails at least on older iPads
            if ("iPad" === window.clientInformation.platform || "iPhone" === window.clientInformation.platform) {
                isMobile = true;
			}
		}
		if (isMobile) {
		    var isChrome = false;// this specially for Chrome, hopefully not the new IE in terms of giving developers a headache!
		    cw.setMobile(true);
            $('#kb').keydown(function(e) {
                e.preventDefault();
            });
            $('#kb').on("input", function() {
                var c = $(this).val();
                console.log("input, c is " + c + " isChrome? " + isChrome);
                if (c != null && c.length > 0) {
                    if (isChrome) {
                        cwkbd(c.charAt(c.length - 1), 1);
					}
				}
            });
            $('#kb').keyup(function(e) {
                var c = e.target.value, k = e.originalEvent.keyCode;
                console.log("keyup, c is " + c);
                if (c == null || c == "") {
                    c = String.fromCharCode(e.keyCode);
				}
                isChrome = (e.keyCode == 229);
                e.preventDefault();
                cwkbd(c, k);
            });
            document.getElementById("cwCanvas").addEventListener('click', function () {
                document.getElementById("kb").focus();
            });
            function cwkbd(c, k) {
                var o = {key: c, keyCode: k, mbke: true};
                console.log("char="+o.key)
                cw.keyDown(o);
			}
        }
    });
</script>
</body>

</html>
