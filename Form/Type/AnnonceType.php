<?php
namespace AnnoncesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('title', TextType::class)
        	->add('content', TextareaType::class)
        	->add('prix', NumberType::class, array('required' => false))
        	->add('category', EntityType::class, array('class' => 'AnnoncesBundle:Category', 'choice_label' => 'name'))
        	->add('city', TextType::class) 
        	->add('photos', CollectionType::class, array('label' => false, 'entry_type' => PhotoType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false))
        	->add('Add', SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnnoncesBundle\Entity\Annonce'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'annoncesbundle_annonce';
    }


}
