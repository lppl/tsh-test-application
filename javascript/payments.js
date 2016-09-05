'use strict';

function startPaymentsApp($) {
    var base_url = arguments.length <= 1 || arguments[1] === undefined ? '/' : arguments[1];

    var AppContainer = $('.tsh-container');
    var PaginationContainer = $('.tsh-pagination');
    var PagesContainer = $('.tsh-table>tbody');
    var SupplierInput = $('input[name=supplier]');
    var CostRatingSelect = $('select[name=cost_rating]');
    var json_url = base_url + 'json';

    AppContainer.on('click', 'a', function (e) {
        var url = $(e.target).attr('href');
        e.preventDefault();
        $.getJSON(json_url + url, function (data) {
            update(data);
            history.pushState(data, data.title, url);
        });
    });

    AppContainer.on('submit', '.tsh-form', function (e) {
        var url = '?' + $(e.target).serialize();
        e.preventDefault();
        $.getJSON(json_url + url, function (data) {
            update(data);
            history.pushState(data, data.title, url);
        });
    });

    window.onpopstate = function (e) {
        if (e.state.payments) {
            e.preventDefault();
            update(e.state);
        }
    };

    function update(data) {
        PagesContainer.html(parsePayments(data));
        PaginationContainer.html(parseLinks(data));
        updateForm(data);
    }

    function parsePayments(data) {
        var rows = [];
        if (data.query_info.length > 0) {
            rows.push(tag_tr_info(data.query_info)[0]);
        }
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = data.payments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var payment = _step.value;

                rows.push(tag_tr_payments(payment)[0]);
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }

        return $(rows);
    }

    function parseLinks(data) {
        if (data.total_pages == 0) {
            return '';
        }
        var prev = tag_pagination_item(data.prev_link, 'tsh-pagination-btn tsh-pagination-btn-prev');
        var next = tag_pagination_item(data.next_link, 'tsh-pagination-btn tsh-pagination-btn-next');
        var links = [];
        links.push(prev[0]);
        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
            for (var _iterator2 = data.page_links[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                var link = _step2.value;

                links.push(tag_pagination_item(link)[0]);
            }
        } catch (err) {
            _didIteratorError2 = true;
            _iteratorError2 = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion2 && _iterator2.return) {
                    _iterator2.return();
                }
            } finally {
                if (_didIteratorError2) {
                    throw _iteratorError2;
                }
            }
        }

        links.push(next[0]);
        return $(links);
    }

    function updateForm(data) {
        SupplierInput.val(data.query_supplier);
        CostRatingSelect.children().filter('[value=0]').html(data.query_cost_rating === 0 ? 'Select pound rating' : 'All pound ratings');
        CostRatingSelect.val(data.query_cost_rating);
    }

    function tag_tr(tr) {
        return $('<tr>').addClass('tsh-table-row');
    }

    function tag_td(text) {
        return $('<td>').addClass('tsh-table-column').text(text);
    }

    function tag_span(text, cssclass) {
        return $('<span>').addClass(cssclass).text(text);
    }

    function tag_td_cost_rating(rating) {
        var tag = tag_td('');
        for (var i = 1; i < 6; i++) {
            tag.append(tag_span(i, 'tsh-rating-ico tsh-rating-ico-' + i + (i <= rating && ' on')));
        }
        return tag;
    }

    function tag_tr_payments(tr) {
        var tag = tag_tr(tr);
        tag.append(tag_td(tr.supplier));
        tag.append(tag_td_cost_rating(tr.cost_rating));
        tag.append(tag_td(tr.ref));
        tag.append(tag_td('£' + tr.amount));
        return tag;
    }

    function tag_tr_info(text) {
        return $('<tr>').append($('<td>').attr('colspan', 4).text(text));
    }

    function tag_a(a) {
        var tag = $('<a>').attr('href', a.url).html(a.text);
        if (a.active) {
            tag.addClass('active');
        }
        if (a.disabled) {
            tag.prop('disabled');
        }
        return tag;
    }

    function tag_pagination_item(item) {
        var cssclass = arguments.length <= 1 || arguments[1] === undefined ? "tsh-pagination-btn" : arguments[1];

        return $('<li>').append(tag_a(item).addClass('tsh-pagination-btn'));
    }
}

//# sourceMappingURL=payments.js.map