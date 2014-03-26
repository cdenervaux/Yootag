<?php
$uid = substr(sha1(uniqid(mt_rand(), true)),0,8);
echo ($uid);
?>