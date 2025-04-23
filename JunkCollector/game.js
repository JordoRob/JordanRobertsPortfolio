


$(document).ready(function() {
    
    setup();
});
function setup(){
let health=3;
let spaceFacts=[
    "The first space tourist was American businessman Dennis Tito, who visited the International Space Station in 2001.",
    "The first artificial satellite in space was called Sputnik.",
    "The first living creature in space was a dog named Laika, who was sent into orbit by the Soviet Union in 1957.",
    "There are about 4,700 satellites still in space, but only an approximate 1,800 are still working.",
    "A piece of space debris can reach speeds of 4.3 to 5 miles per second. That's nearly 7 times faster than a bullet and just about the equivalent of being hit by a bowling ball moving at 300 miles per hour.",
    "An average total between 200 - 400 tracked space debris enter Earths atmosphere every year.",
    "Your chances of getting hit by a falling piece of space waste is 10 million times smaller than the annual odds of being struck by lightning.",
    "There are almost 10,000 metric tons of man-made space waste currently orbiting our planet",
    "A one-millimeter object—the size of a pencil point—could destroy a spacecrafts ability to power up or to reach a certain altitude upon impact",
    "Space debris can be as large as a school bus or as small as paint chips.",
    "On average, one piece of space junk has fallen back into Earths atmosphere every day for the last 50 years.",
    "Neutron stars can spin at a rate of 600 rotations per second",
    "There is an uncountable number of stars in the known universe"
]
    var shipExplodeAnimation = [
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image()
    ];
    shipExplodeAnimation[0].src = "img/shipExplosion/tile000.png";
    shipExplodeAnimation[1].src = "img/shipExplosion/tile001.png";
    shipExplodeAnimation[2].src = "img/shipExplosion/tile002.png";
    shipExplodeAnimation[3].src = "img/shipExplosion/tile003.png";
    shipExplodeAnimation[4].src = "img/shipExplosion/tile004.png";
    shipExplodeAnimation[5].src = "img/shipExplosion/tile005.png";
    shipExplodeAnimation[6].src = "img/shipExplosion/tile006.png";
    shipExplodeAnimation[7].src = "img/shipExplosion/tile007.png";
    shipExplodeAnimation[8].src = "img/shipExplosion/tile008.png";
    shipExplodeAnimation[9].src = "img/shipExplosion/tile009.png";
    shipExplodeAnimation[10].src = "img/shipExplosion/tile010.png";
    shipExplodeAnimation[11].src = "img/shipExplosion/tile011.png";
    shipExplodeAnimation[12].src = "img/shipExplosion/tile012.png";
    shipExplodeAnimation[13].src = "img/shipExplosion/tile013.png";
    shipExplodeAnimation[14].src = "img/shipExplosion/tile014.png";
    shipExplodeAnimation[15].src = "img/shipExplosion/tile015.png";
    shipExplodeAnimation[16].src = "img/shipExplosion/tile016.png";
    shipExplodeAnimation[17].src = "img/shipExplosion/tile017.png";



    var explodeAnimation = [
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image()
    ];
    explodeAnimation[5].src = "img/explode1.png";
    explodeAnimation[4].src = "img/explode2.png";
    explodeAnimation[3].src = "img/explode3.png";
    explodeAnimation[2].src = "img/explode4.png";
    explodeAnimation[1].src = "img/explode5.png";
    explodeAnimation[0].src = "img/explode6.png";


    var pointImg = [
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image(),
        new Image()
    
    ];
    pointImg[0].src = "img/Garbage1.png";
    pointImg[1].src = "img/Garbage2.png";
    pointImg[2].src = "img/Garbage3.png";
    pointImg[3].src = "img/Garbage4.png";
    pointImg[4].src = "img/Garbage5.png";
    pointImg[5].src = "img/Garbage6.png";
    pointImg[6].src = "img/Garbage7.png";
    pointImg[7].src = "img/Garbage8.png";
    pointImg[8].src = "img/Garbage9.png";

    var pop= new Audio('assets/pop.wav');
    var recharge= new Audio('assets/recharge.mp3');
    var music = new Audio('assets/music.mp3');
    var damage = new Audio('assets/boom.wav');
    var game_end= new Audio('assets/game-over.mp3');

   audio= document.getElementById("audio");
    music.volume=.2;
    pop.volume=.2;
    recharge.volume=.2;
    damage.volume=.2;
    game_end.volume=.2;
    audio.onclick = function() {
        if(music.paused){
            music.play();
            music.loop=true;
            }else{
                music.pause();
            }

}


    var score = 0;
    var ship = new Image();
    ship.src = "img/ship.gif";
    var asteroid = new Image();
    asteroid.src = "img/asteroid.png";
    document.onkeydown = keyDownHandler;
    document.onkeyup = keyUpHandler;
    let rightPressed = false;
    let leftPressed = false;
    let upPressed = false;
    let downPressed = false;
    let blink=false;
    let invulnerable=false;
    let maxY= window.screen.height-240;
    let maxX= window.screen.width- 75;
    maxY=maxY*.75;
    let BG=document.getElementById("background");
    let ctx=BG.getContext("2d");
    let FG=document.getElementById("foreground");
    let ctx2=FG.getContext("2d");
    BG.width=maxX;
    BG.height=maxY;
    FG.width=maxX/1.5;
    FG.height=maxY/1.5;
    var background = new Image();
    background.src = "img/BG.png";
    var foreground = new Image();
    foreground.src = "img/foreground.png";
    foreground.style.height="240px";
    let foregroundScrollSpeed=1;
    let foregroundPosition=0;
    let scrollSpeed=0.2;
    let BGPosition=0;
    let shipScale=maxX/2450;
    let difficultyCounter=1;
    let shipRotation=90;
    let shipPosX=50;
    let shipPosY=450;
    var audio = new Audio('audio_file.mp3');

    let collection=[];

    let asteroids=[];
    let asteroidCount=0;
    let objectSpeedXMax=5;
    let objectSpeedXMin=1;
    let objectRotationMax=5;
    let objectRotationMin=-5;
    console.log(maxY);
    if(maxY<800){
        var factorTop=-.24;
        var factorBottom=.58;
    }else{
        var factorTop=-.14;
        var factorBottom=.57;
    }
    let pointCount=0;
    let points = [];
    let info=document.getElementsByClassName("info")[0];
    info.innerHTML=spaceFacts[Math.floor(Math.random() * (10 - 0 + 1) ) + 0];
    var main = setInterval(function() {
        ctx.clearRect(0, 0, BG.width, BG.height);
        ctx2.clearRect(0, 0, FG.width, FG.height);
        $("#scoreCard").html(score);

        
        ctx.drawImage(background, BGPosition, 0);
     // draw image 2
        ctx.drawImage(background, BGPosition+ BG.width, 0);

        ctx2.drawImage(foreground, foregroundPosition, factorTop*maxY);
        ctx2.drawImage(foreground, foregroundPosition+ foreground.width, factorTop*maxY);
        ctx2.drawImage(foreground, foregroundPosition+ foreground.width*2, factorTop*maxY);

        ctx2.drawImage(foreground, foregroundPosition, factorBottom*maxY);
        ctx2.drawImage(foreground, foregroundPosition+ foreground.width, factorBottom*maxY);
        ctx2.drawImage(foreground, foregroundPosition+ foreground.width*2, factorBottom*maxY);
        
 
   
        foregroundPosition -= foregroundScrollSpeed;
        // update image width
        BGPosition -= scrollSpeed;
        
        if(-1*foregroundPosition >= foreground.width)
            foregroundPosition = 0;
        //resetting the images when the first image entirely exits the screen
        if (-1*BGPosition >= maxX)
            BGPosition = 0
    
    
    shipMovement();
    handleAsteroid();
    handlePoints();
    
        
    

        // document.body.appendChild(img);
    
    
    },10);

   var secondary= setInterval(function(){
        difficultyCounter+=1;
        info.style.left="-50%";
        setTimeout(function(){
            info.remove();
            info.style.left="150%";
            info.innerHTML=spaceFacts[Math.floor(Math.random() * (10 - 0 + 1) ) + 0];
        document.getElementById("bottomBar").appendChild(info);
            setTimeout(function(){
                info.style.left="50%";
            },1500)
            
        },1500);
        

    },15000);
 var pointSpawn= setInterval(function(){
        if(pointCount<40){
            spawnPoints();
            pointCount+=1;
        }},5000);

    function shipMovement(){
//draw using canvas

        if(rightPressed) {
            shipPosX = Math.min(shipPosX+5, maxX);
            shipRotation=90;
        }
        else if(leftPressed) {
            shipPosX = Math.max(shipPosX-5, 0);
            shipRotation=-90;
        }
    if(upPressed) {
        shipPosY = Math.max(shipPosY-5, 20);
            if(rightPressed){
                shipRotation=45;
            }else if(leftPressed){
                shipRotation=-45;
            }else{
            shipRotation=0;}
        }
        else if(downPressed) {
            shipPosY = Math.min(shipPosY+5, maxY-20);
            if(rightPressed){
                shipRotation=135;
            }else if(leftPressed){
               shipRotation=-135;
            }else{
            shipRotation=180;}
        }
        drawImage(ship, shipPosX, shipPosY,shipScale, shipRotation);
        }

    
    
    function keyDownHandler(e){
        if(e.keyCode == 39||e.keyCode==68){
        rightPressed = true;
    }
    else if(e.keyCode == 37||e.keyCode==65){
        leftPressed = true;
    }
    else if(e.keyCode == 38||e.keyCode==87){
        upPressed = true;
    }
    else if(e.keyCode == 40||e.keyCode==83){
        downPressed = true;
    }}
    
    function keyUpHandler(e){
    if(e.keyCode == 39||e.keyCode==68){
        rightPressed = false;
    }
    else if(e.keyCode == 37||e.keyCode==65){
        leftPressed = false;
    }
    else if(e.keyCode == 38||e.keyCode==87){
        upPressed = false;
    }
    else if(e.keyCode == 40||e.keyCode==83){
        downPressed = false;
    }}

    function handleAsteroid(){
        if(asteroidCount<difficultyCounter+5 && asteroidCount<40){
            spawnAsteroid();
            asteroidCount+=1;
        }
        for(let i=0; i<asteroids.length; i++){
            if(asteroids[i]['y']>maxY || asteroids[i]['y']<0){
                asteroids[i]['speedY']=-1*asteroids[i]['speedY'];
            }
            if(asteroids[i]['x']<-100){
                asteroids.splice(i,1);
                asteroidCount-=1;
            }else{
                if(asteroids[i]['explode']!=null){
                    
                    if(asteroids[i]['explode']<=0){
                        asteroids.splice(i,1);
                        asteroidCount-=1;

                    }else{
                        drawImage(explodeAnimation[Math.round(asteroids[i]['explode']/2)], asteroids[i]['x'], asteroids[i]['y'],asteroids[i]['scale'], asteroids[i]['rotation']);
                    asteroids[i]['explode']-=1;
                    }
                    
                }else{
                asteroids[i]['x']+=asteroids[i]['speedX'];
                asteroids[i]['y']+=asteroids[i]['speedY'];
                asteroids[i]['rotation']+=asteroids[i]['rotationSpeed'];
                drawImage(asteroid, asteroids[i]['x'], asteroids[i]['y'],asteroids[i]['scale'], asteroids[i]['rotation']);}
             }
            if(checkCollision(asteroids[i])&&!invulnerable){
                badCollide();
                asteroids[i]['explode']=12;
                damage.play();
            }
        }
    }

    function spawnAsteroid(){
        let objectSpeedX=-1*(Math.floor(Math.random() * (objectSpeedXMax - objectSpeedXMin + 1) ) + objectSpeedXMin);
        let objectSpeedY=Math.round(Math.random()) * 2 - 1*(Math.floor(Math.random() * (objectSpeedXMax - objectSpeedXMin + 1) ) + objectSpeedXMin);
        let objectRotation=Math.floor(Math.random() * (objectRotationMax - objectRotationMin + 1) ) + objectRotationMin;
        let asteroidScale=shipScale*Math.floor(Math.random() * (3 - 1 + 1) ) + 1;
        let asteroidX=maxX;
        let asteroidY=Math.floor(Math.random() * (maxY - 0 + 1) ) + 0;
        asteroids.push({
            x:asteroidX,
            y:asteroidY,
            speedX:objectSpeedX,
            speedY:objectSpeedY,
            rotation:0,
            rotationSpeed:objectRotation,
            scale:asteroidScale,
            width:asteroid.width*asteroidScale,
            height:asteroid.height*asteroidScale
        });
    }

    function handlePoints(){
        for(let i=0; i<points.length; i++){
            if(points[i]['y']>maxY || points[i]['y']<0){
                points[i]['speedY']=-1*points[i]['speedY'];
            }
            if(points[i]['x']<-100){
                points.splice(i,1);
                pointCount-=1;
            }else{
                points[i]['x']+=points[i]['speedX'];
                points[i]['y']+=points[i]['speedY'];
                points[i]['rotation']+=points[i]['rotationSpeed'];
                drawImage(pointImg[points[i]['type']], points[i]['x'], points[i]['y'],points[i]['scale'], points[i]['rotation']);
            }
            if(checkCollision(points[i])){
                pop.play();
                let tempImg=pointImg[points[i]['type']].cloneNode(true);
                
                tempImg.className="collectionIMG";
                tempImg.id="collectionIMG"+score;
                collection.push(tempImg);
                document.getElementById("collection").appendChild(tempImg);
                points.splice(i,1);
                pointCount-=1;
                score+=1;
            }
        }
    }

    function spawnPoints(){
        let objectSpeedX=-1*(Math.floor(Math.random() * (objectSpeedXMax - objectSpeedXMin + 1) ) + objectSpeedXMin);
        let objectSpeedY=Math.round(Math.random()) * 2 - 1*(Math.floor(Math.random() * (objectSpeedXMax - objectSpeedXMin + 1) ) + objectSpeedXMin);
        let objectRotation=Math.floor(Math.random() * (objectRotationMax - objectRotationMin + 1) ) + objectRotationMin;
        let pointScale=Math.floor(Math.random() * (2 - 1 + 1) ) + 1;
        let pointX=maxX;
        let pointY=Math.floor(Math.random() * (maxY - 0 + 1) ) + 0;
        let pointType=Math.floor(Math.random() * (8 - 0 + 1) ) + 0;
        points.push({
            x:pointX,
            y:pointY,
            speedX:objectSpeedX,
            speedY:objectSpeedY,
            rotation:0,
            rotationSpeed:objectRotation,
            scale:pointScale,
            width:pointImg[pointType].width*pointScale,
            height:pointImg[pointType].height*pointScale,
            type:pointType
        });
    }


    function checkCollision(a){
        let r1x = shipPosX;                          // left edge
        let r1y = shipPosY;                          // top edge
        let r1w = ship.width*shipScale; // right edge
        let r1h = ship.height*shipScale; // bottom edge
        let r2x = a['x'];                          // left edge
        let r2y = a['y'];                          // top edge
        let r2w = a['width']; // right edge
        let r2h = a['height']; // bottom edge
        return (r1x + r1w >= r2x &&    // r1 right edge past r2 left
        r1x <= r2x + r2w &&    // r1 left edge past r2 right
        r1y + r1h >= r2y &&    // r1 top edge past r2 bottom
        r1y <= r2y + r2h)     // r1 bottom edge past r2 top

    }


    // function checkCollision(a, b){
    //     var aRect = a.getBoundingClientRect();
    //     var bRect = b.getBoundingClientRect();
    
    //     return !(
    //         ((aRect.top + aRect.height) < (bRect.top)) ||
    //         (aRect.top > (bRect.top + bRect.height)) ||
    //         ((aRect.left + aRect.width) < bRect.left) ||
    //         (aRect.left > (bRect.left + bRect.width))
    //     );
    // }

    function badCollide(){
        health-=1;
                document.getElementsByClassName("healthIcon")[0].remove();
        if(health==0){
            gameOver();
            rightPressed=false;
            leftPressed=false;
            upPressed=false;
            downPressed=false;
            document.onkeydown=null;
            document.onkeyup=null;
        
        }if(health>0){

        rightPressed=false;
        leftPressed=false;
        upPressed=false;
        downPressed=false;
        document.onkeydown=null;
        document.onkeyup=null;
        invulnerable=true;
            ship.src="img/shield.png";
        setTimeout(function(){
            document.onkeydown = keyDownHandler;
            document.onkeyup = keyUpHandler;
            var temp=setInterval(function(){
            blink=!blink;
            if(blink){
                ship.src="img/ship.gif";
            }else{
                ship.src="img/shield.png";
            }
            },100);
                    setTimeout(function(){
                                invulnerable=false;
                                clearInterval(temp);
                                ship.src="img/ship.gif";
                                recharge.play();
                            },1500);

        },500);
        
    }}
    function drawImage(image, x, y, scale, rotation){
        ctx.save();
        rotation=rotation*Math.PI/180;
        ctx.setTransform(scale, 0, 0, scale, x, y); // sets scale and origin
        ctx.rotate(rotation);
        ctx.drawImage(image, -image.width / 2, -image.height / 2);
        ctx.restore();
    } 
    
    function gameOver(){
        game_end.play();
        invulnerable=true;
        shipExplodes();
        

        setTimeout(function(){
        clearInterval(main);
        clearInterval(secondary);
        clearInterval(pointSpawn)
        
        let gameOver=document.createElement("div");
        gameOver.className="gameOver";
        let gameOverText=document.createElement("h1");
        gameOverText.innerHTML="GAME OVER";
        gameOver.appendChild(gameOverText);
        let result=document.createElement("h2");
        result.innerHTML="Score: "+score;
        gameOver.appendChild(result);
        gameOver.style.zIndex=100;
        document.body.appendChild(gameOver);
        let collecton = document.createElement("div");
        collection.id="collection2";
        for(let i=0; i<collection.length; i++){
            collecton.appendChild(collection[i]);
        }
        gameOver.appendChild(collecton);
        let restart=document.createElement("button");
        restart.className="button-59";
        restart.innerHTML="RESTART";
        restart.onclick=function(){
            location.reload();
        }
        gameOver.appendChild(restart);
        
    },2000);
    }
    function shipExplodes(){
        let explodeCount=0;
        let explode=setInterval(function(){
            ship=shipExplodeAnimation[explodeCount];
            explodeCount+=1;
            if(explodeCount>17){
                clearInterval(explode);
            }
        },100);
    }
}