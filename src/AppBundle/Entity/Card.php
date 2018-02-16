<?php

namespace AppBundle\Entity;

class Card implements \Gedmo\Translatable\Translatable, \Serializable
{
	private function snakeToCamel($snake) {
		$parts = explode('_', $snake);
		return implode('', array_map('ucfirst', $parts));
	}
	
	public function serialize() {
		$serialized = [];
		if(empty($this->code)) return $serialized;
	
		$mandatoryFields = [
				'code',
				'deck_limit',
				'position',
				'quantity',
				'name',
		];
	
		$optionalFields = [
				'tonnage',
				'traits',
				'capabilities',
				'initiative',
				'jump',
				'attack',
				'defense',
				'crew',
				'computer',
				'hardpoint',
				'hull',
				'internal',
				'contractname',
				'distance',
				'contractrequirements',
				'compslots',
				'abandpenalty',
				'victorypoints',
				'subplots',
				'complicationname',
				'complicationtraits',
				'complicationtext',
				'abandpenmodifier',
				'compflavortext',
				'cost',
				'expense',
				'species',
				'skills',
				'wound',
				'requiredskill',
				'tonnagerequirement',
				'structure',
				'deck_requirements',
				'deck_options',
				'illustrator',
				'flavor',
				'text',
				'imagesrc',
				'octgn_id',
		];
	
		$externalFields = [
				'faction',
				'pack',
				'type',
                'subtype'
		];
	
		switch($this->type->getCode()) {
			case 'adv':
				$mandatoryFields[] = 'contractname';
				$mandatoryFields[] = 'distance';
				$mandatoryFields[] = 'contractrequirements';
				$mandatoryFields[] = 'compslots';
				$mandatoryFields[] = 'abandpenalty';
				$mandatoryFields[] = 'victorypoints';
				$mandatoryFields[] = 'subplots';
				$mandatoryFields[] = 'complicationname';
				$mandatoryFields[] = 'complicationtraits';
				$mandatoryFields[] = 'complicationtext';
				$mandatoryFields[] = 'abandpenmodifier';
				break;
			case 'crew':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'species';
				$mandatoryFields[] = 'skills';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'wound';
				$mandatoryFields[] = 'text';
				break;
			case 'conn':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'subtype';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'text';
				break;
			case 'event':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'text';
				break;
			case 'gear':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'subtype';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'text';
				break;
			case 'heroic':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'requiredskill';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'text';
				break;
			case 'ship':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'tonnage';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'capabilities';
				$mandatoryFields[] = 'initiative';
				$mandatoryFields[] = 'jump';
				$mandatoryFields[] = 'attack';
				$mandatoryFields[] = 'defense';
				$mandatoryFields[] = 'crew';
				$mandatoryFields[] = 'computer';
				$mandatoryFields[] = 'hardpoint';
				$mandatoryFields[] = 'hull';
				$mandatoryFields[] = 'internal';
				$mandatoryFields[] = 'text';
				break;
			case 'upgrade':
				$mandatoryFields[] = 'cost';
				$mandatoryFields[] = 'traits';
				$mandatoryFields[] = 'tonnagerequirement';
				$mandatoryFields[] = 'subtype';
				$mandatoryFields[] = 'structure';
				$mandatoryFields[] = 'expense';
				$mandatoryFields[] = 'text';
				break;
		}
	
		foreach($optionalFields as $optionalField) {
			$getter = 'get' . $this->snakeToCamel($optionalField);
			$serialized[$optionalField] = $this->$getter();
			if(!isset($serialized[$optionalField]) || $serialized[$optionalField] === '') unset($serialized[$optionalField]);
		}
	
		foreach($mandatoryFields as $mandatoryField) {
			$getter = 'get' . $this->snakeToCamel($mandatoryField);
			$serialized[$mandatoryField] = $this->$getter();
		}
	
		foreach($externalFields as $externalField) {
			$getter = 'get' . $this->snakeToCamel($externalField);
			$serialized[$externalField.'_code'] = $this->$getter()->getCode();
		}
	
		ksort($serialized);
		return $serialized;
	}

	public function unserialize($serialized) {
		throw new \Exception("unserialize() method unsupported");
	}
	
    public function toString() {
		return $this->name;
	}
	
	/**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

	/**
     * @var integer
     */
    private $tonnage;
	
