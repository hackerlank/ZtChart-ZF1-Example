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
window.appEx = {
    games:[],
    zones:{},
    zoneData:{},
    maxCount:20,
    interval:60, //seconds
    /**
     *
     */
    logon:function (uname, pwd, fn) {
        this.uname = uname;
        this.pwd = pwd;
        this.showMsg('连接服务器...');
        $.getJSON(this.urls['logon'], {
            uname:uname,
            pwd:pwd
        }, function (data) {
            app.hideMsg();
            if (data.res) {
                app.token = window.localStorage['utoken'] = data.token || 0;
                app.uname = window.localStorage['uname'] = uname;
            }
            else {
                app.showMsg(data.error, 3000);
            }
            fn && fn(data);
        });
    },
    /**
     *
     */
    getGameList:function (fn) {
        this.showMsg("正在获取游戏列表...");
        $.getJSON(this.urls['game'], {
            uname:this.uname,
            token:this.token
        }, function (data) {
            app.hideMsg();
            app.games = data;
            fn(data);
        });
    },
    /**
     *
     */
    getGameZone:function (gid, fn) {
        if (app.zones[gid]) {
            fn(app.zones[gid]);
        }
        else {
            this.showMsg("正在获取区列表...");
            $.getJSON(this.urls['gamezone'], {
                uname:this.uname,
                token:this.token,
                gametype:gid
            }, function (data) {
                log('server back zone:' + data.length);
                app.hideMsg();
                data.unshift({
                    zone_id:0,
                    zone_name:'全部区'
                });
                app.zones[gid] = data;
                fn(data);
            });
        }
    },
    /**
     *
     */
    getGameData:function (gid, zid, fn) {
        $.getJSON(this.urls['gamedata'], {
            uname:this.uname,
            token:this.token,
            gametype:gid,
            zoneid:zid,
            first:(app.zoneData[gid + '_' + zid] && app.zoneData[gid + '_' + zid].length > 1) ? 0 : 1
        }, function (datas) {
            var gzdata = app.zoneData[gid + '_' + zid] || [];
            if ($.isArray(datas.data[0])) {
                $.each(datas.data, function (i, data) {
                    gzdata.push({
                        max:datas.max,
                        min:datas.min,
                        data:data
                    });
                });
            }
            else {
                datas.data[0] = (new Date()).getTime();
                //log('get data at '+(new Date()).getSeconds());
                gzdata = gzdata.concat(datas);
            }
            //log('get data ,cache:' + gzdata.length);
            if (gzdata.length > app.maxCount) {
                gzdata = gzdata.slice(gzdata.length - app.maxCount);
            }
            app.zoneData[gid + '_' + zid] = gzdata;
            fn(gzdata);
        });
    },
    /**
     *
     */
    logonSystem:function () {
        log('try to logon system');
        this.token = window.localStorage['utoken'];
        this.uname = window.localStorage['uname'];
        if (this.token && this.uname) {
            this.showMsg('正在验证用户信息...');
            $.getJSON(this.urls['logon'], {
                uname:this.uname,
                token:this.token
            }, function (data) {
                app.hideMsg();
                if (data && data.res) {
                    $.mobile.changePage('#page-main');
                }
                else {
                    $.mobile.changePage('#page-logon');
                }
            });
        }
        else {
            $.mobile.changePage('#page-logon');
        }
    },
    /**
     *
     */
    initGameList:function () {
        var $glist = $("#select-game");
        var $zlist = $('#select-zone');
        $glist.html("");
        $zlist.html("");
        this.getGameList(function (data) {
            $.each(data || [], function (i, game) {
                $glist.append("<option game_type='" + game.game_code + "'>" + game.game_name + "</option>");
            });
            $glist.change(function () {
                var game = $('option:selected', this).attr('game_type');
                log('get zone for ' + game);
                app.stopDataLoop();
                app.getGameZone(game, function (data) {
                    $zlist.html("");
                    $.each(data, function (i, zone) {
                        $zlist.append("<option zone_id='" + zone.zone_id + "'>" + zone.zone_name + "</option>");
                    });
                    $zlist[0].selectedIndex = 0;
                    $zlist.selectmenu("refresh");
                    $zlist.change();
                });
            });
            $zlist.change(function () {
                var game = $('option:selected', $glist).attr('game_type');
                var zone = $('option:selected', this).attr('zone_id');
                app.startDataLoop(game, zone);
            });
            $glist[0].selectedIndex = 0;
            $glist.selectmenu("refresh");
            $glist.change();
        });
    },
    drawGraph:function (datas) {
        if (datas.length == 0) {
            return;
        }
        this.drawLine(datas);
    },
    /**
     *
     */
    drawLine:function (datas) {
        var dataArr = [];
        var timeArr = [];
        for (var i = 0; i < datas.length; i++) {
            dataArr[i] = datas[i].data[1];
            //dataArr[i]=0;
            //timeArr[i] = (this.interval) * i;
            timeArr[i] = new Date();
            timeArr[i].setTime(datas[i].data[0] * 1000);
            timeArr[i] = timeArr[i].getMinutes();
        }
        RGraph.Clear($("#cvs").get(0));
        RGraph.ObjectRegistry.Clear();
        var line = new RGraph.Line('cvs', dataArr);
        line.Set('chart.title.size', '32px');
        line.Set('chart.title.color', 'green');
        $('#today-info').html('今日最高:' + datas[datas.length - 1].max + ',最低:' + datas[datas.length - 1].min);
        //line.Set('chart.title', '今日最高:' + datas[datas.length - 1].max + ',最低:' + datas[datas.length - 1].min);
        line.Set('chart.linewidth', 3);
        line.Set('chart.labels', timeArr);
        line.Set('chart.background.grid', true);
        line.Set('chart.background.grid.autofit', true);
        line.Set('chart.zoom.factor', 1);
        line.Set('chart.scale.decimals', 1);


        //line.Set('chart.title.yaxis', '人');

        line.Set('chart.title.xaxis.pos',0);
        line.Set('chart.title.xaxis.align','right');

        line.Set('chart.text.size', 20);

        line.Set('chart.scale.formatter', function(obj, num){
            return Math.round(num);
        });
        line.Set('chart.ylabels.inside', true);
        line.Set('chart.xlabels.inside', true);
        line.Set('chart.background.barcolor1', '#ffffff');
        line.Set('chart.background.barcolor2', '#ebf6fa');
        line.Set('chart.colors', ['#4572a7']);
        line.Draw();
    },
    /**
     *
     */
    startDataLoop:function (gid, zid) {
        if ((this.curGame + '_' + this.curZone) != (gid + '_' + zid)) {
            this.stopDataLoop();
            this.zoneData[this.curGame + '_' + this.curZone] = [];
            this.showMsg("正在获取数据...", 1000);
            this.getGameData(gid, zid, function (data) {
                app.hideMsg();
                app.drawGraph(data);
            });
            this.timer = setInterval(function () {
                app.getGameData(gid, zid, function (data) {
                    app.hideMsg();
                    app.drawGraph(data);
                });
            }, app.interval * 1000);
        }
        this.curGame = gid;
        this.curZone = zid;
    },
    /**
     *
     */
    stopDataLoop:function () {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
        RGraph.Clear($("#cvs").get(0));
        RGraph.ObjectRegistry.Clear();
        this.curGame = -1;
        this.curZone = -1;
    },
    reSizeChart:function () {
        $('#cvs').css({
            width:$(window).width(),
            height:$(window).height() - 100
        });
    }
};
///////////////////////////////////////////////////////////////////////////////////
/**
 *the main function
 * @options for config app singleton
 * @callback: app ready callback,when dom and phonegap all ready
 * global objects:
 * app
 */
