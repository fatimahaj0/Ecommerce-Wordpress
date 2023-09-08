;( function ( $ ) {
    'use strict';

    window.addEventListener('load', () => {
    
        (function () {

            const body = document.querySelector('body'),
                popups = document.querySelectorAll('.wlpb-popup');
    
            /**
             * Popup Open Function
             * @param {Element} popup
             * @param {Element} overlay 
             * @param {Element} container 
             * @param {object} settings 
             */
            const popupOpen = (popup, overlay, container, settings) => {
                popup?.classList.add('wlpb-popup-open')
                overlay?.classList.add('animated', 'fadeIn')
                overlay?.style.setProperty('animation-duration', '0.5s')
                overlay?.style.setProperty('animation-delay', '0s')
                popupDisablePageScroll('add', settings)
                popupAnimationIn(container, settings)
                if(+settings?.dismiss_automatically) {
                    dismiss_automatically(popup, overlay, container, settings);
                }
                popup_bar_open(popup, settings)
            }
            /**
             * Popup Open Animation Function
             * @param {Element} container 
             * @param {object} settings 
             */
            const popupAnimationIn = (container, settings) => {
                const animation_in = settings?.popup_animation_in || 'fadeIn',
                    duration_in = +settings?.popup_animation_in_duration || 1,
                    delay_in = +settings?.popup_animation_in_delay || 0.125
                container?.classList.add('animated', `${animation_in}`)
                container?.style.setProperty('animation-duration', `${duration_in}s`)
                container?.style.setProperty('animation-delay', `${delay_in}s`)
            }
    
            /**
             * Popup Close Function
             * @param {Element} popup 
             * @param {Element} overlay 
             * @param {Element} container 
             * @param {object} settings 
             */
            const popupClose = (popup, overlay, container, settings) => {
                const animation_out = settings?.popup_animation_out || 'fadeOut',
                    duration_out = +settings?.popup_animation_out_duration || 0.5
                setTimeout(() => {
                    overlay?.classList.remove('fadeIn')
                    overlay?.classList.add('fadeOut')
                    overlay?.style.setProperty('animation-duration', '0.5s')
                    overlay?.style.setProperty('animation-delay', '0.125s')
                    setTimeout(()=> {
                        popup?.classList.remove('wlpb-popup-open')
                        container?.classList.remove('animated', `${animation_out}`)
                        container?.style.removeProperty('animation-duration')
                        container?.style.removeProperty('animation-delay')
                        overlay?.classList.remove('animated', 'fadeOut')
                        overlay?.style.removeProperty('animation-duration')
                        overlay?.style.removeProperty('animation-delay')
                    }, 125)
                    popupDisablePageScroll('remove', settings)
                }, (duration_out / 3) * 1000)
                popupAnimationOut(container, settings)
                popup_bar_close(popup, settings)
            }
            /**
             * Popup Close Animation Function
             * @param {Element} container 
             * @param {object} settings 
             */
            const popupAnimationOut = (container, settings) => {
                const animation_in = settings?.popup_animation_in || 'fadeIn',
                    animation_out = settings?.popup_animation_out || 'fadeOut',
                    duration_out = +settings?.popup_animation_out_duration || 0.5,
                    delay_out = +settings?.popup_animation_out_duration || 0
                container?.classList.remove(`${animation_in}`)
                container?.classList.add(`${animation_out}`)
                container?.style.setProperty('animation-duration', `${duration_out}s`)
                container?.style.setProperty('animation-delay', `${delay_out}s`)
            }
    
            /**
             * Popup Disable Page Scroll 
             * @param {String} action 
             * @param {object} settings 
             */
            const popupDisablePageScroll = (action, settings) => {
                if(!+settings?.disable_page_scroll || +settings?.close_after_page_scroll) return null;
                if(action === 'add') {
                    body?.classList.add('wlpb-disable-scroll')
                } else {
                    body?.classList.remove('wlpb-disable-scroll')
                }
            }
    
            /**
             * Popup Close Automatically
             * @param {Element} popup
             * @param {Element} overlay 
             * @param {Element} container 
             * @param {object} settings 
             */
            const dismiss_automatically = (popup, popupOverlay, popupContainer, settings) => {
                setTimeout(() => {
                    popupClose(popup, popupOverlay, popupContainer, settings)
                }, +settings?.dismiss_automatically_delay * 1000)
            }
    
            /**
             * Add open transform translateY for popup bar style
             * It will help with bar slide animation
             * @param {Element} popup 
             * @param {object} settings 
             */
            const popup_bar_open = (popup, settings) => {
                if(settings?.popup_display_as === 'bar') {
                    body?.classList.remove('wlpb-disable-scroll')
                    body.style.transition = 'all 0.3s ease 0s'
                    popup.style.transform = 'translateY(0)'
                    if(settings?.popup_bar_position === 'top' && !settings?.popup_bar_float) {
                        body.style.paddingTop = `${popup.offsetHeight}px`
                    } else if(settings?.popup_bar_position === 'bottom' && !settings?.popup_bar_float) {
                        body.style.paddingBottom = `${popup.offsetHeight}px`
                    }
                }
            }
    
            /**
             * Add close transform translateY for popup bar style
             * It will help with bar slide animation
             * @param {Element} popup 
             * @param {object} settings 
             */
            const popup_bar_close = (popup, settings) => {
                if(settings?.popup_display_as === 'bar') {
                    if(settings?.popup_bar_position === 'top') {
                        popup.style.transform = 'translateY(-100%)'
                    } else if(settings?.popup_bar_position === 'bottom') {
                        popup.style.transform = 'translateY(100%)'
                    }
                    if(settings?.popup_bar_position === 'top' && !settings?.popup_bar_float) {
                        body.style.paddingTop = '0px'
                    } else if(settings?.popup_bar_position === 'bottom' && !settings?.popup_bar_float) {
                        body.style.paddingBottom = '0px'
                    }
                    setTimeout(() => {
                        body.style.removeProperty('padding-top')
                        body.style.removeProperty('transition')
                    }, 300)
                }
            }
    
            /**
             * Loop over all popup
             */
            popups.forEach((popup) => {
                const popupInner = popup?.querySelector('.wlpb-popup-inner'),
                    popupOverlay = popup?.querySelector('.wlpb-popup-overlay'),
                    popupContainer = popup?.querySelector('.wlpb-popup-container'),
                    popupContainerInner = popup?.querySelector('.wlpb-popup-container-inner'),
                    popupCloseBtn = popup?.querySelector('.wlpb-popup-close-btn'),
                    settings = JSON.parse(popup?.dataset.settings),
                    triggers = settings?.triggers;
    
    
                /**
                 * Popup bar style default translate style
                 */
                popup_bar_close(settings)
    
                /**
                 * Popup Open on Page Click
                 */
                if(+triggers?.on_click) {
                    let popupActive = false,
                        clickCount = 0;
                    window.addEventListener('click', () => {
                        if(popupActive) return null;
                        clickCount++
                        if(clickCount === +triggers?.clicks_count) {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                            popupActive = true;
                        }
                    })
                }
                
                /**
                 * Popup Open on Custom Selector Click
                 */
                if(+triggers?.on_custom) {
                    const triggersElement = document.querySelectorAll(triggers?.custom_selector);
                    triggersElement?.forEach((element) => {
                        element.addEventListener('click', () => {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                        })
                    })
                }
                
                /**
                 * Popup Open when user try to leave the tab
                 */
                if(+triggers?.on_exit_intent) {
                    document.addEventListener('mouseleave', function (event) {
                        if(event.clientY <= 0) {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                        }
                    });
                }
                
                /**
                 * Popup Open when user stay inactive
                 */
                if(+triggers?.on_inactivity) {
                    let counter = 0,
                        timeout
                    
                    const resetCounter = () => {
                        clearTimeout(timeout);
                        counter = 0;
                        timeout = setTimeout(startCounter, 1000);
                    }
                    const startCounter = () => {
                        if (counter < +triggers?.on_inactivity_time) {
                            counter++;
                            timeout = setTimeout(startCounter, 1000);
                        }
                        if(counter === +triggers?.on_inactivity_time) {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                        }
                    }
                    resetCounter()
                    document.addEventListener('mousemove', resetCounter);
                    document.addEventListener('keydown', resetCounter);
                    document.addEventListener('scroll', resetCounter);
                    document.addEventListener('click', resetCounter);
                }
    
                /**
                 * Popup Open on Page Load
                 */
                if(+triggers?.on_page_load) {
                    setTimeout(() => {
                        popupOpen(popup, popupOverlay, popupContainer, settings)
                    }, +triggers?.page_load_delay * 1000)
                }
    
                /**
                 * Popup Open on Page Scroll
                 */
                if(+triggers?.on_scroll) {
                    let scrollPercentage = +triggers?.scroll_percentage;
                        scrollPercentage = scrollPercentage < 1 ? 50 : scrollPercentage; // if scroll percentage is less than 1 then set it to 50.
                    
                    const scrollDirection = triggers?.scroll_direction,
                        docHeight = document.documentElement.scrollHeight,
                        windowHeight = window.innerHeight,
                        scrollTriggerPoint = ((docHeight * scrollPercentage) / 100) - (windowHeight / 2)
    
                    let popupActive = false,
                        currentScrollPos = 0,
                        lastScrollPos = 0,
                        userScrollDirection = 'down'
                    window.addEventListener('scroll', () => {
                        if(popupActive) return null;
    
                        currentScrollPos = window.scrollY
                        if(currentScrollPos > lastScrollPos) {
                            userScrollDirection = 'down'
                        } else {
                            userScrollDirection = 'up'
                        }
                        lastScrollPos = currentScrollPos
    
                        if(scrollDirection === userScrollDirection && scrollDirection === 'down' && currentScrollPos >= scrollTriggerPoint) {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                            popupActive = true
                        }
                        if(scrollDirection === userScrollDirection && scrollDirection === 'up' && currentScrollPos <= scrollTriggerPoint) {
                            popupOpen(popup, popupOverlay, popupContainer, settings)
                            popupActive = true
                        }
                    })
                }
    
                /**
                 * Popup Close on Button Click
                 */
                popupCloseBtn.addEventListener('click', () => {
                    popupClose(popup, popupOverlay, popupContainer, settings)
                })
                
                /**
                 * Popup Close on Escape Key Press
                 */
                if(+settings?.dismiss_on_esc_key) {
                    document.addEventListener('keydown', (e) => {
                        if(e.key === 'Escape') {
                            popupClose(popup, popupOverlay, popupContainer, settings)
                        }
                    })
                }
                
                /**
                 * Popup Close on Overlay Click
                 */
                if(+settings?.dismiss_on_overlay_click) {
                    popupOverlay.addEventListener('click', () => {
                        popupClose(popup, popupOverlay, popupContainer, settings)
                    })
                }
                
                /**
                 * Popup Close on Page Scroll
                 */
                if(+settings?.close_after_page_scroll && !+settings?.disable_page_scroll) {
                    window.addEventListener('scroll', () => {
                        popupClose(popup, popupOverlay, popupContainer, settings)
                    })
                }
    
            })
                
        })()
    
    })

    $(document).ready(function() {
    }); // end of document ready

} )( jQuery );