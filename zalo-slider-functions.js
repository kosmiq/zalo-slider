jQuery(function( $ ){
  var currentslicksliderindex = 0;
  var newslicksliderindex;

  $('.slickslider').slick({
    infinite: true,
    dots: true,
    adaptiveHeight: false,
    variableWidth: false,
    speed: 500,
    centerMode: true,
    centerPadding: '100px',
    slidesToShow: 1,
    arrows: true,
    cssEase: 'ease',
    swipe: true,
    draggable: true,
    touchMove: true,
    onAfterChange: function(slide, index){
      newslicksliderindex = $(slide.$slides.get(index)).attr('index');
    },
    responsive: [
      {
        breakpoint: 1023,
        settings: {
          centerPadding: '80px',
        }
      },
      {
        breakpoint: 782,
        settings: {
          centerPadding: '60px',
        }
      },
      {
        breakpoint: 480,
        settings: {
          centerPadding: '40px',
        }
      }
    ]
  });

  slickdots = function() {
      $sliderimageheight = 0;
      $slidernavheight = 0;
      $slickdots = 0;

      $sliderimageheight = $(".slick-active .slider-image .wp-post-image").outerHeight();
      $slidernavheight = $(".slick-prev").outerHeight();
      $slidernavtop = Math.floor(parseInt( ($sliderimageheight) / 2) - ($slidernavheight / 2) );
      $slickdots = Math.floor(parseInt( ( $sliderimageheight + 10 ) ));

      $('.slick-prev').css('top', $slidernavtop);
      $('.slick-next').css('top', $slidernavtop);
      $('.slick-dots').css('top', $slickdots)
  };

  $(document).ready(function() {
    $( ".slider-image" ).on( "lazyload.bj", "img", function() {
      $('.slick-active .slider-image').imagesLoaded( slickdots );
    });
  });

  $(window).bind('resize', function(e)
  {
    window.resizeEvt;
    $(window).resize(function()
    {
      clearTimeout(window.resizeEvt);
      window.resizeEvt = setTimeout(function()
      {
        slickdots();
      }, 250);
    });
  });

});