	/**
     * @var string
     */
    private $traits;
	
	/**
     * @var string
     */
    private $capabilities;
	
	/**
     * @var integer
     */
    private $initiative;
	
	/**
     * @var integer
     */
    private $jump;
	
	/**
     * @var integer
     */
    private $attack;
	
	/**
     * @var integer
     */
    private $defense;
	
	/**
     * @var integer
     */
    private $crew;
	
	/**
     * @var integer
     */
    private $computer;
	
	/**
     * @var integer
     */
    private $hardpoint;
	
	/**
     * @var integer
     */
    private $hull;
	
	/**
     * @var integer
     */
    private $internal;
	
	/**
     * @var string
     */
    private $contractname;
	
	/**
     * @var integer
     */
    private $distance;
	
	/**
     * @var string
     */
    private $contractrequirements;
	
	/**
     * @var integer
     */
    private $compslots;
	
	/**
     * @var integer
     */
    private $abandpenalty;
	
	/**
     * @var integer
     */
    private $victorypoints;
	
	/**
     * @var string
     */
    private $subplots;
	
	/**
     * @var string
     */
    private $complicationname;
	
	/**
     * @var string
     */
    private $complicationtraits;
	
	/**
     * @var string
     */
    private $complicationtext;
	
	/**
     * @var integer
     */
    private $abandpenmodifier;
	
	/**
     * @var string
     */
    private $compflavortext;
	
    /**
     * @var integer
     */
    private $cost;

	/**
     * @var integer
     */
    private $expense;
	
	/**
     * @var string
     */
    private $species;
	
	/**
     * @var string
     */
    private $skills;
	
	/**
     * @var integer
     */
    private $wound;
	
	/**
     * @var string
     */
    private $requiredskill;
	
	/**
     * @var integer
     */
    private $tonnagerequirement;
	
	/**
     * @var integer
     */
    private $structure;
	
    /**
     * @var string
     */
    private $text;
	
	/**
     * @var integer
     */
    private $quantity;
	
    /**
     * @var integer
     */
    private $deckLimit;
	/**
     * @var integer
     */
    private $deckRequirements;
	
	/**
     * @var integer
     */
	private $deckOptions;
	
    /**
     * @var \DateTime
     */
    private $dateCreation;

    /**
     * @var \DateTime
     */
    private $dateUpdate;

    /**
     * @var string
     */
    private $flavor;

    /**
     * @var string
     */
    private $illustrator;

    /**
     * @var string
     */
    private $octgnId;
	
    /**
     * @var string
     */
    private $imagesrc;
	

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $reviews;

    /**
     * @var \AppBundle\Entity\Pack
     */
    private $pack;

    /**
     * @var \AppBundle\Entity\Type
     */
    private $type;

    /**
     * @var \AppBundle\Entity\Subtype
     */
    private $subtype;

    /**
     * @var \AppBundle\Entity\Faction
     */
    private $faction;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Card
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Card
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Card
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
	

    public function setTonnage($tonnage)
    {
        $this->tonnage = $tonnage;

        return $this;
    }

    /**
     * Get tonnage
     *
     * @return integer
     */
    public function getTonnage()
    {
        return $this->tonnage;
    }
	
	/**
	* Set traits
	* @param string $traits
	* @return Card
	*/
	public function setTraits($traits)
	{
		$this->traits = $traits;
		return $this;
	}

	/**
	* Get traits
	* @return string
	*/
	public function getTraits()
	{
		return $this->traits;
	}
	
	/**
	* Set capabilities
	* @param integer $capabilities
	* @return Card
	*/
	public function setCapabilities($capabilities)
	{
		$this->capabilities = $capabilities;
		return $this;
	}

	/**
	* Get Capabilities
	* @return string
	*/
	public function getCapabilities()
	{
		return $this->capabilities;
	}
	
	/**
	* Set Initiative
	* @param integer $initiative
	* @return Card
	*/
	public function setInitiative($initiative)
	{
		$this->initiative = $initiative;
		return $this;
	}

	/**
	* Get Initiative
	* @return integer
	*/
	public function getInitiative()
	{
		return $this->initiative;
	}
	
	/**
	* Set Jump
	* @param integer $jump
	* @return Card
	*/
	public function setJump($jump)
	{
		$this->jump = $jump;
		return $this;
	}

