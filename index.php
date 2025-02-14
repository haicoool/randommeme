<?php
session_start();

function getMultipleMemes($count = 5)
{
    $apiUrl = "https://meme-api.com/gimme/$count"; 
    $response = file_get_contents($apiUrl);
    $memeData = json_decode($response, true);

    if (isset($memeData['memes'])) {
        return $memeData['memes']; // Returns an array of meme details
    }
    return [];
}

if (!isset($_SESSION['shownMemes'])) {
    $_SESSION['shownMemes'] = [];
}

// Get a unique meme
function getUniqueMeme()
{
    $maxTries = 10;
    $tries = 0;

    while ($tries < $maxTries) {
        $memes = getMultipleMemes(); 

        foreach ($memes as $meme) {
            if (!in_array($meme['url'], $_SESSION['shownMemes'])) {
                $_SESSION['shownMemes'][] = $meme['url'];

                if (count($_SESSION['shownMemes']) > 50) {
                    array_shift($_SESSION['shownMemes']);
                }

                return [
                    'url' => $meme['url'],
                    'subreddit' => $meme['subreddit']
                ];
            }
        }

        $tries++;
    }

    return [
        'url' => $memes[array_rand($memes)]['url'],
        'subreddit' => $memes[array_rand($memes)]['subreddit']
    ];
}

// Handle Next Meme button
if (isset($_GET['next'])) {
    $meme = getUniqueMeme();
    $_SESSION['memeUrl'] = $meme['url'];
    $_SESSION['memeSubreddit'] = $meme['subreddit'];
    header("Location: index.php");
    exit();
}

// Load stored meme or fetch a new one if not set
if (!isset($_SESSION['memeUrl']) || !isset($_SESSION['memeSubreddit'])) {
    $meme = getUniqueMeme();
    $_SESSION['memeUrl'] = $meme['url'];
    $_SESSION['memeSubreddit'] = $meme['subreddit'];
}

$memeUrl = $_SESSION['memeUrl'];
$memeSubreddit = $_SESSION['memeSubreddit'] ?? 'Unknown'; // Prevent undefined warning
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>😂 Meme Generator</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #f4f4f4;
        }

        .meme-container {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #2a2a2a;
            padding: 10px;
            border-radius: 10px;
            overflow: hidden;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .subreddit {
            margin-top: 10px;
            font-size: 14px;
            color: #bbb;
        }

        .subreddit a {
            color: #61dafb;
            text-decoration: none;
            font-weight: bold;
        }

        .buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        button {
            flex: 1;
            margin: 5px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .next-btn {
            background: #007bff;
            color: white;
        }

        .next-btn:hover {
            background: #0056b3;
        }

        .download-btn {
            background: #28a745;
            color: white;
        }

        .download-btn:hover {
            background: #218838;
        }

        .created-by {
            margin-top: 20px;
            font-size: 14px;
            color: #bbb;
        }

        @media (max-width: 480px) {
            .container {
                width: 100%;
            }

            h2 {
                font-size: 18px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>😂 Random Meme Generator</h2>
        <div class="meme-container">
            <img src="<?= $memeUrl ?>" alt="Random Meme">
        </div>
        <p class="subreddit">Source: <a href="https://www.reddit.com/r/<?= $memeSubreddit ?>" target="_blank">r/<?= $memeSubreddit ?></a></p>
        <div class="buttons">
            <a href="?next=1"><button class="next-btn">Next Meme 🔄</button></a>
        </div>
        <p class="created-by">Created by Haiqal</p>
    </div>

</body>
</html>
