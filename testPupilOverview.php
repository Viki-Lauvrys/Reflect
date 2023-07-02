<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
    $userID = $_GET["id"];


$query = "SELECT first_name, last_name FROM smartschool_users WHERE userID= ? ";

$stmt = $conn->prepare($query);
$stmt -> bind_param('s', $userID);
$stmt -> execute();
$userResult = $stmt -> get_result();

if ($userResult && mysqli_num_rows($userResult) > 0) {
    while ($row = $userResult->fetch_assoc()) {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
        }
}

$result = $conn->query(
    "SELECT DISTINCT schoolYear AS year0,
    schoolYear +1 AS year1
    FROM smartschool_groups_users");

$yearArray = array();
$arrayQ1 = [];
$arrayQ4 = [];
$arrayQ5 = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        // CHART ALLES
        $result1 = $conn->query(
            "SELECT q1, q4, q5
            FROM survey_pupil sp
            LEFT JOIN survey_teacher ON survey_teacher.spid = sp.id
            LEFT JOIN survey_info_info ON survey_info_info.id = sp.siid
            LEFT JOIN surveys ON surveys.id = survey_info_info.sid
            LEFT JOIN survey_period ON surveys.period_id = survey_period.id
            WHERE sp.pupil = '" . $userID . "'
            AND sp.status != 0
            AND survey_period.schoolYear = " . $row['year0']  );
        
        $count = 0;
        $totalQ1 = 0;
        $totalQ4 = 0;
        $totalQ5 = 0;
        if ($result1->num_rows > 0) {
            while($row1 = $result1->fetch_assoc()) {
                $totalQ1 += $row1['q1'];
                $totalQ4 += $row1['q4'];
                $totalQ5 += $row1['q5'];
                $count++;
            }

            $averageQ1 = $totalQ1 / $count;
            $averageQ4 = $totalQ4 / $count;
            $averageQ5 = $totalQ5 / $count;
            array_push($arrayQ1, number_format($averageQ1, 2));
            array_push($arrayQ4, number_format($averageQ4, 2));
            array_push($arrayQ5, number_format($averageQ5, 2));
        }


    }

    $q1StringAlles = "['" . implode("', '", $arrayQ1) . "']";
    $q4StringAlles = "['" . implode("', '", $arrayQ4) . "']";
    $q5StringAlles = "['" . implode("', '", $arrayQ5) . "']";
}


// CHART YEAR
$uniqueTitles = array();
$surveyData = array();

$query = "SELECT survey_title.title AS title, 
    q1, q4, q5
    FROM survey_pupil sp
    LEFT JOIN survey_teacher st ON st.spid = sp.id
    LEFT JOIN survey_info_info sii ON sii.id = sp.siid 
    LEFT JOIN survey_info si ON si.id = sii.info_id
    LEFT JOIN survey_title ON survey_title.id = si.title_id
    WHERE sp.pupil = ?
    AND sp.status != ?
    ORDER BY title ASC";

$status = 0;
$stmt = $conn->prepare($query);
$stmt -> bind_param('ss', $userID, $status);
$stmt -> execute();
$surveyResult = $stmt -> get_result();

if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
    while ($row = $surveyResult->fetch_assoc()) {
        $title = $row['title'];
        $q1 = $row['q1'];
        $q4 = $row['q4'];
        $q5 = $row['q5'];
        $surveyData[$title][] = array('q1' => $q1, 'q4' => $q4, 'q5' => $q5);
    }
}

$averages = array();

foreach ($surveyData as $title => $data) {
    $totalQ1 = 0;
    $totalQ4 = 0;
    $totalQ5 = 0;
    $count = 0;
    foreach ($data as $row) {
        $totalQ1 += $row['q1'];
        $totalQ4 += $row['q4'];
        $totalQ5 += $row['q5'];
        $count++;
    }
    $averageQ1 = $totalQ1 / $count;
    $averageQ4 = $totalQ4 / $count;
    $averageQ5 = $totalQ5 / $count;
    $averages[$title] = array(
        'q1' => $averageQ1,
        'q4' => $averageQ4,
        'q5' => $averageQ5
    );
}


// Get all titles
$getTitles = array_map(function($key) {
    return "'{$key}'";
}, array_keys($averages));

$titles = '[' . implode(', ', $getTitles) . ']';


// Get all q1 in right format
$q1Array = array();
foreach ($averages as $array) {
    $q1 = $array['q1'];
    $q1 = number_format($q1, 2);
    $q1Array[] = $q1;
}
$q1String = "['" . implode("', '", $q1Array) . "']";

// Get all q4 in right format
$q4Array = array();
foreach ($averages as $array) {
    $q4 = $array['q4'];
    $q4 = number_format($q4, 2);
    $q4Array[] = $q4;
}
$q4String = "['" . implode("', '", $q4Array) . "']";

// Get all q5 in right format
$q5Array = array();
foreach ($averages as $array) {
    $q5 = $array['q5'];
    $q5 = number_format($q5, 2);
    $q5Array[] = $q5;
}
$q5String = "['" . implode("', '", $q5Array) . "']";


