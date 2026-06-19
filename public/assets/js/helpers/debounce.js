/**
 * Debounce Helper
 * Delays function execution until specified milliseconds have passed without further calls
 * Useful for search, resize, scroll, etc.
 */

const DebounceHelper = {
    /**
     * Debounce a function
     * @param {function} func - Function to debounce
     * @param {number} delay - Delay in milliseconds (default: 500)
     * @returns {function} Debounced function
     */
    debounce: function(func, delay = 500) {
        let timeoutId;

        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    },

    /**
     * Debounce with immediate execution (fires at start, then waits for trailing)
     * @param {function} func - Function to debounce
     * @param {number} delay - Delay in milliseconds (default: 500)
     * @returns {function} Debounced function with immediate option
     */
    debounceImmediate: function(func, delay = 500) {
        let timeoutId;

        return function(...args) {
            const callNow = !timeoutId;
            clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {
                timeoutId = null;
            }, delay);

            if (callNow) {
                func.apply(this, args);
            }
        };
    },

    /**
     * Throttle a function (execute at most once per delay interval)
     * @param {function} func - Function to throttle
     * @param {number} delay - Delay in milliseconds (default: 500)
     * @returns {function} Throttled function
     */
    throttle: function(func, delay = 500) {
        let lastCallTime = 0;

        return function(...args) {
            const now = Date.now();

            if (now - lastCallTime >= delay) {
                lastCallTime = now;
                func.apply(this, args);
            }
        };
    }
};
