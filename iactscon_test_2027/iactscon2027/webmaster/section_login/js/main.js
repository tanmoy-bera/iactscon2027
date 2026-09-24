// banner slider
// Importing utility function for preloading images
const preloadImages = (selector = 'img') => {
    return new Promise((resolve) => {
        // The imagesLoaded library is used to ensure all images (including backgrounds) are fully loaded.
        imagesLoaded(document.querySelectorAll(selector), {background: true}, resolve);
    });
};

// Exporting utility functions for use in other modules.
export {
    preloadImages
};

// Variable to store the Lenis smooth scrolling object
let lenis;

// Selecting DOM elements

const contentElements = [...document.querySelectorAll('.content--sticky')];
const totalContentElements = contentElements.length;

// Initializes Lenis for smooth scrolling with specific properties
const initSmoothScrolling = () => {
	// Instantiate the Lenis object with specified properties
	lenis = new Lenis({
		lerp: 0.2, // Lower values create a smoother scroll effect
		smoothWheel: true // Enables smooth scrolling for mouse wheel events
	});

	// Update ScrollTrigger each time the user scrolls
	lenis.on('scroll', () => ScrollTrigger.update());

	// Define a function to run at each animation frame
	const scrollFn = (time) => {
		lenis.raf(time); // Run Lenis' requestAnimationFrame method
		requestAnimationFrame(scrollFn); // Recursively call scrollFn on each frame
	};
	// Start the animation frame loop
	requestAnimationFrame(scrollFn);
};

// Function to handle scroll-triggered animations
const scroll = () => {

    contentElements.forEach((el, position) => {
        
        const isLast = position === totalContentElements-1;

        gsap.timeline({
            scrollTrigger: {
                trigger: el,
                start: 'top top',
                end: '+=100%',
                scrub: true
            }
        })
        .to(el, {
            ease: 'none',
            startAt: {filter: 'brightness(100%) contrast(100%)'},
            filter: isLast ? 'none' : 'brightness(60%) contrast(135%)',
            yPercent: isLast ? 0 : -15
        }, 0)
        // Animate the content inner image
        .to(el.querySelector('.content__img'), {
            ease: 'power1.in',
            yPercent: -40,
            rotation: -20
        }, 0);

    });

};

// Initialization function
const init = () => {
    initSmoothScrolling(); // Initialize Lenis for smooth scrolling
    scroll(); // Apply scroll-triggered animations
};

preloadImages('.content__img').then(() => {
    // Once images are preloaded, remove the 'loading' indicator/class from the body
    document.body.classList.remove('loading');
    init();
});
// banner slider

<script id="rendered-js" type="module">
    import gsap from 'https://cdn.skypack.dev/gsap@3.12.0';

    const UPDATE = ({
        x,
        y
    }) => {
        gsap.set(document.documentElement, {
            '--x': gsap.utils.mapRange(0, window.innerWidth, -1, 1, x),
            '--y': gsap.utils.mapRange(0, window.innerHeight, -1, 1, y)
        });

    };

    window.addEventListener('mousemove', UPDATE);


    // Want to handle device orientation too

    const handleOrientation = ({
        beta,
        gamma
    }) => {
        const isLandscape = window.matchMedia('(orientation: landscape)').matches;
        gsap.set(document.documentElement, {
            '--x': gsap.utils.clamp(-1, 1, isLandscape ? gsap.utils.mapRange(-45, 45, -1, 1, beta) : gsap.utils.mapRange(-45, 45, -1, 1, gamma)),
            '--y': gsap.utils.clamp(-1, 1, isLandscape ? gsap.utils.mapRange(20, 70, 1, -1, Math.abs(gamma)) : gsap.utils.mapRange(20, 70, 1, -1, beta))
        });

    };

    const START = () => {
        var _DeviceOrientationEve;
        // if (BUTTON) BUTTON.remove();
        if ((_DeviceOrientationEve =
                DeviceOrientationEvent) !== null && _DeviceOrientationEve !== void 0 && _DeviceOrientationEve.requestPermission) {
            Promise.all([
                DeviceOrientationEvent.requestPermission()
            ]).
            then(results => {
                if (results.every(result => result === "granted")) {
                    window.addEventListener("deviceorientation", handleOrientation);
                }
            });
        } else {
            window.addEventListener("deviceorientation", handleOrientation);
        }
    };
    document.body.addEventListener('click', START, {
        once: true
    });
    //# sourceURL=pen.js
