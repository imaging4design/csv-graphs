<?php include('../inc/header.php'); ?>

<div class="graph-wrapper">

    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h1>ShoofDirect Sales <small>(By Month / Minus Shipping Costs)</small></h1>
            </div>

            <div class="col-lg-2 col-md-3">
                <a href="index.php" class="btn btn-default">CHANGE VIEW</a>
            </div>

            <div class="col-lg-2 col-md-3">

                <?php
                //Get all .csv files in the csv-2021-month directory
                $files = glob("../csv-2021-month/*.csv");

                echo '<select onchange="chartIt()" name="month" id="month" class="form-control">';
                echo '<option value="">SELECT MONTH</option>';
                foreach ($files as $file) :
                    $raw_label = str_replace(['../csv-2021-month/', '-', '.csv'], '', $file);
                    $pretty_label = strtoupper(preg_replace('/[0-9]/', '', $raw_label));
                    echo '<option value="' . str_replace("../csv-2021-month/", "", $file) . '">' . $pretty_label . '</option>';
                endforeach;
                echo '</select>';

                ?>

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
        // chartIt()

        async function chartIt() {

            const CSVdata = await getData();

            const data = {
                labels: CSVdata.xValues,
                datasets: [{
                    label: 'ShoofDirect Sales' + CSVdata.monthLabel,
                    data: CSVdata.yValues,
                    backgroundColor: ['rgba(0, 82, 147, 1)'],
                    borderColor: ['rgba(0, 82, 147, 1)'],
                    fill: true,
                    borderWidth: 0,
                    radius: 4,
                    hoverRadius: 8,
                    borderJoinStyle: 'round',
                    tension: .4
                }]
            };

            const config = {
                type: 'bar',
                data,
                options: {
                    plugins: {  // 'legend' now within object 'plugins {}'
                        legend: {
                            labels: {
                                color: "#333333",  // not 'fontColor:' anymore
                                // fontSize: 18  // not 'fontSize:' anymore
                                font: {
                                    size: 18 // 'size' now within object 'font {}'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: "#333333",
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return '$' + value;
                                }
                            },
                            grid: {
                                color: '#EEEEEE'
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'DAY',
                                color: "#333333",
                            },
                            ticks: {
                                color: "#333333",
                            },
                            grid: {
                                color: '#EEEEEE'
                            },
                        }
                    },
                    layout: {
                        padding: 20
                    }
                }
            };


            // This is a hack to rerender the chart (throws error in console)
            $('#chart').remove();
            $('#graph-container').append('<canvas id="chart"><canvas>');



            let chart = new Chart(
                document.getElementById('chart').getContext('2d'),
                config
            );

            function destroy() {
                chart.destroy();
            }

            //destroy();

            function render() {
                chart = new Chart(
                    document.getElementById('chart').getContext('2d'),
                    config
                );
            }

            //render();


        } // ENDS chartIT




        async function getData() {

            // ORIGINAL CODE ...
            //const CSVmonth = document.getElementById("month").value;
            
            // Set a default month for the initial page load (i.e., January)
            let CSVmonth = null;

            if(document.getElementById("month").value) {
                CSVmonth = document.getElementById("month").value;
            } else {
                CSVmonth = '01-jan.csv';
            }

            // Store values ready to pass to chartIt() function
            const xValues = [];
            const yValues = [];
            const monthLabel = ' - (' + CSVmonth.substring(3, CSVmonth.length-4).toLocaleUpperCase() + ')';

            const response = await fetch('./csv-2021-month/' + CSVmonth);
            const rawData = await response.text();

            const data = CSVToArray(rawData).splice(1); // Parse CSV with parser.js and remove first header row
            data.splice(-2) // remove totals & empty row at end of file

            data.forEach(item => {
                const year = Date.parse(item[0]) / 1000;
                xValues.push(moment.unix(year).format("ddd/DD"));
                const order = item[3].replace('$', '');
                const shipping = item[7].replace('$', '');

                yValues.push(parseFloat(order.replace(/,/g, '')) - parseFloat(shipping.replace(/,/g, ''))); // Sales Amount - Shipping Amount
                //console.log('Date: ' + year, 'No. Orders: ' + order);
                console.log(shipping);
            });

            // Now pass values through to chartIt() function
            return {
                xValues,
                yValues,
                monthLabel
            };

        }

        
        // (async() => {
        // console.log('1')
        // await chartIt();
        // console.log('2')
        // })()

        chartIt();

        
    </script>

<?php include('../inc/footer.php'); ?>