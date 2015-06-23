;/**
 * @category   FZ
 * @package    FZ_GoogleAnalytics
 * @author     Francesco Zoccarato <francesco.zoccarato@gmail.com>
 */

if (!window.FZ)
{
    var FZ = {};
}

if (!window.FZ.GoogleAnalitycs)
{
    FZ.GoogleAnalitycs = {};
}


/* realtime */
FZ.GoogleAnalitycs.Realtime = {};

/* init variables with defaults */
FZ.GoogleAnalitycs.Realtime.debug = false;
FZ.GoogleAnalitycs.Realtime.url = BASE_URL;
FZ.GoogleAnalitycs.Realtime.autoplay = false;
FZ.GoogleAnalitycs.Realtime.timing = 10 * 1000;
FZ.GoogleAnalitycs.Realtime.playText = 'Play';
FZ.GoogleAnalitycs.Realtime.pauseText = 'Pause';
FZ.GoogleAnalitycs.Realtime.errorText = 'Error retrieving realtime data';
FZ.GoogleAnalitycs.Realtime.containerId = 'diagram_tab_realtime';
FZ.GoogleAnalitycs.Realtime.timeout = null;

FZ.GoogleAnalitycs.Realtime.startAutoplay = function ()
{
    if (FZ.GoogleAnalitycs.Realtime.autoplay)
    {
        FZ.GoogleAnalitycs.Realtime.log('autoplayGaRealtime');
        $$('.ga-realtime-action').each(function (item)
        {
            item.addClassName('disabled');
        });
        FZ.GoogleAnalitycs.Realtime.timeout = window.setInterval(FZ.GoogleAnalitycs.Realtime.play, FZ.GoogleAnalitycs.Realtime.timing);
        $(FZ.GoogleAnalitycs.Realtime.containerId).observe('click', FZ.GoogleAnalitycs.Realtime.init);
    }
};

FZ.GoogleAnalitycs.Realtime.log = function (str)
{
    if (FZ.GoogleAnalitycs.Realtime.debug)
    {
        console.log(str);
    }
};

FZ.GoogleAnalitycs.Realtime.toggle = function ()
{
    FZ.GoogleAnalitycs.Realtime.log('toggle');
    if (!$('ga-realtime-toggle-play-pause').hasClassName('disabled'))
    {
        if ($('diagram_tab_trends_container').hasClassName('playing'))
        {
            FZ.GoogleAnalitycs.Realtime.pause();
            $('ga-realtime-toggle-play-pause').innerHTML = '<span><span>' + FZ.GoogleAnalitycs.Realtime.playText + '</span></span>';
        }
        else
        {
            FZ.GoogleAnalitycs.Realtime.play();
            $('diagram_tab_trends_container').addClassName('playing');
            $('ga-realtime-toggle-play-pause').innerHTML = '<span><span>' + FZ.GoogleAnalitycs.Realtime.pauseText + '</span></span>';
        }
    }
};

FZ.GoogleAnalitycs.Realtime.pause = function ()
{
    FZ.GoogleAnalitycs.Realtime.log('pauseGaRealtime');
    FZ.GoogleAnalitycs.Realtime.resetTimer();
    $('movingBallG-loader').addClassName('disabled');
    $('diagram_tab_trends_container').removeClassName('playing');
    $('ga-realtime-toggle-play-pause').innerHTML = '<span><span>' + FZ.GoogleAnalitycs.Realtime.playText + '</span></span>';
};

FZ.GoogleAnalitycs.Realtime.init = function ()
{
    FZ.GoogleAnalitycs.Realtime.log('initGaRealtime');
    $(FZ.GoogleAnalitycs.Realtime.containerId).addClassName('active');
    FZ.GoogleAnalitycs.Realtime.play(false);
};

