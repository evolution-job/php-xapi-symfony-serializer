<?php

namespace Xabbuh\XApi\Serializer\Symfony\Normalizer;

use ArrayObject;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Xabbuh\XApi\Model\Definition;
use Xabbuh\XApi\Model\Extensions;
use Xabbuh\XApi\Model\Interaction\ChoiceInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\FillInInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\InteractionDefinition;
use Xabbuh\XApi\Model\Interaction\LikertInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\LongFillInInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\MatchingInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\NumericInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\OtherInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\PerformanceInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\SequencingInteractionDefinition;
use Xabbuh\XApi\Model\Interaction\TrueFalseInteractionDefinition;
use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\IRL;
use Xabbuh\XApi\Model\LanguageMap;

/**
 * Normalizes and denormalizes PHP arrays to {@link Definition} instances.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class DefinitionNormalizer extends Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$object instanceof Definition) {
            return null;
        }

        $data = [];

        if (($name = $object->getName()) instanceof LanguageMap) {
            $data['name'] = $this->normalizeAttribute($name, $format, $context);
        }

        if (($description = $object->getDescription()) instanceof LanguageMap) {
            $data['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        if (($type = $object->getType()) instanceof IRI) {
            $data['type'] = $type->getValue();
        }

        if (($moreInfo = $object->getMoreInfo()) instanceof IRL) {
            $data['moreInfo'] = $moreInfo->getValue();
        }

        if (($extensions = $object->getExtensions()) instanceof Extensions) {
            $data['extensions'] = $this->normalizeAttribute($extensions, $format, $context);
        }

        if ($object instanceof InteractionDefinition) {
            if (null !== $object->getCorrectResponsesPattern()) {
                $data['correctResponsesPattern'] = $object->getCorrectResponsesPattern();
            }

            switch (true) {
                case $object instanceof ChoiceInteractionDefinition:
                    $data['interactionType'] = 'choice';

                    if (null !== $choices = $object->getChoices()) {
                        $data['choices'] = $this->normalizeAttribute($choices, $format, $context);
                    }

                    break;
                case $object instanceof FillInInteractionDefinition:
                    $data['interactionType'] = 'fill-in';
                    break;
                case $object instanceof LikertInteractionDefinition:
                    $data['interactionType'] = 'likert';

                    if (null !== $scale = $object->getScale()) {
                        $data['scale'] = $this->normalizeAttribute($scale, $format, $context);
                    }

                    break;
                case $object instanceof LongFillInInteractionDefinition:
                    $data['interactionType'] = 'long-fill-in';
                    break;
                case $object instanceof MatchingInteractionDefinition:
                    $data['interactionType'] = 'matching';

                    if (null !== $source = $object->getSource()) {
                        $data['source'] = $this->normalizeAttribute($source, $format, $context);
                    }

                    if (null !== $target = $object->getTarget()) {
                        $data['target'] = $this->normalizeAttribute($target, $format, $context);
                    }

                    break;
                case $object instanceof NumericInteractionDefinition:
                    $data['interactionType'] = 'numeric';
                    break;
                case $object instanceof OtherInteractionDefinition:
                    $data['interactionType'] = 'other';
                    break;
                case $object instanceof PerformanceInteractionDefinition:
                    $data['interactionType'] = 'performance';

                    if (null !== $steps = $object->getSteps()) {
                        $data['steps'] = $this->normalizeAttribute($steps, $format, $context);
                    }

                    break;
                case $object instanceof SequencingInteractionDefinition:
                    $data['interactionType'] = 'sequencing';

                    if (null !== $choices = $object->getChoices()) {
                        $data['choices'] = $this->normalizeAttribute($choices, $format, $context);
                    }

                    break;
                case $object instanceof TrueFalseInteractionDefinition:
                    $data['interactionType'] = 'true-false';
                    break;
            }
        }

        if (empty($data)) {
            return new ArrayObject();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Definition;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($data['interactionType'])) {
            switch ($data['interactionType']) {
                case 'choice':
                    $definition = new ChoiceInteractionDefinition();

                    if (isset($data['choices'])) {
                        $definition = $definition->withChoices($this->denormalizeData($data['choices'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    break;
                case 'fill-in':
                    $definition = new FillInInteractionDefinition();
                    break;
                case 'likert':
                    $definition = new LikertInteractionDefinition();

                    if (isset($data['scale'])) {
                        $definition = $definition->withScale($this->denormalizeData($data['scale'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    break;
                case 'long-fill-in':
                    $definition = new LongFillInInteractionDefinition();
                    break;
                case 'matching':
                    $definition = new MatchingInteractionDefinition();

                    if (isset($data['source'])) {
                        $definition = $definition->withSource($this->denormalizeData($data['source'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    if (isset($data['target'])) {
                        $definition = $definition->withTarget($this->denormalizeData($data['target'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    break;
                case 'numeric':
                    $definition = new NumericInteractionDefinition();
                    break;
                case 'other':
                    $definition = new OtherInteractionDefinition();
                    break;
                case 'performance':
                    $definition = new PerformanceInteractionDefinition();

                    if (isset($data['steps'])) {
                        $definition = $definition->withSteps($this->denormalizeData($data['steps'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    break;
                case 'sequencing':
                    $definition = new SequencingInteractionDefinition();

                    if (isset($data['choices'])) {
                        $definition = $definition->withChoices($this->denormalizeData($data['choices'], 'Xabbuh\XApi\Model\Interaction\InteractionComponent[]', $format, $context));
                    }

                    break;
                case 'true-false':
                    $definition = new TrueFalseInteractionDefinition();
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('The interaction type "%s" is not supported.', $data['interactionType']));
            }

            if (isset($data['correctResponsesPattern'])) {
                $definition = $definition->withCorrectResponsesPattern($data['correctResponsesPattern']);
            }
        } else {
            $definition = new Definition();
        }

        if (isset($data['name'])) {
            $name = $this->denormalizeData($data['name'], LanguageMap::class, $format, $context);
            $definition = $definition->withName($name);
        }

        if (isset($data['description'])) {
            $description = $this->denormalizeData($data['description'], LanguageMap::class, $format, $context);
            $definition = $definition->withDescription($description);
        }

        if (isset($data['type'])) {
            $definition = $definition->withType(IRI::fromString($data['type']));
        }

        if (isset($data['moreInfo'])) {
            $definition = $definition->withMoreInfo(IRL::fromString($data['moreInfo']));
        }

        if (isset($data['extensions'])) {
            $extensions = $this->denormalizeData($data['extensions'], Extensions::class, $format, $context);
            $definition = $definition->withExtensions($extensions);
        }

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        $supportedDefinitionClasses = [Definition::class, ChoiceInteractionDefinition::class, FillInInteractionDefinition::class, LikertInteractionDefinition::class, LongFillInInteractionDefinition::class, MatchingInteractionDefinition::class, NumericInteractionDefinition::class, OtherInteractionDefinition::class, PerformanceInteractionDefinition::class, SequencingInteractionDefinition::class, TrueFalseInteractionDefinition::class];

        return in_array($type, $supportedDefinitionClasses, true);
    }
}
