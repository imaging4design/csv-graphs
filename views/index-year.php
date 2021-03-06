<?php include('../inc/header.php'); ?>


    <?php

        $all_csv_files = glob("../csv-2021-year/*.csv"); // Gets all CSV files in a directory

        $files = array();

        // Loop through all CSV files and assign them to the array $files[]
        foreach($all_csv_files as $single_csv_file){
            $files[] = $single_csv_file;
        }

        for($x=0; $x<count($files); $x++) {
            $the_year = file($files[$x]);
            //echo $files[$x].'<br>';

            array_shift($the_year); // remove first item of array
            array_pop($the_year); // remove last item of array

            // This is out main data array to be fed through the chartJS
            $graphData[$x] = array();


            for($i=0; $i<count($the_year); $i++) {

                $graphMonth[$i] = array();
                $graphAmount[$i] = array();

                $data[$i] = str_getcsv($the_year[$i]);

                $year[$i] = array();
                $amount[$i] = array();

                $data[$i][3] = preg_replace('~[$,]~', '', $data[$i][3]); // Sales Amount
                $data[$i][7] = preg_replace('~[$,]~', '', $data[$i][7]); // Shipping Amount

                $graphMonth[$i] = substr($data[$i][0], -4); // get year portion only
                $graphAmount[$i] = (float) $data[$i][3]; // cast to integer
                $graphShipping[$i] = (float) $data[$i][7]; // cast to integer

                // Subtract Shipping Amount from Sales Account
                $graphAmount[$i] = $graphAmount[$i] - $graphShipping[$i];

            }

            array_push($graphData[$x], $graphAmount,  $graphMonth);

        }


        // print "<pre>";
        // print_r($graphData);
        // print "</pre>";

        //echo json_encode($graphData);

    ?>

<div class="graph-wrapper">

    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h1>ShoofDirect Sales <small>(By Year / Minus Shipping Costs)</small></h1>
            </div>
            <div class="col-lg-2 col-md-3">

                <a href="index.php"class="btn btn-default">CHANGE VIEW</a>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div id="graph-container">
                    <canvas id="chart"></canvas>
                </div>

            </div>
        </div>
    </div>

</div>

    <script>
        $(document).ready(function(){

            let screenSize; // Show larger (taller) graph on mobile screens
            if (window.matchMedia('screen and (max-width: 768px)').matches) {
                screenSize = 1;
                pointRadiusSize = 3;
            } else {
                screenSize = 2;
                pointRadiusSize = 5;
            }


            // turn below into a json array for 'datasets' to be fed into the graph ...
            //let stats;
            const stats = <?php echo json_encode($graphData); ?>;

            console.log(stats);


            // Random HEX color generator function
            function getRandomColor() {
                let letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }




            loop = [];
            randomColor = null;


            for(i=0; i< stats.length; i++) {
                randomColor = getRandomColor();
                console.log(randomColor);
                switch (i) {
                    case 0:
                    randomColor = 'rgba(225,225,225,1)';
                    break;

                    case 1:
                    randomColor = 'rgba(150,150,150,1)';
                    break;

                    case 2:
                    randomColor = 'rgba(0,82,147,1)';
                    break;
                }

                loop[i] = {
                    label: stats[i][1][1],
                    fill: 'start',
                    color: getRandomColor(),
                    data: stats[i][0],
                    backgroundColor: randomColor,
                    borderColor: randomColor,
                    borderWidth: 1,
                    pointBackgroundColor: randomColor,
                    pointRadius: pointRadiusSize,
                    //hidden: true,
                };
            }


            

            
            const ctx = document.getElementById('chart').getContext('2d');
            // Chart.defaults.global.defaultFontColor = "#fff";

            Chart.defaults.font.family = "Roboto Condensed";
            Chart.defaults.font.color = "green";

            const chartStats = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: loop
                },
                options: {

                    // responsive: true,
                    // maintainAspectRatio: true,
                    
                    plugins: {  // 'legend' now within object 'plugins {}'
                        legend: {
                            labels: {
                                color: "#333333",  // not 'fontColor:' anymore
                                font: {
                                    size: 18 // 'size' now within object 'font {}'
                                }
                            }
                        }
                    },

                    aspectRatio: screenSize,

                    animation: {
                        duration: 1000
                    },

                    layout: {
                        padding: {
                            left: 0,
                            right: 0,
                            top: 10,
                            bottom: 20
                        }
                    },

                    title: {
                        position: 'top',
                        display: true,
                        fontFamily: "Roboto Condensed",
                        fontSize: 20,
                        fontColor: 'green',
                        fontStyle: 'bold',
                        text: 'Range (Years)'
                    },

                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'MONTH',
                                color: '#333333',
                            },
                            ticks: {
                                color: '#333333',
                            },
                            grid: {
                                color: '#EEEEEE',
                            },
                        },
                        y: {
                            grid: {
                                color: '#EEEEEE',
                            },
                            ticks: {
                                color: '#333333',
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return '$' + value;
                                }
                            }
                        }
                    },

                }

            });

        });

        </script>




        <script>

        // Select / deselect all checkboxes (select years for graph)
        function checkAll() {

            let checkboxes = document.getElementsByTagName('input');
            let val = null;

            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                if (val === null) val = checkboxes[i].checked;
                    checkboxes[i].checked = val;
                }
            }
        }

        </script>



<?php include('../inc/footer.php'); ?>