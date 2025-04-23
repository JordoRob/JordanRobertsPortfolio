<?php

include_once dirname(__DIR__) . '/src/Employee Page/add-employee.php';
use PHPUnit\Framework\TestCase;

class AddEmployeeTest extends TestCase
{

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
        $this->dummyEmp=null;
    }

    protected function tearDown(): void
    {
        $emp=$this->dummyEmp;
        $stmt = $this->pdo->prepare("DELETE FROM employee WHERE id=?");
        $stmt->execute([$emp]);
        $this->pdo = null;

    }

    public function testAdd_EmployeeFull_NoFile()
    {
        // Dummy data
        $title = 1; // Journeyman
        $name = 'Test Guy';
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant for real".random_int(0,1000);
        $file = null;

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);
        $this->dummyEmp=$code[1];
        //get employee info
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE notes = ?");
        $stmt->execute([$notes]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertTrue($code[0]);
        $this->assertNotNull($employee); 
        $this->assertEquals($name, $employee['name']); 
        $this->assertEquals($phone, $employee['phoneNum']); 
        $this->assertEquals($phoneSec, $employee['phoneNumSecondary']);
        $this->assertEquals($email, $employee['email']); 
        $this->assertEquals($datehired, $employee['hired']); 
        $this->assertEquals($birthday, $employee['birthday']); 
        $this->assertEquals($notes, $employee['notes']);
        $this->assertEquals($title, $employee['role']);


    }

    public function testadd_employee_Invalid_MissingRequired()
    {
        // Wrong data type
        $title = null; // Journeyman
        $name = null;
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $file=null;

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE notes = ?");
        $stmt->execute([$notes]);
        
        $this->assertEquals(0,$stmt->rowCount());
        $this->assertFalse($code[0]); 
        $this->assertEquals("Please fill in name",$code[1]); 
    }
    public function testadd_employee_Invalid_BadPhone()
    {
        // Wrong data type
        $title = 1; // Journeyman
        $name = "Test Guy";
        $phone = "(123) 123-1234 `12`1353254324";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $file=null;

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE notes = ?");
        $stmt->execute([$notes]);
        
        $this->assertEquals(0,$stmt->rowCount());
        $this->assertFalse($code[0]); 
        $this->assertEquals("Invalid phone value",$code[1]); 
    }    
    
    public function testadd_employee_Invalid_LongEmail()
    {
        // Wrong data type
        $title = 1; // Journeyman
        $name = "Test Guy";
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.cajohn@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $file=null;

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE notes = ?");
        $stmt->execute([$notes]);
        
        $this->assertEquals(0,$stmt->rowCount());
        $this->assertFalse($code[0]); 
        $this->assertEquals("Invalid email value",$code[1]); 
    }
    public function testadd_employee_Invalid_LongName()
    {
        // Wrong data type
        $title = 1; // Journeyman
        $name = "Test GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest GuyTest Guy";
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $file=null;

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE notes = ?");
        $stmt->execute([$notes]);
        
        $this->assertEquals(0,$stmt->rowCount());
        $this->assertFalse($code[0]); 
        $this->assertEquals("Name too long",$code[1]); 
    }
    public function testadd_employee_invalid_FileExtension()
    {
        // Wrong data type
        $title = 1; // Journeyman
        $name="Test Guy";
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $filepath=dirname(__DIR__)."/tests/Test Images/not-image.docx";
        $file=array("tmp_name"=>$filepath,"name"=>$filepath,"size"=>filesize($filepath));

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info

        $this->assertEquals("Incorrect file type (docx), PNG, GIF or JPEG Allowed",$code[1]);
        $this->assertFalse($code[0]); 
    }
    public function testadd_employee_invalid_FileSize()
    {
        // Wrong data type
        $title = 1; // Journeyman
        $name="Test Guy";
        $phone = "(123) 123-1234";
        $phoneSec =  "(123) 123-1234";
        $email="john@somewhere.ca";
        $datehired=date("Y-m-d");
        $birthday=date("Y-m-d");
        $notes="Tell him to wear deoderant".random_int(0,1000);
        $filepath=dirname(__DIR__)."/tests/Test Images/big-image.jpg";
        $file=array("tmp_name"=>$filepath,"name"=>$filepath,"size"=>(20 * 1048576)); //I dont want to deal with constantly uploading a massive file to github so this will do probably

        $this->assertNotNull($this->pdo);
        $code=add_employee($name,$phone,$phoneSec,$email,$datehired,$birthday,$notes,$title,$file, $this->pdo);

        //get employee info

        $this->assertEquals("File too large, Max File size: 10Mb",$code[1]);
        $this->assertFalse($code[0]); 
    }
}