/**
 * PromoBar front script (Carousel version)
 * @license MIT
 */
(function () {
  "use strict";

  function setCookie(name, value, days) {
    try {
      var d = new Date();
      d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie =
        name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
    } catch (e) {}
  }

  function pad2(n) {
    return (n < 10 ? "0" : "") + n;
  }

  function decodeEntities(str) {
    var txt = document.createElement("textarea");
    txt.innerHTML = str;
    return txt.value;
  }

  function hydrateMessages(root) {
    var marquees = root.querySelectorAll(".promobar__marquee[data-html]");
    for (var i = 0; i < marquees.length; i++) {
      var marquee = marquees[i];
      var htmlEscaped = marquee.getAttribute("data-html");
      if (htmlEscaped) {
        marquee.innerHTML = decodeEntities(htmlEscaped);
        marquee.removeAttribute("data-html");
      }
    }
  }

  function initCountdowns(root) {
    var countdowns = root.querySelectorAll(".promobar__countdown");
    var timers = [];

    for (var i = 0; i < countdowns.length; i++) {
      var el = countdowns[i];
      var endTimestamp = el.getAttribute("data-end");
      if (!endTimestamp) continue;

      var end = new Date(parseInt(endTimestamp, 10));
      if (isNaN(end.getTime())) continue;

      (function (el, end) {
        var dEl = el.querySelector('[data-cd="d"]');
        var hEl = el.querySelector('[data-cd="h"]');
        var mEl = el.querySelector('[data-cd="m"]');
        var sEl = el.querySelector('[data-cd="s"]');

        function tick() {
          var now = new Date();
          var diff = end - now;

          if (diff <= 0) {
            el.style.display = "none";
            return;
          }

          var sec = Math.floor(diff / 1000);
          var days = Math.floor(sec / 86400);
          sec -= days * 86400;
          var hrs = Math.floor(sec / 3600);
          sec -= hrs * 3600;
          var mins = Math.floor(sec / 60);
          sec -= mins * 60;

          if (dEl) dEl.textContent = pad2(days);
          if (hEl) hEl.textContent = pad2(hrs);
          if (mEl) mEl.textContent = pad2(mins);
          if (sEl) sEl.textContent = pad2(sec);
        }

        tick();
        timers.push(setInterval(tick, 1000));
      })(el, end);
    }

    return timers;
  }

  function initCarousel(bar) {
    var track = bar.querySelector(".promobar__track");
    var slides = bar.querySelectorAll(".promobar__slide");
    var prevBtn = bar.querySelector(".promobar__arrow--prev");
    var nextBtn = bar.querySelector(".promobar__arrow--next");

    if (!track || slides.length <= 1) return null;

    var currentIndex = 0;
    var interval = parseInt(bar.getAttribute("data-carousel-interval") || "5", 10) * 1000;
    var pauseOnHover = bar.getAttribute("data-carousel-pause") === "1";
    var transition = bar.getAttribute("data-carousel-transition") || "fade";
    var autoplayTimer = null;
    var isPaused = false;

    function showSlide(index, direction) {
      if (index < 0) index = slides.length - 1;
      if (index >= slides.length) index = 0;

      var previousIndex = currentIndex;

      // For slide transition, mark previous slide as leaving BEFORE removing active
      if (transition === "slide" && previousIndex !== index) {
        // Determine direction: going backward (prev) or forward (next)
        var goingBackward = direction === "prev";

        // Mark previous slide as leaving
        if (goingBackward) {
          slides[previousIndex].classList.add("promobar__slide--leaving-right");
          slides[previousIndex].classList.remove("promobar__slide--leaving");
        } else {
          slides[previousIndex].classList.add("promobar__slide--leaving");
          slides[previousIndex].classList.remove("promobar__slide--leaving-right");
        }

        // Prepare incoming slide
        if (goingBackward) {
          slides[index].classList.add("promobar__slide--from-left");
          slides[index].classList.remove("promobar__slide--from-right");
        } else {
          slides[index].classList.add("promobar__slide--from-right");
          slides[index].classList.remove("promobar__slide--from-left");
        }

        // Clean up old direction classes
        slides[index].classList.remove("promobar__slide--leaving");
        slides[index].classList.remove("promobar__slide--leaving-right");
      }

      // Remove active class from all slides except the new one
      for (var i = 0; i < slides.length; i++) {
        if (i !== index) {
          slides[i].classList.remove("promobar__slide--active");
        }
      }

      // Add active class to new slide
      slides[index].classList.add("promobar__slide--active");
      currentIndex = index;

      // Clean up classes after transition
      if (transition === "slide" && previousIndex !== index) {
        setTimeout(function() {
          // Disable transitions temporarily to avoid flash when repositioning
          slides[previousIndex].style.transition = "none";
          slides[previousIndex].classList.remove("promobar__slide--leaving");
          slides[previousIndex].classList.remove("promobar__slide--leaving-right");

          // Clean up direction classes from current slide
          slides[index].classList.remove("promobar__slide--from-left");
          slides[index].classList.remove("promobar__slide--from-right");

          // Re-enable transitions after the browser has repositioned the element
          setTimeout(function() {
            slides[previousIndex].style.transition = "";
          }, 50);
        }, 150);
      }
    }

    function nextSlide() {
      showSlide(currentIndex + 1, "next");
    }

    function prevSlide() {
      showSlide(currentIndex - 1, "prev");
    }

    function startAutoplay() {
      if (autoplayTimer) clearInterval(autoplayTimer);
      autoplayTimer = setInterval(function () {
        if (!isPaused) {
          showSlide(currentIndex + 1, "next");
        }
      }, interval);
    }

    function stopAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }

    // Navigation arrows
    if (prevBtn) {
      prevBtn.addEventListener("click", function () {
        prevSlide();
        stopAutoplay();
        startAutoplay();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        nextSlide();
        stopAutoplay();
        startAutoplay();
      });
    }

    // Pause on hover
    if (pauseOnHover) {
      bar.addEventListener("mouseenter", function () {
        isPaused = true;
      });

      bar.addEventListener("mouseleave", function () {
        isPaused = false;
      });
    }

    // Start autoplay
    startAutoplay();

    return {
      stop: stopAutoplay,
      start: startAutoplay,
      next: nextSlide,
      prev: prevSlide
    };
  }

  document.addEventListener("DOMContentLoaded", function () {
    var bar = document.getElementById("promobar");
    if (!bar) return;

    var cookieName = bar.getAttribute("data-cookie") || "promobar_dismissed";
    var cookieDays = parseInt(bar.getAttribute("data-cookie-days") || "30", 10);
    if (isNaN(cookieDays) || cookieDays <= 0) cookieDays = 30;

    if (document.cookie.indexOf(cookieName + "=1") !== -1) {
      bar.style.display = "none";
      return;
    }

    var close = bar.querySelector(".promobar__close");
    if (close) {
      close.addEventListener("click", function () {
        setCookie(cookieName, "1", cookieDays);
        bar.style.display = "none";
      });
    }

    // Hydrate all messages
    hydrateMessages(bar);

    // Initialize all countdowns
    initCountdowns(bar);

    // Initialize carousel if enabled
    var carouselEnabled = bar.getAttribute("data-carousel-enabled") === "1";
    if (carouselEnabled) {
      initCarousel(bar);
    }
  });
})();
