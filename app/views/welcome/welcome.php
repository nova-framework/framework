<?php
/**
 * Sample layout
 */

use Core\Language,
	Helpers\Form;

?>

<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<p><?php echo $data['welcome_message'] ?></p>

<a class="btn btn-md btn-success" href="<?php echo DIR;?>subpage">
	<?php echo Language::show('open_subpage', 'Welcome'); ?>
</a>
<hr>
<?php
echo Form::openPOST(["role"=>"form"]);
echo '<br><label>Button Button</label><br>';
echo Form::buttonButton(["value"=>"test",
					     "class"=>["span","glyphicon glyphicon-off"]
						],
						["class"=>"btn btn-md btn-success",
						 "disabled"]);

echo '<br><br><label>Input Button</label><br>';
echo Form::buttonInput(["class"=>"btn btn-md btn-danger",
						"value"=>"Button Input"]);

echo '<br><br><label>Text Input</label><br>';
echo Form::textInput(["class"=>"form-control",
		 			  "placeholder"=>"placeholder text"]);

echo '<br><br><label>Password Input</label><br>';
echo Form::passwordInput(["class"=>"form-control",
						  "placeholder"=>"placeholder password"]);

echo '<br><br><label>Checkboxes</label><br>';
echo Form::checkbox(["Check 1" => [["id"=>"check1"],["class"=>"haha"]],
					 "Second" => [["class"=>"tiger"],["checked ","class"=>"form-control"]]]);
echo Form::close();

?>