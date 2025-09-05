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
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|null
    {
        if (!$data instanceof Definition) {
            return null;
        }

        $map = [];

        if (($name = $data->getName()) instanceof LanguageMap) {
            $map['name'] = $this->normalizeAttribute($name, $format, $context);
        }

        if (($description = $data->getDescription()) instanceof LanguageMap) {
            $map['description'] = $this->normalizeAttribute($description, $format, $context);
        }

        if (($type = $data->getType()) instanceof IRI) {
            $map['type'] = $type->getValue();
        }

        if (($moreInfo = $data->getMoreInfo()) instanceof IRL) {
            $map['moreInfo'] = $moreInfo->getValue();
        }

        if (($extensions = $data->getExtensions()) instanceof Extensions) {
            $map['extensions'] = $this->normalizeAttribute($extensions, $format, $context);
        }

        if ($data instanceof InteractionDefinition) {
            if (null !== $data->getCorrectResponsesPattern()) {
                $map['correctResponsesPattern'] = $data->getCorrectResponsesPattern();
            }

            switch (true) {
                case $data instanceof ChoiceInteractionDefinition:
                    $map['interactionType'] = 'choice';

                    if (null !== $choices = $data->getChoices()) {
                        $map['choices'] = $this->normalizeAttribute($choices, $format, $context);
                    }

                    break;
                case $data instanceof FillInInteractionDefinition:
                    $map['interactionType'] = 'fill-in';
                    break;
                case $data instanceof LikertInteractionDefinition:
                    $map['interactionType'] = 'likert';

                    if (null !== $scale = $data->getScale()) {
                        $map['scale'] = $this->normalizeAttribute($scale, $format, $context);
                    }

                    break;
                case $data instanceof LongFillInInteractionDefinition:
                    $map['interactionType'] = 'long-fill-in';
                    break;
                case $data instanceof MatchingInteractionDefinition:
                    $map['interactionType'] = 'matching';

                    if (null !== $source = $data->getSource()) {
                        $map['source'] = $this->normalizeAttribute($source, $format, $context);
                    }

                    if (null !== $target = $data->getTarget()) {
                        $map['target'] = $this->normalizeAttribute($target, $format, $context);
                    }

                    break;
                case $data instanceof NumericInteractionDefinition:
                    $map['interactionType'] = 'numeric';
                    break;
                case $data instanceof OtherInteractionDefinition:
                    $map['interactionType'] = 'other';
                    break;
                case $data instanceof PerformanceInteractionDefinition:
                    $map['interactionType'] = 'performance';

                    if (null !== $steps = $data->getSteps()) {
                        $map['steps'] = $this->normalizeAttribute($steps, $format, $context);
                    }

                    break;
                case $data instanceof SequencingInteractionDefinition:
                    $map['interactionType'] = 'sequencing';

                    if (null !== $choices = $data->getChoices()) {
                        $map['choices'] = $this->normalizeAttribute($choices, $format, $context);
                    }

                    break;
                case $data instanceof TrueFalseInteractionDefinition:
                    $map['interactionType'] = 'true-false';
                    break;
            }
        }

        if (empty($map)) {
            return new ArrayObject();
        }

        return $map;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Definition;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, $type, ?string $format = null, array $context = []): mixed
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
    public function supportsDenormalization(mixed $data, $type, ?string $format = null, array $context = []): bool
    {
        $supportedDefinitionClasses = [Definition::class, ChoiceInteractionDefinition::class, FillInInteractionDefinition::class, LikertInteractionDefinition::class, LongFillInInteractionDefinition::class, MatchingInteractionDefinition::class, NumericInteractionDefinition::class, OtherInteractionDefinition::class, PerformanceInteractionDefinition::class, SequencingInteractionDefinition::class, TrueFalseInteractionDefinition::class];

        return in_array($type, $supportedDefinitionClasses, true);
    }
}
