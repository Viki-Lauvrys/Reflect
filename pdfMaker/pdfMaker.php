<?php
require_once('tcpdf.php');

class MyTCPDF extends TCPDF{
    private $currentHeader;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->currentHeader = '';
    }

    /* 
    //header centered
    public function setCustomHeader($header) {
        $this->currentHeader = $header;
    }

    public function Header() {
        $this->Cell(0, 0, $this->currentHeader, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    } 
    */

    //header left and right
    public function setCustomHeader($header, $name, $group, $period) {
        $this->header = $header;
        $this->name = $name;
        $this->group = $group;
        $this->period = $period;
    }

    public function Header() {
        if ($this->header == "yes") {
            $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP +30, PDF_MARGIN_RIGHT);

            $this->SetX(1);
            $this->SetY(5);
            $this->Image('img/hoofding_sui.jpg', PDF_MARGIN_LEFT - 2, 3, 210 - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT + 2, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $this->SetFont('helvetica', '', 12);
            $this->Ln(27);
            $this->Cell(14, 10, 'Naam:', 0, 0, 'L');
            $this->Cell(75, 10, $this->name, 0, 0, 'L');

            $this->Cell(11, 10, 'Klas:', 0, 0, 'L');
            $this->Cell(35, 10, $this->group, 0, 0, 'L');

            $this->Cell(17, 10, 'Periode:', 0, 0, 'L');
            $this->Cell(55, 10, $this->period, 0, 0, 'L');

            $this->SetFont('helvetica', '', 12);
            $this->Write(0, "", '', 0, 'R', true, 0, false, true, 0);
            $this->Line(PDF_MARGIN_LEFT, 40, 210 - PDF_MARGIN_RIGHT, 40); //page width is 210
        } else {
            $this->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
        }
    }

}

// THIS IS FOR ONLINE SERVER
$dbhost = "localhost";
$dbuser = "vikiRemote";
$dbpass = "Viki@aVocado4405";
$dbname = "project_x";
 
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Enable special chars, DON'T YOU DARE DELETE THIS LINE !!!
$conn->set_charset('utf8mb4');

// create a new PDF document
$pdf = new MyTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Viki Lauvrys');
$pdf->SetTitle('Reflectie rapport');
$pdf->SetSubject('PDF reflect');
$pdf->SetKeywords('TCPDF, PDF');

// disable footer
$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP +30, PDF_MARGIN_RIGHT);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 12);

$pdf->setCellPaddings( $left = '1', $top = '2', $right = '1', $bottom = '0');


$userID = urldecode($_GET['id']);
// Order by groups
$query1 = 'SELECT groupName
    FROM smartschool_groups_users
    WHERE userID = "' . $userID . '"
    AND groupName != "2. Leerlingen"';

