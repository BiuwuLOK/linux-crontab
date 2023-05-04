<?php

// Remove when the project online
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("TIMESTAMP", date("d-M-Y,H:i:s"));
// Define a class that represents a file reader
class LogFileReader {
  private $dir; // Declare a property to store the current directory
  private $today;  // Declare a property to store today's date
  private $log_contents;  // Declare a property to store the log contents
	private $timestamp; // Declare a property to store system datetime
	// Declare a constructor that takes the directory as an argument
  public function __construct($dir) {
    // Assign the directory to the property
    $this->dir = $dir;
    // Get today's date and time in Y-m-d H:i:s format
    $this->today = date("Y-m-d");
    // Prepare params, timestamp with initiates.
    $this->timestamp = TIMESTAMP;
    $this->log_contents = " <h2>$this->timestamp daily cron job running.</h2><br>\n";
  }

	// Declare a method that reads and processes the files
  public function readFiles() {
    // List all the .txt files in the directory
    $files = glob($this->dir . "/*.txt");
    // Loop through the files and display their content after today's date and time
    foreach ($files as $file) {
      $this->log_contents .= "<h4>Filename: " . basename($file) . "</h4><br>\n";
      // Read the file content as an array of lines
      $file_content_lines = file($file);
      // Get the last 8 lines of the array
      $last_eight_lines = array_slice($file_content_lines, -8);
      // Filter out the lines that do not contain today's date and time or later
      foreach ($last_eight_lines as $line) {
        // If the line contains today's date and time or later, set the flag to true
        if (strpos($line, $this->today) !== false || strtotime($line) > strtotime($this->today)) {
        	$flag = true;
        } // set flag for lines
        // If the flag is true, print the line
        if ($flag) {    $this->log_contents .= $line;    }
      }
      $this->log_contents .= " \n ---- ---- <hr>\n";
    }
  }

  // Declare a method that returns the log contents
  public function getLogContents() {
    return $this->log_contents;
  }
}

// Create an instance of the FileReader class with the current directory
$log_files = new LogFileReader(getcwd());
// Call the readFiles method to process the files
$log_files->readFiles();
// Get and print the log contents
$log_contents = $log_files->getLogContents();

$errorPattern = '/warning|error|notice/'; // create a pattern like /warning|error|notice/
if (preg_match($errorPattern, $log_contents, $matches)) {
  $todo = "Found {$matches[0]} in.\n";
	echo $todo;
}

if (!empty($log_contents)){ // after log_contents completed

  echo TIMESTAMP . "log_contents completed.<br/>\n"; // confirm log content completed.
	
	$email_content_head = "
	<h1>My Host (Input some IPs or desc) daily crontab re-check</h1>
	<p>--- --- --- ---</p>
	"; // adding content head
	$emailContent = $email_content_head . $log_contents;

echo $emailContent;

	try { // setup phpmailer send daily @recheck_content let Devs knowing crontab status.

    require __DIR__ . "/../phpmailer/PHPMailerAutoload.php";
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'My Host IP';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = false;                               // Enable SMTP authentication
    // $mail->Username = 'user@example.com';                 // SMTP username
    // $mail->Password = 'secret';                           // SMTP password
    // $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 25;                                    // TCP port to connect to

    $mail->setFrom('user@example.com', 'Domain Host Support - Cronjob task');
    $mail->addAddress('user@example.com', 'user');     // Add a recipient
    // $mail->addAddress('maximilian.lok@Domain.mo', 'user');     // Add a recipient
    // $mail->addAddress('ellen@example.com');               // Name is optional
    
    $mail->addReplyTo('user@example.com', 'Domain Host Support - Cronjob task');
    // $mail->addBCC('bcc@example.com');
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->CharSet = "UTF-8";                                // set email encode to UTF-8
    // $mail->Encoding = 'base64';                              // if UTF-8 not working
    $mail->isHTML(true);                                  // Set email format to HTML

    if (!empty($todo)) { // check if any error pop up in log content
    	// raise up flasg to alert
    	$mail->addCustomHeader("X-Message-Flag: Follow up");
    }

    $mail->Subject = 'Domain Host 100.20 daily crontab re-check';
    $mail->Body    = $emailContent;

    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    // $mail->send();
    //return false;

    if ($mail->send()) {
    	echo '<u style="color: red;">Email has been send.</u>' . "\n";
    }
	} catch (phpmailerException $e) {
    echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
    echo $e->getMessage(); //Boring error messages from anything else!
	}
} else {
	echo 'something wrong when reading logs files.';
}

?>