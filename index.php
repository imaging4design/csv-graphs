<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Shoof Graphs</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="">
        <script src="parser.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
        <script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
    </head>
    <body>


    <?php 
        //Get all .csv files in the csv-files directory
        $files = glob("./csv-files/*.csv");

        echo '<select onchange="chartIt()" name="month" id="month">';
            echo '<option value="">SELECT MONTH</option>';
            foreach($files as $file):
                echo '<option value="'.str_replace("./csv-files/","",$file).'">'.str_replace("./csv-files/","",$file).'</option>';
            endforeach;
        echo '</select>';

    ?>


        <div id="graph-container">
        <canvas id="chart" width="800" height="400"></canvas>
        </div>
        
        <script>


            // chartIt()

            async function chartIt() {
                
                const CSVdata = await getData();
                
                const data = {
                    labels: CSVdata.xValues,
                    datasets: [{
                        label: 'ShoofDirect Sales',
                        data: CSVdata.yValues,
                        backgroundColor: ['rgba(0, 55, 132, 0.2)'],
                        borderColor: ['rgba(0, 55, 132, 1)'],
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
                        scales: {
                            y: {
                                ticks: {
                                    // Include a dollar sign in the ticks
                                    callback: function(value, index, values) {
                                        return '$' + value;
                                    }
                                }
                            }
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
                //render();

                function render() {
                    chart = new Chart(
                        document.getElementById('chart').getContext('2d'),
                        config
                    );
                }

                //render();

                
                
            
            } // ENDS chartIT

        
            

            async function getData(){

                const CSVmonth = document.getElementById("month").value;

                const xValues = [];
                const yValues = [];
                

                //const response = await fetch('./csv-files/sales-aug.csv');       
                const response = await fetch('./csv-files/' + CSVmonth);                
                const rawData = await response.text();

                const data = CSVToArray(rawData).splice(1); // Parse CSV with parser.js and remove first header row
                data.splice(-2) // remove totals & empty row at end of file

                data.forEach(item => {
                    const year = Date.parse(item[0])/1000;
                    xValues.push(moment.unix(year).format("ddd/DD"));
                    const order = item[3].replace('$', '');
                    yValues.push(parseFloat(order.replace(/,/g,'')));
                    console.log('Date: ' + year, 'No. Orders: ' + order);
                });

                return {xValues, yValues};
                
            }

        </script>
    </body>
</html>

