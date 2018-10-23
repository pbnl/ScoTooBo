<?php
namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventAttendForm extends AbstractType
{
    private $build;
    private $options = array();
    private $actualElement;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'participationFields' => '',
            'loggedInUser_firstname' => '',
            'loggedInUser_lastname' => '',
            'loggedInUser_Stamm' => '',
            'staemme' => '',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->build = $builder;
        $this->options = $options;

        /**
         * NOTICE:
         * structure: tag, Label (german), checked, required field
         * options['participationFields'][$i][xxx (see above)]
         */
        for ($i=0; $i<count($this->options['participationFields']); $i++) {
            $this->actualElement = $i;
            switch ($this->options['participationFields'][$this->actualElement][0]) {
                case "name":
                    $this->buildFormName();
                    break;
                case "email":
                    $this->buildFormEmail();
                    break;
                case "address":
                    $this->buildFormAddress();
                    break;
                case "stamm":
                    $this->buildFormStamm();
                    break;
                case "group":
                    $this->buildFormGroup();
                    break;
                case "eat":
                    $this->buildFormEat();
                    break;
                case "comment":
                    $this->buildFormComment();
                    break;
            }
        }
        $this->buildFormSave();
    }

    private function buildFormName() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'firstname',
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.firstName"],
                    'label' => "general.firstName",
                    'empty_data' => '',
                    'data' => $this->options['loggedInUser_firstname'],
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            )
                ->add(
                    'lastname',
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.lastName"],
                        'label' => "general.lastName",
                        'empty_data' => '',
                        'data' => $this->options['loggedInUser_lastname'],
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                );
        }
    }

    private function buildFormEmail() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'email',
                EmailType::class,
                array(
                    "attr" => ["placeholder" => "general.mail"],
                    'label' => "general.mail",
                    'empty_data' => '',
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            );
        }
    }

    private function buildFormAddress() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'address_street',
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.street"],
                    'label' => "general.street",
                    'empty_data' => '',
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            )
                ->add(
                    'address_nr',
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.address_nr"],
                        'label' => "general.address_nr",
                        'empty_data' => '',
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                )
                ->add(
                    'address_plz',
                    IntegerType::class,
                    array(
                        "attr" => ["placeholder" => "general.postalCode"],
                        'label' => "general.postalCode",
                        'empty_data' => '',
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                )
                ->add(
                    'address_city',
                    TextType::class,
                    array(
                        "attr" => ["placeholder" => "general.place"],
                        'label' => "general.place",
                        'empty_data' => '',
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                );
        }
    }

    private function buildFormStamm() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'stamm',
                ChoiceType::class,
                array(
                    'label' => "general.stamm",
                    'choices' => $this->options['staemme'],
                    'choice_label' => function ($value, $key, $index) {
                        return $value;
                    },
                    'multiple' => false,
                    'empty_data' => '',
                    'data' => $this->options['loggedInUser_Stamm'],
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            );
        }
    }

    private function buildFormGroup() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'group',
                TextType::class,
                array(
                    "attr" => ["placeholder" => "general.group"],
                    'label' => "general.group",
                    'empty_data' => '',
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            );
        }
    }

    private function buildFormEat() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'pig',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'general.yes' => true,
                        'general.no' => false,
                    ),
                    'label' => 'general.pig',
                    'multiple' => false,
                    'expanded' => true,
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            )
                ->add(
                    'vegi',
                    ChoiceType::class,
                    array(
                        'choices' => array(
                            'general.yes' => true,
                            'general.no' => false,
                        ),
                        'label' => 'general.vegi',
                        'multiple' => false,
                        'expanded' => true,
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                )
                ->add(
                    'vega',
                    ChoiceType::class,
                    array(
                        'choices' => array(
                            'general.yes' => true,
                            'general.no' => false,
                        ),
                        'label' => 'general.vega',
                        'multiple' => false,
                        'expanded' => true,
                        "required" => $this->options['participationFields'][$this->actualElement][3]
                    )
                );
        }
    }

    private function buildFormComment() {
        if ($this->options['participationFields'][$this->actualElement][2]) {
            $this->build->add(
                'comment',
                TextareaType::class,
                array(
                    "attr" => ["placeholder" => "general.comment"],
                    'label' => "general.comment",
                    'empty_data' => '',
                    "required" => $this->options['participationFields'][$this->actualElement][3]
                )
            );
        }
    }

    private function buildFormSave() {
        $this->build->add('save', SubmitType::class, array(
            "label" => "Event.attendInvitationLink.submit",
            "attr" => ["class" => "btn btn-lg btn-primary btn-block"]));
    }

}
