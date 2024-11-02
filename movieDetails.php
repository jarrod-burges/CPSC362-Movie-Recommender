<html>
<head>
    <title>Movie Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 5px;
        }
        .section-title {
            color: #555;
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 2px solid #e71616;
            padding-bottom: 5px;
        }
        p, li {
            color: #555;
            line-height: 1.6;
        }
        .info-block {
            margin-bottom: 10px;
        }
        .info-block strong {
            color: #e71616;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        .cast-list, .alternate-titles {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    $db = new SQLite3('movie.db');

    // Retrieve the movie ID from the URL
    $movieId = isset($_GET['tconst']) ? $_GET['tconst'] : '';

    if ($movieId) {
        // Query for basic movie details
        $query = "SELECT * FROM titleBasics WHERE tconst = '" . SQLite3::escapeString($movieId) . "'";
        $result = $db->query($query);

        if ($movie = $result->fetchArray(SQLITE3_ASSOC)) {
            // Display basic movie details
            echo '<h2>' . htmlspecialchars($movie['primaryTitle']) . '</h2>';
            echo '<div class="info-block"><strong>Original Title:</strong> ' . htmlspecialchars($movie['originalTitle']) . '</div>';
            echo '<div class="info-block"><strong>Genres:</strong> ' . htmlspecialchars($movie['genres']) . '</div>';
            echo '<div class="info-block"><strong>Release Year:</strong> ' . htmlspecialchars($movie['startYear']) . '</div>';
            echo '<div class="info-block"><strong>End Year:</strong> ' . htmlspecialchars($movie['endYear']) . '</div>';
            echo '<div class="info-block"><strong>Runtime:</strong> ' . htmlspecialchars($movie['runtimeMinutes']) . ' minutes</div>';
            echo '<div class="info-block"><strong>Type:</strong> ' . htmlspecialchars($movie['titleType']) . '</div>';
            echo '<div class="info-block"><strong>Is Adult:</strong> ' . ($movie['isAdult'] ? 'Yes' : 'No') . '</div>';

            // Query for movie rating
            $ratingQuery = "SELECT averageRating, numVotes FROM titleRatings WHERE tconst = '" . SQLite3::escapeString($movieId) . "'";
            $ratingResult = $db->query($ratingQuery);
            if ($rating = $ratingResult->fetchArray(SQLITE3_ASSOC)) {
                echo '<div class="info-block"><strong>Average Rating:</strong> ' . htmlspecialchars($rating['averageRating']) . '</div>';
                echo '<div class="info-block"><strong>Number of Votes:</strong> ' . htmlspecialchars($rating['numVotes']) . '</div>';
            }

            // Query for directors and writers
            echo '<div class="section-title">Crew</div>';
            $crewQuery = "SELECT directors, writers FROM titleCrew WHERE tconst = '" . SQLite3::escapeString($movieId) . "'";
            $crewResult = $db->query($crewQuery);
            if ($crew = $crewResult->fetchArray(SQLITE3_ASSOC)) {
                // Get directors' names
                if (!empty($crew['directors'])) {
                    $directorIds = explode(',', $crew['directors']);
                    $directorNames = [];
                    foreach ($directorIds as $id) {
                        $nameQuery = "SELECT primaryName FROM nameBasics WHERE nconst = '" . SQLite3::escapeString($id) . "'";
                        $nameResult = $db->query($nameQuery);
                        if ($name = $nameResult->fetchArray(SQLITE3_ASSOC)) {
                            $directorNames[] = htmlspecialchars($name['primaryName']);
                        }
                    }
                    echo '<div class="info-block"><strong>Directors:</strong> ' . implode(', ', $directorNames) . '</div>';
                }

                // Get writers' names
                if (!empty($crew['writers'])) {
                    $writerIds = explode(',', $crew['writers']);
                    $writerNames = [];
                    foreach ($writerIds as $id) {
                        $nameQuery = "SELECT primaryName FROM nameBasics WHERE nconst = '" . SQLite3::escapeString($id) . "'";
                        $nameResult = $db->query($nameQuery);
                        if ($name = $nameResult->fetchArray(SQLITE3_ASSOC)) {
                            $writerNames[] = htmlspecialchars($name['primaryName']);
                        }
                    }
                    echo '<div class="info-block"><strong>Writers:</strong> ' . implode(', ', $writerNames) . '</div>';
                }
            }

            // Query for principal cast with name joining
            echo '<div class="section-title">Cast</div>';
            echo '<ul class="cast-list">';
            $castQuery = "
                SELECT p.category, p.job, p.characters, n.primaryName
                FROM titlePrincipals AS p
                JOIN nameBasics AS n ON p.nconst = n.nconst
                WHERE p.tconst = '" . SQLite3::escapeString($movieId) . "'
                ORDER BY p.ordering ASC
            ";
            $castResult = $db->query($castQuery);
            while ($cast = $castResult->fetchArray(SQLITE3_ASSOC)) {
                $character = $cast['characters'] ? ' as ' . htmlspecialchars($cast['characters']) : '';
                echo '<li><strong>' . htmlspecialchars($cast['category']) . ':</strong> ' . htmlspecialchars($cast['primaryName']) . $character . '</li>';
            }
            echo '</ul>';

            // Query for alternate titles
            echo '<div class="section-title">Alternate Titles</div>';
            echo '<ul class="alternate-titles">';
            $akasQuery = "SELECT title, region, language FROM titleAkas WHERE titleId = '" . SQLite3::escapeString($movieId) . "' AND isOriginalTitle = 0";
            $akasResult = $db->query($akasQuery);
            while ($aka = $akasResult->fetchArray(SQLITE3_ASSOC)) {
                echo '<li>' . htmlspecialchars($aka['title']) . ' (' . htmlspecialchars($aka['region']) . ', ' . htmlspecialchars($aka['language']) . ')</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Movie not found.</p>';
        }
    } else {
        echo '<p>No movie selected.</p>';
    }

    $db->close();
    ?>
</div>

</body>
</html>