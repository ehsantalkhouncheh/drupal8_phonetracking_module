//Ajax Request to refresh number
(function ($, Drupal) {
  Drupal.behaviors.phonetracking = {
    attach: function (context, settings) {
      $(document, context).once('phonetracking').each( function() {
        let interval = 180000;
        setInterval(
          function(){
            $.ajax({
              type: 'GET',
              url: '/phoneTracking/refreshNumber.json',
              dataType: 'json',
              success: function(data){
                console.log(data);
              },
              fail:function(data){
                console.log(data);
              }
            });
          }, interval
        );
      });

    }
  };
})(jQuery, Drupal);


