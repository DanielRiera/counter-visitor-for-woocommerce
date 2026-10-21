var WCVisitor = {
    loading: false,
    show: function(product, position, isReload) {
        var self = this;
        if (self.loading) {
            return;
        }
        self.loading = true;
        var containers = jQuery('.wcv-container-' + product);
        var contexts = [{}];
        containers.each(function() {
            var element = jQuery(this);
            var context = {
                msgone: element.attr('data-msgone') || '',
                msgmore: element.attr('data-msgmore') || ''
            };
            if ((context.msgone || context.msgmore) && !contexts.some(function(existing) {
                return existing.msgone === context.msgone && existing.msgmore === context.msgmore;
            })) {
                contexts.push(context);
            }
        });
        var pending = contexts.length;
        var loadContext = function(index) {
            var context = contexts[index];
            jQuery.ajax({
                type: 'post',
                url: WCVisitorConfig.url,
                dataType: 'json',
                timeout: 15000,
                data: {
                    action: 'wcvisitor_get_counter',
                    product: product,
                    refresh: isReload || !WCVisitorConfig.record || index > 0 ? 1 : 0,
                    msgone: context.msgone || '',
                    msgmore: context.msgmore || ''
                },
                success: function(result) {
                    if (typeof result.html !== 'string') {
                        return;
                    }
                    var targets = jQuery('.wcv-container-' + product).filter(function() {
                        return (jQuery(this).attr('data-msgone') || '') === (context.msgone || '') &&
                            (jQuery(this).attr('data-msgmore') || '') === (context.msgmore || '');
                    });
                    if (targets.length) {
                        targets.each(function() {
                            var target = jQuery(this);
                            var content = jQuery(result.html).html();
                            if (target.html() !== content) {
                                target.html(content);
                                if (isReload) {
                                    target.find('.wcv-message').addClass('wcv-animation-on');
                                    setTimeout(function() {
                                        target.find('.wcv-message').removeClass('wcv-animation-on');
                                    }, 600);
                                }
                            }
                        });
                    } else if (index === 0 && position) {
                        var placement = JSON.parse(position);
                        var anchor = jQuery(placement[0]);
                        if (placement[1] === 'after') {
                            anchor.after(result.html);
                        } else if (placement[1] === 'before') {
                            anchor.before(result.html);
                        } else if (placement[1] === 'inside') {
                            anchor.append(result.html);
                        }
                    }
                    if (index === 0) {
                        jQuery('.wcv-price-' + product).html(result.text ? ' | ' + result.text : '');
                    }
                },
                complete: function() {
                    pending -= 1;
                    if (pending === 0) {
                        self.loading = false;
                    } else {
                        loadContext(index + 1);
                    }
                }
            });
        };
        loadContext(0);
    },
    reload: function(product, timeToReload) {
        var self = this;
        setInterval(function() {
            self.show(product, false, true);
        }, 1000 * parseInt(timeToReload, 10));
    }
};
