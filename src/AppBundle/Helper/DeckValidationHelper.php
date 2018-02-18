<?php

namespace AppBundle\Helper;

use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Model\SlotCollectionProviderInterface;

class DeckValidationHelper
{

    public function __construct (AgendaHelper $agenda_helper, TranslatorInterface $translator)
    {
        $this->agenda_helper = $agenda_helper;
        $this->translator = $translator;
    }

    public function getInvalidCards ($deck)
    {
        $invalidCards = [];
        foreach($deck->getSlots() as $slot) {
            if(!$this->canIncludeCard($deck, $slot->getCard())) {
                $invalidCards[] = $slot->getCard();
            }
        }
        return $invalidCards;
    }

    public function canIncludeCard ($deck, $card)
    {
        if($card->getFaction()->getCode() === 'neutral') {
            return true;
        }
        if($card->getFaction()->getCode() === $deck->getFaction()->getCode()) {
            return true;
        }
        /*
        if($card->getIsLoyal()) {
            return false;
        }
        foreach($deck->getSlots()->getAgendas() as $slot) {
            if($this->isCardAllowedByAgenda($slot->getCard(), $card)) {
                return true;
            }
        }*/

        return false;
    }

    public function isCardAllowedByAgenda($agenda, $card) {
        switch($agenda->getCode()) {
            case '01198':
            case '01199':
            case '01200':
            case '01201':
            case '01202':
            case '01203':
            case '01204':
            case '01205':
                return $this->agenda_helper->getMinorFactionCode($agenda) === $card->getFaction()->getCode();
            case '09045':
                $trait = $this->translator->trans('card.traits.maester');
                if(preg_match("/$trait\\./", $card->getTraits())) {
                    return $card->getType()->getCode() === 'character';
                }
                return false;
        }

        return false;
    }

    /**
     * @param SlotCollectionProviderInterface $deck
     * @return null|string
     */
    public function findProblem (SlotCollectionProviderInterface $deck)
    {
        $slots = $deck->getSlots();

        $expectedAdvDeckCount = 20;
        $expectedCaptDeckCount = 60;
        $expectedMinCardCount = 60;

        if($slots->getAdventureDeck()->countCards() < $expectedAdvDeckCount) {
            return 'too_few_adventure_cards';
        }
        if($slots->getCaptainDeck()->countCards() > $expectedCaptDeckCount) {
            return 'too_many_captain_cards';
        }
        if($slots->getAdventureDeck()->countCards() > $expectedAdvDeckCount) {
            return 'too_many_adventure_cards';
        }
        if($slots->getCaptainDeck()->countCards() < $expectedCaptDeckCount) {
            return 'too_few_captain_cards';
        }
        if($slots->getDrawDeck()->countCards() < $expectedMinCardCount) {
            return 'too_few_cards';
        }
        foreach($slots->getCopiesAndDeckLimit() as $cardName => $value) {
            if($value['copies'] > $value['deck_limit']) {
                return 'too_many_copies';
            }
        }

        return null;
    }

    public function getProblemLabel ($problem)
    {
        if(!$problem) {
            return '';
        }
        return $this->translator->trans('decks.problems.' . $problem);
    }

}
