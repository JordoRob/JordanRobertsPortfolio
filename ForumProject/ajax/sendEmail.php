<?php
// PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception;
$logged= array('access'=>false);
include '../../database_mason.php';
	$user=$_POST['user'];
	$email=$_POST['email'];
	$query1 = "SELECT password  FROM users WHERE username=? AND email=?";
    $query2 = "UPDATE users SET password=? WHERE username=? AND email=?";
    $sql1 = $connection->prepare($query1);
    $sql1->bind_param("ss",$user,$email);
                        $sql1->execute();
                        $result1 = $sql1->get_result();
                        if(mysqli_num_rows($result1)>0){
                            //SEND EMAIL HERE
                            
                            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                            $passCreate = array(); //remember to declare $pass as an array
                            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                            for ($i = 0; $i < 8; $i++) {
                                $n = rand(0, $alphaLength);
                                $passCreate[] = $alphabet[$n];
                            }
                            $passCreate = implode($passCreate); //turn the array into a string
                            $passHash = md5($passCreate);
                            $sql2 = $connection->prepare($query2);
                            $sql2->bind_param("sss",$passHash,$user,$email);
                            $sql2->execute();
                            $subject="Mason Tavern Password Recovery";
                            $body="Hey! We received a request to recover your password, if this was not you, please ignore this message \n\n Your temporary password is: " . $passCreate .". Please sign in and update your password via the account page.";
                            
                                // Base files 
                                require '../PHPMailer/src/Exception.php';
                                require '../PHPMailer/src/PHPMailer.php';
                                require '../PHPMailer/src/SMTP.php';
                                // create object of PHPMailer class with boolean parameter which sets/unsets exception.
                                $mail = new PHPMailer(true);                              
                                try {
                                    $mail->isSMTP(); // using SMTP protocol                                     
                                    $mail->Host = 'smtp.gmail.com'; // SMTP host as gmail 
                                    $mail->SMTPAuth = true;  // enable smtp authentication                             
                                    $mail->Username = 'masonstavern@gmail.com';  // sender gmail host              
                                    $mail->Password = 'rcwwigoeiypvbeds'; // sender gmail host password                          
                                    $mail->SMTPSecure = 'tls';  // for encrypted connection                           
                                    $mail->Port = 587;   // port for SMTP     

                                    $mail->setFrom('masonstavern@gmail.com', "Mason"); // sender's email and name
                                    $mail->addAddress($email, "Receiver");  // receiver's email and name

                                    $mail->Subject = $subject;
                                    $mail->Body    = $body;
                                    $mail-> IsHTML(true);
                                    
                                    $mail->SMTPOptions = array(
                                        'ssl' => array(
                                            'verify_peer' => false,
                                            'verify_peer_name' => false,
                                            'allow_self_signed' => true
                                        )
                                    );

                                    $mail->send();
                                    $logged['access']=true; 
                                } catch (Exception $e) { // handle error.
                                    echo ($e);
                                }

                            }else{


                            }
                        
echo json_encode($logged);
mysqli_close($connection);
?>