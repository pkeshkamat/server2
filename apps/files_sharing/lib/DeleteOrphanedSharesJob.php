<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2020-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */
namespace OCA\Files_Sharing;

use OCP\AppFramework\Db\TTransactional;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PDO;
use Psr\Log\LoggerInterface;
use function array_map;

/**
 * Delete all share entries that have no matching entries in the file cache table.
 */
class DeleteOrphanedSharesJob extends TimedJob {

	use TTransactional;

	private const CHUNK_SIZE = 1000;

	private const INTERVAL = 24 * 60 * 60; // 1 day

	private IDBConnection $db;

	private LoggerInterface $logger;

	/**
	 * sets the correct interval for this timed job
	 */
	public function __construct(
		ITimeFactory $time,
		IDBConnection $db,
		LoggerInterface $logger
	) {
		parent::__construct($time);

		$this->db = $db;

		$this->setInterval(self::INTERVAL); // 1 day
		$this->setTimeSensitivity(self::TIME_INSENSITIVE);
		$this->logger = $logger;
	}

	/**
	 * Makes the background job do its work
	 *
	 * @param array $argument unused argument
	 */
	public function run($argument) {
		$qbSelect = $this->db->getQueryBuilder();
		$qbSelect->select('id')
			->from('share', 's')
			->leftJoin('s', 'filecache', 'fc', $qbSelect->expr()->eq('s.file_source', 'fc.fileid'))
			->where($qbSelect->expr()->isNull('fc.fileid'))
			->setMaxResults(self::CHUNK_SIZE);
		$deleteQb = $this->db->getQueryBuilder();
		$deleteQb->delete('share')
			->where(
				$deleteQb->expr()->in('id', $deleteQb->createParameter('ids'), IQueryBuilder::PARAM_INT_ARRAY)
			);

		/**
		 * Read a chunk of orphan rows and delete them. Continue as long as the
		 * chunk is filled and time before the next cron run does not run out.
		 *
		 * Note: With isolation level READ COMMITTED, the database will allow
		 * other transactions to delete rows between our SELECT and DELETE. In
		 * that (unlikely) case, our DELETE will have fewer affected rows than
		 * IDs passed for the WHERE IN. If this happens while processing a full
		 * chunk, the logic below will stop prematurely.
		 * Note: The queries below are optimized for low database locking. They
		 * could be combined into one single DELETE with join or sub query, but
		 * that has shown to (dead)lock often.
		 */
		$cutOff = $this->time->getTime() + self::INTERVAL;
		do {
			$deleted = $this->atomic(function () use ($qbSelect, $deleteQb) {
				$result = $qbSelect->executeQuery();
				$ids = array_map('intval', $result->fetchAll(PDO::FETCH_COLUMN));
				$result->closeCursor();
				$deleteQb->setParameter('ids', $ids, IQueryBuilder::PARAM_INT_ARRAY);
				$deleted = $deleteQb->executeStatement();
				$this->logger->debug('{deleted} orphaned share(s) deleted', [
					'app' => 'DeleteOrphanedSharesJob',
					'deleted' => $deleted,
				]);
				return $deleted;
			}, $this->db);
		} while ($deleted >= self::CHUNK_SIZE && $this->time->getTime() <= $cutOff);
	}
}
