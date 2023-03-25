<!DOCTYPE html>
<html lang="en">

<?php
//This starts a session and checks if the user is logged in, if the user is not they will be redirected to login
session_start();
if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true) {
    header("location: log-in.php");
}
?>

<head>
    <link rel="stylesheet" href="assets/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Prices</title>
</head>

<body>
    <div class="container my-5">
        <?php
        function sortTable($data, $sort_column, $sort_order)
        {
            // This is a function to compare rows based on the sort column
            $cmp = function ($a, $b) use ($sort_column, $sort_order) {
                if ($a[$sort_column] == $b[$sort_column]) {
                    return 0;
                } else if ($sort_order == "asc") {
                    return ($a[$sort_column] < $b[$sort_column]) ? -1 : 1;
                } else {
                    return ($a[$sort_column] > $b[$sort_column]) ? -1 : 1;
                }
            };

            // Sort the data array using the comparison function
            usort($data, $cmp);

            return $data;
        }

        // Set the API endpoint URL
        $url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=10&page=1&sparkline=false&price_change_percentage=24h%2C7d%2C30d%2C1y";
        if (isset($_GET["sort"])) {
            $sort = $_GET["sort"];
            $url .= "&order=" . urlencode($sort);
        }

        // Make the API request
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        // Check if the API request failed, if failed an error message shows
        if ($response === false) {
            echo '<div class="alert alert-danger">Error: Failed to fetch data from API.</div>';
            exit();
        }

        // Decode the API response
        $data = json_decode($response, true);

        // Check if the API response was invalid
        if (!is_array($data)) {
            echo '<div class="alert alert-danger">Error: API response was invalid.</div>';
            exit();
        }

        // Define the table header
        $table_header = array(
            "Coin",
            '<a href="?sort=price_asc">Price (Today)</a>',
            '<a href="?sort=price_yesterday_asc">Price (Yesterday)</a>',
            '<a href="?sort=price_week_asc">Price (1 Week Ago)</a>',
            '<a href="?sort=price_month_asc">Price (1 Month Ago)</a>',
            '<a href="?sort=price_year_asc">Price (1 Year Ago)</a>'
        );

        // Define an empty array for the table rows
        $table_rows = array();

        // Loop through each coin in the data
        foreach ($data as $coin_data) {
            // Get the coin name and symbol
            $coin_name = $coin_data["name"];
            $coin_symbol = $coin_data["symbol"];

            // Get the price data for the coin
            $price_today = $coin_data["current_price"];
            $price_yesterday = $coin_data["current_price"] - $coin_data["price_change_percentage_24h_in_currency"] / 100 * $coin_data["current_price"];
            $price_week_ago = $coin_data["current_price"] - $coin_data["price_change_percentage_7d_in_currency"] / 100 * $coin_data["current_price"];
            $price_month_ago = $coin_data["current_price"] - $coin_data["price_change_percentage_30d_in_currency"] / 100 * $coin_data["current_price"];
            $price_year_ago = $coin_data["current_price"] - $coin_data["price_change_percentage_1y_in_currency"] / 100 * $coin_data["current_price"];

            // Add a row to the table for the coin
            $table_rows[] = array(
                $coin_name . " (" . $coin_symbol . ")",
                number_format($price_today, 2),
                number_format($price_yesterday, 2),
                number_format($price_week_ago, 2),
                number_format($price_month_ago, 2),
                number_format($price_year_ago, 2)
            );
        }

        // Define the table HTML with Bootstrap styling
        $table_html = '<table class="table table-striped">
    <thead>
        <tr>';
        foreach ($table_header as $header_item) {
            $table_html .= '<th class="text-center" scope="col">' . $header_item . '</th>';
        }
        $table_html .= '</tr>
    </thead>
    <tbody>';
        foreach ($table_rows as $row_items) {
            $table_html .= '<tr>';
            foreach ($row_items as $row_item) {
                $table_html .= '<td class="text-center">' . $row_item . '</td>';
            }
            $table_html .= '</tr>';
        }
        $table_html .= '</tbody>
</table>';

        // Output the table HTML
        echo '<div class="container my-5">' . $table_html . '</div>';

        //This is the funtion that gets the average price of each coin every four hours
        function saveCoinAverages($coinPrices)
        {
            // Connect to the database
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = 'asett';
            $conn = new mysqli($host, $username, $password, $dbname);

            // Check for connection errors
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Calculate the average price for each coin every four hours
            $coinAverages = array();
            foreach ($coinPrices as $coin => $prices) {
                $n = count($prices);
                for ($i = 0; $i < $n; $i += 4) {
                    $start = $i;
                    $end = min($i + 4, $n);
                    $slice = array_slice($prices, $start, $end - $start);
                    $average = array_sum($slice) / count($slice);
                    $key = $coin . '_' . $i;
                    $coinAverages[$key] = $average;
                }
            }

            // Save the averages to the database
            foreach ($coinAverages as $key => $average) {
                $query = "INSERT INTO coin_averages (key, value) VALUES ('$key', $average)";
                $result = $conn->query($query);
                if (!$result) {
                    die("Error: " . $conn->error);
                }
            }

            // Close the database connection
            $conn->close();
        }

        ?>
</body>
<script>
    const table = document.querySelector('table');
    const headers = table.querySelectorAll('thead th');
    const tbody = table.querySelector('tbody');
    let asc = true;

    // Add event listeners to each header cell
    headers.forEach((header, i) => {
        header.addEventListener('click', () => {
            sortTable(table, i, asc);
            asc = !asc;
        });
    });

    function sortTable(table, column, asc) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        // Sort the rows based on the value in the specified column
        const sorter = (a, b) => {
            const aVal = parseFloat(a.querySelector(`td:nth-child(${column + 1})`).textContent);
            const bVal = parseFloat(b.querySelector(`td:nth-child(${column + 1})`).textContent);
            return asc ? aVal - bVal : bVal - aVal;
        };
        rows.sort(sorter);

        // Re-append the sorted rows to the table
        rows.forEach(row => tbody.appendChild(row));
    }
</script>

</html>