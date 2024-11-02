<html>
<head>
    <title>Movie List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        #header {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .menuItem {
            text-decoration: none;
            background-color: #e71616;
            color: white;
            border-radius: 17px;
            padding: 10px 20px;
            margin: 10px;
            display: inline-block;
        }
        .movie-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .movie-table th, .movie-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .movie-table th {
            background-color: #333;
            color: #fff;
        }
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #333;
            color: white;
            border: none;
        }
    </style>
</head>
<body>

<div id="header">
    <a class="menuItem" href="index.html">Home</a>
</div>

<div class="container">
    <h2>Movies</h2>

    <?php
    $db = new SQLite3('movie.db');

    // Retrieve filters from GET request
    $genre = isset($_GET['genre']) ? $_GET['genre'] : '';
    $recency = isset($_GET['recency']) ? $_GET['recency'] : '';
    $rating = isset($_GET['rating']) ? $_GET['rating'] : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 25;
    $offset = ($page - 1) * $limit;

    // Fixed total movie count and pages (adjust these as needed)
    $totalMovies = 250; // Set a fixed number of total movies
    $totalPages = ceil($totalMovies / $limit);

    // Base query with user filters
    $query = "SELECT * FROM titleBasics WHERE startYear LIKE '____' AND startYear BETWEEN '1000' AND '9999'";
    if (!empty($genre)) {
        $query .= " AND genres LIKE '%" . SQLite3::escapeString($genre) . "%'";
    }
    if (!empty($recency)) {
        $query .= " AND endYear >= " . intval($recency);
    }
    if (!empty($rating)) {
        $query .= " AND rating >= " . floatval($rating);
    }

    // Order by startYear descending and add limit/offset for pagination
    $query .= " ORDER BY startYear DESC LIMIT $limit OFFSET $offset";
    $result = $db->query($query);

    echo '<table class="movie-table">';
    echo '<tr><th>Movie Title</th><th>Year</th><th>Genres</th></tr>';

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $movieTitle = htmlspecialchars($row['primaryTitle']);
        $movieId = urlencode($row['tconst']); // Assuming 'tconst' is the unique identifier for each movie
        $year = htmlspecialchars($row['startYear']);
        $genres = htmlspecialchars($row['genres']);
        echo "<tr><td><a href='movieDetails.php?tconst=$movieId'>$movieTitle</a></td><td>$year</td><td>$genres</td></tr>";
    }

    echo '</table>';

    // Pagination links
    if ($totalPages > 1) {
        echo '<div class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<a class='active'>$i</a>";
            } else {
                echo "<a href='viewData.php?page=$i&genre=$genre&recency=$recency&rating=$rating'>$i</a>";
            }
        }
        echo '</div>';
    }

    $db->close();
    ?>
</div>

</body>
</html>