FZ.GoogleAnalitycs.Realtime.play = function (autorefresh)
{
    if (autorefresh == undefined)
    {
        autorefresh = true;
    }
    FZ.GoogleAnalitycs.Realtime.log('playGaRealtime');
    var containerId = FZ.GoogleAnalitycs.Realtime.containerId;
    FZ.GoogleAnalitycs.Realtime.resetTimer();
    if ($(containerId).hasClassName('active')) {
        $$('.ga-realtime-action').each(function (item)
        {
            item.addClassName('disabled');
        });
        new Ajax.Request(FZ.GoogleAnalitycs.Realtime.url,
                {
                    method: 'get',
                    loaderArea: false,
                    onSuccess: function (response)
                    {
                        if (200 === response.status)
                        {
                            if(response.responseText.indexOf('ID-overviewCounterPanel') === -1)
                            {
                                FZ.GoogleAnalitycs.Realtime.realtimeTimeout = null;
                                document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Realtime.errorText;
                                FZ.GoogleAnalitycs.Realtime.pause();
                            }
                            else{
                                document.getElementById(containerId + '_container').innerHTML = response.responseText;
                                if (autorefresh)
                                {
                                    FZ.GoogleAnalitycs.Realtime.timeout = window.setInterval(FZ.GoogleAnalitycs.Realtime.play, FZ.GoogleAnalitycs.Realtime.timing);
                                    $('ga-realtime-toggle-play-pause').innerHTML = '<span><span>' + FZ.GoogleAnalitycs.Realtime.pauseText + '</span></span>';
                                }
                                else
                                {
                                    FZ.GoogleAnalitycs.Realtime.pause();
                                }
                            }
                        }
                        else
                        {
                            FZ.GoogleAnalitycs.Realtime.timeout = null;
                            document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Realtime.errorText;
                        }
                        $$('.ga-realtime-action').each(function (item)
                        {
                            item.removeClassName('disabled');
                        });
                    },
                    onFailure: function ()
                    {
                        FZ.GoogleAnalitycs.Realtime.realtimeTimeout = null;
                        document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Realtime.errorText;
                        FZ.GoogleAnalitycs.Realtime.pause();
                    }
                });
    }
    else
    {
        if (FZ.GoogleAnalitycs.Realtime.timeout !== undefined || FZ.GoogleAnalitycs.Realtime.timeout !== null)
        {
            clearTimeout(FZ.GoogleAnalitycs.Realtime.timeout);
        }
    }
};

FZ.GoogleAnalitycs.Realtime.resetTimer = function ()
{
    if (FZ.GoogleAnalitycs.Realtime.timeout !== undefined || FZ.GoogleAnalitycs.Realtime.timeout !== null)
    {
        clearTimeout(FZ.GoogleAnalitycs.Realtime.timeout);
    }
};



/* trends */
FZ.GoogleAnalitycs.Trends = {};

/* init variables with defaults */
FZ.GoogleAnalitycs.Trends.debug = false;
FZ.GoogleAnalitycs.Trends.autoplay = false;
FZ.GoogleAnalitycs.Trends.url = BASE_URL;
FZ.GoogleAnalitycs.Trends.report = 'montly';
FZ.GoogleAnalitycs.Trends.containerId = 'diagram_tab_trends';
FZ.GoogleAnalitycs.Trends.errorText = 'Error retrieving trends data';

FZ.GoogleAnalitycs.Trends.init = function() 
{
    $(FZ.GoogleAnalitycs.Trends.containerId).addClassName('active');
    FZ.GoogleAnalitycs.Trends.update();
};

FZ.GoogleAnalitycs.Trends.update = function(report) 
{
    if (report !== undefined) {
        FZ.GoogleAnalitycs.Trends.report = report;
    }
    var containerId = FZ.GoogleAnalitycs.Trends.containerId;
    if ($(containerId).hasClassName('active')) 
    {
        $$('.ga-trends-report').each(function (item) 
        {
            item.addClassName('disabled');
        });
        new Ajax.Request(FZ.GoogleAnalitycs.Trends.url, 
        {
            method: 'get',
            loaderArea: false,
            parameters: {report: FZ.GoogleAnalitycs.Trends.report},
            onSuccess: function (response)
            {
                if (200 == response.status) 
                {
                    if(response.responseText.indexOf('ID-overviewtrends') === -1)
                    {
                        document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Trends.errorText;
                    }
                    else
                    {
                        document.getElementById(containerId + '_container').innerHTML = response.responseText;
                    }
                }
                else {
                    document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Trends.errorText;
                }
                $$('.ga-trends-report').each(function (item) 
                {
                    item.removeClassName('disabled');
                });
                $('ga-trends-' + FZ.GoogleAnalitycs.Trends.report + '-report').addClassName('disabled');
            },
            onFailure: function () 
            {
                document.getElementById(containerId + '_container').innerHTML = FZ.GoogleAnalitycs.Trends.errorText;
            }
        });
    }
}
;