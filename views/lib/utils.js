/* 
 * Utilities functions
 * CAService
 */

var customClock;

/**
 * Round UP by number of decimals
 */
function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);

    return result;
}

/**
 * Format seconds to array
 */
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

/**
 * Format seconds to hh:mm
 */
function formatTime(seconds)
{
    var time;

    // avoid dummi values
    if(seconds != 'n/a'){
        var hours = Math.floor(seconds / 3600);
        var mins = Math.floor((seconds - (hours*3600)) / 60);

        if(hours < 10)
            hours = "0"+hours;
        if(mins < 10)
            mins = "0"+mins;

        time = hours+":"+mins;
    }
    else
        time = 'n/a';

    return time;
}

/**
 * Get time clock by custom init params
 * #progress_clock ID element needed!
 * 
 * Modified to show total hours using Array values instead of Date.
 */
customClock = (function() {

  var timeDiff;
  var timeout;
  var timeHours = 0;
  var timeArray = new Array;

  function addZ(n) {
    return (n < 10? '0' : '') + n;
  }

  function formatTime(d) {
    return addZ(d.getHours()) + ':' +
           addZ(d.getMinutes()) + ':' +
           addZ(d.getSeconds());
  }
  
  function formatTimeFromArray(d) {
    return d[0] + ':' +
           addZ(d[1]) + ':' +
           addZ(d[2]);
  }

  return function (s) {

    var now = new Date();
    var then;

    // Set lag to just after next full second
    var lag = 1015 - now.getMilliseconds();

    // Get the time difference if first run
    if (s) {
      s = s.split(':');
      then = new Date(now);
      then.setHours(+s[0], +s[1], +s[2], 0);
      timeDiff = now - then;
      timeHours = s[0];
    }

    now = new Date(now - timeDiff);
    timeArray[0] = timeHours;
    timeArray[1] = now.getMinutes();
    timeArray[2] = now.getSeconds();

//    $('#progress_clock').val(formatTime(now)); 
    $('#progress_clock').val(formatTimeFromArray(timeArray)); 
    timeout = setTimeout(customClock, lag);
  }
}());