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
 * Format seconds to hh:mm:ss
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
    return addZ(d[0]) + ':' +
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