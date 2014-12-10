<!doctype html>  
<!--[if IE 6 ]><html lang="en-us" class="ie6"> <![endif]-->
<!--[if IE 7 ]><html lang="en-us" class="ie7"> <![endif]-->
<!--[if IE 8 ]><html lang="en-us" class="ie8"> <![endif]-->
<!--[if (gt IE 7)|!(IE)]><!-->
<html lang="en-us"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	
	<title>Simple MVC Framework - Documentation</title>
	
	<meta name="description" content="">
	<meta name="author" content="David Carr">
	<meta name="copyright" content="David Carr">
	<meta name="generator" content="Documenter v2.0 http://rxa.li/documenter">
	<meta name="date" content="2014-12-03T00:00:00+01:00">
	
	<link rel="stylesheet" href="assets/css/documenter_style.css" media="all">
	<link rel="stylesheet" href="assets/css/prism.css" media="all">
	<script src="assets/js/prism.js"></script>

	
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<script src="assets/js/jquery.js"></script>
	
	<script src="assets/js/jquery.scrollTo.js"></script>
	<script src="assets/js/jquery.easing.js"></script>
	
	<script>document.createElement('section');var duration='500',easing='swing';</script>
	<script src="assets/js/script.js"></script>
	
	<style>
		html{background-color:#FFFFFF;color:#383838;}
		::-moz-selection{background:#444444;color:#DDDDDD;}
		::selection{background:#444444;color:#DDDDDD;}
		#documenter_sidebar #documenter_logo{background-image:url(assets/images/image_1.png);}
		a{color:#0000FF;}
		.btn {
			border-radius:3px;
		}
		.btn-primary {
			  background-image: -moz-linear-gradient(top, #0088CC, #0044CC);
			  background-image: -ms-linear-gradient(top, #0088CC, #0044CC);
			  background-image: -webkit-gradient(linear, 0 0, 0 0088CC%, from(#DDDDDD), to(#0044CC));
			  background-image: -webkit-linear-gradient(top, #0088CC, #0044CC);
			  background-image: -o-linear-gradient(top, #0088CC, #0044CC);
			  background-image: linear-gradient(top, #0088CC, #0044CC);
			  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088CC', endColorstr='#0044CC', GradientType=0);
			  border-color: #0044CC #0044CC #bfbfbf;
			  color:#FFFFFF;
		}
		.btn-primary:hover,
		.btn-primary:active,
		.btn-primary.active,
		.btn-primary.disabled,
		.btn-primary[disabled] {
		  border-color: #0088CC #0088CC #bfbfbf;
		  background-color: #0044CC;
		}
		hr{border-top:1px solid #EBEBEB;border-bottom:1px solid #FFFFFF;}
		#documenter_sidebar, #documenter_sidebar ul a{background-color:#DDDDDD;color:#222222;}
		#documenter_sidebar ul a{-webkit-text-shadow:1px 1px 0px #EEEEEE;-moz-text-shadow:1px 1px 0px #EEEEEE;text-shadow:1px 1px 0px #EEEEEE;}
		#documenter_sidebar ul{border-top:1px solid #AAAAAA;}
		#documenter_sidebar ul a{border-top:1px solid #EEEEEE;border-bottom:1px solid #AAAAAA;color:#444444;}
		#documenter_sidebar ul a:hover{background:#444444;color:#DDDDDD;border-top:1px solid #444444;}
		#documenter_sidebar ul a.current{background:#444444;color:#DDDDDD;border-top:1px solid #444444;}
		#documenter_copyright{display:block !important;visibility:visible !important;}
	</style>
	
</head>
<body class="documenter-project-simple-mvc-framework">
	<div id="documenter_sidebar">
		
		<ul id="documenter_nav">
			<li><a href="#docs">Docs</a></li>
			<li><a href="#s1">Overview</a></li>
			<li><a href="#s2">Requirements</a></li>
			<li><a href="#s3">Install</a></li>
			<li><a href="#s4">Install - Ngix</a></li>
			<li><a href="#s5">Install - IIS</a></li>
			<li><a href="#s6">Config</a></li>
			<li><a href="#s7">Routes</a></li>
			<li><a href="#s8">Controllers</a></li>
			<li><a href="#s9">Models</a></li>
			<li><a href="#s10">Views</a></li>
			<li><a href="#s11">Templates</a></li>
			<li><a href="#s12">Errors</a></li>
			<li><a href="#s13">Languages</a></li>
			<li><a href="#s14">Helpers Overview</a></li>
			<li><a href="#s15">Database</a></li>
			<li><a href="#s16">Password</a></li>
			<li><a href="#s17">Pagination</a></li>
			<li><a href="#s18">Sessions</a></li>
			<li><a href="#s19">Url</a></li>
			<li><a href="#s20">PHPMailer</a></li>
			<li><a href="#s21">Document</a></li>
			<li><a href="#s22">Parsedown</a></li>
			<li><a href="#s23">Captcha with Raincaptcha</a></li>
			<li><a href="#s24">GUMP Validation</a></li>
			<li><a href="#s25">Table Builder</a></li>
			<li><a href="#s26">SimpleCurl</a></li>
		</ul>



		<div id="documenter_copyright">Copyright David Carr 2014<br>
		made with the <a href="http://rxa.li/documenter">Documenter v2.0</a> 
		</div>
	</div>
	<div id="documenter_content">

	<section id="docs">
		<p><img src='assets/images/image_1.png'></p>
		<h1>Documentation</h1>
		<hr>
		<ul>
		<li>created: 03/10/2011</li>
		<li>latest update: 12/03/2014</li>
		<li>by: David Carr</li>
		<li>url: <a href="http://simplemvcframework.com">http://simplemvcframework.com</a></li>
		<li>email: <a href="mailto:&#100;&#97;&#118;&#101;&#64;&#100;&#97;&#118;&#101;&#105;&#115;&#109;&#121;&#110;&#97;&#109;&#101;&#46;&#99;&#111;&#109;">&#100;&#97;&#118;&#101;&#64;&#100;&#97;&#118;&#101;&#105;&#115;&#109;&#121;&#110;&#97;&#109;&#101;&#46;&#99;&#111;&#109;</a></li>
		</ul>
	</section>
	
	<section id="s1">
		<div class="page-header">
			<h3>Overview</h3>
			<hr class="notop">
		</div>
		
		<p>Simple MVC Framework is a PHP 5.3+ MVC Framework. It\'s designed to be lightweight and modular, allowing developers to build better and easy to maintain code with PHP.</p>

		<p>The base framework comes with a range of helper classes, Classes can easily be added at any stage of development.</p>

		<div class='alert alert-info'>
		<p>Version 2.1 has been released, The most noteworthy changes are:</p>

		<ul>
		<li>Namespaces added</li>
		<li>Integration with Composer</li>
		<li>Config is now a class called by the core/controller class</li>
		<li>loadModel and loadHelper calls no longer needed (removed) due to namespacing</li>
		<li>autoloader file removed in favour of namespace autoloading with composer</li>
		</ul>
		</div>

	</section>

	<section id="s2">
		<div class="page-header">
			<h3>Requirements</h3>
			<hr class="notop">
		</div>
		
		<p>The framework requirements are limited.</p>

		<div class='alert alert-info'>
		<ul>
		<li>Apache Web Server or equivalent with mod rewrite support.</li>
		<li>IIS with URL Rewrite module installed - <a href='http://www.iis.net/downloads/microsoft/url-rewrite'>http://www.iis.net/downloads/microsoft/url-rewrite</a> See <a href='http://simplemvcframework.com/documentation/v2.1/install-iis'>Install IIS</a> page for more details.</li>
		<li>PHP 5.3 or greater is required</li>
		</ul>
		</div>

		<p>Although a database is not required, if a database is to be used the system is designed to work with a MySQL database. The framework can be changed to work with another database type such as Medoo for example.</p>

	</section>

	<section id="s3">
		<div class="page-header">
			<h3>Install</h3>
			<hr class="notop">
		</div>
		
		<p>The framework is now on packagist <a href='https://packagist.org/packages/simple-mvc-framework/v2'>https://packagist.org/packages/simple-mvc-framework/v2</a>

<p>Install from terminal now by using:</p>

<pre><code class="language-php">composer create-project simple-mvc-framework/v2 foldername -s dev</code></pre>
<p>The foldername is the desired folder to be created.</p>

<p>If you use Sublime you can also use the fetch package to download the framework from within Sublime Text <a href='http://code.tutsplus.com/articles/introducing-nettuts-fetch--net-23490'>http://code.tutsplus.com/articles/introducing-nettuts-fetch--net-23490</a></p>
		
		<ol>
		<li>Download the framework</li>
		<li>Unzip the package.</li>
		<li>To run composer, navigate to your project on a terminal/command prompt then run 'composer install' that will update the vendor folder. Or use the vendor folder as is (composer is not required for this step)</li>
		<li>Upload the framework files to your server. Normally the index.php file will be at your root.</li>
		<li>Open the index.php file with a text editor, setup your routes.</li>
		<li>Open core/config.example.php and set your base URL and database credentials (if a database is needed). Set the default theme. Rename file to config.php</li>
		<li>Edit .htaccess file and save the base path. (if the framework is installed in a folder the base path should reflect the folder path /path/to/folder/ otherwise a single / will do.</li>
		</ol>

	</section>

	<section id="s4">
		<div class="page-header">
			<h3>Install - Ngix</h3>
			<hr class="notop">
		</div>
		
		<p>Nginx configuration kindly provided by <a href='https://irsa.me/'>ARIS S RIPANDI</a></p>

		<p>No special configuration, you only need to configure Nginx and PHP-FPM.</p>

<pre><code class="language-php">
server {  
  listen 80;
  server_name yourdomain.tld;

  access_log /var/www/access.log;
  error_log  /var/www/error.log;

  root   /var/www;
  index  index.php index.html;

  location = /robots.txt {access_log off; log_not_found off;}
  location ~ /\. {deny all; access_log off; log_not_found off;}
  location / {
    try_files $uri $uri/ /index.php$args;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
  }
}
</code></pre>

	</section>

	<section id="s5">
		<div class="page-header">
			<h3>Install - IIS</h3>
			<hr class="notop">
		</div>
		
		<p>IIS with URL Rewrite module installed - <a href='http://www.iis.net/downloads/microsoft/url-rewrite'>http://www.iis.net/downloads/microsoft/url-rewrite</a></p>

		<p>For IIS the htaccess needs to be converted to web.config:</p>

		<pre><code class="language-php">
<?php ob_start();?>
<configuration>
    <system.webserver>
        <directorybrowse enabled="true"/>
	    <rewrite>
	    	<rules>
	    		<rule name="rule 1p" stopprocessing="true">
					<match url="^(.+)/$"/>
					<action type="Rewrite" url="/{R:1}"/>
				</rule>
				<rule name="rule 2p" stopprocessing="true">
					<match url="^(.*)$"/
					<action type="Rewrite" url="/index.php?{R:1}" appendquerystring="true"/>
				</rule>
			</rules>
		</rewrite>
	</system.webserver>
</configuration>
<?php echo htmlspecialchars(ob_get_clean());?>
		</code></pre> 

	</section>

	<section id="s6">
		<div class="page-header">
			<h3>Config</h3>
			<hr class="notop">
		</div>
		
		<p>Settings for the framework setup in app/core/config.php</p>

<pre><code class="language-php">
date_default_timezone_set('Europe/London');
</code></pre>

<p>Next set the application web URL, Once the DIR is set it can be used to get the application address.</p>
<pre><code class="language-php">
define('DIR','http://example.com/');
</code></pre>

<p>If using a database the database credentials will need adding:</p>
<pre><code class="language-php">
define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_NAME','database name');
define('DB_USER','root');
define('DB_PASS','root');
define('PREFIX','smvc_');
</code></pre>

<p>The prefix is optional but highly recommended, its very useful when sharing databases with other applications, to avoid conflicts. The prefix should be the starting patten of all table names like smvc_users</p>

<p>The framework provides a session helper class, in order to avoid session conflicts a prefix is used.</p>
<pre><code class="language-php">
define('SESSION_PREFIX','smvc_');
</code></pre>

<p>The following tells the framework what theme folder to use for views</p>

<pre><code class="language-php">
\helpers\session::set('template','default');
</code></pre>

	</section>

	<section id="s7">
		<div class="page-header">
			<h3>Routes</h3>
			<hr class="notop">
		</div>
		

<p>Routing lets you create your own url paths, based on the path you can load a closure or a controller!</p> 

<p>Routing is build into the v2.1 nothing needs to be called in order to use a route, you only need to create your route in index.php</p>

<p><b>Routing Setup</b></p>
<p>Namespace's are included into all classes now, a namespace is kind of like a layer. Adding a namespace to a class means their can be multiple classes with the same name as long as each is in a different namespace.</p>
<p>With routes the namespace is \Core\Router:: followed by the method call typing out the namespace every time is long winded, thankfully they shortcuts can be created by creating an alias: 

<pre><code class="language-php">
use core\router as Router;
</code></pre>

<p>By using the use keyword \core\router can be references as Router.</p> 

<p>To define a route call the static name Route:: followed by either a post or get ('any' can also be used to match both post and get requests) to match the HTTP action. Next set the path to match and what do, call a closure or a controller.</p>

<pre><code class="language-php">
Router::any('', 'closure or controller');
</code></pre>

<p><b>Closures</b></p>

<p>A closure is a function without a name, they are useful when you only need simple logic for a route, to use a closure first call Route:: then set the url pattern you want to match against followed by a function

<pre><code class="language-php">
Router::get('simple', function(){ 
  //do something simple
});
</code></pre>

<div class='alert alert-info'>
<p>Controllers and model can be used in a closure by instantiating the root controller</p>

<pre><code class="language-php">
$c = new core\controller();
$m = new models\users(); 

$m->get_users();
</code></pre>

<p>Having said that it's better to use a controller once you need access to a model.</p>
</div>

<p>Closures are convenient but can soon become messy.</p> 

<p><b>Controllers</b></p>

<p>To call a route to a controller instead of typing a function instead enter a string. In the string type the namespace of the controller (\controller if located in the root of the controllers folder) then the controller name, finally specify what method of that class to load. They are dictated by a @ symbol.</p>

<p>Say I have a controller called users (in the root of the controllers folder) and I want to load a userslist function:</p>
<pre><code class="language-php">
Route::get('users','\controllers\users@userslist');
</code></pre>

The above would call the users controllers and the userlist method when /users is in the url via a get request.

<div class='alert alert-info'>
<p>Routes can respond to both GET and POST requests</p>
<p>To use a post route:</p>
<pre><code class="language-php">
Router::post('blogsave', '\controllers\blog@savepost');
</code></pre>

<p>To respond to either a post or get request use any:</p>
<pre><code class="language-php">
Router::any('blogsave', '\controllers\blog@savepost');
</code></pre>
</div>

<p><b>Routing Filters</b></p>

<p>Routes can use filters to dynamically pass values to the controller / closures, their are 3 filters:</p>

<ol>
<li>any - can use characters or numbers</li>
<li>num - can only use numbers</li>
<li>all - will accept everything including any slash paths</li>
</ol>

<p>To use a filter place the filter inside parenthesis and use a colon inside route path</p>

<pre><code class="language-php">
Router::get('blog/(:any)', '\controllers\blog@post');
</code></pre>

Would get past to app/controllers/blog.php anything after blog/ will be passed to post method.

<pre><code class="language-php">
public function post($slug){
</code></pre>

<p>If there is no route defined, you can call a custom callback, like:
<pre><code class="language-php">
Router::error('\core\error@index');
</code></pre>

<p>Finally to run the routes:</p>
<pre><code class="language-php">
Router::dispatch();
</code></pre>

<h1>Full Example</h1>
<pre><code class="language-php">
use core\router as Router;

//define routes
Router::get('', '\controllers\welcome@index');

//call a controller in called users inside a admin folder inside the controllers folder
Router::('admin/users','\controllers\admin\users@list');

//if no route found
Router::error('error@index');

//execute matched routes
Router::dispatch();
</code></pre>

<h1>Legacy Calls</h1>

<p>To call controllers and methods by their name and not a custom route can be done by calling the controller file name followed by the method. Any params are passed as keys to an array so only one param is used.</p>

<p>For instance to call the welcome controller and a method called custom enter the url http://example.com/welcome/custom.</p>

<p>To load the index method: http://example.com/welcome the /index is not needed, it would be called automatically.</p>

	</section>

	<section id="s8">
		<div class="page-header">
			<h3>Controllers</h3>
			<hr class="notop">
		</div>
		
<p>Controllers are the bread and butter of the framework they control when a model is used and equally when to include a view for output. A controller is a class with methods, these methods are the outputted pages when used in conjunction with routes.</p>

<p>A method can be used for telling a view to show a page or outputting a data stream such as XML or a JSON array.  Put simply they are the logic behind your application.</p>

<div class='alert alert-info'>from v2.0 controllers can be placed in sub folders relative to the root of the controllers folder</div>

<p>Controllers are created inside the controllers folder. To create a controller, create a new file, the convention is to keep the filename in lowercase without any special characters or spaces.</p>

<p>Controllers will always use a namespace of controllers, if the file is directly located inside the controllers folder. If the file is in another folder that folder name should be part of the name space.</p>

<p>For instance a controller called blog located in controllers/blog would have a namespace of controllers/blog</p>

<p>Controllers need to use the main Controller; they extend it, the syntax is:</p>

<pre><code class="language-php">
namespace controllers;

class Welcome extends \core\controller {

}
</code></pre>

<p>Also the view class is needed to include view files you can either call the namespace then the view:</p>

<pre><code class="language-php">
\core\view::render();
</code></pre>

<p>Or create an alias at the top of the file then the alias can be used:</p>

<pre><code class="language-php">
namespace controllers;
use \core\view as View;

class Welcome extends \core\controller{

	public function __construct(){
		parent::__construct();
	}

	public function index(){	

		$data['title'] = 'Welcome';

		View::rendertemplate('header',$data);
		View::render('welcome/welcome',$data);
		View::rendertemplate('footer',$data);
	}
	
}
</code></pre>

<p>Controllers will need to access methods and properties located in the parent controller (app/core/controller.php) in order to do this they need to call the parent constructor inside a construct method.</p>

<pre><code class="language-php">
public function __construct(){
   parent::__construct();
}
</code></pre>

<p>The construct method is called automatically when the controller is instantiated once called the controller can then call any property or method in the parent controller that is set as public or protected.</p>

<p><b>The following properties are available to the controller</b></p>

<ul>
	<li>$view / object to use view methods</li> (this can be used instead of \core\view::)
	<li>_error($error) / calls the error function, passing in an error causing a 404 page with the error being displayed</li>
</ul>

<div class='alert alert-info'>
<p>Both models and helpers can be used in a constructor and added to a property then becoming available to all methods. The model or helper will need to use its namespace while being called</p>

<pre><code class="language-php">
namespace controllers;
use \core\view as View;

class Blog extends Controller {

	private $_blog;

	public function __construct(){
		parent::__construct();
		$this->_blog = new \models\blog();
	}

	public function blog(){
	   $data['title'] = 'Blog';
	   $data['posts'] = $this->_blog->get_posts();

	  View::render('blog/posts',$data);

	}

}
</code></pre>
</div>

<p><b>Methods:</b></p>

<ul>
<li>To use a model in a controller, create a new instance of the model. The model can be placed directly in the models folder or in a sub folder, For example: </li>
<pre><code class="language-php">
public function index(){
    $data = new \model\classname();
}
</code></pre>
</ul>

<p><b>Helpers:</b></p>

<ul>
<li>A helper can be placed directly in the helpers folder or in a sub folder.</li>
<pre><code class="language-php">
public function index(){
    //call the session helper
    \helpers\session::set('username','Dave');
}
</code></pre>
</ul>

<p>Load a view, by calling the view property and calling its render method, pass in the path to the file inside the views folder Another way is to call view::render. Here are both ways:</p> 

<pre><code class="language-php">
use \core\view as View;

public function index(){
       //default way
	   $this->view->render('welcome/welcome');
        
       //static way
       View::render('welcome/welcome');
}
</code></pre>

<p>A controller can have many methods, a method can call another method, all standard OOP behaviour is honoured.</p>
<p>Data can be passed from a controller to a view by passing an array to the view.</p>
<p>The array can be made up from keys. Each key can hold a single value or another array.</p>
<p>The array must be passed to the method for it to be used inside the view page or in a template (covered in the templates section)</p> 

<pre><code class="language-php">
$data['title'] = 'My Page Title';
$data['content'] = 'The contact for the page';
$data['users'] = array('Dave','Kerry','John');

View::render('contacts',$data);
</code></pre>

<p>Using a model is very similar, an array holds the results from the model, the model calls a method inside the model.</p>

<pre><code class="language-php">
$contacts = new \models\contacts();
$data['contacts'] = $contacts->getContacts();
</code></pre>
		
	</section>

	<section id="s9">
		<div class="page-header">
			<h3>Models</h3>
			<hr class="notop">
		</div>
		
<p>Models control the data source, they are used for collecting and issuing data, this could be from a remote service as XML,JSON or using a database to get and fetch records.</p>

<p>A Model is structured like a controller; it's a class. The model needs to extend the parent Model. Like a controller the constuctor needs to call the parent contstruct in order to gain access to its properties and methods.</p>

<pre><code class="language-php">
namespace models;
class Contacts extends \core\model {
	
	function __construct(){
		parent::__construct();
	}
	
}
</code></pre>

<p>The parent model is very simple it's only role is to create an instance of the database class located in (app/helpers/database.php) once set the instance is available to all child models that extend the parent model.</p>

<pre><code class="language-php">
namespace core;

class Model extends Controller {

	protected $_db;
	
	public function __construct(){
		//connect to PDO here.
		$this->_db = \helpers\database::get();

	}
}
</code></pre> 

<div class='alert alert-info'>Models can be placed in the root of the models folder it in sub-folders. The namespace used in the model should reflect its file path. Classes directly in the models folder will have a namespace of models or if in a folder: namespace \models\classname;</div>

<p>Methods inside a model are used for getting data and returning data back to the controller, a method should never echo data only return it, it's the controller that decides what is done with the data once it's returned.</p>

<p>The most common us of a model is for performing database actions, here is a quick example:</p>

<pre><code class="language-php">
public function getContacts(){
	return $this->_db->select('SELECT firstName,lastName FROM '.PREFIX.'contacts');
}
</code></pre>

<p>This is a very simple database query $this->_db is available from the parent model inside the $_db class holds methods for selecting,inserting,updating and deleting records from a MySQL database using PDO more on this topic in the section <a href='http://simplemvcframework.com/documentation/v2.1/documentation/database'>Database</a>.</p>

	</section>

	<section id="s10">
		<div class="page-header">
			<h3>Views</h3>
			<hr class="notop">
		</div>
		
<p>Views are the visual side of the framework, they are the html output of the pages. All views are located in the views folder. The actual views can be located directly inside the views folder or in a sub folder, this helps with organising your views.</p>

 <p>Views are called from controllers once called they act as included files outputting anything inside of them. They have access to any data passed to them from a data array.</p>

 <p>The parent view class has two methods render and rendertemplate:</p>

<pre><code class="language-php">
namespace core;
use helpers\session as Session;

class View {

	public function render($path,$data = false, $error = false){
		require "app/views/$path.php";
	}

	public function rendertemplate($path,$data = false){
		require "app/templates/".Session::get('template')."/$path.php";
	}
	
}
</code></pre>

 <p>The render method is used to include a view file, the method expects the path to the view. Optionally an array can be passed as well as an error array, this is useful for passing in errors for validation in a controller.</p>

 <p>The rendertemplate is almost the same except its use is for including templates, useful for including header and footer files for your application's design.</p>

 <p>The template folder used is dictated by the template set in the app/core/config file via a session.</p> 

 <p><b>Using a view from a controller</b></p>

 <p>A view can be set inside a method, an array can optionally be created and passed to both the render and rendertemplate methods, this is useful for setting the page title and letting a header template use it. 

<pre><code class="language-php">
 $data['title'] = 'Welcome';

 View::rendertemplate('header',$data);
 View::render('welcome/welcome',$data);
 View::rendertemplate('footer',$data);
</code></pre> 

 <p><b>Inside a view</b></p>

 <p>Views are normal php files they can contain php and html, as such any php logic can be used inside a view though its recommended to use only simple logic inside a view anything more complex is better suited inside a controller.</p>

 <p>An example of a view; looping through an array and outputting its contents:<p>

<pre><code class="language-php">
 &lt;p&gt;Contacts List &lt;/p&gt;
 &lt;?php 
 if($data['contacts']){
 	foreach($data['contacts'] as $row){
 		echo $row.' &lt;br /&gt;';
 	}
 }
 ?&gt;
 </code></pre>

	</section>

	<section id="s11">
		<div class="page-header">
			<h3>Templates</h3>
			<hr class="notop">
		</div>
		
<p>Templates are the site's markup, where images and js,css files are located. The default template is called default.<p>

<p>The structure of the default template is as follows:</p>
<ul>
<li>css/style.css</li>
<li>header.php</li>
<li>footer.php</li>
</ul>

<p>This is a very simple setup a regular site would have multiple css files and a javascript folder containing js files, an image folder. The rest of the sites pages come from the views.</p>

<p>Below is the contents of the header.php template, the title comes from an array with a key of title followed by the constant SITETITLE, this lets the controllers set the page titles in an array that is passed to the template</p>

<p>The url helper is being used to get the full path to the css file.</p>
<pre><code class="language-php">
&lt;!DOCTYPE html&gt;
	&lt;html lang=&quot;&lt;?php echo LANGUAGE_CODE; ?&gt;&quot;&gt;
	&lt;head&gt;

	&lt;!-- Site meta --&gt;
	&lt;meta charset=&quot;utf-8&quot;&gt;
	&lt;title&gt;&lt;?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in app/core/config.php ?&gt;&lt;/title&gt;

	&lt;!-- CSS --&gt;
	&lt;?php
	helpers\assets::css(array(
		'//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css',
		helpers\url::template_path() . 'css/style.css',
		))
	?&gt;</p>
&lt;/head&gt;
&lt;body&gt;
&lt;/body&gt;
</code></pre>

	</section>

	<section id="s12">
		<div class="page-header">
			<h3>Errors</h3>
			<hr class="notop">
		</div>
		
<p>In the event of an error or an exception, a custom error message is displayed:</p>

<blockquote>
An error occurred, The error has been reported to the development team and will be addressed asap. 
</blockquote> 

<p>This comes from the logger class in (app/core/logger.php) the actual error is recorded in errorlog.html located in the root of the framework, its advisable to move this above the public root. Any errors will be recorded in that file, ensuring no sensitive information it displayed on a page.

<blockquote>This does not apply to fatal errors</blockquote> 

<p>To disable the custom error logger comment out the following in app/core/config.php</p>
<pre><code class="language-php">
set_exception_handler('logger::exception_handler');
set_error_handler('logger::error_handler');
</code></pre>

To loop through and display errors without doing it yourself call the display method of the error class:
<pre><code class="language-php">
echo error::display($error); 
</code></pre>

	</section>

	<section id="s13">
		<div class="page-header">
			<h3>Languages</h3>
			<hr class="notop">
		</div>
		
<p>A new feature written by <a href='edwinhoksberg.nl'>Edwin Hoksberg</a> is a language component, this allows for different languages to be supported easily.</p>

<p>A language class exists inside the core folder, this class have 2 methods:<p>

<ol>
<li>Load - Loads the language file, can return the data and set the language to use</li>
<li>get - return an language string if it exists, else it will return false</li>
</ol>

<p>Inside the core/controller.php file the class is instantiated, resulting in $this->language being available to all controllers.</p>

<p>To use a language inside a controller use the following passing the file to be loaded located in the language/code/filename.php by default the language code will be en.</p>

<pre><code class="language-php">
$this->language->load('file/to/load');
</code></pre>

<p>The load method can also be passed if the data is to be returned with a true or false and the language code, useful to set a new language on the call:</p>

<pre><code class="language-php">
$this->language->load('file/to/load', false, 'nl');
</code></pre>

<p>The default language can be set in the config.php file:</p>

<pre><code class="language-php">
//set a default language
define('LANGUAGE_CODE', 'en');
</code></pre>

<p>Inside the language file set the text, each language should contain the same text in their own language for instance:</p>


<pre><code class="language-php">
//en
$lang['welcome_message'] = 'Hello, welcome from the welcome controller!';
</code></pre>

<pre><code class="language-php">
//nl
$lang['welcome_message'] = 'Hallo, welkom van de welcome controller!';
</code></pre>

<p>To use the language strings inside a controller, set a data array and call the get method passing the desired string to return:</p>

<pre><code class="language-php">
$data['welcome_message'] = $this->language->get('welcome_message');
</code></pre>

<p>Then in the view echo $data['welcome_message'] to print the chosen language.</p>

<h1>Welcome example</h1>

<pre><code class="language-php">
namespace controllers;
use core\view as View;

/*
 * Welcome controller
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @version 2.1
 * @date June 27, 2014
 */
class Welcome extends \core\controller{

	/**
	 * call the parent construct
	 */
	public function __construct(){
		parent::__construct();

		$this->language->load('welcome');
	}

	/**
	 * define page title and load template files
	 */
	public function index(){

		$data['title'] = 'Welcome';
		$data['welcome_message'] = $this->language->get('welcome_message');

		View::rendertemplate('header', $data);
		View::render('welcome/welcome', $data);
		View::rendertemplate('footer', $data);
	}

}
</code></pre>


	</section>

	<section id="s14">
		<div class="page-header">
			<h3>Helpers Overview</h3>
			<hr class="notop">
		</div>
		
<p>Helpers are classes that are not part of the core system but can greatly improve it by adding new features and possibilities.</p>

<p>This section documents the included helpers and how to use them. Since helpers are classes they can be added to the framework by placing them inside the helpers folder or in sub folders.</p>

<div class='alert alert-info'>All helpers will have a namespace of helpers if they are in a folder the folder name will be added to that namespace like namespace \helpers\phpmailer;.</div>


	</section>
	
	<section id="s15">
		<div class="page-header">
			<h3>Database</h3>
			<hr class="notop">
		</div>
		
<p>The database class is used to connect to a MySQL database using the connection details set in the root index.php file.</p>

<p>The constants (DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS) are used to connect to the database, the class extends PDO, it can pass the connection details to its parent construct.

<pre><code class="language-php">
try {
	parent::__construct(DB_TYPE.':host='.DB_HOST.'; dbname='.DB_NAME,DB_USER,DB_PASS);
	$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
	Logger::newMessage($e);
	customErrorMsg();
}
</code></pre>

<p>The error mode is set to use exceptions rather than failing silently in the event of an error. If there is an error they are recorded into the custom log file located in /errorlog.html</p>

<p>This class has the following methods:</p>

<ul>
<li><a href='#select'>select</a></li>
<li><a href='#insert'>insert</a></li>
<li><a href='#update'>update</a></li>
<li><a href='#delete'>delete</a></li>
<li><a href='#truncate'>truncate</a></li>
</ul>

<p>NOTE: The methods use prepared statements</p>

<a id='select'></a>
<p><b>Select</b></p>
<p>The SELECT query below will return a result set based upon the SQL statement. The result set will return all records from a table called contacts.</p> 

<pre><code class="language-php">
$this->_db->select("SELECT * FROM ".PREFIX."contacts");
</code></pre>

<p>Optionally an array can be passed to the query, this is helpful to pass in dynamic values, they will be bound to the query in a prepared statement this ensures the data never goes into the query directly and avoids any possible sql injection</p>

<p>Example with passed data:</p>
<pre><code class="language-php">
$this->_db->select("SELECT * FROM ".PREFIX."contacts WHERE contactID = :id", 
array(':id' => $id));
</code></pre>

<p>In this example there is a where condition, instead of passing in an id to the query directly a placeholder is used :id then an array is passed the key in the array matches the placeholder and is bound, so the database will get both the query and the bound data.</p>

<a id='insert'></a>
<p><b>insert</b></p>

<p>The insert method is very simple it expects the table name and an array of data to insert:
<pre><code class="language-php">
$this->_db->insert(PREFIX.'contacts',$data);
</code></pre>

The data array is created in a controller, then passed to a method in a model

<pre><code class="language-php">
$postdata = array(
    'firstName' => $firstName,
    'lastName'  => $lastName,                                 
    'email'     => $email                            
);

$this->_model->insert_contact($postdata); 
</code></pre>

<p>The model passes the array to the insert method along with the name of the table, optionally return the id of the inserted record back to the controller.
<pre><code class="language-php">
public function insert_contact($data){
	$this->_db->insert(PREFIX.'contacts',$data);
	return $this->_db->lastInsertId('contactID');
}
</code></pre>

<a id='update'></a>
<p><b>update</b></p>

<p>The update is very similar to insert an array is passed with data, this time also an identifier is passed and used as the where condition:

<p>Controller:</p>
<pre><code class="language-php">
$postdata = array(
    'firstName' => $firstName,
    'lastName'  => $lastName,                                 
    'email'     => $email                            
);

$where = array('contactID' => $id);

$this->_model->update_contact($postdata, $where); 
</code></pre>

<p>Model:</p>
<pre><code class="language-php">
public function update_job($data, $where){
	$this->_db->update(PREFIX.'contacts',$data, $where);
}
</code></pre>

<a id='delete'></a>
<p><b>delete</b></p>

<p>This method expects the name of the table and an array containing the columns and value for the where claus.</p>

<p>Example array to pass:</p>
<pre><code class="language-php">
$data = array('contactID' => $id);
</code></pre>

<p>Delete in model</p>

<pre><code class="language-php">
public function delete_contact($data){
	$this->_db->delete(PREFIX.'contacts', $data);
}
</code></pre>

<a id='truncate'></a>
<p><b>truncate</b></p>

<p>This method will delete all rows from the table, the method expects the table name as an argument.

<pre><code class="language-php">
public function delete_contact($table){
    $this->_db->truncate($table);
}
</code></pre>

	</section>

	<section id="s16">
		<div class="page-header">
			<h3>Password</h3>
			<hr class="notop">
		</div>
		
<p>The password file uses a password library from <a href='https://github.com/ircmaxell/password_compat'>https://github.com/ircmaxell/password_compat</a></p>

<p>The  library is intended to provide forward compatibility with the <a href="http://php.net/password">password_*</a> functions being worked on for PHP 5.5</p>

<p>This library requires PHP >= 5.3.7 OR a version that has the $2y fix backported into it (such as RedHat provides). Note that Debian's 5.3.3 version is <strong>NOT</strong> supported.</p>

<p>To create a hash of a password, call the make method and provide the password to be hashed, once done save the $hash.</p>

<pre><code class="language-php">$hash = \helpers\password::make($password);</code></pre>

<p>When logging in a user their hash must be retrieved from the database and compared against the provided password to make sure they match, for this a method called password_verify is used, it has 2 parameters the first is the user provided password the second is the hash from the database.</p>

<pre><code class="language-php">
    if(\helpers\password::verify($_POST['password'],$data[0]->password)){
     //passed
    } else {
     //failed
    }
</code></pre>

<p>From time to time you may update your hashing parameters (algorithm, cost, etc). So a function to determine if rehashing is necessary is available:</p>

<pre><code class="language-php">
if(\helpers\password::verify($password, $hash)) {     
   if(\helpers\password::needs_rehash($hash, $algorithm, $options)) {         
    $hash = \helpers\password::make($password, $algorithm, $options); /* Store new hash in db */     
   } 
}
</code></pre>

	</section>

	<section id="s17">
		<div class="page-header">
			<h3>Pagination</h3>
			<hr class="notop">
		</div>
		
<p>Break recordset into a series of pages</p>

<p>First create a new instance of the class pass in the number of items per page and the instance identifier, this is used for the GET parameter such as ?p=2</p>
<p>The set_total method expects the total number of records, either set this or pass in a call to a model that will return records then count them on return.</p>
<p>The method used to get the records will need a get_limit passed to it, this will then return the set number of records for that page.</p>
<p>Lastly a method called page_links will return the page links.</p>

<p>The model that uses the limit will need to expect the limit:
<pre><code class="language-php">
public function get_contacts($limit){
	return $this->_db->select('
		SELECT 
		*,
		(SELECT count(id) FROM '.PREFIX.'contacts) as total
	 FROM '.PREFIX.'contacts '.$limit);
}
</code></pre>

<p>Pagination concept</p>
<pre><code class="language-php">
//create a new object
$pages = new \helpers\paginator('1','p');
 
//calling a method to get the records with the limit set (_contacts would be the var holding the model data)
$data['records'] = $this->_model->get_contacts( $pages->get_limit() );
 
//set the total records, calling a method to get the number of records from a model
$pages->set_total( $data['records'][0]->total );
 
//create the nav menu
$data['page_links'] = $pages->page_links();
</code></pre>

<p>Usage example:</p>

<pre><code class="language-php">
$pages = new \helpers\paginator('50','p');
$data['records'] = $this->_model->get_contacts( $pages->get_limit() );
$pages->set_total($data['records'][0]->total);  
$data['page_links'] = $pages->page_links();
</code></pre>

	</section>

	<section id="s18">
		<div class="page-header">
			<h3>Sessions</h3>
			<hr class="notop">
		</div>
		
<p>The session is a static class, this means it can be used in any controller without needing to be instantiated, the class has an init method if session_start() has not been set then it starts it. This call is in place in (core/config.php) so it can already be used with no setup required.</p>

<p>The advantages of using a session class is all sessions are prefixed using the constant setup in the root index.php file, this avoid sessions clashing with other applications on the same domain.</p>

<p><b>Usage</b></p>

<p>Setting a session, call Session then ::set pass in the session name followed by its value
<pre><code class="language-php">
\helpers\session::set('username','Dave');
</code></pre>

<p>To retrieve an existing session use the get method:
<pre><code class="language-php">
\helpers\session::get('username');
</code></pre>

<p>Pull an existing session key and remove it, use the pull method:
<pre><code class="language-php">
\helpers\session::pull('username');
</code></pre>

<p>use id to return the session id:
<pre><code class="language-php">
\helpers\session::id();
</code></pre>

<p>Destroy a session key  by calling:
<pre><code class="language-php">
\helpers\session::destroy('mykey');
</code></pre>

<p>To look inside the sessions array, call the display method:
<pre><code class="language-php">
print_r(\helpers\session::display());
</code></pre>

	</section>

	<section id="s19">
		<div class="page-header">
			<h3>Url</h3>
			<hr class="notop">
		</div>
		
<p>The URL class is used for having handy methods or redirecting the page and returning the path to the current template.<p>

<p>Redirect - To redirect to another page instead of using a header call the static method redirect:</p>
<pre><code class="language-php">
\helpers\url::redirect('path/to/go');
</code></pre>

<p>previous - To be redirected back to the previous page:</p>
<pre><code class="language-php">
\helpers\url::previous();
</code></pre>

<div class='alert alert-info'>
<p>The redirect method can accept a 2nd option of true is used the path will be used as it is provided.</p>
<p>This is useful to redirect to an external website, by default the redirects are relative to the domain its on.</p>
</div>

<p>The url should be the local path excluding the application url for instance a valid case might be:</p>
<pre><code class="language-php">
\helpers\url::redirect('contacts');
</code></pre>
<p>The redirect method uses the DIR constant to get the application address.</p>

<p>The next method is get_template_path, this returns the path to the template relative from the templates folder, for instance by default it will return: http://www.example.com/templates/default/ this is useful for using absolute paths in your design files such as including css and js files.
<pre><code class="language-php">
\helpers\url::template_path();
</code></pre>

<h1>Autolinks</h1>

<p>Another useful feature is the ability to scan a block of text look for any domain names then convert them into html links.  To use the autolink call url:: followed by the method name and pass in the string to autolink:</p>

<pre><code class="language-php">
$string = "A random piece of text that contains google.com a domain.";
echo \helpers\url::autolink($string);
</code></pre>

<p>The autolink method also accepts a 2nd parameter that will be used as the click text for instance a in the text above I want the link to say Google and not google.com</p>

<pre><code class="language-php">
$string = "A random piece of text that contains google.com a domain.";
echo \helpers\url::autolink($string,'Google');
</code></pre>

<p>When run the link word will be Google which will link to http://google.com</p>

	</section>

	<section id="s20">
		<div class="page-header">
			<h3>PHPMailer</h3>
			<hr class="notop">
		</div>

<p>PHPMailer is a third party class for sending emails, Full docs are available at <a href='https://github.com/Synchro/PHPMailer'>https://github.com/Synchro/PHPMailer</a></p>

<p>To use PHPMailer create a new instance of it:</p>

<pre><code class="language-php">
$mail = new \helpers\phpmailer\mail();
</code></pre>

<p>Once an instance has been created all the properties are available to you, a typical example:</p>

<pre><code class="language-php">
$mail = new \helpers\phpmailer\mail();
$mail->setFrom('noreply@domain.com');
$mail->addAddress('user@domain.com');
$mail->subject('Important Email');
$mail->body("<h1>Hey</h1><p>I like this <b>Bold</b> Text!</p>");
$mail->send();
</code></pre>

<p>The class has the ability to send via SMTP in order to do so edit helpers/phpmailer/mail.php and enter your SMTP settings you can also set a default email from address so you don't have to supply it each time:</p>

<pre><code class="language-php">
public $From     = 'noreply@domain.com';
public $FromName = SITETITLE;
public $Host     = 'smtp.gmail.com';
public $Mailer   = 'smtp';
public $SMTPAuth = true;                         
public $Username = 'email@domain.com';                         
public $Password = 'password';                         
public $SMTPSecure = 'tls';                         
public $WordWrap = 75;
</code></pre>

<p>You don't need to specify a plain text version of the email to be sent out, this is done automatically from the supplied body.</p>

	</section>

	<section id="s21">
		<div class="page-header">
			<h3>Document</h3>
			<hr class="notop">
		</div>

<p>The document class is a collection of useful methods for working with files.<p>

<p>To get the extension of a file call the getExtension method and pass the file name, the extension will then be returned.</p>

<pre><code class="language-php">
\helpers\document::getExtension('customfile.zip');
//returns zip
</code></pre>

<p>To find out the size in a human readable way call the formatBytes method:</p>

<pre><code class="language-php">
\helpers\document::formatBytes('4562');
//returns 4.46 KB
</code></pre>

<p>Using the getFileType a filename is passed and returned is the name of the group that extension belongs to if no matches that 'Other' is returned.</p>
<p>These are the current groups:</p>
<pre><code class="language-php">
$images = array('jpg', 'gif', 'png', 'bmp');
$docs   = array('txt', 'rtf', 'doc', 'docx', 'pdf');
$apps   = array('zip', 'rar', 'exe', 'html');
$video  = array('mpg', 'wmv', 'avi', 'mp4');
$audio  = array('wav', 'mp3');
$db     = array('sql', 'csv', 'xls','xlsx');
</code></pre>

<pre><code class="language-php">
\helpers\document::getFileType('customfile.zip');
//returns Application
</code></pre>

	</section>

	<section id="s22">
		<div class="page-header">
			<h3>Parsedown</h3>
			<hr class="notop">
		</div>

<p>Parsedown is a Markdown parser in PHP</p>
<p>To use the class make a call to the class then reference instance()->parse() passing a variable containing the desired markdown content.</p>

<pre><code class="language-php">
$data = '
#Hello 
##_Parsedown_!
[Google](http://google.com)
';
echo \\helpers\\parsedown::instance()->parse(htmlspecialchars($data));
</code></pre>

<p>More examples at <a href='http://parsedown.org/'>http://parsedown.org/</a>

	</section>

	<section id="s23">
		<div class="page-header">
			<h3>Captcha with Raincaptcha</h3>
			<hr class="notop">
		</div>
		
<p>This class can validate CAPTCHA images with RainCaptcha.</p>
<p>It can generate an URL to display a CAPTCHA validation image served by the RainCaptcha service.</p>
<p>The class can also send a request to RainCaptcha API to verify if the text that the user entered matches the text in the CAPTCHA image.</p>
<p>RainCaptcha is a CAPTCHA class that does not require any image processing extensions (GD, ImageMagick, etc). CAPTCHA was developed to be readable by humans and resistant to OCR software. It generates black-and-white images with 5 distorted letters on them and noise. Its checking algorithm is case-insensitive.</p>
<p>To use the class create a new instance, this can then be used to generate a captcha image using ->getImage()</p>

<pre><code class="language-php">
&lt;?php $rainCaptcha = new \\helpers\\raincaptcha(); ?&gt;

<p>&lt;img id=&quot;captchaImage&quot; src=&quot;&lt;?php echo $rainCaptcha-&gt;getImage(); ?&gt;&quot; /&gt;</p>
<p>&lt;input name=&quot;captcha&quot; type=&quot;text&quot; /&gt;</p>
<p>&lt;button type=&quot;button&quot; class='btn btn-danger' onclick=&quot;document.getElementById('captchaImage').src = </p>
<p>'&lt;?php echo $rainCaptcha-&gt;getImage(); ?&gt;&amp;morerandom=' + Math.floor(Math.random() * 10000);&quot;&gt;&lt;span class=&quot;icon icon-refresh&quot;&gt;&lt;/span&gt;&lt;/button&gt;</p>

</code></pre>

<p>To check if a user's input matches the captcha</p>

<pre><code class="language-php">
$rainCaptcha = new \\helpers\\raincaptcha();

if(!$rainCaptcha->checkAnswer($_POST['captcha'])){

    die('You have not passed the CAPTCHA test!');

}
</code></pre>

	</section>

	<section id="s24">
		<div class="page-header">
			<h3>GUMP Validation</h3>
			<hr class="notop">
		</div>

<p>GUMP is a fast, extensible & stand-alone PHP input validation class that allows you to validate any data.</p>

<p>To use the class call it by using the namespace \helpers\gump</p>

<p>An example validating a username and password:</p>
<pre><code class="language-php">
$is_valid = \helpers\gump::is_valid($_POST, array(
    'username' => 'required|alpha_numeric',
    'password' => 'required|max_len,100|min_len,6'
));

if($is_valid === true) {
    // continue
} else {
    print_r($is_valid);
}
</code></pre>

<p>To pass the error array to the view pass $is_valid (this will hold any errors) as a third param to the render method.</p>
<p>To loop through and display errors without doing it yourself call the display method of the error class:</p>
<pre><code class="language-php">
echo \core\error::display($error); 
</code></pre>

<p>For further examples see  <a href='https://github.com/Wixel/GUMP'>https://github.com/Wixel/GUMP</a> remember to replace GUMP with \helpers\gump</p>

	</section>

	<section id="s25">
		<div class="page-header">
			<h3>Table Builder</h3>
			<hr class="notop">
		</div>
	
<p>Table builder helper is a class that would help you to create tables in MySQL (primarily) 
without really going into details of SQL query.</p>

<h2>Features</h2>

<p>Table builder allows you to add rows, aliases, set primary key, default values, table name and options.</p>

<h2>Start working with Table Builder</h2>

<p>To start working with Table Builder, you need to create instance class of 
\helpers\tableBuilder. Preferably for performance, create your instance class in any model
and pass it \helpers\database instance to avoid duplicating database connection.</p>

<pre><code class="language-php">
	private $tableBuilder;
	
	// Declaration of constructor in new model
	public function __construct () {
		parent::__construct();
		
		// Example of reusing database and avoiding duplicating of database connection
		$this->tableBuilder = new \helpers\tableBuilder($this->db);
	}
</code></pre>

<p>After initiating new table builder instance you can work with it.</p>

<div class='alert alert-info'>WARNING: Table builder automatically creates a `id` field of type `INT(11)` with `AUTO_INCREMENT` and sets is `PRIMARY KEY`.
If you want to set your own name or don't want to have id field, pass `false` as second parameter.
</div>

<h2>Creating simple table</h2>

<p>Now we can create simple table, let's create table for comments:</p>

<pre><code class="language-php">
// Another model's instance
public function createCommentTable () {
	// First argument is field name and second is type or alia
	$this->tableBuilder->addField('author', 'VARCHAR(40)');
	$this->tableBuilder->addField('message', 'TEXT');
	$this->tableBuilder->setName('comments');
	$this->tableBuilder->create();
}
</code></pre>


<p>This example of code would create table named `comments` with `id`, `author` and `message` fields. </p>
<p>If you would try to run this code again you'll see error. To prevent that let's set `IF NOT EXISTS` to true:</p>

<pre><code class="language-php">
// First argument is field name and second is type or alia
$this->tableBuilder->addField('author', 'VARCHAR(40)');
$this->tableBuilder->addField('message', 'TEXT');
$this->tableBuilder->setName('comments');
$this->tableBuilder->setNotExists(TRUE);
$this->tableBuilder->create();
</code></pre>

<p>Now your code shouldn't show any errors.</p>

<h2>Aliases</h2>

<p>Table builder supports aliases instead of using SQL types in `addField` method.
There's only 3 included types: int `INT(11)`, string `VARCHAR(255)` and description `TINYTEXT`.</p>

<p>You can add globally your own alias, for example, in config:</p>

<pre><code class="language-php">
// configs above

\helpers\tableBuilder::setAlias('name', 'VARCHAR(40)');

// configs below

</code></pre>

<h2>Methods</h2>

<h3>addField</h3>

<p>Method `addField` is used to create field in query:</p>

<pre><code class="language-php">
$tableBuilder->addField ($field_name, $type_or_alias, $is_null, $options);
</code></pre>

<p>`field_name` is your name for the field, `type_or_alias` is defined type in MySQL or an alias
defined in tableBuilder, `is_null` is by default is FALSE (so it's not null) but you can
set it to `TRUE` if you needed and options are additional options such as `AUTO_INCREMENT`
or `CURRENT_TIMESTAMP`.</p>

<p>Example of setting field date with `CURRENT_TIMESTAMP`:</p>

<pre><code class="language-php">
$tableBuilder->addField('date', 'TIMESTAMP', FALSE, \helpers\tableBuilder::CURRENT_TIMESTAMP);
</code></pre>

<h3>setDefault</h3>

<p>Method `setDefault` is used to determine default value of field in query. There's the example:</p>

<pre><code class="language-php">
$tableBuilder->setDefault('group_id', 0);
</code></pre>

<p>This example is illustrating how to set default user `group_id` in table.</p>

<div class='alert alert-info'>
	WARNING: Don't use setDefault for timestamps, use `addField` with last argument `\helpers\tableBuilder::CURRENT_TIMESTAMP` instead.
</div>

<h3>create</h3>

<p>Method `create` is used to finish the query and create table in the database:</p>

<pre><code class="language-php">
$table->create();
</code></pre>

<p>You can pass `TRUE` as first argument to reset the tableBuilder and then create another table reusing the same class.</p>

<h3>reset</h3>

<p>Method `reset` resets all properties in tableBuilder in order you could start constructing table from beginning. Use it if you need to add construct another table instead of creating new instance of table builder.</p>

<h2>Debugging</h2>

<p>If you run into some errors with table builder, you can debug SQL code by calling getSQL method:</p>


<pre><code class="language-php">
// Some code ...

echo $this->tableBuilder->getSQL();
</code></pre>

	</section>

	<section id="s26">
		<div class="page-header">
			<h3>Simple Curl</h3>
			<hr class="notop">
		</div>

<p>The simplecurl class is there to curl data from RESTful services. A lot of companies use it nowadays for example twitter, google and facebook.</p>
<p>There are four methods available these are get, post and put.</p>
<div class="alert alert-info">
	<p>You will need to declare the simplecurl helper first to use these examples below. You can do it by adding a use statement at the top of the controller.</p>
</div>

<pre><code class="language-php">
use \helpers\Simplecurl as Curl
</code></pre>

<h2>How to do a get request</h2>
<p>This example will show you how to a get request to get the current bitcoin prices from coinbase</p>

<pre><code class="language-php">
// Get the spot price of a bitcoin it returns a json object.
$spotrate = Curl::get('https://coinbase.com/api/v1/prices/spot_rate');
$data['spotrate'] = json_decode($spotrate);
</code></pre>

<p>The get request returned the data as json data we encoded it and passed it to our view.</p>
<p>Inside your view you could simply do</p>

<pre><code class="language-php">
echo $data['spotrate']->amount
echo $data['spotrate']->currency
</code></pre>

<p>This should print out the currency and rate.</p>
<h2>How to do a post request</h2>
<p>This example will show you how to post a gist to github gists</p>

<pre><code class="language-php">
	
// Post a gist to github
$content = "Hello World!";
$response = Curl::post('https://api.github.com/gists', json_encode(array(
  'description' => 'PHP cURL Post Test',
  'public' => 'true',
  'files' => array(
    'Test.php' => array(
      'content' => $content,
    ),
  ),
)));
	
</code></pre>
<p>The response will be details of the file and the url where it's located.</p>
<h2>How to do a put request</h2>
<p>This example will show you how to do a put request to httpbin a test service for curl.</p>
<pre><code class="language-php">
	
$response = curl::put('http://httpbin.org/put', array(
  'id' => 1,
  'first_name' => 'Simple',
  'last_name' => 'MVC'
));
	
</code></pre>

	</section>

	</div>
</body>
</html>
