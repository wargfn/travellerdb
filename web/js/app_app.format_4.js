(function app_format(format, $)
{

    /**
     * @memberOf format
     */
    format.traits = function traits(card)
    {
        return card.traits || '';
    };

    /**
     * @memberOf format
     */
    format.name = function name(card)
    {
        return (card.is_unique ? '<span class="icon-unique"></span> ' : "") + card.name;
    }

    format.faction = function faction(card)
    {
        var text = '<span class="fg-' + card.faction_code + ' icon-' + card.faction_code + '"></span> ' + card.faction_name + '. ';
        if(card.faction_code != 'neutral') {
            if(card.is_loyal)
                text += Translator.trans('card.info.loyal') + '. ';
            else
                text += Translator.trans('card.info.nonloyal') + '. ';
        }
        return text;
    }

    /**
     * @memberOf format
     */
    format.pack = function pack(card)
    {
        var text = card.pack_name + ' #' + card.position + '. ';
        return text;
    }

    /**
     * @memberOf format
     */
    format.info = function info(card)
    {
        var text = '<span class="card-type">' + card.type_name + '</span>';
        switch(card.type_code) {
            case 'character':
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.str') + ': ' + (card.strength != null ? card.strength : 'X') + '. '
                if(card.is_military)
                    text += '<span class="color-military icon-military" title="' + Translator.trans('challenges.military') + '"></span> ';
                if(card.is_intrigue)
                    text += '<span class="color-intrigue icon-intrigue" title="' + Translator.trans('challenges.intrigue') + '"></span> ';
                if(card.is_power)
                    text += '<span class="color-power icon-power" title="' + Translator.trans('challenges.power') + '"></span> ';
                break;
            case 'attachment':
            case 'location':
            case 'event':
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'plot':
                text += Translator.trans('card.info.income') + ': ' + card.income + '. ';
                text += Translator.trans('card.info.initiative') + ': ' + card.initiative + '. ';
                text += Translator.trans('card.info.claim') + ': ' + card.claim + '. ';
                text += Translator.trans('card.info.reserve') + ': ' + card.reserve + '. ';
                text += Translator.trans('card.info.plotlimit') + ': ' + card.deck_limit + '. ';
                break;
            case 'adv':
                text += '<p><span class="distance"></span>: ' + card.distance +'     <span class="card-type">   ' + card.contractname + '</span></p>';
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += '<p>' + (card.compslots != null ? card.compslots : '') + '<span class="complication"></span>  '
                text +=  + (card.abandpenalty != null ? card.abandpenalty : '') + '<span class="abandonment"></span>  '
                text +=  + (card.victorypoints != null ? card.victorypoints : '') + '<span class="victoryppoint"></span></p>';
                text += '<p>' + Translator.trans('card.info.contractrequirements') + ': ' + card.contractrequirements  + '</p>';
                text += '<p>' + Translator.trans('card.info.subplots') + ': ' + (card.subplots != null ? card.subplots : '') + '</p>';
                text += '<p><span class="card-type">' + card.complicationname + '</span><br />';
                text += '<span class="card-traits">' + (card.complicationtraits != null ? card.complicationtraits : '') + '</span><br />';
                text += '<span class="card-info">' + Translator.trans('card.complicationtext') + ': ' + card.complicationtext + '</span></p>';
                text += '<p><span class="abandonment"></span><span class="card-info">: ' + (card.abandpenmodifier != null ? card.abandpenmodifier : '') + '</span></p>';
                break;
            case 'conn':
                text += ' - <span class="card-type">'+ card.subtype_name +'</span>';
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'crew':
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += '<p><span class="card-info">' + Translator.trans('card.info.species') + ': ' + (card.species != null ? card.species: '<em>Classified</em>' ) + '</span><br />';
                text += '<span class="card-info">' + Translator.trans('card.info.skill') + ': ' + (card.skills != null ? card.skills: '<em>Classified</em>' ) + '</span></p>';
                text += Translator.trans('card.info.wound') + ': ' + (card.wound != null ? card.wound : 'X') + '<span class="wounds"></span>. <br />';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'gear':
                text += ' - <span class="card-type">'+ card.subtype_name +'</span>';
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'heroic':
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += '<span class="card-info">' + Translator.trans('card.info.requiredskill') + ': ' + (card.requiredskill != null ? card.requiredskill: '<em>Classified</em>' ) + '</span></p>';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'upgrade':
                text += ' - <span class="card-type">'+ card.subtype_name +'</span>';
                text += '<p><span class="card-traits">' + card.traits + '</span></p>';
                text += '<span class="card-info">' + Translator.trans('card.info.tonnage') + ': ' + (card.tonnagerequirement != null ? card.tonnagerequirement: '<em>Classified</em>' ) + '</span></p>';
                text += Translator.trans('card.info.structure') + ': ' + (card.structure != null ? card.structure: '<em>Classified</em>' ) + '<span class="structure"></span><br />';
                text += Translator.trans('card.info.cost') + ': ' + (card.cost != null ? card.cost : 'X') + '. ';
                text += Translator.trans('card.info.expense') + ': ' +(card.expense != null ? card.expense : 'X') + '<span class="expenses"></span>';
                break;
            case 'ship':
                text += '<p><span class="card-traits">' + card.traits + '</span>     <span class="card-type">' + Translator.trans('card.info.tonnage') + ': ' + (card.tonnage != null ? card.tonnage: '<em>Classified</em>' ) + '</span></p>';
                text += '<p><span class="card-info">' + Translator.trans('card.info.capabilities') + ': ' + (card.capabilities != null ? card.capabilities: '<em>Classified</em>' ) + '</span></p>';
                text += '<p><span class="initiative"></span>: ' + (card.initiative != null ? card.initiative : 'X') + '  ';
                text += '<span class="jump"></span>: '+ (card.jump != null ? card.jump : 'X') + '  ';
                text += '<span class="attack"></span>: ' + (card.attack != null ? card.attack : 'X') + '  ';
                text += '<span class="defense"></span>: '+ (card.defense != null ? card.defense : 'X') + '</p>';
                text += '<p>'
                    + ' <span class="crew"></span>: ' + (card.crew != null ? card.crew : 'X') + '  '
                    + ' <span class="computer"></span>: ' + (card.computer != null ? card.computer : 'X') + '  '
                    + ' <span class="hardpoint"></span>: ' + (card.hardpoint != null ? card.hardpoint : 'X') + '  '
                    + ' <span class="hull"></span>: ' + (card.hull != null ? card.hull : 'X') + '  '
                    + ' <span class="internal"></span>: ' + (card.internal != null ? card.internal : 'X') + '  '
                    + '</p>';
                break;
        }

        text = text.replace(/\[(\w+)\]/g, '<span class="$1"></span>');
        text = text.replace(/\[Cargo\/Passenger\]/g, '<span class="CargoPassenger"></span>');
        text = text.replace(/\[Military\/Passenger\]/g, '<span class="PassengerMilitary"></span>');
        text = text.replace(/\[Military\/Survey\]/g, '<span class="MilitarySurvey"></span>');
        text = text.replace(/\[Passenger\/Survey\]/g, '<span class="PassengerSurvey"></span>');
        text = text.replace(/\[Cargo\/Survey\]/g, '<span class="CargoSurvey"></span>');
        text = text.replace(/\[Cargo\/Military\]/g, '<span class="CargoMilitary"></span>');
        text = text.replace(/\[Trained\:Admin\]/g, '<span class="admintrained"></span>');
        text = text.replace(/\[Expert\:Admin\]/g, '<span class="adminexpert"></span>');
        text = text.replace(/\[Trained\:Combat\]/g, '<span class="combattrained"></span>');
        text = text.replace(/\[Expert\:Combat\]/g, '<span class="combatexpert"></span>');
        text = text.replace(/\[Trained\:Jack\]/g, '<span class="jacktrained"></span>');
        text = text.replace(/\[Expert\:Jack\]/g, '<span class="jackexpert"></span>');
        text = text.replace(/\[Trained\:Medical\]/g, '<span class="medicaltrained"></span>');
        text = text.replace(/\[Expert\:Medical\]/g, '<span class="medicalexpert"></span>');
        text = text.replace(/\[Trained\:Psionics\]/g, '<span class="psionicstrained"></span>');
        text = text.replace(/\[Expert\:Psionics\]/g, '<span class="psionicsexpert"></span>');
        text = text.replace(/\[Trained\:Psionic\]/g, '<span class="psionictrained"></span>');
        text = text.replace(/\[Expert\:Psionic\]/g, '<span class="psionicexpert"></span>');
        text = text.replace(/\[Trained\:Science\]/g, '<span class="sciencetrained"></span>');
        text = text.replace(/\[Expert\:Science\]/g, '<span class="scienceexpert"></span>');
        text = text.replace(/\[Trained\:Social\]/g, '<span class="socialtrained"></span>');
        text = text.replace(/\[Expert\:Social\]/g, '<span class="socialexpert"></span>');
        text = text.replace(/\[Trained\:StarshipOps\]/g, '<span class="staropstrained"></span>');
        text = text.replace(/\[Expert\:StarshipOps\]/g, '<span class="staropsexpert"></span>');
        text = text.replace(/\[Trained\:Tech\]/g, '<span class="techtrained"></span>');
        text = text.replace(/\[Expert\:Tech\]/g, '<span class="techexpert"></span>');
        text = text.replace(/\[Trained\:Underworld\]/g, '<span class="underworldtrained"></span>');
        text = text.replace(/\[Expert\:Underworld\]/g, '<span class="underworldexpert"></span>');
        text = text.replace(/\[Trained\:StarshipOperation\]/g, '<span class="staropstrained"></span>');
        text = text.replace(/\[Expert\:StarshipOperation\]/g, '<span class="staropsexpert"></span>');
        
        return text;
    };

    /**
     * @memberOf format
     */
    format.text = function text(card)
    {
        var text = card.text || '';
        text = text.replace(/\[(\w+)\]/g, '<span class="$1"></span>');
        text = text.split("\\n").join('</p><p>');
        /* text = text.replace(/\\n/g, '<br />');*/
        text = text.replace(/\[Cargo\/Passenger\]/g, '<span class="CargoPassenger"></span>');
        text = text.replace(/\[Military\/Passenger\]/g, '<span class="PassengerMilitary"></span>');
        text = text.replace(/\[Military\/Survey\]/g, '<span class="MilitarySurvey"></span>');
        text = text.replace(/\[Passenger\/Survey\]/g, '<span class="PassengerSurvey"></span>');
        text = text.replace(/\[Cargo\/Survey\]/g, '<span class="CargoSurvey"></span>');
        text = text.replace(/\[Cargo\/Military\]/g, '<span class="CargoMilitary"></span>');
        text = text.replace(/\[Trained\:Admin\]/g, '<span class="admintrained"></span>');
        text = text.replace(/\[Expert\:Admin\]/g, '<span class="adminexpert"></span>');
        text = text.replace(/\[Trained\:Combat\]/g, '<span class="combattrained"></span>');
        text = text.replace(/\[Expert\:Combat\]/g, '<span class="combatexpert"></span>');
        text = text.replace(/\[Trained\:Jack\]/g, '<span class="jacktrained"></span>');
        text = text.replace(/\[Expert\:Jack\]/g, '<span class="jackexpert"></span>');
        text = text.replace(/\[Trained\:Medical\]/g, '<span class="medicaltrained"></span>');
        text = text.replace(/\[Expert\:Medical\]/g, '<span class="medicalexpert"></span>');
        text = text.replace(/\[Trained\:Psionics\]/g, '<span class="psionicstrained"></span>');
        text = text.replace(/\[Expert\:Psionics\]/g, '<span class="psionicsexpert"></span>');
        text = text.replace(/\[Trained\:Psionic\]/g, '<span class="psionictrained"></span>');
        text = text.replace(/\[Expert\:Psionic\]/g, '<span class="psionicexpert"></span>');
        text = text.replace(/\[Trained\:Science\]/g, '<span class="sciencetrained"></span>');
        text = text.replace(/\[Expert\:Science\]/g, '<span class="scienceexpert"></span>');
        text = text.replace(/\[Trained\:Social\]/g, '<span class="socialtrained"></span>');
        text = text.replace(/\[Expert\:Social\]/g, '<span class="socialexpert"></span>');
        text = text.replace(/\[Trained\:StarshipOps\]/g, '<span class="starsopstrained"></span>');
        text = text.replace(/\[Expert\:StarshipOps\]/g, '<span class="staropsexpert"></span>');
        text = text.replace(/\[Trained\:Tech\]/g, '<span class="techtrained"></span>');
        text = text.replace(/\[Expert\:Tech\]/g, '<span class="techexpert"></span>');
        text = text.replace(/\[Trained\:Underworld\]/g, '<span class="underworldtrained"></span>');
        text = text.replace(/\[Expert\:Underworld\]/g, '<span class="underworldexpert"></span>');
        text = text.replace(/\[Trained\:StarshipOperation\]/g, '<span class="staropstrained"></span>');
        text = text.replace(/\[Expert\:StarshipOperation\]/g, '<span class="staropsexpert"></span>');
        return '<p>' + text + '</p>';
    };

})(app.format = {}, jQuery);
