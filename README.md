# Tic-Tac-Toe PHP API

## How To Install  

 1. Download or clone the project from repository using
 `git clone git@github.com:subzerobo/ttt-slim-api.git`
 2. Install the composer if you dont have it already ;-)  
 3. Run `composer install` in the root directory of project 
 4. Set up virtual host on Apache or Nginx, make sure what DocumentRoot relate on `public` directory
 5. Alternatively you can just run commnad `composer start` to start project using your local machine PHP: Built-in web server for this url : http://localhost:2000   

## How to Use
Both API and the web interface are done in the same project so after you run the project you can access the web interface from : 

***Web Interface URL :*** 
just type `http://localhost:2000/` in your browser address bar.

***Game Modes :***
 
This game has 2 bot AI level Dumb and Genius , Dumb make random moves and the Genius bot is unbeatable, so you can choose using the bot Level Menu on top,and as well you can select the mode backend do the Bot Moves

***Manual :*** in this mode UI will call the `move_manual` so the bot will not take it's move ! so the UI will initiate `ask` method to get the proper bot move and then through the same interface `move_manual` makes the Bot move

***Auto (Default) :*** in this mode UI will call the `move` method in API and the bot will do it's move though the API automatically so after that return the new state and player will select his/her next move !

****

***API Interfaces :***
If you want to test the API Interface you can use any tool for making HTTP queries: postman, curl, etc
there is an PostMan Collection + Environment file in the `postman` folder in the root of project which can simulate the game process you may find it useful for testing procedures.

import both of the in your postman application adn choose TTT-Environment and you are good to go for using postman collection.
[TTT-API Postman Online Document](https://documenter.getpostman.com/view/5331514/RWaLx8CP)

## Using the Unit Tests and Functional Tests
the project contains both unit tests and functional tests, use `composer test` command in the root directory to run tests. 


## Third-Party Codes

I`ve used the CSS styles form the board design from the *phppot.com* tutorial in [How to Code Tic Tac Toe Game in jQuery ](https://phppot.com/jquery/how-to-code-tic-tac-toe-game-in-jquery/) vincy@phppot.com