<?php
/**
 * @author Björn Schießle <schiessle@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace Test\Accounts;

use OC\Accounts\Account;
use OC\Accounts\AccountManager;
use OCP\Accounts\IAccountManager;
use OCP\BackgroundJob\IJobList;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IUser;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Test\TestCase;

/**
 * Class AccountManagerTest
 *
 * @group DB
 * @package Test\Accounts
 */
class AccountManagerTest extends TestCase {

	/** @var  \OCP\IDBConnection */
	private $connection;

	/** @var  IConfig|MockObject */
	private $config;

	/** @var  EventDispatcherInterface|MockObject */
	private $eventDispatcher;

	/** @var  IJobList|MockObject */
	private $jobList;

	/** @var string accounts table name */
	private $table = 'accounts';

	/** @var LoggerInterface|MockObject */
	private $logger;

	/** @var AccountManager */
	private $accountManager;

	protected function setUp(): void {
		parent::setUp();
		$this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
		$this->connection = \OC::$server->get(IDBConnection::class);
		$this->config = $this->createMock(IConfig::class);
		$this->jobList = $this->createMock(IJobList::class);
		$this->logger = $this->createMock(LoggerInterface::class);

		$this->accountManager = new AccountManager(
			$this->connection,
			$this->config,
			$this->eventDispatcher,
			$this->jobList,
			$this->logger,
		);
	}

	protected function tearDown(): void {
		parent::tearDown();
		$query = $this->connection->getQueryBuilder();
		$query->delete($this->table)->execute();
	}

	protected function makeUser(string $uid, string $name, string $email = null): IUser {
		$user = $this->createMock(IUser::class);
		$user->expects($this->any())
			->method('getUid')
			->willReturn($uid);
		$user->expects($this->any())
			->method('getDisplayName')
			->willReturn($name);
		if ($email !== null) {
			$user->expects($this->any())
				->method('getEMailAddress')
				->willReturn($email);
		}

		return $user;
	}

	protected function populateOrUpdate(): void {
		$users = [
			[
				'user' => $this->makeUser('j.doe', 'Jane Doe', 'jane.doe@acme.com'),
				'data' => [
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Jane Doe', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'jane.doe@acme.com', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://acme.com', 'scope' => IAccountManager::SCOPE_PRIVATE],
				],
			],
			[
				'user' => $this->makeUser('a.allison', 'Alice Allison', 'a.allison@example.org'),
				'data' => [
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Alice Allison', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'a.allison@example.org', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@a_alice', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491602312121', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'Dundee Road 45', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_LOCAL],
				],
			],
			[
				'user' => $this->makeUser('b32c5a5b-1084-4380-8856-e5223b16de9f', 'Armel Oliseh', 'oliseh@example.com'),
				'data' => [
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Armel Oliseh', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'oliseh@example.com', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491603121212', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'Sunflower Blvd. 77', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.com', 'scope' => IAccountManager::SCOPE_PUBLISHED],
				],
			],
			[
				'user' => $this->makeUser('31b5316a-9b57-4b17-970a-315a4cbe73eb', 'K. Cheng', 'cheng@emca.com'),
				'data' => [
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'K. Cheng', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'cheng@emca.com', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_PHONE => ['value' => '+71601212123', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'Pinapple Street 22', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://emca.com', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::COLLECTION_EMAIL => [
						['value' => 'k.cheng@emca.com', 'scope' => IAccountManager::SCOPE_LOCAL],
						['value' => 'kai.cheng@emca.com', 'scope' => IAccountManager::SCOPE_LOCAL],
					],
				],
			],
			[
				'user' => $this->makeUser('goodpal@elpmaxe.org', 'Goodpal, Kim', 'goodpal@elpmaxe.org'),
				'data' => [
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Goodpal, Kim', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'goodpal@elpmaxe.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_PHONE => ['value' => '+71602121231', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'Octopus Ave 17', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://elpmaxe.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
				],
			],
		];
		foreach ($users as $userInfo) {
			$this->accountManager->updateUser($userInfo['user'], $userInfo['data'], false);
		}
	}

