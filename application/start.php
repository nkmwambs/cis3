<?php

use Aws\S3\S3Client;
require 'vendor/autoload.php';
require('config/s3_config.php');

$s3Client = new S3Client([
    'profile' => 'default',
    'region' => 'eu-west-1',
    'version' => '2006-03-01'
   ]);
   