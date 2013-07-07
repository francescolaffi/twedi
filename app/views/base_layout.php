<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		
		<title><?php echo ucwords($page_title) ?> - Tecnologie Web e di Internet</title>
		
		<script src="<?php echo $view->static_url('/js/jquery-1.4.4.min.js') ?>" type="text/javascript"></script>
		<script src="<?php echo $view->static_url('/js/jquery.validate.min.js') ?>" type="text/javascript"></script>
		<script src="<?php echo $view->static_url('/js/superfish.js') ?>" type="text/javascript"></script>
		<script src="<?php echo $view->static_url('/js/behaviour.js') ?>" type="text/javascript"></script>

<?php if (true): #true = css, false = less ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $view->static_url('/css/style.css') ?>" />
<?php else: ?>
		<!-- LESS CSS : compile to css and remove on production -->
		<link rel="stylesheet/less" href="<?php echo $view->static_url('/css/style.less') ?>" type="text/css" />
		<script src="<?php echo $view->static_url('/dev/less-1.0.36.min.js') ?>" type="text/javascript"></script>
		<script type="text/javascript" charset="utf-8">
			less.env = "development";
			less.watch();
		</script>
<?php endif; ?>

		<!-- meta/ links / etc -->
	</head>
	<body class="<?php
		//TODO: body class
	?>">
		<div id="top-wrapper">
			<div id="header">
<?php
	if ('home' == $view->current_route()) {
		$site_title_tag = 'h1';
		$site_subtitle_tag = 'h2';
		$content_title_tag = 'h2';
	} else {
		$site_title_tag = 'div';
		$site_subtitle_tag = 'div';
		$content_title_tag = 'h1';
	}
?>
				<<?php echo $site_title_tag ?> id="site-title" class="title">
					<a href="<?php echo $view->url_for('home') ?>">
					Tecnologie <em>Web</em> <br/>
					<em class="amp">&amp;</em> di <em>Internet</em>
					</a>
				</<?php echo $site_title_tag ?>>
				<<?php echo $site_subtitle_tag ?> id="site-subtitle" class="title">
					Università di Bologna <br/>
					Ingegneria Gestionale
				</<?php echo $site_subtitle_tag ?>>
			</div>
			<div id="top-nav">
				<?php $view->render('topnav') ?>
			</div>
		</div>
		
		<div id="main-wrapper"> <div id="main">  <div class="inner"> 
			
			<div id="content">
<?php /*
				<ul id="breadcrumb">
					<div>Sei qui:</div>
					<li><a href="#">home</a></li>
					<li><a href="#">categ</a></li>
					<li>current page</li>
				</ul>
*/ ?>
<?php
			$notices = $view->session()->get_notices();
			if (count($notices)):
?>
				<div id='notices'>
<?php			foreach ($notices as $notice): ?>
					<p class="<?php echo htmlspecialchars($notice['type']) ?>"><?php echo $notice['message'] ?></p>
<?php			endforeach; ?>
				</div>
<?php		endif; ?>

<?php		if (!empty($content_title)): ?>
				<<?php echo $content_title_tag ?> class="content-title"><?php echo $content_title ?></<?php echo $content_title_tag ?>>
<?php		endif ?>
				
				<?php $view->content() ?>
				
			</div>
			
			<div id="sidebar">
				<div id="sidebar_login_box" class='sidebar_box'>
					<?php $view->render('login_sidebar') ?>
				</div>
				<div id="sidebar_links_box" class='sidebar_box'>
					<h4 class="sidebar_box_title">Links utili</h4>
					<ul id="sidebar_links_utili">
						<li><a href="http://www.ing.unibo.it/" title="Sito ufficiale della facoltà di ingegneria">Facoltà di Ingegneria</a></li>
						<li><a href="http://corsi.unibo.it/Laurea/IngegneriaGestionale/" title="Sito ufficiale del corso di ingegneria gestionale">Ingegneria Gestionale</a></li>
						<li><a href="http://www.unibo.it/" title="Sito ufficiale del'università di bologna">Università di Bologna</a></li>
						<li><a href="https://www.universibo.unibo.it/index.php?do=ShowInsegnamento&id_canale=9369" title="Il corso di tecnologie web e di internet su universibo">TWEDI su Universibo</a></li>
						<li><a href="https://almaesami.unibo.it/almaesami/studenti/home.htm" title="Sito ufficiale per iscriversi agli esami">Almaesami</a></li>
					</ul>
				</div>
			</div>
			
		</div> </div> </div>
		
		<div id="bottom-wrapper">
			<div id="footer">
				<p>TWEDI©2010 – <a href="#top-wrapper">Torna all'inizio della pagina</a> – <a href="<?php echo $view->url_for('sitemap') ?>">Sitemap</a> – XHTML e CSS valido</p>
				<p>Portale del corso "Tecnologie Web e di Internet" di Ingegneria Gestionale dell'Università di Bologna, tenuto dalla Professoressa <a href="http://www.unibo.it/SitoWebDocente/default.htm?MAT=015570">M.R. Scalas</a>.</p>
				<p>Sito realizzato da studenti di Tecnologie Web e di Internet come progetto del corso.</p>
			</div>
		</div>
	</body>
</html>