	/**
	* Get Jump
	* @return integer
	*/
	public function getJump()
	{
		return $this->jump;
	}
	
	/**
	 * Set Attack
     *
     * @param integer $attack
     *
     * @return Card
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;

        return $this;
    }

    /**
     * Get Attack
     *
     * @return integer
     */
    public function getAttack()
    {
        return $this->attack;
    }
	
	/**
	 * Set Defense
     *
     * @param integer $defense
     *
     * @return Card
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get Defense
     *
     * @return integer
     */
    public function getDefense()
    {
        return $this->defense;
    }
	
	/**
	 * Set Crew
     *
     * @param integer $crew
     *
     * @return Card
     */
    public function setCrew($crew)
    {
        $this->crew = $crew;

        return $this;
    }

    /**
     * Get Crew
     *
     * @return integer
     */
    public function getCrew()
    {
        return $this->crew;
    }
	/**
	 * Set Computer
     *
     * @param integer $computer
     *
     * @return Card
     */
    public function setComputer($computer)
    {
        $this->computer = $computer;

        return $this;
    }

    /**
     * Get Computer
     *
     * @return integer
     */
    public function getComputer()
    {
        return $this->computer;
    }
	
	/**
	 * Set Hardpoint
     *
     * @param integer $hardpoint
     *
     * @return Card
     */
    public function setHardpoint($hardpoint)
    {
        $this->hardpoint = $hardpoint;

        return $this;
    }

    /**
     * Get Hardpoint
     *
     * @return integer 
     */
    public function getHardpoint()
    {
        return $this->hardpoint;
    }
	/**
	 * Set Hull
     *
     * @param integer $hull
     *
     * @return Card
     */
    public function setHull($hull)
    {
        $this->hull = $hull;

        return $this;
    }

    /**
     * Get Hull
     *
     * @return integer
     */
    public function getHull()
    {
        return $this->hull;
    }
	
	/**
	 * Set Internal
     *
     * @param integer $internal
     *
     * @return Card
     */
    public function setInternal($internal)
    {
        $this->internal = $internal;

        return $this;
    }

    /**
     * Get Internal
     *
     * @return integer
     */
    public function getInternal()
    {
        return $this->internal;
    }
	
	/**
	 * Set ContractName
     *
     * @param string $contractname
     *
     * @return Card
     */
    public function setContractname($contractname)
    {
        $this->contractname = $contractname;

        return $this;
    }

    /**
     * Get ContractName
     *
     * @return string
     */
    public function getContractname()
    {
        return $this->contractname;
    }
	
	/**
	 * Set Distance
     *
     * @param integer $distance
     *
     * @return Card
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get Distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }
	
	/**
	 * Set ContractRequirements
     *
     * @param string $contractrequirements
     *
     * @return Card
     */
    public function setContractrequirements($contractrequirements)
    {
        $this->contractrequirements = $contractrequirements;

        return $this;
    }

    /**
     * Get ContractRequirements
     *
     * @return string
     */
    public function getContractrequirements()
    {
        return $this->contractrequirements;
    }
	
	/**
	 * Set CompSlots
     *
     * @param integer $compslots
     *
     * @return Card
     */
    public function setCompslots($compslots)
    {
        $this->compslots = $compslots;

        return $this;
    }

    /**
     * Get CompSlots
     *
     * @return integer
     */
    public function getCompslots()
    {
        return $this->compslots;
    }
	
	/**
	 * Set Abandpenalty
     *
     * @param integer $Abandpenalty
     *
     * @return Card
     */
    public function setAbandpenalty($abandpenalty)
    {
        $this->abandpenalty = $abandpenalty;

        return $this;
    }

    /**
     * Get Abandpenalty
     *
     * @return integer
     */
    public function getAbandpenalty()
    {
        return $this->abandpenalty;
    }
	
	/**
	 * Set Victorypoints
     *
     * @param integer $victorypoints
     *
     * @return Card
     */
    public function setVictorypoints($victorypoints)
    {
        $this->victorypoints = $victorypoints;

        return $this;
    }

    /**
     * Get Victorypoints
     *
     * @return integer
     */
    public function getVictorypoints()
    {
        return $this->victorypoints;
    }
	
	/**
	 * Set Subplots
     *
     * @param string $subplots
     *
     * @return Card
     */
    public function setSubplots($subplots)
    {
        $this->subplots = $subplots;

        return $this;
    }

