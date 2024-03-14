<?php

declare(strict_types=1);

/**
 * @author Eduardo Morales emoral435@gmail.com>
 *
 * @license GNU AGPL-3.0-or-later
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\Files_Versions\Listener;

use OCA\Files_Versions\Versions\IVersionManager;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\Node\BeforeNodeRenamedEvent;
use OCP\Files\File;
use OCP\IUserSession;

/** @template-implements IEventListener<BeforeNodeRenamedEvent> */
class VersionStorageMoveListener implements IEventListener {
	public function __construct(
		private IVersionManager $versionManager,
		private IUserSession $userSession,
	) {
	}

	/**
	 * @abstract Moves version across storages if necessary.
	 * @param Event $event
	 */
	public function handle(Event $event): void {
		/** @var BeforeNodeRenamedEvent $event **/

		$source = $event->getSource();
		$target = $event->getTarget();

		if (!($target instanceof File)) {
			return;
		}

		$user = $this->userSession->getUser();

		$this->versionManager->moveVersionsAcrossBackends($user, $source, $target);
	}
}
