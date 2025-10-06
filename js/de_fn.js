$( document ).ready(function() {
  setTooltip();
});

function setTooltip(){
    var targets = $('[title]'),
        target  = false,
        tooltip = false,
        title   = false;
 
    targets.unbind('mouseenter mouseleave touchstart');
    targets.bind('mouseenter', function()
    {
        target  = $(this);
        tip     = target.attr('title');
        if (tip) {
            tip = tip.replace('&', '<br>');
        }
        
        // Entferne vorhandene Tooltips
        $('#tt').remove();
        
        tooltip = $('<div id="tt"></div>');
 
        if(!tip || tip == '')
            return false;
 
        target.removeAttr('title');
        target.data('original-title', tip); // Titel sicher speichern
        
        tooltip.css('opacity', 0)
               .html(tip)
               .appendTo('body');
 
        var init_tooltip = function()
        {
            if($(window).width() < tooltip.outerWidth() * 1.5)
                tooltip.css('max-width', $(window).width() / 2);
            else
                tooltip.css('max-width', 340);
 
            var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                pos_top  = target.offset().top - tooltip.outerHeight() - 20;
 
            if(pos_left < 0)
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass('left');
            }
            else
                tooltip.removeClass('left');
 
            if(pos_left + tooltip.outerWidth() > $(window).width())
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass('right');
            }
            else
                tooltip.removeClass('right');
 
            if(pos_top < 0)
            {
                pos_top = target.offset().top + target.outerHeight();
                tooltip.addClass('top');
            }
            else
                tooltip.removeClass('top');
 
            tooltip.css({left: pos_left, top: pos_top})
                   .animate({top: '+=10', opacity: 1}, 50);
        };
 
        init_tooltip();
        $(window).resize(init_tooltip);
 
        var remove_tooltip = function()
        {
            // Window resize unbinden
            $(window).unbind('resize', init_tooltip);
            
            tooltip.animate({top: '-=10', opacity: 0}, 50, function()
            {
                $(this).remove();
            });
 
            // Titel aus data-Attribut wiederherstellen
            var originalTitle = target.data('original-title');
            if(originalTitle) {
                target.attr('title', originalTitle);
            }
            
            // Event-Listener entfernen
            target.unbind('mouseleave', remove_tooltip);
            tooltip.unbind('click touchstart', remove_tooltip);
        };
 
        target.bind('mouseleave', remove_tooltip);
        tooltip.bind('click touchstart', remove_tooltip);
    });
    
    // Zusätzlicher Fallback: Entferne alle Tooltips bei Scroll oder Resize
    $(window).on('scroll resize', function() {
        $('#tt').remove();
        $('[data-original-title]').each(function() {
            var $this = $(this);
            $this.attr('title', $this.data('original-title'));
        });
    });
    
    // Touch-Geräte: Tooltip bei Touch außerhalb entfernen
    $(document).on('touchstart', function(e) {
        if(!$(e.target).closest('#tt').length && !$(e.target).closest('[title], [data-original-title]').length) {
            $('#tt').remove();
            $('[data-original-title]').each(function() {
                var $this = $(this);
                $this.attr('title', $this.data('original-title'));
            });
        }
    });
};


function showHiddenInfo(id){
  $('#hidden_info'+id).css('display', 'block');
  $('#hidden_show'+id).css('display', 'none');
}