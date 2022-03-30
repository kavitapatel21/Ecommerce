<?php
/*
Template Name: Thumbnail-slider
Template Post Type: post, page, my-post-type;
*/
get_header();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.3/assets/owl.carousel.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.3/assets/owl.theme.default.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.3/owl.carousel.min.js"></script>

<div id="sync1" class="owl-carousel owl-theme">
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"
      />
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
    </h4>
  </div>
  <div class="item fade">
    <h4>
      <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
    </h4>
  </div>
</div>

<div id="sync2" class="owl-carousel owl-theme">
  <div class="item">
    <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
  </div>
  <div class="item">
    <!-- <h1>2</h1> -->
    <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/282828/eae0d0/?retina=1&text=hello" alt="landscape"/>
  </div>
  <div class="item">
    <img src="https://fakeimg.pl/440x230/666666/eae0d0/?retina=1&text=world" alt="landscape"/>
  </div>
</div>
<style type="text/css">
  body{
  width: 800px;
}
#sync1 .item {
  margin: 5px;
  color: #fff;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  text-align: center;
}

#sync2 .item {
  background: #c9c9c9;
  /* padding: 10px 0px; */
  margin: 5px;
  color: #fff;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  text-align: center;
  cursor: pointer;
}

#sync2 .item h1 {
  font-size: 18px;
}

#sync2 .current .item {
  background: #0c83e7;
}

.owl-theme .owl-nav [class*="owl-"] {
  transition: all 0.3s ease;
}

.owl-theme .owl-nav [class*="owl-"].disabled:hover {
  background-color: #d6d6d6;
}

#sync1.owl-theme {
  position: relative;
}

#sync1.owl-theme .owl-next,
#sync1.owl-theme .owl-prev {
  width: 22px;
  height: 40px;
  margin-top: -20px;
  position: absolute;
  top: 50%;
}

#sync1.owl-theme .owl-prev {
  left: 10px;
}

#sync1.owl-theme .owl-next {
  right: 10px;
}
/* animate fadin duration 1.5s */
.owl-carousel .animated {
  animation-duration: 1.5s !important;
}
/* 輪播的前後按鈕背景調大 */
#sync1.owl-theme .owl-next,
#sync1.owl-theme .owl-prev {
  width: 35px !important;
  height: 55px !important;
}
#sync1 svg {
  width: 22px !important;
}
</style>
<script type="text/javascript">
  jQuery(document).ready(function() {
    var sync1 = jQuery("#sync1");
    var sync2 = jQuery("#sync2");
    var slidesPerPage = 5; //globaly define number of elements per page
    var syncedSecondary = true;

    sync1.owlCarousel({
      items: 1,
      slideSpeed: 3000,
      nav: true,

      //   animateOut: 'fadeOut',
      animateIn: "fadeIn",
      autoplayHoverPause: true,
      autoplaySpeed: 1400, //過場速度
      dots: false,
      loop: true,
      responsiveClass: true,
      responsive: {
        0: {
          item: 1,
          autoplay: false
        },
        600: {
          items: 1,
          autoplay: true
        }
      },
      responsiveRefreshRate: 200,
      navText: [
        '<svg width="100%" height="100%" viewBox="0 0 11 20"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M9.554,1.001l-8.607,8.607l8.607,8.606"/></svg>',
        '<svg width="100%" height="100%" viewBox="0 0 11 20" version="1.1"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M1.054,18.214l8.606,-8.606l-8.606,-8.607"/></svg>'
      ]
    })
      .on("changed.owl.carousel", syncPosition);

    sync2.on("initialized.owl.carousel", function() {
      sync2.find(".owl-item")
        .eq(0)
        .addClass("current");
    })
      .owlCarousel({
      items: slidesPerPage,
      dots: true,
      //   nav: true,
      smartSpeed: 1000,
      slideSpeed: 1000,
      slideBy: slidesPerPage, //alternatively you can slide by 1, this way the active slide will stick to the first item in the second carousel
      responsiveRefreshRate: 100
    })
      .on("changed.owl.carousel", syncPosition2);

    function syncPosition(el) {
      //if you set loop to false, you have to restore this next line
      //var current = el.item.index;

      //if you disable loop you have to comment this block
      var count = el.item.count - 1;
      var current = Math.round(el.item.index - el.item.count / 2 - 0.5);

      if (current < 0) {
        current = count;
      }
      if (current > count) {
        current = 0;
      }

      //end block

      sync2.find(".owl-item")
        .removeClass("current")
        .eq(current)
        .addClass("current");
      var onscreen = sync2.find(".owl-item.active").length - 1;
      var start = sync2.find(".owl-item.active")
      .first()
      .index();
      var end = sync2.find(".owl-item.active")
      .last()
      .index();

      if (current > end) {
        sync2.data("owl.carousel").to(current, 100, true);
      }
      if (current < start) {
        sync2.data("owl.carousel").to(current - onscreen, 100, true);
      }
    }

    function syncPosition2(el) {
      if (syncedSecondary) {
        var number = el.item.index;
        sync1.data("owl.carousel").to(number, 100, true);
      }
    }

    sync2.on("click", ".owl-item", function(e) {
      e.preventDefault();
      var number = jQuery(this).index();
      sync1.data("owl.carousel").to(number, 300, true);
    });
  });
</script>
<?php
get_footer();
?>