    /**
     * Get Subplots
     *
     * @return string
     */
    public function getSubplots()
    {
        return $this->subplots;
    }
	
	/* End of Contracts Side */
	
	/**
	 * Set Complicationname
     *
     * @param string $complicationname
     *
     * @return Card
     */
    public function setComplicationname($complicationname)
    {
        $this->complicationname = $complicationname;

        return $this;
    }

    /**
     * Get Complicationname
     *
     * @return string
     */
    public function getComplicationname()
    {
        return $this->complicationname;
    }
	
	/**
	 * Set Complicationtraits
     *
     * @param string $complicationtraits
     *
     * @return Card
     */
    public function setComplicationtraits($complicationtraits)
    {
        $this->complicationtraits = $complicationtraits;

        return $this;
    }

    /**
     * Get Complicationtraits
     *
     * @return string
     */
    public function getComplicationtraits()
    {
        return $this->complicationtraits;
    }
	
	/**
	 * Set Complicationtext
     *
     * @param string $omplicationtext
     *
     * @return Card
     */
    public function setComplicationtext($complicationtext)
    {
        $this->complicationtext = $complicationtext;

        return $this;
    }

    /**
     * Get Complicationtext
     *
     * @return string
     */
    public function getComplicationtext()
    {
        return $this->complicationtext;
    }
	
	/**
	 * Set Abandpenmodifier
     *
     * @param integer $abandpenmodifier
     *
     * @return Card
     */
    public function setAbandpenmodifier($abandpenmodifier)
    {
        $this->abandpenmodifier = $abandpenmodifier;

        return $this;
    }

    /**
     * Get Abandpenmodifier
     *
     * @return integer
     */
    public function getAbandpenmodifier()
    {
        return $this->abandpenmodifier;
    }
	
	/**
	 * Set Compflavortext
     *
     * @param string $compflavortext
     *
     * @return Card
     */
    public function setCompflavortext($compflavortext)
    {
        $this->compflavortext = $compflavortext;

        return $this;
    }

    /**
     * Get Compflavortext
     *
     * @return string
     */
    public function getCompflavortext()
    {
        return $this->compflavortext;
    }
	
	/* End of Complications Side */
	
    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return Card
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return integer
     */
    public function getCost()
    {
        return $this->cost;
    }
	
	/**
	 * Set expense
     *
     * @param integer $expense
     *
     * @return Card
     */
    public function setExpense($expense)
    {
        $this->expense = $expense;

        return $this;
    }

    /**
     * Get expense
     *
     * @return integer
     */
    public function getExpense()
    {
        return $this->expense;
    }
	
	/**
	 * Set species
     *
     * @param string $species
     *
     * @return Card
     */
    public function setSpecies($species)
    {
        $this->species = $species;

        return $this;
    }

    /**
     * Get species
     *
     * @return string
     */
    public function getSpecies()
    {
        return $this->species;
    }
	
	/**
	 * Set skills
     *
     * @param string $skills
     *
     * @return Card
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * Get skills
     *
     * @return string
     */
    public function getSkills()
    {
        return $this->skills;
    }
	
	/**
	 * Set wound
     *
     * @param integer $wound
     *
     * @return Card
     */
    public function setWound($wound)
    {
        $this->wound = $wound;

        return $this;
    }

    /**
     * Get Wound
     *
     * @return integer
     */
    public function getWound()
    {
        return $this->wound;
	}
	
	/**
	 * Set requiredskill
     *
     * @param string $requiredskill
     *
     * @return Card
     */
    public function setRequiredskill($requiredskill)
    {
        $this->requiredskill = $requiredskill;

        return $this;
    }

    /**
     * Get requiredskill
     *
     * @return string
     */
    public function getRequiredskill()
    {
        return $this->requiredskill;
    }
	
	/**
	 * Set tonnagerequirement
     *
     * @param integer $tonnagerequirement
     *
     * @return Card
     */
    public function setTonnagerequirement($tonnagerequirement)
    {
        $this->tonnagerequirement = $tonnagerequirement;

        return $this;
    }

    /**
     * Get tonnagerequirement
     *
     * @return integer
     */
    public function getTonnagerequirement()
    {
        return $this->tonnagerequirement;
    }
	
