<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
//if (JDEBUG){ jsll::dbgj('params class', get_class($this->params)); } 
//if (JDEBUG){ jsll::dbgj('params methods', get_class_methods($this->params)); } 


$app = Factory::getApplication();
$doc = $app->getDocument();


//$doc->addStyleSheet($cfg['path_c'].'/pure/pure.css');
//$doc->addStyleSheet($cfg['path_c'].'/pure/grids-responsive.css');
//$doc->addStyleSheet($cfg['path_c'].'/jsll/jsll.css');
$doc->addStyleSheet($cfg['path_c'].'/sanitize.css/sanitize.css');

$styles_inline=
<<<HEREDOC
:root {
  --jsll_back_top: url("{$cfg['path_c']}/images/back_top.jpg"); 
  --jsll_color_primary: #d0c6bc;
}

HEREDOC;
$doc->addStyleSheet($cfg['path_c'].'/template.css');
$doc->addStyleDeclaration($styles_inline);



?>





<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body>
	
  <header>
	<div class="jsll_row">
		<div class="menu">
			<ul>
				<li><a href="#">Home</a></li>
				<li><a href="#">About</a></li>
				<li>
					<a href="#" class="active">Services</a>
					<ul>
						<li><a href="#">Website</a></li>
						<li><a href="#">Web App</a></li>
					</ul>
				</li>
				<li><a href="#">Portfolio</a></li>
				<li><a href="#">Contact</a></li>
			</ul>
		</div>  
	</div>
	<div class="jsll_row">jsll_1</div>
	<div class="jsll_row">jsll_1</div>
  </header>
  <div class="main">
    <article>Article</article>
    
                <?php if ($this->countModules('menu', true)) : ?>
                    <jdoc:include type="modules" name="menu" style="none" />
                <?php endif; ?>
    <nav>Nav</nav>
    <aside>Aside</aside>
  </div>
  <footer>Footer</footer>

</body>

</html>
