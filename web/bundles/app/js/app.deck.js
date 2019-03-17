/* global _, app, Translator */

(function app_deck(deck, $)
{

    var date_creation,
            date_update,
            description_md,
            id,
            name,
            tags,
            faction_code,
            faction_name,
            unsaved,
            user_id,
            problem_labels = _.reduce(
                    ['too_few_cards', 'too_many_copies', 'invalid_cards', 'too_many_adventure_cards','too_few_adventure_cards','too_many_captains_cards','too_few_captains_cards'],
                    function (problems, key)
                    {
                        problems[key] = Translator.trans('decks.problems.' + key);
                        return problems;
                    },
                    {}),
            header_tpl = _.template('<h5><span class="icon <%= code %>"></span> <%= name %> (<%= quantity %>)</h5>'),
            card_line_tpl = _.template('<span class="icon <%= card.type_code %> fg-<%= card.faction_code %>"></span> <a href="<%= card.url %>" class="card card-tip" data-toggle="modal" data-remote="false" data-target="#cardModal" data-code="<%= card.code %>"><%= card.label %></a>'),
            layouts = {},
            layout_data = {};

    /*
     * Templates for the different deck layouts, see deck.get_layout_data
     */
    layouts[1] = _.template('<div class="deck-content"><%= meta %><%= conns %><%= crew %><%= events %><%= gear %><%= heroics %><%= upgrades %></div>');
    layouts[2] = _.template('<div class="deck-content"><div class="row"><div class="col-sm-6 col-print-6"><%= meta %></div><div class="col-sm-6 col-print-6"><%= advs %></div></div><div class="row"><div class="col-sm-6 col-print-6"><%= conns %><%= crews %><%= events %></div><div class="col-sm-6 col-print-6"><%= gears %><%= heroics %><%= upgrades %></div></div></div>');
    layouts[3] = _.template('<div class="deck-content"><div class="row"><div class="col-sm-4"><%= meta %><%= advs %></div><div class="col-sm-4"><%= conns %><%= crews %><%= events %></div><div class="col-sm-4"><%= gears %><%= heroics %><%= upgrades %></div></div></div>');

    /**
     * @memberOf deck
     * @param {object} data 
     */
    deck.init = function init(data)
    {
        date_creation = data.date_creation;
        date_update = data.date_update;
        description_md = data.description_md;
        id = data.id;
        name = data.name;
        tags = data.tags;
        faction_code = data.faction_code;
        faction_name = data.faction_name;
        unsaved = data.unsaved;
        user_id = data.user_id;

        if(app.data.isLoaded) {
            deck.set_slots(data.slots);
        } else {
            console.log("deck.set_slots put on hold until data.app");
            $(document).on('data.app', function ()
            {
                deck.set_slots(data.slots);
            });
        }
    };

    /**
     * Sets the slots of the deck
     * 
     * @memberOf deck
     * @param {object} slots 
     */
    deck.set_slots = function set_slots(slots)
    {
        app.data.cards.update({}, {
            indeck: 0
        });
        for(var code in slots) {
            if(slots.hasOwnProperty(code)) {
                app.data.cards.updateById(code, {indeck: slots[code]});
            }
        }
    };

    /**
     * @memberOf deck
     * @returns string
     */
    deck.get_id = function get_id()
    {
        return id;
    };

    /**
     * @memberOf deck
     * @returns string
     */
    deck.get_name = function get_name()
    {
        return name;
    };

    /**
     * @memberOf deck
     * @returns string
     */
    deck.get_faction_code = function get_faction_code()
    {
        return faction_code;
    };

    /**
     * @memberOf deck
     * @returns string
     */
    deck.get_description_md = function get_description_md()
    {
        return description_md;
    };

    /**
     * @memberOf deck
     * @param {object} sort 
     * @param {object} query 
     */
    deck.get_cards = function get_cards(sort, query)
    {
        sort = sort || {};
        sort['code'] = 1;

        query = query || {};
        query.indeck = {
            '$gt': 0
        };

        return app.data.cards.find(query, {
            '$orderBy': sort
        });
    };

    /**
     * @memberOf deck
     * @param {object} sort 
     */
    deck.get_draw_deck = function get_draw_deck(sort)
    {
        return deck.get_cards(sort, {
            type_code: {
                '$nin': ['adv', 'ship']
            }
        });
    };

    /**
     * @memberOf deck
     * @param {object} sort
     */
    deck.get_adventure_deck = function get_adventure_deck(sort)
    {
        return deck.get_cards(sort, {
            type_code: 'adv'
        });
    };

    /**
     * @memberOf deck
     * @returns the number of adventure cards
     * @param {object} sort
     */
    deck.get_adventure_deck_size = function get_adventure_deck_size(sort)
    {
        var adventure_deck = deck.get_adventure_deck();
        //console.log(deck.get_nb_cards(adventure_deck));
        return deck.get_nb_cards(adventure_deck);
    };

    /**
     * @memberOf deck
     * @param {object} sort
     */
    deck.get_captain_deck = function get_captain_deck(sort)
    {
        return deck.get_cards(sort, {
            type_code: {
                '$nin': ['adv', 'ship']
            }
        });

    };

    /**
     * @memberOf deck
     * @returns the number captain cards
     * @param {object} sort
     */
    deck.get_captain_deck_size = function get_captain_deck_size(sort)
    {
        var captain_deck = deck.get_captain_deck();
        //console.log(deck.get_nb_cards(captain_deck).toString());
        return deck.get_nb_cards(captain_deck);
    };

    /**
     * @memberOf deck
     *
     *  @param {object} sort
     */
    deck.get_draw_deck_size = function get_draw_deck_size(sort)
    {
        var draw_deck = deck.get_draw_deck();
        return deck.get_nb_cards(draw_deck);
    };

    deck.get_nb_cards = function get_nb_cards(cards)
    {
        if(!cards)
            cards = deck.get_cards();
        var quantities = _.pluck(cards, 'indeck');
        return _.reduce(quantities, function (memo, num)
        {
            return memo + num;
        }, 0);
    };

    /**
     * @memberOf deck
     */
    deck.get_included_packs = function get_included_packs()
    {
        var cards = deck.get_cards();
        var nb_packs = {};
        cards.forEach(function (card)
        {
            nb_packs[card.pack_code] = Math.max(nb_packs[card.pack_code] || 0, card.indeck / card.quantity);
        });
        var pack_codes = _.uniq(_.pluck(cards, 'pack_code'));
        var packs = app.data.packs.find({
            'code': {
                '$in': pack_codes
            }
        }, {
            '$orderBy': {
                'available': 1
            }
        });
        packs.forEach(function (pack)
        {
            pack.quantity = nb_packs[pack.code] || 0;
        });
        return packs;
    };

    /**
     * @memberOf deck
     * @param {object} container 
     * @param {object} options 
     */
    deck.display = function display(container, options)
    {

        options = _.extend({sort: 'type', cols: 2}, options);

        var layout_data = deck.get_layout_data(options);
        var deck_content = layouts[options.cols](layout_data);

        $(container)
                .removeClass('deck-loading')
                .empty();

        $(container).append(deck_content);
    };

    deck.get_layout_data = function get_layout_data(options)
    {

        var data = {
            images: '',
            meta: '',
            advs: '',
            conns: '',
            crews: '',
            events: '',
            gears: '',
            heroics: '',
            upgrades: '',
        };

        var problem = deck.get_problem();
        
        deck.update_layout_section(data, 'images', $('<div style="margin-bottom:10px"><img src="/bundles/app/images/factions/' + deck.get_faction_code() + '.png" class="img-responsive">'));
        /* adv.forEach(function (adv) {
            deck.update_layout_section(data, 'images', $('<div><img src="' + adv.imagesrc + '" class="img-responsive">'));
        }); */

        /*deck.update_layout_section(data, 'meta', $('<img src="/bundles/app/images/broadswordpto.png" class="img-responsive>'));*/
        deck.update_layout_section(data, 'meta', $('<img src="/bundles/app/images/cards/' + deck.get_faction_code() + '.png" class="img-responsive">'));
        /*adv.forEach(function (adv) {
            var adv_line = $('<h5>').append($(card_line_tpl({card: adv})));
            adv_line.find('.icon').remove();
            deck.update_layout_section(data, 'meta', adva_line);
        });*/
        var drawDeckSection = $('<div><h4 style="font-weight:bold">' + faction_name + '</h4>' + Translator.transChoice('decks.edit.meta.adventuredeck', deck.get_adventure_deck_size(), {count: deck.get_adventure_deck_size()}) + '</div>');
        drawDeckSection.addClass(problem && problem.indexOf('cards') !== -1 ? 'text-danger' : '');
        deck.update_layout_section(data, 'meta', drawDeckSection);
        var plotDeckSection = $('<div>' + Translator.transChoice('decks.edit.meta.captaindeck', deck.get_captain_deck_size(), {count: deck.get_captain_deck_size()}) + '</div>');
        plotDeckSection.addClass(problem && problem.indexOf('cards') !== -1 ? 'text-danger' : '');
        deck.update_layout_section(data, 'meta', plotDeckSection);
        //deck.update_layout_section(data, 'meta', $('<div>Packs: ' + _.map(deck.get_included_packs(), function (pack) { return pack.name+(pack.quantity > 1 ? ' ('+pack.quantity+')' : ''); }).join(', ') + '</div>'));
        var packs = _.map(deck.get_included_packs(), function (pack)
        {
            return pack.name + (pack.quantity > 1 ? ' (' + pack.quantity + ')' : '');
        }).join(', ');
        deck.update_layout_section(data, 'meta', $('<div>' + Translator.trans('decks.edit.meta.packs', {"packs": packs}) + '</div>'));
        if(problem) {
            deck.update_layout_section(data, 'meta', $('<div class="text-danger small"><span class="fa fa-exclamation-triangle"></span> ' + problem_labels[problem] + '</div>'));
        }

        deck.update_layout_section(data, 'advs', deck.get_layout_data_one_section('type_code', 'adv', 'type_name'));
        //deck.update_layout_section(data, 'advs', deck.get_layout_data_one_section("type_code', 'adv', 'type_name'"));
        deck.update_layout_section(data, 'conns', deck.get_layout_data_one_section('type_code', 'conn', 'type_name'));
        deck.update_layout_section(data, 'crews', deck.get_layout_data_one_section('type_code', 'crew', 'type_name'));
        deck.update_layout_section(data, 'events', deck.get_layout_data_one_section('type_code', 'event', 'type_name'));
        deck.update_layout_section(data, 'gears', deck.get_layout_data_one_section('type_code', 'gear', 'type_name'));
        deck.update_layout_section(data, 'heroics', deck.get_layout_data_one_section('type_code', 'heroic', 'type_name'));
        deck.update_layout_section(data, 'upgrades', deck.get_layout_data_one_section('type_code', 'upgrade', 'type_name'));

        return data;
    };

    deck.update_layout_section = function update_layout_section(data, section, element)
    {
        data[section] = data[section] + element[0].outerHTML;
    };

    deck.get_layout_data_one_section = function get_layout_data_one_section(sortKey, sortValue, displayLabel)
    {
        var section = $('<div>');
        var query = {};
        query[sortKey] = sortValue;
        var cards = deck.get_cards({name: 1}, query);
        if(cards.length) {
            $(header_tpl({code: sortValue, name: cards[0][displayLabel], quantity: deck.get_nb_cards(cards)})).appendTo(section);
            cards.forEach(function (card)
            {
                var $div = $('<div>').addClass(deck.can_include_card(card) ? '' : 'invalid-card');
                $div.append($(card_line_tpl({card: card})));
                $div.prepend(card.indeck + 'x ');
                $div.appendTo(section);
            });
        }
        return section;
    };

    /**
     * @memberOf deck
     * @return boolean true if at least one other card quantity was updated
     */
    deck.set_card_copies = function set_card_copies(card_code, nb_copies)
    {
        var card = app.data.cards.findById(card_code);
        if(!card)
            return false;

        var updated_other_card = false;
        if(nb_copies > 0) {
            // card-specific rules
            switch(card.type_code) {

            }
        }
        app.data.cards.updateById(card_code, {
            indeck: nb_copies
        });
        app.deck_history && app.deck_history.notify_change();

        return updated_other_card;
    };

    /**
     * @memberOf deck
     */
    deck.get_content = function get_content()
    {
        var cards = deck.get_cards();
        var content = {};
        cards.forEach(function (card)
        {
            content[card.code] = card.indeck;
        });
        return content;
    };

    /**
     * @memberOf deck
     */
    deck.get_json = function get_json()
    {
        return JSON.stringify(deck.get_content());
    };

    /**
     * @memberOf deck
     */
    deck.get_export = function get_export(format)
    {

    };

    /**
     * @memberOf deck
     */
    deck.get_copies_and_deck_limit = function get_copies_and_deck_limit()
    {
        var copies_and_deck_limit = {};
        deck.get_draw_deck().forEach(function (card)
        {
            var value = copies_and_deck_limit[card.name];
            if(!value) {
                copies_and_deck_limit[card.name] = {
                    nb_copies: card.indeck,
                    deck_limit: card.deck_limit
                };
            } else {
                value.nb_copies += card.indeck;
                value.deck_limit = Math.min(card.deck_limit, value.deck_limit);
            }
        })
        return copies_and_deck_limit;
    };

    /**
     * @memberOf deck
     */
    deck.get_problem = function get_problem()
    {
        var expectedMinCardCount = 60;
        var expectedCaptainCount = 60;
        var expectedAdventureCount = 20;

        // expect at least 20 adventure cards
        if(deck.get_adventure_deck_size() > expectedAdventureCount) {
            return 'too_many_adventure_cards';
        }
        else if(deck.get_adventure_deck_size() < expectedAdventureCount) {
            return 'too_few_adventure_cards';
        }

        //expect at least 60 captain cards
        if(deck.get_captain_deck_size() > expectedCaptainCount) {
            return 'too_many_captain_cards';
        }
        if(deck.get_captain_deck_size() < expectedCaptainCount) {
            return 'too_few_captain_cards';
        }

        // at least 60 others cards
        if(deck.get_draw_deck_size() < expectedMinCardCount) {
            return 'too_few_cards';
        }

        // too many copies of one card
        if(!_.isUndefined(_.findKey(deck.get_copies_and_deck_limit(), function (value)
        {
            return value.nb_copies > value.deck_limit;
        }))) {
            return 'too_many_copies';
        }

    };



    /**
     * returns true if the deck can include the card as parameter
     * @memberOf deck
     */
    deck.can_include_card = function can_include_card(card)
    {
        // neutral card => yes
        if(card.faction_code === 'neutral')
            return true;

        // in-house card => yes
        if(card.faction_code === faction_code)
            return true;


        // if none above => no
        return false;
    };

})(app.deck = {}, jQuery);