	/**
	 * Set structure
     *
     * @param integer $structure
     *
     * @return Card
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get structure
     *
     * @return integer
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Card
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Card
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
	
    /**
     * Set deckLimit
     *
     * @param integer $deckLimit
     *
     * @return Card
     */
    public function setDeckLimit($deckLimit)
    {
        $this->deckLimit = $deckLimit;

        return $this;
    }

    /**
     * Get deckLimit
     *
     * @return integer
     */
    public function getDeckLimit()
    {
        return $this->deckLimit;
    }
	
    /**
     * Set deckRequirements
     *
	 * @param integer $deckRequirements
     *
     * @return Card
     */
    public function setDeckRequirements($deckRequirements)
    {
        $this->deckRequirements = $deckRequirements;

        return $this;
    }

    /**
     * Get deckRequirements
     *
     * @return integer
     */
    public function getDeckRequirements()
    {
        return $this->deckRequirements;
    }

    /**
     * Set deckOptions
     *
     * @param integer $deckOptions
     *
     * @return Card
     */
    public function setDeckOptions($deckOptions)
    {
        $this->deckOptions = $deckOptions;

        return $this;
    }

    /**
     * Get deckOptions
     *
     * @return integer
     */
    public function getDeckOptions()
    {
        return $this->deckOptions;
    }



    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Card
     */
    public function setDateCreation($dateCreation)
    {
        $this->date_create = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return Card
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->date_update = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set flavor
     *
     * @param string $flavor
     *
     * @return Card
     */
    public function setFlavor($flavor)
    {
        $this->flavor = $flavor;

        return $this;
    }

    /**
     * Get flavor
     *
     * @return string
     */
    public function getFlavor()
    {
        return $this->flavor;
    }

    /**
     * Set illustrator
     *
     * @param string $illustrator
     *
     * @return Card
     */
    public function setIllustrator($illustrator)
    {
        $this->illustrator = $illustrator;

        return $this;
    }

    /**
     * Get illustrator
     *
     * @return string
     */
    public function getIllustrator()
    {
        return $this->illustrator;
    }


    /**
     * Set octgnId
     *
     * @param boolean $octgnId
     *
     * @return Card
     */
    public function setOctgnId($octgnId)
    {
        $this->octgnId = $octgnId;

        return $this;
    }

    /**
     * Get octgnId
     *
     * @return boolean
     */
    public function getOctgnId()
    {
        return $this->octgnId;
    }
	
	
    /**
     * Set imagesrc
     *
     * @param string $imagesrc
     *
     * @return Card
     */
    public function setImagesrc($imagesrc)
    {
        $this->imagesrc = $imagesrc;

        return $this;
    }

    /**
     * Get imagesrc
     *
     * @return string
     */
    public function getImagesrc()
    {
        return $this->imagesrc;
    }


    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return Card
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set pack
     *
     * @param \AppBundle\Entity\Pack $pack
     *
     * @return Card
     */
    public function setPack(\AppBundle\Entity\Pack $pack = null)
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return \AppBundle\Entity\Pack
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\Type $type
     *
     * @return Card
     */
    public function setType(\AppBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set subtype
     *
     * @param \AppBundle\Entity\Subtype $subtype
     *
     * @return Card
     */
    public function setSubtype(\AppBundle\Entity\subtype $subtype = null)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * Get subtype
     *
     * @return \AppBundle\Entity\Subtype
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * Set faction
     *
     * @param \AppBundle\Entity\Faction $faction
     *
     * @return Card
     */
    public function setFaction(\AppBundle\Entity\Faction $faction = null)
    {
        $this->faction = $faction;

        return $this;
    }

    /**
     * Get faction
     *
     * @return \AppBundle\Entity\Faction
     */
    public function getFaction()
    {
        return $this->faction;
    }

    /*
    * I18N vars
    */
    private $locale = 'en';

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /* /**
     * @return int
     */
    /*public function getCostIncome()
    {
        $cost = $this->getCost();
        $income = $this->getIncome();

        if (is_null($cost) and is_null($income)) {
            return -1;
        }
        return max($cost, $income);
    }

    /**
     * @return int
     */
    /*public function getStrengthInitiative()
    {
        $strength = $this->getStrength();
        $initiative = $this->getInitiative();

        if (is_null($strength) and is_null($initiative)) {
            return -1;
        }
        return max($strength, $initiative);
    }
	*/
}
