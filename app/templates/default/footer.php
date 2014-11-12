</div>

<!--
    You can use jquery from JS dir or get latest script from jQuery servers
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
-->

<script src="<?php echo \helpers\url::get_template_path();?>js/jquery.js"></script>

<!-- JS plugins -->
<?php if(isset($data['js'])) : ?>
    <?php echo $data['js']."\n";?>
<?php endif; ?>

<!-- JS script -->
<?php if(isset($data['jq'])) : ?>
    <script>
        $(document).ready(function(){
            <?php echo $data['jq']."\n";?>
        });
    </script>
<?php endif; ?>

</body>
</html>