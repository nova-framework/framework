<?php
/**
 * Example form
 * This is best seen while its in the browser since this code is a bit messy and can easily get lost
 */

use Helpers\Form;

?>

<div class="page-header">
    <h1><?php echo $data['title'] ?></h1>
</div>


<div class="well">

    <!--  Form  -->
    <div class="row">
        <h3>Form Example</h3>

        <div class="col-md-3">
            <?php
            echo Form::openPOST(["class" => "aClass", "role" => "form"]);
            echo "There is a form here, you can see it if you inspect element on your browser";
            echo Form::close();
            ?>
        </div>
        <div class="col-md-9">
            Attributes are optional, if you want to use it
            make sure its in an array and the key-value pair both have to be strings even if its a number
            <pre>
<?php echo htmlentities("
//example attributes
\$options = [\"class\"=>\"test_form\",
            \"id\"=>\"small_test_form\",
            \"autocomplete\"=>\"on\",
            \"action\"=>\"upload\",
            \"enctype\"=>\"multipart/form-data\"];

echo Form::openPOST(\$options)    //starts the form with method=\"POST\" with attributes
echo Form::openGET()             //starts the form with method=\"GET\"

echo Form::close()               //ends the form");
?>
            </pre>
        </div>
    </div>

    <hr>

    <!--  Input  -->
    <div class="row">
        <h3>Inputs Example</h3>

        <div class="col-md-3">

            <?php
            $password = ["class" => "form-control",
                "disabled ",
                "value" => "text1"];
            $subBut = ["class" => "form-control btn btn-md btn-danger",
                "value" => "Submit Input"];
            echo Form::textInput(["class" => "form-control", "placeholder" => "Text Input"]);
            echo Form::passwordInput($password) . "<br>";
            echo Form::fileInput($password) . "<br>";
            echo Form::submitInput($subBut);
            ?>
        </div>
        <div class="col-md-9">
            If you want radio you have to have radio in an array.
            <p><code>["Attribute"=>"Value","Attribute "]</code></p>

            <p>All possible Inputs (IE,FF,Chrome,Safari,Opera does not support some of the listed)</p>
            <ul>
                <li>fileInput</li>
                <li>hiddenInput</li>
                <li>textInput</li>
                <li>passwordInput</li>
                <li>submitInput</li>
                <li>buttonInput</li>
                <li>numberInput</li>
                <li>dateInput</li>
                <li>colorInput</li>
                <li>rangeInput</li>
                <li>monthInput</li>
                <li>weekInput</li>
                <li>timeInput</li>
                <li>datetimeInput</li>
                <li>emailInput</li>
                <li>searchInput</li>
                <li>telInput</li>
                <li>urlInput</li>
            </ul>
            <p>If you do not put key-value pair, make sure there is a space after the text</p>
            <pre>
<?php echo htmlentities("
\$textBox = [\"class\"=>\"form-control\",
             \"placeholder\"=>\"Text Box\",
             \"name\"=>\"text1\",
             \"rows\"=>\"3\"];
\$password = [\"class\"=>\"form-control\",
             \"disabled \",
             \"value\"=>\"text1\"];
\$subBut = [\"class\"=>\"form-control btn btn-md btn-danger\",
            \"value\"=>\"Submit Input\"];
echo Form::textBox(\$textBox);
echo Form::passwordInput(\$password);
echo Form::fileInput(\$password);
echo Form::submitInput(\$subBut);
");
?>
            </pre>
        </div>
    </div>

    <hr>

    <!--  Text Area  -->
    <div class="row">
        <h3>Text Area Example</h3>

        <div class="col-md-3">

            <?php
            $textarea = ["class" => "form-control",
                "rows" => "5"];
            $text = "This is default text";
            echo Form::textBox($textarea,$text);
            ?>
        </div>
        <div class="col-md-9">
            If you want radio you have to have radio in an array.
            <p><code>["Attribute"=>"Value","Attribute "]</code></p>

            <p>If you do not put key-value pair, make sure there is a space after the text</p>
            <pre>
<?php echo htmlentities("
\$textarea = [\"class\" => \"form-control\",
             \"rows\" => \"5\"];
\$text = \"This is default text\";
echo Form::textBox(\$textarea,\$text);
");
?>
            </pre>
        </div>
    </div>

    <hr>

    <!--  Select  -->
    <div class="row">
        <h3>Select Example</h3>

        <div class="col-md-3">

            <?php
            $attributes1 = ["class" => "form-control",
                "id" => "regular"];
            $attributes2 = ["class" => "form-control",
                "id" => "regular",
                "multiple ",
                "disabled "];
            $selects = ["option1" => "Option 1",
                "anotheroption" => "Another option",
                "more" => "more",
                "want" => ["selected", "This is selected"]];
            echo Form::select($attributes1, $selects);
            echo Form::select($attributes2, $selects);
            ?>
        </div>
        <div class="col-md-9">
            <p>Select takes 2 arrays, 1st array is select's <code>[attributes=>value]</code></p>

            <p>2nd array is the options <code>[value=>text,text,value=>['selected',text],['selected',text]]</code> If
                you use selected, make sure selected is first then the text</p>

            <p>if you do not use value, text is set to value</p>
            <pre>
<?php echo htmlentities("
\$attributes1 = [\"class\"=>\"form-control\",
                \"id\"=>\"regular\"];
\$attributes2 = [\"class\"=>\"form-control\",
                \"id\"=>\"regular\",
                \"multiple \",
                \"disabled \"];
\$selects = [\"option1\"=>\"Option 1\",
          \"anotheroption\"=>\"Another option\",
          \"more\"=>\"more\",
          \"want\"=>[\"selected\",\"This is selected\"]];
echo Form::select(\$attributes1, \$selects);
echo Form::select(\$attributes2, \$selects);
");
?>
            </pre>
        </div>
    </div>

    <hr>

    <!--  Checkbox  -->
    <div class="row">
        <h3>Checkbox and Radio Buttons Example</h3>

        <div class="col-md-3">

            <?php
            $checks = ["Uno" => [["id" => "numbero"],
                ["class" => "form-control",
                    "checked ",
                    "name" => "numba[]"]
            ],
                "Triple" => [["id" => "two"],
                    ["class" => "form-control",
                        "disabled ",
                        "name" => "numba[]"]
                ]
            ];
            echo Form::checkbox($checks);
            echo "<br>";
            echo Form::radio($checks);
            ?>
        </div>
        <div class="col-md-9">
            If you want checkboxes/radio you have to have checkboxes/radio in an array.
            <p><code>["Label"=>["LabelAttribute"=>"LabelAttributeValue"],["Checkbox1Attribute"=>"Checkbox1AttributeValue"]]</code>
            </p>

            <p>Each label points to an array, 1st element array is the label's attribute, 2nd element array is
                checkbox/radio attributes.</p>

            <p>If you do not put key-value pair, make sure there is a space after the text</p>
            <pre>
<?php echo htmlentities("
\$params = [\"Uno\" => [[\"id\"=>\"numbero\"],          //1st label
                     [\"class\"=>\"form-control\",   //1st checkbox/radio attributes
                      \"checked \",  //add space after the word
                      \"name\"=>\"numba[]\"]],
           \"Triple\" => [[\"id\"=>\"two\"],            //2nd label
                        [\"class\"=>\"form-control\", //2nd checkbox/radio attributes
                         \"disabled \", //add space after the word
                         \"name\"=>\"numba[]\"]]];

echo Form::checkbox(\$params);                      //Create checkbox(s)
echo Form::radio(\$params);                         //Create radio(s)
");
?>
            </pre>
        </div>
    </div>

    <hr>

    <!--  Buttons  -->
    <div class="row">
        <h3>Buttons Example</h3>

        <div class="col-md-3">

            <?php
            $values = ["value" => "Reset Button ",
                "class" => ["span","glyphicon glyphicon-off"]];
            $values1 = ["value" => "Button Button "];
            $values2 = ["value" => "Submit Button "];
            $attributes = ["class"=>"btn btn-md btn-success form-control"];
            $attributes1 = ["class"=>"btn btn-md btn-danger"];
            $attributes3 = ["disabled "];
            echo Form::resetButton($values,$attributes);
            echo "<br><br>";
            echo Form::buttonButton($values1,$attributes1);
            echo "<br><br>";
            echo Form::submitButton($values2,$attributes2);
            ?>
        </div>
        <div class="col-md-9">
            If you want radio you have to have radio in an array.
            <p>Array 1: Button Text <code>["value"=>text, "class"=>[tag,class]]</code></p>
            <p>Array 2: Button Attribute <code>["Attribute"=>"Value","Attribute "]</code></p>
            <pre>
<?php echo htmlentities("
\$values = [\"value\" => \"Reset Button \",
    \"class\" => [\"span\",\"glyphicon glyphicon-off\"]];
\$values1 = [\"value\" => \"Button Button \"];
\$values2 = [\"value\" => \"Submit Button \"];
\$attributes = [\"class\"=>\"btn btn-md btn-success form-control\"];
\$attributes1 = [\"class\"=>\"btn btn-md btn-danger\"];
\$attributes3 = [\"disabled \"];
echo Form::resetButton(\$values,\$attributes);
echo Form::buttonButton(\$values1,\$attributes1);
echo Form::submitButton(\$values2,\$attributes2);
");?>
            </pre>
        </div>
    </div>
</div>
