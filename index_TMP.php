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
    </head>
    <body>

    <?php 
        //Get all .csv files in the csv-files directory
        $files = glob("./csv-files/*.csv");

        echo '<select onchange="val()" name="month" id="month">';
            echo '<option value="">SELECT MONTH</option>';
            foreach($files as $file):
                echo '<option value="'.str_replace("./csv-files/","",$file).'">'.str_replace("./csv-files/","",$file).'</option>';
            endforeach;
        echo '</select>';

    ?>



        <canvas id="chart" width="800" height="400"></canvas>
        
        <script>

            function val() {
                CSVmonth = document.getElementById("month").value;
                
            }

            chartIt();
            
            async function chartIt() {
                
                const data = await getData();

                const ctx = document.getElementById('chart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.xValues,
                        datasets: [{
                            label: 'ShoofDirect Sales',
                            data: data.yValues,
                            backgroundColor: ['rgba(255, 99, 132, 0.2)'],
                            borderColor: ['rgba(255, 99, 132, 1)'],
                            fill: true,
                            borderWidth: 0,
                            radius: 4,
                            hoverRadius: 8,
                            borderJoinStyle: 'round',
                            //tension: .4
                        }]
                    },
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

                });

                
            
            }




            
            // Call function
            // getData();

            async function getData(){

                const xValues = [];
                const yValues = [];

                const response = await fetch('./csv-files/sales-aug.csv');       
                //const response = await fetch('./csv-files/' + CSVmonth);                
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

