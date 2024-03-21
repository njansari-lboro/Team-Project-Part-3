$(document).on("click", ".handle", function () {
    handleClicked($(this));
});

function reinitializeSliderState() {
    $('.slider').css("--slider-index", 0);
    updateHandleVisibility();
    $(".progress-bar").each(updateProgressBar);
}
//searching functionality
$('[name="search"]').on('input', function() {
    var searchTerm = $(this).val().toLowerCase();
    var visibleItemsCount = 0;
    var hadNoVisibleItems = $('.slider .image-container:visible').length === 0;

    $('.slider .image-container').each(function() {
        var text = $(this).find('.tutspan').text().toLowerCase();
        if (text.includes(searchTerm)) {
            $(this).show();
            visibleItemsCount++;
        } else {
            $(this).hide();
        }
    });
    checkAndDisplayPlaceholders();

    if (visibleItemsCount === 0 || searchTerm === '') {
        $('.slider').css("--slider-index", 0);

    } else if (hadNoVisibleItems && visibleItemsCount > 0) {
        $('.slider').css("--slider-index", 0);
    }

    updateHandleVisibility();
    $(".progress-bar").each(updateProgressBar);
});
//empty search results
function checkAndDisplayPlaceholders() {
    ['technical-slider', 'non-technical-slider'].forEach(function(sliderClass) {
        var items = $('.' + sliderClass + ' .image-container').not('.placeholder');
        var visibleItems = items.filter(':visible').length;

        if (visibleItems === 0) {
            $('.' + sliderClass + ' .placeholder').show();
        } else {
            $('.' + sliderClass + ' .placeholder').hide();
        }
    });
}
//search functionality
$(document).ready(function() {

    $('.clear-search').on('click', function() {
        $(this).prev('input[type="text"]').val('').trigger('input').focus();
    });

    $('.search-bar form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
});



function adjustSliderIndex(visibleItemsCount) {
    const $sliderElem = $('.slider');
    const itemsInView = parseInt($sliderElem.css("--items-per-screen"), 10);
    let currentSliderIndex = parseInt($sliderElem.css("--slider-index"), 10);

    if (currentSliderIndex * itemsInView >= visibleItemsCount) {
        // adjust index to bring visible items into view
        $sliderElem.css("--slider-index", Math.max(0, Math.ceil(visibleItemsCount / itemsInView) - 1));
    }
}



//allows for phone screens
$(document).on("touchstart", ".slider", function (event) {
    const touchStartX = event.originalEvent.touches[0].pageX;
    $(this).data('touchStartX', touchStartX);
});
//allows for phone screens
$(document).on("touchmove", ".slider", function (event) {
    event.preventDefault();

    const touchEndX = event.originalEvent.touches[0].pageX;
    $(this).data('touchDiffX', $(this).data('touchStartX') - touchEndX);
});
//allows for phone screens
$(document).on("touchend", ".slider", function () {
    const touchDiffX = $(this).data('touchDiffX');
    const $container = $(this).closest('.container1');

    if (Math.abs(touchDiffX) > 50) {
        if (touchDiffX > 0) {
            $container.find(".right-handle").trigger('click');
        } else {
            $container.find(".left-handle").trigger('click');
        }
    }

    $(this).removeData('touchStartX').removeData('touchDiffX');
});

const resizeThrottle = throttle(() => $(".progress-bar").each(updateProgressBar), 250);
$(window).on("resize", resizeThrottle);

$(".progress-bar").each(updateProgressBar);
//progress bar is hidden so not really used
function updateProgressBar() {
    const $pBar = $(this);
    $pBar.empty();

    const $sliderElem = $pBar.closest(".row").find(".slider");
    const visibleItems = $sliderElem.children(':visible').length;
    const itemsInView = parseInt($sliderElem.css("--items-per-screen"), 10);
    let currentSliderIndex = parseInt($sliderElem.css("--slider-index"), 10);
    const barItemsCount = Math.ceil(visibleItems / itemsInView);

    if (currentSliderIndex >= barItemsCount) {
        $sliderElem.css("--slider-index", barItemsCount - 1);
        currentSliderIndex = barItemsCount - 1;
    }

    for (let idx = 0; idx < barItemsCount; idx++) {
        const $indicator = $("<div>").addClass("progress-item");

        if (idx === currentSliderIndex) {
            $indicator.addClass("active");
        }

        $pBar.append($indicator);
    }
}

function updateHandleVisibility() {
    const $sliderElem = $('.slider');
    const visibleItemsCount = $sliderElem.find('.image-container:visible').length;
    const itemsInView = parseInt($sliderElem.css("--items-per-screen"), 10);

    if (visibleItemsCount > itemsInView) {
        $('.left-handle, .right-handle').show();
    } else {
        $('.left-handle, .right-handle').hide();
    }
}


//adjust index / elements showing when handle is clicked
function handleClicked($detectedHandle) {
    const $pBar = $detectedHandle.closest(".row").find(".progress-bar");
    const $sliderElem = $detectedHandle.closest(".container1").find(".slider");
    const currentSliderIndex = parseInt($sliderElem.css("--slider-index"), 10);
    const barItemsCount = $pBar.children().length;

    const setActiveIndicator = (index) => {
        $pBar.children().eq(currentSliderIndex).removeClass("active");
        $pBar.children().eq(index).addClass("active");
    };

    if ($detectedHandle.hasClass("left-handle")) {
        const newIndex = currentSliderIndex - 1 < 0 ? barItemsCount - 1 : currentSliderIndex - 1;
        $sliderElem.css("--slider-index", newIndex);
        setActiveIndicator(newIndex);
    }

    if ($detectedHandle.hasClass("right-handle")) {
        const newIndex = currentSliderIndex + 1 >= barItemsCount ? 0 : currentSliderIndex + 1;
        $sliderElem.css("--slider-index", newIndex);
        setActiveIndicator(newIndex);
    }
}

function throttle(func, waitTime = 1000) {
    let isThrottled = false;
    let savedArgs;

    const delayedExecution = () => {
        if (!savedArgs) {
            isThrottled = false;
        } else {
            func(...savedArgs);
            savedArgs = null;
            setTimeout(delayedExecution, waitTime);
        }
    };

    return (...args) => {
        if (isThrottled) {
            savedArgs = args;
            return;
        }

        func(...args);
        isThrottled = true;
        setTimeout(delayedExecution, waitTime);
    }
}
