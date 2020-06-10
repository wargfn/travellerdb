<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Decorator for a collection of SlotInterface 
 */
class SlotCollectionDecorator implements \AppBundle\Model\SlotCollectionInterface
{

    protected $slots;

    public function __construct (\Doctrine\Common\Collections\Collection $slots)
    {
        $this->slots = $slots;
    }

    public function add ($element)
    {
        return $this->slots->add($element);
    }

    public function removeElement ($element)
    {
        return $this->slots->removeElement($element);
    }

    public function count ($mode = null)
    {
        return $this->slots->count($mode);
    }

    public function getIterator ()
    {
        return $this->slots->getIterator();
    }

    public function offsetExists ($offset)
    {
        return $this->slots->offsetExists($offset);
    }

    public function offsetGet ($offset)
    {
        return $this->slots->offsetGet($offset);
    }

    public function offsetSet ($offset, $value)
    {
        return $this->slots->offsetSet($offset, $value);
    }

    public function offsetUnset ($offset)
    {
        return $this->slots->offsetUnset($offset);
    }

    public function countCards ()
    {
        $count = 0;
        foreach($this->slots as $slot) {
            $count += $slot->getQuantity();
        }
        return $count;
    }

    public function getIncludedPacks ()
    {
        $packs = [];
        foreach($this->slots as $slot) {
            $card = $slot->getCard();
            $pack = $card->getPack();
            if(!isset($packs[$pack->getPosition()])) {
                $packs[$pack->getPosition()] = [
                    'pack' => $pack,
                    'nb' => 0
                ];
            }

            $nbpacks = ceil($slot->getQuantity() / $card->getQuantity());
            if($packs[$pack->getPosition()]['nb'] < $nbpacks) {
                $packs[$pack->getPosition()]['nb'] = $nbpacks;
            }
        }
        ksort($packs);
        return array_values($packs);
    }

    public function getSlotsByType ()
    {
        $slotsByType = ['ship' => [], 'adv' => [], 'conn' => [], 'crew' => [], 'event' => [], 'gear' => [], 'heroic' => [], 'upgrade' => [] ];
        foreach($this->slots as $slot) {
            if(array_key_exists($slot->getCard()->getType()->getCode(), $slotsByType)) {
                $slotsByType[$slot->getCard()->getType()->getCode()][] = $slot;
            }
        }
        return $slotsByType;
    }

    public function getCountByType ()
    {
        $countByType = ['adv' => 0, 'conn' => 0, 'crew' => 0, 'event' => 0, 'gear' => 0, 'heroic' => 0, 'upgrade' => 0];
        foreach($this->slots as $slot) {
            if(array_key_exists($slot->getCard()->getType()->getCode(), $countByType)) {
                $countByType[$slot->getCard()->getType()->getCode()] += $slot->getQuantity();
            }
        }
        return $countByType;
    }

    public function getPlotDeck ()
    {
        $plotDeck = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'adv') {
                $plotDeck[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($plotDeck));
    }

    public function getCaptainDeck ()
    {
        $captainDeck = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'conn' || $slot->getCard()->getType()->getCode() === 'crew' || $slot->getCard()->getType()->getCode() === 'event' || $slot->getCard()->getType()->getCode() === 'heroic' || $slot->getCard()->getType()->getCode() === 'upgrade' ) {
                $captainDeck[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($captainDeck));
    }

    public function getAdventureDeck ()
    {
        $advDeck = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'adv') {
                $advDeck[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($advDeck));
    }

    public function getShip ()
    {
        $shipDeck = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'ship') {
                $shipDeck[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($shipDeck));
    }

    public function getAgendas ()
    {
        $agendas = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'agenda') {
                $agendas[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($agendas));
    }

    public function isAlliance ()
    {
        foreach($this->getAgendas() as $agenda) {
            if($agenda->getCard()->getCode() === '06018') {
                return true;
            }
        }
        return false;
    }
    
    public function getDrawDeck ()
    {
        $drawDeck = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === 'conn' || $slot->getCard()->getType()->getCode() === 'crew' || $slot->getCard()->getType()->getCode() === 'event' || $slot->getCard()->getType()->getCode() === 'heroic' || $slot->getCard()->getType()->getCode() === 'upgrade' ) {
                $drawDeck[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($drawDeck));
    }

    public function filterByFaction ($faction_code)
    {
        $slots = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getFaction()->getCode() === $faction_code) {
                $slots[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($slots));
    }

    public function filterByType ($type_code)
    {
        $slots = [];
        foreach($this->slots as $slot) {
            if($slot->getCard()->getType()->getCode() === $type_code) {
                $slots[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($slots));
    }

    public function filterByTrait ($trait)
    {
        $slots = [];
        foreach($this->slots as $slot) {
            if(preg_match("/$trait\\./", $slot->getCard()->getTraits())) {
                $slots[] = $slot;
            }
        }
        return new SlotCollectionDecorator(new ArrayCollection($slots));
    }

    public function getCopiesAndDeckLimit ()
    {
        $copiesAndDeckLimit = [];
        foreach($this->slots as $slot) {
            $cardName = $slot->getCard()->getName();
            if(!key_exists($cardName, $copiesAndDeckLimit)) {
                $copiesAndDeckLimit[$cardName] = [
                    'copies' => $slot->getQuantity(),
                    'deck_limit' => $slot->getCard()->getDeckLimit(),
                ];
            } else {
                $copiesAndDeckLimit[$cardName]['copies'] += $slot->getQuantity();
                $copiesAndDeckLimit[$cardName]['deck_limit'] = min($slot->getCard()->getDeckLimit(), $copiesAndDeckLimit[$cardName]['deck_limit']);
            }
        }
        return $copiesAndDeckLimit;
    }

    public function getSlots ()
    {
        return $this->slots;
    }

    public function getContent ()
    {
        $arr = array();
        foreach($this->slots as $slot) {
            $arr [$slot->getCard()->getCode()] = $slot->getQuantity();
        }
        ksort($arr);
        return $arr;
    }


    public function getCountByFaction() {
        $countByFaction = ['red' => 0, 'yellow' => 0, 'blue' => 0];

        foreach($this->slots as $slot) {
            $code = $slot->getCard()->getFaction()->getCode();
            if(array_key_exists($code, $countByFaction)) {
                $countByFaction[$code] += max($slot->getQuantity(), $slot->getDice());
            }
        }
        return $countByFaction;
    }

    public function getCountByAffiliation() {
        $countByAffiliation = ['villain' => 0, 'hero' => 0];

        foreach($this->slots as $slot) {
            $code = $slot->getCard()->getAffiliation()->getCode();
            if(array_key_exists($code, $countByAffiliation)) {
                $countByAffiliation[$code] += max($slot->getQuantity(), $slot->getDice());
            }
        }
        return $countByAffiliation;
    }

}
