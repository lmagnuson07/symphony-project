$(document).ready(function() {
    /**
     * All value types are converted to rems.
     * If a property allows for px value type, it is simply for convenience.
     *
     * # **args**:
     * ___
     * **responsive**: *an object with properties for different screens sizes.
     * Each property is an object which contains a slides property (for num of slides at that breakpoint) and showDots/showArrows properties to toggle the dots/arrows.
     * ShowArrows and showDots default to true. Slides parameter must be set.
     * Works the same as min-width media queries.*
     * ___
     * **gap**: *Object that sets the amount of space between each slide.
     * Object has type and value properties. Value defaults to px (can be px, rem).
     * Gap can be an object or number (as string or float).
     * If gap is an object, value must be set.*
     * ___
     *
     * @param args
     * @returns {{msg: string, error: boolean}}
     */
    $.fn.carouselInit = function(args) {
        //-------------- Set up defaults --------------//
        let responsive = null;
        let gap = null;
        let remConversion = 16;

        try {
            // Set up the defaults for the responsive object.
           responsive = args.responsive ? args.responsive : responsive;
            if (responsive === null) {
                responsive = {
                    1200: {
                        slides: 3,
                        showArrows: true,
                        showDots: true
                    },
                    520: {
                        slides: 2,
                        showArrows: true,
                        showDots: true
                    },
                    375: {
                        slides:1,
                        showArrows: true,
                        showDots: true
                    },
                }
            } else if (typeof responsive === "object") {
                Object.values(responsive).forEach(size => {
                    if (size.slides === undefined || size.slides === null) {
                        throw new Error("Invalid configuration for responsive property");
                    }
                    if (size.showArrows === undefined || size.showArrows === null) {
                        size.showArrows = true;
                    }
                    if (size.showDots === undefined || size.showDots === null) {
                        size.showDots = true;
                    }
                });
            } else {
                return {
                    error: true,
                    msg: "Invalid configuration for responsive property"
                }
            }
            // Set up the defaults for the gap object.
            gap = args.gap ? args.gap : null;
            if (gap !== null) {
                // Check if gap is an object, or number
                if (
                    typeof gap !== "object"
                ) {
                    if (!$.isNumeric(gap)) {
                        return {
                            error: true,
                            msg: "Invalid configuration for gap property"
                        }
                    } else {
                        let temp = gap;
                        gap = {
                            value: temp
                        };
                    }
                }
                if (
                    typeof gap !== "object"
                    ||
                    (gap.value === undefined || gap.value === null)
                ) {
                    return {
                        error: true,
                        msg: "Invalid configuration for gap property"
                    }
                }
                // Check the type property and set its defaults
                if (gap.type === undefined || gap.type === null) {
                    gap.type = "px";
                } else {
                    if (
                        gap.type !== 'rem' && gap.type !== 'px'
                    ) {
                        return {
                            error: true,
                            msg: "Invalid configuration for gap property"
                        }
                    }
                }
            }
            // Set up the defaults for the primitive properties.


        } catch (error) {
            return {
                error: true,
                msg: error.message
            }
        }

        //-------------- Set size of slides based off responsive object --------------//
        const bodyWidth = $('body').width();
        const panelWidth = $('.panel').width();

        let currentSize = null;
        for (const key in responsive) {
            if (currentSize === null) {
                if (bodyWidth <= parseInt(key)) {
                    currentSize = key;
                    break;
                }
            } else {
                if (bodyWidth <= parseInt(currentSize)) {
                    break;
                }
            }
            currentSize = key;
        }

        if (gap !== null) {
            if (gap.type === "px") {
                gap.value = gap.value / remConversion;
            }
            $('.slide').css('margin-right', gap.value + 'rem');
            $('.slide[data-active="true"]:last').css('margin-right', 0);
        } else {
            gap = {
                value: 0
            };
        }
        let widthPercentage = (1 / responsive[currentSize].slides);
        let width = ((panelWidth * widthPercentage) / remConversion) - (gap.value / responsive[currentSize].slides * (responsive[currentSize].slides - 1));
        $('.slide').css('width', width + 'rem');

        console.log(panelWidth);
        console.log(bodyWidth);
        console.log(currentSize);
        console.log(responsive[currentSize])
        console.log(gap)

        return {
            error: false,
            msg: "Carousel initialized"
        }
    }

    let response = $.fn.carouselInit({
        responsive: {
            1300: {
                slides: 4,
                showArrows: true,
                showDots: true
            },
            1200: {
                slides: 3,
                showArrows: true,
                showDots: true
            },
            700: {
                slides: 2,
                showArrows: true,
                showDots: true
            },
            350: {
                slides: 1,
                showArrows: true,
                showDots: true
            }
        },
        gap: 16
        // gap: {
        //     value: 16,
        //     type: "px"
        // }
    });
    console.log(response)

    $('.carousel').click(function() {
        $('.carousel').addClass('clicked');
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.carousel').length) {
            $('.carousel').removeClass('clicked');
        }
    });

    /* Disable certain events */
    $(document).on("mousedown", ".carousel", function (e) {
        if (e.which == 2) {
            // potentially enable scroll ability. Disables the
            e.preventDefault();
            // alert("middle button");
            return false;
        }
    });

    $(".carousel").on("keydown", function (e) {
        // Tab
        if (e.which == 9) {
            const target = $(e.target);
            if (target.is("a")) {
                console.log(target);
                // Make tab move by rows, or one at a time

                e.preventDefault();
                return false;
            }
        }

    });

    $(document).keydown(function(e) {
        // left arrow key
        if ($('.carousel').hasClass('clicked')) {

        } else {
            if (e.which == 37) {
                e.preventDefault();
            }
            // right arrow key
            if (e.which == 39) {
                e.preventDefault();
            }
        }
    });

});

