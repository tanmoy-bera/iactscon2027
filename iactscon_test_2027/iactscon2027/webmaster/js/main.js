

// gsap.to(".message_box", {
//     x: -20,
//     opacity: 0,
//     duration: 0.5,
//     delay: 0.7,
//     scrollTrigger: {
//         trigger: ".message_wrap ",
//         scroller: "body",
//         markers: true,
//         start: "top 0%",
//           end: "bottom -100%",
//     }
// })

let messagebox = gsap.timeline({
    scrollTrigger: {
        trigger: ".msg_wrap",
        start: "top 40%",
        end: "bottom -40%",
        scrub: 2,

    }
});
messagebox.fromTo(".msg_img",
    {
        transform: "rotateZ(-3deg) translateY(27px)",
        duration: 1.5
    },

    {
        transform: "rotateZ(3deg) translateY(-27px)",
        duration: 1.5
    }
)


let cityimg = gsap.timeline({
    scrollTrigger: {
        trigger: ".city_wrap ",
        start: "top 40%",
        end: "bottom -40%",
        scrub: 2,
    }
});
cityimg.fromTo(".city_img_content",
    {
        transform: "translateX(27px) rotateZ(-6deg)",
        duration: 0.5
    },
    {
        transform: "translateX(-27px) rotateZ(-6deg)",
        duration: 0.5
    }

)

let citytext = gsap.timeline({
    scrollTrigger: {
        trigger: ".city_wrap ",
        start: "top 40%",
        end: "bottom -40%",
        scrub: 2,

    }
});
citytext.from(".city_content h3",
    {
        top: "20px",
        duration: .5
    },
    {
        top: "-20px",
        duration: .5
    }

)


const splitTypeElements = document.querySelectorAll(".heading h2");

splitTypeElements.forEach((msgtext) => {
    let mySplitText = SplitText.create(msgtext, {
        type: "chars",
        charsClass: "char",
    });
    gsap.fromTo(mySplitText.chars, {
        position: 'relative',
        display: 'inline-block',
        opacity: 0.2,
        x: -5,
    },
        {
            opacity: 1,
            x: 0,
            stagger: 0.2,
            scrollTrigger: {
                trigger: msgtext,
                toggleActions: "play pause reverse pause",
                start: "top 90%",
                end: "top 40%",
                scrub: 0.7,
            }
        }
    );

})
const splitTypeElement = document.querySelectorAll(".heading h3");

splitTypeElement.forEach((msgtextt) => {
    let mySplitTextt = SplitText.create(msgtextt, {
        type: "chars",
        charsClass: "char",
    });
    gsap.fromTo(mySplitTextt.chars, {
        position: 'relative',
        display: 'inline-block',
        opacity: 0.2,
        x: -5,
    },
        {
            opacity: 1,
            x: 0,
            stagger: 0.2,
            scrollTrigger: {
                trigger: msgtextt,
                toggleActions: "play pause reverse pause",
                start: "top 90%",
                end: "top 40%",
                scrub: 0.7,
            }
        }
    );

})


gsap.fromTo(".msg_box p", {
    position: 'relative',
    display: 'inline-block',
    opacity: 0.2,
    y: 5,
},
    {
        opacity: 1,
        y: 0,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".msg_box",
            toggleActions: "play pause reverse pause",
            start: "top 90%",
            end: "top 40%",
            scrub: 0.7,
        }
    }
);



gsap.to(".wheel",
    {
        rotate: "360deg",
        duration: 9,
        repeat: -1,
        ease: 'linear',
    }
)
// gsap.to(".component",
//     {
//         y: "40%",
//         duration: 1,
//         scrollTrigger: {
//             trigger: "banner_wrap",
//             start: "top 0%",
//             end: "bottom -50%",
//             scrub: 0.7,
//         }
//     }
// )



// window.addEventListener("load", () => {
//     gsap
//         .timeline({
//             scrollTrigger: {
//                 trigger: ".wrapper",
//                 start: "top top",
//                 end: "+=150%",
//                 pin: true,
//                 scrub: true,
//             }
//         })

//         .to(".taxi", {
//             transform: "translate(-50%, -132px) scale(0.5)",
//             bottom: 0,
//             z: 350,
//             transformOrigin: "center bottom",
//             ease: "power1.inOut"
//         })

// });


let msgimg = gsap.timeline({
    scrollTrigger: {
        trigger: ".msg_wrap",
        start: "top 00%",
        end: "bottom -200%",
        scrub: 2,
    }
});
msgimg.fromTo(".msg_mid img",
    {
        x: "0px",
        duration: 1.5
    },

    {
        x: "100px",
        duration: 1.5
    }
)
const init = () => {
    const marquee = document.querySelectorAll('.reach_btm')
    if (!marquee) return

    marquee.forEach(item => {
        const marqueeInner = item.querySelector('.reach_btm_inner')
        const marqueeContentClone = marqueeInner.cloneNode(true)
        item.append(marqueeContentClone)

        // Marquee animation
        const marqueeContentAll = item.querySelectorAll('.reach_btm_inner')
        marqueeContentAll.forEach(element => {
            gsap.to(element, {
                x: "-101%",
                repeat: -1,
                duration: 80,
                ease: 'linear'
            })
        })
    })
}
document.addEventListener('DOMContentLoaded', init)

gsap.from(".committee_box", {
    y: 20,
    opacity: 0,
    duration: 0.5,
    delay: 0.7,
    stagger: 0.2,
    scrollTrigger: {
        trigger: ".committee_wrap",
        scroller: "body",
        markers: false,
        start: "top 100%",
        end: "bottom 100%",
        scrub: 2,
    }
})

gsap.to(".regi_box_right img",
    {
        x: "40px",
        duration: 1,
        scrollTrigger: {
            trigger: "banner_wrap",
            start: "top 0%",
            end: "bottom -50%",
            scrub: 0.7,
        }
    }
)

gsap.from(".banner_text",
    {
        x: "-50px",
        opacity: 0,
        stagger: 0.2,
        duration: 1.5
    }
)
gsap.from(".banner_btm_img",
    {
        y: "50px",
        opacity: 0,
        duration: 1.5
    }
)
gsap.from(".banner_right img",
    {
        x: "50px",
        opacity: 0,
        duration: 1.5
    }
)
gsap.from(".banner_top_img",
    {
        y: "-50px",
        opacity: 0,
        duration: 1.5
    }
)