////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * das jack js framework----core file
 Version:
 1.0.0.0 - Initial release
 *Date: 2009-12-20  17:34:21
 *Revision: 1
 Compatibility:
 >js 1.6.5
 Discription:
 * index of app
 * 1 config require paths
 * 2 init global vars
 * 3 create app and frame
 *
 Dependencies:

 Credits:
 -
 Author:
 jack.liu
 License:
 >Copyright (C) 2009 jack liu - dasjack@gmail.com
 >
 >Permission is hereby granted, free of charge,
 >to any person obtaining a copy of this software and associated
 >documentation files (the "Software"),
 >to deal in the Software without restriction,
 >including without limitation the rights to use, copy, modify, merge,
 >publish, distribute, sublicense, and/or sell copies of the Software,
 >and to permit persons to whom the Software is furnished to do so,
 >subject to the following conditions:
 >
 >The above copyright notice and this permission notice shall be included
 >in all copies or substantial portions of the Software.
 >
 >THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 >INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 >FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 >IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 >DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 >ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 >OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

(function (ns) {
    var appcache  = {
        status:applicationCache && applicationCache.status,
        onfinish:null,
        onstep:null
    };
    if (!applicationCache) {
        return;
    }
    /**
     *
     */
    appcache.update = function () {
        try {
            applicationCache.update();
        } catch (e) {
            log('app update:' + e.message);
            //appcache.onstep && appcache.onstep('error', e);
        }
    };
    //var events = "checking noupdate downloading cached updateready obsolete error";
    var cacheStatusValues = [];
    cacheStatusValues[0] = 'uncached';
    cacheStatusValues[1] = 'idle';
    cacheStatusValues[2] = 'checking';
    cacheStatusValues[3] = 'downloading';
    cacheStatusValues[4] = 'updateready';
    cacheStatusValues[5] = 'obsolete';
    /**
     *
     * @type {Array}
     */
    appcache.setup = function (finish, step) {
        appcache.onfinish = finish;
        appcache.onstep = step;
        log('appcache status=' + cacheStatusValues[appcache.status]);
        if (applicationCache && applicationCache.status == 0) {
            log('no cache');
            finish && finish('error');
        }
    };
    var events = ['checking', 'noupdate', 'downloading', 'cached', 'updateready', 'error', 'progress'];
    events.forEach(function (etype, i) {
        applicationCache.addEventListener(etype, function (event) {
            log('event ' + event.type + ' appcache:' + cacheStatusValues[applicationCache.status]);
            appcache.onstep && appcache.onstep(etype, event);
            if (event.type == 'updateready') {//use updated immediately
                if (confirm('新版本已更新，是否立即使用? ')) {
                    log('new version updated');
                    window.location.reload();
                }
                else {
                    appcache.onfinish && appcache.onfinish(event.type, event);
                }
            }
            else if (event.type == 'noupdate' || event.type == 'error' || event.type == 'cached') {
                appcache.onfinish && appcache.onfinish(event.type, event);
            }
        }, false);
    });

    ns.app = {
        /**
         *watch when jqm is ready
         */
        initialize:function (fn) {
            this.onReady = fn;
            $(document).bind("mobileinit", function () {
                $.support.cors = true;
                $.extend($.mobile, {
                    allowCrossDomainPages:true,
                    autoInitializePage:false,
                    defaultPageTransition:'flow',
                    loadingMessage:'Loading...'
                });
                $.mobile.buttonMarkup.hoverDelay = 100;
                $.extend($.mobile.page.prototype.options, {
                    addBackBtn:true
                });
                if (!window.hasOwnProperty('cordova')) {
                    $(document).trigger('deviceready');
                }
            });
            var self = this;
            $(document).bind("deviceready", function () {
                appcache.setup(function(){
                    $(function () {//finish
                        $.mobile.initializePage();
                        self.onReady && self.onReady();
                    });
                    $(document).bind('online', function () {
                        app.showMsg('欢迎回来!', 1500);
                    });
                },function(){//step

                });
            });
        },
        /**
         *show message panel
         */
        showMsg:function (msg, timeout) {
            if (!this.$msg) {
                this.$msg = $('.ui-msg');
                var $msgContent = this.$msgContent = $('.ui-msg h3');
                this.$msg.ajaxError(function (event, request, settings) {
                    app.netErrorHandler && app.netErrorHandler(request, settings);
                    $msgContent.html("服务不可用.").parent().stop().show(function () {
                        $(this).fadeOut(1000);
                    });
                });
                this.$msg.ajaxSuccess(function (evt, request, settings) {
                    app.netErrors = 0;
                });
            }
            //
            this.$msgContent.html(msg);
            if (withIcon) {
                $('.ui-icon-loading', this.$msg).show();
            }
            else {
                $('.ui-icon-loading', this.$msg).hide();
            }
            if (timeout) {
                this.$msg.stop().show(function () {
                    $(this).fadeOut(timeout);
                });
            }
            else {
                this.$msg.show();
            }
        },
        /**
         *hide message panel
         */
        hideMsg:function () {
            this.$msg && this.$msg.hide();
        },
        netErrorHandler:function (request, settings) {
            log(settings.url);
            if (settings.url.match('/login')) {
                $('#notice').fadeIn();
            }
            if (this.netErrors++ > 5) {
                this.stopDataLoop();
            }
        },
        netErrors:0
    };
})(typeof(exports)=='undefined' ? window : exports);