$result1 = $conn->query($query1);
$pageCounter = 1;
$counter = 0;
$prevUsername = "";
$schoolYear = $_GET['inputYear'];
$nextYear = $schoolYear +1;
if ($result1->num_rows > 0) {
    while($rowGroup = $result1->fetch_assoc()) {
        
            // query the data from the database
            $query = 'SELECT *, 
                sp.id AS spid,
                smartschool_users.userID AS UserID,
                surveys.id AS `sid`,
                survey_period.name AS periodName
                FROM survey_pupil sp
                LEFT JOIN smartschool_users ON sp.pupil = smartschool_users.userID
                LEFT JOIN survey_info_info ON survey_info_info.id = sp.siid
                LEFT JOIN survey_info ON survey_info.id = survey_info_info.info_id
                LEFT JOIN survey_title ON survey_info.title_id = survey_title.id
                LEFT JOIN surveys ON surveys.id = survey_info_info.sid
                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                LEFT JOIN survey_period ON surveys.period_id = survey_period.id
                WHERE sp.status != "0"
                AND sp.pupil IN (SELECT userID FROM smartschool_groups_users WHERE groupName = "' . utf8_encode($rowGroup['groupName']) . '")
                AND (
                        (
                            (MONTH(survey_period.start_date1) >= 9 AND MONTH(survey_period.start_date1) <= 12) 
                            AND YEAR(survey_period.start_date1) = "' . $schoolYear . '"
                        )
                    OR 
                        (
                            (MONTH(survey_period.start_date1) >= 1 AND MONTH(survey_period.start_date1) <= 8) 
                            AND YEAR(survey_period.start_date1) = "' . $nextYear  . '"
                        )
                    )';

            if(isset($_GET['inputName']) && !empty($_GET['inputName'])) {
                $query .= " AND surveys.name LIKE '%" . utf8_encode($_GET['inputName']) . "%'";
            }

            if(isset($_GET['inputTitle']) && !empty($_GET['inputTitle'])) {
                $query .= " AND survey_title.title LIKE '%" . utf8_encode($_GET['inputTitle']) . "%'";
            }

            if(isset($_GET['inputInfo']) && !empty($_GET['inputInfo'])) {
                    $query .= " AND survey_info.info LIKE '%" . utf8_encode($_GET['inputInfo']) . "%'";
            }

            if(isset($_GET['inputGroup']) && !empty($_GET['inputGroup'])) {
                    $query .= " AND smartschool_users.userID IN (SELECT userID FROM smartschool_groups_users WHERE groupName LIKE '%" . utf8_encode($_GET['inputGroup']) . "%')";
                    $query .= " AND surveys.id IN (SELECT `sid` FROM survey_group WHERE group_name LIKE '%" . utf8_encode($_GET['inputGroup']) . "%')";
            }

            if(isset($_GET['inputPupil']) && !empty($_GET['inputPupil'])) {
                    $query .= " AND CONCAT(smartschool_users.first_name, ' ', smartschool_users.last_name) LIKE '%" . utf8_encode($_GET['inputPupil']) . "%'";
            }

            if(isset($_GET['inputPeriod']) && !empty($_GET['inputPeriod'])) {
                $query .= " AND survey_period.name LIKE '%" . utf8_encode($_GET['inputPeriod']) . "%'";
            }

            //$query .= " ORDER BY smartschool_groups_users.groupName";
            $query .= "GROUP BY spid";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    
                    $html = "";
                    
                    if ($prevUsername == "") {
                        $pdf->setCustomHeader("yes", htmlspecialchars(utf8_decode($row['first_name'])) . " " . htmlspecialchars(utf8_decode($row['last_name'])), htmlspecialchars(utf8_decode($rowGroup['groupName'])), htmlspecialchars(utf8_decode($row['periodName'])), true);
                        $pdf->AddPage();
                        $prevUsername = $row['UserID'];
                    }

                    //blank page when new user
                    if ($row['UserID'] != $prevUsername) {
                        $prevUsername = $row['UserID'];

                        // extra blank page when not even page number
                        if ($pageCounter % 2 != 0) {
                            $pdf->setCustomHeader("no", htmlspecialchars(utf8_decode($row['first_name'])) . " " . htmlspecialchars(utf8_decode($row['last_name'])), htmlspecialchars(utf8_decode($rowGroup['groupName'])), htmlspecialchars(utf8_decode($row['periodName'])), true);
                            $pdf->AddPage();
                            $pageCounter++;
                        } 

                        // add page with heading
                        $pdf->setCustomHeader("yes", htmlspecialchars(utf8_decode($row['first_name'])) . " " . htmlspecialchars(utf8_decode($row['last_name'])), htmlspecialchars(utf8_decode($rowGroup['groupName'])), htmlspecialchars(utf8_decode($row['periodName'])), true);
                        $pdf->AddPage();
                        $pageCounter++;

                        //reset everything again
                        $counter = 0;
                        $pdf->setCustomHeader("yes", htmlspecialchars(utf8_decode($row['first_name'])) . " " . htmlspecialchars(utf8_decode($row['last_name'])), htmlspecialchars(utf8_decode($rowGroup['groupName'])), htmlspecialchars(utf8_decode($row['periodName'])), true);
                    }

                    //style, only working in EOF (denk ik toch)
                    $html .= <<<EOF
                    <!-- EXAMPLE OF CSS STYLE -->
                    <style>
                        table { 
                            border-spacing: 0.1px;
                        }
                        th {
                            text-align: center;
                        }
                        th.lln {
                            text-align: left;
                        }
                        img {
                            height: 50px;
                        }
                        td {
                            height: 30px;
                        }
                    </style>

                    EOF;
                    $counter++;

                    $html .= "<b>" . $row['title'] . "</b><br/>" . $row['info'] . "<br/> <br/>";
                    $html .= '<table border="1">';
                        $html .= '<tr>';
                            $html .= '<th width="35%" class="lln"><b> Leerling</b></th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/0.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/1.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/2.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/3.png" width="200%" height="200%" /> </th>';
                        $html .= '</tr>';

                        $html .= '<tr>';
                            $html .= '<td width="35%"> Zelfevaluatie 1</td>';
                            if ($row['q1'] != '0') {
                                $html .= '<td width="' . ($row['q1'] *(25/100)*65) . '%" bgcolor="' . $_GET['color1'] . '"></td>';
                                $html .= '<td width="' . (65 - $row['q1']*(25/100)*65) . '%"></td>';
                            } else {
                                $html .= '<td></td>';
                            }
                        $html .= '</tr>';

                        $html .= '<tr>';
                            $html .= '<td width="35%"> Plan van aanpak?</td>';
                            $html .= '<td width="65%"> ' . htmlspecialchars(utf8_decode($row['q2'])) . '</td>';
                        $html .= '</tr>';

                        $html .= '<tr>';
                            $html .= '<td width="35%"> Wie of wat kan je hierbij helpen?</td>';
                            $html .= '<td width="65%"> ' . htmlspecialchars(utf8_decode($row['q3'])) . '</td>';
                        $html .= '</tr>';

                        $html .= '<tr>';
                            $html .= '<td width="35%"> Zelfevaluatie 2</td>';
                            if ($row['q4'] != '' && $row['q4'] != 0) {
                            $html .= '<td width="' . ($row['q4'] *(25/100)*65) . '%" bgcolor="' . $_GET['color1'] . '"></td>';
                            $html .= '<td width="' . (65- $row['q4'] *(25/100)*65) . '%"></td>';
                            } else {
                                $html .= '<td></td>';
                            }
                        $html .= '</tr>';

                        $html .= '<tr>';
                            $html .= '<td width="35%"><b>Leerkrachten</b></td>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/0.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/1.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/2.png" width="200%" height="200%" /> </th>';
                            $html .= '<th width="' . 65/4 . '%"> <img src="img/batteries/3.png" width="200%" height="200%" /> </th>';
                        $html .= '</tr>';

                        $spid = $row['spid'];
                        $result2 = $conn->query(
                            "SELECT * FROM survey_teacher
                            LEFT JOIN smartschool_users ON survey_teacher.teacher = smartschool_users.userID
                            WHERE survey_teacher.spid = '$spid'
                            AND survey_teacher.q5 != ''");

                        $numberOfTeachers = 0;
                        $totalOfTeacherAnswers = 0;
                        while($row2 = $result2->fetch_assoc()) {
                            $numberOfTeachers++;
                            $totalOfTeacherAnswers += $row2['q5'];
                            $html .= '<tr>';
                                $html .= '<td width="35%">Evaluatie ' . substr($row2['first_name'], 0, 1) . ". " . $row2['last_name'] . '</td>';
                                $html .= '<td width="' . ($row2['q5'] *(25/100)*65) . '%" bgcolor="' . $_GET['color3'] . '"></td>';
                                $html .= '<td width="' . (65- $row2['q5'] *(25/100)*65) . '%"></td>';
                            $html .= '</tr>';
                        }
                        if ($numberOfTeachers > 0) {
                            $gemiddelde = $totalOfTeacherAnswers / $numberOfTeachers;
                            $html .= '<tr>';
                                $html .= '<td width="35%"> Gemiddelde </td>';
                                $html .= '<td width="' . ($gemiddelde *(25/100)*65) . '%" bgcolor="' . $_GET['color4'] . '"></td>';
                                $html .= '<td width="' . (65- $gemiddelde *(25/100)*65) . '%"></td>';
                            $html .= '</tr>';
                        }

                        $html .= '<tr>';
                            $html .= '<td width="35%"> Feedback</td>';
                            $html .= '<td width="65%"> '.htmlspecialchars($row['feedback']).'</td>';
                        $html .= '</tr>';

                    $html .= '</table> <br/> <br/> ';
                    $pdf->writeHTML($html, true, 0, true, 0);
                    
                    //if($counter == 1) {
                        $pdf->setCustomHeader("no", htmlspecialchars(utf8_decode($row['first_name'])) . " " . htmlspecialchars(utf8_decode($row['last_name'])), htmlspecialchars(utf8_decode($rowGroup['groupName'])), htmlspecialchars(utf8_decode($row['periodName'])), true);
                        $pdf->AddPage();
                        $pageCounter++;
                    //}
                    
                }
            }
    }
    // reset pointer to the last page
    $pdf->lastPage();

    // close and output PDF document
    $pdf->Output('REFLECT.pdf', 'I');
} else {
    echo 'Geen gegevens gevonden om te printen. Wacht op resultaten van leerlingen.';
}

$conn->close();

?>