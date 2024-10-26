<?php 

class BaseView {
	public function indexHead(){ ?><!DOCTYPE HTML>
	<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $page['name']; ?></title>
		<?php generate_js_css_html_includes(array('jquery','bootstrap','bootstraptoggle','thf','bpg','dropdownchosen','jqueryuitime','colorbox','fa4')); ?>
		<script type="text/javascript" src="/administration/jscript/tooltip.js"></script>
		<link rel="stylesheet" type="text/css" href="/administration/css/style.css" media="screen"/>
			<style>
				textarea{min-height:6.6em; resize:vertical;}
				li.active a{color:white !important;}
				div.row{padding-top:0.3em;}
				input[type=text]{padding:2px 8px !important;}
				label{text-align:right;}
				.extra{display:none;}
				#wrapper_body{padding:0.5em 0;}
				.show_dialog{display:none;}
			</style>



		</head>

	<body><?php
	}
	
	
	
	
	
	public function indexFooter(){
		$this->scriptsFooter();
	 	?></body></html><?php	
	}
	
	
	
	
	public function scriptsFooter(){ ?>
	<script>
		jQuery(function(){
			
		});
	
	</script>
	<?php }
}