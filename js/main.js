function showLoader() {
    $('#loader').show();
}

function hideLoader() {
    $('#loader').fadeOut();
}

function clearData() {
    $('#result').children().remove();
}

function loadData(datas) {
    for (var i = 0; i < datas.length; i++) {
        var table = $.parseHTML
            ('<table class="table table-striped table-bordered"></table>');
        var tbody = $.parseHTML('<tbody>');

        var data = datas[i];
        if (data.length == 0) {
            $(tbody).text('No data');
        } else {
            var keys = Object.keys(data[0]);
            // make head
            var thHtmlStr = '<tr>';
            for (var j = 0; j < keys.length; j++) {
                thHtmlStr += '<th>' + keys[j] + '</th>';
            }
            thHtmlStr += '</tr>';

            $(tbody).append($.parseHTML(thHtmlStr));

            for (var k = 0; k < data.length; k++) {
                var tdHtmlStr = '<tr>'
                for (var j = 0; j < keys.length; j++) {
                    tdHtmlStr += '<td>' + data[k][keys[j]] + '</td>';
                };
                tdHtmlStr += '</tr>';
                $(tbody).append($.parseHTML(tdHtmlStr));
            };
        }
        $(table).append(tbody);
        $('#result').append(table);
    }
}

function makeNavItem(reportNo, intro, auto) {
    var htmlStr = 
                 '<a href="#" class="list-group-item" data-report="' + reportNo + '" data-intro="' + intro + '" title="' + intro + '">\
                    <h4 class="list-group-item-heading">' + (isNaN(reportNo)? reportNo : 'Report ' + reportNo) + '</h4>\
                    <p class="list-group-item-text">' + intro.substr(0, 60) + '...</p>\
                  </a>';
    var ele = $.parseHTML(htmlStr);
    if (auto) $(ele).attr('data-auto', 'true');
    return ele;
}

var reports = [
    {
        'no': 'Customer',
        'intro': 'Add customer'
    },
    {
        'no': 'Item',
        'intro': 'Add item'
    },
    {
        'no': 'Order',
        'intro': 'Add order'
    },
    {
        'no': 'Order Item',
        'intro': 'Add items to order'
    },
    {
        'no': 'Order Query',
        'intro': 'See order detail'
    },
    {
        'no': 1,
        'intro': 'Find all the stores along with city, state, phone, description, size, weight and unit price that hold a particular item of stock.'
    },
    {
        'no': 2,
        'intro': 'Find all the orders along with customer name and order date that can be fulfilled by a given store.'
    },
    {
        'no': 3,
        'intro': 'Find all stores along with city name and phone that hold items ordered by a given customer.'
    },
    {
        'no': 4,
        'intro': 'Find the headquarter address along with city and state of all stores that hold stocks of an item above a particular level.'
    },
    {
        'no': 5,
        'intro': 'For each customer order, show the items ordered along with description, store id and city name and the stores that hold the items.',
        'auto': true
    },
    {
        'no': 6,
        'intro': 'Find the city and the state in which a given customer lives.'
    },
    {
        'no': 7,
        'intro': 'Find the stock level of a particular item in all stores in a particular city.'
    },
    {
        'no': 8,
        'intro': 'Find the items, quantity ordered, customer, store and city of an order.'
    },
    {
        'no': 9,
        'intro': 'Find a list of employee customers with name and discount rate.',
        'auto': true
    },
    {
        'no': 10,
        'intro': 'Find a list of non-employee customers with name and post address.',
        'auto': true
    },
    {
        'no': 11,
        'intro': 'Find a list of all customers sorted by sales volume in ascending order.',
        'auto': true
    }
];

function onNavItemClick(e) {
    e.preventDefault();
    if ($(this).hasClass('active')) return false;
    // scroll to top
    $('html, body').animate({scrollTop : 0},300);
    // update view
    var reportNo = $(this).attr('data-report');
    $('#report-nav-list > a').removeClass('active');
    $(this).addClass('active');
    $('#report-title').text($(this).children('h4').text());
    $('.report-control').hide();
    $('.report-control[data-report="' + reportNo + '"]').fadeIn();
    $('#report-intro').fadeIn().text($(this).attr('data-intro'));
    clearData();
    if ($(this).attr('data-auto'))
        onFormSubmit(e, reportNo);
}

function onFormSubmit(e, reportNo) {
    e.preventDefault();
    // get params
    var params = {};
    params['report'] = reportNo
        || $(this).parent().attr('data-report');
    var controls = $(this).parent().find('.form-group > *[name]');
    for (var i = controls.length - 1; i >= 0; i--) {
        params[$(controls[i]).attr('name')] = $(controls[i]).val();
    };
    console.log(params);
    showLoader();
    $.ajax({
        url: 'api.php',
        type: 'json',
        data: params,
        method: 'POST',
        success: function(data) {
            hideLoader();
            clearData();
            loadData(data);
        },
        error: function() {
            hideLoader();
        }
    });
}

function addDataList(params, name) {
    $.get('list.php?table=' + params, function(data) {
        var datalist = $.parseHTML('<datalist id="' + params + '-list' + '"></datalist>');
        for (var i = 0; i < data.length; i++) {
            $(datalist).append($.parseHTML('<option value="' + data[i] + '">'));
        };
        $('body').append(datalist);
        $('input[name="' + name + '"]').attr('list', params + '-list');
    });
}

$(function() {
    // bind form
    $('.report-control a.btn').click(onFormSubmit);
    // generate nav items
    for (var i = 0; i < reports.length; i++) {
        var item 
            = makeNavItem(reports[i]['no'], reports[i]['intro'], reports[i]['auto']);
        $(item).click(onNavItemClick);
        $('#report-nav-list').append(item);
    };
    // only show welcome message
    $('.report-control').hide();
    $('.report-control[data-report="welcome"]').fadeIn();
    // generate show all button
    $('a.show-item').text('show all').attr('href', 'all.php?table=Item')
        .attr('target', '_blank');
    $('a.show-customer').text('show all').attr('href', 'all.php?table=Customer')
        .attr('target', '_blank');
    $('a.show-store').text('show all').attr('href', 'all.php?table=Store')
        .attr('target', '_blank');
    $('a.show-order').text('show all').attr('href', 'all.php?table=Orders')
        .attr('target', '_blank');
    // add datalist
    addDataList('Item', 'item_id');
    addDataList('Store', 'store_id');
    addDataList('Customer', 'customer_id');
    addDataList('City', 'city_id');
    addDataList('Orders', 'order_id');
});