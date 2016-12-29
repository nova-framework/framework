<?php
/**
 * FileField Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */


return array(
    'path'        => base_path('files/:class_slug/:attribute/:unique_id-:file_name'),
    'defaultPath' => base_path('files/default.png')
);

