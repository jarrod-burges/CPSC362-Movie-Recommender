<html>
<body>
<?php
$db = new SQLite3('movie.db'); // Replace 'database.sqlite' with the path to your SQLite3 database

$query = "SELECT primaryName FROM nameBasics LIMIT 250";
$result = $db->query($query);

echo '
<ul style="list-style-type: none; box-sizing: border-box; padding: 0; margin: 0; display: flex; padding: 25px;">
<li><a class="menuItem" style="text-decoration: none; background-color: #e71616; color: white; border-radius: 17px;
padding: 10px; margin: 0 10px; align-items: center; justify-content: space-between;" href="index.html">Home</a></li>
</ul>';

echo '<p style="font-size: 25px; padding-left: 10px;"><strong>Scheduling:</strong><p>';
echo '<table align="left" cellspacing="5" cellpadding="8">';

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo '<tr><td align="left">' . 'Primary Name: ' . $row['primaryName'] . '<p>';
}

$db->close();
?>
</body>
</html>
