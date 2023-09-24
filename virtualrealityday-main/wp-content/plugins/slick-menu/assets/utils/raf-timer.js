// RAF Timer
;(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame'] 
                                   || window[vendors[x]+'CancelRequestAnimationFrame'];
    }
 
    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); }, 
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
 
    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());

;(function(ns) {
	
	'use strict';
	
    var id = 0,
        timers = [],
        L = 0,
        now = Date.now,
        raf = window.requestAnimationFrame;

    function globalTimer() {
        var l = L,
            n = now(),
            t;
        l > 0 ? raf(globalTimer) : (id = 0);
        while (l--) {
            t = timers[l];
            n - t[3] < t[2] || (t[1](), timers.splice(l, 1), L = timers.length)
        }
    }
    ns.setTimeout = function(cb, delay) {
        var i = id++,
            n = now();
        L || (raf(globalTimer));
        L = timers.push([i, cb, delay, n, n]);
        return i
    }
    ns.clearTimeout = function(i) {
        var l = L;
        while (l--) {
            timers[l][0] != i || (timers.splice(l, 1))
        }
    }
}(this.Timers = this.Timers || {}));