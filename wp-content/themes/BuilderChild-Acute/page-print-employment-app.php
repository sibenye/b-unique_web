<?php
/**
 *	Template for displaying the printable format for the employment application.
 *
 */
  require('fpdf/fpdf.php');
  
  class bubaEmploymentApplicationPDF extends FPDF {
	public $title = "B-Unique Barber & Beauty Academy";
	
	//Page header method

        function Header() {



        $this->SetFont('Times','B',18);

        $w = $this->GetStringWidth($this->title)+2;

        //$this->SetDrawColor(0,0,180);

        //$this->SetFillColor(170,169,148);

        $this->SetTextColor(0,0,255);

        //$this->SetLineWidth(1);

        $this->Cell(0,10,$this->title,1,1,'C');

        $this->Ln(10);
        }
		
		//Page footer method

        function Footer()       {

        //Position at 1.5 cm from bottom

        $this->SetY(-15);

        $this->SetFont('Arial','I',9);

        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

        }


  }
session_start();
$em_app = $_SESSION['em_app'];
unset($_SESSION['em_app']);

$pdf = new bubaEmploymentApplicationPDF('P', 'mm', 'A4');

$pdf->AliasNbPages();

$pdf->AddPage();

$pdf->SetFont('Times','',12);

$pdf->Cell(0,6,'Application ID:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['app_code'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//small line space
$pdf->Cell(0,3,'',0,1);//small line space
$pdf->Cell(0,6,'PERSONAL INFO',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'First Name:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['first_name'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Last Name:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['last_name'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Date of Birth:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['dob'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Gender:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['gender'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Marital Status:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['marital_status'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Email:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['email'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Mobile Phone:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['mobile_phone'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Home Phone:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['home_phone'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Other:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['other_phone'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'SSN:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['ssn'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'DL Number:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['dl_number'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Issue State:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['dl_issue'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Education:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['education'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Address:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['address'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'City:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['city'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'State:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['state'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Zip Code:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['zipcode'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'EMPLOYMENT HISTORY',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Employer:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['employer_name'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Phone:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['employer_phone'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Address:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['employer_address'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'City:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['employer_city'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'State:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['employer_state'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Zip Code:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['employer_zipcode'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'EMERGENCY CONTACT',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Name:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['emergency_name'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Relationship:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['emergency_relationship'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Mobile Phone:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['emergency_mobile_number'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Home Phone:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['emergency_home_number'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Address:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['emergency_address'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'City:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['emergency_city'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'State:',0,0,'L');
$pdf->SetX(40);
$pdf->Cell(45,6,$em_app['emergency_state'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Zip Code:',0,0,'L');
$pdf->SetX(135);
$pdf->Cell(45,6,$em_app['emergency_zipcode'],1,1,'L');

//page 2
$pdf->AddPage();

$pdf->SetFont('Times','',12);

$pdf->Cell(0,6,'MEDICAL INFORMATION',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Physical Disability:',0,0,'L');
$pdf->SetX(55);
$pdf->Cell(45,40,$em_app['physical_disability'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'List of Medication:',0,0,'L');
$pdf->SetX(145);
$pdf->Cell(45,40,$em_app['medication'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'JOB INFO',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Applied For:',0,0,'L');
$pdf->SetX(55);
$pdf->Cell(45,6,$em_app['applying_for'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Employment Type:',0,0,'L');
$pdf->SetX(145);
$pdf->Cell(45,6,$em_app['employment_type'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'During:',0,0,'L');
$pdf->SetX(55);
$pdf->Cell(45,6,$em_app['period'],1,0,'L');
$pdf->SetX(105);
$pdf->Cell(0,6,'Offered Job:',0,0,'L');
$pdf->SetX(145);
$pdf->Cell(45,6,$em_app['accepted'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Why this Career:',0,0,'L');
$pdf->SetX(55);
$pdf->Cell(120,80,$em_app['summary'],1,1,'L');


//page 3
$pdf->AddPage();

$pdf->SetFont('Times','',12);
$pdf->Cell(0,6,'OFFICIAL REMARKS/DECISION',0,1,'C');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Application was submitted on:',0,0,'L');
$pdf->SetX(65);
$pdf->Cell(45,6,$em_app['submitted_on'],1,0,'L');
$pdf->SetX(115);
$pdf->Cell(0,6,'Official Decision:',0,0,'L');
$pdf->SetX(145);
$pdf->Cell(45,6,$em_app['accepted'],1,1,'L');
$pdf->Cell(0,3,'',0,1);//line space
$pdf->Cell(0,6,'Official Remark:',0,0,'L');
$pdf->SetX(50);
$pdf->Cell(120,80,$em_app['remarks'],1,1,'L');

$pdf->Output();
  
?>
