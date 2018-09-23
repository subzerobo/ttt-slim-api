var finished = false;
var botLevel = 'dumb';
var botMode = 'auto';
var next_move = [];

function getCurrentStateJSON() {
    return localStorage.getItem("state");
}

function setCurrentStateJSON(state){
    localStorage.setItem("state", JSON.stringify( state ));
}

function displayState(status) {
    var stateJSON = getCurrentStateJSON();
    var state = JSON.parse(stateJSON);
    for (let i = 0; i < state.length; i++) {
        for (let j = 0; j < state[i].length; j++) {
            var tileID = "#tile-" + ((i*3)+j+1)
            if (state[i][j]['type'] >0) {
                $(tileID).addClass('marked');
                $(tileID).addClass(state[i][j]['type']==1? "x-mark": "o-mark"); 
            }
        }
    }

    if (typeof status !='undefined' && (status.state == 'win' || status.state == "draw")) {
        displayWinner(status.state, status.winner);
    }
}

function displayWinner(winner, mark) {
    finished = true;
    if(mark == 1) {
        status = " You Win!";
        className = "win";
    }
    else if(mark == 2) {
        status = " You Lost!";
        className = "lost";
    } else {
        status = " It`s a Draw!";
        className = "draw";
    }
    $("#game-result").show();
    $("#game-result").html("Game Over."+status + " <span class='reset' onclick='init();'>Reset to play again</span>.");
    $("#game-result").addClass(className);
}

function resetTicTacToe() {
    $("#game-result").hide();
    $("#game-result").html("");
    $("#game-result").removeClass("win");
    $("#game-result").removeClass("lost");
    $("#game-result").removeClass("draw");
    $(".tile").removeClass("marked");
    $(".tile").removeClass("o-mark");
    $(".tile").removeClass("x-mark");
    finished = false;
}

function init() {
    resetTicTacToe();
    $.ajax({
        url: '/init',
        dataType: 'json',
        type: 'get',
        contentType: 'application/json',
        success: function( data, textStatus, jQxhr ){
            setCurrentStateJSON(data.layout);
            displayState();
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });
}
// --------------------------------------------------------------------------------- //
//                          Auto Choose Bot Move on Backend
// --------------------------------------------------------------------------------- //

function makeMove(row, col, bot) {
    // Make Move With API
    var currentState = getCurrentStateJSON();
    var stateObject = JSON.parse(currentState);
    $.ajax({
        url: '/move',
        dataType: 'json',
        type: 'post',
        contentType: 'application/json',
        data: JSON.stringify({ layout: stateObject, botName: bot, position : [row,col] }),
        success: function( data, textStatus, jQxhr ){
            setCurrentStateJSON(data.layout);
            displayState(data.status);
            
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });
}

// --------------------------------------------------------------------------------- //
// Manual Choose Bot Move from Frontend and Make move from UI using the same method
// --------------------------------------------------------------------------------- //

function makeManualmove(row, col, player, bot){
    var currentState = getCurrentStateJSON();
    var stateObject = JSON.parse(currentState);
    $.ajax({
        url: '/move_manual',
        dataType: 'json',
        type: 'post',
        contentType: 'application/json',
        data: JSON.stringify({ layout: stateObject, player: player, position : [row,col] }),
        success: function( data, textStatus, jQxhr ){
            setCurrentStateJSON(data.layout);
            displayState(data.status);
            if (player == 1 && data.status.state == "ongoing" ){
                askBotMove(botLevel);
            }
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });
}

function askBotMove(bot) {
    var currentState = getCurrentStateJSON();
    var stateObject = JSON.parse(currentState);
    $.ajax({
        url: '/ask',
        dataType: 'json',
        type: 'post',
        contentType: 'application/json',
        data: JSON.stringify({ layout: stateObject, botName: bot }),
        success: function( data, textStatus, jQxhr ){
            setCurrentStateJSON(data.layout);
            next_move = data.next_move;
            makeManualmove(parseInt(next_move[0]), parseInt(next_move[1]),2);
        },
        error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });
}


$(document).ready(function() {

    init();

    $("#init").on('click', function (e) {
        e.preventDefault();
        init();
        finished = false;
    });

    $(".tile").on('click', function() {
        if(!finished) {
            var squareClass = $(this).attr("class");
            if(squareClass.indexOf("marked")<0) {
                var rc = $(this).attr("data-position").split(',');
                if (botMode == "auto") {
                    makeMove(parseInt(rc[0]), parseInt(rc[1]), botLevel);
                }else{
                    makeManualmove(parseInt(rc[0]), parseInt(rc[1]),1);
                }
            }
        }
    });

    $(".btnBot").on('click', function(e) {
        e.preventDefault();
        botLevel = $(this).attr("data-bot-value").toLowerCase();
        $('.bot_level').html($(this).attr("data-bot-value"));
    });

    $(".btnBotMode").on('click', function(e) {
        e.preventDefault();
        botMode = $(this).attr("data-bot-value").toLowerCase();
        $('.bot_mode').html($(this).attr("data-bot-value"));
    });

});