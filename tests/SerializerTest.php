<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Serializer\Symfony\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Xabbuh\XApi\DataFixtures\AttachmentFixtures;
use Xabbuh\XApi\Model\Account;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Attachment;
use Xabbuh\XApi\Model\Context;
use Xabbuh\XApi\Model\ContextActivities;
use Xabbuh\XApi\Model\Definition;
use Xabbuh\XApi\Model\Extensions;
use Xabbuh\XApi\Model\Interaction\InteractionComponent;
use Xabbuh\XApi\Model\Result;
use Xabbuh\XApi\Model\Score;
use Xabbuh\XApi\Model\StatementReference;
use Xabbuh\XApi\Model\SubStatement;
use Xabbuh\XApi\Model\Verb;
use Xabbuh\XApi\Serializer\Symfony\Serializer;
use XApi\Fixtures\Json\AttachmentJsonFixtures;

class SerializerTest extends TestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = Serializer::createSerializer();
    }

    #[DataProvider('serializeAccountData')]
    public function testSerializeAccount(Account $account, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($account, 'json'));
    }

    public static function serializeAccountData(): array
    {
        return self::buildSerializeTestCases('Account');
    }

    #[DataProvider('deserializeAccountData')]
    public function testDeserializeAccount($json, Account $expectedAccount): void
    {
        $account = $this->serializer->deserialize($json, Account::class, 'json');

        $this->assertInstanceOf(Account::class, $account);
        $this->assertTrue($expectedAccount->equals($account), 'Deserialized account has the expected properties');
    }

    public static function deserializeAccountData(): array
    {
        return self::buildDeserializeTestCases('Account');
    }

    #[DataProvider('serializeActorData')]
    public function testSerializeActor(Actor $actor, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($actor, 'json'));
    }

    public static function serializeActorData(): array
    {
        return self::buildSerializeTestCases('Actor');
    }

    #[DataProvider('deserializeActorData')]
    public function testDeserializeActor($json, Actor $expectedActor): void
    {
        $actor = $this->serializer->deserialize($json, Actor::class, 'json');

        $this->assertInstanceOf(Actor::class, $actor);
        $this->assertTrue($expectedActor->equals($actor), 'Deserialized actor has the expected properties');
    }

    public static function deserializeActorData(): array
    {
        return self::buildDeserializeTestCases('Actor');
    }

    #[DataProvider('serializeActivityData')]
    public function testSerializeActivity(Activity $activity, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($activity, 'json'));
    }

    public static function serializeActivityData(): array
    {
        return self::buildSerializeTestCases('Activity');
    }

    #[DataProvider('deserializeActivityData')]
    public function testDeserializeActivity($json, Activity $expectedActivity): void
    {
        $activity = $this->serializer->deserialize($json, Activity::class, 'json');

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertTrue($expectedActivity->equals($activity), 'Deserialized activity has the expected properties');
    }

    public static function deserializeActivityData(): array
    {
        return self::buildDeserializeTestCases('Activity');
    }

    #[DataProvider('serializeAttachmentData')]
    public function testSerializeAttachment(Attachment $attachment, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($attachment, 'json'));
    }

    /**
     * @return array[]
     */
    public static function serializeAttachmentData(): array
    {
        $tests = [];

        foreach (get_class_methods(AttachmentFixtures::class) as $method) {
            if (str_contains($method, 'ForQuery')) {
                continue;
            }

            $jsonFixture = json_decode((string)call_user_func([AttachmentJsonFixtures::class, $method]), false);

            $tests[$method] = [call_user_func([AttachmentFixtures::class, $method]), json_encode($jsonFixture->metadata)];
        }

        return $tests;
    }

    #[DataProvider('deserializeAttachmentData')]
    public function testDeserializeAttachment($json, $content, Attachment $expectedAttachment): void
    {
        $context = [];

        if (null !== $content) {
            $context['xapi_attachments'] = [hash('sha256', (string)$content) => ['content' => $content]];
        }

        $attachment = $this->serializer->deserialize($json, Attachment::class, 'json', $context);

        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertTrue($expectedAttachment->equals($attachment), 'Deserialized attachment has the expected properties');
    }

    /**
     * @return array
     */
    public static function deserializeAttachmentData(): array
    {
        $tests = [];

        foreach (get_class_methods(AttachmentJsonFixtures::class) as $method) {
            $jsonFixture = json_decode((string)call_user_func([AttachmentJsonFixtures::class, $method]), false);
            $tests[$method] = [json_encode($jsonFixture->metadata), $jsonFixture->content ?? null, call_user_func([AttachmentFixtures::class, $method])];
        }

        return $tests;
    }

    #[DataProvider('serializeContextData')]
    public function testSerializeContext(Context $context, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($context, 'json'));
    }

    public static function serializeContextData(): array
    {
        return self::buildSerializeTestCases('Context');
    }

    #[DataProvider('deserializeContextData')]
    public function testDeserializeContext($json, Context $expectedContext): void
    {
        $context = $this->serializer->deserialize($json, Context::class, 'json');

        $this->assertInstanceOf(Context::class, $context);
        $this->assertEquals($context, $expectedContext, 'Deserialized context has the expected properties');
    }

    public static function deserializeContextData(): array
    {
        return self::buildDeserializeTestCases('Context');
    }

    #[DataProvider('serializeContextActivitiesData')]
    public function testSerializeContextActivities(ContextActivities $contextActivities, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($contextActivities, 'json'));
    }

    public static function serializeContextActivitiesData(): array
    {
        return self::buildSerializeTestCases('ContextActivities');
    }

    #[DataProvider('deserializeContextActivitiesData')]
    public function testDeserializeContextActivities($json, ContextActivities $expectedContextActivities): void
    {
        $contextActivities = $this->serializer->deserialize($json, ContextActivities::class, 'json');

        $this->assertInstanceOf(ContextActivities::class, $contextActivities);
        $this->assertEquals($contextActivities, $expectedContextActivities, 'Deserialized context activities have the expected properties');
    }

    public static function deserializeContextActivitiesData(): array
    {
        return self::buildDeserializeTestCases('ContextActivities');
    }

    #[DataProvider('serializeDefinitionData')]
    public function testSerializeDefinition(Definition $definition, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($definition, 'json'));
    }

    public static function serializeDefinitionData(): array
    {
        return self::buildSerializeTestCases('Definition');
    }

    #[DataProvider('deserializeDefinitionData')]
    public function testDeserializeDefinition($json, Definition $expectedDefinition): void
    {
        $expectedClass = $expectedDefinition::class;
        $definition = $this->serializer->deserialize($json, $expectedClass, 'json');

        $this->assertInstanceOf($expectedClass, $definition, sprintf('Deserialized definition is an instance of "%s"', $expectedClass));
        $this->assertTrue($expectedDefinition->equals($definition), 'Deserialized definition has the expected properties');
    }

    public static function deserializeDefinitionData(): array
    {
        return self::buildDeserializeTestCases('Definition');
    }

    #[DataProvider('serializeExtensionsData')]
    public function testSerializeExtensions(Extensions $extensions, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($extensions, 'json'));
    }

    public static function serializeExtensionsData(): array
    {
        return self::buildSerializeTestCases('Extensions');
    }

    #[DataProvider('deserializeExtensionsData')]
    public function testDeserializeExtensions($json, Extensions $expectedExtensions): void
    {
        $extensions = $this->serializer->deserialize($json, Extensions::class, 'json');

        $this->assertInstanceOf(Extensions::class, $extensions);
        $this->assertTrue($expectedExtensions->equals($expectedExtensions), 'Deserialized extensions have the expected properties');
    }

    public static function deserializeExtensionsData(): array
    {
        return self::buildDeserializeTestCases('Extensions');
    }

    #[DataProvider('serializeInteractionComponentData')]
    public function testSerializeInteractionComponent(InteractionComponent $interactionComponent, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($interactionComponent, 'json'));
    }

    public static function serializeInteractionComponentData(): array
    {
        return self::buildSerializeTestCases('InteractionComponent');
    }

    #[DataProvider('deserializeInteractionComponentData')]
    public function testDeserializeInteractionComponent($json, InteractionComponent $expectedInteractionComponent): void
    {
        $interactionComponent = $this->serializer->deserialize($json, InteractionComponent::class, 'json');

        $this->assertInstanceOf(InteractionComponent::class, $interactionComponent);
        $this->assertTrue($expectedInteractionComponent->equals($interactionComponent), 'Deserialized interaction component has the expected properties');
    }

    public static function deserializeInteractionComponentData(): array
    {
        return self::buildDeserializeTestCases('InteractionComponent');
    }

    #[DataProvider('serializeResultData')]
    public function testSerializeResult(Result $result, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($result, 'json'));
    }

    public static function serializeResultData(): array
    {
        return self::buildSerializeTestCases('Result');
    }

    #[DataProvider('deserializeResultData')]
    public function testDeserializeResult($json, Result $expectedResult): void
    {
        $result = $this->serializer->deserialize($json, Result::class, 'json');

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($expectedResult->equals($result), 'Deserialized result has the expected properties');
    }

    public static function deserializeResultData(): array
    {
        return self::buildDeserializeTestCases('Result');
    }

    #[DataProvider('serializeScoreData')]
    public function testSerializeScore(Score $score, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($score, 'json'));
    }

    public static function serializeScoreData(): array
    {
        return self::buildSerializeTestCases('Score');
    }

    #[DataProvider('deserializeScoreData')]
    public function testDeserializeScore($json, Score $expectedScore): void
    {
        $score = $this->serializer->deserialize($json, Score::class, 'json');

        $this->assertInstanceOf(Score::class, $score);
        $this->assertTrue($expectedScore->equals($score), 'Deserialized score has the expected properties');
    }

    public static function deserializeScoreData(): array
    {
        return self::buildDeserializeTestCases('Score');
    }

    #[DataProvider('serializeStatementReferenceData')]
    public function testSerializeStatementReference(StatementReference $statementReference, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($statementReference, 'json'));
    }

    public static function serializeStatementReferenceData(): array
    {
        return self::buildSerializeTestCases('StatementReference');
    }

    #[DataProvider('deserializeStatementReferenceData')]
    public function testDeserializeStatementReference($json, StatementReference $expectedStatementReference): void
    {
        $statementReference = $this->serializer->deserialize($json, StatementReference::class, 'json');

        $this->assertInstanceOf(StatementReference::class, $statementReference);
        $this->assertTrue($expectedStatementReference->equals($statementReference), 'Deserialized StatementReference has the expected properties');
    }

    public static function deserializeStatementReferenceData(): array
    {
        return self::buildDeserializeTestCases('StatementReference');
    }

    #[DataProvider('serializeSubStatementData')]
    public function testSerializeSubStatement(SubStatement $subStatement, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($subStatement, 'json'));
    }

    public static function serializeSubStatementData(): array
    {
        return self::buildSerializeTestCases('SubStatement');
    }

    #[DataProvider('deserializeSubStatementData')]
    public function testDeserializeSubStatement($json, SubStatement $expectedSubStatement): void
    {
        $subStatement = $this->serializer->deserialize($json, SubStatement::class, 'json');

        $this->assertInstanceOf(SubStatement::class, $subStatement);
        $this->assertTrue($expectedSubStatement->equals($subStatement), 'Deserialized SubStatement has the expected properties');
    }

    public static function deserializeSubStatementData(): array
    {
        return self::buildDeserializeTestCases('SubStatement');
    }

    #[DataProvider('serializeVerbData')]
    public function testSerializeVerb(Verb $verb, string $expectedJson): void
    {
        $this->assertJsonStringEqualsJsonString($expectedJson, $this->serializer->serialize($verb, 'json'));
    }

    public static function serializeVerbData(): array
    {
        return self::buildSerializeTestCases('Verb');
    }

    #[DataProvider('deserializeVerbData')]
    public function testDeserializeVerb($json, Verb $expectedVerb): void
    {
        $verb = $this->serializer->deserialize($json, Verb::class, 'json');

        $this->assertInstanceOf(Verb::class, $verb);
        $this->assertTrue($expectedVerb->equals($verb), 'Deserialized verb has the expected properties');
    }

    public static function deserializeVerbData(): array
    {
        return self::buildDeserializeTestCases('Verb');
    }

    private static function buildSerializeTestCases(string $objectType): array
    {
        $tests = [];

        $phpFixturesClass = 'Xabbuh\XApi\DataFixtures\\' . $objectType . 'Fixtures';
        $jsonFixturesClass = 'XApi\Fixtures\Json\\' . $objectType . 'JsonFixtures';
        $jsonFixturesMethods = get_class_methods($jsonFixturesClass);

        foreach (get_class_methods($phpFixturesClass) as $method) {
            if (str_contains($method, 'ForQuery')) {
                continue;
            }

            // serialized data will always contain type information
            $jsonMethod = in_array($method . 'WithType', $jsonFixturesMethods) ? $method . 'WithType' : $method;

            $tests[$method] = [call_user_func([$phpFixturesClass, $method]), call_user_func([$jsonFixturesClass, $jsonMethod])];
        }

        return $tests;
    }

    private static function buildDeserializeTestCases(string $objectType): array
    {
        $tests = [];

        $jsonFixturesClass = 'XApi\Fixtures\Json\\' . $objectType . 'JsonFixtures';
        $phpFixturesClass = 'Xabbuh\XApi\DataFixtures\\' . $objectType . 'Fixtures';

        foreach (get_class_methods($jsonFixturesClass) as $method) {
            // PHP objects do not contain the type information as a dedicated property
            if (str_ends_with($method, 'WithType')) {
                continue;
            }

            $tests[$method] = [call_user_func([$jsonFixturesClass, $method]), call_user_func([$phpFixturesClass, $method])];
        }

        return $tests;
    }
}
