<?php
/*
 * This file is part of Compost.
 *
 * Compost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Compost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Compost.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $pagename; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>style/style.css" media="all" />
		<style>
			body {
				margin-top: 38px;
			}
			#bodydiv {
				background-image: url(<?php echo base_url(); ?><?php echo $comp['RevisionBackgroundImage']; ?>);
				background-color: #<?php echo $comp['RevisionBackgroundColour']; ?>;
				<?php
					$options = explode(';', $comp['RevisionBackgroundRepeat']);
				?>
				background-repeat: <?php echo $options[0]; ?>;
				background-position: <?php echo $options[1]; ?> !important;
				margin: 0;
				padding: 0;
			}
			input {
				position: fixed;
				top: 4px;
				right: 6px;
			}
			img {
				display: block;
				<?php
					switch ($comp['RevisionPageFloat']) {
						case "top center":
							echo "margin: 30px auto 0;";
						break;
						case "top left":
							echo "float: left;";
							echo "margin-top: 30px;";
						break;
						case "top right":
							echo "float: right;";
							echo "margin-top: 30px;";
						break;
					}
				?>
				padding-bottom: 50px;
			}
			#menubar_top {
				position: fixed;
				left: 0;
				top: 0;
				background: #747474 url(<?php echo base_url(); ?>images/header.jpg) no-repeat scroll right top;
				width: 100%;
				height: 30px;
				padding: 4px 6px;
				border-bottom: solid 1px #000;
			}
			#menubar_top .button_close {
				font-size: 14px;
				font-weight: bold;
				float: right;
			}
  </style>
</head>
<body>
	<div id="bodydiv">
	  <img src="<?php echo base_url(); ?>images/comps/<?php echo $comp['RevisionUrl']; ?>" />
	  <div id="menubar_top">
	  	<div class="button_close">
			<form><input type="button" value="Close" onClick="window.close()" /></form>
		</div>
	  </div>
  </div>
</body>
</html>