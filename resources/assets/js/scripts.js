var cur_page = 1;
var total_pages = 0;
var total = 0;
var dataReceived = [];
function getFiltered(page) {
    cur_page = page;
    if (cur_page<1) {
        cur_page = 1;
    }
    if (total_pages>0 && cur_page>total_pages) {
        cur_page = total_pages;
    }
    var size = parseInt($('select[name=size]').val());
    var from = (cur_page-1) * size;
    $('input[name=from]').val(from);
    $.ajax({
        method: 'POST',
        url: '/filter',
        data: $('.skills').serialize(),
        success: function(data) {
            total = data.total;
            dataReceived = data.hits;
            $('.list').html('');
            $('.total').html('Show ' + $('input[name=from]').val() + ' - ' + parseInt(from+size) + ' of ' + total);
            execRenderMethod(true);
            renderPager();
        }
    });
}
function renderPager() {
    if (total) {
        $(".pagination").paging(total, {
            format: '[< ncnnn! >]',
            perpage: parseInt($('select[name=size]').val()),
            lapping: 0,
            page: cur_page,
            onSelect: function() {
                total_pages = this.pages;
            },
            onFormat: function (type) {
                var active = this.value == cur_page ? 'active' : '';
                switch (type) {
                    case 'block': // n and c
                        return '<li class="'+active+'"><a href="#" onclick="getFiltered(' + this.value + ')">' + this.value + '</a></li>';
                    case 'next':
                        return '<li><a href="#" onclick="getFiltered(cur_page+1)" title="Next">&raquo;</a></li>';
                    case 'prev':
                        return '<li><a href="#" onclick="getFiltered(cur_page-1)" title="Prev">&laquo;</a></li>';
                    case 'first':
                        return '';
                        // return '<li><a href="#" onclick="getFiltered(1)" title="First">First</a></li>';
                    case 'last':
                        return '';
                        // return '<li><a href="#" onclick="getFiltered(total_pages)" title="Last">Last</a></li>';
                }
            }
        });
    }

}
function setDirection(value) {
    if (value=='') {
        $('select[name=dir]').attr('disabled', 'disabled');
    } else {
        $('select[name=dir]').removeAttr('disabled');
    }
}

function drawlist() {
    $.each(dataReceived, function(key, item) {
        $('div.list').loadTemplate($('#template'), item, { append: true });
    });
}

function drawcharts() {
    if (!total) {
        return;
    }
    var data = [];
    var counters = {};
    $.each(dataReceived, function(key, item) {
        $.each(item.skills, function (skey, skill) {
            if (typeof(counters[skill]) == 'undefined') {
                counters[skill] = {counter: 0};
             }
            counters[skill].counter++;
        });
    });
    $.each(counters, function(key, item) {
        data.push({
            name: key,
            y: item.counter
        });
    });
    $('.charts-container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Collected Skills'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b> (<b>{point.percentage:.1f}%</b>)'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Found',
            colorByPoint: true,
            data: data
        }]
    });
}

function drawdashboard() {
    // do nothing
}

var map;
var markers = [];

function drawmap() {
    deleteMarkers();
    $.each(dataReceived, function(key, item) {
        addMarker(item);
    });
    map = new google.maps.Map(document.getElementById('gmap'), {
        zoom: 2,
        center: {lat: 0, lng: 0}
    });
    setMapOnAll(map);
}
// Adds a marker to the map and push to the array.
function addMarker(item) {
    var marker = new google.maps.Marker({
        position: {
            lat: item.location.lat,
            lng: item.location.lon
        },
        title: item.name,
        map: map
    });
    markers.push(marker);
    var contentString = '<div class="gname">' + item.name + '</div>' +
        '<div class="gip">IP: ' + item.ip + '</div>' +
        '<div class="gcity">City: ' + item.city + '</div>' +
        '<div class="gtz">Time Zone: ' + item.timezone + '</div>' +
        '<div class="gcoords">Coordinates: (' + item.location.lon + ', ' + item.location.lat + ')</div>' ;
    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });
    google.maps.event.addListener(marker, 'click', function() {
        if(!marker.open){
            infowindow.open(map,marker);
            marker.open = true;
        }
        else{
            infowindow.close();
            marker.open = false;
        }
        google.maps.event.addListener(map, 'click', function() {
            infowindow.close();
            marker.open = false;
        });
    });
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
    setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    clearMarkers();
    markers = [];
}
function execRenderMethod(check) {
    var target = $("a[aria-expanded='true']").attr('href');
    if (typeof(target)=='undefined' || (check && target=='#dashboard')) {
        target = '#list';
        $('.nav-tabs a[href="' + target + '"]').tab('show');
    }
    var method = 'draw' + target.substr(1);
    eval(method + '()');
}

$(document).ready(function() {

    $('button[name="get-filtered"]').on('click', function() {
        getFiltered(0);
    });
    $('select[name="sort"]').on('change', function(e) {
       setDirection($(e.target).val())
    });
    // $.addTemplateFormatter('SkillFormatter', function(value) {
    //     return '<span class="skill">'+value+'</span>';
    // });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        execRenderMethod(false);
    });

});

