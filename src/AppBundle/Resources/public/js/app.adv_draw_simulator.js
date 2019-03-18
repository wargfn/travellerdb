(function app_adv_draw_simulator(adv_draw_simulator, $)
{

    var advdeck = null,
            initial_size = 0,
            draw_count = 0,
            container = null;

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.reset = function reset()
    {
        $(container).empty();
        adv_draw_simulator.on_data_loaded();
        draw_count = 0;
        adv_draw_simulator.update_odds();
        $('#draw-adv-simulator-clear').prop('disabled', true);
    };

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.on_dom_loaded = function on_dom_loaded()
    {
        $('#table-adv-draw-simulator').on('click', 'button.btn', adv_draw_simulator.handle_click);
        $('#table-adv-draw-simulator').on('click', 'img, div.card-proxy', adv_draw_simulator.toggle_opacity);
        container = $('#table-adv-draw-simulator-content');

        $('#advOddsModal').on({change: adv_draw_simulator.compute_odds}, 'input');
    }

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.compute_odds = function compute_odds()
    {
        var inputs = {};
        $.each(['N', 'K', 'n', 'k'], function (i, key)
        {
            inputs[key] = parseInt($('#odds-calculator-' + key).val(), 10) || 0;
        });
        $('#adv-odds-calculator-p').text(Math.round(100 * app.hypergeometric.get_cumul(inputs.k, inputs.N, inputs.K, inputs.n)));
    }

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.on_data_loaded = function on_data_loaded()
    {
        advdeck = [];

        var cards = app.deck.get_adventure_deck();
        cards.forEach(function (card)
        {
            for(var ex = 0; ex < card.indeck; ex++) {
                advdeck.push(card);
            }
        });
        initial_size = advdeck.length;
    }

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.update_odds = function update_odds()
    {
        for(var i = 1; i <= 3; i++) {
            var odd = app.hypergeometric.get_cumul(1, initial_size, i, draw_count);
            $('#adv-draw-simulator-odds-' + i).text(Math.round(100 * odd));
        }
    }

    /**
     * @memberOf adv_draw_simulator
     * @param draw integer
     */
    adv_draw_simulator.do_draw = function do_draw(draw)
    {
        for(var pick = 0; pick < draw && advdeck.length > 0; pick++) {
            var rand = Math.floor(Math.random() * advdeck.length);
            var spliced = advdeck.splice(rand, 1);
            var card = spliced[0];
            var card_element;
            if(card.imagesrc) {
                card_element = $('<img src="' + card.imagesrc + '">');
            } else {
                card_element = $('<div class="card-proxy"><div>' + card.label + '</div></div>');
            }
            container.append(card_element);
            draw_count++;
        }
        adv_draw_simulator.update_odds();
    }

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.handle_click = function handle_click(event)
    {

        event.preventDefault();

        var command = $(this).data('command');
        $('[data-command=clear]').prop('disabled', false);
        if(command === 'clear') {
            adv_draw_simulator.reset();
            return;
        }
        if(event.shiftKey) {
            adv_draw_simulator.reset();
        }
        var draw;
        if(command === 'all') {
            draw = advdeck.length;
        } else {
            draw = command;
        }

        if(isNaN(draw))
            return;
        adv_draw_simulator.do_draw(draw);

    };

    /**
     * @memberOf adv_draw_simulator
     */
    adv_draw_simulator.toggle_opacity = function toggle_opacity(event)
    {
        $(this).css('opacity', 1.5 - parseFloat($(this).css('opacity')));
    };

})(app.adv_draw_simulator = {}, jQuery);
