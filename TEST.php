<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Miauw</title>
    <style>
        #gameCanvas {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        }
    </style>
</head>
<body>

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script>
        const canvas = document.getElementById("gameCanvas");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const ctx = canvas.getContext("2d");

        const pixelSize = 2;
        let squareX = 0;
        let squareY = 0;
        let distance = 32 * pixelSize;

        let score = 0;
        let totalFish = 0;

        let intervalTime = 0.2;
        let direction = 'right';
        let intervalFunction = 0;
        let catName = 0;

        let _ = [255, 255, 255, 0];
        let U = [255, 255, 255, 1];
        let I = 0;
        let W = 0;
        let O = 0;
        let H = 0;
        let V = [255, 174, 200, 1]; // roos neusje

        function update() {
            
            let index;
            let itemType;
            clearPrev();
            switch (direction) {
                case 'right':
                    squareX += 1;
                    if (squareX == canvas.width-distance) {
                        direction = 'left';
                    }

                    index = placedLocations.findIndex(item => item.x == roundToWidth(squareX)+distance && item.y === roundToWidth(squareY));
                    if (index !== -1) { //something there
                        itemType = placedLocations[index].type;
                        if (itemType == 'fish') {
                            ctx.clearRect(roundToWidth(squareX)+distance, squareY, distance, distance);
                            placedLocations.splice(index, 1);
                            direction = 'left';
                            score++;    
                            checkScore();                     

                        } else if (itemType == 'box') {
                            gameOver();
                        }
                    }
                    break;

                case 'left':
                    squareX -= 1;
                    if (squareX == 0) {
                        direction = 'right';
                    }

                    index = placedLocations.findIndex(item => item.x == roundToWidth(squareX) && item.y === roundToWidth(squareY));
                    if (index !== -1) { //something there
                        itemType = placedLocations[index].type;
                        if (itemType == 'fish') {
                            ctx.clearRect(roundToWidth(squareX), squareY, distance, distance);
                            placedLocations.splice(index, 1);
                            direction = 'right';
                            score++;       
                            checkScore();                  

                        } else if (itemType == 'box') {
                            gameOver();
                        }
                    }

                    break;
            }
            drawSquare(squareX, squareY);
        }

        function roundToWidth(number) {
            return Math.floor(number / distance) * distance;
        }

        function drawSquare(x, y) {

            const pixels = [
                [_, _, _, _, _, _, I, I, I, _, _, _, _, _, _, _, _, _, _, _, I, I, I, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, I, I, W, I, I, _, _, _, _, _, _, _, _, _, I, I, W, I, I, _, _, _, _, _, _, _, _],
                [_, _, _, _, I, I, H, W, O, I, I, _, _, _, _, _, _, _, I, I, W, W, H, I, I, _, _, _, _, _, _, _],
                [_, _, _, _, I, H, H, W, O, O, I, _, _, _, _, _, _, _, I, W, W, W, H, H, I, _, _, _, _, _, _, _],
                [_, _, _, I, I, W, W, W, O, O, I, I, _, _, _, _, _, I, I, W, W, W, H, H, I, I, _, _, _, _, _, _],
                [_, _, _, I, H, H, W, W, O, O, O, I, _, _, _, _, _, I, W, W, W, W, W, W, H, I, _, _, _, _, _, _],
                [_, _, I, I, H, H, H, W, W, O, O, I, I, _, _, _, I, I, W, W, W, W, W, W, W, I, I, _, _, _, _, _],
                [_, _, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, _, _, _, _, _],
                [_, _, I, W, W, W, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, O, W, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, O, O, O, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, O, O, O, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, O, O, O, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, O, O, W, W, W, W, W, W, W, H, H, H, H, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, O, W, W, W, W, U, I, I, W, W, H, H, H, U, I, I, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, O, W, W, W, W, W, I, I, I, W, W, W, H, H, I, I, I, H, H, H, H, H, H, I, _, _, _, _, _],
                [_, _, I, W, W, W, W, W, W, I, I, I, W, W, W, W, H, I, I, I, H, H, H, H, H, H, I, _, _, _, _, _],
                [I, I, I, I, I, I, W, W, W, W, W, W, W, W, W, W, W, H, H, H, H, H, H, I, I, I, I, I, I, _, _, _],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, V, V, V, W, W, H, H, H, H, H, H, H, H, I, _, _, _, _, _],
                [I, I, I, I, I, I, W, W, W, W, W, W, W, W, V, W, W, W, W, W, H, H, H, I, I, I, I, I, I, _, _, _],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, I, _, _, _, _, _],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, I, _, _, _, _, _],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, I, _, I, I, I, I],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, W, W, I, I, I, H, H, I],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, O, O, O, W, I, I, W, W, H, I],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, O, O, O, O, W, I, W, W, W, I, I],
                [_, _, I, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, O, O, O, O, W, I, W, W, I, _, _],
                [_, _, I, H, H, W, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, O, O, O, O, W, I, W, W, I, _, _],
                [_, _, I, H, H, H, H, W, W, W, W, W, W, W, W, W, W, W, W, O, O, O, O, W, W, W, I, W, W, I, _, _],
                [_, _, I, H, H, H, H, H, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, I, W, I, I, _, _],
                [_, _, I, H, H, H, H, H, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, W, I, I, I, _, _, _],
                [_, _, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, I, _, _, _, _],
            ];

            for (let i = 0; i < pixels.length; i++) {
                for (let j = 0; j < pixels[i].length; j++) {
                    const color = `rgb(${pixels[i][j][0]}, ${pixels[i][j][1]}, ${pixels[i][j][2]}, ${pixels[i][j][3]})`;
                    ctx.fillStyle = color;
                    ctx.fillRect(x + (j * pixelSize), y + (i * pixelSize), pixelSize, pixelSize);
                }
            }
        }

        let placedLocations = [];

        function drawRandomSquares() {
            const numRows = Math.floor(canvas.height / distance);
            const rowIndices = Array.from({ length: numRows }, (_, i) => i);

            for (let rowIndex of rowIndices) {
                let x, y;
                do {
                    x = Math.floor(Math.floor(Math.random() * (canvas.width - distance)) / distance) * distance;
                    y = rowIndex * distance;
                    type = 'fish';
                } while (locationOverlaps(x, y));
                
                drawFish(x, y);
                placedLocations.push({ x, y , type});
            }

            for (let rowIndex of rowIndices) {
                let x, y;
                do {
                    x = Math.floor(Math.floor(Math.random() * (canvas.width - distance)) / distance) * distance;
                    y = rowIndex * distance;
                    type = 'box';
                } while (locationOverlaps(x, y));

                if(Math.random() < 0.3) {
                    drawBox(x, y);
                    placedLocations.push({ x, y, type});
                }
            }  
        }

        function locationOverlaps(x, y) {
            for (let location of placedLocations) {
                if (x === location.x && y === location.y) {
                    return true;
                }
            }
            return false;
        }

        function drawFish(x, y) {
            const G = [0, 0, 0, 1]; //BLACK
            const Y = [102, 204, 108, 1]; //GREEN
            const X = [159, 246, 164, 1]; //LIGHT
            const M = [49, 100, 52, 1]; //DARK
            const pixels = [
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [G, G, G, _, _, _, _, _, _, _, _, _, _, _, _, _, G, G, G, G, G, G, G, G, G, _, _, _, _, _, _, _],
                [G, X, X, G, _, _, _, _, _, _, _, _, _, _, _, _, G, Y, X, X, X, X, X, X, X, G, G, G, _, _, _, _],
                [_, G, Y, X, G, G, _, _, _, _, _, _, _, _, G, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, X, X, X, G, G, _, _],
                [_, G, M, Y, X, X, G, _, _, _, _, _, G, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, X, X, G, _],
                [_, _, G, M, Y, Y, X, G, _, G, G, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, M, Y, Y, G, Y, Y, X, G],
                [_, _, _, G, M, Y, Y, X, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, M, Y, Y, Y, Y, Y, Y, G],
                [_, _, _, _, G, M, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, M, Y, Y, Y, Y, Y, Y, G],
                [_, _, _, _, G, M, M, M, M, M, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, M, Y, Y, Y, Y, G, _],
                [_, _, _, _, G, M, M, M, G, G, G, M, M, Y, Y, Y, Y, G, Y, Y, Y, Y, Y, Y, M, M, M, M, G, G, _, _],
                [_, _, _, G, M, M, M, G, _, _, _, G, G, M, M, M, G, M, Y, Y, M, M, M, M, G, G, G, G, _, _, _, _],
                [_, _, G, M, M, M, G, _, _, _, _, _, _, G, G, G, G, M, M, M, G, G, G, G, _, _, _, _, _, _, _, _],
                [_, _, G, G, G, G, _, _, _, _, _, _, _, _, _, _, G, G, G, G, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
            ];

            for (let i = 0; i < pixels.length; i++) {
                for (let j = 0; j < pixels[i].length; j++) {
                    const color = `rgb(${pixels[i][j][0]}, ${pixels[i][j][1]}, ${pixels[i][j][2]}, ${pixels[i][j][3]})`;
                    ctx.fillStyle = color;
                    ctx.fillRect(x + (j * pixelSize), y + (i * pixelSize), pixelSize, pixelSize);
                }
            }
            totalFish++;
        }

        function drawBox(x, y) {
            const G = [0, 0, 0, 1]; //BLACK
            const Y = [253, 190, 113, 1]; //BROWN
            const N = [197, 149, 92, 1]; //DARK
            const M = [156, 117, 70, 1]; //VERY DARK
            const pixels = [
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, G, G, G, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, G, G, N, N, N, G, G, G, G, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, G, G, G, _, _, _, _, G, Y, Y, Y, Y, Y, N, N, N, N, G, G, G, G, _, _, _, _, _],
                [_, _, _, _, _, G, N, N, N, G, G, _, _, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, N, N, N, N, G, G, _, _, _],
                [_, _, _, _, G, Y, Y, Y, Y, N, N, G, G, G, G, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, N, G, _, _, _],
                [_, _, _, G, Y, Y, Y, Y, Y, Y, G, G, N, N, N, N, G, G, G, G, G, Y, Y, Y, Y, Y, Y, N, G, _, _, _],
                [_, _, G, Y, Y, Y, Y, Y, G, G, M, G, N, N, N, N, N, N, N, N, N, G, G, G, G, G, Y, G, _, _, _, _],
                [_, G, Y, Y, Y, Y, Y, G, M, M, M, G, N, N, N, N, N, N, N, N, N, N, N, N, N, N, G, _, _, _, _, _],
                [_, _, G, Y, Y, Y, G, M, M, M, M, G, N, N, N, N, N, N, N, N, N, N, N, N, N, G, N, G, G, _, _, _],
                [_, _, _, G, Y, G, M, M, M, M, M, G, N, N, N, N, N, N, N, N, N, N, N, N, G, Y, Y, N, N, G, G, _],
                [_, _, _, G, G, G, G, M, M, M, M, G, N, N, N, N, N, N, N, N, N, N, N, G, Y, Y, Y, Y, Y, N, N, G],
                [_, G, G, Y, Y, Y, Y, G, G, G, M, G, N, N, N, N, N, N, N, N, N, N, G, Y, Y, Y, Y, Y, Y, Y, Y, G],
                [G, Y, Y, Y, Y, Y, Y, Y, Y, Y, G, G, G, G, G, G, N, N, N, N, N, G, Y, Y, Y, Y, Y, Y, Y, Y, G, _],
                [G, N, N, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, G, G, N, N, G, Y, Y, Y, Y, Y, Y, Y, Y, G, _, _],
                [_, G, G, N, N, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, G, G, G, Y, Y, Y, Y, Y, Y, Y, G, _, _, _],
                [_, _, _, G, G, N, N, N, Y, Y, Y, Y, Y, Y, Y, Y, Y, G, N, G, N, G, G, Y, Y, Y, Y, G, _, _, _, _],
                [_, _, _, _, G, G, G, G, N, N, N, N, N, Y, Y, Y, G, Y, N, G, N, N, N, G, Y, Y, G, _, _, _, _, _],
                [_, _, _, _, G, Y, Y, Y, G, G, G, G, G, N, N, G, Y, Y, N, G, N, N, N, N, G, G, G, _, _, _, _, _],
                [_, _, _, _, G, Y, Y, Y, Y, Y, Y, Y, Y, G, G, Y, Y, Y, N, G, N, N, N, N, N, Y, G, _, _, _, _, _],
                [_, _, _, _, G, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, N, G, N, N, N, N, N, G, _, _, _, _, _, _],
                [_, _, _, _, G, N, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, N, G, N, N, N, N, G, _, _, _, _, _, _, _],
                [_, _, _, _, G, G, N, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, Y, N, G, N, N, N, G, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, G, N, N, N, Y, Y, Y, Y, Y, Y, Y, Y, N, G, N, N, G, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, G, G, G, N, N, N, N, N, N, Y, Y, N, G, N, G, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, G, G, G, G, G, G, N, N, N, G, G, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, G, G, G, G, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _],
                [_, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _, _]
            ];

            for (let i = 0; i < pixels.length; i++) {
                for (let j = 0; j < pixels[i].length; j++) {
                    const color = `rgb(${pixels[i][j][0]}, ${pixels[i][j][1]}, ${pixels[i][j][2]}, ${pixels[i][j][3]})`;
                    ctx.fillStyle = color;
                    ctx.fillRect(x + (j * pixelSize), y + (i * pixelSize), pixelSize, pixelSize);
                }
            }
        }

        function clearPrev() {
            ctx.clearRect(squareX, squareY, distance, distance);
        }

        function moveSquare(event) {
            clearPrev();
            switch (event.keyCode) {
                case 38: // up
                    if (squareY != 0) {
                        squareY -= distance;
                    }
                    break;
                case 40: // down
                    if (squareY < canvas.height-distance) {
                        squareY += distance;
                    }
                    break;
            }
            drawSquare(squareX, squareY);
        }

        function checkScore() {
            if (score === totalFish) {
                clearInterval(intervalFunction);
                $('<div id="end"></div>').appendTo('body');
                $('#end').html('<h1>MIAUW! 😸</h1><p> Je hebt ' + score + ' visjes verzameld!</p>');
            }
        }

        function gameOver() {
            clearInterval(intervalFunction);
            $('#gameOver').show();
        }

        function startGame() {
            $('<canvas id="gameCanvas"></canvas>').appendTo('body');
            drawSquare(squareX, squareY);
            drawRandomSquares();
            document.addEventListener("keydown", moveSquare);
            intervalFunction = setInterval(update, intervalTime);
        }

        //SECRET CODES
        let unoArray = [];
        function checkUno(c, d) {
        if (c === d) return true;
        if (c == null || d == null) return false;
        if (c.length !== d.length) return false;
        for (var e = 0; e < c.length; ++e) if (c[e] !== d[e]) return false;
        return true;
        }
        const sequences = {
            uno: {
                keys: [85, 78, 79],
                state: []
            },
            vlekje: {
                keys: [86, 76, 69, 75, 74, 69],
                state: []
            },
            rosse: {
                keys: [82, 79, 83, 83, 69],
                state: []
            }
        };

        document.addEventListener("keydown", function (event) {
            const keyCode = event.keyCode;
            
            for (const sequenceName in sequences) {
                const sequence = sequences[sequenceName];
                
                sequence.state.push(keyCode);
                sequence.state = sequence.state.slice(-sequence.keys.length);
                if (JSON.stringify(sequence.state) === JSON.stringify(sequence.keys)) {
                
                    if (sequenceName == 'uno') {
                        I = [48, 48, 48, 1]; //donker grijs
                        W = [255, 255, 255, 1]; //wit
                        O = [88, 88, 88, 1]; //grijs
                        H = [88, 88, 88, 1]; //grijs
                    } else if (sequenceName == 'vlekje') {
                        I = [48, 48, 48, 1]; //donker grijs
                        W = [255, 255, 255, 1]; //wit
                        O = [88, 88, 88, 1]; //grijs
                        H = [255, 160, 94, 1]; //ros
                    } else if (sequenceName == 'rosse') {
                        I = [48, 48, 48, 1]; //donker ros
                        W = [255, 160, 94, 1]; //ros
                        O = [255, 160, 94, 1]; //ros
                        H = [255, 160, 94, 1]; //ros
                    }
                catName= sequenceName;
                startGame();
                }
            }
        });
    </script>
</body>
</html>