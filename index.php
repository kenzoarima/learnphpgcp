<?php
/**
 * Files will be stored at e.g. https://storage.googleapis.com/<appspot site url>/testthis.txt
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

use Google\Cloud\Storage\StorageClient;

$app = array();
$app['bucket_name'] = "try-phpappeng.appspot.com";
$app['mysql_user'] = $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = "testdb";
$app['project_id'] = getenv('GCLOUD_PROJECT');



/**
 * Upload a file.
 *
 * @param string $bucketName the name of your Google Cloud bucket.
 * @param string $objectName the name of the object.
 * @param string $source the path to the file to upload.
 *
 * @return Psr\Http\Message\StreamInterface
 */
function upload_object($bucketName, $objectName, $source)
{
    $storage = new StorageClient();
    $file = fopen($source, 'r');
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->upload($file, [
        'name' => $objectName
    ]);
    printf('Uploaded %s to gs://%s/%s' . PHP_EOL, basename($source), $bucketName, $objectName);
}


if ($_FILES) {
	if ($_FILES["uploaded_files"]["error"] > 0) {
		echo "Error: " . $_FILES["uploaded_files"]["error"] . "<br />";
	} elseif ($_FILES["uploaded_files"]["type"] !== "text/plain") {
		echo "File must be a .txt";
	} else {
		$file_handle = fopen($_FILES['uploaded_files']['tmp_name'], 'r');

		upload_object($app['bucket_name'],
						$_FILES['uploaded_files']['name'],
						$_FILES['uploaded_files']['tmp_name']
					);

		var_dump($_FILES);
		echo "\n\n";
		var_dump($file_handle);
	}
}


$servername = null;
$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 
	$dbport, "/cloudsql/try-phpappeng:asia-southeast1:testsql");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "\nConnected successfully\n";


echo "\ntesting gcloud php\n";

?>
<form action="index.php" enctype="multipart/form-data" method="post">
    Files to upload: <br>
   <input type="file" name="uploaded_files" size="40">
   <input type="submit" value="Send">
</form>



















