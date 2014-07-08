<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="Description" content="">
        <meta name="Keywords" content="">
        <meta name="Author" content="Jonathan Goode">
        <meta name="copyright" content="&copy; Jonathan Goode, <?php echo date('Y'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>GitHub Burndown Chart</title>
        <link rel="stylesheet" type="text/css" media="screen, handheld, print" href="http://fonts.googleapis.com/css?family=Roboto+Slab:400,300,700">
        <link rel="stylesheet" type="text/css" media="screen, handheld, print" href="styles/screen.css">
        <script type="text/javascript" charset="utf-8" src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" charset="utf-8" src="scripts/jquery.jqplot.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="scripts/jqplot.highlighter.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="scripts/jqplot.dateAxisRenderer.min.js"></script>
        <script type="text/javascript" charset="utf-8" src="scripts/jqplot.trendline.min.js"></script>
        <!-- https://bitbucket.org/cleonello/jqplot/pull-request/48/add-label-support-to-canvas-overlay-lines/diff -->
        <script type="text/javascript" charset="utf-8" src="scripts/jqplot.canvasOverlay.js"></script>
        <script type="text/javascript" charset="utf-8" src="scripts/custom.js"></script>
        <script type="text/javascript">
            var owner = '';
            var repo = '';
            var accessToken = '';

            //

			$.fn.sort = function(){
				return this.pushStack([].sort.apply(this, arguments), []);
			};

            //

			function getParameterByName(name, url){
				name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
				var regexS = "[\\?&]" + name + "=([^&#]*)";
				var regex = new RegExp(regexS);
				var results = regex.exec(url);
				if(results == null)
					return "";
				else
					return decodeURIComponent(results[1].replace(/\+/g, " "));
			}

            function gettotalIssues(response){
				var meta = response.meta;

                if(meta.Link && meta.Link[1])
                    totalIssues = getParameterByName('page', meta.Link[1][0]);
                else
                    console.log('No data found for: ' + owner + '/' + repo);
			}

            function loadProgress(){
                var closedAsPercentage = Math.round((numberOfClosedIssues / totalIssues) * 100);
                var openAsPercentage = Math.round((numberOfOpenIssues / totalIssues) * 100);

                $('.closed-sub-bar').css({width: closedAsPercentage + '%'});
                $('.closed').text('Closed / ' + closedAsPercentage + '%' + ' (' + numberOfClosedIssues + ')');
                $('.open').text('Open / ' + openAsPercentage + '%' + ' (' + numberOfOpenIssues + ')');
                $('.progress-container').removeClass('hidden');
            }

			function sortByClosedAt(a, b){
                var date1 = !a.closed_at ? '1970-01-01T00:00:00Z' : a.closed_at;
                var date2 = !b.closed_at ? '1970-01-01T00:00:00Z' : b.closed_at;

                return date1 > date2 ? 1 : -1;
			}

            //

            var perPage = 30,
                minimumYAxisThreshold = 7,
                totalPages = 0,
                totalIssues = 0,
                numberOfClosedIssues = 0,
                numberOfOpenIssues = 0,
                ajaxSuccesses = 0;
            var plotData = [], jsonData = [], issueData = [];
            var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var plot;

            //

			$(window).load(function(){
				totalPages = Math.ceil(totalIssues / perPage);

				for(var p = 1; p <= totalPages; p++){
                    $.getJSON('https://api.github.com/repos/' + owner + '/' + repo + '/issues?access_token=' + accessToken + '&state=all&sort=created&direction=asc&milestone=*&page=' + p, function(data){
                        if(data[0] !== undefined)
                            jsonData = jsonData.concat(data); //combine JSON object data
                    });
                }

                $(document).ajaxComplete(function(){
                    ajaxSuccesses++;

                    if(ajaxSuccesses == totalPages){
                        jsonData = $(jsonData).sort(sortByClosedAt);

                        $.each(jsonData, function(i, item){
                            if(!jsonData[i].closed_at)
                                numberOfOpenIssues++;
                            else
                                numberOfClosedIssues++;
                        });

                        totalIssues = numberOfClosedIssues + numberOfOpenIssues;

                        //

                        numberOfClosedIssues = 0; //reset in order to increment through

                        $.each(jsonData, function(i, item){
                            if(jsonData[i].closed_at){
                                numberOfClosedIssues++;
                                var d = new Date(jsonData[i].closed_at);
                                var day = d.getDate();
                                var month = monthNames[d.getMonth()];
                                var year = d.getFullYear().toString().substr(2, 2);
                                plotData.push([day + '-' + month + '-' + year, (totalIssues - numberOfClosedIssues)]);
                                issueData.push('#' + jsonData[i].number + ': ' + jsonData[i].title);
                            }
                        });

                        if(plotData.length){
                            plot = $.jqplot('chart', [plotData], {
                                //animate: !$.jqplot.use_excanvas,
                                title: 'Burndown Chart for: &quot;<a title="View Repo on GitHub" class="repo-link" href="#" target="_blank">' + owner + '/' + repo + '</a>&quot;',
                                seriesDefaults: {
                                    shadow: false,
                                    trendline: {
                                        color: '#736451',
                                        linePattern: 'dashed',
                                        shadow: false
                                    },
                                    markerOptions: {
                                        shadow: false
                                    }
                                },
                                seriesColors: ['#736451'],
                                grid: {
                                    gridLineColor: '#f5efe2',
                                    borderColor: '#f5efe2',
                                    shadow: false
                                },
                                axes: {
                                    xaxis: {
                                        renderer: $.jqplot.DateAxisRenderer,
                                        //max: '1-Dec-14', //delivery date  of last milestone?
                                        tickOptions: {
                                            formatString: '%b&nbsp;%#d&nbsp;\'%y'
                                        }
                                    },
                                    yaxis: {
                                        min: (numberOfClosedIssues <= minimumYAxisThreshold ? (numberOfOpenIssues - 1) : null),
                                        tickInterval: 1,
                                        tickOptions: {
                                            formatString: '%d'
                                        }
                                    }
                                },
                                highlighter: {
                                    show: true,
                                    fadeTooltip: false,
                                    sizeAdjust: 7.5,
                                    tooltipLocation: 'ne',
                                    tooltipContentEditor: function(str, seriesIndex, pointIndex){
                                        return issueData[pointIndex];
                                    }
                                },
                                cursor: {
                                    show: false
                                },
                                canvasOverlay: {
                                    show: (numberOfClosedIssues <= minimumYAxisThreshold ? false : true),
                                    objects: [
                                        {dashedHorizontalLine: {
                                            y: numberOfOpenIssues,
                                            lineWidth: 1,
                                            color: '#dcd7cb',
                                            shadow: false,
                                            label: numberOfOpenIssues,
                                            showLabel: true,
                                            labelLocation: 'e',
                                            labelOffset: 13,
                                            labelAnchor: 'end'
                                        }}
                                    ]
                                }
                            });

                            $('.jqplot-title').on('click', function(){
                                $(this).find('.repo-link').attr('href', 'http://github.com/' + owner + '/' + repo + '/');
                            });

                            loadProgress();
                        }
                    }
                });
            });

            $(document).ready(function(){
                var script = document.createElement('script');
                script.src = 'https://api.github.com/repos/' + owner + '/' + repo + '/issues?access_token=' + accessToken + '&state=all&milestone=*&per_page=1&callback=gettotalIssues';
                document.getElementsByTagName('head')[0].appendChild(script);

                $('#chart').bind('jqplotDataClick',
                    function(ev, seriesIndex, pointIndex, data){
                        $(this).trigger('jqplotHighlighterUnhighlight');

                        //in order to hide the highlighter and its marker then reinitialise
                        $('.jqplot-highlighter-tooltip, .jqplot-highlight-canvas').remove();
                        delay(function(){
                            plot.replot();
                        }, 100);

                        var issueNumber = /#([^:]+):/.exec(issueData[pointIndex])[1];
                        window.open('https://github.com/' + owner + '/' + repo + '/issues/' + issueNumber, '_blank');
                    }
                )

                $(window).bind('resize', function(event, ui){
                    if(plot)
                        plot.replot({resetAxes: ['xaxis']});
                });

                $('#chart').bind('jqplotHighlighterHighlight',
                    function(ev, seriesIndex, pointIndex, data){
                        $(this).css({'cursor': 'pointer'});
                    }
                )

                $('#chart').bind('jqplotHighlighterUnhighlight',
                    function(ev, seriesIndex, pointIndex, data){
                        $(this).css({'cursor': 'auto'});
                    }
                )

                $.jqplot.config.enablePlugins = true; //load trend line

                $('.closed').on('click', function(){
                    $(this).trigger('mouseleave');
                    $(this).attr('href', 'http://github.com/' + owner + '/' + repo + '/issues?page=1&state=closed&milestone=*');
                });

                $('.red').on('click', function(event){
                    event.stopPropagation();
                    $(this).trigger('mouseleave');
                    window.open('http://github.com/' + owner + '/' + repo + '/issues?page=1&state=closed&milestone=*', '_blank');
                });

                $('.open').on('click', function(){
                    $(this).trigger('mouseleave');
                    $(this).attr('href', 'http://github.com/' + owner + '/' + repo + '/issues?page=1&state=open&milestone=*');
                });

                $('.green').on('click', function(){
                    $(this).trigger('mouseleave');
                    window.open('http://github.com/' + owner + '/' + repo + '/issues?page=1&state=open&milestone=*', '_blank');
                });

                $('.red').on({
                    mouseenter: function(event){
                        event.stopPropagation();
                        $(this).addClass('darker-red');
                        $('.red').removeClass('darker-green');
                    },
                    mouseleave: function(){
                        $(this).removeClass('darker-red');
                        $('.green').addClass('darker-green');
                    }
                });

                $('.green').on({
                    mouseenter: function(){
                        $(this).addClass('darker-green');
                    },
                    mouseleave: function(event){
                        event.stopPropagation();
                        $(this).removeClass('darker-green');
                    }
                });
            });
        </script>
    </head>
    <body>
        <div id="chart"></div>
        <div class="progress-container hidden">
            <div class="open-bar green" title="View Open Issues">
                <div class="closed-sub-bar red" title="View Closed Issues"></div>
            </div>
            <a href="#" target="_blank" class="stats closed" title="View Closed Issues"></a>
            <a href="#" target="_blank" class="stats open" title="View Open Issues"></a>
        </div>
    </body>
</html>