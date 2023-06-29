<?php
$host = 'localhost';
$db = 'id20978076_jokesdb';
$user = 'id20978076_root';
$password = 'Huytruong7522.';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}


function hasVoted($pdo, $jokeId)
{
    if (isset($_COOKIE['voted_jokes'])) {
        $votedJokes = explode(',', $_COOKIE['voted_jokes']);
        return in_array($jokeId, $votedJokes);
    }
    return false;
}


function getRandomJoke($pdo)
{
    if (isset($_COOKIE['seen_jokes'])) {
        $seenJokes = explode(',', $_COOKIE['seen_jokes']);
        $votedJokes = isset($_COOKIE['voted_jokes']) ? explode(',', $_COOKIE['voted_jokes']) : [];
        $placeholders = implode(',', array_fill(0, count($seenJokes) + count($votedJokes), '?'));
        $query = $pdo->prepare('SELECT * FROM jokes WHERE id NOT IN (' . $placeholders . ') ORDER BY RAND() LIMIT 1');
        $params = array_merge($seenJokes, $votedJokes);
        $query->execute($params);
    } else {
        $query = $pdo->prepare('SELECT * FROM jokes ORDER BY RAND() LIMIT 1');
        $query->execute();
    }
    $joke = $query->fetch();
    return $joke;
}



function handleVote($pdo, $jokeId, $voteType)
{
    $voteType = ($voteType === 'like') ? 'likes' : 'dislikes';
    $query = $pdo->prepare('UPDATE jokes SET ' . $voteType . ' = ' . $voteType . ' + 1 WHERE id = :id');
    $query->bindParam(':id', $jokeId);
    $query->execute();

    if (isset($_COOKIE['voted_jokes'])) {
        $votedJokes = $_COOKIE['voted_jokes'] . ',' . $jokeId;
    } else {
        $votedJokes = $jokeId;
    }
    setcookie('voted_jokes', $votedJokes, time() + (86400 * 30), '/'); // Cookie lasts for 30 days
}


$joke = getRandomJoke($pdo);


if (isset($_POST['vote']) && isset($_POST['joke_id'])) {
    $jokeId = $_POST['joke_id'];
    $voteType = $_POST['vote'];
    handleVote($pdo, $jokeId, $voteType);
    $joke = getRandomJoke($pdo);
    setcookie('seen_jokes', $_COOKIE['seen_jokes'] . ',' . $jokeId, time() + (86400 * 30), '/'); // Cookie lasts for 30 days
}
include 'index.html';
?>
