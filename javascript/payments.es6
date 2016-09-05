

function startPaymentsApp($, base_url='/') {
    const AppContainer = $('.tsh-container');
    const PaginationContainer = $('.tsh-pagination');
    const PagesContainer = $('.tsh-table>tbody');
    const SupplierInput = $('input[name=supplier]');
    const CostRatingSelect = $('select[name=cost_rating]');
    const json_url = base_url + 'json';

    AppContainer.on('click', 'a', (e) => {
        const url = $(e.target).attr('href');
        e.preventDefault();
        $.getJSON(json_url + url, (data) => {
            update(data);
            history.pushState(data, data.title, url);
        });
    });

    AppContainer.on('submit', '.tsh-form', (e) => {
        const url = '?' + $(e.target).serialize();
        e.preventDefault();
        $.getJSON(json_url + url, (data) => {
            update(data);
            history.pushState(data, data.title, url);
        });
    });

    window.onpopstate = function(e) {
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
        const rows = [];
        if (data.query_info.length > 0) {
            rows.push(tag_tr_info(data.query_info)[0]);
        }
        for(const payment of data.payments) {
            rows.push(tag_tr_payments(payment)[0]);
        }
        return $(rows);
    }

    function parseLinks(data) {
        if (data.total_pages == 0) {
            return '';
        }
        const prev = tag_pagination_item(data.prev_link, 'tsh-pagination-btn tsh-pagination-btn-prev');
        const next = tag_pagination_item(data.next_link, 'tsh-pagination-btn tsh-pagination-btn-next');
        const links = [];
        links.push(prev[0]);
        for(const link of data.page_links) {
            links.push(tag_pagination_item(link)[0]);
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
        const tag = tag_td('');
        for(let i = 1; i < 6; i++) {
            tag.append(tag_span(i, 'tsh-rating-ico tsh-rating-ico-' + i + (i <= rating && ' on')));
        }
        return tag;
    }

    function tag_tr_payments(tr) {
        const tag = tag_tr(tr);
        tag.append(tag_td(tr.supplier));
        tag.append(tag_td_cost_rating(tr.cost_rating));
        tag.append(tag_td(tr.ref));
        tag.append(tag_td('£' + tr.amount));
        return tag;
    }

    function tag_tr_info(text) {
        return  $('<tr>').append($('<td>').attr('colspan', 4).text(text));
    }

    function tag_a(a) {
        const tag =  $('<a>')
            .attr('href', a.url)
            .html(a.text);
        if(a.active) {
            tag.addClass('active');
        }
        if(a.disabled) {
            tag.prop('disabled')
        }
        return tag;
    }

    function tag_pagination_item(item, cssclass="tsh-pagination-btn") {
        return $('<li>').append(tag_a(item).addClass(cssclass));
    }
}

export startPaymentsApp;