$first_name = 'Viki';
$last_name = "Lauvrys";

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Overzicht <?php echo $first_name . " " . $last_name;?> </title>

    <link href="pupilOverview.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-2.1.3.js"></script>

    <style>
        h2 {
            text-align: center;
        }
        #chart {
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        /*.apexcharts-menu-icon {
            display: none;
        }*/

        .exportCSV {
            display: none;
        }
      
    </style>

    <h2>Overzicht <?= $first_name . " " . $last_name; ?></h2>

    <script>
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"><\/script>'
        )
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill@1.2.20171210/classList.min.js"><\/script>'
        )
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/findindex_polyfill_mdn"><\/script>'
        )
    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
      
  </head>

  <body>
  <select id="select" onchange='change(document.getElementById("select").value)'>
        <option value="#AD29D5">Kleur</option>
        <option value="#AD29D5">Paars</option>
        <option value="#FF1694">Roos</option>
        <option value="#4169E1">Blauw</option>
        <option value="#50C878">Groen</option>
        <option value="#FFC857">Geel</option>
        <option value="#FF7F50">Oranje</option>
        <option value="#D52941">Rood</option>
    </select>
    <div id="yearContainer">
        <select id="thisIsTheChosenYear" name="inputPeriod">
            <option value='alles' >Alles</option>
                <?php 
                    //Get all schoolYears
                    $result = $conn->query(
                        "SELECT DISTINCT schoolYear AS year0,
                        schoolYear +1 AS year1
                        FROM smartschool_groups_users");

                    $yearArray = array();
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        
                        $yearData = $row['year0'] . "-" . $row['year1'];
                        array_push($yearArray, $yearData);
                        echo "<option value='" . $row['year0'] . "' >" . $yearData . "</option>";
                      }
                    }
                    $yearString = "['" . implode("', '", $yearArray) . "']";
                ?>
                
            <option value='2023' >2021-2022</option>
            <option value='2023' >2020-2021</option>
        </select>
    </div>
     <div id="chart"></div>

    <script>

    function changeChart(year) {
        function adjust(color, amount) {
            return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
        }
        colorrr = localStorage.getItem('colour');

        const cols = [];
        cols[0]= adjust(colorrr, 40);
        cols[1]= adjust(colorrr, -30);
        cols[2]= adjust(colorrr, -90);


        var numOfBars = <?php echo count(array_keys($averages)) * 3; ?>; // Number of titles * 3
        var x = 50 * numOfBars; //tss 25 en 50 is goeie

        <?php
        $q1StringAlles = "['1', '3', '2']";
        $q4StringAlles = "['3', '4', '4']";
        $q5StringAlles = "['2', '4', '3']";
        $yearString = "['2020-2021', '2021 - 2022', '2022 - 2023']";

        ?>

        if (year === 'alles') {
            var options = {
                colors: cols,
                series: [{
                    name: "Zelfevaluatie 1",
                    data: <?php echo $q1StringAlles; ?>
                }, {
                    name: "Zelfevaluatie 2",
                    data: <?php echo $q4StringAlles; ?>
                }, {
                    name: "Gemiddelde leerkrachten",
                    data: <?php echo $q5StringAlles; ?>
                }],
                chart: {
                    toolbar: {
                        show: true,
                        offsetX: 0,
                        offsetY: 0,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true | '<img src="/static/icons/reset.png" width="20">',
                            customIcons: []
                        },
                        export: {
                            svg: {
                                filename: '<?php echo $first_name . " " . $last_name; ?>',
                            },
                            png: {
                                filename: '<?php echo $first_name . " " . $last_name; ?>',
                            }
                        },
                        autoSelected: 'zoom' 
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [5, 7, 5],
                    curve: 'straight',
                    dashArray: [0, 8, 5]
                },
                title: {
                    text: '',
                    align: 'left'
                },
                legend: {
                    tooltipHoverFormatter: function(val, opts) {
                        return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
                    }
                },
                markers: {
                    size: 0,
                    hover: {
                        sizeOffset: 6
                    }
                },
                xaxis: {
                    categories: <?php echo $yearString; ?>,
                },

                yaxis: {
                    min: 0,
                    max: 4,
                    tickAmount: 4
                },
                
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            $('#chart').empty();
            chart.render();
        
        } else {
            var options = {
                colors: cols,
                series: [{
                    name: "Zelfevaluatie 1",
                    data: [2, 1, 2]
                }, {
                    name: "Zelfevaluatie 2",
                    data: [3, 2, 2]
                }, {
                    name: "Gemiddelde leerkrachten",
                    data: [4, 2, 3]
                }],

                chart: {
                    type: 'bar',
                    height: x,
                    toolbar: {
                        show: true,
                        offsetX: 0,
                        offsetY: 0,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true | '<img src="/static/icons/reset.png" width="20">',
                            customIcons: []
                        },
                        export: {
                            svg: {
                                filename: '<?php echo $first_name . " " . $last_name; ?>',
                            },
                            png: {
                                filename: '<?php echo $first_name . " " . $last_name; ?>',
                            }
                        },
                        autoSelected: 'zoom' 
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: {
                            position: 'top',
                        },
                        barHeight: '85%',
                    },
                    responsive: [{
                        breakpoint: undefined,
                        options: {},
                    }]
                },
                //width between bars
                stroke: {
                    show: true,
                    width: 0,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: <?php echo $titles; ?>,
                    min: 0,
                    max: 4,
                    tickAmount: 4
                },
                
                grid: {
                    show: true,
                    strokeDashArray: 0,
                    position: 'back',
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },   
                    yaxis: {
                        lines: {
                            show: false,
                            color: '#ffffff'
                        }
                    },
                }
            }

                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    $('#chart').empty();
                    chart.render();
                
        }

    };

    $currentYear = 'alles';
    $( document ).ready(function() {
        localStorage.setItem('colour', '#FF1694');
        changeChart("alles");
    });

    $('#thisIsTheChosenYear').on('change', function() {
        changeChart($(this).val());
        $currentYear = $(this).val();
    });


    //change colors
    function change(x) {
        localStorage.setItem('colour', x);
        changeChart($currentYear);
    }

</script>
    
  </body>
</html>