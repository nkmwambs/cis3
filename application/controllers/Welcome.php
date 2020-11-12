<?php
defined('BASEPATH') or exit('No direct script access allowed');
//require 'vendor/autoload.php';



use Aws\S3\S3Client;
use Aws\Exception\AwsException;

use Aws\Iam\IamClient; 


require 'application/start.php';

class Welcome extends MY_Controller
{

	//require 'application/start.php';



	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
	 public $write_db = null;
	 public $read_db = null;
	

	function __construct()
	{
		parent::__construct();

		$this->load->library('session');
		//$this->load->library('S3');

		//$this->load->library('AwsS3');

		$this->read_db = $this->load->database('read_db', true);
		$this->write_db = $this->load->database('write_db', true);

	}

	public function index()
	{
		$page_data['objects']=$this->get_objects();
		$this->load->view('welcome_message',$page_data);
	}
	function list_buckets()
	{
		$page_data['buckets'] = $this->AwsS3->list_buckets();
	}
	function get_objects()
	{
		//connect to S3 API
		require 'application/start.php';

       //List files from Amazon S3 using getIterator
		$objects=$s3Client->getIterator('ListObjects',[
			'Bucket'=>$config['s3']['bucket']
		]);
		return $objects;

	}
	

	function add_department()
	{
		//$post = $this->input->post();

		$data['name'] = $_POST['department_name'];
		$data['created_by'] = 1;
		$data['created_date'] = '2020-06-11';
		$data['last_modified_by'] = 1;

		$this->write_db->insert('department', $data);

		//$this->load->view('welcome_message');
		redirect(base_url() . 'index.php/welcome/index', 'refresh');
	}

	function delete_department($id)
	{
		$this->write_db->where(array('department_id' => $id));
		$this->write_db->delete('department');

		redirect(base_url() . 'index.php/welcome/index', 'refresh');
	}

	function update_department($id = 0)
	{

		if (isset($_POST['department_name'])) {
			$this->write_db->where(array('department_id' => $id));
			$data['name'] = $_POST['department_name'];
			$this->write_db->update('department', $data);
			redirect(base_url() . 'index.php/welcome/index', 'refresh');
		} else {
			$department = $this->read_db->get_where('department', array('department_id' => $id))->row();
			$data['department_name'] = $department->name;
			$data['department_id'] = $department->department_id;
			$this->load->view('update', $data);
		}
	}

	function download_file($file_name){
		try {
			// Get the object.
			// $result = $s3->getObject([
			// 	'Bucket' => $config['s3']['bucket'],
			// 	'Key'    => $file_name
			// ]);

			$result = $s3->getObject([
				'Bucket'                     => $config['s3']['bucket'],
				'Key'                        => $file_name,
				'ResponseContentType'        => 'text/plain',
				'ResponseContentLanguage'    => 'en-US',
				'ResponseContentDisposition' => 'attachment; filename=testing.txt',
				'ResponseCacheControl'       => 'No-cache',
				'ResponseExpires'            => gmdate(DATE_RFC2822, time() + 3600),
			]);
		
			// Display the object in the browser.
			// header("Content-Type: {$result['ContentType']}");
			// echo $result['Body'];
		} catch (S3Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	function aws_client(){
		$client = new IamClient([
			'profile' => 'default',
			'region' => 'us-west-2',
			'version' => '2010-05-08'
		]);

		$result = [];

		$userName = 'nkarisa';

		try {
			$attachedUserPolicies = $client->getIterator('ListAttachedUserPolicies', ([
				'UserName' => $userName,
			]));
		} catch (AwsException $e) {
			// output error message if fails
			$result = $e->getMessage();
		}

		print_r($result);
	}
}