	/**
	 * get a instance of the accountManager
	 *
	 * @param array $mockedMethods list of methods which should be mocked
	 * @return MockObject | AccountManager
	 */
	public function getInstance($mockedMethods = null) {
		return $this->getMockBuilder(AccountManager::class)
			->setConstructorArgs([
				$this->connection,
				$this->config,
				$this->eventDispatcher,
				$this->jobList,
				$this->logger,
			])
			->setMethods($mockedMethods)
			->getMock();
	}

	/**
	 * @dataProvider dataTrueFalse
	 *
	 * @param array $newData
	 * @param array $oldData
	 * @param bool $insertNew
	 * @param bool $updateExisting
	 */
	public function testUpdateUser($newData, $oldData, $insertNew, $updateExisting) {
		$accountManager = $this->getInstance(['getUser', 'insertNewUser', 'updateExistingUser', 'updateVerifyStatus', 'checkEmailVerification']);
		/** @var IUser $user */
		$user = $this->createMock(IUser::class);

		// FIXME: should be an integration test instead of this abomination
		$accountManager->expects($this->once())->method('getUser')->with($user)->willReturn($oldData);

		if ($updateExisting) {
			$accountManager->expects($this->once())->method('checkEmailVerification')
				->with($oldData, $newData, $user)->willReturn($newData);
			$accountManager->expects($this->once())->method('updateVerifyStatus')
				->with($oldData, $newData)->willReturn($newData);
			$accountManager->expects($this->once())->method('updateExistingUser')
				->with($user, $newData);
			$accountManager->expects($this->never())->method('insertNewUser');
		}
		if ($insertNew) {
			$accountManager->expects($this->once())->method('insertNewUser')
				->with($user, $newData);
			$accountManager->expects($this->never())->method('updateExistingUser');
		}

		if (!$insertNew && !$updateExisting) {
			$accountManager->expects($this->never())->method('updateExistingUser');
			$accountManager->expects($this->never())->method('checkEmailVerification');
			$accountManager->expects($this->never())->method('updateVerifyStatus');
			$accountManager->expects($this->never())->method('insertNewUser');
			$this->eventDispatcher->expects($this->never())->method('dispatch');
		} else {
			$this->eventDispatcher->expects($this->once())->method('dispatch')
				->willReturnCallback(
					function ($eventName, $event) use ($user, $newData) {
						$this->assertSame('OC\AccountManager::userUpdated', $eventName);
						$this->assertInstanceOf(GenericEvent::class, $event);
						/** @var GenericEvent $event */
						$this->assertSame($user, $event->getSubject());
						$this->assertSame($newData, $event->getArguments());
					}
				);
		}

		$accountManager->updateUser($user, $newData);
	}

	public function dataTrueFalse() {
		return [
			[['myProperty' => ['value' => 'newData']], ['myProperty' => ['value' => 'oldData']], false, true],
			[['myProperty' => ['value' => 'newData']], [], true, false],
			[['myProperty' => ['value' => 'oldData']], ['myProperty' => ['value' => 'oldData']], false, false]
		];
	}

