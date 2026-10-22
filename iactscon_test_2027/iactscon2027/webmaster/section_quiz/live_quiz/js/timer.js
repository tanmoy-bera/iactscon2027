class CountdownTimer {
  /**
   * Creates a new CountdownTimer instance.
   * @param {string} selector - CSS selector of the element where the timer will be rendered.
   * @param {object} [options] - Options object to configure the timer.
   * @param {number} [options.duration=60] - Total duration of the countdown in seconds.
   * @param {string} [options.color="#c39fe0"] - Color of the progress circle.
   * @param {number} [options.size=200] - Diameter of the timer in pixels.
   * @param {string} [options.backgroundColor="#eee"] - Background color of the timer circle.
   * @param {number} [options.strokeWidth=4] - Stroke width of the progress and background circles in pixels.
   * @param {number} [options.fontSize=0.2] - Font size of the text relative to the timer size.
   * @param {function} [options.onComplete=null] - Function to be called when the countdown reaches zero.
   * @param {function} [options.onTick=null] - Function to be called every second during the countdown. Receives the remaining time in seconds.
   * @throws {Error} If the provided selector is undefined.
   * @throws {Error} If the container element is not found using the selector.
   */
  constructor(selector, options) {
    if (!selector) {
      throw new Error("Selector is undefined");
    }

    this.defaults = {
      duration: 60,
      color: "#c39fe0",
      size: 200,
      backgroundColor: "#eee",
      strokeWidth: 4,
      fontSize: 0.2,
      onComplete: null,
      onTick: null
    };

    this.settings = Object.assign({}, this.defaults, options);
    this.container = document.querySelector(selector);
    this.timeLeft = this.settings.duration;
    this.interval = null;

    if (!this.container) {
      throw new Error("Container element not found");
    }

    // Initialize the timer
    this.init();
  }

  /**
   * Initializes the timer by creating the structure, setting up styles, and starting the countdown.
   */
  init() {
    this.createStructure();
    this.setupStyles();
    this.startCountdown();
  }

  /**
   * Creates the HTML structure of the timer within the container.
   */
  createStructure() {
    const { size, strokeWidth } = this.settings;
    const r = size / 2 - strokeWidth; // Calculate the circle radius
    const half = size / 2; // Calculate the center point

    this.container.innerHTML = `
        <div class="countdown__container">
          <svg class="countdown__circle" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
            <circle class="countdown__background" cx="${half}" cy="${half}" r="${r}" stroke-width="${strokeWidth}" />
            <circle class="countdown__progress" cx="${half}" cy="${half}" r="${r}" stroke-width="${strokeWidth}" transform="rotate(-90 ${half} ${half})" />
          </svg>
          <div class="countdown__text">${this.formatTime(this.timeLeft)}</div>
        </div>
      `;

    this.progressCircle = this.container.querySelector(".countdown__progress");
    this.backgroundCircle = this.container.querySelector(
      ".countdown__background"
    );
    this.textElement = this.container.querySelector(".countdown__text");
  }

  /**
   * Sets up the inline CSS styles for the container and timer elements.
   */
  setupStyles() {
    const { size, color, backgroundColor, fontSize } = this.settings;
    const r = size / 2 - this.settings.strokeWidth;
    const c = 2 * Math.PI * r; // Calculate the circle circumference

    this.container.style.setProperty("--size", `${size}px`);
    this.container.style.setProperty("--color", color);
    this.container.style.setProperty("--background-color", backgroundColor);
    this.container.style.setProperty("--font-size", fontSize);
    this.container.style.setProperty("--circumference", c);
  }

  /**
   * Formats the time in seconds to MM:SS format.
   * @param {number} time - Time in seconds.
   * @returns {string} Formatted time as MM:SS.
   */
  formatTime(time) {
    const minutes = Math.floor(time / 60);
    const seconds = time % 60;
    return `${minutes
      .toString()
      .padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;
  }

  /**
   * Updates the countdown display every second.
   */
  updateCountdown() {
    const r = this.settings.size / 2 - this.settings.strokeWidth;
    const c = 2 * Math.PI * r;

    // Calculate the progress of the countdown (from 0 to 1)
    const progress = this.timeLeft / this.settings.duration;
    // Calculate the 'stroke-dashoffset' value to animate the progress circle
    const dashoffset = c * (1 - progress);

    this.progressCircle.style.strokeDashoffset = dashoffset;
    this.textElement.textContent = this.formatTime(this.timeLeft);

    if (this.settings.onTick) {
      this.settings.onTick(this.timeLeft);
    }

    if (this.timeLeft > 0) {
      this.timeLeft--;
    } else {
      clearInterval(this.interval);

      if (this.settings.onComplete) {
        this.settings.onComplete();
      }
    }
  }

  /**
   * Starts the countdown.
   */
  startCountdown() {
    // Clear any existing interval to prevent multiple countdowns
    if (this.interval) {
      clearInterval(this.interval);
    }
    // Add a smooth transition for the circle animation
    this.progressCircle.style.transition = "stroke-dashoffset 0.95s linear";
    // Set an interval to update the countdown every second
    this.interval = setInterval(() => this.updateCountdown(), 1000);
  }

  /**
   * Pauses the countdown.
   */
  pause() {
    clearInterval(this.interval);
  }

  /**
   * Resumes a paused countdown.
   */
  resume() {
    this.startCountdown();
  }

  /**
   * Resets the countdown to the initial duration and starts it again.
   */
  reset() {
    this.timeLeft = this.settings.duration;
    this.updateCountdown();
    this.startCountdown();
  }

  changeTime(time) {
    this.settings.duration = time;
    this.reset();
  }

  /**
   * Stops the countdown and removes the generated HTML structure from the container.
   */
  destroy() {
    clearInterval(this.interval);
    this.container.innerHTML = "";
  }
}

// Example usage
const timer = new CountdownTimer(".timer-container", {
  duration: 20,
  color: "#FE2F34",
  size: 80,
  backgroundColor: "#f0f0f0",
  strokeWidth: 2,
  fontSize: 0.5,
  onComplete: () => {
    console.log("Countdown complete!");
  },
  onTick: (timeLeft) => {
    // console.log("Time left:", timeLeft);
  }
});

document.getElementById("set-time").addEventListener("click", () => {
  const newTime = parseInt(document.getElementById("time-input").value, 10);
  timer.changeTime(newTime);
});

document.getElementById("pause").addEventListener("click", () => timer.pause());
document
  .getElementById("resume")
  .addEventListener("click", () => timer.resume());
document.getElementById("reset").addEventListener("click", () => timer.reset());