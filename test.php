<?php
$string = "1abc2def3ghi4jklm5nop6qrs7tuv8wqy9zASDFGHJKLZXCVBNM<>?:}{QWERTYUIOP+_!@/$%^&*()-~`";
echo substr(str_shuffle($string), 0, 64);