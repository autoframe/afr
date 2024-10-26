<?php
class Blog_List_ControllerCode {
	function indexGET(){
       /* echo __FILE__.'<br>';
        echo __CLASS__.'<br>';
        echo __FUNCTION__.'<br>';
		echo 'BLOG LIST GET  code controller!';*/
		
		$hh='<!DOCTYPE html>
<html lang="ro">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="follow" />
<title>Work Efficiency General</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
	<script src="https://thf.inovativeweb.ro/?jslib=work_efficiency_general&type=js" type="text/javascript"></script>
    <link href="https://thf.inovativeweb.ro/?jslib=work_efficiency_general&type=css" media="all" rel="stylesheet" type="text/css" />	
	<style>@media (max-width:992px){.container{width:99.9% !important;}  .navbar-brand{font-size: 1em !important;}}</style>
</head>

<body >
  <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/administration/work_efficiency_general/">Work Efficiency General</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
             <li class="dropdown active">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Choose Worksite <span class="caret"></span></a>
              <ul class="dropdown-menu">
               
              </ul>
            </li>
                      </ul>
          
          
                      
          
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	
    <div class="container-fluid" id="bpg_data_scale">

   		<div class="container round" id="login-dp-class"><div class="row"><div class="col-sm-12 col-md-10  col-md-offset-1 "><h3>Login</h3>            
             <form class="form" role="form" method="post" action="" action_x="/administration/work_efficiency_general/login/" accept-charset="UTF-8" id="login-form-class" enctype="application/x-www-form-urlencoded" autocomplete="off">					            
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-user"></i>
                            </span> 
                            <input class="form-control" placeholder="Email/Username" name="username_fl"  autofocus type="text" autocomplete="off">
                        </div>
                    </div>                                                            
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-lock"></i>
                            </span>
                            <input class="form-control" placeholder="Password" name="pass_fl" required  value="" type="password" autocomplete="off">
                        </div>
                    </div>                    <div class="form-group">
                         <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                    </div>
                    <div class="checkbox">
                         <label>
                         <input name="keep_login_fl" type="checkbox" checked="checked" checked> keep me logged-in                         </label>
                    </div>					<div class="help-block text-right" style="font-size:0.8em"><a href="/administration/work_efficiency_general/reset_pass/">Forget the password?</a></div>
                    <!--<div style="text-align: right; font-size: 0.8em;"><a href="/administration/work_efficiency_general/reset_pass/">Forget the password?</a></div>-->
                                  </form> 
            </div>
        
        </div></div>
		 </div> <!-- /container -->
  </body>
</html>

';
		
	$hh='    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/administration/work_efficiency_general/">Work Efficiency General</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
             <li class="dropdown active">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Choose Worksite <span class="caret"></span></a>
              <ul class="dropdown-menu">
               
              </ul>
            </li>
                      </ul>
          
          
                      
          
        </div><!--/.nav-collapse -->
      </div>
    </nav>';
		
		

		
		$v=new thfView();
		$v->parseHTML($hh);
		
		
		
		
		//echo $v->renderParsedHtml();
		//prea($v->domParse); die;
		
		
		$v->attachParsedHtmlToNode('content');
		echo $v->renderHtml();
		prea($v->dom); die;

    }
   
}
