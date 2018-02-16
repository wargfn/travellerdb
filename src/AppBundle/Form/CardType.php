<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pack', 'entity', array('class' => 'AppBundle:Pack', 'property' => 'name'))
            ->add('position')
            ->add('quantity')
            ->add('deck_limit')
            ->add('code')
            ->add('type', 'entity', array('class' => 'AppBundle:Type', 'property' => 'name'))
            ->add('faction', 'entity', array('class' => 'AppBundle:Faction', 'property' => 'name'))
            ->add('name')
			->add('subtype', 'entity', array('class' => 'AppBundle:Subtype', 'property' => 'name'))
			->add('tonnage', 'number', array('required' => false))
			->add('traits','textarea', array('required' => false))
			->add('capabilities', 'textarea', array('required' => false))
			->add('initiative', 'number', array('required' => false))
			->add('jump', 'number', array('required' => false))
			->add('attack', 'number', array('required' => false))
			->add('defense', 'number', array('required' => false))
			->add('crew', 'number', array('required' => false))
			->add('computer', 'number', array('required' => false))
			->add('hardpoint', 'number', array('required' => false))
			->add('hull', 'number', array('required' => false))
			->add('internal', 'number', array('required' => false))
			->add('contractname', 'textarea', array('required' => false))
			->add('distance', 'number', array('required' => false))
			->add('contractrequirements', 'textarea', array('required' => false))
			->add('compslots', 'number', array('required' => false))
			->add('abandpenalty', 'number', array('required' => false))
			->add('victorypoints', 'number', array('required' => false))
			->add('subplots', 'textarea', array('required' => false))
			->add('complicationname', 'textarea', array('required' => false))
			->add('complicationtraits', 'textarea', array('required' => false))
			->add('complicationtext', 'textarea', array('required' => false))
			->add('abandpenmodifier', 'number', array('required' => false))
			->add('compflavortext', 'textarea', array('required' => false))
			->add('cost', 'number', array('required' => false))
			->add('expense', 'number', array('required' => false))
			->add('species', 'textarea', array('required' => false))
			->add('skills', 'textarea', array('required' => false))
			->add('wound', 'number', array('required' => false))
			->add('requiredskill', 'textarea', array('required' => false))
			->add('tonnagerequirement', 'number', array('required' => false))
			->add('structure', 'number', array('required' => false))
			->add('text', 'textarea', array('required' => false))
            ->add('flavor', 'textarea', array('required' => false))
            ->add('illustrator')
            ->add('octgn_id', 'number', array('required' => false))
            ->add('file', 'file', array('label' => 'Image File', 'mapped' => false, 'required' => false))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Card'
        ));
    }

    public function getName()
    {
    	return 'appbundle_cardtype';
    }
}
