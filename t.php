<?php

use Parable\GetSet\Collection\FilesCollection;

require 'vendor/autoload.php';

$files = new FilesCollection();

var_dump($files->getAll());

?>

<form enctype="multipart/form-data" method="POST">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Name of input element determines name in $_FILES array -->
    Send this file: <input name="userfile" type="file" />
    Send this file: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>
