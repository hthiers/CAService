/* 
 * Utilities functions
 * CAService
 */
function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);

    return result;
}

function secondsToTime(secs)
{
    var hours = Math.floor(secs / (60 * 60));
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

    if(hours<10)
        hours = '0'+hours;
    if(minutes<10)
        minutes = '0'+minutes;
    if(seconds<10)
        seconds = '0'+seconds;

    var obj = {
        "h": hours,
        "m": minutes,
        "s": seconds
    };

    return obj;
}