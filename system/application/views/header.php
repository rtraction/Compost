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
	<title><?php echo strip_tags($pagename); ?><?php echo (isset($pagenameTag))? ' - ' .$pagenameTag : '';?> [compost]</title>
	<script type="text/javascript">
		//base_url for JS
		var base_url = '<?php echo base_url(); ?>';
		var index_page = '<?php echo index_page(); ?>';
	</script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/print.css" media="print" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>script/library/jquery.rating.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>script/library/css/colorpicker.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/sunny/jquery-ui-1.7.2.custom.css" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>script/library/css/geogoer.vchecks.css" />
	<!-- custom stylesheet set through admin -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/custom.css" media="all" />
	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>style/ie7.css" media="screen" />
	<![endif]-->
	<script type="text/javascript">
		var ie=0;	
	</script>
	<!--[if IE]><script type="text/javascript">
		var ie=1;
	</script><![endif]-->
	
	<script src="<?php echo base_url(); ?>script/library/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/jquery.rating.pack.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/jquery.vchecks.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/jquery.scrollTo-min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/js/colorpicker.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>script/library/ajaxupload.3.2.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>script/library/jquery.qtip-1.0.0-rc3.min.js"></script>
	<script src="<?php echo base_url(); ?>script/presentation/interface.js" type="text/javascript"></script>
	<?php
		if(isset($scripts)) {
			foreach($scripts as $script) {
				echo '<script type="text/javascript" src="'.base_url().'script/presentation/'.$script.'"></script>';
			}
		}
	?>
</head>
<body>
	<div id="header">
		<h1>
			<?php echo !empty($site_title)? $site_title:'Compost'; ?>
		</h1>
		<?php if(!empty($tab)) {?>
		<ul id="menu">
			<?php if (isset($_SESSION["userrole"])) { ?>
				<li class='<?php echo $tab == "dashboard" ? "current" : ''; ?> <?php echo $_SESSION['userrole'] != "-1" ? 'noborder' : ''; ?>'>
					<a href="<?php echo base_url().index_page() ?>admin">
						Dashboard
					</a>
				</li>
			<?php } ?>
			<?php if (isset($_SESSION["userrole"]) && $_SESSION["userrole"] == "-1") { ?>
				<li <?php echo $tab == "clients" ? "class='current'" : ''; ?>>
					<a href="<?php echo base_url().index_page() ?>client/listing">
						Clients
					</a>
				</li>
			<?php } ?>
			<?php if (isset($_SESSION["userrole"]) && $_SESSION["userrole"] == "-1") { ?>
				<li <?php echo $tab == "users" ? "class='current'" : ''; ?>>
					<a href="<?php echo base_url().index_page() ?>users/listing">
						Users
					</a>
				</li>
			<?php } ?>
			<?php if (isset($_SESSION["userrole"]) && $_SESSION['userrole'] == "-1") { ?>
				<li class="<?php echo $tab == "projects" ? " current " : ''; echo $_SESSION["userrole"] != -1 ? " noborder " : ""; ?>">
					<a href="<?php echo $_SESSION["userrole"] == -1 ? base_url().index_page()."project/listing" : base_url().index_page()."client/open/".$_SESSION['userrole']; ?>">
						Projects
					</a>
				</li>
			<?php } ?>
			<?php if (isset($_SESSION["userrole"]) && $_SESSION["userrole"] == "-1") { ?>
				<li class="noborder<?php echo $tab == "archive" ? " current" : ''; ?>">
					<a href="<?php echo base_url().index_page() ?>client/open/-1">
						Archive
					</a>
				</li>
			<?php } ?>
			<li class="right noborder<?php echo $tab == "login" ? " current" : ''; ?>">
				<?php if (isset($_SESSION['userrole'])) { ?>
					<a href="<?php echo base_url().index_page() ?>login/logout">
						Logout
					</a>
				<?php } else { ?>
					<a href="<?php echo base_url().index_page() ?>login">
						Login
					</a>
				<?php } ?>
			</li>
			<?php if (isset($_SESSION["userrole"]) && $_SESSION['userrole'] == "-1") { ?>
				<li class="right<?php echo $tab == "settings" ? " current " : ''; echo $_SESSION["userrole"] != -1 ? " noborder " : ""; ?>">
					<a href="<?php echo $_SESSION["userrole"] == -1 ? base_url().index_page()."admin/settings" : base_url().index_page()."client/open/".$_SESSION['userrole']; ?>">
						Settings
					</a>
				</li>
			<?php } ?>
			<li class="right<?php echo $tab == "help" ? " current" : ''; ?>">
				<a href="<?php echo base_url().index_page() ?>help">
					Help
				</a>
			</li>
		</ul>
		<?php } ?>
	</div>
	<div id="wrap">
		<div id="page">
			<?php if(isset($menu) && is_array($menu)) { ?>
				<ul id="rightlink"> 
					<?php echo isset($premenu) ? "<li>".$premenu."</li>" : ''; ?>				
					<?php foreach ($menu as $item) { ?>
						<li> 
							<a href="<?php echo base_url().index_page().$item['link']; ?>"><?php echo $item['name']; ?></a>
							<?php echo isset($item['help']) ? '<a target="_blank" href="'.base_url().index_page().'help#'.$item['help'].'" title="Huh?">(?)</a>' : ''; ?>
						</li>
					<?php } ?>
				</ul> 
			<?php } ?>
			<div id="content">
				<h2><?php echo $pagename; ?></h2>
				<br style="clear: both;" />
				<?php if (isset($description) && strlen($description) > 0) {?>
					<p id="description"><?php echo $description; ?></p>
				<?php } ?>
				<?php echo isset($error) ? "<p class='red'>".$error."</p>" : ''; ?>
				<?php if(isset($unread) && count($unread) > 0) { ?>
	<ul class="description" id="notifications">
		<?php foreach($unread as $item) { ?>
			<li>
				<a href="<?php echo base_url().index_page() ?>discussion/open/<?php echo $item['annotation']['CompId'];?>#<?php echo $item['annotation']['AnnotationId']; ?>">
					<?php echo $item['user']['UserName']; ?> commented on <?php echo $item['comp']['CompName']; ?>
				</a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>