	public function updateUserSetScopeProvider() {
		return [
			// regular scope switching
			[
				[
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_AVATAR => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_PRIVATE],
				],
				[
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PRIVATE],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
				],
				[
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PRIVATE],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
				],
			],
			// legacy scope mapping, the given visibility values get converted to scopes
			[
				[
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_PRIVATE],
				],
				[
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::VISIBILITY_PUBLIC],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::VISIBILITY_CONTACTS_ONLY],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::VISIBILITY_PRIVATE],
				],
				[
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_LOCAL],
				],
			],
			// invalid or unsupported scope values get converted to SCOPE_LOCAL
			[
				[
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
				],
				[
					// SCOPE_PRIVATE is not allowed for display name and email
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_PRIVATE],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_PRIVATE],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_LOCAL],
				],
				[
					IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_LOCAL],
				],
				false, false,
			],
			// illegal scope values
			[
				[
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => IAccountManager::SCOPE_FEDERATED],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => IAccountManager::SCOPE_LOCAL],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => IAccountManager::SCOPE_PRIVATE],
				],
				[
					IAccountManager::PROPERTY_PHONE => ['value' => '+491601231212', 'scope' => ''],
					IAccountManager::PROPERTY_ADDRESS => ['value' => 'some street', 'scope' => 'v2-invalid'],
					IAccountManager::PROPERTY_WEBSITE => ['value' => 'https://example.org', 'scope' => 'invalid'],
				],
				[],
				true, true
			],
			// invalid or unsupported scope values throw an exception when passing $throwOnData=true
			[
				[IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_PUBLISHED]],
				[IAccountManager::PROPERTY_DISPLAYNAME => ['value' => 'Display Name', 'scope' => IAccountManager::SCOPE_PRIVATE]],
				null,
				// throw exception
				true, true,
			],
			[
				[IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_PUBLISHED]],
				[IAccountManager::PROPERTY_EMAIL => ['value' => 'test@example.org', 'scope' => IAccountManager::SCOPE_PRIVATE]],
				null,
				// throw exception
				true, true,
			],
			[
				[IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => IAccountManager::SCOPE_PUBLISHED]],
				[IAccountManager::PROPERTY_TWITTER => ['value' => '@sometwitter', 'scope' => 'invalid']],
				null,
				// throw exception
				true, true,
			],
		];
	}

	/**
	 * @dataProvider updateUserSetScopeProvider
	 */
	public function testUpdateUserSetScope($oldData, $newData, $savedData, $throwOnData = true, $expectedThrow = false) {
		$accountManager = $this->getInstance(['getUser', 'insertNewUser', 'updateExistingUser', 'updateVerifyStatus', 'checkEmailVerification']);
		/** @var IUser $user */
		$user = $this->createMock(IUser::class);

		$accountManager->expects($this->once())->method('getUser')->with($user)->willReturn($oldData);

		if ($expectedThrow) {
			$accountManager->expects($this->never())->method('updateExistingUser');
			$this->expectException(\InvalidArgumentException::class);
			$this->expectExceptionMessage('scope');
		} else {
			$accountManager->expects($this->once())->method('checkEmailVerification')
				->with($oldData, $savedData, $user)->willReturn($savedData);
			$accountManager->expects($this->once())->method('updateVerifyStatus')
				->with($oldData, $savedData)->willReturn($savedData);
			$accountManager->expects($this->once())->method('updateExistingUser')
				->with($user, $savedData);
			$accountManager->expects($this->never())->method('insertNewUser');
		}

		$accountManager->updateUser($user, $newData, $throwOnData);
	}

	/**
	 * @dataProvider dataTestGetUser
	 *
	 * @param string $setUser
	 * @param array $setData
	 * @param IUser $askUser
	 * @param array $expectedData
	 * @param bool $userAlreadyExists
	 */
	public function testGetUser($setUser, $setData, $askUser, $expectedData, $userAlreadyExists) {
		$accountManager = $this->getInstance(['buildDefaultUserRecord', 'insertNewUser', 'addMissingDefaultValues']);
		if (!$userAlreadyExists) {
			$accountManager->expects($this->once())->method('buildDefaultUserRecord')
				->with($askUser)->willReturn($expectedData);
			$accountManager->expects($this->once())->method('insertNewUser')
				->with($askUser, $expectedData);
		}

		if (empty($expectedData)) {
			$accountManager->expects($this->never())->method('addMissingDefaultValues');
		} else {
			$accountManager->expects($this->once())->method('addMissingDefaultValues')->with($expectedData)
				->willReturn($expectedData);
		}

		$this->addDummyValuesToTable($setUser, $setData);
		$this->assertEquals($expectedData,
			$accountManager->getUser($askUser)
		);
	}

	public function dataTestGetUser() {
		$user1 = $this->getMockBuilder(IUser::class)->getMock();
		$user1->expects($this->any())->method('getUID')->willReturn('user1');
		$user2 = $this->getMockBuilder(IUser::class)->getMock();
		$user2->expects($this->any())->method('getUID')->willReturn('user2');
		return [
			['user1', ['key' => 'value'], $user1, ['key' => 'value'], true],
			['user1', ['key' => 'value'], $user2, [], false],
		];
	}

	public function testUpdateExistingUser() {
		$user = $this->getMockBuilder(IUser::class)->getMock();
		$user->expects($this->atLeastOnce())->method('getUID')->willReturn('uid');
		$oldData = ['key' => ['value' => 'value']];
		$newData = ['newKey' => ['value' => 'newValue']];

		$this->addDummyValuesToTable('uid', $oldData);
		$this->invokePrivate($this->accountManager, 'updateExistingUser', [$user, $newData]);
		$newDataFromTable = $this->getDataFromTable('uid');
		$this->assertEquals($newData, $newDataFromTable);
	}

	public function testInsertNewUser() {
		$user = $this->getMockBuilder(IUser::class)->getMock();
		$uid = 'uid';
		$data = ['key' => ['value' => 'value']];

		$user->expects($this->atLeastOnce())->method('getUID')->willReturn($uid);
		$this->assertNull($this->getDataFromTable($uid));
		$this->invokePrivate($this->accountManager, 'insertNewUser', [$user, $data]);

		$dataFromDb = $this->getDataFromTable($uid);
		$this->assertEquals($data, $dataFromDb);
	}

	public function testAddMissingDefaultValues() {
		$input = [
			'key1' => ['value' => 'value1', 'verified' => '0'],
			'key2' => ['value' => 'value1'],
		];

		$expected = [
			'key1' => ['value' => 'value1', 'verified' => '0'],
			'key2' => ['value' => 'value1', 'verified' => '0'],
		];

		$result = $this->invokePrivate($this->accountManager, 'addMissingDefaultValues', [$input]);

		$this->assertSame($expected, $result);
	}

	private function addDummyValuesToTable($uid, $data) {
		$query = $this->connection->getQueryBuilder();
		$query->insert($this->table)
			->values(
				[
					'uid' => $query->createNamedParameter($uid),
					'data' => $query->createNamedParameter(json_encode($data)),
				]
			)
			->execute();
	}

	private function getDataFromTable($uid) {
		$query = $this->connection->getQueryBuilder();
		$query->select('data')->from($this->table)
			->where($query->expr()->eq('uid', $query->createParameter('uid')))
			->setParameter('uid', $uid);
		$query->execute();

		$qResult = $query->execute();
		$result = $qResult->fetchAll();
		$qResult->closeCursor();

		if (!empty($result)) {
			return json_decode($result[0]['data'], true);
		}
	}

	public function testGetAccount() {
		$accountManager = $this->getInstance(['getUser']);
		/** @var IUser $user */
		$user = $this->createMock(IUser::class);

		$data = [
			IAccountManager::PROPERTY_TWITTER =>
				[
					'value' => '@twitterhandle',
					'scope' => IAccountManager::SCOPE_LOCAL,
					'verified' => IAccountManager::NOT_VERIFIED,
				],
			IAccountManager::PROPERTY_EMAIL =>
				[
					'value' => 'test@example.com',
					'scope' => IAccountManager::SCOPE_PUBLISHED,
					'verified' => IAccountManager::VERIFICATION_IN_PROGRESS,
				],
			IAccountManager::PROPERTY_WEBSITE =>
				[
					'value' => 'https://example.com',
					'scope' => IAccountManager::SCOPE_FEDERATED,
					'verified' => IAccountManager::VERIFIED,
				],
		];
		$expected = new Account($user);
		$expected->setProperty(IAccountManager::PROPERTY_TWITTER, '@twitterhandle', IAccountManager::SCOPE_LOCAL, IAccountManager::NOT_VERIFIED);
		$expected->setProperty(IAccountManager::PROPERTY_EMAIL, 'test@example.com', IAccountManager::SCOPE_PUBLISHED, IAccountManager::VERIFICATION_IN_PROGRESS);
		$expected->setProperty(IAccountManager::PROPERTY_WEBSITE, 'https://example.com', IAccountManager::SCOPE_FEDERATED, IAccountManager::VERIFIED);

		$accountManager->expects($this->once())
			->method('getUser')
			->willReturn($data);
		$this->assertEquals($expected, $accountManager->getAccount($user));
	}

	public function dataParsePhoneNumber(): array {
		return [
			['0711 / 25 24 28-90', 'DE', '+4971125242890'],
			['0711 / 25 24 28-90', '', null],
			['+49 711 / 25 24 28-90', '', '+4971125242890'],
		];
	}

	/**
	 * @dataProvider dataParsePhoneNumber
	 * @param string $phoneInput
	 * @param string $defaultRegion
	 * @param string|null $phoneNumber
	 */
	public function testParsePhoneNumber(string $phoneInput, string $defaultRegion, ?string $phoneNumber): void {
		$this->config->method('getSystemValueString')
			->willReturn($defaultRegion);

		if ($phoneNumber === null) {
			$this->expectException(\InvalidArgumentException::class);
			self::invokePrivate($this->accountManager, 'parsePhoneNumber', [$phoneInput]);
		} else {
			self::assertEquals($phoneNumber, self::invokePrivate($this->accountManager, 'parsePhoneNumber', [$phoneInput]));
		}
	}

	public function dataParseWebsite(): array {
		return [
			['https://nextcloud.com', 'https://nextcloud.com'],
			['http://nextcloud.com', 'http://nextcloud.com'],
			['ftp://nextcloud.com', null],
			['//nextcloud.com/', null],
			['https:///?query', null],
		];
	}

	/**
	 * @dataProvider dataParseWebsite
	 * @param string $websiteInput
	 * @param string|null $websiteOutput
	 */
	public function testParseWebsite(string $websiteInput, ?string $websiteOutput): void {
		if ($websiteOutput === null) {
			$this->expectException(\InvalidArgumentException::class);
			self::invokePrivate($this->accountManager, 'parseWebsite', [$websiteInput]);
		} else {
			self::assertEquals($websiteOutput, self::invokePrivate($this->accountManager, 'parseWebsite', [$websiteInput]));
		}
	}

	/**
	 * @dataProvider searchDataProvider
	 */
	public function testSearchUsers(string $property, array $values, array $expected): void {
		$this->populateOrUpdate();

		$matchedUsers = $this->accountManager->searchUsers($property, $values);
		$this->assertSame($expected, $matchedUsers);
	}

	public function searchDataProvider(): array {
		return [
			[ #0 Search for an existing name
				IAccountManager::PROPERTY_DISPLAYNAME,
				['Jane Doe'],
				['Jane Doe' => 'j.doe']
			],
			[ #1 Search for part of a name (no result)
				IAccountManager::PROPERTY_DISPLAYNAME,
				['Jane'],
				[]
			],
			[ #2 Search for part of a name (no result, test wildcard)
				IAccountManager::PROPERTY_DISPLAYNAME,
				['Jane%'],
				[]
			],
			[ #3 Search for phone
				IAccountManager::PROPERTY_PHONE,
				['+491603121212'],
				['+491603121212' => 'b32c5a5b-1084-4380-8856-e5223b16de9f'],
			],
			[ #4 Search for twitter handles
				IAccountManager::PROPERTY_TWITTER,
				['@sometwitter', '@a_alice', '@unseen'],
				['@sometwitter' => 'j.doe', '@a_alice' => 'a.allison'],
			],
			[ #5 Search for email
				IAccountManager::PROPERTY_EMAIL,
				['cheng@emca.com'],
				['cheng@emca.com' => '31b5316a-9b57-4b17-970a-315a4cbe73eb'],
			],
			[ #6 Search for email by additional email
				IAccountManager::PROPERTY_EMAIL,
				['kai.cheng@emca.com'],
				['kai.cheng@emca.com' => '31b5316a-9b57-4b17-970a-315a4cbe73eb'],
			],
			[ #7 Search for additional email
				IAccountManager::COLLECTION_EMAIL,
				['kai.cheng@emca.com', 'cheng@emca.com'],
				['kai.cheng@emca.com' => '31b5316a-9b57-4b17-970a-315a4cbe73eb'],
			],
			[ #8 Search for email by additional email (two valid search values, but the same user)
				IAccountManager::PROPERTY_EMAIL,
				['kai.cheng@emca.com', 'cheng@emca.com'],
				[
					'kai.cheng@emca.com' => '31b5316a-9b57-4b17-970a-315a4cbe73eb',
				],
			],
		];
	}
}
