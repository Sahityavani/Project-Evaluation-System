<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don't Try - Be a Developer</title>
    <style>
        body {
            background-color: #282c34;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            margin: 0;
        }

        .quote-box {
            background-color: #444c56;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h1 {
            color: #61dafb;
        }

        p {
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <div class="quote-box">
        <h1>Don't Try to Be a Hacker</h1>
        <p>
            <?php
                $quotes = [
                    "Real devs don't break systems, they build them.",
                    "Ethics over shortcuts. Be a developer, not a hacker.",
                    "Innovation comes from creation, not destruction.",
                    "A true coder solves problems, not causes them.",
                    "Write code that you can be proud of, not code you have to hide.",
                    "The world needs more builders, not breakers.",
                    "Secure your future by building secure code.",
                    "Hacking is easy, engineering is the real challenge.",
                    "Developers build, hackers break. Which side are you on?",
                    "Build systems that protect, not ones that exploit."
                ];

                echo $quotes[array_rand($quotes)];
            ?>
        </p>
    </div>
</body>

</html>