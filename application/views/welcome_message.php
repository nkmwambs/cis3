<?php

use Aws\S3\Exception\S3Exception;


defined('BASEPATH') or exit('No direct script access allowed');

require 'application/start.php';


//Upload Files in Amazon S3
if (isset($_FILES['file'])) {
	$file = $_FILES['file'];

	//File Details
	$name = $file['name'];
	$tmp_name = $file['tmp_name'];

	$extension = explode('.', $name);

	$extension = strtolower(end($extension));

	//Temp details
	$key = md5(uniqid());
	$tmp_file_name = "{$key}.{$extension}";
	$tmp_file_path = "application/uploads/{$tmp_file_name}";

	//var_dump($tmp_file_path);

	move_uploaded_file($tmp_name, $tmp_file_path);
    $result='';
	try {
		$s3Client->putObject([
			'Key' => "{$name}",
			'Bucket' => $config['s3']['bucket'],
			'ACL' => 'public-read',
			'Body' => fopen($tmp_file_path, 'rb')


		]);
		//Remove the temp files after gabbage collection for the S3 guzzlehttp to release resources 
		
		gc_collect_cycles();
		unlink($tmp_file_path);
		
	} catch (S3Exception $s3Ex) {

		die("An exception occured. {$s3Ex}");
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">
		::selection {
			background-color: #E13300;
			color: white;
		}

		::-moz-selection {
			background-color: #E13300;
			color: white;
		}

		body {
			background-color: #fff;
			margin: 40px;
			font: 13px/20px normal Helvetica, Arial, sans-serif;
			color: #4F5155;
		}

		a {
			color: #003399;
			background-color: transparent;
			font-weight: normal;
		}

		h1 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 19px;
			font-weight: normal;
			margin: 0 0 14px 0;
			padding: 14px 15px 10px 15px;
		}

		code {
			font-family: Consolas, Monaco, Courier New, Courier, monospace;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}

		#body {
			margin: 0 15px 15px 15px;
		}

		p.footer {
			text-align: right;
			font-size: 11px;
			border-top: 1px solid #D0D0D0;
			line-height: 32px;
			padding: 0 10px 0 10px;
			margin: 20px 0 0 0;
		}

		#container {
			margin: 10px;
			border: 1px solid #D0D0D0;
			box-shadow: 0 0 8px #D0D0D0;
		}
	</style>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</head>

<body>

	<div id="container">
		<h1>Welcome to CodeIgniter!</h1>

		<div id='body'>

			<div class='row'>
				<div class='col-xs-4'>
					<?php echo form_open(base_url() . 'index.php/welcome/add_department', array('id' => 'frm_department', 'class' => 'form-horizontal form-groups-bordered validate', 'enctype' => 'multipart/form-data')); ?>
					<div class='form-group'>
						<label class='control-label col-xs-2'>Department Name</label>
						<div class='col-xs-10'>
							<input type='text' class='form-control' name='department_name' />
						</div>
					</div>

					<div class='form-group'>
						<div class='col-xs-12'>
							<button class='btn btn-default'>Save</button>
						</div>
					</div>
					</form>
				</div>

				<div class='col-xs-4'>
					<h1>Read Database</h1>
					<?php
					$departments = $this->read_db->select(array('department_id', 'name'))->get_where('department')->result_array();
					?>

					<ul>
						<?php foreach ($departments as $department) { ?>
							<li><?= $department['name']; ?>
								<a href='<?= base_url() ?>index.php/welcome/delete_department/<?= $department['department_id'] ?>'><i class='fa fa-trash'></i></a>
								<a href='<?= base_url() ?>index.php/welcome/update_department/<?= $department['department_id'] ?>'><i class='fa fa-pencil'></i></a>
							</li>
						<?php } ?>
					</ul>
				</div>

				<div class='col-xs-4'>
					<h1>Write Database</h1>
					<?php
					$departments = $this->write_db->select(array('department_id', 'name'))->get_where('department')->result_array();
					?>

					<ul>
						<?php foreach ($departments as $department) { ?>
							<li><?= $department['name']; ?>
								<a href='<?= base_url() ?>index.php/welcome/delete_department/<?= $department['department_id'] ?>'><i class='fa fa-trash'></i></a>
								<a href='<?= base_url() ?>index.php/welcome/update_department/<?= $department['department_id'] ?>'><i class='fa fa-pencil'></i></a>
							</li>
						<?php } ?>
					</ul>
				</div>

			</div>
		</div>
		<hr>


		<div>
			<center>
				<h3>List of Files From AWS-S3</h3>
			</center>
		</div>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>File Name</th>
					<th>Download Link</th>
				</tr>
			</thead>

			<tbody>
				<?php
				foreach ($objects as $object) { 
					
					$cmd = $s3Client->getCommand('GetObject', [
						'Bucket' => $config['s3']['bucket'],
						'Key' => $object['Key']
					   ]);
					   $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
					   
					   $presignedUrl = (string)$request->getUri()
					?>
					<tr>
						<td><?= $object['Key']; ?></td>
						<td><a target='__blank' href='<?=$presignedUrl;?>' download='<?= $object['Key']; ?>'>Download</a></td>
						
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<hr>
		<div class='well'>
			<center>
				<h3>Upload Area For Files To AWS-S3</h3>
			</center>
		</div>
		<form action='<?= $_SERVER['PHP_SELF']; ?>' method="POST" enctype="multipart/form-data">
			<input type='file' name='file'>
			<input type='submit' value='Upload'>
		</form>

		<?php

		//print_r($this->s3->listBuckets());

		?>
		<!-- <img src="<?= $img; ?>"/> -->
</body>

</html>