app.initialize(function () {
    //config urls
    this.urls = {
        logon:'/ws/proxy/login',
        gamedata:'/ws/proxy/gamedata',
        gamezone:'/ws/proxy/gamezone',
        game:'/ws/proxy/game'
    };
    this.urls = {
        logon:'http://192.168.83.186:8124/monitor/login',
        gamedata:'http://192.168.83.186:8124/monitor/data',
        gamezone:'http://192.168.83.186:8124/monitor/zone',
        game:'http://192.168.83.186:8124/monitor/game'
    };
    //check run in alone or not
    var wh = $(window).height();
    if (wh < 450) {//show the guid page
        $('.ui-guid').show();
        return;
    }
    //
    $('#page-logon').on('pageinit', function () {
        $('#logon').click(function () {//logon
            var uname = $('#user').val();
            var pwd = $('#pwd').val();
            //check params
            app.logon(uname, pwd, function (data) {
                if (data && data.res) {
                    $.mobile.changePage('#page-main');
                }
                else{
                    app.showMsg(data.error,1200);
                }
            });
        });
        $("#checkbox1").bind({
            click:function () {
                if ($(this).attr("checked")) {
                    $("#pwd2").val($("#pwd").val());
                    $("#pwd").hide(0);
                    $("#pwd2").show(0);
                } else {
                    $("#pwd").val($("#pwd2").val());
                    $("#pwd2").hide(0);
                    $("#pwd").show(0);
                }
            }
        });
        $(".closeBtn").click(function () {
            $(".step_show_div").hide(0);
        });
    });
    //
    $('#page-main').on('pageinit', function () {
//        app.reSizeChart();
//        $(window).resize(function () {
//            app.reSizeChart();
//        });
    });
});

