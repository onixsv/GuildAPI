<?php

/*
 *
 *  /$$$$$$            /$$ /$$       /$$  /$$$$$$  /$$$$$$$  /$$$$$$
 * /$$__  $$          |__/| $$      | $$ /$$__  $$| $$__  $$|_  $$_/
 * | $$  \__/ /$$   /$$ /$$| $$  /$$$$$$$| $$  \ $$| $$  \ $$  | $$
 * | $$ /$$$$| $$  | $$| $$| $$ /$$__  $$| $$$$$$$$| $$$$$$$/  | $$
 * | $$|_  $$| $$  | $$| $$| $$| $$  | $$| $$__  $$| $$____/   | $$
 * | $$  \ $$| $$  | $$| $$| $$| $$  | $$| $$  | $$| $$        | $$
 * |  $$$$$$/|  $$$$$$/| $$| $$|  $$$$$$$| $$  | $$| $$       /$$$$$$
 *  \______/  \______/ |__/|__/ \_______/|__/  |__/|__/      |______/
 *
 * Copyright (C) 2020 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace alvin0319\GuildAPI;

use alvin0319\GuildAPI\store\GuildStorage;
use InvalidArgumentException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function array_keys;
use function count;
use function in_array;

class Guild{

	public const LEADER = "리더";

	public const CO_LEADER = "공동리더";

	public const MEMBER = "멤버";

	public const GUILD_TYPE_OPEN = "공개";

	public const GUILD_TYPE_CLOSED = "비공개";

	public const GUILD_TYPE_INVITATION = "초대 한정";

	public const GUILD_TYPE_REQUEST_JOIN = "가입 신청";

	public const PERMISSION_FORCE_QUIT = "forceQuit";

	public const PERMISSION_MANAGE_INVITATION = "manageInvitation";

	public const PERMISSION_MANAGE_ROLE = "manageRole";

	public const PERMISSION_SET_JOIN_TYPE = "setJoinType";

	public const PERMISSION_SHOP = "shop";

	protected string $name;

	protected array $players = [];

	/** @var GuildStorage */
	protected GuildStorage $guildStorage;

	protected string $guildJoinType = self::GUILD_TYPE_OPEN;

	public function __construct(string $name, array $players, ?GuildStorage $storage, string $guildJoinType){
		$this->name = TextFormat::clean($name);
		$this->players = $players;
		$this->guildStorage = $storage ?? new GuildStorage(0, false, false, 15, []);
		$this->guildStorage->setGuild($this);
		$this->guildJoinType = $guildJoinType;
	}

	public function getName() : string{
		return $this->name;
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function jsonSerialize() : array{
		return [
			"name" => $this->name,
			"players" => $this->players,
			"guildStorage" => $this->guildStorage->jsonSerialize(),
			"guildJoinType" => $this->guildJoinType
		];
	}

	public static function jsonDeserialize(array $data) : Guild{
		return new Guild($data["name"], $data["players"], GuildStorage::jsonDeserialize($data["guildStorage"]), $data["guildJoinType"]);
	}

	public function getMembers() : array{
		return $this->players;
	}

	/**
	 * @return Player[]
	 */
	public function getOnlineMembers() : array{
		$arr = [];
		foreach(array_keys($this->getMembers()) as $member){
			if(($player = Server::getInstance()->getPlayerExact($member)) instanceof Player){
				$arr[] = $player;
			}
		}
		return $arr;
	}

	public function getOfficers() : array{
		$arr = [];
		foreach($this->players as $name => $role){
			if($role === self::CO_LEADER){
				$arr[] = $name;
			}
		}
		return $arr;
	}

	public function getLeader() : string{
		foreach($this->players as $name => $role){
			if($role === self::LEADER){
				return $name;
			}
		}
		return "없음";
	}

	public function isMember($player) : bool{
		$player = GuildAPI::convert($player);
		return isset($this->players[$player]) or $this->getLeader() === $player;
	}

	public function addMember($player, string $role = self::MEMBER, bool $invitation = false) : void{
		$this->validateRole($role);
		$player = GuildAPI::convert($player);
		if(!isset($this->players[$player])){
			$this->players[$player] = $role;
			if($invitation)
				$this->broadcastMessage("{$player}님이 초대로 길드에 가입하셨습니다.");
		}
	}

	public function removeMember($player, bool $force = false) : void{
		$player = GuildAPI::convert($player);
		if(isset($this->players[$player])){
			unset($this->players[$player]);
			if($force)
				$this->broadcastMessage("{$player}님이 길드에서 추방당했습니다.");
		}
	}

	public function broadcastMessage(string $message, $sender = null) : void{
		foreach($this->getOnlineMembers() as $player){
			$player->sendMessage("§b§l[{$this->name}]§r§7 " . ($sender !== null ? "{$sender} > " : "") . $message);
		}
	}

	public function getGuildStorage() : GuildStorage{
		return $this->guildStorage;
	}

	/**
	 * @param $player
	 *
	 * @return bool
	 * @deprecated
	 */
	public function addInvitation($player) : bool{
		return $this->guildStorage->addInvitation($player);
	}

	/**
	 * @param $player
	 *
	 * @return bool
	 * @deprecated
	 */
	public function removeInvitation($player) : bool{
		return $this->guildStorage->removeInvitation($player);
	}

	public function validateRole(string $role) : void{
		$roles = [
			self::LEADER,
			self::CO_LEADER,
			self::MEMBER
		];
		if(!in_array($role, $roles)){
			throw new InvalidArgumentException("Role $role is not valid.");
		}
	}

	public function getGuildJoinType() : string{
		return $this->guildJoinType;
	}

	public function hasPermission($player) : bool{
		$player = GuildAPI::convert($player);
		return in_array($this->getMembers()[$player], [self::LEADER, self::CO_LEADER]);
	}

	public function canJoin($player) : bool{
		return !$this->isMember($player) and count($this->getMembers()) < $this->getGuildStorage()->getGuildCount();
	}

	public function getRole($player) : string{
		return $this->players[GuildAPI::convert($player)];
	}

	public function setRole($player, string $role = self::MEMBER) : void{
		$this->players[GuildAPI::convert($player)] = $role;
	}

	public function setGuildJoinType(string $type) : void{
		$this->guildJoinType = $type;
	}
}
